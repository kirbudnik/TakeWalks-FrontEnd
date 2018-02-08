<?php


class BookingsPromo extends AppModel{

    public $hasMany = array(
        'BookingsPromosBook' => array(
            'className'	=> 'BookingsPromosBook',
            'foreignKey'=> 'bookings_promos_id',
            //only join books that have questions
            'conditions' => array('exists (SELECT 1 FROM bookings_promos_questions BookingsPromosQuestion WHERE BookingsPromosQuestion.bookings_promos_books_id = BookingsPromosBook.id  )')
        ),
    );

    public function promoValid($promo, $item, $cart){
        // Booking Date
        if (
            (strtotime($promo['BookingsPromo']['booking_start_date']) < time())
            &&
            (strtotime($promo['BookingsPromo']['booking_end_date']) > time())
            ){
        } else {
            return false;
        }


        // Event Date
        if (
            (strtotime($promo['BookingsPromo']['event_start_date']) < strtotime($item['datetime']))
            &&
            (strtotime($promo['BookingsPromo']['event_end_date']) > strtotime($item['datetime']))
        ){
        } else {
            return false;
        }

        // Event Id
        // Currently promo codes may be applied to all tours, or just one specific tour.
        // Want to change this promo logic to support some activities (but not all).
        // Change bookings_promos.events_id to varchar(255) and support comma-delimited list of id's.
        // Thinking on backwards support of promo codes, the promo codes with empty 'events_id' are still valid for All items in cart
        if ( isset($promo['BookingsPromo']['events_id'])  &&  strlen($promo['BookingsPromo']['events_id']) > 0 ){
            $eventsId = preg_replace('/\s+/', '', $promo['BookingsPromo']['events_id']);
            $eventsId = explode(",", $eventsId);
            if (!in_array($item['event_id'] , $eventsId)){
                return false;
            }
        }

        // functionality to define whether a tour can be excluded from Promo Code
        if ( isset($item['disable_promos']) && $item['disable_promos'] == 1 ){
            // add the ability for specific promo codes to over-ride the exclusion
            if ( isset($promo['BookingsPromo']['exclude_override']) && $promo['BookingsPromo']['exclude_override'] == 0 ){
                $promoDisableTourNameExcluded = CakeSession::read('promo_disable_tour_name_excluded');
                $promoDisableTourNameExcluded[ $item['event_id'] ] = $item['name'];
                CakeSession::write('promo_disable_tour_name_excluded', $promoDisableTourNameExcluded );
                return false;
            }
        }

        $amount_local = array_sum(Hash::extract($cart, '{n}.total_price'));
        if ($amount_local < $promo['BookingsPromo']['min_cart_value']){
            return false;
        }

        $items_count = count($cart);
        if ($items_count < $promo['BookingsPromo']['min_events']){
            return false;
        }

        return true;
    }

    public function promoValidDate($promo_code){
        $promo = $this->findByPromoCode($promo_code);

        if(!$promo || !$promo['BookingsPromo']['active']) {
            return false;
        }

        // Booking Date
        if (
            (strtotime($promo['BookingsPromo']['booking_start_date']) < time())
            &&
            (strtotime($promo['BookingsPromo']['booking_end_date']) > time())
            ){
        } else {
            return false;
        }

        return $promo;
    }


    public function calculatePromos($cart, $promo_code){
        CakeSession::delete('promo_code_applied');
        CakeSession::delete('promo_discount_fixed_total');

        CakeSession::write('promo_disable_tour_name_excluded', [] );

        $promoCodeApplied = false;

        $promo = $this->findByPromoCode($promo_code);

        if(!$promo || !$promo['BookingsPromo']['active']) {
            return false;
        }

        $eventsId = [];
        $discount = $promo['BookingsPromo']['discount_amount'];

        if ( isset($promo['BookingsPromo']['events_id'])  &&  strlen($promo['BookingsPromo']['events_id']) > 0 ){
            $eventsId = preg_replace('/\s+/', '', $promo['BookingsPromo']['events_id']);
            $eventsId = explode(",", $eventsId);
        }

        /**
         * to use all the available amount For fixed discounts on promo codes with
         * Event Specific factor (bookings_promos.events_id)
         */
        $fixedDiscount = [];
        if ($promo['BookingsPromo']['fixed'] && count($eventsId) >= 0) {

            $validEventsCount = 0;
            foreach($cart as $item2){
                if ($this->promoValid($promo, $item2, $cart)){
                    $validEventsCount++;
                }
            }
            if ($validEventsCount > 0){
                $validEventsDiscount = $discount/$validEventsCount;

                $totalPromoLocal = 0;
                $totalPromoDiscount = 0;
                $pendingAmountDiscount = 0;
                foreach($cart as  $item2){
                    if ($this->promoValid($promo, $item2, $cart)){
                        $promo_local = 0;
                        $total_price_discount = $item2['total_price'] - $validEventsDiscount;
                        if ($total_price_discount > 0) {
                            $promo_local = $total_price_discount;
                            $promo_discount = $validEventsDiscount;
                        } else {
                            $promo_discount = $item2['total_price'];
                            $pendingAmountDiscount += abs($total_price_discount);
                        }
                        $totalPromoLocal += $promo_local;
                        $totalPromoDiscount += $promo_discount;

                        $item2['promo_discount'] = $promo_discount;
                        $item2['promo_local'] = $promo_local;

                        $fixedDiscount[$item2['event_id']] = $item2;
                    }
                }
                if (count($fixedDiscount) > 0 && $pendingAmountDiscount > 0){
                    foreach($fixedDiscount as $i => $item2){
                        if ($item2['promo_local'] > 0 && $pendingAmountDiscount > 0){
                            $promo_local = 0;
                            $total_price_discount = $item2['promo_local'] - $pendingAmountDiscount;
                            $pendingAmountDiscount -= $pendingAmountDiscount;
                            if ($total_price_discount > 0) {
                                $promo_local = $total_price_discount;
                                $promo_discount = $item2['promo_discount'] + $pendingAmountDiscount;
                            } else {
                                $promo_discount = $item2['total_price'];
                            }

                            $item2['promo_discount'] = $promo_discount;
                            $item2['promo_local'] = $promo_local;

                            $fixedDiscount[$i] = $item2;
                        }
                    }
                }
            }
        }


        $outCart = array();

        $anyValid = false;
        foreach($cart as $item) {
            $promoValid = $this->promoValid($promo, $item, $cart);
            $anyValid = $anyValid || $promoValid;
            //var_dump($promoValid);
            unset($item['promo_discount_fixed']);
            unset($item['promo_discount_fixed_by_event']);
            unset($item['promo_discount_percentage']);
            unset($item['promo_type']);
            unset($item['promo_discount']);
            unset($item['promo_code']);
            unset($item['promo_local']);
            if($promoValid) {

                if ($promo['BookingsPromo']['fixed_cart'] ){
                    $validEventsCount = 0;
                    foreach($cart as $item2){
                        if ($this->promoValid($promo, $item2, $cart)){
                            $validEventsCount++;
                        }
                    }
                    $discount = $discount/$validEventsCount;
                }

                if ( $promo['BookingsPromo']['fixed'] && in_array($item['event_id'] , $eventsId) ){
                    // For fixed discounts on promo codes with Event Specific factor (bookings_promos.events_id)
                    $discount = $fixedDiscount[$item['event_id']]['promo_discount'];
                    $item['promo_discount_fixed_by_event'] = $discount;
                    $item['promo_type'] = 'fixed_by_event';

                    $total_price_discount = $item['total_price'] - $discount;
                    $promo_local = ($total_price_discount < 0) ? 0 : $total_price_discount;
                    $promo_discount = ($promo_local == 0) ? $item['total_price'] : $discount;
                    $item['promo_discount'] = number_format($promo_discount,2);
                    $item['promo_code'] = $promo['BookingsPromo']['promo_code'];
                    $item['promo_local'] = $promo_local;

                    foreach(ExchangeRate::getExchangeRates() as $currency => $exchangeRate){
                        $item['promo_discount_fixed_'.$currency] = ExchangeRate::convert($promo_discount,2,0,$currency);
                    }
                    $promoCodeApplied = true;

                } else if ( $promo['BookingsPromo']['fixed'] ){
                    // There was a misunderstanding.
                    // A fixed discount is suppose to be applied to the total price only and not each tour.

                    // Uncomment next block to enable fixed discount to each tour
                    /*
                    $item['promo_discount_fixed'] = $discount;
                    $promo_local = $item['total_price'] - $discount;
                    if ($promo_local < 0){
                        $promo_local = 0;
                    }
                    $promo_local = round($promo_local, 2);
                    $item['promo_type'] = 'fixed';

                    foreach(ExchangeRate::getExchangeRates() as $currency => $exchangeRate){
                        $item['promo_discount_fixed_'.$currency] = ExchangeRate::convert($discount,2,0,$currency);
                    }

                    $promoCodeApplied = true;
                    */

                } else {
                    // here is the section for percentage promo code (no fixed value)
                    $discount = $promo['BookingsPromo']['discount_amount'];
                    $item['promo_discount_percentage'] = $discount;
                    $discount = 100 - $discount;
                    $discount_per = round($discount / 100, 2);
                    $promo_local = round($item['total_price'] * $discount_per, 2);
                    $item['promo_type'] = 'percent';


                    $item['promo_discount'] = number_format($item['total_price'] - $promo_local,2);
                    $item['promo_code'] = $promo['BookingsPromo']['promo_code'];
                    $item['promo_local'] = $promo_local;

                    $promoCodeApplied = true;
                }

            }
            $outCart[] = $item;
        }

        if ($promo['BookingsPromo']['fixed'] && count($eventsId) == 0 && $promo['BookingsPromo']['discount_amount'] > 0){
            // A fixed discount is suppose to be applied to the total price only.
            $cartValue = 0;
            foreach($outCart as $item) {
                $cartValue += $item['total_price'];
            }
            $discount = $promo['BookingsPromo']['discount_amount'];
            $fixedDiscount = $cartValue - $discount;
            if ($fixedDiscount <= 0){
                $discount = $cartValue;
            }

            $discountCurrency = [];
            $discountCurrency['local'] = $discount;
            foreach (ExchangeRate::getExchangeRates() as $currency => $exchangeRate) {
                $discountCurrency[$currency] = ExchangeRate::convert($discount, 2, 0, $currency);
            }
            CakeSession::write('promo_discount_fixed_total', $discountCurrency);

            $promoCodeApplied = true;
        }

        //don't allow discount codes that are invalid
        if($promo['BookingsPromo']['discount_amount'] == 0 || $promo['BookingsPromo']['discount_amount'] == null || $promo['BookingsPromo']['discount_amount'] == ''){
            $promoCodeApplied = false;
            $anyValid = false;
        }

        CakeSession::write('promo_code_applied', $promoCodeApplied);

        return ['valid' => $anyValid, 'newCart' => $outCart ];
    }


} 
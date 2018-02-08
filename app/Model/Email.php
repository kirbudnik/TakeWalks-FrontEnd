<?php
class Email extends AppModel {

    private $_mailerApiUrl = MAILER_API;
    
    public function sendConfirmationEmail($booking_id, $appConfig, $promo) {
        $this->Booking = ClassRegistry::init('Booking');
        $this->Event = ClassRegistry::init('Event');
        $this->Agent = ClassRegistry::init('Agent');
        $this->PaymentTransaction = ClassRegistry::init('PaymentTransaction');
        $this->Domain = ClassRegistry::init('Domain');
        $this->EventsDomainsGroup = ClassRegistry::init('EventsDomainsGroup');


        $booking = $this->Booking->find('first', array(
            'conditions' => array(
                'Booking.id' => $booking_id
            ),
            'contain' => array('Client')
        ));

        $to = $booking['Client']['email'];
        if ($booking['Client']['agents_id']) {
            $agent = $this->Agent->findById($booking['Client']['agents_id']);
            if (!empty($agent)) {
                $to = $agent['Agent']['email_address'];
            }
        }

        $Client = array('Client' => $booking['Client']);
        $BookingsDetail = $this->Booking->BookingsDetail->findAllByBookingsId($booking_id);
        if ($BookingsDetail) {
            $transactions_ids = array();
            foreach ($BookingsDetail as $BookingsDetail_key => $BookingsDetail_loop) {

                if ($BookingsDetail_loop['BookingsDetail']['transaction_id'] && !in_array($BookingsDetail_loop['BookingsDetail']['transaction_id'], $transactions_ids)) {
                    $transactions_ids[] = $BookingsDetail_loop['BookingsDetail']['transaction_id'];
                }

            }

        }
        $PaymentTransaction = $this->PaymentTransaction->find('all', array(
            'recursive' => 1,
            'conditions' => array(
                "PaymentTransaction.booking_id" => $booking_id
            ),
            'order' => array("PaymentTransaction.transaction_date ASC")
        ));

        //get all of the charity donations
        $this->CharitiesDonation = ClassRegistry::init('CharitiesDonation');
        $charities = $this->CharitiesDonation->find('all', array(
            'conditions' => array('booking_id' => $booking_id),
            'contain' => array('Charity')
        ));

        //sort the tours
        usort($BookingsDetail, function($bookingDetails1, $bookingDetails2){
            return strtotime($bookingDetails1['EventsStage']['datetime']) < strtotime($bookingDetails2['EventsStage']['datetime']) ? -1 : 1;
        });


        //if all tours belong to france
        $eventIds = array();
        foreach($BookingsDetail as $tour){
            $eventIds[] = $tour['Event']['id'];
        }

        //at least one tour belongs to france
        if($this->EventsDomainsGroup->hasAny(array(
            'event_id' => $eventIds,
            'group_id' => 21
        ))){
            //no tours belongs not to france
            if(!$this->EventsDomainsGroup->hasAny(array(
                'event_id' => $eventIds,
                'group_id != ' => 21
            ))) {
                //todo hacky way to do this. do something better later
                $BookingsDetail[0]['isFrance'] = true;
            }else{
                //tours belong to both france and italy
                $BookingsDetail[0]['isFranceAndItaly'] = true;

            }

        }

        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();

        $emailTemplate = 'confirmation';
        $config = 'info';
        $subject = 'Booking Confirmation Email';
        switch($appConfig->domain){

            case 'turkey':
                $emailTemplate = 'turkeyConfirmation';
                $config = 'turkey';
                break;
            case 'new-york':
                $config = 'new_york';
                $emailTemplate = 'nycConfirmation';
                break;
            case 'italy-es':
                $emailTemplate = 'teiConfirmation';
                $subject = 'Confirmación de Reserva';
                $config = 'tei';
                break;
            case 'takeWalks':
                $bookingDetailIds = [];
                foreach($BookingsDetail as $bookingDetailRow){
                    $bookingDetailIds[] = $bookingDetailRow['BookingsDetail']['id'];
                }
		$postBody = json_encode([
                    "booking_detail_ids" => $bookingDetailIds,
                    "template_name" => "takewalks-booking-confirmation-email-english"
                ]);
                $url = $this->_mailerApiUrl . 'sendBookingConfirmation';

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($postBody))
                );

                $result = curl_exec($ch);
                curl_close($ch);


                return;
                break;

        }

        $exchangeRate = CakeSession::read('exchangeRate');
        $promo_discount_fixed_total = CakeSession::read('promo_discount_fixed_total');

        $copyEmail = ['bookings@walks.org' => 'Bookings'];

        $Email->config($config);
        $Email->template($emailTemplate, 'default');
        $Email->viewVars(compact('Client', 'BookingsDetail','PaymentTransaction','booking_id','charities','exchangeRate', 'promo','promo_discount_fixed_total'));
        $Email->emailFormat('html');
        $Email->to($to);
        $Email->cc($copyEmail);
        $Email->subject($subject);
        if($BookingsDetail[0]['isFrance']) $Email->from(array('info@walksoffrance.com' => 'Walks of France'));
        $Email->send();

        $EmailInfo = new CakeEmail();
        $EmailInfo->config($config);
        $EmailInfo->template($emailTemplate, 'default');
        $EmailInfo->viewVars(compact('Client', 'BookingsDetail','PaymentTransaction','booking_id','charities','exchangeRate', 'promo', 'promo_discount_fixed_total'));
        $EmailInfo->emailFormat('html');
        $EmailInfo->to($this->Domain->field('infoemail',array('id'=>$appConfig->domainId)));
        $EmailInfo->cc($copyEmail);
        $EmailInfo->subject($subject);
        if($BookingsDetail[0]['isFrance']) $Email->from(array('info@walksoffrance.com' => 'Walks of France'));
        $EmailInfo->send();
    }

    public function sendGiftCardEmail($booking_id, $promo_id, $gift_card_amount, $theme){
        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();

        $this->Booking = ClassRegistry::init('Booking');
        $this->PaymentTransaction = ClassRegistry::init('PaymentTransaction');
        $this->BookingsPromo = ClassRegistry::init('BookingsPromo');
        $this->Agent = ClassRegistry::init('Agent');

        $booking = $this->Booking->find('first', array(
            'conditions' => array(
                'Booking.id' => $booking_id
            ),
            'contain' => array('Client')
        ));

        $Client = array('Client' => $booking['Client']);

        $to = $booking['Client']['email'];
        if ($booking['Client']['agents_id']) {
            $agent = $this->Agent->findById($booking['Client']['agents_id']);
            if (!empty($agent)) {
                $to = $agent['Agent']['email_address'];
            }
        }

        $emailTemplate = 'gift_card';
        $config = 'info';
        $subject = 'Walks of Italy Gift Card';

        switch ($theme) {
            case "nyc":
                $config = 'new_york';
                $emailTemplate = 'nyc_gift_card';
                $subject = 'Walks of New York Gift Card';
                break;
            case "Turkey":
                $config = 'turkey';
                $emailTemplate = 'turkey_gift_card';
                $subject = 'Walks of Turkey Gift Card';
                break;
        }

        $promo = $this->BookingsPromo->findById($promo_id);

        $PaymentTransaction = $this->PaymentTransaction->find('all', array(
            'recursive' => 1,
            'conditions' => array(
                "PaymentTransaction.booking_id" => $booking_id
            ),
            'order' => array("PaymentTransaction.transaction_date ASC")
        ));

        $Email->config($config);

        $Email->template($emailTemplate, 'default');
        $Email->viewVars(compact('Client', 'promo', 'PaymentTransaction','gift_card_amount','booking_id'));
        $Email->emailFormat('html');
        $Email->to($to);
        $Email->subject($subject);
        $Email->send();


    }

    public function sendDonationConfirmationEmail($donation_id, $appConfig){
        $this->CharitiesDonation = ClassRegistry::init('CharitiesDonation');
        $this->Domain = ClassRegistry::init('Domain');

        $donation = $this->CharitiesDonation->find('first', array(
            'conditions' => array(
                'CharitiesDonation.id' => $donation_id
            ),
            'contain' => array('Charity')
        ));


        $charity = $donation['Charity'];
        $donation = $donation['CharitiesDonation'];

        $to = $donation['email'];



        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();

        $emailTemplate = 'italyDonationConfirmation';
        $config = 'info';

        $exchangeRate = CakeSession::read('exchangeRate');

        $Email->config($config);

        $Email->template($emailTemplate, 'default');
        $Email->viewVars(compact('charity','donation'));
        $Email->emailFormat('html');
        $Email->to($to);
        $Email->subject('Donation Confirmation Email');
        $Email->send();

        $EmailInfo = new CakeEmail();
        $EmailInfo->config($config);
        $EmailInfo->template($emailTemplate, 'default');
        $Email->viewVars(compact('charity','donation'));        $EmailInfo->emailFormat('html');

        $EmailInfo->to($this->Domain->field('infoemail',array('id'=>$appConfig->domainId)));
        $Email->subject('Donation Confirmation Email');
        $EmailInfo->send();
    }

    public function sendContactEmail($data) {
        App::uses('CakeEmail', 'Network/Email');
        $contactEmail = new CakeEmail();
        $contactEmail->config('info')
            ->template('contact', 'default')
            ->viewVars(compact('data'))
            ->emailFormat('html')
            ->to('info@walksofnewyork.com')
            ->subject('Contact Form Submission ' . date('M d, Y h:ia'))
            ->send();
    }

    public function sendDonationFailEmail($donationInfo){
        App::uses('CakeEmail', 'Network/Email');
        $contactEmail = new CakeEmail();
        $contactEmail->config('info')
            ->template('donation_failed', 'default')
            ->viewVars(array(
                'donationInfo' => $donationInfo
            ))
            ->emailFormat('html')
            ->to('aleksey@vimbly.com')
            ->subject('Walks Donation Failed')
            ->send();
    }

}


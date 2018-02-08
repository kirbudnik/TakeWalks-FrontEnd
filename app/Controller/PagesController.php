<?php
App::uses('AppController', 'Controller');
App::import('Utility', 'Hash');

class PagesController extends AppController {

    public $components = array('Session', 'UserApi');
    public $uses = array(
        'Agent',
        'Booking',
        'BookingsAddress',
        'BookingsDetail',
        'BookingsDetailsSubmit',
        'BookingsPromo',
        'BookingsPromosQuestion',
        'Charity',
        'CharitiesDonation',
        'Client',
        'Comment',
        'ConnectionManager',
        'Domain',
        'DomainsGroup',
        'Event',
        'EventsDomainsGroup',
        'EventPrivate',
        'EventsPromotion',
        'EventsReview',
        'EventsStage',
        'EventsSuggestion',
        'Feedback',
        'Tag',
        'EventsTag',
        'EventsImage',
        'EventsStagePaxRemaining',
        'WpPost',
        'CurrenciesExchange',
        'PaymentTransaction',
        'Payment'

    );
    public $ticketTypes = array('adults','children','infants','seniors','students');
    /**
     * Discount for related tours. Percent.
     * @var int
     */
    public $discountRelatedTour = 10;
    public $helpers = array('Time', 'Text', 'Event', 'ReSrc');

    protected $takeWalks = []; //container to pass things to take walks child controller

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();



    }

    public function feedbackProxy() {

        $url = 'http://feedback.walks.org/feed/feed.php?hash=a6ee091dc5e7cb28b5753ffa3528a705&domain=WoI';

        if($eventId = $this->request->query('event')) {
            $url .= "&event=$eventId";
        }

        $response = file_get_contents($url);

        $this->response->body($response);
        return $this->response;
    }


    public function eventDetail($city, $slug) {
        $this->theme = $this->theme == 'nyc' ? 'nyc2' : $this->theme;

        $isTakeWalks = $this->config->domain == 'takeWalks';

        $js = array(
            'lib/jquery.datepick',
            'lib/jquery.datepick.ext',
            'event_detail',
            'lib/moment',
            'lib/underscore'
        );
        $css = array('detail');
        if(in_array($this->theme, array('Turkey', 'Italy', 'ItalyEs', 'nyc'))){
            $js = array_merge($js,array(
                'lib/jquery.fancybox.pack',
                'lib/jquery.mousewheel-3.0.6.pack'
            ));
            $css = array_merge($css,array(
                'lib/jquery.fancybox'
            ));
        }

        $this->set('js', $js);
        $this->set('css', $css);

        $city = substr($city, 0, -6);
        $domainsGroup = $this->DomainsGroup->findByUrlName($city);
        if(!$domainsGroup) {
            throw new NotFoundException;
        }
        $arrcity = array(
            'id' => $domainsGroup['DomainsGroup']['id'],
            'slug' => $domainsGroup['DomainsGroup']['url_name'],
            'name' => $domainsGroup['DomainsGroup']['name']
        );
        $this->set('arrcity', $arrcity);

        $event = $this->Event->find('first', array(
            'conditions' => array(
                'Event.url_name' => $slug,
                'Event.is_active' => 1,
                'EventsDomain.domains_id' => $this->config->domainId,
                //'Event.visible' => 1,
            ),
            'contain' => array(
                'EventsDomain' => array(
                    'Domain'
                ),
                'EventsImage' => array(
                    'order' => array('primary' => 'desc', 'image_order')
                ),
                'EventsStagePaxRemaining'=>array(
                    'conditions' => array(
                        'datetime >= now()',
                        'pax_remaining >= ' => 1
                    ),
                    'limit' => 1
                ),
                'TripadvisorQuote',
                'EventsDomainsGroup' => array(
                    'DomainsGroup',
                    'conditions' => array(
                        'EventsDomainsGroup.group_id' => $domainsGroup['DomainsGroup']['id'],
                        //'EventsDomainsGroup.primary' => 1
                    )
                ),
                'Charity'
            )
        ));

        $this->takeWalks['event'] = $event;

        if ( empty($event) || !isset($event['EventsDomainsGroup'])) throw new NotFoundException();

        if(!$isTakeWalks){
            if ( isset($event['EventsDomainsGroup'][0]) && $event['EventsDomainsGroup'][0]['primary'] != 1 ) {
                $eventsDomainsGroup = $this->EventsDomainsGroup->find('first', array(
                    'conditions' => array(
                        'event_id' => $event['Event']['id'],
                        'primary' => 1
                    ),
                    'contain' => array('DomainsGroup')
                ));

                if( $event['EventsDomainsGroup'][0]['DomainsGroup']['url_name'] != $eventsDomainsGroup['DomainsGroup']['url_name']) {
                    $urlRedirect = '/'.$eventsDomainsGroup['DomainsGroup']['url_name'].'-tours/'.$slug;
                    $this->redirect($urlRedirect);
                } else {
                    // TODO: check https://staging-es.walksofitaly.com/roma-tours/visita-guiada-coliseo, name is "Roma - Global"
                    // until clarification, next lines are commented
                    /*
                    $event['EventsDomainsGroup'][0]['id'] = $eventsDomainsGroup['EventsDomainsGroup']['id'];
                    $event['EventsDomainsGroup'][0]['group_id'] = $eventsDomainsGroup['EventsDomainsGroup']['group_id'];
                    $event['EventsDomainsGroup'][0]['event_id'] = $eventsDomainsGroup['EventsDomainsGroup']['event_id'];
                    $event['EventsDomainsGroup'][0]['primary'] = $eventsDomainsGroup['EventsDomainsGroup']['primary'];
                    $event['EventsDomainsGroup'][0]['DomainsGroup'] = $eventsDomainsGroup['DomainsGroup'];
                    $domainsGroup['DomainsGroup'] = $eventsDomainsGroup['DomainsGroup'];
                    */
                }
            }

            $arrcity = array(
                'id' => $domainsGroup['DomainsGroup']['id'],
                'slug' => $domainsGroup['DomainsGroup']['url_name'],
                'name' => $domainsGroup['DomainsGroup']['name']
            );

            $this->set('arrcity', $arrcity);
        }


        $pax_group = $this->Event->getStages($event['Event']['id'], date('Y-m-d'), date('Y-m-t', strtotime('+1 year')));
        $pax_private = $this->EventPrivate->getStages($event['Event']['id'], date('Y-m-d'), date('Y-m-t', strtotime('+1 year')));
        $daterange = new DatePeriod(new DateTime(), new DateInterval('P1M'), new DateTime('+1 year'));
        $loaded = array();
        foreach($daterange as $date) {
            $loaded[] = $date->format('Y-n');
        }

        reset($pax_group);
        $first_group = strtotime(key($pax_group));
        $first_group = array(
            'year' => date('Y', $first_group),
            'month' => date('n', $first_group),
        );

        reset($pax_private);
        $first_private = strtotime(key($pax_private));
        $first_private = array(
            'year' => date('Y', $first_private),
            'month' => date('n', $first_private),
        );



        $initValues = array(
            'eventId' => $event['Event']['id'],
            'latitude' => $event['Event']['latitude'],
            'longitude' => $event['Event']['longitude'],
            'group_prices' => array(
                'adults' => $event['Event']['adults_price'],
                'seniors' => $event['Event']['seniors_price'],
                'students' => $event['Event']['students_price'],
                'children' => $event['Event']['children_price'],
                'infants' => $event['Event']['infants_price']
            ),

            'private_prices' => array(
                'adults' => $event['Event']['private_adults_price'],
                'seniors' => $event['Event']['private_seniors_price'],
                'students' => $event['Event']['private_students_price'],
                'children' => $event['Event']['private_children_price'],
                'infants' => $event['Event']['private_infants_price'],
            ),

            'private_base_price' => $event['Event']['private_base_price'],

            'sellout_group' => array(),
            'dates_group' => $pax_group,
            'first_group' => $first_group,
            'sellout_private' => array(),
            'dates_private' => $pax_private,
            'first_private' => $first_private,

            'loaded' => $loaded,
            'cart' => $this->Session->read('shopping_cart'),
            //currency exchange rate
            'currency' => array(
                'symbol' => ExchangeRate::getSymbol(),
                'exchangeRate' => ExchangeRate::getExchangeRateFromDbCurrency(),
                'selected' => ExchangeRate::getCurrency()
            )
        );

        $relatedTours = $this->EventsSuggestion->find('all', array(
            'conditions' => array(
                'EventsSuggestion.events_id' => $event['Event']['id']
            ),
            'contain' => array(
                'Event' => array(
                    'EventsImage' => array(
                        'conditions' => array('feature' => 1)
                    ),
                    'EventsDomainsGroup' => array(
                        'DomainsGroup',
                        'conditions' => array(
                            'EventsDomainsGroup.primary' => 1
                        )
                    )
                ),
            ),
            'order' => 'order'
        ));

        //check if event belongs to france
        if($arrcity['id'] == 21){
            $this->set('isFrance',true);
        }

        $relatedToursModal = [];
        $ticketTypes = ['adults','seniors','students','children','infants'];
        foreach($relatedTours as $i => $relatedTour){
            $rt = [];
            if (isset($relatedTour['Event']['id'])){
                $rt['event_id'] = $relatedTour['Event']['id'];
                $rt['sku'] = $relatedTour['Event']['sku'];
                $rt['name_listing'] = htmlentities($relatedTour['Event']['name_listing'], ENT_QUOTES);
                $rt['adults_price'] = $relatedTour['Event']['adults_price'];
                $rt['students_price'] = $relatedTour['Event']['students_price'];
                $rt['children_price'] = $relatedTour['Event']['children_price'];
                $rt['infants_price'] = $relatedTour['Event']['infants_price'];
                $rt['adults_price'] = $relatedTour['Event']['adults_price'];
                $rt['more_info'] = "/".$relatedTour['Event']['EventsDomainsGroup'][0]['DomainsGroup']['url_name']."-tours/".$relatedTour['Event']['url_name'];
                $rt['images_name'] = "";
                $rt['alt_tag'] = "";
                foreach($relatedTour['Event']['EventsImage'] as $image) {
                    if($image['publish']) {
                        $rt['images_name'] = $image['images_name'];
                        $rt['alt_tag'] = $image['alt_tag'];
                        break;
                    }
                }
                foreach(ExchangeRate::getExchangeRates() as $name => $exchangeRate){
                    foreach($ticketTypes as $ticketType){
                        $rt[$ticketType . '_price_converted_' . $name] = ExchangeRate::convert($relatedTour['Event'][$ticketType.'_price'],1,0,$name);
                    }
                }
                $dates_group = $this->Event->getStages($rt['event_id'], date('Y-m-d'), date('Y-m-t', strtotime('+5 months')));
                $rt['dates_group'] = $dates_group;
                $relatedToursModal[] = $rt;
            }
        }
        $initValues['relatedToursModal'] = $relatedToursModal;
        $initValues['discountPercentRelatedTour'] = $this->discountRelatedTour;

        // Wistia video
        if (isset($event['Event']['wistia'])) {
            //$this->set('video', $this->Event->getWistiaVideo($event['Event']['wistia']));
        }

        //todo create a better fix where the events_ids don't overlap for take walks
        $feedbackConditions = [
            'events_id' => $event['Event']['id'],
            'is_published' => 1,
            'event_rating >= ' => 1,
            'event_rating <= ' => 5
        ];

        if(!$isTakeWalks) $feedbackConditions['domain_code'] = $this->Feedback->cityToCode($this->config->domain);


        $eventReviews = $this->Feedback->find('all',array(
            'conditions' => $feedbackConditions,
            'order' => array('feedback_date' => 'DESC')
        ));
        $reviews = array();
        foreach($eventReviews as $review){
            $reviews[] = $review['Feedback'] + array(
                    'feedback_text' => $review['Feedback']['comment_stuff_edited']
                );
        }

        $initValues['product_detail'] = array('id' => $event['Event']['id'], 'name_short' => $event['Event']['name_short'] , 'sku' => $event['Event']['sku'] );
        $initValues['measure'] = 'view_product_detail';
        $initValues['theme'] = $this->theme;

//        if (strtolower($this->theme) == 'italy'){
//            $contentfulWrapper = new ContentfulWrapper();
////            $tourCMS = $contentfulWrapper->getTourById($event['Event']['id']);
//            $tourCMS = $contentfulWrapper->getTourTagsById($event['Event']['id']);
//            echo '<pre>';
//            echo '+****';
//            print_r($tourCMS);
//            die;
//            if (!empty($tourCMS) && isset($tourCMS[0])){
//                $tourCMS = $tourCMS[0];
//                $event['Event']['name_long'] = $tourCMS['tourTitlelong'];
//                $event['Event']['name_short'] = $tourCMS['titleShort'];
//                $event['Event']['meta_description'] = $tourCMS['metaDescription'];
//                $event['Event']['description_listing'] = $tourCMS['shortDescriptionListing'];
//                $event['Event']['display_duration'] = $tourCMS['duration'];
//                $event['Event']['display_time'] = $tourCMS['startTime'];
//                $event['Event']['bullet1'] = $tourCMS['detailIntro'];
//                $event['Event']['bullet2'] = $tourCMS['whyThisSite'];
//                $event['Event']['bullet3'] = $tourCMS['whyThisTour'];
//                $event['Event']['sites_included'] = implode("\n",$tourCMS['sitesVisited']);
//                $event['Event']['price_includes'] = implode("\n",$tourCMS['included']);
//            }
//        }

        $this->set('event', $event['Event']);
        $this->set('ecViewProductDetailPage', $event['Event']);
        $this->set('ecTheme', $this->theme);
        $this->set('images', $event['EventsImage']);
        $this->set('reviews', $reviews);
        $this->set('tripadvisor_quote', $event['TripadvisorQuote']);
        if(!$isTakeWalks){
            $this->set('domainsGroup', $event['EventsDomainsGroup'][0]['DomainsGroup']);
            if($event['EventsDomainsGroup'][0]['DomainsGroup']['name'] == 'Transfers') {
                return $this->render('transfer_detail');
            }
        }
        $this->set('layoutTitle', $event['Event']['page_title']);
        $this->set('initValues', $initValues);
        $this->set('relatedTours', $relatedTours);
        $this->set('relatedToursModal', $relatedToursModal);
        if (!empty($event['Event']['meta_description'])) {
            $this->set('meta_description', $event['Event']['meta_description']);
        }


        $this->set('canonicalURL', FULL_BASE_URL . DS . $city . '-tours' . DS . $slug);

        //get the featured image
        $featuredImgUrl = null;
        foreach($event['EventsImage'] as $image){
            if($image['feature']) {
                $featuredImgUrl = $image['images_name'];
                break;
            }
        }
        $this->set('featuredImgUrl', $featuredImgUrl);
        $this->set('charity',$event['Charity']);
        $this->set('discountPercentRelatedTour', $this->discountRelatedTour);


    }

    public function remove_promo_code() {
        $cart = $this->Session->read('shopping_cart');

        $this->Session->delete('promo_code');
        $this->Session->delete('promo_discount_fixed_total');
        $this->Session->delete('promo_code_applied');
        foreach($cart as $i => $item) {
            unset($cart[$i]['promo_type']);
            unset($cart[$i]['promo_code']);
            unset($cart[$i]['promo_local']);
            unset($cart[$i]['promo_discount']);
            unset($cart[$i]['promo_discount_percentage']);
            unset($cart[$i]['promo_discount_fixed']);
            unset($cart[$i]['promo_discount_fixed_by_event']);
        }

        $this->Session->write('shopping_cart', $cart);
        $this->Session->setFlash('Promo code cleared.', 'FlashMessage/status');

        $this->redirect($this->referer());
    }

    public function remove_from_cart($n, $redirect = true) {
        $cart = array_values($this->Session->read('shopping_cart'));

        // if user remove any tour, remove discount related with that tour
        if($this->theme == 'Italy') {
            $bundledTours = $cart[$n]['related'];
            //get related tours in actual cart, for remove discount for related tour
            foreach ($cart as $j => $items) {
                if (in_array($cart[$j]['event_id'], $bundledTours)) {
                    $cart[$j]['discount_bundle_tour'] = 0;
                    $cart[$j]['discount_bundle_tour_percent'] = 0;
                    foreach(ExchangeRate::getExchangeRates() as $name => $exchangeRate){
                        $cart[$j]['discount_bundle_tour_converted_' . $name] = "0.00";
                    }
                }
            }
        }

        $this->Session->delete('promo_code');
        foreach($cart as $i => $item) {
            unset($cart[$i]['promo_code']);
            unset($cart[$i]['promo_local']);
        }
        array_splice($cart,$n,1);

        $this->Session->write('shopping_cart', array_values($cart));
        $this->Session->delete('promo_discount_fixed_total');
        $promo_discount_fixed_total = ($this->Session->read('promo_discount_fixed_total')) ? $this->Session->read('promo_discount_fixed_total') : null;

        if($this->request->is('ajax')) {
            $this->response->type('json');
            $this->autoRender = false;
            //make the same cart for payment page
            $initValues['cart_payment'] = array();
            if (!is_null($this->Session->read('shopping_cart'))){
                $initValues['cart_payment'] = array_reduce($this->Session->read('shopping_cart'), function($all, $tour){
                    $tickets = array();
                    $discountFixed = array();
                    $promo_discount_fixed = isset($tour['promo_discount_fixed']) ? $tour['promo_discount_fixed'] : 0;
                    //find out how many tickets and the prices
                    foreach($this->ticketTypes as $type){
                        $tour['default'] = 0;
                        if($tour[$type]){
                            $tickets[$type] = array( 'amount' => $tour[$type], 'price' => $tour[$type . '_price'] );
                            //get the price for each currency
                            foreach(ExchangeRate::getExchangeRates() as $currency => $exchangeRate){
                                $tickets[$type][$currency] = $tour[$type . '_price_converted_' . $currency];
                            }
                        }
                    }
                    $basePrice = array();
                    foreach(ExchangeRate::getExchangeRates() as $currency => $exchangeRate){
                        $basePrice[$currency] = $tour['base_price_' . $currency];
                        if ($promo_discount_fixed > 0){
                            $discountFixed[$currency] = ExchangeRate::convert($promo_discount_fixed,2,0,$currency);
                        }
                    }
                    $dateTimeFormat = '';
                    switch($this->theme){
                        case 'Italy':
                            $dateTimeFormat = date('F j, Y - h:ia', strtotime($tour['datetime']));
                            break;
                        default:
                            $dateTimeFormat = date('F j, Y - H.ia', strtotime($tour['datetime']));
                            break;
                    }
                    $all[] = array(
                        'tickets' => $tickets,
                        'type' => $tour['type'],
                        'event_id' => $tour['event_id'],
                        'sku' => $tour['sku'],
                        'name' => $tour['name'],
                        'url_name' => isset($tour['url_name']) ? $tour['url_name'] : '',
                        'dateTime' => $dateTimeFormat,
                        'totalPrice' => $tour['total_price'],
                        'discount' => isset($tour['promo_discount_percentage']) ? $tour['promo_discount_percentage'] : 0,
                        'promo_discount_fixed' => $promo_discount_fixed,
                        'discountFixed' => $discountFixed,
                        'promo_type' => isset($tour['promo_type']) ? $tour['promo_type'] : '',
                        'promo_discount' => isset($tour['promo_discount']) ? $tour['promo_discount'] : '',
                        'promo_local' => isset($tour['promo_local']) ? $tour['promo_local'] : '',
                        'discount_bundle_tour_percent' => isset($tour['discount_bundle_tour_percent']) ? $tour['discount_bundle_tour_percent'] : 0,
                        'basePrice' => $basePrice
                    );
                    return $all;
                },array());
            }
            return json_encode(array(
                'response' => 'ok',
                'cart' => $cart,
                'cart_payment' => $initValues['cart_payment'],
                'promo_discount_fixed_total' => $promo_discount_fixed_total,
            ));
        } else {
            if($redirect) {
                $this->redirect($this->referer());
            }
        }
    }

    protected function _writeExchangeRateToSession(){
        $CurrenciesExchange = $this->CurrenciesExchange->find('list', array(
            'fields' => array('CurrenciesExchange.exchangepair',
                'CurrenciesExchange.adj_rate')));
        $this->set(compact('CurrenciesExchange'));

        $transaction_exchange = $CurrenciesExchange[$this->config->exchangepair];

        //for the donation conversion
        $donationConversion = $this->config->defaultCurrency == 'USD' ? 1 : $transaction_exchange;
        CakeSession::write('exchangeRate', $donationConversion);
    }

    public function add_to_cart() {
        if(!$this->request->is('post')) {
            throw new MethodNotAllowedException('Add to cart must be POST');
        }

        $CurrenciesExchange = $this->CurrenciesExchange->find('list', array(
            'fields' => array('CurrenciesExchange.exchangepair',
                'CurrenciesExchange.adj_rate')));
        $this->set(compact('CurrenciesExchange'));

        $transaction_exchange = $CurrenciesExchange[$this->config->exchangepair];

        //for the donation conversion
        $this->_writeExchangeRateToSession();

        $this->EventsStage->contain(array(
            'Event' => array(
                'EventsImage' => array(
                    'order' => array('image_order' => 'asc')
                )
            )
        ));

        $related = array();
        $discount_bundle_tour_percent = 0;
        $discount_bundle_tour = 0;
        $datetime = $this->request->data('date') . ' ' . $this->request->data('time');
        $events_id = $this->request->data('events_id');

        //holds additional info for currencies
        $cart_item_currency = [];

        if($this->request->data('type') == 'group') {

            $stage = $this->EventsStage->find('first',array(
                'conditions' => array(
                    'EventsStage.events_id' => $events_id,
                    'EventsStage.datetime' => $datetime,
                    'EventsStage.group' => 1,
                )
            ));
            if(!$stage) {
                $this->Session->setFlash('Please select a valid date and time', 'FlashMessage/error');
                $this->redirect($this->referer());
            }

            // set related tours for actual event
            $relatedTours = $this->EventsSuggestion->find('all', array(
                'conditions' => array(
                    'EventsSuggestion.events_id' => $events_id
                ),
                'contain' => array( 'Event' ),
                'order' => 'order'
            ));
            foreach($relatedTours as $i => $relatedTour){
                $related[] = $relatedTour['Event']['id'];
            }

            $totalPax = 0;
            $totalPax += $this->request->data('adults');
            $totalPax += $this->request->data('seniors');
            $totalPax += $this->request->data('students');
            $totalPax += $this->request->data('children');
            $totalPax += $this->request->data('infants');

            $remaining = $this->EventsStagePaxRemaining->findById($stage['EventsStage']['id']);
            $remainingPax = $remaining['EventsStagePaxRemaining']['pax_remaining'];
            if($totalPax > $remainingPax) {
                $errorMessagePax = "Sorry, there are only $remainingPax tickets left for this tour time";
                if($this->request->is('ajax')) {
                    $this->response->type('json');
                    $this->autoRender = false;
                    return json_encode(array(
                        'response' => 'error',
                        'message' => $errorMessagePax,
                    ));
                } else {
                    $this->Session->setFlash($errorMessagePax, 'FlashMessage/error');
                    return $this->redirect($this->referer());
                }
            }

            if($this->theme == 'Italy') {
                //get related tours in actual cart, for apply discount for related tour
                $cart = $this->Session->read('shopping_cart') ?: array();
                $existRelatedInCart = false;
                $sameInCart = false;
                $type = ($stage['Event']['group_private'] == 'Private') ? 'private' : 'group';
                foreach ($cart as $i => $item) {
                    $itemsRelated = $item['related'];
                    if ($item['event_id'] == $events_id) {
                        $sameInCart = true;
                    }
                    if (in_array($events_id, $itemsRelated)) {
                        $existRelatedInCart = true;
                    }
                }
                if ($existRelatedInCart && $type == 'group' && !$sameInCart) {
                    $discount_bundle_tour_percent = $this->discountRelatedTour;
                }
            }

            //for take walks. Fake that all of the tours have a default currency of the db currency
            if($this->config->domain == 'takeWalks') {
                $eventCurrency = $this->_getEventCurrency($stage['EventsStage']['events_id']);
                $stage['EventsStage']['adults_price'] = $this->_convertToDbCurrency($eventCurrency, $stage['EventsStage']['adults_price']);
                $stage['EventsStage']['seniors_price'] = $this->_convertToDbCurrency($eventCurrency, $stage['EventsStage']['seniors_price']);
                $stage['EventsStage']['students_price'] = $this->_convertToDbCurrency($eventCurrency, $stage['EventsStage']['students_price']);
                $stage['EventsStage']['children_price'] = $this->_convertToDbCurrency($eventCurrency, $stage['EventsStage']['children_price']);
                $stage['EventsStage']['infants_price'] = $this->_convertToDbCurrency($eventCurrency, $stage['EventsStage']['infants_price']);


                $contentful = new ContentfulWrapper();

                $content = $contentful->getTourById($stage['EventsStage']['events_id']);

                $stage['Event']['name_listing'] = $content['fields']['tourPageTitleShort'];
                $stage['Event']['url_name'] = $contentful->getCitySlugById($content['fields']['tourCity']) . '/' . $content['fields']['tourPageURL'];

//                if($content['fields']['tourPagePhotoGallery']) {
//                    $item['image'] = $this->_contentfulArrToImg($content['fields']['tourPagePhotoGallery'][0]);
//                }

            }

            $adults_price = $stage['EventsStage']['adults_price'] * $this->request->data('adults');
            $seniors_price = $stage['EventsStage']['seniors_price'] * $this->request->data('seniors');
            $students_price = $stage['EventsStage']['students_price'] * $this->request->data('students');
            $children_price = $stage['EventsStage']['children_price'] * $this->request->data('children');
            $infants_price = $stage['EventsStage']['infants_price'] * $this->request->data('infants');

            $price = $adults_price + $seniors_price + $students_price + $children_price + $infants_price;

            $discount_bundle_tour = $price - ( $price * (1 - $discount_bundle_tour_percent / 100 ));

            //convert cart to all currencies
            $ticketTypes = ['adults','seniors','students','children','infants'];
            foreach(ExchangeRate::getExchangeRates() as $curr => $exchangeRate){

                $totalCurrencyPrice = 0;

                //get the price in each currency for ticket type * amount of tickets
                //and get the total
                foreach($ticketTypes as $ticketType){
                    $ticketCurrencyPrice = ExchangeRate::convert($stage['EventsStage'][$ticketType . '_price'],1,0,$curr) * $this->request->data($ticketType);
                    $cart_item_currency[$ticketType . '_price_converted_' . $curr] = $ticketCurrencyPrice;
                    $totalCurrencyPrice += $ticketCurrencyPrice;
                }

                $cart_item_currency['total_price_converted_' . $curr] = $totalCurrencyPrice;


                //bundled tour discount for this currency
                $cart_item_currency['discount_bundle_tour_converted_' . $curr] = $totalCurrencyPrice * ($discount_bundle_tour_percent / 100);


            }


        } else {

            $stageCount = $this->EventsStage->find('first',array(
                'conditions' => array(
                    'EventsStage.events_id' => $events_id,
                    'EventsStage.datetime' => $datetime,
                    'EventsStage.group' => 0,
                )
            ));

            $this->Event->contain(array(
                'EventsImage' => array(
                    'order' => array('image_order' => 'asc')
                )
            ));
            $stage = $this->Event->findById($events_id);
            $stage['Event']['EventsImage'] = $stage['EventsImage'];
            unset($stage['EventsImage']);
            $stage['EventsStage'] = array(
                'id' => $this->EventPrivate->encodeStageId(FALSE, $events_id, $datetime, count($stageCount) + 1),
                'datetime' => $datetime
            );

            $exempt = 2;
            $adults_price = $seniors_price = $students_price = $children_price = $infants_price = 0;

            $ticketTypes = ['adults','seniors','students','children','infants'];
            $ticketCount = [];

            foreach($ticketTypes as $type) {
                //set initial ticket type count
                if(!isset($ticketCount[$type])) $ticketCount[$type] = 0;

                $amount = $this->request->data($type);

                for($i = 0; $i < $amount; $i++) {
                    if($exempt > 0) {
                        $exempt--;
                        continue;
                    }

                    $ticketCount[$type]++;
                    ${"{$type}_price"} += $stage['Event']["private_{$type}_price"];
                }
            }

            //convert cart to all currencies
            foreach(ExchangeRate::getExchangeRates() as $curr => $exchangeRate){

                $totalCurrencyPrice = ExchangeRate::convert($stage['Event']['private_base_price'],1,0,$curr);

                //get the price in each currency for ticket type * amount of tickets
                //and get the total
                foreach($ticketTypes as $ticketType){
                    $ticketCurrencyPrice = ExchangeRate::convert($stage['Event']['private_' .$ticketType . '_price'],1,0,$curr) * $ticketCount[$ticketType];
                    $cart_item_currency[$ticketType . '_price_converted_' . $curr] = $ticketCurrencyPrice;
                    $totalCurrencyPrice += $ticketCurrencyPrice;
                }

                $cart_item_currency['total_price_converted_' . $curr] = $totalCurrencyPrice;


                //bundled tour discount for this currency
                $cart_item_currency['discount_bundle_tour_converted_' . $curr] = $totalCurrencyPrice * ($discount_bundle_tour_percent / 100);


            }


            $price = $stage['Event']['private_base_price'] + $adults_price + $seniors_price + $students_price + $children_price + $infants_price;

        }


        $cart_item = array(
            'event_id' => $stage['Event']['id'],
            'sku' => $stage['Event']['sku'],
            'name' => $stage['Event']['name_listing'] ?: $stage['Event']['name_short'],
            'image' => $stage['Event']['EventsImage'][0]['images_name'],
            'url_name' => $stage['Event']['url_name'],
            'disable_promos' => $stage['Event']['disable_promos'],

            'stage_id' => $stage['EventsStage']['id'],
            'datetime' => $stage['EventsStage']['datetime'],

            'type' => $this->request->data('type'),

            'adults' => $this->request->data('adults') ?: 0,
            'seniors' => $this->request->data('seniors') ?: 0,
            'students' => $this->request->data('students') ?: 0,
            'children' => $this->request->data('children') ?: 0,
            'infants' => $this->request->data('infants') ?: 0,

            'adults_price' => $adults_price,
            'seniors_price' => $seniors_price,
            'students_price' => $students_price,
            'children_price' => $children_price,
            'infants_price' => $infants_price,

            'restrictions_caption' => isset($stage['Event']['restrictions_caption']) ? $stage['Event']['restrictions_caption'] : '',

            'total_price' => $price,
            'charged_usd_amount' => number_format($price * $transaction_exchange, 2, '.', ''),
            'exchange_rate' => $transaction_exchange,
            'related' => $related,
            'discount_bundle_tour_percent' => $discount_bundle_tour_percent,
            'discount_bundle_tour' => $discount_bundle_tour,

            'base_price' => $this->request->data('type') == 'private' ? $stage['Event']['private_base_price'] : 0
        );

        $cart_item = array_merge($cart_item, $cart_item_currency);

        //convert private tour base price
        foreach(ExchangeRate::getExchangeRates() as $name => $exchangeRate){
            //base price
            $cart_item['base_price_' . $name] = exchangeRate::convert($cart_item['base_price'],1,0,$name);
        }





        if(isset($stage['Event']['charity_id']) && $stage['Event']['charity_id']){
            $cart_item['charity_id'] = $stage['Event']['charity_id'];
        }

        $this->Payment->log_process('Add_to_cart',$cart_item);


        $cart = $this->Session->read('shopping_cart') ?: array();

        $this->Session->delete('promo_code');
        foreach($cart as $i => $item) {
            unset($cart[$i]['promo_code']);
            unset($cart[$i]['promo_local']);
        }

        //if this event with the same date exists in the cart already then replace it with this new one
        $updatedEvent = false;
        for($i=0;$i<count($cart);$i++){
            if($cart[$i]['event_id'] == $cart_item['event_id'] && $cart[$i]['stage_id'] == $cart_item['stage_id']){
                array_splice($cart,$i,1);
                $updatedEvent = true;
            }
        }

        $cart[] = $cart_item;
        $this->Session->write('shopping_cart', $cart);

        if($this->request->is('ajax')) {

            $this->response->type('json');
            $this->autoRender = false;
            echo json_encode(array(
                'response' => 'ok',
                'redirect' => '/payment',
                'cart' => $cart,
            ));
        } else {
            return $this->redirect(array('action' => 'payment'));
        }

    }

    /**
     * Add items to cart. Use this method only for event.type == "group". Not for private tours.
     * @param $events_id
     * @param $datetime
     * @param $type
     * @param $item
     * @param $transaction_exchange
     * @param string $event_name
     */
    private function add_item_to_cart($events_id, $datetime, $type, $itemsToAdd, $transaction_exchange, $event_name = "") {
        $related = array();
        $discount_bundle_tour_percent = 0;
        $discount_bundle_tour = 0;
        if($type == 'group') {

            $stage = $this->EventsStage->find('first',array(
                'conditions' => array(
                    'EventsStage.events_id' => $events_id,
                    'EventsStage.datetime' => $datetime,
                    'EventsStage.group' => 1,
                )
            ));
            if(!$stage) {
                $this->Session->setFlash('Please select a valid date and time for '.$event_name, 'FlashMessage/error');
                $this->redirect($this->referer());
            }

            $relatedTours = $this->EventsSuggestion->find('all', array(
                'conditions' => array(
                    'EventsSuggestion.events_id' => $events_id
                ),
                'contain' => array( 'Event' ),
                'order' => 'order'
            ));

            foreach($relatedTours as $i => $relatedTour){
                $related[] = $relatedTour['Event']['id'];
            }

            $totalPax = 0;
            $totalPax += $itemsToAdd['adults'];
            $totalPax +=  $itemsToAdd['seniors'];
            $totalPax +=  $itemsToAdd['students'];
            $totalPax +=  $itemsToAdd['children'];
            $totalPax +=  $itemsToAdd['infants'];

            $remaining = $this->EventsStagePaxRemaining->findById($stage['EventsStage']['id']);
            $remainingPax = $remaining['EventsStagePaxRemaining']['pax_remaining'];
            if($totalPax > $remainingPax) {
                $this->Session->setFlash("Sorry, there are only $remainingPax tickets left for this tour time", 'FlashMessage/error');
                return $this->redirect($this->referer());
            }

            if($this->theme == 'Italy') {
                //get related tours in actual cart, for apply discount for related tour
                $cart = $this->Session->read('shopping_cart') ?: array();
                $existRelatedInCart = false;
                $sameInCart = false;
                $type = ($stage['Event']['group_private'] == 'Private') ? 'private' : 'group';
                foreach ($cart as $i => $item) {
                    $itemsRelated = $item['related'];
                    if ($item['event_id'] == $events_id) {
                        $sameInCart = true;
                    }
                    if (in_array($events_id, $itemsRelated)) {
                        $existRelatedInCart = true;
                    }
                }
                if ($existRelatedInCart && $type == 'group' && !$sameInCart) {
                    $discount_bundle_tour_percent = $this->discountRelatedTour;
                }
            }

            $adults_price = $stage['EventsStage']['adults_price'] * $itemsToAdd['adults'];
            $seniors_price = $stage['EventsStage']['seniors_price'] *  $itemsToAdd['seniors'];
            $students_price = $stage['EventsStage']['students_price'] *  $itemsToAdd['students'];
            $children_price = $stage['EventsStage']['children_price'] *  $itemsToAdd['children'];
            $infants_price = $stage['EventsStage']['infants_price'] *  $itemsToAdd['infants'];

            $price = $adults_price + $seniors_price + $students_price + $children_price + $infants_price;

            $discount_bundle_tour = $price - ( $price * (1 - $discount_bundle_tour_percent / 100 ));

        }
        $cart_item = array(
            'event_id' => $stage['Event']['id'],
            'sku' => $stage['Event']['sku'],
            'name' => $stage['Event']['name_listing'] ?: $stage['Event']['name_short'],
            'url_name' => $stage['Event']['url_name'],
            'disable_promos' => $stage['Event']['disable_promos'],

            'stage_id' => $stage['EventsStage']['id'],
            'datetime' => $stage['EventsStage']['datetime'],

            'type' => $type,

            'adults' => $itemsToAdd['adults'] ?: 0,
            'seniors' =>  $itemsToAdd['seniors'] ?: 0,
            'students' =>  $itemsToAdd['students'] ?: 0,
            'children' =>  $itemsToAdd['children'] ?: 0,
            'infants' =>  $itemsToAdd['infants'] ?: 0,

            'adults_price' => $adults_price,
            'seniors_price' => $seniors_price,
            'students_price' => $students_price,
            'children_price' => $children_price,
            'infants_price' => $infants_price,

            'restrictions_caption' => isset($stage['Event']['restrictions_caption']) ? $stage['Event']['restrictions_caption'] : '',

            'total_price' => $price,
            'charged_usd_amount' => number_format($price * $transaction_exchange, 2, '.', ''),
            'exchange_rate' => $transaction_exchange,
            'related' => $related,
            'discount_bundle_tour_percent' => $discount_bundle_tour_percent,
            'discount_bundle_tour' => $discount_bundle_tour,

            'base_price' => $type == 'private' ? $stage['Event']['private_base_price'] : 0
        );

        //convert cart to all currencies

        $ticketTypes = ['adults','seniors','students','children','infants'];
        foreach(ExchangeRate::getExchangeRates() as $name => $exchangeRate){
            foreach($ticketTypes as $ticketType){
                $cart_item[$ticketType . '_price_converted_' . $name] = ExchangeRate::convert($cart_item[$ticketType . '_price'],1,0,$name);
            }
            $cart_item['total_price_converted_' . $name] = ExchangeRate::convert($cart_item['total_price'],1,0,$name);
            $cart_item['discount_bundle_tour_converted_' . $name] = ExchangeRate::convert($cart_item['discount_bundle_tour'],1,0,$name);

            //base price
            $cart_item['base_price_' . $name] = exchangeRate::convert($cart_item['base_price'],1,0,$name);

        }

        if(isset($stage['Event']['charity_id']) && $stage['Event']['charity_id']){
            $cart_item['charity_id'] = $stage['Event']['charity_id'];
        }

        $this->Payment->log_process('add_item_to_cart',$cart_item);

        $cart = $this->Session->read('shopping_cart') ?: array();

        $this->Session->delete('promo_code');
        foreach($cart as $i => $item) {
            unset($cart[$i]['promo_code']);
            unset($cart[$i]['promo_local']);
        }

        //if this event with the same date exists in the cart already then replace it with this new one
        $updatedEvent = false;
        for($i=0;$i<count($cart);$i++){
            if($cart[$i]['event_id'] == $cart_item['event_id'] && $cart[$i]['stage_id'] == $cart_item['stage_id']){
                array_splice($cart,$i,1);
                $updatedEvent = true;
            }
        }

        $cart[] = $cart_item;

        $this->Session->write('shopping_cart', $cart);


    }

    /**
     * Add items from modal of promotion
     */
    public function add_to_cart_modal() {
        if(!$this->request->is('post')) {
            throw new MethodNotAllowedException('Add to cart must be POST');
        }

        $CurrenciesExchange = $this->CurrenciesExchange->find('list', array(
            'fields' => array('CurrenciesExchange.exchangepair',
                'CurrenciesExchange.adj_rate')));
        $this->set(compact('CurrenciesExchange'));

        $transaction_exchange = $CurrenciesExchange[$this->config->exchangepair];

        //for the donation conversion
        $this->_writeExchangeRateToSession();

        $this->EventsStage->contain(array(
            'Event' => array(
                'EventsImage' => array(
                    'order' => array('image_order' => 'asc')
                )
            )
        ));

        $item = [];
        $item['adults'] = $this->request->data('modal_adults');
        $item['seniors'] = $this->request->data('modal_seniors');
        $item['students'] = $this->request->data('modal_students');
        $item['children'] = $this->request->data('modal_children');
        $item['infants'] = $this->request->data('modal_infants');
        $type = $this->request->data('modal_type');
        $redirect =  $this->request->data('modal_redirect');
        //for the main event
        $datetime = $this->request->data('date') . ' ' . $this->request->data('time');
        $events_id = $this->request->data('event_id');
        $event_name = $this->request->data('event_name');
        $this->add_item_to_cart($events_id, $datetime, $type, $item, $transaction_exchange, $event_name);
        if ( strlen($redirect) == 0 ){
            //for the promo event
            $datetime = $this->request->data('modal_date_time');
            $events_id = $this->request->data('modal_event_id');
            $discount = $this->request->data('modal_discount');
            $event_name = $this->request->data('modal_event_name');
            $this->add_item_to_cart($events_id, $datetime, $type, $item, $transaction_exchange, $event_name);
            $redirect = array('action' => 'payment') ;
        }
        return $this->redirect($redirect);

    }

    public function home() {
        $this->theme = $this->theme == 'nyc' ? 'nyc2' : $this->theme;

        $this->set('js', array('home', 'lib/underscore'));
        $this->set('css', array('home'));
        $this->set('canonicalURL',FULL_BASE_URL);
        $instagramCount = 42;

        switch($this->theme){
            case 'Italy':
                $this->set('layoutTitle', 'Tours of Rome, Venice, Florence &amp; More | Walks of Italy');
                $this->set('meta_description', "Recommended by Rick Steves, Fodor's, DK Travel & more; Walks of Italy offers small group tours and exclusive access. Take a walk with us!");

//                $this->set('instagram',ClassRegistry::init('Instagram')->find('all',array(
//                    'limit' => 42,
//                    'order' => 'rand()'
//                )));

                $blog_posts = $this->WpPost->getFeaturedPosts(5);

                //criteo oneTag is only for WalksOfItaly
                $this->set('ecViewHomePage', true);

                break;
            case 'ItalyEs':
                $this->set('layoutTitle', 'Walks of Italy | Tours en Roma, Florencia, Venecia, y Más!');
                $this->set('meta_description', "Recomendado por Rick Steves, Fodor's DK Travel y más; Walks of Italy ofrece tours de grupos pequeños con acceso exclusivo. ¡Ven a andar con nosotros!");


                $blog_posts = $this->WpPost->getFeaturedPosts(5);

                $ecViewProductList = array(
                    'list' => null,
                    'events' => null
                );
                $this->set('ecViewProductList', $ecViewProductList);

                break;
            case 'nyc':
            case 'nyc2':
                $this->set('layoutTitle', 'New York Tours &amp; Activities | Walks of New York');
                $this->set('meta_description', 'Small group New York tours for every taste & budget – from Metropolitan Museum tours to Broadway & the official Mario Batali Greenwich Village Food tour.');
                $instagramCount = 14;

                //get tweets
                $connection = new Abraham\TwitterOAuth\TwitterOAuth('zkkBw82xlRXx6XggbkJYLaPS5','u6iDo2akfOR5UKysF6xfLRMhExgVUxnxUc62SsQUcBulsoNgb8','14434626-TepIElV4jqHUtEPwSEH2kKRT47sLPx73glqv28Pvi','GMVvbq7IvMifMJfwJSMKdbNu6qPh9D2jGAJEtV1AxVIsF');
                $tweets = $connection->get("statuses/user_timeline", ["count" => 3, "screen_name" => 'WalksofNewYork']);
                $this->set('tweets', $tweets);
                $blog_posts = $this->WpPost->getRecentPosts();

                //get the tours
                $homeEvents = array_reduce($this->Event->find('all',[
                    'conditions' => [
                        'id' => [1130,1133,1144,1121,1134,1143,1145,1146]
                    ],
                    'contain' => ['EventsImage']
                ]),function($all, $event){
                    $all[$event['Event']['id']] = $event;
                    return $all;
                },[]);

                $this->set('homeEvents',$homeEvents);

                break;
            case 'Turkey':
                $this->set('layoutTitle', 'Istanbul Tours & Bosphorus Cruises | Walks of Turkey');
                $this->set('meta_description', 'Small group Istanbul tours, food tours & Bosphorus cruises: Guaranteed small groups (max 15 people), expert guides & all-inclusive prices.');

                $blog_posts = $this->WpPost->getFeaturedPosts(5);
                break;

        }
        $this->set('instagramCount', $instagramCount);

        $instagram = [];
        try {
            // this value must be updated debugging https://www.instagram.com/explore/tags/takewalks/ after hit button "Load More",
            // look for XHR to https://www.instagram.com/graphql/query/?query_id=17882293912014529&variables=....
            // This value is "hardcoded" in instagram's dynamic javascript file as a parameter 'queryId'.
            // Latest source: https://www.instagram.com/static/bundles/en_US_Commons.js/135da004852d.js
            // TODO: figure out how to predict or catch that value from server
            $instagramQueryId = "17882293912014529";
            $instagramCacheKey = 'instagram_feed_'.$instagramQueryId.'_'.$instagramCount;
            if( !($instagram = Cache::read($instagramCacheKey)) ) {
                $instagramTagName = "takewalks";
                $url = "https://www.instagram.com/graphql/query/?query_id=$instagramQueryId&tag_name=$instagramTagName&first=$instagramCount";
                $process = curl_init($url);
                curl_setopt($process, CURLOPT_TIMEOUT, 30);
                curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
                $json = json_decode( curl_exec($process) );
                $info = curl_getinfo($process);
                curl_close($process);
                if ($info['http_code'] == 200 && isset($json->data->hashtag->edge_hashtag_to_media->edges) ) {
                    $instagramData = $json->data->hashtag->edge_hashtag_to_media->edges;
                    foreach ($instagramData as $item) {
                        $element = new stdClass();
                        $element->display_src = $item->node->display_url;
                        $element->thumbnail_src = $item->node->thumbnail_src;
                        $instagram[] = $element;
                    }
                    Cache::set( ['duration' => '+10 minutes'] );
                    Cache::write($instagramCacheKey, $instagram);
                }
            }

        } catch(Exception $ex) {}

        $this->set('instagram', $instagram);

        $num_results = $this->Event->find('count',array(
            'conditions' => array(
                'EventsDomain.domains_id' => $this->config->domainId,
                'Event.visible' => 1,
            )
        ));
        $heroLocations = $this->DomainsGroup->find('all', array(
            'conditions' => array(
                'url_name' => array('paris','rome', 'florence-pisa', 'venice', 'milan')
            ),
            'order' => 'display_order'
        ));



        $this->set(compact('num_results', 'blog_posts', 'heroLocations'));
        $this->set('currency', array(
            'symbol' => ExchangeRate::getSymbol(),
            'exchangeRate' => ExchangeRate::getExchangeRate()
        ));
    }

    public function contact() {
        $this->set('css', array('contact', 'static'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'contact');
        $blog_posts = $this->WpPost->getRecentPosts();
        $message = $this->request->data('message');
        $message = empty($message);
        if ($this->request->is('post') && !$message) {
            $this->Email = ClassRegistry::init('Email');
            $this->Email->sendContactEmail($this->request->data);
            $this->Session->setFlash('Successfully sent.  Thank you for your message!', 'FlashMessage/status');
        }
        $this->set(compact('blog_posts'));

        switch($this->theme){
            case 'ItalyEs':
                $this->set('layoutTitle', 'Contáctenos');
                break;
            case 'Turkey':
                $this->set('layoutTitle', 'Contact Us: Phone & Email | Walks of Turkey');
                $this->set('meta_description', 'Walks of Turkey phone numbers (Istanbul & US), email addresses and customer service office opening hours. Get in touch!');
                break;
            default:
                $this->set('layoutTitle', 'Contact');
                break;


        }

    }

    public function faq() {
        $this->set('css', array('faq', 'static'));
        $this->set('js', array('faq'));

        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'faq');
        switch($this->theme){

            case 'Turkey':
                $this->set('layoutTitle', 'Frequently Asked Questions | Walks of Turkey');
                $this->set('meta_description', 'Frequently asked questions about Walks of Turkey tours & Istanbul: Dress codes, meeting points, allergies, cancellation policy etc.');
                break;
            default:
                $this->set('layoutTitle', 'FAQ');
                break;


        }

    }

    public function about() {
        $this->set('css', array('about', 'static'));
        $this->set('canonicalURL', FULL_BASE_URL . DS . 'about');
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));


        switch ($this->theme) {
            case 'ItalyEs':
                $this->set('layoutTitle', 'Conócenos');
                break;
            default:
                $this->set('layoutTitle', 'About Us');
                break;
        }
    }
    public function terms() {
        $this->set('css', array('static'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'terms');

        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));

        switch($this->theme){

            case 'Turkey':
                $this->set('layoutTitle', 'Terms & Conditions | Walks of Turkey');
                $this->set('meta_description', 'Terms & Conditions of Walks of Turkey services and site usage: liability, insurance, refunds etc.');
                break;
            case 'ItalyEs':
                $this->set('layoutTitle','Términos y Condiciones | Walks of Italy');
                $this->set('meta_description', 'Términos y condiciones para la compra de un tour en Italia con Walks of Italy o Walks LLC');
                break;
            default:
                $this->set('layoutTitle', 'Terms and Conditions');
                break;


        }

    }
    public function cancellations() {
        $this->set('css', array('static'));
        $this->set('layoutTitle', 'Cancellations');
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'cancellations');
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
    }
    public function privacy() {
        $this->set('css', array('static'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'privacy');
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));

        switch($this->theme){

            case 'Turkey':
                $this->set('layoutTitle', 'Privacy Policy | Walks of Turkey');
                $this->set('meta_description', 'Walks of Turkey Privacy Policy: How we use and protect your personal information and cookies. We will never sell or trade your information.');
                break;
            default:
                $this->set('layoutTitle', 'Privacy');
                break;


        }

    }
    public function press() {
        $this->set('css', array('press', 'static'));
        $this->set('layoutTitle', 'Press');
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
    }

    public function apply_promo() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        //save the payment form to fill it back in later
        if($this->request->data('infoBlob')){
            $this->Session->write('infoBlob', $this->request->data('infoBlob'));
        }


        $cart = $this->Session->read('shopping_cart');
        $promo_code = $this->request->data('promo');

        //remove promo code from URL
        if( $this->Session->check('promoCodeUrl') ) {
            $this->Session->delete('promoCodeUrl');
        }

        //Don't allow 10ricksteves promo code in takewalks site ONLY
        if(strtolower($promo_code) == '10ricksteves' && TAKE_WALKS_SITE){
            $calculatePromo = false;
        }else{

            //check for promo verification questions
            $promo = $this->BookingsPromo->find('first',array(
                'conditions' => array('promo_code' => $promo_code),
                'contains' => array('BookingsPromosBook' => array('BookingsPromosQuestion')),
                'recursive' => 2
            ));

            //if promo exists and has questions
            if($promo && $promo['BookingsPromosBook']){
                //if the question has been answered

                if($question = $this->BookingsPromosQuestion->findById($this->request->data('question_id'))){
                    //check the answer
                    if(strtolower($this->request->data('answer')) != strtolower($question['BookingsPromosQuestion']['answer'])){
                        $this->Session->setFlash('Discount code answer is incorrect', 'FlashMessage/error');
                        return $this->redirect(array('action' => 'payment'));
                    }
                } else{
                    //post data of previos apply form, sent to verify promo
                    $postDataPromoCode = array(
                        'first_name' => $this->request->data('first_name'),
                        'last_name' => $this->request->data('last_name'),
                        'email' => $this->request->data('email'),
                        'confirm_email' => $this->request->data('confirm_email'),
                        'street_address' => $this->request->data('street_address'),
                        'city' => $this->request->data('city'),
                        'state' => $this->request->data('state'),
                        'zip' => $this->request->data('zip'),
                        'country' => $this->request->data('country'),
                        'phone_number' => $this->request->data('phone_number'),
                        'ccFirstName' => $this->request->data('ccFirstName'),
                        'ccLastName' => $this->request->data('ccLastName'),
                        'conditions' => $this->request->data('conditions')
                    );
                    $this->Session->write('postDataPromoCode', $postDataPromoCode);

                    $this->redirect(array('controller'=> null, 'action' => 'verify-promo',$promo_code));
                }

            }

            $calculatePromo = $this->BookingsPromo->calculatePromos($cart, $promo_code);
        }

        $this->Session->delete('promo_code');
        $invalidPromo = 'Invalid Promo Code';
        $validPromo = 'Promo code successfully applied.';
        if($this->theme == 'ItalyEs'){
            $invalidPromo = 'Código promocional no válido.';
            $validPromo = 'Código promocional aplicado exitosamente!';
        }

        if(CakeSession::check('promo_disable_tour_name_excluded')) {
            $promoDisableTourNameExcluded = CakeSession::read('promo_disable_tour_name_excluded');
            // set invalid promo code message
            if( count($promoDisableTourNameExcluded) > 0 ) {
                $tourNames = implode(', ', $promoDisableTourNameExcluded);

                if (strpos(strtolower($tourNames), "tour") === false) {
                    $tourNames .= ' tour';
                }

                $promoDisabled = '<br>Unfortunately, due to high demand and running costs, '.$tourNames.' cannot be purchased at a discounted rate. Promo codes are therefore invalid on this specific tour.';
                $invalidPromo = $invalidPromo . ' ' . $promoDisabled;
                $validPromo = $validPromo . ' ' . $promoDisabled;
                CakeSession::delete('promo_disable_tour_name_excluded');
            }
        }

        if($calculatePromo === false || $calculatePromo['valid'] === false) {
            $this->Session->setFlash($invalidPromo, 'FlashMessage/error');
        }
        else {
            $this->Session->write('shopping_cart', $calculatePromo['newCart']);
            $this->Session->write('promo_code', $promo_code);
            $this->Session->setFlash($validPromo, 'FlashMessage/status');
        }

        return $this->redirect(array('action' => 'payment'));
    }

    /**
     * This method do not support promo code with questions
     *
     * @return bool
     */
    private function _apply_promo_url() {

        if( !$this->Session->check('promoCodeUrl') ) {
            return false;
        }

        $cart = $this->Session->read('shopping_cart');
        $promo_code = $this->Session->read('promoCodeUrl');

        //check for promo verification questions
        $promo = $this->BookingsPromo->find('first',array(
            'conditions' => array('promo_code' => $promo_code),
            'contains' => array('BookingsPromosBook' => array('BookingsPromosQuestion')),
            'recursive' => 2
        ));

        //if promo exists and has questions
        if(!$promo || $promo['BookingsPromosBook']){
            $this->Session->delete('promoCodeUrl');
            return false;
        }

        $calculatePromo = $this->BookingsPromo->calculatePromos($cart, $promo_code);
        $this->Session->write('shopping_cart', $calculatePromo['newCart']);
        $this->Session->delete('promo_code');
        $invalidPromo = 'Invalid Promo Code';
        $validPromo = 'Promo code successfully applied.';
        if($this->theme == 'ItalyEs'){
            $invalidPromo = 'Código promocional no válido.';
            $validPromo = 'Código promocional aplicado exitosamente!';
        }

        if($calculatePromo['valid'] !== false) {
            $this->Session->write('promo_code', $promo_code);
        }
    }

    public function verify_promo($promoCode){
        $this->set('css', array('verify_promo'));
        $this->set('js', array('verify_promo'));
        $this->set('layoutTitle', 'Verify your promo code');

        $promo = $this->BookingsPromo->find('first',array(
            'conditions' => array('promo_code' => $promoCode),
            'recursive' => 2
        ));

        //check that this promo code exists and has to be verified
        if(!$promo || !$promo['BookingsPromosBook']){
            return $this->redirect(array('action' => 'payment'));
        }

        $books = array();
        foreach($promo['BookingsPromosBook'] as $promoBook){

            $book = array(
                'name' => $promoBook['book_name'],
                'questions' => array()
            );

            foreach($promoBook['BookingsPromosQuestion'] as $question){
                $book['questions'][] = array(
                    'question' => $question['question'],
                    'id' => $question['id']
                );
            }

            $books[] = $book;
        }
        $postDataPromoCode = $this->Session->read('postDataPromoCode');
        $this->set('postDataPromoCode',$postDataPromoCode);
        $this->set('promoCode',$promoCode);
        $this->set('initValues', $books);

    }

    public function translateMessage($message){
        $spanish = array(
            'Payment error: ' => 'Error de pago: ',
            'Invalid credit card number.' => "Número de tarjeta de crédito inválido.",
            'Your credit card has expired.' => 'Su tarjeta de credito ha caducado.',
            'Invalid credit card security code.' => 'Codigo de seguridad de tarjeta de credito no valido.',
            'Invalid credit card type.' => 'Tipo de tarjeta de credito no valido.',
            'Invalid credit card expiration date.' => 'Fecha de expiracion de tarjeta de credito no valido.',
            'Credit card is over limit' => 'Tarjeta de credito supero el limite.',
            'Insufficient funds.' => 'Fondos insuficientes.',
            'Account closed' => 'Cuenta cerrada.',
            'Account Frozen' => 'Cuenta congelada.',
            'No address supplied' => 'No se tiene direccion.',
            'Internal error. Please try again later' => 'Error interno. Intentelo de nuevo mas tarde.',
            'Credit card is invalid.' => "Tarjeta de crédito inválida.",
            'The security code is invalid' => 'Codigo de seguridad no valido.',
            "The card issuer is unable to verify your card's security code." => 'No se puede verificar el codigo de seguridad de la tarjeta.'
        );
        switch($this->theme){
            case 'ItalyEs':
                if(array_key_exists($message, $spanish)){
                    return $spanish[$message];
                } else {
                    return $message;
                }
                break;
            default:
                return $message;
                break;

        }
    }

    public function payment() {

        CakeLog::write('debug', "PagesController -> payment ");

        $options = array();
        $this->_apply_promo_url();

        // Refresh exchange rates to prevent stale currency conversions
        ExchangeRate::init($this->config->defaultCurrency, $this->config->dbCurrency, true);

        //Load promo code discount fixed for total
        $promo_discount_fixed_total = ($this->Session->read('promo_discount_fixed_total')) ? $this->Session->read('promo_discount_fixed_total') : null;
        $this->set('promo_discount_fixed_total', $promo_discount_fixed_total);

        // clear information from promo code
        $isPostPromoCode = false;
        if($this->Session->check('postDataPromoCode')) {
            $isPostPromoCode = true;
            $this->Session->delete('postDataPromoCode');
        }

        $validEmail = true;
        if($this->request->is('post') && isset($this->request->data['email']) && !$isPostPromoCode) {
            //make sure user has items in their cart
            if(!CakeSession::check('shopping_cart') || count(CakeSession::read('shopping_cart')) == 0){
                return $this->redirect(array('action' => 'home'));
            }
            $this->request->data['email'] = trim($this->request->data['email']);
            if(!$this->Client->validEmail($this->request->data)) {
                $this->Session->setFlash('That email is not valid, please try again.', 'FlashMessage/error');
                $validEmail = false;
            }
        }
        if ($this->request->is('post') && isset($this->request->data['email']) && $validEmail && !$isPostPromoCode) {

            $currency = $this->request->data('currency');
            $accounts = $this->Payment->getAccounts();

            //figure out the currency situation
            if(!$currency || !isset($accounts[$currency])){
                //if trying to use a nonexistent currency then use the default db currency
                $currency = isset($accounts[$this->config->dbCurrency]) ? $this->config->dbCurrency : 'USD';
            }
            $this->request->data['currency'] = $currency;
            ExchangeRate::setCurrency($currency);

            //create the client array
            $client = array(
                'fname' => $this->request->data('first_name'),
                'lname' => $this->request->data('last_name'),
                'address' => $this->request->data('street_address'),
                'city' => $this->request->data('city'),
                'state' => $this->request->data('state'),
                'zip' => $this->request->data('zip'),
                'mobile_number' => $this->request->data('phone_number'),
                'guest' => 1
            );

            $agent = $this->Agent->findByEmailAddress($this->request->data('email'));
            if ($agent) {
                $client['agents_id'] = $agent['Agent']['id'];
                $client['email'] = 'TravelAgent-' . strtotime('now') . '@walks.org';
            } else if ($this->Auth->loggedIn()) {
                $client['id'] = $this->Auth->user('id');
                $client['email'] = trim($this->request->data('email'));
            } else {
                $checkClient = $this->Client->find('first', array(
                    'conditions' => array(
                        'email' => $this->request->data('email')
                    ),
                    'contain' => array()
                ));
                if (!empty($checkClient)) {
                    $client = $checkClient['Client'];
                } else {
                    $client['email'] = $this->request->data('email');
                }
            }

            //add iata number
	    if (!isset($client['iata'])){
		$client['iata'] = null;
	    }
            $client['iata'] = $this->request->data('iata') ? $this->request->data('iata') : $client['iata'];

            //only nyc has audience rewards
            if($this->request->data('audience_reward')) $client['audience_reward'] = $this->request->data('audience_reward');

            $this->Client->save(array('Client' => $client), false);
            $client_id = $this->Client->id;

            $cart = $this->Session->read('shopping_cart');

            //check if there are enough tickets
            //and update the total amount of tickets sold.
            foreach($cart as $n => $item) {
                if($item['type']== 'group'){
                    $paxTotal = 0;
                    $stage = $this->EventsStage->findById($item['stage_id']);

                    foreach(array('adults', 'seniors', 'students', 'children', 'infants') as $type) {
                        $paxTotal += $item[$type];
                        $stage['EventsStage']["pax_$type"] += $item[$type];
                    }

                    $remaining = $this->EventsStagePaxRemaining->findById($stage['EventsStage']['id']);
                    $remainingPax = $remaining['EventsStagePaxRemaining']['pax_remaining'];
                    if($paxTotal > $remainingPax) {
                        $this->Session->setFlash('There are not enough tickets for "'.$item['name'].'" at this time. This tour was removed from your cart.', 'FlashMessage/error');
                        $this->remove_from_cart($n, false);
                        $this->redirect(array('action' => 'home'));
                    }

                    $stage['EventsStage']['pax_total'] += $paxTotal;
                }
            }


            //get the total cart prices
            $cartPriceTotalUsd = 0;
            $cartPriceTotalLocal = 0;
            $cartPriceTotalConverted = 0;
            $discount = 1;
            foreach($cart as &$tour){
                $subtotalLocal = $tour['base_price_' . $this->config->dbCurrency];
                $subtotalUsd = $tour['base_price_USD'];
                $subtotalConverted = $tour['base_price_' . $currency];

                //add up all of the tickets to get the subtotal
                foreach($this->ticketTypes as $ticketType){
                    $subtotalLocal += $tour[$ticketType . '_price_converted_' . $this->config->dbCurrency];
                    $subtotalConverted += $tour[$ticketType . '_price_converted_' . $currency];
                    $subtotalUsd += $tour[$ticketType . '_price_converted_USD'];

                    //create charged amount
                    $tour[$ticketType . '_price_charged'] = $tour[$ticketType . '_price_converted_' . $currency];

                }

                //apply discount if exists
                $discount = isset($tour['promo_discount_percentage']) ? 1 - ($tour['promo_discount_percentage'] / 100) : 1;

                //assign to sessions subtotal
                $tour['subtotal_usd'] = $subtotalUsd;
                $tour['subtotal_local'] = $subtotalLocal;
                $tour['subtotal_converted'] = $subtotalConverted;

                //apply bundle tour discount if exists
                if ($tour['discount_bundle_tour_percent'] != 0){
                    $discountBundleTour = 1 - ($tour['discount_bundle_tour_percent'] / 100);
                    //apply discount
                    $subtotalUsd = round($subtotalUsd * $discountBundleTour,2);
                    $subtotalLocal = round($subtotalLocal * $discountBundleTour,2);
                    $subtotalConverted = round($subtotalConverted * $discountBundleTour,2);
                }

                //apply promo code discount
                if ( isset($tour['promo_discount_fixed']) || isset($tour['promo_discount_fixed_by_event']) ){

                    $subtotalLocal = $subtotalLocal - $tour['promo_discount_fixed_' . $this->config->dbCurrency];
                    $subtotalLocal = ($subtotalLocal < 0) ? 0 : round($subtotalLocal, 2);

                    $subtotalUsd = $subtotalUsd - $tour['promo_discount_fixed_USD'];
                    $subtotalUsd = ($subtotalUsd < 0) ? 0 : round($subtotalUsd, 2);

                    $subtotalConverted = $subtotalConverted - $tour['promo_discount_fixed_' . $currency];
                    $subtotalConverted = ($subtotalConverted < 0) ? 0 : round($subtotalConverted, 2);

                } else if ( isset($tour['promo_discount_percentage']) ){
                    $subtotalUsd = round($subtotalUsd * $discount,2);
                    $subtotalLocal = round($subtotalLocal * $discount,2);
                    $subtotalConverted = round($subtotalConverted * $discount,2);
                }

                //add to total
                $cartPriceTotalUsd += $subtotalUsd;
                $cartPriceTotalLocal += $subtotalLocal;
                $cartPriceTotalConverted += $subtotalConverted;

                //for later processing
                $tour['charged_usd_amount'] = $subtotalUsd;
                $tour['charged_local_amount'] = $subtotalLocal;
                $tour['charged_amount'] = $subtotalConverted;

            }

            //apply promo code discount fixed total
            if ($promo_discount_fixed_total) {
                $cartPriceTotalLocal = $cartPriceTotalLocal - $promo_discount_fixed_total[$this->config->dbCurrency];
                $cartPriceTotalLocal = ($cartPriceTotalLocal < 0) ? 0 : round($cartPriceTotalLocal, 2);

                $cartPriceTotalUsd = $cartPriceTotalUsd - $promo_discount_fixed_total['USD'];
                $cartPriceTotalUsd = ($cartPriceTotalUsd < 0) ? 0 : round($cartPriceTotalUsd, 2);

                $cartPriceTotalConverted = $cartPriceTotalConverted - $promo_discount_fixed_total[$currency];
                $cartPriceTotalConverted = ($cartPriceTotalConverted < 0) ? 0 : round($cartPriceTotalConverted, 2);

            }

            $cartPriceTotalUsd = round($cartPriceTotalUsd,2);
            $cartPriceTotalLocal = round($cartPriceTotalLocal,2);
            $cartPriceTotalConverted = round($cartPriceTotalConverted,2);

            //show the total price
            $totalPrice = array(
                'usd' => $cartPriceTotalUsd,
                'local' => $cartPriceTotalLocal,
                'converted' => $cartPriceTotalConverted
            );

            //update session with new values
            CakeSession::write('shopping_cart',$cart);
            CakeSession::write('totalPrice',$totalPrice);



            //convert price to different currency
//            $cart_price_total_converted;

            //save the booking
            $this->Booking->save(array(
                'Booking' => array(
                    'clients_id' => $client_id,
                    'amount_local' => $cartPriceTotalLocal,
                    'amount_merchant' => $cartPriceTotalUsd,
                    'currencies_id' => 1
                )
            ));
            $booking_id = $this->Booking->id;
            $this->set(compact('booking_id'));

            $promoCodeApplied = ($this->Session->read('promo_code_applied')) ? $this->Session->read('promo_code_applied') : false;
            $response = [];
            $response['message'] = 'We could not process this transaction. Please try again.';

            //sanitize credit card data
            if (isset($this->request->data['ccNo'])){
                $this->request->data['ccNo'] = preg_replace("/[^0-9]/","",$this->request->data['ccNo']);
            }

            //get email
            $this->request->data['email'] = $client['email'];


            //get the donation information
            $donations = array();
            $donationError = false;
            if(isset($this->request->data['charity_id'])){
                if(count($this->request->data['charity_id']) != count($this->request->data['donation_amount'])){
                    $donationError = true;
                }else{
                    //put the donations into the donations array
                    for($i=0;$i<count($this->request->data['charity_id']);$i++){
                        $donations[$this->request->data['charity_id'][$i]] = $this->request->data['donation_amount'][$i];
                    }
                }
            }
            if($donationError){ $response['message'] = 'We could not process your donation. Please try again.'; }


            //process the payment if there are no errors
            $zeroPrice = $cartPriceTotalUsd == 0;
            
            // TODO: Fix all of this. -dla

            //            if (true) {
            if ($zeroPrice && $promoCodeApplied) {
                $zeroPrice = false;
                $response = [
                    'success' => true,
                    'message' => '',
                    'transactionId' => strtoupper(md5(time())),
                    'authcode' => '',
                    'merchants_id' => '',
                    'payment_status' => ''
                ];
            } else if(!$donationError &&  !$zeroPrice) {
                $this->Payment = ClassRegistry::init('Payment');

                $data = $this->request->data;
                $data['clients_id'] = $client_id;
                $data['amount_local'] = $cartPriceTotalLocal;
                $data['exchange_rate'] = ExchangeRate::getExchangeRate($this->config->dbCurrency) / ExchangeRate::getExchangeRate($currency);
                $data['exchange_from'] = $this->config->dbCurrency;
                $data['exchange_to'] = $currency;

                //check if gift card was used
                if($this->Session->read('promo_code')){
                    $this->loadModel('BookingsPromo');
                    $giftCard = $this->BookingsPromo->findByPromoCode($this->Session->read('promo_code'));
                    if($giftCard['BookingsPromo']['promo_name'] == 'Gift Card'){
                        $options['giftCard'] = $giftCard['BookingsPromo']['promo_code'];
                    }

                }

                // ##########################################################################
                // Payment->processPayment
                // ##########################################################################
                CakeLog::write('debug', "PagesController -> payment processPayment called ");

                $response = $this->Payment->processPayment($data, $cartPriceTotalConverted, $booking_id, $this->config->domain, $donations, $options);

                CakeLog::write('debug', "PagesController -> payment processPayment response ".print_r($response, true));

                $this->Payment->log_process('payment_response', array(
                    'orbital_response' => $response,
                    'total_price_total' => $cartPriceTotalUsd,
                    'total_price_total_local' => $cartPriceTotalLocal
                ));
            }

            //process payment response
            if($donationError || $zeroPrice || !$response['success']) {
                $this->Session->setFlash($this->translateMessage('Payment error: ') . $this->translateMessage($response['message']), 'FlashMessage/error');
            
            } else {
                //transaction id
                $transactionId = $zeroPrice ? $booking_id : $response['authcode'];

                //write all of the info in the session
                $this->Session->write('transaction_id', $transactionId);
                $this->Session->write('booking_id', $booking_id);
                $this->Session->write('travellerInfo', $this->request->data);

                $grossTotalPrice = 0;

                //log booking details in bookings details submit table
                CakeLog::write('debug', "PagesController -> post payment update cart ");

                foreach($cart as $item) {
                    $this->log('PagesController '. $item['stage_id']);
                    $item['bookings_id'] = $booking_id;
                    $item['is_paid'] = !$zeroPrice;
                    $item['comment'] = $this->request->data("restrictions.{$item['event_id']}");
                    $item['transaction_id'] = $transactionId;
                    $item['amount_local'] = $item['charged_local_amount'];
                    $item['local_usd_rate'] = 1 / ExchangeRate::getExchangeRate($this->config->dbCurrency);
                    $item['exchange_from'] = $this->config->dbCurrency;
                    $item['exchange_to'] = ExchangeRate::getCurrency();
                    $item['exchange_rate'] = ExchangeRate::getExchangeRate($this->config->dbCurrency) / ExchangeRate::getExchangeRate($currency);
                    $this->Payment->log_process('purchased_item',$item);
                    $this->BookingsDetailsSubmit->saveBookingDetails($item);

                    $grossTotalPrice += $item['charged_local_amount'];
                }

                //if gift card then subtract amount
                if($this->Session->read('promo_code')){
                    $this->loadModel('BookingsPromo');
                    $giftCard = $this->BookingsPromo->findByPromoCode($this->Session->read('promo_code'));
                    if($giftCard['BookingsPromo']['promo_name'] == 'Gift Card'){
                        $giftCard['BookingsPromo']['discount_amount'] -= ($giftCard['BookingsPromo']['discount_amount'] > $grossTotalPrice) ? $grossTotalPrice : $giftCard['BookingsPromo']['discount_amount'];
                        $this->BookingsPromo->save($giftCard);
                    }

                }

                //log payment transaction
                $transactionAdd['PaymentTransaction'] = array(
                    'amount_local' => $cartPriceTotalLocal,
                    'payment_amount' => $cartPriceTotalConverted,
                    'transaction_date' => date("Y-m-d H:i:s"),
                    'merchant_trans_number' => $response['authcode'],
                    'TxRefNum' => $response['transactionId'],
                    'merchants_id' => $response['merchants_id'],
                    'merchant_result' => $response['success'],
                    'payment_status' => $response['payment_status'],
                    'clients_id' => $client_id, // good
                    'exchange_rate' => ExchangeRate::getExchangeRate($this->config->dbCurrency) / ExchangeRate::getExchangeRate($currency),
                    'booking_id' => $booking_id,
                    'exchange_from' => $this->config->dbCurrency,
                    'exchange_to' => $currency
                );
                $this->PaymentTransaction->save($transactionAdd);

                //save the transaction info and the bookings id to the booking
                $this->BookingsDetail->updateAll(
                    array('payment_transaction_number' => $this->PaymentTransaction->id),
                    array('bookings_id' => $booking_id)
                );


                //save all of the charity donations
                $charitiesDonatedTo = array();
                if($donations){
                    //get all of the charity names
                    $charities = $this->Charity->find('list',array('fields' => array('id','charity_name')));
                    foreach($donations as $charity_id => $donation_amount){
                        if($donation_amount > 0){
                            $this->CharitiesDonation->create();
                            $this->CharitiesDonation->save(array(
                                'charity_id' => $charity_id,
                                'amount_local' => $donation_amount,
                                'booking_id' => $booking_id,
                                'exchange_rate' => CakeSession::read('exchangeRate'),
                                'charged_usd_amount' => $donation_amount * CakeSession::read('exchangeRate')
                            ));
                            //list all of the charities donated to for the confirm page
                            $charitiesDonatedTo[$charities[$charity_id]] = $donation_amount;
                        }
                    }
                }

                //remove promo code from URL
                if( $this->Session->check('promoCodeUrl') ) {
                    $this->Session->delete('promoCodeUrl');
                }

                CakeSession::write('charitiesDonatedTo', $charitiesDonatedTo);


                // For TakeWalks: When a logged user submits the payment form, only update the user's info
                // in the user API if the booking is successful.

                if( $this->config->domain == 'takeWalks'
                    && !empty($this->Auth->user())
                ){
                    $this->UserApi->userUpdate($this->Auth->user('id'), trim($client['fname']), trim($client['lname']), '', trim($client['mobile_number']), '');
                }

                CakeLog::write('debug', "PagesController -> payment return from success ");

                return $this->redirect(array('action' => 'confirm'));
            } // end $response['success']

        } // end $this->request->is('post') etc....
        // ---------------------------------------------------------------------------

        if(!$this->Session->read('shopping_cart')) {
            //return $this->redirect(array('action' => 'home'));
        }

        if($this->Session->check('infoBlob')) {
            parse_str($this->Session->read('infoBlob'), $_POST);
            $this->Session->delete('infoBlob');
        }

        if($promo_code = $this->Session->read('promo_code')) {
            $this->set(compact('promo_code'));
        }


        $this->set('css', array('payment'));
        $this->set('js', array('lib/creditcard', 'payment', 'lib/jquery.validate'));



        switch($this->theme){

            case 'Turkey':
                $this->set('layoutTitle', 'Shopping Cart & Payment | Walks of Turkey');
                $this->set('meta_description', 'Input your details, add and remove things from cart and confirm your Walks of Turkey booking!');
                break;
            case 'Italy':


                $this->set('layoutTitle', 'Payment');
                break;
            default:
                $this->set('layoutTitle', 'Payment');
                break;


        }

        //add all of the charities associated with the tours
        $charities = array();
        if($this->Session->read('shopping_cart')){
            $this->Charity = ClassRegistry::init('Charity');
            foreach(CakeSession::read('shopping_cart') as $item){
                if(isset($item['charity_id']) && !isset($charities[$item['charity_id']])){
                    $charities[$item['charity_id']] = $this->Charity->findById($item['charity_id']);
                }
            }
        }
        $this->set('charities', array_values($charities));

        //set the initValues

        $initValues['promo_discount_fixed_total'] = $promo_discount_fixed_total;
        //make the cart
        $initValues['cart'] = array();
        if (!is_null($this->Session->read('shopping_cart'))){
            $initValues['cart'] = array_reduce($this->Session->read('shopping_cart'), function($all, $tour){

                $tickets = array();
                $discountFixed = array();
                $promo_discount_fixed = isset($tour['promo_discount_fixed']) ? $tour['promo_discount_fixed'] : 0;
                $promo_discount_fixed_by_event = isset($tour['promo_discount_fixed_by_event']) ? $tour['promo_discount_fixed_by_event'] : 0;
                $promo_discount = isset($tour['promo_discount']) ? $tour['promo_discount'] : 0;

                $discount = (isset($tour['promo_discount_percentage'])) ? $tour['promo_discount_percentage'] : $promo_discount;

                //find out how many tickets and the prices
                foreach($this->ticketTypes as $type){
                    $tour['default'] = 0;
                    if($tour[$type]){
                        $tickets[$type] = array(
                            'amount' => $tour[$type],
                            'price' => $tour[$type . '_price']
                        );

                        //get the price for each currency
                        foreach(ExchangeRate::getExchangeRates() as $currency => $exchangeRate){
                            $tickets[$type][$currency] = $tour[$type . '_price_converted_' . $currency];
                        }

                    }
                }

                $basePrice = array();
                foreach(ExchangeRate::getExchangeRates() as $currency => $exchangeRate){
                    $basePrice[$currency] = $tour['base_price_' . $currency];
                    if ($promo_discount_fixed > 0){
                        $discountFixed[$currency] = ExchangeRate::convert($promo_discount_fixed,2,0,$currency);
                    } else if($promo_discount_fixed_by_event > 0) {
                        $discountFixed[$currency] = ExchangeRate::convert($tour['promo_discount'],2,0,$currency);
                    }
                }

                $dateTimeFormat = '';
                switch($this->theme){
                    case 'Italy':
                        $dateTimeFormat = date('F j, Y - h:ia', strtotime($tour['datetime']));
                        break;
                    default:
                        $dateTimeFormat = date('F j, Y - H.ia', strtotime($tour['datetime']));
                        break;
                }

                $all[] = array(
                    'tickets' => $tickets,
                    'type' => $tour['type'],
                    'event_id' => $tour['event_id'],
                    'sku' => $tour['sku'],
                    'url_name' => isset($tour['url_name']) ? $tour['url_name'] : '',
                    'name' => $tour['name'],
                    'dateTime' => $dateTimeFormat,
                    'formattedDate' => date('D, j M, Y \a\t g:i a', strtotime($tour['datetime'])),
                    'totalPrice' => $tour['total_price'],
                    'discount' => $discount,
                    'promo_discount_fixed' => $promo_discount_fixed,
                    'promo_discount_fixed_by_event' => $promo_discount_fixed_by_event,
                    'discountFixed' => $discountFixed,
                    'promo_type' => isset($tour['promo_type']) ? $tour['promo_type'] : '',
                    'promo_discount' => isset($tour['promo_discount']) ? $tour['promo_discount'] : '',
                    'promo_local' => isset($tour['promo_local']) ? $tour['promo_local'] : '',
                    'discount_bundle_tour_percent' => isset($tour['discount_bundle_tour_percent']) ? $tour['discount_bundle_tour_percent'] : 0,
                    'basePrice' => $basePrice
                );

                return $all;
            },array());
        }

        //get the exchange rates
        $initValues['exchangeRates'] = ExchangeRate::getExchangeRates();
        //for GA
        $initValues['measure'] = 'view_payment_page';
        $initValues['theme'] = $this->theme;
        //currency exchange rate
        $initValues['currency'] = array(
            'symbol' => ExchangeRate::getSymbol(),
            'exchangeRate' => ExchangeRate::getExchangeRateFromDbCurrency(),
            'selected' => ExchangeRate::getCurrency()
        );
        $this->set('initValues', $initValues);

        $ecViewPaymentPage = true;
        $this->set('ecViewPaymentPage', $ecViewPaymentPage);
        $this->set('ecTheme', $this->theme);
    }


    public function confirm() {
        $booking_id = $this->Session->read('booking_id');
        $this->set('booking_id', $booking_id);

        if(!$this->Session->check('shopping_cart')) {
            return $this->redirect(array('action' => 'home'));
        }

        if($this->request->is('post')) {
            if($this->request->data('noHotel') != 1) {
                $addresses = $this->request->data;
                foreach($addresses as $n => $address) {
                    $addresses[$n]['BookingsAddress']['bookings_id'] = $booking_id;
                }

                $this->BookingsAddress->saveMany($addresses);
            }
            $this->redirect(array('action' => 'print_vouchers'));
        }

        $this->set('travellerInfo', $this->Session->read('travellerInfo'));
        $travellerInfo = $this->Session->read('travellerInfo');

        $confirm_cart = $this->Session->read('shopping_cart');
//        if(Configure::read('debug') != 2){
            $this->Session->delete('shopping_cart');
//        }
        $this->set('cart', array());

        $this->Session->write('confirmCart', $confirm_cart);
        $this->set('confirmCart', $confirm_cart);

        $transaction_id = $this->Session->read('transaction_id');
        $this->set('booking_number', $transaction_id);

        //create the transaction variable for google analytics
        $transaction = array(
            'success' => 1,
            'booking_id' => $booking_id,
            'confirmCart' => $confirm_cart
        );
        $this->set('transaction', $transaction);
        $this->set('currency', CakeSession::read('currency'));

        $promo_code = $this->Session->read('promo_code');
        $promo = array();
        if($promo_code) {
            $promo = $this->BookingsPromo->findByPromoCode($promo_code);
            if($promo && $promo['BookingsPromo']['evergreen'] == 0) {
                $promo['BookingsPromo']['active'] = 0;
                $this->BookingsPromo->save($promo);
            }
            $this->set('promo',$promo);
        }

        $this->Email = ClassRegistry::init('Email');

        $this->Email->sendConfirmationEmail($booking_id, $this->config, $promo);

        $this->BookingsDetail->updateAll(array(
            'email_sent' => true,
            'BookingsDetail.imported_promo' => $this->BookingsDetail->getDatasource()->value(
                $this->Session->read('promo_code'), 'string'
            )
        ), array(
            'BookingsDetail.bookings_id' => $booking_id
        ));



        $start_date = min(Hash::extract($confirm_cart, '{n}.datetime'));
        $end_date = max(Hash::extract($confirm_cart, '{n}.datetime'));
        $this->set(compact('start_date', 'end_date'));

        $this->set('exchangeRate', CakeSession::read('exchangeRate'));

        $this->set('totalPrice',CakeSession::read('totalPrice'));

        $this->set('css', array('confirm'));
        $this->set('js', array('lib/jquery.datepick.min'));
        $this->set('layoutTitle', 'Confirmation');

        $promo_discount_fixed_total = ($this->Session->read('promo_discount_fixed_total')) ? $this->Session->read('promo_discount_fixed_total') : null;
        $this->Session->delete('promo_discount_fixed_total');
        $this->Session->delete('gift_card_amount');
        $this->Session->delete('gift_card_code');
        $this->set('promo_discount_fixed_total', $promo_discount_fixed_total);

        //for Periscopix Setup
        $customMetrics  = '';
        $j = 0;
//        foreach($confirm_cart as $i => $item) {
//            $j = $i + 1;
//            $paxConfirm = 0;
//            $promoDiscountConfirm = 0;
//            $paxConfirm += (isset($item['adults'])) ? $item['adults'] : 0;
//            $paxConfirm += (isset($item['seniors'])) ? $item['seniors'] : 0;
//            $paxConfirm += (isset($item['students'])) ? $item['students'] : 0;
//            $paxConfirm += (isset($item['children'])) ? $item['children'] : 0;
//            $paxConfirm += (isset($item['infants'])) ? $item['infants'] : 0;
//            //use local currency for discount
//            if ($promo_discount_fixed_total) {
//                $promoDiscountConfirm = $promo_discount_fixed_total['local'];
//            } else {
//                $promoDiscountConfirm = $item['total_price'] - $item['charged_local_amount'];
//            }
//            $dateBooking = explode(' ', $item['datetime']);
//            $customMetrics .= 'Tour_'.$j.'_Booked='.$item['sku'].';';
//            $customMetrics .= 'Date_of_Tour_'.$j.'='.$dateBooking[0].';';
//            $customMetrics .= 'People_on_Tour_'.$j.'='.$paxConfirm.';';
//            $customMetrics .= 'Promo_Discount_'.$j.'='.$promoDiscountConfirm.';';
//        }

        $uCount = 2;
        foreach($confirm_cart as $item) {
            $itemSku = '';
            $dateBooking = '';
            $paxConfirm = '';
            $promoDiscountConfirm = '';

            $paxConfirm = 0;
            $promoDiscountConfirm = 0;
            $paxConfirm += (isset($item['adults'])) ? $item['adults'] : 0;
            $paxConfirm += (isset($item['seniors'])) ? $item['seniors'] : 0;
            $paxConfirm += (isset($item['students'])) ? $item['students'] : 0;
            $paxConfirm += (isset($item['children'])) ? $item['children'] : 0;
            $paxConfirm += (isset($item['infants'])) ? $item['infants'] : 0;
            //use local currency for discount
            if ($promo_discount_fixed_total) {
                $promoDiscountConfirm = $promo_discount_fixed_total['local'];
            } else {
                $promoDiscountConfirm = $item['total_price'] - $item['charged_local_amount'];
            }
            $itemSku = $item['sku'];
            $dateBooking = explode(' ', $item['datetime']);
            $dateBooking = $dateBooking[0];
            $j++;

            $customMetrics .= 'u'.$uCount.'='.$itemSku.';';
            $uCount++;
            $customMetrics .= 'u'.$uCount.'='.$dateBooking.';';
            $uCount++;
            $customMetrics .= 'u'.$uCount.'='.$paxConfirm.';';
            $uCount++;
            $customMetrics .= 'u'.$uCount.'='.$promoDiscountConfirm.';';
            $uCount++;
        }


        $customMetrics .= 'State='.$travellerInfo['state'].';';
        //$customMetrics .= 'Tours_Booked='.$j.';';
        $customMetrics = 'u1='.$j.';'.$customMetrics;
        $this->set('customMetrics', $customMetrics);

        //for GA
        $initValues['cart'] = array();
        $initValues['measure'] = 'transaction_success';
        $initValues['theme'] = $this->theme;
        $initValues['transaction'] = $transaction;
        //currency exchange rate
        $initValues['currency'] = array(
            'symbol' => ExchangeRate::getSymbol(),
            'exchangeRate' => ExchangeRate::getExchangeRateFromDbCurrency(),
            'selected' => ExchangeRate::getCurrency()
        );

        $this->Session->delete('promo_code');
        $this->Session->delete('promo_discount_fixed_total');
        $this->Session->delete('promo_code_applied');

        $this->set('initValues', $initValues);
        $this->set('ecTheme', $this->theme);
    }



    public function print_vouchers($transaction_id) {
        $booking = $this->BookingsDetail->findByTransactionId($transaction_id);
        $booking_id = $booking['BookingsDetail']['bookings_id'];
    }


    private function _getFilterConditions($filters) {
        $conditions = array();

        if(!empty($filters['min_date'])) {
            $filters['min_date'] = date('Y-m-d H:i:s', strtotime($filters['min_date']));
            $conditions[] = 'EXISTS (select 1 from events_stage_pax_remainings where events_id = Event.id AND pax_remaining > 0 AND datetime > NOW() AND datetime > \'' . $filters['min_date'] . '\')';
        }
        if(!empty($filters['max_date'])) {
            $filters['max_date'] = date('Y-m-d H:i:s', strtotime($filters['max_date']));
            $conditions[] = 'EXISTS (select 1 from events_stage_pax_remainings where events_id = Event.id AND pax_remaining > 0 AND datetime > NOW() AND datetime < \'' . $filters['max_date'] . '\')';
        }

        if(!empty($filters['min_price'])) {
            $conditions[] = 'Event.adults_price >= ' . intval($filters['min_price']);
        }
        if(!empty($filters['max_price'])) {
            $conditions[] = 'Event.adults_price <= ' . intval($filters['max_price']);
        }

        if(!empty($filters['group_private'])) {
            //if all then show everything
            if(in_array('All', $filters['group_private'])) {
                $conditions['Event.group_private'][] = 'Group';
                $conditions['Event.group_private'][] = 'Private';
            }else{
                $conditions['Event.group_private'] = $filters['group_private'];
            }
            $conditions['Event.group_private'][] = 'Both';
        }


        if(!empty($filters['type'])) {
            $conditions[] = 'EXISTS (select 1 from events_tags where events_tags.event_id = Event.id AND events_tags.tag_id in ('.implode(',', $filters['type']).'))';
        }

        if(!empty($filters['city'])) {
            $conditions[] = 'EXISTS (select 1 from events_domains_groups where events_domains_groups.event_id = Event.id AND events_domains_groups.group_id = '.$filters['city'].')';
        }
        return $conditions;
    }

    public function filters() {
        $this->autoLayout = false;
        $this->set('layoutTitle', 'Filters IFrame');

        if($this->request->is('post')) {
            $city = $this->request->query('city');
            unset($this->request->query['city']);
            unset($this->request->query['type']);
            $this->redirect(array('action' => 'listing', 'city' => "$city-tours", '?' => $this->request->query));
        }

        if($city = $this->request->query('city')) {
            $domainsGroup = $this->DomainsGroup->findByUrlName($city);
            if(!$domainsGroup) {
                throw new NotFoundException;
            }

            $filters['city'] = $domainsGroup['DomainsGroup']['id'];

            $city = array(
                'id' => $domainsGroup['DomainsGroup']['id'],
                'slug' => $domainsGroup['DomainsGroup']['url_name'],
                'name' => $domainsGroup['DomainsGroup']['name']
            );
            $this->set('city', $city);

            $this->set('layoutTitle', "{$city['name']} Tour List | Walks of Italy");
        }

        $tags = $this->Tag->query("
                SELECT Tag.id, Tag.name, Tag.tag_type
                FROM events_domains_groups
                INNER JOIN events_tags on events_tags.event_id = events_domains_groups.event_id
                INNER JOIN events on events.id = events_tags.event_id and events.visible = 1
                INNER JOIN tags as Tag on events_tags.tag_id = Tag.id
                WHERE events_domains_groups.group_id = '{$city['id']}' and Tag.tag_type in (1, 2)
                GROUP BY Tag.id
                ORDER BY Tag.tag_type
            ");

        $this->set('tags', $tags);
        $this->set('filters', array('group_private' => array('Group')));

    }

    public function listing() {

        if ($this->theme == 'ItalyEs') {
            throw new NotFoundException();
        }

        $this->set('js', array('lib/jquery.datepick.min', 'listing'));
        $this->set('css', array('listing'));
        $this->set('layoutTitle', 'New York Tour List | Walks of New York');
        $this->set('meta_description', 'A full list of New York tours including Met Museum tour, Broadway tour, Official Mario Batali Greenwich Village tour, New York Walking tour & more.');
        $this->set('canonicalURL', FULL_BASE_URL . DS . $this->request->param('city'));
        $this->set('pageUrl', $this->request->url);

        $filters = $this->request->query;

        if($city = $this->request->query('city')){
            unset($this->request->query['city']);
            $this->redirect(array('city' => "$city-tours", '?' => $this->request->query));
        }

        if($city = $this->request->param('city')) {
            $city = substr($city, 0, -6);

            //check if a category is selected
            $cityParts = explode('-' ,$city);
            $possibleTags = array();
            if(count($cityParts) > 1){
                while($cityParts = array_splice($cityParts,1)){
                    $possibleTags[] = implode('-',$cityParts);
                }
                $currentTag = $this->Tag->find('first',array(
                    'conditions' => array(
                        'url_name' => $possibleTags
                    ),
                    'recursive' => 0
                ));
                if($currentTag){
                    //remove tag from url
                    $city = str_replace('-' . $currentTag['Tag']['url_name'],'',$city);

                    //add the tag to the filters
                    $filters['type'] = array($currentTag['Tag']['id']);
                }


            }

            //if only one category in the filter then redirect to correct url if it's not the correct url already
            if(isset($filters['type']) && count($filters['type']) == 1){
                $redirectTag = $this->Tag->find('first',array(
                    'conditions' => array(
                        'id' => $filters['type'][0]
                    ),
                    'recursive' => 0
                ));
                if($redirectTag && isset($redirectTag['Tag']['url_name'])){
                    if(!isset($currentTag) || $redirectTag['Tag']['id'] != $currentTag['Tag']['id']){

                        $url = $city . '-' . $redirectTag['Tag']['url_name'] . '-tours';

                        $this->redirect(array('city' => $url, '?' => $this->request->query));
                    }
                }

            }


            $domainsGroup = $this->DomainsGroup->findByUrlName($city);
            if(!$domainsGroup) {
                throw new NotFoundException;
            }

            $filters['city'] = $domainsGroup['DomainsGroup']['id'];

            $city = array(
                'id' => $domainsGroup['DomainsGroup']['id'],
                'slug' => $domainsGroup['DomainsGroup']['url_name'],
                'name' => $domainsGroup['DomainsGroup']['name'],
                'hero' => $domainsGroup['DomainsGroup']['hero']
            );
            $this->set('city', $city);
            //if france then add france class for style
            if($city['id'] == 21) $this->set('isFrance',true);

            $this->set('layoutTitle', $domainsGroup['DomainsGroup']['page_title']);
            $this->set('meta_description', $domainsGroup['DomainsGroup']['meta_description']);

            switch($this->theme){
                case 'Turkey':
                    $this->set('layoutTitle', 'Istanbul Tours, Food Tours & Cruises | Walks of Turkey');
                    $this->set('meta_description', 'Small group Istanbul tours, walking tours, food tours & Bosphorus cruises. Visit Hagia Sophia, the Blue Mosque, the Bazzars, Galata Tower & more!');
                    break;
            }

        }


        if(empty($filters['group_private'])) {
            $filters['group_private'] = array($this->config->filterDefaultTourType);
        }


        $filter_conditions = $this->_getFilterConditions($filters);

        $order = array();
        $this->set('sort', isset($filters['sort']) ? $filters['sort'] : '');
        $this->set('sort_name', 'Most Popular');

        if (!isset($filters['sort'])) {
            $filters['sort'] = 'popular';
        }
        if ($filters['sort'] == 'popular') {
            $order = array('Event.popularity' => 'ASC');
            $this->set('sort_name', 'Most Popular');
        } else if ($filters['sort'] == 'best') {
            // Should be sort by rating but using popularity for now
            $order = array('Event.popularity' => 'DESC');
            $this->set('sort_name', 'Best Rated');
        } else if ($filters['sort'] == 'priceLow') {
            $order = array('Event.adults_price + 0' => 'ASC');
            $this->set('sort_name', 'Lowest Price');
        } else if ($filters['sort'] == 'priceHigh') {
            $order = array('Event.adults_price + 0' => 'DESC');
            $this->set('sort_name', 'Highest Price');
        }

        $this->Event->getDatasource()->getLog(false, true);
        $events_tags = $this->Event->find('all',array(
            'conditions' => array(
                'AND' => array(
                    'EventsDomain.domains_id' => $this->config->domainId,
                    $filter_conditions,
                    'Event.visible' => 1,
                )
            ),
                'EventsStagePaxRemaining'=>array(
                    'conditions' => array(
                        'datetime >= now()',
                        'pax_remaining >= ' => 1
                    ),
                    'limit' => 1
                ),
            'contain' => array(
                'EventsImage' => array(
                    'order' => array('EventsImage.image_order' => 'ASC')
                ),
                'EventsSchedule',
                'EventsDomain',
                'Tag' => array(
                    'conditions' => array(
                        'Tag.name NOT' => array('featured', 'superfeatured')
                    )
                ),
                'EventsDomainsGroup' => array(
                    'DomainsGroup',
                    'conditions' => array(
                        'EventsDomainsGroup.primary' => 1
                    )
                )
            ),
            'order' => $order
        ));


        if (($this->config->domain != 'italy') && empty($events_tags))
            $this->Session->setFlash('Your search did not match any tours.  Please broaden your search criteria.', 'FlashMessage/error');

        $min_price = count($events_tags) ? min(Hash::extract($events_tags, '{n}.Event.adults_price')) : 0;
        $tags = $this->Tag->find('all', array(
            'conditions' => array(
                'Tag.name NOT' => array('featured', 'superfeatured')
            )
        ));

        if(isset($city)) {
            $tags = $this->Tag->query("
                SELECT Tag.id, Tag.name, Tag.tag_type
                FROM events_domains_groups
                INNER JOIN events_tags on events_tags.event_id = events_domains_groups.event_id
                INNER JOIN events on events.id = events_tags.event_id and events.visible = 1
                INNER JOIN tags as Tag on events_tags.tag_id = Tag.id
                WHERE events_domains_groups.group_id = '{$city['id']}' and Tag.tag_type in (1, 2)
                GROUP BY Tag.id
                ORDER BY Tag.tag_type
            ");
        }

        //fix the sorting because some items use adult price and others use private base price
        if($filters['sort'] == 'priceLow' || $filters['sort'] == 'priceHigh'){
            usort($events_tags, function($a ,$b){
                $price1 = $a['Event']['adults_price'] ?: $a['Event']['private_base_price'];
                $price2 = $b['Event']['adults_price'] ?: $b['Event']['private_base_price'];
                return $price1 > $price2;
            });

            if($filters['sort'] == 'priceHigh'){
                $events_tags = array_reverse($events_tags);
            }
        }



        $this->set('tags', $tags);

        $this->set('events', $events_tags);
        $this->set('num_results', count($events_tags));
        $this->set('min_price', $min_price);
        $this->set('filters', $filters);
        $this->set('query', $this->request->query);

        //Enhanced Ecommerce
        $ecListName = '';
        if(isset($city)) {
            $ecListName = $city['slug'];
        }
        $eventList = array();
        if ($this->theme == 'Italy') {
            //list with values needed for WrapperGA on listing page
            foreach($events_tags as $et ){
                $eventList[] = array(
                    'event_id' => $et['Event']['id'],
                    'event_name_short' => $et['Event']['name_short'],
                    'event_url_name' => $et['Event']['url_name']
                );
            }
        } else {
            $eventList = $events_tags;
        }
        $ecViewProductList = array(
            'list' => $ecListName,
            'events' => $eventList
        );
        //this initValues is intended to be used in WrapperGA on listing page
        //currently only for Italy page
        if ($this->theme == 'Italy') {
            $initValues = array(
                'debug' => Configure::read('debug'),
                'measure' => 'view_product_list',
                'theme' => $this->theme,
                'cart' => $this->Session->read('shopping_cart'),
                //currency exchange rate
                'currency' => array(
                    'symbol' => ExchangeRate::getSymbol(),
                    'exchangeRate' => ExchangeRate::getExchangeRateFromDbCurrency(),
                    'selected' => ExchangeRate::getCurrency()
                ),
                'product_list' => $ecViewProductList
            );
            $this->set('initValues', $initValues);
        }
        $this->set('ecViewProductList', $ecViewProductList);
        $this->set('ecTheme', $this->theme);
        $this->set('discountPercentRelatedTour', $this->discountRelatedTour);
    }

    //temporary while it's hard coded
    private function getTransferEventInfo($eventID,$name){
        $event = $this->Event->find('first', array(
            'conditions' => array('Event.id' => $eventID),
            'fields' => array('url_name','private_base_price')
        ));
        $event = $event['Event'];

        $event['name'] = $name;
        return $event;

    }
    public function transfers() {
        $transfers = array(
            'Florence' => array(
                $this->getTransferEventInfo(66, 'Livorno Shore Excursion Full-Day'),
                $this->getTransferEventInfo(88, 'Private Full-Day Drive from Florence: Montalcino & More'),
                $this->getTransferEventInfo(43, 'Private Half-Day Tuscany Wine Tour'),
            ),
            'Rome' => array(
                $this->getTransferEventInfo(55, 'Transfer: Rome City'),
                $this->getTransferEventInfo(62, 'Rome to Pompeii'),
                $this->getTransferEventInfo(56, 'Rome Airport'),
                $this->getTransferEventInfo(58, 'Rome Civitavecchia')
            ),
            'Venice' => array(
                $this->getTransferEventInfo(69, 'Venice Airport to City Transfer'),
                $this->getTransferEventInfo(70, ' Transfer Venice - Airport to Port')
            )
        );
        $this->set('transfers',$transfers);
        $this->set('layoutTitle','Transfers');
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'transfers');
        //echo '<pre>' . print_r($transfers, 1) . '</pre>';

    }

    public function sustainability(){
        $this->set('layoutTitle','Sustainability policy');
        $this->set('css', array('static'));
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
        switch($this->theme){
            case 'ItalyEs':
                throw new NotFoundException();
                break;
            default:
                $this->set('layoutTitle','Cancellation policy');
                break;


        }
    }

    public function cancellation(){

        $this->set('css', array('static'));
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'cancellation');
        switch($this->theme){

            case 'Turkey':
                $this->set('layoutTitle','Cancellation Policy | Walks of Turkey');
                $this->set('meta_description', 'Walks of Turkey Cancellation Policy: Up to 72 hours before tour start time, cancellations are free of charge.');
                break;
            case 'ItalyEs':
                $this->set('layoutTitle','Política de Cancelaciones | Walks of Italy');
                $this->set('meta_description', 'Política de Cancelaciones y Modificaciones de Walks of Italy, perteneciente a todos los tours (grupo/privado) de walksofitalycom o Walks LLC.');
                break;
            default:
                $this->set('layoutTitle','Cancellation policy');
                break;


        }

    }

    public function travelAgents(){

        $this->set('css', array('static'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'travel-agents');
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));

        switch($this->theme){

            case 'Turkey':
                $this->set('layoutTitle','Info for Travel Agents | Walks of Turkey');
                $this->set('meta_description', 'Booking with Walks of Turkey as a travel agent - claiming your commission, processing your booking, getting in touch');
                break;
            default:
                $this->set('layoutTitle','Travel Agents');
                break;


        }

    }

    public function process_log(){
        $this->autoLayout = false;
        $this->set('layoutTitle','Logs');
        //$logs = array_reverse(file('/var/log/cc_log/process_log.txt'));
        //        if( Configure::read('debug') != 0){
        ////            $logs = array_reverse(file('/var/log/cc_log/process_log.txt'));
        //
        //            $handle = @fopen('/var/log/cc_log/process_log.txt', "r");
        //            if ($handle) {
        //                while (($buffer = fgets($handle, 4096)) !== false) {
        //                    echo $buffer;
        //                }
        //                fclose($handle);
        //            }
        //
        //        }else{
        //            $logs = array();
        //        }
        $logs = array_reverse(file('/var/log/cc_log/process_log.txt'));

        $this->set('logs',$logs);
    }

    //clear cache
    public function clearCache(){
        CACHE::clear();
        $this->redirect('/');
    }

    //Prevent historical urls from 404-ing
    public function oldURL(){
        $domainIdTranslation = array(
            '1' => '6',//     Rome
            '2' => '12',//     Florence/Pisa
            '3' => '13',//     Pompeii/Amalfi
            '4' => '3',//     Venice
            '5' => '14',//     Tuscany/Siena
            '6' => '2',//     Vatican City
            //'7' => '',//     Siena (not in new system)
            '8' => '6',//     Umbria
            '9' => '18',//     Puglia
            '12' => '7',//    Milan
            '10' => '16',//    Transfers

        );


        $url = $this->params['pass'];
        switch($url[0]){
            case 'tour_all_listing': //tour_all_listing/[DomainGroupId]/[EventsId]
                $event = $this->Event->find('first', array(
                    'conditions'=> array(
                        'Event.sku' => $url[2]
                    )
                ));

                $primaryDomainsGroupID = null;

                //get the primary domain ID if possible
                if($event != null){
                    $primaryDomainsGroupID = null;
                    foreach($event['EventsDomainsGroup'] as $domainsGroup){
                        if($domainsGroup['primary'] == 1){ $primaryDomainsGroupID = $domainsGroup['group_id']; }
                    }
                }


                // if primary group is not found because the event doesn't exist
                // then try to get the domain group id from the old domains list
                if($primaryDomainsGroupID == null){
                    if(isset($domainIdTranslation[$url[1]])){
                        $primaryDomainsGroupID = $domainIdTranslation[$url[1]];
                    }else{
                        throw new NotFoundException();
                    }
                }

                //by this point there has to at least be a domain group id
                $domain = $this->DomainsGroup->findById($primaryDomainsGroupID);

                $this->redirect(array(
                    'controller' => $domain['DomainsGroup']['url_name'] . '-tours',
                    //if the event(tour) was not found then redirect to general city page
                    'action' => ($event != null)? $event['Event']['url_name'] : '',
                    '?' => $this->request->query
                ),
                    array('status' => '301')
                );

                break;
            case 'tour_listing': // tour_listing/[DomainGroupId]/[DomainGroupId]



                if(isset($domainIdTranslation[$url[1]])){
                    //translate old domains group ID to a new one
                    $primaryDomainsGroupID = $domainIdTranslation[$url[1]];
                    //ge the domain group
                    $domainsGroup = $this->DomainsGroup->findById($primaryDomainsGroupID);

                }else{
                    throw new NotFoundException();
                }

                //not found 404
                if($domainsGroup == null) {
                    throw new NotFoundException();
                }

                $this->redirect(array(
                    'controller' => $domainsGroup['DomainsGroup']['url_name'] . '-tours',
                    '?' => $this->request->query
                ),
                    array('status' => '301')
                );
                break;
            case 'importantnote':
                $event = $this->Event->find('first',array(
                    'conditions' => array('id' => $url[1]),
                    'contain' => array('EventsDomainsGroup'=> array('DomainsGroup'))
                ));

                //not found 404
                if($event == null) {
                    throw new NotFoundException();
                }

                $this->redirect(FULL_BASE_URL . "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}",
                    array('status' => '301')
                );
                break;

        }
    }

    public function flushCache(){
        $host = '127.0.0.1';
        $port = 11211;
        $memcache = new Memcache;
        $memcache->connect($host, $port) or die ("Could not connect to memcache server");
        $memcache->flush();
    }

    public function test(){
        //$this->render(false,false);

    }

    public function testgeoip(){
        $this->render(false,false);
        $ip = $this->request->query('ip');
        $currency = ($this->request->query('cur')) ? $this->request->query('cur') : 'USD';
        echo '<pre>';
        echo '<br>ExchangeRate::setCurrency(cur)  ';ExchangeRate::setCurrency($currency);
        echo '<br>ExchangeRate::getCurrency()     = '.ExchangeRate::getCurrency();
        echo '<br>_SERVER["REMOTE_ADDR"]          = '.$_SERVER["REMOTE_ADDR"];
        echo '<br>GeoIpMaxMind::getCurrencyByIp   = '.GeoIpMaxMind::getCurrencyByIp($_SERVER["REMOTE_ADDR"]);
        echo '<br>this->request->clientIp(false)  = '.$this->request->clientIp(false);
        echo '<br>GeoIpMaxMind::getCurrencyByIp   = '.GeoIpMaxMind::getCurrencyByIp($this->request->clientIp());
        echo '<br>/testgeoip?ip=123.456.789.123   = '.$ip;
        echo '<br>/testgeoip?cur=AAA              = '.$currency;
        echo '<br>this->Session->read(clientIp)   = '.$this->Session->read('clientIp');
        if ($ip && $currency){
            echo '<br>CHANGE CURRENCY ';
            echo '<br>this->request->clientIp()       = '.$ip;
            echo '<br>GeoIpMaxMind::getCurrencyByIp   = '.GeoIpMaxMind::getCurrencyByIp($ip);
            ExchangeRate::setCurrency( GeoIpMaxMind::getCurrencyByIp($ip) );
            $this->Session->write('clientIp', $ip);
            echo '<br>ExchangeRate::getCurrency()     = '.ExchangeRate::getCurrency();
            echo '<br>this->Session->read(clientIp)   = '.$this->Session->read('clientIp');
        }
        echo '<br><br>_SERVER:<br>';
        print_r($_SERVER);
        echo '</pre>';

    }

    public function gift_cards() {
        if( !in_array($this->theme, ['Italy', 'nyc', 'Turkey'] ) ) return $this->redirect('/');
        $this->loadModel('BookingsPromo');

        //for the donation conversion
        $this->_writeExchangeRateToSession();

        // Refresh exchange rates to prevent stale currency conversions
        ExchangeRate::init($this->config->defaultCurrency, $this->config->dbCurrency, true);


        //validate email
        $validEmail = true;
        if($this->request->is('post') && isset($this->request->data['email'])) {
            $this->request->data['email'] = trim($this->request->data['email']);
            if(!$this->Client->validEmail($this->request->data)) {
                $this->Session->setFlash('That email is not valid, please try again.', 'FlashMessage/error');
                $validEmail = false;
            }
        }

        if($this->request->is('post') && isset($this->request->data['email']) && $validEmail) {

            //figure out the currency situation
            $currency = $this->request->data('currency');
            $accounts = $this->Payment->getAccounts();

            if(!$currency || !isset($accounts[$currency])){
                //if trying to use a nonexistent currency then use the default db currency
                $currency = isset($accounts[$this->config->dbCurrency]) ? $this->config->dbCurrency : 'USD';
            }
            $this->request->data['currency'] = $currency;
            ExchangeRate::setCurrency($currency);



            //---------client stuff-------------
            //create the client array
            $client = array(
                'fname' => $this->request->data('first_name'),
                'lname' => $this->request->data('last_name'),
                'address' => $this->request->data('street_address'),
                'city' => $this->request->data('city'),
                'state' => $this->request->data('state'),
                'zip' => $this->request->data('zip'),
                'mobile_number' => $this->request->data('phone_number'),
                'guest' => 1
            );

            $agent = $this->Agent->findByEmailAddress($this->request->data('email'));
            if ($agent) {
                $client['agents_id'] = $agent['Agent']['id'];
                $client['email'] = 'TravelAgent-' . strtotime('now') . '@walks.org';
            } else if ($this->Auth->loggedIn()) {
                $client['id'] = $this->Auth->user('id');
                $client['email'] = trim($this->request->data('email'));
            } else {
                $checkClient = $this->Client->find('first', array(
                    'conditions' => array(
                        'email' => $this->request->data('email')
                    ),
                    'contain' => array()
                ));
                if (!empty($checkClient)) {
                    $client = $checkClient['Client'];
                } else {
                    $client['email'] = $this->request->data('email');
                }
            }

            //only nyc has audience rewards
            if($this->request->data('audience_reward')) $client['audience_reward'] = $this->request->data('audience_reward');

            $this->Client->save(array('Client' => $client), false);
            $client_id = $this->Client->id;


            $giftCardAmount = $this->request->data('price');

            //sanitize credit card data
            $this->request->data['ccNo'] = preg_replace("/[^0-9]/", "", $this->request->data['ccNo']);

            $this->request->data['currency'] = $currency;


            $exchangeRate = ExchangeRate::getExchangeRate($this->config->dbCurrency) / ExchangeRate::getExchangeRate($currency);


            //add needed variables to the payment data
            $data = $this->request->data;
            $data['clients_id'] = $client_id;
            $data['amount_local'] = number_format($giftCardAmount / $exchangeRate, 2);
            $data['exchange_rate'] = ExchangeRate::getExchangeRate($this->config->dbCurrency) / ExchangeRate::getExchangeRate($currency);
            $data['exchange_from'] = $this->config->dbCurrency;
            $data['exchange_to'] = $currency;


            //---------booking---------------
            //save the booking
            $this->Booking->save(array(
                'Booking' => array(
                    'clients_id' => $client_id,
                    'amount_local' => $giftCardAmount / $exchangeRate,
                    'amount_merchant' => $giftCardAmount * ExchangeRate::getExchangeRate(),
                    'currencies_id' => 1
                )
            ));
            $booking_id = $this->Booking->id;
            $this->set(compact('booking_id'));

            //create a unique code
            $newGiftCard = true;
            while($newGiftCard){
                $giftCardCode = substr('gc' . md5(time() . 'secret salt!' . rand(1,1000000)),0, 9);
                if(!$this->BookingsPromo->hasAny(['promo_code' => $giftCardCode])) break;

            }


            $response = $this->Payment->processGiftCard($data, $booking_id, $this->config->domain, $giftCardAmount);
            $this->Payment->log_process('payment_response_gift_card', array('orbital_response' => $response, 'total_price_total' => $giftCardAmount, 'total_price_total_local' => $giftCardAmount * CakeSession::read('exchangeRate')));

            if(!$response['success']) {
                $this->Session->setFlash('Payment error: ' . $response['message'], 'FlashMessage/error');
            } else {


                $transactionId = $response['authcode'];

                $this->Session->write('transaction_id', $transactionId);
                $this->Session->write('travellerInfo', $this->request->data);
                $this->Session->write('gift_card_amount', $giftCardAmount);


                //log payment transaction
                $transactionAdd['PaymentTransaction'] = array(
                    'amount_local' => $giftCardAmount / $exchangeRate,
                    'payment_amount' => $giftCardAmount,
                    'transaction_date' => date("Y-m-d H:i:s"),
                    'merchant_trans_number' => $response['authcode'],
                    'TxRefNum' => $response['transactionId'],
                    'merchants_id' => $response['merchants_id'],
                    'merchant_result' => $response['success'],
                    'payment_status' => $response['payment_status'],
                    'clients_id' => 0, // good
                    'exchange_rate' => $exchangeRate,
                    'booking_id' => $booking_id,
                    'exchange_from' => $this->config->dbCurrency,
                    'exchange_to' => $currency
                );
                $this->PaymentTransaction->save($transactionAdd);


                $this->BookingsPromo->create();




                $this->BookingsPromo->save([
                    'promo_name' => 'Gift Card',
                    'promo_description' => $booking_id,
                    'promo_code' => $giftCardCode,
                    'booking_start_date' => date('Y-m-d H:i:s'),
                    'booking_end_date' => date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 365 * 5), //2 years
                    'event_start_date' => date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 365 * 2),
                    'event_end_date' => date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 365 * 5),
                    'discount_amount' => number_format($giftCardAmount / $exchangeRate, 2),
                    'min_cart_value' => 1,
                    'active' => 1,
                    'fixed' => 1,
                    'fixed_card' => 0,
                    'evergreen' => 1,
                    'min_events' => 1,
                ]);
                $promo_id = $this->BookingsPromo->getLastInsertID();

                $this->Session->write('gift_card_code', $giftCardCode);
                $this->Session->write('gift_card_amount', $giftCardAmount);
                $this->Session->write('transaction_id', $transactionId);
                $this->Session->write('booking_id', $booking_id);
                $this->Session->write('travellerInfo', $this->request->data);

                $this->Email = ClassRegistry::init('Email');
                $this->Email->sendGiftCardEmail($booking_id, $promo_id, $giftCardAmount, $this->theme);

                return $this->redirect(array('controller' => 'confirm_gift_card'));
            }

        }



        $this->set('css', array('payment', 'donate'));
        $this->set('js', array('https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min', 'lib/creditcard', 'payment', 'lib/jquery.validate'));


        //get the exchange rates
        $initValues['exchangeRates'] = ExchangeRate::getExchangeRates();

        $this->set('initValues', $initValues);


        $this->set('layoutTitle', 'Purchase Gift Card');




    }


    public function confirm_gift_card(){

        $booking_id = $this->Session->read('booking_id');
        $gift_card = [
            'code' => $this->Session->read('gift_card_code'),
            'amount' => $this->Session->read('gift_card_amount')
        ];
        $customer = $this->Session->read('travellerInfo');

        if($this->theme == 'Turkey') {
            $this->set('css', array('confirm'));
        }

        $this->set('booking_id', $booking_id);

        $this->set('gift_card', $gift_card);

        $this->set('travellerInfo', $customer);

        $this->set('layoutTitle', 'Gift card confirmation');
    }

    public function paris(){

        $this->set(array(
            'layoutTitle' => 'Walks are in Paris',
            'isFrance' => true
        ));
    }
    public function faq_paris(){

        $this->set('css', array('faq', 'static'));
        $this->set('js', array('faq'));
        $this->set('isFrance', 'true');

        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'faq');
        $this->set('layoutTitle', 'FAQ');
    }

    public function contact_paris(){
        $this->set('isFrance', 'true');
        $this->set('css', array('contact', 'static'));
        $this->set('canonicalURL',FULL_BASE_URL . DS . 'contact');
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
        $this->set('layoutTitle', 'Contact');

    }


    public function activity_tag(){

        require_once(APPLIBS . 'ContentfulWrapper.php');

        $this->set('css', array('activity-tag'));
        $this->set('js', array('activity-tag', 'lib/moment'));
        $contentfulWrapper = new ContentfulWrapper();
//        $contentCMS = $contentfulWrapper->getCities();
//        $contentCMS = $contentfulWrapper->getTours();
//        $contentCMS = $contentfulWrapper->getTourTags($event['Event']['id']);
//        $contentCMS = $contentfulWrapper->getTourTags();
//        $contentCMS = $contentfulWrapper->getTourTags('360rTIoqtaQS0iUEqkAQYW','fields.city.sys.id');

        $this->set('mapImage','rome');

        switch($this->request->url){
            case 'colosseum-tours-compare':
                $tourTagKey = '4w9BTEo6pyegQQQCM0MwgE'; //rome
                $this->set('layoutTitle','Colosseum Tours');
                $this->set('viewAllTourLink', '/rome-tours');
                break;
            case 'st-marks-doges-palace-tours-compare':// venice
                $tourTagKey = '2soAd9hshmom82SmmQUQgg';
                $this->set('mapImage','venice');
                $this->set('layoutTitle','St Mark\'s Basilica & Doge\'s Palace Tours');
                $this->set('viewAllTourLink','/venice-tours');
                break;
            default: //vatican tours just in case
                $tourTagKey = '1proWpfqVGOkOwiMiEmuY4';
                $this->set('layoutTitle','Vatican Tours');
                $this->set('viewAllTourLink', '/vatican-tours');
                break;

        }
        $contentCMS = $contentfulWrapper->getTourTags($tourTagKey,'sys.id');

        $contentCMS = $contentCMS['items'][0];
//        debug($contentCMS);
        $this->set('contentCMS', $contentCMS);
        $this->set('layoutTitle', $contentCMS['tagPageTitle']);



        //hero image here
//        debug($contentCMS);
        $this->set('heroImage', $contentfulWrapper->getAsset($contentCMS['tagPageHeroBanner']['sys']['id'], true));

        $eventIds = isset($contentCMS['tagPageRelatedTours']) ? array_filter(explode(',',trim($contentCMS['tagPageRelatedTours'])), function($val){  return $val !== ''; }) : [];


        //get similar tours
        $relatedTours = $this->Event->find('all',[
            'conditions' => [
                'Event.id' => $eventIds
            ],
            'contain' => [
                'EventsImage',
                'EventsDomainsGroup' => [
                    'conditions' => ['primary' => true],
                    'DomainsGroup' => [
                        'fields' => ['url_name']
                    ]
                ],
                'EventsStage' => [
                    'limit' => 1,
                    'conditions' => ['datetime > now()'],
                    'fields' => ['adults_price']
                ]
//                'EventsSchedule' => [
//                    'conditions' => [
//                        'date_start > now()'
//                    ],
//                    'fields' => ['date_start'],
//                    'limit' => 1,
//                    'order' => ['date_start ASC']
//                ]
            ]
        ]);


        $similarTours = [];
        foreach($relatedTours as $i => $relatedTour){

            $contentfulTour = $contentfulWrapper->getTourById($relatedTour['Event']['id']);
            $event = [];
            $event['event_id'] = $relatedTour['Event']['id'];
            $event['group_size'] = $relatedTour['Event']['group_size'];
            $event['name_listing'] = htmlentities($relatedTour['Event']['name_listing'], ENT_QUOTES);
            $event['description_listing'] = isset($contentfulTour['fields']['tourPageShortDescriptionComparative']) ? $contentfulTour['fields']['tourPageShortDescriptionComparative'] : '';
//            debug($contentfulTour['fields']);
            $event['sites_included'] = $contentfulTour && isset($contentfulTour['fields']['tourPageSitesVisited']) ? $contentfulTour['fields']['tourPageSitesVisited'] : [];
            $event['adults_price'] = $relatedTour['Event']['adults_price'];
            //get adult price from tickets
            if($relatedTour['EventsStage']){
                $event['adults_price'] = $relatedTour['EventsStage'][0]['adults_price'];
            }

            $event['more_info'] = "/".$relatedTour['EventsDomainsGroup'][0]['DomainsGroup']['url_name']."-tours/".$relatedTour['Event']['url_name'];
            $event['images_name'] = [];
            $event['title'] = $contentfulTour['fields']['tourTitleLong'];
            $event['next_tour_date'] = ( isset($relatedTour['EventsSchedule']) && isset($relatedTour['EventsSchedule'][0]) ) ? $relatedTour['EventsSchedule'][0]['date_start'] : null;

            if(isset($contentfulTour['fields']['tagPageTourImages'])){
                foreach($contentfulTour['fields']['tagPageTourImages'] as $img){
                    $event['images_name'][] = $contentfulWrapper->getImageAssetUrl($img['sys']['id']) . '?w=400';
                }
            }


            $duration = intval($relatedTour['Event']['duration']);
            $duration = ( $duration > 0) ? round($duration / 60, 1)." hrs" : "-";
            $event['duration'] = $duration;

            $similarTours[$event['event_id']] = $event;

        }

        $this->set('similarTours', $similarTours);

        //get highlights
        $highlightIndex = 1;
        $highlights = [];
        while(isset($contentCMS['tagPageHighlightTitle' . $highlightIndex])){
            $image = $contentCMS['tagPageHighlightImage' . $highlightIndex];
            $imageId = isset($image[0]) ? $image[0]['sys']['id'] : $image['sys']['id'];
            $highlights[] = [
                'title' => $contentCMS['tagPageHighlightTitle' . $highlightIndex],
                'image' => $contentfulWrapper->getImageAssetUrl($imageId) . '?w=400',
                'description' => isset($contentCMS['tagPageHighlightDescription' . $highlightIndex]) ? $contentCMS['tagPageHighlightDescription' . $highlightIndex] : ''
            ];
            $highlightIndex++;
        }
//        debug($highlights);

        $this->set('highlights', $highlights);


    }



    public function activity_tag_tour_upcoming() {
        $dateSelected = $this->request->data('dateSelected');
        $eventIds = $this->request->data('eventIds');
        $this->response->type('json');
        $this->autoRender = false;
        $upcomingTours = [];
        if($this->request->is('ajax') && !is_null($dateSelected) && !is_null($eventIds)) {
            $dateSelected = date('Y-m-d', strtotime($dateSelected));

            $events = $this->Event->find('all', array(
                'conditions' => array(
                    'Event.id' => $eventIds,
                    'Event.is_active' => 1,
                    'EventsDomain.domains_id' => $this->config->domainId,
                ),
                'contain' => array(
                    'EventsDomain' => array(
                        'Domain'
                    ),
                    'EventsDomainsGroup' => array(
                        'DomainsGroup',
                        'conditions' => array(
                            'EventsDomainsGroup.primary' => 1
                        )
                    ),
                    'EventsStage' => [
                        'limit' => 1,
                        'conditions' => ['datetime > now()'],
                        'fields' => ['adults_price']
                    ]

                )
            ));

            foreach ($events as $event) {
                $pax_group = $this->Event->getStages($event['Event']['id'], $dateSelected, date('Y-m-d', strtotime($dateSelected.' +1 days')));
                if (count($pax_group) == 0 || !array_key_exists($dateSelected, $pax_group) ) continue;

                $times_group = [];
                foreach ($pax_group as $date => $times) {
                    foreach ($times as $time => $detail) {
                        if ($dateSelected == $date){
                            $times_group[] = $detail['pretty_time'];
                        }
                    }
                }

                $upcomingTour = array(
                    'event_id' => $event['Event']['id'],
                    'name_listing' => htmlentities($event['Event']['name_listing'], ENT_QUOTES),
                    'more_info' => "/".$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']."-tours/".$event['Event']['url_name'],
                    'adults_price' => ExchangeRate::convert(ceil($event['Event']['adults_price']), false),
                    'pax_group' => $pax_group,
                    'times_group' => $times_group
                );
                //get adult price from tickets
                if($event['EventsStage']){
                    $upcomingTour['adults_price'] = ExchangeRate::convert($event['EventsStage'][0]['adults_price'], false);
                }

                $duration = intval($event['Event']['duration']);
                $duration = ( $duration > 0) ? round($duration / 60, 1)." hrs" : "-";
                $upcomingTour['duration'] = $duration;

                $upcomingTours[] = $upcomingTour;
            }


            usort($upcomingTours, function($tour1, $tour2){
                $tour1Time = array_keys($tour1['pax_group'][array_keys($tour1['pax_group'])[0]])[0];
                $tour2Time = array_keys($tour2['pax_group'][array_keys($tour2['pax_group'])[0]])[0];
                return $tour1Time > $tour2Time;
            });

            return json_encode($upcomingTours);
        }
        return json_encode($upcomingTours);
    }

    public function temp(){
        $this->view = 'event_detail';
        $this->set('layoutTitle', 'detail page');
    }

    public function temp1(){
        $this->view = 'tour_list';
        $this->set('layoutTitle', 'tour list');
    }

    public function temp2(){
        $this->view = 'tag_comparison';
        $this->set('layoutTitle', 'tag comparison');
    }

    public function temp3(){
        $this->view = 'compare_tours';
        $this->set('layoutTitle', 'compare tours');
    }

    public function temp4(){
        $this->view = 'checkout';
        $this->set('layoutTitle', 'checkout');
    }

    public function temp5(){
        $this->view = 'checkout_complete';
        $this->set('layoutTitle', 'checkout complete');
    }

    public function temp23(){
        $this->view = 'checkout_complete_nonlogined';
        $this->set('layoutTitle', '[non-logined] checkout complete');
    }

    public function temp6(){
        $this->view = 'my_account_sign_up';
        $this->set('layoutTitle', 'my account');
    }

    public function temp7(){
        $this->view = 'my_account';
        $this->set('layoutTitle', 'my account');
    }

    public function temp20(){
        $this->view = 'my_account_upcoming_empty';
        $this->set('layoutTitle', 'upcoming tours empty');
    }

    public function temp8(){
        $this->view = 'my_account_upcoming_detail';
        $this->set('layoutTitle', 'upcoming tour detail');
    }

    public function temp9(){
        $this->view = 'my_account_upcoming_modify';
        $this->set('layoutTitle', 'upcoming tour modify');
    }

    public function temp10(){
        $this->view = 'my_account_upcoming_cancel';
        $this->set('layoutTitle', 'upcoming tour cancel');
    }

    public function temp11(){
        $this->view = 'my_account_past_tours';
        $this->set('layoutTitle', 'past tours');
    }

    public function temp22(){
        $this->view = 'my_account_past_tours_empty';
        $this->set('layoutTitle', 'past tours empty');
    }

    public function temp12(){
        $this->view = 'my_account_past_refund';
        $this->set('layoutTitle', 'request refund');
    }

    public function temp13(){
        $this->view = 'wishlist';
        $this->set('layoutTitle', 'wishlist');
    }

    public function temp21(){
        $this->view = 'wishlist_empty';
        $this->set('layoutTitle', 'wishlist empty');
    }

    public function temp14(){
        $this->view = 'settings';
        $this->set('layoutTitle', 'settings');
    }

    public function temp15(){
        $this->view = 'login';
        $this->set('layoutTitle', 'login');
    }

    public function temp16(){
        $this->view = 'sign_up';
        $this->set('layoutTitle', 'sign up');
    }

    public function temp17(){
        $this->view = 'forgot_password';
        $this->set('layoutTitle', 'forgot_password');
    }

    public function temp24(){
        $this->view = 'guide_profile';
        $this->set('layoutTitle', 'guide profile');
    }

    public function temp25(){
        $this->view = 'contact';
        $this->set('layoutTitle', 'contact');
    }

    public function temp26(){
        $this->view = 'cancellation_policy';
        $this->set('layoutTitle', 'cancellation policy');
    }

    public function temp27(){
        $this->view = 'privacy_policy';
        $this->set('layoutTitle', 'privacy policy');
    }

    public function temp28(){
        $this->view = 'tos';
        $this->set('layoutTitle', 'terms and conditions');
    }

    //convert a price to the currency that the code expects to find in the database
    protected function _convertToDbCurrency($from, $price){
        $to = ExchangeRate::getDbCurrency();
        $from = substr($from,0,3);
        if($from == $to) return $price;
        $fromExchangeRate = ExchangeRate::getUnadjustedExchangeRate($from);
        $toExchangeRate = ExchangeRate::getUnadjustedExchangeRate($to);
        return round(($price * $fromExchangeRate) / $toExchangeRate,2);


    }

    //get the currency of an event
    protected function _getEventCurrency($eventId){
        $domains = $this->Event->find('first',[
            'conditions' => [
                'Event.id' => $eventId
            ],
            'fields' => ['id'],
            'contain' => [
                'EventsDomainsGroup' => [
                    'fields' => ['id'],
                    'DomainsGroup' => [
                        'fields' => ['id'],
                        'Domain' => [
                            'fields' => ['id','name','exchangePair']
                        ]
                    ]
                ]
            ]
        ]);

        //start with the db currency (euros) just in case the domain is missing.
        //Most of the time this will be right.
        $currency = ExchangeRate::getDbCurrency();
        if(isset($domains['EventsDomainsGroup'])){
            foreach($domains['EventsDomainsGroup'] as $domain){
                $eventCurrency = $domain['DomainsGroup']['Domain']['exchangePair'];
                if($eventCurrency){
                    $currency = $eventCurrency;
                    break;
                }
            }
        }
        return $currency;
    }


}

<?php
App::uses('PagesController', 'Controller');
require_once(APPLIBS . 'ContentfulWrapper.php');

class TakeWalksController extends PagesController {
    private $Contentful;
    private $_medals;

    /**
     * @param ContentfulWrapper $Contentful
     */
    public function setContentful($Contentful)
    {
        $this->Contentful = $Contentful;
    }

    /**
     * @return mixed
     */
    public function getContentful()
    {
        return $this->Contentful;
    }

    /*********************************************************************************************************
     * *******************************************************************************************************
     * Cache Functions
     * *******************************************************************************************************
     *********************************************************************************************************/

    /**
     * @param $cacheKey
     * @param $cacheConfig
     * @return array
     */
    public function beforeFilterCache($cacheKey, $cacheConfig) {
        $medals = [];
        foreach($this->Contentful->getTourStyles()['items'] as $item){
            $medals[strtolower($item['fields']['tourStyleTitle'])] = $item['fields'];
        }
        Cache::write($cacheKey,$medals,$cacheConfig);
        return $medals;
    }

    /**
     * @param $cacheKey
     * @param $cacheConfig
     * @return array
     */
    public function homeCache($cacheKey, $cacheConfig) {
        $home = $this->Contentful->getHomePage();
        $home = $home['items'][0]['fields'];
        //get tour guides
        $tourGuides = [];
        foreach ($home['homepageFeaturedTourGuides'] as $tourGuide) {
            $tourGuideId = $tourGuide['sys']['id'];
            $tourGuide = $this->Contentful->getEntry($tourGuide);
            $country = $this->Contentful->getEntry($tourGuide['tourGuideCountry']);
            $city = $this->Contentful->getEntry($tourGuide['tourGuideCity']);
            $tourGuides[] = [
                'tourGuideId' => $tourGuideId,
                'name' => $tourGuide['tourGuideName'],
                'city' => $city['cityListingName'],
                'country' => $country['countryName'],
                'avatar' => $this->Contentful->getImageAssetUrl($tourGuide['tourGuideImage'][0]['sys']['id']),
                'description' => $tourGuide['tourGuideFeaturedDescription'],
            ];
        }

        //get feature cities
        $featuredCities = [];
        foreach ($home['homepageFeaturedCities'] as $city) {
            $city = $this->Contentful->getEntry($city);

            $featuredCities[] = [
                'name' => $city['cityListingName'],
                'image' => $this->Contentful->getImageAssetUrl($city['cityFeaturedImage'])
            ];

        }

        //get selling points
        $sellingPoints = [];
        $i = 1;
        while (isset($home["homepageSellingPoint{$i}Title"])) {
            $sellingPoints[] = [
                'title' => $home["homepageSellingPoint{$i}Title"],
                'description' => $home["homepageSellingPoint{$i}Description"],
                'image' => $this->Contentful->getImageAssetUrl($home["homepageSellingPointImage{$i}"])
            ];
            $i++;
        }

        //featured tour
        $featuredTour = $this->Contentful->getEntry($home['homepageFeaturedTour']);
        $featuredTourCity = $this->Contentful->getCitySlugById($featuredTour['tourCity']);
        $featuredTourURL = FULL_BASE_URL . DS . $featuredTourCity . DS . $featuredTour['tourPageURL'];

        $content = [
            'subHeading' => $home['tourGuidesSubheading'],
            'heroTitle' => $home['homepageHeroBannerTitle'],
            'meta' => [
                'title' => $home['homepageMetaTitle'],
                'description' => $home['homepageMetaDescription']
            ],
            'heroDescription' => $home['homepageHeroBannerDescription'],
            'heroImage' => $this->Contentful->getImageAssetUrl($home['homepageHeroBanner']['sys']['id']),
            'heroVideo' => isset($home['homepageHeroVideo']) ? $home['homepageHeroVideo'] : null,
            'tourGuides' => $tourGuides,
            'featuredCities' => $featuredCities,
            'sellingPointsTitle' => $home['homepageSellingPointsTitle'],
            'sellingPointsSubheading' => $home['homepageSellingPointsSubheading'],
            'sellingPointsDescription' => $home['homepageSellingPointsDescriptionLong'],
            'sellingPoints' => $sellingPoints,
            'featuredTourTitle' => $featuredTour['tourPageTitleShort'],
            'featuredTourURL' => $featuredTourURL,
            'featuredTourHeroImage' => $this->Contentful->getImageAssetUrl($featuredTour['tourPagePhotoGallery'][0]),
            'featuredTourDescription' => $featuredTour['tourPageShortDescriptionComparative'],
            'signUpDescription' => $home['homepageSignUpDescription']
        ];

        Cache::write($cacheKey,$content,$cacheConfig);
        return $content;
    }

    /**
     * @param $citySlug
     * @param $cacheConfig
     * @return array
     */
    public function listingCache($citySlug, $cacheConfig) {
        $city = $this->Contentful->getCity($this->_citySlugToName($citySlug));
        $cityCid = $city['sys']['id'];
        $city = $city['fields'];

        $key = 'page_listing_tags_' . $cityCid;

        //get tags
        $tagInfos = $this->Contentful->getTagsByCity($cityCid);
        //        debug($tagInfos[0]);

        $tags = [];
        foreach ($tagInfos as $tag) {
            $tag = $tag['fields'];
            //get first three tag tours
            $tagTours = [];

            $tagId = isset($tag['adminTagId']) ? $tag['adminTagId'] : null;

            //if missing tagId then this tag is not meant to be shown
            if(!$tagId) continue;

            //get associated event ids from tag
            $tagIds = $this->Tag->find('first',[
                'conditions' => [
                    'Tag.id' => $tagId
                ],
                'contain' => [
                    'Event' => [
                        'fields' => ['id'],
                        'order' => 'tag_order',
                        'EventsStage' => [
                            'conditions' => [
                                'datetime >' => date('Y-m-d H:i:s'),
                            ],
                            'fields' => ['datetime','adults_price'],
                            'limit' => 1
                        ],
                        'EventsPromotion' => [
                            'fields' => ['original_price']
                        ]

                    ]
                ]
            ]);

            //skip tags with no tours
            if(!isset($tagIds['Event']) || count($tagIds['Event']) == 0) continue;

            foreach ($tagIds['Event'] as $tourRef) {
                $tour = $this->Contentful->getTourById($tourRef['id']);
                if (!isset($tour['fields'])) continue;
                $tour = $tour['fields'];

                $flag = isset($tour['tourListingFlag']) ? $this->Contentful->getListingTourFlag($tour['tourListingFlag']) : ['total' => 0];
                $flag = $flag['total'] > 0 ? $flag['items'][0]['fields']['listingPageFlagTitle'] : null;

                if (!isset($tour['tourPageTourStyle'])) continue;
                $medal = $this->Contentful->getTourStyle($tour['tourPageTourStyle']);
                $medal = $medal['total'] > 0 ? $medal['items'][0]['fields']['tourStyleTitle'] : null;

                $tourRating = $this->_getTourRating($tour['eventId']);

                $price = $tourRef['EventsStage'] ? $tourRef['EventsStage'][0]['adults_price'] : 0;

                $tourListingImage = isset($tour['tourListingImage']) ? $this->Contentful->getImageAssetUrl($tour['tourListingImage']) : '';

                //make sure all prices are in the db currency
                $dbCurrencyPrice = $this->_convertToDbCurrency($this->_getEventCurrency($tour['eventId']), $price);

                $tagTours[] = [
                    'eventId' => $tour['eventId'],
                    'medal' => $medal,
                    'citySlug' => $this->Contentful->getCitySlugById($tour['tourCity']),
                    'slug' => $tour['tourPageURL'],
                    'image' => $tourListingImage,
                    'name' => $tour['tourTitleLong'],
                    'description' => isset($tour['tourPageShortDescriptionComparative']) ? $tour['tourPageShortDescriptionComparative'] : '',
                    'listingText' => isset($tour['listingText']) ? $tour['listingText'] : '',
                    'price' => $dbCurrencyPrice,
                    'reviewsAverage' => $tourRating['rating_average'],
                    'reviewsCount' => $tourRating['rating_count'],
                    'duration' => $tour['tourDuration'],
                    'groupSize' => $tour['tourGroupSize'],
                    'flag' => $flag,
                    'discount' => $tourRef['EventsPromotion'] ? $tourRef['EventsPromotion'][0]['original_price'] : false
                ];
            }

            $tags[] = [
                'name' => isset($tag['cityListingTagPageTitle']) ? $tag['cityListingTagPageTitle'] : '',
                'description' => isset($tag['cityListingTagPageShortText']) ? $tag['cityListingTagPageShortText'] : '',
                'slug' => isset($tag['tagPageURL']) ? $tag['tagPageURL'] : '',
                'tours' => $tagTours
            ];
        }

        Cache::write($key,$tags,$cacheConfig);
        return $tags;
    }

    /**
     * @param $city
     * @param $slug
     * @param $cacheConfig
     * @return array
     */
    public function eventDetailCache($city, $slug, $cacheConfig) {

        $key = 'page_eventDetail_' . $city . '-' . $slug;

        $eventDetailCache = [];

        $content = $this->Contentful->getTourBySlug($slug);
        $contentfulEventSysId = $content['sys']['id'];
        $contentfulEventId = $content['fields']['eventId'];

        $tour = $this->Event->find('first', [
            'fields' => ['id', 'url_name'],
            'conditions' => [
                'Event.id' => $contentfulEventId
            ],
            'contain' => [
                'EventsDomainsGroup' => 'DomainsGroup',
                'EventsPromotion' => [
                    'fields' => ['original_price']
                ]
            ]

        ]);

        $discount = $tour['EventsPromotion'] ? $tour['EventsPromotion'][0]['original_price'] : false;
        $tourCitySlug = $this->Contentful->getCitySlugById($content['fields']['tourCity']);

        $this->config->domainId = $tour['EventsDomainsGroup'][0]['DomainsGroup']['domains_id'];

        //view vars caching
        parent::eventDetail($city, $tour['Event']['url_name']);


        //convert all prices into the db currency (euros)
        $eventCurrency = $this->_getEventCurrency($tour['Event']['id']);
        foreach($this->viewVars['initValues']['group_prices'] as $paxType => &$price){
            $price = $this->_convertToDbCurrency($eventCurrency, $price);
        }
        unset($price);
        foreach($this->viewVars['initValues']['private_prices'] as $paxType => &$price){
            $price = $this->_convertToDbCurrency($eventCurrency, $price);
        }
        unset($price);
        foreach($this->viewVars['initValues']['dates_group'] as $date => &$dateInfo){
            foreach($dateInfo as $time => &$timeInfo){
                foreach($timeInfo['prices'] as $paxType => &$price){
                    $price = $this->_convertToDbCurrency($eventCurrency, $price);
                }
            }
        }
        unset($dateInfo);

        //calculate review average
        $reviewAverage = 0;
        if($this->viewVars['reviews']) {
            foreach ($this->viewVars['reviews'] as $review) {
                $reviewAverage += $review['event_rating'];
            }
            $reviewAverage /= count($this->viewVars['reviews']);
        }

        $eventDetailCache['contentfulEventId'] = $contentfulEventId;

        $cityContent = $this->Contentful->getCity($this->_citySlugToName($city));


        $contentfulTourId = $content['sys']['id'];
        $content = $content['fields'];

        $gallery = [];
        if (isset($content['tourPagePhotoGallery'])){
            foreach ($content['tourPagePhotoGallery'] as $img) {

                $img = $this->_contentfulArrToImg($img, true);
                $gallery[] = [
                    'url' => str_replace('downloads.contentful','images.contentful', $img['link']),
                    'description' => isset($img['description']) ? $img['description'] : ''
                ];
            }
        }

        //get featured tours
        $featuredReviews = $this->Contentful->getFeaturedTourReviews($contentfulTourId);

        //get faq
        $faqs = [];
        foreach ($this->Contentful->getFaqById($contentfulTourId) as $faq) {
            $faqs[] = [
                'question' => $faq['fields']['faqQuestion'],
                'answer' => $faq['fields']['faqAnswer']
            ];
        }

        //get similar tours
        $similarTours = [];
        if(isset($content['tourPageSimilarTours'])) {
            foreach($content['tourPageSimilarTours'] as $tourCid) {
                $tour = $this->Contentful->getTourById($tourCid);
                if (!isset($tour['fields'])) continue;
                $tour = $tour['fields'];
                $nextTicket = $this->_getNextTourTicket($tour['eventId']);
                $medal = $this->Contentful->getTourStyle($tour['tourPageTourStyle']);

                $similarTours[] = [
                    'medal' => $medal['items'][0]['fields']['tourStyleTitle'],
                    'image' => isset($tour['tourListingImage']) ? $this->Contentful->getImageAssetUrl($tour['tourListingImage']) : '',
                    'price' => $this->_convertToDbCurrency($this->_getEventCurrency($tour['eventId']), $nextTicket['EventsStage']['adults_price']),
                    'title' => $tour['tourTitleLong'],
                    'description' => $tour['tourPageShortDescriptionComparative'],
                    'duration' => $tour['tourDuration'],
                    'maxGroup' => $tour['tourGroupSize'],
                    'citySlug' => $this->Contentful->getCitySlugById($tour['tourCity']),
                    'slug' => $tour['tourPageURL'],
                ];
            }
        }

        //find tag for similar tours link. Tag must have tag url
        $eventTagIds = array_values($this->EventsTag->find('list',[
            'fields' => ['tag_id'],
            'conditions' => [
                'event_id' => $content['eventId']
            ]
        ]));

        $tags = $this->Contentful->getTagsByDbIds($eventTagIds);
        $tagSlug = null;
        foreach($tags as $tag){
            if(isset($tag['fields']['tagPageURL'])){
                $tagSlug = $tag['fields']['tagPageURL'];
                break;
            }
        }

        //get tour guides
        $tourGuides = [];
        $tourGuidesContentful = $this->Contentful->getTourGuidesByFeaturedTourId($contentfulEventSysId);
        if ( count($tourGuidesContentful) == 0) {
            $tourGuidesContentful = $this->Contentful->getTourGuidesByCityId($cityContent);
        }

        foreach(array_slice($tourGuidesContentful,0,2) as $guide){
            $tourGuides[] = [
                'tourGuideId' => $guide['sys']['id'],
                'image' => $this->Contentful->getImageAssetUrl($guide['fields']['tourGuideImage'][0]),
                'name' => $guide['fields']['tourGuideName'],
                'city' => $this->Contentful->getCityById($guide['fields']['tourGuideCity'])['fields']['cityListingName'],
                'country' => $this->Contentful->getCountryById($guide['fields']['tourGuideCountry'])['fields']['countryName'],
                'description' => isset($guide['fields']['tourGuideFeaturedDescription']) ? $guide['fields']['tourGuideFeaturedDescription'] : ''
            ];

        }

        $this->viewVars['initValues']['group_prices']['discount'] = $discount;

        $eventDetailCache = [
            'content' => $content,
            'tour' => $tour,
            'gallery' => $gallery,
            'faqs' => $faqs,
            'featuredReviews' => $featuredReviews,
            'similarTours' => $similarTours,
            'tagSlug' => $tagSlug,
            'tourGuides' => $tourGuides,
            'cityContent' => $cityContent,
            'viewVars' => $this->viewVars,
            'reviewsAverage' => $reviewAverage,
            'tourCitySlug' => $tourCitySlug
        ];

        Cache::write($key,$eventDetailCache,$cacheConfig);
        return $eventDetailCache;
    }

    /**
     * @param $slug
     * @param $cacheConfig
     * @return array
     */
    public function compareCache($slug, $cacheConfig) {
        $cacheCompare = [];
        $key = 'compare_page_content_tours_' . $slug;

        $tag = $this->Contentful->getTagPageBySlug($slug);

        if(!$tag){ return $cacheCompare; } //throw page not found

        $tagCid = $tag['sys']['id'];
        $tag = $tag['fields'];

        //get city
        $city = $this->Contentful->getCityById($tag['tagPageCity'][0])['fields'];

        //get all of the information
        //get highlights
        $highlights = [];
        $counter = 1;
        while (isset($tag['tagPageHighlightTitle' . $counter])) {
            $tag['tagPageHighlightImage' . $counter] = isset($tag['tagPageHighlightImage' . $counter]['sys']) ? $tag['tagPageHighlightImage' . $counter] : $tag['tagPageHighlightImage' . $counter][0];
            $title = isset($tag['tagPageHighlightTitle' . $counter]) ? $tag['tagPageHighlightTitle' . $counter] : '';
            $highlightPullQuote = isset($tag['highlightPullQuote' . $counter]) ? ContentfulWrapper::parseMarkdown($tag['highlightPullQuote' . $counter]) : '';
            $description = isset($tag['tagPageHighlightDescription' . $counter]) ? ContentfulWrapper::parseMarkdown($tag['tagPageHighlightDescription' . $counter]) : '';
            $image = isset($tag['tagPageHighlightImage' . $counter]) ? $this->Contentful->getImageAssetUrl($tag['tagPageHighlightImage' . $counter]) : '';
            $highlights[] = [
                'title' => $title,
                'highlightPullQuote' => $highlightPullQuote,
                'description' => $description,
                'image' => $image
            ];
            $counter++;
        }

        //get the FAQ
        $faq = [];
        foreach($this->Contentful->getFaqByTagId($tagCid) as $question){
            $faq[] = [
                'question' => $question['fields']['faqQuestion'],
                'answer' => $question['fields']['faqAnswer'],
            ];
        }

        $content = [
            'title' => $tag['tagPageTitle'],
            'description' => ContentfulWrapper::parseMarkdown($tag['tagPageIntroText']),
            'heroImage' => $this->Contentful->getImageAssetUrl($tag['tagPageHeroBanner'], true),
            'highlights' => $highlights,
            'sitesVisited' => explode(',',$tag['sitesVisited']),
            'faq' => $faq
        ];

        $tagRow = $this->Tag->find('first',[
            'fields' => ['id'],
            'conditions' => [
                'Tag.id' => $tag['adminTagId'],
            ],
            'contain' => [
                'Event' => [
                    'fields' => ['id','url_name'],
                    'conditions' => [
                        'EXISTS (SELECT 1 FROM events_stages WHERE events_id = Event.id AND datetime > NOW() limit 1)'
                    ],
                    'EventsTag' => [
                        'fields' => ['id','tag_order'],
                        'order' => ['tag_order' => 'asc']
                    ],
                    'EventsStage' => [
                        'conditions' => [
                            'datetime >' => date('Y-m-d H:i:s'),
                        ],
                        'fields' => ['datetime','adults_price'],
                        'limit' => 1
                    ],
                    'EventsStagePaxRemaining' => [
                        'fields' => ['pax_remaining'],
                        'conditions' => [
                            'datetime >' => date('Y-m-d H:i:s'),
                            'pax_remaining >= ' => 1
                        ]
                    ],
                    'EventsPromotion' => [
                        'fields' => ['original_price']
                    ]
                ]
            ]

        ]);

        usort($tagRow['Event'], function($a, $b) {
            return $a['EventsTag']['tag_order'] > $b['EventsTag']['tag_order'];
        });

        $tours = [];
        foreach ($tagRow['Event'] as $event) {
            if( !isset($event['EventsStagePaxRemaining']) || count($event['EventsStagePaxRemaining']) == 0 ) continue;
            $tour = $this->Contentful->getTourById($event['id']);
            if(!$tour) continue;

            $tour = $tour['fields'];
            $city = $this->Contentful->getEntry($tour['tourCity']);

            $reviews = $this->_getTourRating($event['id']);

            $flag = isset($tour['tourListingFlag']) ? $this->Contentful->getListingTourFlag($tour['tourListingFlag']) : ['total' => 0];
            $flag = $flag['total'] > 0 ? $flag['items'][0]['fields']['listingPageFlagTitle'] : null;

            $tours[] = [
                'id' => $event['id'],
                'title' => $tour['tourTitleLong'],
                'titleShort' => isset($tour['tourPageTitleShort']) ? $tour['tourPageTitleShort'] : $tour['tourTitleLong'] ,
                'slug' => $tour['tourPageURL'],
                'citySlug' => $this->Contentful->getCitySlugById($tour['tourCity']),
                'city' => $city['cityListingName'],
                'image' => $this->Contentful->getImageAssetUrl($tour['tourListingImage']),
                'duration' => $tour['tourDuration'],
                'groupSize' => $tour['tourGroupSize'],
                'startTime' => $tour['tourStartTime'],
                'price' => $this->_convertToDbCurrency($this->_getEventCurrency($event['id']),$event['EventsStage'][0]['adults_price']),
                'medal' => isset($tour['tourPageTourStyle']) && $tour['tourPageTourStyle'] ? $this->Contentful->getEntry($tour['tourPageTourStyle'])['tourStyleTitle'] : '',
                'description' => $tour['tourPageShortDescriptionComparative'],
                'whoFor' => $tour['tourPageComparativeWhoFor'],
                'whoNotFor' => $tour['tourPageComparativeWhoNotFor'],
                'sitesVisited' => isset($tour['comparativeSitesVisited']) ? explode(',', $tour['comparativeSitesVisited']) : [],
                'reviewsAverage' => $reviews['rating_average'],
                'reviewsCount' => $reviews['rating_count'],
                'flag' => $flag,
                'discount' => $event['EventsPromotion'] ? $event['EventsPromotion'][0]['original_price'] : false
            ];
        }

        $cacheCompare = ['content' => $content, 'tours' => $tours, 'city' => $city, 'tag' => $tag];

        Cache::write($key, $cacheCompare, $cacheConfig);
        return $cacheCompare;
    }

    /**
     * @param $tourId
     * @return mixed
     */
    public function getTourRatingCache($tourId, $cacheConfig){
        $key = 'tour_rating_' . $tourId;
        $rating = $this->Feedback->find('all',array(
            'conditions' => array(
                'events_id' => $tourId,
                'is_published' => 1,
                'event_rating >= ' => 1,
                'event_rating <= ' => 5

            ),
            'fields' => ['AVG(event_rating) as rating_average','COUNT(event_rating) rating_count'],
            'limit' => '100',
            'order' => array('feedback_date' => 'DESC')
        ))[0][0];
        Cache::write($key,$rating,$cacheConfig);
        return $rating;
    }

    /*********************************************************************************************************
     * *******************************************************************************************************
     * Controller Functions
     * *******************************************************************************************************
     *********************************************************************************************************/

    public function beforeFilter() {

        if($_SERVER['HTTP_HOST'] == 'take.walks'){
//            Configure::write('Cache.disable', true);
        }

        parent::beforeFilter();

        $this->Contentful = new ContentfulWrapper();

        $this->UserApi= $this->Components->load('UserApi');

        if(!$this->_medals = Cache::read('tw_medals','long')){
            $this->_medals = $this->beforeFilterCache('tw_medals', 'long');
        }

        $this->set('medals',$this->_medals);


        // get the countries and cities for the header
        $contentfulCountries = $this->Contentful->getCountries();
        $countries = [];

        foreach($contentfulCountries['items'] as $country){
            if(!isset($country['sys'])) continue;
            $countryCid = $country['sys']['id'];

            $contentfulCities = $this->Contentful->getCountryCity($countryCid);
            $cities = [];
            foreach($contentfulCities['items'] as $city){
                $cities[] = [
                    'name' => $city['fields']['cityListingName'],
                ];
            }

            isset($country['fields']['descriptionDescriptionShort']) ?  $country['fields']['descriptionDescriptionShort'] : '';

            $countries[$country['fields']['countryName']] = [
                'name' => $country['fields']['countryName'],
                'alt' => isset($country['fields']['descriptionDescriptionShort']) ? $country['fields']['descriptionDescriptionShort'] : $country['fields']['countryName'],
                'cities' => $cities
            ];
        }

        $this->set('header',[
            'countries' => $countries
        ]);

    }


    public function beforeRender() {

        //get cart
        if($this->request->param('action') != 'payment'){
            $this->viewVars['initValues'] = isset($this->viewVars['initValues']) ? $this->viewVars['initValues'] : [];

            $this->viewVars['initValues']['cart'] = $this->Session->read('shopping_cart');
            $this->viewVars['initValues']['currency'] = [
                'symbol' => ExchangeRate::getSymbol(),
                'exchangeRate' => ExchangeRate::getExchangeRateFromDbCurrency(),
                'selected' => ExchangeRate::getCurrency()
            ];
        }





    }

    public function home() {
        $key = 'page_home';
//        if(!$content = Cache::read($key, 'short')) {
            $content = $this->homeCache($key, 'short');
//        }
        $this->set('metaTitle', $content['meta']['title']);
        $this->set('metaDescription',$content['meta']['description']);
        $this->set('content', $content);


        $this->set('canonicalURL', FULL_BASE_URL . DS );

    }

    public function city($citySlug) {

    }

    private function _citySlugToName($slug){
        $city = str_replace('-tours','',$slug);
        $city = str_replace('-',' ',$city);
        return $city;
    }

    public function listing(){
        $citySlug = $this->request->params['city'];
        $city = $this->Contentful->getCity($this->_citySlugToName($citySlug));
        $cityCid = $city['sys']['id'];
        $city = $city['fields'];

        $key = 'page_listing_tags_' . $cityCid;
        if(!$tags = Cache::read($key, 'short')) {
            $tags = $this->listingCache($city, 'short');
        }

        $data = $this->request->data;

        $startDate = (isset($data['start_date']) && $data['start_date'] != '' ) ? date('Y-m-d', strtotime($data['start_date'])) : null;
        $endDate = (isset($data['end_date']) && $data['end_date'] != '' ) ? date('Y-m-d', strtotime($data['end_date'])) : null;

        if($startDate || $endDate){
            $eventIds = [];
            foreach ($tags as $tag) {
                foreach ($tag['tours'] as $tour) {
                    if(!in_array($tour['eventId'],$eventIds)) $eventIds[] = $tour['eventId'];
                }
            }

            $conditions = [
                'Event.id' => $eventIds,
                'date(datetime) >= ' => $startDate
            ];

            if ($endDate != '') $conditions['date(datetime) <= '] = $endDate;

            $eventsDates = $this->EventsStage->find('all', [ 'fields' => ['DISTINCT Event.id'], 'conditions' => $conditions ]);

            $eventsDates = array_map(function($e){ return $e['Event']['id']; }, $eventsDates);

            foreach ($tags as &$tag) {
                foreach ($tag['tours'] as $i => $tour) {
                    if(!in_array($tour['eventId'],$eventsDates)) unset($tag['tours'][$i]);
                }
            }
        }



        $this->set('content',[
            'cityName' => $city['cityListingName'],
            'featuredImage' => isset($city['cityFeaturedImage']) ? $this->Contentful->getImageAssetUrl($city['cityFeaturedImage']) : '',
//            'tours' => $tours,
            'medals' => $this->_medals,
            'citySlug' => $citySlug,
            'startDate' => date('m/d/Y', strtotime($startDate ? $startDate : date('Y-m-d'))),
            'endDate' => ($endDate != '') ? date('m/d/Y', strtotime($endDate)) : '',
            'tags' => $tags
        ]);

        $this->set('metaTitle', $city['cityListingMetaTitle']);
        $this->set('metaDescription',$city['cityListingMetaDescription']);
        $this->set('canonicalURL', FULL_BASE_URL . DS .$citySlug);

    }

    public function eventDetail($city, $slug) {

        $key = 'page_eventDetail_' . $city . '-' . $slug;

        $eventDetailCache = Cache::read($key, 'short');
        if( empty($eventDetailCache)) {
            $eventDetailCache = $this->eventDetailCache($city, $slug, 'short');
        }

        if ( $eventDetailCache['tourCitySlug'] != $city) {
            //throw new NotFoundException('Tour not found');
            $urlRedirect = DS.$eventDetailCache['tourCitySlug'].DS.$slug;
            $this->redirect($urlRedirect);
        }


        $content = $eventDetailCache['content'];
//        $contentfulEventId = $eventDetailCache['contentfulEventId'];
        $gallery = $eventDetailCache['gallery'];
        $faqs = $eventDetailCache['faqs'];
        $featuredReviews = $eventDetailCache['featuredReviews'];
        $similarTours = $eventDetailCache['similarTours'];
        $tagSlug = $eventDetailCache['tagSlug'];
        $tourGuides = $eventDetailCache['tourGuides'];
        $cityContent = $eventDetailCache['cityContent'];



        $this->viewVars = array_merge($this->viewVars, $eventDetailCache['viewVars']);

        $contentView = [
            'meta' => [
                'title' => $content['tourPageMetaTitle'],
                'description' => $content['tourPageMetaDescription']
            ],
            'layoutTitle' => $content['tourPageMetaTitle'],
            'title' => $content['tourTitleLong'],
            'shortTitle' => $content['tourPageTitleShort'],
            'duration' => $content['tourDuration'],
            'maxGroupSize' => $content['tourGroupSize'],
            'intro' => $content['tourPageIntro'],
            'descriptionTitle' => isset($content['descriptionHeading']) ? $content['descriptionHeading'] : 'Detailed Description',
            'description' => $content['tourPageLongDescription'],
            'sitesVisited' => $content['tourPageSitesVisited'],
            'tourIncludes' => $content['tourPageIncluded'],
            'video' => isset($content['tourPageVideo']) ? $content['tourPageVideo'] : null,
            'gallery' => $gallery,
            'reviewsAverage' => $eventDetailCache['reviewsAverage'],
            'faqs' => $faqs,
            'featuredReviews' => $featuredReviews,
            'similarTours' => $similarTours,
            'tagSlug' => $tagSlug,
            'tourGuides' => $tourGuides,
            'citySlug' => $this->_cityNameToSlug($cityContent['fields']['cityListingName']),
            'cityName' => $cityContent['fields']['cityListingName']

        ];

        $this->set('metaTitle', $contentView['meta']['title']);
        $this->set('metaDescription', $contentView['meta']['description']);
        $this->set('content', $contentView);
        $this->set('canonicalURL', FULL_BASE_URL . DS . $city .  DS . $slug);
        $this->set('user', $this->Auth->user() );
    }

    public function payment() {

        $postData = [];
        //massage post data
        if($this->request->is(['post']) && $this->request->data('first_name')) {
            $postData = $this->request->data;

            if( !empty($this->Auth->user()) ) {
                $resultUpdate = $this->UserApi->userUpdate($this->Auth->user('id'), $postData['first_name'], $postData['last_name'], $postData['email'], $postData['phone_number'], '');
                $returnUpdate = $this->_getAjaxReturn($resultUpdate);
                if($returnUpdate['success']) {
                    $this->Auth->login((array)$returnUpdate['results']->data->user);
                }
            }



            $this->request->data['ccType'] = $this->_getCardType($this->request->data('ccNo'));
//            debug($this->request->data);
        }

        parent::payment();

        $states = [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming'
        ];

        $countries = [
            'US' => 'United States of America',
            'IT' => 'Italy',
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas the',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island (Bouvetoya)',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
            'VG' => 'British Virgin Islands',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros the',
            'CD' => 'Congo',
            'CG' => 'Congo the',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote d\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FO' => 'Faroe Islands',
            'FK' => 'Falkland Islands (Malvinas)',
            'FJ' => 'Fiji the Fiji Islands',
            'FI' => 'Finland',
            'FR' => 'France, French Republic',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia the',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island and McDonald Islands',
            'VA' => 'Holy See (Vatican City State)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IL' => 'Israel',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KP' => 'Korea',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyz Republic',
            'LA' => 'Lao',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'AN' => 'Netherlands Antilles',
            'NL' => 'Netherlands the',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn Islands',
            'PL' => 'Poland',
            'PT' => 'Portugal, Portuguese Republic',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia (Slovak Republic)',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia, Somali Republic',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia and the South Sandwich Islands',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard & Jan Mayen Islands',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland, Swiss Confederation',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'UM' => 'United States Minor Outlying Islands',
            'VI' => 'United States Virgin Islands',
            'UY' => 'Uruguay, Eastern Republic of',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe'
        ];

        //        debug($this->viewVars['cart']);

        $this->set([
            'states' => $states,
            'countries' => $countries,
            'postData' => $postData,
            'user' => $this->Auth->user()
        ]);

    }

    public function confirm(){
        parent::confirm();
    }

    //takes a contentful array with 'sys' key and returns the images url
    private function _contentfulArrToImg($arr, $moreInfo = false) {
        $cid = $arr['sys']['id'];
        $content = $this->Contentful->getAsset($cid);
        $content['fields']['link'] = $this->Contentful->getImageAssetUrl($cid);

        return $moreInfo ? $content['fields'] : $content['fields']['link'];
    }

    private function _getCardType($pan, $include_sub_types = false) {
        //maximum length is not fixed now, there are growing number of CCs has more numbers in length, limiting can give false negatives atm

        //these regexps accept not whole cc numbers too
        //visa
        $visa_regex = "/^4[0-9]{0,}$/";
        $vpreca_regex = "/^428485[0-9]{0,}$/";
        $postepay_regex = "/^(402360|402361|403035|417631|529948){0,}$/";
        $cartasi_regex = "/^(432917|432930|453998)[0-9]{0,}$/";
        $entropay_regex = "/^(406742|410162|431380|459061|533844|522093)[0-9]{0,}$/";
        $o2money_regex = "/^(422793|475743)[0-9]{0,}$/";

        // MasterCard
        $mastercard_regex = "/^(5[1-5]|222[1-9]|22[3-9]|2[3-6]|27[01]|2720)[0-9]{0,}$/";
        $maestro_regex = "/^(5[06789]|6)[0-9]{0,}$/";
        $kukuruza_regex = "/^525477[0-9]{0,}$/";
        $yunacard_regex = "/^541275[0-9]{0,}$/";

        // American Express
        $amex_regex = "/^3[47][0-9]{0,}$/";

        // Diners Club
        $diners_regex = "/^3(?:0[0-59]{1}|[689])[0-9]{0,}$/";

        //Discover
        $discover_regex = "/^(6011|65|64[4-9]|62212[6-9]|6221[3-9]|622[2-8]|6229[01]|62292[0-5])[0-9]{0,}$/";

        //JCB
        $jcb_regex = "/^(?:2131|1800|35)[0-9]{0,}$/";

        //ordering matter in detection, otherwise can give false results in rare cases
        if(preg_match($jcb_regex, $pan)) {
            return "jcb";
        }

        if(preg_match($amex_regex, $pan)) {
            return "AX";
        }

        if(preg_match($diners_regex, $pan)) {
            return "diners_club";
        }

        //sub visa/mastercard cards
        if($include_sub_types) {
            if(preg_match($vpreca_regex, $pan)) {
                return "v-preca";
            }
            if(preg_match($postepay_regex, $pan)) {
                return "postepay";
            }
            if(preg_match($cartasi_regex, $pan)) {
                return "cartasi";
            }
            if(preg_match($entropay_regex, $pan)) {
                return "entropay";
            }
            if(preg_match($o2money_regex, $pan)) {
                return "o2money";
            }
            if(preg_match($kukuruza_regex, $pan)) {
                return "kukuruza";
            }
            if(preg_match($yunacard_regex, $pan)) {
                return "yunacard";
            }
        }

        if(preg_match($visa_regex, $pan)) {
            return "VI";
        }

        if(preg_match($mastercard_regex, $pan)) {
            return "MC";
        }

        if(preg_match($discover_regex, $pan)) {
            return "DI";
        }

        if(preg_match($maestro_regex, $pan)) {
            if($pan[0] == '5') {//started 5 must be mastercard
                return "mastercard";
            } else {
                return "maestro"; //maestro is all 60-69 which is not something else, thats why this condition in the end
            }
        }

        return "unknown"; //unknown for this system
    }

    private function _getTourInformation($tourIds,$fields){
        $fields[] = 'id';
        $fields = array_unique($fields);

        $tours = $this->Event->find('all',[
            'fields' => $fields,
            'conditions' => [
                'id' => $tourIds
            ],
            'contain' => false
        ]);

        $tourInfo = [];
        foreach($tours as $tour){
            $tourInfo[$tour['Event']['id']] = $tour['Event'];
        }


        return $tourInfo;
    }

    public function compare($tour1 = null, $tour2 = null, $tour3 = null){
        $slug = $this->request->param('tag');

        //get all of the information
        $key = 'compare_page_content_tours_' . $slug;
        $cacheCompare = Cache::read($key, 'short');
        if( empty($cacheCompare) ) {
            $cacheCompare = $this->compareCache($slug, 'short');
        }

        if(!isset($cacheCompare['content'])){
            throw new NotFoundException('Page not found');
        } //throw page not found

        $content = $cacheCompare['content'];
        $tours = $cacheCompare['tours'];
        $city = $cacheCompare['city'];
        $tag = $cacheCompare['tag'];

        $this->set('content',$content);

        //if comparing tours
        $tourIds = array_filter([$tour1, $tour2, $tour3]);
        if($tourIds){
            $this->view = 'compare_tours';
            $selectedTours = [];
            foreach($tours as $tour){
                if(in_array($tour['id'], $tourIds)) $selectedTours[] = $tour;
            }
            $tours = $selectedTours;
        }

        $this->set('metaTitle', $tag['tagPageMetaTitle']);
        $this->set('metaDescription', $tag['tagPageMetaDescription']);

//        debug($tag);
        $this->set('canonicalURL', FULL_BASE_URL . DS . $slug);

        $this->set([
            'tagPageNotes' => ContentfulWrapper::parseMarkdown((isset($tag['tagPageNotes'])) ? $tag['tagPageNotes'] : ''),
            'tagName' => $tag['cityListingTagPageTitle'],
            'cityName' => $city['cityListingName'],
            'citySlug' => $this->_cityNameToSlug($city['cityListingName']),
            'tours' => $tours,
            'slug' => $slug
        ]);

    }

    public function ajax_upcoming_tours($tagSlug, $date = null){
        $this->response->type('json');
        $this->autoRender = false;
        $tours = [];


        $date = explode('-', $date);

        if($cTag = $this->Contentful->getTagPageBySlug($tagSlug)){
            //if bad date then just get the next date

            if(!$date || count($date) != 3 || !checkdate($date[0],$date[1],$date[2])){
                $nextDate = $this->Tag->find('first',[
                    'fields' => ['id'],
                    'conditions' => ['Tag.id' => $cTag['fields']['adminTagId']],
                    'contain' => [
                        'Event' => [
                            'fields' => ['id'],
                            'EventsStage' => [
                                'conditions' => [
                                    'date(datetime) > ' => date('Y-m-d') ],
                                'fields' => ['adults_price','datetime'],
                                'order' => ['datetime asc'],
                                'limit' => 1
                            ],
                            'EventsStagePaxRemaining' => [
                                'fields' => ['pax_remaining'],
                                'conditions' => [
                                    'date(datetime) > ' => date('Y-m-d'),
                                    'pax_remaining >= ' => 1
                                ]
                            ]
                        ]
                    ]
                ]);

                //find smallest date
                $earliestDate = null;
                foreach($nextDate['Event'] as $event){
                    if( !isset($event['EventsStagePaxRemaining']) || count($event['EventsStagePaxRemaining']) == 0 ) continue;
                    foreach($event['EventsStage'] as $slot){
                        $earliestDate = $earliestDate == null ? strtotime($slot['datetime']) : min($earliestDate, strtotime($slot['datetime']));
                    }
                }


                $date = $earliestDate ? explode('-',date('m-d-Y', $earliestDate)) : $earliestDate;
            }
            if($date){
                //get all of the tours that belong to the tag and have a tour on the requested date
                $rawTours = $this->Tag->find('first',[
                    'fields' => ['id'],
                    'conditions' => ['Tag.id' => $cTag['fields']['adminTagId']],
                    'contain' => [
                        'Event' => [
                            'fields' => ['id'],
                            'EventsStage' => [
                                'conditions' => [ 'date(datetime)' => $date[2] . '-' . $date[0] . '-' . $date[1] ],
                                'fields' => ['adults_price','datetime']
                            ],
                            'EventsStagePaxRemaining' => [
                                'fields' => ['pax_remaining'],
                                'conditions' => [
                                    'date(datetime)' => $date[2] . '-' . $date[0] . '-' . $date[1],
                                    'pax_remaining >= ' => 1
                                ]
                            ]
                        ]
                    ]
                ]);

                //format everything and get contentful data
                foreach($rawTours['Event'] as $tour){
                    if(!$tour['EventsStage'] || !isset($tour['EventsStagePaxRemaining']) || count($tour['EventsStagePaxRemaining']) == 0 ) continue;

                    $cTour = $this->Contentful->getTourById($tour['id'])['fields'];

                    foreach($tour['EventsStage'] as $slot){
                        $tours[] = [
                            'time' => date('g:i a', strtotime($slot['datetime'])),
                            'date' => date('m/d/Y', strtotime($slot['datetime'])),
                            'name' => $cTour['tourPageTitleShort'],
                            'price' => ExchangeRate::convert($this->_convertToDbCurrency($this->_getEventCurrency($cTour['eventId']),$slot['adults_price']),false),
                            'duration' => $cTour['tourDuration'],
                            'url' => $this->Contentful->getTourUrlByTourId($tour['id'])
                        ];
                    }


                }
            }




        }


        return json_encode($tours);
    }

    public function sitemap(){
        $this->Components->load('RequestHandler')->respondAs('application/xml');
        $this->layout = false;
        $urls = [];
        //get home page
        $urls[] = '/';
        $urls[] = '/robots.txt';

        //static pages
        $urls[] = '/contact';
        $urls[] = '/cancellation-policy';
        $urls[] = '/privacy-policy';
        $urls[] = '/terms';
        $urls[] = '/about';

        //get cities
        foreach($this->Contentful->getAllCities() as $city){
            //todo update this to use the internal city slug field from contentful
            $citySlug = '/' . $this->_cityNameToSlug($city['fields']['cityListingName']);
            $urls[] = $citySlug;

            //get tags
            foreach($this->Contentful->getTagsByCity($city) as $tag){
                if(!isset($tag['fields']['tagPageURL']) || !$tag['fields']['tagPageURL']) continue;
                $urls[] = '/' . $tag['fields']['tagPageURL'];
            }

            //get tours
            foreach($this->Contentful->getToursByCityId($city['sys']['id']) as $tour){
                $urls[] = $citySlug . '/' . $tour['tourPageURL'];

            }
        }

        foreach($this->Contentful->getAllTourGuides() as $guide){
            $urls[] = '/guide/'.str_replace(' ','-',strtolower($guide['fields']['tourGuideName']));
        }

        $urls = array_map(function($url){ return FULL_BASE_URL . $url; }, $urls);
        $this->set('urls', array_unique($urls));

    }

    public function sitemap_static_pages(){
        $this->Components->load('RequestHandler')->respondAs('application/xml');
        $this->layout = false;
        $urls = [];

        //static pages
        $urls[] = '/contact';
        $urls[] = '/cancellation-policy';
        $urls[] = '/privacy-policy';
        $urls[] = '/terms';
        $urls[] = '/about';
        $urls = array_map(function($url){ return FULL_BASE_URL . $url; }, $urls);
        $this->set('urls', array_unique($urls));
    }

    public function sitemap_city_tours(){
        $this->Components->load('RequestHandler')->respondAs('application/xml');
        $this->layout = false;
        $urls = [];

        //get cities
        foreach($this->Contentful->getAllCities() as $city){
            //todo update this to use the internal city slug field from contentful
            $citySlug = '/' . $this->_cityNameToSlug($city['fields']['cityListingName']);
            $urls[] = $citySlug;
        }
        $urls = array_map(function($url){ return FULL_BASE_URL . $url; }, $urls);
        $this->set('urls', array_unique($urls));
    }

    public function sitemap_tour_details(){
        $this->Components->load('RequestHandler')->respondAs('application/xml');
        $this->layout = false;
        $urls = [];

        //get cities
        foreach($this->Contentful->getAllCities() as $city){
            //todo update this to use the internal city slug field from contentful
            $citySlug = '/' . $this->_cityNameToSlug($city['fields']['cityListingName']);

            //get tours
            foreach($this->Contentful->getToursByCityId($city['sys']['id']) as $tour){
                $urls[] = $citySlug . '/' . $tour['tourPageURL'];
            }
        }
        $urls = array_map(function($url){ return FULL_BASE_URL . $url; }, $urls);
        $this->set('urls', array_unique($urls));
    }

    public function sitemap_tour_compare(){
        $this->Components->load('RequestHandler')->respondAs('application/xml');
        $this->layout = false;
        $urls = [];

        //get cities
        foreach($this->Contentful->getAllCities() as $city){
            //todo update this to use the internal city slug field from contentful
            $citySlug = '/' . $this->_cityNameToSlug($city['fields']['cityListingName']);

            //get tags
            foreach($this->Contentful->getTagsByCity($city) as $tag){
                if(!isset($tag['fields']['tagPageURL']) || !$tag['fields']['tagPageURL']) continue;
                $urls[] = '/' . $tag['fields']['tagPageURL'];
            }
        }
        $urls = array_map(function($url){ return FULL_BASE_URL . $url; }, $urls);
        $this->set('urls', array_unique($urls));
    }

    private function _getAjaxReturn($result) {
        $return = [];
        if( isset($result->status) && $result->status == 'success'){
            $return['success'] = true;
            $return['results'] = $result;
        }else if( isset($result->error) ){
            $return['success'] = false;
            $return['errors'] = (array)$result->error;
        }else{
            $return['success'] = false;
            if(isset($result->message) && $result->message){
                $return['errors'] = [$result->message];
            }else{
                $return['errors'] = $this->_errorsToArray($result->data);
            }

        }
        return $return;
    }

    public function ajax_user_register($data = []){
        $this->autoRender = false;
        $this->response->type('json');

        $return = [];
        $post = empty($data) ? $this->request->data : $data;


        $result = $this->UserApi->register($post['first_name'], $post['last_name'], $post['email'], $post['password'], 'address');

        CakeLog::write('debug', "ajax_user_register result ".print_r($result, true));

        $return = $this->_getAjaxReturn($result);

        CakeLog::write('debug', "ajax_user_register return ".print_r($return, true));

        if($return['success']) {
            $this->Auth->login((array)$return['results']->data->user);
        }

        // then add social provider, if there is any
        $provider = ( isset($post['provider']) && in_array( strtolower($post['provider']), ['facebook', 'google'] ) )
            ? $post['provider'] : '';
        $socialUserId = ( isset($post['socialUserId']) && strlen($post['socialUserId']) > 0 )
            ? $post['socialUserId'] : '';

        if ( $provider != '' && $socialUserId != '' ) {
            $this->UserApi->addSocialProvider($this->Auth->user('id'), $provider, $socialUserId);
        }



        return json_encode($return);
    }




    public function ajax_user_update(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];
        $post = $this->request->data;
        $result = $this->UserApi->userUpdate($this->Auth->user('id'), $post['first_name'], $post['last_name'], $post['email'], $post['mobile_number'], '');

        $return = $this->_getAjaxReturn($result);

        if($return['success']) {
            $this->Auth->login((array)$return['results']->data->user);
        }

        return json_encode($return);
    }

    public function ajax_user_change_password(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $post = $this->request->data;
        $result = $this->UserApi->userChangePassword($this->Auth->user('id'), $post['passwordOld'], $post['passwordNew'], $post['passwordNewVerify']);

        $return['success'] = ( isset($result->status) && $result->status == 'success');
        $return['results'] = $result;

        return json_encode($return);
    }

    public function ajax_user_login(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $post = $this->request->data;
        $result = $this->UserApi->login($post['email'], $post['password']);

         CakeLog::write('debug', "ajax_user_login result ".print_r($result, true));


        $return = $this->_getAjaxReturn($result);

        if($return['success']) {
            $this->Auth->login((array)$return['results']->data->user);
        }

        return json_encode($return);
    }

    public function ajax_user_login_social(){

        CakeLog::write('debug', "ajax_user_login_social  ");

        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $post = $this->request->data;

        $post['password'] = '';

        CakeLog::write('debug', "ajax_user_login_social post ".print_r($post, true));

        // Call login social 
        $result = $this->UserApi->loginSocial($post['provider'], $post['socialUserId']);


        CakeLog::write('debug', "ajax_user_login_social UserApi loginSocial ".print_r($result, true));


        $return['success'] = false;

        // if successfull, get user data
        if( isset($result->status) && $result->status == 'success'){

            CakeLog::write('debug', "ajax_user_login_social UserApi success ");

            $return['success'] = true;
            $return['results'] = $result;

            $this->Auth->login((array)$return['results']->data->user);

        }else if ($result->code == 400){

            CakeLog::write('debug', "ajax_user_login_social UserApi code 400 ");


            return $this->ajax_user_register($post);


        }else if( isset($result->error) ){

            CakeLog::write('debug', "ajax_user_login_social errors present");

            $return['errors'] = (array)$result->error;
        }else{
            $return['errors'] = $this->_errorsToArray($result->data);
        }

        return json_encode($return);
    }

    /**
     * @return string
     */
    public function ajax_user_get(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $data = $this->request->data;

        if (isset($data['facebook_id'])) {
            $url = "/user/".$data['facebook_id']."/social";
        } elseif (isset($data['id'])) {
            $url = "/user/".$data['id'];
        }

        $result = $this->UserApi->getUser($data, $url);

        $return = $this->_getAjaxReturn($result);

        return json_encode($return);
    }

    public function ajax_user_wishlist_add(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $data = $this->request->data;

        $result = $this->UserApi->addToWishlist($this->Auth->user('id'), $data['event_id']);

        $return = $this->_getAjaxReturn($result);

        return json_encode($return);
    }

    public function ajax_user_wishlist_remove(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $data = $this->request->data;

        $result = $this->UserApi->removeFromWishlist($this->Auth->user('id'), $data['event_id']);

        $return = $this->_getAjaxReturn($result);

        return json_encode($return);
    }


    public function ajax_user_social(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $data = $this->request->data;

        $userId = $this->Auth->user('id');

        $provider = ( isset($data['provider']) && in_array( strtolower($data['provider']), ['facebook', 'google'] ) )
            ? $data['provider'] : '';
        $socialUserId = ( isset($data['socialUserId']) && strlen($data['socialUserId']) > 0 )
            ? $data['socialUserId'] : '';
        $socialProviderId = ( isset($data['socialProviderId']) && strlen($data['socialProviderId']) > 0 )
            ? $data['socialProviderId'] : '';

        $return = [];
        if ( $provider != '' && $socialUserId != '' ) {
            $result = $this->UserApi->addSocialProvider($userId, $provider, $socialUserId);
        } else if ( $socialProviderId != '' ) {
            $result = $this->UserApi->removeSocialProvider($userId, $socialProviderId);
        } else {
            $return = ['success' => false];
        }

        return json_encode($return);
    }

    public function ajax_user_signup(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $data = $this->request->data;

        CakeLog::write('debug', "ajax_user_signup ".print_r($data, true));

        $signupName = isset($data['signupName']) ? $data['signupName'] : '';
        $signupEmail = isset($data['signupEmail']) ? $data['signupEmail'] : '';

        $resp = $this->UserApi->signUp($signupName, $signupEmail);

        if( isset($resp->status) && $resp->status == 'success'){
                
            $return = ['success' => true];

            } else if( isset($resp->error) ){

                $errors[] = (array)$resp->error;
                $return['success'] = false;
                $return['errors'] = $errors;

            } else {
                $errors[] = $this->_errorsToArray($resp->data);

                $return['success'] = false;
                $return['errors'] = $errors;

            }

        CakeLog::write('debug', "ajax_user_signup return ".print_r($return, true));
        return json_encode($return);
    }

    public function ajax_user_upcoming_trip(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];
        $data = $this->request->data;
        $trips = count($data['city']);
        $travelers = count($data['first_name']);
        $userId = $data['id'];
        $resultDestination = [];
        $resultTraveler = [];
        $errors = [];

        for ($i = 0; $i < $trips; $i++) {
            $resp = $this->UserApi->addDestination($userId, $data['city'][$i], $data['hotel_name'][$i], $data['hotel_phone'][$i], $data['start_date'][$i], $data['end_date'][$i]);
            if( isset($resp->status) && $resp->status == 'success'){
                $resultDestination[] = $resp;
            }else if( isset($resp->error) ){
                $errors[] = (array)$resp->error;
            } else {
                $errors[] = $this->_errorsToArray($resp->data);
            }
        }

        for ($i = 0; $i < $travelers; $i++) {
            $resp = $this->UserApi->addTraveler($userId, $data['first_name'][$i], $data['last_name'][$i], $data['email'][$i], $data['phone'][$i]);
            if( isset($resp->status) && $resp->status == 'success'){
                $resultTraveler[] = $resp;
            }else if( isset($resp->error) ){
                $errors[] = (array)$resp->error;
            } else {
                $errors[] = $this->_errorsToArray($resp->data);
            }
        }

        if( $trips == count($resultDestination) && $travelers == count($resultTraveler) ){
            $return['success'] = true;
        } else {
            $return['success'] = false;
            $return['errors'] = $errors;
        }
        $return['results'] = ['destinations' => $resultDestination, 'travelers' => $resultTraveler];

        return json_encode($return);
    }
    
    public function ajax_user_booking_cancel(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];

        $flashMessage = 'An error occured. Please email us directly';
        $flashType = 'error';

        $data = $this->request->data;

        $cancelDescriptions = $this->_getCancelOptions();

        $selectedOption = $data['selectedOption'];
        $reasonArrPieces = explode('_',$selectedOption);
        $reasonIndex = intval($reasonArrPieces[1]) - 1;        

        $description = $cancelDescriptions[$reasonIndex];

        //Append the customer 'option cancel option' to the description of why cancelling
        if (count($cancelDescriptions) == intval($reasonArrPieces[1])) {
            $description .= ' ' . $data['otherOption'];
        }
            
        $result = $this->UserApi->postBookingCancel($this->Auth->user('id'), $data['bookingDetailsId'], $description);
        $return = $this->_getAjaxReturn($result);
        
        if($return['success']) {
            $flashMessage = 'Your cancellation request has been submitted.  Thank you';
            $flashType = 'status';
        }
        $this->Session->setFlash($flashMessage, "FlashMessage/{$flashType}");

        return json_encode($return);
    }
    

    public function ajax_user_forgot_password(){
        $this->autoRender = false;
        $this->response->type('json');
        $return = [];
        $data = $this->request->data;
        $result = $this->UserApi->passwordEmail($data['email']);

        CakeLog::write('debug', "ajax_user_forgot_password result ".print_r($result, true));

        $return = $this->_getAjaxReturn($result);
        return json_encode($return);
    }

    public function account(){
        $this->_redirectNonAuth();

        $result = $this->UserApi->getTourList($this->Auth->user('id'));
        $upcomingTours = [];
        $apiUpcomingTours = [];
        $today = date('Y-m-d');
        if ($result->status == 'success' && count($result->data) > 0 && count($result->data->tours) > 0) {
            $tours = $result->data->tours;
            foreach ($tours as $tour) {
                if( strtotime($today) <= strtotime($tour->tourDateTime) ) {
                    $price = isset($tour->exchange_amount) ? $tour->exchange_amount : 0;
                    $price = ExchangeRate::format($price, $tour->exchange_to);
                    $tourContentful = $this->Contentful->getTourById($tour->tourEventId);
                    if (!isset($tourContentful['fields'])) continue;
                    $tourContentful = $tourContentful['fields'];
                    $upcomingTours[] = [
                        'citySlug' => $this->Contentful->getCitySlugById($tourContentful['tourCity']),
                        'slug' => $tourContentful['tourPageURL'],
                        'name' => utf8_decode($tour->name),
                        'tourDateTime' => $tour->tourDateTime,
                        'bookingTime' => $tour->booking_time,
                        'bookingDetailsId' => $tour->bookingDetailsId,
                        'guests' => $tour->number_adults + $tour->number_students + $tour->number_children + $tour->number_seniors + $tour->number_infants,
                        'price' => $price,
                    ];
                }
            }
        }
        $this->set('upcomingTours', $upcomingTours);

    }

    public function upcomingTourDetail($city, $slug, $bookingDetailsId){
        $this->_redirectNonAuth();

        $result = $this->UserApi->getTourList($this->Auth->user('id'));

        $upcomingTours = [];
        $today = date('Y-m-d');
        if ($result->status == 'success' && count($result->data) > 0 && count($result->data->tours) > 0) {
            $tours = $result->data->tours;
            foreach ($tours as $tour) {
                if( strtotime($today) <= strtotime($tour->tourDateTime) ) {
                    $upcomingTours[] = $tour;
                }
            }
        }

        $currentTour = [];
        foreach ($upcomingTours as $upcomingTour) {
            if ($upcomingTour->bookingDetailsId == $bookingDetailsId) {
                $currentTour = (array)$upcomingTour;
                break;
            }
        }

        $contentfulTour = $this->Contentful->getTourBySlug($slug);

        if ( empty($currentTour) ||  !isset($contentfulTour['fields']) ) {
            $this->redirect('/account');
        }

        $contentfulTourId = $contentfulTour['sys']['id'];
        $contentfulTour = $contentfulTour['fields'];

        $tourListingImage = isset($contentfulTour['tourListingImage']) ? $this->Contentful->getImageAssetUrl($contentfulTour['tourListingImage']) : '';

        $meetingPoints = $this->Contentful->getMeetingPointsTourEventId($contentfulTour['eventId']);
        $meetingPoints = $meetingPoints[0]['fields'];

        $meetingPointMaps = isset($meetingPoints['meetingPointMaps'][0]) ? $this->Contentful->getImageAssetUrl($meetingPoints['meetingPointMaps'][0]) : '';
        $meetingPointImages = isset($meetingPoints['meetingPointImages'][0]) ? $this->Contentful->getImageAssetUrl($meetingPoints['meetingPointImages'][0]) : '';

        $googleMapPlaceId = '';
        if ( isset($meetingPoints['googleMapLink']) ) {
            $googleMapPlaceId = explode('&', $meetingPoints['googleMapLink'])[0];
        }

        $googleMapLink = isset($meetingPoints['meetingPointMaps'][0]) ? $this->Contentful->getImageAssetUrl($meetingPoints['meetingPointMaps'][0]) : $meetingPointMaps;

        // for part two of the tour
        $part2MeetingPointMaps = isset($meetingPoints['part2MeetingPointMaps'][0]) ? $this->Contentful->getImageAssetUrl($meetingPoints['part2MeetingPointMaps'][0]) : '';
        $part2MeetingPointImages = isset($meetingPoints['part2MeetingPointImages'][0]) ? $this->Contentful->getImageAssetUrl($meetingPoints['part2MeetingPointImages'][0]) : '';
        $part2GoogleMapPlaceId = '';
        if ( isset($meetingPoints['part2GoogleMapLink']) ) {
            $part2GoogleMapPlaceId = explode('&', $meetingPoints['part2GoogleMapLink'])[0];
        }
        $part2GoogleMapLink = ($part2MeetingPointMaps != '') ? $this->Contentful->getImageAssetUrl($part2MeetingPointMaps) : $part2MeetingPointMaps;

        // for the new meeting point
        $newMeetingPointMaps = isset($meetingPoints['newMeetingPointMaps'][0]) ? $this->Contentful->getImageAssetUrl($meetingPoints['newMeetingPointMaps'][0]) : '';
        $newMeetingPointImages = isset($meetingPoints['newMeetingPointImages'][0]) ? $this->Contentful->getImageAssetUrl($meetingPoints['newMeetingPointImages'][0]) : '';
        $newGoogleMapPlaceId = '';
        if ( isset($meetingPoints['newGoogleMapsLink']) ) {
            $newGoogleMapPlaceId = explode('&', $meetingPoints['newGoogleMapsLink'])[0];
        }
        $newGoogleMapLink = ($newMeetingPointMaps != '') ? $this->Contentful->getImageAssetUrl($newMeetingPointMaps) : $newMeetingPointMaps;

        $tourDetail = [
            'api' => $currentTour,
            'contentful' => $contentfulTour,
            'image' => $tourListingImage,
            'meetingPoints' => $meetingPoints,
            'meetingPointMaps' => $meetingPointMaps,
            'meetingPointImages' => $meetingPointImages,
            'googleMapPlaceId' => $googleMapPlaceId,
            'part2GoogleMapLink' => $part2GoogleMapLink,
            'part2MeetingPointMaps' => $part2MeetingPointMaps,
            'part2MeetingPointImages' => $part2MeetingPointImages,
            'part2GoogleMapPlaceId' => $part2GoogleMapPlaceId,
            'newMeetingPointMaps' => $newMeetingPointMaps,
            'newMeetingPointImages' => $newMeetingPointImages,
            'newGoogleMapPlaceId' => $newGoogleMapPlaceId,
            'tourUrl' => FULL_BASE_URL.DS . $city .DS. $slug,
        ];

        //get faq
        $faqs = [];
        foreach ($this->Contentful->getFaqById($contentfulTourId) as $faq) {
            $faqs[] = [
                'question' => $faq['fields']['faqQuestion'],
                'answer' => $faq['fields']['faqAnswer']
            ];
        }

        $this->set('tourDetail', $tourDetail);
        $this->set('faqs', $faqs);

    }

    public function upcomingTourCancel($bookingDetailsId){
        $result = $this->UserApi->getTourList($this->Auth->user('id'));
        $upcomingTour = [];
        if ($result->status == 'success' && count($result->data) > 0 && count($result->data->tours) > 0) {
            $tours = $result->data->tours;
            foreach ($tours as $tour) {
                if( $bookingDetailsId == $tour->bookingDetailsId ) {
                    $upcomingTour = (array)$tour;
                    break;
                }
            }
        }

        if ( empty($upcomingTour) ) {
            $this->redirect('/account');
        }

        $contentfullTour = $this->Contentful->getTourById(intval($upcomingTour['tourEventId']));
        $contentfullTour = $contentfullTour['fields'];
        $cancelDescriptions = $this->_getCancelOptions();

        $this->set([
            'upcomingTour' => $upcomingTour,
            'contentfullTour' => $contentfullTour,
            'cancelDescriptions' => $cancelDescriptions,
            'backUrl' => '/upcoming/' . $this->Contentful->getCitySlugById($contentfullTour['tourCity']) . DS. $contentfullTour['tourPageURL'] . DS . $bookingDetailsId,
        ]);
    }

    public function pastTourRefund($bookingDetailsId){
        $result = $this->UserApi->getTourList($this->Auth->user('id'));
        $pastTour = [];
        if ($result->status == 'success' && count($result->data) > 0 && count($result->data->tours) > 0) {
            $tours = $result->data->tours;
            foreach ($tours as $tour) {
                if( $bookingDetailsId == $tour->bookingDetailsId ) {
                    $pastTour = (array)$tour;
                    break;
                }
            }
        }

        if ( empty($pastTour) ) {
            $this->redirect('/account');
        }

        $contentfullTour = $this->Contentful->getTourById(intval($pastTour['tourEventId']));
        $contentfullTour = $contentfullTour['fields'];

        $refundDescriptions = $this->_getRefundOptions();
        
        $this->set([
            'pastTour' => $pastTour,
            'refundDescriptions' => $refundDescriptions,
            'bookingDetailsId' => $bookingDetailsId,
            'contentfullTour' => $contentfullTour
        ]);
    }
    
    private function _getRefundOptions(){
        return [
            "I had an issue with my guide",
            "I didn't take my tour (could not find my guide, lost my guide or arrived late)",
            "I took the tour but did not receive the full tour (e.g. a site was closed)",
            "I had issues with my headset",
            "I had an issue with the transport on my tour",
            "I did not enjoy my tour or felt it was falsely advertised"
        ];
    }

    private function _getCancelOptions(){
        return [
            "My plans have changed and I will be unable to take my tour",
            "I made a duplicate booking",
            "I had an issue with my guide",
            "I didn't take my tour (could not find my guide, lost my guide or arrived late)",
            "I took the tour but did not receive the full tour (e.g. a site was closed)",
            "I had issues with my headset",
            "I had an issue with the transport on my tour",
            "I did not enjoy my tour or felt it was falsely advertised",
            "Other, please note below:"
        ];
    }

    public function ajax_user_booking_refund(){
        $this->autoRender = false;
        $this->response->type('json');
        
        $return = [];
        $flashMessage = 'An error occured. Please email us directly';
        $flashType = 'error';
            
        $postData = $this->request->data;
        $bookingDetailsId = $postData['bookingDetailsId'];
        $selectedOption = $postData['selectedOption'];

        $refundDescriptions = $this->_getRefundOptions();
        
        $reasonArr = explode('_',$selectedOption);
        $reasonIndex = intval($reasonArr[1]) - 1;        
        $description = $refundDescriptions[$reasonIndex];

        $result = $this->UserApi->refundRequest($this->Auth->user('id'), $bookingDetailsId, $description);
        $return = $this->_getAjaxReturn($result);
        if($return['success']) {
            $flashMessage = 'Your refund request has been submitted.  Thank you';
            $flashType = 'status';
        }
        $this->Session->setFlash($flashMessage, "FlashMessage/{$flashType}");
        return json_encode($return);
    }
    
    public function wishlist(){
        $this->_redirectNonAuth();

        // get wishlist
        $result = $this->UserApi->getWishlist($this->Auth->user('id'));
        $tours = [];
        $wishlistTours = [];
        if ($result->status == 'success' && count($result->data) > 0 && count($result->data->tours) > 0) {
            $tours = $result->data->tours;
        }

        // get tours from contentful
        foreach ($tours as $tour) {
            $contentfulTour = $this->Contentful->getTourById($tour->tourEventId);
            $contentfulTour = $contentfulTour['fields'];
            $wishlistTours[] = [
                'event_id' => $tour->tourEventId,
                'price' => $tour->price,
                'image' => isset($contentfulTour['tourListingImage']) ? $this->Contentful->getImageAssetUrl($contentfulTour['tourListingImage']) : '',
                'title' => $contentfulTour['tourTitleLong'],
                'description' => $contentfulTour['tourPageShortDescriptionComparative'],
                'citySlug' => $this->Contentful->getCitySlugById($contentfulTour['tourCity']),
                'slug' => $contentfulTour['tourPageURL'],
            ];
        }
        $this->set('wishlistTours', $wishlistTours);
    }

    public function past_tours(){
        $this->_redirectNonAuth();

        $result = $this->UserApi->getTourList($this->Auth->user('id'));
        $pastTours = [];
        $today = date('Y-m-d');
        if ($result->status == 'success' && count($result->data) > 0 && count($result->data->tours) > 0) {
            $tours = $result->data->tours;
            foreach ($tours as $tour) {
                if( strtotime($today) > strtotime($tour->tourDateTime) ) {
                    $pastTours[] = $tour;
                }
            }
        }
        $this->set('pastTours', $pastTours);
    }
    
    public function settings(){
        $this->_redirectNonAuth();

        $result = $this->UserApi->getSocialProvider($this->Auth->user('id'));
        $return = $this->_getAjaxReturn($result);

        if($return['success']) {
            $return = (array)$return['results']->data->socialProviders;
        } else {
            $return = [];
        }

        $facebook = ['socialProviderId' => '', 'socialUserId' => '', 'status' => 'not connected', 'class' => ''];
        $google = ['socialProviderId' => '', 'socialUserId' => '', 'status' => 'not connected', 'class' => ''];
        foreach ($return as $socialProvider) {
            if (strtolower($socialProvider->provider) == 'facebook') {
                $facebook['socialProviderId'] = $socialProvider->id;
                $facebook['socialUserId'] = $socialProvider->socialUserId;
                $facebook['status'] = 'Connected';
                $facebook['class'] = 'connected';
            } else if (strtolower($socialProvider->provider) == 'google') {
                $google['socialProviderId'] = $socialProvider->id;
                $google['socialUserId'] = $socialProvider->socialUserId;
                $google['status'] = 'Connected';
                $google['class'] = 'connected';
            }
        }

        $this->set('socialProviders', $return);
        $this->set('facebook', $facebook);
        $this->set('google', $google);

    }

    public function guideProfile($tourGuideName) {
        $tourGuideInfo = $this->Contentful->getTourGuideByName(ucwords(str_replace('-',' ',$tourGuideName)));
        $tourGuide = $tourGuideInfo['items'][0]['fields'];

        $city = $this->Contentful->getCityById($tourGuide['tourGuideCity']);
        $cityName = $city['fields']['cityListingName'];

        $country = $this->Contentful->getCountryById($tourGuide['tourGuideCountry']);
        $countryName = $country['fields']['countryName'];

        $tourGuideImage = $this->Contentful->getImageAssetUrl($tourGuide['tourGuideImage'][0]);

        $tours = [];
        if (isset($tourGuide['tourGuideFeaturedTour'])) {
            foreach ($tourGuide['tourGuideFeaturedTour'] as $tourFeatured) {
                $tour = $this->Contentful->getTourById($tourFeatured);
                $tour = $tour['fields'];
                $tours[] = [
                    'citySlug' => $this->Contentful->getCitySlugById($tour['tourCity']),
                    'slug' => $tour['tourPageURL'],
                    'titleShort' => $tour['tourPageTitleShort'],
                ];
            }
        }

        $this->set([
            'user' => $this->Auth->user(),
            'fullName' => $tourGuide['tourGuideName'],
            'tourGuideImage' => $tourGuideImage,
            'country' => $countryName,
            'city' => $cityName,
            'description' => $tourGuide['tourGuideFeaturedDescription'],
            'quote' => $tourGuide['guidePullQuote'],
            'descriptionLong' => ContentfulWrapper::parseMarkdown($tourGuide['guideLongCopy']),
            'tours' => $tours
        ]);
    }

    public function contact(){
	$loggedIn = (isset($this->Auth->user()['id'])) ? true : false;
        $this->set('loggedIn', $loggedIn);
    }

    public function cancellation_policy(){
    }

    public function privacy_policy(){
    }

    public function about(){
    }

    public function terms(){
    }

    private function _redirectNonAuth(){
        if(empty($this->Auth->user())) $this->redirect('/');
    }

    public function logout(){
        $this->Auth->logout();
        $this->redirect('/');
    }

    public function forgot_password(){
    }



    public function resend_voucher($booking_detail_id){
        // If No User Redirect
        if(empty($this->Auth->user())) $this->redirect('/');
	
        $user = $this->Auth->user();
	$resendResult = $this->UserApi->bookingVoucher(
            $user['id'], 
            $booking_detail_id 
        );
        if ($resendResult->code == 200){
            $flashType = 'status';
            $flashMessage = 'Voucher email has been resent';
        } else {
            $flashType = 'error';
            $flashMessage = 'Error: Please contact Customer Service';
        }

        $this->Session->setFlash($flashMessage, "FlashMessage/{$flashType}");
        $this->redirect('/account');

    }


    public function password_reset(){
	//pfh

        $query_data = $this->request->query;
        $hash = $query_data['key'];
 

        if ($hash){
	    // request client data, based on hash
            $result = $this->UserApi->userPasswordResetKey($hash);
            if ($result->code == 200){
            	$this->set('client',$result->data->user);
                $this->set('hashNotFound',false);
            } else {
                $this->set('hashNotFound',true);
            }
        } else {
            // If there is no Hash, redirect to homepage
            $this->redirect('/');
        }

        // Data Posted Back After Password Reset
        if($this->request->is(['post'])) {
            if ($this->request->data['reset_hash'] == $result->data->user->reset_hash){
                $resetResult = $this->UserApi->userUpdatePassword(
		    $this->request->data['id'], 
		    $this->request->data['password'] 
		);
                if ($resetResult->code == 200){
		    //$loginResult = $this->UserApi->login(
                    //    $resetResult->data->user->email, 
                    //    $this->request->data['password']
                    //);

                    $this->Auth->login((array)$resetResult->data->user);
                    $this->redirect('/account');
		}

            } else {
		// Hash does not match, before reset request is sent, redirect to home
                $this->redirect('/');
		
            }




            // POST
            // http://staging-userapi.walks.org/v1/user/152880
	} else {

		// Page from Get

	}
    }

    public function login(){

    }

    private function _errorsToArray($errors){

         CakeLog::write('debug', "_errorsToArray errors ".print_r($errors, true));


        $errorMsgs = [];
        foreach($errors as $key => $errorArr){
            foreach($errorArr as $error){
                $errorMsgs[] = $error;
            }
        }

        CakeLog::write('debug', "_errorsToArray errorMsgs ".print_r($errorMsgs, true));
        return $errorMsgs;
    }

    private function _getTourRating($tourId){
        $key = 'tour_rating_' . $tourId;
        $rating = Cache::read($key, 'long');
        if(!$rating){
            $rating = $this->getTourRatingCache($tourId, 'long');
        }

        return $rating;
    }

    private function _getNextTourTicket($tourId){
        return $this->EventsStage->find('first',[
           'conditions' => [
               'events_id' => $tourId,
               'datetime >' => date('Y-m-d H:i:s'),
           ],
           'fields' => ['datetime','adults_price'],
           'limit' => 1
        ]);

    }
    private function _cityNameToSlug($cityName){
        return str_replace(' ','-',strtolower($cityName)) . '-tours';
    }

}

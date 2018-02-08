<?php

/**
 * Class ContentfulWrapper
 * Documentation:
 * https://www.contentful.com/developers/docs/references/content-delivery-api/
 * https://app.contentful.com/spaces/twizyp0t114t/api/content_model
 */
use Ciconia\Ciconia;

class ContentfulWrapper {

    private $_spaceId;
    private $_token;
    private $_apiUrl;
    private $_spaceUrl;

    public function __construct() {
        // Space ID
        $this->_spaceId = "twizyp0t114t";
        // Dev Key. Content Delivery API
        $this->_token = "c64e7fa2fad30d82a6d0b8a90c5c1f621aef1f0dbb7cc0aad0743346e3761e30";
        // Dev Key. Content Preview API
        // $this->_token = "e50a0532f2c8c6bf189f2fedaa9d93aa6683c58156df342c059eb7c3fe927ca7";

        $this->_apiUrl = "https://cdn.contentful.com";
        $this->_spaceUrl = $this->_apiUrl ."/spaces/".$this->_spaceId;

    }

    /*********************************************************************************************************
     * *******************************************************************************************************
     * Cache Functions
     * *******************************************************************************************************
     *********************************************************************************************************/

    /**
     * @param $countryId
     * @param $cacheConfig
     * @return array
     */
    public function getCountryByIdCache($countryId, $cacheConfig) {
        if(is_array($countryId)) $countryId = $countryId['sys']['id'];
        $key = 'contentful_country_' . $countryId;
        $countries = $this->_getUrl('content_type=countries&sys.id=' . $countryId);
        $country = ($countries['total'] == 0) ? [] : $countries['items'][0];
        Cache::write($key,$country,$cacheConfig);
        return $country;
    }

    /**
     * @param $cityId
     * @param $cacheConfig
     * @return null
     */
    public function getCityByIdCache($cityId, $cacheConfig){
        if(is_array($cityId)) $cityId = $cityId['sys']['id'];
        $key = 'contentful_city_' . $cityId;
        $cities = $this->_getUrl('content_type=city&sys.id=' . ucwords($cityId));
        $city = ($cities['total'] == 0) ? null : $cities['items'][0];
        Cache::write($key,$city,$cacheConfig);
        return $city;
    }

    /**
     * @param $tourId
     * @param $cacheConfig
     * @return array
     */
    public function getTourByIdCache($tourId, $cacheConfig, $altTourId = "") {
        $tourId = $this->_getTourId($tourId);
        $key = 'contentful_tour_' . md5($tourId);
        if(is_numeric($tourId)){
            $url = $this->_entriesUrl() . "&content_type=tour&fields.eventId=" . $tourId;
        }elseif(is_array($tourId)){
            $url = $this->_entriesUrl() . "&content_type=tour&sys.id=" . $tourId['sys']['id'];
        }else{
            $url = $this->_entriesUrl() . "&content_type=tour&sys.id=" . $tourId;
        }
        $response = $this->_curlRequest($url);
        $result = $response['items'] ? $response['items'][0] : [];

        Cache::write($key, $result,$cacheConfig);
        if($altTourId != "") {
            $key = 'contentful_tour_' . md5($altTourId);
            Cache::write($key, $result, $cacheConfig);
        }
        return $result;
    }

    /**
     * @param $styleCid
     * @param $cacheConfig
     * @return mixed
     */
    public function getTourStyleCache($styleCid, $cacheConfig){
        if(is_array($styleCid)) $styleCid = $styleCid['sys']['id'];
        $key = 'contentful_style_' . $styleCid;
        $url = $this->_entriesUrl() . "&content_type=tourStyles&sys.id=" . $styleCid;
        $style= $this->_curlRequest($url);
        Cache::write($key, $style, $cacheConfig);
        return $style;
    }

    /**
     * @param $slug
     * @param $cacheConfig
     * @return array
     */
    public function getTagPageBySlugCache($slug, $cacheConfig){
        $key = 'contentful_tag_' . $slug;
        $url = $this->_entriesUrl() . "&content_type=category&fields.tagPageURL={$slug}";
        $results = $this->_curlRequest($url);
        $tag = ($results['total'] > 0) ? $results['items'][0] : [];
        Cache::write($key, $tag, $cacheConfig);
        return $tag;
    }

    public function getTagsByCityCache($cityCid, $cacheConfig){
        if(is_array($cityCid)) $cityCid = $cityCid['sys']['id'];
        $key = 'contentful_tag_city_' . $cityCid;
        $url = $this->_entriesUrl() . "&content_type=category&fields.tagPageCity.sys.id={$cityCid}&order=fields.tagPageListingOrder";
        $results = $this->_curlRequest($url);
        $results = ($results['total'] > 0) ? $results['items'] : [];
        Cache::write($key, $results, $cacheConfig);
        return $results;
    }


    /*********************************************************************************************************
     * *******************************************************************************************************
     * ContentfulWrapper Functions
     * *******************************************************************************************************
     *********************************************************************************************************/

    private function _entriesUrl(){
        return $this->_spaceUrl . "/entries?access_token=" . $this->_token;
    }

    private function _curlRequest($curlURL){
        $process = curl_init($curlURL);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response_raw = curl_exec($process);
        $info = curl_getinfo($process);
        $error = curl_errno($process);
        curl_close($process);
        $response = json_decode($response_raw, true);

        // TODO handle errors
        if ($info['http_code'] == 200 && $error == 0){

        }

        return $response;
    }

    private function _getUrl($url){
        $url = $this->_entriesUrl() . '&' .$url;
        return $this->_curlRequest($url);
    }

    public function getCountries(){
        return $this->_getUrl('content_type=countries&order=fields.Order');
    }

    public function getCountryById($countryId){
        if(is_array($countryId)) $countryId = $countryId['sys']['id'];
        $key = 'contentful_country_' . $countryId;

        if(!$country = Cache::read($key,'short')){
            $country = $this->getCountryByIdCache($countryId,'short');
        }

        return $country;

    }
    public function getCountryCity($countryCId){
        return $this->_getUrl('content_type=city&fields.country.sys.id=' . $countryCId . '&order=fields.order');
    }

    public function getAllCities(){
        $cities = $this->_getUrl('content_type=city');
        return $cities['items'];
    }

    public function getCity($city){
        if (is_array($city)) {
            $city = $city['cityListingName'];
        }
        $cities = $this->_getUrl('content_type=city&fields.cityListingName=' . urlencode(ucwords($city)));
        if($cities['total'] == 0) return null;
        return $cities['items'][0];
    }

    public function getCityById($cityId){
        if(is_array($cityId)) $cityId = $cityId['sys']['id'];
        $key = 'contentful_city_' . $cityId;

        if(!$city = Cache::read($key,'short')){
            $city = $this->getCityByIdCache($cityId, 'short');
        }

        return $city;

    }

    public function getCitySlugById($cityId){
        $city = $this->getCityById($cityId);
        if($cityId){
            return str_replace(' ', '-',strtolower($city['fields']['cityListingName'])) . '-tours';
        }
        return null;
    }

    public function getCities(){
        $data = [];
        $url = $this->_entriesUrl() . "&content_type=city";
        $response = $this->_curlRequest($url);
        if (isset($response['total']) && $response['total'] > 0){
            foreach ($response['items'] as $item) {
                $e = $item['fields'];
                $e['id'] = $item['sys']['id'];
                $data[] = $e;
            }
        }
        return $data;
    }

    /**
     * @param $cityId
     * @return array
     */
    public function getToursByCityId($cityId){
        $data = [];
        $url = $this->_entriesUrl() . "&content_type=tour&fields.tourCity.sys.id=".$cityId;
        $response = $this->_curlRequest($url);

        if (isset($response['total']) && $response['total'] > 0){
            foreach ($response['items'] as $item) {
                $e = $item['fields'];
                $e['id'] = $item['sys']['id'];
                $data[] = $e;
            }
        }
        return $data;

    }

    /**
     * @param string $id
     * @param string $field, possible values: 'fields.eventId' || 'sys.id' || 'fields.city.sys.id'
     * @return array
     */
    public function getTours($id = '', $field = 'fields.eventId'){
        $data = [];
        $url = $this->_entriesUrl() . "&content_type=tour";
        $url = ($id == '') ? $url : $url . "&" . $field . "=" . $id;
        $response = $this->_curlRequest($url);
        if (isset($response['total']) && $response['total'] > 0){
            foreach ($response['items'] as $item) {
                $e = $item['fields'];
                $e['id'] = $item['sys']['id'];
                $data[] = $e;
            }
        }
        return $data;
    }

    /**
     * i.e //https://cdn.contentful.com/spaces/twizyp0t114t/entries?access_token=c64e7fa2fad30d82a6d0b8a90c5c1f621aef1f0dbb7cc0aad0743346e3761e30&content_type=category&fields.eventId=207
     * @param string $id
     * @param string $field, possible values: 'fields.eventId' || 'sys.id' || 'fields.city.sys.id'
     * @return array
     */
    public function getTourTags($id = '', $field = 'fields.eventId'){
        $data = [];
        $url = $this->_entriesUrl() . "&content_type=category";
        $url = ($id == '') ? $url : $url . "&" . $field . "=" . $id;
        $response = $this->_curlRequest($url);
        if (isset($response['total']) && $response['total'] > 0){
            $data['items'] = [];
            $data['assets'] = [];
            foreach ($response['items'] as $item) {
                $e = $item['fields'];
                $e['id'] = $item['sys']['id'];

                if (is_array($e)){
                    foreach ($e as $key => $fieldValues) {
                        if ($key == 'introImages'){
                            $images = [];
                            foreach ($fieldValues as $fieldValue) {
                                $images[$fieldValue['sys']['id']] = $fieldValue['sys']['id'];
                            }
                            $e[$key] = $images;
                        }
                        if (strpos($key, 'highlightImage') !== false){
                            $images = [];
                            if (isset($fieldValues['sys'])){
                                $images[$fieldValues['sys']['id']] = $fieldValues['sys']['id'];
                            } else {
                                foreach ($fieldValues as $fieldValue) {
                                    $images[$fieldValue['sys']['id']] = $fieldValue['sys']['id'];
                                }
                            }
                            $e[$key] = $images;
                        }
                    }
                }

                $data['items'][] = $e;
            }
            foreach ($response['includes']['Asset'] as $item) {
                $e = [];
                $e['title'] = $item['fields']['title'];
                $e['description'] = (isset($item['fields']['description'])) ? $item['fields']['description'] : '';
                $e['url'] = $item['fields']['file']['url'];
                $data['assets'][$item['sys']['id']] = $e;
            }

            foreach ($data['items'] as $i => $e) {
                if (is_array($e)){
                    foreach ($e as $key => $fieldValues) {
                        if ($key == 'introImages' || strpos($key, 'highlightImage') !== false){
                            $imagesArray = [];
                            foreach ($fieldValues as $imageId => $ImageValue) {
                                foreach ($data['assets'] as $key2 => $asset) {
                                    if ($key2 == $imageId){
                                        $imagesArray[] = $asset;
                                        break;
                                    }
                                }
                            }
                            $data['items'][$i][$key] = $imagesArray;
                        }
                    }
                }
            }
        }
        unset($data['assets']);
        return $data;
    }

    public static function parseMarkdown($str){
        $ciconia = new Ciconia();
        $str = $ciconia->render($str);
        return $str;
    }

    public function getAsset($assetId, $onlyUrl=0){
        if(is_array($assetId)){
            $assetId = $assetId['sys']['id'];
        }
        $url = $this->_spaceUrl . "/assets/{$assetId}?access_token=" . $this->_token;
        $response = $this->_curlRequest($url);
        if($onlyUrl) return isset($response['fields']) ? $response['fields']['file']['url'] : null;

        return $response;
    }

    public function getEntry($asset, $onlyFields = true){
        if(is_array($asset)){
            $asset = $asset['sys']['id'];
        }

        $url = $this->_spaceUrl . "/entries/{$asset}?access_token=" . $this->_token;
        $response = $this->_curlRequest($url);

        if($onlyFields) return $response['fields'];

        return $response;
    }

    public function getFaqById($contentfulTourId){
        $url = $this->_entriesUrl() . "&content_type=faq&fields.faqRelatedTours.sys.id=" .$contentfulTourId . '&order=fields.faqPriority';
        $response = $this->_curlRequest($url);
        return $response['items'] ? $response['items'] : [];
    }

    public function getFaqByTagId($tagId){
        //
        $url = $this->_entriesUrl() . "&content_type=faq&fields.faqRelatedTagPage.sys.id=" . $tagId . '&order=fields.faqPriority';
        $response = $this->_curlRequest($url);
        return $response['items'] ? $response['items'] : [];
    }

    public function getImageAssetUrl($asset){
        if(is_array($asset)){
            $asset = $asset['sys']['id'];
        }

//        return str_replace('downloads.','images.', $this->getAsset($asset, true));
        return $this->getAsset($asset, true);
    }

    private function _getTourId($tour) {
        $tourId = null;
        if(is_numeric($tour)){
            $tourId = $tour;
        }elseif(is_array($tour)){
            $tourId = $tour['sys']['id'];
        }else{
            $tourId = (string) $tour;
        }
        return $tourId;
    }

    public function getTourById($tourId){
        $tourId = $this->_getTourId($tourId);
        $key = 'contentful_tour_' . md5($tourId);
        if(!$result = Cache::read($key, 'short')){
            $result = $this->getTourByIdCache($tourId, 'short');
        }
        return $result;
    }

    public function getTourBySlug($slug){
        $url = $this->_entriesUrl() . "&content_type=tour&fields.tourPageURL=" . $slug;
        $content = $this->_curlRequest($url);

        return $content['total'] > 0 ? $content['items'][0] : [];
    }

    public function getHomePage(){
        $url = $this->_entriesUrl() . "&content_type=homepage";
        return $this->_curlRequest($url);
    }

    public function getFeaturedTourReviews($cTourId){
        $url = $this->_entriesUrl() . "&content_type=featuredTourPageReviews&fields.tour.sys.id=" . $cTourId;
        $results = $this->_curlRequest($url);
        $featuredReviews = [];
        if($results['total'] > 0){
            foreach($results['items'] as $item){
                $featuredReviews[] = $item['fields'];
            }
            //sort reviews
//            usort($featuredReviews,function($a, $b){
//                return $a['order'] > $b['order'];
//            });
        }

        return $featuredReviews;

    }

    public function getTourStyles(){
        $url = $this->_entriesUrl() . "&content_type=tourStyles";
        return $this->_curlRequest($url);
    }

    public function getTourStyle($styleCid){
        if(is_array($styleCid)) $styleCid = $styleCid['sys']['id'];

        $key = 'contentful_style_' . $styleCid;

        if(!$style = Cache::read($key,'long')) {
            $style = $this->getTourStyleCache($styleCid, 'long');
        }
        return $style;
    }

    public function getTagPageBySlug($slug){
        $key = 'contentful_tag_' . $slug;

        if(!$tag = Cache::read($key,'short')) {
            $tag = $this->getTagPageBySlugCache($slug, 'short');
        }

        return $tag;
    }

    public function getTagsByCity($cityCid){
        if(is_array($cityCid)) $cityCid = $cityCid['sys']['id'];
        $key = 'contentful_tag_city_' . $cityCid;
        if(!$results = Cache::read($key,'short')) {
            $results = $this->getTagsByCityCache($cityCid, 'short');
        }
        return $results;
    }

    //get all tags that have matching database ids
    public function getTagsByDbIds($tagIds){
        $tagIds = implode(',', $tagIds);
        $url = $this->_entriesUrl() . "&content_type=category&fields.adminTagId[in]={$tagIds}&order=fields.tagPageListingOrder";
        $results = $this->_curlRequest($url);


        return $results['total'] > 0 ? $results['items'] : [];
    }

    public function getListingTourFlag($flagCid){
        if(is_array($flagCid)) $flagCid = $flagCid['sys']['id'];
        $url = $this->_entriesUrl() . "&content_type=listingPageFlags&sys.id={$flagCid}";
        return $this->_curlRequest($url);
    }

    public function getTourGuideById($tourGuideId){
        if(is_array($tourGuideId)) $tourGuideId = $tourGuideId['sys']['id'];
        $url = $this->_entriesUrl() . "&content_type=tourGuideProfiles&sys.id=" . $tourGuideId;
        $results = $this->_curlRequest($url);
        return ($results['total'] > 0) ? $results : [];
    }

    public function getAllTourGuides(){
        $url = $this->_entriesUrl() . "&content_type=tourGuideProfiles";
        $results = $this->_curlRequest($url);
        return ($results['total'] > 0) ? $results['items'] : [];
    }

    public function getTourGuideByName($name){
        $url = $this->_entriesUrl() . "&content_type=tourGuideProfiles&fields.tourGuideName=" . urlencode($name);
        $results = $this->_curlRequest($url);
        return ($results['total'] > 0) ? $results : [];
    }

    public function getTourGuidesByCityId($cityCid){
        if(is_array($cityCid)) $cityCid = $cityCid['sys']['id'];
        $url = $this->_entriesUrl() . "&content_type=tourGuideProfiles&fields.tourGuideCity.sys.id=" . $cityCid;
        $results = $this->_curlRequest($url);
        return ($results['total'] > 0) ? $results['items'] : [];
    }

    public function getTourGuidesByFeaturedTourId($tourId){
        if(is_array($tourId)) $tourId = $tourId['sys']['id'];
        $url = $this->_entriesUrl() . "&content_type=tourGuideProfiles&fields.tourGuideFeaturedTour.sys.id=" . $tourId;
        $results = $this->_curlRequest($url);
        return ($results['total'] > 0) ? $results['items'] : [];
    }

    public function getMeetingPointsTourEventId($tourEventId){
        $url = $this->_entriesUrl() . "&content_type=confirmations&fields.eventId=" . $tourEventId;
        $results = $this->_curlRequest($url);
        return ($results['total'] > 0) ? $results['items'] : [];
    }

    public function getTourUrlByTourId($tourId){
        $tour = $this->getTourById($tourId);

        if(!$tour) return null;

        $city = $this->getCityById($tour['fields']['tourCity']);

        if(!$city) return null;

        return '/' . $city['fields']['url'] . '/' . $tour['fields']['tourPageURL'];

    }

}
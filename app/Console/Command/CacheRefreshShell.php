<?php
App::import('Model','User');

App::uses('TakeWalksController', 'Controller');

App::uses('ComponentCollection', 'Controller');
App::import('Component','Session');
App::import('Component','Auth');

require_once(APPLIBS . 'ContentfulWrapper.php');


class CacheRefreshShell extends AppShell
{

    public function main() {
        echo 'Start '.date('Y-m-d H:i:s').PHP_EOL;
        $_SERVER['HTTP_HOST'] = 'take.walks';
        $this->TakeWalksController = new TakeWalksController();


        //create auth
//        $collection = new ComponentCollection();
//        $collection->init(new TakeWalksController());
//        $this->TakeWalksController->Auth = new AuthComponent($collection);




        $collection = new ComponentCollection();
        $this->TakeWalksController->Session = new SessionComponent($collection);
        $this->TakeWalksController->Auth = new AuthComponent($collection);
        $this->TakeWalksController->request = new CakeRequest();
        $this->TakeWalksController->beforeFilter();

        $this->TakeWalksController->setContentful(new ContentfulWrapper());

        echo 'Cache ContentfulWrapper methods...'.PHP_EOL;

        $contentCountries = $this->TakeWalksController->getContentful()->getCountries();
        foreach ($contentCountries['items'] as $item) {
            $this->TakeWalksController->getContentful()->getCountryByIdCache($item, 'short');
        }

        $cities = [];

        $contentCities = $this->TakeWalksController->getContentful()->getCities();
        foreach ($contentCities as $city) {
            $cities[ $city['id'] ]['url'] = $city['url'];
            echo ' *** city: '.$city['id'].' => '.$city['url'].' '.PHP_EOL;
            $toursByCityId = $this->TakeWalksController->getContentful()->getToursByCityId($city['id']);
            $tagsByCityId = $this->TakeWalksController->getContentful()->getTagsByCityCache($city['id'], 'short');
            $cityById = $this->TakeWalksController->getContentful()->getCityByIdCache($city['id'], 'short');

            $compareTags = [];
            foreach ($tagsByCityId as $tagByCityId) {
                if(isset($tagByCityId['fields']['tagPageURL'])) {
                    $compareTags[] = $tagByCityId['fields']['tagPageURL'];
                    echo '  *** compare tag: '.$tagByCityId['fields']['tagPageURL'].' '.PHP_EOL;
                    $this->TakeWalksController->getContentful()->getTagPageBySlugCache($tagByCityId['fields']['tagPageURL'],'short');
                }
            }
            $cities[ $city['id'] ]['compareTags'] = $compareTags;

            $tours = [];
            foreach ($toursByCityId as $tourByCity) {
                echo '  *** tour: '.$tourByCity['eventId'].' => '.$tourByCity['tourPageURL'].' '.PHP_EOL;
                $tours[ $tourByCity['eventId'] ]['tourPageURL'] = $tourByCity['tourPageURL'];
                $this->TakeWalksController->getTourRatingCache($tourByCity['eventId'], 'long');
                $tourById = $this->TakeWalksController->getContentful()->getTourByIdCache($tourByCity['eventId'],'short', $tourByCity['id']);
                if ( isset($tourById['fields']['tourPageTourStyle'])) {
                    $this->TakeWalksController->getContentful()->getTourStyleCache($tourById['fields']['tourPageTourStyle'], 'short');
                }
                $this->TakeWalksController->getContentful()->getTagPageBySlugCache($tourByCity['tourPageURL'],'short');
            }
            $cities[ $city['id'] ]['tours'] = $tours;
        }

        echo '----------------------------------------------------------------------------------------------'.PHP_EOL;
        echo 'Cache TakeWalksController methods...'.PHP_EOL;
        echo 'beforeFilterCache...'.PHP_EOL;
        $this->TakeWalksController->beforeFilterCache('tw_medals', 'long');
        echo 'homeCache...'.PHP_EOL;
        $this->TakeWalksController->homeCache('page_home', 'short');

        echo 'listingCache, eventDetailCache, getTourRatingCache...'.PHP_EOL;
        foreach ($cities as $cityId => $city) {
            echo ' city: '.$city['url'].' '.PHP_EOL;
            $this->TakeWalksController->listingCache($city['url'], 'short');
            $toursByCityId = $city['tours'];
            $tagsByCityId = $city['compareTags'];

            foreach ($tagsByCityId as $tagByCityId) {
                echo '  compare tag: '.$tagByCityId.' '.PHP_EOL;
                try {
                    $this->TakeWalksController->compareCache($tagByCityId, 'short');
                } catch(Exception $ex) {
                    echo 'Exception: '.$ex->getMessage().PHP_EOL;
                }
            }

            foreach ($toursByCityId as $tourId => $tourByCity) {
                echo '  tour: '.$tourId.' => '.$tourByCity['tourPageURL'].' '.PHP_EOL;
                try {

                    $this->TakeWalksController->eventDetailCache($city['url'], $tourByCity['tourPageURL'], 'short');
                } catch(Exception $ex) {
                    echo 'Exception: '.$ex->getMessage().PHP_EOL;
                }
            }
        }
        echo 'End '.date('Y-m-d H:i:s').PHP_EOL;

    }
}
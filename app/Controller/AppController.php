<?php
App::uses('Controller', 'Controller');

class AppController extends Controller {

    public $uses = array(
        'DomainsGroup',
        'Tag',
        'Feedback'
    );

    public $components = array(
        'Session',
        'Auth' => array(
            'authError' => 'You are not allowed to see that',
            'authenticate' => array(
                'Form' => array(
                    'userModel' => 'Client',
                    'fields' => array(
                        'username' => 'email',
                        'password' => 'password'
                    ),
                    'passwordHasher' => array(
                        'className' => 'Simple',
                        'hashType' => 'md5'
                    )
                )
            )
        )
//    ,'DebugKit.Toolbar'
    );

    public $helpers = array('ReSrc','RichSnippets');

    public function beforeRender(){

        if($this->name == 'CakeError'){
            $this->set('css',array('error'));
            $this->set('config', $this->config);
            $this->set('layoutTitle', 'Page not found');
        }

        //get the "view tours" link for turkey
        if($this->config->domain == 'turkey'){
            $this->loadModel('DomainsGroup');
            $this->set('viewToursLink', $this->DomainsGroup->field('url_name'));

        }
    }

    public function beforeFilter() {

        $this->set('version', 0.6);
        Security::setHash('md5');

        $this->set('user', $this->Auth->user());

        $this->Auth->loginAction = array('controller' => 'pages', 'action' => 'home');
        $this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'home');
        $this->Auth->logoutRedirect = array('controller' => 'pages', 'action' => 'home');


        // Cart is a global object
        $this->set('cart', $this->Session->read('shopping_cart') ?: array());

        //currency exchange library
        App::uses('ExchangeRate', 'Lib');
        App::uses('GeoIpMaxMind', 'Lib');
        App::uses('ContentfulWrapper', 'Lib');


        switch($_SERVER['HTTP_HOST']){
            case 'www.walksofnewyork.com':
            case 'nyc.walks':
            case 'wony.walks.org':
            case 'dev-www.walksofnewyork.com':
            case 'staging-www.walksofnewyork.com':
            case 'production-www.walksofnewyork.com':
                $this->_applyTheme('new-york');
                $this->_config('new-york');
                $this->city = 'nyc';
                $this->theme = 'nyc';
                break;
            case 'www.walksofitaly.com':
            case 'italy.vimbly.com':
            case 'italy.walks':
            case 'woi.walks.org':
            case 'gwoi.walks.org':
            case '8.26.65.15':
            case 'dev-www.walksofitaly.com':
            case 'staging-www.walksofitaly.com':
            case 'production-www.walksofitaly.com':
                $this->theme = 'Italy';
                $this->_applyTheme('italy');
                $this->_config('italy');
                $this->city = 'rome';
                break;
            case 'www.walksofturkey.com':
            case 'wot.walks.org':
            case 'gwot.walks.org':
            case 'turkey.walks':
            case 'dev-www.walksofturkey.com':
            case 'staging-www.walksofturkey.com':
            case 'production-www.walksofturkey.com':
                $this->theme = 'Turkey';
                $this->_applyTheme('turkey');
                $this->_config('turkey');
                $this->city = 'istanbul';
                break;
            case 'es.walksofitaly.com':
            case 'italyes.walks':
            case 'walks.local':
            case 'tei.walks.org':
            case '192.168.33.10':
            case 'dev-es.walksofitaly.com':
            case 'staging-es.walksofitaly.com':
            case 'production-es.walksofitaly.com':
                $this->theme = 'ItalyEs';
                $this->_applyTheme('italy-es');
                $this->_config('italy-es');
                $this->city = 'italy';
                break;
            case 'take.walks':
            case 'local-www.takewalks.com':
            case 'www.takewalks.com':
            case 'production-www.takewalks.com':
            case 'staging-www.takewalks.com':
            case 'staging06-www.takewalks.com':
            case 'dev-kevin-www.takewalks.com':
            case 'dev-philip-www.takewalks.com':
                $this->theme = 'TakeWalks';
                $this->_applyTheme('take-walks');
                $this->_config('take-walks');
                $this->city = 'takeWalks';
                break;
            default:
                throw new ErrorException('This URL is not associated with a theme');

        }

        //check for currency switch
        if(isset($this->request->data['changeCurrency'])){
            ExchangeRate::setCurrency($this->request->data['changeCurrency']);
        }

        // check for GET parameter "promo=[eventId]", save in session, and automatically apply at cart.
        // Only ever save the last promo code entered in session.
        if($this->request->query('promo')){
            $this->loadModel('BookingsPromo');
            $promocode = $this->BookingsPromo->promoValidDate($this->request->query('promo'));
            if ($promocode !== false){
                $this->Session->write('promoCodeUrl', $this->request->query('promo'));
                $this->Session->write('promo_code_applied',true);
                // to show a message for VALID promo code
                $this->Session->write('promoCodeUrlShadowBox', 1);
            } else {
                // to show a message for invalid promo code
                $this->Session->write('promoCodeUrlShadowBox', 2);
                if ($this->Session->check('promoCodeUrl')){
                    $this->Session->delete('promoCodeUrl');
                }
            }
        }

        //Default currency based on IP geo lookup. Get the client IP from cache if possible
        $cacheClientIp = $this->Session->read('clientIp');
        $clientIp = $this->request->clientIp(false);
        //$clientIp = GeoIpMaxMind::$TEST_IP_ADDRESS['uk'];
        if ($cacheClientIp != $clientIp){
            ExchangeRate::setCurrency( GeoIpMaxMind::getCurrencyByIp($clientIp) );
            $this->Session->write('clientIp', $clientIp);
        }

        //get the ratings
        $this->set('ratings', $this->Feedback->groupByEventId($this->config->domain));


        //get everything for the menu
        $this->set('featured', $this->Tag->featured(false, $this->config));

        $locations = $this->DomainsGroup->find('all', array(
            'conditions' => array(
                'url_name !=' => 'transfers',
                'domains_id' => $this->config->domainId
            ),
            'order' => 'display_order',
            'contain' => array()
        ));
        $this->set('locations', $locations);

        $featuredTags = $this->Tag->query("
                SELECT Tag.id, Tag.name, Tag.tag_type
                FROM events_domains_groups
                INNER JOIN events_tags on events_tags.event_id = events_domains_groups.event_id
                INNER JOIN events on events.id = events_tags.event_id and events.visible = 1
                INNER JOIN tags as Tag on events_tags.tag_id = Tag.id
                INNER JOIN domains_groups on domains_groups.id = events_domains_groups.group_id
                WHERE domains_groups.url_name = 'rome' and Tag.tag_type in (1)
                GROUP BY Tag.id
                ORDER BY Tag.tag_type
                LIMIT 5
            ");

        $this->set(compact('featuredTags'));

    }

    // Domain-specific themes
    private function _applyTheme($domain) {
        $theme = array(
            'new-york' => array(
                'siteTitle' => 'Walks of New York',
                'baseUrl' => 'https://www.walksofnewyork.com',
                'blogUrl' => 'https://www.walksofnewyork.com/blog',
                'city_slug' => 'new-york',
            ),
            'italy' => array(
                'siteTitle' => 'Walks of Italy',
                'baseUrl' => 'https://www.walksofitaly.com',
                'blogUrl' => 'http://www.walksofitaly.com/blog/',
                'city_slug' => 'italy',
            ),
            'italy-es' => array(
                'siteTitle' => 'Walks of Italy ES',
                'baseUrl' => 'https://es.walksofitaly.com/',
                'blogUrl' => 'https://es.walksofitaly.com/blog/',
                'city_slug' => 'italy-es',
            ),
            'turkey' => array(
                'siteTitle' => 'Walks of Turkey',
                'baseUrl' => 'https://www.walksofturkey.com',
                'blogUrl' => 'http://www.walksofturkey.com/blog/',
                'city_slug' => 'turkey',
            ),
            'take-walks' => array(
                'siteTitle' => 'Take Walks',
                'baseUrl' => 'https://www.walks.org',
                'blogUrl' => 'http://www.walksofitaly.com/blog/',
                'city_slug' => 'take-walks'
            )

        );

        $this->set('theme', (object) $theme[$domain]);
    }

    // Domain-specific settings
    private function _config($domain) {
        $config = array(
            'new-york' => array(
                'domainId' => 5,
                'domain' => 'new-york',
                'exchangepair' => 'USDUSD',
                'defaultCurrency' => 'USD',
                'dbCurrency' => 'USD',
                'filterDefaultTourType' => 'Group'
            ),
            'italy' => array(
                'domainId' => 1,
                'domain' => 'italy',
                'exchangepair' => 'EURUSD',
                'defaultCurrency' => 'EUR',
                'dbCurrency' => 'EUR',
                'filterDefaultTourType' => 'All'
            ),
            'italy-es' => array(
                'domainId' => 11,
                'domain' => 'italy-es',
                'exchangepair' => 'EURUSD',
                'defaultCurrency' => 'EUR',
                'dbCurrency' => 'EUR',
                'filterDefaultTourType' => 'All'
            ),
            'turkey' => array(
                'domainId' => 10,
                'domain' => 'turkey',
                'exchangepair' => 'TRYUSD',
                'defaultCurrency' => 'USD',
                'dbCurrency' => 'TRY',
                'filterDefaultTourType' => 'All'
            ),
            'take-walks' => array(
                'domainId' => 1,
                'domain' => 'takeWalks',
                'exchangepair' => 'EURUSD',
                'defaultCurrency' => 'EUR',
                'dbCurrency' => 'EUR',
                'filterDefaultTourType' => 'All'
            ),
        );

        ExchangeRate::init($config[$domain]['defaultCurrency'], $config[$domain]['dbCurrency']);

        $this->config = (object) $config[$domain];
    }
}

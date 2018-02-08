<?php

use GeoIp2\Database\Reader;

/**
 * Class to manage GeoIp Lookup. Use MaxMind API: https://maxmind.github.io/GeoIP2-php/.
 * Set default currency of users based on their location.
 * in America -> USD
 * in Canada -> CAD
 * in UK -> GBP
 * in Europe -> EUR
 * in Australia -> AUD
 * If not in any of the above locations, use USD
 * @see
 * Database documentation: http://dev.maxmind.com/geoip/geoip2/geolite2/
 * Download database: http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz
 * For installation on server: http://ctrtard.com/code/how-to-install-the-maxmind-geoip2-database-for-php/
 */
class GeoIpMaxMind {

    public static $COUNTRY_CODES =   [ 'CA' => 'CAD', 'GB' => 'GBP', 'AU' => 'AUD' ];
    public static $CONTINENT_CODES = [ 'EU' => 'EUR', 'NA' => 'USD', 'SA' => 'USD' ];

    /**
     * Path to GeoLite2 Country Maxmind DB in server
     * @var string
     */
    public static $MAP_DB_COUNTRY = '../Vendor/geoip/GeoLite2-Country.mmdb';

    /**
     * Example of use: <i>GeoIpMaxMind::$TEST_IP_ADDRESS['usa|spain|canada|uk|australia|colombia']</i>
     * @var array
     */
    public static $TEST_IP_ADDRESS = ['usa' => '128.101.101.101',   'spain'     => '195.57.2.4',    'canada'   => '24.114.29.162',
                                      'uk'  => '82.132.221.123',    'australia' => '61.9.194.49',   'colombia' => '181.135.86.223'];

    private $ip = null;
    private $currency = null;
    private $reader = null;
    private $lookupResult = null;

    public function __construct($ip, $currency = 'USD'){
        $this->currency = $currency;
        $this->ip = $ip;
        try {
            $this->reader = new Reader(static::$MAP_DB_COUNTRY);
            $this->lookupResult = $this->reader->country($this->ip);
        } catch (Exception $ex) {
            //throw new Exception($ex->getMessage());
            //print_r($ex->getMessage()." - ".$ex->getTraceAsString());die;
        }
    }

    public static function getCurrencyByIp($ip, $currency = 'USD'){
        try {
            $reader = new Reader(static::$MAP_DB_COUNTRY);
            $lookupResult = $reader->country($ip);
            $codeCountry = $lookupResult->country->isoCode;
            $codeContinent = $lookupResult->continent->code;
            if (array_key_exists($codeCountry,static::$COUNTRY_CODES)){
                $currency = static::$COUNTRY_CODES[$codeCountry];
            } else if (array_key_exists($codeContinent,static::$CONTINENT_CODES)){
                $currency = static::$CONTINENT_CODES[$codeContinent];
            }
        } catch (Exception $ex) {
            //throw new Exception($ex->getMessage());
            //print_r($ex->getMessage()." - ".$ex->getTraceAsString());die;
        }
        return $currency;
    }

    public function getCurrency() {
        if ($this->lookupResult){
            $codeCountry = $this->lookupResult->country->isoCode;
            $codeContinent = $this->lookupResult->continent->code;

            if (array_key_exists($codeCountry,static::$COUNTRY_CODES)){
                $this->currency = static::$COUNTRY_CODES[$codeCountry];
            } else if (array_key_exists($codeContinent,static::$CONTINENT_CODES)){
                $this->currency = static::$CONTINENT_CODES[$codeContinent];
            }
        }
        return $this->currency;
    }

    public function getContinentName() {
        return $this->lookupResult->continent->names['en'];
    }

    public function getContinentCode() {
        return $this->lookupResult->continent->code;
    }

    public function getCountryName() {
        return $this->lookupResult->country->name;
    }

    public function getCountryCode() {
        return $this->lookupResult->country->isoCode;
    }

}


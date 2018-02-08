<?php

class ExchangeRate{
    private static $currency; //current currency 3 chars
    private static $translation; //table with all exchange rates and symbols
    private static $dbCurrency; //the currency of the DB

    //Initialized in AppController::_config
    public static function init($defaultCurrency, $dbCurrency, $forceRefresh = false){
        self::$dbCurrency = $dbCurrency;
        //get the exchange rates and symbols from database or cache if possible
        self::$translation = Cache::read('currencyExchange');
        if($forceRefresh || !self::$translation){
            $currenciesExchange = ClassRegistry::init('CurrenciesExchange');

            self::$translation = array(
                'USD' => array('symbol' => '&#36;', 'exchange' => 1, 'separator' => ',', 'decimal' => '.','unadjustedRate' => 1)
            );

            $lookup = array(
                'EURUSD' => array('name' => 'EUR', 'symbol' => '&#8364;', 'separator' => '.', 'decimal' => ','),
                'GBPUSD' => array('name' => 'GBP', 'symbol' => '&#163;', 'separator' => '.', 'decimal' => ','),
                'TRYUSD' => array('name' => 'TRY', 'symbol' => '&#8378;','separator' => '.', 'decimal' => ','),
                'CADUSD' => array('name' => 'CAD', 'symbol' => '&#36;', 'separator' => ',', 'decimal' => '.'),
                'AUDUSD' => array('name' => 'AUD', 'symbol' => '&#36;', 'separator' => ',', 'decimal' => '.')
            );

            foreach($lookup as $field => $info){
                $adjRate = $currenciesExchange->field('adj_rate',array('exchangepair'=> $field));
                if($adjRate){
                    $info['exchange'] = $adjRate;
                    $info['unadjustedRate'] = $currenciesExchange->field('rate',array('exchangepair'=> $field));
                    self::$translation[$info['name']] = $info;
                }
            }
            Cache::write('currencyExchange', self::$translation);
        }

        //get the customers chosen currency or use the default currency
        if(CakeSession::check('currency')){
            self::$currency = CakeSession::read('currency');
        }else{
            self::setCurrency($defaultCurrency);
        }
    }

    private static function _checkCurrency($currency){
        //check if custom currency or selected one
        $currency = $currency == 'selected' ? self::$currency : $currency;
        //if user entered currency doesn't exist then load from session
        return isset(self::$translation[$currency]) ? $currency : self::$currency;
    }

    public static  function getSymbol($currency = 'selected'){
        $currency = self::_checkCurrency($currency);
        return self::$translation[$currency]['symbol'];
    }

    public static  function getExchangeRate($currency = 'selected'){
        $currency = self::_checkCurrency($currency);
        return self::$translation[$currency]['exchange'];
    }
    public static  function getUnadjustedExchangeRate($currency = 'selected'){
        $currency = self::_checkCurrency($currency);
        return self::$translation[$currency]['unadjustedRate'];
    }

    /**
     * @param string $currency currency to get
     * @return int number to divide by to get the price
     */
    public static function getExchangeRateFromDbCurrency($currency = 'selected'){
        $currency = self::_checkCurrency($currency);
        $exchangeRate = 1;
        if($currency != self::$dbCurrency){
            $exchangeRate *= self::$translation[self::$dbCurrency]['exchange'];
            if($currency != 'USD'){
                $exchangeRate /= self::$translation[$currency]['exchange'];
            }
        }
        return $exchangeRate;
    }

    public static function getCurrency(){
        return self::$currency;
    }


    public static function setCurrency($newCurrency){
        $newCurrency = strtoupper($newCurrency);
        if(isset(self::$translation[$newCurrency])){
            self::$currency = $newCurrency;
            CakeSession::write('currency',$newCurrency);
        }
    }

    public static function format($amount, $formatType = 'selected', $decimal = true,  $format = true){
        //don't show decimal if zero cents
        //$decimal = $amount - floor($amount) == 0 ? false : $decimal;

        if($formatType == 'selected') $formatType = self::$currency;


        //if hide decimal point then round amount
        $amount = $decimal ? $amount : round($amount);

        return ($format ? self::getSymbol($formatType): '') . number_format($amount,$decimal ? 2 : 0, $format ? self::$translation[$formatType]['decimal'] : '.', $format ? self::$translation[$formatType]['separator'] : '');


    }


    public static function convert($amount, $decimal=true, $format=true, $toCurrency='selected'){
        $toCurrency = $toCurrency == 'selected' ? self::$currency : $toCurrency;
        //capitalize just in case
        $toCurrency = strtoupper($toCurrency);
        //check if currency is valid otherwise change to default
        $toCurrency = isset(self::$translation[$toCurrency]) ? $toCurrency : self::$currency;

        //if currency is the same as the currency in db then don't touch it
        if($toCurrency != self::$dbCurrency){
            //since all the conversions are in USD, convert amount to USD
            $amount = $amount * self::$translation[self::$dbCurrency]['exchange'];

            //if usd then it's already converted, otherwise convert to new amount
            if($toCurrency != 'USD'){
                $amount = $amount / self::$translation[$toCurrency]['exchange'];
            }
        }

        //format the currency
        return self::format($amount, $toCurrency, $decimal, $format);
    }

    public static function getExchangeRates(){
        return self::$translation;
    }

    public static function getDbCurrency(){
        return self::$dbCurrency;
    }

}
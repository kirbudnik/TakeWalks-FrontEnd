<?php
App::uses('HttpSocket', 'Network/Http');
class Payment extends AppModel {
    private $_submitUrl = null;
    private $_apiUrl = null;
    private $_username = null;
    private $_password = null;
    private $_merchantId = null;
    private $_paymentType = null;
    private $_currency = null;
    private $_httpSocket = null;

    private $_accounts = array(
        'AUD' => 239989,
        'CAD' => 239993,
        'EUR' => 239986,
        'GBP' => 239991,
        'USD' => 239983
    );

    /**
     * Possible values for 'country_code'
     * @var array
     */
    private $_countryCodes = [
        'US', // =>  'United States',
        'CA', // => 'Canada',
        'GB', // => 'Great Britain',
        'UK'  // => 'United Kingdom',
    ];

    private $_lastRawCurlResponse = null;

    private $_errorMessages = array(
        'approval' => array(
            '14' => 'Invalid credit card number.',
            '33' => 'Your credit card has expired.',
            '64' => 'Invalid credit card security code.',
            '68' => 'Invalid credit card number.',
            '69' => 'Invalid credit card type.',
            '74' => 'Invalid credit card expiration date.',
            'C4' => 'Credit card is over limit',
            'C8' => 'Credit card is over limit',
            'D7' => 'Insufficient funds.',
            'F3' => 'Account closed',
            'F7' => 'Account Frozen',
        ),
        'avs' => array(
            '1' => 'No address supplied'
        ),
        'process' => array(
            '521' => 'Internal error. Please try again later',
            '818' => 'Invalid credit card security code.',
            '839' => 'Credit card is invalid.',
            '847' => 'Credit card is invalid.',
        ),
        'ccv2' => array(
            'N' => 'The security code is invalid',
            'U' => "The card issuer is unable to verify your card's security code."
        )

    );

    function __construct(){
        $this->_apiUrl = PAYMENT_API;
        $this->_submitUrl = PAYMENT_SUBMIT_URL;
        $this->_username = PAYMENT_API_USERNAME;
        $this->_password = PAYMENT_API_PASSWORD;
             
        // Merchant ID
        // MID: 239983 (USD), 239986 (EUR), 239989 (AUD), 239991 (GBP), 239993 (CAD)
        $this->_merchantId = "239983";

        // Transaction Type
        // A -- Authorize, AC -- Auth and Capture, F -- Force Auth only, FC -- Force Auth and Capture, R -- Refund
        $this->_paymentType = "AC";

        $this->_httpSocket = new HttpSocket();
    }


    public function doPayment($data, $price) {
        $firstname = $data['first_name'];
        $lastname = $data['last_name'];
        $email = $data['email'];
        $phone = $data['phone_number'];
        $street_address = $data['street_address'];
        $city = $data['city'];
        $state = $data['state'];
        $zip = $data['zip'];
        $country = $data['country'];
        $ccNo = $data['ccNo'];
        $ccMonth = $data['ccMonth'];
        $ccYear = $data['ccYear'];
        $ccCCV = $data['ccCCV'];
        $ccFirstName = $data['ccFirstName'];
        $ccLastName = $data['ccLastName'];

        $type = Configure::read('debug') > 0 ? 'test' : 'real';

        return $this->authorize($type, $ccNo, "$ccMonth-$ccYear", $ccCCV, $price, $ccFirstName, $ccLastName, $street_address, $state, $zip, $city, $country, $phone, $email);
    }

    private function authorize($trueortest, $card_no, $card_expiry_date, $ccCCV, $d3_price1, $bill_fname, $bill_lname, $bill_address, $bill_state, $bill_city, $bill_zip, $bill_country, $bill_phone, $bill_email) {
        if ($trueortest == "test") {
            $post_values = array(
                // the API Login ID and Transaction Key must be replaced with valid values
                'x_login' => '5DD42Vfr2', //test keys
                'x_tran_key' => '2Kfg5GC3dDP2p45e', //test keys
                "x_version" => "3.1",
                "x_delim_data" => "TRUE",
                "x_delim_char" => "|",
                "x_relay_response" => "FALSE",
                "x_type" => "AUTH_CAPTURE",
                "x_method" => "CC",
                "x_card_num" => $card_no, //"4111111111111111",
                "x_exp_date" => $card_expiry_date, //"0115",
                'x_card_code' => $ccCCV,
                "x_amount" => number_format($d3_price1, 2),
                "x_description" => "Touring Services",
                "x_first_name" => $bill_fname,
                "x_last_name" => $bill_lname,
                "x_address" => $bill_address,
                "x_state" => $bill_state,
                'x_city' => $bill_city,
                'x_zip' => $bill_zip,
                'x_country' => $bill_country,
                'x_phone' => $bill_phone,
                'x_email' => $bill_email,
            );
            $post_url = "https://test.authorize.net/gateway/transact.dll";
        }
        if ($trueortest == "real") {
            $post_values = array(
                // the API Login ID and Transaction Key must be replaced with valid values
                "x_login" => "9Uz9y9WwTBb",
                "x_tran_key" => "248utWkT3V6z37MR",
                "x_version" => "3.1",
                "x_delim_data" => "TRUE",
                "x_delim_char" => "|",
                "x_relay_response" => "FALSE",
                "x_type" => "AUTH_CAPTURE",
                "x_method" => "CC",
                "x_card_num" => $card_no, //"4111111111111111",
                "x_exp_date" => $card_expiry_date, //"0115",
                'x_card_code' => $ccCCV,
                "x_amount" => number_format($d3_price1, 2),
                "x_description" => "Touring Services",
                "x_first_name" => $bill_fname,
                "x_last_name" => $bill_lname,
                "x_address" => $bill_address,
                "x_state" => $bill_state,
                'x_city' => $bill_city,
                'x_zip' => $bill_zip,
                'x_country' => $bill_country,
                'x_phone' => $bill_phone,
                'x_email' => $bill_email,
            );
            $post_url = "https://secure.authorize.net/gateway/transact.dll";
        }
        $post_string = "";
        //pr($post_values);
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");

        // This line takes the response and breaks it into an array using the specified delimiting character

        $post_response = $this->a_net($post_url, $post_string);
        $cc_sleep = 1;

        while ($post_response == FALSE && $cc_sleep < 4) {
            sleep(3);
            $this->log("cURL Error on attempt number " . $cc_sleep . " for " . $bill_fname . " " . "$bill_lname");
            $post_response = $this->a_net($post_url, $post_string);
            $cc_sleep++;
        }

        //$post_response = a_net($post_url, $post_string);
        $response_array = explode($post_values["x_delim_char"], $post_response);
        return $response_array;
    }

    function a_net($post_url, $post_string) {
        $request = curl_init($post_url); // initiate curl object
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 45);
        curl_setopt ($request, CURLOPT_CAINFO, WWW_ROOT . DS ."cacert.pem");
//        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        // uncomment this line if you get no gateway response.
        $post_response = curl_exec($request);
        // execute curl post and store results in $post_response
        // additional options may be required depending upon your server configuration
        // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close($request); // close curl object
        return $post_response;
    }

    function processDonation($data, $domain, $charityId, $amount){
        $donationAmount = number_format($amount * CakeSession::read('exchangeRate'), 2, '.', '');
        $donationResponse = $this->doOrbitalPaymentApi($data, $donationAmount,'Charity', $domain, array('charityId' => $charityId));
        return $donationResponse;
    }
    function processGiftCard($data, $booking_id, $domain, $amount){
        $this->_currency = $data['currency'];
        $this->_merchantId = $this->_accounts[$data['currency']];
        $donationAmount = number_format($amount, 2, '.', '');
        $donationResponse = $this->doOrbitalPaymentApi($data, $donationAmount, $booking_id, $domain, array('giftCardPurchase' => true));
        return $donationResponse;
    }

    function processPayment($data, $price, $bookingId, $domain, $donations, $options = array()){
        $this->_currency = $data['currency'];
        $this->_merchantId = $this->_accounts[$data['currency']];

        $payments = array();

        //process the actual tour
        $response = $this->doOrbitalPaymentApi($data, $price, $bookingId, $domain, $options);
        $payments[] = array('orderId' => $response['orderId'], 'txRefNum' => $response['transactionId']);
        if($response['success']) {
            //$this->_markForCaptureAll($payments);
        }else{
            return $response;
            //if one fails then void all of the previous ones and return the response
            //$this->_voidAll($payments);
        }


        //process donations
        if($donations){
            //attempt to auth each of the donations
            foreach($donations as $donationId => $donationAmount){
                if($donationAmount){
                    $donationAmount = number_format($donationAmount * CakeSession::read('exchangeRate'), 2, '.', '');
                    $donationResponse = $this->doOrbitalPaymentApi($data, $donationAmount, $bookingId, $domain, $donationId);
                    $payments[] = array('orderId' => $donationResponse['orderId'], 'txRefNum' => $donationResponse['transactionId']);
                    if(!$donationResponse['success']) {
                        //if one fails then void all of the previous ones and return the response
                        //$this->_voidAll($payments);
                        //return $response;
                        $this->Email = isset($this->Email) ? $this->Email : ClassRegistry::init('Email');
                        $this->Email->sendDonationFailEmail(array('orderId' => $donationResponse['orderId'], 'txRefNum' => $donationResponse['transactionId']));
                    }
                }
            }

        }



        return $response;
    }

    function doOrbitalPaymentApi($data, $price, $bookingId, $domain, $options = array()){
        $avsStatus = 'ON';
//        $avsStatus = 'OFF';
        $postFields = [
            'domain' => $domain,
            'booking_id' => $bookingId,
            'gift_card_purchase' => isset($options['giftCardPurchase']) ? $options['giftCardPurchase'] : false,
            'booking_promo' => isset($options['giftCard']) ? $options['giftCard'] : null,
            'charity_id' => isset($options['charityId']) ? $options['charityId'] : null,
            'currency' => $data['currency'],
            'avs_status' => $avsStatus,
            'amount' => $price,
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'street_address' => $data['street_address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'country' => $data['country'],
            'cc_number' => $data['ccNo'],
            'cc_type' => $data['ccType'],
            'cc_month' => $data['ccMonth'],
            'cc_year' => $data['ccYear'],
            'cc_ccv' => $data['ccCCV'],
            'cc_full_name' => $data['ccFirstName'].' '.$data['ccLastName'],
            'clients_id' => $data['clients_id'],
            'amount_local' => $data['amount_local'],
            'exchange_rate' => $data['exchange_rate'],
            'exchange_from' => $data['exchange_from'],
            'exchange_to' => $data['exchange_to'],
        ];

        if ($avsStatus == 'ON'){
            if (in_array($postFields['country'], $this->_countryCodes)){
                $postFields['country_code'] = $postFields['country'];
                $postFields['state'] = strtoupper($postFields['state']);
                if (strlen($postFields['state']) == 1){
                    $postFields['state'] = $postFields['state']."_";
                } else if (strlen($postFields['state']) > 2){
                    $postFields['state'] = substr($postFields['state'], 0,2);
                }
            } else {
                // disable AVS, because country selected is not available for AVS
                $postFields['avs_status'] = 'OFF';
            }
        }

//        $httpResponse = $this->_httpSocket->post($this->_apiUrl."/api/payments/", $postFields);
//        $response_raw = $httpResponse->body;

        $process = curl_init($this->_apiUrl."/api/payments/");
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, TRUE);
        curl_setopt($process, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response_raw = curl_exec($process);
        $info = curl_getinfo($process);
        $error = curl_errno($process);
        curl_close($process);



        $response = json_decode($response_raw, true);

        $this->log_process('api_payment',array(
            'card_type' => $data['ccType'],
            'cc_number' => $data['ccNo'],
            'amount' => $price,
            'email' => $data['email'],
            'response' => $response,
            'response_raw' => $response_raw
        ));


        //if ($httpResponse->code == 200){
        if ($info['http_code'] == 200 && $error == 0){
            if ($response['success']){
                $response['data']['merchants_id'] = $response['data']['merchants_id'][0];
                return $response['data'];
            } else {
                $message = ( count($response['data']) > 0 ) ? $response['data']['message'] : $response['message'];
                $status = $response['status'];
                $errors = '';
                if($status != 0){
                    $errors .= 'Code '.$status.': '.$message.';  ';
                }

                return array(
                    'success' => false,
                    'status' => $status,
                    'message' => $message,
                    'error' => $status,
                    'transactionId' => '',
                    'authcode' => '',
                    'merchants_id' => $this->_merchantId,
                    'payment_status' => '',
                    'orderId' => ''
                );
            }
        } else {
            return array(
                'success' => false,
                'status' => '99',
                'message' => 'Wrong connection with payment API',
                'error' => 'Was not possible to connect with '.$this->_apiUrl,
                'transactionId' => '',
                'authcode' => '',
                'merchants_id' => $this->_merchantId,
                'payment_status' => '',
                'orderId' => ''
            );
        }

    }

    // Chase/Orbital
    function doOrbitalPayment($data, $price, $bookingId, $domain, $charityId = null){
        $amount = str_replace('.','',sprintf('%.2f',trim($price)));
        $cardName = $data['ccFirstName'] . ' ' . $data['ccLastName'];
        $cardExpiry = $data['ccMonth'] . $data['ccYear'];
        // For BIN 000001, must supply AVSzip, AVSaddress1, and AVScity in order for data to be transmitted to Host Processing System
        // AVSaddress1 Should not include any of the following characters: % | ^ \ /
        //$AVSaddress1 = str_replace(["%", "|", "^", "\\", "/"], " ", $data['street_address']);
        //$AVScity = $data['city'];

        $sdMerchantName = "WALKS LLC   ";       // Fixed length string
        if ($domain == 'new-york') {
            $sdProductDescription = "NEW YORK ";    // Fixed length string
            $suffix = 'WONY';
        } else if ($domain == 'italy' || $domain == 'italy-es') {
            $sdProductDescription = "ITALY TRV";    // Fixed length string
            $suffix = 'WOI';
        } else if ($domain == 'turkey') {
            $sdProductDescription = "TURKEY   ";    // Fixed length string
            $suffix = 'WOT';
        } else {
            echo 'Error: Invalid tour domain.  Please contact customer service.';
            die;
        }


        $orderId = $suffix . '-' . $bookingId  . ($charityId ? '-C' . $charityId : '');
        // Construct post fields

        /**
         *This is the original way this was.
         * Removed currency and soft descriptors because they were not enabled/supported by the other accounts
         * I only found currency codes for usd and cad. Not sure what currency exponent is.
         * Soft descripters (SD) need to enabled for each account in order to use them.
        $xml_chase_post = '<?xml version="1.0" encoding="UTF-8"?>
                        <Request>
                                <NewOrder>
                                        <OrbitalConnectionUsername>' . $this->_username . '</OrbitalConnectionUsername>
                                        <OrbitalConnectionPassword>' . $this->_password . '</OrbitalConnectionPassword>
                                        <IndustryType>EC</IndustryType>
                                        <MessageType>' . $this->_paymentType . '</MessageType>
                                        <BIN>000001</BIN>
                                        <MerchantID>' . $this->_merchantId . '</MerchantID>
                                        <TerminalID>001</TerminalID>
                                        <CardBrand>' . $data['ccType'] . '</CardBrand>
                                        <AccountNum>' . $data['ccNo'] . '</AccountNum>
                                        <Exp>' .  $cardExpiry . '</Exp>
                                        <CurrencyCode>840</CurrencyCode>
                                        <CurrencyExponent>2</CurrencyExponent>
                                        <CardSecVal>' . $data['ccCCV'] . '</CardSecVal>
                                        <AVSname>' . $cardName . '</AVSname>
                                        <OrderID>' . $orderId . '</OrderID>
                                        <Amount>' . $amount . '</Amount>
                                        <Comments> Payment Made on '.date("Y-m-d h:is").'</Comments>
                                        <SDMerchantName>' . $sdMerchantName . '</SDMerchantName>
                                        <SDProductDescription>' . $sdProductDescription .'</SDProductDescription>
                                        <SDMerchantCity></SDMerchantCity>
                                        <SDMerchantPhone>888-683-8670</SDMerchantPhone>
                                        <SDMerchantURL></SDMerchantURL>
                                        <SDMerchantEmail></SDMerchantEmail>
                                        <CustomerEmail>' . $data['email'] . '</CustomerEmail>
                                </NewOrder>
                        </Request>
        ';
            **/

        $xml_chase_post = '<?xml version="1.0" encoding="UTF-8"?>
                            <Request>
                                <NewOrder>
                                        <OrbitalConnectionUsername>' . $this->_username . '</OrbitalConnectionUsername>
                                        <OrbitalConnectionPassword>' . $this->_password . '</OrbitalConnectionPassword>
                                        <IndustryType>EC</IndustryType>
                                        <MessageType>' . $this->_paymentType . '</MessageType>
                                        <BIN>000001</BIN>
                                        <MerchantID>' . $this->_merchantId . '</MerchantID>
                                        <TerminalID>001</TerminalID>
                                        <CardBrand>' . $data['ccType'] . '</CardBrand>
                                        <AccountNum>' . $data['ccNo'] . '</AccountNum>
                                        <Exp>' .  $cardExpiry . '</Exp>
                                        <CardSecVal>' . $data['ccCCV'] . '</CardSecVal>                  
                                        <OrderID>' . $orderId . '</OrderID>
                                        <Amount>' . $amount . '</Amount>
                                        <Comments> Payment Made on '.date("Y-m-d h:is").'</Comments>
                                        <SDMerchantName>' . $sdMerchantName . '</SDMerchantName>
                                        <SDProductDescription>' . $sdProductDescription .'</SDProductDescription>
                                        <SDMerchantCity></SDMerchantCity>
                                        <SDMerchantPhone>888-683-8670</SDMerchantPhone>
                                        <SDMerchantURL></SDMerchantURL>
                                        <SDMerchantEmail></SDMerchantEmail>
                                        <CustomerEmail>' . $data['email'] . '</CustomerEmail>
                                </NewOrder>
                        </Request>
        ';

        // this should go under <AVSzip>, if needed
//        <AVSaddress1>' . $AVSaddress1 . '</AVSaddress1>
//        <AVScity>' . $AVScity . '</AVScity>

        //process curl
        $response = $this->_curlRequest($xml_chase_post);

        $this->log_process('payment',array(
            'card_type' => $data['ccType'],
            'cc_number' => $data['ccNo'],
            'expires' => $cardExpiry,
            'ccv' => $data['ccCCV'],
            'card_name' => $cardName,
            'booking_id' => $bookingId,
            'amount' => $amount,
            'merchant' => $sdMerchantName,
            'email' => $data['email'],
            'response' => $response,
            'response_raw' => $this->_lastRawCurlResponse
        ));

        // Error checking
        $error = $this->_orbitalError($response);
        $this->log_credit_card_response($response, $data);
        return array(
            'success' => empty($error),
            'message' => $error,
            'transactionId' => empty($error) ? (string) $response->NewOrderResp->TxRefNum : '',
            'authcode' => empty($error) ? (string) $response->NewOrderResp->AuthCode : '',
            'merchants_id' => $response->NewOrderResp->MerchantID,
            'payment_status' => $response->NewOrderResp->error_message_merchant,
            'orderId' => $orderId
        );
    }

    private function _orbitalError($response){
        $message = 'Could not process payment, please try again';
        //make sure response is not empty
        if (empty($response)){
            return 'Payment processing error. Please try again later';
        }


        $RespCode = (string) $response->NewOrderResp->RespCode;
        $status = (string) $response->NewOrderResp->StatusMsg;
        $procRespCode =  !empty($response->QuickResp->ProcStatus)? (string)$response->QuickResp->ProcStatus : (string)$response->NewOrderResp->ProcStatus;
        //not used
        $CVV2RespCode = (string) $response->NewOrderResp->CVV2RespCode;
        //not used
        $AVSRespCode = trim((string) $response->NewOrderResp->AVSRespCode);


        //if everything went well then return empty error message
        if ($response->NewOrderResp->ProcStatus == 0 && $response->NewOrderResp->ApprovalStatus == 1){
            return '';
        }else{//try to get a message

            if($procRespCode > 1){
                $message = $this->_getErrorMessage('process',$procRespCode);
            }else{
                $message = $this->_getErrorMessage('approval', $RespCode);
            }
        }

        return $message;

    }

    private function _getErrorMessage($type,$errorCode){
        switch($type){
            case 'approval':
                $defaultMessage = 'Your credit card was declined.';
                break;
            default:
                $defaultMessage = 'Internal error. Please try again later';
        }
        return isset($this->_errorMessages[$type][$errorCode]) ? $this->_errorMessages[$type][$errorCode] : $defaultMessage;
    }



    function log_process($type,$info){
        $credit_card_log_path = '/var/log/cc_log/process_log.txt';
        $info['type'] = $type;
        $info['created'] = date('Y-m-d H:i:s');
        $info['ip'] = $_SERVER['REMOTE_ADDR'];

        $cc_number = (isset($info['cc_number'])) ? $info['cc_number'] : '';
        $cc_number = strlen($cc_number) > 4 ? str_repeat('*', strlen($cc_number) - 4) . substr($cc_number, -4) : $cc_number;
        $json_info = json_encode($info);
        if( $cc_number != '') {
            $json_info = str_replace($info['cc_number'], $cc_number,  $json_info);
        }

        file_put_contents($credit_card_log_path, $json_info . "\n", FILE_APPEND);
    }

    function log_credit_card_response($response, $client_info){

        $credit_card_log_path = '/var/log/cc_log/cc_log.txt';

        //Depending on cc details submitted, 
        //response status could be in one of many locations
        $response_code = (string) $response->NewOrderResp->AuthCode;
        $status_message =(string) $response->QuickResp->StatusMsg;
        $status = (string) $response->QuickResp->ProcStatus;
        $approval_status = (string) $response->NewOrderResp->ApprovalStatus;
        $resp_code_ip =(string) $response->NewOrderResp->RespCode;
        $resp_code =(string) $response->NewOrderResp->ResponseCode;
        $cvv_resp_code =(string) $response->NewOrderResp->CVV2RespCode;
        $avs_resp_code =(string) $response->NewOrderResp->AVSRespCode;

        //In some cases responses are arrays
        //This section merges them
        if (is_array($resp_code_ip)) {
            $resp_code_ip = implode(",", $resp_code_ip);
        }

        if (is_array($resp_code)) {
            $resp_code = implode(",", $resp_code);
        }

        if (is_array($cvv_resp_code)) {
            $cvv_resp_code = implode(",", $cvv_resp_code);
        }

        if (is_array($avs_resp_code)) {
            $avs_resp_code = implode(",", $avs_resp_code);
        }

        if (is_array($response_code)) {
            $response_code = implode(",", $response_code);
        }

        //Compiling data into an array for the purposes of writing to the file
        //Imploded to semi-colon seperated data
        $log_cc_response_data = array();
        $log_cc_response_data['test_or_real'] = 'Null';  //$client_info['test_or_real'];
        $log_cc_response_data['time'] = date("Y-m-d H:i:s");
        $log_cc_response_data['email_address'] = $client_info['email'];
        $log_cc_response_data['last_name'] = $client_info['last_name'];
        $log_cc_response_data['proc_status_1'] = $status;
        $log_cc_response_data['proc_status_2'] = $status;
        $log_cc_response_data['status'] = $approval_status;
        $log_cc_response_data['zip_code'] = $client_info['zip'];
        $log_cc_response_data['country'] = $client_info['country'];
        $log_cc_response_data['card_type'] = $client_info['ccType'];
        $log_cc_response_data['response'] = $response_code;
        $log_cc_response_data['status_msg'] = $status_message."--".$resp_code_ip."--".$resp_code."--".$cvv_resp_code."--".$avs_resp_code;

        $log_cc_response_data_str = implode(';',$log_cc_response_data);
        $log_cc_response_data_str = $log_cc_response_data_str."\n";


        file_put_contents($credit_card_log_path, $log_cc_response_data_str, FILE_APPEND);
    }

    private function _curlRequest($xml_chase_post){
        $xml_header = array(
            "POST /AUTHORIZE HTTP/1.0",
            "MIME-Version: 1.0",
            "Content-type: application/PTI56",
            "Content-length: " . strlen($xml_chase_post),
            "Content-transfer-encoding: text/xml",
            "Request-number: 1",
            "Document-type: Request",
            "Interface-Version: 0.3"
        );

        // Construct curl request
        $init_curl = curl_init($this->_submitUrl);
        curl_setopt($init_curl, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($init_curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($init_curl, CURLOPT_HTTPHEADER, $xml_header);
        curl_setopt($init_curl, CURLOPT_POST, TRUE);
        curl_setopt($init_curl, CURLOPT_POSTFIELDS, $xml_chase_post);
        curl_setopt($init_curl, CURLOPT_SSL_VERIFYPEER, false); //TODO true

        $curl_response = curl_exec($init_curl);
        //for logging
        $this->_lastRawCurlResponse = $curl_response;
        curl_close($init_curl);
        $response = simplexml_load_string($curl_response);

        return $response;
    }

    /**
     * voids all of the payments
     * @param $payments array('txRefNum' => '###', 'orderId' => '###')
     */
    private function _markForCaptureAll($payments){
        foreach($payments as $payment) {
            $xml_chase_post = "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <Request>
        <MarkForCapture>
            <OrbitalConnectionUsername>{$this->_username}</OrbitalConnectionUsername>
            <OrbitalConnectionPassword>{$this->_password}</OrbitalConnectionPassword>
            <OrderID>{$payment['orderId']}</OrderID>
            <BIN>000001</BIN>
            <MerchantID>{$this->_merchantId}</MerchantID>
            <TerminalID>001</TerminalID>
            <TxRefNum>{$payment['txRefNum']}</TxRefNum>
        </MarkForCapture>
    </Request>";

            $response = $this->_curlRequest($xml_chase_post);
            $this->log_process('capture', array(
                'orderId' => $payment['orderId'],
                'response' => $response,
                'response_raw' => $this->_lastRawCurlResponse
            ));
        }
    }

    /**
     * voids all of the payments
     * @param $payments array('txRefNum' => '###', 'orderId' => '###')
     */
    private function _voidAll($payments){

        foreach($payments as $payment) {

            $xml_chase_post = "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <Request>
        <Reversal>
            <OrbitalConnectionUsername>{$this->_username}</OrbitalConnectionUsername>
            <OrbitalConnectionPassword>{$this->_password}</OrbitalConnectionPassword>
            <TxRefNum>{$payment['txRefNum']}</TxRefNum>
            <OrderID>{$payment['orderId']}</OrderID>
            <BIN>000001</BIN>
            <MerchantID>{$this->_merchantId}</MerchantID>
            <TerminalID>001</TerminalID>
            <OnlineReversalInd>Y</OnlineReversalInd>
        </Reversal>
    </Request>";

            $response = $this->_curlRequest($xml_chase_post);
            $this->log_process('void', array(
                'orderId' => $payment['orderId'],
                'response' => $response,
                'response_raw' => $this->_lastRawCurlResponse
            ));
        }
    }

    public function getAccounts(){
        return $this->_accounts;
    }

} 


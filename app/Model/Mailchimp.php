<?php


App::import('Network/Email', 'VMail');
class Mailchimp extends AppModel {
    private $key = '8b464632f7ca29d01a81d96e4be43b77-us2';
    public $useTable = false;


    function listMailchimpLists() {

        require_once APP . 'Vendor/Mailchimp/MCAPI.class.php';

        $MCAPI = new MCAPI($this->key);


        return $MCAPI->lists();
    }


    function addToMailchimp($email) {

        require_once APP . DS . 'Vendor/Mailchimp/MCAPI.class.php';

        $usersListID = '37e1fd89ff'; //walks of italy mailing list
        $MCAPI = new MCAPI($this->key);

        $email_address = $email;
        $group = 'Email Distribution';

        // All times must be in GMT
        $now = new DateTime('now', new DateTimeZone('GMT'));

        if ($this->validEmail($email_address)) {

            // Prevent internal IP from being sent to Mailchimp
            $ip = $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? '65.202.51.130' : $_SERVER['REMOTE_ADDR'];

            $merge_vars = array(
                //'GROUPINGS' => array(array('name'=>'Post-Launch Users', 'groups' => $group)),
                'OPTIN_IP' => $ip,
                'OPTIN_TIME' => $now->format('Y-m-d H:i:s'),
                'SIGNUPDATE' => $now->format('Y-m-d'),
            );

            $MCAPI->listSubscribe( $usersListID, $email_address, $merge_vars, 'html', false, true, false, false);


            if ($MCAPI->errorCode){
                $message = $MCAPI->errorMessage;
                if (preg_match('/already subscribed/', $MCAPI->errorMessage)) $message = $MCAPI->errorMessage;//"You are already signed up.  We appreciate your enthusiasm!";

                return array('status' => 'error', 'message' => $message);
            } else {

                return array('status' => 'ok', 'timestamp' => $now->format('Y-m-d H:i:s'));
            }
        }  else {
            return array('status' => 'error', 'message' => "Please enter a valid email.");

        }
    }

    function validEmail($email) {
        if(is_array($email)) {
            $email = array_values($email)[0];
        }

        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen-1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
                    $isValid = false;
                }
            } else if(strpos($domain, '.') === false) {
                $isValid = false;
            } else if (!(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;
    }
}
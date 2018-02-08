<?php
App::uses('AppModel', 'Model');
class Client extends AppModel {
    var $belongsTo = array(
        'Country' => array(
            'className'		=> 'Country',
            'foreignKey'	=> 'countries_id'
        ),
        'Agent' => array(
            'className'		=> 'Agent',
            'foreignKey'	=> 'agents_id'
        )
    );


    public $validate = array(
        'email' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Please enter your email address.',
            ),
            'email' => array(
                'rule' => array('validEmail'),
                'message' => 'Please enter a valid email address.',
            ),
            'notexists' => array(
                'rule' => array('isUnique'),
                'message' => 'This email is already registered.'
            ),
        ),
        'password' => array(
            'rule'    => array('minLength', 6),
            'message' => 'Password must be at least 6 characters long.'
        ),
        'password_confirm' => array(
            'equaltofield' => array(
                'rule' => array('equalToField', 'password'),
                'message' => 'Your passwords do not match.',
                'on' => 'create'
            )
        )
    );



    // Hash password before saving
    public function beforeSave($options = array()) {
        if (!empty($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = md5($this->data[$this->alias]['password']);
        }
        return true;
    }

    function equalToField($data, $otherfield) {
        $values = array_values($data);
        $compareValue = $values[0];
        return $this->data[$this->name][$otherfield] == $compareValue;
    }

    function validEmail($check) {
        $email = $check['email'];
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
            }
            if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;
    }
}

?>

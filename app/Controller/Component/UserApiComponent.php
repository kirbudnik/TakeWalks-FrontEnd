<?php

App::uses('Component', 'Controller');

class UserApiComponent extends Component {
    private $_apiUrl = USER_API;

    public function register($firstName, $lastName, $email, $password, $address){
        return $this->_curl('post', 'user', [
            "fname"=> $firstName,
            "lname"=> $lastName,
            "email"=> $email,
            "password"=> $password,
            "address"=> $address,
        ]);

    }


     public function bookingVoucher($userId, $bookingDetailsId){
        return $this->_curl('post', 'user/'.$userId.'/booking/voucher', [
            "bookingDetailsId"=> $bookingDetailsId
        ]);
    }


    public function refundRequest($userId, $bookingDetailsId, $reason){
        return $this->_curl('post', 'user/'.$userId.'/booking/refund', [
            "bookingDetailsId"=> $bookingDetailsId,
            "message"=> $reason,
        ]);
    }

    public function userUpdate($userId, $firstName, $lastName, $email, $mobilePhone, $password){
        $data = [];
        if (strlen($firstName) > 0) $data['fname'] = $firstName;
        if (strlen($lastName) > 0) $data['lname'] = $lastName;
        if (strlen($email) > 0) $data['email'] = $email;
        if (strlen($mobilePhone) > 0) $data['mobile_number'] = $mobilePhone;
        if (strlen($password) > 0) $data['password'] = $password;
        return $this->_curl('post', 'user/'.$userId, $data);
    }

    public function userUpdatePassword($userId, $password){
        $data = [];
        if (strlen($password) > 0) $data['password'] = $password;
        $data['reset_hash'] = 'xxxx';
        return $this->_curl('post', 'user/'.$userId, $data);
    }

    public function login($email, $password){
        return $this->_curl('get', 'user/login', [
            "email"=> $email,
            "password"=> $password,
        ]);
    }

    public function loginSocial($provider, $socialUserId){
        return $this->_curl('post', 'user/login/social', [
            "provider"=> $provider,
            "socialUserId"=> $socialUserId,
        ]);
    }

    public function userChangePassword($userId, $oldPassword, $newPassword, $verifyPassword){
        return $this->_curl('post', 'user/'.$userId.'/password/change',[
            "passwordCurrent"=> $oldPassword,
            "passwordNew"=> $newPassword,
            "passwordNewVerify"=> $verifyPassword,
        ]);
    }

    public function passwordEmail($email){
        return $this->_curl('put', 'user/passwordEmail',[
            "email"=> $email
        ]);
    }

    public function getUser($data, $url){
        return $this->_curl('get', $url, $data);
    }

    public function getTourList($userId){
        return $this->_curl('get', 'user/'.$userId.'/tourlist');
    }

    public function postBookingCancel($userId, $bookingDetailsId, $reason){
        return $this->_curl('post', 'user/'.$userId.'/booking/cancel',[
            "bookingDetailsId"=> $bookingDetailsId,
            "message"=> $reason

        ]);
    }


    public function signUp($name, $emailAddress){
        return $this->_curl('post', 'user/signup',[
            "listId"=> 965,
            "name"=> $name,
            "email"=> $emailAddress,
        ]);
    }


    public function addToWishlist($userId, $eventId){
        return $this->_curl('post', 'user/'.$userId.'/wishlist',[
            "events_id"=> $eventId,
        ]);
    }

    public function removeFromWishlist($userId, $eventId){
        return $this->_curl('delete', 'user/'.$userId.'/wishlist',[
            "events_id"=> $eventId,
        ]);
    }

    public function getWishlist($userId){
        return $this->_curl('get', 'user/'.$userId.'/wishlist');
    }

    public function addDestination($userId, $city, $hotel, $hotelPhone, $startDate, $endDate){
        return $this->_curl('post', 'user/'.$userId.'/destination',[
            "city"=> $city,
            "hotel"=> $hotel,
            "hotelPhone"=> $hotelPhone,
            "startDate"=> date('Y-m-d', strtotime($startDate)),
            "endDate"=> date('Y-m-d', strtotime($endDate)),
        ]);
    }

    public function addTraveler($userId, $firstName, $lastName, $email, $phone){
        return $this->_curl('post', 'user/'.$userId.'/traveler',[
            "fname"=> $firstName,
            "lname"=> $lastName,
            "email"=> $email,
            "phone"=> $phone,
        ]);
    }

    public function getSocialProvider($userId){
        return $this->_curl('get', 'user/'.$userId.'/social');
    }

    /**
     * @param $userId
     * @param $provider, facebook|google
     * @param $socialProviderId
     * @return mixed
     */
    public function addSocialProvider($userId, $provider, $socialUserId){
        $provider = strtolower($provider);
        return $this->_curl('post', 'user/'.$userId.'/social',[
            "provider"=> $provider,
            "socialUserId"=> $socialUserId,
        ]);
    }

    public function removeSocialProvider($userId, $socialProviderId){
        return $this->_curl('delete', 'user/'.$userId.'/social',[
            "socialProviderId"=> $socialProviderId,
        ]);
    }
    public function userPasswordResetKey($resetKey){
        return $this->_curl('post', 'user/password/resetkey',[
            "resetKey"=> $resetKey
        ]);
    }



    private function _curl($requestType, $url, $vars = []){

        CakeLog::write('debug', "UserApiComponent _curl requestType ".print_r($requestType, true));
        CakeLog::write('debug', "UserApiComponent _curl url ".print_r($url, true));
        CakeLog::write('debug', "UserApiComponent _curl vars ".print_r($vars, true));
        // $this->_apiUrl
        CakeLog::write('debug', "UserApiComponent _curl _apiUrl ".$this->_apiUrl);

        switch($requestType){
            case 'get':
                $params = http_build_query($vars);
                $url = $url . ($vars ? '?' . $params : '');
                $ch = curl_init($this->_apiUrl . $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($requestType));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'authorization:' . getenv('user_api_token')
                ]);
                $result = curl_exec($ch);

                curl_close($ch);
                break;
            case 'put':

                //$postData = json_encode($vars);
                $postData = http_build_query($vars);
                $ch = curl_init($this->_apiUrl . $url);

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($requestType));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/x-www-form-urlencoded',
                    'authorization:' . getenv('user_api_token')
                ]);

                $result = curl_exec($ch);
                curl_close($ch);

                break;
            case 'post':
            case 'delete':
                $postData = json_encode($vars);
                $ch = curl_init($this->_apiUrl . $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($requestType));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postData),
                    'authorization:' . getenv('user_api_token')
                ]);

                $result = curl_exec($ch);

                curl_close($ch);


                break;
            default:
                throw new Exception('UserApiComponent: invalid request type');

        }



        return json_decode($result);
    }

}

<?php

App::uses('AppController', 'Controller');
class MailController extends AppController{

    public function beforeFilter($options = array()) {
        parent::beforeFilter($options);

        $this->Auth->allow();
    }

    public function subscribe() {
        $this->autoRender = null;

        App::import('Model', 'Mailchimp');
        $this->Email = new Mailchimp();

        if (!$this->request->is('post')) {
            $this->response->body(json_encode(array('status' => 'error', 'message' => 'Suspicious request.  Your IP and credentials have been logged.')));

        } else {
            $this->Email->addToMailchimp($this->request->data);
            $response = $this->Email->addToMailchimp($this->request->data['email']);
            $this->response->body(json_encode($response));
        }

        return $this->response;
    }

    public function listLists() {
        $this->autoRender = null;

        App::import('Model', 'Mailchimp');
        $this->Email = new Mailchimp();

        debug($this->Email->listMailchimpLists());
    }

}
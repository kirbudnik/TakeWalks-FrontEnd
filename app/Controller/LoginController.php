<?php
class LoginController extends AppController {
    var $name = 'Login';

    public $helpers = array('Html', 'Form');

    var $uses = array(
        'Client'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function logout() {
        $this->autoRender = false;
        $this->Session->setFlash('Successfully logged out.', 'FlashMessage/status');
        $this->Auth->logout();
        $this->redirect($this->Auth->logoutRedirect);
    }


    public function authenticate() {
        $this->layout = false;

        if ($this->Auth->user()) {
            $this->Session->setFlash('You are already logged in.', 'FlashMessage/error');
        } else {
            $this->Auth->logout();
            if ($this->Auth->login()) {
                $this->Session->setFlash('Successfully logged in.', 'FlashMessage/status');
            } else {
                $this->Session->setFlash('Invalid email/password combination.', 'FlashMessage/error');
            }
        }
        $this->redirect($this->referer());
    }

    public function create() {
        $this->autoRender = false;

        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $data = $this->request->data;

        $this->Client->set($data);
        $errors = $this->Client->invalidFields();

        if (!empty($errors)) {
            // Concatenate errors
            $error = implode(array_map(function($error) {return $error[0];}, $errors), '  ');
            $this->Session->setFlash($error, 'FlashMessage/error');
            $this->redirect($this->referer());
        } else {
            $this->Client->save();
            $this->Auth->login();
            $this->Session->setFlash('Successfully logged in.', 'FlashMessage/status');
            $this->redirect($this->referer());
        }
    }
}
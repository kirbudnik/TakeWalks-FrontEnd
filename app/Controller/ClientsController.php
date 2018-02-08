<?php
App::uses('AppController', 'Controller');

class ClientsController extends AppController {
    var $uses = array('Client', 'WpPost', 'Booking');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function account() {
        $this->set('css', array('contact','static'));
        $this->set('js', array('static'));
        $this->set('layoutTitle', 'My Account');

        $user = $this->Client->find('first', array(
            'conditions' => array('Client.id' => $this->Auth->user('id')),
            'contain' => array()
        ));

        if ($this->request->is('post')) {
            $data = $this->request->data;

            if (!empty($data['password']) || !empty($data['password_new']) || !empty($data['password_verify'])) {
                if ($user['Client']['password'] != md5($data['password'])) {
                    $this->Session->setFlash('Invalid password.', 'FlashMessage/error');
                } else if ($data['password_new'] != $data['password_verify']) {
                    $this->Session->setFlash('New passwords must match.', 'FlashMessage/error');
                } else if (strlen($data['password_new']) < 6) {
                    $this->Session->setFlash('New password must be at least 6 characters long.', 'FlashMessage/error');
                } else {
                    $user['Client']['password'] = $data['password_new'];
                    $this->Client->save($user);
                    $this->Session->setFlash('Password updated.', 'FlashMessage/status');
                }
            } else {
                $user['Client']['fname'] = $data['firstname'];
                $user['Client']['lname'] = $data['lastname'];
                unset($user['Client']['password']);
                $this->Client->save($user);
            }

        }


        $this->Client->find('first');

        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set('user', $user['Client']);
        $this->set(compact('blog_posts'));
    }

    public function history() {
        $this->set('css', array('history','static'));
        $this->set('layoutTitle', 'My Account');

        $bookings = $this->Booking->find('all', array(
            'conditions' => array(
                'Booking.clients_id' => $this->Auth->user('id')
            ),
            'contain' => array(
                'BookingsDetail' => array(
                    'Event' => array(
                        'EventsImage'
                    )
                )
            ),
            'order' => 'Booking.id desc'
        ));
        $tours = Hash::extract($bookings, '{n}.BookingsDetail.{n}');

        // Order by tour date
        uasort($tours, function($a, $b) {
            return strcmp($a['events_datetimes'], $b['events_datetimes']) * -1;
        });

        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts', 'tours'));
    }

    public function resend_confirmation($bookingId) {
        $check = $this->Booking->find('first', array(
            'conditions' => array(
                'Booking.id' => $bookingId,
                'Booking.clients_id' => $this->Auth->user('id')
            )
        ));

        if (!empty($check)) {
            $this->Email = ClassRegistry::init('Email');
            $this->Email->sendConfirmationEmail($booking_id, $this->config);
            $this->Session->setFlash('Confirmation email resent.', 'FlashMessage/status');
        } else {
            $this->Session->setFlash('An error occurred resending your confirmation email.', 'FlashMessage/error');
        }
        $this->redirect($this->referer());
    }

    public function forgotpassword() {
        $this->set('css', array('contact','static'));
        $this->set('layoutTitle', 'Forgot Password');
        $blog_posts = $this->WpPost->getRecentPosts();
        $this->set(compact('blog_posts'));
    }

}
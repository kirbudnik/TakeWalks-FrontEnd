<?php
App::uses('AppController', 'Controller');

class EventsController extends AppController {
    var $uses = array('Event', 'EventPrivate','Feedback');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function getGroupStages($events_id, $year, $month) {
        $this->response->type('json');
        $this->autoRender = false;

        $timestamp = strtotime("$year-$month");

        $stages = $this->Event->getStages($events_id, date('Y-m-d', $timestamp), date('Y-m-t', $timestamp));

        return json_encode(array(
            'year_month' => date('Y-n', $timestamp),
            'stages' => $stages
        ));
    }

    public function getPrivateStages($events_id, $year, $month) {
        $this->response->type('json');
        $this->autoRender = false;

        $timestamp = strtotime("$year-$month");

        $stages = $this->EventPrivate->getStages($events_id, date('Y-m-d', $timestamp), date('Y-m-t', $timestamp));

        return json_encode(array(
            'year_month' => date('Y-n', $timestamp),
            'stages' => $stages
        ));
    }

    public function getEventReviews() {
        $events_id = intval($this->request->query['e']);
        $pagination = intval($this->request->query['p']);
        $this->response->type('json');
        $this->autoRender = false;
        $eventReviews = $this->Feedback->find('all',array(
            'conditions' => array(
                'events_id' => $events_id,
                'is_published' => 1,
                'event_rating >= ' => 1,
                'event_rating <= ' => 5
            ),
            'limit' => 10,
            'order' => ['feedback_date' => 'DESC'],
            'page' => $pagination
        ));
        $reviews = array();
        foreach($eventReviews as $review){
            $reviews[] = $review['Feedback'] + array(
                    'feedback_text' => $review['Feedback']['comment_stuff_edited']
                );
        }
        $origin = isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : '*';
        $this->response->header('Access-Control-Allow-Origin', $origin);
        return json_encode(array(
            'pagination' => $pagination,
            'reviews' => $reviews
        ));
    }
    
    public function eventReviews() {
        $events_id = intval($this->request->query['e']);
        $pagination = intval($this->request->query['p']);
//        $this->response->type('json');
        $this->autoRender = false;
        $eventReviews = $this->Feedback->find('all',array(
            'conditions' => array(
                'events_id' => $events_id,
                'is_published' => 1,
                'event_rating >= ' => 1,
                'event_rating <= ' => 5
            ),
            'limit' => 48,
            'order' => ['feedback_date' => 'DESC'],
            'page' => $pagination
        ));
        $reviews = array();
        foreach($eventReviews as $review){
            $reviews[] = $review['Feedback'] + array(
                    'feedback_text' => $review['Feedback']['comment_stuff_edited']
                );
        }
        $this->layout = false;
        $origin = isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : '*';
        $this->response->header('Access-Control-Allow-Origin', $origin);
        $this->set('pagination', $pagination);
        $this->set('reviews', $reviews);
        $this->set('events_id', $events_id);
        return $this->render('event_reviews');
    }


    public function getEventStages($eventsId, $dateStart, $dateEnd) {
        $this->response->type('json');
        $this->autoRender = false;
        $dates_group = $this->Event->getStages($eventsId, date( $dateStart ), date($dateEnd ));
        $origin = isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : '*';
        $this->response->header('Access-Control-Allow-Origin', $origin);
        return json_encode(array(
            'dates_group' => $dates_group
        ));
    }
}
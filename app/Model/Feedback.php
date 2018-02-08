<?php
Class Feedback extends AppModel{
    public $useDbConfig = 'feedback';
    public $useTable = 'feedback';

    private $_domainCodes = array(
        'new-york' => 'WoNY',
        'italy' => 'WoI',
        'turkey' => 'WoT',
        'italy-es' => 'TeI'
    );

    private function _ratingToClass($rating) {
        $letterRatings = array('a','b','c','d','e','f','g','h','i','j','k');
        $letterRating = 'k';
        if($rating >=0 && $rating <= 5){
            $letterRating = $letterRatings[ceil($rating / .5)];
        }
        return $letterRating;
    }

    public function cityToCode($city){
        return isset($this->_domainCodes[$city]) ? $this->_domainCodes[$city] : '';

    }

    public function groupByEventId($city){
        $domainCode = $this->cityToCode($city);

        if(!$domainCode) return array();

        $grouped = Cache::read('ratings');

        if(!$grouped){


            $reviews = $this->find('all',array(
                'conditions' => array(
                    'domain_code' => $domainCode,
                    'is_published' => 1,
                    'event_rating >= ' => 1,
                    'event_rating <= ' => 5
                ),
                'fields' => array(
                    'events_id',
                    'count(1) as amount',
                    'avg(event_rating) as average'
                ),
                'group' => 'events_id'
            ));
            $grouped = array();

            foreach($reviews as $event){
                $grouped[$event['Feedback']['events_id']] = array(
                    'amount' => $event[0]['amount'],
                    'average' => $event[0]['average'],
                    'letterClass' => $this->_ratingToClass($event[0]['average'])
                );
            }
            Cache::write('ratings',$grouped);
        }

        return $grouped;
    }

}

?>
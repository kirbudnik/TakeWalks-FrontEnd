<?php
class EventsReview extends AppModel{

    private function _ratingToClass($rating) {
        $letterRatings = array('a','b','c','d','e','f','g','h','i','j','k');
        $letterRating = 'k';
        if($rating >=0 && $rating <= 5){
            $letterRating = $letterRatings[ceil($rating / .5)];
        }
        return $letterRating;
    }

    public function groupByEventId(){
        $grouped = Cache::read('reviews');

        if(!$grouped){


            $reviews = $this->find('all');
            $grouped = array();

            foreach($reviews as $review){
                $eventId = $review['EventsReview']['events_id'];

                if(!isset($grouped[$eventId])){
                    $grouped[$eventId]['ratings'] = array();
                }

                //WONY doesn't have this
                $grouped[$eventId]['ratings'][] = isset($review['EventsReview']['event_rating']) ? $review['EventsReview']['event_rating'] : '';
            }

            foreach($grouped as $eventId => $val){
                $grouped[$eventId]['average'] = array_sum($grouped[$eventId]['ratings']) / count($grouped[$eventId]['ratings']);
                $grouped[$eventId]['amount'] = count($grouped[$eventId]['ratings']);
                $grouped[$eventId]['letterClass'] = $this->_ratingToClass($grouped[$eventId]['average']);
                unset($grouped[$eventId]['ratings']);

            }
            Cache::write('reviews',$grouped);
        }

        return $grouped;
    }

}
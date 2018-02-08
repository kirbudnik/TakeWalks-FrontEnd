<?php
class Event extends AppModel {

    const TYPE_GROUP = 1;
    const TYPE_PRIVATE = 0;

    var $belongsTo = array(
        'Currency' => array(
            'className'	=> 'Currency',
            'foreignKey'	=> 'currencies_id'
        ),
        'Charity' => array(
            'className'  => 'Charity',
            'foreignKey' => 'charity_id'
        )
    );

    public $hasOne = array(
        'EventsDomain' => array(
            'className'	=> 'EventsDomain',
            'foreignKey'	=> 'id'
        ),
        'TripadvisorQuote' => array(
            'className' => 'TripadvisorQuote',
            'foreignKey' => 'event_id'
        )
    );

    public $hasMany = array(
        'EventsDomainsGroup' => array(
            'className'  => 'EventsDomainsGroup'
        ),
        'EventsEquipment' => array(
            'className'  => 'EventsEquipment',
            'foreignKey'    => 'events_id'
        ),
        'EventsImage' => array(
            'className'  => 'EventsImage',
            'foreignKey'    => 'events_id'
        ),
        'EventsStagePaxRemaining' => array(
            'className'  => 'EventsStagePaxRemaining',
            'foreignKey'    => 'events_id'
        ),
        'EventsStage' => array(
            'className'  => 'EventsStage',
            'foreignKey'    => 'events_id'
        ),
        'EventsSchedule' => array(
            'className'  => 'EventsSchedule',
            'foreignKey'    => 'events_id'
        ),
        'EventsReview' => array(
            'className'  => 'EventsReview',
            'foreignKey'    => 'events_id'
        ),
        'EventsSpecialPrice' => array(
            'className' => 'EventsSpecialPrice',
            'foreignKey' => 'events_id',
            'conditions' => array(
                'date_end >= CURDATE()'
            )
        ),
        'EventsSuggestion' => array(
            'className' => 'EventsSuggestion',
            'foreignKey' => 'events_id'
        ),
        'EventsPromotion' => array(
            'foreignKey' => 'events_id',
            'conditions' => [
                'start_date <= date(now())',
                'end_date >= date(now())'
            ]
        )
    );

    public $hasAndBelongsToMany = array(
        'Tag' => array(
            'className' => 'Tag',
            'joinTable' => 'events_tags',
            'foreignKey' => 'event_id',
            'associationForeignKey' => 'tag_id'
        ),
    );

    public function formatDuration($minutes) {
        $duration = '';
        $hours = floor($minutes / 60);
        if ($hours > 0) {
            $duration .= $hours;
            $duration .= ' Hour';
            if ($hours > 1) {
                $duration .= 's';
            }
            $duration .= ' ';
        }
        $minutes = $minutes - $hours * 60;
        if ($minutes > 0) {
            $duration .= $minutes . ' Minutes';
        }
        return trim($duration);
    }

    public function getStages($events_id, $date_start, $date_end){

        $date_end = date("Y-m-d",strtotime("+1 day", strtotime($date_end)));

        $conditions = array(
            'group' => 1,
            'events_id' => $events_id,
            'datetime >= ' => $date_start,
            'datetime <= ' => $date_end,
            'pax_remaining >= ' => 1
        );

        $pax_remaining = $this->EventsStagePaxRemaining->find('all',array(
            'conditions' => $conditions,
            'contain' => array('Event'),
            'order' => 'datetime'
        ));

        $stage_arr = array();
        foreach($pax_remaining as $pax) {
            list($date, $time) = explode(' ', $pax['EventsStagePaxRemaining']['datetime']);

            //if(strtotime($pax['EventsStagePaxRemaining']['datetime']) $pax['Event']['stall_hours'] )

            if(!isset($stage_arr[$date])) {
                $stage_arr[$date] = array();
            }

            $stage_arr[$date][$time] = array(
                'id' => $pax['EventsStagePaxRemaining']['id'],
                'events_id' => $pax['EventsStagePaxRemaining']['events_id'],
                'date' => $date,
                'time' => $time,
                'pretty_time' => date('g:i a', strtotime($time)),
                'group' => 1,
                'pax_remaining' => $pax['EventsStagePaxRemaining']['pax_remaining'],
                'prices' => array(
                    'adults' => $pax['EventsStagePaxRemaining']['adults_price'],
                    'seniors' => $pax['EventsStagePaxRemaining']['seniors_price'],
                    'students' => $pax['EventsStagePaxRemaining']['students_price'],
                    'children' => $pax['EventsStagePaxRemaining']['children_price'],
                    'infants' => $pax['EventsStagePaxRemaining']['infants_price'],
                )
            );
        }

        return $stage_arr;
    }

    public function getWistiaVideo($wistiaId) {
        $url = "http://fast.wistia.com/oembed?url=http://home.wistia.com/medias/{$wistiaId}?embedType=seo&handle=oEmbedVideo&width=640&height=360&videoFoam=true";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);
        curl_close($ch);
        if (empty($json)) return false;

        $video = json_decode($json, true);
        $video['id'] = $wistiaId;
        return $video;
    }

}
<?php


class BookingsDetail extends AppModel {

    public $belongsTo = array(
        'Event' => array(
            'foreignKey' => 'events_id'
        ),
        'EventsStage' => array(
            'foreignKey' => 'stage_id'
        ),
        'EventsPrimaryGroup' => array(
            'foreignKey'	=> 'events_id'
        )
    );
} 
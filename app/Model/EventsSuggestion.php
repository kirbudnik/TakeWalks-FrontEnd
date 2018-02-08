<?php
class EventsSuggestion extends AppModel {
    var $actsAs = array('Containable');

    var $belongsTo = array(
        'ParentEvent' => array(
            'className' => 'Event',
            'foreignKey' => 'events_id'
        ),
        'Event' => array(
            'className' => 'Event',
            'foreignKey' => 'suggest_id'
        )
    );
}
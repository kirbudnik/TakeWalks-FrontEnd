<?php
class EventsTag extends AppModel {

    var $actsAs = array('Containable');

    var $belongsTo = array(
        'Event' => array(
            'className'	=> 'Event',
            'foreignKey'	=> 'event_id'
        ),
        'Tag' => array(
            'className'	=> 'Tag',
            'foreignKey'	=> 'tag_id'
        ),
        'EventsPrimaryGroup' => array(
            'className'	=> 'EventsPrimaryGroup',
            'foreignKey'	=> 'event_id'
        )
    );



}

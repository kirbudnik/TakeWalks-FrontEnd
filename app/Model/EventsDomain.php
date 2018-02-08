<?php
class EventsDomain extends AppModel {

    var $actsAs = array('Containable');

    var $belongsTo = array(
        'Domain' => array(
            'className'		=> 'Domain',
            'foreignKey'	=> 'domains_id'
        ),
        'Event' => array(
            'className'		=> 'Event',
            'foreignKey'	=> 'id'
        ),
    );
}

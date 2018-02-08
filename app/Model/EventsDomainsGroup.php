<?php

class EventsDomainsGroup extends AppModel {

    var $actsAs = array('Containable');

    var $belongsTo = array(
        'DomainsGroup' => array(
            'className' => 'DomainsGroup',
            'foreignKey' => 'group_id'
        ),
        'Event' => array(
            'className' => 'Event',
            'foreignKey' => 'event_id'
        ),
        'EventsDomain' => array(
            'className' => 'EventsDomain',
            'foreignKey' => 'event_id'
        ),
    );

}

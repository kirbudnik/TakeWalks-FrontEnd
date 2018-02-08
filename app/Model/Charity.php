<?php
App::uses('AppModel', 'Model');
class Charity extends AppModel {
    public $hasMany = array(
        'CharitiesDonation' => array(
            'className'  => 'CharitiesDonation',
            'foreignKey'    => 'charity_id'
        ),
        'Event' => array(
            'className' => 'Event',
            'foreignKey'    => 'charity_id'
        )
    );
}
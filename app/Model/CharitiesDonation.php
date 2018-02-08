<?php
App::uses('AppModel', 'Model');
class CharitiesDonation extends AppModel {
    public $belongsTo = array(
        'Charity' => array(
            'className'	=> 'Charity',
            'foreignKey'	=> 'charity_id'
        ),
        'Booking' => array(
            'className'	=> 'Booking',
            'foreignKey'	=> 'booking_id'
        )
    );
}
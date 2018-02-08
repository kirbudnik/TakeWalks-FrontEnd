<?php


class Booking extends AppModel {

    public $belongsTo = array(
        'Client' => array(
            'foreignKey' => 'clients_id'
        )
    );

    public $hasMany = array(
        'BookingsDetail' => array(
            'foreignKey' => 'bookings_id'
        ),
        'BookingsAddress' => array(
            'foreignKey' => 'bookings_id'
        )
    );

} 
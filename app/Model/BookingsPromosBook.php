<?php
App::uses('AppModel', 'Model');
class BookingsPromosBook extends AppModel {
    public $belongsTo = array(
        'BookingsPromoBook' => array(
            'className'	=> 'BookingsPromo',
            'foreignKey'=> 'bookings_promos_id'
        ),
    );

    public $hasMany = array(
        'BookingsPromosQuestion' => array(
            'className'	=> 'BookingsPromosQuestion',
            'foreignKey'=> 'bookings_promos_books_id',
        ),
    );
}
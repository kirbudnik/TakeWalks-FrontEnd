<?php
App::uses('AppModel', 'Model');
class BookingsPromosQuestion extends AppModel {
    public $belongsTo = array(
        'BookingsPromoBook' => array(
            'className'	=> 'BookingsPromosBook',
            'foreignKey'=> 'bookings_promos_books_id'
        ),
    );
}
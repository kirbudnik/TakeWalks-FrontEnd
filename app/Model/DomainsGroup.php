<?php
class DomainsGroup extends AppModel {

    var $actsAs = array('Containable');

    var $belongsTo = array(
        'Domain' => array(
            'className'		=> 'Domain',
            'foreignKey'	=> 'domains_id'
        ),
    );
}

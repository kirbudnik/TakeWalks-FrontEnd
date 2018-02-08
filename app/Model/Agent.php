<?php
class Agent extends AppModel {
    var $name = 'Agent';
    var $uses = 'agents';

    public $hasMany = array(
        'Client' => array(
            'className'  => 'Client',
            'foreignKey'    => 'agents_id'
        )
    );

}
?>

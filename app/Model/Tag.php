<?php
class Tag extends AppModel {
    var $actsAs = array('Containable');

    public $hasAndBelongsToMany = array(
        'Event' => array(
            'className' => 'Event',
            'joinTable' => 'events_tags',
            'foreignKey' => 'tag_id',
            'associationForeignKey' => 'event_id'
        ),
        'EventsPrimaryGroup' => array(
            'className' => 'EventsPrimaryGroup',
            'joinTable' => 'events_tags',
            'foreignKey' => 'tag_id',
            'associationForeignKey' => 'event_id'
        ),
    );

    public function getEventsByTagName($domainId, $name, $limit = null, $excludeIds = array()) {
        $tag = $this->find('first', array(
            'fields' => array('id'),
            'conditions' => array('Tag.name' => $name),
            'contain' => array(
                'Event' => array(
                    'conditions' => array(
                        'NOT' => array('Event.id' => $excludeIds),
                        'Event.visible' => 1,
                    ),
                    'fields' => array('id'),
                    'limit' => $limit,
                )
            )
        ));

        $event_ids = Hash::extract($tag, 'Event.{n}.id');
        return $this->Event->find('all', array(
            'conditions' => array(
                'Event.id' => $event_ids,
                'EventsDomain.domains_id' => $domainId,
                'Event.visible' => 1,
            ),
            'contain' => array(
                'EventsImage' => array(
                    'order' => array('EventsImage.image_order ASC'),
                ),
                'EventsDomain',
                'Tag' => array(
                    'conditions' => array(
                        'Tag.name NOT' => array('featured', 'superfeatured')
                    )
                ),
                'EventsSpecialPrice',
                'EventsDomainsGroup' => array(
                    'DomainsGroup',
                    'conditions' => array(
                        'EventsDomainsGroup.primary' => 1
                    )
                )
            )
        ));
    }

    public function featured($limit = false, $config){
        if($limit === false) {
            switch ($config->domain) {
                case 'italy':
                case 'turkey':
                    $limit = 5;
                    break;
                case 'nyc':
                    $limit = 10;
                    break;
                case 'italyes':
                    $limit = 6;
                    break;
            }
        }

        $super = $this->getEventsByTagName($config->domainId, 'superfeatured', $limit);
        $superIds = Hash::extract($super, '{n}.Event.id');

        $featured = $this->getEventsByTagName($config->domainId, 'featured', $limit - count($super), $superIds);

        return array_merge($super, $featured);
    }

}

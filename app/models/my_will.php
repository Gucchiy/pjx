<?php
class MyWill extends AppModel {
	var $name = 'MyWill';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Will' => array(
			'className' => 'Will',
			'foreignKey' => 'will_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentWill' => array(
			'className' => 'ParentWill',
			'foreignKey' => 'parent_will_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

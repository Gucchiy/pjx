<?php
class Willconnector extends AppModel {
	var $name = 'Willconnector';
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
		)
		/* 親との連結を検討 */
		,
		'ParentWillconnector' => array(
			'className' => 'Willconnector',
			'foreignKey' => 'parent_willconnector_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

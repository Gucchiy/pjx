<?php
class Question extends AppModel {
	var $name = 'Question';
	var $displayField = 'question';
	var $validate = array(
		'question' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	
	var $belongsTo = array(
		'PrevQuestion' => array(
			'className' => 'Question',
			'foreignKey' => 'prev_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	/*	
	var $hasOne = array(
		'NextQuestion' => array(
			'className' => 'Question',
			'foreignKey' => 'prev_question_id'
		)
	);
	*/
	
	var $hasMany = array(
		'Answer' => array(
			'className' => 'Answer',
			'foreignKey' => 'question_id'
		)
	);

}

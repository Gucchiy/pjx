<?php
class Chiebukuro extends AppModel {
	var $name = 'Chiebukuro';
	var $displayField = 'content';
	var $apiKey = "Oh5Rg6ixg67yYhm3j7phsmhiZSTHW8IgDdCDXkMQ.80BN9kM6hcK9RXday1LI6HRckXjfccn";
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Will' => array(
			'className' => 'Will',
			'foreignKey' => 'will_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

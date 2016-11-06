<?php
class Tweet extends AppModel {
	var $name = 'Tweet';
	var $displayField = 'tweet';

	// For twitter
    // Consumer key の値
    var $consumerKey = 'iY5JduddxIjnRyzZfl34w';
    // Consumer secret の値
    var $consumerSecret = '5V3uuzJKxFfygdZVEpCHSGKprR5IK62xXe6iWiEiA';
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Willconnectors' => array(
			'className' => 'Willconnectors',
			'foreignKey' => 'willconnector_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
}

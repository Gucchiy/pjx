<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */
App::import('Vendor', 'facebook/src/facebook');   

class AppController extends Controller {

	var $uses = array('User');
	
	var $fbapi_appid = '215361505226167';	// localhost 用
	var $fbapi_secret = '1501be2717029dff06bfe634b97d859f';
	// var $fbapi_appid = '223552481067647';	// pjx.sakura 用
	// var $fbapi_secret = '135f01caa13e43db47833a9d218302ee';
	var $facebook;
	var $fb_me;
	var $fb_friends;
	var $user_id;
	var $user_data;
	
    public function __construct()
    {
		parent::__construct();
		
		// リリース用に入れ替え
		$fbapi_appid = Configure::read('fbapi_appid');
		if( strlen($fbapi_appid ) )
			$this->fbapi_appid =$fbapi_appid;
		
		$fbapi_secret = Configure::read('fbapi_secret');
		if( strlen( $fbapi_secret ) )
			$this->fbapi_secret = $fbapi_secret;
		
 		$this->facebook = new Facebook(array(
			'appId' => 	$this->fbapi_appid,
			'secret' => $this->fbapi_secret,
			'cookie' => true,
		));
    }
	
	function callback(){  
	   
	}  
	
	function beforeFilter() {
		
		$this->user_id = $this->Session->read('user_id');		

		if( $this->name == 'Users' && $this->action == 'callback_facebook' ) return;
		if( $this->name == 'Users' && $this->action == 'logout' ) return;

		parent::beforeFilter();
		
		$pjx_base_url = Configure::read('pjx_base_url');
		
		$fb_user = $this->facebook->getUser();
		// test
		/* $url = $this->facebook->getLogoutUrl(array('next'=>$pjx_base_url.'users/logout'));
				$this->set('fb_logout_url', $url);
		echo 'tst'.$url.'<br />';
		echo $this->fbapi_appid.'<br />';
		echo $this->fbapi_secret.'<br />';
		 */
		if( $fb_user ){
			try{
				$uid = 	$this->facebook->getUser();
				
				// echo count( $this->Session->read('fb_me') );
				if( $this->Session->read('fb_me') ){
				
					$this->fb_me = $this->Session->read('fb_me');
					if( !isset($this->fb_me['username'])){	// username はセットされていないことがある
						$this->fb_me['username'] = $this->fb_me['username']['first_name'];
					}
					
				}else{
					$this->fb_me = 	$this->facebook->api('/me');//ログイン情報					
					if( !isset($this->fb_me['username'])){	// username はセットされていないことがある
						$this->fb_me['username'] = $this->fb_me['first_name'];
					}
					$this->Session->write('fb_me', $this->fb_me );
				}
				
				if( $this->Session->read('fb_friends') ){
					
					$this->fb_friends = $this->Session->read('fb_friends');
				
				}else{
					
					$this->fb_friends = $this->facebook->api('/me/friends');//友達情報
					$this->Session->write('fb_friends', $this->fb_friends );
				}
				
				$url = $this->facebook->getLogoutUrl(array('next'=>$pjx_base_url.'users/logout'));
				$this->set('fb_logout_url', $url);
			}
			catch(FacebookApiException $err){
				error_log($err);
			}
						
			if( !isset( $this->user_id ) ){
				
				// echo 'etst<br />';
				
				$user_data = $this->User->find('first',
					array('conditions'=>array('fbid'=>$this->fb_me['id'])));
				$this->user_id = $user_data['User']['id'];
				$this->Session->write('user_id', $this->user_id );
			}
			
			$this->user_data = array('User'=>
				array('id'=>$this->user_id,'username'=>$this->fb_me['username'],'email'=>$this->fb_me['email']));
				
			$this->set('user_data', $this->user_data );
			$this->set('fb_me', $this->fb_me );
					
		}else{
			
			if( $this->name == 'Wills' && $this->action == 'index' ){
				
				$url = $this->facebook->getLoginUrl(
					array('scope' => 'email,publish_stream', 'redirect_uri'=>$pjx_base_url.'users/callback_facebook'));  			
				$this->set('fb_login_url', $url);
				
			}else{
				
				$this->redirect(array( 'controller' => 'wills', 'action' => 'index'));
			}
		}
						
	}

	private function __randomString($minlength = 20, $maxlength = 20, $useupper = true, $usespecial = false, $usenumbers = true){
		$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|][";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		$key = '';
		for ($i=0; $i<$length; $i++){
			$key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		}
		return $key;
	}

}

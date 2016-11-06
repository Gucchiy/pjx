<?php
App::import('Vendor', 'facebook/src/facebook'); 

class UsersController extends AppController {
	
	var $name = 'Users';
	var $components = array('Cookie');
	var $uses = array("User","Willconnector","Will","Answer","Question","Passport","Tweet","Question");//使用するモデルを追加

	// passport
	var $expires = 1209600; //60 * 60 * 24 * 14

	function index() {
		
		// POST データ処理
		if(!empty($this->data)){
			
			// インタビュー結果編集
			if( isset($this->data['Answer']['id']) ){
				
				$this->Answer->save($this->data);
				$this->redirect(array('action' => 'index'));
			}
		}
				
		// インタビューの結果リストを表示する		
		$options = array(
		      // 'fields' => array('Will.id','Will.title', 'Will.created', 'Will.modified'),
		      'conditions'=>array(
		            'Answer.user_id'=>$this->user_id		            
		      ),
		      'limit' => '10'
		   );
		$this->paginate = $options;
		$this->set('answers', $this->paginate('Answer') );
		
		// FB のお友達且つ DB 管理されている人を探す
		$friends_ids = array();
		$friends = $this->fb_friends['data'];
		foreach( $friends as $friend ){
			
			array_push( $friends_ids, $friend['id']);
		}
		$this->User->recursive = 3;	// User の最新 Willを取得する
		$friends = $this->User->find('all', array('conditions'=>array('fbid'=>$friends_ids)));
		$this->set(compact('friends'));
	}
	
	function setting()
	{
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {

				$auth = $this->Auth->user();
				$auth['User']['username'] = $this->data['User']['username'];
				$auth['User']['email'] = $this->data['User']['email'];
				$this->Session->write('Auth', $auth);				
				
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));


			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}

		$this->User->recursive = 0;
		$user = $this->User->read(null,$this->user_id);
		$this->set('user', $user );
		$this->data = $user;
		
		// print_r($this->data);
		// echo FULL_BASE_URL."<br />";
		// echo ROOT."<br />";
		// echo Configure::read('pjx_base_url')."<br />";
		
		/*
		if( !isset($user['User']['twitter1'])){echo "<br />come1<br />";}
		if( $user['User']['twitter1']==NULL){ echo "<br />Come2<br />";}
		 */
		// $this->set('users', $this->paginate());
		
	}
	
	function setting_twitter() {
		
        $consumer = new OAuth_Consumer($this->Tweet->consumerKey, $this->Tweet->consumerSecret);		
		$pjx_base_url = Configure::read('pjx_base_url');
		
		
		// 認証後、「http://localhost/examples/exMain」にリダイレクトする
		$requestToken=$consumer->getRequestToken(
		                           'http://twitter.com/oauth/request_token',
		                           $pjx_base_url.'users/callback_twitter');
		
		
		// 認証後、アクセストークンを取得する際に必要なので保存
		$this->Session->write('request_token',$requestToken);
		
		
		// Twitterの認証ページにリダイレクト
		$this->redirect('http://twitter.com/oauth/authorize?oauth_token='
		                  .$requestToken->key);

		// $this->redirect(array('action' => 'index'));
	}
	
	function callback_twitter(){
	    // 認証を拒否したかどうか調べる
	    if (isset($this->params['url']['denied'])) {
			$this->Session->setFlash( 'access denied from twitter' );
			$this->redirect(array('action' => 'index'));
	      	      return;
	    }
		$user = $this->Auth->User();
		
        $consumer = new OAuth_Consumer($this->Tweet->consumerKey, $this->Tweet->consumerSecret);		

	    $requestToken=$this->Session->read('request_token');
	    $accessToken=$consumer->getAccessToken(
	                              'http://twitter.com/oauth/access_token',
	                              $requestToken);
							
		$this->User->save(array('User'=>array(
			'id'=>$user['User']['id'],
			'twitter1'=>$accessToken->key,'twitter2'=>$accessToken->secret)));

		$auth = $this->Auth->user();
		$auth['User']['twitter1'] = $accessToken->key;
		$auth['User']['twitter2'] = $accessToken->secret;
		$this->Session->write('Auth', $auth);
	
	    // 自分のつぶやきを一つ取得
	    /*
	    $tweet=$consumer->get(
	                          $accessToken->key,
	                          $accessToken->secret,
	                          'http://api.twitter.com/1/statuses/user_timeline.xml',
	                          array('count'=>1));
							  
		$this->Session->setFlash( $tweet );
		 */
		
		$this->Session->setFlash( 'twitter認証に成功しました' );
		$this->redirect(array('action' => 'setting'));
			
	}

    function callback_facebook(){  

		// $this->autoRender = false;  
		       
        $uid = $this->facebook->getUser();  
        $me = null;  
        
        if ($uid) {  
            try {  
                $uid = $this->facebook->getUser();  
                $me = $this->facebook->api('/me');  
            } catch (FacebookApiException $e) {  
                error_log($e);  
            }  
        }
		
		if( !isset($me['username']) ){
			
			$me['username'] = $me['first_name'];
		}

        $access_token = $this->facebook->getAccessToken();  
        $user_data = array(  
            'User' => array(  
                'username' => $me['username'],  
                'email' => $me['email'],  
            	'fbid' => $me['id'],
                'fbname' => $me['name'], 
                'fbtoken' => $access_token  
            )  
        );

		$user_data_db = $this->User->find('first', array('conditions'=>array('fbid'=>$me['id'])));
		
		// query 結果がある(count はなぜか1を返す)
		if( isset( $user_data_db['User'] ) ){
			
			$user_data['User']['id'] = $user_data_db['User']['id'];
			$this->User->save( $user_data );

		}else{
			
			$this->User->create();
			$this->User->save( $user_data );			
			$user_data['User']['id'] = $this->User->getID();
			
			$this->Session->setFlash('はじめまして、'.$me['username'].'さん');
		}
		$this->Session->write('user_id',$user_data['User']['id']);
		
		// $this->redirect(array('controller'=>'wills','action'=>'index','#2'));
		$pjx_base_url = Configure::read('pjx_base_url');
		$this->set('redirect_url', $pjx_base_url.'wills');
		// $this->redirect($pjx_base_url.'wills');
		// header('Location: '.$pjx_base_url.'users');
    }  

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$user = $this->User->read(null,$id);
		$this->set( compact('user') );
		
		$answers = $this->Answer->find('all', array('conditions'=>array('Answer.user_id'=>$id)));
		$this->set( compact('answers') );
		
		$wills = $this->Willconnector->find('all', 
		      array('conditions'=>array(
		            'Willconnector.user_id'=>$id,
		            'Willconnector.parent_willconnector_id'=>null,
		      ),
		      'order' => array('Will.modified DESC'),
			));
		$this->set( compact('wills') );
		
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		// $todos = $this->User->Todo->find('list');
		// $this->set(compact('todos'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		// $todos = $this->User->Todo->find('list');
		// $this->set(compact('todos'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function delete_answer($id = null){
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for answer', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Answer->delete($id)) {
			$this->Session->setFlash(__('インタビューへの回答を削除しました', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Answer was not deleted', true));
		$this->redirect(array('action' => 'index'));
		
	}
	
	// twitter 
	function twitter()
	{
		
	}
	
	function beforeFilter()
	{
		parent::beforeFilter();

	    // $this->Auth->allow('add','callback_facebook');

		// $this->Auth->autoRedirect = false;
		// $this->Auth->loginError = 'ユーザー名もしくはパスワードが違います。';
		// $this->Auth->loginRedirect = 'admin/memos/index';
		/*
		$this->Auth->autoRedirect = false;  
		
		$this->Auth->UserModel = "user";
		
		$this->Auth->loginRedirect = array(
			"controller" => 'wills',
			"action" => 'index'
		);
		
	    // user は email で確認する
	    $this->Auth->fields = array(
	        'username' => 'email',
	        'password' => 'password'
	        );	
	    $this->Auth->allow('add');
		 */
	}
	
	function logout()
	{
		$this->facebook->destroySession();
		$this->redirect(array('controller'=>'Wills','action'=>'index'));
	}
}

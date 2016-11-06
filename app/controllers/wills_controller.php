<?php
App::import('Vendor', 'Oauth', array('file'=>'OAuth'.DS.'oauth_consumer.php'));

class WillsController extends AppController {

	var $name = 'Wills';
	var $uses = array('Will','User','Willconnector','Tweet','Willseed',"Question","Answer");


    // Access Token の値
    // var $accessToken = '10701462-9PMk5LS4VNUpmqv3EGcDnOlvPvmz8ASMGWHy3BBNq';
    // Access Token Secret の値
    // var $accessTokenSecret = 'C9Wp6rxgpSaQFx9E6LWXB0xGsFt8DZAsy69cZ9ucrlo';
	
	protected function get_words($query, &$verb=array(), &$adjective=array() ){
		
		$query = urlencode($query);
		$apiKey = "Oh5Rg6ixg67yYhm3j7phsmhiZSTHW8IgDdCDXkMQ.80BN9kM6hcK9RXday1LI6HRckXjfccn";

		$url = "http://jlp.yahooapis.jp/MAService/V1/parse?appid=" . $apiKey . "&sentence=" . $query;
		
		$rss = file_get_contents($url);
		$xml = simplexml_load_string($rss);
		
		$retValue = Array();
		$verb = Array();
		$adjective = Array();
		// $i = 0;		
		foreach($xml->ma_result->word_list->word as $item) {
			
			if( strcmp( $item->pos , "名詞" ) == 0 ){
			
				array_push($retValue, $item->surface );
			}
			
			if( strcmp( $item->pos, "動詞") == 0 ){
				
				array_push($verb, $item->surface );
			}
			
			if( strcmp( $item->pos, "形容詞") == 0 ){
				
				array_push($adjective, $item->surface );
			}
			
			
			
			// $item->surfaceに解析後の単語、$item->posにその単語の品詞が入っているので、後は好きな用に処理すればよし。今回は単語と品詞を表示するだけ。
		}
		
		return $retValue;
		
	}

	function index() {

		if( !isset($this->fb_me) ){	return; }
		
		$this->Will->recursive = 0;
		// $this->set('debug_data',$this->data);
		
		// 入力処理
		if (!empty($this->data)) {
			
			$redirect = false;
			// Will が入力されていない場合もある-> Answer だけを登録
			if( isset($this->data['Will']) && strlen($this->data['Will']['title'])){

				$this->Will->create();
				 // 形態素解析後のデータを持つ
				$words_noun = $this->get_words($this->data['Will']['title'], $words_verb, $words_adj );
				$this->data['Will']['words_noun'] = implode( '|', $words_noun );
				$this->data['Will']['words_verb'] = implode( '|', $words_verb );
				$this->data['Will']['words_adjective'] = implode( '|', $words_adj );
				if ($this->Will->save($this->data)) {
	
					$this->Willconnector->create();
					$this->Willconnector->save(
						array('Willconnector'=>
							array('user_id'=>$this->user_data['User']['id'],'will_id'=>$this->Will->getID())));
	
					$this->Session->setFlash(__('新たな挑戦が登録されました', true));
					$redirect = true;
	
	
				} else {
					$this->Session->setFlash(__('システム不具合で登録できませんでした。', true));
				}
				
			}
			
			if( isset($this->data['Answer']) && strlen($this->data['Answer']['content'])){
					
				$this->Answer->save($this->data);
				$redirect = true;
			}
			if( $redirect )
				$this->redirect(array('action' => 'index'));
			
		}


		// 自身の全Willを知っているのは Willconnector
		// 表示リストの query
		$options = array(
		      // 'fields' => array('Will.id','Will.title', 'Will.created', 'Will.modified'),
		      'conditions'=>array(
		            'Willconnector.user_id'=>$this->user_id,
		            'Willconnector.parent_willconnector_id'=>null,
		            
		      ),
		      'order' => array('Will.modified DESC'),
		      'limit' => '10'
		   );
		$this->paginate = $options;
		$this->set('wills', $this->paginate('Willconnector') );


		// インタビューも取得しておく
		
		// 既に答えているインタビューID を生成
		$answers = $this->Answer->find('all',
			array('conditions'=>array('user_id'=>$this->user_id)));
			
		$answered_question_ids = array();
		foreach ($answers as $answer) {
			
			array_push( $answered_question_ids, $answer['Answer']['question_id']);
		}

		// 取得するべき インタビューの prev_question列
		$prev_question_ids = array_merge(array(0),$answered_question_ids);

		// SQL conditions の生成
		$conditions = array( 'Question.prev_question_id'=>$prev_question_ids );
		if(count($answered_question_ids)){
			
			$conditions = array_merge($conditions,array('NOT'=>array('Question.id'=>$answered_question_ids)));
		}
		
		
		$this->Question->recursive = 3;	// とりあえず3段まで
				
		$question = $this->Question->find('first',
			array('conditions'=>
				$conditions,
				'order'=>'rand()','limit'=>'1'));
				

		$this->set( compact('question') );
		// $this->set( 'debug_data', $question );
		
	}

	function interview() {
		
		$this->Question->recursive = 3;
		$questions = array();
		
		$question_first = $this->Question->find('first',
			array('conditions'=>array('Question.prev_question_id'=>NULL),
				'order'=>'rand()','limit'=>'1'));

		$questions = array();				
		if( isset($question_first['Question']['id']) ){

			array_push( $questions, $question_first['Question'] );

			if( isset($question_first['NextQuestion']) && isset($question_first['NextQuestion']['id'])){

				$question_next = $question_first['NextQuestion'];
				
				while( isset($question_next) ){
					 
					print_r($question_next);
					echo ('<br />0.5<br />');
					if( isset($question_next['NextQuestion']) && isset($question_next['NextQuestion']['id']) ){
						
						$question_push = $question_next;
						unset( $question_push['NextQuestion']);
						$question_next = $question_next['NextQuestion'];
						
					}else{
						
						$question_push = $question_next;
						$question_next = NULL;


					}
					array_push( $questions, $question_push );				
					print_r($question_push);
					echo ('<br />1<br />');
				}
				
			}
			
		}

		$this->set( compact('questions') );
		$this->set( compact('question_first'));
	}

	function view($id = null) {	// $id は willconnector

		$this->Session->write('Back_URL',Router::Url());
		// $this->Session->write('Back_URL',Router::Url());

		if (!$id) {
			$this->Session->setFlash(__('Invalid will', true));
			$this->redirect(array('action' => 'index'));
		}

		// $user_data = $this->Auth->User();
		// $this->set('user_data', $this->user_data['User'] );
		$will = $this->Willconnector->read(null, $id);
		$this->set('will', $will);
		

		// マイルストーン生成処理
		if (!empty($this->data)) {
			
			if( strlen( $this->data['Will']['title'] ) ){
				
				$this->Will->create();
	
				// 形態素解析後のデータを持つ
				$words_noun = $this->get_words($this->data['Will']['title'], $words_verb, $words_adj );
				$this->data['Will']['words_noun'] = implode( '|', $words_noun );
				$this->data['Will']['words_verb'] = implode( '|', $words_verb );
				$this->data['Will']['words_adjective'] = implode( '|', $words_adj );
				// 関連キーワードのコピー
				$this->data['Will']['related_words_noun'] = $will['Will']['words_noun'];
				$this->data['Will']['related_words_verb'] = $will['Will']['words_verb'];
				$this->data['Will']['related_words_adjective'] = $will['Will']['words_adjective'];
				
				if ($this->Will->save($this->data)) {
	
					$this->Willconnector->create();
					$this->Willconnector->save(
						array('Willconnector'=> array('user_id'=>$this->user_data['User']['id'],
								'will_id'=>$this->Will->getID(),
								'parent_willconnector_id'=>$will['Willconnector']['id'] )));
	
					$this->Session->setFlash(__('The will has been saved', true));
					// post データの確認（チェックボックス）
					// print_r( $this->data );
					// データの確認用途
					// print_r(  implode( ',', $this->get_words($this->data['Will']['title'])));
					$this->redirect(array('action' => 'view', $id));
				} else {
					$this->Session->setFlash(__('The will could not be saved. Please, try again.', true));
				}
			}
			
			// print_r($this->data);
			
			// お勧めデータの確認（チェックボックス）
			foreach( $this->data['Will']['recommends'] as $recommend_id ){
				
				// echo $recommend_id."<br />";
				// echo $recommend_title."<br />";
				$this->Willconnector->create();
				$this->Willconnector->save(
					array('Willconnector'=> array('user_id'=>$this->user_data['User']['id'],
							'will_id'=>$recommend_id,
							'parent_willconnector_id'=>$will['Willconnector']['id'] )));
				
			}
			
		}

		// $user_data = $this->Auth->User();
		// $this->set('user_data', $user_data['User'] );
		
		// 自身の全Willを知っているのは Willconnector
		$options = array(
		      // 'fields' => array('Will.id','Will.title', 'Will.created', 'Will.modified'),
		      'conditions'=>array(
		            'Willconnector.user_id'=>$this->user_data['User']['id'],
		            'Willconnector.parent_willconnector_id'=>$will['Willconnector']['id'],
		            
		      ),
		      'limit' => 10
		   );
		$this->paginate = $options;
		$this->set('millstones', $this->paginate('Willconnector') );

		// お勧めの生成

		$words = explode("|",$will['Will']['words_noun']);
		$finds = array();
				
		foreach( $words as $word )
		{
			// Word ごとに検索結果を array に入力
			if( strlen( $word ) ){
					
				$find_records = $this->Willconnector->find('all',
				
					// お勧めの検索：user_id = NULL は別途 OR を取らねばならない
					array('conditions'=>array('Will.related_words_noun LIKE ?'=>'%'.$word.'%',
							'Willconnector.parent_willconnector_id'=>NULL,
							'OR'=>array('Willconnector.user_id <>'=>$this->user_data['User']['id'],'Willconnector.user_id'=>null))));

				// $finds_records['count']=1;
				if( count( $find_records ) ){	
					array_push($finds, $find_records );
				}
			}
		}
		
		// お勧めフォーム用
		$recommends = array();

		// print_r($words);
		// echo count($finds);

		/*
		echo "<br />検索結果-----------------------------------<br />\n";
		
		pr(print_r( $finds ));
		
		echo "<br />IDs---------------------------------<br />\n";

		foreach( $finds as $find ){
			foreach( $find as $data ){
				
				echo $data['Will']['id'].'<br />';
			}
		}
		

		echo "<br />-----------------------------------<br />\n";
		 */
	
		// レコメンドされるべきものが一つ以上はある
		$recommend_datas = array();
		
		foreach( $finds as $find ){
			
			foreach( $find as $data ){
				for( $i=0; $i < count($recommend_datas); $i++ ){
									
					if( $recommend_datas[$i]['Will']['id'] == $data['Will']['id'] ){
						
						$recommend_datas[$i]['count']++;
						if( isset($data['User']['id']) ){
							array_push( $recommend_datas[$i]['Users'], $data['User']);
						}
						$i = -1;
						break;
					}						
				}
				
				// もし $recommend_datas に未登録であった場合
				if( $i != -1 ){

					// すでに登録済みのものを除外処理
					// will は、他の人(またはnull）が登録している willで、自分が持っている willである可能性がある
					// TODO: ただし、もっと良い方法はあるような気がする
					if( !$this->Willconnector->find('count',
								array('conditions'=>array('Willconnector.user_id'=>$this->user_data['User']['id'],
									'Willconnector.will_id'=>$data['Will']['id'],
									'OR'=>array(
										array('Willconnector.parent_willconnector_id'=>$will['Willconnector']['id']),
										array('Willconnector.parent_willconnector_id'=>NULL )
									)
								))								
					)){										

						$recommend_data =
							array( 'Will'=>$data['Will'], 'count'=>1 );
						if( isset($data['User']['id']) ){
							$recommend_data['Users'] = array($data['User']);						
						}else{
							$recommend_data['Users'] = array();													
						}				
						array_push( $recommend_datas, $recommend_data );
					}
				}
			}
		}
		
/*		
		echo "<br />生成結果-----------------------------------<br />\n";
		foreach( $recommend_datas as $data ){
			
			echo $data['Will']['id'].'<br />';
		}
		

		echo "<br />-----------------------------------<br />\n";


		pr(print_r( $recommend_datas ));
*/				
		function sort_compare($a, $b){
			
			if( $a['count'] > $b['count'] ){
				
				return -1;
			
			}else{
				
				return 1;
			}
		}
		
		// $recommend_datas[3]['count']=3;
		
		usort( $recommend_datas, "sort_compare");
/*	
		echo "<br />-----------------------------------<br />\n";		
		pr(print_r( $recommend_datas ));
 */	
		foreach( $recommend_datas as $recommend_data ){
			
			$recommends[$recommend_data['Will']['id']] = $recommend_data['Will']['title'];
		}

		$this->set('recommends', $recommends );
		$this->set('recommend_datas', $recommend_datas );
	}

	function add() {
		// $user_data = $this->Auth->User();
		// $this->set('user_data', $user_data['User'] );

		if (!empty($this->data)) {
			$this->Will->create();
			if ($this->Will->save($this->data)) {

				// will conncetor へ登録
				$this->Willconnector->create();
				$this->Willconnector->save(
					array('Willconnector'=>
						array('user_id'=>$user_data['User']['id'],'will_id'=>$this->Will->getID())));

				$this->Session->setFlash(__('The will has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The will could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		$this->set('back_url',$this->Session->read('Back_URL'));
		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid will', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Will->save($this->data)) {
				$this->Session->setFlash(__('The will has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The will could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Will->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for will', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Will->delete($id)) {
			$this->Session->setFlash(__('Will deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Will was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}

	function delete_Willconnector($id = null) {
			
		$redirect_url = FULL_BASE_URL.$this->Session->read('Back_URL2');
		$this->Session->write('Back_URL',Router::Url());
		
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for will', true));
			$this->redirect($redirect_url);
		}
		if ($this->Willconnector->delete($id)) {
			// $this->Session->setFlash(__('Willconnector deleted', true));
			$this->Session->setFlash($redirect_url);
			$this->redirect($redirect_url);
			
		}
		$this->Session->setFlash(__('Willconnector was not deleted', true));
		$this->redirect($redirect_url);
	}

	function beforeFilter()
	{
		parent::beforeFilter();
		// $this->Auth->allow('index', 'view');
	}
	
	function progress($id=null)	{ // $id は willconnector
	
		$this->set('back_url',$this->Session->read('Back_URL'));
		$this->Session->write('Back_URL2', $this->Session->read('Back_URL'));	// 更に前を保存
		$this->Session->write('Back_URL',Router::Url());

		if (!$id) {
			$this->Session->setFlash(__('Invalid id for will', true));
			$this->redirect(array('action'=>'index'));
		}
		// echo "id:".$id."<br />";
		
		// $user_data = $this->Auth->User();
		// $this->set('user_data', $this->user_data['User'] );
		$willconnector = $this->Willconnector->read(null, $id);		
		$this->set('will', $willconnector);
		// pr($this->data);
		// echo "test";
		
		if( !empty($this->data) ){
			
			/*			
	        $consumer = new OAuth_Consumer($this->Tweet->consumerKey, $this->Tweet->consumerSecret);		
	        $tweet = $consumer->post(
	            $user_data['User']['twitter1'],
	            $user_data['User']['twitter2'],
	            'http://twitter.com/statuses/update.xml',
	            array('status'=>$this->data['Tweet']['content'])
	        );
			 * 
			 */
			
			if( isset($this->data['Tweet']['content'])){

				$this->Tweet->create();
				$this->data['Tweet']['willconnector_id']=$id;
				// echo $id."<br />";
				// print_r($this->data);
				$this->Tweet->save($this->data);
				$this->facebook->api('/me/feed','POST',
					array('message'=>'[PJX]['.$willconnector['Will']['title'].']'.$this->data['Tweet']['content']));
				
			}
			
			if( isset($this->data['Willconnector']['period'])){
				
				$this->Willconnector->save($this->data);
			}

			// $this->data = $this->Willconnector->read(null, $id);
			
			
			
			$this->redirect(array('action'=>'progress',$id));	
		}

		if (empty($this->data)) {
			$this->data = $this->Willconnector->read(null, $id);
		}
		
		$this->set('tweets', $this->Tweet->find('all',
			array('conditions'=>array('Tweet.willconnector_id'=>$id), 'order'=>'Tweet.modified DESC')));
		
		$this->set('count', $this->Tweet->find('count',
			array('conditions'=>array('willconnector_id'=>$id),'group'=>'DATE(Tweet.modified)' ) ) );
	
        // pr($tweet);	
	}

	function execution()
	{
		$this->Session->write('Back_URL',Router::Url());

		$this->Willconnector->recursive = 3;

		$options = array('conditions'=>array('Willconnector.user_id'=>$this->user_id,'Willconnector.parent_willconnector_id <>'=>null));

		$this->paginate = $options;
		$this->set('wills', $this->paginate('Willconnector') );
	}

	function show($id=null){ // $id は will
		$this->set('back_url',$this->Session->read('Back_URL'));
				
		if (!$id) {
			$this->Session->setFlash(__('Invalid will', true));
			$this->redirect(array('action' => 'index'));
		}
		
		// $user_data = $this->Auth->User();
		$this->set('user_data', $this->user_data['User'] );
		$will = $this->Will->read(null, $id);
		$this->set('will', $will);
		
	}

	function import(){
		
		$willseeds = $this->Willseed->find('all');
		foreach( $willseeds as $willseed ){
			
			if( !$willseed['Willseed']['exported'] ){

				// 形態素解析後のデータを持つ
				$words_noun = $this->get_words($willseed['Willseed']['title'], $words_verb, $words_adj );
				
				// Will を生成する				
				$this->Will->create();
				$this->Will->save(
					array('Will'=>
						array('title'=>$willseed['Willseed']['title'],
						'words_noun'=>implode('|',$words_noun),
						'words_verb'=>implode('|',$words_verb),
						'words_adjective'=>implode('|',$words_adj),
						'related_words_noun'=>$willseed['Willseed']['words_noun'],
						'related_words_verb'=>$willseed['Willseed']['words_verb'],
						'related_words_adjective'=>$willseed['Willseed']['words_adjective'],
						)));

				// Willconnector を生成する
				$this->Willconnector->create();
				$this->Willconnector->save(
					array('Willconnector'=>
						array('will_id'=>$this->Will->getID())));

				// import 済みフラグを立てる
				$willseed['Willseed']['exported']='1';
				$this->Willseed->save($willseed);
			}
		}
	}

}

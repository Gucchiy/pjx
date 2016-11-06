<?php
App::import('Vendor', 'Oauth', array('file'=>'OAuth'.DS.'oauth_consumer.php'));

class WillsController extends AppController {

	var $name = 'Wills';
	var $components = array('Auth');
	var $uses = array('Will','User','Willconnector','Tweet','Willseed');


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
		$this->Will->recursive = 0;
		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );

		// 入力処理
		if (!empty($this->data)) {
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
						array('user_id'=>$user_data['User']['id'],'will_id'=>$this->Will->getID())));

				$this->Session->setFlash(__('新たな挑戦が登録されました', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('システム不具合で登録できませんでした。', true));
			}
		}

		// 自身の全Willを知っているのは Willconnector
		$options = array(
		      // 'fields' => array('Will.id','Will.title', 'Will.created', 'Will.modified'),
		      'conditions'=>array(
		            'Willconnector.user_id'=>$user_data['User']['id'],
		            'Willconnector.parent_will_id'=>null,
		            
		      ),
		      'limit' => 10
		   );
		$this->paginate = $options;
		$this->set('wills', $this->paginate('Willconnector') );
		
		// $this->set('wills', $this->paginate( array('Willconnector.user_id' => $user_data['User']['id']) ));
		// $this->set('wills', $this->paginate());
	}

	function view($id = null) {

		$this->Session->write('Back_URL',Router::Url());

		if (!$id) {
			$this->Session->setFlash(__('Invalid will', true));
			$this->redirect(array('action' => 'index'));
		}

		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );
		$will = $this->Will->read(null, $id);
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
						array('Willconnector'=> array('user_id'=>$user_data['User']['id'],
								'will_id'=>$this->Will->getID(),
								'parent_will_id'=>$id )));
	
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
					array('Willconnector'=> array('user_id'=>$user_data['User']['id'],
							'will_id'=>$recommend_id,
							'parent_will_id'=>$id )));
				
			}
			
		}

		// $user_data = $this->Auth->User();
		// $this->set('user_data', $user_data['User'] );
		
		// 自身の全Willを知っているのは Willconnector
		$options = array(
		      // 'fields' => array('Will.id','Will.title', 'Will.created', 'Will.modified'),
		      'conditions'=>array(
		            'Willconnector.user_id'=>$user_data['User']['id'],
		            'Willconnector.parent_will_id'=>$id,
		            
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
							'OR'=>array('Willconnector.user_id <>'=>$user_data['User']['id'],'Willconnector.user_id'=>null))));

				// $finds_records['count']=1;
				if( count( $find_records ) ){	
					array_push($finds, $find_records );
				}
				// $finds = array_merge( $finds, $finds_record );
				
				/*
				foreach( $finds_record as $data ){
					// 子供たちも追加する
					array_push($finds, $this->Willconnector->find('all',
						array('conditions'=>array('Willconnector.parent_will_id'=>$data['Will']['id'],
							'Willconnector.user_id <>'=>$user_data['User']['id']))));
				}
				 * */
			}
		}
		
		/*
		foreach( $finds as $find ){
			
			
		}
		 */

		// お勧めフォーム用
		$recommends = array();

		print_r($words);
		echo count($finds);

		echo "<br />検索結果-----------------------------------<br />\n";
		
		pr(print_r( $finds ));

		echo "<br />-----------------------------------<br />\n";
		
		// レコメンドされるべきものが一つ以上はある
		$recommend_datas = array();
		if(count($finds)){
			
			$recommend_datas = $finds[0]; 
			for( $i=0; $i < count($recommend_datas); $i++ ){
				$recommend_datas[$i]['Will']['count'] = 1;
			}
		
		}
		echo "<br />recommend_datas-----------------------------------<br />\n";

		pr(print_r( $recommend_datas ));



/*		
		for( $i=1; $i<count($recommend_datas); $i++){
			
			for( $)
			
		}
*/		
		
//		$recommend_datas[0]['Will']['count'] = 1;
//		pr(print_r($recommend_datas));
//		print_r( $recommend_datas );

		// TODO: 特徴語抽出アルゴリズムが必要
		
		$recommend_datas_addtemp = array();
		for( $i=1; $i < count($finds); $i++ ){
			
			$compare_datas = $finds[$i];
			echo "<br />finds[$i]-----------------------------------<br />\n";	
			pr(print_r( $finds[$i] ));
			
			/*
			if( count($recommend_datas) > count($compare_datas) ){
				
				foreach( $compare_datas as $compare_data ) {
					for( $j=0; $j < count($recommend_datas); $j++ ){
						if( $recommend_datas[$j]['Will']['id'] == $compare_data['Will']['id'] ){
							$recommend_datas[$j]['Will']['count']++;
						}else{
							$compare_data['Will']['count'] = 1;
							array_push($recommend_datas_addtemp, $compare_data );
						}						
					}
				}								
					
			}else{
				for( $j=0; $j < count($recommend_datas); $j++ ){
					foreach( $compare_datas as $compare_data ){						
						if( $recommend_datas[$j]['Will']['id'] == $compare_data['Will']['id'] ){
							$recommend_datas[$j]['Will']['count']++;						
						}else{
							$compare_data['Will']['count'] = 1;
							array_push($recommend_datas_addtemp, $compare_data );
							// echo "<br />recommend_datas_addtemp-----------------------------------<br />\n";	
							// pr(print_r( $recommend_datas_addtemp ));
							// echo "<br />pusshing title-----------------------------------<br />\n";	
							// pr(print_r( $compare_data['Will']['title'] ));
													}						
					}
				}				
			}
			 */
		}
		echo "<br />生成結果-----------------------------------<br />\n";
		pr(print_r( $recommend_datas_addtemp ));
		
		if( count($recommend_datas_addtemp)){
			$recommend_datas = array_merge($recommend_datas,$recommend_datas_addtemp);
		}
		
		function sort_compare($a, $b){
			
			if( $a['Will']['count'] > $b['Will']['count'] ){
				
				return 1;
			
			}else if( $a['Will']['count'] == $b['Will']['count'] ){
				
				return 0;
			
			}else{
				
				return -1;
			}
		}
		
		usort( $recommend_datas, "sort_compare");
		
		echo "<br />-----------------------------------<br />\n";
		
		pr(print_r( $recommend_datas ));
		
		foreach( $recommend_datas as $recommend_data ){
			
			$recommends[$recommend_data['Will']['id']] = $recommend_data['Will']['title'];
		}


		// ロジックに変更が必要
		/*
		foreach( $finds as $find ){
			
			foreach( $find as $data ){
				
				// すでに登録済みのものを除外処理
				if( !$this->Willconnector->find('count',
							array('conditions'=>array('Willconnector.user_id'=>$user_data['User']['id'],
								'Willconnector.will_id'=>$data['Will']['id'],
								'OR'=>array(
									array('Willconnector.parent_will_id'=>$will['Will']['id']),
									array('Willconnector.parent_will_id'=>NULL )
								)
							))								
				)){
									
					$recommends[$data['Will']['id']] = $data['Will']['title'] ;
					
				}
				
				// echo $data['Will']['id']."<br />";
				// print_r( $recommends );
			}
		}
		 */

		/*		
		$this->set('millstones', $this->Willconnector->find(
			array(
		            'Willconnector.user_id'=>$user_data['User']['id'],
		            'Willconnector.parent_will_id'=>$id,
		     )));
		 *
		 */
		$this->set('recommends', $recommends );
	}

	function add() {
		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );

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

	function beforeFilter()
	{
		// $this->Auth->allow('index', 'view');
	}
	
	function progress($id=null)	{ // $id は willconnector
	
		// echo "id:".$id."<br />";
		$this->set('back_url',$this->Session->read('Back_URL'));
		
		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );
		$willconnector = $this->Willconnector->read(null, $id);		
		$this->set('will', $willconnector);
		// pr($this->data);
		// echo "test";
		
		if( !empty($this->data) ){
			
	        $consumer = new OAuth_Consumer($this->Tweet->consumerKey, $this->Tweet->consumerSecret);		
	        $tweet = $consumer->post(
	            $user_data['User']['twitter1'],
	            $user_data['User']['twitter2'],
	            'http://twitter.com/statuses/update.xml',
	            array('status'=>$this->data['Tweet']['content'])
	        );

			$this->Tweet->create();
			$this->data['Tweet']['willconnector_id']=$id;
			// echo $id."<br />";
			// print_r($this->data);
			$this->Tweet->save($this->data);
			$this->data = $this->Willconnector->read(null, $id);
			
			$this->redirect(array('action'=>'progress',$id));	
		}

		if (empty($this->data)) {
			$this->data = $this->Willconnector->read(null, $id);
		}
		
		$this->set('tweets', $this->Tweet->find('all',
			array('conditions'=>array('Tweet.willconnector_id'=>$id), 'order'=>'Tweet.modified DESC')
		));
	
        // pr($tweet);	
	}

	function import()
	{
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

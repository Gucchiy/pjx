<?php
class ChiebukurosController extends AppController {

	var $name = 'Chiebukuros';
	var $uses = array('Chiebukuro','Will','User','Willconnector','Tweet');

	protected function get_words($query, &$verb=array(), &$adjective=array() ){
		
		$query = urlencode($query);
		$url = "http://jlp.yahooapis.jp/MAService/V1/parse?appid=" . $this->Chiebukuro->apiKey . "&sentence=" . $query;
		
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
		$this->Chiebukuro->recursive = 0;
		$this->set('chiebukuros', $this->paginate());
	}
	
	function get_data()
	{
		set_time_limit(0);
		
		$category_ids = array('2078297513','2078297937','2078297382','2078297854','2078297790',
			'2079526977','2078297878','2078297897','2078297811','2078297784','2078297812','2078297753',
			'2078297918','2078297616','2078297283','2078297353','2078297354');



		foreach( $category_ids as $category_id ){
			$url = "http://chiebukuro.yahooapis.jp/Chiebukuro/V1/getNewQuestionList?appid=" . $this->Chiebukuro->apiKey 
					."&condition=solved&results=20&category_id=".$category_id;
			
			$rss = file_get_contents($url);
			$xml = simplexml_load_string($rss);

			echo "<ul>";
			
			// $this->Chiebukuro->create();
			// $this->Chiebukuro->save(array('Chiebukuro'=>array('id'=>5555)));
			
				
	        foreach($xml->{'Result'} as $que) {
	        	
				$nouns = $this->get_words( substr( $que->{'Content'}, 0, 200 ), $verbs, $adjs );
				$noun = implode('|', $nouns);
				$verb = implode('|', $verbs);
	        	$adj = implode('|', $adjs);
				
				$this->Chiebukuro->create();
				if( $this->Chiebukuro->save(array("Chiebukuro"=>array(
					'id'=>"{$que->{'QuestionId'}}",
					'content'=>"{$que->{'Content'}}",
					'url'=>"{$que->{'QuestionUrl'}}",
					'words_noun'=>$noun,'words_verb'=>$verb,
					'words_adjective'=>$adj,
					'category_path'=>"{$que->{'CategoryPath'}}",
					'best_answer'=>"{$que->{'BestAnswer'}}"
					)))){
					
					$this->Session->setFlash(__('The chiebukuro datas have been saved', true));
					// $this->redirect(array('action' => 'index'));
					
				}
				  
				 
				
	            print("<li>{$que->{'Content'}}<br><a href=\"{$que->{'QuestionUrl'}}\">{$que->{'QuestionUrl'}}</a>\n");
				echo "<br />{$que->{'BestAnswer'}}<br />";
	        }

			echo "</ul>";
		}
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid chiebukuro', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$chiebukuro = $this->Chiebukuro->read(null, $id);
		$this->set('chiebukuro', $chiebukuro );

		if( !empty($this->data)){
			
			// $this->Will->create();
			if( $this->Will->save( $this->data ) ){
			
				/*
				$this->Willconnector->create();
				$this->Willconnector->save(
					array('Willconnector'=> array(
							'will_id'=>$this->Will->getID(),
							'parent_will_id'=> 0 )));	// 0 means static reecomend
				
				 * 
				 */
				 $this->Chiebukuro->save(array('Chiebukuro'=>array(
					'id'=>$chiebukuro['Chiebukuro']['id'],
					'will_id'=>$this->Will->getID(),
					'not_for_menu'=>$this->data['Chiebukuro']['not_for_menu']
				)));

				$this->Session->setFlash(__('The will has been saved', true));
				$this->redirect(array('action' => 'view/'.$id));
				
				// TODO: メニュー向きでなかった場合は削除処理が必要
			}

		}
		
		if( $chiebukuro['Chiebukuro']['will_id']==NULL ){
			
			$this->data['Willseed']['title'] = $chiebukuro['Chiebukuro']['best_answer'];
			$this->data['Willseed']['words_noun'] = $chiebukuro['Chiebukuro']['words_noun'];
			$this->data['Willseed']['words_verb'] = $chiebukuro['Chiebukuro']['words_verb'];
			$this->data['Willseed']['words_adjective'] = $chiebukuro['Chiebukuro']['words_adjective'];
		
		}else{
			
			$will = $this->Will->read(null,$chiebukuro['Chiebukuro']['will_id']);
			$this->data = $will;
		}
		$this->data['Chiebukuro']['not_for_menu'] = $chiebukuro['Chiebukuro']['not_for_menu'];
	}

	function add() {
		if (!empty($this->data)) {
			$this->Chiebukuro->create();
			if ($this->Chiebukuro->save($this->data)) {
				$this->Session->setFlash(__('The chiebukuro has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The chiebukuro could not be saved. Please, try again.', true));
			}
		}
		$wills = $this->Chiebukuro->Will->find('list');
		$this->set(compact('wills'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid chiebukuro', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Chiebukuro->save($this->data)) {
				$this->Session->setFlash(__('The chiebukuro has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The chiebukuro could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Chiebukuro->read(null, $id);
		}
		$wills = $this->Chiebukuro->Will->find('list');
		$this->set(compact('wills'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for chiebukuro', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Chiebukuro->delete($id)) {
			$this->Session->setFlash(__('Chiebukuro deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Chiebukuro was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
}

<?php
class TodosController extends AppController {

	var $name = 'Todos';
	var $components = array('Auth');
	var $uses = array('Todo','User','UsersTodo');

	function index() {
		$this->Todo->recursive = 0;
		
		// ユーザーデータの取得
		
		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );
		
		if (!empty($this->data)) {
			$this->Todo->create();

			// 中間テーブルにUserIDと関連付けて登録する
			$this->data['User']['User'] = $user_data['User']['id'];
			
			// TODO 中間テーブルとの紐付方法
			// $this->User->set('User' , $user_data['User']['id'] );
			
			if ($this->Todo->save($this->data)) {

				$this->Session->setFlash(__('The todo has been saved', true));
				$this->redirect(array('action' => 'index'));

			} else {
				$this->Session->setFlash(__('The todo could not be saved. Please, try again.', true));
			}
		}
		// TODO: pagination は後でやろう
		$this->UsersTodo->bindModel(array(
		    'belongsTo' => array('Todo' =>
		           array(
		               'className' => 'Todo',
		               'foreignKey' => 'todo_id',
		            )
		        ))
		    ,false
		);
		$options = array(
		      'fields' => array('Todo.id','Todo.title', 'Todo.created', 'Todo.modified'),
		      'conditions'=>array(
		            'UsersTodo.user_id'=>2,
		      ),
		      'limit' => 10
		   );
		$this->paginate = $options;
		$this->set('todos', $this->paginate('UsersTodo') );

		//		$this->set('todos', $this->paginate( array('user_id' => $user_data['User']['id']) ));
		//		$this->set('todos', $this->paginate());
		//		$this->set('todos', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid todo', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('todo', $this->Todo->read(null, $id));
	}

	function add() {

		// セッションからユーザーデータの取得
		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );

		if (!empty($this->data)) {
			$this->Todo->create();

			// 中間テーブルにUserIDと関連付けて登録する
			$this->data['User']['User'] = $user_data['User']['id'];
			
			// TODO 中間テーブルとの紐付方法
			// $this->User->set('User' , $user_data['User']['id'] );
			
			if ($this->Todo->save($this->data)) {
				$this->Session->setFlash(__('The todo has been saved', true));
				$this->redirect(array('action' => 'index'));

			} else {
				$this->Session->setFlash(__('The todo could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Todo->User->find('list');
		$this->set(compact('users'));
	}

	function edit($id = null) {

		// View に Login データを届ける
		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid todo', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Todo->save($this->data)) {
				$this->Session->setFlash(__('The todo has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The todo could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Todo->read(null, $id);
		}
		$users = $this->Todo->User->find('list');
		$this->set(compact('users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for todo', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Todo->delete($id)) {
			$this->Session->setFlash(__('Todo deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Todo was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}

	function beforeFilter()
	{
		// $this->Auth->allow('index', 'view');
	}
}

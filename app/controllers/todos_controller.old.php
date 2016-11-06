<?php
class TodosController extends AppController {

	var $name = 'Todos';
	var $components = array('Auth');

	function index() {
		$this->Todo->recursive = 0;
		
		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );
		$this->set('todos', $this->paginate( array('user_id' => $user_data['User']['id']) ));
		
		// todoの追加
		if (!empty($this->data)) {
			$this->Todo->create();
			if ($this->Todo->save($this->data)) {
				$this->Session->setFlash(__('The todo has been saved', true));
				// リダイレクトされているので、POSTのデータは失われています
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The todo could not be saved. Please, try again.', true));
			}
		}
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid todo', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('todo', $this->Todo->read(null, $id));
	}

	function add() {

		$user_data = $this->Auth->User();
		$this->set('user_data', $user_data['User'] );

		if (!empty($this->data)) {
			$this->Todo->create();
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

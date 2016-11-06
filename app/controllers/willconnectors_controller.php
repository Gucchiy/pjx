<?php
class WillconnectorsController extends AppController {

	var $name = 'Willconnectors';

	function index() {
		$this->Willconnector->recursive = 0;
		$this->set('willconnectors', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid willconnector', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('willconnector', $this->Willconnector->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Willconnector->create();
			if ($this->Willconnector->save($this->data)) {
				$this->Session->setFlash(__('The willconnector has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The willconnector could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Willconnector->User->find('list');
		$wills = $this->Willconnector->Will->find('list');
		$this->set(compact('users', 'wills'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid willconnector', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Willconnector->save($this->data)) {
				$this->Session->setFlash(__('The willconnector has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The willconnector could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Willconnector->read(null, $id);
		}
		$users = $this->Willconnector->User->find('list');
		$wills = $this->Willconnector->Will->find('list');
		$this->set(compact('users', 'wills'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for willconnector', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Willconnector->delete($id)) {
			$this->Session->setFlash(__('Willconnector deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Willconnector was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}

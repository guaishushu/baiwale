<?php
require dirname(__FILE__).'/../lib/adminAction.php';

class Action extends adminAction
{
	public function index() {
		$topics = _model('topic')->getList();
		$this->display('topic.html',array(
			'topics' => $topics,
		));
	}

	public function delete($id) {

	}
}
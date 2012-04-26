<?php
require dirname(__FILE__).'/../lib/adminAction.php';

class Action extends adminAction
{
	public function index() {
		$tags = _model('tag')->getList();
		$this->display('tag.html',array(
			'tags' => $tags,
		));
	}

	public function create() {

	}

	public function delete($id) {

	}

	public function edit($id) {

	}
}
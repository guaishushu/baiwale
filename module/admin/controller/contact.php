<?php
require dirname(__FILE__).'/../lib/adminAction.php';

class Action extends adminAction
{
	function index()
	{
	    $data = _model('config')->read(array('name'=>'contact'));
	    $this->display('contact.html',array('data'=>$data));
	}

	public function data() {
	    $index = _POST('contact', '');
	    if ( !empty($index) ) {
	        _model('config')->update(array('name' => 'contact'), array('data' => $index));
	    } else {
	        $this->msg("是数字啊 哥哥");
	    }
	    $this->redirect("admin/contact.html");
	}
}
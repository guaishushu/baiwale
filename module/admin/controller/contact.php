<?php
require dirname(__FILE__).'/../lib/adminAction.php';

class Action extends adminAction
{
	function index()
	{
	    $data = _model('config')->read(array('name'=>'contact'));
	    $this->display('contact.html',array('data'=>$data));
	}

	public function date() {
	    $index = _POST('about', 0);
	    if ( is_int($index) ) {
	        _model('config')->update(array('name' => 'about'), array('data' => $index));
	    } else {
	        $this->msg("是数字啊 哥哥");
	    }
	    $this->redirect("admin.html");
	}
}
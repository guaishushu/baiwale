<?php
require dirname(__FILE__).'/../lib/adminAction.php';

class Action extends adminAction
{
	function index()
	{
	    $data = _model('config')->read(array('name'=>'about'));
	    $this->display('about.html',array('data'=>$data));
	}

	public function data() {
	    $index = _POST('about', '');
	    if ( !empty($index) ) {
	        _model('config')->update(array('name' => 'about'), array('data' => $index));
	    } else {
	        $this->msg("是数字啊 哥哥");
	    }
	    $this->redirect("admin/about.html");
	}
}
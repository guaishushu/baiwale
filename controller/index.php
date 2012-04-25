<?php  
class Action extends SiteAction {
    public function index() {
    	$config = _model('config')->read(array('name'=>'首页专题'));
    	$info = _model('topic')->read(array('tid'=>$config['data']));
        $this->display("index.html",array(
        	'info' => $info, 
        ));
    }
}
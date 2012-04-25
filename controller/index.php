<?php  
class Action extends SiteAction {
    public function index() {
    	$config = _model('config')->read(array('name'=>'index'));
    	$info = _model('topic')->read(array('tid'=>$config['data']));
    	$tags = _model('tag')->getList();
        $this->display("index.html",array(
        	'info' => $info, 
        	'tags' => $tags,
        ));
    }
}
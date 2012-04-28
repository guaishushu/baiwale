<?php  
class Action extends SiteAction {
    public function index() {
    	$config = _model('config')->read(array('name'=>'about'));
        $this->display("about.html", array(
        	'active' => 'about',
        	'config' => $config,
        ));
    }
}
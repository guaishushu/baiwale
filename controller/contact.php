<?php  
class Action extends SiteAction {
    public function index() {
    	$config = _model('config')->read(array('name'=>'contact'));
        $this->display("contact.html", array(
        	'active' => 'contact',
        	'config' => $config,
        ));
    }
}
<?php  
class Action extends SiteAction {
    public function index() {
        $this->display("contact.html", array(
        	'active' => 'contact',
        ));
    }
}
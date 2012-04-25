<?php  
class Action extends SiteAction {
    public function index() {
        $this->display("about.html", array(
        	'active' => 'about',
        ));
    }
}
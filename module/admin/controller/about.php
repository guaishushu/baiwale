<?php
require dirname(__FILE__).'/../lib/adminAction.php';

class Action extends adminAction
{
	public function index() {
		$this->display('about.php');
	}
}
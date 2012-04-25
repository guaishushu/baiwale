<?php
require_once './Library/__init__.php';

Site::getInstance()->setComponent(require './config/config.php');

Database::$preFix = '';

Site::getInstance()->run(new WebApplication());

?>
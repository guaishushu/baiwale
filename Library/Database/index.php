<?php


require_once 'Database.php';

$db = new Database('mysql', 'localhost', 'root', 'root', 'atom');
print_r($db->getAll('show tables'));

?>
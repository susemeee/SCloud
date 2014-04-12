<?php
	define('BASEPATH', TRUE);
	require_once('db.php');
	//let's create a table!
	$db = new Database();
	echo $db->create_table();

?>
<?php
	define('BASEPATH', TRUE);
	require_once('db.php');
	//let's create a table!
	$db = new Database();
	if($db->create_table()){
        echo "Success";
    }else{
        echo "Failure";
    }

?>
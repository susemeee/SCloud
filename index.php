<?php $time = microtime(true); session_start(); ?>
<?php define('BASEPATH', "asdf"); ?>

<?php
	$e = "Importing core module failed. please check whether BASEPATH and _core/index.php/BASEPATH is correct.";

	if(isset($_REQUEST["logined"])){ 
		(@include_once($_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH.'/fragments/filelist.php')) or die($e); 
	}else{
		(@include_once($_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH.'/fragments/main.php')) or die($e);
	}
?>


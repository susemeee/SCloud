<?php
echo "starting consistency check<br>\n";
$o_path = $_SERVER['DOCUMENT_ROOT']."/storage/";

$dir_array = scandir($o_path);

foreach($dir_array as $dir){
	if($dir == "user_bg" || $dir == ".." || $dir == "." || $dir == ".DS_Store")
		continue;
	else{
		$newdir = $o_path.$dir;

		if(!is_dir($newdir)){
			echo $dir." is not a directory, will delete<br>\n";
			unlink($dir);
		}else{
			echo $dir." Directory working<br>\n";
			$fa = scandir($newdir);
			print_r($fa);
			////
		}
	}
}

echo "cleanup finished in ".((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"])*1000)."ms";
?>
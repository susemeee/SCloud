<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
define("MYSQL_USER_ID", "root");
define("MYSQL_USER_PASSWORD", "Qawsedrf1234");

class Database{

	function check_injection($ar){
		// foreach ($ar as $value) {
		// 	if(preg_match('/(and|null|where|limit)/i', $value))
		// 		return true;
		// }
	}

	function create_table(){
		$success = true;
		$cq_user = "CREATE TABLE IF NOT EXISTS user(
			uid varchar(40) NOT NULL PRIMARY KEY,
			id varchar(128) NOT NULL,
			pw varchar(128) NOT NULL,
			disk_capacity int NOT NULL);";
		$cq_fileindex = "CREATE TABLE IF NOT EXISTS files(
			fid varchar(40) NOT NULL PRIMARY KEY,
			name varchar(256) NOT NULL,
			size int NOT NULL,
			type varchar(20),
			ownerid varchar(40) NOT NULL,
			share_link varchar(6) UNIQUE,
			share_count int,
			folder varchar(40),
			last_pos_x tinyint NOT NULL,
			last_pos_y tinyint NOT NULL		
			);";
		//"ownerid" references uid of user table
		$cq_fkey = "";

		$c = $this->db_init();
		mysqli_query($c, $cq_user) or die(mysqli_error($c));
		mysqli_query($c, $cq_fileindex) or die(mysqli_error($c));
		// mysqli_query($c, $cq_fkey) or die(mysqli_error($c));

		return $success;
	}

	function create_db($c){
		mysqli_query($c, "CREATE DATABASE SCloud") or die(mysqli_error($c));;
		mysqli_select_db($c, "SCloud");
	}

	function db_init(){
		$c = mysqli_connect("localhost", MYSQL_USER_ID, MYSQL_USER_PASSWORD) or die("ERROR: ".mysqli_error($c));
		mysqli_set_charset($c, "utf-8");

		mysqli_select_db($c, "SCloud") or $this->create_db($c);
		return $c;
	}
}
?>
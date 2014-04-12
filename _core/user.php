<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('db.php');

class User extends Database{

	function add($id, $pass){

		$c = $this->db_init();
		$id = mysqli_real_escape_string($c, $id); $pass = mysqli_real_escape_string($c, $pass);
		
		//all user has the limit of 1024M first
		$q = "INSERT INTO user VALUES(md5('$id'), '$id', PASSWORD('$pass'), 1024);";
		$result = mysqli_query($c, $q);

		return $result;
	}

	function auth($id, $pass){

		$c = $this->db_init();
		$id = mysqli_real_escape_string($c, $id); $pass = mysqli_real_escape_string($c, $pass);
		
		$q = "SELECT * FROM user WHERE id='$id' AND pw=PASSWORD('$pass');";
		//echo $q;
		$result = mysqli_query($c, $q) or die(mysqli_error($c));
		
		if($result === false || mysqli_num_rows($result) < 1)
			return null;
		else{
			$res = mysqli_fetch_array($result);
			return $res['uid'];
		}
	}
}	

?>
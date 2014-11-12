<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('db.php');

class File extends Database{

//졸라 난잡하네 슈1발
	function render_html($r){
		$size = $r[2] > 1024*1024 ? round($r[2]/1000/1000, 2)."MB" : 
		($r[2] > 1024 ? ($r[2]/1000)."KB" : $r[2]." Bytes");

		$ea = explode('.', $r[1]);
		$ext = strtolower($ea[count($ea)-1]);
		
		$ext_path = $_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH.'/_res/icons/'.$ext.'.png';
		//we use default icon if we don't have icons of extension
		if(!file_exists($ext_path)){
			$ext = 'file';
		}
			
		$fstr = "<file id='$r[0]' type='$r[3]' style='left: $r[8]px; top: $r[9]px; background-image: url(_res/icons/$ext.png);'>";
		$fstr = $fstr."<div class='file-tooltip'>
		<div class='t-btn-wrap'>
			<div class='btn link'>Link</div>
			<div class='btn btn-danger del'>Del</div>
		</div>
		<div class='t-desc'>
			<span class='name'>$r[1]</span>
			<span class='ext'>$size</span>
		</div>
		</div></file>";
		return $fstr;	
	}

	function getAll($uid){

		$c = $this->db_init();
		$uid = mysqli_real_escape_string($c, $uid);
		$q = "SELECT * FROM files WHERE ownerid='$uid';";
		$result = mysqli_query($c, $q) or die(mysqli_error($c));
		
		while($row = $result->fetch_array()){
			$rows[] = $row;
		}

		if(isset($rows)){
			foreach($rows as $row){
			//printing result
				echo $this->render_html($row);
			}
		}
	}

	function file_open_stream($file){
		//$file is MySQL Object

		// /storage/f5d1278e8109edd94e1e4197e04873b9/bfcc01ed68ad2f5de56370b0609a6278
		$path = $_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH."/storage/".$file[4]."/".$file[0];
		$name = $file[1];
		$size = $file[2];
		$type = (strlen($file[3]) > 0) ? $file[3] : "application/octet-stream";

		if ($fd = fopen ($path, "r")) {
			$fsize = filesize($path);
			if($fsize != $size)
				trigger_error("filesize mismatch between database and actual file");
			else{
			//$path_parts = pathinfo($path);
				header("Content-type: $type"); 
				header("Content-Disposition: attachment; filename=\"$name\""); 
				header("Content-length: $fsize");
				header("Cache-control: private");
				while(!feof($fd)) {
					$buffer = fread($fd, 2048);
					echo $buffer;
				}
			}
		}
		fclose ($fd);
		exit;
	}

	function download($uid, $file_id){

		//we check if the owner has this file first
		$c = $this->db_init();
		$uid = mysqli_real_escape_string($c, $uid);
		$file_id = mysqli_real_escape_string($c, $file_id);
		
		$q = "SELECT * FROM files WHERE ownerid='$uid' AND fid='$file_id';";
		$result = mysqli_query($c, $q) or die(mysqli_error($c));
		
		if($result === false || mysqli_num_rows($result) < 1)
			die("Access Denied");
		else{
			$result = mysqli_fetch_row($result);
			$this->file_open_stream($result);
		}
	}

	function download_via_share($share_link){

		$c = $this->db_init();
		$share_link = mysqli_real_escape_string($c, $share_link);

		$q = "SELECT * FROM files WHERE share_link='$share_link';";
		$result = mysqli_query($c, $q) or die(mysqli_error($c));
		
		if($result === false || mysqli_num_rows($result) < 1)
			die("No such link present");
		//checking share count, maximum 5
		else {
			//result must be one
			$result = mysqli_fetch_row($result);
			if(intval($result[6]) > 5)
				die("File sharing limit exceeded");
			else{
				$q2 = "UPDATE files SET share_count = share_count + 1 WHERE share_link='$share_link'";
				mysqli_query($c, $q2);
				//getting uid
				$this->file_open_stream($result);
			}
		}
	}

	function store($uid, $file_array, $x, $y){

		//registering new file info into DB
		$c = $this->db_init();
		$uid = mysqli_real_escape_string($c, $uid);
		$x = mysqli_real_escape_string($c, $x);
		$y = mysqli_real_escape_string($c, $y);

		//multiple upload not supported yet
		//foreach ($file_array["file"]["error"] as $key => $error) {
		if ($file_array["file"]["error"] == UPLOAD_ERR_OK) {
			$tmp_name = $file_array["file"]["tmp_name"];
			$name = $file_array["file"]["name"];
			$size = $file_array["file"]["size"];
			$type = $file_array["file"]["type"];
			$new_fid = md5(rand());
			$path_dir = $_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH."/storage/".$uid."/";

			//create a directory if not exists
			if (!file_exists($path_dir)) {
   				mkdir($path_dir, 0777, true);
			}
			//upstreaming file data
			move_uploaded_file($tmp_name, $path_dir.$new_fid) or die("upload failed");

			$q = "INSERT INTO files VALUES('$new_fid', '$name', '$size', '$type', '$uid', NULL, NULL, NULL, 0, 0);";
			$result = mysqli_query($c, $q) or die(mysqli_error($c));

			//updating new file position
			$this->update_position($new_fid, $x, $y);

			return $this->render_html(array($new_fid, $name, $size, $type, 0,0,0,0,$x, $y));
		}
		//}
	}
	//updating Box2D-Calculated file position, should be called when file movement is detected
	function update_position($fid, $x, $y){

		$c = $this->db_init();
		$fid = mysqli_real_escape_string($c, $fid);
		$x = mysqli_real_escape_string($c, $x);
		$y = mysqli_real_escape_string($c, $y);

		$q = "UPDATE files SET last_pos_x='$x', last_pos_y='$y' WHERE fid='$fid';";
		$result = mysqli_query($c, $q) or die(mysqli_error($c));
	}

	function delete($uid, $fid){

		$c = $this->db_init();
		$fid = mysqli_real_escape_string($c, $fid);
		$uid = mysqli_real_escape_string($c, $uid);
		$q = "DELETE FROM files WHERE fid='$fid' AND ownerid='$uid';";
		$result = mysqli_query($c, $q) or die(mysqli_error($c));
	
		$path = $_SERVER['DOCUMENT_ROOT']."/storage/".$uid."/".$fid;
		if(is_readable($path)){
			unlink($path);
		}else{
			echo "something is wrong";
		}
	}
}

?>
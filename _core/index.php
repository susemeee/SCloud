<?php 
define('BASEPATH', "asdf");


require_once('./user.php');
require_once('./file.php');

if(isset($_REQUEST['action'])){

	$file = new File();
	$user = new User();
	$action = $_REQUEST['action'];
	
	//file-upload, file-download, makefile-link, logout
	if($action){
		//"download" function
		if($action == "download"){
			session_start();
			$user = $_SESSION['user'];
			$fid = $_GET['fid'];
			//Both the session and file id should be exist if you want to download the file
			if(!isset($user)){
				die("Access Denied");
			}else if(!isset($fid)){
				die("insufficient variables");
			}
			else{
				$file->download($user, $fid);
			}
		}
		else if($action == "logout"){
			session_start();
			$_SESSION['user'] = "";
			$_SESSION['bg'] = "";
			session_destroy();
			header('Location: ./');
		}
		else if($action == "updatepos"){
			$fid = $_GET['file']; $x = $_GET['x']; $y = $_GET['y'];
			$file->update_position($fid, $x, $y);
			die("position updated");
		}
		else if($action == "upload"){
			session_start();
			$user = $_SESSION['user'];
			
			if(!isset($user)){
				die("Access Denied");
			}else if(count($_FILES) > 0){
				$x = isset($_REQUEST['x']) ? $_REQUEST['x'] : 0;
				$y = isset($_REQUEST['y']) ? $_REQUEST['y'] : 0;

				// http_response_code(200);
				$file_html = $file->store($user, $_FILES, $x, $y);
				//이거떄문에 30분날림 ㅡ
				die($file_html);				
			}else{
				die("no file sent OR content-length exceeds the limit. \n Please review the server settings.");
			}
		}
		else if($action == "auth"){
			//proceeds when all variable is correctly set
			if(isset($_POST['id']) && isset($_POST['pw'])){

				if($user->check_injection($_REQUEST)){
					die("hacker");
				}

				$uid = $user->auth($_POST['id'], $_POST['pw']);
				if($uid){
					//auth success and making session
					session_start();
					$_SESSION['user'] = $uid;
					//only used to assign background image
					$_SESSION['bg'] = "url(./storage/user_bg/".$_POST['id'].".jpg";
					die("success");
				}else{
					//auth failed
					die("unknown_account");
				}
			}
			else 
				die("insufficient variables");
		}
		else if($action == "register"){
			//proceeds when all variable is correctly set
			if(isset($_POST['id']) && isset($_POST['pw']))
				$user->add($_POST['id'], $_POST['pw']);
			else 
				die("insufficient variables");
		}
		else if($action == "delete"){
			session_start();
			$user = $_SESSION['user'];
			$fid = $_GET['fid'];
			//Both the session and file id should be exist if you want to download the file
			if(!isset($user)){
				die("Access Denied");
			}else if(!isset($fid)){
				die("insufficient variables");
			}else{
				$file->delete($user, $fid);
				die("delete success");
			}
		}else if($action == "link"){
			
		}
		else{
			header('Location: /');
		}
	}
}else{
	//print_r($_REQUEST);
	header('Location: /');
}

header('Location: /');
?>
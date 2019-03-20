<?php
	include_once 'Authorization.php';
	function connectToDatabase(){
		return new PDO('mysql:host=localhost;dbname=authorization', 'root', '');
	}

	function login(){
		$user = isset($_POST['user'])?$_POST['user']:'';
		$pass = isset($_POST['password'])?$_POST['password']:'';
		$auth = getAuth(connectToDatabase());

		$try = $auth->login($user,$pass);
		if($try === true){
			setcookie("_session", $auth->getUserSession()['token'], time()+99*55);
			setcookie("user", $user, time()+99*55);

			echo json_encode([ 'token' => $auth->getUserSession()['token'], 'user' => $user, ]);
		} else {
			echo json_encode([ 'error' => $try['error'], ]);
		}
	}

	function logout(){	
		$auth = getAuth(connectToDatabase());
		$auth->disableToken($_COOKIE["_session"]);
		unset($_COOKIE["_session"]);
		unset($_COOKIE["user"]);
	}
	function getAuth($conn){
		$auth = new Authorization($conn);
		return $auth;
	}
	function verifyToken(){
		$auth = new Authorization(connectToDatabase());
		$response = $auth->verifyToken($_COOKIE["_session"]);
		if($response === true){
			echo '1';
		}else{
			echo false;
		}
	}

	$action = $_POST['action'];

	switch ($action) {
		case 'login':
			login();
			break;
		
		case 'logout':
			logout();
			break;
		case 'verifyToken':
			verifyToken();
			break;
		default:
			break;
	}
?>
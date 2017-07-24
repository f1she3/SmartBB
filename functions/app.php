<?php

function is_logged(){
	if(isset($_SESSION['name']) && !empty($_SESSION['name'])){
		return true;

	}else{
		return false;
	}
}
function is_user($input){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $input);
	mysqli_stmt_execute($query);
	$result = mysqli_stmt_fetch($query);
	if($result == 0){
		return false;
	
	}else{
		return true;
	}
}
function define_url_base($routing_mode){
	switch($routing_mode){
		case('DEFAULT'):
			$base = '/index.php?page=';
			break;
		case('DEFAULT_SHORT'):
			$base = '/?page=';
			break;
		case('ROUTER'):
			$base = '/';
			break;
		default:
			$base = '/index.php?page=';
			break;
	}
	
	define('BASE_URL', $base);
}
function redirect($location){
	if(is_numeric($location)){
		switch($location){
			case(0):
				$location = 'login';
				break;
			case(1):
				$location = 'home';
				break;
			case(2):
				$location = 'error404';
				break;
			case(3):
				$location = 'contacts';
				break;
			default:
				$page = 'error404';
				break;
		}
	}
	header('Location:'.$_SESSION['host'].constant('BASE_URL').$location);
}
function get_link(){
	$mysqli = mysqli_connect(constant('HOST'), constant('USER'), constant('PASSWORD'),
		constant('DB_NAME'));

	return $mysqli;
}
function secure($var){
	$mysqli = get_link();
	$var = htmlspecialchars($var);
	$var = stripslashes(mysqli_escape_string($mysqli, htmlspecialchars(trim($var))));
	
	return $var;
}

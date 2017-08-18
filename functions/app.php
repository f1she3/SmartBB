<?php

function define_base_url($routing_mode){
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
function get_base_url(){
	return constant('BASE_URL');
}
function get_root_url(){
	return '//'.$_SERVER['HTTP_HOST'];
}
function get_project_name(){
	return constant('PROJECT_NAME');
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
	header('Location:'.get_root_url().get_base_url().$location);
}
function get_link(){
	$mysqli = mysqli_connect(constant('HOST'), constant('USER'), constant('PASSWORD'),
		constant('DB_NAME'));

	return $mysqli;
}
function secure($var){
	$mysqli = get_link();
	$var = trim($var);
	$var = htmlspecialchars($var);
	$var = mysqli_real_escape_string($mysqli, $var);
	//$var = stripslashes($var);
	
	return $var;
}

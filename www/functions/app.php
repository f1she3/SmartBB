<?php
/* app.php
 *
 * Defines the functions used internally by the system
 */

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
function get_location($input){
	if(is_numeric($input)){
		switch($input){
			case(1):
				if(is_logged()){
					$location = 'home';
				}else{
					$location = 'login';
				}
				break;
			case(2):
				$location = 'error404';
				break;
			case(3):
				$location = 'contacts';
				break;
			default:
				$location = 'error404';
				break;
		}
	}else{
		$location = $input;
	}

	return $location;
}
function redirect($location){
	header('Location:'.get_root_url().get_base_url().get_location($location));
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
	$var = format_new_line($var);
	$var = stripslashes($var);
	
	return $var;
}
function get_auth_pages($is_logged){
	if(is_logged()){
		$auth_pages = scandir('pages/');
		if($key = array_search('login', $auth_pages) !== FALSE){
			unset($auth_pages[$key]);
		}
		if($key = array_search('register', $auth_pages) !== FALSE){
			unset($auth_pages[$key]);
		}
		if($key = array_search('forget', $auth_pages) !== FALSE){
			unset($auth_pages[$key]);
		}
	}else{
		$auth_pages = array(
			0 => 'login',
			1 => 'register',
			2 => 'welcome',
			3 => 'error404',
			4 => 'error403',
			5 => 'forget'
		);
	}

	return $auth_pages;
}

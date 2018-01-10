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
			// Home page
			case(1):
				if(is_logged()){
					$location = 'home';
				}else{
					if(in_array('home.php', get_auth_pages(is_logged()))){
						$location = 'home';
					}else{
						$location = 'login';
					}
				}
				break;
			// Error page
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
function is_forum_public($visibility){
	if($visibility === 'PUBLIC'){
		$result = true;
	}else if($visibility === 'PRIVATE'){
		$result = false;
	}else{
		$result = NULL;
	}

	return $result;
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
		if($key = array_search('login', $auth_pages) !== false){
			unset($auth_pages[$key]);
		}
		if($key = array_search('register', $auth_pages) !== false){
			unset($auth_pages[$key]);
		}
		if($key = array_search('forget', $auth_pages) !== false){
			unset($auth_pages[$key]);
		}
	}else{
		if(is_forum_public(constant('FORUM_VISIBILITY')) === true){
			$auth_pages = array(
				0 => 'login.php',
				1 => 'register.php',
				2 => 'welcome.php',
				3 => 'error404.php',
				4 => 'error403.php',
				5 => 'forget.php',
				6 => 'home.php'
			);
		}else if(is_forum_public(constant('FORUM_VISIBILITY')) === false){
			$auth_pages = array(
				0 => 'login.php',
				1 => 'register.php',
				2 => 'welcome.php',
				3 => 'error404.php',
				4 => 'error403.php',
				5 => 'forget.php',
			);
		}else{
			$title = get_project_name().' | erreur';
			$page = 'error';
			if(is_logged()){
				require 'content/header-2.php';

			}else{
				require 'content/header-1.php';
			}
			set_error('[System error]', 'wrench', 'Forum visibility not correctly defined, check the documentation again', '');
		}
	}

	return $auth_pages;
}

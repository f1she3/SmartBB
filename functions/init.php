<?php

/* ROUTING_MODE : 
 * DEFAULT : 		/index.php?page=page
 * DEFAULT_SHORT : 	/?page=page
 * ROUTER : 		/page (requires an additonal webserver configuration)
*/ 
define('ROUTING_MODE', 'DEFAULT_SHORT');
require 'app.php';
require 'actions.php';
require 'display.php';
// Defines a constant containing the
// base URL according to ROUTING_MODE
define_url_base(constant('ROUTING_MODE'));
session_start();
date_default_timezone_set('Europe/Paris');
// Root of the server
$_SESSION['host'] = '//'.$_SERVER['HTTP_HOST'];
define('HOST', 'p:127.0.0.1');
define('USER', 'project_usr');
define('PASSWORD', 'roka304@');
define('DB_NAME', 'project');
$mysqli = mysqli_connect(constant('HOST'), constant('USER'), constant('PASSWORD'), constant('DB_NAME'));
if(!mysqli_connect_errno()){
	if(!mysqli_set_charset($mysqli, 'utf8')){
		$title = $_SESSION['host'].' | erreur';
		if(is_logged()){
			require 'content/header-2.php';
		}else{
			require 'content/header-1.php';
		}
		set_error('Erreur', 'exclamation-sign', '/!\ Erreur lors du chargement de UTF-8 /!\\', '');
	}
}else{
	$title = $_SESSION['host'].' | erreur';
	if(is_logged()){
		require 'content/header-2.php';

	}else{
		require 'content/header-1.php';
	}
	set_error('Erreur', 'exclamation-sign', '/!\ Erreur lors de la connection à la base de données /!\\', '');
}

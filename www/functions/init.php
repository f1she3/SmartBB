<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-type: text/html; charset=utf-8');
require 'app.php';
require 'actions.php';
require 'display.php';
/*
 * ROUTING_MODE :
 * DEFAULT : 		/index.php?page=page
 * DEFAULT_SHORT : 	/?page=page
 * ROUTER : 		/page
 * ROUTER requires webserver configuration provided in
 * folder /conf
 *
 */
define('ROUTING_MODE', 'DEFAULT_SHORT');

// Define root url according to ROUTING_MODE
define_base_url(constant('ROUTING_MODE'));

// Define if the forum will be visible to non registered users
define('FORUM_VISIBILITY', 'PUBLICI');
session_start();
define('PROJECT_NAME', '[DEV]');
define('HOST', 'p:127.0.0.1');
define('USER', 'project_usr');
define('PASSWORD', 'roka304@');
define('DB_NAME', 'project');
$mysqli = mysqli_connect(constant('HOST'), constant('USER'), constant('PASSWORD'), constant('DB_NAME'));
if(!mysqli_connect_errno()){
	if(!mysqli_set_charset($mysqli, 'utf8')){
		$title = get_project_name().' | erreur';
		$page = 'error';
		if(is_logged()){
			require 'content/header-2.php';
		}else{
			require 'content/header-1.php';
		}
		set_error('Erreur', 'exclamation-sign', '/!\ Erreur du chargement de UTF-8 avec la base de donnée /!\\', '');
	}else{
		if(!mb_internal_encoding("UTF-8")){
			$title = get_project_name().' | erreur';
			$page = 'error';
			if(is_logged()){
				require 'content/header-2.php';
			}else{
				require 'content/header-1.php';
			}
			set_error('Erreur', 'exclamation-sign', '/!\ Erreur du chargement de UTF-8 /!\\', '');
		}else{
			if(!date_default_timezone_set('Europe/Paris')){
				$title = get_project_name().' | erreur';
				$page = 'error';
				if(is_logged()){
					require 'content/header-2.php';
				}else{
					require 'content/header-1.php';
				}
				set_error('Erreur', 'exclamation-sign', '/!\ Erreur du chargement du fuseau horaire /!\\', '');
			}
		}
	}
}else{
	$title = get_project_name().' | erreur';
	$page = 'error';
	if(is_logged()){
		require 'content/header-2.php';

	}else{
		require 'content/header-1.php';
	}
	set_error('Erreur', 'exclamation-sign', '/!\ Erreur lors de la connexion à la base de données /!\\', '');
}

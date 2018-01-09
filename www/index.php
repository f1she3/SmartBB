<?php

require 'functions/init.php';
// Access right check & redirecting
if(isset($_GET['page']) && !empty($_GET['page']) && is_string($_GET['page'])){
	$page = $_GET['page'] = secure($_GET['page']);
	// Remove the final / of an URL
	if(substr($page, 0, 1) == '/'){
		$page = substr($page, 1);
	} 
	$pages = scandir('pages/');
	// PHP or HTML file
	$file_type = false;	
	if(in_array($page.'.php', $pages)){
		$file_type = 'php';
		
	}else if(in_array($page.'.html', $pages)){
		$file_type = 'html';
	}	
	if($file_type){
		if(is_logged()){
			$ranks = get_rank_list();
			$my_rank = get_rank($_SESSION['name']);
			if($page === 'login' || $page === 'register' || $page === 'forget'){
				$page = 'error404';
				$file_type = 'php';
			}else if($page == 'admin' && $my_rank < $ranks['administrator']){
				$page = 'error404';
				$file_type = 'php';
			}
		}else if($page != 'login' && $page != 'register' && $page != 'welcome' &&
			$page != 'error404' && $page != 'error403' && $page != 'forget'){
			$page = 'error404';
			$file_type = 'php';
		}
	}else{
		$page = 'error404';
		$file_type = 'php';
	}
}else{
	redirect('welcome');
}
$user_ip = get_user_ip();
if(is_logged()){
	$is_banned = is_banned($_SESSION['name'], NULL);
	if($is_banned['result']){
		if($page != 'welcome'){
			$title = 'Erreur';
			require 'content/header-2.php';
			set_error('Erreur', 'ban-circle', 'Vous êtes banni de '.get_project_name(), 'welcome');
		}
	}
}else{
	$is_banned = is_banned(NULL, $user_ip);
	if($is_banned['result']){
		$bans = get_ban_duration_list();
		$duration = $bans[$is_banned['ban_level']][0];
		$come_back = $is_banned['ending'];
		$come_back = date_create($come_back);
		$come_back = date_format($come_back, 'd/m/Y à H\hi \e\t s\s');
		if($page != 'welcome'){
			$title = 'Erreur';
			require 'content/header-1.php';
			set_error('Erreur', 'ban-circle', 'Vous êtes banni de '.get_project_name().' : <abbr title="retour le '.$come_back.'">'.$duration, 'welcome');
		}
	}
}
// Enf of access right check
// Inclusion of several files, the order is important
if(is_logged()){
	$title = get_project_name().' @'.$_SESSION['name']; 
	require 'content/header-2.php';
}else{
	$title = get_project_name().' | '.$page;
	require 'content/header-1.php';
}
$pages = scandir('functions/');
if(in_array($page.'.func.php',$pages)){
	require 'functions/'.$page.'.func.php';
}
require 'pages/'.$page.'.'.$file_type;
require 'content/footer.html';

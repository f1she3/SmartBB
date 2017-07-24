<?php

require 'functions/init.php';
// Access right check & redirecting
if(isset($_GET['page']) && !empty($_GET['page']) && is_string($_GET['page'])){
	$page = $_GET['page'] = secure($_GET['page']);
	// Remove the / of an URL
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
			if($page == 'login' || $page == 'register'){
				$page = 'error404';
				$file_type = 'php';
			}else{
				if($page == 'admin' && get_rank($_SESSION['name']) < 3){
					$page = 'error404';
					$file_type = 'php';
				}else{
					if($page == 'moderator' && (get_rank($_SESSION['name'] != (1 | 2)))){
						$page = 'error404';
						$file_type = 'php';
					}
				}
			}
			
		}else{
			if($page != 'login' && $page != 'register' && $page != 'welcome' &&
				$page != 'error404' && $page != 'error403'){
				$page = 'error404';
				$file_type = 'php';
			}
		}

	}else{
		$page = 'error404';
		$file_type = 'php';
	}
	
}else if(is_logged()){
	redirect(1);
	
}else{
	redirect(0);
}
// Enf of access right check
// Inclusion of several files, the order is important
if(is_logged()){
	$title = 'Project @'.$_SESSION['name']; 
	require 'content/header-2.php';
}else{
	$title = 'Project | '.$page;
	require 'content/header-1.php';
}
$pages = scandir('functions/');
if(in_array($page.'.func.php',$pages)){
	require 'functions/'.$page.'.func.php';
}
require 'pages/'.$page.'.'.$file_type;
if($page == 'home'){
	require 'content/footer-1.html';
}else{
	require 'content/footer-1.html';
}/*
else{
	require 'content/footer-2.html';
	
}*/

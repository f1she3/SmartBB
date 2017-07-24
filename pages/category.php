<?php

if(isset($_GET['cat']) && !empty($_GET['cat']) && is_string($_GET['cat'])){
	if(isset($_GET['id']) && !empty($_GET['id']) && is_string($_GET['id'])){
		$id = $_GET['id'] = secure($_GET['id']);
	}else{
		$id = 1;
	}
	$category = $_GET['cat'] = secure($_GET['cat']);
	if(is_category($category)){
		display_category($category, $id);
	}else{
		set_error('Erreur 404', 'zoom-out', 'La catégorie que vous recherchez n\'éxiste pas', 'home');
	}
}else{
	redirect(1);
}

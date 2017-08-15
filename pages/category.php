<?php

if(isset($_GET['cat']) && !empty($_GET['cat']) && is_string($_GET['cat'])){
	if(isset($_GET['id']) && !empty($_GET['id']) && is_string($_GET['id'])){
		$id = $_GET['id'] = secure($_GET['id']);
	}else{
		$id = 1;
	}
	$category = $_GET['cat'] = secure($_GET['cat']);
	if(is_category($category)){
		$category_infos = get_category_infos($category);
		$my_rank = get_rank($_SESSION['name']);
		if($my_rank < $category_infos['access_restriction']){
			set_error('Erreur 404', 'zoom-out', 'La catégorie que vous recherchez n\'éxiste pas', 'home');
		}
		echo 	"<div class=\"page-header\">
				<h2 class=\"text-left\">".$category."</h2>
			</div>
			<ul class=\"breadcrumb\">
				<li><a href=\"".get_base_url()."home\">Accueil</a></li>
				<li><a href=\"".get_base_url()."category&cat=".$category."\">".$category."</a></li>
			</ul>";
		display_articles($category, $id);
	}else{
		set_error('Erreur 404', 'zoom-out', 'La catégorie que vous recherchez n\'éxiste pas', 'home');
	}
}else{
	redirect(1);
}

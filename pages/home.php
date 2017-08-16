<?php 

if(isset($_POST['new_category'])){
	$ranks = get_rank_list();
	if(check_rank($_SESSION['name'], $ranks['max'])){
		display_new_cat_form(false);
	}
}else if(isset($_POST['edit_category']) && !empty($_POST['edit_category']) && is_string($_POST['edit_category'])){
	$edit_category = $_POST['edit_category'] = secure($_POST['edit_category']);
	$category_infos = get_category_infos($edit_category);
	display_new_cat_form($edit_category);
	
}else if(isset($_POST['write_article']) && !empty($_POST['write_article']) && is_string($_POST['write_article'])){
	$parent_category = $_POST['write_article'] = secure($_POST['write_article']);
	if(is_category($parent_category)){
		display_article_writing_form($parent_category);
	}else{
		set_error('Erreur', 'zoom-out', 'Cette catégorie n\'éxiste pas', 'home');
	}
}else if(isset($_POST['submit_article'])){
	if(!isset($_POST['article_title']) || empty($_POST['article_title']) || !is_string($_POST['article_title']) || mb_strlen($_POST['article_title'] > 100)){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec le titre de l\'article', 'home');
	}
	$article_title = $_POST['article_title'] = secure($_POST['article_title']);
	if(!isset($_POST['article_category']) || empty($_POST['article_category']) || !is_string($_POST['article_category'])){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec la catégorie de l\'article', 'home');
	}
	$article_category = $_POST['article_category'] = secure($_POST['article_category']);
	if(!is_category($article_category)){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec la catégorie de l\'article', 'home');
	}
	$categories = get_category_list();
	$category_infos = get_category_infos($article_category);
	$my_rank = get_rank($_SESSION['name']);
	if($my_rank < $category_infos['post_restriction']){
		set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour poster ici', 'home');
	}
	if(!isset($_POST['article_content']) || empty($_POST['article_content']) || !is_string($_POST['article_content']) || mb_strlen($_POST['article_content'] > 1000)){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec le contenu de l\'article', 'home');
	}
		$article_content = $_POST['article_content'] = secure($_POST['article_content']);
		$article_id = post_article($_SESSION['name'], $article_category, $article_title, $article_content);
		redirect('article&id='.$article_id);
}else if(isset($_POST['submit_cat_creation']) || isset($_POST['submit_cat_edition'])){
	if(!isset($_POST['category_name']) || empty($_POST['category_name']) || !is_string($_POST['category_name'])){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec le nom de catégorie', 'home');
	}
	$ranks = get_rank_list();
	if(get_rank($_SESSION['name']) != $ranks['max']){
		set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour effectuer cette action', 'home');
	}
	$category_name = $_POST['category_name']	= secure($_POST['category_name']);
	if(!isset($_POST['access_restriction']) || !is_string($_POST['access_restriction'])){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec la limite d\'accès', 'home');
	}
	$access_restriction = $_POST['access_restriction'] = secure($_POST['access_restriction']);
	if(!is_rank($access_restriction)){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec la limite d\'accès', 'home');
	}
	if(!isset($_POST['post_restriction']) || !is_string($_POST['post_restriction'])){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec la limite de poste', 'home');
	}
	$post_restriction = $_POST['post_restriction'] = secure($_POST['post_restriction']);
	if(!is_rank($post_restriction)){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec la limite de poste', 'home');
	}
	if($post_restriction < $access_restriction){
		set_error('Erreur', 'exclamation-sign', 'Le rang des posteurs de la catégorie est trop faible', 'home');
	}
	if(!isset($_POST['owned_by']) || !is_string($_POST['owned_by'])){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec les propriétaires de la catégorie', 'home');
	}
	$owned_by = $_POST['owned_by'] = secure($_POST['owned_by']);
	if(!is_rank($owned_by)){
		set_error('Erreur', 'exclamation-sign', 'Erreur avec les propriétaires de la catégorie', 'home');
	}
	if($owned_by < $post_restriction){
		set_error('Erreur', 'exclamation-sign', 'Le rang des propriétaires de la catégorie est trop faible', 'home');
	}
	if(isset($_POST['submit_cat_creation'])){
		if(is_category($category_name)){
			set_error('Erreur', 'exclamation-sign', 'Cette catégorie éxiste déjà', 'home');
		}
		update_category(false, $category_name, $access_restriction, $post_restriction, $owned_by);
	}else if(isset($_POST['submit_cat_edition'])){
		if(!isset($_POST['old_cat_name']) || empty($_POST['old_cat_name']) || !is_string($_POST['old_cat_name'])){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec l\'ancien nom de catégorie', 'home');
		}
		$old_cat_name = $_POST['old_cat_name'] = secure($_POST['old_cat_name']);
		if(!is_category($old_cat_name)){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec l\'ancien nom de catégorie', 'home');
		}
		update_category($old_cat_name, $category_name, $access_restriction, $post_restriction, $owned_by);
	}
	display_home_page(); 
}else if(isset($_POST['delete_category']) && !empty($_POST['delete_category']) && is_string($_POST['delete_category'])){
	$delete_category = $_POST['delete_category'] = secure($_POST['delete_category']);
	$ranks = get_rank_list();
	if(check_rank($_SESSION['name'], $ranks['max'])){
		if(is_category($delete_category)){
			display_confirm_cat_del_form($delete_category);
		}
	}
}else if(isset($_POST['confirm_cat_del']) && !empty($_POST['confirm_cat_del']) && is_string($_POST['confirm_cat_del'])){
	$category = $_POST['confirm_cat_del'] = secure($_POST['confirm_cat_del']);
	if(is_category($category)){
		if(isset($_POST['action']) && !empty($_POST['action']) && is_string($_POST['action'])){
			$action = $_POST['action'] = secure($_POST['action']);
			if($action == 1){
				delete_category($category, false);
				display_home_page(); 
			}else{
				if(is_category($action)){
					delete_category($category, $action);
					display_home_page(); 
				}
			}
		}
	}
}else{
	display_home_page();
}

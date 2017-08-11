<?php 

if(isset($_POST['write_article']) && !empty($_POST['write_article']) && is_string($_POST['write_article'])){
	$parent_category = $_POST['write_article'] = secure($_POST['write_article']);
	if(is_category($parent_category)){
		display_article_writing_form($parent_category);
	}else{
		set_error('Erreur', 'zoom-out', 'Cette catégorie n\'éxiste pas', 'home');
	}
	
}else if(isset($_POST['create_category'])){
	if(isset($_POST['category_name']) && !empty($_POST['category_name']) && is_string($_POST['category_name'])){
		$ranks = get_rank_list();
		if(check_rank($_SESSION['name'], $ranks['max'])){
			$category_name = $_POST['category_name']	= secure($_POST['category_name']);
			if(!is_category($category_name)){
				create_category($category_name);
				display_home_page(); 
			}
		}
	}
}else if(isset($_POST['delete_category']) && !empty($_POST['delete_category']) && is_string($_POST['delete_category'])){
	$delete_category = $_POST['delete_category'] = secure($_POST['delete_category']);
	$ranks = get_rank_list();
	if(check_rank($_SESSION['name'], $ranks['max'])){
		if(is_category($delete_category)){
			display_confirm_cat_deletion_form($delete_category);
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

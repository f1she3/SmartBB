<?php 

if(isset($_POST['create_category'])){
	if(isset($_POST['category_name']) && !empty($_POST['category_name']) && is_string($_POST['category_name'])){
		$ranks = get_rank_list();
		if(check_rank($_SESSION['name'], $ranks['max'])){
			$category_name = $_POST['category_name']	= secure($_POST['category_name']);
			if(!is_category($category_name)){
				create_category($category_name);
			}
		}
	}
}else if(isset($_POST['delete_category']) && !empty($_POST['delete_category']) && is_string($_POST['delete_category'])){
	$delete_category = $_POST['delete_category'] = secure($_POST['delete_category']);
	$ranks = get_rank_list();
	if(check_rank($_SESSION['name'], $ranks['max'])){
		if(is_category($delete_category)){
			delete_category($delete_category);
		}
	}
}
display_home_page(); 

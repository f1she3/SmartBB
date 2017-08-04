<?php 

if(isset($_POST['create_category'])){
	if(isset($_POST['category_name']) && !empty($_POST['category_name']) && is_string($_POST['category_name'])){
		$category_name = $_POST['category_name']	= secure($_POST['category_name']);
		if(!is_category($category_name)){
			create_category($category_name);
		}
	}
}
display_home_page(); 

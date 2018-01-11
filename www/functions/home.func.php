<?php

function display_home_page(){
	echo 	"<div class=\"page-header\">
			<h2 class=\"text-center\">Accueil</h2>
		</div>";
	$ranks = get_rank_list();
	if(is_logged()){
		if(get_rank($_SESSION['name']) == $ranks['max']){
			echo "<form method=\"POST\">
					<button name=\"new_category\" class=\"btn btn-default\">
						<span class=\"glyphicon glyphicon-plus\"></span>
						 Catégorie
					</button>
				</form>";
		}
	}
	$mysqli = get_link();
	if(is_logged()){
		$my_rank = get_rank($_SESSION['name']);
	}else{
		$my_rank = $ranks['visitor'];
	}
	$query = mysqli_prepare($mysqli, 'SELECT * FROM categories WHERE access_restriction <= ? ORDER BY is_pinned DESC');
	mysqli_stmt_bind_param($query, 'i', $my_rank);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $result['id'], $result['name'], $result['access_restriction'], 
		$result['post_restriction'], $result['rank_owner'], $result['is_pinned']);
	$x = 0;
	while(mysqli_stmt_fetch($query)){
		$link = get_link();
		$req = mysqli_prepare($link, 'SELECT id FROM articles WHERE category = ?');
		mysqli_stmt_bind_param($req, 's', $result['name']);
		mysqli_stmt_execute($req);
		$i = 0;
		while(mysqli_stmt_fetch($req)){
			$i++;
		}
		if(is_logged()){
			echo 	"<form method=\"POST\">
					<h3>
						".$result['name']."
						<a href=\"".get_base_url()."category&cat=".$result['name']."\">(".$i.")</a>
					</h3>";
			if(get_rank($_SESSION['name']) >= $result['post_restriction']){
				echo "<button name=\"write_article\" class=\"btn btn-success btn-sm\" value=\"".$result['name']."\">
						<span class=\"glyphicon glyphicon-pencil\"></span>
						<span class=\"glyphicon glyphicon-plus\"></span>
					</button> ";
			}
			if(get_rank($_SESSION['name']) == $ranks['max']){
				echo 	"<button name=\"edit_category\" class=\"btn btn-primary btn-sm\" value=\"".$result['name']."\">
						<span class=\"glyphicon glyphicon-wrench\"></span>
					</button>
					<button name=\"delete_category\" class=\"btn btn-danger btn-sm\" value=\"".$result['name']."\">
						<span class=\"glyphicon glyphicon-trash\"></span>
					</button>";
			}
		}else{
			echo 	"<h3>
					".$result['name']."
					<a href=\"".get_base_url()."category&cat=".$result['name']."\">(".$i.")</a>
				</h3>";
			
		}
		echo 		"<hr>";
		display_articles($result['name'], false);
		echo 	"</form>";
		$x++;
	}
	if($x == 0){
		echo "<p class=\"text-center\">Aucune catégorie pour le moment</p>";
	}
}
function display_new_cat_form($category_name){
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">";
	if($category_name){
		$category_infos = get_category_infos($category_name);
		$button_name = 'submit_cat_edition';
		echo 		$category_name;
	}else{
		$category_infos['name'] = '';
		$category_infos['access_restriction'] = '';
		$category_infos['post_restriction'] = '';
		$category_infos['rank_owner'] = '';
		$category_infos['is_pinned'] = 0;
		$button_name = 'submit_cat_creation';
		echo 		"Nouvelle catégorie";
	}
	echo 		"</h3>
		</div>
		<form method=\"POST\" class=\"col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2\">
			<div class=\"form-group\">
				<label>Nom :</label>
				<input type=\"text\" name=\"category_name\" class=\"form-control\" placeholder=\"Nouvelle catégorie\" value=\"".$category_infos['name']."\" autofocus required>
			</div>
			<div class=\"form-group\">
				<label>Rang minimum d'accès :</label>
				<select name=\"access_restriction\" class=\"form-control\">";
	$ranks = get_rank_list();
	foreach($ranks as $key => $value){
		// Skip symbolic ranks
		// ex : $ranks['user']
		if(!is_numeric($value)){
			if($key == $category_infos['access_restriction']){
				$attribute = 'selected';
			}else{
				$attribute = '';
			}
			echo 			"<option value=\"".$key."\" ".$attribute.">".$value."</option>";
		}
	}
	echo 			"</select>
			</div>
			<div class=\"form-group\">
				<label>Rang minimum pour poster :</label>
				<select name=\"post_restriction\" class=\"form-control\">";
	$ranks = get_rank_list();
	foreach($ranks as $key => $value){
		if(!is_numeric($value) && $value !== $ranks[$ranks['visitor']]){
			if($key == $category_infos['post_restriction']){
				$attribute = 'selected';
			}else{
				$attribute = '';
			}
			echo 			"<option value=\"".$key."\" ".$attribute.">".$value."</option>";
		}
	}
	echo			"</select>
			</div>
			<div class=\"form-group\">
				<label>Gérée par :</label>
				<select name=\"owned_by\" class=\"form-control\">";
	foreach($ranks as $key => $value){
		if(!is_numeric($value) && $value !== $ranks[$ranks['visitor']]){
			if($key == $category_infos['rank_owner']){
				$attribute = 'selected';
			}else if($key == $ranks['moderator']){
				$attribute = 'selected';
			}else{
				$attribute = '';
			}
			echo 			"<option value=\"".$key."\" ".$attribute.">".$value."</option>";
		}
	}
	if($category_infos['is_pinned'] === 1){
		$attribute = 'checked';
	}else{
		$attribute = '';
	}
	echo 			"</select>
			</div>
			<div class=\"checkbox\">
				<label><input type=\"checkbox\" name=\"is_pinned\" ".$attribute.">Épingler</label>
			</div>
			<button name=\"".$button_name."\" class=\"btn btn-success col-sm-2 col-xs-3\">
				<span class=\"glyphicon glyphicon-plus\"></span>
				<span class=\"glyphicon glyphicon-folder-open\"></span>
			</button>
			<a class=\"btn btn-default col-sm-3 col-sm-offset-7 col-xs-6 col-xs-offset-3\" href=\"".get_base_url()."home\">
				<span class=\"glyphicon glyphicon-remove\"></span>
				Annuler
			</a>";
	if($category_name){
		echo	"<input name=\"old_cat_name\" type=\"hidden\" value=\"".$category_name."\">";
	}
	echo	"</form>";
	
}
function update_category($old_category_name, $category_name, $access_restriction, $post_restriction, $rank_owner, $is_pinned){
	$mysqli = get_link();
	if($old_category_name){
		$query = mysqli_prepare($mysqli, 'UPDATE categories SET name = ?, access_restriction = ?, post_restriction = ?, 
			rank_owner = ?, is_pinned = ? WHERE BINARY name = ?');
		mysqli_stmt_bind_param($query, 'siiiis', $category_name, $access_restriction, $post_restriction, $rank_owner, $is_pinned, $old_category_name);
		mysqli_stmt_execute($query);
		$mysqli = get_link();
		$query = mysqli_prepare($mysqli, 'UPDATE articles SET category = ? WHERE BINARY category = ?');
		mysqli_stmt_bind_param($query, 'ss', $category_name, $old_category_name);
		mysqli_stmt_execute($query);
	}else{
		$query = mysqli_prepare($mysqli, 'INSERT INTO categories (name, access_restriction, post_restriction, 
			rank_owner, is_pinned) VALUES (?, ?, ?, ?, ?)');
		mysqli_stmt_bind_param($query, 'siiii', $category_name, $access_restriction, $post_restriction, $rank_owner, $is_pinned);
		mysqli_stmt_execute($query);
	}
}
function display_confirm_cat_del_form($category_name){
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">Supprimer la catégorie \"".$category_name."\" ?</h3>
		</div>
			<form method=\"POST\" class=\"col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2\">
				<div class=\"form-group\">
					<label>Articles de cette catégorie :</label>
					<select name=\"action\" class=\"form-control\">
						<option value=\"1\" selected>Supprimer</option>";
	$categories = get_category_list();
	foreach($categories as $category){
		if($category['name'] != $category_name){
			echo 				"<option value=\"".$category['name']."\">Déplacer vers \"".$category['name']."\"</option>";
		}
	}
	echo				"</select>
				</div>
				<button name=\"confirm_cat_del\" class=\"btn btn-danger\" value=\"".$category_name."\">
					<span class=\"glyphicon glyphicon-trash\"></span>
					Supprimer
				</button>
				<a class=\"btn btn-default\" href=\"".get_base_url()."home\">
					<span class=\"glyphicon glyphicon-remove\"></span>
					Annuler
				</a>
			</form>";
}

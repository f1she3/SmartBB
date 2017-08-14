<?php

function display_home_page(){
	echo 	"<div class=\"page-header\">
			<h2 class=\"text-center\">Accueil</h2>
		</div>";
	$ranks = get_rank_list();
	if(check_rank($_SESSION['name'], $ranks['max'])){
		echo "<form method=\"POST\">
				<button name=\"new_category\" class=\"btn btn-default\">
					<span class=\"glyphicon glyphicon-plus\"></span>
					 Catégorie
				</button>
			</form>";
	}
	$mysqli = get_link();
	$my_rank = get_rank($_SESSION['name']);
	$query = mysqli_prepare($mysqli, 'SELECT * FROM categories WHERE access_restriction <= ?');
	mysqli_stmt_bind_param($query, 'i', $my_rank);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $result['id'], $result['name'], $result['access_restriction'], $result['post_restriction'], $result['rank_owner']);
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
		echo 	"<form method=\"POST\">
				<h3>
					".$result['name']."
					<a href=\"".get_base_url()."category&cat=".$result['name']."\">(".$i.")</a>
				</h3>";
		if(get_rank($_SESSION['name']) >= intval($result['post_restriction'])){
			echo "<button name=\"write_article\" class=\"btn btn-success btn-sm\" value=\"".$result['name']."\">
					<span class=\"glyphicon glyphicon-pencil\"></span>
					<span class=\"glyphicon glyphicon-plus\"></span>
				</button> ";
		}
		if(check_rank($_SESSION['name'], $ranks['max'])){
			echo 	"<button name=\"delete_category\" class=\"btn btn-danger btn-sm\" value=\"".$result['name']."\">
					<span class=\"glyphicon glyphicon-trash\"></span>
				</button>";
		}
	echo "<hr>";
	display_articles($result['name'], false);
	echo "</form>";
	$x++;
}
if($x == 0){
	echo "<p class=\"text-center\">Aucune catégorie pour le moment</p>";
	}
}
function display_new_cat_form(){
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">
				Nouvelle catégorie
			</h3>
		</div>
		<form method=\"POST\" class=\"col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2\">
			<div class=\"form-group\">
				<label>Nom :</label>
				<input type=\"text\" name=\"category_name\" class=\"form-control\" placeholder=\"Nouvelle catégorie\" autofocus required>
			</div>
			<div class=\"form-group\">
				<label>Rang minimum d'accès :</label>
				<select name=\"access_restriction\" class=\"form-control\">";
	$ranks = get_rank_list();
	foreach($ranks as $key => $value){
		if($value != $ranks['max']){
			echo 			"<option value=\"".$key."\">".$value."</option>";
		}
	}
	echo 			"</select>
			</div>
			<div class=\"form-group\">
				<label>Rang minimum pour poster :</label>
				<select name=\"post_restriction\" class=\"form-control\">";
	$ranks = get_rank_list();
	foreach($ranks as $key => $value){
		if($value != $ranks['max']){
			echo 			"<option value=\"".$key."\">".$value."</option>";
		}
	}
	echo			"</select>
			</div>
			<div class=\"form-group\">
				<label>Gérée par :</label>
				<select name=\"owned_by\" class=\"form-control\">";
	foreach($ranks as $key => $value){
		if($key > 0){
			if($value == $ranks[1]){
				$attribute = 'selected';
			}else{
				$attribute = '';
			}
			echo 			"<option value=\"".$key."\" ".$attribute.">".$value."</option>";
		}
	}
	echo 			"</select>
			</div>
			<button name=\"submit_cat_creation\" class=\"btn btn-success\">
				<span class=\"glyphicon glyphicon-plus\"></span>
				<span class=\"glyphicon glyphicon-folder-open\"></span>
			</button>
			<a class=\"btn btn-default\" href=\"".get_base_url()."home\">
				<span class=\"glyphicon glyphicon-remove\"></span>
				Annuler
			</a>
		</form>";
	
}
function create_category($category_name, $access_restriction, $post_restriction, $owned_by){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO categories (name, access_restriction, post_restriction, rank_owner) VALUES (?, ?, ?, ?)');
	mysqli_stmt_bind_param($query, 'siii', $category_name, $access_restriction, $post_restriction, $owned_by);
	mysqli_stmt_execute($query);
}
function display_confirm_cat_del_form($category_name){
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">Supprimer la catégorie \"".$category_name."\" ?</h3>
		</div>
			<form method=\"POST\" class=\"col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2\">
				<div class=\"form-group\">
					<label>Articles de cette catégorie : </label>
					<select name=\"action\" class=\"form-control\">
						<option value=\"1\" selected>Supprimer</option>";
	$categories = get_category_list();
	foreach($categories as $name){
		if($name != $category_name){
			echo 				"<option value=\"".$name."\">Déplacer vers \"".$name."\"</option>";
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

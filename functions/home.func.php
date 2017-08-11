<?php

function display_home_page(){
	echo 	"<div class=\"page-header\">
			<h2 class=\"text-center\">Accueil</h2>
		</div>";
	$ranks = get_rank_list();
	if(check_rank($_SESSION['name'], $ranks['max'])){
	echo 	"<form method=\"POST\" class=\"form-inline\">
			<div class=\"form-group\">
				<input type=\"text\" name=\"category_name\" class=\"form-control input-sm\" placeholder=\"Nouvelle catégorie\" autofocus required>
			</div>
			<button name=\"create_category\" class=\"btn btn-success btn-sm\">
				<span class=\"glyphicon glyphicon-plus\"></span>
				<span class=\"glyphicon glyphicon-folder-open\"></span>
			</button>
		</form>";
	}
	$mysqli = get_link();
	$query = mysqli_query($mysqli, 'SELECT * FROM categories');
	$x = 0;
	while($result = mysqli_fetch_assoc($query)){
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
				</h3>
				<button name=\"write_article\" class=\"btn btn-success\" value=\"".$result['name']."\">
					<span class=\"glyphicon glyphicon-pencil\"></span>
					<span class=\"glyphicon glyphicon-plus\"></span>
				</button> ";
		if(check_rank($_SESSION['name'], $ranks['max'])){
			echo 	"<button name=\"delete_category\" class=\"btn btn-danger\" value=\"".$result['name']."\">
					<span class=\"glyphicon glyphicon-trash\"></span>
				</button>";
		}
		echo "<hr>";
		display_articles($result['name'], false);
		$x++;
	}
	if($x == 0){
		echo "<p class=\"text-center\">Aucune catégorie pour le moment</p>";
	}
}
function create_category($category_name){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO categories (name) VALUES (?)');
	mysqli_stmt_bind_param($query, 's', $category_name);
	mysqli_stmt_execute($query);
}
function display_confirm_cat_deletion_form($category_name){
	echo 	"<div class=\"page-header\">
			<h3>Supprimer la catégorie \"".$category_name."\" ?</h3>
		</div>
			<form method=\"POST\">
				<div class=\"form-group\">
					<label>Articles de cette catégorie : </label>
					<select name=\"action\" class=\"form-control\">
						<option value=\"1\" selected>Supprimer</option>";
	$categories = get_category_list();
	foreach($categories as $name){
		echo 				"<option value=\"".$name."\">Déplacer vers \"".$name."\"</option>";
	}
	echo				"</select>
				</div>
				<button name=\"confirm_cat_del\" class=\"btn btn-danger\" value=\"".$category_name."\">
						<span class=\"glyphicon glyphicon-trash\"></span>
						Supprimer
				</button>
				<button class=\"btn btn-default\">
						<span class=\"glyphicon glyphicon-remove\"></span>
						Annuler
				</button>
			</form>";
}

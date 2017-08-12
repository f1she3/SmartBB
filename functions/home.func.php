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
		/*
	echo 	"<form method=\"POST\" class=\"col-sm-6\">
			<div class=\"form-group\">
				<input type=\"text\" name=\"category_name\" class=\"form-control input-sm\" placeholder=\"Nouvelle catégorie\" autofocus required>
			</div>
			<div class=\"form-group\">
				<label>limite de rang</label>
				<select name=\"rank_limit\" class=\"form-control input-sm\">
	 				<option>-- limite de rang --</option>";
	$ranks = get_rank_list();
	foreach($ranks as $rank){
		if($rank != $ranks['max']){
			echo 			"<option value=\"".$rank."\">".$rank."</option>";
		}
	}
	echo			"</select>
			</div>
			<div class=\"form-group\">
				<input type=\"text\" name=\"owned_by\" list=\"owner_list\" class=\"form-control btn-sm\" placeholder=\"Responsable\" required>
			</div>
			<button name=\"create_category\" class=\"btn btn-success btn-sm\">
				<span class=\"glyphicon glyphicon-plus\"></span>
				<span class=\"glyphicon glyphicon-folder-open\"></span>
			</button>
		</form>
		<datalist id=\"owner_list\">";
	foreach($ranks as $key => $rank){
		if($key > 0){
			echo "<option value=\"".$rank."\">";
		}
	}
	/*
	$ret_datalist_options = datalist_options($_SESSION['name'], false);
	foreach($ret_datalist_options as $owner){
		if(get_rank($owner) >= $ranks[1]){
			$owner = '"'.$owner;
			$owner = $owner.'"';
			$owner = htmlspecialchars($owner);
			echo "<option value=\"".$owner."\">";
		}
	}
	 
	echo	"</datalist>"; */
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
				<button name=\"write_article\" class=\"btn btn-success btn-sm\" value=\"".$result['name']."\">
					<span class=\"glyphicon glyphicon-pencil\"></span>
					<span class=\"glyphicon glyphicon-plus\"></span>
				</button> ";
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
				<label>Restriction de rang :</label>
				<select name=\"rank_restriction\" class=\"form-control\">";
	$ranks = get_rank_list();
	foreach($ranks as $rank){
		if($rank != $ranks['max']){
			echo 			"<option value=\"".$rank."\">".$rank."</option>";
		}
	}
	echo			"</select>
			</div>
			<div class=\"form-group\">
				<label>Gérée par :</label>
				<select name=\"owned_by\" class=\"form-control\">";
	foreach($ranks as $key => $rank){
		if($key > 0){
			if($rank == $ranks[1]){
				$attribute = 'selected';
			}else{
				$attribute = '';
			}
			echo 			"<option value=\"".$rank."\" ".$attribute.">".$rank."</option>";
		}
	}
	echo 			"</select>
			</div>
			<button name=\"submit_cat_creation\" class=\"btn btn-success col-sm-2 col-sm-offset-5 col-xs-2 col-xs-offset-5\">
				<span class=\"glyphicon glyphicon-plus\"></span>
				<span class=\"glyphicon glyphicon-folder-open\"></span>
			</button>
		</form>";
	
}
function create_category($category_name, $rank_restriction, $owned_by){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO categories (name, rank_restriction, owner) VALUES (?, ?, ?)');
	mysqli_stmt_bind_param($query, 'sss', $category_name, $rank_restriction, $owned_by);
	mysqli_stmt_execute($query);
}
function display_confirm_cat_del_form($category_name){
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

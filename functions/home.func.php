<?php

function display_articles($category){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id, author, title, content, date FROM articles WHERE category = ? ORDER BY id DESC LIMIT 12');
	mysqli_stmt_bind_param($query, 's', $category);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id, $author, $title, $content, $date);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$link = get_link();
		$req = mysqli_prepare($link, 'SELECT id FROM comments WHERE article_id = ?');
		mysqli_stmt_bind_param($req, 'i', $id);
		mysqli_stmt_execute($req);
		mysqli_stmt_bind_result($req, $reply_id);
		$x = 0;
		while(mysqli_stmt_fetch($req)){
			$x++;	
		}
		if($x > 1){
			$text = $x.' réponses';
		}else{
			$text = $x.' réponse';
		}
		echo			"<tr>
						<td><a href=\"".constant('BASE_URL')."profile&user=".$author."\">".$author."</a></td>
						<td><a href=\"".constant('BASE_URL')."article&id=".$id."\">".$title."</a></td>
						<td>".$text."</td>
						<td>".$date."</td>
					</tr>";
		$i++;
	}
	if($i == 0){
		echo "<p class=\"text-center\">Aucun article dans cette catégorie, soyez le premier à en poster un !</p>";
	}
}
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
		echo 	"<div class=\"col-sm-12\">
				<table class=\"table table-bordered\">
					<h3>".$result['name']." <a href=\"".constant('BASE_URL')."category&cat=".$result['name']."\">(".$i.")</a>
					</h3><hr>
						<tbody>";
		display_articles($result['name']);
		$x++;
	}
	if($x == 0){
		echo "<p class=\"text-center\">Aucune catégorie pour le moment</p>";
	}
	echo		"</tbody>
		</table>
	</div>";
}
function create_category($category_name){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO categories (name) VALUES (?)');
	mysqli_stmt_bind_param($query, 's', $category_name);
	mysqli_stmt_execute($query);
}

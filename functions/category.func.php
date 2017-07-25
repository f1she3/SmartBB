<?php

function is_category($input){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id FROM categories WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $input);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;	
	}
	if($i == 0){
		return false;
	}else{
		return true;
	}
}
function display_category($category, $page_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT COUNT(*) FROM articles WHERE BINARY category = ?');
	mysqli_stmt_bind_param($query, 's', $category);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $count);
	mysqli_stmt_fetch($query);
	$page_count = ceil($count / 20);
	$position = ($page_id - 1) * 20 + 1;
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE BINARY category = ? ORDER BY id DESC LIMIT ?, 20');
	mysqli_stmt_bind_param($query, 'si', $category, $position);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id, $category, $author, $title, $content, $date, $is_pinned);
	echo 	"<div class=\"page-header\">
			<h2 class=\"text-left\">".$category."</h2>
		</div>";
	echo 	"<ul class=\"breadcrumb\">
			<li><a href=\"".constant('BASE_URL')."home\">Accueil</a></li>
			<li><a href=\"".constant('BASE_URL')."category&cat=".$category."\">".$category."</a></li>
		</ul>";
	echo 	"<div class=\"col-sm-12\">
			<table class=\"table table-bordered\">
				<tbody>";
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$link = get_link();
		$req = mysqli_prepare($link, 'SELECT id FROM comments WHERE parent_id = ?');
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
		echo 'Aucun article dans cette catégorie, soyez le premier à en poster un !';
	}
	echo			"</tbody>
			</table>
		</div>
		<ul class=\"pagination pagination-sm\">";
	for($i = 1; $i <= $page_count; $i++){
		if($i == $page_id){
			echo "<li class=\"active\"><a href=\"".constant('BASE_URL')."category&cat=".$category."&id=".$i."\">".$i."</a></li>";
		}else{
			echo "<li><a href=\"".constant('BASE_URL')."category&cat=".$category."&id=".$i."\">".$i."</a></li>";
		}
		//echo "<a href=".constant('BASE_URL')."category&cat=".$category."&id=".$i.">".$i." </a>";
	}
	echo 	"</ul>";
}

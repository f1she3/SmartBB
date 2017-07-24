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
		$req = mysqli_prepare($link, 'SELECT id FROM replies WHERE article_id = ?');
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
						<td><a href=\"#\">".$author."</a></td>
						<td><a href=\"#\">".$title."</a></td>
						<td>".$text."</td>
						<td>".$date."</td>
					</tr>";
		$i++;
	}
	if($i == 0){
		echo 'Aucun article dans cette catégorie, soyez le premier à en poster un !';
	}
}
function display_home_page(){
	$mysqli = get_link();
	echo 	"<div class=\"page-header\">
			<h2 class=\"text-center\">Accueil</h2>
		</div>";
	$query = mysqli_query($mysqli, 'SELECT name, item_count FROM categories');
	while($result = mysqli_fetch_assoc($query)){
		echo 	"<div class=\"col-sm-12\">
				<table class=\"table table-bordered\">
					<h3>".$result['name']." <a href=\"".constant('BASE_URL')."category&cat=".$result['name']."\">(".$result['item_count'].")</a></h3><hr>
						<tbody>";
		display_articles($result['name']);
	}
	echo		"</tbody>
		</table>
	</div>";
}

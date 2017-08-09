<?php

function set_error($title, $icon, $content, $location){
	if($title){
		$title =	"<div class=\"page-header\">
					<h2 class=\"text-center\">".$title."</h2>
				</div>";
	}else{
		$title = '';
	}
	if($icon){
		if($icon == 'error'){
			$icon =	"<img src=\"../css/images/emojis/e_s.svg\" height=\"40\" width=\"40\" class=\"center-block\">
					<h4 class=\"text-center\">
						<span class=\"glyphicon glyphicon-".$icon."\"></span>
					</h4>";
		}else{
			$icon = 	"<h4 class=\"text-center\">
						<span class=\"glyphicon glyphicon-".$icon."\"></span>
					</h4>";
		}
	}else{
		$icon = '';
	}
	if($content){
		$content = 	"<h4 class=\"text-center\">".$content."</h4>";
	}else{
		$content = '';
	}
	if($location){
		$location = 	"<a href=\"".$_SESSION['host'].constant('BASE_URL').$location."\">
					<img src=\"".$_SESSION['host']."/css/images/home.svg\" height=\"75\" width=\"75\" class=\"center-block\">
				</a>";
	}else{
		$location = '';
	}
	die($title.$icon.$content.$location.file_get_contents('content/footer-1.html'));
}
function datalist_options($username, $rank_restriction){
	$mysqli = get_link();
	if($rank_restriction){
		$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name != ? AND rank < ?');
		mysqli_stmt_bind_param($query, 'ss', $username, $rank_restriction);

	}else{
		$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name != ?');
		mysqli_stmt_bind_param($query, 's', $username);
	}
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $r);
	$i = 0;
	$result = array();
	while(mysqli_stmt_fetch($query)){
		$result[$i] = "<option value=\"".$r."\">\n"; $i++;
	}
	
	return $result;
}
function bb_decode($text){
	$pattern_1 = '#https?://[a-zA-Z0-9-\.]+\.[a-zA-Z]{2,4}(/\S*)?#';
	$pattern_2 = '#https?://[0-9]{1,3}+(\.[0-9]{1,3}){3}#';
	$text = htmlspecialchars_decode($text);
	if(preg_match($pattern_1, $text)){
		$text = strtolower($text);
		$text = preg_replace($pattern_1, '<a href="$0" target="_blank">$0</a>', $text);
		$i = 0;
	}
	if(preg_match($pattern_2, $text)){
		$text = strtolower($text);
		$text = preg_replace($pattern_2, '<a href="$0" target="_blank">$0</a>', $text);
		$i = 1;
	}
	if(!isset($i)){
		$text = str_ireplace( ':/', '<img src="../css/images/emojis/e_p.svg" height="16" width="16">', $text);
	}
	$text = str_ireplace( ';)', '<img src="../css/images/emojis/e_o.svg" height="16" width="16">', $text);
	$text = str_ireplace( ':)', '<img src="../css/images/emojis/e_c.svg" height="16" width="16">', $text);
	$text = str_ireplace( ':(', '<img src="../css/images/emojis/e_s.svg" height="16" width="16">', $text);
	$text = str_ireplace( ':p', '<img src="../css/images/emojis/e_l.svg" height="16" width="16">', $text);
	$text = str_ireplace( ':\\', '<img src="../css/images/emojis/e_p.svg" height="16" width="16">', $text);
	$text = str_ireplace( ':D', '<img src="../css/images/emojis/e_b.svg" height="16" width="16">', $text);
	$text = str_ireplace( ':-D', '<img src="../css/images/emojis/e_x.svg" height="16" width="16">', $text);
	$text = str_ireplace( ':-)', '<img src="../css/images/emojis/e_x.svg" height="16" width="16">', $text);
	
	return $text;
}
function display_articles($category, $page_id){
	$mysqli = get_link();
	if($page_id){
		$query = mysqli_prepare($mysqli, 'SELECT COUNT(*) FROM articles WHERE BINARY category = ?');
		mysqli_stmt_bind_param($query, 's', $category);
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $count);
		mysqli_stmt_fetch($query);
		$mysqli = get_link();
		$per_page = 20;
		$page_count = ceil($count / $per_page);
		$position = ($page_id - 1) * $per_page;
		$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE BINARY category = ? ORDER BY is_pinned DESC, id DESC LIMIT ?, ?');
		mysqli_stmt_bind_param($query, 'sii', $category, $position, $per_page);
	}else{
		$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE BINARY category = ? ORDER BY is_pinned DESC, id DESC LIMIT 12');
		mysqli_stmt_bind_param($query, 's', $category);
	}
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id, $category, $author, $title, $content, $date, $is_pinned);
	echo	"<div class=\"col-sm-12\">
			<table class=\"table table-bordered\">
				<tbody>";
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
		if($is_pinned == 1){
			$tr_class = 'info';
		}else{
			$tr_class = '';
		}
		echo			"<tr class=\"".$tr_class."\">
						<td class=\"col-sm-6\"><span class=\"glyphicon glyphicon-envelope\"></span><a href=\"".constant('BASE_URL')."article&id=".$id."\"> ".$title."</a></td>
						<td><span class=\"glyphicon glyphicon-user\"></span><a href=\"".constant('BASE_URL')."profile&user=".$author."\"> ".$author."</a></td>
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
		</div>";
	if($page_id){
		if($page_count > 1){
			echo 	"<ul class=\"pagination pagination-sm\">";
			for($i = 1; $i <= $page_count; $i++){
				if($i == $page_id){
					echo "<li class=\"active\"><a href=\"".constant('BASE_URL')."category&cat=".$category."&id=".$i."\">".$i."</a></li>";
				}else{
					echo "<li><a href=\"".constant('BASE_URL')."category&cat=".$category."&id=".$i."\">".$i."</a></li>";
				}
			}
			echo 	"</ul>";
		}
	}
}

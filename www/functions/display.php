<?php
/* display.php
 *
 * Defines functions that will display content
 */

function set_error($title, $icon, $content, $location){
	if($title){
		$display_title =	"<div class=\"page-header\">
						<h2 class=\"text-center\">".$title."</h2>
					</div>";
	}else{
		$display_title = '';
	}
	if($icon){
		if($icon === 'error'){
			$display_icon =	"<span class=\"glyphicon glyphicon-arrow-left\"></span>
					<h4 class=\"text-center\">
						<span class=\"glyphicon glyphicon-".$icon."\"></span>
					</h4>";
		}else{
			$display_icon = "<h4 class=\"text-center\">
					<span class=\"glyphicon glyphicon-".$icon."\"></span>
				</h4>";
		}
	}else{
		$display_icon = '';
	}
	if($content){
		$display_content = 	"<h4 class=\"text-center\">".$content."</h4>";
	}else{
		$display_content = '';
	}
	if($location === 1){
		$display_location = 	"<h1 class=\"text-center\">
					<a href=\"".get_root_url().get_base_url().get_location(1)."\" class=\"text-danger\">
						<span class=\"glyphicon glyphicon-home\"></span>
					</a>
				</h1>";
	}else{
		$location = get_location($location);
		if($location){
			$display_location = 	"<h1 class=\"text-center\">
							<a href=\"".get_root_url().get_base_url().$location."\" class=\"text-danger\">
								<span class=\"glyphicon glyphicon-circle-arrow-left\"></span>
							</a>
						</h1>";
		}else{
			$display_location = '';
		}
	}	
	die($display_title.$display_icon.$display_content.$display_location.file_get_contents('content/footer.html'));
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
		$result[$i] = $r;
		$i++;
	}
	
	return $result;
}
function format_new_line($text){
	$text = str_ireplace( '\r\n', "\r\n", $text);

	return $text;
}
function bb_decode($text){
	$text = str_ireplace('[h1]', "<h3>", $text);
	$text = str_ireplace('[h1 center]', "<h3 class=\"text-center\">", $text);
	$text = str_ireplace('[/h1]', "</h3>", $text);
	$text = str_ireplace('[code]', "<kbd>", $text);
	$text = str_ireplace('[/code]', "</kbd>", $text);
	$text = str_ireplace('[p]', "<p style=\"white-space: pre-wrap\">", $text);
	$text = str_ireplace('[p center]', "<p class=\"text-center\" style=\"white-space: pre-wrap\">", $text);
	$text = str_ireplace('[p justify]', "<p class=\"text-justify\" style=\"white-space: pre-wrap\">", $text);
	$text = str_ireplace('[p left]', "<p class=\"text-left\" style=\"white-space: pre-wrap\">", $text);
	$text = str_ireplace('[p right]', "<p class=\"text-right\" style=\"white-space: pre-wrap\">", $text);
	$text = str_ireplace('[/p]', "</p>", $text);
	
	return $text;	
}
function format_text($text){
	$text = bb_decode($text);
	$pattern_1 = '#https?://[a-zA-Z0-9-\.]+\.[a-zA-Z]{2,4}(/\S*)?#';
	$pattern_2 = '#https?://[0-9]{1,3}+(\.[0-9]{1,3}){3}#';
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
	$text = str_ireplace(';)', '<img src="../css/images/emojis/e_o.svg" height="16" width="16">', $text);
	$text = str_ireplace(':)', '<img src="../css/images/emojis/e_c.svg" height="16" width="16">', $text);
	$text = str_ireplace(':(', '<img src="../css/images/emojis/e_s.svg" height="16" width="16">', $text);
	$text = str_ireplace(':p', '<img src="../css/images/emojis/e_l.svg" height="16" width="16">', $text);
	$text = str_ireplace(':\\', '<img src="../css/images/emojis/e_p.svg" height="16" width="16">', $text);
	$text = str_ireplace(':D', '<img src="../css/images/emojis/e_b.svg" height="16" width="16">', $text);
	$text = str_ireplace(':-D', '<img src="../css/images/emojis/e_x.svg" height="16" width="16">', $text);
	$text = str_ireplace(':-)', '<img src="../css/images/emojis/e_x.svg" height="16" width="16">', $text);
	
	return $text;
}
function display_articles($category, $page_id){
	$category_infos = get_category_infos($category);
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
		$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE BINARY category = ? ORDER BY is_pinned DESC, 
			status, id DESC LIMIT ?, ?');
		mysqli_stmt_bind_param($query, 'sii', $category, $position, $per_page);
	}else{
		$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE BINARY category = ? ORDER BY is_pinned DESC, 
			status, id DESC LIMIT 12');
		mysqli_stmt_bind_param($query, 's', $category);
	}
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id, $category, $author, $title, $content, $date, $is_pinned, $status);
	echo	"<div class=\"col-sm-12\">
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
		if($is_pinned == 1){
			$tr_class = 'info';
			$glyphicon = 'paperclip';
		}else{
			$tr_class = 'active';
			$glyphicon = 'envelope';
		}
		if($status === 1){
			$prefix = '<kbd>Fermé</kbd> ';
		}else{
			$prefix = '';
		}
		echo			"<tr class=\"".$tr_class."\">
						<td><span class=\"glyphicon glyphicon-user\"></span><a href=\"".get_base_url()."profile&user=".$author."\"> ".$author."</a></td>
						<td class=\"col-sm-6\"><span class=\"glyphicon glyphicon-".$glyphicon."\"></span> ".$prefix."<a href=\"".get_base_url()."article&id=".$id."\"> ".$title."</a></td>
						<td>".$text."</td>
						<td>".$date."</td>
					</tr>";
		$i++;
	}
	echo			"</tbody>
			</table>";
	if($i == 0){
		echo "<p class=\"text-center\">Aucun article dans cette catégorie, soyez le premier à en poster un !</p>";
	}
	echo 	"</div>";
	if($page_id){
		if($page_count > 1){
			echo 	"<ul class=\"pagination pagination-sm\">";
			for($i = 1; $i <= $page_count; $i++){
				if($i == $page_id){
					echo "<li class=\"active\"><a href=\"".get_base_url()."category&cat=".$category."&id=".$i."\">".$i."</a></li>";
				}else{
					echo "<li><a href=\"".get_base_url()."category&cat=".$category."&id=".$i."\">".$i."</a></li>";
				}
			}
			echo 	"</ul>";
		}
	}
}
function display_breadcrumb($category_name, $article_id, $opt_article_name){
	$article_infos = get_article_infos($article_id);
	echo 	"<ul class=\"breadcrumb\">
			<li><a href=\"".get_base_url().get_location(1)."\"><span class=\"label label-default\">Accueil</span></a></li>
			<li><a href=\"".get_base_url()."category&cat=".$category_name."\"><span class=\"label label-default\">".$category_name."</span></a></li>";
	if($article_id === false && $opt_article_name !== false){
		echo "<li>".$opt_article_name."</li>";
	}else{
		echo "<li><a href=\"".get_base_url()."article&id=".$article_id."\"><span class=\"label label-default\">".$article_infos['title']."</span></a></li>";
	}
	echo "</ul>";
}
function display_article_creation_form($category_name){
	$category_infos = get_category_infos($category_name);
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">Poster un article</h3>
		</div>";
	display_breadcrumb($category_name, false, 'Nouvel article');
	echo "<form method=\"POST\">
			<div class=\"form-group col-md-4 col-md-offset-1 col-sm-6 col-sm-offset-1 col-xs-6\">
				<label>Titre : </label>
				<input name=\"article_title\" class=\"form-control\" maxlength=\"100\" autofocus required>
			</div>
			<div class=\"form-group col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-1 col-xs-4 col-xs-offset-2\">
				<label>Catégorie : </label>
				<select name=\"article_category\" class=\"form-control\">";
	$categories = get_category_list();
	$my_rank = get_rank($_SESSION['name']);
	foreach($categories as $category){
		if($my_rank >= $category['post_restriction']){
			if($category['name'] == $category_name){
				$attribute = 'selected';
			}else{
				$attribute = '';
			}
			echo 			"<option value=\"".$category['name']."\" ".$attribute.">".$category['name']."</option>";
		}
	}
	echo			"</select>
			</div>
			<div class=\"form-group col-sm-10 col-sm-offset-1\">
				<textarea name=\"article_content\" class=\"form-control\" rows=\"10\" placeholder=\"[h1 center]Mon article[/h1]\" maxlength=\"1000\" required></textarea>
			</div>";
	if($my_rank >= $category_infos['rank_owner']){
		echo 	"<div class=\"checkbox col-sm-8 col-sm-offset-1\">
				<label><input type=\"checkbox\" name=\"pin\">Épingler</label>
			</div>";
	}
	echo 		"<button name=\"submit_article\" class=\"btn btn-primary col-sm-2 col-sm-offset-5 col-xs-4 col-xs-offset-4\">
				<span class=\"glyphicon glyphicon-pencil\"></span>
				Publier
			</button>
		</form>";
}

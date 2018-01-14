<?php

function get_user_description($user){
	$rank = get_rank($user);
	$ranks = get_rank_list();
	$infos['rank'] = $ranks[$rank];
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id FROM articles WHERE BINARY author = ?');
	mysqli_stmt_bind_param($query, 's', $user);
	mysqli_stmt_execute($query);
	$infos['article_count'] = 0;
	while(mysqli_stmt_fetch($query)){
		$infos['article_count']++;
	}
	if($infos['article_count'] > 1){
		$articles_txt = 'articles postés';
	}else{
		$articles_txt = 'article posté';
	}
	$result = $infos['rank']."\n".$articles_txt.' : '.$infos['article_count']."\n";

	return $result;
}
function is_article($input_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id FROM articles WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $input_id);
	mysqli_stmt_execute($query);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;
	}
	if($i > 0){
		return true;
	}else{
		return false;
	}
}
function get_article_infos($article_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $article_id);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $infos['id'], $infos['category'], $infos['author'], $infos['title'], $infos['content'], $infos['date'], $infos['is_pinned'], $infos['status']);
	mysqli_stmt_fetch($query);

	return $infos;
}
function get_comment_infos($comment_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM comments WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $comment_id);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $infos['id'], $infos['parent_id'], $infos['author'], $infos['content'], $infos['reply_to'], $infos['date']);
	mysqli_stmt_fetch($query);

	return $infos;
}
function is_comment($input_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id FROM comments WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $input_id);
	mysqli_stmt_execute($query);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;
	}
	if($i > 0){
		return true;
	}else{
		return false;
	}
}
function display_comment($comment_id, $article_id, $page_id){
	$mysqli = get_link();
	$ranks = get_rank_list();
	// Display specific comment
	if($comment_id){
		$comment_infos = get_comment_infos($comment_id);
		$fdate = date_create($comment_infos['date']);
		$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
		$user_description = get_user_description($comment_infos['author']);
		$content = format_text($comment_infos['content']);
		echo	"<h4 class=\"text-left\">
				<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
				<a href=\"".get_base_url()."profile&user=".$comment_infos['author']."\">
					<abbr title=\"".$user_description."\">".$comment_infos['author']."</abbr>
				</a>
				".$fdate."
			</h4>";
		if($comment_infos['reply_to'] == 0){
			echo 	"<pre>".$content."</pre>";
		}else if(is_comment($comment_infos['reply_to'])){
			$parent_comment = get_comment_infos($comment_infos['reply_to']);
			$parent_comment['content'] = bb_decode($parent_comment['content']);
			echo 	"<blockquote>
					".$parent_comment['content']."
					<footer>".$parent_comment['author']."</footer>
				</blockquote>
				<pre>".$content."</pre>";
		}
		echo 	"<form method=\"POST\">";
		if($comment_infos['author'] == $_SESSION['name'] && $my_rank >= $category_infos['post_restriction']){
			if($article_infos['status'] === 0){
				echo 	"<button name=\"edit_reply\" class=\"btn btn-primary\">
						<span class=\"glyphicon glyphicon-edit\"></span>
						éditer
					</button> ";
			}
		}else{
			echo 	"<a href=\"#\" class=\"btn btn-info\">
					<span class=\"glyphicon glyphicon-send\"> mp</span> 
				</a>";
		}
		echo 	"</form><hr>
			<form method=\"POST\">
				<input type=\"hidden\" name=\"parent_comment\" value=\"".$comment_id."\">
				<div class=\"form-group col-md-8 col-md-offset-2\">
					<textarea class=\"form-control\" style=\"resize:none\" name=\"answer\" maxlength=\"500\" placeholder=\"répondre à ".$comment_infos['author']."\" autofocus required></textarea>
				</div>
				<div class=\"form-group col-md-4 col-md-offset-4\">
					<button class=\"btn btn-primary col-sm-2 col-xs-2 col-xs-offset-5\" name=\"submit_answer\">
						<span class=\"glyphicon glyphicon-send\"></span> 
					</button>
				</div>
			</form>";
	// Display all comments
	}else{
		$query = mysqli_prepare($mysqli, 'SELECT COUNT(*) FROM comments WHERE parent_id = ?');
		mysqli_stmt_bind_param($query, 'i', $article_id);
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $count);
		mysqli_stmt_fetch($query);
		$per_page = 8;
		$page_count = ceil($count / $per_page);
		$position = ($page_id - 1) * $per_page;
		$mysqli = get_link();
		$query = mysqli_prepare($mysqli, 'SELECT * FROM  comments WHERE parent_id = ? LIMIT ?, ?');
		mysqli_stmt_bind_param($query, 'iii', $article_id, $position, $per_page);
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $id, $parent_id, $author, $content, $reply_to, $date);
		if($page_id > 1){
			$article_infos = get_article_infos($article_id);
			echo 	"<div class=\"page-header\">
					<h2 class=\"text-center\">".$article_infos['title']."</h2>
				</div>
				<ul class=\"breadcrumb\">
					<li><a href=\"".get_base_url().get_location(1)."\">Accueil</a></li>
					<li><a href=\"".get_base_url()."category&cat=".$article_infos['category']."\">".$article_infos['category']."</a></li>
					<li><a href=\"".get_base_url()."article&id=".$article_id."\">".$article_infos['title']."</a></li>
					<li>".$page_id."</li>
				</ul>";
		}
		while(mysqli_stmt_fetch($query)){
			$fdate = date_create($date);
			$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
			$user_description = get_user_description($author);
			$content = format_text($content);
			echo	"<h4 class=\"text-left\">
					<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
					<a href=\"".get_base_url()."profile&user=".$author."\">
						<abbr title=\"".$user_description."\">".$author."</abbr>
					</a>
					".$fdate."
				</h4>";
			if($reply_to == 0){
				echo 	"<pre>".$content."</pre>";
			}else if(is_comment($reply_to)){
				$parent_comment = get_comment_infos($reply_to);
				$parent_comment['content'] = format_text($parent_comment['content']);
				echo 	"<blockquote>
						".$parent_comment['content']."
						<footer>".$parent_comment['author']."</footer>
					</blockquote>
					<pre>".$content."</pre>";
			}
			echo 	"<form method=\"POST\">";
			$article_infos = get_article_infos($article_id);
			if(is_logged()){
				$my_rank = get_rank($_SESSION['name']);
			}else{
				$my_rank = $ranks['visitor'];
			}
			$category_infos = get_category_infos($article_infos['category']);
			$comment_infos = get_comment_infos($id);
			if(is_logged()){
				if($comment_infos['author'] != $_SESSION['name']){
					if($article_infos['status'] === 0){
						if($my_rank >= $category_infos['post_restriction']){
							echo 	"<button name=\"reply\" class=\"btn btn-primary\" value=\"".$id."\">
									<span class=\"glyphicon glyphicon-share\"></span> 
									répondre
								</button> ";
						}
					}
				}else if($comment_infos['author'] == $_SESSION['name'] && $my_rank >= $category_infos['post_restriction']){
					if($article_infos['status'] === 0){
						echo 		"<button name=\"edit_comment\" class=\"btn btn-primary\" value=\"".$id."\">
									<span class=\"glyphicon glyphicon-edit\"></span>
									éditer
								</button> ";
					}
				}
				if($comment_infos['author'] != $_SESSION['name']){
					echo		"<a href=\"#\" class=\"btn btn-info\">
								<span class=\"glyphicon glyphicon-send\"> mp</span> 
							</a>";
				}
			}
			echo	"</form><hr>";
		}
		if($page_count > 1){
			echo "<ul class=\"pagination\">";
			for($i = 1; $i <= $page_count; $i++){
				if($i == $page_id){
					echo "<li class=\"active\"><a href=\"".get_base_url()."article&id=".$article_id."&pid=".$i."\">".$i."</a></li>";
				}else{
					echo "<li><a href=\"".get_base_url()."article&id=".$article_id."&pid=".$i."\">".$i."</a></li>";
				}
			}
			echo 	"</ul>";
		}
		$article_infos = get_article_infos($article_id);
		if(is_logged()){
			$my_rank = get_rank($_SESSION['name']);
			$category_infos = get_category_infos($article_infos['category']);
			if($article_infos['status'] === 0){
				if($my_rank >= $category_infos['post_restriction']){
					echo 	"<form method=\"POST\">
							<div class=\"form-group col-md-8 col-md-offset-2\">
								<textarea class=\"form-control\" style=\"resize:none\" name=\"comment\" 
									placeholder=\"Commenter cet article\" maxlength=\"500\" required></textarea>
							</div>
							<div class=\"form-group col-md-4 col-md-offset-4\">
								<button class=\"btn btn-primary col-sm-2 col-xs-2 col-xs-offset-5\" name=\"submit_comment\">
									<span class=\"glyphicon glyphicon-send\"></span> 
								</button>
							</div>
						</form>";
				}
			}
		}else{
			$my_rank = $ranks['visitor'];
		}
	}
}
function display_comment_edition_form($comment_id){
	$comment_infos = get_comment_infos($comment_id);
	$fdate = date_create($comment_infos['date']);
	$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
	$user_description = get_user_description($comment_infos['author']);
	$comment_infos['content'] = format_text($comment_infos['content']);
	display_article($comment_infos['parent_id'], false);
	echo	"<div class=\"col-sm-8 col-sm-offset-2\">
			<h4 class=\"text-left\">
				<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
				<a href=\"".get_base_url()."profile&user=".$comment_infos['author']."\">
					<abbr title=\"".$user_description."\">".$comment_infos['author']."</abbr>
				</a>
				".$fdate."
			</h4>
			<form method=\"POST\">
				<div class=\"form-group\">
					<textarea name=\"new_content\" class=\"form-control\" autofocus required>".$comment_infos['content']."</textarea>
				</div>
				<button name=\"submit_comment_edition\" class=\"btn btn-primary col-sm-3 
					col-sm-offset-4 col-xs-3 col-xs-offset-4\" value=\"".$comment_id."\">
					<span class=\"glyphicon glyphicon-pencil\"></span>
					Publier	
				</button>
			</form>
		</div>";
}
function display_article($article_id, $page_id){
	$mysqli = get_link();
	$article_infos = get_article_infos($article_id);
	$category_infos = get_category_infos($article_infos['category']);
	$fdate = date_create($article_infos['date']);
	$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
	$user_description = get_user_description($article_infos['author']);
	$article_infos['content'] = format_text($article_infos['content']);
	if($article_infos['status'] === 1){
		$prefix = "<kbd>Fermé</kbd>";
	}else{
		$prefix = '';
	}
	echo 	"<div class=\"page-header\">
			<h2 class=\"text-center\">".$prefix." ".$article_infos['title']."</h2>
		</div>
		<ul class=\"breadcrumb\">
			<li><a href=\"".get_base_url().get_location(1)."\">Accueil</a></li>
			<li><a href=\"".get_base_url()."category&cat=".$article_infos['category']."\">".$article_infos['category']."</a></li>
			<li><a href=\"".get_base_url()."article&id=".$article_infos['id']."\">".$article_infos['title']."</a></li>
		</ul>
		<h4 class=\"text-left\">
			<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
			<a href=\"".get_base_url()."profile&user=".$article_infos['author']."\">
				<abbr title=\"".$user_description."\">".$article_infos['author']."</abbr>
			</a>
			".$fdate."
		</h4>
		<pre>
			<p style=\"white-space: pre-wrap\">".$article_infos['content']."</p>
		</pre>
		<form method=\"POST\">";
	$author_rank = get_rank($article_infos['author']);
	$ranks = get_rank_list();
	if(is_logged()){
		$my_rank = get_rank($_SESSION['name']);
		if($my_rank == $ranks['max'] || $my_rank >= $category_infos['rank_owner'] && $my_rank > $author_rank){
			if($article_infos['status'] === 0){
				echo 	"<button name=\"close_article\" class=\"btn btn-danger pull-right\">
						<span class=\"glyphicon glyphicon-lock\"> fermer</span> 
					</button>";
			}else{
				echo 	"<button name=\"open_article\" class=\"btn btn-success\">
						<span class=\"glyphicon glyphicon-ok\"> ouvrir</span> 
					</button>
					<button name=\"delete_article\" class=\"btn btn-danger pull-right\">
						<span class=\"glyphicon glyphicon-trash\">
					</button> ";
			}
		}
		if($article_infos['author'] == $_SESSION['name']){
			if($article_infos['status'] === 0 && $my_rank >= $category_infos['post_restriction']){
				echo 	"<button name=\"edit_article\" class=\"btn btn-primary\">
						<span class=\"glyphicon glyphicon-edit\"></span>
						éditer
					</button> ";
			}
		}else{
			echo 		"<a href=\"#\" class=\"btn btn-info\">
						<span class=\"glyphicon glyphicon-send\"> mp</span> 
					</a>";
		}
	}else{
		$my_rank = $ranks['visitor'];
	}
	echo	"</form><hr>";
	if($page_id){
		display_comment(false, $article_id, $page_id);
	}
}
function post_comment($parent_id, $author, $content, $reply_to){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO comments (parent_id, author, content, reply_to, date) VALUES (?, ?, ?, ?, NOW())');
	mysqli_stmt_bind_param($query, 'isss', $parent_id, $author, $content, $reply_to);
	mysqli_stmt_execute($query);
}
function display_reply_form($article_id, $comment_id){
	display_article($article_id, false);
	display_comment($comment_id, false, false);
}
// Status : 
// 0 : open
// 1 : close
function set_article_status($article_id, $status){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'UPDATE articles SET status = ? WHERE id = ?');
	mysqli_stmt_bind_param($query, 'ii', $status, $article_id);
	mysqli_stmt_execute($query);
}
function display_article_edition_form($article_id, $editor){
	$article_infos = get_article_infos($article_id);
	$category_infos = get_category_infos($article_infos['category']);
	$my_rank = get_rank($editor);
	if($article_infos['author'] != $editor){
		$title_attribute = 'disabled';
		$textarea_attribute = 'disabled';
		$button_name = 'submit_move_article';
	}else{
		$title_attribute = 'autofocus required';
		$textarea_attribute = 'required';
		$button_name = 'submit_article_edition';
	}
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">Éditer mon article</h3>
		</div>
		<ul class=\"breadcrumb\">
			<li><a href=\"".get_base_url().get_location(1)."\">Accueil</a></li>
			<li><a href=\"".get_base_url()."category&cat=".$article_infos['category']."\">".$article_infos['category']."</a></li>
			<li><a href=\"".get_base_url()."article&id=".$article_id."\">".$article_infos['title']."</a></li>
		</ul>
		<form method=\"POST\">
			<div class=\"form-group col-md-4 col-md-offset-1 col-sm-6 col-sm-offset-1 col-xs-6\">
				<label>Titre : </label>
				<input name=\"new_article_title\" class=\"form-control\" maxlength=\"100\" 
					value=\"".$article_infos['title']."\" ".$title_attribute.">
			</div>";
	if($article_infos['author'] != $editor){
		echo 	"<div class=\"form-group col-md-2 col-md-offset-4 col-sm-3 col-sm-offset-1 col-xs-5 col-xs-offset-1\">
				<label>Déplacer :</label>
				<select name=\"move_article\" class=\"form-control\">";
		$categories = get_category_list();
		$i = 0;
		foreach($categories as $category){
			if($category['name'] != $article_infos['category']){
				if(get_rank($editor) >= $category['rank_owner']){
					$i++;
					echo 	"<option value=\"".$category['name']."\">\"".$category['name']."\"</option>";
				}
			}
		}
		if($i == 0){
			echo 	"<option value=\"0\">-- Impossible --</option>";
		}
		echo		"</select>
			</div>";
	}
		$article_infos['content'] = format_new_line($article_infos['content']);
		echo	"<div class=\"form-group col-sm-10 col-sm-offset-1\">
				<textarea name=\"new_article_content\" class=\"form-control\" rows=\"10\" placeholder=\"[h1 center]Mon article[/h1]\" 
					maxlength=\"1000\" ".$textarea_attribute.">".$article_infos['content']."</textarea>
			</div>";
		if($my_rank >= $category_infos['rank_owner']){
			if($article_infos['is_pinned'] === 1){
				$checkbox_attribute = 'checked';
			}else{
				$checkbox_attribute = '';
			}
			echo	"<div class=\"checkbox col-sm-8 col-sm-offset-1\">
					<label><input type=\"checkbox\" name=\"pin\" ".$checkbox_attribute."> Épingler</label>
				</div>";
		}		
		echo "<button name=\"".$button_name."\" class=\"btn btn-primary col-sm-2 col-sm-offset-5 col-xs-4 col-xs-offset-4\">
				<span class=\"glyphicon glyphicon-pencil\"></span>
				Publier
			</button>
		</form>";

}
function update_article($article_id, $new_title, $new_content, $new_category, $is_pinned){
	if($new_title){
		$mysqli = get_link();
		$query = mysqli_prepare($mysqli, 'UPDATE articles SET title = ? WHERE id = ?');
		mysqli_stmt_bind_param($query, 'si', $new_title, $article_id);
		mysqli_stmt_execute($query);
		$mysqli = get_link();
	}
	if($new_content){
		$mysqli = get_link();
		$query = mysqli_prepare($mysqli, 'UPDATE articles SET content = ? WHERE id = ?');
		mysqli_stmt_bind_param($query, 'si', $new_content, $article_id);
		mysqli_stmt_execute($query);
	}
	if($new_category){
		$mysqli = get_link();
		$query = mysqli_prepare($mysqli, 'UPDATE articles SET category = ? WHERE id = ?');
		mysqli_stmt_bind_param($query, 'si', $new_category, $article_id);
		mysqli_stmt_execute($query);
	}
	if(!is_null($is_pinned)){
		$mysqli = get_link();
		$query = mysqli_prepare($mysqli, 'UPDATE articles SET is_pinned = ? WHERE id = ?');
		mysqli_stmt_bind_param($query, 'ii', $is_pinned, $article_id);
		mysqli_stmt_execute($query);
	}
}
function update_comment($comment_id, $new_content){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'UPDATE comments SET content = ? WHERE id = ?');
	mysqli_stmt_bind_param($query, 'si', $new_content, $comment_id);
	mysqli_stmt_execute($query);
}
function display_article_deletion_form(){
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">
				Supprimer cet article ?
			</h3>
		</div>
		<form method=\"POST\" class=\"col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2\">
			<div class=\"form-group\">
				<label>Veuillez saisir votre mot de passe :</label>
				<input type=\"password\" name=\"password\" class=\"form-control\" placeholder=\"Votre mot de passe\" maxlength=\"60\" autofocus required>
			</div>
			<button name=\"submit_article_deletion\" class=\"btn btn-danger\">
				<span class=\"glyphicon glyphicon-trash\">
				Supprimer
			</button>";
}
function delete_comment($parent_id, $comment_id){
	$mysqli = get_link();
	if($parent_id === NULL){
		$query = mysqli_prepare($mysqli, 'DELETE FROM comments WHERE id = ?');
		mysqli_stmt_bind_param($query, 'i', $comment_id);
		mysqli_stmt_execute($query);
	}else{
		$query = mysqli_prepare($mysqli, 'DELETE FROM comments WHERE parent_id = ?');
		mysqli_stmt_bind_param($query, 'i', $parent_id);
		mysqli_stmt_execute($query);
	}
}
function delete_article($id){
	$mysqli = get_link();
	delete_comment($id, false);
	$query = mysqli_prepare($mysqli, 'DELETE FROM articles WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $id);
	mysqli_stmt_execute($query);
}

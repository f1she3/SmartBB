<?php

function user_infos($user){
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
	if($infos['article_count'] > 0){
		$articles_txt = 'articles postés';
	}else{
		$articles_txt = 'article posté';
	}
	$result = $infos['rank']."\n".$articles_txt.' : '.$infos['article_count']."\n";

	return $result;
}
function article_infos($article_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $article_id);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $infos['id'], $infos['category'], $infos['author'], $infos['title'], $infos['content'], $infos['date'], $infos['is_pinned']);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;	
	}
	if($i == 0){
		return false;
	}else{
		return $infos;
	}
}
function comment_infos($comment_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM comments WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $comment_id);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $infos['id'], $infos['parent_id'], $infos['author'], $infos['content'], $infos['reply_to'], $infos['date']);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;	
	}
	if($i == 0){
		return false;
	}else{
		return $infos;
	}
}
function display_comments($article_id, $page_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT COUNT(*) FROM comments WHERE article_id = ?');
	mysqli_stmt_bind_param($query, 'i', $article_id);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $count);
	mysqli_stmt_fetch($query);
	$per_page = 8;
	$page_count = ceil($count / $per_page);
	$position = ($page_id - 1) * $per_page;
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM  comments WHERE article_id = ? LIMIT ?, ?');
	mysqli_stmt_bind_param($query, 'iii', $article_id, $position, $per_page);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id, $parent_id, $author, $content, $reply_to, $date);
	if($page_id > 1){
		$infos = article_infos($article_id);
		echo 	"<div class=\"page-header\">
				<h2 class=\"text-center\">".$infos['title']."</h2>
			</div>
			<ul class=\"breadcrumb\">
				<li><a href=\"".constant('BASE_URL')."category&cat".$infos['category']."\">".$infos['category']."</a></li>
				<li><a href=\"".constant('BASE_URL')."article&id=".$article_id."\">".$infos['title']."</a></li>
				<li>".$page_id."</li>
			</ul>";
	}
	while(mysqli_stmt_fetch($query)){
		$parent_comment = comment_infos($id);
		$fdate = date_create($date);
		$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
		$user_infos = user_infos($author);
		if($reply_to == 0){
			echo 	"<pre>".bb_decode($parent_comment['content'])."</pre>
				<h4 class=\"text-left\">
					<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
					<a href=\"".constant('BASE_URL')."profile&user=".$author."\"><abbr title=\"".$user_infos."\">".$author."</abbr></a>
					".$fdate."
				</h4>
				<hr>";
			
		}else{
			echo 	"<blockquote>
					".$parent_comment['content']."
					<footer>".$parent_comment['author']."</footer>
				</blockquote>
				<p>".$content."</p>
				<h4 class=\"text-left\">
					<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
					<a href=\"".constant('BASE_URL')."profile&user=".$author."\"><abbr title=\"".$user_infos."\">".$author."</abbr></a>
					".$fdate."
				</h4><hr>";
		}
	}
	if($page_count > 1){
		echo "<ul class=\"pagination\">";
		for($i = 1; $i <= $page_count; $i++){
			if($i == $page_id){
				echo "<li class=\"active\"><a href=\"".constant('BASE_URL')."article&id=".$article_id."&pid=".$i."\">".$i."</a></li>";
			}else{
				echo "<li><a href=\"".constant('BASE_URL')."article&id=".$article_id."&pid=".$i."\">".$i."</a></li>";
			}
		}
		echo 	"</ul>";
	}
}
function display_article($input_id, $page_id){
	$mysqli = get_link();
	$infos = article_infos($input_id);
	if(!$infos){
		set_error('Erreur 404', 'zoom-out', 'L\'article que vous recherchez n\'éxiste pas', 'home');
	}else{
		$fdate = date_create($infos['date']);
		$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
		$user_infos = user_infos($infos['author']);
		echo 	"<div class=\"page-header\">
				<h2 class=\"text-center\">".$infos['title']."</h2>
			</div>";
		echo 	"<ul class=\"breadcrumb\">
				<li><a href=\"".constant('BASE_URL')."category&cat".$infos['category']."\">".$infos['category']."</a></li>
				<li>".$infos['title']."</li>
			</ul>";
		echo 	"<h4 class=\"text-left\">
				<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
				<a href=\"".constant('BASE_URL')."profile&user=".$infos['author']."\">
					<abbr title=\"".$user_infos."\">".$infos['author']."</abbr>
				</a>
				".$fdate."
			</h4>";
		echo "<form method=\"POST\">
					<button name=\"send_mp\" class=\"btn btn-info\" value=\"1\">
						<span class=\"glyphicon glyphicon-send\"> mp</span> 
					</button>";
		echo 		"</form>
			<pre>
				<p>".$infos['content']."</p>
			</pre>";
		if(get_rank($_SESSION['name']) > 0){
			echo 	"<form method=\"POST\">
						<button name=\"close_article\" class=\"btn btn-primary\">
							<span class=\"glyphicon glyphicon-share\"> répondre</span>
						</button>
						<button name=\"like\" class=\"btn btn-success\">
							<span class=\"glyphicon glyphicon-thumbs-up\"></span>
						</button>
						<button name=\"dislike\" class=\"btn btn-warning\">
							<span class=\"glyphicon glyphicon-thumbs-down\"></span>
						</button>";

			$user_rank = get_rank($infos['author']);
			$my_rank = get_rank($_SESSION['name']);
			$ranks = get_rank_list();
			if($my_rank == $ranks['max'] || $my_rank > $user_rank){
				echo 		"<button name=\"close_article\" class=\"btn btn-danger pull-right\">
							<span class=\"glyphicon glyphicon-ban-circle\"> fermer</span> 
						</button>";
			}
			echo "	</form><hr>";
		}
		display_comments($input_id, $page_id);
		echo 	"<form method=\"POST\">
				<div class=\"form-group col-md-8 col-md-offset-2\">
					<textarea class=\"form-control\" style=\"resize:none\" name=\"comment\" maxlength=\"500\" required></textarea>
				</div>
				<div class=\"form-group col-md-4 col-md-offset-4\">
					<button class=\"btn btn-primary col-sm-2 col-xs-2 col-xs-offset-5\" name=\"submit_comment\">
						<span class=\"glyphicon glyphicon-send\"></span> 
					</button>
				</div>
			</form>";
	}
}
function post_comment($parent_id, $author, $content, $reply_to){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO comments (article_id, author, content, reply_to, date) VALUES (?, ?, ?, ?, NOW())');
	mysqli_stmt_bind_param($query, 'isss', $parent_id, $author, $content, $reply_to);
	mysqli_stmt_execute($query);
}

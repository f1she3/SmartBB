<?php

function user_infos($user){
	$user = 'test';
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
	$result = $infos['rank']."\n".'articles postés : '.$infos['article_count']."\n";

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
function display_article($input_id){
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
		echo "<ul class=\"breadcrumb\">
			<li><a href=\"".constant('BASE_URL')."category&cat".$infos['category']."\">".$infos['category']."</a></li>
				<li>".$infos['title']."</li>
			</ul>";
		echo 	"<h4 class=\"text-left\">
				<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
				<a href=\"".constant('BASE_URL')."profile&user=".$infos['author']."\"><abbr title=\"".$user_infos."\">".$infos['author']."</abbr></a>
				".$fdate."
			</h4>";
		echo 	"<pre><p class=\"\">".$infos['content']."</p></pre>";
		if(get_rank($_SESSION['name']) > 0){
		echo 	"<form method=\"POST\">
				<div class=\"form-group\">
					<button name=\"close_article\" class=\"btn btn-danger\">
						<span class=\"glyphicon glyphicon-ban-circle\"></span> 
					</button>
					<button name=\"close_article\" class=\"btn btn-info\">
						<span class=\"glyphicon glyphicon-ban-circle\"></span> 
					</button>
				</div>
			</form><hr>";
			
		}
		display_comments($input_id);
		echo 	"<form method=\"POST\">
				<div class=\"form-group col-md-8 col-md-offset-2\">
					<textarea class=\"form-control\" style=\"resize:none\" name=\"comment\"></textarea>
				</div>
				<div class=\"form-group col-md-4 col-md-offset-4\">
					<button class=\"btn btn-primary col-sm-2 col-xs-2 col-xs-offset-5\">
						<span class=\"glyphicon glyphicon-send\"></span> 
					</button>
				</div>
			</form>";
	}
}
function display_comments($parent_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM  comments WHERE article_id = ?');
	mysqli_stmt_bind_param($query, 'i', $parent_id);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id, $parent_id, $author, $content, $reply_to, $date);
	while(mysqli_stmt_fetch($query)){
		$parent_comment = comment_infos($parent_id);
		$fdate = date_create($date);
		$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
		$user_infos = user_infos($author);
		if($reply_to == 0){
			echo 	"<p>".$parent_comment['content']."</p>
				<h4 class=\"text-left\">
					<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
					<a href=\"".constant('BASE_URL')."profile&user=".$author."\"><abbr title=\"".$user_infos."\">".$author."</abbr></a>
					".$fdate."
				</h4><hr>";
			
		}else{
			echo "<blockquote>
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
}

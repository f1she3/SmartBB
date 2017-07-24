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
function display_article($input_id){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM articles WHERE id = ?');
	mysqli_stmt_bind_param($query, 'i', $input_id);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id, $category, $author, $title, $content, $date, $is_pinned);
	while(mysqli_stmt_fetch($query)){
		$fdate = date_create($date);
		$fdate = date_format($fdate, 'G:i, \l\e j/m Y');
		$user_infos = user_infos($author);
		echo 	"<div class=\"page-header\">
				<h2 class=\"text-center\"><kbd>[".$category."]</kbd> ".$title."</h2>
			</div>";

		echo 	"<pre style=\"border: 1px solid\"><p class=\"lead\">".$content."</p></pre>";
		echo 	"<h4 class=\"text-left\">
				<img src=\"../css/images/account_black.svg\" height=\"40\" width=\"40\">
				<a href=\"".constant('BASE_URL')."profile&user=".$author."\"><abbr title=\"".$user_infos."\">".$author."</abbr></a>
				".$fdate."
			</h4>";

	}

}

<?php

function is_banned($username){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT msg, banned_by FROM ban WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $reason, $banned_by);
	$result = mysqli_stmt_fetch($query);
	if(empty($result)){
		return false;
	}else{
		$result = array();
		$result['message'] = $reason;
		$result['banned_by'] = $banned_by;

		return $result;
	}
}
function is_category($input_name){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id FROM categories WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $input_name);
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
function is_logged(){
	if(isset($_SESSION['name']) && !empty($_SESSION['name'])){
		return true;

	}else{
		return false;
	}
}
function is_user($input){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $input);
	mysqli_stmt_execute($query); $result = mysqli_stmt_fetch($query);
	if($result == 0){
		return false;
	
	}else{
		return true;
	}
}
function is_rank($input_rank){
	$ranks = get_rank_list();
	$result = false;
	foreach($ranks as $key => $value){
		if(isset($ranks[$key])){
			$result = true;
			break;
		}
	}

	return $result;
}
function get_rank($username){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT rank FROM users WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $rank);
	mysqli_stmt_fetch($query);
	
	return $rank;
}
function get_rank_list(){
	// Add ranks here
	$ranks[0] = 'Membre';
	$ranks[1] = 'Modérateur';
	$ranks[2] = 'Administrateur';
	$ranks[3] = 'Chef Administrateur';
	// Change this value to the number of ranks
	$ranks['max'] = 3;

	return $ranks;
}
function get_rank_id($rank_name){
	$ranks = get_rank_list();
	foreach($ranks as $key => $value){
		if($ranks[$key] == $rank_name){
			return $key;
		}
	}
}
function set_rank($username, $rank){
	$ranks = get_rank_list();
	if(isset($ranks[$rank])){
		$mysqli = get_link();
		$query = mysqli_prepare($mysqli, 'UPDATE users SET rank = ? WHERE BINARY name = ?');
		mysqli_stmt_bind_param($query, 'is', $rank, $username);
		mysqli_stmt_execute($query);
	
	}else{
		return false;
	}
}
function ban($username, $reason){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO ban (name, msg, banned_by) VALUES (?, ?, ?)');
	mysqli_stmt_bind_param($query, 'sss', $username, $reason, $_SESSION['name']);
	mysqli_stmt_execute($query);
}
function deban($username){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'DELETE FROM ban WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
}
function post_article($author, $category_name, $title, $content){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO articles (author, category, title, content, date) VALUES (?, ?, ?, ?, NOW())');
	mysqli_stmt_bind_param($query, 'ssss', $author, $category_name, $title, $content);
	mysqli_stmt_execute($query);
	$query = mysqli_prepare($mysqli, 'SELECT id FROM articles WHERE BINARY author = ? ORDER BY id DESC');
	mysqli_stmt_bind_param($query, 's', $author);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id);
	mysqli_stmt_fetch($query);

	return $id;
}
function like($article_id, $user, $status){
	// status : 
	// 0 : like
	// 1 : dislike
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO likes (article_id, name, status) VALUES (?, ?, ?)');
	mysqli_stmt_bind_param($query, 'isi', $article_id, $user, $status);
	mysqli_stmt_execute($query);
}
function delete_category($category_name, $new_category){
	$mysqli = get_link();
	if(!$new_category){
		$query = mysqli_prepare($mysqli, 'DELETE FROM articles WHERE BINARY category = ?');
		mysqli_stmt_bind_param($query, 's', $category_name);
		mysqli_stmt_execute($query);
	}else{
		$query = mysqli_prepare($mysqli, 'UPDATE articles SET category = ? WHERE BINARY category = ?');
		mysqli_stmt_bind_param($query, 'ss', $new_category, $category_name);
		mysqli_stmt_execute($query);
	}
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'DELETE FROM categories WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $category_name);
	mysqli_stmt_execute($query);
}
function get_category_list(){
	$mysqli = get_link();
	$query = mysqli_query($mysqli, 'SELECT * FROM categories ORDER BY is_pinned DESC, id');
	$i = 0;
	$my_rank = get_rank($_SESSION['name']);
	while($tmp = mysqli_fetch_assoc($query)){
		if($my_rank >= $result['post_restriction']){
			$result[$i] = $tmp;
			$i++;
		}
	}

	return $result;
}
function get_category_infos($category){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM categories WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $category);
	mysqli_stmt_execute($query);
	$result = array();
	mysqli_stmt_bind_result($query, $result['id'], $result['name'], $result['access_restriction'], 
		$result['post_restriction'], $result['rank_owner'], $result['is_pinned']);
	mysqli_stmt_fetch($query);
	
	return $result;
}

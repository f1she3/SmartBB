<?php

function is_banned($username, $ip){
	$mysqli = get_link();
	if($username === NULL){
		$query = mysqli_prepare($mysqli, 'SELECT msg, banned_by, ending, ban_level FROM ban WHERE BINARY ip = ? AND status = 0');
		mysqli_stmt_bind_param($query, 's', $ip);
	}else if($ip === NULL){
		$query = mysqli_prepare($mysqli, 'SELECT msg, banned_by, ending, ban_level FROM ban WHERE BINARY name = ? AND status = 0');
		mysqli_stmt_bind_param($query, 's', $username);
	}
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $reason, $banned_by, $ending, $ban_level);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;
	}
	$fdate = date_create();
	$fending = date_create($ending);
	$null = date_create('0000-00-00 00:00:00');
	if($fending != $null){
		if($fdate >= $fending){
			deban($username, $ip);
		}
	}
	if($i === 0){
		$result = array();
		$result['result'] = false;
	}else{
		$result = array();
		$result['result'] = true;
		$result['message'] = $reason;
		$result['banned_by'] = $banned_by;
		$result['ending'] = $ending;
		$result['ban_level'] = $ban_level;
	}
	
	return $result;
}
function get_ban_count($username, $ip){
	$mysqli = get_link();
	if($username === NULL){ 
		$query = mysqli_prepare($mysqli, 'SELECT id FROM ban WHERE BINARY ip = ?');
		mysqli_stmt_bind_param($query, 's', $ip);
	}else if($ip === NULL){ 
		$query = mysqli_prepare($mysqli, 'SELECT id FROM ban WHERE BINARY name = ?');
		mysqli_stmt_bind_param($query, 's', $username);
	}
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;
	}

	return $i;
}
function get_user_ip(){
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];
	if(filter_var($client, FILTER_VALIDATE_IP)){
		$ip = $client;
	}elseif(filter_var($forward, FILTER_VALIDATE_IP)){
		$ip = $forward;
	}else{
		$ip = $remote;
	}

	return $ip;
}
function check_ids($type, $input, $username){
	$mysqli = get_link();
	if($type == 'name'){
		$query = mysqli_prepare($mysqli, 'SELECT id FROM users WHERE BINARY name = ?');
		mysqli_stmt_bind_param($query, 's', $username);
		mysqli_stmt_execute($query);
		$result = mysqli_stmt_fetch($query);
		if(!empty($result)){
			return true;
		
		}else{
			return false;
		}
	
	}else if($type == 'password'){
		$query = mysqli_prepare($mysqli, 'SELECT password FROM users WHERE BINARY name = ?');
		mysqli_stmt_bind_param($query, 's', $username);
		mysqli_stmt_execute($query);
		mysqli_stmt_bind_result($query, $db_password);
		mysqli_stmt_fetch($query);
		if(password_verify($input, $db_password)){
			return true;
	
		}else{
			return false;
		}
	}
}
function update_password($username, $new_password){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'UPDATE users SET password = ? WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 'ss', $new_password, $username);
	mysqli_stmt_execute($query);
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
function get_user_infos($user){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT * FROM users WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $user);
	mysqli_stmt_execute($query);
	$result = array();
	mysqli_stmt_bind_result($query, $result['id'], $result['name'], $result['email'], $result['password'], 
		$result['reg_date'], $result['rank'], $result['recovery_code'], $result['token']);
	mysqli_stmt_fetch($query);

	return $result;
}
function is_rank($input_rank){
	$ranks = get_rank_list();
	$result = false;
	foreach($ranks as $key => $rank){
		if($key == $input_rank){
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
	// Change this value to the actual number of ranks - 1
	$ranks['moderator'] = 1;
	$ranks['administrator'] = 2;
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
		return true;
	
	}else{
		return false;
	}
}
function get_ban_duration_list(){
	$current_date = date_create();
	$ban[0][0] = 'de 15 minutes';
	$ban[0][1] = date_modify($current_date, '+15 minutes');
	$ban[0][1] = date_format($current_date, 'Y-m-d H-i-s');
	$ban[1][0] = 'de 1 heure';
	$ban[1][1] = date_modify($current_date, '+1 hour');
	$ban[1][1] = date_format($current_date, 'Y-m-d H-i-s');
	$ban[2][0] = 'de 1 jour';
	$ban[2][1] = date_modify($current_date, '+1 day');
	$ban[2][1] = date_format($current_date, 'Y-m-d H-i-s');
	$ban[3][0] = 'de 1 semaine';
	$ban[3][1] = date_modify($current_date, '+1 week');
	$ban[3][1] = date_format($current_date, 'Y-m-d H-i-s');
	$ban[4][0] = 'de 1 mois';
	$ban[4][1] = date_modify($current_date, '+1 month');
	$ban[4][1] = date_format($current_date, 'Y-m-d H-i-s');
	$ban[5][0] = 'de 1 an'; 
	$ban[5][1] = date_modify($current_date, '+1 year');
	$ban[5][1] = date_format($current_date, 'Y-m-d H-i-s');
	$ban[6][0] = 'indéfinie'; 
	$ban[6][1] = NULL;
	$ban['max'] = 6;

	return $ban;
}
function ban($username, $reason, $ip, $ban_level, $banned_by){
	$bans = get_ban_duration_list();
	if($ban_level === NULL){
		$ban_level = 'NULL';
		$ending = 'NULL';
	}else{
		if($bans[$ban_level] === $bans['max']){
			$ending = 'NULL';
		}
		$ending = $bans[$ban_level][1];
	}
	if($banned_by === NULL){
		$banned_by = 'NULL';
	}
	if($username === NULL){
		deban(NULL, $ip);
		$username = 'NULL';
	}else{
		deban($username, NULL);
	}
	if($ip === NULL){
		$ip = 'NULL';
	}
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO ban (name, ip, msg, banned_by, ending, ban_level) VALUES (?, ?, ?, ?, ?, ?)');
	mysqli_stmt_bind_param($query, 'sssssi', $username, $ip, $reason, $banned_by, $ending, $ban_level);
	mysqli_stmt_execute($query);
}
function deban($username, $ip){
	$mysqli = get_link();
	if($username === NULL){
		$query = mysqli_prepare($mysqli, 'UPDATE ban SET status = 1 WHERE BINARY ip = ?');
		mysqli_stmt_bind_param($query, 's', $ip);
	}else if($ip === NULL){
		$query = mysqli_prepare($mysqli, 'UPDATE ban SET status = 1 WHERE BINARY name = ?');
		mysqli_stmt_bind_param($query, 's', $username);
	}
	mysqli_stmt_execute($query);
}
function post_article($author, $category_name, $title, $content, $is_pinned){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'INSERT INTO articles (author, category, title, content, is_pinned, date) VALUES (?, ?, ?, ?, ?, NOW())');
	mysqli_stmt_bind_param($query, 'ssssi', $author, $category_name, $title, $content, $is_pinned);
	mysqli_stmt_execute($query);
	$query = mysqli_prepare($mysqli, 'SELECT id FROM articles WHERE BINARY author = ? ORDER BY id DESC');
	mysqli_stmt_bind_param($query, 's', $author);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $id);
	mysqli_stmt_fetch($query);

	return $id;
}
// status : 
// 0 : like
// 1 : dislike
function like($article_id, $user, $status){
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
		if($my_rank >= $tmp['post_restriction']){
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
function auto_ban_process($user_ip){
	$max_attempts = 3;
	if(!isset($_SESSION['attempts'])){
		$_SESSION['attempts'] = 0;
	}
	$_SESSION['attempts']++;
	$diff = $max_attempts - $_SESSION['attempts'];
	if($diff === 0){
		$bans = get_ban_duration_list();
		$ban_count = get_ban_count(NULL, $user_ip);
		$ban_level = $ban_count;
		if($ban_count != 0 && $ban_count != $bans['max']){
			$ban_level = $ban_count++;	
		}
		ban(NULL, 'Ban automatique', $user_ip, $ban_level, NULL);
		$_SESSION['attempts'] = 0;
		$error = 'Code erroné, vous êtes banni pour une durée '.$bans[$ban_level][0];
	}else{
		if($diff > 1){
			$text = 'essais';
		}else{
			$text = 'essai';
		}
		$type = 'danger';
		$error = 'Code erroné, il vous reste '.$diff.' '.$text;
	}
	
	return $error;	
}

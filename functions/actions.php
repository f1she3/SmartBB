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
function check_rank($username, $rank){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id FROM users WHERE BINARY name = ? AND rank = ?');
	mysqli_stmt_bind_param($query, 'ss', $username, $rank);
	mysqli_stmt_execute($query);
	$result = mysqli_stmt_fetch($query);
	if($result == 0){
		return false;
	
	}else{
		return true;
	}
}

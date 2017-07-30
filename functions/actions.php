<?php


function is_banned($username){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT msg FROM ban WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $reason);
	$result = mysqli_stmt_fetch($query);
	if(empty($result)){
		return false;
	
	}else{
		$result = array();
		$result['message'] = $reason;
		return $result;	
	}
}
function is_mute($username){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT end FROM mute WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $end);
	$result = mysqli_stmt_fetch($query);
	if(empty($result)){
		return false;	
	
	}else{
		$now = date('Y-m-d H:i:s');
		if($now > $end){
			demute($username);

			return false;
		
		}else{
			return true;
		}
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

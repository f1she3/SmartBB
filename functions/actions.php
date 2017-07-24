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

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

<?php

// Returns an username if the input is an email address
function find_name($input){
	$mysqli = get_link();
	$email = sha1($input);
	$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name = ? OR email = ?');
	mysqli_stmt_bind_param($query, 'ss', $input, $email);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $name);
	mysqli_stmt_fetch($query);
	
	return $name;
}

<?php

function login($username){
	$mysqli = get_link();
	$var = 'NULL';
	$query = mysqli_prepare($mysqli, 'UPDATE users SET recovery_code = ?, token = ? WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 'sss', $var, $var, $username);
	mysqli_stmt_execute($query);
	$_SESSION['code_validate'] = 0;
	$_SESSION['token_validate'] = 0;
	$_SESSION['tmp_name'] = 0;
	$_SESSION['name'] = $username;
}

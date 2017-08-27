<?php

function code_gen(){
	$tmp_code = random_int(100000000000000000, 999999999999999999);
	$code = sha1($tmp_code);
	$code = substr($code, 0, 6);

	return $code;
}
function set_recovery_code($code, $user){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'UPDATE users SET recovery_code = ? WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 'ss', $code, $user);
	mysqli_stmt_execute($query);
}

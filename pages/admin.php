<?php

if(isset($_POST['user']) && !empty($_POST['user']) && is_string($_POST['user'])){
	$_POST['user'] = secure($_POST['user']);
	if($_POST['user'] != $_SESSION['name']){
		redirect('profile&user='.$_POST['user']);
	}else{
		display_server_infos();
	}
}else{
	display_server_infos();
}

<?php

if(isset($_GET['user']) && !empty($_GET['user']) && is_string($_GET['user'])){
	$user = $_GET['user'] = secure($_GET['user']);
	if($user == $_SESSION['name']){
		redirect('profile');
	}
	if(is_user($user)){
		display_user_infos($user, false);
	}else{
		set_error('Erreur', 'zoom-out', 'Cet utilisateur n\'éxiste pas', 'home');
	}
}else{
	display_user_infos($_SESSION['name'], true);
}

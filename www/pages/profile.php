<?php

if(isset($_GET['user']) && !empty($_GET['user']) && is_string($_GET['user'])){
	$user = $_GET['user'] = secure($_GET['user']);
	if($user == $_SESSION['name']){
		redirect('profile');
	}
	if(!is_user($user)){
		set_error('Erreur', 'zoom-out', 'Cet utilisateur n\'éxiste pas', 'home');
	}
	$user_rank = get_rank($user);
	$my_rank = get_rank($_SESSION['name']);
	if(isset($_POST['submit_ban'])){
		if(isset($_POST['ban_message']) && !empty($_POST['ban_message']) && is_string($_POST['ban_message']) && 
			mb_strlen($_POST['ban_message']) <= 50){
			$ban_message = $_POST['ban_message'] = secure($_POST['ban_message']);
			if($user_rank < $my_rank){
				ban($user, $ban_message, NULL, NULL, $_SESSION['name']);
			}
		}
	}else if(isset($_POST['submit_deban'])){
		$is_banned = is_banned($user, NULL);
		$ranks = get_rank_list();
		if($is_banned['result']){
			if($my_rank > $user_rank){
				if($my_rank == $ranks['max']){
					deban($user, NULL);
				}else{
					if($my_rank > get_rank($is_banned['banned_by'])){
						deban($user, NULL);
					}
				}
			}
		}
	}else if(isset($_POST['set_rank'])){
		if(isset($_POST['rank']) && ctype_digit($_POST['rank'])){
			$ranks = get_rank_list();
			$rank = $_POST['rank'] = secure($_POST['rank']);
			if(!is_rank($rank)){
				set_error('Erreur', 'exclamation-sign', 'Erreur avec le nouveau rang', 'profile&user='.$user);
			}
			if($my_rank >= $ranks['administrator'] && $my_rank > $user_rank){
				set_rank($user, $rank);
			}
		}
	}
	display_user_infos($user, false);
}else{
	display_user_infos($_SESSION['name'], true);
}

<?php

if(isset($_GET['user']) && !empty($_GET['user']) && is_string($_GET['user'])){
	$user = $_GET['user'] = secure($_GET['user']);
	if($user == $_SESSION['name']){
		redirect('profile');
	}
	if(is_user($user)){
		if(isset($_POST['submit_ban'])){
			if(isset($_POST['ban_message']) && !empty($_POST['ban_message']) && is_string($_POST['ban_message']) && strlen($_POST['ban_message']) <= 50){
				$ban_message = $_POST['ban_message'] = secure($_POST['ban_message']);
				if(get_rank($user) < get_rank($_SESSION['name'])){
					ban($user, $ban_message);
				}

			}
		}else if(isset($_POST['submit_deban'])){
			$ret_is_banned = is_banned($user);
			$user_rank = get_rank($user);
			$my_rank = get_rank($_SESSION['name']);
			$ranks = get_rank_list();
			if($ret_is_banned){
				if($my_rank > $user_rank){
					if($my_rank == $ranks['max']){
						deban($user);
					}else{
						if($my_rank > get_rank($ret_is_banned['banned_by'])){
							deban($user);
						}
					}
				}
			}
			
		}else{
			if(isset($_POST['set_rank'])){
				if(isset($_POST['rank']) && (!empty($_POST['rank']) || $_POST['rank'] == 0) && ctype_digit($_POST['rank'])){
					$rank = $_POST['rank'] = secure($_POST['rank']);
					$my_rank = get_rank($_SESSION['name']);
					if($my_rank > 1 && $my_rank > get_rank($user)){
						set_rank($user, $rank);
					}
				}
			}
		}
		display_user_infos($user, false);
	}else{
		set_error('Erreur', 'zoom-out', 'Cet utilisateur n\'éxiste pas', 'home');
	}
}else{
	display_user_infos($_SESSION['name'], true);
}

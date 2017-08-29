<?php

$error = '';
$style = 'a';
$max_attempts = 3;
$_SESSION['code_validate'] = 0;
$_SESSION['token_validate'] = 0;
if(isset($_POST['submit_email'])){
	$style = '';
	if(isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$email = $_POST['email'] = secure($_POST['email']);
		$email_hash = sha1($email);
		$is_user = is_user($email_hash);
		if($is_user != NULL){
			$user_infos = get_user_infos($is_user);
			if($email_hash === $user_infos['email']){
				$code = code_gen(9);
				$token = code_gen(32);
				set_recovery_codes($is_user, $code, $token);
				$subject = get_project_name().' | Votre code';
				$to = $email;
				$from = $name = get_project_name();
				$headers = 'MIME-Version: 1.0'."\r\n";
				$headers .= 'Content-type:text/html;charset=UTF-8'."\r\n";
				$headers .= 'Date: ' . date('r', time())."\r\n";
				$headers .= 'From: "' . $name . '" <' . $from . '>'."\r\n";
				$headers .= 'Return-Path: "' . $name . '" <' . $from . '>'."\r\n";
				$headers .= 'X-Mailer: PHP ' . phpversion()."\r\n";
				$headers .= 'X-Priority: 2'."\r\n";
				$headers .= 'X-MSMail-Priority: High'."\r\n";
				$headers .= 'X-Originating-IP: ' . $_SERVER['SERVER_ADDR']."\r\n";
				$content = 	"<!DOCTYPE html>
							<html lang=\"en\">
								<head>
									<meta charset=\"utf-8\">
									<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
									<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
									<meta name=\"description\" content=\"Forum francophone de cyber-sécurité\">
									<meta name=\"author\" content=\"f1she3\">
									<link rel=\"icon\" href=\"".get_root_url().'/css/images/favicon.ico'."\">
									<link href=\"".get_root_url().'/css/bootstrap.min.css'."\" rel=\"stylesheet\">
									<link href=\"".get_root_url().'/css/style.css'."\" rel=\"stylesheet\">
								</head>
								<body>
									<div class=\"container\">
										<div class=\"page-header\">
											<h3 class=\"text-center\">
												".get_project_name()." : Réinitialiser mon mot de passe
											</h3>
										</div>
										<h3 class=\"text-center\">
											Votre code de sécurité : 
										</h3>
										<h3 class=\"text-center\">
											<kbd>".$code."</kbd>
										</h3><br>
										<h3 class=\"text-center\">
											<a href=\"".get_root_url().get_base_url()."forget&token=".$token."\" target=\"_blank\">Valider ici</a>
										</h3>
									</div>
								</body>
							</html>";
				mail($to, $subject, $content, $headers);
				$msg = 'Un email vous a été envoyé, veuillez suivre les instructions de ce dernier';
				display_confirm_email_form('success', $msg, $style);
			}else{
				$error = 'adresse email incorrecte';
				display_confirm_email_form('danger', $error, $style);
			}
		}else{
			if(!isset($_SESSION['attempts'])){
				$_SESSION['attempts'] = 0;
			}
			$_SESSION['attempts']++;
			$diff = $max_attempts - $_SESSION['attempts'];
			if($diff > 0){
				if($diff === 1){
					$text = 'essai';
				}else{
					$text = 'essais';
				}
				$error = 'adresse email incorrecte, il vous reste '.$diff.' '.$text;
			}else{
				$bans = get_ban_duration_list();
				$user_ip = get_user_ip();
				$ban_count = get_ban_count(NULL, $user_ip);
				$ban_level = $ban_count;
				if($ban_count != 0 && $ban_count < $bans['max']){
					$ban_level = $ban_count++;	
				}
				if($bans[$ban_level] === $bans['max']){
					$error = 'adresse email incorrecte, vous êtes banni à vie';
				}else{
					$error = 'adresse email incorrecte, vous êtes banni pour une durée de '.$bans[$ban_level][0];
				}
				ban(NULL, 'Ban automatique', $user_ip, $ban_level, NULL);
				$_SESSION['attempts'] = 0;
			}
			display_confirm_email_form('danger', $error, $style);
		}
	}else{
		$error = 'adresse email invalide';
		display_confirm_email_form('danger', $error, $style);
	}
}else if(isset($_GET['token']) && !empty($_GET['token']) && is_string($_GET['token'])){
	$token = $_GET['token'] = secure($_GET['token']);
	if(!is_token($token)){
		if(!isset($_SESSION['attempts'])){
			$_SESSION['attempts'] = 0;
		}
		$_SESSION['attempts']++;
		$diff = $max_attempts - $_SESSION['attempts'];
		if($diff === 0){
			$user_ip = get_user_ip();
			$ban_level = $ban_count;
			if($ban_count != 0 && $ban_count != $bans['max']){
				$ban_level = $ban_count++;	
			}
			ban(NULL, 'Ban automatique', $user_ip, $ban_level, NULL);
			$_SESSION['attempts'] = 0;
			set_error('Erreur', 'zoom-out', 'Token invalide, veuillez ne pas réessayer', 'forget');
		}else{
			set_error('Erreur', 'zoom-out', 'Token invalide, veuillez ne pas réessayer', 'forget');
		}
	}
	if(isset($_POST['submit_code'])){
		if(isset($_POST['code']) && !empty($_POST['code']) && is_string($_POST['code'])){
			$code = $_POST['code'] = secure($_POST['code']);
			$name = check_codes($token, $code);
			if($name != NULL){
				$_SESSION['tmp_name'] = $name;
				$_SESSION['code_validate'] = 1;
				$_SESSION['token_validate'] = 1;
				display_new_password_form();
			}
		}
	}else if(isset($_POST['submit_password'])){
		if($_SESSION['code_validate'] && $_SESSION['token_validate']){
			if(isset($_POST['new_password'], $_POST['repeat_new_password']) && !empty($_POST['new_password']) && 
				!empty($_POST['repeat_new_password']) && is_string($_POST['new_password']) && is_string($_POST['repeat_new_password'])){
				$new_password = $_POST['new_password'] = secure($_POST['new_password']);
				$repeat_new_password = $_POST['repeat_new_password'] = secure($_POST['repeat_new_password']);
				update_passwords($new_password, $repeat_password);
			}else{
				$error = 'Veuillez saisir votre nouveau mot de passe';
			}
		}else{
			set_error('Erreur', 'exclamation-sign', 'Erreur lors du processus de réinitialisation, veuillez suivre les instructions', 'forget');
		}
	}else{
		display_confirm_code_form();
	}
}else{
	display_confirm_email_form('danger', $error, $style);
}

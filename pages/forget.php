<?php

$error = '';
$style = 'a';
$user_ip = get_user_ip();
if(!isset($_SESSION['code_validate'])){
	$_SESSION['code_validate'] = 0;
}
if(!isset($_SESSION['token_validate'])){
	$_SESSION['token_validate'] = 0;
}
if(!isset($_SESSION['tmp_name'])){
	$_SESSION['tmp_name'] = 0;
}
if(isset($_POST['submit_email'])){
	$style = '';
	if(isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$email = $_POST['email'] = secure($_POST['email']);
		$is_user = is_user($email);
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
			$error = auto_ban_process($user_ip);
			display_confirm_email_form('danger', $error, $style);
		}
	}else{
		$error = 'adresse email invalide';
		display_confirm_email_form('danger', $error, $style);
	}
}else if(isset($_GET['token']) && !empty($_GET['token']) && is_string($_GET['token'])){
	$token = $_GET['token'] = secure($_GET['token']);
	if(!is_token($token)){
		$error = auto_ban_process($user_ip);
		set_error('Erreur', 'exclamation-sign', $error, 'forget');
	}
	if(isset($_POST['submit_code'])){
		if(isset($_POST['code']) && !empty($_POST['code']) && is_string($_POST['code'])){
			$code = $_POST['code'] = secure($_POST['code']);
			$name = check_codes($token, $code);
			if($name != NULL && is_user($name)){
				$_SESSION['tmp_name'] = $name;
				$_SESSION['code_validate'] = 1;
				$_SESSION['token_validate'] = 1;
				display_new_password_form('a', '');
			}else{
				$type = 'danger';
				$error = auto_ban_process($user_ip);
				display_confirm_code_form($type, $error);
			}
		}else{
			$type = 'danger';
			$error = auto_ban_process($user_ip);
			display_confirm_code_form($type, $error);
		}
	}else if(isset($_POST['submit_password'])){
		if($_SESSION['code_validate'] && $_SESSION['token_validate'] && $_SESSION['tmp_name']){
			if(isset($_POST['new_password']) && !empty($_POST['new_password']) && is_string($_POST['new_password'])){
				if(mb_strlen($_POST['new_password']) >= 6){
					if(mb_strlen($_POST['new_password']) <= 64){
						$new_password = $_POST['new_password'] = secure($_POST['new_password']);
						if(isset($_POST['repeat_new_password']) && !empty($_POST['repeat_new_password']) && 
							is_string($_POST['repeat_new_password'])){
							$repeat_new_password = $_POST['repeat_new_password'] = secure($_POST['repeat_new_password']);
							if($repeat_new_password === $new_password){
								$new_password = password_hash($new_password, PASSWORD_DEFAULT);
								finish_recovery_process($_SESSION['tmp_name'], $new_password);
								header('Refresh: 3; url = '.get_base_url().'login');
								set_error('Réinitialisation réalisée avec succès !', 'ok', 'Vous allez être redirigé ...', 'login');

							}else{
								$error = 'Les mots de passe entrés sont différents';
								display_new_password_form('', $error);
							}
						}else{
							$error = 'Veuillez répéter votre mot de passe';
							display_new_password_form('', $error);
						}
					}else{
						$error = 'Le mot de passe est trop long';
						display_new_password_form('', $error);
					}
				}else{
					$error = 'Le nouveau mot de passe est trop court';
					display_new_password_form('', $error);
				}
			}else{
				$error = 'Veuillez saisir votre nouveau mot de passe';
				display_new_password_form('', $error);
			}
		}else{
			$error = 'Erreur lors du processus de réinitialisation, veuillez suivre les instructions envoyées par mail';
			auto_ban_process($user_ip);
			display_new_password_form('', $error);
		}
	}else{
		$type = 'danger';
		$error = '';
		display_confirm_code_form($type, $error);
	}
}else{
	display_confirm_email_form('danger', $error, $style);
}

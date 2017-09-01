<?php

$error = '';
$style = 'a';
if(isset($_POST['submit'])){
	$style = '';
	if(isset($_POST['input']) && !empty($_POST['input']) && is_string($_POST['input'])){
		$input = $_POST['input'] = secure($_POST['input']);
		if(isset($_POST['password']) && !empty($_POST['password']) && is_string($_POST['password'])){
			$password = $_POST['password'] = secure($_POST['password']);
			$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
			$is_user = is_user($input);
			if($is_user != NULL){
				if(!empty($input)){
					if(!empty($_POST['password'])){	
						if(strlen($_POST['password']) >= 6){
							$is_banned = is_banned($is_user, NULL);
							if(!$is_banned['result']){
								$check_input = check_ids('name', false, $is_user);
								$check_pass = check_ids('password', $_POST['password'], $is_user);
								if($check_input && $check_pass){
									login($is_user);
									redirect(1);
								}else{
									$error = 'nom d\'utilisateur ou mot de passe incorrect';
								}
							}else{
								$error = 'banni pour la raison suivante : "'.format_text($is_banned['message']).'"';
							}
					}else{
						$error = 'nom d\'utilisateur ou mot de passe incorrect';
					}
				}else{
					$error = 'veuillez saisir votre mot de passe';
				}
			}else{
				$error = 'veuillez saisir votre pseudo';
			}
		}else{
			$error = 'nom d\'utilisateur ou mot de passe incorrect';
		}
		}else{
			$error = 'veuillez saisir votre mot de passe';
		}
	}else{
		$error = 'veuillez saisir votre pseudo';
	}
}
?>
<h2 class="text-center">Connexion</h2>
<div class="col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
<?php

if(!empty($error)){
	echo 	"<div class=\"alert alert-danger text-center\">
			<span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span> ".$error."
		</div>";
}
if(!empty($style)){
	echo "<div class=\"alert alert-danger invisible\">".$style."</div>";
}
?>
</div>
<div class="col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<strong>Se connecter</strong>
			</h3>
		</div>
		<div class="panel-body">
			<form method="POST" action="">
				<div class="form-group">
					<label for="input_log">Pseudo ou e-mail :</label>
					<input type="text" name="input" class="form-control" placeholder="Pseudo ou e-mail" maxlength="40" autofocus required>
				</div>
				<div class="form-group">
					<label for="password_log">Mot de passe :</label>
					<input type="password" name="password" class="form-control" placeholder="************" maxlength="64" required>
				</div>
				<a href="<?= get_root_url().get_base_url() ?>forget">Mot de passe oublié ?</a>
				<button name="submit" class="btn btn-primary center-block">Connexion</button>
			</form>
		</div>
	</div>
</div>

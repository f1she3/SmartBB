<?php

$error = '';
$style = 'a';
if(isset($_POST['submit'])){
	$style = '';
	if(isset($_POST['name']) && !empty($_POST['name']) && is_string($_POST['name'])){
		$name = $_POST['name'] = secure($_POST['name']);
		if(isset($_POST['password']) && !empty($_POST['password']) && is_string($_POST['password'])){
			$password = $_POST['password'] = secure($_POST['password']);
			$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
			$is_user = is_user($name);
			if($is_user != NULL){
				if(!empty($name)){
					if(!empty($_POST['password'])){	
						if(strlen($_POST['password']) >= 6){
							$is_banned = is_banned($is_user, NULL);
							if(!$is_banned['result']){
								$check_name = check_ids('name', false, $is_user);
								$check_pass = check_ids('password', $_POST['password'], $is_user);
								if($check_name && $check_pass){
									$_SESSION['name'] = $is_user;
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
					<label for="name_log">Pseudo :</label>
					<input type="text" name="name" class="form-control" placeholder="e-mail ou pseudo" maxlength="40" autofocus required>
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

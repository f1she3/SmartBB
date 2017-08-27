<?php

if(!isset($_SESSION['attempts'])){
	$_SESSION['attempts'] = 0;
}
$error = '';
$style = 'a';
if(isset($_POST['submit'])){
	$style = '';
	if(isset($_POST['email']) && !empty($_POST['email']) && is_string($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$email = $_POST['email'] = secure($_POST['email']);
		$email_hash = sha1($email);
		$is_user = is_user($email_hash);
		if($is_user != NULL){
			$user_infos = get_user_infos($is_user);
			if($email_hash === $user_infos['email']){
				$code = code_gen(9);
				set_recovery_code($code, $is_user);
				mail($is_user, get_project_name().' | réinitialiser votre mot de passe', 'Voici votre code : '.$code);
			}else{
				$error = 'adresse email incorrecte';
			}
		}else{
			$error = 'adresse email incorrecte';
			$_SESSION['attempts']++;
		}
	}else{
		$error = 'adresse email invalide';
	}
}
?>
<div class="page-header">
	<h3 class="text-center">Mot de passe oublié</h3>
</div>
<div class="col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
<?php
	
	if(!empty($error)){
		echo "<div class=\"alert alert-danger text-center\">
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
				<strong>Réinitialiser mon mot de passe</strong>
			</h3>
		</div>
		<div class="panel-body">
			<form method="POST">
				<div class="form-group">
					<label>Votre adresse email :</label>
					<input type="email" class="form-control" name="email" maxlength="40" 
						placeholder="exemple@exemple.com" autofocus required>
				</div>
				<button class="btn btn-primary center-block" name="submit">Confirmer</button>
			</form>
		</div>
	</div>
</div>

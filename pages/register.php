<?php

$error = '';
$style = 'a';
if(isset($_POST['reg_submit'])){
	$style = '';
	if(!empty($_POST['name'])){
		if(!empty($_POST['email'])){
			if(!empty($_POST['password'])){
				if(!empty($_POST['r_password'])){
					$name = $_POST['name'] = secure($_POST['name']);
					$ret_check_pattern_username = preg_match('#^[a-zA-Z0-9_@[\]éè-]+$#', $name);
					if($ret_check_pattern_username){
						if(strlen($name) >= 4 && strlen($name) <= 15){
							$email = $_POST['email'] = secure($_POST['email']);
							$ret_check_pattern_email = preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,20}\.[a-z]{2,4}$#', $email);
							if($ret_check_pattern_email){
								if(strlen($_POST['password']) >= 6){
									if($_POST['password'] == $_POST['r_password']){
										$ret_is_used_username = is_used('name', $name);
										if(!$ret_is_used_username){
											$email_hash = sha1($email);
											$ret_is_used_email = is_used('email', $email_hash);
											if(!$ret_is_used_email){
												$_POST['password'] = secure($_POST['password']);
												$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
												$_POST['r_password'] = secure($_POST['r_password']);
												register($name, $email_hash, $password);
												$_SESSION['name'] = $name;
												redirect('chat');
											}else{
											    $error = 'cette adresse email est déjà utilisée';
											}
										}else{
											$error = 'ce nom d\'utilisateur est déjà utilisé';
										}
									}else{
										$error = 'les mots de passe ne correspondent pas';
									}
								}else{
									$error = 'le mot de passe doit contenir au minimum 6 caractères';
								}
							}else{
								$error = 'l\'adresse email entrée est invalide';
							}
						}else{
							$error = 'le nom d\'utilisateur doit contenir au minimum 4 caractères';
						}
					}else{
						$error = 'le pseudo choisi est invalide';
					}
		    		}else{
					$error = 'veuillez répéter le mot de passe';
			    	}
			}else{
			    $error = 'veuillez saisir un mot de passe';
			}
		}else{
			$error = 'veuillez saisir votre adresse email';
		}
	}else{
		$error = 'veuillez saisir un pseudo';
	}
}
?>
<h2 class="text-center">Inscription</h2>
<div class="col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
<?php 

if(!empty($error)){
	echo "<div class='alert alert-danger errors medium-box center-block text-center'>
			<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> ".$error."
		</div>";
} 
if(!empty($style)){
	echo "<div class='alert alert-danger invisible'>".$style."</div>";
}
?>
</div>
<div class="col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><strong>S'inscrire</strong></h3>
	</div>
	<div class="panel-body">
		<form method="POST" action="">
			<div class="form-group">
				<label for="name_reg">Votre nom d'utilisateur :</label>
				<input class="form-control" placeholder="4 caractères minimum" name="name" type="text" maxlength="15" autofocus required>
			</div>
			<div class="form-group">
				<label for="email_reg">Votre adresse email :</label>
				<input class="form-control" placeholder="me@example.com" name="email" type="email" maxlength="40" required>
			</div>
			<div class="form-group">
				<label for="password_reg">Votre mot de passe :</label>
				<input class="form-control" placeholder="6 caractères minimum" name="password" type="password" maxlength="60" required>
			</div>
			<div class="form-group">
				<label for="r_password_reg">Répétez votre mot de passe :</label>
				<input class="form-control" placeholder="************" name="r_password" type="password" maxlength="60" required>
			</div>
			<button name="reg_submit" class="btn btn-success center-block">Inscription</button>
		</form>
	</div>
</div>

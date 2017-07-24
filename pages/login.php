<?php

$error = '';
$style = 'a';
if(isset($_POST['submit'])){
	$password = $_POST['password'] = secure($_POST['password']);
	$name = $_POST['name'] = secure($_POST['name']);
	$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
	$ret_find_name = find_name($name);
	$ret_check_name = check_ids('name', false, $ret_find_name);
	$ret_check_pass = check_ids('password', $_POST['password'], $ret_find_name);
	$style = '';
	if(!empty($name)){
		if(!empty($_POST['password'])){	
			if(strlen($_POST['password']) >= 6){
				if(!is_banned($ret_find_name)){
					if(check_ids('name', false, $ret_find_name) && check_ids('password', $_POST['password'], $ret_find_name)){
						$_SESSION['name'] = $ret_find_name;
						redirect(1);

					}else{
						$error = 'nom d\'utilisateur ou mot de passe incorrect';
					}
				
				}else{
					$ret_is_banned = is_banned($ret_find_name);
					$error = 'banni pour la raison suivante : "'.bb_decode($ret_is_banned['message']).'"';
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
	echo "<div class='alert alert-danger text-center'>
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
		<h3 class="panel-title"><strong>Se connecter</strong></h3>
	</div>
	<div class="panel-body">
		<form method="POST" action="">
			<div class="form-group">
				<label for="name_log">Pseudo :</label>
				<input class="form-control" placeholder="e-mail ou pseudo" name="name" type="text" maxlength="15" autofocus required>
			</div>
			<div class="form-group">
				<label for="password_log">Mot de passe :</label>
				<input class="form-control" placeholder="************" name="password" type="password" required>
			</div>
			<button name="submit" class="btn btn-primary center-block">Connexion</button>
		</form>
	</div>
</div>
</div>

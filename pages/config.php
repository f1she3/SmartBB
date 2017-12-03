<?php

$error = '';
$style = 'a';
if(isset($_POST['submit'])){
	$style = '';
	if(isset($_POST['old_password']) && !empty($_POST['old_password']) && is_string($_POST['old_password'])){
		$old_password = $_POST['old_password'] = secure($_POST['old_password']);
		if(isset($_POST['new_password']) && !empty($_POST['new_password']) && is_string($_POST['new_password'])){
			$new_password = $_POST['new_password'] = secure($_POST['new_password']);
			if(isset($_POST['repeat_password']) && !empty($_POST['repeat_password']) && is_string($_POST['repeat_password'])){
				$repeat_password = $_POST['repeat_password'] = secure($_POST['repeat_password']);
				if($repeat_password === $new_password){
					if(check_ids('password', $old_password, $_SESSION['name'])){
					}else{
						$error = 'l\'ancien mot de passe est incorrect';
					}
				}else{
					$error = 'les nouveaux mots de passe sont différents';
				}
			}else{
				$error = 'veuillez répéter votre nouveau mot de passe';
			}
		}else{
			$error = 'veuillez saisir votre nouveau mot de passe';
		}
	}else{
		$error = 'veuillez saisir votre mot de passe actuel';
	}
}
?>
<div class="page-header">
	<h3 class="text-center">Mon compte</h3>
</div>
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
				<strong>Changer mon mot de passe</strong>
			</h3>
		</div>
		<div class="panel-body">
			<form method="POST" action="">
				<div class="form-group">
					<label>Mot de passe actuel :</label>
					<input type="password" class="form-control" name="old_password" maxlength="64" placeholder="************" autofocus required>
					<label>Nouveau mot de passe :</label>
					<input type="password" class="form-control" name="new_password" maxlength="64" placeholder="************" required>
					<label>Répeter votre mot de passe :</label>
					<input type="password" class="form-control" name="repeat_password" maxlength="64" placeholder="************" required>
				</div>
				<button class="btn btn-primary center-block" name="submit">
					<span class="glyphicon glyphicon-ok"></span>
					Confirmer
				</button>
			</form>
		</div>
	</div>
</div>

<?php

$content = '';
$style = 'a';
$type = 'danger';
$glyphicon = 'exclamation-sign';
if(isset($_POST['submit'])){
	$style = '';
	if(isset($_POST['old_password']) && !empty($_POST['old_password']) && is_string($_POST['old_password'])){
		$old_password = $_POST['old_password'] = secure($_POST['old_password']);
		if(check_ids('password', $old_password, $_SESSION['name'])){
			if(isset($_POST['new_password']) && !empty($_POST['new_password']) && is_string($_POST['new_password'])){
				$new_password = $_POST['new_password'] = secure($_POST['new_password']);
				if(mb_strlen($new_password) >= 6){
					if(mb_strlen($new_password) <= 64){
						if(isset($_POST['repeat_password']) && !empty($_POST['repeat_password']) && is_string($_POST['repeat_password'])){
							$repeat_password = $_POST['repeat_password'] = secure($_POST['repeat_password']);
							if($repeat_password === $new_password){
								if($new_password !== $old_password){
									$new_password = password_hash($new_password, PASSWORD_DEFAULT);
									update_password($_SESSION['name'], $new_password);
									$type = 'success';
									$content = 'le mot de passe a été changé avec succès';
									$glyphicon = 'ok';
								}else{
									$content = 'veuillez saisir un nouveau mot de passe différent de l\'actuel';
								}
							}else{
								$content = 'les nouveaux mots de passe sont différents';
							}
						}else{
							$content = 'veuillez répéter votre nouveau mot de passe';
						}
					}else{
						$content = 'le nouveau mot de passe est trop long (> 64 caractères)';
					}
				}else{
					$content = 'le nouveau mot de passe est trop court (< 6 caractères)';
				}
			}else{
				$content = 'veuillez saisir votre nouveau mot de passe';
			}
		}else{
			$content = 'le mot de passe actuel est incorrect';
		}
	}else{
		$content = 'veuillez saisir votre mot de passe actuel';
	}
}
?>
<div class="icon">
	<a href="<?= get_base_url() ?>delete">
		<span class="glyphicon glyphicon-trash"></span>
	</a>
</div>
<h3 class="text-center">Mon compte</h3>
<div class="col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
<?php

if(!empty($content)){
	echo 	"<div class=\"alert alert-".$type." text-center\">
			<span class=\"glyphicon glyphicon-".$glyphicon."\" aria-hidden=\"true\"></span> ".$content."
		</div>";
}
if(!empty($style)){
	echo "<div class=\"alert invisible\">".$style."</div>";
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
					<label for="old_password">Mot de passe actuel :</label>
					<input type="password" name="old_password" class="form-control" placeholder="************" maxlength="64" autofocus required>
				</div><br>
				<div class="form-group">
					<label for="new_password">Nouveau mot de passe :</label>
					<input type="password" name="new_password" class="form-control" placeholder="************" maxlength="64" required>
				</div>
				<div class="form-group">
					<label for="input_log">Répéter le mot de passe :</label>
					<input type="password" name="repeat_password" class="form-control" placeholder="************" maxlength="64" required>
				</div>
				<button name="submit" class="btn btn-primary center-block">
					<span class="glyphicon glyphicon-ok"></span>
					Confirmer
				</button>
			</form>
		</div>
	</div>
</div>

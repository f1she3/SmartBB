<?php

// Pseudo random strings of max 64 chars
function code_gen($max){
	if($max > 64){
		$max = 64;
	}
	// Decrement, so that there are exactly $max chars
	$max--;
	$first = pow(10, $max);
	$last = '';
	for($i = 0; $i <= $max; $i++){
		$last .= '9';
	}
	// Pow returns float
	$first = intval($first);
	// String to int
	$last = intval($last);
	$tmp_code = random_int($first, $last);
	$code = sha1($tmp_code);
	// Increment for correct value at substr
	$max++;
	$code = substr($code, 0, $max);

	return $code;
}
function set_recovery_codes($user, $recovery_code, $token){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'UPDATE users SET recovery_code = ?, token = ? WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 'sss', $recovery_code, $token, $user);
	mysqli_stmt_execute($query);
}
function is_token($token){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT id FROM users WHERE BINARY token = ?');
	mysqli_stmt_bind_param($query, 's', $token);
	mysqli_stmt_execute($query);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;
	}
	if($i === 0){
		return false;
	}else{
		return true;
	}
}
function check_codes($token, $code){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY token = ? AND BINARY recovery_code = ?');
	mysqli_stmt_bind_param($query, 'ss', $token, $code);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $name);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;
	}
	if($i === 0){
		return NULL;
	}else{
		return $name;
	}
}
function display_confirm_email_form($type, $error, $style){
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">Mot de passe oublié</h3>
		</div>
		<div class=\"col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3\">";
	if(!empty($error)){
		echo 	"<div class=\"alert alert-".$type." text-center\">
				<span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span> ".$error."
			</div>";
	}
	if(!empty($style)){
		echo "<div class=\"alert alert-".$type." invisible\">".$style."</div>";
	}
	echo	"</div>
		<div class=\"col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3\">
			<div class=\"panel panel-default\">
				<div class=\"panel-heading\">
					<h3 class=\"panel-title\">
						<strong>Réinitialiser mon mot de passe</strong>
					</h3>
				</div>
				<div class=\"panel-body\">
					<form method=\"POST\">
						<div class=\"form-group\">
							<label>Votre adresse email :</label>
							<input type=\"email\" class=\"form-control\" name=\"email\" maxlength=\"40\" 
								placeholder=\"exemple@exemple.com\" autofocus required>
						</div>
						<button class=\"btn btn-primary\" name=\"submit_email\">Confirmer</button>
						<a class=\"btn btn-default\" href=\"".get_root_url().get_base_url()."login\">
							<span class=\"glyphicon glyphicon-remove\"></span>
							Annuler
						</a>
					</form>
				</div>
			</div>
		</div>";
}
function display_confirm_code_form($type, $error){
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">Votre code de sécurité</h3>
		</div>
		<div class=\"col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3\">";
	if(!empty($error)){
		echo 	"<div class=\"alert alert-".$type." text-center\">
				<span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span> ".$error."
			</div>";
	}
	if(!empty($style)){
		echo 	"<div class=\"alert alert-".$type." invisible\">".$style."</div>";
	}
	echo	"</div>
		<div class=\"col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3\">
			<div class=\"panel panel-default\">
				<div class=\"panel-heading\">
					<h3 class=\"panel-title\">
						<strong>Code de sécurité</strong>
					</h3>
				</div>
				<div class=\"panel-body\">
					<form method=\"POST\">
						<div class=\"form-group\">
							<label>Veuillez saisir votre code :</label>
							<input type=\"text\" class=\"form-control\" name=\"code\" maxlength=\"32\" 
								placeholder=\"*****************\" autofocus required>
						</div>
						<button class=\"btn btn-success center-block\" name=\"submit_code\">
						<span class=\"glyphicon glyphicon-lock\"></span>
							Valider
						</button>
					</form>
				</div>
			</div>
		</div>";
}
function display_new_password_form($style, $error){
	$type = 'danger';
	echo 	"<div class=\"page-header\">
			<h3 class=\"text-center\">Réinitialiser votre mot de passe</h3>
		</div>
		<div class=\"col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3\">";
	if(!empty($error)){
		echo 	"<div class=\"alert alert-".$type." text-center\">
				<span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span> ".$error."
			</div>";
	}
	if(!empty($style)){
		echo 	"<div class=\"alert alert-".$type." invisible\">".$style."</div>";
	}
	echo	"</div>
		<div class=\"col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3\">
			<div class=\"panel panel-default\">
				<div class=\"panel-heading\">
					<h3 class=\"panel-title\">
						<strong>Nouveau mot de passe</strong>
					</h3>
				</div>
				<div class=\"panel-body\">
					<form method=\"POST\">
						<div class=\"form-group\">
							<label>Nouveau mot de passe :</label>
							<input type=\"password\" class=\"form-control\" name=\"new_password\" maxlength=\"64\" 
								placeholder=\"*****************\" autofocus required>
						</div>
						<div class=\"form-group\">
							<label>Répétez le mot de passe :</label>
							<input type=\"password\" class=\"form-control\" name=\"repeat_new_password\" maxlength=\"64\" 
								placeholder=\"*****************\" autofocus required>
						</div>
						<button class=\"btn btn-primary center-block\" name=\"submit_password\">
							<span class=\"glyphicon glyphicon-ok\"></span>
							Réinitialiser
						</button>
					</form>
				</div>
			</div>
		</div>";
}
function finish_recovery_process($username, $new_password){
	$mysqli = get_link();
	$var = 'NULL';
	$query = mysqli_prepare($mysqli, 'UPDATE users SET password = ?, recovery_code = ?, token = ? WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 'ssss', $new_password, $var, $var, $username);
	mysqli_stmt_execute($query);
	$_SESSION['code_validate'] = 0;
	$_SESSION['token_validate'] = 0;
	$_SESSION['tmp_name'] = 0;
}

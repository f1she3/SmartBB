<?php

function user_infos($username){
	$mysqli = get_link();
	// Message counter
	$query = mysqli_prepare($mysqli, 'SELECT id FROM articles WHERE BINARY author = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
	$i = 0;
	while(mysqli_stmt_fetch($query)){
		$i++;
	}
	$result['article_count'] = $i;
	// Friend counter	
	$query = mysqli_prepare($mysqli, 'SELECT id FROM friends WHERE (BINARY sender = ? OR BINARY contact = ?) AND validate = 1');
	mysqli_stmt_bind_param($query, 'ss', $username, $username);
	mysqli_stmt_execute($query);
	$x = 0;
	while(mysqli_stmt_fetch($query)){
		$x++;
	}
	$result['friend_count'] = $x;
	$query = mysqli_prepare($mysqli, 'SELECT rank, reg_date FROM users WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $result['rank'], $result['reg_date']);
	mysqli_stmt_fetch($query);

	return $result;		
}
// $flag 
// true : display current user's profile
// false : display another user's profile
function display_user_infos($username, $flag){
	$mysqli = get_link();
	$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name = ?');
	mysqli_stmt_bind_param($query, 's', $username);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $name);
	mysqli_stmt_fetch($query);
	$ranks = get_rank_list();
	$ret_user_infos = user_infos($username);
	$ret_reg_date = date_create($ret_user_infos['reg_date']);
	$ret_reg_date = date_format($ret_reg_date, '\l\e j/m Y');
	if($ret_user_infos['article_count'] > 1){
		$msg_text = 'Articles postés : ';
	
	}else{
		$msg_text = 'Article posté : ';
	}
	if($ret_user_infos['friend_count'] > 1){
		$friend_text = 'Amis : ';
	
	}else{
		$friend_text = 'Ami : ';
	}
	if(!$flag){
		echo "<div class=\"page-header\">
				<h3 class=\"text-center\">Informations</h3>
			</div>
			<h3 class=\"text-center\">".$username."</h3>
			<pre>
				<ul class=\"\">
					<li><h4>Pseudo : ".$username."</h4></li>
					<li><h4>Inscription : ".$ret_reg_date."</h4></li>
					<li><h4>".$msg_text." ".$ret_user_infos['article_count']."</h4></li>
					<li><h4>Grade : ".$ranks[$ret_user_infos['rank']]."</h4></li>
				</ul>
			</pre>";
		$user_rank = get_rank($username);
		$my_rank = get_rank($_SESSION['name']);
		if($my_rank > 1 && $my_rank > $user_rank){
			echo 	"<form method=\"POST\">";
			$ret_is_banned = is_banned($username);
			if($ret_is_banned){
				echo 	"<div class=\"col-sm-4\">
						<blockquote>".$ret_is_banned['message']."
							<footer>Bannit par <i>".$ret_is_banned['banned_by']."</i></footer>
						</blockquote>";
				if($my_rank == $ranks['max']){
					echo 	"<button name=\"submit_deban\" class=\"btn btn-warning\">
							Débannir
						</button>";
				}else{
					if($my_rank > get_rank($ret_is_banned['banned_by'])){
						echo 	"<button name=\"submit_deban\" class=\"btn btn-warning\">
								Débannir
							</button>";
					}
				}
				echo	"</div>";
			}else{
				echo 	"<div class=\"col-md-4 col-sm-4 col-xs-6\">
						<div class=\"form-group\">
							<input type=\"text\" name=\"ban_message\" placeholder=\"Raison\" maxlength=\"50\" class=\"form-control\">
						</div>
						<button name=\"submit_ban\" class=\"btn btn-danger\">Bannir</button>
					</div>";
			}
			echo 		"<div class=\"col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-6\">
						<div class=\"form-group\">
							<select name=\"rank\" class=\"form-control\">";
			if($my_rank == $ranks['max']){
				// Only one super admin
				$my_rank = $my_rank - 1;	
			}
			for($i = 0; $i <= $my_rank; $i++){
				if($i == $user_rank){
					echo 			"<option value=\"".$i."\" selected>".$ranks[$i]."</option>";
					
				}else{
					echo 			"<option value=\"".$i."\">".$ranks[$i]."</option>";
				}
			}
			echo 				"</select>
						</div>
						<button name=\"set_rank\" class=\"btn btn-info\">Changer</button>
					</div>
				</form>";
		}
	}else{
		echo 	"<div class=\"icon\">
				<a href=\"".constant('BASE_URL')."delete\">
					<span class=\"glyphicon glyphicon-trash\"></span>
				</a>
			</div>";
		echo "<div class=\"page-header\">
				<h3 class=\"text-center\">Informations</h3>
			</div>
			<h3 class=\"text-center\">".$username."</h3>
			<pre class=\"col-sm-8 col-sm-offset-2\">
				<ul>
					<li style=\"font-size:18px\">Pseudo : ".$username."</li>
					<li style=\"font-size:18px\">Inscription : ".$ret_reg_date."</li>
					<li style=\"font-size:18px\">".$friend_text." ".$ret_user_infos['friend_count']."</li>
					<li style=\"font-size:18px\">".$msg_text." ".$ret_user_infos['article_count']."</li>
					<li style=\"font-size:18px\">Grade : ".$ranks[$ret_user_infos['rank']]."</li>
					<li style=\"font-size:18px\">Adresse IP actuelle : ".$_SERVER['REMOTE_ADDR']."</li>
				</ul>
			</pre>";
	}
}

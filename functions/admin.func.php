<?php

function server_infos(){
	$mysqli = get_link();
	$query = mysqli_query($mysqli, 'SELECT id FROM users');
	$i = 0;
	while($tmp = mysqli_fetch_assoc($query)){
		$i++;	
	}
	$result = array();
	$result['member_count'] = $i;
	$query = mysqli_query($mysqli, 'SELECT id FROM articles');
	$i = 0;
	while($tmp = mysqli_fetch_assoc($query)){
		$i++;	
	}
	$result['articles_count'] = $i;
	$query = mysqli_query($mysqli, 'SELECT name FROM users WHERE rank = 1');
	$i = 0;
	while($tmp = mysqli_fetch_assoc($query)){
		$result['moderator_name'][$i] = $tmp['name'];
		$i++;
	}
	$result['moderator_count'] = $i;
	$query = mysqli_query($mysqli, 'SELECT name FROM users WHERE rank = 2 OR rank = 3');
	$i = 0;
	while($tmp = mysqli_fetch_assoc($query)){
		$result['administrator_name'][$i] = $tmp['name'];
		$i++;
	}
	$result['administrator_count'] = $i;

	return $result;
}
function display_server_infos(){
	echo	"<div class=\"page-header\">
			<h3 class=\"text-center\">Administration</h3>
		</div>
		<form method=\"POST\" class=\"col-md-6 col-md-offset-3\">
			<div class=\"input-group\">
				<input type=\"text\" name=\"user\" list=\"search_users\" class=\"form-control\" maxlength=\"25\" autofocus>
				<span class=\"input-group-btn\">
					<button class=\"btn btn-default\" type=\"submit\">
						<span class=\"glyphicon glyphicon-search\"></span>
					</button>
				</span>
			</div>
		</form>
		<datalist id=\"search_users\">";
	$ret_datalist_options = datalist_options($_SESSION['name'], false);
	$i = 0;
	while(isset($ret_datalist_options[$i])){
		echo $ret_datalist_options[$i];
		$i++;
	}
	echo "</datalist>";
	$infos = server_infos();
	if($infos['member_count'] > 1){
		$member_txt = 'Membres';
	}else{
		$member_txt = 'Membre';
	}
	if($infos['moderator_count'] > 1){
		$modo_txt = 'Modérateurs';
	}else{
		$modo_txt = 'Modérateur';
	}
	if($infos['administrator_count'] > 1){
		$admin_txt = 'Administrateurs';
	}else{
		$admin_txt = 'Administrateur';
	}
	$modo_names = '';
	for($i = 0; isset($infos['moderator_name'][$i]); $i++){
		$modo_names .= '- '.$infos['moderator_name'][$i]."\n";
	}
	$admin_names = '';
	for($i = 0; isset($infos['administrator_name'][$i]); $i++){
		$admin_names .= '- '.$infos['administrator_name'][$i]."\n";
	}
	echo 	"<div class=\"col-md-8 col-md-offset-2\">
			<pre>
				<ul>
					<li><h4>".$member_txt." : ".$infos['member_count']."</h4></li>
					<li><h4>Articles postés : ".$infos['articles_count']."</h4></li>
					<li><h4><abbr title=\"".$modo_names."\">".$modo_txt."</abbr> : ".$infos['moderator_count']."</h4></li>
					<li><h4><abbr title=\"".$admin_names."\">".$admin_txt."</abbr> : ".$infos['administrator_count']."</h4></li>
				</ul>
			</pre>
		</div>";
}

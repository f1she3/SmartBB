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
		$text = 'Membres';
	
	}else{
		$text = 'Membre';
	}
	echo 	"<div class=\"col-md-8 col-md-offset-2\">
			<pre>
				<ul>
					<li><h4>".$text." : ".$infos['member_count']."</h4></li>
					<li><h4>Articles postés : ".$infos['articles_count']."</h4></li>
				</ul>
			</pre>
		</div>";
}

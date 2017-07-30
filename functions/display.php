<?php

function set_error($title, $icon, $content, $location){
	if($title){
		$title = "<div class=\"page-header\">
				<h2 class=\"text-center\">".$title."</h2>
				</div>";
	
	}else{
		$title = '';
	}
	if($icon){
		if($icon == 'error'){
			$icon = "<img src=\"../css/images/emojis/e_s.svg\" height=\"40\" width=\"40\" class=\"center-block\">
				<h4 class=\"text-center\"><span class=\"glyphicon glyphicon-".$icon."\"></span></h4>";
		
		}else{
			$icon = "<h4 class=\"text-center\"><span class=\"glyphicon glyphicon-".$icon."\"></span></h4>";
		}
	
	}else{
		$icon = '';
	}
	if($content){
		$content = 	"<h4 class=\"text-center\">".$content."</h4>";
	}else{
		$content = '';
	}
	if($location){
		$location = "<a href=\"".$_SESSION['host'].constant('BASE_URL').$location."\">
					<img src=\"".$_SESSION['host']."/css/images/home.svg\" height=\"75\" width=\"75\" class=\"center-block\">
				</a>";
	}else{
		$location = '';
	}
	die($title.$icon.$content.$location.file_get_contents('content/footer-1.html'));
}
function datalist_options($username, $rank_restriction){
	$mysqli = get_link();
	if($rank_restriction){
		$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name != ? AND rank < ?');
		mysqli_stmt_bind_param($query, 'ss', $username, $rank_restriction);

	}else{
		$query = mysqli_prepare($mysqli, 'SELECT name FROM users WHERE BINARY name != ?');
		mysqli_stmt_bind_param($query, 's', $username);
	}
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query, $r);
	$i = 0;
	$result = array();
	while(mysqli_stmt_fetch($query)){
		$result[$i] = "<option value=\"".$r."\">\n"; $i++;
	}
	
	return $result;
}

<?php

if(isset($_GET['id']) && !empty($_GET['id']) && ctype_digit($_GET['id'])){
	if(isset($_GET['pid']) && !empty($_GET['pid']) && ctype_digit($_GET['pid'])){
		$pid = $_GET['pid'] = secure($_GET['pid']);
	}else{
		$pid = 1;	
	}
	$id = $_GET['id'] = secure($_GET['id']);
	if(isset($_POST['submit_comment'])){
		if(isset($_POST['comment']) && !empty($_POST['comment']) && is_string($_POST['comment']) && strlen($_POST['comment']) <= 500){
			$comment = $_POST['comment'] = secure($_POST['comment']);
			post_comment($id, $_SESSION['name'], $comment, 0);
			display_article($id, $pid);
		}else{
			if($pid != 1){
				display_comments($id, $pid);
			}else{
				display_article($id, $pid);
			}
		}
	}else{
		if($pid != 1){
			display_comments($id, $pid);
		}else{
			display_article($id, $pid);
		}
	}
}else{
	set_error('Erreur 404', 'zoom-out', 'L\'article que vous recherchez n\'éxiste pas', 'home');
}

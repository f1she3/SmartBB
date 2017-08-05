<?php

if(isset($_GET['id']) && !empty($_GET['id']) && ctype_digit($_GET['id'])){
	if(isset($_GET['pid']) && !empty($_GET['pid']) && ctype_digit($_GET['pid'])){
		$pid = $_GET['pid'] = secure($_GET['pid']);
	}else{
		$pid = 1;	
	}
	$id = $_GET['id'] = secure($_GET['id']);
	if(isset($_POST['answer']) && !empty($_POST['answer']) && ctype_digit($_POST['answer'])){
		$answer = $_POST['answer'] = secure($_POST['answer']);
		if(is_comment($answer)){
			redirect('article&id='.$id.'&reply='.$answer);
		}else{
			set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id);
		}
	}
	$reply_to = 0;
	if(isset($_GET['reply'])){
		if(!empty($_GET['reply']) && ctype_digit($_GET['reply'])){
			$reply_to = $_GET['reply'] = secure($_GET['reply']);
			if(!is_comment($reply_to)){
				set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id);
			}
		}else{
			set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id);
		}
	}
	if(isset($_POST['submit_comment'])){
		if(isset($_POST['comment']) && !empty($_POST['comment']) && is_string($_POST['comment']) && strlen($_POST['comment']) <= 500){
				$comment = $_POST['comment'] = secure($_POST['comment']);
				post_comment($id, $_SESSION['name'], $comment, $reply_to);
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

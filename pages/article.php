<?php

if(isset($_GET['id']) && !empty($_GET['id']) && ctype_digit($_GET['id'])){
	if(isset($_GET['pid']) && !empty($_GET['pid']) && ctype_digit($_GET['pid'])){
		$pid = $_GET['pid'] = secure($_GET['pid']);
	}else{
		$pid = 1;	
	}
	$id = $_GET['id'] = secure($_GET['id']);
	if(!is_article($id)){
		set_error('Erreur', 'zoom-out', 'l\'article que vous recherchez n\'éxiste pas', 'home');
	}
	$article_infos = article_infos($id);
	$my_rank = get_rank($_SESSION['name']);
	$category_infos = category_infos($article_infos['category']);
	if($my_rank < $category_infos['access_restriction']){
		set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour accéder à cet article', 'home');
	}
	$reply_to = 0;
	if(isset($_POST['reply']) && !empty($_POST['reply']) && ctype_digit($_POST['reply'])){
		if($my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour commenter cet article', 'article&id='.$id.'&pid='.$pid);
		}
		$reply = $_POST['reply'] = secure($_POST['reply']);
		if(is_comment($reply)){
			display_reply_form($id, $reply);
		}else{
			set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id.'&pid='.$pid);
		}
	}else{
		if(isset($_POST['submit_answer'])){
			if($my_rank < $category_infos['post_restriction']){
				set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour commenter cet article', 'article&id='.$id.'&pid='.$pid);
			}
			if(isset($_POST['parent_comment']) && !empty($_POST['parent_comment']) && ctype_digit($_POST['parent_comment'])){
				$parent_comment = $_POST['parent_comment'] = secure($_POST['parent_comment']);
				if(is_comment($parent_comment)){
					if(isset($_POST['answer']) && !empty($_POST['answer']) && is_string($_POST['answer']) && strlen($_POST['answer']) <= 500){
						$answer = $_POST['answer'] = secure($_POST['answer']);
						post_comment($id, $_SESSION['name'], $answer, $parent_comment);
						display_article($id, $pid);
					}
				}else{
					set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id.'&pid='.$pid);
				}
			}
		}else if(isset($_POST['submit_comment'])){
				if($my_rank < $category_infos['post_restriction']){
					set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour commenter cet article', 'article&id='.$id.'&pid='.$pid);
				}
				if(isset($_POST['comment']) && !empty($_POST['comment']) && is_string($_POST['comment']) && strlen($_POST['comment']) <= 500){
						$comment = $_POST['comment'] = secure($_POST['comment']);
						post_comment($id, $_SESSION['name'], $comment, $reply_to);
						display_article($id, $pid);
				}else{
					if($pid != 1){
						display_comment(false, $id, $pid);
					}else{
						display_article($id, $pid);
					}
				}
			}else{
				if($pid != 1){
					display_comment(false, $id, $pid);
				}else{
					display_article($id, $pid);
				}
			}
	    }
}else{
	set_error('Erreur 404', 'zoom-out', 'L\'article que vous recherchez n\'éxiste pas', 'home');
}

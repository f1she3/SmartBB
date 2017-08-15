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
	$ranks = get_rank_list();
	$article_infos = get_article_infos($id);
	$author_rank = get_rank($article_infos['author']);
	$my_rank = get_rank($_SESSION['name']);
	$category_infos = get_category_infos($article_infos['category']);
	if($my_rank < $category_infos['access_restriction']){
		set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour accéder à cet article', 'home');
	}
	$reply_to = 0;
	if(isset($_POST['reply']) && !empty($_POST['reply']) && ctype_digit($_POST['reply'])){
		if($article_infos['status'] === 1){
			set_error('Erreur', 'exclamation-sign', 'Cet article est fermé', 'article&id='.$id.'&pid='.$pid);
		}
		if($my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour commenter cet article', 'article&id='.$id.'&pid='.$pid);
		}
		$reply = $_POST['reply'] = secure($_POST['reply']);
		if(is_comment($reply)){
			display_reply_form($id, $reply);
		}else{
			set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id.'&pid='.$pid);
		}
	}else if(isset($_POST['submit_answer'])){
		if($article_infos['status'] === 1){
			set_error('Erreur', 'exclamation-sign', 'Cet article est fermé', 'article&id='.$id.'&pid='.$pid);
		}
		if($my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour commenter cet article', 'article&id='.$id.'&pid='.$pid);
		}
		if(isset($_POST['parent_comment']) && !empty($_POST['parent_comment']) && ctype_digit($_POST['parent_comment'])){
			$parent_comment = $_POST['parent_comment'] = secure($_POST['parent_comment']);
			if(is_comment($parent_comment)){
				if(isset($_POST['answer']) && !empty($_POST['answer']) && is_string($_POST['answer']) && mb_strlen($_POST['answer']) <= 500){
					$answer = $_POST['answer'] = secure($_POST['answer']);
					post_comment($id, $_SESSION['name'], $answer, $parent_comment);
					//display_article($id, $pid);
				}
			}else{
				set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id.'&pid='.$pid);
			}
		}
	}else if(isset($_POST['submit_comment'])){
		if($article_infos['status']){
			set_error('Erreur', 'exclamation-sign', 'Cet article est fermé', 'article&id='.$id.'&pid='.$pid);
		}
		if($my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour commenter cet article', 'article&id='.$id.'&pid='.$pid);
		}
		if(isset($_POST['comment']) && !empty($_POST['comment']) && is_string($_POST['comment']) && mb_strlen($_POST['comment']) <= 500){
				$comment = $_POST['comment'] = secure($_POST['comment']);
				post_comment($id, $_SESSION['name'], $comment, $reply_to);
				display_comment(false, $id, $pid);
		}else if($pid != 1){
			display_comment(false, $id, $pid);
		}else{
			display_article($id, $pid);
		}
	}else if(isset($_POST['close_article'])){
		if($my_rank < $category_infos['rank_owner'] || ($my_rank <= $author_rank && $my_rank != $ranks['max'])){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		if($article_infos['status'] === 1){
			set_error('Erreur', 'exclamation-sign', 'Cet article est déjà fermé', 'article&id='.$id);
		}
		set_article_status($id, 1);
		display_article($id, false);
		display_comment(false, $id, $pid);
	}else if(isset($_POST['open_article'])){
		if($my_rank < $category_infos['rank_owner'] || ($my_rank <= $author_rank && $my_rank != $ranks['max'])){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		if($article_infos['status'] === 0){
			set_error('Erreur', 'exclamation-sign', 'Cet article est déjà ouvert', 'article&id='.$id);
		}
		set_article_status($id, 0);
		display_article($id, false);
		display_comment(false, $id, $pid);
	}else if(isset($_POST['edit_article'])){
		if($article_infos['author'] != $_SESSION['name'] || $my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		display_article_edition_form($id);
	}else if(isset($_POST['submit_article_edition'])){
		if(!isset($_POST['new_article_title']) || empty($_POST['new_article_title']) || !is_string($_POST['new_article_title']) || mb_strlen($_POST['new_article_title']) > 100){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec le nouveau titre de l\'article', 'article&id='.$id);
		}
		if(!isset($_POST['new_article_content']) || empty($_POST['new_article_content']) || !is_string($_POST['new_article_content']) || mb_strlen($_POST['new_article_content']) > 1000){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec le nouveau contenu de l\'article', 'article&id='.$id);
		}
		$new_article_title = $_POST['new_article_title'] = secure($_POST['new_article_title']);
		$new_article_content = $_POST['new_article_content'] = secure($_POST['new_article_content']);
		if($new_article_title != $article_infos['title']){
			update_article($id, $new_article_title, $new_article_content);
		}else if($new_article_content != $article_infos['content']){
			update_article($id, $new_article_title, $new_article_content);
		}
		display_article($id, $pid);
	}else{
		if($pid != 1){
			display_comment(false, $id, $pid);
		}else{
			display_article($id, $pid);
		}
	}
}else{
	set_error('Erreur 404', 'zoom-out', 'L\'article que vous recherchez n\'éxiste pas', 'home');
}

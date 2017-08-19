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
			if(!is_comment($parent_comment)){
				set_error('Erreur', 'zoom-out', 'Ce commentaire n\'éxiste pas', 'article&id='.$id.'&pid='.$pid);
			}
			if(isset($_POST['answer']) && !empty($_POST['answer']) && is_string($_POST['answer']) && mb_strlen($_POST['answer']) <= 500){
				$answer = $_POST['answer'] = secure($_POST['answer']);
				post_comment($id, $_SESSION['name'], $answer, $parent_comment);
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
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour 
				effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		if($article_infos['status'] === 1){
			set_error('Erreur', 'exclamation-sign', 'Cet article est déjà fermé', 'article&id='.$id);
		}
		set_article_status($id, 1);
		display_article($id, false);
		display_comment(false, $id, $pid);
	}else if(isset($_POST['open_article'])){
		if($my_rank < $category_infos['rank_owner'] || ($my_rank <= $author_rank && $my_rank != $ranks['max'])){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour 
				effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		if($article_infos['status'] === 0){
			set_error('Erreur', 'exclamation-sign', 'Cet article est déjà ouvert', 'article&id='.$id);
		}
		set_article_status($id, 0);
		display_article($id, false);
		display_comment(false, $id, $pid);
	}else if(isset($_POST['edit_article'])){
		if(($article_infos['author'] != $_SESSION['name'] && $my_rank < $category_infos['rank_owner']) || 
			$my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour 
				effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		display_article_edition_form($id, $_SESSION['name']);
	}else if(isset($_POST['edit_comment'])){
		$edit_comment = $_POST['edit_comment'] = secure($_POST['edit_comment']);
		if(!is_comment($edit_comment)){
			set_error('Erreur', 'exclamation-sign', 'Ce commentaire n\'éxiste pas', 'article&id='.$id);
		}
		$comment_infos = get_comment_infos($edit_comment);
		if($comment_infos['author'] != $_SESSION['name'] || $my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour 
				effectuer cette action', 'article&id='.$id);
		}
		display_comment_edition_form($edit_comment);
	}else if(isset($_POST['submit_article_edition'])){
		if($_SESSION['name'] != $article_infos['author']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour 
				effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		if($my_rank < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour 
				effectuer cette action', 'article&id='.$id.'&pid='.$pid);
		}
		if(!isset($_POST['new_article_title']) || empty($_POST['new_article_title']) || 
			!is_string($_POST['new_article_title']) || mb_strlen($_POST['new_article_title']) > 100){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec le nouveau titre de l\'article', 'article&id='.$id);
		}
		if(!isset($_POST['new_article_content']) || empty($_POST['new_article_content']) || 
			!is_string($_POST['new_article_content']) || mb_strlen($_POST['new_article_content']) > 1000){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec le nouveau contenu de l\'article', 'article&id='.$id);
		}
		$pin = NULL;
		if(isset($_POST['pin'])){
			$pin = 1;
		}else{
			$pin = 0;
		}
		$new_article_title = $_POST['new_article_title'] = secure($_POST['new_article_title']);
		$new_article_content = $_POST['new_article_content'] = secure($_POST['new_article_content']);
		if($new_article_title != $article_infos['title']){
			update_article($id, $new_article_title, $new_article_content, false, $pin);
		}else if($new_article_content != $article_infos['content']){
			update_article($id, $new_article_title, $new_article_content, false, $pin);
		}else if(!is_null($pin)){
			update_article($id, false, false, false, $pin);
		}
		display_article($id, $pid);
	}else if(isset($_POST['submit_move_article'])){
		if($my_rank < $category_infos['rank_owner']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour effectuer cette action', 'article&id='.$id);
		}
		if(!isset($_POST['move_article']) || empty($_POST['move_article']) || !is_string($_POST['move_article'])){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec le nom de la nouvelle catégorie', 'article&id='.$id);
		}
		$move_article = $_POST['move_article'] = secure($_POST['move_article']);
		if(!is_category($move_article)){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec le nom de la nouvelle catégorie', 'article&id='.$id);
		}
		$new_cat_infos = get_category_infos($move_article);
		if($my_rank < $new_cat_infos['rank_owner']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour effectuer cette action', 'article&id='.$id);
		}
		update_article($id, false, false, $move_article);
		display_article($id, $pid);
	}else if(isset($_POST['submit_comment_edition'])){
		$submit_comment_edition = $_POST['submit_comment_edition'] = secure($_POST['submit_comment_edition']);
		if(!is_comment($submit_comment_edition)){
			set_error('Erreur', 'exclamation-sign', 'Erreur lors de l\'édition du commentaire', 'article&id='.$id);
		}
		$comment_infos = get_comment_infos($submit_comment_edition);
		if($comment_infos['author'] != $_SESSION['name'] || get_rank($comment_infos['author']) < $category_infos['post_restriction']){
			set_error('Erreur', 'exclamation-sign', 'Vous n\'avez pas les droits nécessaires pour effectuer cette action', 'article&id='.$id);
		}
		if(!isset($_POST['new_content']) || empty($_POST['new_content']) || !is_string($_POST['new_content'])){
			set_error('Erreur', 'exclamation-sign', 'Erreur avec le nouveau contenu du commentaire', 'article&id='.$id);
		}
		$new_content = $_POST['new_content'] = secure($_POST['new_content']);
		update_comment($submit_comment_edition, $new_content);
		display_article($comment_infos['parent_id'], 1);
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


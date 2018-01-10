<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Forum francophone de cyber-sécurité">
		<meta name="author" content="f1she3">
		<link rel="icon" href="<?= get_root_url().'/css/images/favicon.ico' ?>">
		<title>
			<?= $title; ?>
		</title>
		<link href="<?= get_root_url().'/css/bootstrap.min.css' ?>" rel="stylesheet">
		<link href="<?= get_root_url().'/css/style.css' ?>" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar navbar-default navbar-inverse navbar-static-top" role="navigation">
			<div class="container-fluid">
				<input type="checkbox" id="navbar-toggle-cbox">
				<div class="navbar-header">
					<label for="navbar-toggle-cbox" class="navbar-toggle collapsed" data-toggle="collapse" 
						data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</label>
					<a class="navbar-brand" href="<?= get_root_url().get_base_url().'welcome' ?>">
						<?= get_project_name() ?>
					</a>
				</div>
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-right"> 
						<li class="<?php echo ($page == get_location(1)) ? 'active' : '' ?>">
							<a href="<?= get_root_url().get_base_url().get_location(1) ?>">
								<span class="glyphicon glyphicon-home"></span>
								ACCUEIL
							</a>
						</li>
						<?php

						$my_rank = get_rank($_SESSION['name']);
						$ranks = get_rank_list();
						if($my_rank >= $ranks['administrator']){
							if($page == 'admin'){
								$class = 'active';
							}else{
								$class = '';
							}
							echo "<li class=\"".$class."\">
									<a href=\"".get_root_url().get_base_url()."admin\">
										<span class=\"glyphicon glyphicon-tasks\"></span> ADMINISTRATION 
									</a>
								</li>";
						}						
						?>
						<li class="<?php echo ($page == 'profile') ? ' active' : '' ?>">
							<a href="<?= get_root_url().get_base_url().'profile' ?>">
								<span class="glyphicon glyphicon-user"></span>
								COMPTE
							</a>
						</li>
						<li>
							<a href="<?= get_root_url().get_base_url().'logout' ?>">
								<span class="glyphicon glyphicon-off"></span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container">

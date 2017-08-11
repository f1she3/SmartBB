<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Open source real time web chat">
		<meta name="author" content="f1she3">
		<link rel="icon" href="<?= get_root_url().'/css/images/favicon.ico' ?>">
		<title><?= $title; ?></title>
		<link href="<?= get_root_url().'/css/bootstrap.min.css' ?>" rel="stylesheet">
		<link href="<?= get_root_url().'/css/style.css' ?>" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar navbar-default navbar-inverse navbar-static-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
						<span class="sr-only"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?= get_root_url().get_base_url().'welcome' ?>">
						Project	
					</a>
				</div>
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-right"> 
						<li class="<?php echo ($page == 'register') ? 'active' : '' ?>">
							<a href="<?= get_root_url().get_base_url().'register' ?>">
								<span class="glyphicon glyphicon-user"></span>
								INSCRIPTION
							</a>
						</li>
						<li class="<?php echo ($page == 'login') ? ' active' : '' ?>">
							<a href="<?= get_root_url().get_base_url().'login' ?>">
								<span class="glyphicon glyphicon-log-in"></span>
								CONNEXION
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container">

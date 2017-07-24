<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Open source real time web chat">
		<meta name="author" content="f1she3">
		<link rel="icon" href="<?= $_SESSION['host'].'/css/images/favicon.ico' ?>">
		<title><?= $title; ?></title>
		<link href="<?= $_SESSION['host'].'/css/bootstrap.min.css' ?>" rel="stylesheet">
		<link href="<?= $_SESSION['host'].'/css/style.css' ?>" rel="stylesheet">
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
					<a class="navbar-brand" href="<?= $_SESSION['host'].constant('BASE_URL').'welcome' ?>">
						Project	
					</a>
				</div>
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-right"> 
						<li class="<?php echo ($page == 'home') ? 'active' : '' ?>">
							<a href="<?= $_SESSION['host'].constant('BASE_URL').'home' ?>">
								<span class="glyphicon glyphicon-home"></span>
								ACCUEIL	
							</a>
						</li>
						<li class="<?php echo ($page == 'profile') ? ' active' : '' ?>">
							<a href="<?= $_SESSION['host'].constant('BASE_URL').'profile' ?>">
								<span class="glyphicon glyphicon-user"></span>
								COMPTE
							</a>
						</li>
						<li>
							<a href="<?= $_SESSION['host'].constant('BASE_URL').'logout' ?>">
								<span class="glyphicon glyphicon-off"></span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container">

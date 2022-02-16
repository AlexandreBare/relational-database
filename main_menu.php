<?php
session_start();

// If the login has not been entered
if(!isset($_SESSION["login"]))
	header('Location: index.php');
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Menu principal</title>
	</head>
	<body>
    <h2>Menu principal</h2>
		<ul>
			<li><a href="search.php">Effectuer une recherche dans la base de donnée</a></li>
			<li><a href="country.php">Afficher la liste des pays d'une pateforme de streaming</a></li>
			<li><a href="add_episode.php">Ajouter un épisode</a></li>
			<li><a href="black_mirror.php">Afficher la liste des utilisateurs ayant finis Black Mirror</a></li>
			<li><a href="episodes_popularity.php">Afficher les épisodes par ordre croissant de popularité</a></li>
		</ul>

	  <!-- Formulaire pour se déconnecter -->
 	  <form method="post" action="index.php">
 	  <p>
 		  <input type="hidden" name="disconnect" value="yes">
 		  <input type="submit" value="Déconnection"/>
 	  </p>
 	  </form>
  </body>
</html>

<?php
session_start();
?>

<html>
	<head>
		<title>Projet2 BDD</title>
	</head>
	<body>
		<?php

		header('X-XSS-Protection:0');

		//Retirer les variables de session si on s'est déconnectés
		if(isset($_POST['disconnect'])){
			session_unset();
		}

		// First connection to the database
		require_once('credentials.php');
		require_once('Connection.php');
		$bdd = new Connection('mysql:host=localhost;dbname=group40', $login, $pass);
		// Save connection in session
	  $_SESSION['db'] = serialize($bdd);

		// If the login has been entered
    if(isset($_POST["login"])){
			// Check whether the login and password are correct by fetching them in the database
			$query = "SELECT *
								FROM users
								WHERE login = '" . str_replace("'", "\'", $_POST["login"]) . "'
								AND pass = '" . str_replace("'", "\'", $_POST["pass"]) . "' ";
			$req = $bdd->query($query);
			$tuple = $req->fetch();

			if($tuple){ // if the login and password are correct
				$_SESSION['login'] = $tuple["login"];
			}else
				echo "Votre login/mot de passe est incorrect<BR><BR>";
		}


		if(isset($_SESSION['login'])){ // if we just got connected or if we are still connected
			header('Location: main_menu.php'); // Move to main_menu.php page
		}else{
		?>

		<h1>Veuillez entrer vos identifiants</h1>

		<form method="post" action="index.php">
			<p>
				<input type="text" name="login" required>
				<input type="password" name="pass" required>
				<input type="submit" value="Envoyer"/>
			</p>
		</form>

		<?php
	  }
	  ?>
  </body>
</html>

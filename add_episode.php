<?php
session_start();

// If the login has not been entered
if(!isset($_SESSION["login"]))
	header('Location: index.php');
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  	<title>Ajouter un épisode</title>
  </head>
  <body>
      <h3>Ajouter un épisode à la fin d'une série</h3>

      <form action="add_episode.php" method="post">
       <p>Nom de la série : </p>
       <select name="select_series" required>
         <option disabled selected value=""> -- Selectionner la série -- </option>
       </select>
       <br>
       <br>
       <input type="checkbox" name="is_new_season" value="new_season">
       <label for="is_new_season"> Ajouter à une nouvelle saison </label>
       <br>
       <p>Durée de l'épisode : <input type="number" name="duration" min="1" required/></p>
       <p>Synopsis de l'épisode : <input type="text" name="synopsis" required/></p>
       <p>Acteurs de l'épisode (pour effectuer des sélections multiples, appuyer en même temps sur la touche ctrl (Windows) ou cmd (Mac)) :</p>
       <select name="select_actors[]" multiple required>
         <option disabled selected value=""> -- Sélectionner un ou plusieurs acteurs -- </option>
       </select>

       <p><input type="submit" value="Ajouter"></p>
      </form>


      <br>
      <button type="button" onclick="location.href='main_menu.php'">Page précédente</button>

      <?php
      // Connection to the database
      include 'Connection.php';
      $db = unserialize($_SESSION['db']);

      // Fetch the series name
      $querySeries = "SELECT nom FROM serie";
      $reqSeries = $db->query($querySeries);
      $tableSeries = $reqSeries->fetchAll(\PDO::FETCH_NUM);

      // Fetch the actors
      $queryActors = "SELECT prenom, nom, numero
                      FROM acteur NATURAL JOIN personne";
      $reqActors = $db->query($queryActors);
      $tableActors = $reqActors->fetchAll(\PDO::FETCH_ASSOC);

      if ($_POST) { // if the user submited a new episode to add
        // Fetch the last episode of the last season of the selected series
        $queryEpisode = "SELECT n_saison, n_episode
                         FROM episodes
                         WHERE nom = '" . $_POST['select_series'] . "'
                         ORDER BY n_saison DESC, n_episode DESC
                         LIMIT 1";
        $reqEpisode = $db->query($queryEpisode);
        $episode = $reqEpisode->fetch(\PDO::FETCH_ASSOC);

        if($_POST['is_new_season'] == "new_season"){ // If the episode is added to a new season
          $episode['n_episode'] = "1";
          $episode['n_saison'] = strval((int) $episode['n_saison'] + 1);
        }else{ // If the episode is added to the last season
          $episode['n_episode'] = strval((int) $episode['n_episode'] + 1);
        }

        try {
          $db->exec("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
          $db->beginTransaction();
          // Insert a new episode
          $queryInsertEpisode = "INSERT INTO episodes (n_saison, n_episode, duree, synopsis, nom)
                                 VALUES (?, ?, ?, ?, ?)";
          $insertEpisode = $db->prepare($queryInsertEpisode);
          $success = $insertEpisode->execute(array($episode['n_saison'],
                                                   $episode['n_episode'],
                                                   $_POST['duration'],
                                                   $_POST['synopsis'],
                                                   $_POST['select_series']));
          if(!$success){
              $db->rollBack();
              throw new Exception();
          }
          // Insert actor in table joue_dans
          $queryInsertActor = "INSERT INTO joue_dans (numero, n_saison, n_episode, nom)
                               VALUES (?, ?, ?, ?)";
          $insertActor = $db->prepare($queryInsertActor);
          foreach ($_POST['select_actors'] as $selectedActor){
              $success = $insertActor->execute(array($selectedActor,
                                                      $episode['n_saison'],
                                                      $episode['n_episode'],
                                                      $_POST['select_series']));
            if(!$success){
              $db->rollBack();
              throw new Exception();
            }
          }
          $db->commit(); // Commit only if all inserts were done
          $db->exec("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");

          // Success Alert
          echo '<script type="text/javascript">
                  alert("The episode has been added !");
                </script>';

        }catch (Exception $e){
          // Error alert
          echo '<script type="text/javascript">
                  alert("Error when loading the episode !");
                </script>';
        }
      }
      ?>

      <script type="text/javascript">
        // Add options to select the episode series
        var selectSeries = document.getElementsByName("select_series")[0];
        var series = <?php echo json_encode($GLOBALS['tableSeries']); ?>;
        for(var i = 0; i < series.length; i++){
            var selectOption = document.createElement("OPTION");
            selectOption.value = series[i];
            selectOption.text = series[i];
            selectOption.id = series[i];
            selectSeries.appendChild(selectOption);
        }

        // Add options to select the actors playing in the episode
        var selectActors = document.getElementsByName("select_actors[]")[0];
        var actors = <?php echo json_encode($GLOBALS['tableActors']); ?>;
        for(var i = 0; i < actors.length; i++){
            var selectOption = document.createElement("OPTION");
            selectOption.value = actors[i]["numero"];
            selectOption.text = actors[i]["prenom"] + " " + actors[i]["nom"];
            selectActors.appendChild(selectOption);
        }
      </script>
  </body>
</html>

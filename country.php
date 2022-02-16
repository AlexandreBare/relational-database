<?php
session_start();

// If the login has not been entered
if(!isset($_SESSION["login"]))
	header('Location: index.php');
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script type = "text/javascript" src = "dynamicTable.js" ></script>
  	<title>Pays disponibles</title>
  </head>
  <body>
      <h3>Veuillez sélectionner une plateforme de streaming</h3>
      <form method="post" action="country.php">
        <select name="select_platform" onchange="this.form.submit()">
          <option disabled selected value=""> -- Choisissez une plateforme de streaming -- </option>
        </select>
      </form>

      <div id="table_div"></div>

      <br>
      <button type="button" onclick="location.href='main_menu.php'">Page précédente</button>

      <?php
      // Connection to the database
      include 'Connection.php';
      $db = unserialize($_SESSION['db']);

      // Fetch the countries available
      $headers = array("pays");
      if(isset($_POST['select_platform'])){ // if the user selected a streaming platform
        $selectPlatform = $_POST['select_platform'];
        $queryCountries = "SELECT " . $headers[0] . "
                           FROM plateforme_streaming NATURAL JOIN pays
                           WHERE nom = '" . $selectPlatform . "'";
      }else {
        $queryCountries = "SELECT " . $headers[0] . "
                           FROM pays";
      }

      $reqCountries = $db->query($queryCountries);
      $tableCountries = $reqCountries->fetchAll(\PDO::FETCH_ASSOC);

      // Fetch the streaming platform names
      $queryPlatforms = "SELECT nom
                         FROM plateforme_streaming";
      $reqPlatforms = $db->query($queryPlatforms);
      $tablePlatforms = $reqPlatforms->fetchAll(\PDO::FETCH_NUM);
      ?>

      <script type="text/javascript">
        // Create a table for the streaming platform available countries
        var countries = <?php echo json_encode($GLOBALS['tableCountries']); ?>;
        var tableId = "countries_table";
        var headers = <?php echo json_encode($GLOBALS['headers']); ?>;
        document.getElementById("table_div").appendChild(createTable(headers, countries, tableId));

        // Add options to select the streaming platforms
        var select = document.getElementsByName("select_platform")[0];
        var platforms = <?php echo json_encode($GLOBALS['tablePlatforms']); ?>;
        for(var i = 0; i < platforms.length; i++){
            var selectOption = document.createElement("OPTION");
            selectOption.value = platforms[i];
            selectOption.text = platforms[i];
            selectOption.id = platforms[i];
            select.appendChild(selectOption);
        }

        // Display last selection
        var selectValue = <?php echo json_encode($_POST['select_platform']); ?>;
        if(selectValue != undefined){
          select.options.namedItem(selectValue).selected = true;
        }
      </script>
  </body>
</html>

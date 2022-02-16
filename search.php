<?php
session_start();

// If the login has not been entered
if(!isset($_SESSION["login"]))
	header('Location: index.php');
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type = "text/javascript" src = "dynamicTable.js" ></script>
  	<title>Recherche</title>
  </head>
  <body>
      <h3>Dans quelle table souhaitez-vous effectuer une recherche?</h3>
      <form method="post" action="search.php">
        <select name="select_table" onchange="this.form.submit()">
          <option disabled selected value=""> -- Choisissez une table -- </option>
        </select>
      </form>

      <h3 id="subtitle"></h3>

      <form method="post" action="search.php" id="search_inputs">
      </form>

      <br>
      <div id="table_div"></div>

      <br>
      <button type="button" onclick="location.href='main_menu.php'">Page précédente</button>

      <?php
      // Connection to the database
      include 'Connection.php';
      $db = unserialize($_SESSION['db']);

      // Fetch the database table names
      $queryTableNames = "SELECT TABLE_NAME
                          FROM INFORMATION_SCHEMA.TABLES
                          WHERE TABLE_TYPE = 'BASE TABLE'
                          AND TABLE_SCHEMA = 'group40'
                          AND TABLE_NAME != 'users'";
      $reqTableNames = $db->query($queryTableNames);
      $tableNames = $reqTableNames->fetchAll(\PDO::FETCH_NUM);

      if(isset($_POST['select_table'])){ // if the user has selected a table name
        $_SESSION['select_table'] = $_POST['select_table'];

        // Fetch the tuples of the selected table
        $queryTable = "SELECT * FROM " . $_POST['select_table'];
        $reqTable = $db->query($queryTable);
        $table = $reqTable->fetchAll(\PDO::FETCH_ASSOC);
        $headers = array_keys($table[0]);

      }else if(isset($_SESSION['select_table'])){ // else if the user had selected a table name
        // Fetch the headers
        $queryFirstRow = "SELECT * FROM " . $_SESSION['select_table'] . " LIMIT 1";
        $reqFirstRow = $db->query($queryFirstRow);
        $firstRow = $reqFirstRow->fetchAll(\PDO::FETCH_ASSOC);
        $headers = array_keys($firstRow[0]);

        // Fetch the tuples of the selected table and
        // takes in account the constraints imposed by the user on the different fields
        $queryTable = "SELECT * FROM " . $_SESSION['select_table'];
        $first = true;
        $i = 0;
        foreach($headers as $header){ // for each search input
          if($_POST['search_' . $header] != ""){ // if the user wrote in it, add constraint
            if($first == true){
              $first = false;
              $queryTable .= " WHERE ";
            }else{
              $queryTable .= " AND ";
            }

            //getColumnMeta($i) -> gives an array of meta data of a column in a request
            $columnMeta = $reqFirstRow->getColumnMeta($i);
            if($columnMeta['native_type'] == 'VAR_STRING' || $columnMeta['native_type'] == 'STRING'){
              // constraint of containing a string
              $queryTable .= $header . " LIKE " . "'%" . $_POST['search_' . $header] . "%'";
            }else{
              // equality constraint for numbers and dates
              $queryTable .= $header . " = " . "'" . $_POST['search_' . $header] . "'";
            }
          }
          $i++;
        }

        $reqTable = $db->query($queryTable);
        $table = $reqTable->fetchAll(\PDO::FETCH_ASSOC);
      }

      ?>

      <script type="text/javascript">

        // Add options to select the table to search into
        var selectTable = document.getElementsByName("select_table")[0];
        var tableNames = <?php echo json_encode($GLOBALS['tableNames']);?>;
        for(var i = 0; i < tableNames.length; i++){
          var option = document.createElement("OPTION");
          option.text = tableNames[i];
          option.value = tableNames[i];
          option.id = tableNames[i];
          selectTable.appendChild(option);
        }

        // Display last selection
        var selectValue = <?php echo json_encode($_SESSION['select_table']);?> ;
        if(selectValue != undefined){
          selectTable.options.namedItem(selectValue).selected = true;
        }

        // If a table was selected
        if(document.getElementsByTagName("SELECT")[0].value != ""){
          // Display second title
          document.getElementById("subtitle").innerHTML = "Que recherchez-vous? (sensible à la casse)";

          // Add as many search inputs as there are columns in the search table
          var headers = <?php echo json_encode($GLOBALS['headers']); ?>;
          var searchInputs = document.getElementById("search_inputs");
          for(let i = 0; i < headers.length; i++){
              var searchInput = document.createElement("INPUT");
              searchInput.type = "text";
              searchInput.name = "search_" + headers[i];
              searchInput.placeholder = "Recherchez " + headers[i];
              searchInputs.appendChild(searchInput);
          }

          // Filter submit button
          var submitFilter = document.createElement("INPUT");
          submitFilter.type = "submit";
          submitFilter.innerText = "Filtrer";
          searchInputs.appendChild(submitFilter);

          // Display search table
          var dbArray = <?php echo json_encode($GLOBALS['table']); ?>;
          var tableId = "search_table";
          document.getElementById("table_div").appendChild(createTable(headers, dbArray, tableId));
        }
      </script>
  </body>
</html>

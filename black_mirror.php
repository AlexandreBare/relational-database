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
        <style>
            table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;
            }
            table tr:nth-child(even) {
            background-color: #eee;
            }
            table tr:nth-child(odd) {
            background-color: #fff;
            }
            table th {
            background-color: black;
            color: white;
            }
            table{
              width: 100%;
            }
        </style>
        <title>Black Mirror</title>
    </head>

    <body>
        <h1>Black Mirror</h1>

        <div id="table_div"></div>

        <br>
        <button type="button" onclick="location.href='main_menu.php'">Page précédente</button>

        <?php
        include 'Connection.php';
        $db = unserialize($_SESSION['db']);

        $query = "
        SELECT nom, prenom
        FROM personne
        WHERE numero IN(
            SELECT numero
            FROM (
                SELECT numero, COUNT(DISTINCT(n_saison)) AS saison_watched, COUNT(n_episode) AS episode_watched, nb_saison, nb_epis
                FROM(
                    SELECT *
                    FROM(
                        SELECT numero, nom_serie, n_saison, n_episode
                        FROM regarde
                        WHERE nom_serie = 'Black Mirror'
                    ) AS t1
                    NATURAL JOIN(
                        SELECT nom, COUNT(DISTINCT(n_saison)) AS nb_saison, COUNT(n_episode) AS nb_epis
                        FROM episodes
                        WHERE nom = 'Black Mirror'
                    ) AS t2
                ) AS t3
            GROUP BY numero
            HAVING saison_watched = nb_saison AND episode_watched = nb_epis
            ) AS t4
        )
        ";

        $req = $db->query($query);
        $table = $req->fetchAll(\PDO::FETCH_ASSOC);
        ?>

        <script type="text/javascript">
        // Display search table
        var dbArray = <?php echo json_encode($GLOBALS['table']); ?>;
        var tableId = "black_mirror_table";
        var headers = Object.keys(dbArray[0]);
        document.getElementById("table_div").appendChild(createTable(headers, dbArray, tableId));
        </script>
    </body>
</html>

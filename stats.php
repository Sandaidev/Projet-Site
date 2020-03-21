<?php
session_start();
require_once "./assets/lib/lib_jardin_autonome.php";
if (check_if_session_is_valid($_SESSION) == false) {
    echo "<script>window.location.replace('index.php');</script>";
}
check_if_db_contains_default_data();
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/main.css">
    <title>Statistiques - Jardin Autonome</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="./index.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

        <div class="header-right">
            <a href="./home.php">Accueil</a>
            <a class="active" href="./stats.php">Stats</a>
            <a href="./capteurs.php">Capteurs</a>
            <a href="./settings.php">Paramètres</a>
            <a href="./about.html">À propos</a>
        </div>
    </div>

    <div class="login-container">
        <h2>Statistiques</h2>
        <hr>

        <?php
        require_once "./assets/lib/lib_jardin_autonome.php";

        $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);
        $select_history_query = "SELECT * FROM $table_history_name ORDER BY id DESC LIMIT 16";

        $select_history_result = $db_connection->query($select_history_query);

        if ($select_history_result->num_rows > 0) {
            // output data of each row

            echo "<table>";
            echo "<tr>
                <th>Mois</th>
                <th>Jour</th>
                <th>Heure</th>
            </tr>";

            while ($row = $select_history_result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $row['mois'] . "</td>"
                    . "<td>" . $row['jour'] . "</td>"
                    . "<td>" . $row['heure'] . "</td>"
                    . "</tr>";
            }

            echo "</table>";
        } else {
            echo "<strong>0 results</strong>";
        }

        $db_connection->close();

        ?>

    </div>

    <div class="footer">
        <h4>Liste des dernières mises à jour</h4>
    </div>

</body>

</html>
<?php
session_start();
require_once "./assets/lib/lib_jardin_autonome.php";
if (check_if_session_is_valid($_SESSION) == false) {
    echo "<script>window.location.replace('index.php');</script>";
}
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/w3.css">
    <link rel="stylesheet" href="css/main.css">
    <title>Accueil - Jardin Autonome</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="./index.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

        <div class="header-right">
            <a class="active" href="./home.php">Accueil</a>
            <a href="./stats.php">Stats</a>
            <a href="./capteurs.php">Capteurs</a>
            <a href="./settings.php">Paramètres</a>
            <a href="./about.html">À propos</a>
        </div>
    </div>

    <div class="login-container">
        <h2>Accueil</h2>
        <hr>
        <?php
        require_once "./assets/lib/lib_jardin_autonome.php";

        $sensors_data = return_formatted_sensor_table();

        echo "<p>Niveau d'eau dans la cuve :</p>

        <div class='w3-light-grey w3-round'>
            <div class='w3-container w3-round w3-blue' style='width:" . $sensors_data['POURCENTAGE_CUVE'] . "'>" . $sensors_data['POURCENTAGE_CUVE'] . "</div>
          </div>

        ";

        echo "<hr>";
        echo "Dernier rechargement de la cuve : Le <strong>" . $sensors_data['DERNIERE_RECHARGE_CUVE_MOIS'] . "/"
            . $sensors_data['DERNIERE_RECHARGE_CUVE_JOUR'] . "</strong> à <strong>" . $sensors_data['DERNIERE_RECHARGE_CUVE_HEURE'] . "h</strong>";
        echo "<br>";
        echo "Dernier arrosage : Le <strong>" . $sensors_data['DERNIER_ARROSAGE_MOIS'] . "/"
            . $sensors_data['DERNIER_ARROSAGE_JOUR'] . "</strong> à <strong>" . $sensors_data['DERNIER_ARROSAGE_HEURE'] . "h</strong>";
        echo "<br>";

        ?>


    </div>


    </div>

</body>

</html>
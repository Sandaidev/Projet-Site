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
    <title>Capteurs - Jardin Autonome</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="./index.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

        <div class="header-right">
            <a href="./home.php">Accueil</a>
            <a href="./stats.php">Stats</a>
            <a class="active" href="./capteurs.php">Capteurs</a>
            <a href="./settings.php">Paramètres</a>
            <a href="./about.html">À propos</a>
        </div>
    </div>

    <div class="login-container">
        <h2>Capteurs</h2>
        <hr>

        <?php
        require_once "./assets/lib/lib_jardin_autonome.php";

        $sensors_data = return_formatted_sensor_table();

        $formatted_humidity = $sensors_data['HUMIDITE_TERRE'];

        echo "<p>Humidité de la terre :</p>

        <div class='w3-light-grey w3-round-xlarge'>
            <div class='w3-round-xlarge w3-blue' style='width:" . $formatted_humidity . "'>" . $formatted_humidity . "</div>
          </div>";

        echo "<hr>";
        echo "Dernière mise à jour : Le <strong>" . $sensors_data['DERNIERE_MISE_A_JOUR_MOIS'] . "/"
            . $sensors_data['DERNIERE_MISE_A_JOUR_JOUR'] . "</strong> à <strong>" . $sensors_data['DERNIERE_MISE_A_JOUR_HEURE'] . "h</strong>";
        ?>

    </div>

</body>

</html>
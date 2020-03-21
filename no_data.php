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
    <link rel="stylesheet" href="css/main.css">
    <title>Synchronisation... - Jardin Autonome</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="./index.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

        <div class="header-right">
            <a href="./home.php">Accueil</a>
            <a href="./stats.php">Stats</a>
            <a href="./capteurs.php">Capteurs</a>
            <a href="./settings.php">Paramètres</a>
            <a href="./about.html">À propos</a>
        </div>
    </div>

    <div class="login-container">
        <h2>Synchronisation</h2>
        <hr>
        <p><strong>Aucune donnée n'est actuellement disponible sur l'état du jardin autonome</strong></p>
        <p>Veuillez patienter et revenez dans une demi-heure.</p>
        <hr>
        <a href="./debug.php">[DEBUG] - Injecter des données</a>
    </div>

</body>

</html>
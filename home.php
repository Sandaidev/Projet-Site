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
    <title>Accueil - Jardin Autonome</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="./index.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

        <div class="header-right">
            <a class="header-right active" href="./home.php">Accueil</a>
            <a class="header-right" href="./stats.php">Stats</a>
            <a class="header-right" href="./capteurs.php">Capteurs</a>
            <a class="header-right" href="./about.html">À propos</a>
        </div>
    </div>

    <div class="login-container">
        <img src="https://media.giphy.com/media/BIuuwHRNKs15C/giphy.gif">
    </div>

</body>

</html>
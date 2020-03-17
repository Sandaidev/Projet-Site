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
            <a href="./home.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

        <div class="header-right">
            <a class="header-right active" href="./home.php">Accueil</a>
            <a class="header-right" href="./stats.php">Stats</a>
            <a class="header-right" href="./capteurs.php">Capteurs</a>
            <a class="header-right" href="./about.html">À propos</a>
        </div>
    </div>

    <?php

    // Importation des librairies
    require_once "./assets/lib/lib_jardin_autonome.php";

    // On regarde si la BDD existe
    if (check_if_db_exists() == false) {
        initialize_database();
    }

    ?>

</body>

</html>
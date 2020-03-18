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
    <title>Paramètres - Jardin Autonome</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="./home.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

        <div class="header-right">
            <a href="./home.php">Accueil</a>
            <a href="./stats.php">Stats</a>
            <a href="./capteurs.php">Capteurs</a>
            <a class="active" href="./settings.php">Paramètres</a>
            <a href="./about.html">À propos</a>
        </div>
    </div>

    <div class="login-container">
        <h2>Modifier vos infos de login</h2>
        <hr>

        <form method="POST">
            <table>
                <tr>
                    <td><label for="old-username">Ancien utilisateur - </label></td>
                    <td><input type="text" required id="old-username" name="old-username" size="16" maxlength="16"></td>
                </tr>

                <tr>
                    <td><label for="old-password">Ancien mot de passe - </label></td>
                    <td><input type="password" required id="old-password" name="old-password" size="16" maxlength="64"></td>

                </tr>

                <tr>
                    <td><label for="new-username">Nouvel utilisateur - </label></td>
                    <td><input type="text" required id="new-username" name="new-username" size="16" maxlength="16"></td>
                </tr>

                <tr>
                    <td><label for="new-password">Nouveau mot de passe - </label></td>
                    <td><input type="password" required id="new-password" name="new-password" size="16" maxlength="64"></td>
                </tr>

                <br>
            </table>

            <input type="submit" value="Changer les infos de login">
        </form>

    </div>

    <?php

    // Importation des librairies
    require_once "./assets/lib/lib_jardin_autonome.php";

    // On regarde si les credentials sont disponibles dans $_POST
    if (isset($_POST['old-username']) && !empty($_POST['old-username'])) {
        // On les compare à la BDD
        if (check_if_creds_are_valid($_POST['old-username'], $_POST['old-password']) == true) {
            // Les anciens creds sont valides, on peut remplacer les valeurs creds sur la BDD
            modify_creds_in_database($_POST['new-username'], $_POST['new-password']);
            // On affiche un message dans le footer, et on redirige vers la page login!

            echo "
            <div class='footer'>
            <h4>
            Veuillez patienter, vous allez bientôt être redirigé vers la page de login...
            </h4>
            </div>";

            echo "<script>
            t1 = window.setTimeout(function(){ window.location.replace('index.php'); },1500);
            </script>"; // Redirect to index.php after 1.5 seconds
        } else {
            echo "
            <div class='footer'>
            <h4>
            Vos anciennes informations de login sont incorrectes!
            </h4>
            </div>";
        }
    }

    ?>

</body>

</html>
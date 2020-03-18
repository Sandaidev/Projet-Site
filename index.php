<?php
session_start();
require_once "./assets/lib/lib_jardin_autonome.php";

// On regarde si la BDD existe
if (check_if_db_exists() == false) {
    initialize_database();

    echo "<div class='footer'>
        <h4>
        Il semblerait que vous vous connectez pour la première fois,
        <br>
        vôtre nom d'utilisateur est '<em>jardin</em>' et vôtre mot de passe est '<em>autonome</em>'</h4>
        </div>";
}

if (check_if_session_is_valid($_SESSION) == true) {
    echo "<script>window.location.replace('home.php');</script>";
}
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <title>Login - Jardin Autonome</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <div class="header">
        <div class="logo">
            <a href="./index.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

    </div>

    <div class="login-container">
        <h2>Login</h2>
        <hr>

        <form method="POST">
            <table>
                <tr>
                    <td><label for="username">Username - </label></td>
                    <td><input type="text" required id="username" name="username" size="16" maxlength="16"></td>
                </tr>

                <tr>
                    <td><label for="password">Password - </label></td>
                    <td><input type="password" required id="password" name="password" size="16" maxlength="64"></td>
                </tr>

                <br>
            </table>

            <input type="submit" value="OK">
        </form>

        <a href="./nukedb.php">Mot de passe oublié</a>

    </div>

    <?php

    // Importation des librairies
    require_once "./assets/lib/lib_jardin_autonome.php";

    // On regarde si les credentials sont disponibles dans $_POST
    if (isset($_POST['username']) && !empty($_POST['username'])) {
        // On les compare à la BDD
        if (check_if_creds_are_valid($_POST['username'], $_POST['password']) == true) {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['password'] = $_POST['password'];
            echo "<script>window.location.replace('home.php');</script>";
        }
    }

    ?>

</body>

</html>
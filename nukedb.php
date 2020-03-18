<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <title>Mot de passe oublié - Jardin Autonome</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <div class="header">
        <div class="logo">
            <a href="./home.php" class="logo"><strong>JARDIN AUTONOME</strong></a>
        </div>

    </div>

    <div class="login-container">
        <h2>Mot de passe oublié</h2>
        <hr>
        <form>
            <table>
                <tr>
                    <td><label for="confirm">Confirmer? - </label></td>
                    <td><input type="text" required id="confirm" name="confirm" size="9" maxlength="9"></td>
                </tr>
            </table>

            <input type="submit" value="OK">
        </form>

        <a href="./index.php">Retournez à la page d'accueil</a>

        <?php

        // Importation des librairies
        require_once "./assets/lib/lib_jardin_autonome.php";

        // On regarde si l'utilisateur a confirmé
        if (isset($_GET['confirm']) && strtolower($_GET['confirm']) == "confirmer") {
            // On nuke la BDD, et on redirige l'utilisateur vers la page index.php
            nuke_database();
            echo "<script>
            t1 = window.setTimeout(function(){ window.location.replace('index.php'); },1500);
            </script>"; // Redirect to index.php after 1.5 seconds
        }


        ?>

    </div>

    <?php

    // On change le message du footer si l'utilisateur a confirmé
    if (isset($_GET['confirm']) && strtolower($_GET['confirm']) == "confirmer") {
        echo "
        <div class='footer'>
        <h4>
            Veuillez patienter, vous allez bientôt être redirigé vers la page d'accueil...
        </h4>
        </div>";
    } else {
        echo "
        <div class='footer'>
        <h4>
            Cette action va réinitialiser et supprimer toutes les données sauvegardées,
            Si vous en êtes sûr, tapez '<em>Confirmer</em>' et clickez sur OK.
        </h4>
        </div>";
    }

    ?>


</body>

</html>
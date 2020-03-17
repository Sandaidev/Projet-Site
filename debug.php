<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <title>DEBUG - Jardin Autonome</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <div class="header">
        <div class="logo">
            <a href="./debug.php" class="logo"><strong>JARDIN AUTONOME - DEBUG PAGE</strong></a>
        </div>

    </div>

    <div class="login-container">
        <h2>DEBUG REQUESTS</h2>
        <hr>
        <!-- utiliser <form> sans paramètres est valide en HTML5, çe renvoie les données sur la page actuelle -->
        <form>
            <!-- UPDATE SENSORS DATA -->
            <table>
                <tr>
                    <td><label for="cuve1">CUVE1 - </label></td>
                    <td><input type="text" required id="cuve1" name="cuve1" size="5"></td>
                </tr>

                <tr>

                    <td><label for="cuve2">CUVE2 - </label></td>
                    <td><input type="text" required id="cuve2" name="cuve2" size="5"></td>
                </tr>

                <tr>

                    <td><label for="cuve3">CUVE3 - </label></td>
                    <td><input type="text" required id="cuve3" name="cuve3" size="5"></td>
                </tr>

                <tr>

                    <td><label for="cuve4">CUVE4 - </label></td>
                    <td><input type="text" required id="cuve4" name="cuve4" size="5"></td>
                </tr>

                <tr>

                    <td><label for="humidite">HUMIDITE - </label></td>
                    <td><input type="text" required id="humidite" name="humidite" size="5"></td>
                </tr>

            </table>

            <input type="submit" value="SUBMIT DATA">
        </form>

    </div>

    <?php

    require_once "./assets/lib/lib_jardin_autonome.php";

    // On check si les variables sont définies
    if (isset($_GET['cuve1']) && !empty($_GET['cuve1'])) {
        // Toutes les variables sont définies, on peut ajouter à la BDD!
        inject_sensors_data_into_db($_GET['cuve1'], $_GET['cuve2'], $_GET['cuve3'], $_GET['cuve4'], $_GET['humidite']);
    }

    ?>

</body>

</html>
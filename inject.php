<?php
// Cette partie du code est faite pour être utilisée par l'Arduino, qui envoie les données au serveur Apache
// via une requête GET

// On importe notre librairie
require_once "./assets/lib/lib_jardin_autonome.php";

// On check si au moins une variable est définie avant d'injecter les données à la BDD
if (isset($_GET['cuve1']) && !empty($_GET['cuve1'])) {
    // On injecte les variables à la BDD!
    inject_sensors_data_into_db($_GET['cuve1'], $_GET['cuve2'], $_GET['cuve3'], $_GET['cuve4'], $_GET['humidite']);
}

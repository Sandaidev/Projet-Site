<?php

$db_servername = "localhost";
$db_username = "root";
$db_password = "";

$db_name = "jardin";
$table_sensors_name = "capteurs";
$table_history_name = "historique";
$table_creds_name = "creds";
$creds_default_username = "jardin";
$creds_default_password = "autonome";

$db_generic_default_value = "syncing...";
$table_sensors_default_value = $db_generic_default_value;
$table_last_watering_default_value = $db_generic_default_value;
$table_last_refill_default_value = $db_generic_default_value;

$table_last_watering_name = "last_watering";
$table_last_refill_name = "last_refill";


function initialize_database()
{
    /*
    * Se connecte à la BDD et l'initialise
    * (ou la remet à zéro si elle existe)
    */

    // Changing the scope of these vars to match the ones at the top of the script.
    global $db_servername;
    global $db_username;
    global $db_password;
    global $db_name;
    global $table_sensors_name;
    global $table_history_name;
    global $table_creds_name;
    global $creds_default_username;
    global $creds_default_password;
    global $table_sensors_default_value;
    global $table_last_watering_name;
    global $table_last_watering_default_value;
    global $table_last_refill_name;
    global $table_last_refill_default_value;

    // MySQLi à la rescousse
    $db_connection = new mysqli($db_servername, $db_username, $db_password);

    // Check la connexion à la BDD
    if ($db_connection->connect_error) {
        // On tue le script si on a une erreur: my bad, will fix.
        die("MySQLi connection failed! Details: " . $db_connection->connect_error);
    }

    // Avant de créer la BDD, on doit supprimer l'ancienne juste pour être sûr; avec PHP on sait jamais.
    $drop_db_query = "DROP DATABASE [IF EXISTS] $db_name";
    $db_connection->query($drop_db_query);

    $create_db_query = "CREATE DATABASE jardin";

    // Envoyer la query et arrêter si ça a foiré.
    if ($db_connection->query($create_db_query) != true) {
        die("Error creating database! Details: " . $db_connection->error);
    }

    /*
    *   Initialisation des tables
    */

    // Select la BDD, pas de checks car on sait déjà que tout est OK.
    mysqli_select_db($db_connection, $db_name);

    // Var def: SQL Query : Table des capteurs.
    $create_table_sensors_query = "CREATE TABLE `$table_sensors_name` (
        `CUVE1` varchar(10) NOT NULL,
        `CUVE2` varchar(10) NOT NULL,
        `CUVE3` varchar(10) NOT NULL,
        `CUVE4` varchar(10) NOT NULL,
        `HUMIDITE` varchar(10) NOT NULL
      )";

    // Var def : SQL Query : Table d'historique
    $create_table_history_query = "CREATE TABLE `$table_history_name` ( `id` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `mois` VARCHAR(20) NOT NULL,
        `jour` VARCHAR(20) NOT NULL,
        `heure` VARCHAR(20) NOT NULL,
        PRIMARY KEY (`id`))";

/*
*   ATTENTION CODE POURRI : Comme demandé sur le CDC, LE MOT DE PASSE N'EST PAS HASHÉ!!!
*   Il est stocké "en clair" dans la BDD... Le client est roi j'imagine.
*/
    
    $create_table_creds_query = "CREATE TABLE `$table_creds_name` ( `username` VARCHAR(20) NOT NULL , `password` VARCHAR(64) NOT NULL )";

    // Var def : SQL Query : Table du dernier arrosage 
    $create_table_last_watering_query = "CREATE TABLE `$table_last_watering_name` ( `mois` VARCHAR(20) NOT NULL , `jour` VARCHAR(20) NOT NULL , `heure` VARCHAR(20) NOT NULL )";

    // Var def : SQL Query : Table credentials 
    $create_table_last_refill_query = "CREATE TABLE `$table_last_refill_name` ( `mois` VARCHAR(20) NOT NULL , `jour` VARCHAR(20) NOT NULL , `heure` VARCHAR(20) NOT NULL )";

    // Exécution des queries; création des tables
    $db_connection->query($create_table_last_refill_query);
    $db_connection->query($create_table_history_query);
    $db_connection->query($create_table_sensors_query);
    $db_connection->query($create_table_creds_query);
    $db_connection->query($create_table_last_watering_query);

    // Var def : SQL Query : Initialisation table capteurs avec des placeholders
    $initialize_table_sensors_query = "INSERT INTO `$table_sensors_name` (`CUVE1`, `CUVE2`, `CUVE3`, `CUVE4`, `HUMIDITE`) VALUES (
        '$table_sensors_default_value',
        '$table_sensors_default_value',
        '$table_sensors_default_value',
        '$table_sensors_default_value',
        '$table_sensors_default_value')";

    // Même chose pour la table mdp + utilisateur
    $initialize_table_creds_query = "INSERT INTO `creds` (`username`, `password`) VALUES (
        '$creds_default_username',
        '$creds_default_password')";

    // Idem pour le dernier arrosage
    $initialize_table_last_watering_query = "INSERT INTO `last_watering` (`mois`, `jour`, `heure`) VALUES ('syncing...', 'syncing...', 'syncing...')";

    // Imotep. Pour la dernière recharge
    $initialize_table_last_refill_query = "INSERT INTO `last_refill` (`mois`, `jour`, `heure`) VALUES ('syncing...', 'syncing...', 'syncing...')";

    // Exécution des queries; initialisation des tables
    $db_connection->query($initialize_table_sensors_query);
    $db_connection->query($initialize_table_creds_query);
    $db_connection->query($initialize_table_last_watering_query);
    $db_connection->query($initialize_table_last_refill_query);
    $db_connection->close();

    return true;
}

function check_if_db_contains_default_data()
{
    /*
    * OK, donc là c'est chaud. Si la BDD contient UNE valeur égale au $default_value
    * On redirige l'utilisateur vers une page temporaire (on a pas les données donc c'est chaud)
    * Et on propose une option pour avoir la page Debug
    */

    global $db_generic_default_value;

    // Logiquement, cette fonction est exécutée APRÈS avoir initialisé la BDD
    $db_data_array = return_formatted_sensor_table();

    // FIXME : /!\ ATTENTION VIEUX HACK POURRI /!\
    // -------------------------------------------
    // Il-y-a de meilleures façons de faire une redirection, là c'est impossible de renvoyer un header HTTP sur PHP...
    // On utilise une balise JS.
    foreach ($db_data_array as $array_item) {
        if ($array_item == $db_generic_default_value) {
            echo "<script>window.location.replace('no_data.php');</script>";
            die();
        }
    }
}

function check_if_db_exists()
{
    /*
    * Self-explanatory; On essaie d'établir une connexion avec la BDD, si la connexion a échoué (e.g. false),
    * On select la BDD et on regarde le flag retourné.
    */

    global $db_servername;
    global $db_username;
    global $db_password;
    global $db_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password);

    if (mysqli_select_db($db_connection, $db_name) != true) {
        return false;
    } else {
        return true;
    }

    $db_connection->close();
}

function inject_sensors_data_into_db($cuve_1, $cuve_2, $cuve_3, $cuve_4, $humidite)
{
    /*
    * We only need to change the values of the sensors table,
    * So we use the UPDATE request and not INSERT.
    */

    global $db_servername;
    global $db_username;
    global $db_password;
    global $table_sensors_name;
    global $db_name;
    global $table_last_watering_name;
    global $table_sensors_default_value;
    global $table_sensors_name;
    global $table_last_refill_name;
    global $table_history_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);

    $retrieve_sensors_data_query = "SELECT * FROM $table_sensors_name";
    $inject_sensors_data_query = "UPDATE `capteurs` SET `CUVE1`=$cuve_1,`CUVE2`=$cuve_2,`CUVE3`=$cuve_3,`CUVE4`=$cuve_4,`HUMIDITE`=$humidite";

    // We get the old humidity value from the database and compare it against the new one
    $sensors_data_result = $db_connection->query($retrieve_sensors_data_query);
    $sensors_row = $sensors_data_result->fetch_assoc();

    $old_humidity_value = $sensors_row['HUMIDITE'];

    if ($old_humidity_value == $table_sensors_default_value || $humidite > $old_humidity_value) {
        // The value in the DB is the default one or the new humidity value is greater than the last

        $update_month = date("m");
        $update_day = date("d");
        $update_hour = date("G");

        $table_last_watering_inject_query = "UPDATE `$table_last_watering_name` SET `mois`='$update_month',`jour`='$update_day',`heure`='$update_hour'";
        $db_connection->query($table_last_watering_inject_query);
    }

    // We now need to check if the water tank has been refilled,
    $old_tank_percentage = return_formatted_sensor_table()['POURCENTAGE_CUVE'];
    $new_tank_percentage = "0%";

    if ($cuve_1 == 1) {
        $new_tank_percentage = "25%";

        if ($cuve_2 == 1) {
            $new_tank_percentage = "50%";

            if ($cuve_3 == 1) {
                $new_tank_percentage = "75%";

                if ($cuve_4 == 1) {
                    $new_tank_percentage = "100%";
                }
            }
        }
    }

    if ($new_tank_percentage > $old_tank_percentage) {
        // The water tank was refilled, we inject the new date into the water tank table
        $update_month = date("m");
        $update_day = date("d");
        $update_hour = date("G");

        $water_tank_injection_query = "UPDATE `$table_last_refill_name` SET `mois`='$update_month',`jour`='$update_day',`heure`='$update_hour'";
        $db_connection->query($water_tank_injection_query);
    }

    // We inject the data to the sensors table
    $db_connection->query($inject_sensors_data_query);

    // We now need to add a new line to the history table, with the current date.
    $insertion_query = "INSERT INTO `$table_history_name`(`id`, `mois`, `jour`, `heure`) VALUES (NULL,'$update_month','$update_day','$update_hour')";

    $db_connection->query($insertion_query);
    $db_connection->close();
}


function truncate_history_table()
{
    /*
    * Empties the history table
    */

    global $db_servername;
    global $db_username;
    global $db_password;
    global $table_history_name;
    global $db_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);
    $truncate_query = "TRUNCATE TABLE $table_history_name";

    $db_connection->query($truncate_query);
    $db_connection->close();
}

function return_formatted_sensor_table()
{
    global $db_servername;
    global $db_username;
    global $db_password;
    global $table_sensors_name;
    global $db_name;
    global $table_last_refill_name;
    global $table_last_watering_name;
    global $table_history_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);

    $select_sensors_query = "SELECT * FROM $table_sensors_name";
    $result = $db_connection->query($select_sensors_query);

    // Ugly code incoming, I have no idea on how else I'm doing this
    // Basically, we have 4 vars, if one is true, it should increment by 25% the final result

    $row = $result->fetch_assoc();

    $cuve_percentage = "0%";

    if ($row['CUVE1'] == 1) {
        $cuve_percentage = "25%";

        if ($row['CUVE2'] == 1) {
            $cuve_percentage = "50%";

            if ($row['CUVE3'] == 1) {
                $cuve_percentage = "75%";

                if ($row['CUVE4'] == 1) {
                    $cuve_percentage = "100%";
                }
            }
        }
    }

    $humidity_value = $row['HUMIDITE'];

    $select_water_tank_refill_query = "SELECT * FROM $table_last_refill_name";
    $last_refill_result = $db_connection->query($select_water_tank_refill_query);
    $last_refill_row = $last_refill_result->fetch_assoc();

    $select_last_watering_query = "SELECT * FROM $table_last_watering_name";
    $last_watering_result = $db_connection->query($select_last_watering_query);
    $last_watering_row = $last_watering_result->fetch_assoc();

    $select_history_query = "SELECT * FROM $table_history_name ORDER BY id DESC";
    $history_result = $db_connection->query($select_history_query);
    $history_row = $history_result->fetch_assoc();

    // Workaround the inverted row bug
    /*
    while ($history_row = $history_result->fetch_assoc()) {
        $history_items[] = $history_row;
    }

    $history_items = array_reverse($history_items, true);
    */

    $db_connection->close();

    $formatted_humidity = floatval($humidity_value) * 100 . "%";

    $final_result = [
        'HUMIDITE_TERRE' => $formatted_humidity,
        'POURCENTAGE_CUVE' => $cuve_percentage,
        'DERNIERE_RECHARGE_CUVE_MOIS' => $last_refill_row['mois'],
        'DERNIERE_RECHARGE_CUVE_JOUR' => $last_refill_row['jour'],
        'DERNIERE_RECHARGE_CUVE_HEURE' => $last_refill_row['heure'],
        'DERNIER_ARROSAGE_MOIS' => $last_watering_row['mois'],
        'DERNIER_ARROSAGE_JOUR' => $last_watering_row['jour'],
        'DERNIER_ARROSAGE_HEURE' => $last_watering_row['heure'],
        'DERNIERE_MISE_A_JOUR_MOIS' => $history_row['mois'],
        'DERNIERE_MISE_A_JOUR_JOUR' => $history_row['jour'],
        'DERNIERE_MISE_A_JOUR_HEURE' => $history_row['heure']
    ];

    return $final_result;
}

function nuke_database()
{
    global $db_servername;
    global $db_username;
    global $db_password;
    global $db_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password);
    $drop_db_query = "DROP DATABASE $db_name";

    $db_connection->query($drop_db_query);
    $db_connection->close();
}

function check_if_session_is_valid($session_array_data)
{
    /*
    * Checks the session data / credentials based on the superglobal $_SESSION parameter (here $session_array_data)
    */

    global $db_servername;
    global $db_username;
    global $db_password;
    global $db_name;
    global $table_creds_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);
    $select_creds_query = "SELECT * FROM $table_creds_name";

    $result = $db_connection->query($select_creds_query);
    $row = $result->fetch_assoc();

    $db_connection->close();

    if (isset($session_array_data['username']) && !empty($session_array_data['username'])) {
        // Si les creds existent, mais qu'ils sont invalides
        if ($row['username'] != $session_array_data['username'] || $row['password'] != $session_array_data['password']) {
            return false;
        }
    } else {
        // Les creds n'existent pas
        return false;
    }

    return true;
}

function check_if_creds_are_valid($username, $password)
{
    global $db_servername;
    global $db_username;
    global $db_password;
    global $db_name;
    global $table_creds_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);
    $select_creds_query = "SELECT * FROM $table_creds_name";

    $result = $db_connection->query($select_creds_query);
    $row = $result->fetch_assoc();

    $db_connection->close();

    if ($row['username'] == $username && $row['password'] == $password) {
        return true;
    } else {
        return false;
    }
}

function modify_creds_in_database($new_username, $new_password)
{
    /*
    * Change the credentials inside the database
    */

    global $db_servername;
    global $db_username;
    global $db_password;
    global $db_name;
    global $table_creds_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);
    $modify_creds_query = "UPDATE `$table_creds_name` SET `username`='$new_username',`password`='$new_password' WHERE 1";

    $db_connection->query($modify_creds_query);
    $db_connection->close();
}

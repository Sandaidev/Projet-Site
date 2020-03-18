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
$table_sensors_default_value = "syncing...";
$table_last_watering_default_value = "syncing...";
$table_last_watering_name = "last_watering";
$table_last_refill_name = "last_refill";
$table_last_refill_default_value = "syncing...";

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

    // Create and initialize a new mysqli object (Oriented Object amirite)
    $db_connection = new mysqli($db_servername, $db_username, $db_password);

    // Check if the connection we just made is valid
    if ($db_connection->connect_error) {
        // Stop the function immediately if the connection is not valid.
        die("MySQLi connection failed! Details: " . $db_connection->connect_error);
    }

    // Before initializing the database, we first need to remove it if it exists
    $drop_db_query = "DROP DATABASE [IF EXISTS] $db_name";
    $db_connection->query($drop_db_query);


    $create_db_query = "CREATE DATABASE jardin";

    // Send the query and stop the script if an error happened
    if ($db_connection->query($create_db_query) != true) {
        die("Error creating database! Details: " . $db_connection->error);
    }

    // We now need to create the tables
    mysqli_select_db($db_connection, $db_name);

    $create_table_sensors_query = "CREATE TABLE `$table_sensors_name` (
        `CUVE1` varchar(10) NOT NULL,
        `CUVE2` varchar(10) NOT NULL,
        `CUVE3` varchar(10) NOT NULL,
        `CUVE4` varchar(10) NOT NULL,
        `HUMIDITE` varchar(10) NOT NULL
      )";

    $create_table_history_query = "CREATE TABLE `$table_history_name` (
        `mois` varchar(10) DEFAULT NULL,
        `jour` varchar(10) DEFAULT NULL,
        `heure` varchar(10) DEFAULT NULL
      )";

    $create_table_creds_query = "CREATE TABLE `$table_creds_name` ( `username` VARCHAR(20) NOT NULL , `password` VARCHAR(64) NOT NULL )";

    $create_table_last_watering_query = "CREATE TABLE `$table_last_watering_name` ( `mois` VARCHAR(20) NOT NULL , `jour` VARCHAR(20) NOT NULL , `heure` VARCHAR(20) NOT NULL )";

    $create_table_last_refill_query = "CREATE TABLE `$table_last_refill_name` ( `mois` VARCHAR(20) NOT NULL , `jour` VARCHAR(20) NOT NULL , `heure` VARCHAR(20) NOT NULL )";

    $db_connection->query($create_table_last_refill_query);
    $db_connection->query($create_table_history_query);
    $db_connection->query($create_table_sensors_query);
    $db_connection->query($create_table_creds_query);
    $db_connection->query($create_table_last_watering_query);

    $initialize_table_sensors_query = "INSERT INTO `$table_sensors_name` (`CUVE1`, `CUVE2`, `CUVE3`, `CUVE4`, `HUMIDITE`) VALUES (
        '$table_sensors_default_value',
        '$table_sensors_default_value',
        '$table_sensors_default_value',
        '$table_sensors_default_value',
        '$table_sensors_default_value')";

    $initialize_table_creds_query = "INSERT INTO `creds` (`username`, `password`) VALUES (
        '$creds_default_username',
        '$creds_default_password')";

    $initialize_table_last_watering_query = "INSERT INTO `$table_last_watering_name`(`mois`, `jour`, `heure`) VALUES (
        $table_last_watering_default_value,
        $table_last_watering_default_value,
        $table_last_watering_default_value)";

    $initialize_table_last_refill_query = "INSERT INTO `$table_last_refill_name`(`mois`, `jour`, `heure`) VALUES (
        $table_last_refill_default_value,
        $table_last_refill_default_value,
        $table_last_refill_default_value)";

    $db_connection->query($initialize_table_sensors_query);
    $db_connection->query($initialize_table_creds_query);
    $db_connection->query($initialize_table_last_watering_query);
    $db_connection->query($initialize_table_last_refill_query);
    $db_connection->close();

    return true;
}

function check_if_db_exists()
{
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

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);

    $retrieve_sensors_data_query = "SELECT * FROM $table_sensors_name";
    $inject_sensors_data_query = "UPDATE `$table_sensors_name` SET `CUVE1`='$cuve_1',`CUVE2`='$cuve_2',`CUVE3`='$cuve_3',`CUVE4`='$cuve_4',`HUMIDITE`='$humidite' WHERE 1";

    // We get the old humidity value from the database and compare it against the new one
    $sensors_data_result = $db_connection->query($retrieve_sensors_data_query);
    $sensors_row = $sensors_data_result->fetch_assoc();

    $old_humidity_value = $sensors_row['HUMIDITE'];

    if ($old_humidity_value == $table_sensors_default_value) {
        // The value in the DB is the default one,
        // 
        $update_month = date("m");
        $update_day = date("d");
        $update_hour = date("G");

        $table_last_watering_inject_query = "UPDATE `$table_last_watering_name` SET `mois`='$update_month',`jour`='$update_day',`heure`='$update_hour' WHERE 1";
        $db_connection->query($table_last_watering_inject_query);
    } elseif ($humidite > $old_humidity_value) {
        // The new humidity value is greater than the older one,
        // We need to update the last_watering table

        $update_month = date("m");
        $update_day = date("d");
        $update_hour = date("G");

        $table_last_watering_inject_query = "UPDATE `$table_last_watering_name` SET `mois`='$update_month',`jour`='$update_day',`heure`='$update_hour' WHERE 1";
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

        $water_tank_injection_query = "UPDATE `$table_last_refill_name` SET `mois`='$update_month',`jour`='$update_day',`heure`='$update_hour' WHERE 1";
        $db_connection->query($water_tank_injection_query);
    }

    // We inject the data to the sensors table
    $db_connection->query($inject_sensors_data_query);

    // We now need to add a new line to the history table, with the current date.
    insert_date_into_stats(date("m"), date("d"), date("G"));

    $db_connection->close();
}

function insert_date_into_stats($month, $day, $hour)
{
    /*
    * We insert a new date in the history table.
    */

    global $db_servername;
    global $db_username;
    global $db_password;
    global $table_history_name;
    global $db_name;

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);
    $insertion_query = "INSERT INTO `$table_history_name`(`mois`, `jour`, `heure`) VALUES ($month,$day,$hour)";

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

    $db_connection->close();

    $final_result = [
        'HUMIDITE_TERRE' => $humidity_value,
        'POURCENTAGE_CUVE' => $cuve_percentage,
        'DERNIERE_RECHARGE_CUVE_MOIS' => $last_refill_row['mois'],
        'DERNIERE_RECHARGE_CUVE_JOUR' => $last_refill_row['jour'],
        'DERNIERE_RECHARGE_CUVE_HEURE' => $last_refill_row['heure'],
        'DERNIER_ARROSAGE_MOIS' => $last_watering_row['mois'],
        'DERNIER_ARROSAGE_JOUR' => $last_watering_row['jour'],
        'DERNIER_ARROSAGE_HEURE' => $last_watering_row['heure']
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

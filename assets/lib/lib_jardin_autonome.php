<?php

$db_servername = "localhost";
$db_username = "root";
$db_password = "";

$db_name = "jardin";
$table_sensors_name = "capteurs";
$table_history_name = "historique";


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

    $db_connection->query($create_table_history_query);
    $db_connection->query($create_table_sensors_query);

    return true;

    $db_connection->close();
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

    $db_connection = new mysqli($db_servername, $db_username, $db_password);
    $inject_data_query = "UPDATE `$table_sensors_name` SET `CUVE1`=$cuve_1,`CUVE2`=$cuve_2,`CUVE3`=$cuve_3,`CUVE4`=$cuve_4,`HUMIDITE`=$humidite WHERE 1";

    $db_connection->query($inject_data_query);
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

    $db_connection = new mysqli($db_servername, $db_username, $db_password);
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

    $db_connection = new mysqli($db_servername, $db_username, $db_password);
    $truncate_query = "TRUNCATE TABLE $table_history_name";

    $db_connection->query($truncate_query);
    $db_connection->close();
}

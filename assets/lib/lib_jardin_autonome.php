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

    $initialize_table_sensors_query = "INSERT INTO `$table_sensors_name` (`CUVE1`, `CUVE2`, `CUVE3`, `CUVE4`, `HUMIDITE`) VALUES ('0', '0', '0', '0', '0')";
    $db_connection->query($initialize_table_sensors_query);

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

    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_name);
    $inject_data_query = "UPDATE `$table_sensors_name` SET `CUVE1`='$cuve_1',`CUVE2`='$cuve_2',`CUVE3`='$cuve_3',`CUVE4`='$cuve_4',`HUMIDITE`='$humidite' WHERE 1";

    // We inject the data to the sensors table
    $db_connection->query($inject_data_query);

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

    $humidite_value = $row['HUMIDITE'];
    $db_connection->close();

    $final_result = [
        'HUMIDITE_TERRE' => $humidite_value,
        'POURCENTAGE_CUVE' => $cuve_percentage
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

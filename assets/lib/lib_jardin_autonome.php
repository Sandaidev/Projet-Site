<?php

$db_servername = "localhost";
$db_username = "root";
$db_password = "";

$db_name = "jardin";



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
    if ($db_connection->query($create_db_query) != false) {
        die("Error creating database! Details: " . $db_connection->error);
    }
}

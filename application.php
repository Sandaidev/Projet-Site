<?php

require_once "./assets/lib/lib_jardin_autonome.php";

$db_data = return_formatted_sensor_table();
$app_json = json_encode($db_data, JSON_PRETTY_PRINT);

header("Content-Type: application/json");
echo $app_json;

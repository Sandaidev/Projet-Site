<?php

require_once "./assets/lib/lib_jardin_autonome.php";

$db_data = return_formatted_sensor_table();
$app_json = json_encode($db_data);

echo $app_json;

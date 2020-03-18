<?php
session_start();
require_once "./assets/lib/lib_jardin_autonome.php";
session_destroy();
echo "<script>window.location.replace('index.php');</script>";

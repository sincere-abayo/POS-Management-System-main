<?php
    $dbuser="root";
    $dbpass="";
    $host="localhost";
    $db="rposystem";
    $mysqli=new mysqli($host,$dbuser, $dbpass, $db);
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
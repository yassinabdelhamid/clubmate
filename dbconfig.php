<?php

$host = 'localhost';
$username = 'testuser';
$password = '';
$database = 'projektdb';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error){
    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}

?>
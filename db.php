<?php
$db_host = "host";//DataBase Connection 
$db_user = "user";
$db_password = "password";
$db_name = "database_name";
$_SERVER['HTTP_HOST'] = "hostname";
//Create Connection

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

//Check Connection


if (!$conn) {
    die("Connection Failed" . mysqli_connect_error());
}
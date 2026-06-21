<?php
$host = "127.0.0.1"; 
$user = "root";
$pass = "";
$dbname = "scfems";
$port = 3306; 


$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
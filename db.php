<?php
$host = "localhost";
$user = "root";          
$pass = "";              
$db   = "notehub";      

$conn = mysqli_connect($host, $user, $pass, $db);


if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


mysqli_set_charset($conn, "utf8mb4");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
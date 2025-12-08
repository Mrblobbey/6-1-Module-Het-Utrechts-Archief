<?php
$servername = "mysql_db";
$username = "root"; 
$password = "root";         
$dbname = "artikel";

// $servername = "localhost";
// $username = "root"; 
// $password = "";         
// $dbname = "artikel";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Verbinding mislukt: " . $e->getMessage());
}

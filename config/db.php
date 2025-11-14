<?php
$host = 'localhost';
$dbname = 'Flowpesa';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully to Flowpesa database!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

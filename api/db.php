<?php
// db.php
$dsn = 'mysql:host=localhost;dbname=flowpesa;charset=utf8mb4';
$user = 'flowpesa';
$pass = '';

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

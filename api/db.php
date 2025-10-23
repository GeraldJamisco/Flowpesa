<?php
// api/db.php
declare(strict_types=1);

function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;

  // ===== MySQL (production-ready) =====
  $host = '127.0.0.1';   // or your server host
  $db   = 'flowpesa';    // <-- DB name (change here if you pick a different one)
  $user = 'root';        // <-- your MySQL user
  $pass = '';      // <-- your MySQL password
  $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  return $pdo;
}

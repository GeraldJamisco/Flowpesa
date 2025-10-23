<?php
// api/create_tables.php
declare(strict_types=1);
require __DIR__ . '/db.php';

// MySQL/MariaDB-compatible schema (no SQLite-specific syntax)
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS users (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name           VARCHAR(100) NOT NULL,
  email          VARCHAR(255) NOT NULL UNIQUE,
  phone          VARCHAR(32),
  country        VARCHAR(64),
  password_hash  VARCHAR(255) NOT NULL,
  tier           TINYINT UNSIGNED NOT NULL DEFAULT 0,
  kyc_pct        TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;

db()->exec($sql);
echo "OK: tables ready\n";
?>

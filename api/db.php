
<?php
// Flowpesa DB connection

$host     = 'localhost';
$dbname   = 'flowpesa';          
$username = 'root';      
$password = '';     

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch as assoc arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // real prepared statements
];

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        $options
    );
} catch (PDOException $e) {

    // ----------- LOG ERROR SAFELY (PRODUCTION READY) -----------
    $logMessage = "[" . date('Y-m-d H:i:s') . "] DB ERROR: " . $e->getMessage() . "\n";
    
    // Path: create a folder called logs/ next to db.php (with 775 permission)
    error_log($logMessage, 3, __DIR__ . '/logs/db_errors.log');

    // Show SAFE message to user
    exit('Flowpesa database connection error. Please try again later.');
}


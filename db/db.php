<?php
$host = 'localhost';
$port = 5432;
$dbname = 'usermgr';
$user = 'postgres';
$password = '';

// Construct the DSN
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    // Create a PDO instance
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for error handling
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Set default fetch mode to associative array
    ]);

    echo "Connected to the PostgreSQL database successfully!";
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}

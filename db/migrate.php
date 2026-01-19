<?php


try {
    $dbConfig = require __DIR__ . '/../config/db.php';

    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}";

    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/postgres.sql');

    // Execute the SQL commands
    $pdo->exec($sql);

    echo "Schema executed successfully!\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
} catch (Exception $e) {
    die("Error executing schema: " . $e->getMessage());
}

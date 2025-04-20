<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

try {
    // Create new PDO connection to the SQLite database
    $pdo = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite3');

    // Set PDO to throw exceptions on error for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If connection fails, show error message,this is for secure part
    die("Database connection failed: " . $e->getMessage());
}
?>

<?php
// ====================================================================
// Database Configuration File
// Project: Blockchain-Based Ayurvedic Medicine System
// File: config/database.php
// ====================================================================

$host     = "localhost";
$db_name  = "ayurvedic_blockchain";
$username = "root";
$password = ""; // Default XAMPP MySQL password is empty

try {
    // Create PDO Connection
    $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4", $username, $password);
    
    // Set Error Mode to Exception for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Stop execution and show friendly message if connection fails
    die("<div style='color:red; font-family:sans-serif; padding:20px; text-align:center;'>
            <h2>Database Connection Failed!</h2>
            <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
            <p>Please make sure MySQL is running in XAMPP and the database <b>ayurvedic_blockchain</b> is created.</p>
         </div>");
}
?>

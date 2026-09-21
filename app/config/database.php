<?php
// ====================================================================
// Database Configuration
// Project: AYURCHAIN
// File: app/config/database.php
// ====================================================================

$host     = "localhost";
$db_name  = "ayurvedic_blockchain";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='color:red; font-family:sans-serif; padding:20px; text-align:center;'>
            <h2>Database Connection Failed!</h2>
            <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
            <p>Please make sure MySQL is running and database <b>ayurvedic_blockchain</b> exists.</p>
         </div>");
}
?>

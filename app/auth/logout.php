<?php
// ====================================================================
// Logout Script
// Project: Blockchain-Based Ayurvedic Medicine Storage & Verification System
// File: logout.php
// ====================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy session
session_destroy();

// Redirect to login page with logged out message
header("Location: login.php?msg=logged_out");
exit();
?>

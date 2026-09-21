<?php
// ====================================================================
// Authentication Helper & CSRF Security Token Module
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: includes/auth.php
// ====================================================================

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

/**
 * Guard Function: Protects admin-only pages.
 * Redirects unauthenticated requests to login.php.
 */
function check_admin_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php?msg=please_login");
        exit();
    }
}

/**
 * Helper Function: Check if current session is logged in as admin.
 * @return bool
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Generate CSRF Token for form protection
 * @return string
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate submitted CSRF Token
 * @param string $token
 * @return bool
 */
function verify_csrf_token($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}
?>

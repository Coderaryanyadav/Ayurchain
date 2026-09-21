<?php
// ====================================================================
// Authentication Helper & Session Guard
// Project: AYURCHAIN
// File: app/includes/auth.php
// ====================================================================

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

/**
 * Protect admin-only pages
 */
function check_admin_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        $login_url = get_base_url() . 'app/auth/login.php?msg=please_login';
        header("Location: " . $login_url);
        exit();
    }
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get base URL path for dynamic links
 */
function get_base_url() {
    // Detect base directory dynamically
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $pos = strpos($script_dir, '/app');
    if ($pos !== false) {
        return substr($script_dir, 0, $pos) . '/';
    }
    return '/';
}

/**
 * CSRF Token Generator
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token Verifier
 */
function verify_csrf_token($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}
?>

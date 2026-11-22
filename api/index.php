<?php
// ====================================================================
// Vercel Serverless Front Controller & Asset Router
// Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
// File: api/index.php
// ====================================================================

// Session storage configuration for serverless environments
if (!empty(getenv('VERCEL')) || !empty($_ENV['VERCEL'])) {
    if (session_status() === PHP_SESSION_NONE) {
        @ini_set('session.save_path', '/tmp');
    }
}

$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$parsed_url  = parse_url($request_uri);
$path        = $parsed_url['path'] ?? '/';

// Normalize path
$clean_path = ltrim($path, '/');
if (empty($clean_path)) {
    $clean_path = 'app/public/index.php';
}

// 1. Static file check (CSS, JS, images, PDF, contracts, icons, fonts)
$ext = strtolower(pathinfo($clean_path, PATHINFO_EXTENSION));
$static_exts = [
    'css'   => 'text/css',
    'js'    => 'application/javascript',
    'json'  => 'application/json',
    'png'   => 'image/png',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'gif'   => 'image/gif',
    'svg'   => 'image/svg+xml',
    'ico'   => 'image/x-icon',
    'pdf'   => 'application/pdf',
    'sol'   => 'text/plain',
    'sql'   => 'text/plain',
    'woff'  => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf'   => 'font/ttf'
];

if (isset($static_exts[$ext])) {
    $static_file = realpath(__DIR__ . '/../' . $clean_path);
    if ($static_file && file_exists($static_file) && is_file($static_file)) {
        header('Content-Type: ' . $static_exts[$ext]);
        header('Cache-Control: public, max-age=86400');
        readfile($static_file);
        exit();
    }
}

// 2. Map route aliases and root-level shortcuts to actual app scripts
$route_map = [
    'index.php'                      => 'app/public/index.php',
    'verify.php'                     => 'app/public/verify.php',
    'about.php'                      => 'app/public/about.php',
    'view_medicine.php'              => 'app/public/view_medicine.php',
    'medicines.php'                  => 'app/admin/medicines.php',
    'dashboard.php'                  => 'app/admin/dashboard.php',
    'history.php'                    => 'app/admin/history.php',
    'add_medicine.php'               => 'app/admin/add_medicine.php',
    'edit_medicine.php'              => 'app/admin/edit_medicine.php',
    'delete_medicine.php'            => 'app/admin/delete_medicine.php',
    'profile.php'                    => 'app/admin/profile.php',
    'change_password.php'            => 'app/admin/change_password.php',
    'login.php'                      => 'app/auth/login.php',
    'logout.php'                     => 'app/auth/logout.php',
    'api/save_blockchain_record.php' => 'app/api/save_blockchain_record.php',
    'api/add_history_record.php'     => 'app/api/add_history_record.php',
    'test_runner.php'                => 'tests/test_runner.php'
];

$target_script = $route_map[$clean_path] ?? $clean_path;

// 3. Resolve target file
$target_file = realpath(__DIR__ . '/../' . $target_script);

// If target doesn't exist, check without or with .php
if (!$target_file || !file_exists($target_file) || is_dir($target_file)) {
    if (isset($route_map[$clean_path . '.php'])) {
        $target_file = realpath(__DIR__ . '/../' . $route_map[$clean_path . '.php']);
    } elseif (file_exists(__DIR__ . '/../app/public/' . $clean_path . '.php')) {
        $target_file = realpath(__DIR__ . '/../app/public/' . $clean_path . '.php');
    } elseif (file_exists(__DIR__ . '/../app/admin/' . $clean_path . '.php')) {
        $target_file = realpath(__DIR__ . '/../app/admin/' . $clean_path . '.php');
    } else {
        $target_file = realpath(__DIR__ . '/../app/public/index.php');
    }
}

// Set environment variables for header navigation and get_base_url()
$_SERVER['SCRIPT_NAME'] = '/' . ltrim(str_replace(realpath(__DIR__ . '/..'), '', $target_file), '/\\');
$_SERVER['PHP_SELF']    = $_SERVER['SCRIPT_NAME'];

// Switch working directory so relative file operations and includes resolve reliably
chdir(dirname($target_file));

// Execute target application script
require $target_file;

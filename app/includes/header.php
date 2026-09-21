<?php
// ====================================================================
// Navigation & Header Include
// Project: AYURCHAIN
// File: app/includes/header.php
// ====================================================================
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
$base_url = get_base_url();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Theme CSS -->
    <link href="<?php echo $base_url; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-ayurchain sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center me-4" href="<?php echo $base_url; ?>app/public/index.php">
      <i class="fa-solid fa-seedling text-warning fa-xl me-2"></i>
      <div>
        <span class="fw-bold tracking-tight">AYUR<span class="text-warning">CHAIN</span></span>
        <span class="navbar-subtitle text-uppercase">Ayurvedic Storage & Verification</span>
      </div>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAyurContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarAyurContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php if ($current_page === 'index.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/public/index.php">
            <i class="fa-solid fa-house me-1"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if ($current_page === 'medicines.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/admin/medicines.php">
            <i class="fa-solid fa-pills me-1"></i> Medicines Directory
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if ($current_page === 'verify.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/public/verify.php">
            <i class="fa-solid fa-shield-halved me-1"></i> Verify Medicine
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if ($current_page === 'history.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/admin/history.php">
            <i class="fa-solid fa-cubes me-1"></i> Blockchain Records
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if ($current_page === 'about.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/public/about.php">
            <i class="fa-solid fa-circle-info me-1"></i> About Project
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
        <?php if (is_admin_logged_in()): ?>
          <li class="nav-item">
            <a class="btn btn-warning btn-sm text-dark font-weight-bold px-3 <?php if ($current_page === 'dashboard.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/admin/dashboard.php">
              <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-light btn-sm px-3" href="<?php echo $base_url; ?>app/admin/add_medicine.php">
              <i class="fa-solid fa-plus me-1"></i> Add Medicine
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($current_page === 'profile.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/admin/profile.php" title="Admin Profile">
              <i class="fa-solid fa-user-gear text-warning me-1"></i> Profile
            </a>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-danger btn-sm text-white px-3" href="<?php echo $base_url; ?>app/auth/logout.php">
              <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-gold btn-sm px-3 shadow-sm" href="<?php echo $base_url; ?>app/auth/login.php">
              <i class="fa-solid fa-user-lock me-1"></i> Admin Login
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="main-wrapper">

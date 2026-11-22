<?php
// ====================================================================
// Navigation & Header Include
// Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
// File: app/includes/header.php
// ====================================================================
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['PHP_SELF'] ?? '');
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Theme CSS -->
    <link href="<?php echo $base_url; ?>assets/css/style.css" rel="stylesheet">
    <script>window.BASE_URL = "<?php echo $base_url; ?>";</script>
</head>
<body>

<!-- Top Ayurvedic & Blockchain Network Strip -->
<div class="top-network-bar d-none d-md-block">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <span class="sanskrit-motto me-2">“स्वास्थस्य स्वास्थ्य रक्षणं, आतुरस्य विकार प्रशमनं च”</span>
      <span class="opacity-75 small">— चरक संहिता (Charaka Samhita)</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 py-1 px-2 font-monospace">
        <i class="fa-solid fa-circle text-success me-1 fa-xs"></i> Ethereum Ganache Live
      </span>
      <span class="text-white-50 small">
        <i class="fa-solid fa-cube text-warning me-1"></i> Block #101 Mined
      </span>
      <a href="<?php echo $base_url; ?>app/public/verify.php?medicine_id=AYU001" class="text-warning text-decoration-none small fw-semibold">
        <i class="fa-solid fa-bolt me-1"></i> Quick Test
      </a>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark navbar-ayurchain sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center me-4" href="<?php echo $base_url; ?>app/public/index.php">
      <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 42px; height: 42px;">
        <i class="fa-solid fa-leaf fa-lg"></i>
      </div>
      <div>
        <span class="fw-bold tracking-tight">AYUR<span class="text-warning">CHAIN</span></span>
        <span class="navbar-subtitle">Ayurvedic Storage & Blockchain Verification</span>
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
            <i class="fa-solid fa-timeline me-1"></i> Supply Chain Provenance
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if ($current_page === 'about.php') echo 'active'; ?>" href="<?php echo $base_url; ?>app/public/about.php">
            <i class="fa-solid fa-circle-info me-1"></i> About System
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <a href="<?php echo $base_url; ?>app/public/verify.php" class="btn btn-gold btn-sm px-3 shadow-sm">
          <i class="fa-solid fa-qrcode me-1"></i> Verify Batch
        </a>
        <?php if (is_admin_logged_in()): ?>
          <a href="<?php echo $base_url; ?>app/admin/dashboard.php" class="btn btn-outline-light btn-sm px-3">
            <i class="fa-solid fa-gauge-high me-1"></i> Admin Panel
          </a>
          <a href="<?php echo $base_url; ?>app/auth/logout.php" class="btn btn-outline-danger btn-sm" title="Log Out">
            <i class="fa-solid fa-right-from-bracket"></i>
          </a>
        <?php else: ?>
          <a href="<?php echo $base_url; ?>app/auth/login.php" class="btn btn-outline-light btn-sm px-3">
            <i class="fa-solid fa-user-lock me-1"></i> Admin Login
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<div class="main-wrapper">

<?php
// ====================================================================
// Home Landing Page
// Project: AYURCHAIN
// File: app/public/index.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';

$total_medicines  = 0;
$total_blockchain = 0;
$total_batches    = 0;

try {
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM medicines");
    $total_medicines = $stmt1->fetchColumn();

    $stmt2 = $pdo->query("SELECT COUNT(*) FROM blockchain_records");
    $total_blockchain = $stmt2->fetchColumn();

    $stmt3 = $pdo->query("SELECT COUNT(DISTINCT batch_number) FROM medicines");
    $total_batches = $stmt3->fetchColumn();
} catch (PDOException $e) {
    // Catch connection exception
}
?>

<!-- Hero Section -->
<section class="hero-section mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="badge bg-warning text-dark font-monospace fw-bold mb-3 px-3 py-2 rounded-pill shadow-sm">
                    <i class="fa-solid fa-leaf me-1"></i> AYURCHAIN VERIFICATION SYSTEM
                </div>
                <h1 class="hero-title mb-3">
                    AYURCHAIN<br>
                    <span>Blockchain-Based Ayurvedic Storage System</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    Ensuring botanical authenticity, quality lab certificate verification, and tamper-evident batch tracking for traditional Indian Ayurvedic medicines using Ethereum smart contracts.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo get_base_url(); ?>app/public/verify.php" class="btn btn-gold btn-lg shadow">
                        <i class="fa-solid fa-shield-check me-2"></i>Verify Medicine Batch
                    </a>
                    <a href="<?php echo get_base_url(); ?>app/admin/medicines.php" class="btn btn-outline-light btn-lg fw-semibold">
                        <i class="fa-solid fa-search me-2"></i>Search Directory
                    </a>
                    <?php if (!is_admin_logged_in()): ?>
                        <a href="<?php echo get_base_url(); ?>app/auth/login.php" class="btn btn-ayur btn-lg shadow-sm">
                            <i class="fa-solid fa-user-lock me-2"></i>Admin Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-5 mt-lg-0">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-mortar-pestle fa-6x text-warning mb-3"></i>
                    <h4 class="fw-bold text-white mb-1">Genuine Botanical Assurance</h4>
                    <p class="small text-light-green mb-0">Cryptographic SHA-256 File Hashing & Ethereum Ganache Integration</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">

    <!-- Quick Search Widget -->
    <div class="card card-ayur p-4 mb-5 shadow-sm border-start border-5 border-warning">
        <h4 class="fw-bold text-dark mb-2"><i class="fa-solid fa-magnifying-glass text-warning me-2"></i>Instant Medicine & Batch Verification Lookup</h4>
        <p class="text-muted small mb-3">Enter Medicine ID (e.g. <code>AYU-2026-001</code>) or Batch Number (e.g. <code>BATCH-AYU-2026-01</code>) to check off-chain and on-chain records.</p>
        <form method="GET" action="<?php echo get_base_url(); ?>app/public/verify.php" class="row g-2">
            <div class="col-md-9">
                <input type="text" name="medicine_id" class="form-control form-control-lg border-2" placeholder="Type Medicine ID or Batch Number..." required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-ayur btn-lg w-100 fw-bold">
                    <i class="fa-solid fa-search me-1"></i> Search & Verify
                </button>
            </div>
        </form>
    </div>

    <!-- Dynamic Metrics Counter Cards -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="display-6 fw-extrabold text-success mb-0"><?php echo $total_medicines; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Total Medicines Stored</span>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card gold-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="display-6 fw-extrabold text-warning mb-0"><?php echo $total_blockchain; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Blockchain Records</span>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card mint-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="display-6 fw-extrabold text-dark mb-0"><?php echo $total_batches; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Active Tracked Batches</span>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Pillars Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-ayur h-100 p-4 text-center">
                <div class="stat-icon bg-success text-white mx-auto mb-3 shadow-sm">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <h5 class="fw-bold text-dark">Botanical Metadata Management</h5>
                <p class="text-muted small">Stores detailed Ayurvedic attributes including Latin botanical names, formulation categories, active herbs, and source origins in MySQL.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-ayur h-100 p-4 text-center">
                <div class="stat-icon bg-warning text-dark mx-auto mb-3 shadow-sm">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <h5 class="fw-bold text-dark">Lab Certificate Hashing</h5>
                <p class="text-muted small">Computes cryptographic SHA-256 digital signatures for lab purity reports and AYUSH quality certificates directly in the browser.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-ayur h-100 p-4 text-center">
                <div class="stat-icon bg-dark text-warning mx-auto mb-3 shadow-sm">
                    <i class="fa-solid fa-link"></i>
                </div>
                <h5 class="fw-bold text-dark">Immutable Smart Contract</h5>
                <p class="text-muted small">Anchors critical batch hashes onto an Ethereum Smart Contract (Ganache), preventing counterfeiting or unauthorized record modification.</p>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

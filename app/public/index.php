<?php
// ====================================================================
// Home Landing Page - Ayurvedic Blockchain Verification Suite
// Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
// File: app/public/index.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';

$total_medicines  = 0;
$total_blockchain = 0;
$total_batches    = 0;
$featured_medicines = [];
$recent_blocks      = [];

try {
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM medicines");
    $total_medicines = $stmt1->fetchColumn();

    $stmt2 = $pdo->query("SELECT COUNT(*) FROM blockchain_records");
    $total_blockchain = $stmt2->fetchColumn();

    $stmt3 = $pdo->query("SELECT COUNT(DISTINCT batch_number) FROM medicines");
    $total_batches = $stmt3->fetchColumn();

    // Fetch featured Ayurvedic medicines
    $stmt_med = $pdo->query("
        SELECT m.*, b.transaction_hash, b.block_number, b.record_hash
        FROM medicines m
        LEFT JOIN blockchain_records b ON m.medicine_id = b.medicine_id
        ORDER BY m.id ASC LIMIT 4
    ");
    $featured_medicines = $stmt_med->fetchAll();

    // Fetch recent blockchain mined transactions
    $stmt_tx = $pdo->query("
        SELECT b.*, m.medicine_name, m.botanical_name, m.category
        FROM blockchain_records b
        JOIN medicines m ON b.medicine_id = m.medicine_id
        ORDER BY b.block_number DESC LIMIT 3
    ");
    $recent_blocks = $stmt_tx->fetchAll();

} catch (PDOException $e) {
    // Database connection fallback
}
?>

<!-- Hero Section -->
<section class="hero-section mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center badge bg-warning text-dark font-monospace fw-bold mb-3 px-3 py-2 rounded-pill shadow-sm">
                    <i class="fa-solid fa-leaf me-2"></i> AYUSH CERTIFIED &bull; ETHEREUM SMART CONTRACT VERIFIED
                </div>
                <h1 class="hero-title mb-3">
                    AYURCHAIN<br>
                    <span>Blockchain-Based Ayurvedic Storage & Verification</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    Ensuring botanical authenticity, lab test certificate integrity, and tamper-evident supply chain tracking for traditional Indian Ayurvedic medicines using Ethereum smart contracts and cryptographic SHA-256 digital signatures.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo get_base_url(); ?>app/public/verify.php" class="btn btn-gold btn-lg shadow">
                        <i class="fa-solid fa-shield-check me-2"></i>Verify Medicine Batch
                    </a>
                    <a href="<?php echo get_base_url(); ?>app/admin/medicines.php" class="btn btn-outline-light btn-lg fw-semibold">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Browse Directory
                    </a>
                    <a href="<?php echo get_base_url(); ?>app/public/verify.php?medicine_id=AYU001&tamper=1" class="btn btn-outline-warning btn-lg fw-semibold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>Tamper Alert Demo
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-5 mt-lg-0">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <div class="bg-warning text-dark rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 86px; height: 86px;">
                        <i class="fa-solid fa-mortar-pestle fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-white mb-2">Immutable Botanical Provenance</h4>
                    <p class="small text-white-50 mb-3">Client-Side SHA-256 PDF Certificate Hashing & Ethereum Distributed Ledger</p>
                    <div class="p-3 bg-black bg-opacity-30 rounded-3 text-start small font-monospace">
                        <div class="text-warning mb-1"><i class="fa-solid fa-link me-1"></i> Contract: <code>0xd8b9...fa8</code></div>
                        <div class="text-white-50 mb-1"><i class="fa-solid fa-cube me-1"></i> Block Height: #101, #102, #103</div>
                        <div class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Gas Cost: 0 Gwei (Read Checks)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">

    <!-- Instant Medicine & Batch Lookup Search Bar -->
    <div class="card card-ayur p-4 mb-5 shadow-sm border-start border-5 border-warning">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h4 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-magnifying-glass text-warning me-2"></i>Instant Medicine & Batch Verification Lookup
                </h4>
                <p class="text-muted small mb-3 mb-lg-0">Enter Medicine ID (e.g. <code>AYU001</code>) or Batch Number (e.g. <code>BATCH-ASH-2025-01</code>) to perform real-time cryptographic hash verification against off-chain records and the smart contract.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark border p-2 small font-monospace">
                    <i class="fa-solid fa-bolt text-warning me-1"></i> Zero-Gas Verification
                </span>
            </div>
        </div>
        <hr class="my-3 opacity-25">
        <form method="GET" action="<?php echo get_base_url(); ?>app/public/verify.php" class="row g-2">
            <div class="col-md-9">
                <input type="text" name="medicine_id" class="form-control form-control-lg border-2" placeholder="Type Medicine ID (AYU001, AYU002) or Batch Number..." required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-ayur btn-lg w-100 fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Verify On-Chain
                </button>
            </div>
        </form>
        <!-- Quick presentation chips -->
        <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2">
            <small class="text-muted fw-bold me-1">Quick Presentation Tests:</small>
            <a href="<?php echo get_base_url(); ?>app/public/verify.php?medicine_id=AYU001" class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 text-decoration-none py-2 px-3 rounded-pill">
                <i class="fa-solid fa-check me-1"></i> AYU001 (Ashwagandha)
            </a>
            <a href="<?php echo get_base_url(); ?>app/public/verify.php?medicine_id=AYU002" class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 text-decoration-none py-2 px-3 rounded-pill">
                <i class="fa-solid fa-check me-1"></i> AYU002 (Triphala Guti)
            </a>
            <a href="<?php echo get_base_url(); ?>app/public/verify.php?medicine_id=AYU-2026-001" class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 text-decoration-none py-2 px-3 rounded-pill">
                <i class="fa-solid fa-check me-1"></i> AYU-2026-001 (Chyawanprash)
            </a>
            <a href="<?php echo get_base_url(); ?>app/public/verify.php?medicine_id=AYU001&tamper=1" class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-decoration-none py-2 px-3 rounded-pill">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Tamper Simulation Alert
            </a>
        </div>
    </div>

    <!-- Dynamic Metrics Counter Cards -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="display-6 fw-extrabold text-success mb-0"><?php echo $total_medicines; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Medicines Stored</span>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card gold-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="display-6 fw-extrabold text-warning mb-0"><?php echo $total_blockchain; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Blockchain Blocks</span>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card mint-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="display-6 fw-extrabold text-dark mb-0"><?php echo $total_batches; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Tracked Batches</span>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card terracotta-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="display-6 fw-extrabold text-danger mb-0">100%</h2>
                        <span class="text-muted fw-semibold small text-uppercase">Tamper Proof</span>
                    </div>
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Ayurvedic Formulations Showcase -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-seedling text-success me-2"></i>Verified Ayurvedic Formulations</h3>
                <p class="text-muted small mb-0">Authentic classical remedies with immutable blockchain certificate hashes</p>
            </div>
            <a href="<?php echo get_base_url(); ?>app/admin/medicines.php" class="btn btn-outline-success btn-sm fw-semibold">
                View Full Directory <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($featured_medicines as $med): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="med-card">
                        <div class="med-card-top">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary font-monospace"><?php echo e($med['medicine_id']); ?></span>
                                <span class="badge badge-category"><?php echo e($med['category']); ?></span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1 text-truncate" title="<?php echo e($med['medicine_name']); ?>"><?php echo e($med['medicine_name']); ?></h5>
                            <small class="text-muted fst-italic d-block text-truncate"><?php echo e($med['botanical_name']); ?></small>
                        </div>
                        <div class="med-card-body">
                            <div class="small mb-2">
                                <span class="text-muted">Batch:</span> <code><?php echo e($med['batch_number']); ?></code>
                            </div>
                            <div class="small mb-3">
                                <span class="text-muted">Manufacturer:</span><br>
                                <strong class="text-dark"><?php echo e($med['manufacturer']); ?></strong>
                            </div>
                            <div class="p-2 bg-light rounded border small mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted fw-bold">Blockchain Status:</span>
                                    <?php if (!empty($med['transaction_hash'])): ?>
                                        <span class="badge bg-success font-monospace">Block #<?php echo e($med['block_number'] ?? '101'); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Off-Chain</span>
                                    <?php endif; ?>
                                </div>
                                <code class="text-truncate d-block small font-monospace text-muted" style="font-size: 0.72rem;">
                                    <?php echo !empty($med['transaction_hash']) ? substr($med['transaction_hash'], 0, 18) . '...' : 'Awaiting Ganache Mining'; ?>
                                </code>
                            </div>
                        </div>
                        <div class="med-card-footer">
                            <a href="<?php echo get_base_url(); ?>app/public/view_medicine.php?id=<?php echo urlencode($med['medicine_id']); ?>" class="btn btn-sm btn-outline-secondary">
                                Details & QR
                            </a>
                            <a href="<?php echo get_base_url(); ?>app/public/verify.php?medicine_id=<?php echo urlencode($med['medicine_id']); ?>" class="btn btn-sm btn-gold">
                                <i class="fa-solid fa-check me-1"></i> Verify
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- How AyurChain Works (Interactive 5-Stage Pipeline) -->
    <div class="card card-ayur p-4 p-lg-5 mb-5 shadow-sm border-top border-4 border-success">
        <div class="text-center mb-4">
            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill font-monospace mb-2">
                <i class="fa-solid fa-microchip me-1"></i> THE CRYPTOGRAPHIC PROVENANCE LIFECYCLE
            </span>
            <h3 class="fw-bold text-dark">How AyurChain Secures Traditional Medicine</h3>
            <p class="text-muted col-md-8 mx-auto small">Every herbal harvest, laboratory quality test, and logistics transit milestone is immutably sealed on Ethereum distributed ledger technology.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-6 col-lg-2-4 pipeline-step">
                <div class="pipeline-icon-box">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <h6 class="fw-bold mb-1">1. Wildcrafting & Harvest</h6>
                <p class="small text-muted mb-0">Ethical sourcing in Western Ghats & Madhya Pradesh reserves.</p>
            </div>
            <div class="col-6 col-lg-2-4 pipeline-step">
                <div class="pipeline-icon-box">
                    <i class="fa-solid fa-flask-vial"></i>
                </div>
                <h6 class="fw-bold mb-1">2. Lab Testing & Hash</h6>
                <p class="small text-muted mb-0">NABL test certificates hashed client-side via SHA-256 Web Crypto.</p>
            </div>
            <div class="col-6 col-lg-2-4 pipeline-step">
                <div class="pipeline-icon-box">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <h6 class="fw-bold mb-1">3. Smart Contract Anchor</h6>
                <p class="small text-muted mb-0">Mined immutably into Ethereum Ganache blocks.</p>
            </div>
            <div class="col-6 col-lg-2-4 pipeline-step">
                <div class="pipeline-icon-box">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h6 class="fw-bold mb-1">4. Supply Chain Transit</h6>
                <p class="small text-muted mb-0">Manufactured &rarr; Dispatched &rarr; Received lifecycle milestones.</p>
            </div>
            <div class="col-6 col-lg-2-4 pipeline-step">
                <div class="pipeline-icon-box">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h6 class="fw-bold mb-1">5. Mobile QR Verification</h6>
                <p class="small text-muted mb-0">Zero-gas public scan verifying bottle authenticity instantly.</p>
            </div>
        </div>
    </div>

    <!-- Live Blockchain Ledger Feed -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card card-ayur h-100 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-cubes text-warning me-2"></i>Live Blockchain Ledger Explorer
                    </h5>
                    <span class="badge bg-success font-monospace">Synchronized</span>
                </div>
                <p class="text-muted small mb-3">Recently mined transactions on Ganache Local Ethereum Testnet:</p>

                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Block</th>
                                <th>Medicine</th>
                                <th>Transaction Hash</th>
                                <th>Cryptographic Record Hash</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_blocks as $blk): ?>
                                <tr>
                                    <td><span class="badge bg-dark font-monospace">#<?php echo e($blk['block_number']); ?></span></td>
                                    <td>
                                        <strong><?php echo e($blk['medicine_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo e($blk['category']); ?></small>
                                    </td>
                                    <td>
                                        <code class="font-monospace text-primary" title="<?php echo e($blk['transaction_hash']); ?>">
                                            <?php echo substr($blk['transaction_hash'], 0, 14) . '...'; ?>
                                        </code>
                                    </td>
                                    <td>
                                        <code class="font-monospace text-muted" title="<?php echo e($blk['record_hash']); ?>">
                                            <?php echo substr($blk['record_hash'], 0, 12) . '...'; ?>
                                        </code>
                                    </td>
                                    <td>
                                        <span class="badge bg-success font-monospace">Mined</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-ayur h-100 p-4 shadow-sm border-start border-4 border-warning">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-scroll text-warning me-2"></i>Ayurvedic Quality Standards</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fa-solid fa-circle-check text-success fa-lg mt-1 me-2"></i>
                        <div>
                            <strong>NABL Accredited Lab Testing:</strong>
                            <p class="text-muted mb-0">Every batch undergoes rigorous chemical and botanical chromatography analysis.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fa-solid fa-circle-check text-success fa-lg mt-1 me-2"></i>
                        <div>
                            <strong>Heavy Metal & Pesticide Screen:</strong>
                            <p class="text-muted mb-0">Zero arsenic, lead, mercury, or synthetic contaminant thresholds met.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="fa-solid fa-circle-check text-success fa-lg mt-1 me-2"></i>
                        <div>
                            <strong>SHA-256 Immutability:</strong>
                            <p class="text-muted mb-0">Lab certificates cannot be substituted, modified, or backdated post-issuance.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

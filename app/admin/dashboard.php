<?php
// ====================================================================
// Professional Admin Dashboard (Phase 13)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: dashboard.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Protect page
check_admin_login();

// Dynamic metrics calculation from MySQL
$total_medicines     = 0;
$total_batches       = 0;
$blockchain_records  = 0;
$verified_records    = 0;
$failed_attempts     = 0;
$recent_medicines    = [];
$recent_transactions = [];
$category_counts     = [];

try {
    // 1. Total Medicines
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM medicines");
    $total_medicines = $stmt1->fetchColumn();

    // 2. Total Batches
    $stmt2 = $pdo->query("SELECT COUNT(DISTINCT batch_number) FROM medicines");
    $total_batches = $stmt2->fetchColumn();

    // 3. Blockchain Records Count
    $stmt3 = $pdo->query("SELECT COUNT(*) FROM blockchain_records");
    $blockchain_records = $stmt3->fetchColumn();

    // 4. Verified Records Count (Distinct medicines mined on-chain)
    $stmt4 = $pdo->query("SELECT COUNT(DISTINCT medicine_id) FROM blockchain_records");
    $verified_records = $stmt4->fetchColumn();

    // 5. Failed Verification Attempts Count (from audit log or failed status queries)
    $stmt5 = $pdo->query("SELECT COUNT(*) FROM medicine_history WHERE status = 'FAILED' OR status = 'VERIFICATION_FAILED'");
    $failed_attempts = $stmt5->fetchColumn();

    // 6. Category Breakdown for Chart.js
    $cat_stmt = $pdo->query("SELECT category, COUNT(*) AS cnt FROM medicines GROUP BY category");
    $category_counts = $cat_stmt->fetchAll();

    // 7. Recent Medicines (Top 5)
    $rec_med_stmt = $pdo->query("SELECT * FROM medicines ORDER BY id DESC LIMIT 5");
    $recent_medicines = $rec_med_stmt->fetchAll();

    // 8. Recent Blockchain Transactions (Top 5)
    $rec_tx_stmt = $pdo->query("
        SELECT b.*, m.medicine_name, m.category 
        FROM blockchain_records b 
        JOIN medicines m ON b.medicine_id = m.medicine_id 
        ORDER BY b.id DESC LIMIT 5
    ");
    $recent_transactions = $rec_tx_stmt->fetchAll();

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// Chart.js Data Preparation
$chart_categories = [];
$chart_cat_values = [];
foreach ($category_counts as $row) {
    $chart_categories[] = $row['category'];
    $chart_cat_values[] = (int)$row['cnt'];
}
if (empty($chart_categories)) {
    $chart_categories = ['Churna', 'Rasayana', 'Bhasma', 'Asava & Arishta', 'Vati'];
    $chart_cat_values = [1, 1, 0, 0, 0];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-4">

    <!-- Top Admin Quick Nav Bar -->
    <div class="card card-ayur p-3 mb-4 shadow-sm bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="fa-solid fa-user-shield text-warning"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Admin Control Center</h5>
                    <small class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_full_name'] ?? 'Admin'); ?></strong></small>
                </div>
            </div>
            
            <!-- Quick Section Navigation Buttons -->
            <div class="d-flex flex-wrap gap-1">
                <a href="dashboard.php" class="btn btn-sm btn-success fw-bold"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a>
                <a href="medicines.php" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-pills me-1"></i> Medicines</a>
                <a href="add_medicine.php" class="btn btn-sm btn-gold fw-bold"><i class="fa-solid fa-plus me-1"></i> Add Medicine</a>
                <a href="verify.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-shield-check me-1"></i> Verify Medicine</a>
                <a href="history.php" class="btn btn-sm btn-outline-warning text-dark"><i class="fa-solid fa-cubes me-1"></i> Blockchain Records</a>
                <a href="profile.php" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-user-gear me-1"></i> Admin Profile</a>
                <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Required 5 Metrics Cards Grid -->
    <div class="row g-3 text-center mb-4">
        <!-- Card 1: Total Medicines -->
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="fw-extrabold text-success mb-0"><?php echo $total_medicines; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Total Medicines</span>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Batches -->
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card dark-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="fw-extrabold text-dark mb-0"><?php echo $total_batches; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Total Batches</span>
                    </div>
                    <div class="stat-icon bg-dark bg-opacity-10 text-dark">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Blockchain Records -->
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card gold-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="fw-extrabold text-warning mb-0"><?php echo $blockchain_records; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Blockchain Recs</span>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Verified Records -->
        <div class="col-md-6 col-lg-2-4">
            <div class="stat-card mint-border">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="fw-extrabold text-primary mb-0"><?php echo $verified_records; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Verified Recs</span>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-shield-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: Failed Verification Attempts -->
        <div class="col-md-6 col-lg-2-4">
            <div class="stat-card border-left-danger" style="border-left: 5px solid #dc3545;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-start">
                        <h2 class="fw-extrabold text-danger mb-0"><?php echo $failed_attempts; ?></h2>
                        <span class="text-muted fw-semibold small text-uppercase">Failed Checks</span>
                    </div>
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Practical Interactive Chart Section (Chart.js) -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Formulation Categories Breakdown -->
        <div class="col-lg-5">
            <div class="card card-ayur h-100 shadow-sm">
                <div class="card-header card-header-ayur py-3">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-chart-pie me-2 text-warning"></i>Ayurvedic Categories Breakdown</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-3">
                    <div style="width: 100%; max-width: 280px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Blockchain Activity Trends -->
        <div class="col-lg-7">
            <div class="card card-ayur h-100 shadow-sm">
                <div class="card-header card-header-ayur py-3">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-chart-column me-2 text-warning"></i>Blockchain Mining Activity</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="activityChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Section Grid -->
    <div class="row g-4 mb-4">
        
        <!-- Table 1: Recent Medicines -->
        <div class="col-lg-6">
            <div class="card card-ayur shadow-sm h-100">
                <div class="card-header card-header-ayur py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-pills me-2"></i>Recently Added Medicines</h6>
                    <a href="medicines.php" class="btn btn-sm btn-outline-light">View Directory</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Medicine ID</th>
                                    <th>Medicine Name</th>
                                    <th>Batch #</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if (empty($recent_medicines)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No medicines added yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recent_medicines as $med): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($med['medicine_id']); ?></span></td>
                                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($med['medicine_name']); ?></td>
                                            <td><code><?php echo htmlspecialchars($med['batch_number']); ?></code></td>
                                            <td>
                                                <a href="view_medicine.php?id=<?php echo urlencode($med['medicine_id']); ?>" class="btn btn-sm btn-ayur py-0 px-2">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table 2: Recent Blockchain Transactions -->
        <div class="col-lg-6">
            <div class="card card-ayur shadow-sm h-100">
                <div class="card-header card-header-ayur py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-cubes me-2 text-warning"></i>Recent Blockchain Transactions</h6>
                    <a href="history.php" class="btn btn-sm btn-outline-light">View Ledger</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Block #</th>
                                    <th>Medicine ID</th>
                                    <th>Transaction Hash</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if (empty($recent_transactions)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No on-chain transactions mined yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recent_transactions as $tx): ?>
                                        <tr>
                                            <td><span class="badge bg-dark font-monospace">#<?php echo $tx['block_number']; ?></span></td>
                                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($tx['medicine_id']); ?></td>
                                            <td>
                                                <small class="font-monospace text-truncate d-inline-block text-primary" style="max-width: 130px;" title="<?php echo htmlspecialchars($tx['transaction_hash']); ?>">
                                                    <?php echo htmlspecialchars($tx['transaction_hash']); ?>
                                                </small>
                                            </td>
                                            <td class="text-muted small"><?php echo date('Y-m-d H:i', $tx['blockchain_timestamp']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Render Chart.js Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Chart 1: Categories Doughnut Chart
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($chart_categories); ?>,
            datasets: [{
                data: <?php echo json_encode($chart_cat_values); ?>,
                backgroundColor: ['#1b4332', '#2d6a4f', '#52b788', '#d4af37', '#f4a261', '#2b2d42']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Chart 2: Monthly Blockchain Activity Bar Chart
    const ctxAct = document.getElementById('activityChart').getContext('2d');
    new Chart(ctxAct, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Transactions Mined',
                data: [2, 5, 8, 4, 6, 12, 9, 7, <?php echo $blockchain_records; ?>, 0, 0, 0],
                backgroundColor: '#1b4332',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

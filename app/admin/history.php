<?php
// ====================================================================
// Blockchain Supply Chain Lifecycle History (Phase 12)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: history.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$filter_med_id = trim($_GET['medicine_id'] ?? '');

// Fetch all registered medicines for admin selection dropdown
$all_medicines = [];
try {
    $med_stmt = $pdo->query("SELECT medicine_id, medicine_name, batch_number FROM medicines ORDER BY id DESC");
    $all_medicines = $med_stmt->fetchAll();
} catch (PDOException $e) {
    // Database error
}

// Fetch history records
$history_sql = "
    SELECT 
        h.*, 
        m.medicine_name, 
        m.botanical_name 
    FROM medicine_history h
    JOIN medicines m ON h.medicine_id = m.medicine_id
";
$params = [];

if (!empty($filter_med_id)) {
    $history_sql .= " WHERE h.medicine_id = :mid";
    $params['mid'] = $filter_med_id;
}

$history_sql .= " ORDER BY h.id ASC";

try {
    $hist_stmt = $pdo->prepare($history_sql);
    $hist_stmt->execute($params);
    $history_records = $hist_stmt->fetchAll();
} catch (PDOException $e) {
    $error = $e->getMessage();
}

// Define the 5 tracked lifecycle stages
$tracked_stages = ["Manufactured", "Dispatched", "Received", "Distributed", "Sold"];
$completed_stages = [];

foreach ($history_records as $rec) {
    if (in_array($rec['status'], $tracked_stages)) {
        $completed_stages[$rec['status']] = $rec;
    }
}
?>

<div class="container my-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fa-solid fa-timeline text-warning me-2"></i>Supply Chain Blockchain History</h2>
            <p class="text-muted small mb-0">Track medicine status progression: <strong>Manufactured &rarr; Dispatched &rarr; Received &rarr; Distributed &rarr; Sold</strong></p>
        </div>
        <span class="badge bg-dark px-3 py-2 font-monospace"><i class="fa-solid fa-link text-warning me-1"></i> Ganache Lifecycle Ledger</span>
    </div>

    <!-- Admin Status Update Panel (Only for Logged-In Admin) -->
    <?php if (is_admin_logged_in()): ?>
        <div class="card card-ayur mb-5 shadow-sm border-start border-4 border-success">
            <div class="card-header card-header-ayur py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2"></i>Add New Supply Chain Status Update (MetaMask On-Chain Mining)</h5>
            </div>
            <div class="card-body p-4">
                
                <form id="form_add_history">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Select Medicine Batch <span class="text-danger">*</span></label>
                            <select id="hist_medicine_id" class="form-select" required>
                                <option value="">-- Choose Medicine --</option>
                                <?php foreach ($all_medicines as $m): ?>
                                    <option value="<?php echo htmlspecialchars($m['medicine_id']); ?>" data-batch="<?php echo htmlspecialchars($m['batch_number']); ?>" <?php if ($filter_med_id === $m['medicine_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($m['medicine_id']); ?> - <?php echo htmlspecialchars($m['medicine_name']); ?> (<?php echo htmlspecialchars($m['batch_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status Stage <span class="text-danger">*</span></label>
                            <select id="hist_status" class="form-select" required>
                                <option value="Manufactured">1. Manufactured</option>
                                <option value="Dispatched">2. Dispatched</option>
                                <option value="Received">3. Received</option>
                                <option value="Distributed">4. Distributed</option>
                                <option value="Sold">5. Sold</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Location Node</label>
                            <input type="text" id="hist_location" class="form-control" placeholder="e.g. Haridwar Factory / Delhi Depot" required>
                        </div>
                    </div>

                    <div id="hist_status_box" class="alert alert-info d-none mt-3 p-3">
                        <span id="hist_status_text"></span>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="button" id="btn_submit_status" class="btn btn-gold px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-link me-1"></i> Mine Status Update via MetaMask
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <script>
        document.getElementById('btn_submit_status').addEventListener('click', async function() {
            const medSelect = document.getElementById('hist_medicine_id');
            const statusSelect = document.getElementById('hist_status');
            const locationInput = document.getElementById('hist_location');

            const medId = medSelect.value;
            const selectedOption = medSelect.options[medSelect.selectedIndex];
            const batchNo = selectedOption ? selectedOption.getAttribute('data-batch') : '';
            const statusVal = statusSelect.value;
            const locationVal = locationInput.value;

            const box = document.getElementById('hist_status_box');
            const text = document.getElementById('hist_status_text');

            if (!medId || !statusVal || !locationVal) {
                alert("Please select a Medicine ID, Status Stage, and enter Location!");
                return;
            }

            this.disabled = true;
            box.classList.remove('d-none');
            box.className = "alert alert-info mt-3 p-3";
            text.innerHTML = "<i class='fa-solid fa-spinner fa-spin me-2'></i>Opening MetaMask... Approve transaction signature to mine status.";

            try {
                await connectMetaMaskWallet();
                
                text.innerHTML = "<i class='fa-solid fa-spinner fa-spin me-2'></i>Transaction submitted to Ganache... Mining block...";
                
                const result = await addStatusUpdateToBlockchain(medId, batchNo, statusVal, locationVal);

                box.className = "alert alert-success mt-3 p-3";
                text.innerHTML = "<i class='fa-solid fa-circle-check me-2'></i><strong>STATUS MINED ON BLOCKCHAIN!</strong> Block #" + result.block_number + "<br><small class='font-monospace text-break'>Tx Hash: " + result.transaction_hash + "</small>";

                setTimeout(function() {
                    window.location.href = "history.php?medicine_id=" + encodeURIComponent(medId);
                }, 2500);

            } catch (err) {
                this.disabled = false;
                box.className = "alert alert-danger mt-3 p-3";
                text.innerHTML = "<i class='fa-solid fa-circle-xmark me-2'></i><strong>Mining Error:</strong> " + err.message;
            }
        });
        </script>
    <?php endif; ?>

    <!-- Filter Bar by Medicine -->
    <div class="card card-ayur p-3 mb-4 bg-white shadow-sm">
        <form method="GET" action="history.php" class="row g-2 align-items-center">
            <div class="col-md-8">
                <select name="medicine_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- All Medicines Supply Chain Histories --</option>
                    <?php foreach ($all_medicines as $m): ?>
                        <option value="<?php echo htmlspecialchars($m['medicine_id']); ?>" <?php if ($filter_med_id === $m['medicine_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($m['medicine_id']); ?> - <?php echo htmlspecialchars($m['medicine_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 text-end">
                <?php if (!empty($filter_med_id)): ?>
                    <a href="history.php" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Visual Vertical Supply Chain Timeline Display -->
    <div class="card card-ayur p-4 mb-5 shadow-sm">
        <h4 class="fw-bold text-dark mb-4 text-center">
            <i class="fa-solid fa-route text-success me-2"></i>Supply Chain Progression Timeline
            <?php if (!empty($filter_med_id)) echo " for " . htmlspecialchars($filter_med_id); ?>
        </h4>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="timeline-container">
                    <?php 
                    $stage_index = 1;
                    foreach ($tracked_stages as $stage): 
                        $is_done = isset($completed_stages[$stage]);
                        $rec_info = $is_done ? $completed_stages[$stage] : null;
                    ?>
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3">
                                <?php if ($is_done): ?>
                                    <span class="badge bg-success rounded-circle p-3 shadow-sm fs-5"><i class="fa-solid fa-check text-white"></i></span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border rounded-circle p-3 fs-5"><?php echo $stage_index; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 p-3 rounded border <?php echo $is_done ? 'border-success bg-light' : 'bg-white opacity-75'; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0 <?php echo $is_done ? 'text-success' : 'text-muted'; ?>">
                                        <?php echo $stage_index; ?>. <?php echo $stage; ?>
                                    </h5>
                                    <?php if ($is_done): ?>
                                        <span class="badge bg-success font-monospace">ON-CHAIN MINED</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pending</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($is_done): ?>
                                    <p class="mb-1 small text-dark mt-2">
                                        <strong>Location:</strong> <?php echo htmlspecialchars($rec_info['location'] ?? 'N/A'); ?> | 
                                        <strong>Timestamp:</strong> <?php echo date('Y-m-d H:i:s', strtotime($rec_info['performed_at'])); ?>
                                    </p>
                                    <?php if (!empty($rec_info['transaction_hash'])): ?>
                                        <small class="font-monospace text-break text-muted d-block">
                                            <strong>Tx Hash:</strong> <?php echo htmlspecialchars($rec_info['transaction_hash']); ?>
                                        </small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($stage_index < 5): ?>
                            <div class="text-center my-1">
                                <i class="fa-solid fa-arrow-down <?php echo $is_done ? 'text-success' : 'text-muted opacity-50'; ?> fa-lg"></i>
                            </div>
                        <?php endif; ?>

                    <?php 
                        $stage_index++;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Ledger Table with Transaction Hashes -->
    <div class="card card-ayur shadow-sm">
        <div class="card-header card-header-ayur py-3">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list-check me-2"></i>Detailed Blockchain History Ledger</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-ayur">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Medicine ID</th>
                            <th>Medicine Name</th>
                            <th>Stage Status</th>
                            <th>Location Node</th>
                            <th>Transaction Hash</th>
                            <th>Block #</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history_records)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No supply chain history logged yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($history_records as $rec): ?>
                                <tr>
                                    <td>#<?php echo $rec['id']; ?></td>
                                    <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($rec['medicine_id']); ?></span></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($rec['medicine_name']); ?></td>
                                    <td><span class="badge badge-category"><?php echo htmlspecialchars($rec['status']); ?></span></td>
                                    <td><small class="fw-semibold text-dark"><?php echo htmlspecialchars($rec['location'] ?? 'Factory'); ?></small></td>
                                    <td>
                                        <?php if (!empty($rec['transaction_hash'])): ?>
                                            <small class="font-monospace text-truncate d-inline-block text-primary" style="max-width: 140px;" title="<?php echo htmlspecialchars($rec['transaction_hash']); ?>">
                                                <?php echo htmlspecialchars($rec['transaction_hash']); ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border">Off-Chain</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($rec['block_number'])): ?>
                                            <span class="badge bg-dark font-monospace">#<?php echo $rec['block_number']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($rec['performed_at']); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

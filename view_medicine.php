<?php
// ====================================================================
// View Medicine Details (Phase 7)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: view_medicine.php
// ====================================================================

require_once 'config/database.php';
require_once 'includes/header.php';

$medicine_id = trim($_GET['id'] ?? '');

if (empty($medicine_id)) {
    echo "<div class='container my-4'><div class='alert alert-danger'>Medicine ID is required!</div></div>";
    require_once 'includes/footer.php';
    exit();
}

try {
    // Fetch medicine details
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE medicine_id = :id LIMIT 1");
    $stmt->execute(['id' => $medicine_id]);
    $medicine = $stmt->fetch();

    if (!$medicine) {
        echo "<div class='container my-4'><div class='alert alert-danger'>Medicine record not found!</div></div>";
        require_once 'includes/footer.php';
        exit();
    }

    // Fetch documents
    $doc_stmt = $pdo->prepare("SELECT * FROM medicine_documents WHERE medicine_id = :id");
    $doc_stmt->execute(['id' => $medicine_id]);
    $documents = $doc_stmt->fetchAll();

    // Fetch blockchain record
    $bc_stmt = $pdo->prepare("SELECT * FROM blockchain_records WHERE medicine_id = :id LIMIT 1");
    $bc_stmt->execute(['id' => $medicine_id]);
    $blockchain_record = $bc_stmt->fetch();

} catch (PDOException $e) {
    echo "<div class='container my-4'><div class='alert alert-danger'>Error: " . $e->getMessage() . "</div></div>";
    require_once 'includes/footer.php';
    exit();
}
?>

<div class="container my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="medicines.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Directory
        </a>
        <?php if (is_admin_logged_in()): ?>
            <div class="btn-group">
                <a href="edit_medicine.php?id=<?php echo urlencode($medicine['medicine_id']); ?>" class="btn btn-warning btn-sm font-weight-bold">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Metadata
                </a>
                <a href="delete_medicine.php?id=<?php echo urlencode($medicine['medicine_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete record <?php echo htmlspecialchars($medicine['medicine_id']); ?>?');">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Left Column: Medicine Profile -->
        <div class="col-lg-7">
            <div class="card card-ayur shadow-sm">
                <div class="card-header card-header-ayur py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold"><?php echo htmlspecialchars($medicine['medicine_name']); ?></h4>
                    <span class="badge bg-warning text-dark font-monospace px-3 py-2 fs-6"><?php echo htmlspecialchars($medicine['medicine_id']); ?></span>
                </div>
                <div class="card-body p-4">
                    <table class="table table-bordered align-middle">
                        <tr>
                            <th width="35%" class="bg-light">Botanical Name</th>
                            <td><em><?php echo htmlspecialchars($medicine['botanical_name']); ?></em></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Category</th>
                            <td><span class="badge badge-category"><?php echo htmlspecialchars($medicine['category']); ?></span></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Batch Number</th>
                            <td><code><?php echo htmlspecialchars($medicine['batch_number']); ?></code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Form / State</th>
                            <td><?php echo htmlspecialchars($medicine['form']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Manufacturer</th>
                            <td><?php echo htmlspecialchars($medicine['manufacturer']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Source / Origin</th>
                            <td><?php echo htmlspecialchars($medicine['source']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Manufacturing Date</th>
                            <td><?php echo htmlspecialchars($medicine['manufacturing_date']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Expiry Date</th>
                            <td><?php echo htmlspecialchars($medicine['expiry_date']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Active Herbs</th>
                            <td><?php echo nl2br(htmlspecialchars($medicine['ingredients'])); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Description</th>
                            <td><?php echo nl2br(htmlspecialchars($medicine['description'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Blockchain Storage & Certificate Hashes -->
        <div class="col-lg-5">
            
            <!-- Blockchain Status Card -->
            <div class="card card-ayur mb-4 border-start border-5 border-warning shadow-sm">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    <i class="fa-solid fa-cubes text-warning me-2"></i>Blockchain Immutability Ledger
                </div>
                <div class="card-body">
                    <?php if ($blockchain_record): ?>
                        <div class="alert alert-success d-flex align-items-center p-3 mb-3">
                            <i class="fa-solid fa-certificate fa-2x text-success me-3"></i>
                            <div>
                                <strong class="text-success">ON-CHAIN VERIFIED</strong><br>
                                <small class="text-dark">Record immutably anchored on Ethereum local testnet.</small>
                            </div>
                        </div>
                        <p class="mb-1 small fw-bold">Transaction Hash:</p>
                        <p class="text-break bg-light p-2 rounded border font-monospace small">
                            <?php echo htmlspecialchars($blockchain_record['transaction_hash']); ?>
                        </p>
                        <p class="mb-1 small fw-bold">Record SHA-256 Hash:</p>
                        <p class="text-break bg-light p-2 rounded border font-monospace small">
                            <?php echo htmlspecialchars($blockchain_record['record_hash']); ?>
                        </p>
                        <p class="mb-0 small fw-bold">Block Number: <span class="badge bg-secondary">#<?php echo $blockchain_record['block_number']; ?></span></p>
                    <?php else: ?>
                        <div class="alert alert-warning p-3 mb-0">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <strong>Off-Chain Record in MySQL</strong><br>
                            <small>Record exists in MySQL database. Awaiting smart contract mining on Ganache.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quality Certificates Card -->
            <div class="card card-ayur shadow-sm">
                <div class="card-header card-header-ayur py-3 fw-bold">
                    <i class="fa-solid fa-file-pdf me-2"></i>Quality Lab Certificates
                </div>
                <div class="card-body p-3">
                    <?php if (empty($documents)): ?>
                        <p class="text-muted mb-0 small">No PDF or image certificate attached to this record.</p>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="border rounded p-3 mb-2 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-file-pdf fa-2x text-danger me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($doc['document_name']); ?></h6>
                                        <small class="text-muted"><?php echo round($doc['file_size'] / 1024, 2); ?> KB</small>
                                    </div>
                                </div>
                                <small class="d-block text-muted mb-1 fw-bold">SHA-256 Certificate Hash:</small>
                                <code class="text-break d-block bg-white p-2 rounded border small mb-2 font-monospace">
                                    <?php echo htmlspecialchars($doc['certificate_hash']); ?>
                                </code>
                                <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-danger w-100 fw-semibold">
                                    <i class="fa-solid fa-download me-1"></i> View Document
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>

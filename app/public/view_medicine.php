<?php
// ====================================================================
// View Medicine Details & QR Code Generator (Phase 9 Integration)
// Project: AYURCHAIN
// File: app/public/view_medicine.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';

$medicine_id = trim($_GET['id'] ?? '');

if (empty($medicine_id)) {
    echo "<div class='container my-4'><div class='alert alert-danger'>Medicine ID is required!</div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE medicine_id = :id LIMIT 1");
    $stmt->execute(['id' => $medicine_id]);
    $medicine = $stmt->fetch();

    if (!$medicine) {
        echo "<div class='container my-4'><div class='alert alert-danger'>Medicine record not found!</div></div>";
        require_once __DIR__ . '/../includes/footer.php';
        exit();
    }

    $doc_stmt = $pdo->prepare("SELECT * FROM medicine_documents WHERE medicine_id = :id");
    $doc_stmt->execute(['id' => $medicine_id]);
    $documents = $doc_stmt->fetchAll();

    $bc_stmt = $pdo->prepare("SELECT * FROM blockchain_records WHERE medicine_id = :id LIMIT 1");
    $bc_stmt->execute(['id' => $medicine_id]);
    $blockchain_record = $bc_stmt->fetch();

} catch (PDOException $e) {
    echo "<div class='container my-4'><div class='alert alert-danger'>Error: " . e($e->getMessage()) . "</div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

// Generate Public Verification URL for QR Code (Phase 9)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$public_verify_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . get_base_url() . "app/public/verify.php?medicine_id=" . urlencode($medicine['medicine_id']);
?>

<div class="container my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?php echo get_base_url(); ?>app/admin/medicines.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Directory
        </a>
        <div class="d-flex gap-2">
            <!-- Phase 9: QR Code Modal Button -->
            <button class="btn btn-warning btn-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
                <i class="fa-solid fa-qrcode me-1"></i> Generate QR Code
            </button>

            <?php if (is_admin_logged_in()): ?>
                <a href="<?php echo get_base_url(); ?>app/admin/edit_medicine.php?id=<?php echo urlencode($medicine['medicine_id']); ?>" class="btn btn-outline-warning text-dark btn-sm font-weight-bold">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Metadata
                </a>
                <a href="<?php echo get_base_url(); ?>app/admin/delete_medicine.php?id=<?php echo urlencode($medicine['medicine_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete record <?php echo e($medicine['medicine_id']); ?>?');">
                    <i class="fa-solid fa-trash-can me-1"></i> Delete
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Medicine Profile -->
        <div class="col-lg-7">
            <div class="card card-ayur shadow-sm">
                <div class="card-header card-header-ayur py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold"><?php echo e($medicine['medicine_name']); ?></h4>
                    <span class="badge bg-warning text-dark font-monospace px-3 py-2 fs-6"><?php echo e($medicine['medicine_id']); ?></span>
                </div>
                <div class="card-body p-4">
                    <table class="table table-bordered align-middle">
                        <tr>
                            <th width="35%" class="bg-light">Botanical Name</th>
                            <td><em><?php echo e($medicine['botanical_name']); ?></em></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Category</th>
                            <td><span class="badge badge-category"><?php echo e($medicine['category']); ?></span></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Batch Number</th>
                            <td><code><?php echo e($medicine['batch_number']); ?></code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Form / State</th>
                            <td><?php echo e($medicine['form']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Manufacturer</th>
                            <td><?php echo e($medicine['manufacturer']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Source / Origin</th>
                            <td><?php echo e($medicine['source']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Manufacturing Date</th>
                            <td><?php echo e($medicine['manufacturing_date']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Expiry Date</th>
                            <td><?php echo e($medicine['expiry_date']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Active Herbs</th>
                            <td><?php echo nl2br(e($medicine['ingredients'])); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Description</th>
                            <td><?php echo nl2br(e($medicine['description'])); ?></td>
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
                            <?php echo e($blockchain_record['transaction_hash']); ?>
                        </p>
                        <p class="mb-1 small fw-bold">Record SHA-256 Hash:</p>
                        <p class="text-break bg-light p-2 rounded border font-monospace small">
                            <?php echo e($blockchain_record['record_hash']); ?>
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
                                        <h6 class="mb-0 fw-bold"><?php echo e($doc['document_name']); ?></h6>
                                        <small class="text-muted"><?php echo round($doc['file_size'] / 1024, 2); ?> KB</small>
                                    </div>
                                </div>
                                <small class="d-block text-muted mb-1 fw-bold">SHA-256 Certificate Hash:</small>
                                <code class="text-break d-block bg-white p-2 rounded border small mb-2 font-monospace">
                                    <?php echo e($doc['certificate_hash']); ?>
                                </code>
                                <a href="<?php echo get_base_url() . e($doc['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-danger w-100 fw-semibold">
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

<!-- Phase 9: QR Code Generation Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header card-header-ayur">
        <h5 class="modal-title fw-bold" id="qrModalLabel"><i class="fa-solid fa-qrcode me-2"></i>Medicine Verification QR Code</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        <p class="text-muted small mb-3">Scan this QR Code with any mobile camera to verify this batch on the public verification portal.</p>
        
        <div id="qrcode_canvas" class="d-flex justify-content-center p-3 bg-white rounded border mb-3"></div>
        
        <small class="text-muted d-block font-monospace text-break bg-light p-2 rounded border">
            <?php echo e($public_verify_url); ?>
        </small>
      </div>
      <div class="modal-footer justify-content-between">
        <span class="badge bg-success font-monospace">Public Verification Link</span>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const qrContainer = document.getElementById("qrcode_canvas");
    if (qrContainer && typeof QRCode !== "undefined") {
        new QRCode(qrContainer, {
            text: <?php echo json_encode($public_verify_url); ?>,
            width: 180,
            height: 180,
            colorDark: "#1b4332",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

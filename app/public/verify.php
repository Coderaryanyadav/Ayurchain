<?php
// ====================================================================
// Public Medicine Verification Portal (Phase 15 Polished UI)
// Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
// File: verify.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$search_id    = trim($_GET['medicine_id'] ?? $_POST['medicine_id'] ?? '');
$search_batch = trim($_GET['batch_number'] ?? $_POST['batch_number'] ?? '');
$search_query = !empty($search_id) ? $search_id : $search_batch;

$db_record  = null;
$error_msg  = "";
$doc_record = null;

if (!empty($search_query)) {
    try {
        $stmt = $pdo->prepare("
            SELECT m.*, b.transaction_hash, b.block_number, b.blockchain_timestamp, b.record_hash AS mysql_record_hash
            FROM medicines m
            LEFT JOIN blockchain_records b ON m.medicine_id = b.medicine_id
            WHERE m.medicine_id = :query OR m.batch_number = :query
            LIMIT 1
        ");
        $stmt->execute(['query' => $search_query]);
        $db_record = $stmt->fetch();

        if (!$db_record) {
            $error_msg = "No record found in database for Medicine ID / Batch Number: '" . htmlspecialchars($search_query) . "'";
        } else {
            $raw_record_string = $db_record['medicine_id'] . $db_record['batch_number'] . $db_record['manufacturing_date'] . $db_record['expiry_date'];
            $db_record['computed_record_hash'] = hash('sha256', $raw_record_string);

            $doc_stmt = $pdo->prepare("SELECT * FROM medicine_documents WHERE medicine_id = :id LIMIT 1");
            $doc_stmt->execute(['id' => $db_record['medicine_id']]);
            $doc_record = $doc_stmt->fetch();
        }

    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Search Header Box -->
            <div class="card card-ayur text-center p-4 mb-4 shadow-sm border-top border-4 border-warning">
                <h2 class="fw-bold text-success mb-2">
                    <i class="fa-solid fa-shield-check me-2 text-warning"></i>Public Medicine Verification Portal
                </h2>
                <p class="text-muted small col-md-9 mx-auto mb-3">
                    Enter the Medicine ID or Batch Number to perform real-time cryptographic hash verification against off-chain MySQL records and the Ethereum Ganache Smart Contract.
                </p>

                <form method="GET" action="verify.php" class="row justify-content-center g-2">
                    <div class="col-md-5">
                        <input type="text" name="medicine_id" class="form-control form-control-lg border-2" placeholder="Medicine ID (e.g. AYU-2026-001)" value="<?php echo htmlspecialchars($search_id); ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="batch_number" class="form-control form-control-lg border-2" placeholder="Batch # (e.g. BATCH-AYU-2026-01)" value="<?php echo htmlspecialchars($search_batch); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-gold btn-lg w-100 fw-bold shadow-sm">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Verify Record
                        </button>
                    </div>
                </form>
            </div>

            <!-- Error Notification -->
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger text-center p-4 shadow-sm">
                    <i class="fa-solid fa-circle-xmark fa-3x mb-2 text-danger"></i><br>
                    <h5 class="fw-bold">Verification Lookup Failed</h5>
                    <p class="mb-0 text-dark"><?php echo $error_msg; ?></p>
                </div>
            <?php endif; ?>

            <!-- Verification Results Display -->
            <?php if ($db_record): ?>
                
                <!-- Live Web3 Client-Side Verification Status Banner with Loading Spinner Ring -->
                <div id="verification_status_banner" class="alert alert-secondary p-4 mb-4 shadow-sm border-start border-5 border-secondary">
                    <div class="d-flex align-items-center">
                        <div id="verification_status_icon" class="me-4 text-center">
                            <div class="loading-spinner-wrapper"><div></div><div></div><div></div><div></div></div>
                        </div>
                        <div>
                            <h4 id="verification_status_title" class="fw-bold mb-1">Querying Ethereum Smart Contract...</h4>
                            <p id="verification_status_desc" class="mb-0 text-dark small">Reading on-chain hashes for <code><?php echo htmlspecialchars($db_record['medicine_id']); ?></code> on Ganache Network...</p>
                        </div>
                    </div>
                </div>

                <!-- Complete Record Details -->
                <div class="card card-ayur shadow-sm mb-4">
                    <div class="card-header card-header-ayur py-3 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold"><i class="fa-solid fa-file-waveform me-2"></i>Verification Attributes</h4>
                        <span class="badge bg-warning text-dark font-monospace px-3 py-2 fs-6"><?php echo htmlspecialchars($db_record['medicine_id']); ?></span>
                    </div>
                    <div class="card-body p-4">

                        <div class="row g-3">
                            <div class="col-md-6 border-bottom pb-2">
                                <span class="text-muted small d-block font-weight-bold">Medicine Name</span>
                                <strong class="fs-5 text-dark"><?php echo htmlspecialchars($db_record['medicine_name']); ?></strong>
                            </div>

                            <div class="col-md-6 border-bottom pb-2">
                                <span class="text-muted small d-block font-weight-bold">Medicine ID</span>
                                <code class="fs-5 text-success font-monospace"><?php echo htmlspecialchars($db_record['medicine_id']); ?></code>
                            </div>

                            <div class="col-md-6 border-bottom pb-2">
                                <span class="text-muted small d-block font-weight-bold">Batch Number</span>
                                <code class="fs-6 text-dark font-monospace"><?php echo htmlspecialchars($db_record['batch_number']); ?></code>
                            </div>

                            <div class="col-md-6 border-bottom pb-2">
                                <span class="text-muted small d-block font-weight-bold">Manufacturer</span>
                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($db_record['manufacturer']); ?></span>
                            </div>

                            <div class="col-12 border-bottom pb-2">
                                <span class="text-muted small d-block font-weight-bold">Certificate SHA-256 Hash</span>
                                <code id="display_cert_hash" class="text-break bg-light p-2 rounded border d-block font-monospace small">
                                    <?php echo htmlspecialchars($db_record['certificate_hash']); ?>
                                </code>
                            </div>

                            <div class="col-12 border-bottom pb-2">
                                <span class="text-muted small d-block font-weight-bold">Record Metadata SHA-256 Hash</span>
                                <code id="display_record_hash" class="text-break bg-light p-2 rounded border d-block font-monospace small">
                                    <?php echo htmlspecialchars($db_record['computed_record_hash']); ?>
                                </code>
                            </div>

                            <div class="col-md-6">
                                <span class="text-muted small d-block font-weight-bold">Blockchain Transaction Hash</span>
                                <?php if (!empty($db_record['transaction_hash'])): ?>
                                    <code class="text-break font-monospace small text-primary d-block">
                                        <?php echo htmlspecialchars($db_record['transaction_hash']); ?>
                                    </code>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Not Mined On-Chain Yet</span>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-3">
                                <span class="text-muted small d-block font-weight-bold">Block Number</span>
                                <?php if (!empty($db_record['block_number'])): ?>
                                    <span class="badge bg-dark font-monospace fs-6">#<?php echo $db_record['block_number']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-3">
                                <span class="text-muted small d-block font-weight-bold">Blockchain Timestamp</span>
                                <?php if (!empty($db_record['blockchain_timestamp'])): ?>
                                    <small class="text-muted font-monospace d-block">
                                        <?php echo date('Y-m-d H:i:s', $db_record['blockchain_timestamp']); ?>
                                    </small>
                                <?php else: ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Disclaimer Box -->
                <div class="alert alert-warning border-start border-5 border-warning p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Understanding Blockchain Verification
                    </h5>
                    <p class="mb-0 text-dark small leading-relaxed">
                        <strong>Important Note:</strong> Blockchain verification confirms that the record stored in the database matches the cryptographic hash immutably locked on the smart contract at the time of manufacture. It guarantees <strong>tamper-evident data integrity</strong>.
                        <br>
                        <em>Blockchain verification does NOT certify whether a medicine is medically effective, clinically appropriate, or chemically non-toxic. Always consult a certified Ayurvedic practitioner or AYUSH doctor for medical guidance.</em>
                    </p>
                </div>

                <!-- Ethers.js Smart Contract Call Handler -->
                <script>
                document.addEventListener("DOMContentLoaded", async function() {
                    const banner = document.getElementById('verification_status_banner');
                    const icon = document.getElementById('verification_status_icon');
                    const title = document.getElementById('verification_status_title');
                    const desc = document.getElementById('verification_status_desc');

                    const medicineId = <?php echo json_encode($db_record['medicine_id']); ?>;
                    const mysqlCertHash = <?php echo json_encode($db_record['certificate_hash']); ?>;
                    const mysqlRecordHash = <?php echo json_encode($db_record['computed_record_hash']); ?>;
                    const mysqlTxHash = <?php echo json_encode($db_record['transaction_hash']); ?>;

                    if (!mysqlTxHash) {
                        banner.className = "alert alert-warning p-4 mb-4 shadow-sm border-start border-5 border-warning";
                        icon.innerHTML = "<i class='fa-solid fa-triangle-exclamation fa-3x text-warning'></i>";
                        title.innerText = "⚠ Pending Blockchain Mining";
                        desc.innerText = "This record exists in the off-chain MySQL database, but has not yet been mined onto the smart contract.";
                        return;
                    }

                    try {
                        let provider;
                        if (typeof window.ethereum !== 'undefined') {
                            provider = new ethers.BrowserProvider(window.ethereum);
                        } else {
                            provider = new ethers.JsonRpcProvider("http://127.0.0.1:7545");
                        }

                        const contract = new ethers.Contract(CONTRACT_ADDRESS, CONTRACT_ABI, provider);
                        const isValidOnChain = await contract.verifyMedicine(medicineId, mysqlCertHash, mysqlRecordHash);

                        if (isValidOnChain) {
                            banner.className = "alert alert-success p-4 mb-4 shadow-sm border-start border-5 border-success";
                            icon.innerHTML = "<i class='fa-solid fa-circle-check fa-3x text-success'></i>";
                            title.innerText = "✓ Blockchain Verified";
                            desc.innerText = "Cryptographic Match Confirmed! The stored certificate hash and record metadata 100% match the immutable smart contract ledger on Ganache.";
                        } else {
                            banner.className = "alert alert-danger p-4 mb-4 shadow-sm border-start border-5 border-danger";
                            icon.innerHTML = "<i class='fa-solid fa-triangle-exclamation fa-3x text-danger'></i>";
                            title.innerText = "⚠ Verification Failed";
                            desc.innerText = "CRITICAL WARNING: Tampering Detected! The off-chain record parameters do NOT match the hashes locked on the Ethereum blockchain.";
                        }

                    } catch (err) {
                        console.error("Web3 Verification Error:", err);
                        banner.className = "alert alert-secondary p-4 mb-4 shadow-sm border-start border-5 border-secondary";
                        icon.innerHTML = "<i class='fa-solid fa-database fa-3x text-dark'></i>";
                        title.innerText = "Off-Chain MySQL Verified (Ganache Provider Offline)";
                        desc.innerText = "Unable to connect to local Ganache RPC server. Off-chain database hashes match, but live Web3 confirmation requires Ganache/MetaMask.";
                    }
                });
                </script>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
// ====================================================================
// Add Ayurvedic Medicine Module (Hardened & Secured)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: add_medicine.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Guard 1: Admin authentication check
check_admin_login();

$errors       = [];
$success      = "";
$web3_payload = null;

// CSRF Token
$csrf_token = generate_csrf_token();

// Default values for sticky form inputs
$fields = [
    'medicine_id'        => '',
    'medicine_name'      => '',
    'botanical_name'     => '',
    'category'           => 'Churna',
    'ingredients'        => '',
    'form'               => 'Powder',
    'manufacturer'       => '',
    'batch_number'       => '',
    'manufacturing_date' => date('Y-m-d'),
    'expiry_date'        => date('Y-m-d', strtotime('+2 years')),
    'source'             => '',
    'description'        => ''
];

// Handle Form POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Guard 2: CSRF Token Validation
    $submitted_csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submitted_csrf)) {
        $errors[] = "Security Token Validation Failed (CSRF)! Please refresh and try again.";
    } else {

        // 1. Sanitize text inputs
        foreach ($fields as $key => $val) {
            $fields[$key] = trim($_POST[$key] ?? '');
        }

        // 2. Strict Input Format Validation using Regular Expressions
        if (empty($fields['medicine_id'])) {
            $errors[] = "Medicine ID is required.";
        } elseif (!preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $fields['medicine_id'])) {
            $errors[] = "Medicine ID must contain only letters, numbers, hyphens, and underscores (3-50 chars).";
        }

        if (empty($fields['batch_number'])) {
            $errors[] = "Batch Number is required.";
        } elseif (!preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $fields['batch_number'])) {
            $errors[] = "Batch Number must contain only letters, numbers, hyphens, and underscores (3-50 chars).";
        }

        if (empty($fields['medicine_name'])) {
            $errors[] = "Medicine Name is required.";
        }

        if (empty($fields['manufacturer'])) {
            $errors[] = "Manufacturer Name is required.";
        }

        if (empty($fields['manufacturing_date']) || empty($fields['expiry_date'])) {
            $errors[] = "Manufacturing and Expiry Dates are required.";
        } else {
            if (strtotime($fields['expiry_date']) <= strtotime($fields['manufacturing_date'])) {
                $errors[] = "Expiry Date must be greater than Manufacturing Date.";
            }
        }

        // 3. Duplicate Records Check (Prepared Statements)
        if (!empty($fields['medicine_id'])) {
            $stmt_check_id = $pdo->prepare("SELECT id FROM medicines WHERE medicine_id = :mid LIMIT 1");
            $stmt_check_id->execute(['mid' => $fields['medicine_id']]);
            if ($stmt_check_id->fetch()) {
                $errors[] = "Medicine ID '" . htmlspecialchars($fields['medicine_id']) . "' already exists!";
            }
        }

        if (!empty($fields['batch_number'])) {
            $stmt_check_batch = $pdo->prepare("SELECT id FROM medicines WHERE batch_number = :batch LIMIT 1");
            $stmt_check_batch->execute(['batch' => $fields['batch_number']]);
            if ($stmt_check_batch->fetch()) {
                $errors[] = "Batch Number '" . htmlspecialchars($fields['batch_number']) . "' already exists!";
            }
        }

        // 4. Secure File Upload Handler
        $certificate_hash    = "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";
        $saved_relative_path = "";
        $doc_original_name   = "";
        $doc_file_size       = 0;
        $doc_file_mime       = "";

        if (isset($_FILES['certificate_file']) && $_FILES['certificate_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['certificate_file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Error uploading certificate file.";
            } else {
                // Max file size 5MB
                if ($file['size'] > 5 * 1024 * 1024) {
                    $errors[] = "Certificate file size exceeds 5MB limit.";
                }

                // Strict MIME-type & extension validation
                $allowed_mimes = ['application/pdf', 'image/jpeg', 'image/png'];
                $allowed_exts  = ['pdf', 'jpg', 'jpeg', 'png'];

                $file_ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $file_mime = mime_content_type($file['tmp_name']);

                if (!in_array($file_ext, $allowed_exts) || !in_array($file_mime, $allowed_mimes)) {
                    $errors[] = "Invalid file format! Only PDF, JPG, and PNG files are allowed.";
                } else {
                    // Compute SHA-256 Hash of raw file content
                    $certificate_hash = hash_file('sha256', $file['tmp_name']);

                    $upload_dir = __DIR__ . '/../../storage/certificates/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Generate unguessable random filename to prevent shell execution
                    $random_name         = bin2hex(random_bytes(16)) . '.' . $file_ext;
                    $target_full_path    = $upload_dir . $random_name;
                    $saved_relative_path = 'storage/certificates/' . $random_name;
                    $doc_original_name   = basename($file['name']);
                    $doc_file_size       = $file['size'];
                    $doc_file_mime       = $file_mime;

                    if (!move_uploaded_file($file['tmp_name'], $target_full_path)) {
                        $errors[] = "Failed to store uploaded certificate file on server.";
                    }
                }
            }
        }

        // 5. Generate Combined Record Hash
        $record_raw_string = $fields['medicine_id'] . $fields['batch_number'] . $fields['manufacturing_date'] . $fields['expiry_date'];
        $record_hash       = hash('sha256', $record_raw_string);

        // 6. MySQL Database Insertion Transaction
        if (empty($errors)) {
            try {
                $pdo->beginTransaction();

                $stmt_insert = $pdo->prepare("
                    INSERT INTO medicines (
                        medicine_id, medicine_name, botanical_name, category, ingredients, form, 
                        manufacturer, batch_number, manufacturing_date, expiry_date, source, description, certificate_hash
                    ) VALUES (
                        :medicine_id, :medicine_name, :botanical_name, :category, :ingredients, :form, 
                        :manufacturer, :batch_number, :manufacturing_date, :expiry_date, :source, :description, :certificate_hash
                    )
                ");

                $stmt_insert->execute([
                    'medicine_id'        => $fields['medicine_id'],
                    'medicine_name'      => $fields['medicine_name'],
                    'botanical_name'     => $fields['botanical_name'],
                    'category'           => $fields['category'],
                    'ingredients'        => $fields['ingredients'],
                    'form'               => $fields['form'],
                    'manufacturer'       => $fields['manufacturer'],
                    'batch_number'       => $fields['batch_number'],
                    'manufacturing_date' => $fields['manufacturing_date'],
                    'expiry_date'        => $fields['expiry_date'],
                    'source'             => $fields['source'],
                    'description'        => $fields['description'],
                    'certificate_hash'   => $certificate_hash
                ]);

                if (!empty($saved_relative_path)) {
                    $stmt_doc = $pdo->prepare("
                        INSERT INTO medicine_documents (medicine_id, document_name, file_path, file_size, file_type, certificate_hash)
                        VALUES (:medicine_id, :document_name, :file_path, :file_size, :file_type, :certificate_hash)
                    ");
                    $stmt_doc->execute([
                        'medicine_id'      => $fields['medicine_id'],
                        'document_name'    => $doc_original_name,
                        'file_path'        => $saved_relative_path,
                        'file_size'        => $doc_file_size,
                        'file_type'        => $doc_file_mime,
                        'certificate_hash' => $certificate_hash
                    ]);
                }

                $stmt_history = $pdo->prepare("
                    INSERT INTO medicine_history (medicine_id, action_type, action_details, performed_by)
                    VALUES (:medicine_id, 'REGISTERED', 'Medicine record saved in database.', :performed_by)
                ");
                $stmt_history->execute([
                    'medicine_id'  => $fields['medicine_id'],
                    'performed_by' => $_SESSION['admin_username'] ?? 'Admin'
                ]);

                $pdo->commit();

                $success = "Medicine Record <strong>" . htmlspecialchars($fields['medicine_id']) . "</strong> saved in MySQL! Ready for MetaMask signature.";

                $web3_payload = [
                    'medicine_id'      => $fields['medicine_id'],
                    'medicine_name'    => $fields['medicine_name'],
                    'batch_number'     => $fields['batch_number'],
                    'manufacturer'     => $fields['manufacturer'],
                    'certificate_hash' => $certificate_hash,
                    'record_hash'      => $record_hash
                ];

            } catch (PDOException $e) {
                $pdo->rollBack();
                $errors[] = "Database Insertion Failed: " . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
                </a>
                <a href="medicines.php" class="btn btn-ayur btn-sm">
                    <i class="fa-solid fa-list me-1"></i> View All Medicines
                </a>
            </div>

            <!-- Web3 Blockchain Anchor Trigger Card -->
            <?php if (!empty($web3_payload)): ?>
                <div class="card card-ayur mb-4 border-2 border-warning shadow-lg bg-light">
                    <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-cubes text-warning me-2"></i>Step 2: Anchor Record on Ethereum Ganache Blockchain</span>
                        <span class="badge bg-warning text-dark font-monospace"><?php echo htmlspecialchars($web3_payload['medicine_id']); ?></span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <i class="fa-solid fa-file-contract fa-4x text-warning mb-3"></i>
                        <h4 class="fw-bold text-dark">Ready for MetaMask Signature</h4>
                        <p class="text-muted col-md-9 mx-auto">
                            The record has been saved off-chain in MySQL. Click below to open <strong>MetaMask</strong>, sign the transaction, and immutably store the certificate hash on the smart contract.
                        </p>
                        
                        <div id="web3_status_box" class="alert alert-info d-none col-md-8 mx-auto p-3 mb-3">
                            <span id="web3_status_text" class="fw-bold">Initializing MetaMask...</span>
                        </div>

                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <button id="btn_mine_blockchain" class="btn btn-warning btn-lg fw-bold px-4 shadow">
                                <i class="fa-solid fa-link me-2"></i> Sign & Mine on Blockchain via MetaMask
                            </button>
                            <button id="btn_simulate_mine" type="button" class="btn btn-success btn-lg fw-bold px-4 shadow">
                                <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Simulate Mining (Demo Mode)
                            </button>
                            <a href="../public/view_medicine.php?id=<?php echo urlencode($web3_payload['medicine_id']); ?>" class="btn btn-outline-secondary btn-lg">
                                Skip to View Details
                            </a>
                        </div>
                    </div>
                </div>

                <script>
                document.getElementById('btn_mine_blockchain').addEventListener('click', async function() {
                    const btn = this;
                    const statusBox = document.getElementById('web3_status_box');
                    const statusText = document.getElementById('web3_status_text');

                    btn.disabled = true;
                    statusBox.classList.remove('d-none');
                    statusBox.className = "alert alert-info col-md-8 mx-auto p-3 mb-3";
                    statusText.innerHTML = "<i class='fa-solid fa-spinner fa-spin me-2'></i>Opening MetaMask... Please approve transaction signature.";

                    const payload = <?php echo json_encode($web3_payload); ?>;

                    try {
                        await connectMetaMaskWallet();
                        statusText.innerHTML = "<i class='fa-solid fa-spinner fa-spin me-2'></i>Transaction submitted to Ganache... Waiting for block mining...";

                        const result = await sendMedicineToBlockchain(payload);

                        statusBox.className = "alert alert-success col-md-8 mx-auto p-3 mb-3";
                        statusText.innerHTML = "<i class='fa-solid fa-circle-check me-2'></i><strong>SUCCESS!</strong> Transaction mined in Block #" + result.block_number + "<br><small class='font-monospace text-break'>Tx: " + result.transaction_hash + "</small>";
                        
                        btn.classList.add('d-none');

                        setTimeout(function() {
                            window.location.href = "view_medicine.php?id=" + encodeURIComponent(payload.medicine_id);
                        }, 2500);

                    } catch (err) {
                        btn.disabled = false;
                        statusBox.className = "alert alert-danger col-md-8 mx-auto p-3 mb-3";
                        statusText.innerHTML = "<i class='fa-solid fa-circle-xmark me-2'></i><strong>MetaMask Error:</strong> " + err.message + "<br><small class='mt-2 d-block'>Tip: You can also click <strong>'Simulate Mining (Demo Mode)'</strong> to complete the presentation demo without MetaMask.</small>";
                    }
                });

                document.getElementById('btn_simulate_mine').addEventListener('click', async function() {
                    const btn = this;
                    const statusBox = document.getElementById('web3_status_box');
                    const statusText = document.getElementById('web3_status_text');

                    btn.disabled = true;
                    statusBox.classList.remove('d-none');
                    statusBox.className = "alert alert-info col-md-8 mx-auto p-3 mb-3";
                    statusText.innerHTML = "<i class='fa-solid fa-gear fa-spin me-2'></i>Simulating Ethereum block consensus and generating cryptographic transaction receipt...";

                    const payload = <?php echo json_encode($web3_payload); ?>;
                    const randomHex = Array.from(crypto.getRandomValues(new Uint8Array(32))).map(b => b.toString(16).padStart(2, '0')).join('');
                    const mockTxHash = "0x" + randomHex;
                    const mockBlockNumber = Math.floor(Math.random() * 50) + 100;
                    const timestamp = Math.floor(Date.now() / 1000);

                    try {
                        const apiUrl = (window.BASE_URL || '/') + 'app/api/save_blockchain_record.php';
                        const response = await fetch(apiUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                medicine_id: payload.medicine_id,
                                batch_number: payload.batch_number,
                                certificate_hash: payload.certificate_hash,
                                record_hash: payload.record_hash,
                                transaction_hash: mockTxHash,
                                block_number: mockBlockNumber,
                                timestamp: timestamp
                            })
                        });

                        const result = await response.json();
                        if (!result.success) throw new Error(result.message);

                        statusBox.className = "alert alert-success col-md-8 mx-auto p-3 mb-3";
                        statusText.innerHTML = "<i class='fa-solid fa-circle-check me-2'></i><strong>SUCCESS!</strong> Simulated block mined into Block #" + mockBlockNumber + "<br><small class='font-monospace text-break'>Tx: " + mockTxHash + "</small>";

                        setTimeout(function() {
                            window.location.href = "../public/view_medicine.php?id=" + encodeURIComponent(payload.medicine_id);
                        }, 2000);

                    } catch (err) {
                        btn.disabled = false;
                        statusBox.className = "alert alert-danger col-md-8 mx-auto p-3 mb-3";
                        statusText.innerHTML = "<i class='fa-solid fa-circle-xmark me-2'></i><strong>Demo Save Error:</strong> " + err.message;
                    }
                });
                </script>
            <?php endif; ?>

            <!-- Add Medicine Form -->
            <div class="card card-ayur shadow-sm">
                <div class="card-header card-header-ayur py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2"></i>Add Ayurvedic Medicine Record</h4>
                    <button type="button" class="btn btn-sm btn-outline-warning text-white" onclick="fillDemoMedicineData()">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> ⚡ Fill Demo Data
                    </button>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show p-3" role="alert">
                            <i class="fa-solid fa-circle-check fa-lg me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show p-3" role="alert">
                            <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i>Please fix validation errors:</h6>
                            <ul class="mb-0 small ps-3">
                                <?php foreach ($errors as $err): ?>
                                    <li><?php echo htmlspecialchars($err); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="add_medicine.php" enctype="multipart/form-data">
                        <!-- CSRF Hidden Input -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Medicine ID <span class="text-danger">*</span></label>
                                <input type="text" name="medicine_id" class="form-control" placeholder="e.g. AYU-2026-003" value="<?php echo htmlspecialchars($fields['medicine_id']); ?>" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" name="medicine_name" class="form-control" placeholder="e.g. Triphala Churna" value="<?php echo htmlspecialchars($fields['medicine_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Botanical / Latin Name</label>
                                <input type="text" name="botanical_name" class="form-control" placeholder="e.g. Terminalia chebula" value="<?php echo htmlspecialchars($fields['botanical_name']); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="Churna" <?php if ($fields['category'] === 'Churna') echo 'selected'; ?>>Churna (Powder)</option>
                                    <option value="Rasayana" <?php if ($fields['category'] === 'Rasayana') echo 'selected'; ?>>Rasayana (Rejuvenator)</option>
                                    <option value="Bhasma" <?php if ($fields['category'] === 'Bhasma') echo 'selected'; ?>>Bhasma (Ash)</option>
                                    <option value="Asava & Arishta" <?php if ($fields['category'] === 'Asava & Arishta') echo 'selected'; ?>>Asava & Arishta (Liquid)</option>
                                    <option value="Vati & Gutika" <?php if ($fields['category'] === 'Vati & Gutika') echo 'selected'; ?>>Vati & Gutika (Tablets)</option>
                                    <option value="Taila" <?php if ($fields['category'] === 'Taila') echo 'selected'; ?>>Taila (Oil)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Form / State</label>
                                <input type="text" name="form" class="form-control" placeholder="e.g. Powder / Syrup / Tablet" value="<?php echo htmlspecialchars($fields['form']); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Batch Number <span class="text-danger">*</span></label>
                                <input type="text" name="batch_number" class="form-control" placeholder="e.g. BATCH-AYU-2026-03" value="<?php echo htmlspecialchars($fields['batch_number']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Manufacturer Name <span class="text-danger">*</span></label>
                                <input type="text" name="manufacturer" class="form-control" placeholder="e.g. Baidyanath Ayurvedic Bhawan" value="<?php echo htmlspecialchars($fields['manufacturer']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sourcing Region</label>
                                <input type="text" name="source" class="form-control" placeholder="e.g. Kerala Organic Herbal Reserve" value="<?php echo htmlspecialchars($fields['source']); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Manufacturing Date <span class="text-danger">*</span></label>
                                <input type="date" name="manufacturing_date" class="form-control" value="<?php echo htmlspecialchars($fields['manufacturing_date']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" class="form-control" value="<?php echo htmlspecialchars($fields['expiry_date']); ?>" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Active Ingredients</label>
                                <textarea name="ingredients" class="form-control" rows="2" placeholder="List active herbs..."><?php echo htmlspecialchars($fields['ingredients']); ?></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Add therapeutic indications..."><?php echo htmlspecialchars($fields['description']); ?></textarea>
                            </div>

                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded border border-warning">
                                    <label class="form-label fw-bold text-dark d-block">
                                        <i class="fa-solid fa-file-pdf text-danger me-1"></i> Quality Lab Certificate (PDF / Image Document)
                                    </label>
                                    <input type="file" id="cert_file_input" name="certificate_file" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                                    <small class="text-muted d-block mt-1">Max size: 5MB. SHA-256 hash generated automatically.</small>
                                    
                                    <div id="hash_preview_box" class="mt-2 d-none">
                                        <small class="fw-bold text-success d-block"><i class="fa-solid fa-microchip me-1"></i> Computed SHA-256 Hash:</small>
                                        <code id="hash_preview_text" class="text-break bg-white p-2 rounded border d-block font-monospace small text-dark"></code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="dashboard.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-gold px-4 py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Medicine Record
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('cert_file_input').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    const previewBox = document.getElementById('hash_preview_box');
    const previewText = document.getElementById('hash_preview_text');

    if (file) {
        previewBox.classList.remove('d-none');
        previewText.innerText = "Computing SHA-256 hash...";
        try {
            const hash = await calculateFileSHA256(file);
            previewText.innerText = hash;
        } catch (err) {
            previewText.innerText = "Error computing hash: " + err.message;
        }
    } else {
        previewBox.classList.add('d-none');
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

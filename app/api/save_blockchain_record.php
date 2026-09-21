<?php
// ====================================================================
// Save Mined Blockchain Record API
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: api/save_blockchain_record.php
// ====================================================================

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Only logged in admins can save blockchain records
if (!is_admin_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Parse incoming JSON payload from Ethers.js
$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

$medicine_id        = trim($data['medicine_id'] ?? '');
$batch_number       = trim($data['batch_number'] ?? '');
$certificate_hash   = trim($data['certificate_hash'] ?? '');
$record_hash        = trim($data['record_hash'] ?? '');
$transaction_hash   = trim($data['transaction_hash'] ?? '');
$block_number       = isset($data['block_number']) ? (int)$data['block_number'] : null;
$blockchain_timestamp = isset($data['timestamp']) ? (int)$data['timestamp'] : time();

if (empty($medicine_id) || empty($batch_number) || empty($transaction_hash)) {
    echo json_encode(['success' => false, 'message' => 'Missing required blockchain parameters.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Insert into blockchain_records table
    $stmt = $pdo->prepare("
        INSERT INTO blockchain_records (
            medicine_id, batch_number, record_hash, certificate_hash, 
            transaction_hash, block_number, blockchain_timestamp
        ) VALUES (
            :medicine_id, :batch_number, :record_hash, :certificate_hash, 
            :transaction_hash, :block_number, :blockchain_timestamp
        )
        ON DUPLICATE KEY UPDATE 
            transaction_hash = VALUES(transaction_hash),
            block_number = VALUES(block_number),
            blockchain_timestamp = VALUES(blockchain_timestamp)
    ");

    $stmt->execute([
        'medicine_id'          => $medicine_id,
        'batch_number'         => $batch_number,
        'record_hash'          => $record_hash,
        'certificate_hash'     => $certificate_hash,
        'transaction_hash'     => $transaction_hash,
        'block_number'         => $block_number,
        'blockchain_timestamp' => $blockchain_timestamp
    ]);

    // 2. Add audit log to medicine_history
    $hist_stmt = $pdo->prepare("
        INSERT INTO medicine_history (medicine_id, action_type, action_details, performed_by)
        VALUES (:medicine_id, 'BLOCKCHAIN_MINED', :details, :performed_by)
    ");
    $hist_stmt->execute([
        'medicine_id'  => $medicine_id,
        'details'      => "Transaction mined on block #" . $block_number . " (Tx: " . substr($transaction_hash, 0, 10) . "...)",
        'performed_by' => $_SESSION['admin_username'] ?? 'Admin'
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Blockchain record successfully stored in MySQL database!',
        'transaction_hash' => $transaction_hash
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>

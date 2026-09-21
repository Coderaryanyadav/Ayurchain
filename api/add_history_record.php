<?php
// ====================================================================
// Add Lifecycle History API Endpoint
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: api/add_history_record.php
// ====================================================================

header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/auth.php';

// Only authorized admins can post lifecycle status updates
if (!is_admin_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit();
}

$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

$medicine_id      = trim($data['medicine_id'] ?? '');
$batch_number     = trim($data['batch_number'] ?? '');
$status           = trim($data['status'] ?? '');
$location         = trim($data['location'] ?? '');
$transaction_hash = trim($data['transaction_hash'] ?? '');
$block_number     = isset($data['block_number']) ? (int)$data['block_number'] : null;

if (empty($medicine_id) || empty($status) || empty($transaction_hash)) {
    echo json_encode(['success' => false, 'message' => 'Missing required history parameters (Medicine ID, Status, Transaction Hash).']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO medicine_history (
            medicine_id, batch_number, status, location, action_details, transaction_hash, block_number, performed_by
        ) VALUES (
            :medicine_id, :batch_number, :status, :location, :action_details, :transaction_hash, :block_number, :performed_by
        )
    ");

    $action_details = "Lifecycle Status updated to '" . $status . "' at location: " . (!empty($location) ? $location : 'N/A');

    $stmt->execute([
        'medicine_id'      => $medicine_id,
        'batch_number'     => $batch_number,
        'status'           => $status,
        'location'         => $location,
        'action_details'   => $action_details,
        'transaction_hash' => $transaction_hash,
        'block_number'     => $block_number,
        'performed_by'     => $_SESSION['admin_username'] ?? 'Admin'
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Lifecycle status update mined on-chain and recorded in database!',
        'transaction_hash' => $transaction_hash
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>

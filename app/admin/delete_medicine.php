<?php
// ====================================================================
// Delete Ayurvedic Medicine Handler (Protected Admin Script)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: delete_medicine.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Guard: Admin protection
check_admin_login();

$medicine_id = trim($_GET['id'] ?? '');

if (!empty($medicine_id)) {
    try {
        // 1. Find attached certificate files to clean up from disk
        $stmt_docs = $pdo->prepare("SELECT file_path FROM medicine_documents WHERE medicine_id = :mid");
        $stmt_docs->execute(['mid' => $medicine_id]);
        $files = $stmt_docs->fetchAll();

        foreach ($files as $f) {
            $full_path = __DIR__ . '/../../' . $f['file_path'];
            if (file_exists($full_path)) {
                @unlink($full_path); // Delete file from server storage
            }
        }

        // 2. Delete medicine record from MySQL database
        // Foreign keys with ON DELETE CASCADE automatically clean child rows in medicine_documents, blockchain_records, & medicine_history
        $stmt_delete = $pdo->prepare("DELETE FROM medicines WHERE medicine_id = :mid");
        $stmt_delete->execute(['mid' => $medicine_id]);

        header("Location: medicines.php?msg=deleted");
        exit();

    } catch (PDOException $e) {
        die("<div class='container my-4'><div class='alert alert-danger'>Delete Error: " . htmlspecialchars($e->getMessage()) . "</div></div>");
    }
} else {
    header("Location: medicines.php");
    exit();
}
?>

<?php
// ====================================================================
// Admin Profile & Security Preferences (Phase 13)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: profile.php
// ====================================================================

require_once 'config/database.php';
require_once 'includes/auth.php';

// Protect page
check_admin_login();

$admin_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['admin_id']]);
    $admin_info = $stmt->fetch();
} catch (PDOException $e) {
    // Database error
}

require_once 'includes/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-gear text-success me-2"></i>Admin Profile Settings</h2>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
                </a>
            </div>

            <div class="card card-ayur shadow-sm mb-4">
                <div class="card-header card-header-ayur py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-id-card me-2"></i>Account Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fa-solid fa-user-shield text-warning"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($admin_info['full_name'] ?? 'Admin'); ?></h4>
                            <p class="text-muted mb-1">Username: <code><?php echo htmlspecialchars($admin_info['username'] ?? 'admin'); ?></code></p>
                            <p class="text-muted mb-0 small">Email: <strong><?php echo htmlspecialchars($admin_info['email'] ?? 'admin@ayurvedicblockchain.org'); ?></strong></p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1">Security & Password</h6>
                            <small class="text-muted">Update your administrative password regularly to maintain ledger integrity.</small>
                        </div>
                        <a href="change_password.php" class="btn btn-gold fw-bold shadow-sm">
                            <i class="fa-solid fa-key me-1"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
// ====================================================================
// Change Admin Password Page (Protected Admin Page)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: change_password.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Protect page
check_admin_login();

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required!";
        $message_type = "danger";
    } elseif ($new_password !== $confirm_password) {
        $message = "New password and Confirm password do not match!";
        $message_type = "danger";
    } elseif (strlen($new_password) < 6) {
        $message = "New password must be at least 6 characters long.";
        $message_type = "danger";
    } else {
        try {
            // Fetch current password hash from database
            $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $_SESSION['admin_id']]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($current_password, $admin['password'])) {
                // Hash new password using password_hash()
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                $update_stmt = $pdo->prepare("UPDATE admins SET password = :password WHERE id = :id");
                $update_stmt->execute([
                    'password' => $new_hashed_password,
                    'id'       => $_SESSION['admin_id']
                ]);

                $message = "Password updated successfully! Next time you log in, use your new password.";
                $message_type = "success";
            } else {
                $message = "Incorrect current password!";
                $message_type = "danger";
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-ayur shadow-sm">
                <div class="card-header card-header-ayur py-3">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-key me-2"></i>Change Admin Password</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="change_password.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-gold fw-bold shadow-sm">
                                <i class="fa-solid fa-shield-check me-1"></i> Update Password
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

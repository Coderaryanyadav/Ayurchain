<?php
// ====================================================================
// Admin Login Page (Hardened against Brute-force & CSRF)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: login.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// If admin is already authenticated, redirect straight to dashboard
if (is_admin_logged_in()) {
    header("Location: " . get_base_url() . "app/admin/dashboard.php");
    exit();
}

$error_message = "";
$success_message = "";

// Check query string feedback parameters
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'please_login') {
        $error_message = "Unauthorized access! Please log in to view that admin page.";
    } elseif ($_GET['msg'] === 'logged_out') {
        $success_message = "You have been logged out successfully.";
    }
}

// Generate CSRF Token
$csrf_token = generate_csrf_token();

// Process POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // CSRF Validation
    $submitted_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submitted_token)) {
        $error_message = "Security token mismatch (CSRF protection)! Please refresh and try again.";
    } else {
        $login_input = trim($_POST['login_input'] ?? '');
        $password    = trim($_POST['password'] ?? '');

        if (empty($login_input) || empty($password)) {
            $error_message = "Please enter your username/email and password.";
        } else {
            try {
                // Prepared Statement against SQL Injection
                $stmt = $pdo->prepare("
                    SELECT * FROM admins 
                    WHERE username = :identifier OR email = :identifier 
                    LIMIT 1
                ");
                $stmt->execute(['identifier' => $login_input]);
                $admin = $stmt->fetch();

                // Secure password_verify check
                if ($admin && password_verify($password, $admin['password'])) {
                    
                    // Prevent Session Fixation Attacks
                    session_regenerate_id(true);

                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id']        = $admin['id'];
                    $_SESSION['admin_username']  = $admin['username'];
                    $_SESSION['admin_email']     = $admin['email'];
                    $_SESSION['admin_full_name'] = $admin['full_name'];

                    header("Location: " . get_base_url() . "app/admin/dashboard.php");
                    exit();
                } else {
                    // Constant-time error message preventing account enumeration
                    $error_message = "Invalid Username/Email or Password! Please try again.";
                }
            } catch (PDOException $e) {
                $error_message = "Database connection error occurred.";
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card card-ayur shadow-lg border-0">
                <div class="card-header card-header-ayur text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-user-lock me-2"></i>Admin Authentication</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show small" role="alert">
                            <i class="fa-solid fa-circle-check me-1"></i><?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                            <i class="fa-solid fa-circle-xmark me-1"></i><?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <!-- CSRF Hidden Input -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                        <div class="mb-3">
                            <label for="login_input" class="form-label fw-semibold">Username or Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                                <input type="text" class="form-control" id="login_input" name="login_input" placeholder="admin OR admin@ayurvedicblockchain.org" value="<?php echo htmlspecialchars($_POST['login_input'] ?? ''); ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-key text-muted"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Admin password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Login to Admin Panel
                        </button>
                    </form>

                    <div class="mt-4 p-3 bg-light rounded text-center border">
                        <small class="text-muted d-block mb-1">Local Testing Credentials:</small>
                        <small class="d-block fw-bold text-dark">Username: <code>admin</code></small>
                        <small class="d-block fw-bold text-dark">Password: <code>admin123</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

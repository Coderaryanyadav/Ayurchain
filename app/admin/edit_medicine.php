<?php
// ====================================================================
// Edit Ayurvedic Medicine (Protected Admin Page)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: edit_medicine.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// PROTECTED: Require admin login
check_admin_login();

$medicine_id = trim($_GET['id'] ?? $_POST['medicine_id'] ?? '');

if (empty($medicine_id)) {
    header("Location: medicines.php");
    exit();
}

$message = "";
$message_type = "";

// Fetch existing record
try {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE medicine_id = :id LIMIT 1");
    $stmt->execute(['id' => $medicine_id]);
    $medicine = $stmt->fetch();

    if (!$medicine) {
        die("<div class='container my-4'><div class='alert alert-danger'>Medicine record not found!</div></div>");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicine_name      = trim($_POST['medicine_name'] ?? '');
    $botanical_name     = trim($_POST['botanical_name'] ?? '');
    $category           = trim($_POST['category'] ?? '');
    $ingredients        = trim($_POST['ingredients'] ?? '');
    $form               = trim($_POST['form'] ?? '');
    $manufacturer       = trim($_POST['manufacturer'] ?? '');
    $manufacturing_date = trim($_POST['manufacturing_date'] ?? '');
    $expiry_date        = trim($_POST['expiry_date'] ?? '');
    $source             = trim($_POST['source'] ?? '');
    $description        = trim($_POST['description'] ?? '');

    if (empty($medicine_name)) {
        $message = "Medicine Name is required!";
        $message_type = "danger";
    } else {
        try {
            $update_stmt = $pdo->prepare("
                UPDATE medicines SET 
                    medicine_name = :medicine_name,
                    botanical_name = :botanical_name,
                    category = :category,
                    ingredients = :ingredients,
                    form = :form,
                    manufacturer = :manufacturer,
                    manufacturing_date = :manufacturing_date,
                    expiry_date = :expiry_date,
                    source = :source,
                    description = :description
                WHERE medicine_id = :medicine_id
            ");

            $update_stmt->execute([
                'medicine_name'      => $medicine_name,
                'botanical_name'     => $botanical_name,
                'category'           => $category,
                'ingredients'        => $ingredients,
                'form'               => $form,
                'manufacturer'       => $manufacturer,
                'manufacturing_date' => $manufacturing_date,
                'expiry_date'        => $expiry_date,
                'source'             => $source,
                'description'        => $description,
                'medicine_id'        => $medicine_id
            ]);

            // Log update action in medicine_history audit table
            $hist_stmt = $pdo->prepare("
                INSERT INTO medicine_history (medicine_id, action_type, action_details, performed_by)
                VALUES (:medicine_id, 'UPDATED', 'Medicine details modified by admin.', :performed_by)
            ");
            $hist_stmt->execute([
                'medicine_id'  => $medicine_id,
                'performed_by' => $_SESSION['admin_username'] ?? 'Admin'
            ]);

            $message = "Medicine profile updated successfully!";
            $message_type = "success";

            // Refresh medicine record array
            $stmt->execute(['id' => $medicine_id]);
            $medicine = $stmt->fetch();

        } catch (PDOException $e) {
            $message = "Update Error: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="mb-3">
                <a href="view_medicine.php?id=<?php echo urlencode($medicine_id); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Medicine Profile
                </a>
            </div>

            <div class="card card-ayur shadow-sm">
                <div class="card-header card-header-ayur py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Medicine Record</h4>
                    <span class="badge bg-warning text-dark font-monospace fs-6"><?php echo htmlspecialchars($medicine['medicine_id']); ?></span>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-1"></i><?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="edit_medicine.php">
                        <input type="hidden" name="medicine_id" value="<?php echo htmlspecialchars($medicine['medicine_id']); ?>">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Medicine ID (Locked)</label>
                                <input type="text" class="form-control bg-light font-monospace" value="<?php echo htmlspecialchars($medicine['medicine_id']); ?>" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Batch Number (Locked)</label>
                                <input type="text" class="form-control bg-light font-monospace" value="<?php echo htmlspecialchars($medicine['batch_number']); ?>" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" name="medicine_name" class="form-control" value="<?php echo htmlspecialchars($medicine['medicine_name']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Botanical / Latin Name</label>
                                <input type="text" name="botanical_name" class="form-control" value="<?php echo htmlspecialchars($medicine['botanical_name']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category" class="form-select">
                                    <option value="Churna" <?php if ($medicine['category'] === 'Churna') echo 'selected'; ?>>Churna (Powder)</option>
                                    <option value="Rasayana" <?php if ($medicine['category'] === 'Rasayana') echo 'selected'; ?>>Rasayana (Rejuvenator)</option>
                                    <option value="Bhasma" <?php if ($medicine['category'] === 'Bhasma') echo 'selected'; ?>>Bhasma (Ash)</option>
                                    <option value="Asava & Arishta" <?php if ($medicine['category'] === 'Asava & Arishta') echo 'selected'; ?>>Asava & Arishta (Liquid)</option>
                                    <option value="Vati & Gutika" <?php if ($medicine['category'] === 'Vati & Gutika') echo 'selected'; ?>>Vati & Gutika (Tablets)</option>
                                    <option value="Taila" <?php if ($medicine['category'] === 'Taila') echo 'selected'; ?>>Taila (Herbal Oil)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Form / Type</label>
                                <input type="text" name="form" class="form-control" value="<?php echo htmlspecialchars($medicine['form']); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Manufacturer</label>
                                <input type="text" name="manufacturer" class="form-control" value="<?php echo htmlspecialchars($medicine['manufacturer']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Source Region</label>
                                <input type="text" name="source" class="form-control" value="<?php echo htmlspecialchars($medicine['source']); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Manufacturing Date</label>
                                <input type="date" name="manufacturing_date" class="form-control" value="<?php echo htmlspecialchars($medicine['manufacturing_date']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control" value="<?php echo htmlspecialchars($medicine['expiry_date']); ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Active Ingredients</label>
                                <textarea name="ingredients" class="form-control" rows="2"><?php echo htmlspecialchars($medicine['ingredients']); ?></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($medicine['description']); ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="view_medicine.php?id=<?php echo urlencode($medicine_id); ?>" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-gold px-4 fw-bold shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Medicine Record
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

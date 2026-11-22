<?php
// ====================================================================
// Medicine Directory (Phase 15 Polished UI & Empty-State Container)
// Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
// File: medicines.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$search_query    = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$where_clauses = ["1=1"];
$params = [];

if (!empty($search_query)) {
    $where_clauses[] = "(m.medicine_id LIKE :search OR m.medicine_name LIKE :search OR m.batch_number LIKE :search OR m.manufacturer LIKE :search OR m.botanical_name LIKE :search)";
    $params['search'] = '%' . $search_query . '%';
}

if (!empty($category_filter)) {
    $where_clauses[] = "m.category = :category";
    $params['category'] = $category_filter;
}

$where_sql = implode(" AND ", $where_clauses);

try {
    $count_sql = "SELECT COUNT(*) FROM medicines m WHERE $where_sql";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $limit);
    if ($total_pages < 1) $total_pages = 1;

    $data_sql = "
        SELECT 
            m.id, m.medicine_id, m.medicine_name, m.botanical_name, m.category, 
            m.manufacturer, m.batch_number, m.manufacturing_date, m.expiry_date, 
            b.transaction_hash 
        FROM medicines m
        LEFT JOIN blockchain_records b ON m.medicine_id = b.medicine_id
        WHERE $where_sql
        ORDER BY m.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($data_sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue(':' . $key, $val, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $medicines = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_msg = "Database Query Error: " . $e->getMessage();
}
?>

<div class="container my-4">

    <!-- Alerts -->
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show p-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>Medicine record deleted successfully from system database.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show p-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>Medicine profile updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fa-solid fa-pills text-success me-2"></i>Ayurvedic Medicine Directory</h2>
            <p class="text-muted small mb-0">Total Formulations Found: <strong><?php echo $total_records; ?></strong></p>
        </div>
        <?php if (is_admin_logged_in()): ?>
            <a href="add_medicine.php" class="btn btn-gold fw-bold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Add New Medicine
            </a>
        <?php endif; ?>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card card-ayur mb-4 p-3 bg-white shadow-sm">
        <form method="GET" action="medicines.php" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by Medicine ID, Name, Batch Number, or Manufacturer..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="">-- All Categories --</option>
                    <option value="Churna" <?php if ($category_filter === 'Churna') echo 'selected'; ?>>Churna (Powder)</option>
                    <option value="Rasayana" <?php if ($category_filter === 'Rasayana') echo 'selected'; ?>>Rasayana (Rejuvenator)</option>
                    <option value="Bhasma" <?php if ($category_filter === 'Bhasma') echo 'selected'; ?>>Bhasma (Ash)</option>
                    <option value="Asava & Arishta" <?php if ($category_filter === 'Asava & Arishta') echo 'selected'; ?>>Asava & Arishta (Liquid)</option>
                    <option value="Vati & Gutika" <?php if ($category_filter === 'Vati & Gutika') echo 'selected'; ?>>Vati & Gutika (Tablets)</option>
                    <option value="Taila" <?php if ($category_filter === 'Taila') echo 'selected'; ?>>Taila (Oil)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-ayur w-100 fw-bold">Search</button>
            </div>
        </form>
    </div>

    <!-- Medicine Table / Empty State Container -->
    <?php if (empty($medicines)): ?>
        <div class="card card-ayur shadow-sm">
            <div class="card-body p-0">
                <div class="empty-state-box">
                    <div class="empty-state-icon">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <h4 class="fw-bold text-dark">No Medicine Records Found</h4>
                    <p class="text-muted col-md-6 mx-auto mb-4">
                        We couldn't find any Ayurvedic formulations matching your search query or filter settings.
                    </p>
                    <?php if (is_admin_logged_in()): ?>
                        <a href="add_medicine.php" class="btn btn-gold fw-bold shadow-sm">
                            <i class="fa-solid fa-plus me-1"></i> Add First Medicine
                        </a>
                    <?php else: ?>
                        <a href="medicines.php" class="btn btn-outline-secondary">Reset Search Filters</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card card-ayur shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-ayur">
                        <thead>
                            <tr>
                                <th>Medicine ID</th>
                                <th>Medicine Name</th>
                                <th>Botanical Name</th>
                                <th>Manufacturer</th>
                                <th>Batch Number</th>
                                <th>Mfg Date</th>
                                <th>Exp Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicines as $med): ?>
                                <tr>
                                    <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($med['medicine_id']); ?></span></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($med['medicine_name']); ?></td>
                                    <td><em><?php echo htmlspecialchars($med['botanical_name']); ?></em></td>
                                    <td class="small"><?php echo htmlspecialchars($med['manufacturer']); ?></td>
                                    <td><code><?php echo htmlspecialchars($med['batch_number']); ?></code></td>
                                    <td class="small text-muted"><?php echo htmlspecialchars($med['manufacturing_date']); ?></td>
                                    <td class="small text-muted"><?php echo htmlspecialchars($med['expiry_date']); ?></td>
                                    <td>
                                        <?php if (!empty($med['transaction_hash'])): ?>
                                            <span class="badge badge-verified-onchain"><i class="fa-solid fa-link me-1"></i> Mined</span>
                                        <?php else: ?>
                                            <span class="badge badge-offchain"><i class="fa-solid fa-database me-1"></i> Off-Chain</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="view_medicine.php?id=<?php echo urlencode($med['medicine_id']); ?>" class="btn btn-ayur" title="View Details">
                                                <i class="fa-solid fa-eye me-1"></i> View
                                            </a>
                                            <?php if (is_admin_logged_in()): ?>
                                                <a href="edit_medicine.php?id=<?php echo urlencode($med['medicine_id']); ?>" class="btn btn-outline-warning text-dark" title="Edit Metadata">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <a href="delete_medicine.php?id=<?php echo urlencode($med['medicine_id']); ?>" class="btn btn-outline-danger" title="Delete Record" onclick="return confirm('Are you sure you want to delete medicine <?php echo htmlspecialchars($med['medicine_id']); ?>?');">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Medicine pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="medicines.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_query); ?>&category=<?php echo urlencode($category_filter); ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                            <a class="page-link <?php if ($page == $i) echo 'bg-success border-success text-white'; ?>" href="medicines.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&category=<?php echo urlencode($category_filter); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="medicines.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_query); ?>&category=<?php echo urlencode($category_filter); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

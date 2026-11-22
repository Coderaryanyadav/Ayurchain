<?php
// Root Compatibility Redirect
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: app/admin/add_medicine.php" . $query);
exit();

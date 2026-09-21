<?php
// ====================================================================
// View Medicine Redirector / Bridge
// File: app/admin/view_medicine.php
// ====================================================================

$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: ../public/view_medicine.php" . $query);
exit();

<?php
// Root Compatibility Redirect
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: app/admin/history.php" . $query);
exit();

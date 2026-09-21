<?php
// ====================================================================
// Helper Functions Library
// Project: AYURCHAIN
// File: app/includes/functions.php
// ====================================================================

/**
 * Sanitize text output for XSS prevention
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format date strings
 */
function format_date($date_str, $format = 'd M Y') {
    if (empty($date_str)) return 'N/A';
    return date($format, strtotime($date_str));
}

/**
 * Render Blockchain Verification Badge
 */
function render_verification_badge($transaction_hash) {
    if (!empty($transaction_hash)) {
        return '<span class="badge badge-verified-onchain"><i class="fa-solid fa-link me-1"></i> Mined</span>';
    } else {
        return '<span class="badge badge-offchain"><i class="fa-solid fa-database me-1"></i> Off-Chain</span>';
    }
}
?>

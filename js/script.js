// ====================================================================
// Client-side JavaScript
// Project: Blockchain-Based Ayurvedic Medicine Storage and Verification System
// File: js/script.js
// ====================================================================

document.addEventListener("DOMContentLoaded", function () {
    console.log("Ayurvedic Blockchain System JS Loaded.");

    // Auto-dismiss alert notifications after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

/**
 * Calculate SHA-256 hash of a file on client side using Web Crypto API
 * @param {File} file 
 * @returns {Promise<string>} Hexadecimal SHA-256 string
 */
async function calculateFileSHA256(file) {
    const arrayBuffer = await file.arrayBuffer();
    const hashBuffer = await crypto.subtle.digest("SHA-256", arrayBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex;
}

// ====================================================================
// Client-side Application Script
// Project: AYURCHAIN
// File: assets/js/script.js
// ====================================================================

document.addEventListener("DOMContentLoaded", function () {
    console.log("AYURCHAIN System JS Loaded.");

    // Auto-dismiss notification alerts
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

/**
 * 1-Click Demo Data Auto-Populator for College Presentations & Evaluations
 */
function fillDemoMedicineData() {
    const randomIdSuffix = Math.floor(Math.random() * 900) + 100;
    const medId = document.querySelector('input[name="medicine_id"]');
    const medName = document.querySelector('input[name="medicine_name"]');
    const botanical = document.querySelector('input[name="botanical_name"]');
    const category = document.querySelector('select[name="category"]');
    const form = document.querySelector('input[name="form"]');
    const batch = document.querySelector('input[name="batch_number"]');
    const manufacturer = document.querySelector('input[name="manufacturer"]');
    const source = document.querySelector('input[name="source"]');
    const mfgDate = document.querySelector('input[name="manufacturing_date"]');
    const expDate = document.querySelector('input[name="expiry_date"]');
    const ingredients = document.querySelector('textarea[name="ingredients"]');
    const description = document.querySelector('textarea[name="description"]');

    if (medId) medId.value = "AYU" + randomIdSuffix;
    if (medName) medName.value = "Brahmi Rasayana";
    if (botanical) botanical.value = "Bacopa monnieri";
    if (category) category.value = "Rasayana";
    if (form) form.value = "Herbal Jam / Lehya";
    if (batch) batch.value = "BATCH-BRA-" + new Date().getFullYear() + "-" + randomIdSuffix;
    if (manufacturer) manufacturer.value = "Dabur India Ltd - Ayurvedic Division";
    if (source) source.value = "Western Ghats Certified Organic Reserve, Kerala";
    if (mfgDate) mfgDate.value = "2025-06-01";
    if (expDate) expDate.value = "2028-06-01";
    if (ingredients) ingredients.value = "Brahmi (Bacopa monnieri), Shankhpushpi, Vacha, Ghee, Pure Honey, Cardamom";
    if (description) description.value = "Traditional Ayurvedic cognitive memory enhancer and natural neuroprotective rejuvenator formulated according to Charaka Samhita guidelines.";
}

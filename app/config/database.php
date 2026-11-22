<?php
// ====================================================================
// Universal Database Configuration (MySQL + SQLite Fallback)
// Project: AYURCHAIN - Blockchain-Based Ayurvedic Medicine Storage & Verification System
// File: app/config/database.php
// ====================================================================

// Support standard cloud & local environment variables
$db_url   = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';
$host     = getenv('DB_HOST') ?: 'localhost';
$port     = getenv('DB_PORT') ?: '3306';
$db_name  = getenv('DB_DATABASE') ?: getenv('DB_NAME') ?: 'ayurvedic_blockchain';
$username = getenv('DB_USERNAME') ?: getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: '';

$pdo = null;

// Parse DATABASE_URL / MYSQL_URL if provided
if (!empty($db_url)) {
    $parsed = parse_url($db_url);
    if ($parsed && isset($parsed['host'])) {
        $host     = $parsed['host'];
        $port     = $parsed['port'] ?? 3306;
        $username = $parsed['user'] ?? $username;
        $password = $parsed['pass'] ?? $password;
        $db_name  = isset($parsed['path']) ? ltrim($parsed['path'], '/') : $db_name;
    }
}

// Check environment flags
$force_sqlite = (getenv('DB_CONNECTION') === 'sqlite');
$is_vercel    = !empty(getenv('VERCEL')) || !empty($_ENV['VERCEL']);

// 1. Attempt MySQL connection unless SQLite is forced or on Vercel without remote DB credentials
if (!$force_sqlite && !($is_vercel && empty($db_url) && $host === 'localhost')) {
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 2,
        ]);
    } catch (Throwable $e) {
        $pdo = null; // Seamless fallback to SQLite
    }
}

// 2. If MySQL is unavailable (e.g. Vercel serverless demo mode, or local without MySQL service)
if ($pdo === null) {
    try {
        $sqlite_dir = $is_vercel ? '/tmp' : __DIR__ . '/../../database';
        if (!is_dir($sqlite_dir)) {
            @mkdir($sqlite_dir, 0755, true);
        }
        $sqlite_file = rtrim($sqlite_dir, '/\\') . '/ayurvedic_blockchain.sqlite';
        $needs_init  = !file_exists($sqlite_file) || (filesize($sqlite_file) === 0);

        $pdo = new PDO("sqlite:" . $sqlite_file, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("PRAGMA foreign_keys = ON;");

        // Verify if tables exist; if not, initialize schema & demo data
        if ($needs_init) {
            init_ayurchain_sqlite($pdo);
        } else {
            $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='medicines'")->fetch();
            if (!$check) {
                init_ayurchain_sqlite($pdo);
            }
        }
    } catch (Throwable $sqlite_err) {
        die("<div style='color:red; font-family:sans-serif; padding:20px; text-align:center;'>
                <h2>AyurChain Database Initialization Error</h2>
                <p>" . htmlspecialchars($sqlite_err->getMessage()) . "</p>
             </div>");
    }
}

/**
 * Auto-initialize SQLite Schema and Seed Demo Data
 */
function init_ayurchain_sqlite(PDO $db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS medicines (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            medicine_id TEXT NOT NULL UNIQUE,
            medicine_name TEXT NOT NULL,
            botanical_name TEXT NOT NULL,
            category TEXT NOT NULL,
            ingredients TEXT NOT NULL,
            form TEXT NOT NULL,
            manufacturer TEXT NOT NULL,
            batch_number TEXT NOT NULL,
            manufacturing_date TEXT NOT NULL,
            expiry_date TEXT NOT NULL,
            source TEXT DEFAULT NULL,
            description TEXT DEFAULT NULL,
            certificate_hash TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS medicine_documents (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            medicine_id TEXT NOT NULL,
            document_name TEXT NOT NULL,
            file_path TEXT NOT NULL,
            file_size INTEGER NOT NULL,
            file_type TEXT NOT NULL,
            certificate_hash TEXT NOT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE IF NOT EXISTS blockchain_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            medicine_id TEXT NOT NULL,
            batch_number TEXT NOT NULL,
            record_hash TEXT NOT NULL,
            certificate_hash TEXT NOT NULL,
            transaction_hash TEXT NOT NULL UNIQUE,
            block_number INTEGER DEFAULT NULL,
            blockchain_timestamp INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE IF NOT EXISTS medicine_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            medicine_id TEXT NOT NULL,
            batch_number TEXT DEFAULT NULL,
            status TEXT NOT NULL,
            location TEXT DEFAULT NULL,
            action_details TEXT DEFAULT NULL,
            transaction_hash TEXT DEFAULT NULL,
            block_number INTEGER DEFAULT NULL,
            performed_by TEXT DEFAULT 'Admin',
            performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE ON UPDATE CASCADE
        );
    ");

    // 1. Seed Default Admin: admin / admin123
    $admin_pw = password_hash('admin123', PASSWORD_BCRYPT);
    $admin_stmt = $db->prepare("INSERT OR IGNORE INTO admins (username, password, full_name, email) VALUES (?, ?, ?, ?)");
    $admin_stmt->execute(['admin', $admin_pw, 'System Administrator', 'admin@ayurchain.org']);

    // 2. Seed Demo Ayurvedic Medicines
    $med_stmt = $db->prepare("INSERT OR IGNORE INTO medicines (
        medicine_id, medicine_name, botanical_name, category, form, manufacturer,
        manufacturing_date, expiry_date, batch_number, source, ingredients, description, certificate_hash
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $med_stmt->execute([
        'AYU001', 'Ashwagandha Churna', 'Withania somnifera', 'Churna', 'Herbal Powder',
        'Dabur India Ltd - Ayurvedic Division', '2025-01-15', '2027-01-15', 'BATCH-ASH-2025-01',
        'Madhya Pradesh Organic Cultivation Zone', 'Pure Ashwagandha (Withania somnifera) root extract powder',
        'Premium standardized adaptogenic stress-relief herbal powder.',
        '3a7be4b84b8d7522d109f25712f6ecf911964f4347ecfe54be0ad1b29a674511'
    ]);

    $med_stmt->execute([
        'AYU002', 'Triphala Guti', 'Terminalia chebula, bellerica, Emblica officinalis', 'Vati & Gutika', 'Standardized Tablet',
        'Baidyanath Ayurvedic Bhawan', '2025-02-10', '2028-02-10', 'BATCH-TRI-2025-02',
        'Vindhya Herbal Reserve, UP', 'Amalaki, Haritaki, Bibhitaki in equal proportions',
        'Classical digestive stimulant and colon detox formulation.',
        '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'
    ]);

    $med_stmt->execute([
        'AYU003', 'Mahanarayan Taila', 'Multiple Ayurvedic Herbs & Sesame Oil', 'Taila', 'Medicated Herbal Oil',
        'Kottakkal Arya Vaidya Sala', '2025-03-01', '2028-03-01', 'BATCH-MAH-2025-03',
        'Kerala Herbal Estate', 'Sesame oil base, Bilva, Ashwagandha, Bala, Shatavari, Rasna',
        'Classical formulation for neuromuscular and joint mobility support.',
        null
    ]);

    $med_stmt->execute([
        'AYU-2026-001', 'Chyawanprash Special', 'Emblica officinalis', 'Rasayana', 'Paste',
        'Dabur Herbal Labs India', '2026-01-15', '2028-01-14', 'BATCH-AYU-2026-01',
        'Uttarakhand Organic Reserve', 'Amla, Ashwagandha, Guduchi, Honey',
        'Premium restorative herbal jam formulated according to Charaka Samhita.',
        'a591a6d40bf420404a011733cfb7b190d62c65bf0bcda32b57b277d9ad9f146e'
    ]);

    $med_stmt->execute([
        'AYU-2026-002', 'Brahmi Rasayana', 'Bacopa monnieri', 'Rasayana', 'Herbal Jam / Lehya',
        'Patanjali Ayurveda Kendra', '2026-02-10', '2028-02-09', 'BATCH-AYU-2026-02',
        'Western Ghats Certified Organic Reserve, Kerala', 'Brahmi (Bacopa monnieri), Shankhpushpi, Vacha, Ghee, Pure Honey, Cardamom',
        'Traditional Ayurvedic cognitive memory enhancer and natural neuroprotective rejuvenator.',
        '7f83b1657ff1fc53b92dc18148a1d65dfc2d4b1fa3d677284ddd200126d9069e'
    ]);

    // 3. Seed Lab Quality Documents
    $doc_stmt = $db->prepare("INSERT OR IGNORE INTO medicine_documents (medicine_id, document_name, file_path, file_size, file_type, certificate_hash) VALUES (?, ?, ?, ?, ?, ?)");
    $doc_stmt->execute(['AYU001', 'NABL_Lab_Quality_Cert_AYU001.pdf', 'storage/certificates/demo_ashwagandha_cert.pdf', 1048576, 'application/pdf', '3a7be4b84b8d7522d109f25712f6ecf911964f4347ecfe54be0ad1b29a674511']);
    $doc_stmt->execute(['AYU002', 'Heavy_Metal_Purity_Analysis_AYU002.pdf', 'storage/certificates/demo_triphala_cert.pdf', 845210, 'application/pdf', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92']);

    // 4. Seed Mined Blockchain Records with Verified Cryptographic SHA-256 Hashes
    $hash_ayu001 = hash('sha256', 'AYU001BATCH-ASH-2025-012025-01-152027-01-15');
    $hash_ayu002 = hash('sha256', 'AYU002BATCH-TRI-2025-022025-02-102028-02-10');
    $hash_ayu2026 = hash('sha256', 'AYU-2026-001BATCH-AYU-2026-012026-01-152028-01-14');

    $bc_stmt = $db->prepare("INSERT OR IGNORE INTO blockchain_records (medicine_id, batch_number, record_hash, certificate_hash, transaction_hash, block_number, blockchain_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $bc_stmt->execute(['AYU001', 'BATCH-ASH-2025-01', $hash_ayu001, '3a7be4b84b8d7522d109f25712f6ecf911964f4347ecfe54be0ad1b29a674511', '0x8a92f8b1c4e7d3a5620194857201847583920194857201847583920194857201', 101, 1736937000]);
    $bc_stmt->execute(['AYU002', 'BATCH-TRI-2025-02', $hash_ayu002, '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0x7e29471b05819385cba728194857291048572910485729104857291048572910', 102, 1739183400]);
    $bc_stmt->execute(['AYU-2026-001', 'BATCH-AYU-2026-01', $hash_ayu2026, 'a591a6d40bf420404a011733cfb7b190d62c65bf0bcda32b57b277d9ad9f146e', '0x88df014758d042613d964f43c3968ae240c5f5b24ecfcb41fcf811985474d28d', 103, 1737000000]);

    // 5. Seed Supply Chain Provenance Tracking
    $hist_stmt = $db->prepare("INSERT OR IGNORE INTO medicine_history (medicine_id, batch_number, status, location, action_details, transaction_hash, block_number, performed_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $hist_stmt->execute(['AYU001', 'BATCH-ASH-2025-01', 'Manufactured', 'Haridwar Manufacturing Plant #4', 'Batch manufactured, chemical tested, and hermetically sealed.', '0x8a92f8b1c4e7d3a5620194857201847583920194857201847583920194857201', 101, 'admin']);
    $hist_stmt->execute(['AYU001', 'BATCH-ASH-2025-01', 'Dispatched', 'Central Ayurvedic Logistics Hub, Delhi', 'Dispatched in GPS-monitored climate-controlled carrier.', '0x9b83c7d6e5f4a3b2c1d0e9f8a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8', 103, 'admin']);
    $hist_stmt->execute(['AYU001', 'BATCH-ASH-2025-01', 'Received', 'Regional AYUSH Distribution Center, Mumbai', 'Stock received, barcode verified, placed in dry herbal cold storage.', '0xa1b2c3d4e5f67890123456789abcdef0123456789abcdef0123456789abcdef0', 105, 'admin']);
    $hist_stmt->execute(['AYU002', 'BATCH-TRI-2025-02', 'Manufactured', 'Jhansi Plant #1', 'Initial batch extraction & tablet compression complete.', '0x7e29471b05819385cba728194857291048572910485729104857291048572910', 102, 'admin']);
    $hist_stmt->execute(['AYU-2026-001', 'BATCH-AYU-2026-01', 'Manufactured', 'Dabur Haridwar Factory', 'Initial batch formulation and purity testing completed.', '0x88df014758d042613d964f43c3968ae240c5f5b24ecfcb41fcf811985474d28d', 101, 'admin']);
}
?>

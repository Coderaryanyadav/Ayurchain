-- ====================================================================
-- Database Creation Script (Updated Phase 12)
-- Project: AyurChain - Ayurvedic Medicine Storage & Verification System
-- File: database.sql
-- ====================================================================

CREATE DATABASE IF NOT EXISTS ayurvedic_blockchain;
USE ayurvedic_blockchain;

-- Table 1: admins
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 2: medicines
CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id VARCHAR(50) NOT NULL UNIQUE,
    medicine_name VARCHAR(150) NOT NULL,
    botanical_name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    ingredients TEXT NOT NULL,
    form VARCHAR(50) NOT NULL,
    manufacturer VARCHAR(150) NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    manufacturing_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    source VARCHAR(150) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    certificate_hash VARCHAR(64) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 3: medicine_documents
CREATE TABLE IF NOT EXISTS medicine_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id VARCHAR(50) NOT NULL,
    document_name VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    certificate_hash VARCHAR(64) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 4: blockchain_records
CREATE TABLE IF NOT EXISTS blockchain_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id VARCHAR(50) NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    record_hash VARCHAR(64) NOT NULL,
    certificate_hash VARCHAR(64) NOT NULL,
    transaction_hash VARCHAR(66) NOT NULL UNIQUE,
    block_number BIGINT DEFAULT NULL,
    blockchain_timestamp BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 5: medicine_history (Updated with status, location & transaction hash)
CREATE TABLE IF NOT EXISTS medicine_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id VARCHAR(50) NOT NULL,
    batch_number VARCHAR(50) DEFAULT NULL,
    status VARCHAR(50) NOT NULL, -- Manufactured, Dispatched, Received, Distributed, Sold
    location VARCHAR(150) DEFAULT NULL,
    action_details TEXT DEFAULT NULL,
    transaction_hash VARCHAR(66) DEFAULT NULL,
    block_number BIGINT DEFAULT NULL,
    performed_by VARCHAR(50) DEFAULT 'Admin',
    performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin
INSERT INTO admins (username, password, full_name, email) VALUES
('admin', '$2y$10$wE1f/a0dR1eC.mK.0N9Nje/z5h7.sLd.8xP6V10aB/2O/vB.7n1qG', 'System Administrator', 'admin@ayurvedicblockchain.org')
ON DUPLICATE KEY UPDATE username=username;

-- Seed Sample Medicines
INSERT INTO medicines (
    medicine_id, medicine_name, botanical_name, category, ingredients, form, 
    manufacturer, batch_number, manufacturing_date, expiry_date, source, description, certificate_hash
) VALUES
('AYU-2026-001', 'Chyawanprash Special', 'Emblica officinalis', 'Rasayana', 'Amla, Ashwagandha, Guduchi, Honey', 'Paste', 'Dabur Herbal Labs India', 'BATCH-AYU-2026-01', '2026-01-15', '2028-01-14', 'Uttarakhand Organic Reserve', 'Premium restorative herbal jam.', 'a591a6d40bf420404a011733cfb7b190d62c65bf0bcda32b57b277d9ad9f146e'),
('AYU-2026-002', 'Ashwagandha Churna', 'Withania somnifera', 'Churna', 'Pure Organic Ashwagandha Root Powder', 'Powder', 'Patanjali Ayurveda Kendra', 'BATCH-AYU-2026-02', '2026-02-10', '2028-02-09', 'Madhya Pradesh Farms', 'Adaptogenic Ayurvedic root powder.', '7f83b1657ff1fc53b92dc18148a1d65dfc2d4b1fa3d677284ddd200126d9069e')
ON DUPLICATE KEY UPDATE medicine_id=medicine_id;

-- Seed Sample Lifecycle Timeline History
INSERT INTO medicine_history (medicine_id, batch_number, status, location, action_details, transaction_hash, block_number, performed_by) VALUES
('AYU-2026-001', 'BATCH-AYU-2026-01', 'Manufactured', 'Dabur Haridwar Factory', 'Initial batch formulation and purity testing completed.', '0x88df014758d042613d964f43c3968ae240c5f5b24ecfcb41fcf811985474d28d', 101, 'admin'),
('AYU-2026-001', 'BATCH-AYU-2026-01', 'Dispatched', 'Haridwar Logistics Hub', 'Batch sealed in thermal containers and dispatched to regional depot.', '0x99ef014758d042613d964f43c3968ae240c5f5b24ecfcb41fcf811985474d29e', 102, 'admin'),
('AYU-2026-001', 'BATCH-AYU-2026-01', 'Received', 'Delhi Central Storage', 'Shipment received and inspected at central distribution node.', '0xaa01014758d042613d964f43c3968ae240c5f5b24ecfcb41fcf811985474d30f', 103, 'admin');

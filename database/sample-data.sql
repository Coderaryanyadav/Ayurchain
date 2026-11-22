USE `ayurvedic_blockchain`;

-- 1. Insert Demo Ayurvedic Medicines (idempotent with INSERT IGNORE)
INSERT IGNORE INTO `medicines` (
    `medicine_id`, 
    `medicine_name`, 
    `botanical_name`, 
    `category`, 
    `form`, 
    `manufacturer`, 
    `manufacturing_date`, 
    `expiry_date`, 
    `batch_number`, 
    `source`, 
    `ingredients`, 
    `description`, 
    `certificate_hash`
) VALUES
(
    'AYU001', 
    'Ashwagandha Churna', 
    'Withania somnifera', 
    'Churna', 
    'Herbal Powder', 
    'Dabur India Ltd - Ayurvedic Division', 
    '2025-01-15', 
    '2027-01-15', 
    'BATCH-ASH-2025-01', 
    'Madhya Pradesh Organic Cultivation Zone', 
    'Pure Ashwagandha (Withania somnifera) root extract powder', 
    'Premium standardized adaptogenic stress-relief herbal powder.', 
    '3a7be4b84b8d7522d109f25712f6ecf911964f4347ecfe54be0ad1b29a674511'
),
(
    'AYU002', 
    'Triphala Guti', 
    'Terminalia chebula, bellerica, Emblica officinalis', 
    'Vati & Gutika', 
    'Standardized Tablet', 
    'Baidyanath Ayurvedic Bhawan', 
    '2025-02-10', 
    '2028-02-10', 
    'BATCH-TRI-2025-02', 
    'Vindhya Herbal Reserve, UP', 
    'Amalaki, Haritaki, Bibhitaki in equal proportions', 
    'Classical digestive stimulant and colon detox formulation.', 
    '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'
),
(
    'AYU003', 
    'Mahanarayan Taila', 
    'Multiple Ayurvedic Herbs & Sesame Oil', 
    'Taila', 
    'Medicated Herbal Oil', 
    'Kottakkal Arya Vaidya Sala', 
    '2025-03-01', 
    '2028-03-01', 
    'BATCH-MAH-2025-03', 
    'Kerala Herbal Estate', 
    'Sesame oil base, Bilva, Ashwagandha, Bala, Shatavari, Rasna', 
    'Classical formulation for neuromuscular and joint mobility support.', 
    NULL
);

-- 2. Insert Lab Quality Documents
INSERT IGNORE INTO `medicine_documents` (`medicine_id`, `document_name`, `file_path`, `file_size`, `file_type`, `certificate_hash`) VALUES
('AYU001', 'NABL_Lab_Quality_Cert_AYU001.pdf', 'storage/certificates/demo_ashwagandha_cert.pdf', 1048576, 'application/pdf', '3a7be4b84b8d7522d109f25712f6ecf911964f4347ecfe54be0ad1b29a674511'),
('AYU002', 'Heavy_Metal_Purity_Analysis_AYU002.pdf', 'storage/certificates/demo_triphala_cert.pdf', 845210, 'application/pdf', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92');

-- 3. Insert Mined Blockchain Records (With SHA-256 Hashes & Block Numbers)
INSERT IGNORE INTO `blockchain_records` (`medicine_id`, `batch_number`, `record_hash`, `certificate_hash`, `transaction_hash`, `block_number`, `blockchain_timestamp`) VALUES
('AYU001', 'BATCH-ASH-2025-01', SHA2('AYU001BATCH-ASH-2025-012025-01-152027-01-15', 256), '3a7be4b84b8d7522d109f25712f6ecf911964f4347ecfe54be0ad1b29a674511', '0x8a92f8b1c4e7d3a5620194857201847583920194857201847583920194857201', 101, 1736937000),
('AYU002', 'BATCH-TRI-2025-02', SHA2('AYU002BATCH-TRI-2025-022025-02-102028-02-10', 256), '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0x7e29471b05819385cba728194857291048572910485729104857291048572910', 102, 1739183400);

-- 4. Insert Supply Chain Tracking History
INSERT IGNORE INTO `medicine_history` (`medicine_id`, `batch_number`, `status`, `location`, `action_details`, `transaction_hash`, `block_number`) VALUES
('AYU001', 'BATCH-ASH-2025-01', 'Manufactured', 'Haridwar Manufacturing Plant #4', 'Batch manufactured, chemical tested, and hermetically sealed.', '0x8a92f8b1c4e7d3a5620194857201847583920194857201847583920194857201', 101),
('AYU001', 'BATCH-ASH-2025-01', 'Dispatched', 'Central Ayurvedic Logistics Hub, Delhi', 'Dispatched in GPS-monitored climate-controlled carrier.', '0x9b83c7d6e5f4a3b2c1d0e9f8a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8', 103),
('AYU001', 'BATCH-ASH-2025-01', 'Received', 'Regional AYUSH Distribution Center, Mumbai', 'Stock received, barcode verified, placed in dry herbal cold storage.', '0xa1b2c3d4e5f67890123456789abcdef0123456789abcdef0123456789abcdef0', 105),
('AYU002', 'BATCH-TRI-2025-02', 'Manufactured', 'Jhansi Plant #1', 'Initial batch extraction & tablet compression complete.', '0x7e29471b05819385cba728194857291048572910485729104857291048572910', 102);

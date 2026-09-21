-- ====================================================================
-- Sample Seed Data for AYURCHAIN Database
-- File: database/sample-data.sql
-- ====================================================================

USE `ayurvedic_blockchain`;

-- Sample Medicines
INSERT INTO `medicines` (`medicine_id`, `medicine_name`, `category`, `manufacturer`, `manufacturing_date`, `expiry_date`, `batch_number`, `ingredients`, `dosage_instructions`, `blockchain_status`, `qr_code_path`) VALUES
('AYU001', 'Ashwagandha Churna', 'Churna', 'Dabur India Ltd', '2025-01-15', '2027-01-15', 'BATCH-ASH-2025-01', 'Withania somnifera (Ashwagandha root powder)', '1-2 teaspoons twice daily with warm milk or water.', 'RECORDED', ''),
('AYU002', 'Triphala Guti', 'Vati/Gutika', 'Baidyanath Ayurveda', '2025-02-10', '2028-02-10', 'BATCH-TRI-2025-02', 'Amalaki, Haritaki, Bibhitaki', '2 tablets before sleep with warm water.', 'RECORDED', ''),
('AYU003', 'Mahanarayan Taila', 'Taila', 'Kottakkal Arya Vaidya Sala', '2025-03-01', '2028-03-01', 'BATCH-MAH-2025-03', 'Sesame oil, Bilva, Ashwagandha, Bala, Shatavari', 'For external application and massage on joints as advised by physician.', 'PENDING', '');

-- Sample Supply-Chain History
INSERT INTO `medicine_history` (`medicine_id`, `batch_number`, `status`, `location`, `remarks`, `transaction_hash`, `block_number`) VALUES
('AYU001', 'BATCH-ASH-2025-01', 'Manufactured', 'Haridwar Manufacturing Unit #4', 'Batch quality tested & sealed', '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef', 101),
('AYU001', 'BATCH-ASH-2025-01', 'Dispatched', 'Central Ayurvedic Warehouse, Delhi', 'In transit via temperature controlled vehicle', '0xabcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890', 102),
('AYU002', 'BATCH-TRI-2025-02', 'Manufactured', 'Jhansi Plant #1', 'Initial batch production complete', '0x7890abcdef1234567890abcdef1234567890abcdef1234567890abcdef123456', 103);

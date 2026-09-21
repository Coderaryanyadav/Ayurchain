# AYURCHAIN
### Blockchain-Based Ayurvedic Medicine Storage & Verification System

AYURCHAIN is a diploma-level Web2 + Web3 hybrid application designed to store Ayurvedic medicine records and quality lab test certificate hashes on an Ethereum-compatible blockchain (Ganache), making records tamper-evident and publicly verifiable.

---

## Features
- **Admin Dashboard**: Real-time analytics, dynamic charts (Chart.js), and quick action controls.
- **Off-Chain Storage (MySQL)**: Stores detailed botanical attributes, herbs, categories, and file paths.
- **On-Chain Immutability (Solidity Smart Contract)**: Stores 64-character SHA-256 hashes (`certificateHash`, `recordHash`, `timestamp`).
- **Client-Side SHA-256 Hashing**: Calculates digital signatures of uploaded lab certificate PDFs directly in the browser via Web Crypto API.
- **Web3 Wallet Integration (Ethers.js + MetaMask)**: Enables one-click transaction signing without complex backend node setups.
- **Public Verification Portal**: Allows consumers and distributors to perform zero-gas read-only verification checks (`✓ Blockchain Verified` vs `⚠ Verification Failed`).
- **Supply Chain Progression Timeline**: Tracks the 5 lifecycle stages (*Manufactured → Dispatched → Received → Distributed → Sold*).

---

## Technology Stack
- **Frontend**: HTML5, CSS3, Vanilla JavaScript, Bootstrap 5, Chart.js
- **Backend**: PHP (PDO - PHP Data Objects)
- **Database**: MySQL / MariaDB (phpMyAdmin)
- **Blockchain**: Solidity (`^0.8.0`), Ganache Desktop App, Remix IDE
- **Web3 Bridge**: Ethers.js v6 CDN, MetaMask Extension

---

## Local Setup & Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Coderaryanyadav/Ayurchain.git
   ```

2. **Move to XAMPP htdocs**:
   Move the project folder into `C:\xampp\htdocs\ayurvedic-blockchain`.

3. **Database Import**:
   - Start Apache and MySQL in XAMPP Control Panel.
   - Open `http://localhost/phpmyadmin`.
   - Import `database.sql` into database `ayurvedic_blockchain`.

4. **Local Blockchain Setup**:
   - Open **Ganache Desktop App** (`http://127.0.0.1:7545`).
   - Import account private key into **MetaMask**.

5. **Deploy Smart Contract**:
   - Open Remix IDE (`remix.ethereum.org`) and compile `blockchain/contract/AyurvedicMedicineVerification.sol`.
   - Deploy contract to Ganache via MetaMask (`Injected Provider`).
   - Copy deployed contract address into `blockchain/js/contract-config.js`.

6. **Run Application**:
   - Open `http://localhost/ayurvedic-blockchain/` in your browser.
   - Admin Login: Username `admin` | Password `admin123`.

---

## Security Measures
- **SQL Injection**: 100% Parameterized PDO prepared statements.
- **XSS Protection**: HTML entity encoding (`htmlspecialchars`).
- **CSRF Protection**: Cryptographic token generation (`generate_csrf_token()`) and verification (`hash_equals()`).
- **Secure File Uploads**: Strict MIME validation (`mime_content_type`), 5MB size limit, and randomized file names.

---
*Aryan , Hitansh and Nirmay &copy; 2026 AYURCHAIN. All Rights Reserved.*

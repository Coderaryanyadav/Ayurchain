# 🌿 AYURCHAIN – Complete Setup & Presentation Guide

**Project:** Blockchain-Based Ayurvedic Medicine Storage and Verification System  
**Live Production URL (Vercel):** [https://ayurchain-ten.vercel.app](https://ayurchain-ten.vercel.app)  
**GitHub Repository:** [https://github.com/Coderaryanyadav/Ayurchain](https://github.com/Coderaryanyadav/Ayurchain)  

---

## ⚡ 1-Click Online Demo (Zero Setup Required)

You can access and test the entire AyurChain platform live right in your web browser:

| Portal | URL | Description |
| :--- | :--- | :--- |
| 🏠 **Home Landing Page** | [https://ayurchain-ten.vercel.app](https://ayurchain-ten.vercel.app) | Instant batch lookup, live stats, formulation showcase |
| 🛡️ **Public Verification Portal** | [https://ayurchain-ten.vercel.app/verify.php](https://ayurchain-ten.vercel.app/verify.php) | Live cryptographic hash verification & tamper simulation |
| 💊 **Medicines Directory** | [https://ayurchain-ten.vercel.app/medicines.php](https://ayurchain-ten.vercel.app/medicines.php) | Botanical formulations, lab test records & QR generator |
| 🚚 **Supply Chain Provenance** | [https://ayurchain-ten.vercel.app/history.php](https://ayurchain-ten.vercel.app/history.php) | 5-stage timeline from harvest to retail delivery |
| 🔐 **Admin Management Portal** | [https://ayurchain-ten.vercel.app/login.php](https://ayurchain-ten.vercel.app/login.php) | Admin dashboard, analytics charts & medicine registration |
| 🧪 **Automated Test Suite** | [https://ayurchain-ten.vercel.app/test_runner.php](https://ayurchain-ten.vercel.app/test_runner.php) | Live 10-step end-to-end verification test suite |

### 🔐 Admin Login Credentials
- **Username / Email:** `admin` or `admin@ayurchain.org`
- **Password:** `admin123`

---

## 💻 Local Setup & Execution Guide

### Option A: Portable PHP Built-In Server (Fastest Local Setup)

1. Open a terminal / command prompt in this project folder:
   ```bash
   cd Ayurchain
   ```
2. Start the local server:
   ```bash
   php -S localhost:8000
   ```
3. Open `http://localhost:8000` in your browser.  
   *(The universal database automatically initializes SQLite in `database/ayurvedic_blockchain.sqlite` with all seed data if MySQL is not running).*

---

### Option B: Local XAMPP Setup (Apache + MySQL)

1. **Move Folder**: Copy this project folder into your XAMPP web root:
   - Windows: `C:\xampp\htdocs\ayurvedic-blockchain`
   - macOS: `/Applications/XAMPP/xamppfiles/htdocs/ayurvedic-blockchain`
2. **Start Services**: Open XAMPP Control Panel and click **Start** on **Apache** and **MySQL**.
3. **Import Database**:
   - Navigate to `http://localhost/phpmyadmin`.
   - Create database: `ayurvedic_blockchain`.
   - Import `database/database.sql`, then import `database/sample-data.sql`.
4. **Access Portal**: Open `http://localhost/ayurvedic-blockchain/` in your browser.

---

## 🎬 Step-by-Step Presentation & Evaluation Guide

### 🧪 Demo 1: Public Cryptographic Blockchain Verification
1. Open the [Verification Portal](https://ayurchain-ten.vercel.app/verify.php).
2. Click **"Demo Batch 1 (AYU001 - Ashwagandha)"**.
3. **What happens:**
   - Shows **✓ Blockchain Record Verified** with a green cryptographic shield.
   - Displays Block **#101** and Transaction Hash `0x8a92f8b1c4e7...`.
   - Confirms that the digital SHA-256 hash of the NABL purity certificate matches the on-chain smart contract ledger.

---

### 🧪 Demo 2: Tamper Detection (Anti-Counterfeiting Security)
1. On the same page, click the red button: **"Demo Tampered Batch (Tamper Alert)"** (or visit `verify.php?medicine_id=AYU001&tamper=1`).
2. **What happens:**
   - Instantly triggers a high-visibility security alert: **⚠ Verification Failed (Tampering Detected!)**.
   - Explains how someone attempted to alter the batch parameters or certificate off-chain, and the cryptographic hash mismatch against Block #101 caught the fraud.

---

### 🧪 Demo 3: Supply Chain Provenance (Farm to Bottle)
1. Click **Supply Chain Provenance** on the top menu.
2. Select **AYU001 - Ashwagandha Churna**.
3. Inspect the 5 lifecycle milestones:
   - **Manufactured**: Haridwar Manufacturing Plant #4 (Block #101)
   - **Dispatched**: Central Ayurvedic Logistics Hub, Delhi (Block #103)
   - **Received**: Regional AYUSH Distribution Center, Mumbai (Block #105)

---

### 🧪 Demo 4: Mobile QR Code Generation
1. Visit the details page of any formulation (e.g. `view_medicine.php?id=AYU001`).
2. Click **"Generate QR Code"**.
3. A high-resolution QR code appears on screen that can be scanned using any smartphone camera to verify the physical medicine bottle in real time.

---

## 📁 Repository Tree Structure

```text
Ayurchain/
├── api/
│   └── index.php                       # Vercel serverless router and asset streamer
├── app/
│   ├── admin/                          # Administrative control pages
│   │   ├── add_medicine.php            # Register new Ayurvedic formulations & lab certificates
│   │   ├── change_password.php         # Admin credential security
│   │   ├── dashboard.php               # Analytics metrics & dynamic Chart.js
│   │   ├── delete_medicine.php         # Clean up medicine records
│   │   ├── edit_medicine.php           # Edit botanical metadata
│   │   ├── history.php                 # Supply chain provenance tracker
│   │   ├── medicines.php               # Full paginated medicine directory
│   │   ├── profile.php                 # Admin profile settings
│   │   └── view_medicine.php           # Admin medicine detail router
│   ├── api/                            # Internal JSON REST endpoints
│   │   ├── add_history_record.php      # Milestone recording endpoint
│   │   └── save_blockchain_record.php  # On-chain mined transaction storage
│   ├── auth/                           # Authentication modules
│   │   ├── login.php                   # CSRF protected admin login
│   │   └── logout.php                  # Session termination
│   ├── config/
│   │   └── database.php                # Universal MySQL + SQLite PDO connection
│   ├── includes/                       # Shared UI templates & security helpers
│   │   ├── auth.php                    # Session handling & CSRF verification
│   │   ├── footer.php                  # Global footer with Web3, QR & theme JS
│   │   ├── functions.php               # XSS sanitization & helper utilities
│   │   └── header.php                  # Global navigation bar & Sanskrit motto
│   └── public/                         # Public customer-facing pages
│       ├── about.php                   # Project documentation & smart contract specs
│       ├── index.php                   # Landing portal with live batch search
│       ├── verify.php                  # Real-time verification & tamper detection
│       └── view_medicine.php           # Botanical attributes & QR generator
├── assets/
│   ├── css/
│   │   └── style.css                   # Ayurvedic visual theme & responsive styles
│   └── js/
│       └── script.js                   # Client-side Web Crypto SHA-256 hashing
├── blockchain/
│   ├── contract/
│   │   └── AyurvedicMedicineVerification.sol # Solidity smart contract (^0.8.0)
│   └── js/
│       ├── contract-config.js          # Deployed contract ABI & address
│       └── web3-contract.js            # Ethers.js v6 + MetaMask connector
├── database/
│   ├── database.sql                    # MySQL schema definition
│   └── sample-data.sql                 # Sample medicines & mined blockchain records
├── docs/
│   └── VIVA_AND_PRESENTATION_GUIDE.md  # Detailed college viva questions & answers
├── setup/                              # Automated launchers & installation scripts
│   ├── run_portable.bat                # Portable PHP built-in server launcher
│   ├── setup.bat                       # Batch installer launcher
│   ├── setup.ps1                       # PowerShell automated environment installer
│   └── setup_new_computer.bat          # 1-click user setup launcher
├── storage/
│   └── certificates/                   # Sample NABL lab quality test certificates
├── tests/
│   └── test_runner.php                 # 10-step automated end-to-end verification test suite
├── uploads/                            # User document uploads directory
├── .htaccess                           # Apache/XAMPP URL rewrite configuration
├── .gitignore                          # Git ignore definitions
├── index.php                           # Master application entry point
├── vercel.json                         # Vercel serverless functions configuration
└── README.md                           # Comprehensive GitHub documentation
```

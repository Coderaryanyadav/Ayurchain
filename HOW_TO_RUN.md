# AYURCHAIN – How to Run the Project & Complete Operating Guide

> **System Name:** AYURCHAIN – Blockchain-Based Ayurvedic Medicine Storage and Verification System  
> **Repository:** [https://github.com/Coderaryanyadav/Ayurchain](https://github.com/Coderaryanyadav/Ayurchain)  
> **Target Audience:** College Faculty, Evaluators, and IT Students

---

## 1. Overview & Architecture

**AYURCHAIN** combines traditional Ayurvedic medicine quality record-keeping with Ethereum blockchain tamper-evidence:
- **Off-Chain Database (MySQL):** Stores detailed medicine batch information, ingredient compositions, lab certificates, and manufacturing dates.
- **On-Chain Testnet (Ganache / Ethereum):** Stores cryptographic SHA-256 hashes (`certificate_hash` and `record_hash`) and lifecycle status transitions on a Solidity smart contract.
- **Client Application (PHP / JS / Ethers.js / Bootstrap):** Responsive web portal for public verification and admin management with MetaMask wallet connectivity.

```
+-------------------------------------------------------------+
|                      AYURCHAIN SYSTEM                       |
+-------------------------------------------------------------+
|                                                             |
|  [ Public Users ]                [ Admin / Manufacturers ]  |
|         │                                    │              |
|         ▼                                    ▼              |
|  Verify & QR Lookup                   Add / Manage Batches  |
|         │                                    │              |
|         ▼                                    ▼              |
|  +──────────────+                    +──────────────+       |
|  |  MySQL DB    | <────────────────> |  MetaMask &  |       |
|  |  (Off-Chain) |                    |  Ethers.js   |       |
|  +──────────────+                    +──────────────+       |
|         ▲                                    │              |
|         │    Compare Hashes                  ▼              |
|         └─── [ Tamper-Evident Check ] ── [ Smart Contract ] |
|                                          [ (Ganache/Eth)  ] |
+-------------------------------------------------------------+
```

---

## 2. Quick Setup Options

### Option A: Automatic Setup (Recommended for Windows)

1. Open the project folder in terminal or file explorer.
2. Double-click `setup/setup.bat` (or run PowerShell as Administrator: `powershell -ExecutionPolicy Bypass -File setup/setup.ps1`).
3. The script will:
   - Verify PHP, Apache, and MySQL
   - Create database `ayurvedic_blockchain`
   - Import `database/database.sql` and `database/sample-data.sql`
   - Ensure storage directory permissions (`storage/certificates/`)
   - Launch your browser to the web portal

---

### Option B: Manual Setup in 4 Steps

#### Step 1: Start MySQL & Apache in XAMPP
1. Open **XAMPP Control Panel**.
2. Click **Start** for **Apache** and **MySQL**.
3. Copy this project folder into `C:\xampp\htdocs\ayurvedic-blockchain` (or run the built-in PHP server).

#### Step 2: Import Database
1. Open your browser to `http://localhost/phpmyadmin`.
2. Create a new database named `ayurvedic_blockchain` (Collation: `utf8mb4_unicode_ci`).
3. Click **Import** -> Select `database/database.sql` -> Click **Go**.
4. *(Optional Sample Data)*: Click **Import** -> Select `database/sample-data.sql` -> Click **Go**.

#### Step 3: Start Ganache & Connect MetaMask
1. Open **Ganache** (Quickstart Ethereum workspace on `HTTP://127.0.0.1:7545`, Network ID: `5777`).
2. Open **MetaMask** in your browser:
   - Add a custom network:
     - **Network Name:** Ganache Local
     - **RPC URL:** `http://127.0.0.1:7545`
     - **Chain ID:** `1337` (or `5777`)
     - **Currency Symbol:** `ETH`
   - Import an account using one of the Private Keys displayed in Ganache.

#### Step 4: Deploy Smart Contract
1. Open [https://remix.ethereum.org](https://remix.ethereum.org).
2. Create a new file `AyurvedicMedicineVerification.sol` and paste the contents of `blockchain/contracts/AyurvedicMedicineVerification.sol`.
3. Under **Solidity Compiler**, select compiler `0.8.0` or higher and click **Compile**.
4. Under **Deploy & Run Transactions**:
   - Environment: **Injected Provider - MetaMask**
   - Click **Deploy** and confirm the transaction in MetaMask.
5. Copy the deployed contract address (e.g. `0x123...`).
6. Open `blockchain/js/contract-config.js` and paste your address:
   ```javascript
   const CONTRACT_ADDRESS = "0xYourDeployedContractAddressHere";
   ```

---

## 3. Running with PHP Built-in Server (Alternative without XAMPP)

If running directly via PHP CLI without Apache:
```bash
php -S localhost:8000
```
Then visit: [http://localhost:8000/app/public/index.php](http://localhost:8000/app/public/index.php)

---

## 4. Default Admin Credentials

- **Admin Login URL:** `http://localhost/ayurvedic-blockchain/app/auth/login.php`
- **Email:** `admin@ayurchain.org`
- **Password:** `admin123`

---

## 5. How Each Feature Works (Step-by-Step Flow)

### 1. Adding a Medicine Batch (Admin)
1. Go to **Dashboard** -> **Add Medicine**.
2. Fill in the Medicine ID (e.g., `AYU004`), Batch Number, Manufacturing Date, Expiry Date, and Ingredients.
3. Upload a lab testing certificate (PDF, JPG, or PNG).
4. The system automatically calculates:
   - File SHA-256 hash
   - Combined Record SHA-256 hash
5. Click **Add Medicine & Record on Blockchain**.
6. MetaMask pops up asking to confirm the `addMedicine()` transaction on the smart contract.
7. Upon mining, the contract stores the tamper-evident hashes, and the exact block timestamp is recorded in MySQL.

### 2. Verifying a Medicine (Public Consumer / Retailer)
1. Go to **Verify Medicine** (`app/public/verify.php`).
2. Enter Medicine ID (e.g., `AYU001`) or Batch Number.
3. The system fetches the MySQL record and simultaneously queries the blockchain smart contract:
   - **Match Found:** Displays **✓ Blockchain Record Verified** with block number, transaction hash, and timestamp.
   - **Mismatch Found:** Displays **⚠ Verification Failed (Tamper Detected)**.
4. *Important Notice:* Verification guarantees that the digital records, certificates, and batch parameters have not been tampered with since creation.

### 3. QR Code Verification (Phase 9)
1. On any medicine details page (`app/public/view_medicine.php?id=AYU001`), click the **QR Code** button.
2. An interactive QR code is generated pointing to the public verification endpoint.
3. Any smartphone camera can scan the code to instantly verify the batch.

### 4. Supply Chain History Tracking
1. Go to **Blockchain Records** (`app/admin/history.php`).
2. View the chronological lifecycle updates (*Manufactured* -> *Dispatched* -> *Received* -> *Distributed* -> *Sold*).
3. Each transition is signed by MetaMask and permanently recorded on the blockchain with full provenance.

---

## 6. Project Directory Map

```text
AYURCHAIN/
├── app/
│   ├── admin/       # Dashboard, Add/Edit/Delete Medicine, History, Profile
│   ├── public/      # Landing page, Public Search, Verify, Details, About
│   ├── auth/        # Login & Logout
│   ├── api/         # JSON endpoints for Ethers.js transaction saving
│   ├── config/      # PDO database configuration
│   └── includes/    # Header, Footer, Auth guards, Utility functions
├── blockchain/
│   ├── contracts/   # Solidity Smart Contract
│   ├── js/          # Ethers.js integration & contract configuration
│   └── README.md    # Step-by-step blockchain deployment guide
├── assets/          # CSS themes, UI JavaScript, images & icons
├── database/        # database.sql schema & sample-data.sql seed data
├── storage/         # Secure storage for uploaded lab certificates
├── setup/           # One-click Windows setup scripts (BAT & PS1)
├── docs/            # Diploma IT project documentation & diagrams
└── tests/           # QA testing checklists & test cases
```

---
*Developed with PHP, MySQL, Solidity, Ethers.js, MetaMask, and Bootstrap 5.*

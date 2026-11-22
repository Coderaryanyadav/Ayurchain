# 🌿 AYURCHAIN – Simple Step-by-Step Guide

**Project:** Blockchain-Based Ayurvedic Medicine Storage and Verification System  
**GitHub Repository:** [https://github.com/Coderaryanyadav/Ayurchain](https://github.com/Coderaryanyadav/Ayurchain)  

---

## ⚡ Super Quick Start (In 3 Steps)

### 📌 Step 1: Start XAMPP & Import Database
1. Open **XAMPP Control Panel** on your computer.
2. Click **Start** button next to **Apache** and **MySQL** (both should turn green).
3. Open your browser and go to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
4. Click **New** on the left menu:
   - Database name: `ayurvedic_blockchain`
   - Click **Create**
5. Click on the **SQL** tab at the top.
6. Open the file [`database/database.sql`](database/database.sql), copy everything, paste into SQL box, and click **Go**.
7. Open the file [`database/sample-data.sql`](database/sample-data.sql), copy everything, paste into SQL box, and click **Go**.

---

### 📌 Step 2: Open the Website
Ensure this project folder is located in `C:\xampp\htdocs\ayurvedic-blockchain`  
*(or run `php -S localhost:8000` in the terminal inside this folder)*.

Open these URLs in your web browser:

| Page | URL | Purpose |
| :--- | :--- | :--- |
| 🏠 **Home Page** | `http://localhost/ayurvedic-blockchain/app/public/index.php` | Search medicines & see statistics |
| 🛡️ **Verify Portal** | `http://localhost/ayurvedic-blockchain/app/public/verify.php` | Test verification & tamper detection |
| 🔐 **Admin Login** | `http://localhost/ayurvedic-blockchain/app/auth/login.php` | Admin panel login |

---

### 📌 Step 3: Admin Login Credentials

- **Email:** `admin@ayurchain.org`
- **Password:** `admin123`

---

## 🎬 How to Show the Demo to Teachers / Evaluators

### 🧪 Demo 1: Show Public Blockchain Verification
1. Open `http://localhost/ayurvedic-blockchain/app/public/verify.php`
2. Click the green button: **"Demo Batch 1 (AYU001 - Ashwagandha)"**
3. **What happens:**  
   - Shows **✓ Blockchain Record Verified**
   - Shows Block #101 and Transaction Hash
   - Shows Lab Quality Certificate details and SHA-256 hash match!

---

### 🧪 Demo 2: Show Tamper Detection (Security Feature)
1. On the same verify page, click the red button: **"Demo Tampered Batch (Tamper Alert)"**
2. **What happens:**  
   - Instantly shows **⚠ Verification Failed (Tampering Detected!)**
   - Explains that someone tried to modify the record or certificate off-chain, and the blockchain cryptographic hash mismatch caught it!

---

### 🧪 Demo 3: Add a New Ayurvedic Medicine
1. Log into Admin Panel (`http://localhost/ayurvedic-blockchain/app/auth/login.php`).
2. Click **Add Medicine** on the top menu.
3. Click the yellow button: **"⚡ Fill Demo Data"** (auto-fills all fields like Name, Batch, Expiry, Ingredients).
4. Click **Save Medicine Record**.
5. Click **"⚡ Simulate Mining (Demo Mode)"** (or use MetaMask with Ganache).
6. **Result:** A new block is mined and the medicine is recorded permanently on the blockchain!

---

### 🧪 Demo 4: Show QR Code Verification
1. Go to any medicine details page (e.g. `app/public/view_medicine.php?id=AYU001`).
2. Click **"Generate QR Code"**.
3. A QR code opens on screen. Anyone can scan it with a mobile phone camera to verify the medicine batch instantly!

---

### 🧪 Demo 5: Show Supply Chain History
1. In the top navigation bar, click **Blockchain Records** (`app/admin/history.php`).
2. Select any medicine batch.
3. See every step in order:
   - **Manufactured** (Plant #4)
   - **Dispatched** (Central Logistics Hub, Delhi)
   - **Received** (Cold Storage, Mumbai)
4. Each step shows its own blockchain transaction hash and block number!

---

## ⚙️ Optional: Connecting Real MetaMask & Ganache

If you want to show live MetaMask popups:
1. Open **Ganache** (listening on `127.0.0.1:7545`).
2. In MetaMask, add a custom network:
   - RPC URL: `http://127.0.0.1:7545`
   - Chain ID: `1337` (or `5777`)
3. Deploy [`blockchain/contracts/AyurvedicMedicineVerification.sol`](blockchain/contracts/AyurvedicMedicineVerification.sol) in [Remix IDE](https://remix.ethereum.org).
4. Paste the deployed contract address into [`blockchain/js/contract-config.js`](blockchain/js/contract-config.js).

*(Note: The built-in Demo Simulator also works 100% offline without needing MetaMask or Ganache running).*

---

## ❓ Frequently Asked Questions (Troubleshooting)

**Q: Database connection error?**  
👉 Check if MySQL is running in XAMPP and make sure database name is `ayurvedic_blockchain`.

**Q: CSS or JS styles not loading?**  
👉 Make sure the project folder name inside `htdocs` is `ayurvedic-blockchain`.

**Q: How to reset all sample data?**  
👉 Re-import [`database/sample-data.sql`](database/sample-data.sql) in phpMyAdmin anytime.

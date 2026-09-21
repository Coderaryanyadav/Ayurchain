// ====================================================================
// Web3 Ethers.js & MetaMask Orchestrator Script (Updated Phase 12)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: blockchain/js/web3-contract.js
// ====================================================================

function isMetaMaskInstalled() {
    return typeof window.ethereum !== 'undefined';
}

async function connectMetaMaskWallet() {
    if (!isMetaMaskInstalled()) {
        throw new Error("MetaMask extension not found! Please install MetaMask to interact with Blockchain.");
    }
    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
    return accounts[0];
}

/**
 * Send Initial Medicine Verification Record to Smart Contract
 */
async function sendMedicineToBlockchain(medData) {
    if (!isMetaMaskInstalled()) throw new Error("MetaMask extension not detected.");

    const provider = new ethers.BrowserProvider(window.ethereum);
    const signer = await provider.getSigner();

    if (!CONTRACT_ADDRESS || CONTRACT_ADDRESS === "0x0000000000000000000000000000000000000000") {
        throw new Error("Smart contract address not configured in blockchain/js/contract-config.js!");
    }

    const contract = new ethers.Contract(CONTRACT_ADDRESS, CONTRACT_ABI, signer);

    const tx = await contract.addMedicine(
        medData.medicine_id,
        medData.medicine_name,
        medData.batch_number,
        medData.manufacturer,
        medData.certificate_hash,
        medData.record_hash
    );

    const receipt = await tx.wait();
    const block = await provider.getBlock(receipt.blockNumber);
    const timestamp = block ? block.timestamp : Math.floor(Date.now() / 1000);

    const response = await fetch('api/save_blockchain_record.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            medicine_id: medData.medicine_id,
            batch_number: medData.batch_number,
            certificate_hash: medData.certificate_hash,
            record_hash: medData.record_hash,
            transaction_hash: receipt.hash,
            block_number: receipt.blockNumber,
            timestamp: timestamp
        })
    });

    const result = await response.json();
    if (!result.success) {
        throw new Error("Mined on blockchain, but failed to save in MySQL: " + result.message);
    }

    return {
        transaction_hash: receipt.hash,
        block_number: receipt.blockNumber,
        timestamp: timestamp
    };
}

/**
 * Phase 12 Function: Send Supply Chain Status Update (Manufactured -> Dispatched -> Received -> Distributed -> Sold) to Smart Contract
 */
async function addStatusUpdateToBlockchain(medicineId, batchNumber, status, location) {
    if (!isMetaMaskInstalled()) throw new Error("MetaMask extension not detected.");

    const provider = new ethers.BrowserProvider(window.ethereum);
    const signer = await provider.getSigner();
    const contract = new ethers.Contract(CONTRACT_ADDRESS, CONTRACT_ABI, signer);

    // Call addMedicineHistory() on Smart Contract -> Opens MetaMask prompt!
    const tx = await contract.addMedicineHistory(
        medicineId,
        batchNumber,
        status,
        location
    );

    const receipt = await tx.wait(); // Wait for mining

    // Call PHP API to store status & txHash in MySQL medicine_history table
    const response = await fetch('api/add_history_record.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            medicine_id: medicineId,
            batch_number: batchNumber,
            status: status,
            location: location,
            transaction_hash: receipt.hash,
            block_number: receipt.blockNumber
        })
    });

    const result = await response.json();
    if (!result.success) {
        throw new Error("Status mined on blockchain, but MySQL save failed: " + result.message);
    }

    return {
        transaction_hash: receipt.hash,
        block_number: receipt.blockNumber
    };
}

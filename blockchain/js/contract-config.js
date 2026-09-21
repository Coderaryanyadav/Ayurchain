// ====================================================================
// Blockchain Smart Contract Configuration (Updated Phase 12)
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: blockchain/js/contract-config.js
// ====================================================================

const CONTRACT_ADDRESS = "0xd9145CCE52D386f254917e481eB44e9943F39138";

const CONTRACT_ABI = [
  {
    "inputs": [],
    "stateMutability": "nonpayable",
    "type": "constructor"
  },
  {
    "anonymous": false,
    "inputs": [
      { "indexed": true, "internalType": "string", "name": "medicineId", "type": "string" },
      { "indexed": true, "internalType": "string", "name": "batchNumber", "type": "string" },
      { "indexed": false, "internalType": "string", "name": "status", "type": "string" },
      { "indexed": false, "internalType": "string", "name": "location", "type": "string" },
      { "indexed": false, "internalType": "uint256", "name": "timestamp", "type": "uint256" },
      { "indexed": true, "internalType": "address", "name": "updatedBy", "type": "address" }
    ],
    "name": "HistoryAdded",
    "type": "event"
  },
  {
    "anonymous": false,
    "inputs": [
      { "indexed": true, "internalType": "string", "name": "medicineId", "type": "string" },
      { "indexed": true, "internalType": "string", "name": "batchNumber", "type": "string" },
      { "indexed": false, "internalType": "string", "name": "medicineName", "type": "string" },
      { "indexed": false, "internalType": "string", "name": "manufacturer", "type": "string" },
      { "indexed": false, "internalType": "string", "name": "certificateHash", "type": "string" },
      { "indexed": false, "internalType": "string", "name": "recordHash", "type": "string" },
      { "indexed": false, "internalType": "uint256", "name": "timestamp", "type": "uint256" },
      { "indexed": true, "internalType": "address", "name": "addedBy", "type": "address" }
    ],
    "name": "MedicineAdded",
    "type": "event"
  },
  {
    "inputs": [
      { "internalType": "string", "name": "_medicineId", "type": "string" },
      { "internalType": "string", "name": "_medicineName", "type": "string" },
      { "internalType": "string", "name": "_batchNumber", "type": "string" },
      { "internalType": "string", "name": "_manufacturer", "type": "string" },
      { "internalType": "string", "name": "_certificateHash", "type": "string" },
      { "internalType": "string", "name": "_recordHash", "type": "string" }
    ],
    "name": "addMedicine",
    "outputs": [],
    "stateMutability": "nonpayable",
    "type": "function"
  },
  {
    "inputs": [
      { "internalType": "string", "name": "_medicineId", "type": "string" },
      { "internalType": "string", "name": "_batchNumber", "type": "string" },
      { "internalType": "string", "name": "_status", "type": "string" },
      { "internalType": "string", "name": "_location", "type": "string" }
    ],
    "name": "addMedicineHistory",
    "outputs": [],
    "stateMutability": "nonpayable",
    "type": "function"
  },
  {
    "inputs": [
      { "internalType": "string", "name": "_medicineId", "type": "string" }
    ],
    "name": "getMedicine",
    "outputs": [
      { "internalType": "string", "name": "medicineId", "type": "string" },
      { "internalType": "string", "name": "medicineName", "type": "string" },
      { "internalType": "string", "name": "batchNumber", "type": "string" },
      { "internalType": "string", "name": "manufacturer", "type": "string" },
      { "internalType": "string", "name": "certificateHash", "type": "string" },
      { "internalType": "string", "name": "recordHash", "type": "string" },
      { "internalType": "uint256", "name": "timestamp", "type": "uint256" },
      { "internalType": "bool", "name": "exists", "type": "bool" }
    ],
    "stateMutability": "view",
    "type": "function"
  },
  {
    "inputs": [
      { "internalType": "string", "name": "_medicineId", "type": "string" }
    ],
    "name": "getMedicineHistory",
    "outputs": [
      {
        "components": [
          { "internalType": "string", "name": "medicineId", "type": "string" },
          { "internalType": "string", "name": "batchNumber", "type": "string" },
          { "internalType": "string", "name": "status", "type": "string" },
          { "internalType": "string", "name": "location", "type": "string" },
          { "internalType": "uint256", "name": "timestamp", "type": "uint256" },
          { "internalType": "address", "name": "updatedBy", "type": "address" }
        ],
        "internalType": "struct AyurvedicMedicineVerification.HistoryRecord[]",
        "name": "",
        "type": "tuple[]"
      }
    ],
    "stateMutability": "view",
    "type": "function"
  },
  {
    "inputs": [ { "internalType": "string", "name": "_medicineId", "type": "string" } ],
    "name": "medicineExists",
    "outputs": [ { "internalType": "bool", "name": "", "type": "bool" } ],
    "stateMutability": "view",
    "type": "function"
  },
  {
    "inputs": [
      { "internalType": "string", "name": "_medicineId", "type": "string" },
      { "internalType": "string", "name": "_certificateHash", "type": "string" },
      { "internalType": "string", "name": "_recordHash", "type": "string" }
    ],
    "name": "verifyMedicine",
    "outputs": [ { "internalType": "bool", "name": "isValid", "type": "bool" } ],
    "stateMutability": "view",
    "type": "function"
  }
];

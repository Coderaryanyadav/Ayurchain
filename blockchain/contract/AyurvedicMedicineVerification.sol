// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

/**
 * @title AyurvedicMedicineVerification
 * @dev Smart Contract for storing tamper-evident Ayurvedic medicine records, certificate hashes, and supply chain history.
 * Project: AyurChain - Ayurvedic Medicine Storage & Verification System
 */
contract AyurvedicMedicineVerification {

    address public owner;

    // Struct 1: Medicine Verification Parameters
    struct Medicine {
        string medicineId;
        string medicineName;
        string batchNumber;
        string manufacturer;
        string certificateHash;
        string recordHash;
        uint256 timestamp;
        bool exists;
    }

    // Struct 2: Supply Chain Lifecycle History Record
    struct HistoryRecord {
        string medicineId;
        string batchNumber;
        string status;      // "Manufactured", "Dispatched", "Received", "Distributed", "Sold"
        string location;    // e.g. "Haridwar Factory", "Delhi Depot"
        uint256 timestamp;
        address updatedBy;
    }

    // Mappings
    mapping(string => Medicine) private medicines;
    mapping(string => bool) private batchExists;
    mapping(string => HistoryRecord[]) private medicineHistories;

    // Events
    event MedicineAdded(
        string indexed medicineId,
        string indexed batchNumber,
        string medicineName,
        string manufacturer,
        string certificateHash,
        string recordHash,
        uint256 timestamp,
        address indexed addedBy
    );

    event HistoryAdded(
        string indexed medicineId,
        string indexed batchNumber,
        string status,
        string location,
        uint256 timestamp,
        address indexed updatedBy
    );

    modifier onlyOwner() {
        require(msg.sender == owner, "Only contract owner can invoke this operation.");
        _;
    }

    constructor() {
        owner = msg.sender;
    }

    /**
     * @dev Add a new medicine record to the blockchain.
     */
    function addMedicine(
        string memory _medicineId,
        string memory _medicineName,
        string memory _batchNumber,
        string memory _manufacturer,
        string memory _certificateHash,
        string memory _recordHash
    ) public {
        require(!medicines[_medicineId].exists, "Error: Medicine ID already registered on-chain!");
        require(!batchExists[_batchNumber], "Error: Batch Number already registered on-chain!");

        medicines[_medicineId] = Medicine({
            medicineId: _medicineId,
            medicineName: _medicineName,
            batchNumber: _batchNumber,
            manufacturer: _manufacturer,
            certificateHash: _certificateHash,
            recordHash: _recordHash,
            timestamp: block.timestamp,
            exists: true
        });

        batchExists[_batchNumber] = true;

        emit MedicineAdded(
            _medicineId,
            _batchNumber,
            _medicineName,
            _manufacturer,
            _certificateHash,
            _recordHash,
            block.timestamp,
            msg.sender
        );

        // Automatically log initial "Manufactured" status event
        medicineHistories[_medicineId].push(HistoryRecord({
            medicineId: _medicineId,
            batchNumber: _batchNumber,
            status: "Manufactured",
            location: _manufacturer,
            timestamp: block.timestamp,
            updatedBy: msg.sender
        }));

        emit HistoryAdded(_medicineId, _batchNumber, "Manufactured", _manufacturer, block.timestamp, msg.sender);
    }

    /**
     * @dev Function to append supply chain status updates to blockchain history.
     */
    function addMedicineHistory(
        string memory _medicineId,
        string memory _batchNumber,
        string memory _status,
        string memory _location
    ) public {
        require(medicines[_medicineId].exists, "Error: Medicine ID does not exist on-chain!");

        HistoryRecord memory newHist = HistoryRecord({
            medicineId: _medicineId,
            batchNumber: _batchNumber,
            status: _status,
            location: _location,
            timestamp: block.timestamp,
            updatedBy: msg.sender
        });

        medicineHistories[_medicineId].push(newHist);

        emit HistoryAdded(_medicineId, _batchNumber, _status, _location, block.timestamp, msg.sender);
    }

    /**
     * @dev Function to retrieve full supply chain history array for a medicine from blockchain.
     */
    function getMedicineHistory(string memory _medicineId) public view returns (HistoryRecord[] memory) {
        require(medicines[_medicineId].exists, "Error: Medicine ID does not exist on-chain!");
        return medicineHistories[_medicineId];
    }

    /**
     * @dev Fetch medicine record details.
     */
    function getMedicine(string memory _medicineId) public view returns (
        string memory medicineId,
        string memory medicineName,
        string memory batchNumber,
        string memory manufacturer,
        string memory certificateHash,
        string memory recordHash,
        uint256 timestamp,
        bool exists
    ) {
        require(medicines[_medicineId].exists, "Error: Medicine ID does not exist on-chain!");
        Medicine memory med = medicines[_medicineId];
        return (
            med.medicineId,
            med.medicineName,
            med.batchNumber,
            med.manufacturer,
            med.certificateHash,
            med.recordHash,
            med.timestamp,
            med.exists
        );
    }

    /**
     * @dev Check if medicine exists.
     */
    function medicineExists(string memory _medicineId) public view returns (bool) {
        return medicines[_medicineId].exists;
    }

    /**
     * @dev Cryptographic verification check.
     */
    function verifyMedicine(
        string memory _medicineId,
        string memory _certificateHash,
        string memory _recordHash
    ) public view returns (bool isValid) {
        if (!medicines[_medicineId].exists) {
            return false;
        }

        Medicine memory med = medicines[_medicineId];
        bool certMatches = keccak256(bytes(med.certificateHash)) == keccak256(bytes(_certificateHash));
        bool recordMatches = keccak256(bytes(med.recordHash)) == keccak256(bytes(_recordHash));

        return (certMatches && recordMatches);
    }
}

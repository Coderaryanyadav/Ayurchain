<?php
// ====================================================================
// About Project Page
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: about.php
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-4">
    
    <!-- Header Banner -->
    <div class="card card-ayur p-4 mb-4 border-start border-5 border-success bg-white shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-success mb-2">
                    <i class="fa-solid fa-circle-info me-2"></i>About AyurChain Project
                </h2>
                <p class="text-muted leading-normal mb-0">
                    <strong>Blockchain-Based Ayurvedic Medicine Storage and Verification System</strong> is a college-level IT diploma project designed to solve trust, supply chain counterfeiting, and lab certificate tampering challenges in traditional Indian herbal medicines.
                </p>
            </div>
            <div class="col-md-4 text-center mt-3 mt-md-0">
                <i class="fa-solid fa-seedling fa-5x text-warning opacity-75"></i>
            </div>
        </div>
    </div>

    <!-- Core Objectives & Architecture Breakdown -->
    <div class="row g-4 mb-4">
        
        <!-- Column 1: The Problem -->
        <div class="col-md-6">
            <div class="card card-ayur h-100 p-4">
                <h4 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>The Problem in Ayurvedic Supply Chain
                </h4>
                <ul class="text-muted leading-relaxed mb-0">
                    <li class="mb-2"><strong>Counterfeiting & Adulteration:</strong> High demand for authentic Ayurvedic botanicals often leads to fake or diluted herb powders.</li>
                    <li class="mb-2"><strong>Lab Report Tampering:</strong> PDF certificates of quality and heavy metal purity reports can be easily edited or falsified.</li>
                    <li class="mb-2"><strong>Lack of Batch Traceability:</strong> Consumers have no easy way to check if a batch number printed on a bottle matches genuine manufacturer test records.</li>
                </ul>
            </div>
        </div>

        <!-- Column 2: The Solution -->
        <div class="col-md-6">
            <div class="card card-ayur h-100 p-4 border-start border-4 border-success">
                <h4 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-shield-cat text-success me-2"></i>The AyurChain Solution
                </h4>
                <ul class="text-muted leading-relaxed mb-0">
                    <li class="mb-2"><strong>Hybrid Web2 + Web3 Storage:</strong> MySQL stores detailed textual metadata, while Ethereum blockchain stores immutable SHA-256 hashes.</li>
                    <li class="mb-2"><strong>Client-Side Hashing:</strong> PDF lab certificates are hashed directly in the browser via Web Crypto API before transaction signature.</li>
                    <li class="mb-2"><strong>Instant Verification:</strong> Public users can verify any medicine batch or upload a certificate file for zero-gas read-only validation.</li>
                </ul>
            </div>
        </div>

    </div>

    <!-- Technology Stack Grid -->
    <div class="card card-ayur p-4 mb-4">
        <h4 class="fw-bold text-dark mb-4 text-center"><i class="fa-solid fa-layer-group text-warning me-2"></i>Technology Stack</h4>
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded border">
                    <i class="fa-brands fa-html5 fa-2x text-danger mb-2"></i>
                    <h6 class="fw-bold mb-1">Frontend</h6>
                    <small class="text-muted">HTML5, Bootstrap 5, JavaScript</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded border">
                    <i class="fa-brands fa-php fa-2x text-primary mb-2"></i>
                    <h6 class="fw-bold mb-1">Backend</h6>
                    <small class="text-muted">PHP (PDO Driver)</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded border">
                    <i class="fa-solid fa-database fa-2x text-success mb-2"></i>
                    <h6 class="fw-bold mb-1">Off-Chain DB</h6>
                    <small class="text-muted">MySQL / MariaDB</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded border">
                    <i class="fa-solid fa-cubes fa-2x text-warning mb-2"></i>
                    <h6 class="fw-bold mb-1">Blockchain</h6>
                    <small class="text-muted">Solidity, Ganache, Ethers.js</small>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

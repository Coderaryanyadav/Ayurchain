<?php
// ====================================================================
// Footer Include with Ethers.js & Web3 Scripts
// Project: AyurChain - Ayurvedic Medicine Storage & Verification System
// File: includes/footer.php
// ====================================================================
?>
</div> <!-- End main-wrapper -->

<footer class="ayur-footer mt-5">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-md-5">
        <h5 class="d-flex align-items-center mb-3">
          <i class="fa-solid fa-leaf text-warning me-2"></i> AyurChain Verification System
        </h5>
        <p class="small opacity-85">
          A tamper-evident Ayurvedic medicine record storage and verification platform. Combining traditional herbal quality standardizations with Ethereum blockchain cryptographic immutability.
        </p>
      </div>
      <div class="col-md-3 ms-auto">
        <h5 class="mb-3">Quick Links</h5>
        <ul class="list-unstyled small mb-0">
          <li class="mb-2"><a href="index.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Home</a></li>
          <li class="mb-2"><a href="verify.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Verify Medicine Batch</a></li>
          <li class="mb-2"><a href="medicines.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Medicine Directory</a></li>
          <li class="mb-2"><a href="history.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Blockchain History</a></li>
          <li class="mb-0"><a href="about.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> About System</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h5 class="mb-3">Tech Stack</h5>
        <ul class="list-unstyled small mb-0">
          <li class="mb-1"><span class="badge bg-secondary me-1">PHP</span> Backend Engine</li>
          <li class="mb-1"><span class="badge bg-secondary me-1">MySQL</span> Off-Chain DB</li>
          <li class="mb-1"><span class="badge bg-warning text-dark me-1">Solidity</span> Smart Contract</li>
          <li class="mb-1"><span class="badge bg-success me-1">Ganache</span> Local Testnet</li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary my-3 opacity-25">
    <div class="row align-items-center small opacity-75">
      <div class="col-md-6 text-center text-md-start">
        &copy; <?php echo date('Y'); ?> <strong>AyurChain</strong> - Diploma IT Project. All Rights Reserved.
      </div>
      <div class="col-md-6 text-center text-md-end">
        <span class="text-warning"><i class="fa-solid fa-shield-cat me-1"></i> Blockchain Tamper-Evident Security</span>
      </div>
    </div>
  </div>
</footer>

<!-- Bootstrap 5 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
<!-- Ethers.js v6 CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js"></script>
<!-- Contract Config & Web3 Scripts -->
<script src="blockchain/js/contract-config.js"></script>
<script src="blockchain/js/web3-contract.js"></script>
<!-- Custom Application JS -->
<script src="js/script.js"></script>

</body>
</html>

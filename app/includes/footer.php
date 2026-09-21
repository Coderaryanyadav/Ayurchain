<?php
// ====================================================================
// Footer Include (Phase 2 & Phase 9 QR Integration)
// Project: AYURCHAIN
// File: app/includes/footer.php
// ====================================================================

$base_url = get_base_url();
?>
</div> <!-- End main-wrapper -->

<footer class="ayur-footer mt-5">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-md-5">
        <h5 class="d-flex align-items-center mb-3">
          <i class="fa-solid fa-seedling text-warning me-2"></i> AYURCHAIN System
        </h5>
        <p class="small opacity-85">
          Blockchain-Based Ayurvedic Medicine Storage and Verification System. Combining traditional herbal quality standardizations with Ethereum blockchain cryptographic immutability.
        </p>
      </div>
      <div class="col-md-3 ms-auto">
        <h5 class="mb-3">Quick Navigation</h5>
        <ul class="list-unstyled small mb-0">
          <li class="mb-2"><a href="<?php echo $base_url; ?>app/public/index.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Home</a></li>
          <li class="mb-2"><a href="<?php echo $base_url; ?>app/public/verify.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Verify Medicine Batch</a></li>
          <li class="mb-2"><a href="<?php echo $base_url; ?>app/admin/medicines.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Medicine Directory</a></li>
          <li class="mb-2"><a href="<?php echo $base_url; ?>app/admin/history.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> Blockchain History</a></li>
          <li class="mb-0"><a href="<?php echo $base_url; ?>app/public/about.php"><i class="fa-solid fa-chevron-right text-warning me-1 small"></i> About System</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h5 class="mb-3">Technology Stack</h5>
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
        &copy; <?php echo date('Y'); ?> <strong>AYURCHAIN</strong> - Diploma IT Project. All Rights Reserved.
      </div>
      <div class="col-md-6 text-center text-md-end">
        <span class="text-warning"><i class="fa-solid fa-shield-cat me-1"></i> Blockchain Tamper-Evident Security</span>
      </div>
    </div>
  </div>
</footer>

<script>
  window.BASE_URL = "<?php echo $base_url; ?>";
</script>
<!-- Bootstrap 5 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
<!-- Ethers.js v6 CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js"></script>
<!-- QRCode.js CDN for Phase 9 QR Verification -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<!-- Contract Config & Web3 Scripts -->
<script src="<?php echo $base_url; ?>blockchain/js/contract-config.js"></script>
<script src="<?php echo $base_url; ?>blockchain/js/web3-contract.js"></script>
<!-- Custom Application JS -->
<script src="<?php echo $base_url; ?>assets/js/script.js"></script>

</body>
</html>

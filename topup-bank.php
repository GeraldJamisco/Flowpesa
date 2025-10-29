<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Flowpesa — Top-up by Bank</title>

  <link rel="icon" href="assets/flowpesa-icon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script defer src="js/topup-bank.js"></script>
</head>
<body>
  <div class="stage">
    <div class="title">Bank Transfer</div>

    <div class="phone">
      <!-- Header -->
      <div class="hero">
        <div class="hello"><a href="dashboard.php" class="auth-link"><i class="bi bi-chevron-left"></i> Back</a></div>
        <div class="name">Add money via bank</div>
        <div class="hero-row">
          <div>
            <div class="text-secondary small">Wallet balance</div>
            <div class="balance" id="balance">UGX 0.00</div>
          </div>
          <div class="coins"><span class="dot"></span><span id="points">0</span></div>
        </div>
      </div>

      <!-- Body -->
      <div class="body">
        <!-- Flowpesa bank details (read-only) -->
        <div class="section-card">
          <div class="section-title"><b>Transfer to Flowpesa account</b></div>
          <div class="list">
            <div class="rowi">
              <div><i class="bi bi-bank"></i>Bank</div>
              <div class="text-end">DFCU Bank</div>
            </div>
            <div class="rowi">
              <div><i class="bi bi-person-badge"></i>Account name</div>
              <div class="text-end">Flowpesa Ltd</div>
            </div>
            <div class="rowi">
              <div><i class="bi bi-hash"></i>Account number</div>
              <div class="text-end" id="fpAcct">0123456789</div>
            </div>
            <div class="rowi">
              <div><i class="bi bi-geo"></i>Branch</div>
              <div class="text-end">Kampala Main</div>
            </div>
          </div>
          <div class="mutelink" style="margin-top:8px">Make a bank transfer to the details above, then submit your deposit info below so we can match it faster.</div>
        </div>

        <!-- Your transfer details -->
        <div class="section-card">
          <div class="section-title"><b>Your transfer</b></div>

          <label class="label" for="amount">Amount (UGX)</label>
          <input id="amount" class="input" inputmode="numeric" placeholder="e.g. 200,000">

          <div class="grid-2" style="margin-top:10px">
            <div>
              <label class="label" for="bankName">Your bank</label>
              <input id="bankName" class="input" placeholder="e.g. Stanbic">
            </div>
            <div>
              <label class="label" for="txnRef">Bank reference</label>
              <input id="txnRef" class="input" placeholder="e.g. TT123456">
            </div>
          </div>

          <label class="label" for="note" style="margin-top:10px">Note (optional)</label>
          <input id="note" class="input" placeholder="e.g. July savings">

          <label class="label" for="proof" style="margin-top:10px">Upload proof (optional)</label>
          <input id="proof" class="input" type="file" accept="image/*,.pdf">
        </div>

        <div class="section-card" style="display:flex;gap:10px;align-items:center;justify-content:space-between">
          <div class="text-secondary small" id="summary">Fill your transfer details.</div>
          <button id="submit" class="auth-primary" type="button" disabled>Submit</button>
        </div>

        <div class="empty" id="hint" style="display:none">
          <i class="bi bi-info-circle"></i>
          We’ll review your transfer and credit your wallet once it’s received. Typical time: 1–2 business days.
        </div>
      </div>

      <!-- Bottom nav -->
      <nav class="nav">
        <a href="dashboard.php"><i class="bi bi-house"></i>Home</a>
        <a href="#"><i class="bi bi-receipt"></i>Bills</a>
        <a href="wallet.php"><i class="bi bi-wallet2"></i>Wallet</a>
        <a href="#"><i class="bi bi-person"></i>Profile</a>
      </nav>
    </div>
  </div>

  <div class="toast" id="toast"></div>
</body>
</html>

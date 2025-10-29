<?php
// If you already have bootstrap.php that starts the session, require it here.
// require __DIR__ . '/bootstrap.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Flowpesa — Add Money</title>

  <link rel="icon" href="assets/flowpesa-icon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script defer src="js/topup.js"></script>
</head>
<body>
  <div class="stage">
    <div class="title">Add Money</div>

    <div class="phone">
      <!-- Header (shared hero style) -->
      <div class="hero">
        <div class="hello"><a href="dashboard.php" class="auth-link"><i class="bi bi-chevron-left"></i> Back</a></div>
        <div class="name">Wallet Balance</div>
        <div class="hero-row">
          <div>
            <div class="text-secondary small">Current</div>
            <div class="balance" id="balance">UGX 0.00</div>
          </div>
          <div class="coins"><span class="dot"></span><span id="points">0</span></div>
        </div>
      </div>

      <!-- Sheet body -->
      <div class="body body--sheet show" id="sheet">
        <!-- Amount -->
        <div class="section-card">
          <label class="label" for="amount">Amount (UGX)</label>
          <input id="amount" class="input" inputmode="numeric" placeholder="e.g. 50,000" autocomplete="off">
          <div class="grid" style="margin-top:10px">
            <button class="a-btn" data-quick="10000">+10k</button>
            <button class="a-btn" data-quick="20000">+20k</button>
            <button class="a-btn" data-quick="50000">+50k</button>
            <button class="a-btn" data-quick="100000">+100k</button>
          </div>
        </div>

        <!-- Payment method -->
        <div class="section-card">
          <div class="section-title"><b>Select method</b><span class="text-secondary small">MTN • Airtel • Bank</span></div>

          <button class="method-card" data-method="mtn">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-sim"></i></div>
              <div>
                <div class="method-title">MTN Mobile Money</div>
                <div class="method-sub">Instant • fees may apply</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </button>

          <button class="method-card" data-method="airtel">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-sim"></i></div>
              <div>
                <div class="method-title">Airtel Money</div>
                <div class="method-sub">Instant • fees may apply</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </button>

          <button class="method-card" data-method="bank">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-bank"></i></div>
              <div>
                <div class="method-title">Bank Transfer</div>
                <div class="method-sub">1–2 days • no MoMo fees</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>

        <!-- Reference / note -->
        <div class="section-card">
          <label class="label" for="ref">Reference (optional)</label>
          <input id="ref" class="input" placeholder="e.g. Salary top-up, pocket money">
        </div>

        <!-- Confirm -->
        <div class="section-card" style="display:flex;gap:10px;align-items:center;justify-content:space-between">
          <div class="text-secondary small" id="summary">Select a method and enter amount.</div>
          <button id="confirm" class="auth-primary" type="button" disabled>Confirm</button>
        </div>

        <div class="empty" id="hint" style="display:none">
          <i class="bi bi-info-circle"></i>
          You’ll receive a prompt on your phone (USSD/STK Push) to approve the payment.
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

  <!-- Toast -->
  <div class="toast" id="toast"></div>
</body>
</html>

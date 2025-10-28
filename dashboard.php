<?php require __DIR__.'/require_login.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Flowpesa — Dashboard</title>

  <!-- Bootstrap (utilities you used) + Icons -->
   <link rel="icon" href="assets/flowpesa-icon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Fonts + Unified app CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">

</head>
<body>
  <div class="stage">
    <div class="title">Home</div>

    <!-- Use the shared phone layout; our unified CSS already makes it a flex column -->
<div class="phone phone--withnav">


      <!-- Header -->
      <div class="hero hero--card">
        <div class="hero-row">
          <div>
            <div class="hello">Welcome back!</div>
            <div class="name" id="name">—</div>
          </div>
          <div class="coins"><span class="dot"></span><span id="points">9,500</span></div>
        </div>

        <div class="hero-balance">
          <div class="text-secondary small">Balance</div>
          <div class="balance" id="balance">UGX 895,500.00</div>
        </div>

        <div class="hero-cta">
          <button class="btn-hero"><i class="bi bi-plus-circle"></i><span>Add Money</span></button>
          <button class="btn-hero btn-hero--outline"><i class="bi bi-arrow-up-right-circle"></i><span>Withdraw</span></button>
        </div>
      <!-- Top Up sheet (hidden by default) -->
<div class="body body--sheet" id="topup-body" style="display:none">
  <div class="section-title mb-2"><b>Top Up Method</b></div>

  <a class="method-card" href="#" data-method="bank">
    <div class="method-left">
      <div class="method-ico"><i class="bi bi-bank"></i></div>
      <div>
        <div class="method-title">Bank Transfer</div>
        <div class="method-sub">Top up balance via bank transfer</div>
      </div>
    </div>
    <i class="bi bi-chevron-right"></i>
  </a>

  <a class="method-card" href="#" data-method="agent">
    <div class="method-left">
      <div class="method-ico"><i class="bi bi-shop"></i></div>
      <div>
        <div class="method-title">Agent / Flowpesa</div>
        <div class="method-sub">Top up via nearby agent</div>
      </div>
    </div>
    <i class="bi bi-chevron-right"></i>
  </a>

  <a class="method-card" href="#" data-method="virtual">
    <div class="method-left">
      <div class="method-ico"><i class="bi bi-credit-card-2-front"></i></div>
      <div>
        <div class="method-title">Virtual Account</div>
        <div class="method-sub">Confirm automatically within 24 hours</div>
      </div>
    </div>
    <i class="bi bi-chevron-right"></i>
  </a>

  <button class="btn btn-outline-dark w-100 mt-3" id="closeTopup">
    <i class="bi bi-arrow-left"></i> Back to Dashboard
  </button>
</div>

<div class="quick-actions mt-3">
  <button class="a-btn" href="wallet.php" role="button" aria-label="Open Wallet">
    <i class="bi bi-wallet2"></i>Wallet
  </button>
  <button class="a-btn a-btn--primary" type="button" id="openTopup" aria-label="Transfer">
    <i class="bi bi-arrow-left-right"></i>Transfer
  </button>
  <button class="a-btn" type="button" aria-label="Bills"><i class="bi bi-receipt"></i>Bills</button>
  <button class="a-btn" type="button" aria-label="Scan"><i class="bi bi-qr-code-scan"></i>Scan</button>
</div>



      <!-- Body -->
      <div class="body">
        <div class="section">
          <div class="section-head">Services</div>
          <div class="services-block">
          <div class="grid" aria-label="services">
           <div class="svc"><i class="bi bi-phone"></i>Pulse</div>
           <div class="svc"><i class="bi bi-wifi"></i>Internet</div>
           <div class="svc"><i class="bi bi-telephone"></i>Call packages</div>
           <div class="svc"><i class="bi bi-droplet"></i>Water</div>
           <div class="svc"><i class="bi bi-lightning-charge"></i>Electricity</div>
           <div class="svc"><i class="bi bi-credit-card"></i>Insurance</div>
           <div class="svc"><i class="bi bi-controller"></i>Game</div>
           <div class="svc"><i class="bi bi-grid"></i>More</div>
          </div>
          </div>
        </div>

        <div class="section">
          <div class="section-head d-flex justify-content-between align-items-center">
            <span>Activity</span>
            <a href="#" class="text-secondary small">See all →</a>
          </div>
          <div class="history">
            <div class="item">
              <div class="d-flex align-items-center gap-2">
                <span class="badge-success">✓</span>
                <div>
                  <div class="fw-bold small">UGX120,000 received</div>
                  <div class="text-muted" style="font-size:11px">2:20 PM • Jun 22, 2025</div>
                </div>
              </div>
              <div class="fw-bold text-success">+UGX120,000</div>
            </div>
            <div class="item">
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark rounded-circle p-1">!</span>
                <div>
                  <div class="fw-bold small">UGX56,000 sent</div>
                  <div class="text-muted" style="font-size:11px">2:20 PM • Jun 22, 2025</div>
                </div>
              </div>
              <div class="fw-bold text-danger">-UGX56,000</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom nav (sticky via unified CSS .nav) -->
      <nav class="nav">
        <a href="dashboard.php" class="active"><i class="bi bi-house"></i>Home</a>
        <a href="#"><i class="bi bi-receipt"></i>Bills</a>
        <a href="wallet.php"><i class="bi bi-wallet2"></i>Wallet</a>
        <a href="profile.php" ><i class="bi bi-person-fill"></i>Profile</a>
      </nav>
    </div>
  </div>
  <script src="js/api.js"></script>
  <script src="js/anim.js"></script>
  <script src="js/dashboard.js"></script>
  
</body>
</html>

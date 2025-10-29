<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Flowpesa — Top-up via Mobile Money</title>

  <link rel="icon" href="assets/flowpesa-icon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script defer src="js/topup-momo.js"></script>
</head>
<body>
  <div class="stage">
    <div class="title">Mobile Money</div>

    <div class="phone">
      <!-- Header -->
      <div class="hero">
        <div class="hello"><a href="dashboard.php" class="auth-link"><i class="bi bi-chevron-left"></i> Back</a></div>
        <div class="name">Top-up with MTN / Airtel</div>
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
        <!-- Amount -->
        <div class="section-card">
          <label class="label" for="amount">Amount (UGX)</label>
          <input id="amount" class="input" inputmode="numeric" placeholder="e.g. 20,000" autocomplete="off">
          <div class="grid" style="margin-top:10px">
            <button class="a-btn" data-quick="10000">+10k</button>
            <button class="a-btn" data-quick="20000">+20k</button>
            <button class="a-btn" data-quick="50000">+50k</button>
            <button class="a-btn" data-quick="100000">+100k</button>
          </div>
        </div>

        <!-- Network + phone -->
        <div class="section-card">
          <div class="section-title"><b>Choose network</b><span class="text-secondary small">Instant top-up</span></div>

          <div class="grid">
            <button type="button" class="method-card" data-network="mtn" id="btnMTN">
              <div class="method-left">
                <div class="method-ico"><i class="bi bi-sim"></i></div>
                <div>
                  <div class="method-title">MTN Mobile Money</div>
                  <div class="method-sub">STK Push / Prompt</div>
                </div>
              </div>
              <i class="bi bi-check2-circle d-none" id="tickMTN"></i>
            </button>

            <button type="button" class="method-card" data-network="airtel" id="btnAirtel">
              <div class="method-left">
                <div class="method-ico"><i class="bi bi-sim"></i></div>
                <div>
                  <div class="method-title">Airtel Money</div>
                  <div class="method-sub">STK Push / Prompt</div>
                </div>
              </div>
              <i class="bi bi-check2-circle d-none" id="tickAirtel"></i>
            </button>
          </div>

          <div class="grid" style="margin-top:10px">
            <div>
              <label class="label" for="msisdn">Mobile number</label>
              <input id="msisdn" class="input" inputmode="tel" placeholder="+256 700 000000" autocomplete="tel">
            </div>
            <div>
              <label class="label" for="ref">Reference (optional)</label>
              <input id="ref" class="input" placeholder="e.g. wallet top-up">
            </div>
          </div>
        </div>

        <!-- Summary + confirm -->
        <div class="section-card" style="display:flex;gap:10px;align-items:center;justify-content:space-between">
          <div class="text-secondary small" id="summary">Enter amount, select network and phone.</div>
          <button id="confirm" class="auth-primary" type="button" disabled>Confirm</button>
        </div>

        <div class="empty" id="hint" style="display:none">
          <i class="bi bi-info-circle"></i>
          You’ll receive a prompt on your phone to approve the Mobile Money payment.
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

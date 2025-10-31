<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Flowpesa — Transfer</title>

  <link rel="icon" href="assets/flowpesa-icon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script defer src="js/transfer.js"></script>
</head>
<body>
  <div class="stage">
    <div class="title">Transfer</div>

    <div class="phone">

      <!-- Header -->
      <div class="hero">
        <div class="hello"><a href="dashboard.php" class="auth-link"><i class="bi bi-chevron-left"></i> Back</a></div>
        <div class="name">Send money</div>
        <div class="hero-row">
          <div>
            <div class="text-secondary small">Wallet balance</div>
            <div class="balance" id="balance">UGX 0.00</div>
          </div>
          <div class="coins">
            <span class="dot"></span><span id="points">0</span>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="body">

        <!-- Quick Send (handle / phone) -->
        <div class="section-card">
          <div class="section-title"><b>Quick send</b><span class="text-secondary small">Flowpesa handle or phone</span></div>
          <div class="grid">
            <div>
              <label class="label" for="quickTo">@handle or phone</label>
              <input id="quickTo" class="input" placeholder="@janedoe or +256 700 000000" autocomplete="off">
            </div>
            <div>
              <label class="label" for="quickAmt">Amount (UGX)</label>
              <input id="quickAmt" class="input" inputmode="numeric" placeholder="20,000">
            </div>
          </div>
          <div style="display:flex;gap:10px;margin-top:10px">
            <button id="quickSend" class="auth-primary" type="button">Send</button>
            <button id="scanBtn" class="a-btn" type="button"><i class="bi bi-qr-code-scan"></i> Scan</button>
          </div>
        </div>

        <!-- Methods -->
        <div class="section-card">
          <div class="section-title"><b>Choose method</b><span class="text-secondary small">Different rails</span></div>

          <a class="method-card" href="transfer-bank.php">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-bank"></i></div>
              <div>
                <div class="method-title">To Bank Account</div>
                <div class="method-sub">Local/International account transfer</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </a>

          <a class="method-card" href="transfer-card.php">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-credit-card-2-front"></i></div>
              <div>
                <div class="method-title">To Bank Card</div>
                <div class="method-sub">Card number transfer</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </a>

          <a class="method-card" href="transfer-mobile.php">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-sim"></i></div>
              <div>
                <div class="method-title">To Mobile Number</div>
                <div class="method-sub">MTN/Airtel Mobile Money</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </a>

          <a class="method-card" href="transfer-contact.php">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-person-lines-fill"></i></div>
              <div>
                <div class="method-title">To a Contact</div>
                <div class="method-sub">Pick from phonebook</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </a>

          <a class="method-card" href="transfer-handle.php">
            <div class="method-left">
              <div class="method-ico"><i class="bi bi-at"></i></div>
              <div>
                <div class="method-title">To Flowpesa @tag</div>
                <div class="method-sub">Send to any Flowpesa user</div>
              </div>
            </div>
            <i class="bi bi-chevron-right"></i>
          </a>
        </div>

        <!-- Recent recipients -->
        <div class="section-card">
          <div class="section-title"><b>Recent</b><a href="#" class="text-secondary small" id="viewAll">See all →</a></div>
          <div id="recentList" class="list">
            <!-- JS injects last 5 recipients -->
          </div>
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

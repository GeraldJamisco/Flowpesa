<?php
// verify-phone.php
session_start();

// 1) Make sure user came from register.php
if (!isset($_SESSION['pending_msisdn'], $_SESSION['pending_otp'])) {
    header('Location: register.php');
    exit;
}

$msisdn    = (string)$_SESSION['pending_msisdn'];
$savedOtp  = (string)$_SESSION['pending_otp'];
$errorMsg  = '';

// simple mask: +256 78•• ••12
function mask_msisdn(string $msisdn): string {
    // remove spaces just for masking logic
    $plain = preg_replace('/\s+/', '', $msisdn);
    if (!preg_match('/^(\+\d{1,3})(\d+)$/' , $plain, $m)) {
        return $msisdn; // fallback
    }
    $cc   = $m[1];
    $rest = $m[2];

    // keep first 2 and last 2 digits
    if (strlen($rest) <= 4) return $cc.' '.$rest;
    $first = substr($rest, 0, 2);
    $last  = substr($rest, -2);
    return sprintf('%s %s•• ••%s', $cc, $first, $last);
}

// 2) Handle POST (form submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $digits = $_POST['otp'] ?? [];

    if (is_array($digits)) {
        $code = implode('', array_map('trim', $digits));
    } else {
        $code = trim((string)$digits);
    }

    if (!preg_match('/^\d{6}$/', $code)) {
        $errorMsg = 'Enter the 6-digit code we sent you.';
    } elseif ($code !== $savedOtp) {
        $errorMsg = 'The code is incorrect or has expired.';
    } else {
        // ✅ OTP correct
        $_SESSION['phone_verified'] = true;

        // Optional: clear OTP
        // unset($_SESSION['pending_otp'], $_SESSION['otp_created_at']);

        header('Location: verify-country.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Flowpesa — Verify phone</title>
  <link rel="stylesheet" href="css/vars.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/auth-override.css" />
</head>
<body class="theme-dark">
  <header class="top-bar">
    <div class="top">
      <button class="icon-btn" type="button" onclick="history.back()" aria-label="Go back">
        <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15 18L9 12l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="top-title">Sign Up</h1>  
      <span class="top-ghost" aria-hidden="true"></span>
    </div>
  </header>

  <main class="screen">
    <section class="intro">
      <h2 class="heading">6-digit code</h2>
      <p class="subheading" id="otp-help">
        Code sent to
        <span id="msisdn">
          <?= htmlspecialchars(mask_msisdn($msisdn), ENT_QUOTES) ?>
        </span>
        unless you already have an account
      </p>
    </section>

    <form class="card" id="otp-form" method="post" novalidate>
      <div class="digit-code-input" role="group" aria-label="6-digit code">
        <?php for ($i = 0; $i < 6; $i++): ?>
          <input
            class="otp-input"
            type="text"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="1"
            name="otp[]"
            aria-label="Digit <?= $i+1 ?>"
          >
        <?php endfor; ?>
      </div>

      <?php if ($errorMsg): ?>
        <p class="error" id="otp-error" role="alert">
          <?= htmlspecialchars($errorMsg, ENT_QUOTES) ?>
        </p>
      <?php else: ?>
        <p class="error" id="otp-error" role="alert" hidden></p>
      <?php endif; ?>

      <div class="stack-under">
        <p class="subheading">
          <span id="resend-text">Resend code in 00:30</span>
        </p>
        <p class="subheading">
          Already have an account? <a class="link" href="/login">Log in</a>
        </p>
      </div>

      <button id="verify-btn" class="regular-button" type="submit" disabled>
        <span class="button-label">Continue</span>
      </button>
    </form>
  </main>

  <script>
    // NOTE: msisdn now comes from PHP, not from URL
    const inputs = Array.from(document.querySelectorAll('.otp-input'));
    const btn = document.getElementById('verify-btn');
    const err = document.getElementById('otp-error');

    if (inputs.length) inputs[0].focus();

    function check() {
      const code = inputs.map(i => i.value).join('');
      const ok = code.length === inputs.length;
      btn.disabled = !ok;
      btn.classList.toggle('is-active', ok);
      if (!ok && !err.textContent) err.hidden = true;  // don't hide PHP error
      return ok;
    }

    inputs.forEach((inp, i) => {
      inp.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g,'').slice(0,1);
        if (e.target.value && i < inputs.length - 1) inputs[i+1].focus();
        check();
      });
      inp.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !inp.value && i > 0) {
          inputs[i-1].value = '';
          inputs[i-1].focus();
          e.preventDefault();
        }
      });
      inp.addEventListener('paste', e => {
        const t = (e.clipboardData || window.clipboardData)
          .getData('text')
          .replace(/\D/g,'')
          .slice(0,6);
        if (!t) return;
        e.preventDefault();
        inputs.forEach((x,j)=> x.value = t[j] || '');
        inputs[Math.min(t.length,6)-1].focus();
        check();
      });
    });

    // ❌ NO preventDefault here → PHP can actually receive the form
    document.getElementById('otp-form').addEventListener('submit', e => {
      if (!check()) {
        e.preventDefault();
      }
    });

    // simple resend countdown
    const resendText = document.getElementById('resend-text');
    let s = 30;
    const timer = setInterval(() => {
      s -= 1;
      if (!resendText) { clearInterval(timer); return; }
      resendText.textContent = s > 0
        ? `Resend code in 00:${String(s).padStart(2,'0')}`
        : 'Resend code';
      if (s <= 0) clearInterval(timer);
    }, 1000);
  </script>


<?php
/* ============================
   DEV MODE: SHOW OTP FOR TEST

   ============================ */

$devMode = true;  // set to false when pushing to production

if ($devMode && isset($_SESSION['pending_otp'])): ?>
    <div style="
      position:fixed;
      bottom:10px; left:10px;
      background:#222;
      color:#0f0;
      padding:8px 14px;
      border-radius:8px;
      font-family:monospace;
      font-size:14px;
      opacity:0.85;
      z-index:9999;
    ">
        DEV OTP: <strong><?= htmlspecialchars($_SESSION['pending_otp'], ENT_QUOTES) ?></strong>
    </div>
<?php endif; ?>

</body>
</html>

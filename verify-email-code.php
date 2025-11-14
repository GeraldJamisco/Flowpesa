<?php
session_start();
require __DIR__ . '/api/db.php'; // must give you $pdo (PDO)

// 1) Must have active registration flow
if (empty($_SESSION['reg_id'])) {
    header('Location: register.php');
    exit;
}

$regId    = (int) $_SESSION['reg_id'];
$errorMsg = '';
$debugCode = $_GET['debug_code'] ?? null;

// Load flow
$stmt = $pdo->prepare("SELECT * FROM registration_flows WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $regId]);
$flow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flow) {
    unset($_SESSION['reg_id']);
    header('Location: register.php');
    exit;
}

// Guard chain:
//  a) phone must be verified
//  b) email must be set
//  c) step should be email_otp (or we at least know an OTP exists)
if ((int)$flow['phone_verified'] !== 1) {
    header('Location: verify-phone-code.php');
    exit;
}
if (empty($flow['email'])) {
    header('Location: verify-email.php');
    exit;
}
if (empty($flow['email_otp_hash']) || empty($flow['email_otp_expires_at'])) {
    // No active email OTP; send them back to email entry
    header('Location: verify-email.php');
    exit;
}

// Handle POST (submitting the 6-digit code)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Accept either one field "code" or 6 separate digits d1..d6
    $code = trim($_POST['code'] ?? '');
    if ($code === '') {
        // try combine 6 inputs
        $parts = [];
        for ($i = 1; $i <= 6; $i++) {
            $parts[] = trim($_POST["d{$i}"] ?? '');
        }
        $code = implode('', $parts);
    }

    // Normalise
    $code = preg_replace('/\D+/', '', $code);

    if ($code === '' || strlen($code) !== 6) {
        $errorMsg = 'Enter the 6-digit code we sent to your email.';
    } else {
        // Check attempts first
        $attempts = (int)($flow['attempts_email'] ?? 0);
        if ($attempts >= 5) {
            $errorMsg = 'Too many incorrect attempts. Please request a new code.';
        } else {
            // Check expiry
            try {
                $now = new DateTimeImmutable();
                $exp = new DateTimeImmutable($flow['email_otp_expires_at']);
            } catch (Exception $e) {
                $exp = null;
            }

            if (!$exp || $exp < $now) {
                $errorMsg = 'That code has expired. Please request a new one.';
            } else {
                // Verify hash
                $hash = $flow['email_otp_hash'];
                if (!password_verify($code, $hash)) {
                    // Wrong code → bump attempts
                    $attempts++;
                    $upd = $pdo->prepare("
                        UPDATE registration_flows
                        SET attempts_email = :a
                        WHERE id = :id
                    ");
                    $upd->execute([
                        ':a'  => $attempts,
                        ':id' => $regId,
                    ]);

                    $errorMsg = 'Incorrect code. Check the email and try again.';
                } else {
                    // OK → mark email as verified, clear OTP + attempts, move to next step
                    $upd = $pdo->prepare("
                        UPDATE registration_flows
                        SET email_verified       = 1,
                            email_otp_hash      = NULL,
                            email_otp_expires_at= NULL,
                            attempts_email      = 0,
                            step                = 'id_type'
                        WHERE id = :id
                    ");
                    $upd->execute([':id' => $regId]);

                    // Next step in your flow: choose ID type (identity/passport/driving licence)
                    header('Location: verify-id-type.php');
                    exit;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Verify email code</title>

  <!-- Re-use your global + auth styles (same as phone OTP page) -->
  <link rel="stylesheet" href="css/vars.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/auth-override.css" />
</head>
<body class="theme-dark register-digit-code-dark-mode">
  <header class="top-bar">
    <div class="top">
      <button class="icon-btn" type="button" onclick="history.back()" aria-label="Go back">
        <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15 18L9 12l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="top-title">Verify email</h1>
      <span class="top-ghost" aria-hidden="true"></span>
    </div>
  </header>

  <main class="screen">
    <section class="intro">
      <h2 class="heading">6-digit code</h2>
      <p class="subheading" id="otp-help">
        We sent a code to <strong><?= htmlspecialchars($flow['email']) ?></strong>.
        Enter it below to confirm your email address.
      </p>
    </section>

    <form class="card" id="otp-form" method="post" novalidate>
      <?php if ($debugCode): ?>
        <!-- DEV ONLY: remove when going live -->
        <p style="color:#ffdf5b; font-size:13px; margin-bottom:10px;">
          DEV email code: <strong><?= htmlspecialchars($debugCode) ?></strong>
        </p>
      <?php endif; ?>

      <div class="digit-code-input" role="group" aria-label="6-digit code">
        <input name="d1" class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 1">
        <input name="d2" class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 2">
        <input name="d3" class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 3">
        <input name="d4" class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 4">
        <input name="d5" class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 5">
        <input name="d6" class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 6">
      </div>

      <!-- Hidden combined code if you ever want to use JS-only "code" post -->
      <input type="hidden" name="code" id="otp-code" value="">

      <p
        class="error"
        id="otp-error"
        role="alert"
        <?= $errorMsg ? '' : 'hidden'; ?>
      >
        <?= htmlspecialchars($errorMsg ?: 'Enter the 6-digit code we sent to your email.') ?>
      </p>

      <div class="stack-under">
        <p class="subheading" id="resend-text">Resend code in 00:30</p>
        <p class="subheading">
          Need to change email?
          <a class="link" href="verify-email.php">Update email address</a>
        </p>
      </div>

      <button id="verify-btn" class="regular-button" type="submit" disabled>
        <span class="button-label">Continue</span>
      </button>
    </form>
  </main>

  <script>
    (function(){
      const inputs = Array.from(document.querySelectorAll('.otp-input'));
      const btn    = document.getElementById('verify-btn');
      const error  = document.getElementById('otp-error');
      const codeField = document.getElementById('otp-code');
      const resendText = document.getElementById('resend-text');

      if (!inputs.length || !btn) return;

      inputs[0].focus();

      function collectCode() {
        const code = inputs.map(i => i.value.replace(/\D/g,'')).join('');
        if (codeField) codeField.value = code;
        const ok = code.length === inputs.length;
        btn.disabled = !ok;
        btn.classList.toggle('is-active', ok);
        if (!ok && error && !<?= $errorMsg ? 'true' : 'false' ?>) {
          error.hidden = true;  // don’t shout while user is typing
        }
        return ok;
      }

      inputs.forEach((inp, idx) => {
        inp.addEventListener('input', e => {
          e.target.value = e.target.value.replace(/\D/g,'').slice(0,1);
          if (e.target.value && idx < inputs.length - 1) {
            inputs[idx+1].focus();
          }
          collectCode();
        });

        inp.addEventListener('keydown', e => {
          if (e.key === 'Backspace' && !inp.value && idx > 0) {
            inputs[idx-1].value = '';
            inputs[idx-1].focus();
            e.preventDefault();
            collectCode();
          }
        });

        inp.addEventListener('paste', e => {
          const txt = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
          if (!txt) return;
          e.preventDefault();
          inputs.forEach((i, j) => { i.value = txt[j] || ''; });
          inputs[Math.min(txt.length, 6) - 1].focus();
          collectCode();
        });
      });

      document.getElementById('otp-form').addEventListener('submit', e => {
        collectCode();
        // Let PHP handle validation; we just block empty
        if (btn.disabled) {
          e.preventDefault();
        }
      });

      // simple resend countdown (pure display; backend resend can be wired later)
      let s = 30;
      const timer = setInterval(() => {
        s -= 1;
        if (!resendText) return;
        if (s > 0) {
          resendText.textContent = 'Resend code in 00:' + String(s).padStart(2,'0');
        } else {
          resendText.textContent = 'Resend code';
          clearInterval(timer);
        }
      }, 1000);
    })();
  </script>
</body>
</html>

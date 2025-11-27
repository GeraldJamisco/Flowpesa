<?php
session_start();
require __DIR__ . '/api/db.php'; // $pdo (PDO)

// 1) Must have an active registration flow
if (empty($_SESSION['reg_id'])) {
    header('Location: register.php');
    exit;
}

$regId    = (int) $_SESSION['reg_id'];
$errorMsg = '';
$emailVal = '';   // to prefill input
$debugCode = null;

// Load flow
$stmt = $pdo->prepare("SELECT * FROM registration_flows WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $regId]);
$flow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flow) {
    unset($_SESSION['reg_id']);
    header('Location: register.php');
    exit;
}

// Guard: phone must be verified first
if ((int)$flow['phone_verified'] !== 1) {
    header('Location: verify-phone-code.php');
    exit;
}

$emailVal = $flow['email'] ?? '';

// Simple mail sender (adjust addresses for real use)
function sendVerificationEmail(string $to, string $code): bool
{
    $subject = 'Your Flowpesa email verification code';
    $message = "Hi,\n\nYour Flowpesa verification code is: {$code}\n\n" .
               "Enter this code in the Flowpesa app to confirm your email.\n\n" .
               "If you didn’t request this, you can ignore this message.\n\n" .
               "— Flowpesa Security";
    $headers = "From: Flowpesa <no-reply@flowpesa.com>\r\n" .
               "Reply-To: support@flowpesa.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // On localhost this may silently fail; that’s fine for now.
    return @mail($to, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailVal = trim($_POST['email'] ?? '');

    // Basic validation
    if ($emailVal === '') {
        $errorMsg = 'Email is required.';
    } elseif (!filter_var($emailVal, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Enter a valid email address (e.g., name@domain.com).';
    } elseif (mb_strlen($emailVal) > 100) {
        $errorMsg = 'Email is too long (max 100 characters).';
    }

    if ($errorMsg === '') {
        // Optional: check if already used by another user
        $check = $pdo->prepare("SELECT id FROM registration_flows WHERE email = :e LIMIT 1");
        $check->execute([':e' => $emailVal]);
        if ($check->fetch()) { $errorMsg = 'This email is already in use.'; }

        if ($errorMsg === '') {
            // Generate email OTP
            $code  = (string) random_int(100000, 999999);
            $hash  = password_hash($code, PASSWORD_DEFAULT);
            $now   = new DateTimeImmutable();
            $exp   = $now->add(new DateInterval('PT10M')); // 10 minutes

            // Store in registration_flows
            $upd = $pdo->prepare("
                UPDATE registration_flows
                SET email              = :email,
                    email_verified     = 0,
                    email_otp_hash    = :hash,
                    email_otp_expires_at = :exp,
                    attempts_email    = 0,
                    step              = 'email_otp'
                WHERE id = :id
            ");
            $upd->execute([
                ':email' => $emailVal,
                ':hash'  => $hash,
                ':exp'   => $exp->format('Y-m-d H:i:s'),
                ':id'    => $regId,
            ]);

            // Try sending mail (won’t work on some local setups, that’s ok)
            $sent = sendVerificationEmail($emailVal, $code);

            // DEV ONLY: expose code so you can continue even if mail() fails
            $debugCode = $code;

            // Redirect to code entry page with debug_code in URL for dev
            header('Location: verify-email-code.php?debug_code=' . urlencode($code));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Verify email</title>

  <!-- Global first, page-scoped second -->
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/verify-email.css" />
</head>
<body class="email-screen">
  <!-- Top bar -->
  <header class="top-bar">
    <div class="top">
      <button class="back-btn" type="button" onclick="history.back()" aria-label="Go back">
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15 18L9 12l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <span aria-hidden="true"></span>
    </div>
  </header>

  <main class="content">
    <h1 class="title">Email</h1>
    <p class="subtitle">Please provide your email address.</p>

    <?php if ($debugCode !== null): ?>
      <!-- DEV ONLY: remove when going live -->
      <p style="color:#ffdf5b; font-size:13px; margin-bottom:10px;">
        DEV email code: <strong><?= htmlspecialchars($debugCode) ?></strong>
      </p>
    <?php endif; ?>

    <form id="email-form" method="post" novalidate>
      <label class="label" for="email">Email address</label>
      <div class="pill">
        <input
          id="email"
          name="email"
          type="email"
          inputmode="email"
          autocomplete="email"
          placeholder="you@example.com"
          maxlength="100"
          aria-describedby="email-help"
          required
          value="<?= htmlspecialchars($emailVal) ?>"
        />
        <button class="clear-btn" type="button" aria-label="Clear" <?= $emailVal === '' ? 'hidden' : '' ?>>&times;</button>
      </div>

      <div class="hint-row">
        <span id="email-help">We’ll use this for receipts and security alerts.</span>
        <span class="counter" data-for="email">
          <?= strlen($emailVal) ?>/100
        </span>
      </div>

      <p
        class="error"
        id="email-error"
        role="alert"
        <?= $errorMsg ? '' : 'hidden'; ?>
      >
        <?= htmlspecialchars($errorMsg ?: 'Enter a valid email address (e.g., name@domain.com).') ?>
      </p>

      <button
        id="email-continue"
        class="cta"
        type="submit"
        <?= $errorMsg || $emailVal === '' ? 'disabled' : '' ?>
      >
        Continue
      </button>
    </form>
  </main>

  <script src="Js/verify-email.js"></script>
  <script>
    // tiny sync so JS & PHP stay in sync on first load
    (function(){
      const emailInput = document.getElementById('email');
      const clearBtn   = document.querySelector('.clear-btn');
      const counter    = document.querySelector('.counter[data-for="email"]');
      const btn        = document.getElementById('email-continue');
      const errorEl    = document.getElementById('email-error');

      if (!emailInput || !btn) return;

      function update() {
        const val = emailInput.value.trim();
        if (counter) counter.textContent = (val.length || 0) + '/100';

        const ok = val.length > 0 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
        btn.disabled = !ok;
        if (clearBtn) clearBtn.hidden = val.length === 0;

        // Don’t show error while typing unless PHP already sent one
        <?php if (!$errorMsg): ?>
        if (!ok) errorEl.hidden = true;
        <?php endif; ?>
      }

      emailInput.addEventListener('input', update);
      if (clearBtn) {
        clearBtn.addEventListener('click', () => {
          emailInput.value = '';
          emailInput.focus();
          update();
        });
      }
      update();
    })();
  </script>
</body>
</html>

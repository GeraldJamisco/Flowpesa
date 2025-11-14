<?php
session_start();
require __DIR__ . '/api/db.php';

if (empty($_SESSION['reg_id'])) {
    header('Location: register.php');
    exit;
}

$regId = (int) $_SESSION['reg_id'];

$stmt = $pdo->prepare("SELECT * FROM registration_flows WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $regId]);
$flow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flow) {
    unset($_SESSION['reg_id']);
    header('Location: register.php');
    exit;
}

if ((int) $flow['phone_verified'] === 1) {
    header('Location: verify-email.php');
    exit;
}

if (empty($flow['phone_otp_hash']) || empty($flow['phone_otp_expires_at'])) {
    header('Location: verify-phone.php');
    exit;
}

$debugOtp   = $_GET['debug_otp'] ?? null;
$infoBanner = '';
if (isset($_GET['sent'])) {
    $infoBanner = 'We sent a fresh 6-digit code to your phone.';
}
if (isset($_GET['sms_error'])) {
    $infoBanner .= ' SMS delivery failed, but the code is active here.';
}

$msisdn   = $flow['msisdn'];
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if (!preg_match('/^\d{6}$/', $otp)) {
        $errorMsg = 'Enter the 6-digit code.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM registration_flows WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $regId]);
        $flow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$flow) {
            $errorMsg = 'Something went wrong. Please start again.';
        } else {
            try {
                $expires = new DateTimeImmutable($flow['phone_otp_expires_at']);
            } catch (Throwable $e) {
                $expires = null;
            }

            if (!$expires || $expires < new DateTimeImmutable()) {
                $errorMsg = 'This code has expired. Resend a new one.';
            } elseif ((int) ($flow['attempts_phone'] ?? 0) >= 5) {
                $errorMsg = 'Too many attempts. Please resend the code.';
            } elseif (!password_verify($otp, $flow['phone_otp_hash'])) {
                $pdo->prepare("
                    UPDATE registration_flows
                       SET attempts_phone = attempts_phone + 1
                     WHERE id = :id
                ")->execute([':id' => $regId]);

                $errorMsg = 'Incorrect code. Please try again.';
            } else {
                $pdo->prepare("
                    UPDATE registration_flows
                       SET phone_verified        = 1,
                           step                  = 'email',
                           attempts_phone        = 0,
                           phone_otp_hash        = NULL,
                           phone_otp_expires_at  = NULL
                     WHERE id = :id
                ")->execute([':id' => $regId]);

                header('Location: verify-email.php');
                exit;
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
        <span id="msisdn"><?= htmlspecialchars($msisdn) ?></span>
        unless you already have an account.
      </p>
      <?php if ($infoBanner): ?>
        <p class="subheading" style="color:#6ee7b7;"><?= htmlspecialchars($infoBanner) ?></p>
      <?php endif; ?>
      <?php if ($debugOtp !== null): ?>
        <p class="subheading" style="color:#ffdf5b; font-size:13px;">
          DEV OTP: <strong><?= htmlspecialchars($debugOtp) ?></strong>
        </p>
      <?php endif; ?>
    </section>

    <form class="card" id="otp-form" method="post" novalidate>
      <div class="digit-code-input" role="group" aria-label="6-digit code">
        <?php for ($i = 0; $i < 6; $i++): ?>
          <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit <?= $i + 1 ?>">
        <?php endfor; ?>
      </div>

      <input type="hidden" name="otp" id="otp-full">

      <p class="error" id="otp-error" role="alert" <?= $errorMsg ? '' : 'hidden'; ?>>
        <?= htmlspecialchars($errorMsg ?: 'Enter the 6-digit code.') ?>
      </p>

      <div class="stack-under">
        <p class="subheading">
          <a class="link" href="verify-phone.php?resend=1">Resend SMS code</a>
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
    const inputs = Array.from(document.querySelectorAll('.otp-input'));
    const btn    = document.getElementById('verify-btn');
    const err    = document.getElementById('otp-error');
    const hidden = document.getElementById('otp-full');

    if (inputs.length) inputs[0].focus();

    function updateState() {
      const code = inputs.map(i => i.value).join('');
      const ok   = code.length === inputs.length;
      btn.disabled = !ok;
      btn.classList.toggle('is-active', ok);
      return { ok, code };
    }

    inputs.forEach((inp, i) => {
      inp.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g,'').slice(0,1);
        if (e.target.value && i < inputs.length - 1) {
          inputs[i+1].focus();
        }
        updateState();
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
          .slice(0, inputs.length);
        if (!t) return;
        e.preventDefault();
        inputs.forEach((x,j) => x.value = t[j] || '');
        inputs[Math.min(t.length, inputs.length) - 1].focus();
        updateState();
      });
    });

    document.getElementById('otp-form').addEventListener('submit', e => {
      const { ok, code } = updateState();
      if (!ok) {
        e.preventDefault();
        err.hidden = false;
        err.textContent = 'Enter the 6-digit code.';
        return;
      }
      hidden.value = code;
    });
  </script>
</body>
</html>

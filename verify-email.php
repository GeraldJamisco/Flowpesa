<?php
declare(strict_types=1);

require __DIR__ . '/api/db.php';

$requestToken  = (string)($_GET['request'] ?? '');
$saved_email   = (string)($_GET['email'] ?? '');
$flash_success = '';
$flash_error   = '';
$dev_code      = null;

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid':
            $flash_error = 'Enter a valid email address (e.g., name@domain.com).';
            break;
        case 'server':
            $flash_error = 'We could not start email verification. Please try again.';
            break;
        default:
            $flash_error = 'Something went wrong. Please try again.';
    }
}

if (isset($_GET['mail_error'])) {
    $flash_error = 'We saved your request but could not send the email automatically.';
}

if (!$flash_error && isset($_GET['sent'])) {
    $flash_success = 'We\'ve sent a verification code to your email.';
}

if ($requestToken !== '') {
    try {
        $stmt = $pdo->prepare(
            'SELECT contact_value, code, status
               FROM verification_requests
              WHERE request_token = :token AND verification_type = :type
              LIMIT 1'
        );
        $stmt->execute([
            ':token' => $requestToken,
            ':type'  => 'email',
        ]);
        $row = $stmt->fetch();

        if ($row) {
            $saved_email = $row['contact_value'];
            $dev_code    = $row['code'];

            if ($row['status'] === 'verified' && !$flash_error) {
                $flash_success = 'This email address is already verified.';
            }
        } elseif (!$flash_error) {
            $flash_error = 'We could not find that email verification request.';
        }
    } catch (Throwable $e) {
        $flash_error = 'Unable to load your verification request.';
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

    <?php if ($flash_success): ?>
      <div class="flash success">
        <?= htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if ($flash_error): ?>
      <div class="flash error">
        <?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- DEV-ONLY helper: show verification code while designing -->
    <?php if ($dev_code !== null): ?>
      <div class="dev-box">
        <strong>DEV ONLY:</strong> Email code =
        <code><?= htmlspecialchars($dev_code, ENT_QUOTES, 'UTF-8') ?></code>
      </div>
    <?php endif; ?>

    <form id="email-form" method="post" action="send_email.php" novalidate>
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
          value="<?= htmlspecialchars($saved_email, ENT_QUOTES, 'UTF-8') ?>"
        />
        <button class="clear-btn" type="button" aria-label="Clear" <?= $saved_email === '' ? 'hidden' : '' ?>>&times;</button>
      </div>

      <div class="hint-row">
        <span id="email-help">We’ll use this for receipts and security alerts.</span>
        <span class="counter" data-for="email">
          <?= strlen($saved_email) ?>/100
        </span>
      </div>

      <p class="error" id="email-error" <?= $flash_error ? '' : 'hidden' ?>>
        <?= $flash_error
            ? htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8')
            : 'Enter a valid email address (e.g., name@domain.com).' ?>
      </p>

      <button
        id="email-continue"
        class="cta<?= ($saved_email && !$flash_error) ? ' is-active' : '' ?>"
        type="submit"
        <?= ($saved_email && !$flash_error) ? '' : 'disabled' ?>
      >
        Continue
      </button>
    </form>
  </main>

  <script src="Js/verify-email.js"></script>
</body>
</html>

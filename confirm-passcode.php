<?php
// confirm-passcode.php
session_start();
require __DIR__ . '/api/db.php'; // $pdo

if (empty($_SESSION['reg_id'])) {
    header('Location: register.php');
    exit;
}

$regId = (int) $_SESSION['reg_id'];
$error = '';

// Load flow with temp hash
$stmt = $pdo->prepare("
    SELECT
        id,
        phone_verified,
        email_verified,
        temp_passcode_hash,
        passcode_hash,
        step
      FROM registration_flows
     WHERE id = :id
     LIMIT 1
");
$stmt->execute([':id' => $regId]);
$flow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flow) {
    unset($_SESSION['reg_id']);
    header('Location: register.php');
    exit;
}

if ((int)$flow['phone_verified'] !== 1) {
    header('Location: verify-phone-code.php');
    exit;
}

if ((int)$flow['email_verified'] !== 1) {
    header('Location: verify-email.php');
    exit;
}

if (!empty($flow['passcode_hash']) && empty($flow['temp_passcode_hash'])) {
    header('Location: verify-id-citizenship.php');
    exit;
}

if (empty($flow['temp_passcode_hash'])) {
    header('Location: set-passcode.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passcode = $_POST['passcode'] ?? '';
    $passcode = preg_replace('/\D+/', '', $passcode);

    if (strlen($passcode) !== 6) {
        $error = 'Passcode must be exactly 6 digits.';
    } elseif (!password_verify($passcode, $flow['temp_passcode_hash'])) {
        $error = 'Passcodes do not match. Please try again.';
    } else {
        // Promote temp hash to final passcode_hash
        $upd = $pdo->prepare("
            UPDATE registration_flows
            SET passcode_hash      = temp_passcode_hash,
                temp_passcode_hash = NULL,
                step               = 'id_type'
            WHERE id = :id
        ");
        $upd->execute([':id' => $regId]);

        header('Location: verify-id-citizenship.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Confirm passcode</title>

  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/set-passcode.css" />
</head>
<body class="passcode-screen">
  <header class="top-bar">
    <div class="top">
      <button class="back-btn" onclick="history.back()" aria-label="Back">
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15 18L9 12l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <span aria-hidden="true"></span>
    </div>
  </header>

  <main class="content">
    <h1 class="title">Confirm passcode</h1>
    <p class="subtitle">Type the same 6-digit passcode again.</p>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form id="passcode-form" method="post" novalidate>
      <input type="hidden" name="passcode" id="passcode-field" value="">

      <div class="dots" id="dots" aria-hidden="true"></div>

      <div class="keypad" aria-label="Numeric keypad">
        <div class="row">
          <button class="key" type="button" data-key="1">1</button>
          <button class="key" type="button" data-key="2">2</button>
          <button class="key" type="button" data-key="3">3</button>
        </div>
        <div class="row">
          <button class="key" type="button" data-key="4">4</button>
          <button class="key" type="button" data-key="5">5</button>
          <button class="key" type="button" data-key="6">6</button>
        </div>
        <div class="row">
          <button class="key" type="button" data-key="7">7</button>
          <button class="key" type="button" data-key="8">8</button>
          <button class="key" type="button" data-key="9">9</button>
        </div>
        <div class="row">
          <button class="key key-action" type="button" id="clear" data-action="clear">⌫</button>
          <button class="key" type="button" data-key="0">0</button>
          <button class="key key-action" type="submit" id="enter">→</button>
        </div>
      </div>
    </form>
  </main>

  <script src="Js/confirm-passcode.js" defer></script>
</body>
</html>

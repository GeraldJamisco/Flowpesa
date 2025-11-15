<?php
// set-passcode.php
session_start();
require __DIR__ . '/api/db.php'; // gives you $pdo (PDO)

// Must have an active registration flow
if (empty($_SESSION['reg_id'])) {
    header('Location: register.php');
    exit;
}

$regId   = (int) $_SESSION['reg_id'];
$error   = '';

// Load flow to be sure it exists
$stmt = $pdo->prepare("SELECT id, passcode_hash FROM registration_flows WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $regId]);
$flow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flow) {
    unset($_SESSION['reg_id']);
    header('Location: register.php');
    exit;
}

// If they already have a passcode, you could redirect straight to dashboard
// if (!empty($flow['passcode_hash'])) {
//     header('Location: dashboard.php');
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passcode = $_POST['passcode'] ?? '';
    $passcode = preg_replace('/\D+/', '', $passcode); // keep digits only

    if (strlen($passcode) !== 6) {
        $error = 'Passcode must be exactly 6 digits.';
    } else {
        $hash = password_hash($passcode, PASSWORD_DEFAULT);

        $upd = $pdo->prepare("
            UPDATE registration_flows
            SET passcode_hash = :h,
                step          = 'complete'
            WHERE id = :id
        ");
        $upd->execute([
            ':h'  => $hash,
            ':id' => $regId,
        ]);

        header('Location: confirm-passcode.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Create passcode</title>

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
    <h1 class="title">Create passcode</h1>
    <p class="subtitle">Passcode should be exactly 6 digits long</p>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- form wraps dots + keypad -->
    <form id="passcode-form" method="post" novalidate>
      <!-- Hidden field that will carry the 6-digit code -->
      <input type="hidden" name="passcode" id="passcode-field" value="">

      <!-- Rendered dots go here -->
      <div class="dots" id="dots" aria-hidden="true"></div>

      <!-- On-screen keypad -->
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

  <script src="Js/set-passcode.js" defer></script>
</body>
</html>

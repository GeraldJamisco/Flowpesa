<?php
// public/login.php
declare(strict_types=1);
session_start();
require __DIR__ . '/./api/db.php';

// CSRF token (simple)
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf'];

$errors = [];
$flash  = '';
if (isset($_GET['registered'])) {
  $flash = 'Account created. Please sign in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    $errors[] = 'Invalid session. Reload the page.';
  } else {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if ($password === '') $errors[] = 'Password is required.';

    if (!$errors) {
      $q = db()->prepare('SELECT id,name,email,password_hash,tier,kyc_pct FROM users WHERE email = ? LIMIT 1');
      $q->execute([$email]);
      $user = $q->fetch();

      if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = 'Invalid email or password.';
      } else {
        // success: set session
        $_SESSION['uid'] = (int)$user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['tier'] = (int)$user['tier'];
        $_SESSION['kyc_pct'] = (int)$user['kyc_pct'];

        // rotate CSRF on login
        $_SESSION['csrf'] = bin2hex(random_bytes(16));

        // redirect (change to dashboard.php if you convert it to PHP later)
        header('Location: dashboard.php');
        exit;
      }
    }
  }
}

// tiny helper
function old($k){ return htmlspecialchars($_POST[$k] ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Flowpesa — Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="wrap">
    <div class="phone" role="main">
      <div class="content">
        <h1>Welcome Back!</h1>
        <p class="sub">Sign in to your Flowpesa account</p>

        <?php if ($flash): ?>
          <div class="alert alert-success py-2"><?= htmlspecialchars($flash, ENT_QUOTES) ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
          <div class="alert alert-warning" role="alert">
            <?php foreach ($errors as $e): ?>
              <div><?= htmlspecialchars($e, ENT_QUOTES) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" autocomplete="on">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>"/>

          <div class="field">
            <label class="label" for="email">Email</label>
            <div class="control">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                <path d="m22 6-10 7L2 6"/>
              </svg>
              <input id="email" name="email" type="email" class="input" placeholder="you@example.com" required value="<?= old('email') ?>"/>
            </div>
          </div>

          <div class="field">
            <label class="label" for="password">Password</label>
            <div class="control">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              <input id="password" name="password" type="password" class="input" placeholder="••••••••" required/>
            </div>
          </div>

          <div class="auth-row-right">
            <a class="auth-link" href="#">Forgot Password?</a>
          </div>

          <button class="primary" type="submit">Sign In</button>

          <p class="signup">Don’t have an account? <a href="signup.php">Sign Up</a></p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

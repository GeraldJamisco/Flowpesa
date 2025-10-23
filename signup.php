<?php
// public/signup.php
declare(strict_types=1);
session_start();
require __DIR__ . '/./api/db.php'; //goes to database connection

// CSRF
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF check
  if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    $errors[] = 'Invalid session. Please reload the page.';
  } else {
    // Gather fields
    $name     = trim($_POST['name'] ?? '');
    $country  = trim($_POST['country'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirm'] ?? '');

    // Validate
    if ($name === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 4) $errors[] = 'Password must be at least 4 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
      try {
        // Unique email check
        $q = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $q->execute([$email]);
        if ($q->fetch()) {
          $errors[] = 'This email is already registered.';
        } else {
          $hash = password_hash($password, PASSWORD_DEFAULT);
          $ins = db()->prepare('INSERT INTO users (name,email,phone,country,password_hash,tier,kyc_pct) VALUES (?,?,?,?,?,?,?)');
          $ins->execute([$name,$email,$phone,$country,$hash,0,0]);
          // Success → go to login
          header('Location: login.php?registered=1');
          exit;
        }
      } catch (Throwable $e) {
        // You can log $e->getMessage() to a file in production
        $errors[] = 'Server error. Please try again.';
      }
    }
  }
}

// helper to persist old values on error
function old($key, $default='') {
  return htmlspecialchars($_POST[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Flowpesa — Sign Up</title>

  <link rel="icon" href="assets/flowpesa-icon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/auth-signup.js" defer></script>
</head>
<body>
  <div class="wrap">
    <div class="phone" role="main">
      <div class="auth-content">
        <h1>Create Account</h1>
        <p class="auth-sub">Start your Flowpesa Wallet in minutes</p>

        <!-- Stepper (UI only for now; submit on Step 1) -->
        <div class="auth-stepper" aria-label="signup steps">
          <div class="auth-chip auth-chip--active">1 · Account</div>
          <div class="auth-chip">2 · Verify</div>
          <div class="auth-chip">3 · Security</div>
          <div class="auth-chip">4 · KYC</div>
        </div>

        <!-- Flash errors -->
        <?php if ($errors): ?>
          <div class="alert alert-warning" role="alert" style="margin: 8px 0 12px">
            <?php foreach ($errors as $e): ?>
              <div><?= htmlspecialchars($e, ENT_QUOTES) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- STEP 1: ACCOUNT DETAILS  (server POST happens here) -->
        <form id="step1" method="post" autocomplete="on" novalidate>
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>"/>

          <div class="auth-field">
            <label class="auth-label" for="fullName">Full name</label>
            <div class="auth-control">
              <input id="fullName" name="name" class="auth-input" placeholder="Jane Doe" autocomplete="name" required value="<?= old('name') ?>"/>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="country">Country</label>
            <div class="auth-control">
              <select id="country" name="country" class="auth-input" required>
                <?php
                  // keep UG as default if no POST yet
                  $sel = $_POST['country'] ?? 'UG';
                  $opts = ['UG'=>'Uganda','KE'=>'Kenya','TZ'=>'Tanzania','RW'=>'Rwanda'];
                  foreach ($opts as $val=>$label) {
                    $s = $val === $sel ? 'selected' : '';
                    echo "<option value=\"".htmlspecialchars($val,ENT_QUOTES)."\" $s>".htmlspecialchars($label,ENT_QUOTES)."</option>";
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="email">Email</label>
            <div class="auth-control">
              <input id="email" name="email" type="email" class="auth-input" placeholder="you@example.com" autocomplete="email" required value="<?= old('email') ?>"/>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="phone">Phone</label>
            <div class="auth-control">
              <input id="phone" name="phone" type="tel" class="auth-input" placeholder="+256 700 000000" autocomplete="tel" value="<?= old('phone') ?>"/>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="password">Password</label>
            <div class="auth-control">
              <input id="password" name="password" type="password" class="auth-input" placeholder="••••••••" autocomplete="new-password" required/>
              <svg class="auth-trailing" id="togglePwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="confirm">Confirm password</label>
            <div class="auth-control">
              <input id="confirm" name="confirm" type="password" class="auth-input" placeholder="••••••••" autocomplete="new-password" required/>
              <svg class="auth-trailing" id="toggleConfirm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-link" style="cursor:default">
              <input id="agree" type="checkbox" required style="margin-right:8px"> I agree to the Terms & Privacy
            </label>
          </div>

          <!-- v1: Submit here to create the account -->
          <button class="auth-primary" type="submit" id="continue">Create account</button>
          <p class="auth-signup">Already have an account? <a href="login.php">Sign In</a></p>
        </form>

        <!-- STEP 2: OTP (UI only for now) -->
        <div id="step2" style="display:none">
          <p class="auth-sub" style="margin-top:0">Enter the 6-digit code we sent to <strong id="otpTarget">your email/phone</strong>.</p>
          <div class="otp" aria-label="OTP inputs">
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
          </div>
          <button class="auth-primary" type="button" id="verify">Verify</button>
          <p class="auth-signup">Didn't get a code? <a href="#" id="resend">Resend</a></p>
        </div>

        <!-- STEP 3: PIN (UI only) -->
        <div id="step3" style="display:none">
          <p class="auth-sub">Set a 4-digit transaction PIN for quick approvals.</p>
          <div class="otp" aria-label="PIN inputs">
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
            <input maxlength="1" inputmode="numeric" />
          </div>
          <button class="auth-primary" type="button" id="pinNext">Save PIN</button>
        </div>

        <!-- STEP 4: Basic KYC (UI only for now) -->
        <div id="step4" style="display:none">
          <div class="auth-field">
            <label class="auth-label">ID Type</label>
            <div class="auth-control">
              <select class="auth-input">
                <option>National ID (NIN)</option>
                <option>Passport</option>
                <option>Driver's License</option>
              </select>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label">ID Number</label>
            <div class="auth-control">
              <input class="auth-input" placeholder="CF1234567" />
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label">Date of Birth</label>
            <div class="auth-control">
              <input type="date" class="auth-input"/>
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label">Address (City)</label>
            <div class="auth-control">
              <input class="auth-input" placeholder="Kampala" />
            </div>
          </div>

          <div class="auth-field">
            <label class="auth-label">Upload ID Front (JPG/PNG)</label>
            <input type="file" accept="image/*"/>
          </div>

          <div class="auth-field">
            <label class="auth-label">Selfie (for liveness)</label>
            <input type="file" accept="image/*"/>
          </div>

          <button class="auth-primary" type="button">Finish</button>
          <p class="auth-signup">You can complete full KYC later from Settings.</p>
        </div>

      </div>
    </div>
  </div>
</body>
</html>

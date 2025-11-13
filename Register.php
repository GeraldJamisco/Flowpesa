<?php
// register.php
session_start();

$countryCode = '+256';
$phone       = '';
$error       = '';
$debugOtp    = null; // for local testing only

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $countryCode = $_POST['country_code'] ?? '+256';
    $phoneInput  = $_POST['phone'] ?? '';
    $phone       = $phoneInput; // keep original for re-fill

    // Strip spaces and non-digits for validation/normalisation
    $digits = preg_replace('/\D+/', '', $phoneInput);

    $isUg      = ($countryCode === '+256');
    $ugPattern = '/^0?\d{9}$/';     // 9 digits, optional leading 0
    $intl      = '/^\d{6,14}$/';    // generic

    if ($isUg) {
        if (!preg_match($ugPattern, $digits)) {
            $error = 'Enter a valid Ugandan mobile number.';
        }
    } else {
        if (!preg_match($intl, $digits)) {
            $error = 'Enter a valid mobile number.';
        }
    }

    if ($error === '') {
        // Normalise UG number: drop leading 0 if present (e.g. 07xxxxxxx -> 7xxxxxxx)
        if ($isUg && strlen($digits) === 10 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }

        $msisdn = $countryCode . $digits;

        // Generate a 6-digit OTP
        $otp = random_int(100000, 999999);

        // Store in session for verify-phone.php
        $_SESSION['pending_msisdn'] = $msisdn;
        $_SESSION['pending_otp']    = $otp;
        $_SESSION['otp_created_at'] = time();

        // For NOW (design mode) we don’t send SMS, we only redirect.
        // If you want to see the code while testing, comment out the redirect and var_dump it.
        header('Location: verify-phone.php?msisdn=' . urlencode($msisdn));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Sign up</title>

  <!-- your global styles -->
  <link rel="stylesheet" href="css/vars.css" />
  <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/create-account.css" />

</head>
<body class="theme-dark register-phone-number-deactive-dark-mode">
  <!-- Top bar -->
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

  <!-- Screen content -->
  <main class="screen">
    <section class="intro">
      <h2 class="heading">Let’s get started</h2>
      <p class="subheading" id="phone-help">
        Enter your phone number. We’ll send you a confirmation code.
      </p>
    </section>

    <form class="card" id="signup-form" method="post" action="register.php" novalidate>
      <div class="phone-row">
        <select id="country-code" name="country_code" class="cc-select" aria-label="Country code">
          <option value="+256" <?= $countryCode === '+256' ? 'selected' : '' ?>>🇺🇬 +256</option>
          <option value="+254" <?= $countryCode === '+254' ? 'selected' : '' ?>>🇰🇪 +254</option>
          <option value="+255" <?= $countryCode === '+255' ? 'selected' : '' ?>>🇹🇿 +255</option>
          <option value="+250" <?= $countryCode === '+250' ? 'selected' : '' ?>>🇷🇼 +250</option>
          <option value="+44"  <?= $countryCode === '+44'  ? 'selected' : '' ?>>🇬🇧 +44</option>
          <option value="+1"   <?= $countryCode === '+1'   ? 'selected' : '' ?>>🇺🇸 +1</option>
          <option value="+90"  <?= $countryCode === '+90'  ? 'selected' : '' ?>>🇹🇷 +90</option>
        </select>

        <div class="input-pill">
          <input
            id="phone"
            name="phone"
            type="tel"
            inputmode="tel"
            autocomplete="tel-national"
            placeholder="Mobile number"
            value="<?= htmlspecialchars($phone, ENT_QUOTES) ?>"
          />
          <button type="button" class="clear-btn" aria-label="Clear" <?= $phone === '' ? 'hidden' : '' ?>>&times;</button>
        </div>
      </div>

      <p class="error" id="phone-error" role="alert" <?= $error === '' ? 'hidden' : '' ?>>
        <?= htmlspecialchars($error, ENT_QUOTES) ?>
      </p>

      <button id="continue-btn" class="regular-button" type="submit" <?= $error === '' && $phone === '' ? 'disabled' : '' ?>>
        <span class="button-label">Continue</span>
      </button>
    </form>

    <p class="auth-foot">
      Already have an account?
      <a href="/login" class="link">Log in</a>
    </p>

    <?php if ($debugOtp): ?>
      <!-- Debug only: remove in production -->
      <p style="margin-top:20px;color:#888;font-size:13px;">
        Debug OTP (local testing): <strong><?= (int)$debugOtp ?></strong>
      </p>
    <?php endif; ?>
  </main>

  <script src="Js/register-account.js"></script>
</body>
</html>

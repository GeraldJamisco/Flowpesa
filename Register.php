<?php
session_start();
require __DIR__ . '/api/db.php';
require __DIR__ . '/RegisterControllerPhone.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Sign up</title>
  <!-- your existing css -->
  <link rel="stylesheet" href="css/vars.css" />
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/create-account.css" />
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
      <h2 class="heading">Let’s get started</h2>
      <p class="subheading" id="phone-help">Enter your phone number. We’ll send you a confirmation code there</p>
    </section>

<form class="card" id="signup-form" method="post" novalidate>
  <div class="phone-row">
    <select id="country-code" name="country_code" class="cc-select" aria-label="Country code">
      <option value="+256" <?= $old_country === '+256' ? 'selected' : '' ?>>🇺🇬 +256</option>
      <option value="+254" <?= $old_country === '+254' ? 'selected' : '' ?>>🇰🇪 +254</option>
      <!-- etc... -->
    </select>

    <div class="input-pill">
      <input
        id="phone"
        name="phone"
        type="tel"
        placeholder="Mobile number"
        inputmode="tel"
        value="<?= htmlspecialchars($old_phone) ?>"
      />
      <button type="button" class="clear-btn" aria-label="Clear" hidden>&times;</button>
    </div>
  </div>

  <?php if (!empty($errors)): ?>
    <p class="error">
      <?= htmlspecialchars($errors[0]) ?>
    </p>
  <?php endif; ?>

  <button id="continue-btn" class="regular-button" type="submit">
    <span class="button-label">Sign Up</span>
  </button>
</form>



    <p class="auth-foot">Already have an account? <a class="link" href="/login">Log in</a></p>
  </main>

  <script src="Js/create-account.js"></script>
</body>
</html>

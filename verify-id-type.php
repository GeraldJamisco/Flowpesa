<?php
// verify-id-type.php
session_start();

// Optional guard: make sure we at least passed citizenship step
// If you want it strict, uncomment this:

if (!isset($_SESSION['fp_citizenship_country'])) {
    header('Location: verify-id-citizenship.php');
    exit;
}

$countryName = $_SESSION['fp_citizenship_country'] ?? 'Uganda';

// If user already picked a doc type before, we can pre-check it
$existingDocType = $_SESSION['fp_doc_type'] ?? null;

// Handle POST (user pressed Continue)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docType = $_POST['doc_type'] ?? '';

    $allowed = ['national_id', 'passport', 'drivers_license'];

    if (in_array($docType, $allowed, true)) {
        // Save in session for later steps
        $_SESSION['fp_doc_type'] = $docType;

        // we upload to the dabase what type of document user selected
        
        $registrationId = $_SESSION['fp_registration_id'] ?? null;
        if ($registrationId) {
            require __DIR__ . '/config.php'; // must define $pdo

            $stmt = $pdo->prepare(
                "UPDATE registration_flows
                 SET id_doc_type = :doc_type
                 WHERE id = :id"
            );
            $stmt->execute([
                ':doc_type' => $docType,
                ':id'       => $registrationId,
            ]);
        }
        

        // Go to next screen in the KYC flow
        // Change this to your actual next page name if different
        header('Location: verify-id-front.php');
        exit;
    }

    // If invalid doc_type posted, just fall through and re-render
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Select ID Type</title>

  <!-- Global first -->
  <link rel="stylesheet" href="css/style.css" />
  <!-- Page-scoped styles -->
  <link rel="stylesheet" href="css/verify-id-type.css" />
</head>
<body class="idtype-screen">
  <header class="top-bar">
    <div class="top">
      <button class="back-btn" type="button" aria-label="Back" onclick="history.back()">
        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15 18L9 12l6-6"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"/>
        </svg>
      </button>
      <span class="top-spacer" aria-hidden="true"></span>
    </div>
  </header>

  <main class="content">
    <h1 class="title">Please select a unique document to upload.</h1>
    <p class="subtitle">
      To verify your identity and confirm your residency in
      <strong><?= htmlspecialchars($countryName, ENT_QUOTES, 'UTF-8') ?></strong>,
      please provide a valid document. Rest assured, your data will be handled securely.
    </p>

    <form
      id="idtype-form"
      class="options"
      method="post"
      role="radiogroup"
      aria-label="Document type"
    >
      <!-- National ID -->
      <label class="option">
        <input
          type="radio"
          name="doc_type"
          value="national_id"
          <?= $existingDocType === 'national_id' ? 'checked' : '' ?>
        />
        <span class="option-body">
          <span class="opt-label">Identity card</span>
          <span class="opt-radio" aria-hidden="true"></span>
        </span>
      </label>

      <!-- Passport -->
      <label class="option">
        <input
          type="radio"
          name="doc_type"
          value="passport"
          <?= $existingDocType === 'passport' ? 'checked' : '' ?>
        />
        <span class="option-body">
          <span class="opt-label">Passport</span>
          <span class="opt-radio" aria-hidden="true"></span>
        </span>
      </label>

      <!-- Driver’s licence -->
      <label class="option">
        <input
          type="radio"
          name="doc_type"
          value="drivers_license"
          <?= $existingDocType === 'drivers_license' ? 'checked' : '' ?>
        />
        <span class="option-body">
          <span class="opt-label">Driver’s license</span>
          <span class="opt-radio" aria-hidden="true"></span>
        </span>
      </label>

      <button id="continue" class="btn btn-primary" type="submit" disabled>
        Continue
      </button>
    </form>

    <p class="foot-note">
      <a href="verify-id-citizenship.php" class="link">Change your citizenship</a>
    </p>
  </main>

  <script src="Js/verify-id-type.js" defer></script>
</body>
</html>

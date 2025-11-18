<?php
// verify-id-citizenship.php
session_start();

/**
 * Very simple country guesser.
 * For now:
 *  - You can override via ?country=Uganda
 *  - You can later replace the stub with a real IP->country lookup.
 */
function guessCountryName(): string
{
    // 1) Let developer override via query param (good for testing)
    if (!empty($_GET['country'])) {
        return trim($_GET['country']);
    }

    // 2) Resolve country from IP using fast server-provided headers/local modules
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '';

    // If mod_geoip/GeoIP2 (server module) exposes the country name directly
    if (!empty($_SERVER['GEOIP_COUNTRY_NAME'])) {
        return trim($_SERVER['GEOIP_COUNTRY_NAME']);
    }

    // If upstream (e.g., Cloudflare/AppEngine) provides an ISO country code
    $isoCode = $_SERVER['HTTP_CF_IPCOUNTRY']
        ?? $_SERVER['HTTP_X_APPENGINE_COUNTRY']
        ?? $_SERVER['GEOIP_COUNTRY_CODE']
        ?? null;

    if (!empty($isoCode) && strtoupper($isoCode) !== 'ZZ') {
        $iso = strtoupper(trim($isoCode));
        // Minimal ISO->name map (extend as needed). Keep small for speed.
        static $isoToName = [
            'UG' => 'Uganda', 'KE' => 'Kenya', 'TZ' => 'Tanzania', 'RW' => 'Rwanda',
            'BI' => 'Burundi', 'SS' => 'South Sudan', 'SD' => 'Sudan', 'ET' => 'Ethiopia',
            'NG' => 'Nigeria', 'GH' => 'Ghana', 'ZA' => 'South Africa', 'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe', 'MW' => 'Malawi', 'CM' => 'Cameroon', 'CD' => 'Congo - Kinshasa',
            'CG' => 'Congo - Brazzaville', 'BW' => 'Botswana', 'NA' => 'Namibia', 'MZ' => 'Mozambique',
            'SO' => 'Somalia', 'DJ' => 'Djibouti', 'ER' => 'Eritrea', 'UGA' => 'Uganda', // leniency
        ];
        if (isset($isoToName[$iso])) {
            return $isoToName[$iso];
        }
        // Fallback: return the code itself if unknown (human-readable enough)
        return $iso;
    }

    // 3) For now, default to Uganda to keep flow moving
    return 'Uganda';
}

$countryName = guessCountryName();

// OPTIONAL: if you already have a registration row, you can grab its id
$registrationId = $_SESSION['fp_registration_id'] ?? null;

// Handle the answer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer = $_POST['citizen'] ?? null; // "yes" or "no"

    if ($answer === 'yes' || $answer === 'no') {

        // Store in session – later steps (ID type, etc.) can use this
        $_SESSION['fp_citizenship_country'] = $countryName;
        $_SESSION['fp_is_citizen'] = ($answer === 'yes') ? 1 : 0;

        
        require __DIR__ . '/api/db.php'; // $pdo

        if ($registrationId && isset($pdo)) {
            $stmt = $pdo->prepare(
                "UPDATE registration_flows
                 SET citizenship_country = :country,
                     citizenship_is_citizen = :is_citizen
                 WHERE id = :id"
            );
            $stmt->execute([
                ':country'    => $countryName,
                ':is_citizen' => ($answer === 'yes') ? 1 : 0,
                ':id'         => $registrationId,
            ]);
        }
        

        // Next step: choose ID type (National ID / Passport / Driver’s licence)
        header('Location: verify-id-type.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flowpesa — Verify ID</title>

  <!-- Global styles -->
  <link rel="stylesheet" href="css/style.css" />

  <!-- Page-scoped overrides (same CSS file you already had) -->
  <link rel="stylesheet" href="css/verify-id-citizenship.css" />
</head>
<body class="citizenship-screen">
  <!-- Top bar -->
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

  <main class="citizenship-content">
    <h1 class="title">
      Do you hold citizenship in
      <?= htmlspecialchars($countryName, ENT_QUOTES, 'UTF-8') ?>?
    </h1>
    <p class="subtitle">
      This information is required to set up your Flowpesa account.
    </p>

    <figure class="id-figure">
      <img src="assets/identity.png" alt="Identity badge illustration">
    </figure>
  </main>

  <!-- Sticky bottom actions: post the choice -->
  <form method="post" class="citizenship-actions" aria-label="Citizenship choice">
    <button class="btn btn-outline" type="submit" name="citizen" value="no">
      No
    </button>
    <button class="btn btn-primary" type="submit" name="citizen" value="yes">
      Yes
    </button>
  </form>
</body>
</html>

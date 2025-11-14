<?php
session_start();
require __DIR__ . '/api/db.php';

$smsClient = null;
try {
    require __DIR__ . '/config/at.php';
    $smsClient = $sms ?? null;
} catch (Throwable $e) {
    $smsClient = null;
}

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

function issue_phone_otp(PDO $pdo, int $regId): array
{
    $code    = (string) random_int(100000, 999999);
    $hash    = password_hash($code, PASSWORD_DEFAULT);
    $expires = (new DateTimeImmutable('+10 minutes'))->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        UPDATE registration_flows
           SET phone_otp_hash       = :hash,
               phone_otp_expires_at = :expires,
               attempts_phone       = 0,
               step                 = 'phone_otp'
         WHERE id = :id
    ");
    $stmt->execute([
        ':hash'    => $hash,
        ':expires' => $expires,
        ':id'      => $regId,
    ]);

    return ['code' => $code];
}

function send_phone_sms($client, string $msisdn, string $code): ?string
{
    if (!$client) {
        return 'SMS client not configured (dev mode).';
    }

    try {
        $client->send([
            'to'      => [$msisdn],
            'message' => "Flowpesa verification code: {$code}",
        ]);
        return null;
    } catch (Throwable $e) {
        return $e->getMessage();
    }
}

$needsFreshOtp = isset($_GET['resend']) || empty($flow['phone_otp_hash']) || empty($flow['phone_otp_expires_at']) || $flow['step'] !== 'phone_otp';

if (!$needsFreshOtp) {
    try {
        $expiry = new DateTimeImmutable($flow['phone_otp_expires_at']);
        if ($expiry <= new DateTimeImmutable()) {
            $needsFreshOtp = true;
        }
    } catch (Throwable $e) {
        $needsFreshOtp = true;
    }
}

if (!$needsFreshOtp) {
    header('Location: verify-phone-code.php');
    exit;
}

$errorMsg = '';

try {
    $result   = issue_phone_otp($pdo, $regId);
    $smsError = send_phone_sms($smsClient, $flow['msisdn'], $result['code']);

    $params = [
        'sent'      => 1,
        'debug_otp' => $result['code'], // DEV ONLY: remove when shipping
    ];

    if ($smsError) {
        $params['sms_error'] = 1;
    }

    header('Location: verify-phone-code.php?' . http_build_query($params));
    exit;
} catch (Throwable $e) {
    $errorMsg = 'We could not send a code right now. Please try again in a moment.';
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
  <main class="screen" style="display:flex;align-items:center;justify-content:center;min-height:100vh;">
    <section class="card" style="max-width:420px;">
      <h1 class="heading">Hang tight</h1>
      <p class="subheading" style="margin-bottom:16px;">
        <?= htmlspecialchars($errorMsg ?: 'Redirecting you to enter the verification code…', ENT_QUOTES) ?>
      </p>
      <p class="subheading">
        <a class="link" href="register.php">Go back</a>
      </p>
    </section>
  </main>
</body>
</html>

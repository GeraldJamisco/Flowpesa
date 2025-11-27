<?php
// Start session only if not already active (register.php may start it)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/api/db.php';

// Normalize phone into +CCCXXXXXXXX format and avoid double country codes
function normalize_msisdn($countryCode, $phone)
{
    $ccDigits = preg_replace('/\D+/', '', $countryCode);
    $digits   = preg_replace('/\D+/', '', $phone);

    // Strip country code if user already typed it (e.g. +2567... with +256 selected)
    if ($ccDigits !== '' && str_starts_with($digits, $ccDigits)) {
        $digits = substr($digits, strlen($ccDigits));
    }

    // If UG (+256) and number starts with 0 -> drop leading 0
    if ($ccDigits === '256' && str_starts_with($digits, '0')) {
        $digits = substr($digits, 1);
    }

    return '+' . $ccDigits . $digits; // e.g. +2567xxxxxxx
}

$errors = [];
// For showing the last entered values if validation fails
$old_country = $_POST['country_code'] ?? '+256';
$old_phone   = $_POST['phone'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = trim($_POST['country_code'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $msisdn  = null;

    if ($country === '' || $phone === '') {
        $errors[] = 'Phone number is required.';
    } else {
        $msisdn = normalize_msisdn($country, $phone);

        // Basic validation – you can make this stricter later
        if (!preg_match('/^\+\d{6,15}$/', $msisdn)) {
            $errors[] = 'Enter a valid mobile number.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("\n                INSERT INTO registration_flows\n                    (country_code, phone, msisdn, step, phone_verified, email_verified,\n                     citizenship_country, citizenship_is_citizen, id_doc_type)\n                VALUES\n                    (:country_code, :phone, :msisdn, 'phone', 0, 0, '', '', '')\n            ");
            $stmt->execute([
                ':country_code' => $country,
                ':phone'        => $phone,
                ':msisdn'       => $msisdn,
            ]);

            $_SESSION['reg_id'] = (int) $pdo->lastInsertId();
            header('Location: verify-phone.php');
            exit;
        } catch (PDOException $e) {
            // Handle duplicate (msisdn, step='phone') by reusing the existing row
            if ($e->getCode() === '23000') {
                $existing = $pdo->prepare("\n                    SELECT id FROM registration_flows\n                     WHERE msisdn = :msisdn AND step = 'phone'\n                     ORDER BY id DESC\n                     LIMIT 1\n                ");
                $existing->execute([':msisdn' => $msisdn]);
                $row = $existing->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $_SESSION['reg_id'] = (int) $row['id'];

                    // Reset verification state so the flow restarts cleanly for this number
                    $pdo->prepare("\n                        UPDATE registration_flows\n                           SET country_code          = :country_code,\n                               phone                 = :phone,\n                               step                  = 'phone',\n                               phone_verified        = 0,\n                               email_verified        = 0,\n                               phone_otp_hash        = NULL,\n                               phone_otp_expires_at  = NULL,\n                               attempts_phone        = 0,\n                               email                 = NULL,\n                               email_otp_hash        = NULL,\n                               email_otp_expires_at  = NULL,\n                               attempts_email        = 0,\n                               temp_passcode_hash    = NULL\n                         WHERE id = :id\n                    ")->execute([
                        ':country_code' => $country,
                        ':phone'        => $phone,
                        ':id'           => $row['id'],
                    ]);

                    header('Location: verify-phone.php');
                    exit;
                }
            }

            error_log('Register phone error: ' . $e->getMessage());
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

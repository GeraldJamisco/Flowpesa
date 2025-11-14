<?php
session_start();
require 'config/db.php';   

// Helper: normalize phone (very simple for now)
function normalize_msisdn(string $countryCode, string $phone): string {
    $digits = preg_replace('/\D+/', '', $phone); // keep numbers only

    // If UG (+256) and number starts with 0 -> drop leading 0
    if ($countryCode === '+256' && str_starts_with($digits, '0')) {
        $digits = substr($digits, 1);
    }

    return $countryCode . $digits; // e.g. +2567xxxxxxx
}

$errors = [];
// For showing the last entered values if validation fails
$old_country = $_POST['country_code'] ?? '+256';
$old_phone   = $_POST['phone'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = trim($_POST['country_code'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');

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
        // Generate OTP
        $otp = random_int(100000, 999999);
        $otpHash = password_hash((string)$otp, PASSWORD_DEFAULT);

        // Expiry in 10 minutes
        $expiresAt = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

        try {
            // Insert registration flow
            $stmt = $pdo->prepare("
                INSERT INTO registration_flows
                    (country_code, phone, msisdn, phone_otp_hash, phone_otp_expires_at, step)
                VALUES
                    (:country_code, :phone, :msisdn, :otp_hash, :expires_at, 'phone')
            ");
            $stmt->execute([
                ':country_code' => $country,
                ':phone'        => $phone,
                ':msisdn'       => $msisdn,
                ':otp_hash'     => $otpHash,
                ':expires_at'   => $expiresAt
            ]);

            $regId = (int)$pdo->lastInsertId();
            $_SESSION['reg_id'] = $regId;

            // TODO: send SMS via AfricasTalking here
            // For now, **developer-only**: log OTP somewhere safe.
            // DO NOT show this to real users in production.
            // file_put_contents(__DIR__.'/storage/otp_debug.log', date('c')." $msisdn => $otp\n", FILE_APPEND);

            // TEMP: show OTP in URL so you can test easily (remove later)
            header('Location: verify-phone.php?msisdn=' . urlencode($msisdn) . '&debug_otp=' . $otp);
            exit;

        } catch (PDOException $e) {
            // In production: log $e->getMessage() instead of echoing
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>

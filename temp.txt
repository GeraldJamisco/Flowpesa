<?php
session_start();
require __DIR__ . '/api/db.php';

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
        try {
            $stmt = $pdo->prepare("
                INSERT INTO registration_flows
                    (country_code, phone, msisdn, step, phone_verified, email_verified)
                VALUES
                    (:country_code, :phone, :msisdn, 'phone', 0, 0)
            ");
            $stmt->execute([
                ':country_code' => $country,
                ':phone'        => $phone,
                ':msisdn'       => $msisdn,
            ]);

            $_SESSION['reg_id'] = (int) $pdo->lastInsertId();

            header('Location: verify-phone.php');
            exit;

        } catch (PDOException $e) {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>

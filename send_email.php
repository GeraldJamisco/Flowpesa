<?php
declare(strict_types=1);

require __DIR__ . '/api/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: verify-email.php');
    exit;
}

$email = trim((string)($_POST['email'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $qs = http_build_query([
        'error' => 'invalid',
        'email' => $email,
    ]);
    header('Location: verify-email.php?' . $qs);
    exit;
}

try {
    $token  = bin2hex(random_bytes(16));
    $code   = (string)random_int(100000, 999999);
    $now    = new DateTimeImmutable('now');
    $expiry = $now->modify('+15 minutes');

    $stmt = $pdo->prepare(
        'INSERT INTO verification_requests
           (request_token, verification_type, contact_value, code, status, attempt_count, last_sent_at, expires_at, created_at, updated_at)
         VALUES
           (:token, :type, :contact, :code, :status, 0, :sent_at, :expires_at, :created_at, :updated_at)'
    );

    $timestamp = $now->format('Y-m-d H:i:s');

    $stmt->execute([
        ':token'      => $token,
        ':type'       => 'email',
        ':contact'    => $email,
        ':code'       => $code,
        ':status'     => 'pending',
        ':sent_at'    => $timestamp,
        ':expires_at' => $expiry->format('Y-m-d H:i:s'),
        ':created_at' => $timestamp,
        ':updated_at' => $timestamp,
    ]);
} catch (Throwable $e) {
    header('Location: verify-email.php?error=server');
    exit;
}

$subject = 'Your Flowpesa email verification code';
$message = "Hi,\n\n"
         . "Your Flowpesa verification code is: {$code}\n\n"
         . "Enter this code in the app to verify your email.\n\n"
         . "If you did not request this, you can ignore this email.\n";
$headers = "From: Flowpesa <no-reply@flowpesa.com>\r\n";

$mailSent = @mail($email, $subject, $message, $headers);

$params = [
    'request' => $token,
    'sent'    => 1,
];

if (!$mailSent) {
    $params['mail_error'] = 1;
}

header('Location: verify-email.php?' . http_build_query($params));
exit;

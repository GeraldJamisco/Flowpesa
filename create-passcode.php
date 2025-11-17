<?php
// create-passcode.php
declare(strict_types=1);

session_start();
header('Content-Type: application/json');

// 1) Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Method not allowed',
    ]);
    exit;
}

// 2) User must be in registration flow (set earlier at verify-phone)
if (empty($_SESSION['reg_user_id'])) {
    http_response_code(401);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Registration session expired. Please start again.',
        'code'    => 'session_expired',
    ]);
    exit;
}

$regUserId = (int) $_SESSION['reg_user_id'];

// 3) Read JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request body',
    ]);
    exit;
}

$passcode = $data['passcode'] ?? '';
$passcode = trim((string) $passcode);

// 4) Validate: must be exactly 6 digits
if (!preg_match('/^\d{6}$/', $passcode)) {
    http_response_code(422);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Passcode must be exactly 6 digits.',
        'code'    => 'invalid_passcode',
    ]);
    exit;
}

// 5) Hash passcode (never store plain PIN)
$hash = password_hash($passcode, PASSWORD_DEFAULT);

if ($hash === false) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Could not hash passcode.',
    ]);
    exit;
}

// 6) Store in DB
require __DIR__ . '/api/db.php';   // adjust path if your db.php lives elsewhere

try {
    // Optional: only allow setting passcode if phone & email are already verified
    // You can drop this WHERE condition if you don’t want that strictness yet.
    $sql = "
        UPDATE user_registration_staging
        SET passcode_hash = :hash, updated_at = NOW()
        WHERE id = :id
          AND phone_verified = 1
          AND email_verified = 1
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':hash' => $hash,
        ':id'   => $regUserId,
    ]);

    if ($stmt->rowCount() < 1) {
        // Either user not found OR not yet verified phone/email
        http_response_code(409);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Unable to save passcode. Make sure phone and email are verified.',
            'code'    => 'cannot_set_passcode',
        ]);
        exit;
    }

    // 7) Success
    echo json_encode([
        'status'   => 'ok',
        'message'  => 'Passcode saved.',
        // Frontend can use this if you want:
        // 'redirect' => 'verify-id-type.php',
    ]);
    exit;

} catch (Throwable $e) {
    // In production, log $e->getMessage() somewhere safe instead of echoing
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while saving passcode.',
    ]);
    exit;
}

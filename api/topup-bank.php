<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit;
}

$uid = (int)$_SESSION['uid'];

$amount   = isset($_POST['amount']) ? (int)preg_replace('/\D/','', $_POST['amount']) : 0;
$bank     = trim($_POST['bank'] ?? '');
$reference= trim($_POST['reference'] ?? '');
$note     = trim($_POST['note'] ?? '');

if ($amount < 1000 || $bank === '' || $reference === '') {
  http_response_code(422);
  echo json_encode(['ok'=>false,'error'=>'invalid_request']); exit;
}

/* TODO:
 * - Save a “pending top-up” record (topup_requests) with user_id, amount, bank, reference, note, uploaded file path.
 * - Optionally move uploaded proof:
 */
if (!empty($_FILES['proof']['tmp_name'])) {
  $dir = __DIR__ . '/../uploads';
  if (!is_dir($dir)) @mkdir($dir, 0777, true);
  $ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
  $name = 'proof_'.$uid.'_'.time().'.'.$ext;
  move_uploaded_file($_FILES['proof']['tmp_name'], $dir.'/'.$name);
  // Save $name with the request
}

echo json_encode(['ok'=>true,'data'=>[
  'status' => 'pending_review',
  'amount' => $amount,
  'reference' => $reference
]]);

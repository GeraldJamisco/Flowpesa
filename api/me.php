<?php
declare(strict_types=1);
require __DIR__ . '/db.php';
session_start();                      
header('Content-Type: application/json');
// Must be logged in

if (empty($_SESSION['uid'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit;
}

$q = db()->prepare('SELECT name,email,phone,country,tier,kyc_pct FROM users WHERE id=? LIMIT 1');
$q->execute([$_SESSION['uid']]);
$me = $q->fetch();

if (!$me) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'not_found']); exit; }

echo json_encode(['ok'=>true,'data'=>[
  'name'   => $me['name'],
  'email'  => $me['email'],
  'phone'  => $me['phone'],
  'country'=> $me['country'],
  'tier'   => (int)$me['tier'],
  'kycPct' => (int)$me['kyc_pct'],
  // temporary demo values until ledger exists:
  'balance'=> 895500,
  'points' => 9500
]]);

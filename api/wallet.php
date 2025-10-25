<?php
declare(strict_types=1);
require __DIR__ . '/db.php';
session_start();

header('Content-Type: application/json');
if (empty($_SESSION['uid'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit;
}

/*
  Later we’ll pull these numbers from DB tables like wallet_txns, sacco_balances, etc.
  For now we send demo data so the JS/UI can render.
*/

$data = [
  'total'        => 2895500,
  'available'    => 1850000,
  'locked'       => 250000,
  'savingsTotal' => 795500,
  'mainWallet'   => 1850000,
  'walletDelta'  => 35000,
  'savedMonth'   => 320000,
  'spentMonth'   => 210000,
  'saccos' => [
    [
      'name'        => 'Kampala Traders SACCO',
      'member'      => 'KT-00921',
      'balance'     => 350000,
      'goal'        => 1000000,
      'last'        => 'Jun 18',
      'monthlySave' => 120000
    ],
    [
      'name'        => 'Mpigi Farmers SACCO',
      'member'      => 'MF-10234',
      'balance'     => 285500,
      'goal'        => 800000,
      'last'        => 'Jun 20',
      'monthlySave' => 90000
    ],
    [
      'name'        => 'Youth Builders SACCO',
      'member'      => 'YB-55812',
      'balance'     => 160000,
      'goal'        => 500000,
      'last'        => 'Jun 16',
      'monthlySave' => 110000
    ]
  ]
];

echo json_encode(['ok'=>true,'data'=>$data]);

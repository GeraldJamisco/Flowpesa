<?php
// api/wallet.php
declare(strict_types=1);
require __DIR__ . '/db.php';
session_start();

header('Content-Type: application/json');

// Must be logged in
if (empty($_SESSION['uid'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false, 'error'=>'unauthorized']); exit;
}

/*
  TODO (next iterations): pull from DB tables (wallet, sacco_balances, txns…)
  For now we return demo numbers so the UI renders live.
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
  'saccos'       => [
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
    ],
  ],
];

echo json_encode(['ok'=>true, 'data'=>$data]);

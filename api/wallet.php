<?php
// api/wallet.php — DB-powered
declare(strict_types=1);
require __DIR__ . '/db.php';
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit;
}

$uid = (int)$_SESSION['uid'];

try {
  $pdo = db();

  // --- Main wallet (available/locked) ---
  $stmt = $pdo->prepare('
    SELECT balance, locked
    FROM wallet_accounts
    WHERE user_id = ?
    LIMIT 1
  ');
  $stmt->execute([$uid]);
  $wa = $stmt->fetch() ?: ['balance'=>0, 'locked'=>0];

  $mainWallet = (float)$wa['balance'];
  $locked     = (float)$wa['locked'];
  $available  = max(0, $mainWallet - $locked);

  // --- SACCOs + balances + goals + monthly save ---
  // monthlySave = sum of credit txns to that SACCO in current month
  $stmt = $pdo->prepare("
    SELECT s.id, s.name, sm.member_code,
           COALESCE(sb.balance,0) AS balance,
           COALESCE(sm.goal,0)    AS goal,
           DATE_FORMAT(sb.last_activity, '%b %e') AS last_fmt,
           COALESCE(ms.monthly_save,0) AS monthly_save
    FROM sacco_memberships sm
    JOIN saccos s ON s.id = sm.sacco_id
    LEFT JOIN sacco_balances sb
      ON sb.user_id = sm.user_id AND sb.sacco_id = sm.sacco_id
    LEFT JOIN (
      SELECT sacco_id, SUM(amount) AS monthly_save
      FROM wallet_txns
      WHERE user_id = ? AND type='credit'
        AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
      GROUP BY sacco_id
    ) ms ON ms.sacco_id = sm.sacco_id
    WHERE sm.user_id = ?
    ORDER BY s.name
  ");
  $stmt->execute([$uid, $uid]);
  $saccos = [];
  $savingsTotal = 0.0;
  while ($row = $stmt->fetch()) {
    $saccos[] = [
      'name'        => $row['name'],
      'member'      => $row['member_code'],
      'balance'     => (float)$row['balance'],
      'goal'        => (float)$row['goal'],
      'last'        => $row['last_fmt'] ?: '',
      'monthlySave' => (float)$row['monthly_save'],
    ];
    $savingsTotal += (float)$row['balance'];
  }

  // --- Saved vs Spent this month (all wallet activity) ---
  $stmt = $pdo->prepare("
    SELECT
      COALESCE(SUM(CASE WHEN type='credit' THEN amount END),0) AS savedMonth,
      COALESCE(SUM(CASE WHEN type='debit'  THEN amount END),0) AS spentMonth
    FROM wallet_txns
    WHERE user_id = ?
      AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
  ");
  $stmt->execute([$uid]);
  $m = $stmt->fetch() ?: ['savedMonth'=>0, 'spentMonth'=>0];

  // --- Today’s delta (net inflow/outflow today) ---
  $stmt = $pdo->prepare("
    SELECT COALESCE(SUM(CASE WHEN type='credit' THEN amount ELSE -amount END),0) AS delta
    FROM wallet_txns
    WHERE user_id = ? AND DATE(created_at) = CURDATE()
  ");
  $stmt->execute([$uid]);
  $delta = (float)($stmt->fetch()['delta'] ?? 0);

  // --- Compose totals for header ---
  $total = $mainWallet + $savingsTotal; // household view: wallet + saccos

  echo json_encode(['ok'=>true, 'data'=>[
    'total'        => (float)$total,
    'available'    => (float)$available,
    'locked'       => (float)$locked,
    'savingsTotal' => (float)$savingsTotal,
    'mainWallet'   => (float)$mainWallet,
    'walletDelta'  => (float)$delta,
    'savedMonth'   => (float)$m['savedMonth'],
    'spentMonth'   => (float)$m['spentMonth'],
    'saccos'       => $saccos,
  ]]);

} catch (Throwable $e) {
  // Optional: log $e->getMessage() to a file
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'server_error']);
}


// the code below was to check for errors in the console since the data in trhe database was not showing at all it was for demo

// } catch (Throwable $e) {
//   http_response_code(500);
//   echo json_encode([
//     'ok'    => false,
//     'error' => 'server_error',
//     'detail'=> $e->getMessage()  // TEMP: remove in prod
//   ]);
// }

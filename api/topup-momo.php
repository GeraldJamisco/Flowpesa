<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$network = $input['network'] ?? null;
$msisdn  = trim((string)($input['msisdn'] ?? ''));
$amount  = (int)($input['amount'] ?? 0);
$reference = trim((string)($input['reference'] ?? ''));

if (!in_array($network, ['mtn','airtel'], true) || $amount < 1000 || $msisdn === '') {
  http_response_code(422);
  echo json_encode(['ok'=>false,'error'=>'invalid_request']); exit;
}

/*
 * TODO (when wiring real payments):
 * - Validate MSISDN format per network.
 * - Create a pending payment record (e.g., momo_topups table).
 * - Call PSP/Aggregator API to initiate STK Push/Collect.
 * - Store PSP reference and return it to client.
 * - Update wallet on callback/webhook when confirmed.
 */

// Mock behavior: pretend balance was 895,500 and we’ll add amount AFTER approval.
// For dev UX, we can return a “preview” newBalance so UI looks alive,
// but do NOT actually update DB here until payment confirms.
$current = 895500;
$new     = $current + $amount;

echo json_encode([
  'ok'   => true,
  'data' => [
    'status'       => 'prompt_sent',
    'provider'     => $network,
    'msisdn'       => $msisdn,
    'amount'       => $amount,
    'pspRef'       => 'SIM-' . time(),  // mock reference
    'newBalance'   => $new              // for UI preview only
  ]
]);

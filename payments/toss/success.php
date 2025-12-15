<?php
// /payments/toss/success.php
@ini_set('display_errors', 1);
@error_reporting(E_ALL);

include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/api/toss/_toss_config.php');

if (!defined('_GNUBOARD_')) die('common.php 로드 실패');

$paymentKey = $_GET['paymentKey'] ?? '';
$orderId    = $_GET['orderId'] ?? '';
$amount     = (int)($_GET['amount'] ?? 0);
$ru         = $_GET['ru'] ?? '/result.php';

if (!$paymentKey || !$orderId || $amount <= 0) die('잘못된 요청');

$order = sql_fetch("SELECT * FROM g5_lotto_toss_orders WHERE order_id='".sql_real_escape_string($orderId)."'");
if (!$order) die('주문을 찾을 수 없습니다.');

if ($order['status'] === 'DONE') {
  echo "이미 처리된 결제입니다. (orderId: ".htmlspecialchars($orderId).")";
  exit;
}
if ((int)$order['amount'] !== $amount) die('결제 금액 불일치');

// 1) Toss 결제 승인(Confirm) API 호출 (서버→서버)
$payload = json_encode([
  'paymentKey' => $paymentKey,
  'orderId'    => $orderId,
  'amount'     => $amount,
], JSON_UNESCAPED_UNICODE);

$ch = curl_init(TOSS_BASE_URL.'/v1/payments/confirm'); // 공식 Confirm 엔드포인트 :contentReference[oaicite:1]{index=1}
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Authorization: Basic '.base64_encode(TOSS_SECRET_KEY.':'),
    'Content-Type: application/json',
  ],
  CURLOPT_POSTFIELDS => $payload,
]);
$resBody = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($resBody === false) die('승인요청 실패: '.$curlErr);

$resJson = json_decode($resBody, true);

// 2) 승인 실패 처리
if ($httpCode < 200 || $httpCode >= 300) {
  $code = $resJson['code'] ?? 'CONFIRM_FAIL';
  $msg  = $resJson['message'] ?? '결제 승인 실패';

  sql_query("UPDATE g5_lotto_toss_orders
             SET status='FAIL', fail_code='".sql_real_escape_string($code)."',
                 fail_message='".sql_real_escape_string($msg)."',
                 raw_json='".sql_real_escape_string($resBody)."'
             WHERE order_id='".sql_real_escape_string($orderId)."'");

  die("결제 승인 실패: ".htmlspecialchars($msg));
}

// 3) 승인 성공 → 크레딧 충전 (중복 방지 + 트랜잭션)
require_once G5_LIB_PATH . '/lotto_credit.lib.php';

sql_query("START TRANSACTION");

// 주문 재확인(락은 InnoDB에서 row-lock을 위해 FOR UPDATE 권장)
$order2 = sql_fetch("SELECT * FROM g5_lotto_toss_orders WHERE order_id='".sql_real_escape_string($orderId)."' FOR UPDATE");
if (!$order2) { sql_query("ROLLBACK"); die('주문 재조회 실패'); }
if ($order2['status'] === 'DONE') { sql_query("COMMIT"); echo "이미 처리된 결제입니다."; exit; }

$mb_id = $order2['mb_id'];
$credit_qty = (int)$order2['credit_qty'];
$product_code = $order2['product_code'] ?? '';

// 상품명 매핑
$product_names = [
    'CREDIT_5' => '크레딧 5회 충전',
    'CREDIT_20' => '크레딧 20회 충전',
    'CREDIT_50' => '크레딧 50회 충전',
];
$orderName = $product_names[$product_code] ?? "크레딧 {$credit_qty}회 충전";

// 전용 크레딧 충전 함수 사용 (트랜잭션 내부에서 실행)
// lotto_charge_credit 함수는 내부적으로 트랜잭션을 사용하므로
// 여기서는 직접 SQL로 처리하되, 로그는 lotto_credit_log 테이블에 기록

// 크레딧 row 확보 (없으면 생성)
$creditRow = sql_fetch("SELECT * FROM g5_lotto_credit WHERE mb_id='".sql_real_escape_string($mb_id)."' FOR UPDATE");
if (!$creditRow) {
  sql_query("INSERT INTO g5_lotto_credit (mb_id, free_uses, credit_balance, created_at, updated_at)
             VALUES ('{$mb_id}', 2, 0, NOW(), NOW())");
  $creditRow = sql_fetch("SELECT * FROM g5_lotto_credit WHERE mb_id='".sql_real_escape_string($mb_id)."' FOR UPDATE");
}

$before_balance = (int)$creditRow['credit_balance'];
$after_balance = $before_balance + $credit_qty;

// ✅ credit_balance에 충전 (유료 크레딧)
sql_query("UPDATE g5_lotto_credit
           SET credit_balance={$after_balance}, updated_at=NOW()
           WHERE mb_id='".sql_real_escape_string($mb_id)."'");

// 로그 기록
sql_query("INSERT INTO g5_lotto_credit_log
          (mb_id, change_type, amount, before_balance, after_balance, memo, ref_key, ip, created_at)
          VALUES
          ('{$mb_id}', 'charge', {$credit_qty}, {$before_balance}, {$after_balance},
           '토스 결제 크레딧 충전 - {$orderName}', '".sql_real_escape_string($orderId)."',
           '".sql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? '')."', NOW())");

// 주문 DONE 마감
sql_query("UPDATE g5_lotto_toss_orders
           SET status='DONE', payment_key='".sql_real_escape_string($paymentKey)."',
               approved_at=NOW(),
               raw_json='".sql_real_escape_string($resBody)."'
           WHERE order_id='".sql_real_escape_string($orderId)."'");

sql_query("COMMIT");

// ✅ result.php로 복귀 (토스트/잔액갱신은 result.php에서 처리)
$sep = (strpos($ru, '?') !== false) ? '&' : '?';
header('Location: '.$ru.$sep.'pay=success&orderId='.urlencode($orderId));
exit;
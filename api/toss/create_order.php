<?php
// /api/toss/create_order.php
@ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
include_once(__DIR__.'/_toss_config.php');

if (!defined('_GNUBOARD_')) { echo json_encode(['ok'=>false,'message'=>'common.php 로드 실패']); exit; }
if (!$is_member) { echo json_encode(['ok'=>false,'message'=>'로그인 후 이용 가능합니다.']); exit; }

$raw = file_get_contents("php://input");
$body = json_decode($raw, true);
$product = $body['product'] ?? '';

$PRODUCTS = [
  'CREDIT_5'  => ['amount'=>1000, 'credit'=>5,  'name'=>'크레딧 5회 충전'],
  'CREDIT_20' => ['amount'=>2500, 'credit'=>20, 'name'=>'크레딧 20회 충전'],
  'CREDIT_50' => ['amount'=>5000, 'credit'=>50, 'name'=>'크레딧 50회 충전'],
];

if (!isset($PRODUCTS[$product])) {
  echo json_encode(['ok'=>false,'message'=>'잘못된 상품입니다.']); exit;
}

$mb_id = $member['mb_id'];
$amount = (int)$PRODUCTS[$product]['amount'];
$credit = (int)$PRODUCTS[$product]['credit'];
$orderName = $PRODUCTS[$product]['name'];

// 고유 orderId 생성
$orderId = 'LC'.date('YmdHis').bin2hex(random_bytes(6));

$sql = "INSERT INTO g5_lotto_toss_orders
        (order_id, mb_id, product_code, amount, credit_qty, status, created_at)
        VALUES
        ('{$orderId}', '{$mb_id}', '{$product}', {$amount}, {$credit}, 'READY', NOW())";
sql_query($sql);

$site = 'https://lottoinsight.ai';

// 결제 완료/실패 후 돌아갈 페이지
$returnUrl  = '/result.php'; // 필요시 실제 경로로 수정
$successUrl = $site.'/payments/toss/success.php?ru='.urlencode($returnUrl);
$failUrl    = $site.'/payments/toss/fail.php?ru='.urlencode($returnUrl);

$customerName  = $member['mb_name'] ?: ($member['mb_nick'] ?: $mb_id);
$customerEmail = $member['mb_email'] ?? '';

echo json_encode([
  'ok' => true,
  'clientKey' => TOSS_CLIENT_KEY,
  'orderId' => $orderId,
  'amount' => $amount,
  'orderName' => $orderName,
  'customerName' => $customerName,
  'customerEmail' => $customerEmail,
  'successUrl' => $successUrl,
  'failUrl' => $failUrl,
], JSON_UNESCAPED_UNICODE);

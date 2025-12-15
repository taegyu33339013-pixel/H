<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
if (!defined('_GNUBOARD_')) exit;

header('Content-Type: application/json; charset=utf-8');

if (empty($is_member) || empty($member['mb_id'])) {
  echo json_encode(['success'=>false,'reason'=>'NOT_LOGGED_IN'], JSON_UNESCAPED_UNICODE);
  exit;
}

$package_code = trim($_POST['package_code'] ?? '');
$method       = trim($_POST['method'] ?? 'card'); // kakao/toss/card 등

// ✅ 서버 고정 패키지 정의 (원하시면 가격/구성만 바꾸면 됨)
$PACKAGES = [
  'P5'  => ['credits'=>5,  'bonus'=>0,  'amount'=>1000, 'name'=>'오늘로또 크레딧 5회'],
  'P15' => ['credits'=>15, 'bonus'=>5,  'amount'=>2500, 'name'=>'오늘로또 크레딧 15+5회'],
  'P35' => ['credits'=>35, 'bonus'=>15, 'amount'=>5000, 'name'=>'오늘로또 크레딧 35+15회'],
];

if (!isset($PACKAGES[$package_code])) {
  echo json_encode(['success'=>false,'reason'=>'INVALID_PACKAGE'], JSON_UNESCAPED_UNICODE);
  exit;
}

$p = $PACKAGES[$package_code];

// TossPayments orderId 길이 고려해서 64자 이내로 생성
try {
  $rand = bin2hex(random_bytes(8));
} catch (Exception $e) {
  $rand = uniqid();
}
$order_id = 'LI-'.$member['mb_id'].'-'.$rand;
$order_id = substr($order_id, 0, 64);

$mb_id = sql_real_escape_string($member['mb_id']);
$order_id_sql = sql_real_escape_string($order_id);
$method_sql = sql_real_escape_string($method);
$package_sql = sql_real_escape_string($package_code);

$sql = "
  INSERT INTO g5_lotto_charge_order
  SET mb_id='{$mb_id}',
      order_id='{$order_id_sql}',
      package_code='{$package_sql}',
      credits='".(int)$p['credits']."',
      bonus='".(int)$p['bonus']."',
      amount='".(int)$p['amount']."',
      method='{$method_sql}',
      status='READY',
      requested_at='".G5_TIME_YMDHIS."'
";
$res = sql_query($sql, false);

if (!$res) {
  echo json_encode(['success'=>false,'reason'=>'DB_ERROR'], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode([
  'success'   => true,
  'orderId'   => $order_id,
  'amount'    => (int)$p['amount'],
  'orderName' => $p['name'],
], JSON_UNESCAPED_UNICODE);

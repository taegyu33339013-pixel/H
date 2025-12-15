<?php
// /payments/toss/fail.php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');

$code   = $_GET['code'] ?? '';
$msg    = $_GET['message'] ?? '';
$orderId= $_GET['orderId'] ?? '';
$ru     = $_GET['ru'] ?? '/result.php';

if ($orderId) {
  sql_query("UPDATE g5_lotto_toss_orders
             SET status='FAIL',
                 fail_code='".sql_real_escape_string($code)."',
                 fail_message='".sql_real_escape_string($msg)."'
             WHERE order_id='".sql_real_escape_string($orderId)."'");
}

$sep = (strpos($ru, '?') !== false) ? '&' : '?';
header('Location: '.$ru.$sep.'pay=fail&message='.urlencode($msg ?: '결제 실패'));
exit;

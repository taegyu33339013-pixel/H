<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');

$code    = $_GET['code'] ?? '';
$message = $_GET['message'] ?? '';
$orderId = $_GET['orderId'] ?? '';

if ($orderId) {
    $orderIdEsc = sql_real_escape_string($orderId);
    sql_query("UPDATE g5_lotto_payment_log SET
        status='FAIL',
        fail_code='".sql_real_escape_string($code)."',
        fail_message='".sql_real_escape_string($message)."'
      WHERE order_id='{$orderIdEsc}' AND status='READY'");
}

echo "결제가 실패했습니다. ".htmlspecialchars($message ?: $code);

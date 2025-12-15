<?php
// /www/toss/fail.php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');

// 토스 리디렉트 파라미터
$code     = $_GET['code']      ?? '';
$msg      = $_GET['message']   ?? '결제에 실패했습니다.';
$orderId  = $_GET['orderId']   ?? '';
$amount   = $_GET['amount']    ?? ''; // 참고용

// 결제 실패 로그를 g5_payments 에 남김 (가능한 경우)
if ($orderId) {
    $safeOrderId = sql_real_escape_string($orderId);
    $safeCode    = sql_real_escape_string($code);
    $safeMsg     = sql_real_escape_string($msg);

    // raw_log 에 실패 정보 append + 상태 FAILED 로 표시
    sql_query("
        UPDATE g5_payments
           SET status    = 'FAILED',
               raw_log   = CONCAT(
                             IFNULL(raw_log, ''),
                             '[TOSS_FAIL code={$safeCode}, msg={$safeMsg}, amt={$amount}]'
                         ),
               updated_at= NOW()
         WHERE order_id  = '{$safeOrderId}'
         LIMIT 1
    ");
}

// 사용자 안내 문구 (PG쪽 상세 메시지는 그대로 쓰되, 너무 기술적이어도 무방)
alert($msg, G5_URL.'/');

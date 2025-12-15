<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');

$paymentKey = $_GET['paymentKey'] ?? '';
$orderId    = $_GET['orderId'] ?? '';
$amount     = (int)($_GET['amount'] ?? 0);

if (!$paymentKey || !$orderId || $amount <= 0) {
    exit('잘못된 요청입니다.');
}

// ✅ TODO: 라이브 Secret Key 넣기(절대 프론트 노출 금지)
$TOSS_SECRET_KEY = 'LIVE_SECRET_KEY_HERE';

// 1) 주문 조회
$orderIdEsc = sql_real_escape_string($orderId);
$row = sql_fetch("SELECT * FROM g5_lotto_payment_log WHERE order_id='{$orderIdEsc}'");
if (!$row) exit('주문이 없습니다.');

// 2) 금액 검증 (클라이언트 amount 신뢰 금지)
if ((int)$row['amount'] !== $amount) exit('금액 불일치');

// ✅ 이미 DONE이면 중복 지급 방지
if ($row['status'] === 'DONE') {
    echo "이미 처리된 결제입니다.";
    exit;
}

// 3) Toss confirm 호출
$payload = json_encode([
    'paymentKey' => $paymentKey,
    'orderId' => $orderId,
    'amount' => $amount
], JSON_UNESCAPED_UNICODE);

$ch = curl_init('https://api.tosspayments.com/v1/payments/confirm');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic '.base64_encode($TOSS_SECRET_KEY.':'),
        'Content-Type: application/json',
        // 멱등키(권장): confirm 중복 호출 방지
        'Idempotency-Key: '.$orderId
    ],
    CURLOPT_POSTFIELDS => $payload,
]);
$resBody = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$confirm = json_decode($resBody, true);

// 4) 승인 실패 처리
if (!($httpCode >= 200 && $httpCode < 300) || !is_array($confirm)) {
    $code = $confirm['code'] ?? 'CONFIRM_FAILED';
    $msg  = $confirm['message'] ?? '결제 승인 실패';

    sql_query("UPDATE g5_lotto_payment_log SET
        status='FAIL',
        payment_key='".sql_real_escape_string($paymentKey)."',
        fail_code='".sql_real_escape_string($code)."',
        fail_message='".sql_real_escape_string($msg)."',
        raw_confirm_json='".sql_real_escape_string($resBody)."'
      WHERE order_id='{$orderIdEsc}'");

    exit("결제 승인 실패: ".htmlspecialchars($msg));
}

// 5) 승인 성공 → DB DONE + 크레딧 지급(트랜잭션)
$mb_id = $row['mb_id'];
$creditToAdd = (int)$row['credit_amount'];
$method = $confirm['method'] ?? '카드';

sql_query("START TRANSACTION");

try {
    // (A) 결제로그 DONE 처리 (동시에 1회만 처리되도록 조건 추가)
    $updated = sql_query("UPDATE g5_lotto_payment_log SET
        payment_key='".sql_real_escape_string($paymentKey)."',
        status='DONE',
        method='".sql_real_escape_string($method)."',
        approved_at=NOW(),
        raw_confirm_json='".sql_real_escape_string($resBody)."'
      WHERE order_id='{$orderIdEsc}' AND status='READY'");

    // 만약 READY가 아니어서 업데이트 안 됐다면(경합) → 롤백 후 종료
    // (gnuboard의 sql_query는 영향행을 바로 주지 않을 수 있어 여기서 한 번 더 상태확인)
    $chk = sql_fetch("SELECT status FROM g5_lotto_payment_log WHERE order_id='{$orderIdEsc}'");
    if (!$chk || $chk['status'] !== 'DONE') {
        sql_query("ROLLBACK");
        exit("중복 처리 감지: 결제 상태 갱신 실패");
    }

    // (B) wallet 행 잠금(없으면 생성)
    $mbEsc = sql_real_escape_string($mb_id);

    // row lock
    $w = sql_fetch("SELECT * FROM g5_lotto_credit_wallet WHERE mb_id='{$mbEsc}' FOR UPDATE");

    if (!$w) {
        sql_query("INSERT INTO g5_lotto_credit_wallet (mb_id, balance) VALUES ('{$mbEsc}', 0)");
        $before = 0;
    } else {
        $before = (int)$w['balance'];
    }

    $after = $before + $creditToAdd;

    // (C) wallet 업데이트
    sql_query("UPDATE g5_lotto_credit_wallet SET balance={$after} WHERE mb_id='{$mbEsc}'");

    // (D) credit_log 기록 (사용 중인 테이블 구조 그대로 맞추세요)
    // 아래는 질문에 있던 컬럼 기준(change_type, amount, before_balance, after_balance, memo, ref_key, ip)
    $ip = sql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
    $memo = sql_real_escape_string("토스 카드 결제 충전({$row['product_code']})");
    $refKey = sql_real_escape_string($orderId);

    sql_query("INSERT INTO g5_lotto_credit_log
      (mb_id, change_type, amount, before_balance, after_balance, memo, ref_key, ip, created_at)
      VALUES
      ('{$mbEsc}', 'charge', {$creditToAdd}, {$before}, {$after}, '{$memo}', '{$refKey}', '{$ip}', NOW())
    ");

    sql_query("COMMIT");

} catch (Exception $e) {
    sql_query("ROLLBACK");
    exit("처리 중 오류: ".$e->getMessage());
}

echo "결제가 완료되었습니다. 크레딧이 충전되었습니다.";

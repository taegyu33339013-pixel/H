<?php
// /www/toss/create.php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
header('Content-Type: application/json; charset=utf-8');

// 로그인/권한 체크가 필요하면 여기에 추가하세요.

// 클라이언트에서 넘어온 값들
$match_id   = intval($_POST['match_id']  ?? 0);
$vendor_id  = intval($_POST['vendor_id'] ?? 0);
$log_id     = intval($_POST['log_id']    ?? 0);
$req_amount = intval($_POST['amount']    ?? 0); // 참고용(검증/로그용)

// 기본 파라미터 검증
if (!$match_id || !$vendor_id || !$log_id) {
    echo json_encode([
        'ok'      => false,
        'message' => '잘못된 요청입니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 견적/전문가 조회(서버 금액 확정용)
$row = sql_fetch("
    SELECT 
        ml.match_id,
        ml.log_id,
        ml.vendor_id,
        ml.vendor_price,
        m.mb_id,
        m.mb_name,
        m.mb_email
    FROM g5_clean_match_log AS ml
    LEFT JOIN g5_member AS m ON ml.vendor_id = m.mb_no
    WHERE ml.match_id = '{$match_id}'
      AND ml.vendor_id = '{$vendor_id}'
      AND ml.log_id    = '{$log_id}'
    LIMIT 1
");

// 유효성 체크
if (!$row || (int)$row['vendor_price'] <= 0) {
    echo json_encode([
        'ok'      => false,
        'message' => '유효한 견적을 찾지 못했습니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ★ 결제 금액은 무조건 서버(DB) 기준으로 사용
$amount = (int)$row['vendor_price'];

// (선택) 프론트에서 넘어온 금액과 다른 경우 로그 남기고 싶으면 여기에 처리
// if ($req_amount > 0 && $req_amount !== $amount) {
//     // 예: error_log("TOSS amount mismatch: req={$req_amount}, db={$amount}, match_id={$match_id}");
// }

// 주문 ID/이름 생성
$orderId   = 'ZJA-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
$orderName = '집잘알 예약 - ' . $row['log_id'] . '_' . ($row['mb_name'] ?: '전문가');

// 고객 정보 (로그인 회원 우선, 없으면 전문가 이메일로 폴백)
$customerName  = $member['mb_name']  ?? '고객';
$customerEmail = $member['mb_email'] ?? ($row['mb_email'] ?? '');

// 초기 결제 레코드 (스키마에 맞게 컬럼만 유지)
sql_query("
    INSERT INTO g5_payments
    SET pg         = 'toss',
        order_id   = '" . sql_real_escape_string($orderId) . "',
        match_id   = '{$match_id}',
        amount     = '{$amount}',
        method     = 'CARD',
        status     = 'INIT',
        created_at = NOW()
");

// 콜백 URL (절대경로: G5_URL은 common.php에서 정의됨)
$successUrl = G5_URL . '/toss/success.php';
$failUrl    = G5_URL . '/toss/fail.php';

// 프론트에 응답
echo json_encode([
    'ok'            => true,
    'orderId'       => $orderId,
    'amount'        => $amount,
    'orderName'     => $orderName,
    'customerName'  => $customerName,
    'customerEmail' => $customerEmail,
    'successUrl'    => $successUrl,
    'failUrl'       => $failUrl
], JSON_UNESCAPED_UNICODE);

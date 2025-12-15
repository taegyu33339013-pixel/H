<?php
// /www/toss/success.php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');

// 토스 리디렉트 파라미터
$paymentKey = $_GET['paymentKey'] ?? '';
$orderId    = $_GET['orderId'] ?? '';
$amountCli  = (int)($_GET['amount'] ?? 0);

// 필수 파라미터 검증
if (!$paymentKey || !$orderId) {
    alert('유효하지 않은 결제 요청입니다.');
}

// 우리 주문 재확인
$pay = sql_fetch("
    SELECT *
    FROM g5_payments
    WHERE order_id = '".sql_real_escape_string($orderId)."'
    LIMIT 1
");
if (!$pay) {
    alert('주문 정보가 없습니다.');
}

// 이미 완료된 주문이면 재요청은 그냥 완료페이지로 돌려보냄 (idempotent)
if ($pay['status'] === 'DONE') {
    goto_url(G5_URL.'/order/complete.php?oid='.urlencode($orderId));
}

// 서버 기준 결제 금액 (create.php 에서 저장한 전체 금액)
$amount = (int)$pay['amount']; // **서버 금액이 진실**

// 리디렉트 쿼리스트링 금액과 다르면 실패 처리 + 로그 남김
if ($amountCli > 0 && $amountCli !== $amount) {
    // 금액 위변조/버그 의심 케이스: 결제 진행 막고 로그 남김
    sql_query("
        UPDATE g5_payments
           SET status = 'FAILED',
               raw_log = CONCAT(
                   IFNULL(raw_log, ''),
                   '[AMOUNT_MISMATCH cli={$amountCli}, srv={$amount}]'
               ),
               updated_at = NOW()
         WHERE order_id = '".sql_real_escape_string($orderId)."'
         LIMIT 1
    ");
    alert('유효하지 않은 결제 금액입니다. 관리자에게 문의 바랍니다.');
}

// 토스 시크릿 키 (검수/실서버에서 교체 필요)
$secretKey = 'live_sk_Z1aOwX7K8mYPbn4Znyp08yQxzvNP'; // 실서버는 live 키, 환경변수/설정파일로 분리 권장
$auth = base64_encode($secretKey.':');

// 토스 결제 승인 요청
$ch = curl_init('https://api.tosspayments.com/v1/payments/confirm');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic '.$auth,
        'Content-Type: application/json'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'paymentKey' => $paymentKey,
        'orderId'    => $orderId,
        'amount'     => $amount  // 서버 기준 금액으로 승인
    ], JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 10
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($res, true);

if ($http === 200 && ($data['status'] ?? '') === 'DONE') {
    // ─────────────────────────────────────
    // 1) 결제 확정 DB 업데이트 (g5_payments)
    // ─────────────────────────────────────
    sql_query("
        UPDATE g5_payments
           SET status      = 'DONE',
               payment_key = '".sql_real_escape_string($paymentKey)."',
               method      = '".sql_real_escape_string($data['method'] ?? 'CARD')."',
               receipt_url = '".sql_real_escape_string($data['receipt']['url'] ?? '')."',
               raw_log     = '".sql_real_escape_string(json_encode($data, JSON_UNESCAPED_UNICODE))."',
               updated_at  = NOW()
         WHERE order_id   = '".sql_real_escape_string($orderId)."'
         LIMIT 1
    ");

    // ─────────────────────────────────────
    // 2) 매칭 확정 처리 (중복 확정 방지)
    //    - 같은 log_id 에 이미 is_selected='Y' 있으면 건너뜀
    //    - **여기서 업체 확정 + 알림톡 발송(select_expert.php 로직 이관)**
    // ─────────────────────────────────────
    $match_id = (int)$pay['match_id'];

    if ($match_id > 0) {
        // 같은 log_id 에 이미 확정된 업체가 있는지 체크
        $already = sql_fetch("
            SELECT COUNT(*) AS c
            FROM g5_clean_match_log
            WHERE log_id = (
                SELECT log_id
                FROM g5_clean_match_log
                WHERE match_id = {$match_id}
                LIMIT 1
            )
              AND is_selected = 'Y'
        ");

        $just_selected = false;

        if ((int)$already['c'] === 0) {
            // 이번 결제 건으로 업체 확정
            sql_query("
                UPDATE g5_clean_match_log
                   SET is_selected = 'Y',
                       selected_at = NOW()
                 WHERE match_id = {$match_id}
                 LIMIT 1
            ");
            $just_selected = true;
        }

        // 확정/정산 공통으로 쓸 매칭 정보 로드
        $ml = sql_fetch("
            SELECT ml.match_id, ml.log_id, ml.vendor_id, ml.vendor_price
            FROM g5_clean_match_log AS ml
            WHERE ml.match_id = {$match_id}
            LIMIT 1
        ");

        if ($ml) {
            $log_id    = (int)$ml['log_id'];
            $vendor_id = (int)$ml['vendor_id'];

            // ─────────────────────────────────────
            // 2-1) (기존 select_expert.php 의 카카오 알림톡 로직)
            //      결제로 인해 '처음으로' 업체 확정된 경우에만 발송
            // ─────────────────────────────────────
            if ($just_selected && $vendor_id > 0) {
                // 확정 메세지 (업체)
                include_once(G5_THEME_PATH . '/send_biztalk_confirm_vendor.php');
                // 권팀장님 알림
                include_once(G5_THEME_PATH . '/send_biztalk_notify_staff.php');
            }

            // ─────────────────────────────────────────────
            // 3) 결제 확정 이후: 벤더/로그/정산 파생값을 g5_payments에 기록
            //    (관리자 정산 참고용으로만 사용, 실제 정산은 관리자 화면에서 수동 처리)
            // ─────────────────────────────────────────────
            // 수수료 규칙(가변): 여기서 계산해서 g5_payments 에 기록만 해둠
            //  - 실제 지급/정산은 관리자에서 이 값을 참고해서 처리
            $FEE_RATE = 10.0;   // 10% 수수료율
            $MIN_FEE  = 0;      // 최소 수수료 (필요시 3000 등으로 변경)

            // 1) 수수료 금액 계산
            $fee_calc = (int)floor($amount * ($FEE_RATE / 100));
            if ($MIN_FEE > 0 && $fee_calc < $MIN_FEE) {
                $fee_calc = $MIN_FEE;
            }

            // 2) 업체에게 실제로 지급할 금액 = 총 결제금액 - 수수료
            $payout = (int)max(0, $amount - $fee_calc);

            sql_query("
                UPDATE g5_payments
                   SET vendor_id     = '{$vendor_id}',
                       log_id        = '{$log_id}',
                       fee_rate      = '{$FEE_RATE}',
                       fee_amount    = '{$fee_calc}',
                       payout_amount = '{$payout}'
                 WHERE order_id     = '".sql_real_escape_string($orderId)."'
                 LIMIT 1
            ");
        }
    }

    // ─────────────────────────────────────
    // 4) 완료 페이지로 이동
    // ─────────────────────────────────────
    goto_url(G5_URL.'/toss/complete.php?oid='.urlencode($orderId));

} else {
    // 토스 응답 실패 → 결제 실패 처리
    sql_query("
        UPDATE g5_payments
           SET status    = 'FAILED',
               raw_log   = '".sql_real_escape_string($res)."',
               updated_at= NOW()
         WHERE order_id  = '".sql_real_escape_string($orderId)."'
         LIMIT 1
    ");
    alert('결제 확정에 실패했습니다. 다시 시도해주세요.', G5_URL.'/');
}

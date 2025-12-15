<?php
// /toss/ready.php
include_once('../common.php');
@session_start();

// ===== 1) 입력값 수신/검증 =====
$required = ['itemname','vendor_id','log_id','match_id','username','userid','usertel','useremail','useragent','price','amount','orderid','user_phone','payment_method'];
$data = [];
foreach ($required as $k) { $data[$k] = $_POST[$k] ?? ''; }

$amount  = (int)$data['amount'];
$orderId = preg_replace('/[^A-Za-z0-9_\-\.]/','', $data['orderid']); // 안전 필터
if ($amount <= 0 || !$orderId) {
  alert('잘못된 결제 요청입니다.'); exit;
}

// 선택: 공급자/주문자 정보 더 가져오기(예: 업체명 렌더링용)
$vendor_name = '';
if (!empty($data['vendor_id'])) {
    $v = sql_fetch("SELECT mb_name FROM g5_member WHERE mb_no = '".intval($data['vendor_id'])."'");
    $vendor_name = $v['mb_name'] ?? '선택된 업체';
}

// ===== 2) 세션에 결제컨텍스트 저장(서버 검증용) =====
$_SESSION['toss_pay_ctx'] = [
  'orderId'   => $orderId,
  'amount'    => $amount,
  'match_id'  => (int)$data['match_id'],
  'vendor_id' => (int)$data['vendor_id'],
  'log_id'    => (int)$data['log_id'],
  'user_name' => (string)$data['username'],
  'user_phone'=> (string)$data['user_phone'],
  'user_email'=> (string)$data['useremail'],
  'created_at'=> date('Y-m-d H:i:s'),
];

// ===== 3) 토스 클라이언트 키/리다이렉트 URL =====
// ※ 실제 값으로 교체하세요.
$TOSS_CLIENT_KEY = 'test_ck_xxxxxxxxxxxxxxxxxxxxx'; // 테스트/라이브 구분
$BASE_URL        = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$SUCCESS_URL     = $BASE_URL . '/toss/success.php';
$FAIL_URL        = $BASE_URL . '/toss/fail.php';

// 주문명 구성
$orderName = '업체 확정 결제료 - ' . ($vendor_name ?: '집잘알');

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>토스페이 결제 준비중…</title>
<script src="https://js.tosspayments.com/v1"></script>
<style>
  body{font-family:system-ui,-apple-system,'Segoe UI',Roboto,'Noto Sans KR',sans-serif;padding:24px}
  .card{max-width:520px;margin:40px auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 6px 16px rgba(0,0,0,.06)}
  .muted{color:#64748b;font-size:14px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border:1px solid #e5e7eb;border-radius:10px;background:#111827;color:#fff;font-weight:700;cursor:pointer}
</style>
</head>
<body>
  <div class="card">
    <h2 style="margin:0 0 8px">토스페이로 이동합니다</h2>
    <p class="muted">창이 자동으로 열리지 않으면 아래 버튼을 눌러주세요.</p>
    <button class="btn" id="payBtn">토스페이 결제하기</button>
  </div>

<script>
(function(){
  const clientKey = "<?= htmlspecialchars($TOSS_CLIENT_KEY, ENT_QUOTES) ?>";
  const tossPayments = TossPayments(clientKey);

  const params = {
    amount: <?= (int)$amount ?>,
    orderId: "<?= htmlspecialchars($orderId, ENT_QUOTES) ?>",
    orderName: "<?= htmlspecialchars($orderName, ENT_QUOTES) ?>",
    successUrl: "<?= htmlspecialchars($SUCCESS_URL, ENT_QUOTES) ?>",
    failUrl: "<?= htmlspecialchars($FAIL_URL, ENT_QUOTES) ?>",
    customerEmail: "<?= htmlspecialchars($data['useremail'], ENT_QUOTES) ?>",
    customerName: "<?= htmlspecialchars($data['username'], ENT_QUOTES) ?>",
    customerMobilePhone: "<?= htmlspecialchars($data['user_phone'], ENT_QUOTES) ?>",
  };

  function go(){
    // 결제수단 고정: '토스페이'
    tossPayments.requestPayment('토스페이', params).catch(function (error) {
      // 사용자가 취소/닫기한 경우 등
      alert('결제가 취소되었거나 실패했습니다. 다시 시도해 주세요.');
      console.error(error);
      //window.location.href = "<?= G5_URL ?>/match_result_20250922.php?log_id=<?= (int)$data['log_id'] ?>&user_id=<?= urlencode($_POST['userid'] ?? '') ?>";
	  history.go(-1);
    });
  }

  // auto trigger
  window.addEventListener('load', go);
  document.getElementById('payBtn').addEventListener('click', go);
})();
</script>
</body>
</html>

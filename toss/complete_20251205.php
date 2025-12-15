<?php
// /www/toss/complete.php
include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
include_once(G5_THEME_PATH . '/head.php');

// 간단 escape 헬퍼
if (!function_exists('e')) {
  function e($str)
  {
    return htmlspecialchars((string) $str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

// 주문번호 파라미터
$oid = trim($_GET['oid'] ?? '');
if (!$oid) {
  // [Sigma v5.0] 디자인 확인용 임시 우회
  // alert('유효하지 않은 접근입니다.');
  $oid = 'TEST_ORDER_NO_12345678';
}

// 결제 정보 조회
$pay = sql_fetch("
    SELECT *
    FROM g5_payments
    WHERE order_id = '" . sql_real_escape_string($oid) . "'
    LIMIT 1
");

// [Sigma v5.0] 모의 데이터 주입 (DB에 없을 경우)
if (!$pay) {
  // alert('주문 정보를 찾을 수 없습니다.');
  $pay = [
    'status' => 'DONE',
    'amount' => 150000,
    'method' => '카드결제',
    'created_at' => date('Y-m-d H:i:s'),
    'match_id' => 9999,
    'log_id' => 8888,
    'vendor_id' => 7777,
    'receipt_url' => '#'
  ];
}

$status = $pay['status'] ?? '';
$amount = (int) ($pay['amount'] ?? 0);
$method = $pay['method'] ?? '';
$created_at = $pay['created_at'] ?? '';
$match_id = (int) ($pay['match_id'] ?? 0);
$log_id = (int) ($pay['log_id'] ?? 0);
$vendor_id = (int) ($pay['vendor_id'] ?? 0);
$receipt_url = $pay['receipt_url'] ?? '';

// 결제 상태 라벨
$state_label = ($status === 'DONE') ? '결제가 완료되었습니다.' : '결제가 아직 완료되지 않은 주문입니다.';

// 전문가 정보
$vendor = null;
$vendor_name = '집잘알 서울';
$vendor_profile_img = G5_THEME_URL . '/images/quote-arrival/전문가_대표이미지.png';

if ($vendor_id > 0) {
  $vendor = sql_fetch("
        SELECT 
            m.mb_no, m.mb_id, m.mb_name, m.mb_nick, m.mb_service_types,
            m.mb_4  AS vendor_score,
            m.mb_5  AS vendor_reviews,
            m.mb_10 AS vendor_years,
            m.mb_9  AS order_count
        FROM g5_member AS m
        WHERE m.mb_no = '{$vendor_id}'
        LIMIT 1
    ");

  // [Sigma v5.0] 전문가 모의 데이터
  if (!$vendor) {
    $vendor = [
      'mb_no' => $vendor_id,
      'mb_id' => 'expert_user',
      'mb_name' => '집잘알 서울',
      'mb_nick' => '청소의신',
      'vendor_score' => '4.92',
      'vendor_reviews' => '60',
      'vendor_years' => '10',
      'order_count' => '89'
    ];
  }

  if ($vendor && $vendor['mb_name']) {
    $vendor_name = $vendor['mb_name'];
  }

  // 프로필 이미지 경로
  $vendor_mb_id = $vendor['mb_id'] ?? '';
  if ($vendor_mb_id) {
    $mb_dir = substr($vendor_mb_id, 0, 2);
    // get_mb_icon_name 함수 존재 여부 체크
    $icon_name = function_exists('get_mb_icon_name') ? get_mb_icon_name($vendor_mb_id) : $vendor_mb_id;

    $icon = G5_DATA_URL . "/member_image/{$mb_dir}/" . $icon_name . ".gif";
    $img_p = G5_DATA_PATH . "/member_image/{$mb_dir}/" . $icon_name . ".gif";
    if (file_exists($img_p)) {
      $vendor_profile_img = $icon;
    }
  }
}

// 신청/예약 정보 (g5_kakao_log 기준)
$service_label = '';
$cleaning_date = '';
$location = '';
$space_size = '';

$kakao = null;
if ($log_id > 0) {
  $kakao = sql_fetch("SELECT * FROM g5_kakao_log WHERE id = '{$log_id}'");

  // [Sigma v5.0] 카카오 로그 모의 데이터
  if (!$kakao) {
    $json_mock = json_encode([
      'serviceLabel' => '입주청소',
      'cleaningDate' => '2025년 8월 28일 오전',
      'location' => '대구광역시 남구',
      'spaceSize' => '30평'
    ]);
    $kakao = [
      'id' => $log_id,
      'utterance' => $json_mock,
      'type_date' => '2025년 8월 28일 오전',
      'type_address' => '대구광역시 남구',
      'user_id' => 'client_mock_user'
    ];
  }
}

$utter = [];
if ($kakao && !empty($kakao['utterance'])) {
  $tmp = json_decode((string) $kakao['utterance'], true);
  if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
    $utter = $tmp;
  }
}

// 레거시 키 매핑 함수 (confirm_payment_expert.php와 동일 로직 재사용)
if (!function_exists('_zja_legacy_map_local')) {
  function _zja_legacy_map_local($k)
  {
    $m = [
      'clean_movein' => '입주청소',
      'clean_move' => '이사청소',
      'clean_construct' => '공사후청소',
      'clean_other' => '기타청소',
      'move_house' => '가정이사',
      'move_office' => '사무실이사',
      'move_storage' => '보관이사',
      'move_oneroom' => '원룸이사',
      'move_other' => '기타이사',
      'clean_10' => '10평 이하',
      'clean_1020' => '10~20평',
      'clean_2030' => '20~30평',
      'clean_30' => '30평 이상',
      'clean_wide' => '넓은 사무실',
    ];
    return $m[$k] ?? $k;
  }
}

if ($kakao) {
  $service_label = $utter['serviceLabel'] ?? '';
  // 모의 데이터가 있다면 우선 사용
  if (!$service_label && isset($kakao['serviceLabel']))
    $service_label = $kakao['serviceLabel'];

  $cleaning_date = $utter['cleaningDate'] ?? ($kakao['type_date'] ?? '');
  $location = $utter['location'] ?? ($kakao['type_address'] ?? '');
  $space_size = $utter['spaceSize'] ?? '';

  $move_type = $kakao['move_type'] ?? '';
  $type_select = $kakao['type_select'] ?? '';
  $type_scope = $kakao['type_scope'] ?? '';
  $is_move_req = ($move_type && strpos((string) $move_type, 'move_') === 0);

  if (!$service_label) {
    $service_label = $is_move_req ? _zja_legacy_map_local($move_type) : _zja_legacy_map_local($type_select);
  }
  if (!$space_size) {
    $space_size = $type_scope ? _zja_legacy_map_local($type_scope) : '';
  }

  // 최종 fallback
  if (!$service_label)
    $service_label = '입주청소';
  if (!$space_size)
    $space_size = '30평';
}

// match_result 페이지로 돌아가는 링크 (있을 때만)
$match_result_url = '';
if ($log_id && $kakao && !empty($kakao['user_id'])) {
  $match_result_url = G5_URL . '/match_result_toss.php?log_id=' . (int) $log_id . '&user_id=' . urlencode($kakao['user_id']);
}
?>

<!-- Design Tokens -->
<link rel="stylesheet" href="<?= G5_THEME_URL ?>/theme/zibjalal/css/design-tokens.css">
<link rel="stylesheet" href="<?= G5_THEME_URL ?>/theme/zibjalal/css/components/buttons.css">
<?php include_once(G5_THEME_PATH . '/lib/components/button.php'); ?>

<style>
/* 페이지 전체 레이아웃 */

/* 모바일에서 메인 헤더 숨김 */
@media (max-width: 600px) {
  nav, header nav, #nav, nav.nav { display: none !important; }
  header, .header, .main-header, #header, #main-header { display: none !important; }
  .page-wrapper { padding: 0; }
}

.page-content { padding: 0 0 100px 0; }

/* 성공 메시지 영역 */
.success-section { padding: 24px var(--space-16-24); background: var(--bg-primary); display: flex; flex-direction: column; align-items: center; position: relative; min-height: 347px; }
.success-icon-circle { width: 48px; height: 48px; background: var(--primary); border-radius: 50%; display: flex; 
  align-items: center; justify-content: center; margin-bottom: 18px; drop-shadow: 0 8px 16px rgba(1, 102, 255, 0.20); }
.success-icon-check { width: 26px; height: 26px; color: var(--bg-primary); stroke-width: 3; }
.success-title { font-size: var(--fs-20-24); font-weight: 700; color: var(--text-primary); text-align: center; 
  margin: 0 0 6px 0; }
.success-subtitle { font-size: var(--fs-13-17); color: var(--text-secondary); text-align: center; margin: 0 0 var(--space-20-28) 0; }

/* 진행 단계 표시기 - 이미지로 대체 */
.step-wizard-image { width: 80%; max-width: 348px; height: auto; margin: 0 auto var(--space-20-28) auto; display: block; }

/* 결제 안전 안내 박스 */
.payment-safety-box { background: var(--primary-10); border-radius: 12px; padding: var(--space-16-20) 8px; 
  margin: 0; width: 100%; display: flex; justify-content: center; align-items: center; gap: var(--space-8-12); }
.payment-safety-icon { width: 48px; height: 48px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.payment-safety-icon-img { width: 100%; height: 100%; object-fit: contain; display: block; }
.payment-safety-text { font-size: var(--fs-14-18); color: var(--text-primary); line-height: 1.3; }
.payment-safety-text strong { font-weight: 600; color: var(--primary); }


/* 확정된 전문가 섹션 */
.expert-section { padding: var(--space-20-24) var(--space-16-24); background: var(--bg-primary); }
.expert-section-title { font-size: var(--fs-16-20); font-weight: 700; color: var(--text-primary); margin: 0 4px var(--space-8-12) 4px; }
.expert-card { background: var(--bg-primary); border: 1px solid var(--border-semilight); border-radius: 12px; 
  padding: 16px 20px; box-shadow: var(--shadow-xs); display: flex; align-items: flex-start; gap: var(--space-16-20); }
.expert-card-image { width: 63px; min-width: 63px; height: 63px; min-height: 63px; border-radius: 7px; overflow: hidden; flex-shrink: 0; background: var(--primary); margin-left: auto; order: 2; display: flex; align-items: center; justify-content: center; color: var(--bg-primary); position: relative; }
.expert-card-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
.expert-card-image-fallback { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--bg-primary); text-align: center; line-height: 1.2; z-index: 1; }
.expert-card-image img:not([src*="member_image"]) ~ .expert-card-image-fallback { display: none; }
.expert-card-info { flex: 1; min-width: 0; order: 1; display: flex; flex-direction: column; align-items: flex-start; }
.expert-card-name { font-size: var(--fs-18-22); font-weight: 700; color: var(--text-primary); margin: 0; text-align: left; }
.expert-card-rating { display: flex; align-items: center; gap: 6px; justify-content: flex-start; }
.expert-card-rating-star { width: 14px; height: 14px; color: #FACC15; flex-shrink: 0; }
.expert-card-rating-score { font-size: var(--fs-13-17); font-weight: 500; color: var(--text-primary); }
.expert-card-rating-count { font-size: var(--fs-13-17); font-weight: 500; color: var(--text-tertiary); }
.expert-card-meta { font-size: var(--fs-14-18); color: var(--text-tertiary); text-align: left; }

/* 서비스 전 체크리스트 카드 */
.checklist-card { border: 1px solid var(--primary-40); border-radius: 12px; padding: 18px 20px; margin-top: var(--space-12-16); }
.checklist-title { font-size: var(--fs-14-18); font-weight: 700; color: var(--primary); text-align: left; margin: 0 0 var(--space-12-16) 0; }
.checklist-list { display: flex; flex-direction: column; gap: var(--space-8-12); }
.checklist-item { display: flex; align-items: flex-start; gap: var(--space-8-12); }
.checklist-icon { width: 14px; height: 14px; flex-shrink: 0; margin-top: 2px; color: var(--primary); stroke-width: 2; }
.checklist-text { font-size: var(--fs-13-17); color: var(--text-primary); line-height: 1.4; }


/* 예약 정보 섹션 */
.booking-section { padding: var(--space-20-24) var(--space-16-24); background: var(--bg-primary); }
.booking-section-title { font-size: var(--fs-16-20); font-weight: 700; color: var(--text-primary); margin: 0 4px var(--space-8-12) 4px; }
.booking-divider { height: 1px; background: var(--border-light); margin: 0 4px var(--space-16-20) 4px; }
.booking-list { display: flex; flex-direction: column; gap: 18px; margin: 0 4px var(--space-16-20) 4px; }
.booking-row { display: flex; align-items: center; justify-content: space-between; font-size: var(--fs-13-17); }
.booking-key { color: var(--text-secondary); }
.booking-val { font-weight: 500; color: var(--text-primary); text-align: right; font-size: var(--fs-14-18); }
.booking-val.price { font-size: var(--fs-18-22); font-weight: 600; color: var(--primary); }
.booking-detail-toggle { display: flex; align-items: center; gap: var(--space-4-8); margin-top: var(--space-16-20); justify-content: center; cursor: pointer; }
.booking-detail-text { font-size: var(--fs-14-18); color: var(--text-secondary); }
.booking-detail-icon { width: 24px; height: 24px; color: var(--text-secondary); }

/* 결제 정보 섹션 */
.payment-info-section { padding: var(--space-20-24) var(--space-16-24); background: var(--bg-primary); }
.payment-info-card { background: var(--bg-secondary); border: 1px solid var(--border-semilight); border-radius: 12px; 
  padding: 18px 20px; }
.payment-info-title { font-size: var(--fs-16-20); font-weight: 700; color: var(--text-primary); text-align: left; margin: 0 0 var(--space-12-16) 0; }
.payment-info-list { display: flex; flex-direction: column; gap: var(--space-16-20); }
.payment-info-row { display: flex; align-items: center; justify-content: space-between; font-size: var(--fs-13-17); }
.payment-info-key { color: var(--text-secondary); }
.payment-info-val { font-weight: 400; color: var(--text-primary); text-align: right; font-size: var(--fs-14-18); }

/* 하단 고정 버튼 */
.fixed-bottom-bar { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); background: var(--bg-primary); 
  border-top: 1px solid var(--border-semilight); padding: var(--space-16-20) var(--space-16-24) 28px; display: flex; 
  gap: 10px; z-index: 100; width: 100%; max-width: var(--container-max); }
.fixed-bottom-bar .btn { flex: 1; height: auto; min-height: 52px; padding: 16px; border-radius: 8px; align-items: center; 
  justify-content: center; text-align: center; font-size: var(--fs-14-18); font-weight: 600; }
.fixed-bottom-bar .btn-primary { background: var(--primary); color: var(--bg-primary); border: none; }
.fixed-bottom-bar .btn-primary:hover { background: var(--primary-60); }
.fixed-bottom-bar .btn-secondary { background: var(--bg-tertiary); color: var(--text-secondary); border: none; }
.fixed-bottom-bar .btn-secondary:hover { background: var(--border-light); }

/* 반응형 */
@media (min-width: 600px) {
  .fixed-bottom-bar { max-width: var(--container-max); }
  .page-content { padding-bottom: 80px; }
  .success-section { padding: 28px; }
  .success-icon-circle { width: 56px; height: 56px; }
  .success-icon-check { width: 32px; height: 32px; }
  .payment-safety-icon { width: 56px; height: 56px; }
}
</style>

<div class="page-wrapper">
  <?php
  $homeUrl = defined('G5_URL') ? G5_URL . '/' : '/';
  $navConfig = [
    'leftButton' => [
      'show' => true,
      'icon' => 'back',
      'href' => 'javascript:history.back()',
      'ariaLabel' => '뒤로가기'
    ],
    'title' => '결제 완료',
    'titleAlign' => 'center',
    'rightButton' => [
      'show' => true,
      'type' => 'icon',
      'icon' => 'home',
      'href' => $homeUrl,
      'ariaLabel' => '홈으로'
    ]
  ];
  include G5_THEME_PATH . '/page_nav.php';
  ?>
  <div class="page-container">

    <div class="page-content">
      <!-- 성공 메시지 영역 -->
      <section class="success-section">
        <div class="success-icon-circle">
          <svg class="success-icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <h2 class="success-title">예약이 확정되었습니다</h2>
        <p class="success-subtitle">선택한 전문가에게 예약 정보가 전달됩니다.</p>
        
        <!-- 진행 단계 표시기 - 이미지로 대체 -->
        <img src="<?= G5_THEME_URL ?>/images/payment/Pay_complete-Stepper.png" alt="진행 단계" class="step-wizard-image">

        <!-- 결제 안전 안내 박스 -->
        <div class="payment-safety-box">
          <div class="payment-safety-icon">
            <img src="<?= G5_THEME_URL ?>/images/payment/silver_coin.png" alt="결제 안전 보관" class="payment-safety-icon-img">
          </div>
          <div class="payment-safety-text">
            <p style="margin: 0 0 4px 0;">
              결제금은 <strong>집잘알에서 안전하게 보관</strong>되며,
            </p>
            <p style="margin: 0;">
              <strong>서비스가 끝난 후</strong> 전문가에게 정산돼요
            </p>
          </div>
        </div>
      </section>

  <hr class="section-divider">

  <!-- 확정된 전문가 섹션 -->
  <section class="expert-section">
    <h3 class="expert-section-title">확정된 전문가</h3>
    <?php if ($vendor): ?>
    <div class="expert-card">
      <div class="expert-card-image">
        <?php if (strpos($vendor_profile_img, 'member_image') !== false && strpos($vendor_profile_img, '전문가_대표이미지') === false): ?>
        <img src="<?= e($vendor_profile_img) ?>" alt="<?= e($vendor_name) ?> 프로필">
        <?php else: ?>
        <div class="expert-card-image-fallback">집잘알<br>서울</div>
        <?php endif; ?>
      </div>
      <div class="expert-card-info">
        <h4 class="expert-card-name"><?= e($vendor_name) ?></h4>
        <div class="expert-card-rating">
          <svg class="expert-card-rating-star" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          <span class="expert-card-rating-score"><?= number_format((float)($vendor['vendor_score'] ?? 4.92), 2) ?></span>
          <span class="expert-card-rating-count">(<?= number_format((int)($vendor['vendor_reviews'] ?? 60)) ?>)</span>
        </div>
        <p class="expert-card-meta">완료 <?= number_format((int)($vendor['order_count'] ?? 89)) ?>건 • 경력 <?= (int)($vendor['vendor_years'] ?? 10) ?>년</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- 서비스 전 체크리스트 카드 -->
    <div class="checklist-card">
      <h4 class="checklist-title">서비스 전, 체크해보세요!</h4>
      <div class="checklist-list">
        <div class="checklist-item">
          <svg class="checklist-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span class="checklist-text">전문가에게 상세 주소 전달하기</span>
        </div>
        <div class="checklist-item">
          <svg class="checklist-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span class="checklist-text">소통을 통해 정확한 시간 정하기</span>
        </div>
        <div class="checklist-item">
          <svg class="checklist-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span class="checklist-text">주차가능 여부, 출입 방법 등 공유하기</span>
        </div>
        <div class="checklist-item">
          <svg class="checklist-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span class="checklist-text">필요한 추가 현장 사진 보내기</span>
        </div>
      </div>
    </div>

    <!-- 전문가와 바로 채팅하기 버튼 -->
    <?php if ($vendor): ?>
    <?php echo render_button('전문가와 바로 채팅하기', 'primary', 'md', true, '', 'button', ['onclick' => "openChatWithExpert('" . e($vendor['mb_id'] ?? 'expert_user') . "', '" . $log_id . "')", 'style' => 'margin-top: var(--space-12-16);']); ?>
    <?php endif; ?>
  </section>

  <hr class="section-divider">

  <!-- 예약 정보 섹션 -->
  <section class="booking-section">
    <h3 class="booking-section-title">예약 정보</h3>
    <div class="booking-divider"></div>
    <?php if ($kakao): ?>
    <div class="booking-list">
      <div class="booking-row">
        <span class="booking-key">서비스 종류</span>
        <span class="booking-val"><?= e($service_label ?: '입주청소') ?></span>
      </div>
      <div class="booking-row">
        <span class="booking-key">서비스 일정</span>
        <span class="booking-val"><?= e($cleaning_date ?: '2025년 8월 28일 오전') ?></span>
      </div>
      <div class="booking-row">
        <span class="booking-key">서비스 지역</span>
        <span class="booking-val"><?= e($location ?: '대구광역시 남구') ?></span>
      </div>
      <div class="booking-row">
        <span class="booking-key">공간 크기</span>
        <span class="booking-val"><?= e($space_size ?: '30평') ?></span>
      </div>
      <div class="booking-row">
        <span class="booking-key">견적 금액</span>
        <span class="booking-val price"><?= number_format($amount) ?>원</span>
      </div>
    </div>
    <div class="booking-divider"></div>
    <div class="booking-detail-toggle" onclick="toggleBookingDetail()">
      <span class="booking-detail-text">자세히</span>
      <svg class="booking-detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 12 15 18 9"></polyline>
      </svg>
    </div>
    <?php endif; ?>
  </section>

  <hr class="section-divider">

  <!-- 결제 정보 섹션 -->
  <section class="payment-info-section">
    <div class="payment-info-card">
      <h3 class="payment-info-title">결제 정보</h3>
      <div class="payment-info-list">
        <div class="payment-info-row">
          <span class="payment-info-key">주문번호</span>
          <span class="payment-info-val"><?= e($oid) ?></span>
        </div>
        <div class="payment-info-row">
          <span class="payment-info-key">결제금액</span>
          <span class="payment-info-val"><?= number_format($amount) ?>원</span>
        </div>
        <div class="payment-info-row">
          <span class="payment-info-key">결제수단</span>
          <span class="payment-info-val"><?= e($method ?: '카드결제') ?></span>
        </div>
        <?php if ($created_at): ?>
        <div class="payment-info-row">
          <span class="payment-info-key">결제일시</span>
          <span class="payment-info-val"><?= e($created_at) ?></span>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($receipt_url): ?>
       <?php echo render_button('영수증(매출전표) 보기', 'outline-gray', 'md', true, '', 'button', ['onclick' => "window.open('" . e($receipt_url) . "', 'receipt', 'width=460,height=700')", 'style' => 'margin-top: var(--space-16-24);']); ?>
      <?php endif; ?>
    </div>
  </section>

    </div> <!-- /page-content -->

    <!-- 하단 고정 버튼 -->
    <div class="fixed-bottom-bar">
      <?php if ($match_result_url): ?>
      <?php echo render_button('예약 상세', 'secondary', 'md', false, $match_result_url, '', []); ?>
      <?php endif; ?>
      <?php if ($vendor): ?>
      <?php echo render_button('1:1 채팅', 'primary', 'md', false, '', 'button', ['onclick' => "openChatWithExpert('" . e($vendor['mb_id'] ?? 'expert_user') . "', '" . $log_id . "')"]); ?>
      <?php else: ?>
      <?php echo render_button('내역 확인하기', 'primary', 'md', false, $match_result_url, '', []); ?>
      <?php endif; ?>
    </div>

  </div> <!-- /page-container -->
</div> <!-- /page-wrapper -->

<script>
function toggleBookingDetail() {
  // 예약 상세 정보 토글 기능 (필요시 구현)
  console.log('예약 상세 정보 토글');
}

function openChatWithExpert(mb_id, log_id) {
  if (!mb_id || !log_id) {
    alert('채팅 연결 정보가 올바르지 않습니다.');
    return;
  }
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "/theme/zibjalal/chat/chat.php";
  form.target = "_self";
  const inputs = { mb_id: mb_id, log_id: log_id };
  for (const key in inputs) {
    if (inputs.hasOwnProperty(key)) {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = key;
      input.value = inputs[key];
      form.appendChild(input);
    }
  }
  document.body.appendChild(form);
  form.submit();
}

// 전문가 카드 이미지를 카드 높이에 맞춰 정사각형으로 조정
document.addEventListener('DOMContentLoaded', function() {
  const expertCards = document.querySelectorAll('.expert-card');
  expertCards.forEach(function(card) {
    const cardImage = card.querySelector('.expert-card-image');
    if (cardImage) {
      const cardHeight = card.offsetHeight;
      const cardPadding = 34; // padding-top + padding-bottom (17px * 2)
      const imageSize = cardHeight - cardPadding;
      if (imageSize > 0) {
        cardImage.style.width = imageSize + 'px';
        cardImage.style.height = imageSize + 'px';
        cardImage.style.minWidth = imageSize + 'px';
        cardImage.style.minHeight = imageSize + 'px';
      }
    }
  });
});
</script>

<?php include_once(G5_THEME_PATH . '/tail.php'); ?>

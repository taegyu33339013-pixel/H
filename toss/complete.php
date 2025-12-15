<?php
// /www/toss/complete.php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
include_once(G5_THEME_PATH . '/head.php');

// 간단 escape 헬퍼
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// 주문번호 파라미터
$oid = trim($_GET['oid'] ?? '');
if (!$oid) {
    alert('유효하지 않은 접근입니다.');
}

// 결제 정보 조회
$pay = sql_fetch("
    SELECT *
    FROM g5_payments
    WHERE order_id = '".sql_real_escape_string($oid)."'
    LIMIT 1
");
if (!$pay) {
    alert('주문 정보를 찾을 수 없습니다.');
}

$status     = $pay['status'] ?? '';
$amount     = (int)($pay['amount'] ?? 0);
$method     = $pay['method'] ?? '';
$created_at = $pay['created_at'] ?? '';
$match_id   = (int)($pay['match_id'] ?? 0);
$log_id     = (int)($pay['log_id'] ?? 0);
$vendor_id  = (int)($pay['vendor_id'] ?? 0);
$receipt_url= $pay['receipt_url'] ?? '';

// 결제 상태 라벨
$state_label = ($status === 'DONE') ? '결제가 완료되었습니다.' : '결제가 아직 완료되지 않은 주문입니다.';

// 전문가 정보
$vendor = null;
$vendor_name = '선택된 전문가';
$vendor_profile_img = G5_THEME_URL.'/images/quote-arrival/전문가_대표이미지.png';

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

    if ($vendor && $vendor['mb_name']) {
        $vendor_name = $vendor['mb_name'];
    }

    // 프로필 이미지 경로
    $vendor_mb_id = $vendor['mb_id'] ?? '';
    if ($vendor_mb_id) {
        $mb_dir = substr($vendor_mb_id, 0, 2);
        $icon   = G5_DATA_URL . "/member_image/{$mb_dir}/" . get_mb_icon_name($vendor_mb_id) . ".gif";
        $img_p  = G5_DATA_PATH . "/member_image/{$mb_dir}/" . get_mb_icon_name($vendor_mb_id) . ".gif";
        if (file_exists($img_p)) {
            $vendor_profile_img = $icon;
        }
    }
}

// 신청/예약 정보 (g5_kakao_log 기준)
$service_label = '';
$cleaning_date = '';
$location      = '';
$space_size    = '';

$kakao = null;
if ($log_id > 0) {
    $kakao = sql_fetch("SELECT * FROM g5_kakao_log WHERE id = '{$log_id}'");
}

$utter = [];
if ($kakao && !empty($kakao['utterance'])) {
    $tmp = json_decode((string)$kakao['utterance'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
        $utter = $tmp;
    }
}

// 레거시 키 매핑 함수 (confirm_payment_expert.php와 동일 로직 재사용)
if (!function_exists('_zja_legacy_map_local')) {
    function _zja_legacy_map_local($k){
        $m=[
            'clean_movein'=>'입주청소','clean_move'=>'이사청소','clean_construct'=>'공사후청소','clean_other'=>'기타청소',
            'move_house'=>'가정이사','move_office'=>'사무실이사','move_storage'=>'보관이사','move_oneroom'=>'원룸이사','move_other'=>'기타이사',
            'clean_10'=>'10평 이하','clean_1020'=>'10~20평','clean_2030'=>'20~30평','clean_30'=>'30평 이상','clean_wide'=>'넓은 사무실',
        ];
        return $m[$k] ?? $k;
    }
}

if ($kakao) {
    $service_label = $utter['serviceLabel'] ?? '';
    $cleaning_date = $utter['cleaningDate'] ?? ($kakao['type_date'] ?? '');
    $location      = $utter['location']     ?? ($kakao['type_address'] ?? '');
    $space_size    = $utter['spaceSize']    ?? '';

    $move_type   = $kakao['move_type']   ?? '';
    $type_select = $kakao['type_select'] ?? '';
    $type_scope  = $kakao['type_scope']  ?? '';
    $is_move_req = ($move_type && strpos((string)$move_type, 'move_') === 0);

    if (!$service_label) {
        $service_label = $is_move_req ? _zja_legacy_map_local($move_type) : _zja_legacy_map_local($type_select);
    }
    if (!$space_size) {
        $space_size = $type_scope ? _zja_legacy_map_local($type_scope) : '';
    }
}

// match_result 페이지로 돌아가는 링크 (있을 때만)
$match_result_url = '';
if ($log_id && $kakao && !empty($kakao['user_id'])) {
    $match_result_url = G5_URL . '/match_result_toss.php?log_id='.(int)$log_id.'&user_id='.urlencode($kakao['user_id']);
}
?>

<style>
/* 페이지 한정: 기존 헤더/푸터 숨김 (앱 레이아웃) */
body > header.header{display:none}
body > footer.footer{display:none}

/* 공통 레이아웃 */
.page-section {
  padding: 0;
}
@media (max-width: 600px) {
  .page-section {
    padding: 0;
  }
}
.pay-wrap {
  max-width: 680px;
  margin: 32px auto 140px;
  padding: 0 var(--space-16-24);
  font-family: 'Pretendard', sans-serif;
}
.card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  box-shadow: 0 4px 10px rgba(0,0,0,.04);
  padding: 20px 18px;
}
.success-hero {
  text-align: center;
  padding: 24px 16px 8px;
}
.success-icon {
  width: 64px;
  height: 64px;
  border-radius: 999px;
  margin: 0 auto 16px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: var(--primary-10, #e0ecff);
  color: var(--primary, #0166FF);
}
.success-icon svg {
  width: 32px;
  height: 32px;
}
.success-title {
  margin: 0;
  font-size: var(--fs-18-22);
  font-weight: 800;
  color: var(--text-primary, #0f172a);
}
.success-sub {
  margin-top: 8px;
  font-size: var(--fs-14-18);
  color: var(--text-secondary, #64748b);
}

/* 메타 카드 */
.meta-card {
  margin-top: 16px;
  background:#f8fafc;
  border:1px solid #e5e7eb;
  border-radius:14px;
  padding:14px 16px;
  color:#334155;
  font-size:14px;
}
.meta-row {
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:8px 0;
  border-bottom:1px dashed #e5e7eb;
}
.meta-row:last-child { border-bottom:0; }
.meta-key {
  min-width:96px;
  color:#64748b;
  font-weight:600;
}
.meta-val {
  font-weight:700;
  color:#0f172a;
  text-align:right;
}

/* 전문가 카드 요약 */
.expert-summary {
  margin-top: 18px;
  border:1px solid #E5E7EB;
  border-radius:14px;
  padding:14px 16px;
  background:#ffffff;
}
.expert-summary-title{
  font-size:var(--fs-14-18);
  font-weight:700;
  margin:0 0 8px 0;
  color:var(--primary,#0166FF);
}
.expert-summary-body{
  display:flex;
  align-items:center;
  gap:12px;
}
.expert-summary-img{
  width:56px;
  height:56px;
  border-radius:12px;
  overflow:hidden;
  flex-shrink:0;
}
.expert-summary-img img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}
.expert-summary-info{
  flex:1;
  min-width:0;
}
.expert-name{
  margin:0;
  font-size:var(--fs-16-20);
  font-weight:800;
  color:#1A1E27;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
.expert-meta{
  margin-top:4px;
  font-size:13px;
  color:#6D7788;
}

/* 예약 정보 */
.resv-section-title{
  margin-top:20px;
  font-weight:700;
  font-size:var(--fs-14-18);
  color:var(--primary);
}
.resv-list{
  margin-top:8px;
}
.resv-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:6px 0;
  font-size:var(--fs-14-18);
}
.resv-key{
  color:var(--text-secondary,#64748b);
  min-width:90px;
}
.resv-val{
  color:var(--text-primary,#0f172a);
  text-align:right;
}

/* 하단 버튼 영역 */
.actions{
  margin-top:24px;
  display:flex;
  flex-direction:column;
  gap:10px;
}
.btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  padding:12px 16px;
  border-radius:10px;
  font-size:var(--fs-14-18);
  font-weight:600;
  text-decoration:none;
  border:1px solid transparent;
  cursor:pointer;
  transition:all .2s ease;
}
.btn-primary{
  background:var(--primary,#0166FF);
  color:#fff;
  border-color:var(--primary,#0166FF);
}
.btn-primary:hover{
  background:var(--primary-60,#0354cc);
  border-color:var(--primary-60,#0354cc);
}
.btn-ghost{
  background:#fff;
  color:var(--text-secondary,#64748b);
  border-color:#e5e7eb;
}
.btn-ghost:hover{
  background:#f9fafb;
}

/* 상단 앱 네비 */
.as-navbar {
  position: sticky;
  top: 0;
  z-index: 10;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
}
.as-navbar-content {
  max-width: var(--container-max, 960px);
  margin: 0 auto;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.as-navbar-left,
.as-navbar-right {
  width: 40px;
  display:flex;
  align-items:center;
  justify-content:flex-start;
}
.as-navbar-center {
  flex: 1;
  text-align: center;
}
.as-navbar-title {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
}
.as-navbar-icon {
  border: 0;
  background: transparent;
  padding: 4px;
  cursor: pointer;
}
</style>

<section class="page-section">
  <div class="app-container">
    <!-- 앱 네비게이션 -->
    <nav class="as-navbar" aria-label="탐색">
      <div class="as-navbar-content">
        <div class="as-navbar-left">
          <button type="button" class="as-navbar-icon" aria-label="뒤로가기" onclick="history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
        </div>
        <div class="as-navbar-center">
          <h1 class="as-navbar-title">결제 완료</h1>
        </div>
        <div class="as-navbar-right"></div>
      </div>
    </nav>

    <div class="pay-wrap">
      <div class="card">
        <!-- 상단 성공 히어로 -->
        <div class="success-hero">
          <div class="success-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.12"></circle>
              <path d="M9.5 12.5l1.8 1.8 3.7-3.8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
            </svg>
          </div>
          <h2 class="success-title"><?= e($state_label) ?></h2>
          <?php if ($status === 'DONE'): ?>
            <p class="success-sub">
              결제가 정상적으로 완료되었어요.<br>
              선택한 전문가에게 예약 정보가 전달됩니다.
            </p>
          <?php else: ?>
            <p class="success-sub">
              결제 상태: <?= e($status) ?><br>
              문제가 지속되면 고객센터로 문의해 주세요.
            </p>
          <?php endif; ?>
        </div>

        <!-- 결제 메타 정보 -->
        <div class="meta-card">
          <div class="meta-row">
            <div class="meta-key">주문번호</div>
            <div class="meta-val"><?= e($oid) ?></div>
          </div>
          <div class="meta-row">
            <div class="meta-key">결제금액</div>
            <div class="meta-val"><?= number_format($amount) ?>원</div>
          </div>
          <div class="meta-row">
            <div class="meta-key">결제수단</div>
            <div class="meta-val"><?= e($method ?: '카드결제') ?></div>
          </div>
          <?php if ($created_at): ?>
          <div class="meta-row">
            <div class="meta-key">결제일시</div>
            <div class="meta-val"><?= e($created_at) ?></div>
          </div>
          <?php endif; ?>
        </div>

        <!-- 전문가 요약 -->
        <?php if ($vendor): ?>
        <div class="expert-summary">
          <div class="expert-summary-title">확정된 전문가</div>
          <div class="expert-summary-body">
            <div class="expert-summary-img">
              <img src="<?= e($vendor_profile_img) ?>" alt="전문가 프로필">
            </div>
            <div class="expert-summary-info">
              <p class="expert-name"><?= e($vendor_name) ?></p>
              <p class="expert-meta">
                <?php if (!empty($vendor['vendor_years'])): ?>
                  경력 <?= (int)$vendor['vendor_years'] ?>년&nbsp;&nbsp;·&nbsp;
                <?php endif; ?>
                <?php if (!empty($vendor['vendor_score'])): ?>
                  평점 <?= number_format((float)$vendor['vendor_score'], 1) ?>
                <?php endif; ?>
              </p>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- 예약 정보 -->
        <?php if ($kakao): ?>
        <div class="resv-section-title">예약 정보</div>
        <div class="resv-list">
          <div class="resv-row">
            <div class="resv-key">서비스 종류</div>
            <div class="resv-val"><?= e($service_label ?: '-') ?></div>
          </div>
          <div class="resv-row">
            <div class="resv-key">서비스 일정</div>
            <div class="resv-val"><?= e($cleaning_date ?: '-') ?></div>
          </div>
          <div class="resv-row">
            <div class="resv-key">서비스 지역</div>
            <div class="resv-val"><?= e($location ?: '-') ?></div>
          </div>
          <div class="resv-row">
            <div class="resv-key">공간 크기</div>
            <div class="resv-val"><?= e($space_size ?: '-') ?></div>
          </div>
        </div>
        <?php endif; ?>

        <!-- 하단 버튼 -->
        <div class="actions">
          <?php if ($match_result_url): ?>
            <a href="<?= e($match_result_url) ?>" class="btn btn-primary">예약 상세 보러가기</a>
          <?php endif; ?>
          <?php if ($receipt_url): ?>
            <a href="<?= e($receipt_url) ?>" target="_blank" class="btn btn-ghost">영수증(매출전표) 보기</a>
          <?php endif; ?>
          <a href="<?= G5_URL ?>/" class="btn btn-ghost">홈으로 돌아가기</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include_once(G5_THEME_PATH . '/tail.php'); ?>

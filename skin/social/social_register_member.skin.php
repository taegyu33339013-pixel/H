<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!$config['cf_social_login_use']) {     // 소셜 로그인을 사용하지 않으면
    return;
}

// 기존 스킨에서 사용하던 CSS/JS (remodal 등)
add_stylesheet('<link rel="stylesheet" href="'.G5_JS_URL.'/remodal/remodal.css">', 11);
add_stylesheet('<link rel="stylesheet" href="'.G5_JS_URL.'/remodal/remodal-default-theme.css">', 12);
add_stylesheet('<link rel="stylesheet" href="'.get_social_skin_url().'/style.css?ver='.G5_CSS_VER.'">', 13);
add_javascript('<script src="'.G5_JS_URL.'/remodal/remodal.js"></script>', 10);
add_javascript('<script src="'.G5_JS_URL.'/jquery.register_form.js"></script>', 14);
if ($config['cf_cert_use'] && ($config['cf_cert_simple'] || $config['cf_cert_ipin'] || $config['cf_cert_hp']))
    add_javascript('<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>', 15);

$email_msg = $is_exists_email ? '등록할 이메일이 중복되었습니다. 다른 이메일을 입력해 주세요.' : '';
?>

<style>
/* ===========================
 * 오늘로또 – 카카오 가입 디자인
 * (두 번째로 주신 디자인을 이 스킨에 맞게 이식)
 * =========================== */

/* 페이지 배경 */
body {
  background: #030711;
  background-image:
    radial-gradient(ellipse 80% 50% at 50% -20%, rgba(0, 255, 204, 0.12) 0%, transparent 50%),
    radial-gradient(ellipse 60% 40% at 80% 50%, rgba(168, 85, 247, 0.08) 0%, transparent 50%);
}

/* 최상단 컨테이너 */
.lotto-social-register {
  max-width: 720px;
  margin: 40px auto 24px;
  padding: 0 16px 40px;
  box-sizing: border-box;
  font-family: 'Pretendard', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: #e5e7eb;
}

/* 페이지 헤더 */
.lotto-page-header {
  text-align: center;
  margin-bottom: 32px;
}
.lotto-page-header h1 {
  font-family: 'Outfit', 'Pretendard', sans-serif;
  font-size: clamp(1.8rem, 5vw, 2.3rem);
  font-weight: 800;
  margin-bottom: 10px;
}
.lotto-page-header h1 span {
  background: linear-gradient(135deg, #00ffcc 0%, #00d4ff 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.lotto-page-header p {
  color: #94a3b8;
  font-size: 0.95rem;
}

/* 진행 단계 표시 */
.lotto-steps {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}
.lotto-step-item {
  display: flex;
  align-items: center;
  gap: 8px;
}
.lotto-step-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(15,23,42,0.8);
  border: 2px solid rgba(148,163,184,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Outfit', sans-serif;
  font-size: 0.8rem;
  font-weight: 700;
  color: #64748b;
}
.lotto-step-label {
  font-size: 0.8rem;
  color: #64748b;
}
.lotto-step-item.active .lotto-step-circle {
  background: linear-gradient(135deg, #00ffcc, #00d4ff);
  border-color: transparent;
  color: #0f172a;
  box-shadow: 0 0 18px rgba(0,255,204,0.45);
}
.lotto-step-item.active .lotto-step-label {
  color: #e5e7eb;
}
.lotto-step-connector {
  width: 38px;
  height: 2px;
  background: rgba(148,163,184,0.5);
  border-radius: 999px;
}

/* 공통 카드 */
.lotto-card-shell {
  background: linear-gradient(145deg,
    rgba(15,23,42,0.95) 0%,
    rgba(3,7,18,0.98) 100%);
  border-radius: 24px;
  padding: 24px 20px 26px;
  border: 1px solid rgba(148, 163, 184, 0.45);
  box-shadow:
    0 16px 40px rgba(15,23,42,0.85),
    0 0 0 1px rgba(15,23,42,0.9);
  position: relative;
  overflow: hidden;
}
.lotto-card-shell::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at top left, rgba(56,189,248,0.18), transparent 55%),
    radial-gradient(circle at bottom right, rgba(168,85,247,0.12), transparent 60%);
  opacity: .9;
  pointer-events: none;
}

/* 상단 설명 */
.lotto-head {
  position: relative;
  z-index: 1;
}
.lotto-head-title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}
.lotto-head-emoji {
  width: 40px;
  height: 40px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(0,255,204,0.28), rgba(59,130,246,0.18));
  border: 1px solid rgba(34,197,94,0.5);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:1.5rem;
}
.lotto-head h1 {
  font-size: 1.3rem;
  font-weight: 700;
}
.lotto-head p {
  font-size: 0.9rem;
  color: #9ca3af;
  line-height: 1.6;
}

/* 안내 문구 */
.lotto-notice {
  margin: 16px 0 10px;
  font-size: 0.82rem;
  color: #9ca3af;
  display: flex;
  align-items: center;
  gap: 6px;
}
.lotto-notice i {
  color: #22c55e;
}

/* ===== 약관/개인정보 카드 – fieldset 기반 ===== */
.lotto-fieldset {
  background: linear-gradient(145deg,
    rgba(15, 23, 42, 0.96) 0%,
    rgba(3, 7, 17, 0.98) 100%);
  border-radius: 20px;
  border: 1px solid rgba(148,163,184,0.4);
  padding: 18px 16px 16px;
  margin-top: 14px;
  position: relative;
  overflow: hidden;
}
.lotto-fieldset-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(30,64,175,0.6);
}
.lotto-fieldset-title {
  display: flex;
  align-items: center;
  gap: 10px;
}
.lotto-fieldset-icon {
  width: 34px;
  height: 34px;
  border-radius: 12px;
  background: linear-gradient(135deg, rgba(56,189,248,0.22), rgba(129,140,248,0.22));
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:1.1rem;
}
.lotto-fieldset-title h2 {
  font-size: 0.96rem;
  font-weight: 600;
  margin: 0;
}
.lotto-fieldset-title small {
  display:block;
  margin-top:2px;
  font-size:0.78rem;
  color:#9ca3af;
}

/* 동의 토글 버튼 */
.lotto-agree-toggle {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 14px;
  border-radius: 999px;
  background: rgba(15,23,42,0.95);
  border: 1px solid rgba(148,163,184,0.7);
  cursor: pointer;
  font-size: 0.8rem;
  color: #e5e7eb;
  transition: all .25s ease;
}
.lotto-agree-toggle .check-icon {
  width: 18px;
  height: 18px;
  border-radius: 999px;
  border: 2px solid currentColor;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:0.7rem;
}
.lotto-agree-toggle.checked {
  background: linear-gradient(135deg, #00ffcc, #00d4ff);
  border-color: transparent;
  color: #0f172a;
  box-shadow: 0 0 20px rgba(0,255,204,0.5);
}
.lotto-agree-toggle.checked .check-icon {
  background:#0f172a;
}

/* 약관 textarea */
.lotto-term-box textarea {
  width: 100%;
  min-height: 140px;
  margin-top: 10px;
  border-radius: 12px;
  border: 1px solid rgba(55,65,81,0.9);
  background: #020617;
  color: #e5e7eb;
  font-size: 0.82rem;
  padding: 10px 11px;
  line-height: 1.55;
  resize: vertical;
}
.lotto-term-box textarea:focus-visible {
  outline: none;
  border-color: #22c55e;
}

/* 개인정보 테이블 – 새로운 스타일 */
.lotto-term-box table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-top: 10px;
  border-radius: 14px;
  overflow: hidden;
  background: rgba(15,23,42,0.95);
  border: 1px solid rgba(148,163,184,0.6);
}
.lotto-term-box table thead th {
  background: linear-gradient(135deg,
    rgba(0,255,204,0.14) 0%,
    rgba(59,130,246,0.12) 100%);
  padding: 12px 12px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #e5e7eb;
  border-bottom: 1px solid rgba(148,163,184,0.7);
}
.lotto-term-box table tbody td {
  padding: 11px 12px;
  font-size: 0.8rem;
  color: #cbd5e1;
  border-bottom: 1px solid rgba(31,41,55,0.9);
}
.lotto-term-box table tbody tr:last-child td {
  border-bottom: none;
}

/* 전체 동의 */
.lotto-allagree {
  margin-top: 16px;
  padding-top: 12px;
  border-top: 1px dashed rgba(55,65,81,0.9);
}
.lotto-allagree .lotto-agree-toggle {
  width: 100%;
  justify-content: center;
}

/* 개인정보 확인 카드 */
.lotto-info-card {
  margin-top: 22px;
  padding: 18px 16px 6px;
  border-radius: 20px;
  background: linear-gradient(145deg,
    rgba(15,23,42,0.96) 0%,
    rgba(3,7,17,0.98) 100%);
  border: 1px solid rgba(55,65,81,0.9);
}
.lotto-info-card h2 {
  font-size: 0.98rem;
  margin-bottom: 10px;
}
.lotto-info-card p.helper {
  font-size: 0.78rem;
  color: #9ca3af;
  margin-bottom: 10px;
}
.lotto-info-card ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
.lotto-info-card li {
  margin-bottom: 14px;
}
.lotto-label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.82rem;
  color: #9ca3af;
  margin-bottom: 4px;
}

/* 인풋 공통 */
.lotto-info-card .frm_input {
  width: 100%;
  box-sizing: border-box;
  background: #020617;
  border-radius: 10px;
  border: 1px solid rgba(55,65,81,0.9);
  padding: 10px 11px;
  font-size: 0.88rem;
  color: #e5e7eb;
}
.lotto-info-card .frm_input:focus {
  border-color: #22c55e;
  outline: none;
  box-shadow: 0 0 0 1px rgba(34,197,94,0.3);
}
.lotto-info-card .check {
  margin-top: 4px;
  font-size: 0.78rem;
  color: #f97373;
}

/* 버튼 영역 */
.lotto-btns {
  margin-top: 22px;
  display: flex;
  gap: 10px;
}
.lotto-btn-secondary,
.lotto-btn-primary {
  height: 44px;
  border-radius: 999px;
  font-size: 0.9rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  cursor: pointer;
}
.lotto-btn-secondary {
  min-width: 110px;
  background: transparent;
  color: #9ca3af;
  border: 1px solid rgba(148,163,184,0.6);
}
.lotto-btn-secondary:hover {
  background: rgba(15,23,42,0.9);
}
.lotto-btn-primary {
  flex: 1;
  background: linear-gradient(90deg,#00D4AA,#00E0B3);
  color: #020617;
  border: none;
  box-shadow: 0 12px 30px rgba(16,185,129,0.35);
}
.lotto-btn-primary:hover {
  filter: brightness(1.05);
  transform: translateY(-1px);
}

/* 기존 계정 연결 */
.lotto-connect {
  text-align: center;
  margin: 8px auto 32px;
  font-size: 0.86rem;
  color: #9ca3af;
}
.lotto-connect .strong {
  font-weight: 600;
  margin-bottom: 4px;
}
.lotto-connect .btn-txt {
  background: none;
  border: none;
  color: #38bdf8;
  cursor: pointer;
  font-size: 0.86rem;
}

/* 작은 화면 */
@media (max-width: 640px){
  .lotto-social-register {
    margin-top: 24px;
  }
  .lotto-card-shell {
    padding: 20px 14px 22px;
    border-radius: 20px;
  }
  .lotto-fieldset {
    padding: 16px 12px 12px;
  }
}

/* ===========================
 * 오늘로또 다크톤 보정
 * (테마 기본 흰색/분홍 배경 덮어쓰기)
 * =========================== */

/* 약관/개인정보 카드 전체를 다크 톤으로 강제 */
.lotto-social-register .lotto-fieldset {
  background: linear-gradient(145deg,
    rgba(15, 23, 42, 0.96) 0%,
    rgba(3, 7, 17, 0.98) 100%) !important;
  border-color: rgba(148,163,184,0.45) !important;
  color: #e5e7eb;
}

/* 약관 박스 안쪽 흰 배경 제거 */
.lotto-social-register .lotto-term-box {
  background: transparent !important;
}

.lotto-social-register .lotto-term-box textarea {
  background: #020617 !important;
  color: #e5e7eb !important;
}

.lotto-social-register .lotto-term-box table {
  background: rgba(15,23,42,0.95) !important;
}

/* 개인정보 확인 카드 – 분홍 배경을 다크 톤으로 덮기 */
#register_form.lotto-info-card {
  background: linear-gradient(145deg,
    rgba(15,23,42,0.96) 0%,
    rgba(3,7,17,0.98) 100%) !important;
  border-color: rgba(55,65,81,0.9) !important;
  color: #e5e7eb;
}

/* 혹시 테마에서 분홍 박스를 주고 있다면 제거 */
.lotto-social-register #register_form,
.lotto-social-register .register_form_inner,
.lotto-social-register .tbl_frm01,
.lotto-social-register .tbl_frm01 ul li {
  background: transparent !important;
  border: none;
}

/* 입력 필드는 기존 다크 입력 스타일 유지 */
.lotto-social-register .tbl_frm01 .frm_input {
  background: #020617 !important;
  border-color: rgba(55,65,81,0.9) !important;
  color: #e5e7eb !important;
}

/* 알림 문구 색상은 살짝 줄인 색으로 */
.lotto-social-register .lotto-info-card p.helper {
  color: #9ca3af;
}
</style>

<div class="lotto-social-register">

    <!-- 상단 제목 / 진행상태 -->
    <div class="lotto-page-header">
        <h1><span>오늘로또</span> 카카오 회원가입</h1>
        <p>AI 로또 분석 서비스 이용을 위해 약관을 확인해 주세요.</p>
    </div>

    <div class="lotto-steps">
        <div class="lotto-step-item active">
            <div class="lotto-step-circle">1</div>
            <div class="lotto-step-label">약관동의</div>
        </div>
        <div class="lotto-step-connector"></div>
        <div class="lotto-step-item">
            <div class="lotto-step-circle">2</div>
            <div class="lotto-step-label">정보확인</div>
        </div>
        <div class="lotto-step-connector"></div>
        <div class="lotto-step-item">
            <div class="lotto-step-circle">3</div>
            <div class="lotto-step-label">가입완료</div>
        </div>
    </div>

    <div class="lotto-card-shell">

        <div class="lotto-head">
            <div class="lotto-head-title-row">
                <div class="lotto-head-emoji">🧠</div>
                <div>
                    <h1>카카오 로그인 거의 완료!</h1>
                    <p>약관에 동의하고 이메일과 휴대폰 번호만 확인하면<br>바로 AI 로또 분석을 이용하실 수 있습니다.</p>
                </div>
            </div>
        </div>

        <div class="lotto-body">

            <p class="lotto-notice">
                <i class="fa fa-check-circle" aria-hidden="true"></i>
                회원가입약관 및 개인정보 수집·이용에 모두 동의하셔야 가입이 완료됩니다.
            </p>

            <form name="fregisterform" id="fregisterform"
                  action="<?php echo $register_action_url; ?>"
                  onsubmit="return fregisterform_submit(this);"
                  method="POST" autocomplete="off">

                <!-- 회원가입 약관 -->
                <fieldset class="lotto-fieldset" id="fregister_term">
                    <div class="lotto-fieldset-header">
                        <div class="lotto-fieldset-title">
                            <div class="lotto-fieldset-icon">📋</div>
                            <div>
                                <h2>회원가입약관</h2>
                                <small>서비스 이용을 위한 필수 약관입니다.</small>
                            </div>
                        </div>
                        <label class="lotto-agree-toggle" id="termsToggle">
                            <span class="check-icon">✓</span>
                            <span>동의합니다</span>
                            <!-- 실제 체크박스는 숨김 (이름/ID 그대로 유지) -->
                            <input type="checkbox" name="agree" value="1" id="agree11" class="selec_chk" style="display:none;">
                        </label>
                    </div>

                    <div class="lotto-term-box">
                        <textarea readonly><?php echo get_text($config['cf_stipulation']); ?></textarea>
                    </div>
                </fieldset>

                <!-- 개인정보 수집 및 이용 -->
                <fieldset class="lotto-fieldset" id="fregister_private">
                    <div class="lotto-fieldset-header">
                        <div class="lotto-fieldset-title">
                            <div class="lotto-fieldset-icon">🔒</div>
                            <div>
                                <h2>개인정보 수집 및 이용</h2>
                                <small>서비스 제공을 위해 필요한 최소 정보만 수집합니다.</small>
                            </div>
                        </div>
                        <label class="lotto-agree-toggle" id="privacyToggle">
                            <span class="check-icon">✓</span>
                            <span>동의합니다</span>
                            <input type="checkbox" name="agree2" value="1" id="agree21" class="selec_chk" style="display:none;">
                        </label>
                    </div>

                    <div class="lotto-term-box">
                        <table class="privacy-table">
                            <caption class="sound_only">개인정보 수집 및 이용</caption>
                            <thead>
                            <tr>
                                <th>목적</th>
                                <th>항목</th>
                                <th>보유기간</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>이용자 식별 및 본인 여부 확인</td>
                                <td>
                                    아이디, 이름, 비밀번호<?php echo ($config['cf_cert_use']) ? ", 생년월일, 휴대폰 번호(본인인증 시), 암호화된 개인식별부호(CI)" : ""; ?>
                                </td>
                                <td>회원 탈퇴 시까지</td>
                            </tr>
                            <tr>
                                <td>고객서비스 이용에 관한 통지,<br>CS 대응을 위한 이용자 식별</td>
                                <td>연락처 (이메일, 휴대전화번호)</td>
                                <td>회원 탈퇴 시까지</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>

                <!-- 전체동의 -->
                <div id="custom_chkall" class="lotto-allagree">
                    <label class="lotto-agree-toggle" id="chkAllToggle">
                        <span class="check-icon">✓</span>
                        <span>회원가입 약관 전체 동의</span>
                        <input type="checkbox" name="chk_all" id="chk_all_auto" class="selec_chk" style="display:none;">
                    </label>
                </div>

                <!-- ==== 기존 hidden 필드들 (그대로 유지) ==== -->
                <input type="hidden" name="w" value="<?php echo $w; ?>">
                <input type="hidden" name="url" value="<?php echo $urlencode; ?>">
                <input type="hidden" name="provider" value="<?php echo $provider_name; ?>">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
                <input type="hidden" name="cert_no" value="">
                <input type="hidden" name="mb_id" value="<?php echo $user_id; ?>" id="reg_mb_id">
                <?php if ($config["cf_cert_use"]) { ?>
                    <input type="hidden" id="reg_mb_name" name="mb_name" value="<?php echo $user_name ? $user_name : $user_nick ?>">
                <?php } ?>

                <?php if ($config['cf_use_hp'] || ($config["cf_cert_use"] && ($config['cf_cert_hp'] || $config['cf_cert_simple']))) {  ?>
                    <?php if ($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple'])) { ?>
                        <input type="hidden" name="old_mb_hp" value="<?php echo get_text($user_phone); ?>">
                    <?php } ?>
                <?php } ?>

                <!-- 개인정보 확인 카드 -->
                <div id="register_form" class="form_01 lotto-info-card">
                    <h2>개인정보 확인</h2>
                    <p class="helper">카카오 계정에서 가져온 정보입니다. 필요한 경우만 수정해 주세요.</p>

                    <div class="tbl_frm01 tbl_wrap register_form_inner">
                        <ul>
                            <?php if ($req_nick) {  ?>
                                <li>
                                    <label for="reg_mb_nick" class="lotto-label">
                                        닉네임 (필수)
                                    </label>

                                    <input type="hidden" name="mb_nick_default" value="<?php echo isset($user_nick) ? get_text($user_nick) : ''; ?>">
                                    <input type="text" name="mb_nick"
                                           value="<?php echo isset($user_nick) ? get_text($user_nick) : ''; ?>"
                                           id="reg_mb_nick"
                                           required
                                           class="frm_input required nospace full_input"
                                           maxlength="20"
                                           placeholder="닉네임을 입력하세요" readonly>
                                    <span id="msg_mb_nick"></span>
                                </li>
                            <?php }  ?>

                            <li>
                                <label for="reg_mb_email" class="lotto-label">
                                    E-mail (필수)
                                </label>
                                <input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
                                <input type="text" name="mb_email"
                                       value="<?php echo isset($user_email) ? $user_email : ''; ?>"
                                       id="reg_mb_email"
                                       required
                                       <?php echo (isset($user_email) && $user_email != '' && !$is_exists_email)? "readonly":''; ?>
                                       class="frm_input email full_input required"
                                       maxlength="100"
                                       placeholder="이메일 주소를 입력하세요">
                                <div class="check"><?php echo $email_msg; ?></div>
                            </li>

                            <li>
                                <label for="reg_mb_hp" class="lotto-label">
                                    휴대전화번호 (필수)
                                </label>
                                <input type="text"
                                       name="mb_hp"
                                       id="reg_mb_hp"
                                       value="<?php echo get_text($user_phone); ?>"
                                       required
                                       class="frm_input required full_input"
                                       maxlength="20"
                                       placeholder="'-' 없이 숫자만 입력해 주세요">
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 버튼 영역 -->
                <div class="btn_confirm lotto-btns">
                    <a href="<?php echo G5_URL ?>" class="lotto-btn-secondary">취소</a>
                    <button type="submit" id="btn_submit"
                            class="lotto-btn-primary" accesskey="s">
                        <?php echo $w == '' ? '회원가입 완료하기' : '정보수정'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 기존 계정 연결 영역 -->
<div class="member_connect lotto-connect">
    <p class="strong">기존 집잘알/사이트 계정이 있으신가요?</p>
    <button type="button" class="connect-opener btn-txt" data-remodal-target="modal">
        기존 계정에 SNS 아이디 연결하기
        <i class="fa fa-angle-double-right"></i>
    </button>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<script>
$(function() {
    /** 상태에 맞게 토글 UI 갱신 */
    function updateToggleUI() {
        var termsChecked   = $('#agree11').prop('checked');   // 회원가입약관
        var privacyChecked = $('#agree21').prop('checked');   // 개인정보수집
        var allChecked     = termsChecked && privacyChecked;

        // 개별 토글 UI
        $('#termsToggle').toggleClass('checked', termsChecked);
        $('#privacyToggle').toggleClass('checked', privacyChecked);

        // 전체동의 체크/UI
        $('#chk_all_auto').prop('checked', allChecked);
        $('#chkAllToggle').toggleClass('checked', allChecked);
    }

    /** 회원가입 약관 토글 클릭 */
    $('#termsToggle').on('click', function(e) {
        if (e.target.tagName.toLowerCase() === 'input') return;

        var $cb = $('#agree11');
        $cb.prop('checked', !$cb.prop('checked'));
        updateToggleUI();
    });

    /** 개인정보 수집·이용 토글 클릭 */
    $('#privacyToggle').on('click', function(e) {
        if (e.target.tagName.toLowerCase() === 'input') return;

        var $cb = $('#agree21');
        $cb.prop('checked', !$cb.prop('checked'));
        updateToggleUI();
    });

    /** 전체 동의 토글 클릭 */
    $('#chkAllToggle').on('click', function(e) {
        if (e.target.tagName.toLowerCase() === 'input') return;

        // 현재 두 약관이 모두 체크되어 있으면 → 전체 해제,
        // 하나라도 해제 상태면 → 전체 선택
        var allChecked = $('#agree11').prop('checked') && $('#agree21').prop('checked');
        var newState   = !allChecked;

        // hidden 전체동의 + 개별 약관 체크박스 상태를 모두 맞춰줌
        $('#chk_all_auto, #agree11, #agree21').prop('checked', newState);

        updateToggleUI();
    });

    /** 다른 스크립트에서 체크박스 상태를 바꿔도 UI 동기화 */
    $('#agree11, #agree21').on('change', updateToggleUI);

    // 초기 상태 한 번 동기화
    updateToggleUI();

    /* ====== (기존 jQuery 스크립트들 – 필요한 부분만 유지) ====== */
    // cf_cert 관련 기본 코드 유지
    $("#reg_zip_find").css("display", "inline-block");
    var pageTypeParam = "pageType=register";

    // 툴팁 (지금은 닉네임 툴팁 안 쓰지만 원래 코드 유지)
    $(document).on("click", ".tooltip_icon", function(e) {
        $(this).next(".tooltip").fadeIn(400).css("display", "inline-block");
    }).on("mouseout", ".tooltip_icon", function(e) {
        $(this).next(".tooltip").fadeOut();
    });

    <?php if ($config['cf_cert_use'] && $config['cf_cert_simple']) { ?>
    $(".win_sa_cert").click(function() {
        if (!cert_confirm()) return false;
        var type = $(this).data("type");
        var params = "?directAgency=" + type + "&" + pageTypeParam;
        call_sa("<?php echo G5_INICERT_URL; ?>/ini_request.php" + params);
    });
    <?php } ?>

    <?php if ($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
    $("#win_ipin_cert").click(function() {
        if (!cert_confirm()) return false;
        var url = "<?php echo G5_OKNAME_URL; ?>/ipin1.php?" + pageTypeParam;
        certify_win_open('kcb-ipin', url);
    });
    <?php } ?>

    <?php if ($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
    $("#win_hp_cert").click(function() {
        if (!cert_confirm()) return false;
        <?php
        $cert_url = "";
        $cert_type = "";
        switch ($config['cf_cert_hp']) {
            case 'kcb': $cert_url = G5_OKNAME_URL.'/hpcert1.php'; $cert_type = 'kcb-hp'; break;
            case 'kcp': $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php'; $cert_type = 'kcp-hp'; break;
            case 'lg':  $cert_url = G5_LGXPAY_URL.'/AuthOnlyReq.php'; $cert_type = 'lg-hp'; break;
        }
        ?>
        var url = "<?php echo $cert_url; ?>?" + pageTypeParam;
        certify_win_open("<?php echo $cert_type; ?>", url);
    });
    <?php } ?>
});

// 제출 최종 체크 (원래 코드 그대로)
function fregisterform_submit(f) {

    if (!f.agree.checked) {
        alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
        f.agree.focus();
        return false;
    }

    if (!f.agree2.checked) {
        alert("개인정보 수집 및 이용의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
        f.agree2.focus();
        return false;
    }

    <?php if ($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) { ?>
    if (f.cert_no.value == "") {
        alert("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
        return false;
    }
    <?php } ?>

    if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick.defaultValue != f.mb_nick.value)) {
        var msg = reg_mb_nick_check();
        if (msg) {
            alert(msg);
            f.reg_mb_nick.select();
            return false;
        }
    }

    if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
        var msg = reg_mb_email_check();
        if (msg) {
            alert(msg);
            f.reg_mb_email.select();
            return false;
        }
    }

    document.getElementById("btn_submit").disabled = "disabled";
    return true;
}
</script>

<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$mb_name  = get_text($mb['mb_name']);
$mb_id    = isset($mb['mb_id']) ? $mb['mb_id'] : '';
$mb_email = isset($mb['mb_email']) ? $mb['mb_email'] : '';
$mb_hp	  = isset($mb['mb_hp']) ? $mb['mb_hp'] : '';
?>

<style>
  .reg-result-page {
    min-height: calc(100vh - 100px);
    padding: 40px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, #1b2740 0, #050814 55%, #020307 100%);
    font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
    color: #ffffff;
  }

  .reg-result-card {
    width: 100%;
    max-width: 540px;
    background: rgba(10, 15, 28, 0.92);
    border-radius: 28px;
    padding: 32px 28px 26px;
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255,255,255,0.08);
    position: relative;
    overflow: hidden;
  }

  .reg-result-glow {
    position: absolute;
    inset: -40%;
    background:
      radial-gradient(circle at 0% 0%, rgba(0, 212, 170, 0.16) 0, transparent 55%),
      radial-gradient(circle at 100% 100%, rgba(255, 215, 0, 0.14) 0, transparent 55%);
    opacity: 0.9;
    pointer-events: none;
  }

  .reg-result-inner {
    position: relative;
    z-index: 1;
  }

  .reg-result-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(0,0,0,0.45);
    border: 1px solid rgba(255,255,255,0.12);
    font-size: 0.8rem;
    color: #c7d2fe;
    margin-bottom: 18px;
  }

  .reg-result-badge span.icon {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    background: linear-gradient(135deg, #ffd700, #ffb347);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #111827;
  }

  .reg-result-title {
    font-family: 'Outfit', 'Pretendard', sans-serif;
    font-size: 1.7rem;
    line-height: 1.35;
    margin-bottom: 6px;
    letter-spacing: -0.02em;
  }

  .reg-result-title span {
    background: linear-gradient(135deg, #00d4aa 0%, #00b894 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .reg-result-sub {
    font-size: 0.95rem;
    color: #9ca3af;
    margin-bottom: 20px;
  }

  .reg-result-user {
    margin: 18px 0 22px;
    padding: 14px 14px 12px;
    border-radius: 18px;
    background: rgba(15,23,42,0.95);
    border: 1px solid rgba(148,163,184,0.4);
    display: grid;
    row-gap: 8px;
  }

  .reg-result-user-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    font-size: 0.9rem;
  }

  .reg-result-user-label {
    color: #9ca3af;
  }

  .reg-result-user-value {
    color: #e5e7eb;
    font-weight: 600;
  }

  .reg-result-email-info {
    font-size: 0.85rem;
    color: #9ca3af;
    margin-bottom: 14px;
    line-height: 1.6;
  }

  .reg-result-divider {
    height: 1px;
    background: linear-gradient(to right,
      rgba(148,163,184,0.0),
      rgba(148,163,184,0.5),
      rgba(148,163,184,0.0)
    );
    margin: 20px 0 14px;
  }

  .reg-result-tip {
    font-size: 0.85rem;
    color: #6b7280;
    line-height: 1.6;
  }

  .reg-result-tip strong {
    color: #e5e7eb;
  }

  .reg-result-actions {
    margin-top: 26px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .reg-result-btn-primary,
  .reg-result-btn-secondary {
    flex: 1;
    min-width: 0;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    padding: 12px 18px;
    text-decoration: none;
    border-radius: 999px;
    font-size: 0.95rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
  }

  .reg-result-btn-primary {
    background: linear-gradient(135deg, #00d4aa 0%, #00b894 100%);
    color: #020617;
    box-shadow: 0 10px 30px rgba(34, 197, 158, 0.35);
  }

  .reg-result-btn-primary:hover {
    filter: brightness(1.06);
    transform: translateY(-1px);
  }

  .reg-result-btn-secondary {
    background: rgba(15,23,42,0.9);
    color: #e5e7eb;
    border: 1px solid rgba(148,163,184,0.5);
  }

  .reg-result-btn-secondary:hover {
    background: rgba(15,23,42,1);
  }

  .reg-result-btn-primary span.icon-arrow {
    font-size: 1rem;
  }

  @media (max-width: 640px) {
    .reg-result-page {
      padding: 24px 12px 32px;
      min-height: calc(100vh - 60px);
    }
    .reg-result-card {
      padding: 22px 18px 20px;
      border-radius: 22px;
    }
    .reg-result-title {
      font-size: 1.4rem;
    }
    .reg-result-actions {
      flex-direction: column;
    }
  }
</style>

<div class="reg-result-page">
  <div class="reg-result-card">
    <div class="reg-result-glow"></div>
    <div class="reg-result-inner">
      <div class="reg-result-badge">
        <span class="icon">🎉</span>
        <span>회원가입이 완료되었습니다</span>
      </div>

      <h1 class="reg-result-title">
        <span><?php echo $mb_name; ?></span>님, 가입을 축하드립니다!
      </h1>

      <p class="reg-result-sub">
        이제부터 로또 인사이트가 포착한 <strong>이번 주 패턴</strong>을 바로 확인하실 수 있습니다.
      </p>

      <div class="reg-result-user">
        <div class="reg-result-user-row">
          <span class="reg-result-user-label">아이디</span>
          <span class="reg-result-user-value"><?php echo $mb_id; ?></span>
        </div>
        <?php if ($mb_email) { ?>
        <div class="reg-result-user-row">
          <span class="reg-result-user-label">이메일</span>
          <span class="reg-result-user-value"><?php echo $mb_email; ?></span>
        </div>
        <?php } ?>
        <?php if ($mb_hp) { ?>
        <div class="reg-result-user-row">
          <span class="reg-result-user-label">전화번호</span>
          <span class="reg-result-user-value"><?php echo $mb_hp; ?></span>
        </div>
        <?php } ?>
      </div>

      <?php if (is_use_email_certify()) { ?>
        <p class="reg-result-email-info">
          가입 시 입력하신 이메일 주소로 <strong>인증 메일</strong>이 발송되었습니다.<br>
          메일함에서 인증을 완료하신 후, 모든 기능을 원활히 이용하실 수 있습니다.
        </p>
      <?php } ?>

      <div class="reg-result-divider"></div>

      <p class="reg-result-tip">
        회원님의 비밀번호는 <strong>암호화되어 안전하게 저장</strong>되며,<br>
        분실 시에는 가입 시 입력하신 이메일을 통해 찾으실 수 있습니다.<br>
        탈퇴는 언제든지 가능하며, 일정 기간이 지난 후 정보는 안전하게 삭제됩니다.
      </p>

      <div class="reg-result-actions">
        <a href="<?php echo G5_URL; ?>/result.php" class="reg-result-btn-primary">
          AI 분석 시작하기
          <span class="icon-arrow">→</span>
        </a>
        <a href="<?php echo G5_URL; ?>/" class="reg-result-btn-secondary">
          메인으로 돌아가기
        </a>
      </div>
    </div>
  </div>
</div>

<?php if (!defined('_GNUBOARD_')) exit; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  
  <!-- Primary Meta Tags -->
  <title>회원가입 | 오늘로또 - 카카오 3초 시작 무료 1회</title>
  <meta name="title" content="회원가입 | 오늘로또 - 카카오 3초 시작 무료 1회">
  <meta name="description" content="카카오톡 3초 간편 가입! AI 로또 분석 무료 1회를 즉시 받아보세요. 1,180회차 데이터 기반 패턴 분석으로 통계 기반 번호를 제공합니다.">
  <meta name="robots" content="index, follow">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="https://lottoinsight.ai/auth.html">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://lottoinsight.ai/auth">
  <meta property="og:title" content="회원가입 | 오늘로또 - 카카오 3초 시작 무료 1회">
  <meta property="og:description" content="카카오 3초 시작! AI 로또 분석 무료 1회 즉시 제공. 1,180회차 데이터 기반 패턴 분석.">
  <meta property="og:image" content="https://lottoinsight.ai/og-image.png">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:title" content="회원가입 | 오늘로또 - 카카오 3초 시작 무료 1회">
  <meta property="twitter:description" content="카카오 3초 시작! AI 로또 분석 무료 1회 즉시 제공!">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#0B132B">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">

  <!-- Fonts (Outfit for headings, Inter for body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <!-- Shared Styles -->
  <link rel="stylesheet" href="/styles/shared.css">

  <style>
    /* Auth Page Specific Styles */
    html, body {
      overflow-x: hidden;
    }
    
    body {
      background: var(--gradient-hero);
      min-height: 100vh;
      display: flex;
      align-items: stretch;
      justify-content: center;
      width: 100%;
      max-width: 100vw;
    }

    .auth-wrapper {
      max-width: 1200px;
      width: 100%;
      margin: 40px 16px;
      display: grid;
      grid-template-columns: 1.1fr 1fr;
      gap: 40px;
      background: rgba(10, 15, 28, 0.85);
      border-radius: 32px;
      padding: 40px;
      box-shadow: var(--shadow-card);
      border: 1px solid rgba(255,255,255,0.08);
      backdrop-filter: blur(18px);
    }

    /* 왼쪽 브랜드 영역 */
    .auth-left {
      padding: 16px 24px 16px 8px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border-right: 1px solid rgba(255,255,255,0.06);
    }

    .auth-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.6rem;
      text-decoration: none;
      color: var(--text-primary);
    }

    .auth-logo-icon {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      background: var(--gradient-cyan);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }

    .auth-title-block {
      margin-top: 40px;
    }

    .auth-title-block h1 {
      font-family: 'Outfit', 'Pretendard', sans-serif;
      font-size: clamp(2rem, 3vw, 2.6rem);
      letter-spacing: -0.02em;
      margin-bottom: 16px;
    }

    .auth-title-block h1 span {
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .auth-title-block p {
      color: var(--text-secondary);
      font-size: 0.98rem;
      line-height: 1.7;
    }

    .auth-benefits {
      margin-top: 32px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .auth-benefit {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .auth-benefit-icon {
      width: 22px;
      height: 22px;
      border-radius: 8px;
      background: rgba(0,212,170,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      color: var(--accent-cyan);
    }

    .auth-footer-text {
      margin-top: 40px;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* 오른쪽 폼 영역 */
    .auth-right {
      padding: 16px 8px 16px 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .auth-card {
      width: 100%;
      max-width: 420px;
      background: var(--primary);
      border-radius: 24px;
      padding: 32px 28px 28px;
      border: 1px solid rgba(255,255,255,0.08);
      box-shadow: var(--shadow-card);
    }

    .auth-tabs {
      display: flex;
      background: rgba(255,255,255,0.03);
      border-radius: 999px;
      padding: 4px;
      margin-bottom: 24px;
    }

    .auth-tab {
      flex: 1;
      text-align: center;
      padding: 10px 0;
      font-size: 0.95rem;
      border-radius: 999px;
      cursor: pointer;
      color: var(--text-secondary);
      border: none;
      background: none;
      font-weight: 500;
      transition: all 0.25s ease;
    }

    .auth-tab.active {
      background: var(--gradient-cyan);
      color: var(--primary-dark);
      font-weight: 700;
    }

    .auth-heading {
      margin-bottom: 6px;
      font-size: 1.4rem;
    }

    .auth-sub {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-bottom: 24px;
    }

    .auth-sub span {
      color: var(--accent-gold);
      font-weight: 600;
    }

    .auth-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .auth-field {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .auth-label {
      font-size: 0.88rem;
      color: var(--text-secondary);
    }

    .auth-input {
      background: #111827;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.08);
      padding: 12px 14px;
      font-size: 0.95rem;
      color: var(--text-primary);
      outline: none;
      transition: all 0.2s ease;
    }

    .auth-input:focus {
      border-color: var(--accent-cyan);
      box-shadow: 0 0 0 1px rgba(0,212,170,0.4);
    }

    .auth-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .auth-link {
      color: var(--accent-cyan);
      text-decoration: none;
      font-size: 0.85rem;
    }

    .auth-button {
      margin-top: 8px;
      padding: 14px 16px;
      border-radius: 14px;
      border: none;
      background: var(--gradient-cyan);
      color: var(--primary-dark);
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.3s ease;
    }

    .auth-button:hover {
      filter: brightness(1.06);
      transform: translateY(-2px);
    }

    .auth-separator {
      margin: 20px 0 10px;
      text-align: center;
      font-size: 0.8rem;
      color: var(--text-muted);
      position: relative;
    }

    .auth-separator::before,
    .auth-separator::after {
      content: "";
      position: absolute;
      top: 50%;
      width: 35%;
      height: 1px;
      background: rgba(255,255,255,0.08);
    }
    .auth-separator::before { left: 0; }
    .auth-separator::after { right: 0; }

    .auth-social-btn {
      width: 100%;
      margin-top: 8px;
      padding: 14px 18px;
      border-radius: 12px;
      border: none;
      background: #FEE500;
      color: #000000;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 14px rgba(254, 229, 0, 0.3);
    }

    .auth-social-btn:hover {
      background: #F5DC00;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(254, 229, 0, 0.4);
    }

    .auth-helper-text {
      margin-top: 16px;
      text-align: center;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .auth-helper-text a {
      color: var(--accent-cyan);
      text-decoration: none;
      font-weight: 500;
    }

    @media (max-width: 960px) {
      .auth-wrapper {
        grid-template-columns: 1fr;
        margin: 24px 12px;
        padding: 24px;
      }
      .auth-left {
        border-right: none;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        padding-bottom: 24px;
      }
      .auth-right {
        padding-top: 8px;
      }
    }

    @media (max-width: 640px) {
      body {
        padding-bottom: 0;
      }

      .auth-wrapper {
        margin: 0;
        padding: 16px;
        border-radius: 0;
        min-height: 100vh;
        gap: 20px;
      }

      .auth-left {
        padding: 8px 0 16px;
      }

      .auth-logo {
        font-size: 1.3rem;
      }

      .auth-logo-icon {
        width: 32px;
        height: 32px;
        font-size: 1rem;
      }

      .auth-title-block {
        margin-top: 20px;
      }

      .auth-title-block h1 {
        font-size: 1.5rem;
        margin-bottom: 12px;
      }

      .auth-title-block p {
        font-size: 0.9rem;
      }

      .auth-benefits {
        margin-top: 20px;
        gap: 8px;
      }

      .auth-benefit {
        font-size: 0.85rem;
      }

      .auth-benefit-icon {
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
      }

      .auth-footer-text {
        display: none; /* 모바일에서 숨김 */
      }

      .auth-card {
        padding: 20px 16px;
        border-radius: 20px;
      }

      .auth-tabs {
        margin-bottom: 20px;
      }

      .auth-tab {
        padding: 10px 0;
        font-size: 0.9rem;
      }

      .auth-heading {
        font-size: 1.2rem;
        margin-bottom: 4px;
      }

      .auth-sub {
        font-size: 0.85rem;
        margin-bottom: 20px;
      }

      .auth-form {
        gap: 12px;
      }

      .auth-label {
        font-size: 0.8rem;
      }

      .auth-input {
        padding: 12px;
        font-size: 16px; /* iOS 줌 방지 */
        border-radius: 10px;
      }

      /* 모바일 버튼 최적화 - 54~60px */
      .auth-button {
        height: 54px;
        padding: 0 16px;
        border-radius: 14px;
        font-size: 0.95rem;
        margin-top: 4px;
      }

      .auth-separator {
        margin: 16px 0 8px;
      }

      .auth-social-btn {
        height: 48px;
        border-radius: 10px;
        font-size: 0.85rem;
      }

      .auth-helper-text {
        margin-top: 12px;
        font-size: 0.8rem;
      }

      .auth-row {
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <!-- LEFT -->
    <div class="auth-left">
      <div>
        <a href="<?php echo G5_URL;?>" class="auth-logo">
          <div class="auth-logo-icon">🔍</div>
          오늘로또
        </a>

        <div class="auth-title-block">
          <h1><span>1,180회차 데이터,</span><br>패턴이 보입니다</h1>
          <p>
            카카오 3초 시작으로 <strong>AI 분석 무료 1회</strong> 즉시!<br>
            AI가 오늘의 균형 잡힌 조합을 분석했습니다.
          </p>

          <div class="auth-benefits">
            <div class="auth-benefit">
              <div class="auth-benefit-icon">🔥</div>
              <span>최신 데이터 기반 패턴 분석 완료</span>
            </div>
            <div class="auth-benefit">
              <div class="auth-benefit-icon">✓</div>
              <span>인간이 보기 어려운 패턴까지 포착</span>
            </div>
            <div class="auth-benefit">
              <div class="auth-benefit-icon">✓</div>
              <span>무료 1회 사용 후 결정해보세요</span>
            </div>
          </div>
        </div>
      </div>

      <p class="auth-footer-text">
        © 2025 오늘로또 | 만 19세 이상 | 통계 기반 분석, 당첨 보장 아님
      </p>
    </div>

    <!-- RIGHT -->
    <div class="auth-right">
      <div class="auth-card">
        <div class="auth-tabs">
          <button class="auth-tab active" data-tab="login">로그인</button>
          <button class="auth-tab" data-tab="signup">회원가입</button>
        </div>

		<!-- 로그인 폼 -->
		<div class="auth-panel" id="panel-login">
		  <h2 class="auth-heading">다시 오셨군요 👋</h2>
		  <p class="auth-sub">당신을 위한 <span>맞춤형 조합</span>이 대기 중입니다.</p>

		  <!-- 실제 그누보드 로그인 폼 -->
		  <form name="flogin" id="flogin" class="auth-form"
				action="<?php echo $login_action_url; ?>" method="post" autocomplete="off"
				onsubmit="return flogin_submit(this);">
			<!-- 로그인 후 돌아갈 주소 -->
			<input type="hidden" name="url" value="<?php echo $login_url; ?>">

			<div class="auth-field">
			  <label class="auth-label">아이디</label>
			  <input type="text" name="mb_id" class="auth-input"
					 placeholder="아이디를 입력하세요">
			</div>
			<div class="auth-field">
			  <label class="auth-label">비밀번호</label>
			  <input type="password" name="mb_password" class="auth-input"
					 placeholder="비밀번호를 입력하세요">
			</div>
			<div class="auth-row">
			  <span></span>
			  <a href="<?php echo G5_BBS_URL; ?>/password_lost.php" class="auth-link">비밀번호 찾기</a>
			</div>
			<button type="submit" class="auth-button">
			  로그인 후 내 조합 확인하기 →
			</button>
		  </form>

			<div class="auth-separator">또는</div>

			<?php if (function_exists('social_check_login_before')) { ?>
			  <!-- 그누보드 소셜 로그인 (카카오/네이버 등) 실제 버튼 출력 -->
			  <div class="auth-social-wrap">
				<?php @include_once(get_social_skin_path().'/social_login.skin.php'); // 소셜로그인 사용시 소셜로그인 버튼 ?>
			  </div>
			<?php } ?>

		  <div class="auth-helper-text">
			아직 계정이 없으신가요?
			<a href="<?php echo G5_BBS_URL; ?>/register.php">회원가입</a>
		  </div>
		</div>

		<!-- 회원가입 폼 -->
		<div class="auth-panel" id="panel-signup" style="display:none;">
		  <h2 class="auth-heading">지금 가입하고 확인하기 ✨</h2>
		  <p class="auth-sub">
			가입만 해도 <span>무료 2회 제공</span> — 패턴이 지금 달라졌습니다.
		  </p>

		  <!-- 실제 그누보드 회원가입 폼 -->
		  <form name="fregister" id="fregister" class="auth-form"
				action="<?php echo G5_HTTPS_BBS_URL; ?>/register_form_update.php"
				method="post" autocomplete="off"
				onsubmit="return fregister_submit(this);">

			<!-- 신규 가입 -->
			<input type="hidden" name="w" value="">
			<!-- 가입 후 이동할 주소 (원하면 analysis_ai.php 로 바꿔도 됨) -->
			<input type="hidden" name="url" value="<?php echo G5_URL; ?>">
			<!-- 약관/개인정보 동의 (바로 동의한 상태로 전송) -->
			<input type="hidden" name="agree"  value="1">
			<input type="hidden" name="agree2" value="1">
			<!-- 닉네임은 이름과 동일하게 사용 -->
			<input type="hidden" name="mb_nick" value="">

			<div class="auth-field">
			  <label class="auth-label">아이디</label>
			  <input type="text" name="mb_id" class="auth-input"
					 placeholder="영문 소문자, 숫자 조합 3~20자">
			</div>

			<div class="auth-field">
			  <label class="auth-label">이름</label>
			  <input type="text" name="mb_name" class="auth-input"
					 placeholder="이름을 입력하세요">
			</div>

			<div class="auth-field">
			  <label class="auth-label">이메일</label>
			  <input type="email" name="mb_email" class="auth-input"
					 placeholder="example@email.com">
			</div>

			<div class="auth-field">
			  <label class="auth-label">비밀번호</label>
			  <input type="password" name="mb_password" class="auth-input"
					 placeholder="8자 이상 영문/숫자 조합">
			</div>

			<div class="auth-field">
			  <label class="auth-label">비밀번호 확인</label>
			  <input type="password" name="mb_password_re" class="auth-input"
					 placeholder="비밀번호를 다시 입력하세요">
			</div>

			<div class="auth-row" style="margin-top:8px;">
			  <label style="display:flex; align-items:center; gap:6px; font-size:0.82rem; color:var(--text-muted);">
				<input type="checkbox" id="agree_all" style="accent-color:#00d4aa;">
				<span>이용약관 및 개인정보 처리방침에 동의합니다</span>
			  </label>
			</div>

			<button type="submit" class="auth-button">
			  AI가 뽑은 번호 바로 확인하기 →
			</button>
		  </form>

		  <div class="auth-separator">또는</div>
			<style>
			  .auth-social-wrap a,
			  .auth-social-wrap button {
				width: 100%;
				display: block;
				margin-top: 8px;
			  }
			</style>

			<?php if (function_exists('social_check_login_before')) { ?>
			  <!-- 그누보드 소셜 로그인 (카카오/네이버 등) 실제 버튼 출력 -->
			  <div class="auth-social-wrap">
				<?php include(get_social_skin_path().'/social_login.skin.php'); // 소셜로그인 사용시 소셜로그인 버튼 ?>
			  </div>
			<?php } ?>

			<div class="auth-separator">또는</div>
		  <div class="auth-helper-text">
			이미 계정이 있으신가요? <a href="#" id="go-login">로그인</a>
		  </div>
		</div>
      </div>
    </div>
  </div>

  <script>
    const tabs = document.querySelectorAll('.auth-tab');
    const panelLogin = document.getElementById('panel-login');
    const panelSignup = document.getElementById('panel-signup');
    const goSignup = document.getElementById('go-signup');
    const goLogin = document.getElementById('go-login');

    function showTab(type) {
      tabs.forEach(t => t.classList.remove('active'));
      if (type === 'login') {
        panelLogin.style.display = 'block';
        panelSignup.style.display = 'none';
        tabs[0].classList.add('active');
      } else {
        panelLogin.style.display = 'none';
        panelSignup.style.display = 'block';
        tabs[1].classList.add('active');
      }
    }

    tabs[0].addEventListener('click', () => showTab('login'));
    tabs[1].addEventListener('click', () => showTab('signup'));
    goSignup.addEventListener('click', (e) => { e.preventDefault(); showTab('signup'); });
    goLogin.addEventListener('click', (e) => { e.preventDefault(); showTab('login'); });

    // 가입 유도 토스트 메시지 (10초 후)
    setTimeout(() => {
      if (!sessionStorage.getItem('authToastShown')) {
        showAuthToast();
        sessionStorage.setItem('authToastShown', 'true');
      }
    }, 10000);

    function showAuthToast() {
      const toast = document.createElement('div');
      toast.className = 'auth-toast';
      toast.innerHTML = `
        <div class="toast-icon">🔥</div>
        <div class="toast-content">
          <strong>이번 주 패턴 변동 감지!</strong>
          <span>무료 2회로 지금 확인하세요</span>
        </div>
        <button class="toast-close">✕</button>
      `;
      document.body.appendChild(toast);

      setTimeout(() => toast.classList.add('active'), 100);

      toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.classList.remove('active');
        setTimeout(() => toast.remove(), 300);
      });

      // Auto dismiss after 8 seconds
      setTimeout(() => {
        toast.classList.remove('active');
        setTimeout(() => toast.remove(), 300);
      }, 8000);
    }

    function flogin_submit(f) {
      if (!f.mb_id.value) {
        alert('아이디를 입력해 주세요.');
        f.mb_id.focus();
        return false;
      }
      if (!f.mb_password.value) {
        alert('비밀번호를 입력해 주세요.');
        f.mb_password.focus();
        return false;
      }
      return true;
    }

    function flogin_submit(f) {
      if (!f.mb_id.value) {
        alert('아이디를 입력해 주세요.');
        f.mb_id.focus();
        return false;
      }
      if (!f.mb_password.value) {
        alert('비밀번호를 입력해 주세요.');
        f.mb_password.focus();
        return false;
      }
      return true;
    }

    // ▼ 여기부터 추가
    function fregister_submit(f) {
      if (!f.mb_id.value) {
        alert('아이디를 입력해 주세요.');
        f.mb_id.focus();
        return false;
      }

	// 비밀번호 규칙:
	// - 8~20자
	// - 영문 최소 1개
	// - 숫자 최소 1개
	// - 특수문자(영문/숫자 제외 문자) 최소 1개
	var pwRule = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,20}$/;

	if (!pwRule.test(f.mb_password.value)) {
	  alert('비밀번호는 8~20자, 영문/숫자/특수문자를 모두 1개 이상 포함해야 합니다.');
	  f.mb_password.focus();
	  return false;
	}

      if (!f.mb_name.value) {
        alert('이름을 입력해 주세요.');
        f.mb_name.focus();
        return false;
      }

      if (!f.mb_email.value) {
        alert('이메일을 입력해 주세요.');
        f.mb_email.focus();
        return false;
      }

      if (!f.mb_password.value) {
        alert('비밀번호를 입력해 주세요.');
        f.mb_password.focus();
        return false;
      }

      if (f.mb_password.value.length < 4) { // 필요하면 8자로 올리셔도 됩니다.
        alert('비밀번호는 4자 이상으로 입력해 주세요.');
        f.mb_password.focus();
        return false;
      }

      if (!f.mb_password_re.value) {
        alert('비밀번호 확인을 입력해 주세요.');
        f.mb_password_re.focus();
        return false;
      }

      if (f.mb_password.value !== f.mb_password_re.value) {
        alert('비밀번호와 비밀번호 확인이 일치하지 않습니다.');
        f.mb_password_re.focus();
        return false;
      }

      var agreeAll = document.getElementById('agree_all');
      if (!agreeAll || !agreeAll.checked) {
        alert('이용약관 및 개인정보 처리방침에 동의해 주세요.');
        if (agreeAll) agreeAll.focus();
        return false;
      }

      // 닉네임은 이름과 동일하게 전송 (기본 설정에서 닉네임 필수일 수 있어서)
      if (f.mb_nick) {
        f.mb_nick.value = f.mb_name.value;
      }

      return true;
    }
  </script>

  <style>
    .auth-toast {
      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%) translateY(100px);
      background: linear-gradient(135deg, rgba(0, 212, 170, 0.95), rgba(0, 184, 148, 0.95));
      border-radius: 16px;
      padding: 14px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 10px 40px rgba(0, 212, 170, 0.3);
      z-index: 1000;
      opacity: 0;
      transition: all 0.3s ease;
      max-width: calc(100% - 32px);
    }

    .auth-toast.active {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }

    .toast-icon {
      font-size: 1.5rem;
    }

    .toast-content {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .toast-content strong {
      color: var(--primary-dark);
      font-size: 0.9rem;
    }

    .toast-content span {
      color: rgba(10, 15, 28, 0.7);
      font-size: 0.8rem;
    }

    .toast-close {
      background: rgba(0, 0, 0, 0.1);
      border: none;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      color: var(--primary-dark);
      cursor: pointer;
      font-size: 0.8rem;
    }

    @media (max-width: 640px) {
      .auth-toast {
        bottom: 80px;
        padding: 12px 16px;
      }
    }


	/* ────────────────────────────────
	 카카오 소셜 버튼 스타일 커스텀
	 (시안처럼 넓은 노란 버튼)
	 ──────────────────────────────── */
	.auth-social-wrap #sns_login {
		margin-top: 8px;
		padding: 0;
		background: transparent;
		border: 0;
	}

	.auth-social-wrap #sns_login h3 {
		display: none; /* "소셜계정으로 로그인" 문구 숨김 */
	}

	.auth-social-wrap #sns_login .sns-wrap {
		margin: 0;
	}

	.auth-social-wrap #sns_login .sns-wrap a.sns-kakao {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 100%;
		padding: 14px 18px;
		border-radius: 14px;
		background: #FEE500;
		color: #000;
		font-weight: 700;
		font-size: 0.95rem;
		border: none;
		text-decoration: none;
		box-shadow: 0 4px 14px rgba(254, 229, 0, 0.35);
	}

	.auth-social-wrap #sns_login .sns-wrap a.sns-kakao .ico {
		margin-right: 8px;
	}

	.auth-social-wrap #sns_login .sns-wrap a.sns-kakao .txt i {
		font-style: normal;
		font-weight: 400;
	}
  </style>
</body>
</html>


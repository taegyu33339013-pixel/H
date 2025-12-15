<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$g5['title'] = '오늘로또 - AI 기반 로또 번호 분석 | 1,201회차 데이터 실시간 반영';

include_once(G5_THEME_PATH.'/head.php');
?>

  <!-- Loading Screen -->
  <div class="loading-screen" id="loadingScreen">
    <div class="loading-logo">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
        <circle cx="9" cy="9" r="5" fill="#FFD75F"/>
        <circle cx="13" cy="12" r="4" stroke="#0B132B" stroke-width="2.5" fill="none"/>
        <line x1="16" y1="15" x2="20" y2="19" stroke="#0B132B" stroke-width="2.5" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="loading-text">오늘로또</div>
    <div class="loading-bar">
      <div class="loading-bar-fill"></div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu">
    <button class="mobile-menu-close" onclick="closeMobileMenu()">✕</button>
    <div class="mobile-menu-links">
      <a href="#features" class="mobile-menu-link" onclick="closeMobileMenu()">분석 기능</a>
      <a href="#stats" class="mobile-menu-link" onclick="closeMobileMenu()">통계</a>
      <a href="#pricing" class="mobile-menu-link" onclick="closeMobileMenu()">요금</a>
      <a href="#faq" class="mobile-menu-link" onclick="closeMobileMenu()">FAQ</a>
      <a href="auth.html" class="mobile-menu-link">로그인</a>
    </div>
    <a href="auth.html" class="mobile-menu-cta">무료로 시작하기</a>
  </div>

  <!-- Back to Top Button -->
  <button class="back-to-top" id="backToTop" onclick="scrollToTop()">↑</button>

	<!-- Navbar -->
	<nav class="li-navbar" id="navbar">
	  <div class="li-navbar-inner">
		<a href="/" class="li-nav-logo">
		  <div class="li-nav-logo-icon">
			<svg width="22" height="22" viewBox="0 0 32 32" fill="none">
			  <!-- 3D Lotto Ball -->
			  <circle cx="11" cy="12" r="8" fill="url(#gold-ball-idx)"/>
			  <ellipse cx="8" cy="9" rx="3" ry="2" fill="rgba(255,255,255,0.5)" transform="rotate(-25 8 9)"/>
			  <!-- AI Analysis Ring -->
			  <circle cx="18" cy="18" r="7" fill="none" stroke="#030711" stroke-width="2"/>
			  <!-- Neural Nodes -->
			  <circle cx="16" cy="16" r="1.2" fill="#030711"/>
			  <circle cx="20" cy="19" r="1.2" fill="#030711"/>
			  <circle cx="18" cy="14" r="1.2" fill="#030711"/>
			  <!-- Handle -->
			  <line x1="23" y1="23" x2="28" y2="28" stroke="#030711" stroke-width="2.5" stroke-linecap="round"/>
			  <defs>
				<linearGradient id="gold-ball-idx" x1="20%" y1="20%" x2="80%" y2="80%">
				  <stop offset="0%" stop-color="#ffe066"/>
				  <stop offset="50%" stop-color="#ffd700"/>
				  <stop offset="100%" stop-color="#cc9f00"/>
				</linearGradient>
			  </defs>
			</svg>
		  </div>
		  오늘로또
		</a>
		<div class="li-nav-menu">
		  <a href="#features" class="li-nav-link">분석 기능</a>
		  <a href="#stats" class="li-nav-link">통계</a>
		  <a href="#pricing" class="li-nav-link">요금</a>
		  <a href="#faq" class="li-nav-link">FAQ</a>
		  <a href="auth.html" class="li-nav-link">로그인</a>
		  <a href="auth.html" class="li-nav-cta">무료로 시작</a>
		</div>
		<button class="li-mobile-menu-btn" onclick="openMobileMenu()">
		  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
			<line x1="3" y1="12" x2="21" y2="12"></line>
			<line x1="3" y1="6" x2="21" y2="6"></line>
			<line x1="3" y1="18" x2="21" y2="18"></line>
		  </svg>
		</button>
	  </div>
	</nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="floating-ball floating-ball-1"></div>
    <div class="floating-ball floating-ball-2"></div>
    <div class="floating-ball floating-ball-3"></div>

    <div class="hero-container">
      <div class="hero-content">
        <div class="hero-badge">
          <span class="hero-badge-dot"></span>
          1,201회차 데이터 실시간 반영
        </div>
        <h1 class="hero-title">
          <span class="line">23년간의 패턴을</span>
          <span class="line"><span class="gradient">AI가 분석</span>합니다</span>
        </h1>
        <p class="hero-subtitle">
          동행복권 공식 데이터 7,206개 당첨번호를 AI가 분석하여<br>
          <strong>균형 잡힌 번호 조합</strong>을 제공합니다.
        </p>
        <div class="hero-cta-group">
          <a href="auth.html" class="hero-cta-primary">
            🔮 무료 분석 받기
          </a>
          <a href="#archive" class="hero-cta-secondary">
            AI 추천 기록 보기 →
          </a>
        </div>
        <div class="hero-trust">
          <div class="hero-trust-item">
            <span class="hero-trust-icon">✓</span>
            동행복권 공식 데이터
          </div>
          <div class="hero-trust-item">
            <span class="hero-trust-icon">✓</span>
            카카오 3초 가입
          </div>
          <div class="hero-trust-item">
            <span class="hero-trust-icon">✓</span>
            무료 1회 즉시 제공
          </div>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-card">
          <div class="hero-card-header">
            <div class="live-badge">
              <span class="live-dot"></span>
              LIVE 최신 결과
            </div>
            <span class="hero-card-round">1201회 · 2025-12-06</span>
          </div>
          <div class="hero-balls-container">
            <div class="hero-ball ball-yellow">7</div>
            <div class="hero-ball ball-yellow">9</div>
            <div class="hero-ball ball-red">24</div>
            <div class="hero-ball ball-red">27</div>
            <div class="hero-ball ball-gray">35</div>
            <div class="hero-ball ball-gray">36</div>
            <span class="bonus-sep">+</span>
            <div class="hero-ball ball-gray">37</div>
          </div>
          <div class="hero-card-info">
            <span class="hero-card-prize">
              1등 당첨금 <strong>14억 1,555만원</strong>
            </span>
            <a href="https://dhlottery.co.kr" target="_blank" rel="noopener" class="hero-card-link">
              공식 확인 →
            </a>
          </div>
          <div class="hero-card-status">
            <div class="status-mini">
              <span class="status-mini-dot ok"></span>
              <span>동행복권 API 연결</span>
            </div>
            <div class="status-mini">
              <span class="status-mini-dot ok"></span>
              <span>AI 엔진 가동중</span>
            </div>
            <div class="status-mini">
              <span class="status-mini-dot ok"></span>
              <span>1,201회 동기화</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Queue Position Banner -->
  <div class="queue-banner">
    <div class="queue-content">
      <span class="queue-icon">🎯</span>
      <span class="queue-text">오늘 <span class="queue-number" id="queueNumber">247</span>번째 분석자가 되세요!</span>
      <a href="auth.html" class="queue-cta">지금 분석하기</a>
    </div>
  </div>

  <!-- Countdown Timer Section -->
  <section class="countdown-section">
    <div class="countdown-container">
      <div class="countdown-card">
        <div class="countdown-label">
          <span class="countdown-pulse"></span>
          다음 추첨까지 남은 시간
        </div>
        <div class="countdown-timer" id="countdownTimer">
          <div class="countdown-item">
            <div class="countdown-value" id="countDays">2</div>
            <div class="countdown-unit">일</div>
          </div>
          <span class="countdown-sep">:</span>
          <div class="countdown-item">
            <div class="countdown-value" id="countHours">14</div>
            <div class="countdown-unit">시간</div>
          </div>
          <span class="countdown-sep">:</span>
          <div class="countdown-item">
            <div class="countdown-value" id="countMins">32</div>
            <div class="countdown-unit">분</div>
          </div>
          <span class="countdown-sep">:</span>
          <div class="countdown-item">
            <div class="countdown-value" id="countSecs">17</div>
            <div class="countdown-unit">초</div>
          </div>
        </div>
        <div class="countdown-info">
          ✨ 지금 분석하면 <strong>1202회차</strong>에 적용됩니다
        </div>
      </div>
    </div>
  </section>

  <!-- Live Activity Feed -->
  <section class="activity-section">
    <div class="activity-container">
      <div class="activity-header">
        <h3 class="activity-title">
          <span class="activity-pulse"></span>
          실시간 분석 현황
        </h3>
        <div class="activity-counter">
          📊 오늘 총 <strong id="todayCount">1,247</strong>회 분석 완료
        </div>
      </div>
      <div class="activity-feed" id="activityFeed">
        <div class="activity-item">
          <div class="activity-avatar">김*</div>
          <div class="activity-content">
            <p class="activity-text"><strong>김** (서울)</strong> 님이 <span class="style-tag">Hot/Cold 분석</span>을 완료했습니다</p>
          </div>
          <span class="activity-time">방금 전</span>
        </div>
        <div class="activity-item">
          <div class="activity-avatar">이*</div>
          <div class="activity-content">
            <p class="activity-text"><strong>이** (부산)</strong> 님이 <span class="style-tag">밸런스 최적화</span>를 완료했습니다</p>
          </div>
          <span class="activity-time">1분 전</span>
        </div>
        <div class="activity-item">
          <div class="activity-avatar">박*</div>
          <div class="activity-content">
            <p class="activity-text"><strong>박** (대전)</strong> 님이 <span class="style-tag">홀짝/고저 분석</span>을 완료했습니다</p>
          </div>
          <span class="activity-time">2분 전</span>
        </div>
        <div class="activity-item">
          <div class="activity-avatar">최*</div>
          <div class="activity-content">
            <p class="activity-text"><strong>최** (인천)</strong> 님이 <span class="style-tag">AI 추천</span>을 완료했습니다</p>
          </div>
          <span class="activity-time">3분 전</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Cumulative Stats Counter -->
  <section class="cumulative-section">
    <div class="cumulative-container">
      <div class="cumulative-grid">
        <div class="cumulative-item">
          <div class="cumulative-number" id="totalUsers">12,847</div>
          <div class="cumulative-label">가입 사용자</div>
        </div>
        <div class="cumulative-item">
          <div class="cumulative-number" id="totalAnalysis">87,234</div>
          <div class="cumulative-label">누적 분석 횟수</div>
        </div>
        <div class="cumulative-item">
          <div class="cumulative-number">1,201</div>
          <div class="cumulative-label">최신 반영 회차</div>
        </div>
      </div>
      <p class="cumulative-note">
        <span class="pulse-dot"></span>
        실시간 업데이트 중 (마지막 갱신: 3초 전)
      </p>
    </div>
  </section>

  <!-- AI Archive Section -->
  <section class="archive-section" id="archive">
    <div class="archive-container">
      <div class="section-header">
        <div class="section-badge">📈 성과 아카이브</div>
        <h2 class="section-title">최근 8주 AI 추천 기록</h2>
        <p class="section-subtitle">
          AI 추천과 실제 당첨 결과를 투명하게 공개합니다
        </p>
      </div>

      <div class="archive-card">
        <div class="archive-header">
          <div>회차</div>
          <div>AI 추천</div>
          <div>실제 당첨</div>
          <div>일치</div>
        </div>
        <div class="archive-table-body">
          <div class="archive-row">
            <div class="archive-round">1201회</div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">3</span>
              <span class="archive-ball ball-yellow matched">9</span>
              <span class="archive-ball ball-blue">15</span>
              <span class="archive-ball ball-red matched">27</span>
              <span class="archive-ball ball-gray matched">35</span>
              <span class="archive-ball ball-green">42</span>
            </div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">7</span>
              <span class="archive-ball ball-yellow">9</span>
              <span class="archive-ball ball-red">24</span>
              <span class="archive-ball ball-red">27</span>
              <span class="archive-ball ball-gray">35</span>
              <span class="archive-ball ball-gray">36</span>
            </div>
            <div class="archive-match">
              <span class="archive-match-num archive-match-good">3개</span>
            </div>
          </div>
          <div class="archive-row">
            <div class="archive-round">1200회</div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow matched">2</span>
              <span class="archive-ball ball-blue">11</span>
              <span class="archive-ball ball-blue">18</span>
              <span class="archive-ball ball-red matched">26</span>
              <span class="archive-ball ball-gray">34</span>
              <span class="archive-ball ball-green">41</span>
            </div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">2</span>
              <span class="archive-ball ball-yellow">9</span>
              <span class="archive-ball ball-blue">16</span>
              <span class="archive-ball ball-red">25</span>
              <span class="archive-ball ball-red">26</span>
              <span class="archive-ball ball-gray">38</span>
            </div>
            <div class="archive-match">
              <span class="archive-match-num archive-match-avg">2개</span>
            </div>
          </div>
          <div class="archive-row">
            <div class="archive-round">1199회</div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">5</span>
              <span class="archive-ball ball-blue">12</span>
              <span class="archive-ball ball-red matched">21</span>
              <span class="archive-ball ball-red matched">27</span>
              <span class="archive-ball ball-gray">33</span>
              <span class="archive-ball ball-green">40</span>
            </div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">6</span>
              <span class="archive-ball ball-blue">14</span>
              <span class="archive-ball ball-blue">16</span>
              <span class="archive-ball ball-red">21</span>
              <span class="archive-ball ball-red">27</span>
              <span class="archive-ball ball-gray">37</span>
            </div>
            <div class="archive-match">
              <span class="archive-match-num archive-match-avg">2개</span>
            </div>
          </div>
          <div class="archive-row">
            <div class="archive-round">1198회</div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow matched">1</span>
              <span class="archive-ball ball-blue matched">17</span>
              <span class="archive-ball ball-red">23</span>
              <span class="archive-ball ball-gray matched">31</span>
              <span class="archive-ball ball-gray">38</span>
              <span class="archive-ball ball-green">44</span>
            </div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">1</span>
              <span class="archive-ball ball-blue">17</span>
              <span class="archive-ball ball-blue">19</span>
              <span class="archive-ball ball-red">25</span>
              <span class="archive-ball ball-gray">31</span>
              <span class="archive-ball ball-green">41</span>
            </div>
            <div class="archive-match">
              <span class="archive-match-num archive-match-good">3개</span>
            </div>
          </div>
          <div class="archive-row">
            <div class="archive-round">1197회</div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">4</span>
              <span class="archive-ball ball-blue matched">13</span>
              <span class="archive-ball ball-blue">19</span>
              <span class="archive-ball ball-red matched">28</span>
              <span class="archive-ball ball-gray">36</span>
              <span class="archive-ball ball-green">43</span>
            </div>
            <div class="archive-balls">
              <span class="archive-ball ball-yellow">8</span>
              <span class="archive-ball ball-blue">13</span>
              <span class="archive-ball ball-red">22</span>
              <span class="archive-ball ball-red">28</span>
              <span class="archive-ball ball-gray">32</span>
              <span class="archive-ball ball-green">45</span>
            </div>
            <div class="archive-match">
              <span class="archive-match-num archive-match-avg">2개</span>
            </div>
          </div>
        </div>
        <div class="archive-summary">
          <div class="archive-summary-item">
            <div class="archive-summary-value">2.3개</div>
            <div class="archive-summary-label">8주 평균 일치</div>
          </div>
          <div class="archive-summary-item">
            <div class="archive-summary-value">4개</div>
            <div class="archive-summary-label">최고 일치 (1187회)</div>
          </div>
          <div class="archive-summary-item">
            <div class="archive-summary-value">52주</div>
            <div class="archive-summary-label">누적 기록</div>
          </div>
        </div>

        <!-- 데이터 검증 (합쳐진 버전) -->
        <div class="archive-verify">
          <div class="archive-verify-header">
            <span class="archive-verify-icon">🔎</span>
            <span>특정 회차 데이터 검증</span>
          </div>
          <div class="archive-verify-input-group">
            <input type="number" class="archive-verify-input" id="verifyInput" placeholder="회차 입력 (예: 1201)" min="1" max="1201">
            <button class="archive-verify-btn" onclick="verifyData()">검증</button>
          </div>
          <div class="archive-verify-result" id="verifyResult" style="display: none;">
            <div class="archive-verify-result-header">
              <span class="archive-verify-title" id="verifyTitle">1201회차</span>
              <span class="archive-verify-status">✓ 일치</span>
            </div>
            <div class="archive-verify-balls" id="verifyBalls"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Quality Gauge Section -->
  <section class="quality-section">
    <div class="quality-container">
      <div class="quality-card">
        <div class="quality-header">
          <h3 class="quality-title">📊 AI 생성 번호 평균 품질</h3>
          <p class="quality-subtitle">오늘로또 AI가 생성하는 번호의 평균 품질 지표</p>
        </div>
        <div class="quality-gauges">
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">⚖️ 균형 점수</span>
              <span class="quality-gauge-value">82점</span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill excellent" style="width: 82%"></div>
            </div>
            <p class="quality-desc">홀짝, 고저, 색상 분포 균형도</p>
          </div>
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">🧮 AC값</span>
              <span class="quality-gauge-value">9.2</span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill excellent" style="width: 92%"></div>
            </div>
            <p class="quality-desc">번호 조합의 다양성 지수 (최대 10)</p>
          </div>
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">🎯 패턴 적합도</span>
              <span class="quality-gauge-value">78점</span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill good" style="width: 78%"></div>
            </div>
            <p class="quality-desc">역대 당첨 패턴과의 유사도</p>
          </div>
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">🔥 Hot/Cold 반영</span>
              <span class="quality-gauge-value">85점</span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill excellent" style="width: 85%"></div>
            </div>
            <p class="quality-desc">최근 출현 빈도 반영 정도</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Algorithm Transparency Section -->
  <section class="algorithm-section">
    <div class="algorithm-container">
      <div class="section-header">
        <div class="section-badge">🔍 100% 투명 공개</div>
        <h2 class="section-title">알고리즘 완전 공개</h2>
        <p class="section-subtitle">
          숨기는 것 없이, 분석 방법을 모두 공개합니다
        </p>
      </div>

      <div class="algorithm-card">
        <div class="algorithm-steps">
          <div class="algorithm-step">
            <span class="algorithm-step-num">1</span>
            <div class="algorithm-step-icon">📥</div>
            <h4 class="algorithm-step-title">데이터 수집</h4>
            <p class="algorithm-step-desc">동행복권 공식 API에서<br>1~1,201회차 데이터 수집</p>
          </div>
          <span class="algorithm-arrow">→</span>
          <div class="algorithm-step">
            <span class="algorithm-step-num">2</span>
            <div class="algorithm-step-icon">📊</div>
            <h4 class="algorithm-step-title">통계 분석</h4>
            <p class="algorithm-step-desc">빈도, 패턴, 연관성<br>다각도 통계 분석</p>
          </div>
          <span class="algorithm-arrow">→</span>
          <div class="algorithm-step">
            <span class="algorithm-step-num">3</span>
            <div class="algorithm-step-icon">⚖️</div>
            <h4 class="algorithm-step-title">균형 점수화</h4>
            <p class="algorithm-step-desc">홀짝, 고저, AC값 등<br>다중 기준 점수 산정</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features-section" id="features">
    <div class="features-container">
      <div class="section-header">
        <div class="section-badge">✨ 핵심 기능</div>
        <h2 class="section-title">AI가 분석하는 5가지 패턴</h2>
        <p class="section-subtitle">
          23년간의 당첨 데이터에서 통계적으로 의미 있는 패턴을 찾습니다
        </p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">🔥</div>
          <h3 class="feature-title">Hot/Cold 분석</h3>
          <p class="feature-desc">
            최근 100회 기준 과출/미출 번호를 분석하여 균형 잡힌 조합을 생성합니다.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">⚖️</div>
          <h3 class="feature-title">홀짝/고저 균형</h3>
          <p class="feature-desc">
            역대 당첨번호의 홀짝, 고저 비율 패턴을 분석하여 최적의 균형을 찾습니다.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🎨</div>
          <h3 class="feature-title">색상볼 통계</h3>
          <p class="feature-desc">
            번호대별 색상 분포를 분석하여 다양성이 높은 조합을 제안합니다.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🧠</div>
          <h3 class="feature-title">상관관계 분석</h3>
          <p class="feature-desc">
            특정 번호가 함께 출현하는 동반출현 패턴을 AI가 학습합니다.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🧮</div>
          <h3 class="feature-title">AC값 분석</h3>
          <p class="feature-desc">
            번호 조합의 다양성 지수(AC값)를 계산하여 품질을 평가합니다.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">📋</div>
          <h3 class="feature-title">투명한 리포트</h3>
          <p class="feature-desc">
            각 번호가 선정된 이유와 통계적 근거를 상세히 제공합니다.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Statistics Dashboard -->
  <section class="dashboard-section" id="stats">
    <div class="dashboard-container">
      <div class="section-header">
        <div class="section-badge">📊 실제 데이터</div>
        <h2 class="section-title">1,201회차 통계 대시보드</h2>
        <p class="section-subtitle">
          동행복권 공식 데이터 기반 실시간 통계
        </p>
      </div>

      <div class="dashboard-grid">
        <div class="dashboard-card">
          <h3 class="dashboard-card-title">🔥 최다 출현 번호 (TOP 5)</h3>
          <div class="hot-numbers">
            <div class="hot-ball ball-red">
              27
              <span class="ball-count">191</span>
            </div>
            <div class="hot-ball ball-gray">
              34
              <span class="ball-count">188</span>
            </div>
            <div class="hot-ball ball-green">
              43
              <span class="ball-count">186</span>
            </div>
            <div class="hot-ball ball-yellow">
              10
              <span class="ball-count">186</span>
            </div>
            <div class="hot-ball ball-gray">
              33
              <span class="ball-count">185</span>
            </div>
          </div>
        </div>

        <div class="dashboard-card">
          <h3 class="dashboard-card-title">❄️ 최소 출현 번호 (BOTTOM 5)</h3>
          <div class="cold-numbers">
            <div class="cold-ball ball-red">
              23
              <span class="ball-count">173</span>
            </div>
            <div class="cold-ball ball-yellow">
              9
              <span class="ball-count">174</span>
            </div>
            <div class="cold-ball ball-red">
              29
              <span class="ball-count">174</span>
            </div>
            <div class="cold-ball ball-yellow">
              2
              <span class="ball-count">175</span>
            </div>
            <div class="cold-ball ball-red">
              22
              <span class="ball-count">175</span>
            </div>
          </div>
        </div>

        <div class="dashboard-card">
          <h3 class="dashboard-card-title">⚖️ 역대 홀짝 비율 분포</h3>
          <div class="ratio-bars">
            <div class="ratio-item">
              <span class="ratio-label">3:3</span>
              <div class="ratio-bar"><div class="ratio-fill" style="width: 32%"></div></div>
              <span class="ratio-value">32%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">4:2</span>
              <div class="ratio-bar"><div class="ratio-fill" style="width: 28%"></div></div>
              <span class="ratio-value">28%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">2:4</span>
              <div class="ratio-bar"><div class="ratio-fill" style="width: 27%"></div></div>
              <span class="ratio-value">27%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">5:1</span>
              <div class="ratio-bar"><div class="ratio-fill" style="width: 7%"></div></div>
              <span class="ratio-value">7%</span>
            </div>
          </div>
        </div>

        <div class="dashboard-card">
          <h3 class="dashboard-card-title">📈 역대 패턴 분석</h3>
          <div class="ratio-bars">
            <div class="ratio-item">
              <span class="ratio-label">합계 100~170</span>
              <div class="ratio-bar"><div class="ratio-fill" style="width: 75%"></div></div>
              <span class="ratio-value">75%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">연속번호 포함</span>
              <div class="ratio-bar"><div class="ratio-fill" style="width: 42%"></div></div>
              <span class="ratio-value">42%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">같은 끝자리 2+</span>
              <div class="ratio-bar"><div class="ratio-fill" style="width: 68%"></div></div>
              <span class="ratio-value">68%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Prize Simulator Section -->
  <section class="prize-section">
    <div class="prize-container">
      <div class="prize-card">
        <div class="prize-icon">💰</div>
        <h2 class="prize-title">만약 이 번호로 당첨된다면?</h2>
        <p class="prize-subtitle">1202회 예상 당첨금 시뮬레이션 (추정치)</p>
        <div class="prize-grid">
          <div class="prize-item first">
            <div class="prize-rank">🥇 1등</div>
            <div class="prize-amount">약 14억원</div>
            <div class="prize-after-tax">세후 <span>약 9.8억원</span></div>
          </div>
          <div class="prize-item">
            <div class="prize-rank">🥈 2등</div>
            <div class="prize-amount">약 5,800만원</div>
            <div class="prize-after-tax">세후 <span>약 4,900만원</span></div>
          </div>
          <div class="prize-item">
            <div class="prize-rank">🥉 3등</div>
            <div class="prize-amount">약 150만원</div>
            <div class="prize-after-tax">세후 <span>약 127만원</span></div>
          </div>
        </div>
        <p class="prize-note">
          ※ 당첨금은 판매액과 당첨자 수에 따라 변동됩니다. 위 금액은 최근 평균 기준 추정치입니다.
        </p>
      </div>
    </div>
  </section>

  <!-- Never Happened Warning Section -->
  <section class="warning-section">
    <div class="warning-container">
      <div class="warning-card">
        <div class="warning-header">
          <div class="warning-icon">⚠️</div>
          <div>
            <h3 class="warning-title">역대 1,201회 중 이런 패턴은 없었습니다</h3>
            <p class="warning-subtitle">AI는 아래 패턴을 자동으로 회피합니다</p>
          </div>
        </div>
        <div class="warning-list">
          <div class="warning-item">
            <div class="warning-item-icon">🔢</div>
            <div class="warning-item-content">
              <h4>연속번호 4개 이상</h4>
              <p>예: 12, 13, 14, 15, 22, 33<br>발생 횟수: <span class="warning-item-stat">0회</span></p>
            </div>
          </div>
          <div class="warning-item">
            <div class="warning-item-icon">📊</div>
            <div class="warning-item-content">
              <h4>합계 70 이하 또는 180 이상</h4>
              <p>극단적인 합계 범위<br>발생 횟수: <span class="warning-item-stat">0회</span></p>
            </div>
          </div>
          <div class="warning-item">
            <div class="warning-item-icon">🎨</div>
            <div class="warning-item-content">
              <h4>한 색상에서 6개 모두</h4>
              <p>예: 모두 1~10 또는 모두 41~45<br>발생 횟수: <span class="warning-item-stat">0회</span></p>
            </div>
          </div>
          <div class="warning-item">
            <div class="warning-item-icon">⚖️</div>
            <div class="warning-item-content">
              <h4>홀수 6개 또는 짝수 6개</h4>
              <p>완전한 홀짝 편중<br>발생 횟수: <span class="warning-item-stat">0회</span></p>
            </div>
          </div>
        </div>
        <div class="warning-footer">
          <span class="warning-footer-icon">✅</span>
          <p class="warning-footer-text">
            오늘로또 AI는 위 패턴을 <strong>100% 자동 회피</strong>하여 균형 잡힌 번호만 생성합니다.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- User Reviews Section -->
  <section class="reviews-section">
    <div class="reviews-container">
      <div class="section-header">
        <div class="section-badge">💬 사용자 후기</div>
        <h2 class="section-title">실제 사용자들의 이야기</h2>
      </div>

      <div class="reviews-grid">
        <div class="review-card">
          <p class="review-content">
            "번호 선택할 때 고민이 줄었어요. 당첨은 운이지만 최소한 균형 잡힌 조합이라 만족합니다. 분석 리포트가 상세해서 좋아요."
          </p>
          <div class="review-author">
            <div class="review-avatar">김*</div>
            <div class="review-info">
              <div class="review-name">김** (30대, 직장인)</div>
              <div class="review-meta">서울 · 6개월 사용</div>
            </div>
          </div>
        </div>
        <div class="review-card">
          <p class="review-content">
            "알고리즘이 투명하게 공개되어 있어서 믿고 씁니다. 당첨 보장은 없지만 참고용으로 좋아요. 가격도 합리적이에요."
          </p>
          <div class="review-author">
            <div class="review-avatar">이*</div>
            <div class="review-info">
              <div class="review-name">이** (40대, 자영업)</div>
              <div class="review-meta">부산 · 3개월 사용</div>
            </div>
          </div>
        </div>
        <div class="review-card">
          <p class="review-content">
            "다른 곳처럼 당첨 보장 안 하고 솔직해서 오히려 신뢰가 가요. 통계 데이터 보는 재미도 있고, 번호 뽑는 시간이 줄었어요."
          </p>
          <div class="review-author">
            <div class="review-avatar">박*</div>
            <div class="review-info">
              <div class="review-name">박** (50대, 회사원)</div>
              <div class="review-meta">대구 · 1년 사용</div>
            </div>
          </div>
        </div>
        <div class="review-card">
          <p class="review-content">
            "무료로 1회 해보고 괜찮아서 계속 쓰고 있어요. 매주 분석 결과 비교해서 공개하는 것도 좋고, 뭔가 숨기지 않는 느낌?"
          </p>
          <div class="review-author">
            <div class="review-avatar">최*</div>
            <div class="review-info">
              <div class="review-name">최** (20대, 학생)</div>
              <div class="review-meta">인천 · 2개월 사용</div>
            </div>
          </div>
        </div>
      </div>

      <div class="reviews-disclaimer">
        ⚠️ 실제 사용자 후기이며, 당첨을 보장하지 않습니다. 로또는 확률 게임입니다.
      </div>
    </div>
  </section>

  <!-- Community Section -->
  <section class="community-section">
    <div class="community-container">
      <div class="section-header">
        <div class="section-badge">👥 커뮤니티</div>
        <h2 class="section-title">사용자들의 번호 조합</h2>
        <p class="section-subtitle">
          다른 사용자들의 AI 분석 결과와 전략을 구경해 보세요
        </p>
      </div>

      <div class="community-grid">
        <div class="community-post">
          <div class="community-post-header">
            <div class="community-avatar">김*</div>
            <div class="community-author-info">
              <div class="community-author">김**</div>
              <div class="community-date">오늘 14:32</div>
            </div>
            <span class="community-tag">Hot/Cold</span>
          </div>
          <h4 class="community-title">이번 주 Hot 번호 위주로 뽑아봤어요</h4>
          <div class="community-balls">
            <span class="community-ball ball-yellow">3</span>
            <span class="community-ball ball-blue">12</span>
            <span class="community-ball ball-red">27</span>
            <span class="community-ball ball-gray">34</span>
            <span class="community-ball ball-gray">38</span>
            <span class="community-ball ball-green">43</span>
          </div>
          <div class="community-stats">
            <span class="community-stat">
              <span class="community-stat-icon">👍</span> 24
            </span>
            <span class="community-stat">
              <span class="community-stat-icon">💬</span> 8
            </span>
            <span class="community-stat">
              <span class="community-stat-icon">👁️</span> 156
            </span>
          </div>
        </div>

        <div class="community-post">
          <div class="community-post-header">
            <div class="community-avatar">이*</div>
            <div class="community-author-info">
              <div class="community-author">이**</div>
              <div class="community-date">오늘 11:15</div>
            </div>
            <span class="community-tag">밸런스</span>
          </div>
          <h4 class="community-title">홀짝 3:3, 고저 균형 맞춘 조합!</h4>
          <div class="community-balls">
            <span class="community-ball ball-yellow">7</span>
            <span class="community-ball ball-blue">14</span>
            <span class="community-ball ball-red">21</span>
            <span class="community-ball ball-red">28</span>
            <span class="community-ball ball-gray">35</span>
            <span class="community-ball ball-green">42</span>
          </div>
          <div class="community-stats">
            <span class="community-stat">
              <span class="community-stat-icon">👍</span> 31
            </span>
            <span class="community-stat">
              <span class="community-stat-icon">💬</span> 12
            </span>
            <span class="community-stat">
              <span class="community-stat-icon">👁️</span> 203
            </span>
          </div>
        </div>

        <div class="community-post">
          <div class="community-post-header">
            <div class="community-avatar">박*</div>
            <div class="community-author-info">
              <div class="community-author">박**</div>
              <div class="community-date">어제 22:48</div>
            </div>
            <span class="community-tag">AC값</span>
          </div>
          <h4 class="community-title">AC값 10으로 다양성 최대화</h4>
          <div class="community-balls">
            <span class="community-ball ball-yellow">2</span>
            <span class="community-ball ball-blue">11</span>
            <span class="community-ball ball-blue">19</span>
            <span class="community-ball ball-red">26</span>
            <span class="community-ball ball-gray">33</span>
            <span class="community-ball ball-green">45</span>
          </div>
          <div class="community-stats">
            <span class="community-stat">
              <span class="community-stat-icon">👍</span> 18
            </span>
            <span class="community-stat">
              <span class="community-stat-icon">💬</span> 5
            </span>
            <span class="community-stat">
              <span class="community-stat-icon">👁️</span> 98
            </span>
          </div>
        </div>
      </div>

      <div class="community-cta">
        <a href="auth.html" class="community-cta-btn">
          커뮤니티 참여하기 →
        </a>
      </div>
    </div>
  </section>

  <!-- Notification Section -->
  <section class="notification-section">
    <div class="notification-container">
      <div class="notification-card">
        <div class="notification-icon">🔔</div>
        <h2 class="notification-title">당첨 결과 알림 받기</h2>
        <p class="notification-desc">
          매주 토요일 추첨 후, AI 추천 번호와 실제 당첨 번호 비교 결과를<br>
          카카오톡으로 바로 받아보세요!
        </p>
        <div class="notification-benefits">
          <span class="notification-benefit">
            <span class="notification-benefit-icon">✓</span>
            추첨 직후 자동 알림
          </span>
          <span class="notification-benefit">
            <span class="notification-benefit-icon">✓</span>
            일치 번호 하이라이트
          </span>
          <span class="notification-benefit">
            <span class="notification-benefit-icon">✓</span>
            다음 주 AI 추천 미리보기
          </span>
        </div>
        <form class="notification-form" onsubmit="subscribeNotification(event)">
          <input type="tel" class="notification-input" placeholder="카카오톡 연결 전화번호" id="notificationPhone">
          <button type="submit" class="notification-submit">알림 신청</button>
        </form>
        <p class="notification-note">* 무료 서비스입니다. 언제든 해지 가능합니다.</p>
      </div>
    </div>
  </section>

  <!-- Disclaimer Section -->
  <section class="disclaimer-section">
    <div class="disclaimer-container">
      <div class="disclaimer-card">
        <div class="disclaimer-header">
          ⚠️ 중요: 오늘로또가 보장하지 않는 것
        </div>
        <div class="disclaimer-grid">
          <div class="disclaimer-col">
            <h4>❌ 우리가 보장하지 않는 것</h4>
            <ul class="disclaimer-list">
              <li>
                <span class="icon-x">✗</span>
                당첨 확률 향상 (모든 조합 확률 동일: 1/8,145,060)
              </li>
              <li>
                <span class="icon-x">✗</span>
                당첨 번호 예측 (미래는 예측 불가능)
              </li>
              <li>
                <span class="icon-x">✗</span>
                수익 보장 (복권은 손실 가능성 있음)
              </li>
            </ul>
          </div>
          <div class="disclaimer-col">
            <h4>✅ 우리가 제공하는 것</h4>
            <ul class="disclaimer-list">
              <li>
                <span class="icon-check">✓</span>
                역대 당첨번호의 통계적 패턴 분석
              </li>
              <li>
                <span class="icon-check">✓</span>
                균형 잡힌 번호 조합 (홀짝, 고저, 색상 등)
              </li>
              <li>
                <span class="icon-check">✓</span>
                번호 선정의 투명한 근거
              </li>
            </ul>
          </div>
        </div>
        <div class="disclaimer-footer">
          <p>💡 즐거움을 위한 참고 도구로만 사용해 주세요.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="pricing-section" id="pricing">
    <div class="pricing-container">
      <div class="section-header">
        <div class="section-badge">💰 합리적인 가격</div>
        <h2 class="section-title">심플한 요금제</h2>
        <p class="section-subtitle">
          가입 즉시 무료 1회! 추가 분석은 회당 200원
        </p>
      </div>

      <div class="pricing-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="pricing-card">
          <div class="pricing-name">무료 체험</div>
          <div class="pricing-desc">가입만 해도 즉시 제공</div>
          <div class="pricing-price">
            <span class="pricing-amount">무료</span>
            <span class="pricing-period">/ 1회</span>
          </div>
          <ul class="pricing-features">
            <li><span class="check">✓</span> AI 분석 1회 무료</li>
            <li><span class="check">✓</span> 10가지 분석 스타일</li>
            <li><span class="check">✓</span> 선정 이유 제공</li>
            <li><span class="check">✓</span> 균형 점수 리포트</li>
          </ul>
          <a href="auth.html" class="pricing-cta pricing-cta-secondary">
            무료로 시작하기
          </a>
        </div>

        <div class="pricing-card featured">
          <div class="pricing-badge">인기</div>
          <div class="pricing-name">크레딧 충전</div>
          <div class="pricing-desc">필요한 만큼만 충전</div>
          <div class="pricing-price">
            <span class="pricing-amount">200원</span>
            <span class="pricing-period">/ 1회</span>
          </div>
          <ul class="pricing-features">
            <li><span class="check">✓</span> 무료 체험 모든 기능</li>
            <li><span class="check">✓</span> 분석 내역 저장</li>
            <li><span class="check">✓</span> 10회 시 2회 보너스</li>
            <li><span class="check">✓</span> 우선 고객 지원</li>
          </ul>
          <a href="auth.html" class="pricing-cta pricing-cta-primary">
            크레딧 충전하기
          </a>
        </div>

        <div class="pricing-card subscription">
          <div class="pricing-badge">💎 BEST</div>
          <div class="pricing-name">월간 구독</div>
          <div class="pricing-desc">헤비 유저를 위한 무제한</div>
          <div class="pricing-price">
            <span class="pricing-amount">2,900원</span>
            <span class="pricing-period">/ 월</span>
          </div>
          <span class="pricing-savings">15회 이상 이용 시 이득!</span>
          <ul class="pricing-features">
            <li><span class="check">✓</span> <strong>무제한</strong> AI 분석</li>
            <li><span class="check">✓</span> 커뮤니티 프리미엄 배지</li>
            <li><span class="check">✓</span> 당첨 결과 카톡 알림</li>
            <li><span class="check">✓</span> 과거 분석 데이터 열람</li>
          </ul>
          <a href="auth.html" class="pricing-cta pricing-cta-primary">
            구독 시작하기
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="faq-section" id="faq">
    <div class="faq-container">
      <div class="section-header">
        <div class="section-badge">❓ 자주 묻는 질문</div>
        <h2 class="section-title">궁금한 점이 있으신가요?</h2>
      </div>

      <div class="faq-list">
        <div class="faq-item active">
          <button class="faq-question">
            정말 당첨 확률이 높아지나요?
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-answer">
            <p class="faq-answer-content">
              아니요. 모든 로또 조합의 당첨 확률은 동일합니다 (1/8,145,060). 
              오늘로또는 당첨을 보장하거나 예측하지 않습니다. 
              역대 당첨번호의 통계적 패턴을 분석하여 균형 잡힌 조합을 제안하는 참고 정보입니다.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            데이터는 어디서 가져오나요?
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-answer">
            <p class="faq-answer-content">
              동행복권(dhlottery.co.kr) 공식 사이트의 데이터를 사용합니다. 
              1회부터 현재까지의 모든 당첨번호를 분석하며, 매주 토요일 추첨 후 자동으로 업데이트됩니다.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            환불이 가능한가요?
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-answer">
            <p class="faq-answer-content">
              미사용 크레딧에 한해 결제일로부터 7일 이내 환불이 가능합니다. 
              사용한 크레딧, 무료 제공 크레딧, 보너스 크레딧은 환불되지 않습니다.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question">
            만 19세 미만도 이용할 수 있나요?
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-answer">
            <p class="faq-answer-content">
              아니요. 오늘로또는 만 19세 이상만 이용할 수 있습니다. 
              로또 구매는 반드시 동행복권 공식 판매처에서만 가능합니다.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Media & Security Badges Section -->
  <section class="badges-section">
    <div class="badges-container">
      <div class="badges-grid">
        <div class="badges-col">
          <h4 class="badges-col-title">📰 미디어 소개</h4>
          <div class="media-badges">
            <div class="media-badge">
              <div class="media-badge-name">IT조선</div>
              <div class="media-badge-quote">"AI 로또 분석의 새 패러다임"</div>
            </div>
            <div class="media-badge">
              <div class="media-badge-name">블로터</div>
              <div class="media-badge-quote">"투명한 알고리즘 공개 눈길"</div>
            </div>
            <div class="media-badge">
              <div class="media-badge-name">테크M</div>
              <div class="media-badge-quote">"데이터 기반 번호 분석"</div>
            </div>
          </div>
        </div>
        <div class="badges-col">
          <h4 class="badges-col-title">🔒 보안 인증</h4>
          <div class="security-badges">
            <div class="security-badge">
              <span class="security-badge-icon">🔐</span>
              <span class="security-badge-text">SSL 256bit 암호화</span>
            </div>
            <div class="security-badge">
              <span class="security-badge-icon">🛡️</span>
              <span class="security-badge-text">개인정보 최소 수집</span>
            </div>
            <div class="security-badge">
              <span class="security-badge-icon">💳</span>
              <span class="security-badge-text">토스페이먼츠 안전결제</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Trust Section -->
  <section class="trust-section">
    <div class="trust-container">
      <div class="trust-grid">
        <div class="trust-badge">
          <span class="trust-badge-icon">✓</span>
          동행복권 공식 데이터 사용
        </div>
        <div class="trust-badge">
          <span class="trust-badge-icon">✓</span>
          1,201회차 실시간 반영
        </div>
        <div class="trust-badge">
          <span class="trust-badge-icon">✓</span>
          23년간 7,206개 번호 분석
        </div>
        <div class="trust-badge">
          <span class="trust-badge-icon">✓</span>
          알고리즘 100% 투명 공개
        </div>
      </div>
    </div>
  </section>

  <!-- Final CTA Section -->
  <section class="cta-section">
    <div class="cta-container">
      <h2 class="cta-title">지금 바로 시작하세요</h2>
      <p class="cta-subtitle">
        카카오 3초 가입으로 무료 분석 1회를 즉시 받아보세요.<br>
        AI가 분석한 균형 잡힌 번호 조합이 기다리고 있습니다.
      </p>
      <a href="auth.html" class="cta-button">
        💫 무료로 시작하기
      </a>
      <p class="cta-note">신용카드 불필요 · 3초 가입 · 즉시 분석</p>
    </div>
  </section>

  <!-- Floating Share Button -->
  <div class="floating-share" id="floatingShare">
    <div class="share-buttons">
      <button class="share-btn share-kakao" onclick="shareKakao()" title="카카오톡 공유">
        💬
      </button>
      <button class="share-btn share-twitter" onclick="shareTwitter()" title="트위터 공유">
        𝕏
      </button>
      <button class="share-btn share-facebook" onclick="shareFacebook()" title="페이스북 공유">
        f
      </button>
      <button class="share-btn share-link" onclick="copyLink()" title="링크 복사">
        🔗
      </button>
    </div>
    <button class="share-toggle" onclick="toggleShare()" title="공유하기">
      📤
    </button>
  </div>

  <!-- Share Modal -->
  <div class="share-modal" id="shareModal">
    <div class="share-modal-content">
      <button class="share-modal-close" onclick="closeShareModal()">✕</button>
      <h3 class="share-modal-title">🎱 이번 주 AI 추천 번호</h3>
      <div class="share-modal-preview">
        <p class="share-modal-round">1202회차 AI 추천</p>
        <div class="share-modal-balls">
          <span class="share-modal-ball ball-yellow">5</span>
          <span class="share-modal-ball ball-blue">13</span>
          <span class="share-modal-ball ball-red">22</span>
          <span class="share-modal-ball ball-red">28</span>
          <span class="share-modal-ball ball-gray">34</span>
          <span class="share-modal-ball ball-green">41</span>
        </div>
        <p class="share-modal-text">오늘로또 AI가 분석한 균형 잡힌 번호!</p>
      </div>
      <div class="share-modal-buttons">
        <button class="share-modal-btn kakao" onclick="shareKakao()">
          <span class="share-modal-btn-icon">💬</span>
          카카오톡
        </button>
        <button class="share-modal-btn twitter" onclick="shareTwitter()">
          <span class="share-modal-btn-icon">𝕏</span>
          트위터
        </button>
        <button class="share-modal-btn facebook" onclick="shareFacebook()">
          <span class="share-modal-btn-icon">f</span>
          페이스북
        </button>
        <button class="share-modal-btn copy" onclick="copyLink()">
          <span class="share-modal-btn-icon">🔗</span>
          링크 복사
        </button>
      </div>
    </div>
  </div>

  <!-- Copy Toast -->
  <div class="copy-toast" id="copyToast">✓ 링크가 복사되었습니다!</div>

    <!-- Background Effects -->
    <div class="bg-grid"></div>
    <div class="floating-orb orb-1"></div>
    <div class="floating-orb orb-2"></div>

    <!-- 당첨 알림 피드 -->
    <div class="winner-feed" id="winner-feed"></div>

	<!-- Navigation -->
	<nav class="navbar">
		<div class="container nav-content">
			<a href="/" class="logo" aria-label="오늘로또 메인으로 이동">
				<div class="logo-icon" aria-hidden="true">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none">
					  <circle cx="9" cy="9" r="6" fill="#FFD75F"/>
					  <circle cx="14" cy="12" r="5" stroke="currentColor" stroke-width="2" fill="none"/>
					  <line x1="18" y1="16" x2="22" y2="20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
					</svg>
				</div>
				오늘로또
			</a>

			<?php
			// 로그인 여부에 따라 이동 경로 분기
			// 1) 로그인 상태  → /result.php
			// 2) 비로그인 상태 → /bbs/login.php (로그인 후 result.php로 돌아오도록 url 파라미터 포함)
			$cta_url = $is_member
				? G5_URL.'/result.php'
				: G5_BBS_URL.'/login.php?url='.urlencode(G5_URL.'/result.php');
			?>
			<a href="<?php echo $cta_url; ?>"
			   class="nav-cta"
			   aria-label="카카오 3초 시작 무료 1회">
				무료 1회 받기 →
			</a>
		</div>
	</nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <div class="hero-badge">
                    <span class="badge-pulse"></span>
                    <span class="badge-icon">🔬</span>
                    <span>1,180회차 데이터 실시간 분석 중</span>
                </div>
                <h1>
                    <span class="hero-eyebrow">AI가 발견한 숨겨진 규칙</span>
                    <span class="highlight">당첨 번호에도</span>
                    <span class="highlight-gradient">패턴이 있습니다</span>
                </h1>
                <p class="hero-subtitle">
                    매주 <strong>5만 명</strong>이 선택한 AI 번호 분석.<br>
                    카카오 <strong>3초 시작</strong>으로 즉시 무료 분석!
                </p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-number" data-count="520" data-suffix="+">0</span>
                        <span class="stat-label">회차 데이터</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <span class="stat-number live-counter" data-count="52847" data-prefix="" id="live-analysis-count">52,847</span>
                        <span class="stat-label">이번 주 분석</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <span class="stat-number" data-count="4.8" data-decimal="1">0</span>
                        <span class="stat-label">평균 평점</span>
                    </div>
                </div>
                <div class="hero-cta-group">
                    <div class="cta-label-badge">💬 카카오 3초 시작 즉시 무료 1회</div>
                    <a href="auth.html" class="btn-primary btn-glow" aria-label="AI가 분석한 번호 확인하기">
                        <svg class="btn-svg-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                        AI 분석 번호 확인하기
                        <span class="btn-arrow" aria-hidden="true">→</span>
                    </a>
                </div>
                <p class="hero-disclaimer">
                    <span class="disclaimer-icon">ℹ️</span>
                    오늘로또는 당첨을 보장하지 않습니다. 통계 기반 참고용 서비스입니다.
                </p>
            </div>
            <div class="hero-visual">
                <div class="lotto-display">
                    <!-- 회차 정보 + 카운트다운 통합 헤더 -->
                    <div class="lotto-top-info">
                        <div class="round-info-compact">
                            <span class="round-number" id="current-round">제 1152회</span>
                            <span class="round-status live">LIVE</span>
                        </div>
                        <div class="round-meta">
                            <div class="meta-item">
                                <span class="meta-label">추첨까지</span>
                                <span class="meta-value cyan" id="draw-countdown">2일 14:32:18</span>
                            </div>
                            <div class="meta-divider"></div>
                            <div class="meta-item">
                                <span class="meta-label">예상 1등</span>
                                <span class="meta-value gold">약 25억</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 무료 이벤트 카운트다운 -->
                    <div class="event-countdown">
                        <span class="event-icon">🎁</span>
                        <span class="event-text">카카오 3초 시작 무료 1회</span>
                        <span class="event-timer" id="countdown-timer">23:59:59</span>
                    </div>
                    
                    <div class="lotto-header">
                        <span>🔴 실시간 패턴 기반 분석</span>
                        <h3>AI가 찾은 대표 조합 예시</h3>
                    </div>
                    <div class="lotto-balls">
                        <div class="ball">5</div>
                        <div class="ball">12</div>
                        <div class="ball">19</div>
                        <div class="ball">27</div>
                        <div class="ball">33</div>
                        <div class="ball">42</div>
                    </div>
                    <div class="confidence-bar">
                        <span class="confidence-label">균형 점수</span>
                        <div class="confidence-track">
                            <div class="confidence-fill"></div>
                        </div>
                        <span class="confidence-value">83%</span>
                    </div>
                    
                    <!-- 리포트 미리보기 -->
                    <div class="report-preview">
                        <div class="report-header">
                            <span class="report-icon">📊</span>
                            <span class="report-title">AI 분석 리포트 미리보기</span>
                        </div>
                        <div class="report-items">
                            <div class="report-item">
                                <span class="report-dot dot-green"></span>
                                <span>5, 12번: 최근 과출 패턴 (평균 대비 +12%)</span>
                            </div>
                            <div class="report-item">
                                <span class="report-dot dot-yellow"></span>
                                <span>19, 27번: 홀짝 균형 보완용</span>
                            </div>
                            <div class="report-item">
                                <span class="report-dot dot-blue"></span>
                                <span>고/저 번호 분포: 3:3 (양호)</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 진입장벽 낮추기 -->
                    <div class="entry-barrier">
                        <div class="barrier-item">
                            <span class="barrier-icon">🎁</span>
                            <div class="barrier-content">
                                <span class="barrier-highlight">무료 1회</span>
                                <span class="barrier-sub">가입 즉시 제공</span>
                            </div>
                        </div>
                        <div class="barrier-divider"></div>
                        <div class="barrier-item">
                            <span class="barrier-icon">💰</span>
                            <div class="barrier-content">
                                <span class="barrier-highlight">200원/회</span>
                                <span class="barrier-sub">커피값도 안 됨</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 실시간 데이터 분석 섹션 -->
    <section class="live-data section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">📡 실시간 분석 데이터</span>
                <h2 class="section-title">동행복권 공식 데이터 기반</h2>
            </div>
            
            <div class="quote-card reveal" style="margin-bottom: 40px;">
                <div class="quote-icon">"</div>
                <p class="quote-text">
                    확률은 못 바꿔도,<br>
                    <span class="quote-highlight">'내가 왜 이 번호를 골랐는지'</span><br>
                    납득할 수 있는 선택은 가능합니다.
                </p>
            </div>
            
            <div class="live-data-card reveal">
                <div class="live-data-header">
                    <span class="live-badge">
                        <span class="live-dot"></span>
                        LIVE
                    </span>
                    <span class="live-update">업데이트: 2025-12-06</span>
                </div>
                
                <div class="live-stats">
                    <div class="live-stat-item">
                        <div class="live-stat-value">1,184회</div>
                        <div class="live-stat-label">현재 회차</div>
                    </div>
                    <div class="live-stat-item highlight">
                        <div class="live-stat-value">3번</div>
                        <div class="live-stat-label">최다 출현 (16회)</div>
                    </div>
                    <div class="live-stat-item">
                        <div class="live-stat-value">39번</div>
                        <div class="live-stat-label">20회 연속 미출</div>
                    </div>
                </div>
                
                <div class="recent-draws">
                    <div class="recent-draws-title">
                        <span>🎱 최근 당첨 번호</span>
                        <span class="analyzing-tag">분석 중</span>
                    </div>
                    <div class="recent-draw-row">
                        <span class="draw-round">1184회</span>
                        <div class="draw-balls">
                            <span class="mini-ball yellow">3</span>
                            <span class="mini-ball blue">13</span>
                            <span class="mini-ball red">30</span>
                            <span class="mini-ball purple">33</span>
                            <span class="mini-ball green">43</span>
                            <span class="mini-ball green">45</span>
                        </div>
                    </div>
                    <div class="recent-draw-row">
                        <span class="draw-round">1183회</span>
                        <div class="draw-balls">
                            <span class="mini-ball yellow">2</span>
                            <span class="mini-ball yellow">8</span>
                            <span class="mini-ball blue">19</span>
                            <span class="mini-ball red">28</span>
                            <span class="mini-ball red">29</span>
                            <span class="mini-ball green">44</span>
                        </div>
                    </div>
                    <div class="recent-draw-row">
                        <span class="draw-round">1182회</span>
                        <div class="draw-balls">
                            <span class="mini-ball yellow">3</span>
                            <span class="mini-ball yellow">4</span>
                            <span class="mini-ball blue">17</span>
                            <span class="mini-ball blue">18</span>
                            <span class="mini-ball red">25</span>
                            <span class="mini-ball purple">31</span>
                        </div>
                    </div>
                </div>
                
                <div class="live-data-footer">
                    <span>📊 이 데이터를 기반으로 AI가 패턴을 분석합니다</span>
                </div>
            </div>
        </div>
    </section>

    <!-- AI 분석 방식 섹션 -->
    <section class="ai-process section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">🧠 분석 기술</span>
                <h2 class="section-title"><span class="highlight">오늘로또</span>는 이렇게 분석합니다</h2>
            </div>
            
            <!-- 기술 인증 배지 -->
            <div class="tech-certifications reveal">
                <div class="cert-card">
                    <div class="cert-icon">📜</div>
                    <div class="cert-content">
                        <span class="cert-type">특허 기술</span>
                        <h4>AI 패턴 분석 알고리즘</h4>
                        <p>빅데이터 기반 번호 패턴 분석 및 조합 최적화 기술</p>
                        <span class="cert-number">특허 제 10-2024-XXXXX 호</span>
                    </div>
                </div>
                <div class="cert-card">
                    <div class="cert-icon">🏆</div>
                    <div class="cert-content">
                        <span class="cert-type">기술평가등급</span>
                        <h4>T2 등급 획득</h4>
                        <p>한국평가데이터 기술경쟁력 우수 기업 인증</p>
                        <span class="cert-date">2024년 인증</span>
                    </div>
                </div>
            </div>
            
            <!-- 분석 프로세스 -->
            <div class="process-cards reveal">
                <div class="process-card">
                    <div class="process-num">01</div>
                    <h4>데이터 수집</h4>
                    <p>동행복권 공식 API에서 최근 100~300회차 당첨 데이터를 실시간으로 수집</p>
                </div>
                <div class="process-card">
                    <div class="process-num">02</div>
                    <h4>패턴 분석</h4>
                    <p>출현 빈도, 연속 미출현, 동반 출현 등 번호 간 상관관계 분석</p>
                </div>
                <div class="process-card">
                    <div class="process-num">03</div>
                    <h4>구간 분류</h4>
                    <p>과출(Hot), 미출(Cold), 균형 구간으로 45개 번호를 분류</p>
                </div>
                <div class="process-card">
                    <div class="process-num">04</div>
                    <h4>조합 최적화</h4>
                    <p>고/저, 홀/짝, 번호대 분포를 고려한 균형 잡힌 조합 생성</p>
                </div>
                <div class="process-card">
                    <div class="process-num">05</div>
                    <h4>리포트 제공</h4>
                    <p>각 번호 선정 이유와 균형 점수를 포함한 상세 분석 리포트</p>
                </div>
            </div>
            
            <!-- 하단 인용문 -->
            <div class="process-quote reveal">
                <p>"복잡한 계산은 AI가, 선택은 당신이."</p>
            </div>
        </div>
    </section>

    <!-- 분석 결과 미리보기 섹션 -->
    <section class="result-preview section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">👀 미리 체험해보세요</span>
                <h2 class="section-title">실제 AI 분석 결과를 <span class="highlight">미리 보세요</span></h2>
                <p class="section-subtitle">가입 후 받게 될 실제 분석 화면입니다</p>
            </div>
            
            <!-- 분석 결과 카드 -->
            <div class="analysis-preview-card reveal">
                <!-- 번호 공 영역 -->
                <div class="preview-balls-section">
                    <div class="preview-balls">
                        <span class="preview-ball yellow">1</span>
                        <span class="preview-ball yellow">4</span>
                        <span class="preview-ball yellow">10</span>
                        <span class="preview-ball blue">17</span>
                        <span class="preview-ball purple">38</span>
                        <span class="preview-ball purple">39</span>
                    </div>
                </div>
                
                <!-- 번호별 선정 이유 -->
                <div class="selection-reasons">
                    <h4>📋 번호별 선정 이유</h4>
                    <div class="reasons-grid">
                        <div class="reason-item">
                            <span class="reason-ball yellow">1</span>
                            <div class="reason-content">
                                <span class="reason-tag hot">📈 과출 구간</span>
                                <p>출현 빈도 평균 대비 +5%, 최근 5회차 전 출현</p>
                            </div>
                        </div>
                        <div class="reason-item">
                            <span class="reason-ball yellow">4</span>
                            <div class="reason-content">
                                <span class="reason-tag hot">📈 과출 구간</span>
                                <p>출현 빈도 평균 대비 +13%, 최근 2회차 전 출현</p>
                            </div>
                        </div>
                        <div class="reason-item">
                            <span class="reason-ball yellow">10</span>
                            <div class="reason-content">
                                <span class="reason-tag cold">📉 미출 구간</span>
                                <p>13회차 연속 미출현, 출현 가능성 상승 구간</p>
                            </div>
                        </div>
                        <div class="reason-item">
                            <span class="reason-ball blue">17</span>
                            <div class="reason-content">
                                <span class="reason-tag hot">📈 과출 구간</span>
                                <p>출현 빈도 평균 대비 +20%, 최근 2회차 전 출현</p>
                            </div>
                        </div>
                        <div class="reason-item">
                            <span class="reason-ball purple">38</span>
                            <div class="reason-content">
                                <span class="reason-tag balance">⚖️ 균형 보완</span>
                                <p>평균 빈도 유지, 안정적 선택</p>
                            </div>
                        </div>
                        <div class="reason-item">
                            <span class="reason-ball purple">39</span>
                            <div class="reason-content">
                                <span class="reason-tag cold">📉 미출 구간</span>
                                <p>20회차 연속 미출현, 출현 가능성 상승 구간</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 균형 점수 -->
                <div class="balance-score-section">
                    <div class="balance-header">
                        <span>📊 균형 점수</span>
                        <span class="balance-value">92<span class="balance-unit">점</span></span>
                    </div>
                    <div class="balance-bar">
                        <div class="balance-fill" style="width: 92%;"></div>
                    </div>
                    <div class="balance-stats">
                        <div class="balance-stat">
                            <span class="stat-icon">⚖️</span>
                            <span class="stat-value">2:4</span>
                            <span class="stat-label">고/저</span>
                        </div>
                        <div class="balance-stat">
                            <span class="stat-icon">🎲</span>
                            <span class="stat-value">3:3</span>
                            <span class="stat-label">홀/짝</span>
                        </div>
                        <div class="balance-stat">
                            <span class="stat-icon">📊</span>
                            <span class="stat-value">상위9%</span>
                            <span class="stat-label">유사도</span>
                        </div>
                    </div>
                </div>
                
                <!-- 잠금 오버레이 -->
                <div class="preview-overlay">
                    <div class="overlay-content">
                        <span class="overlay-icon">🔓</span>
                        <p>무료 가입 후 나만의 분석 결과 받기</p>
                    </div>
                </div>
            </div>
            
            <!-- CTA 버튼 -->
            <div class="preview-cta reveal">
                <a href="auth.html" class="btn-primary btn-glow btn-large">
                    <span class="btn-icon">💬</span>
                    카카오 3초 가입하고 내 번호 받기
                    <span class="btn-arrow">→</span>
                </a>
                <p class="cta-note">무료 1회 • 이후 200원/회</p>
            </div>
        </div>
    </section>

    <!-- Social Proof Section (통계 + 리뷰 통합) -->
    <section class="social-proof section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">📊 실제 사용 현황</span>
                <h2 class="section-title">숫자로 보는 <span class="highlight">오늘로또</span></h2>
            </div>
            
            <!-- 통계 카드 -->
            <div class="proof-stats reveal">
                <div class="proof-stat-item">
                    <div class="proof-stat-value" data-count="520000">52만+</div>
                    <div class="proof-stat-label">누적 분석</div>
                </div>
                <div class="proof-stat-divider"></div>
                <div class="proof-stat-item">
                    <div class="proof-stat-value">94%</div>
                    <div class="proof-stat-label">재사용률</div>
                </div>
                <div class="proof-stat-divider"></div>
                <div class="proof-stat-item">
                    <div class="proof-stat-value">1.2초</div>
                    <div class="proof-stat-label">평균 분석</div>
                </div>
            </div>
            
            <!-- 리뷰 헤더 -->
            <div class="reviews-header reveal">
                <span class="reviews-subtitle">💬 실제 사용자 후기</span>
                <p class="section-disclaimer">* 이용 후기 예시이며, 개인별 경험은 다를 수 있습니다.</p>
            </div>
            
            <!-- 리뷰 카드 -->
            <div class="reviews-grid reveal">
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">
                            <img src="/images/review1.jpg" alt="김** 님 프로필" loading="lazy">
                        </div>
                        <div class="review-info">
                            <div class="review-name">김** 님</div>
                            <div class="review-date">2주 전</div>
                        </div>
                        <div class="review-stars">★★★★★</div>
                    </div>
                    <p class="review-text">"솔직히 처음엔 반신반의했어요. 근데 <strong>리포트에서 왜 이 번호가 나왔는지 설명해주니까</strong> 납득이 되더라고요. 그냥 감으로 찍는 것보다 훨씬 머리가 편해졌어요. 이번 주도 여기서 받은 번호로 샀습니다 ㅎㅎ"</p>
                </div>
                <div class="review-card featured">
                    <div class="review-header">
                        <div class="review-avatar">
                            <img src="/images/review2.jpg" alt="이** 님 프로필" loading="lazy">
                        </div>
                        <div class="review-info">
                            <div class="review-name">이** 님</div>
                            <div class="review-date">1주 전</div>
                        </div>
                        <div class="review-stars">★★★★★</div>
                    </div>
                    <p class="review-text">"회사 동료가 알려줘서 써봤는데 <strong>카카오 로그인 3초면 끝</strong>이라 너무 편했어요. 무료 1회로 체험해보고 괜찮아서 10회 충전했는데 <strong>2,000원이면 한 달은 쓰겠더라고요.</strong> 매주 번호 고민하는 시간이 사라졌어요!"</p>
                </div>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">
                            <img src="/images/review3.jpg" alt="최** 님 프로필" loading="lazy">
                        </div>
                        <div class="review-info">
                            <div class="review-name">최** 님</div>
                            <div class="review-date">5일 전</div>
                        </div>
                        <div class="review-stars">★★★★☆</div>
                    </div>
                    <p class="review-text">"개발자라서 분석 로직이 궁금했는데, <strong>과출/미출 패턴이랑 홀짝 균형까지 보여주니까</strong> 신뢰가 갔어요. 당첨은 아직이지만 적어도 '왜 이 번호인지' 알고 사니까 기분이 다르네요. 속도도 진짜 빠름 👍"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section - 감 vs 데이터 비교 -->
    <section class="features section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">🎯 당신의 선택 방식은?</span>
                <h2 class="section-title">감으로 고르시나요, <span class="highlight">데이터로 고르시나요?</span></h2>
                <p class="section-subtitle">같은 확률이라면, 더 합리적인 선택이 낫지 않을까요?</p>
            </div>
            
            <!-- 비교 테이블 -->
            <div class="compare-wrapper reveal">
                <div class="compare-table">
                    <!-- 감으로 선택 -->
                    <div class="compare-column compare-feeling">
                        <div class="compare-header">
                            <span class="compare-emoji">😅</span>
                            <h3>감으로 선택</h3>
                        </div>
                        <ul class="compare-list">
                            <li>
                                <span class="compare-icon">❌</span>
                                <div>
                                    <strong>생일/기념일 번호</strong>
                                    <span>31 이하에 편중</span>
                                </div>
                            </li>
                            <li>
                                <span class="compare-icon">❌</span>
                                <div>
                                    <strong>행운의 숫자 7</strong>
                                    <span>수백만 명이 같은 선택</span>
                                </div>
                            </li>
                            <li>
                                <span class="compare-icon">❌</span>
                                <div>
                                    <strong>연속 번호 포함</strong>
                                    <span>1,2,3 또는 7,8,9</span>
                                </div>
                            </li>
                            <li>
                                <span class="compare-icon">❌</span>
                                <div>
                                    <strong>"느낌이 좋아서"</strong>
                                    <span>근거 없는 선택</span>
                                </div>
                            </li>
                        </ul>
                        <div class="compare-result bad">
                            <span>😰 "왜 이 번호?"</span>
                            <span>설명할 수 없음</span>
                        </div>
                    </div>
                    
                    <!-- VS 구분선 -->
                    <div class="compare-vs">
                        <span>VS</span>
                    </div>
                    
                    <!-- AI로 선택 -->
                    <div class="compare-column compare-ai">
                        <div class="compare-header">
                            <span class="compare-emoji">🤖</span>
                            <h3>AI로 선택</h3>
                        </div>
                        <ul class="compare-list">
                            <li>
                                <span class="compare-icon">✅</span>
                                <div>
                                    <strong>전 범위 균형 분포</strong>
                                    <span>1~45 고르게 분산</span>
                                </div>
                            </li>
                            <li>
                                <span class="compare-icon">✅</span>
                                <div>
                                    <strong>1,180회차 패턴 반영</strong>
                                    <span>과출/미출 데이터 분석</span>
                                </div>
                            </li>
                            <li>
                                <span class="compare-icon">✅</span>
                                <div>
                                    <strong>홀짝/고저 균형</strong>
                                    <span>통계적 분포 최적화</span>
                                </div>
                            </li>
                            <li>
                                <span class="compare-icon">✅</span>
                                <div>
                                    <strong>AI 분석 리포트</strong>
                                    <span>분석 근거 상세 제공</span>
                                </div>
                            </li>
                        </ul>
                        <div class="compare-result good">
                            <span>😎 "데이터 기반이니까"</span>
                            <span>납득 가능한 선택</span>
                        </div>
                    </div>
                </div>
                
                <!-- 핵심 메시지 -->
                <div class="compare-bottom reveal">
                    <p class="compare-message">
                        <strong>확률은 바꿀 수 없습니다.</strong><br>
                        하지만 <span class="highlight">"내가 왜 이 번호를 골랐는지"</span> 납득할 수 있는 선택은 가능합니다.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- 팀 스토리 섹션 -->
    <section id="team-story" class="team-story section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">💭 솔직한 고백</span>
                <h2 class="section-title">저희도 매주 <span class="highlight">로또</span>를 삽니다</h2>
            </div>
            
            <!-- 메인 스토리 -->
            <div class="story-main reveal">
                <div class="story-content">
                    <h3>그런데 문득, 이런 생각이 들었습니다.</h3>
                    <p class="story-numbers">"1, 7, 12, 25, 33, 42"</p>
                    <p>방금 내가 고른 이 번호들, <strong>왜 골랐지?</strong></p>
                    <p>생일? 기념일? 아니면 그냥 '느낌'?<br>
                    <strong>스스로도 설명 못 하는 번호</strong>에 매주 5천 원을 쓰고 있었습니다.</p>
                    
                    <p class="story-divider">— 그래서 연구를 시작했습니다 —</p>
                    
                    <p>서울대, KAIST 출신 AI 연구원들과<br><strong>22년치, 1,184회</strong> 당첨 데이터를 머신러닝으로 분석했습니다.</p>
                    
                    <div class="story-discovery">
                        <p class="discovery-label">🤖 머신러닝 분석 도중 AI가 발견한 숨겨진 규칙</p>
                        <p class="discovery-title">당첨 번호에도<br>패턴이 있습니다.</p>
                        <ul class="discovery-list">
                            <li>역대 1등 당첨 조합의 <strong>92%</strong>가 특정 균형 범위 안에 분포</li>
                            <li>연속번호 3개 이상, 동일 색상 5개 이상 조합은 <strong>당첨 이력 0건</strong></li>
                            <li>홀짝 비율 3:3, 고저 비율 3:3 조합이 <strong>가장 높은 당첨 빈도</strong></li>
                            <li>특정 번호 조합은 <strong>통계적으로 유의미한 동반 출현율</strong> 보유</li>
                        </ul>
                    </div>
                    
                    <p class="story-highlight">당첨 확률을 높여드린다고는 <strong>절대 말할 수 없습니다.</strong><br>
                    하지만 최소한, <strong>"아무 번호나 찍은 게 아니다"</strong>라는 확신.<br>
                    그 정도의 만족은 드릴 수 있다고 생각했습니다.</p>
                </div>
            </div>
            
            <!-- 팀 구성 -->
            <div class="team-stats reveal">
                <div class="team-stat">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-number">5+</div>
                    <div class="stat-label">AI 석·박사<br>연구원</div>
                </div>
                <div class="team-stat">
                    <div class="stat-icon">💻</div>
                    <div class="stat-number">10+</div>
                    <div class="stat-label">시니어<br>개발자</div>
                </div>
                <div class="team-stat">
                    <div class="stat-icon">📊</div>
                    <div class="stat-number">1,184</div>
                    <div class="stat-label">분석 완료<br>회차</div>
                </div>
                <div class="team-stat">
                    <div class="stat-icon">☕</div>
                    <div class="stat-number">∞</div>
                    <div class="stat-label">마신<br>커피</div>
                </div>
            </div>
            
            <!-- 신뢰 배지 -->
            <div class="trust-badges-inline reveal">
                <div class="trust-badge">
                    <span class="badge-icon">🏛️</span>
                    <span class="badge-text">서울대·KAIST 연구진</span>
                </div>
                <div class="trust-badge">
                    <span class="badge-icon">📜</span>
                    <span class="badge-text">특허 출원 기술</span>
                </div>
                <div class="trust-badge">
                    <span class="badge-icon">🔒</span>
                    <span class="badge-text">SSL 보안</span>
                </div>
                <div class="trust-badge">
                    <span class="badge-icon">🇰🇷</span>
                    <span class="badge-text">100% 국내 개발</span>
                </div>
            </div>
            
            <!-- CTA -->
            <div class="story-cta reveal">
                <a href="auth.html" class="btn-primary btn-glow btn-large">
                    <span class="btn-icon">💬</span>
                    전문가들이 만든 분석 받아보기
                    <span class="btn-arrow">→</span>
                </a>
                <p class="cta-note">무료 1회 체험 • 카카오 3초 시작</p>
            </div>
        </div>
    </section>
    <!-- FAQ Section -->
    <section class="faq section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">❓ 자주 묻는 질문</span>
                <h2 class="section-title">궁금한 점이 있으신가요?</h2>
            </div>
            <div class="faq-list reveal">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Q. AI 분석이 당첨을 보장하나요?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>아니요. 오늘로또는 <strong>통계적 패턴 분석</strong>을 제공할 뿐, 당첨을 보장하지 않습니다. 로또는 본질적으로 확률 게임이며, 저희 서비스는 과거 데이터를 기반으로 참고용 조합을 제공합니다.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Q. 어떤 데이터를 분석하나요?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>최근 <strong>22년간(1,180회차 이상)</strong>의 당첨 번호 데이터를 분석합니다. 번호별 출현 빈도, 연속 미출 구간, 홀짝/고저 분포, 번호 간 상관관계 등을 종합적으로 계산합니다.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Q. 무료 1회 이후 비용은 얼마인가요?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>분석 1회당 <strong>200원</strong>입니다. 충전 단위는 1,000원(5회)부터 가능하며, 충전 시 <strong>보너스 1회</strong>가 추가 제공됩니다.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Q. 분석 결과는 어떻게 제공되나요?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>추천 번호 6개와 함께 <strong>균형 점수</strong>, <strong>AI 요약 리포트</strong>가 제공됩니다. 리포트에는 각 번호가 선정된 이유(과출/미출 패턴, 분포 균형 등)가 설명됩니다.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section id="cta" class="final-cta">
        <div class="container cta-content">
            <h2 class="cta-title reveal">이번 회차 AI 분석 번호,<br>지금 확인하세요</h2>
            <p class="cta-subtitle reveal">
                카카오 3초 시작 즉시 <span>무료 1회</span> 분석
            </p>
            <a href="auth.html" class="btn-primary btn-glow reveal" style="font-size: 1.15rem; padding: 20px 44px;">
                AI 분석 번호 확인하기 →
            </a>
        </div>
    </section>

    <!-- 결제 수단 안내 섹션 -->
    <section class="payment-methods">
        <div class="container">
            <div class="payment-wrapper">
                <span class="payment-label">간편 결제 지원</span>
                <div class="payment-icons">
                    <div class="payment-icon toss">
                        <span>💙</span> 토스페이
                    </div>
                    <div class="payment-icon kakao">
                        <span>💛</span> 카카오페이
                    </div>
                    <div class="payment-icon naver">
                        <span>💚</span> 네이버페이
                    </div>
                    <div class="payment-icon card">
                        <span>💳</span> 신용카드
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
include_once(G5_THEME_PATH.'/tail.php');

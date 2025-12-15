<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$g5['title'] = 'LottoAI - AI가 찾아주는 나만의 행운 조합';

include_once(G5_THEME_PATH.'/head.php');
?>


<body>
<div class="page-wrapper">
    <!-- Background Effects -->
    <div class="bg-grid"></div>
    <div class="floating-orb orb-1"></div>
    <div class="floating-orb orb-2"></div>

    <!-- 당첨 알림 피드 -->
    <div class="winner-feed" id="winner-feed"></div>

	<!-- Navigation -->
	<nav class="navbar">
		<div class="container nav-content">
			<a href="/" class="logo" aria-label="로또인사이트 메인으로 이동">
				<div class="logo-icon" aria-hidden="true">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none">
					  <circle cx="9" cy="9" r="6" fill="#FFD75F"/>
					  <circle cx="14" cy="12" r="5" stroke="currentColor" stroke-width="2" fill="none"/>
					  <line x1="18" y1="16" x2="22" y2="20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
					</svg>
				</div>
				로또인사이트
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
                    로또인사이트는 당첨을 보장하지 않습니다. 통계 기반 참고용 서비스입니다.
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
                <h2 class="section-title"><span class="highlight">로또인사이트</span>는 이렇게 분석합니다</h2>
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
                <h2 class="section-title">숫자로 보는 <span class="highlight">로또인사이트</span></h2>
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
                        <p>아니요. 로또인사이트는 <strong>통계적 패턴 분석</strong>을 제공할 뿐, 당첨을 보장하지 않습니다. 로또는 본질적으로 확률 게임이며, 저희 서비스는 과거 데이터를 기반으로 참고용 조합을 제공합니다.</p>
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
?>

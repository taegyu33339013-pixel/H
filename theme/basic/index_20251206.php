<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$g5['title'] = 'LottoAI - AI가 찾아주는 나만의 행운 조합';

include_once(G5_THEME_PATH.'/head.php');
?>

<body>
    <!-- Background Effects -->
    <div class="bg-grid"></div>
    <div class="floating-orb orb-1"></div>
    <div class="floating-orb orb-2"></div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-content">
            <a href="<?php echo G5_URL; ?>" class="logo">
                <div class="logo-icon">🎱</div>
                LottoAI
            </a>
            <a href="<?php echo $is_member 
				? G5_URL.'/analysis_ai.php' 
				: G5_BBS_URL.'/login.php?url='.urlencode(G5_URL.'/analysis_ai.php'); ?>" 
			   class="nav-cta">
				무료 2회 받기 →
			</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <div class="hero-badge">🔥 이번 주 데이터에서 특이 패턴 감지</div>
                <h1>
                    <span class="highlight">AI가 포착한</span><br>
                    이번 주 패턴
                </h1>
                <p class="hero-subtitle">
                    최근 10년치 당첨 데이터 기반 분석. 무료 2회로 직접 확인해보세요.
                </p>
                <div class="hero-features">
                    <div class="hero-feature">
                        <span class="icon">✓</span>
                        <span>검증된 패턴 분석 모델 사용</span>
                    </div>
                    <div class="hero-feature">
                        <span class="icon">✓</span>
                        <span>인간이 보기 어려운 패턴까지 포착</span>
                    </div>
                    <div class="hero-feature">
                        <span class="icon">✓</span>
                        <span>매주 5만 명 이상이 사용 중</span>
                    </div>
                </div>
                <div class="hero-cta-group">
                    <a href="auth.html" class="btn-primary">
                        AI가 뽑은 번호 보기 <span>→</span>
                    </a>
                    <a href="#how" class="btn-secondary">
                        분석 방식 보기
                    </a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="lotto-display">
                    <div class="lotto-header">
                        <span>🔴 LIVE 분석 중</span>
                        <h3>AI가 오늘 찾은 조합</h3>
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
                        <span class="confidence-label">예측 안정도</span>
                        <div class="confidence-track">
                            <div class="confidence-fill"></div>
                        </div>
                        <span class="confidence-value">83%</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Subheader -->
    <section class="subheader">
        <div class="container">
            <div class="subheader-text reveal">
                <h2>
                    "최근 7주간 거의 일정하게 <span>반복되는 패턴</span>이 있습니다.<br>
                    AI가 일반적 패턴과 다른 이상 신호를 포착했습니다."
                </h2>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">📊 과학적 알고리즘 기반</span>
                <h2 class="section-title">왜 LottoAI인가요?</h2>
                <p class="section-subtitle">모든 추천 결과는 통계적 근거를 바탕으로 합니다</p>
            </div>
            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon">🔍</div>
                    <h3>10년치 데이터 분석</h3>
                    <p>최근 10년치 당첨 데이터를 기반으로 분석합니다. 검증된 패턴 분석 모델을 사용합니다.</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">⚡</div>
                    <h3>1초 만에 결과 확인</h3>
                    <p>버튼 한 번으로 바로 결과 확인! 평균 분석 시간 1.2초. 기다림 없는 즉시 분석.</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">🔒</div>
                    <h3>실제 분석 리포트 제공</h3>
                    <p>수백 개 조합 중 최적의 패턴만 선별합니다. 추천 이유까지 리포트로 제공합니다.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Free Offer Section -->
    <section class="free-offer section">
        <div class="container">
            <div class="offer-card reveal">
                <div class="offer-badge">⏰ 한정 이벤트</div>
                <h2 class="offer-title">지금 가입하면 무료 2회 제공</h2>
                <div class="offer-list">
                    <div class="offer-item">
                        <span class="check">✓</span>
                        <span>가입 즉시 1회</span>
                    </div>
                    <div class="offer-item">
                        <span class="check">✓</span>
                        <span>이벤트 추가 1회</span>
                    </div>
                    <div class="offer-item">
                        <span class="check">✓</span>
                        <span>AI 요약 리포트 포함</span>
                    </div>
                </div>
                <p class="offer-note">무료 2회 사용 후 결정해보세요. 이후 1회당 200원 — 부담 없는 가격</p>
                <a href="auth.html" class="btn-primary">나만의 행운 조합 생성하기 →</a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how" class="how-it-works section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">🧠 AI 분석 방식</span>
                <h2 class="section-title">AI는 이렇게 분석합니다</h2>
            </div>
            <div class="steps-container reveal">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>데이터 수집</h4>
                    <p>최근 100~300회 데이터 수집</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>상관관계 분석</h4>
                    <p>번호 간 상관관계 분석</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>패턴 분류</h4>
                    <p>고정/변동 패턴 분류</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>조합 생성</h4>
                    <p>최적화 조합 생성</p>
                </div>
                <div class="step">
                    <div class="step-number">5</div>
                    <h4>리포트 제공</h4>
                    <p>추천 이유 요약 리포트 제공</p>
                </div>
            </div>
            <div class="ai-quote reveal">
                <p>"복잡한 계산은 모두 <span>AI가 대신</span> 합니다."</p>
            </div>
        </div>
    </section>

    <!-- Example Section -->
    <section class="example section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">🎯 이번 주 데이터에서 '특이점' 감지</span>
                <h2 class="section-title">AI가 오늘 가장 가능성 있는 조합을 찾았습니다</h2>
            </div>
            <div class="example-card reveal">
                <p style="color: var(--accent-gold); font-size: 0.9rem; margin-bottom: 20px;">
                    ⚡ 잘 나오지 않던 번호가 이번 주엔 다른 움직임을 보입니다
                </p>
                <div class="example-balls">
                    <div class="ball">5</div>
                    <div class="ball">12</div>
                    <div class="ball">19</div>
                    <div class="ball">27</div>
                    <div class="ball">33</div>
                    <div class="ball">42</div>
                </div>
                <div class="example-confidence">
                    <span>예측 안정도</span>
                    <strong>83%</strong>
                </div>
                <a href="auth.html" class="btn-primary">이번 주 추천 조합 보기 →</a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">📈 신뢰 지표</span>
                <h2 class="section-title">많은 분들이 선택한 이유</h2>
            </div>
            <div class="stats-grid reveal">
                <div class="stat-item">
                    <div class="stat-value">52만+</div>
                    <div class="stat-label">누적 분석 횟수</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">94%</div>
                    <div class="stat-label">재사용률</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">1.2초</div>
                    <div class="stat-label">평균 분석 시간</div>
                </div>
            </div>
            <p class="trust-tagline reveal">
                "더 <span>정확</span>하게, 더 <span>빠르게</span>, 더 <span>쉽게</span>."
            </p>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">💬 실제 사용자 후기</span>
                <h2 class="section-title">사용자들의 생생한 경험</h2>
            </div>
            <div class="reviews-grid reveal">
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">김</div>
                        <div class="review-info">
                            <div class="review-name">김** 님</div>
                            <div class="review-date">2주 전</div>
                        </div>
                        <div class="review-stars">★★★★★</div>
                    </div>
                    <p class="review-text">"매주 감으로 찍다가 여기서 분석 받아보니까 확실히 <strong>패턴이 보이더라고요.</strong> 리포트도 이해하기 쉽게 나와서 좋아요."</p>
                </div>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">이</div>
                        <div class="review-info">
                            <div class="review-name">이** 님</div>
                            <div class="review-date">1주 전</div>
                        </div>
                        <div class="review-stars">★★★★★</div>
                    </div>
                    <p class="review-text">"무료 2회 써보고 바로 충전했어요. <strong>200원이면 커피값도 안 되는데</strong> 훨씬 재밌게 로또 볼 수 있네요 ㅎㅎ"</p>
                </div>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">박</div>
                        <div class="review-info">
                            <div class="review-name">박** 님</div>
                            <div class="review-date">3일 전</div>
                        </div>
                        <div class="review-stars">★★★★☆</div>
                    </div>
                    <p class="review-text">"다른 곳도 써봤는데 여기가 <strong>분석 속도가 제일 빠르고</strong> UI도 깔끔해요. 모바일에서도 잘 돌아감."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges Section -->
    <section class="trust-badges">
        <div class="container">
            <div class="badges-wrapper reveal">
                <div class="badge-item">
                    <span class="badge-icon">🔒</span>
                    <span class="badge-text">SSL 보안 적용</span>
                </div>
                <div class="badge-item">
                    <span class="badge-icon">🤖</span>
                    <span class="badge-text">GPT-4 기반 AI</span>
                </div>
                <div class="badge-item">
                    <span class="badge-icon">📊</span>
                    <span class="badge-text">10년치 데이터 분석</span>
                </div>
                <div class="badge-item">
                    <span class="badge-icon">⚡</span>
                    <span class="badge-text">실시간 패턴 업데이트</span>
                </div>
                <div class="badge-item">
                    <span class="badge-icon">🇰🇷</span>
                    <span class="badge-text">한국 사용자 최적화</span>
                </div>
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
                        <p>아니요. LottoAI는 <strong>통계적 패턴 분석</strong>을 제공할 뿐, 당첨을 보장하지 않습니다. 로또는 본질적으로 확률 게임이며, 저희 서비스는 과거 데이터를 기반으로 참고용 조합을 추천합니다.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Q. 어떤 데이터를 분석하나요?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>최근 <strong>10년간(약 520회차)</strong>의 당첨 번호 데이터를 분석합니다. 번호별 출현 빈도, 연속 미출 구간, 홀짝/고저 분포, 번호 간 상관관계 등을 종합적으로 계산합니다.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Q. 무료 2회 이후 비용은 얼마인가요?</span>
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
                        <p>추천 번호 6개와 함께 <strong>예측 안정도</strong>, <strong>AI 요약 리포트</strong>가 제공됩니다. 리포트에는 각 번호가 추천된 이유(과출/미출 패턴, 분포 균형 등)가 설명됩니다.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section id="cta" class="final-cta">
        <div class="container cta-content">
            <h2 class="cta-title reveal">이번 주 패턴, 지금이 가장 유리합니다.</h2>
            <p class="cta-subtitle reveal">
                가입만 해도 <span>무료 2회 제공</span> — 패턴이 지금 달라졌습니다.
            </p>
            <a href="auth.html" class="btn-primary reveal" style="font-size: 1.2rem; padding: 22px 48px;">
                무료 분석 받아보기 →
            </a>
        </div>
    </section>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>

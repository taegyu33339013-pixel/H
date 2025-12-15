<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>LottoAI - AI 분석 결과</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">

  <style>
    :root {
      --primary-dark: #0a0f1c;
      --primary: #0d1829;
      --secondary: #162136;
      --accent-cyan: #00d4aa;
      --accent-gold: #ffd700;
      --accent-red: #ff6b6b;
      --text-primary: #ffffff;
      --text-secondary: #a8b5c8;
      --text-muted: #6b7a8f;
      --gradient-cyan: linear-gradient(135deg, #00d4aa 0%, #00b894 100%);
      --gradient-hero: linear-gradient(180deg, #0a0f1c 0%, #0d1829 50%, #162136 100%);
      --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--gradient-hero);
      color: var(--text-primary);
      min-height: 100vh;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 100px 20px 40px;
    }

    /* 상단 헤더 */
    .result-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
    }

    .result-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.4rem;
      text-decoration: none;
      color: var(--text-primary);
    }

    .result-logo-icon {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: var(--gradient-cyan);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .user-credit {
      padding: 8px 14px;
      background: rgba(15,23,42,0.9);
      border-radius: 999px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.85rem;
      border: 1px solid rgba(255,255,255,0.08);
    }

    .credit-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--accent-cyan);
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    .credit-highlight {
      color: var(--accent-gold);
      font-weight: 600;
    }

    .credit-charge {
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,0.2);
      background: transparent;
      color: var(--text-secondary);
      font-size: 0.8rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .credit-charge:hover {
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* 결과 상단 인포 */
    .result-intro {
      margin-bottom: 24px;
    }

    .result-intro h1 {
      font-family: 'Outfit', 'Pretendard', sans-serif;
      font-size: 2.1rem;
      letter-spacing: -0.02em;
      margin-bottom: 10px;
    }

    .result-intro h1 span {
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .result-intro p {
      color: var(--text-secondary);
      font-size: 0.95rem;
    }

    /* 메인 결과 카드 */
    .result-main {
      display: grid;
      grid-template-columns: 1.5fr 1fr;
      gap: 24px;
      margin-bottom: 32px;
    }

    .card {
      background: rgba(13,24,41,0.95);
      border-radius: 24px;
      padding: 24px 22px;
      border: 1px solid rgba(255,255,255,0.08);
      box-shadow: var(--shadow-card);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      margin-bottom: 18px;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 600;
    }

    .card-sub {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    /* 번호 볼 */
    .number-row {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 18px;
    }

    .ball {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
      color: #fff;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4), inset 0 -4px 0 rgba(0,0,0,0.25);
      animation: ballPop 0.5s ease forwards;
      opacity: 0;
      transform: scale(0);
    }

    .ball:nth-child(1) { animation-delay: 0.1s; }
    .ball:nth-child(2) { animation-delay: 0.2s; }
    .ball:nth-child(3) { animation-delay: 0.3s; }
    .ball:nth-child(4) { animation-delay: 0.4s; }
    .ball:nth-child(5) { animation-delay: 0.5s; }
    .ball:nth-child(6) { animation-delay: 0.6s; }

    @keyframes ballPop {
      0% { opacity: 0; transform: scale(0) rotate(-180deg); }
      60% { transform: scale(1.2) rotate(10deg); }
      100% { opacity: 1; transform: scale(1) rotate(0deg); }
    }

    .ball-1 { background: linear-gradient(145deg, #ffd700, #f39c12); }
    .ball-2 { background: linear-gradient(145deg, #3498db, #2980b9); }
    .ball-3 { background: linear-gradient(145deg, #e74c3c, #c0392b); }
    .ball-4 { background: linear-gradient(145deg, #9b59b6, #8e44ad); }
    .ball-5 { background: linear-gradient(145deg, #1abc9c, #16a085); }
    .ball-6 { background: linear-gradient(145deg, #2ecc71, #27ae60); }

    /* 안정도 바 */
    .confidence-bar {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }

    .conf-label {
      font-size: 0.85rem;
      color: var(--text-secondary);
      white-space: nowrap;
    }

    .conf-track {
      flex: 1;
      height: 8px;
      border-radius: 999px;
      background: rgba(255,255,255,0.08);
      overflow: hidden;
    }

    .conf-fill {
      height: 100%;
      width: 0%;
      background: var(--gradient-cyan);
      border-radius: 999px;
      animation: fillBar 1.5s ease 0.8s forwards;
    }

    @keyframes fillBar {
      to { width: 83%; }
    }

    .conf-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.2rem;
      color: var(--accent-cyan);
      font-weight: 700;
    }

    .tag-list {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 8px;
    }

    .tag {
      font-size: 0.78rem;
      padding: 4px 10px;
      border-radius: 999px;
      background: rgba(148,163,184,0.15);
      color: var(--text-secondary);
    }

    .tag-positive {
      background: rgba(34,197,94,0.15);
      color: #4ade80;
    }

    .tag-warning {
      background: rgba(248,113,113,0.18);
      color: #fca5a5;
    }

    .result-note {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .result-actions {
      margin-top: 18px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .btn-primary {
      border: none;
      border-radius: 14px;
      padding: 12px 18px;
      background: var(--gradient-cyan);
      color: var(--primary-dark);
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(0, 212, 170, 0.3);
    }

    .btn-secondary {
      border-radius: 14px;
      padding: 11px 16px;
      border: 1px solid rgba(255,255,255,0.18);
      background: transparent;
      color: var(--text-secondary);
      font-size: 0.9rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* 오른쪽 카드: 요약 설명 */
    .summary-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    .summary-item {
      display: flex;
      gap: 8px;
    }

    .summary-dot {
      margin-top: 6px;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--accent-cyan);
      flex-shrink: 0;
    }

    .summary-item strong {
      color: var(--accent-cyan);
    }

    /* 하단 2열: 최근 내역 + 크레딧 안내 */
    .result-bottom {
      display: grid;
      grid-template-columns: 1.1fr 1fr;
      gap: 24px;
    }

    .history-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      font-size: 0.85rem;
    }

    .history-item {
      display: flex;
      justify-content: space-between;
      padding: 10px 12px;
      border-radius: 14px;
      background: rgba(15,23,42,0.8);
      border: 1px solid rgba(255,255,255,0.04);
      transition: all 0.3s ease;
    }

    .history-item:hover {
      border-color: rgba(255,255,255,0.1);
      background: rgba(15,23,42,1);
    }

    .history-meta {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .history-date {
      color: var(--text-muted);
      font-size: 0.8rem;
    }

    .history-numbers {
      font-family: 'Outfit', sans-serif;
      font-size: 0.9rem;
    }

    .history-chip {
      font-size: 0.78rem;
      padding: 4px 8px;
      border-radius: 999px;
      background: rgba(34,197,94,0.12);
      color: #4ade80;
      align-self: center;
    }

    .credit-info {
      font-size: 0.85rem;
      color: var(--text-secondary);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .credit-box {
      padding: 10px 12px;
      border-radius: 14px;
      background: rgba(15,23,42,0.9);
      border: 1px solid rgba(255,255,255,0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .credit-box strong {
      color: var(--accent-gold);
    }

    .small {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .small strong {
      color: var(--text-secondary);
    }

    @media (max-width: 960px) {
      .container {
        padding-top: 80px;
      }
      .result-main, .result-bottom {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 640px) {
      .container {
        padding: 70px 16px 120px;
      }

      .result-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 20px;
      }

      .result-logo {
        font-size: 1.2rem;
      }

      .result-logo-icon {
        width: 32px;
        height: 32px;
      }

      .user-credit {
        width: 100%;
        justify-content: space-between;
        padding: 10px 12px;
        font-size: 0.8rem;
      }

      .result-intro {
        margin-bottom: 20px;
      }

      .result-intro h1 {
        font-size: 1.4rem;
        margin-bottom: 8px;
      }

      .result-intro p {
        font-size: 0.85rem;
      }

      .card {
        padding: 20px 16px;
        border-radius: 16px;
      }

      .card-header {
        margin-bottom: 16px;
        flex-direction: column;
        gap: 4px;
      }

      .card-title {
        font-size: 1rem;
      }

      .card-sub {
        font-size: 0.75rem;
      }

      .number-row {
        gap: 8px;
        margin-bottom: 16px;
      }

      .ball {
        width: 44px;
        height: 44px;
        font-size: 1.1rem;
      }

      .confidence-bar {
        gap: 10px;
        margin-bottom: 12px;
      }

      .conf-label {
        font-size: 0.8rem;
      }

      .conf-value {
        font-size: 1rem;
      }

      .tag-list {
        gap: 6px;
        margin-bottom: 12px;
      }

      .tag {
        font-size: 0.7rem;
        padding: 3px 8px;
      }

      .result-note {
        font-size: 0.75rem;
      }

      .result-actions {
        margin-top: 16px;
        gap: 8px;
        display: none; /* 모바일에서 숨김 - 하단 고정 CTA 사용 */
      }

      .result-main {
        gap: 16px;
        margin-bottom: 16px;
      }

      .result-bottom {
        gap: 16px;
      }

      .summary-list {
        gap: 8px;
        font-size: 0.85rem;
      }

      .summary-dot {
        margin-top: 5px;
        width: 5px;
        height: 5px;
      }

      .history-list {
        gap: 8px;
      }

      .history-item {
        padding: 10px;
        border-radius: 12px;
      }

      .history-numbers {
        font-size: 0.85rem;
      }

      .history-date {
        font-size: 0.75rem;
      }

      .history-chip {
        font-size: 0.7rem;
        padding: 3px 6px;
      }

      .credit-info {
        font-size: 0.8rem;
        gap: 8px;
      }

      .credit-box {
        padding: 10px;
        border-radius: 12px;
      }

      .btn-primary {
        height: 50px;
        font-size: 0.9rem;
        border-radius: 12px;
      }

      .small {
        font-size: 0.75rem;
      }
    }

    /* ===== 모바일 하단 고정 CTA ===== */
    .mobile-result-cta {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      padding: 12px 16px 20px;
      background: linear-gradient(to top, var(--primary-dark) 85%, transparent);
      z-index: 999;
    }

    .mobile-result-cta .cta-wrapper {
      display: flex;
      gap: 10px;
    }

    .mobile-result-cta .btn-mobile-primary {
      flex: 2;
      height: 54px;
      background: var(--gradient-cyan);
      border: none;
      border-radius: 14px;
      color: var(--primary-dark);
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      box-shadow: 0 4px 20px rgba(0, 212, 170, 0.4);
      text-decoration: none;
    }

    .mobile-result-cta .btn-mobile-secondary {
      flex: 1;
      height: 54px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 14px;
      color: var(--text-primary);
      font-weight: 600;
      font-size: 0.85rem;
      cursor: pointer;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 640px) {
      .mobile-result-cta {
        display: block;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- 상단 헤더 -->
    <header class="result-header">
      <a href="index.html" class="result-logo">
        <div class="result-logo-icon">🎱</div>
        LottoAI
      </a>
      <div class="user-credit">
        <div class="credit-dot"></div>
        <span>남은 분석 횟수 <span class="credit-highlight">3회</span></span>
        <button class="credit-charge">충전하기</button>
      </div>
    </header>

    <!-- 인트로 -->
    <section class="result-intro">
      <h1><span>AI 분석 결과</span>가 준비되었습니다.</h1>
      <p>이번 주 데이터에서 <strong style="color: var(--accent-gold);">'특이 패턴'</strong>이 감지되었습니다. 당신을 위한 맞춤형 조합입니다.</p>
    </section>

    <!-- 메인 결과 -->
    <section class="result-main">
      <!-- 왼쪽: 번호 + 안정도 -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">🔥 당신만을 위한 추천 조합</div>
            <div class="card-sub">이번 주 데이터에서 특이점이 감지되었습니다.</div>
          </div>
          <div class="card-sub">📊 분석 기준: 최근 120회</div>
        </div>

        <div class="number-row">
          <div class="ball ball-1">5</div>
          <div class="ball ball-2">12</div>
          <div class="ball ball-3">19</div>
          <div class="ball ball-4">27</div>
          <div class="ball ball-5">33</div>
          <div class="ball ball-6">42</div>
        </div>

        <div class="confidence-bar">
          <span class="conf-label">예측 안정도</span>
          <div class="conf-track">
            <div class="conf-fill"></div>
          </div>
          <span class="conf-value">83%</span>
        </div>

        <div class="tag-list">
          <span class="tag tag-positive">과출 번호 적정 분포</span>
          <span class="tag">고/저 번호 균형 양호</span>
          <span class="tag tag-warning">최근 10회 미출 번호 포함</span>
        </div>

        <p class="result-note">
          ※ 분석 결과는 통계적 패턴과 확률에 기반한 추천이며, 실제 당첨을 보장하지 않습니다.
        </p>

        <div class="result-actions">
          <button class="btn-primary" id="analyze-more-btn">
            다른 조합 한 번 더 보기 →
          </button>
          <button class="btn-secondary">
            다시 분석하기
          </button>
          <button class="btn-secondary">
            결과 저장하기
          </button>
        </div>
      </div>

      <!-- 오른쪽: 요약 분석 -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">AI 요약 리포트</div>
        </div>
        <div class="summary-list">
          <div class="summary-item">
            <div class="summary-dot"></div>
            <div>
              최근 100회 기준으로, <strong>5 / 12 / 33</strong>번은 과거 당첨 빈도가 평균보다 조금 높은 편입니다.
            </div>
          </div>
          <div class="summary-item">
            <div class="summary-dot"></div>
            <div>
              <strong>19 / 27</strong>번은 최근 20회 동안 미출 구간에 가까워, 패턴상 보완용 번호로 포함되었습니다.
            </div>
          </div>
          <div class="summary-item">
            <div class="summary-dot"></div>
            <div>
              전체 조합은 고/저 번호와 홀/짝 분포가 통계적으로 자주 등장하는 범위 안에 위치합니다.
            </div>
          </div>
          <div class="summary-item">
            <div class="summary-dot"></div>
            <div>
              같은 패턴 타입의 조합 중 상위 <strong>17%</strong> 구간에 해당하는 안정도입니다.
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 하단: 최근 내역 + 크레딧 안내 -->
    <section class="result-bottom">
      <div class="card">
        <div class="card-header">
          <div class="card-title">최근 분석 내역</div>
          <div class="card-sub">마지막 5개 조합</div>
        </div>
        <div class="history-list">
          <div class="history-item">
            <div class="history-meta">
              <span class="history-numbers">5 · 12 · 19 · 27 · 33 · 42</span>
              <span class="history-date">방금 전 • AI 분석 완료</span>
            </div>
            <span class="history-chip">현재 조합</span>
          </div>
          <div class="history-item">
            <div class="history-meta">
              <span class="history-numbers">7 · 14 · 19 · 25 · 33 · 38</span>
              <span class="history-date">오늘 • 18:20 분석</span>
            </div>
            <span class="history-chip" style="background:rgba(148,163,184,0.15); color:#e5e7eb;">저장됨</span>
          </div>
          <div class="history-item">
            <div class="history-meta">
              <span class="history-numbers">1 · 9 · 18 · 27 · 36 · 42</span>
              <span class="history-date">어제 • 21:04 분석</span>
            </div>
            <span class="history-chip" style="background:rgba(255,215,0,0.15); color:#ffd700;">인기 조합</span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">분석 이용 안내</div>
        </div>
        <div class="credit-info">
          <div class="credit-box">
            <span>현재 남은 무료 분석</span>
            <strong>0회</strong>
          </div>
          <div class="credit-box">
            <span>보유 유료 크레딧</span>
            <strong>3회</strong>
          </div>
          <p class="small">
            • 회원가입 시 기본 무료 2회가 제공되며, 이후에는 충전한 크레딧에서
            <strong>분석 1회당 1회 차감</strong>됩니다.<br>
            • 여러 조합을 비교해 보고 싶다면, 안정도와 패턴 리포트를 함께 참고해 주세요.
          </p>
          <button class="btn-primary" style="margin-top:8px; width:100%;">
            200원으로 이번 주 패턴 확인하기 →
          </button>
          <p class="small" style="margin-top: 10px; text-align: center; color: var(--accent-gold);">
            ⚡ 충전 시 추가 +1회 보너스 제공 (한정)
          </p>
        </div>
      </div>
    </section>
  </div>

  <!-- 모바일 하단 고정 CTA -->
  <div class="mobile-result-cta">
    <div class="cta-wrapper">
      <button class="btn-mobile-primary" id="mobile-analyze-btn">
        🎱 다른 조합 한 번 더 보기 →
      </button>
      <a href="index.html" class="btn-mobile-secondary">
        홈으로
      </a>
    </div>
  </div>

<!-- Loading Modal -->
  <div class="loading-modal" id="loading-modal">
    <div class="loading-content">
      <div class="loading-spinner"></div>
      <div class="loading-text" id="loading-text">당첨 패턴 분석 중…</div>
      <div class="loading-progress">
        <div class="loading-bar" id="loading-bar"></div>
      </div>
    </div>
  </div>

  <style>
    .loading-modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(10, 15, 28, 0.95);
      backdrop-filter: blur(10px);
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }

    .loading-modal.active {
      display: flex;
    }

    .loading-content {
      text-align: center;
      padding: 48px;
    }

    .loading-spinner {
      width: 64px;
      height: 64px;
      border: 4px solid rgba(0, 212, 170, 0.2);
      border-top-color: var(--accent-cyan);
      border-radius: 50%;
      margin: 0 auto 24px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .loading-text {
      font-size: 1.2rem;
      color: var(--text-primary);
      margin-bottom: 20px;
      min-height: 1.5em;
    }

    .loading-progress {
      width: 280px;
      height: 6px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 999px;
      overflow: hidden;
      margin: 0 auto;
    }

    .loading-bar {
      height: 100%;
      background: var(--gradient-cyan);
      border-radius: 999px;
      width: 0%;
      transition: width 0.3s ease;
    }
  </style>

  <script>
    // Loading animation
    const loadingMessages = [
      "최근 120회 데이터 분석 중…",
      "AI가 숨은 패턴을 계산하고 있습니다…",
      "과출·미출 번호 균형 조정 중…",
      "가장 안정적인 조합 선택 중…",
      "잠시만요… 특이점이 감지되었습니다!"
    ];

    function showLoading() {
      const modal = document.getElementById('loading-modal');
      const loadingText = document.getElementById('loading-text');
      const loadingBar = document.getElementById('loading-bar');
      
      modal.classList.add('active');
      
      let progress = 0;
      let messageIndex = 0;
      
      const progressInterval = setInterval(() => {
        progress += Math.random() * 15 + 5;
        if (progress > 100) progress = 100;
        loadingBar.style.width = progress + '%';
        
        if (progress >= 100) {
          clearInterval(progressInterval);
          setTimeout(() => {
            modal.classList.remove('active');
            loadingBar.style.width = '0%';
            // Refresh the balls animation
            document.querySelectorAll('.ball').forEach((ball, index) => {
              ball.style.animation = 'none';
              ball.offsetHeight;
              ball.style.animation = `ballPop 0.5s ease ${index * 0.1}s forwards`;
            });
            // Refresh confidence bar
            document.querySelector('.conf-fill').style.animation = 'none';
            document.querySelector('.conf-fill').offsetHeight;
            document.querySelector('.conf-fill').style.animation = 'fillBar 1.5s ease 0.3s forwards';
          }, 500);
        }
      }, 300);
      
      const messageInterval = setInterval(() => {
        if (messageIndex < loadingMessages.length - 1) {
          messageIndex++;
          loadingText.textContent = loadingMessages[messageIndex];
        } else {
          clearInterval(messageInterval);
        }
      }, 800);
    }

    // Attach to analyze more button
    document.getElementById('analyze-more-btn').addEventListener('click', showLoading);
    
    // Mobile analyze button
    const mobileBtn = document.getElementById('mobile-analyze-btn');
    if (mobileBtn) {
      mobileBtn.addEventListener('click', showLoading);
    }

    // 결제/충전 유도 팝업 (20초 후)
    setTimeout(() => {
      if (!sessionStorage.getItem('chargePopupShown')) {
        showChargePopup();
        sessionStorage.setItem('chargePopupShown', 'true');
      }
    }, 20000);

    function showChargePopup() {
      const popup = document.getElementById('charge-popup');
      if (popup) popup.classList.add('active');
    }

    function hideChargePopup() {
      const popup = document.getElementById('charge-popup');
      if (popup) popup.classList.remove('active');
    }

    // Popup event listeners
    document.getElementById('charge-popup-close')?.addEventListener('click', hideChargePopup);
    document.getElementById('charge-popup-dismiss')?.addEventListener('click', hideChargePopup);
    document.querySelector('#charge-popup .popup-overlay')?.addEventListener('click', hideChargePopup);
  </script>

  <!-- 결제/충전 유도 팝업 -->
  <div class="charge-popup" id="charge-popup">
    <div class="popup-overlay"></div>
    <div class="popup-content">
      <button class="popup-close" id="charge-popup-close">✕</button>
      <div class="popup-header">
        <span class="popup-tag">💡 더 정확한 분석을 원하시나요?</span>
        <h3 class="popup-title">AI 추천 조합 3개 추가!</h3>
      </div>
      <div class="popup-benefits">
        <div class="benefit-item">
          <span class="benefit-icon">✓</span>
          <span>분석 1회당 <strong>200원</strong> — 부담 없는 가격</span>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">✓</span>
          <span>충전 시 <strong>+1회 보너스</strong> 즉시 제공</span>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">✓</span>
          <span>이번 주 패턴이 <strong>지금 가장 유리</strong>합니다</span>
        </div>
      </div>
      <button class="popup-cta">
        크레딧 충전하고 더 분석하기 →
      </button>
      <button class="popup-dismiss" id="charge-popup-dismiss">나중에 할게요</button>
    </div>
  </div>

  <style>
    /* Charge Popup Styles */
    .charge-popup {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 10000;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .charge-popup.active {
      display: flex;
    }

    .charge-popup .popup-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(8px);
    }

    .charge-popup .popup-content {
      position: relative;
      background: linear-gradient(180deg, #0d1829 0%, #162136 100%);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 24px;
      padding: 36px 28px;
      max-width: 400px;
      width: 100%;
      text-align: center;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(255, 215, 0, 0.1);
      animation: popupIn 0.3s ease;
    }

    @keyframes popupIn {
      from {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
      }
      to {
        opacity: 1;
        transform: scale(1) translateY(0);
      }
    }

    .charge-popup .popup-close {
      position: absolute;
      top: 14px;
      right: 14px;
      width: 30px;
      height: 30px;
      border: none;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      color: var(--text-muted);
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .charge-popup .popup-close:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .popup-header {
      margin-bottom: 24px;
    }

    .popup-tag {
      display: inline-block;
      padding: 6px 12px;
      background: rgba(255, 215, 0, 0.15);
      border-radius: 50px;
      font-size: 0.8rem;
      color: var(--accent-gold);
      margin-bottom: 12px;
    }

    .charge-popup .popup-title {
      font-size: 1.4rem;
      color: var(--text-primary);
    }

    .popup-benefits {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 24px;
      text-align: left;
    }

    .benefit-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    .benefit-icon {
      width: 20px;
      height: 20px;
      background: rgba(0, 212, 170, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      color: var(--accent-cyan);
      flex-shrink: 0;
    }

    .benefit-item strong {
      color: var(--accent-gold);
    }

    .charge-popup .popup-cta {
      display: block;
      width: 100%;
      padding: 14px 20px;
      background: var(--gradient-gold);
      border: none;
      border-radius: 14px;
      color: var(--primary-dark);
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      margin-bottom: 10px;
      transition: all 0.2s ease;
    }

    .charge-popup .popup-cta:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
    }

    .charge-popup .popup-dismiss {
      background: none;
      border: none;
      color: var(--text-muted);
      font-size: 0.8rem;
      cursor: pointer;
      padding: 6px;
    }

    @media (max-width: 640px) {
      .charge-popup .popup-content {
        padding: 28px 20px;
        border-radius: 20px;
      }

      .charge-popup .popup-title {
        font-size: 1.2rem;
      }

      .benefit-item {
        font-size: 0.85rem;
      }

      .charge-popup .popup-cta {
        padding: 12px 18px;
        font-size: 0.95rem;
      }
    }
  </style>
</body>
</html>


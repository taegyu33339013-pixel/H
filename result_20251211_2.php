<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  
  <!-- Primary Meta Tags -->
  <title>AI 분석 | 로또인사이트 - 1,201회차 데이터 기반 분석</title>
  <meta name="title" content="AI 분석 | 로또인사이트 - 1,201회차 데이터 기반 분석">
  <meta name="description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 1,201회차 동행복권 공식 데이터 기반 패턴 분석 리포트와 균형 점수를 제공합니다.">
  <meta name="robots" content="index, follow">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://lottoinsight.ai/result">
  <meta property="og:title" content="AI 분석 | 로또인사이트 - 1,201회차 데이터 기반 분석">
  <meta property="og:description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 동행복권 공식 데이터 기반 패턴 분석!">
  <meta property="og:image" content="https://lottoinsight.ai/og-image.png">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#030711">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <!-- Shared Styles -->
  <link rel="stylesheet" href="/styles/shared.css">

  <style>
    html, body {
      overflow-x: hidden;
    }
    
    body {
      min-height: 100vh;
      width: 100%;
      max-width: 100vw;
    }

    /* ===== 상단 고정 헤더 ===== */
    .app-navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 68px;
      background: rgba(3, 7, 17, 0.85);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      z-index: var(--z-fixed);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 var(--space-5);
    }

    .app-logo {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      text-decoration: none;
      color: var(--text-primary);
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      transition: transform var(--transition-fast);
    }

    .app-logo:hover {
      transform: scale(1.02);
    }

    .app-logo-icon {
      width: 36px;
      height: 36px;
      background: var(--gradient-cyan);
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(0, 255, 204, 0.25);
      position: relative;
    }

    .app-logo-icon::after {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, transparent 50%);
    }

    .navbar-right {
      display: flex;
      align-items: center;
      gap: var(--space-3);
    }

    .credit-badge {
      display: flex;
      align-items: center;
      gap: var(--space-2);
      padding: var(--space-2) var(--space-4);
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: var(--radius-full);
      font-size: 0.8rem;
      color: var(--text-secondary);
      transition: all var(--transition-fast);
    }

    .credit-badge:hover {
      background: rgba(255, 255, 255, 0.06);
      border-color: rgba(255, 255, 255, 0.12);
    }

    .credit-count {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .user-avatar-btn {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      text-decoration: none;
      font-size: 1rem;
      overflow: hidden;
      transition: all var(--transition-normal);
    }

    .user-avatar-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: scale(1.05);
      border-color: rgba(255, 255, 255, 0.15);
    }

    .user-avatar-btn.logged-in {
      background: var(--gradient-cyan);
      border-color: var(--accent-cyan);
      box-shadow: 0 4px 15px rgba(0, 255, 204, 0.3);
    }

    .user-avatar-btn img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .charge-btn {
      padding: var(--space-2) var(--space-4);
      background: var(--gradient-gold);
      border: none;
      border-radius: var(--radius-full);
      font-family: 'Outfit', sans-serif;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-dark);
      cursor: pointer;
      transition: all var(--transition-normal);
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.25);
    }

    .charge-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 215, 0, 0.35);
    }

    /* ===== 메인 컨테이너 ===== */
    .app-container {
      max-width: 580px;
      margin: 0 auto;
      padding: 88px var(--space-5) 120px;
    }

    /* ===== 최신 당첨 결과 섹션 ===== */
    .latest-result-section {
      background: linear-gradient(145deg, 
        rgba(15, 23, 42, 0.9) 0%, 
        rgba(3, 7, 17, 0.95) 100%);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: var(--radius-2xl);
      padding: var(--space-5);
      margin-bottom: var(--space-5);
      position: relative;
      overflow: hidden;
      box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    .latest-result-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, 
        transparent, 
        rgba(239, 68, 68, 0.8), 
        rgba(249, 115, 22, 0.8), 
        transparent);
    }

    .latest-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--space-4);
    }

    .live-badge {
      display: inline-flex;
      align-items: center;
      gap: var(--space-2);
      padding: var(--space-2) var(--space-3);
      background: rgba(239, 68, 68, 0.12);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: var(--radius-full);
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--accent-red);
    }

    .live-dot {
      width: 6px;
      height: 6px;
      background: var(--accent-red);
      border-radius: 50%;
      animation: pulse-live 1.5s ease infinite;
    }

    @keyframes pulse-live {
      0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
      50% { opacity: 0.8; transform: scale(1.2); box-shadow: 0 0 0 4px rgba(239, 68, 68, 0); }
    }

    .latest-round {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .latest-balls {
      display: flex;
      justify-content: center;
      gap: var(--space-2);
      margin-bottom: var(--space-4);
      padding: var(--space-4);
      background: rgba(0, 0, 0, 0.25);
      border-radius: var(--radius-xl);
      border: 1px solid rgba(255, 255, 255, 0.03);
    }

    .latest-ball {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1rem;
      color: #fff;
      position: relative;
      transform-style: preserve-3d;
      transition: transform var(--transition-bounce);
    }

    .latest-ball:hover {
      transform: translateY(-4px) scale(1.1);
    }

    .latest-ball::before {
      content: '';
      position: absolute;
      top: 8%;
      left: 18%;
      width: 30%;
      height: 25%;
      background: radial-gradient(ellipse at 30% 30%, 
        rgba(255, 255, 255, 0.6) 0%, 
        rgba(255, 255, 255, 0.2) 40%,
        transparent 70%);
      border-radius: 50%;
      transform: rotate(-25deg);
    }

    .bonus-separator {
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-muted);
      font-size: 1.2rem;
      margin: 0 var(--space-1);
    }

    .latest-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: var(--space-3);
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .latest-prize {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .latest-prize strong {
      color: var(--accent-gold);
      font-weight: 700;
    }

    .latest-link {
      font-size: 0.8rem;
      color: var(--accent-cyan);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: var(--space-1);
      transition: all var(--transition-fast);
    }

    .latest-link:hover {
      color: var(--accent-cyan-light);
      transform: translateX(2px);
    }

    /* ===== AI vs 실제 비교 섹션 ===== */
    .ai-comparison-section {
      background: linear-gradient(145deg, 
        rgba(15, 23, 42, 0.9) 0%, 
        rgba(3, 7, 17, 0.95) 100%);
      border: 1px solid rgba(0, 255, 204, 0.15);
      border-radius: var(--radius-2xl);
      padding: var(--space-5);
      margin-bottom: var(--space-5);
      box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.3),
        0 0 40px rgba(0, 255, 204, 0.03);
    }

    .comparison-header {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      margin-bottom: var(--space-4);
    }

    .comparison-icon {
      width: 36px;
      height: 36px;
      background: rgba(0, 255, 204, 0.1);
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }

    .comparison-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .comparison-rows {
      display: flex;
      flex-direction: column;
      gap: var(--space-3);
      margin-bottom: var(--space-4);
    }

    .comparison-row {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      padding: var(--space-3);
      background: rgba(0, 0, 0, 0.2);
      border-radius: var(--radius-lg);
      border: 1px solid rgba(255, 255, 255, 0.03);
    }

    .comparison-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      width: 70px;
      flex-shrink: 0;
    }

    .comparison-balls {
      display: flex;
      gap: var(--space-2);
      flex: 1;
    }

    .mini-ball {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.7rem;
      color: #fff;
      position: relative;
      transition: transform var(--transition-fast);
    }

    .mini-ball:hover {
      transform: scale(1.15);
    }

    .mini-ball::before {
      content: '';
      position: absolute;
      top: 3px;
      left: 6px;
      width: 6px;
      height: 5px;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      transform: rotate(-25deg);
    }

    .mini-ball.matched {
      box-shadow: 0 0 0 2px var(--accent-cyan), 0 0 15px rgba(0, 255, 204, 0.5);
      animation: ball-matched 0.5s ease;
    }

    @keyframes ball-matched {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.2); }
    }

    .comparison-result {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: var(--space-3) var(--space-4);
      background: rgba(0, 255, 204, 0.08);
      border: 1px solid rgba(0, 255, 204, 0.15);
      border-radius: var(--radius-lg);
    }

    .match-count {
      font-family: 'Outfit', sans-serif;
      font-size: 0.95rem;
      color: var(--accent-cyan);
      font-weight: 700;
    }

    .match-numbers {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .comparison-disclaimer {
      margin-top: 12px;
      padding: 10px 12px;
      background: rgba(255, 215, 95, 0.08);
      border-radius: 10px;
      font-size: 0.75rem;
      color: var(--accent-gold);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* ===== 신뢰 배지 섹션 (신뢰도 기능 5) ===== */
    .trust-section {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 20px;
    }

    .trust-item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 12px;
      font-size: 0.75rem;
      color: var(--text-secondary);
    }

    .trust-check {
      color: var(--accent-cyan);
      font-weight: 700;
    }

    /* ===== 대시보드 상태 ===== */
    .dashboard-view {
      display: block;
    }

    .dashboard-view.hidden {
      display: none;
    }

    /* 환영 메시지 */
    .welcome-section {
      text-align: center;
      margin-bottom: 24px;
    }

    .user-avatar {
      width: 64px;
      height: 64px;
      background: var(--gradient-cyan);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 auto 16px;
    }

    .welcome-text h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .welcome-text p {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    /* 분석 스타일 선택 */
    .style-section {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 24px;
      margin-bottom: 24px;
    }

    .style-title {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .style-multi-badge {
      padding: 4px 10px;
      background: rgba(255, 215, 95, 0.15);
      border: 1px solid rgba(255, 215, 95, 0.3);
      border-radius: 12px;
      font-size: 0.65rem;
      color: var(--accent-gold);
      font-weight: 500;
    }

    .style-buttons-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 12px;
    }

    .style-buttons-grid .style-btn:last-child {
      grid-column: span 2;
    }

    .style-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      background: rgba(255, 255, 255, 0.03);
      border: 2px solid rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    }

    .style-btn:hover {
      background: rgba(255, 255, 255, 0.06);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .style-btn.active {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
    }

    .style-icon {
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .style-name {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-primary);
      display: block;
    }

    .style-btn.active .style-name {
      color: var(--accent-cyan);
    }

    .style-desc {
      font-size: 0.65rem;
      color: var(--text-muted);
      display: block;
      margin-top: 2px;
    }

    .style-check {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 22px;
      height: 22px;
      background: transparent;
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      color: transparent;
      transition: all 0.2s ease;
    }

    .style-btn.active .style-check {
      background: var(--accent-cyan);
      border-color: var(--accent-cyan);
      color: var(--primary-dark);
    }

    .style-selected-count {
      text-align: center;
      font-size: 0.8rem;
      color: var(--text-muted);
      padding-top: 8px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .style-selected-count span {
      color: var(--accent-cyan);
      font-weight: 700;
    }

    /* 분석 시작 버튼 */
    .analyze-section {
      margin-bottom: 24px;
    }

    .analyze-btn {
      width: 100%;
      padding: 20px;
      background: var(--gradient-cyan);
      border: none;
      border-radius: 16px;
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 10px 40px rgba(0, 224, 164, 0.3);
      transition: all 0.3s ease;
    }

    .analyze-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 50px rgba(0, 224, 164, 0.4);
    }

    .analyze-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }

    .analyze-cost {
      text-align: center;
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-top: 10px;
    }

    .analyze-cost span {
      color: var(--accent-cyan);
      font-weight: 600;
    }

    /* 이전 분석 내역 */
    .history-section {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 24px;
    }

    .history-title {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .history-empty {
      text-align: center;
      padding: 32px;
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    .history-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .history-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 16px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .history-item:hover {
      background: rgba(255, 255, 255, 0.06);
    }

    .history-numbers {
      font-family: 'Outfit', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .history-meta {
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    /* ===== 분석 결과 상태 ===== */
    .result-view {
      display: none;
    }

    .result-view.visible {
      display: block;
    }

    .result-intro {
      text-align: center;
      margin-bottom: 20px;
    }

    .result-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 50px;
      font-size: 0.85rem;
      color: var(--accent-cyan);
      margin-bottom: 16px;
    }

    .result-intro h2 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .result-intro p {
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    /* 결과 네비게이션 */
    .result-nav {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
      overflow-x: auto;
      padding-bottom: 8px;
      -webkit-overflow-scrolling: touch;
    }

    .result-nav::-webkit-scrollbar {
      display: none;
    }

    .result-nav-btn {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 10px 16px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-secondary);
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .result-nav-btn:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .result-nav-btn.active {
      background: rgba(0, 224, 164, 0.15);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* 결과 카드 */
    .result-cards-container {
      position: relative;
      margin-bottom: 16px;
    }

    .result-card {
      display: none;
      background: rgba(13, 24, 41, 0.95);
      border: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 24px;
      padding: 24px 20px;
      animation: fadeIn 0.3s ease;
    }

    .result-card.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .result-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .result-card-style {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .result-card-style-icon {
      font-size: 1.3rem;
    }

    .result-card-style-name {
      font-family: 'Outfit', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .result-card-number {
      padding: 4px 12px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    /* 3D 볼 */
    .balls-container {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 24px;
      padding: 20px;
      background: rgba(0, 0, 0, 0.25);
      border-radius: 16px;
    }

    .ball-3d {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
      color: #fff;
      position: relative;
      opacity: 0;
      animation: ballPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .ball-3d::after {
      content: '';
      position: absolute;
      top: 8px;
      left: 12px;
      width: 12px;
      height: 8px;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      transform: rotate(-30deg);
    }

    .ball-3d:nth-child(1) { animation-delay: 0.1s; }
    .ball-3d:nth-child(2) { animation-delay: 0.2s; }
    .ball-3d:nth-child(3) { animation-delay: 0.3s; }
    .ball-3d:nth-child(4) { animation-delay: 0.4s; }
    .ball-3d:nth-child(5) { animation-delay: 0.5s; }
    .ball-3d:nth-child(6) { animation-delay: 0.6s; }

    @keyframes ballPop {
      0% { opacity: 0; transform: scale(0); }
      100% { opacity: 1; transform: scale(1); }
    }

    .ball-yellow { background: var(--ball-yellow); box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4); }
    .ball-blue { background: var(--ball-blue); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }
    .ball-red { background: var(--ball-red); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4); }
    .ball-gray { background: var(--ball-gray); box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4); }
    .ball-green { background: var(--ball-green); box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4); }

    /* 번호별 선정 이유 */
    .number-stories {
      margin-bottom: 20px;
      padding: 16px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 14px;
    }

    .story-title {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 12px;
    }

    .story-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }

    .story-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 10px;
    }

    .story-ball {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.75rem;
      color: #fff;
      flex-shrink: 0;
    }

    .story-content {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .story-tag {
      font-size: 0.65rem;
      font-weight: 600;
      padding: 2px 6px;
      border-radius: 4px;
      width: fit-content;
    }

    .tag-hot { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .tag-cold { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .tag-balance { background: rgba(0, 224, 164, 0.15); color: var(--accent-cyan); }

    .story-desc {
      font-size: 0.7rem;
      color: var(--text-muted);
    }

    /* 리포트 요약 */
    .report-summary {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 16px;
    }

    .summary-tag {
      padding: 6px 12px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 20px;
      font-size: 0.8rem;
      color: var(--accent-cyan);
    }

    .report-insights {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-bottom: 16px;
      line-height: 1.6;
      padding: 12px;
      background: rgba(0, 224, 164, 0.05);
      border-radius: 10px;
    }

    /* 균형 점수 */
    .balance-section {
      padding: 16px;
      background: rgba(0, 224, 164, 0.05);
      border: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 14px;
    }

    .balance-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .balance-label {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .balance-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--accent-cyan);
    }

    .balance-bar {
      height: 8px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 4px;
      overflow: hidden;
      margin-bottom: 12px;
    }

    .balance-fill {
      height: 100%;
      background: var(--gradient-cyan);
      border-radius: 4px;
      width: 0;
    }

    @keyframes fillBar {
      to { width: var(--target-width, 87%); }
    }

    .balance-details {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
    }

    .balance-item {
      text-align: center;
      padding: 8px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 8px;
    }

    .balance-item-icon {
      font-size: 1rem;
      margin-bottom: 2px;
    }

    .balance-item-value {
      font-family: 'Outfit', sans-serif;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--text-primary);
    }

    .balance-item-label {
      font-size: 0.6rem;
      color: var(--text-muted);
    }

    /* 결과 인디케이터 */
    .result-indicators {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-bottom: 20px;
    }

    .result-indicator {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .result-indicator.active {
      width: 24px;
      border-radius: 4px;
      background: var(--accent-cyan);
    }

    /* 액션 버튼 */
    .result-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 20px;
    }

    .action-btn {
      padding: 16px;
      border-radius: 14px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .action-btn-primary {
      background: var(--gradient-cyan);
      border: none;
      color: var(--primary-dark);
    }

    .action-btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(0, 224, 164, 0.3);
    }

    .action-btn-secondary {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: var(--text-primary);
    }

    .action-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    /* 면책 조항 */
    .disclaimer {
      padding: 16px;
      background: rgba(239, 68, 68, 0.05);
      border: 1px solid rgba(239, 68, 68, 0.15);
      border-radius: 12px;
      margin-bottom: 20px;
    }

    .disclaimer p {
      font-size: 0.7rem;
      color: #ef4444;
      margin-bottom: 6px;
      text-align: center;
    }

    .disclaimer ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 4px;
    }

    .disclaimer li {
      font-size: 0.65rem;
      color: var(--text-muted);
      padding-left: 10px;
      position: relative;
    }

    .disclaimer li::before {
      content: '•';
      position: absolute;
      left: 0;
      color: rgba(239, 68, 68, 0.5);
    }

    /* ===== 로딩 모달 ===== */
    .loading-modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(11, 19, 43, 0.98);
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
      padding: 40px;
    }

    .loading-spinner {
      width: 64px;
      height: 64px;
      border: 4px solid rgba(0, 224, 164, 0.2);
      border-top-color: var(--accent-cyan);
      border-radius: 50%;
      margin: 0 auto 24px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .loading-text {
      font-size: 1rem;
      color: var(--text-secondary);
      margin-bottom: 20px;
    }

    .loading-progress {
      width: 200px;
      height: 4px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 2px;
      margin: 0 auto;
      overflow: hidden;
    }

    .loading-bar {
      height: 100%;
      background: var(--gradient-cyan);
      border-radius: 2px;
      width: 0;
      transition: width 0.3s ease;
    }

    .loading-data {
      margin-top: 28px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      max-width: 340px;
      text-align: left;
      opacity: 0;
      transform: translateY(10px);
      animation: fadeInUp 0.5s ease 0.5s forwards;
    }

    @keyframes fadeInUp {
      to { opacity: 1; transform: translateY(0); }
    }

    .data-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .data-source {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-cyan);
    }

    .data-update {
      font-size: 0.7rem;
      color: var(--text-muted);
    }

    .data-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 16px;
    }

    .stat-item {
      text-align: center;
      padding: 10px 8px;
      background: rgba(0, 224, 164, 0.05);
      border-radius: 10px;
    }

    .stat-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .stat-label {
      font-size: 0.6rem;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .recent-numbers {
      background: rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      padding: 12px;
    }

    .recent-title {
      font-size: 0.7rem;
      color: var(--text-muted);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .recent-row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      opacity: 0;
      animation: slideIn 0.3s ease forwards;
    }

    .recent-row:last-child {
      border-bottom: none;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-10px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .recent-round {
      font-size: 0.7rem;
      color: var(--text-muted);
      width: 50px;
    }

    .recent-balls {
      display: flex;
      gap: 4px;
    }

    .analyzing-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      background: rgba(0, 224, 164, 0.1);
      border-radius: 10px;
      font-size: 0.6rem;
      color: var(--accent-cyan);
      animation: pulse 1.5s ease infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    /* ===== 모바일 반응형 ===== */
    @media (max-width: 640px) {
      /* 기본 시인성 강화 */
      body {
        font-size: 16px;
        line-height: 1.7;
      }

      .app-navbar {
        padding: 0 16px;
        height: 60px;
      }

      .app-logo {
        font-size: 1.15rem !important;
      }

      .app-container {
        padding: 80px 16px 100px;
      }

      .trust-section {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .trust-item {
        padding: 16px;
        font-size: 0.95rem;
      }

      .style-buttons-grid {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .style-buttons-grid .style-btn:last-child {
        grid-column: span 1;
      }

      .style-btn {
        padding: 18px 16px;
        min-height: 52px;
        font-size: 1rem;
      }

      .balls-container {
        gap: 10px;
        padding: 20px 16px;
        flex-wrap: wrap;
        justify-content: center;
      }

      .ball-3d {
        width: 48px;
        height: 48px;
        font-size: 1.15rem;
        font-weight: 700;
      }

      .latest-balls {
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
      }

      .latest-ball {
        width: 42px;
        height: 42px;
        font-size: 1rem;
        font-weight: 700;
      }

      .story-grid {
        grid-template-columns: 1fr;
      }

      .result-actions {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .action-btn-primary,
      .action-btn-secondary {
        padding: 16px 20px;
        font-size: 1rem;
        min-height: 52px;
      }

      .disclaimer ul {
        grid-template-columns: 1fr;
      }

      /* 카드 패딩 증가 */
      .section-card,
      .result-card,
      .history-section {
        padding: 24px 20px;
        border-radius: 20px;
      }

      /* 섹션 타이틀 */
      .section-title {
        font-size: 1.1rem;
      }

      /* 분석 버튼 */
      .analyze-btn,
      .reanalyze-btn {
        padding: 18px 24px;
        font-size: 1.1rem;
        min-height: 56px;
      }

      /* 충전 모달 */
      .charge-modal-body {
        padding: 24px 20px;
      }

      .charge-option {
        padding: 18px 16px;
      }

      .charge-submit-btn {
        padding: 18px 24px;
        font-size: 1.05rem;
        min-height: 56px;
      }

      /* 결과 네비게이션 */
      .result-nav {
        gap: 10px;
        padding: 8px 0;
      }

      .result-nav-btn {
        padding: 12px 18px;
        font-size: 0.95rem;
      }

      /* 크레딧 배지 */
      .credit-badge {
        padding: 10px 14px;
        font-size: 0.9rem;
      }

      .charge-btn {
        padding: 10px 16px;
        font-size: 0.9rem;
      }

      /* 히스토리 */
      .history-item {
        padding: 16px;
      }

      .history-numbers {
        font-size: 1rem;
      }
    }

    @media (max-width: 375px) {
      .app-container {
        padding: 72px 12px 90px;
      }

      .ball-3d {
        width: 44px;
        height: 44px;
        font-size: 1.05rem;
      }

      .latest-ball {
        width: 38px;
        height: 38px;
        font-size: 0.95rem;
      }

      .section-card {
        padding: 20px 16px;
      }

      .analyze-btn {
        padding: 16px 20px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- 상단 네비게이션 -->
  <nav class="app-navbar">
    <a href="index.html" class="app-logo">
      <div class="app-logo-icon">
        <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
          <!-- 3D Lotto Ball -->
          <circle cx="11" cy="12" r="8" fill="url(#gold-ball-nav)"/>
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
            <linearGradient id="gold-ball-nav" x1="20%" y1="20%" x2="80%" y2="80%">
              <stop offset="0%" stop-color="#ffe066"/>
              <stop offset="50%" stop-color="#ffd700"/>
              <stop offset="100%" stop-color="#cc9f00"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      로또인사이트
    </a>
    <div class="navbar-right">
      <div class="credit-badge">
        <span>🔋</span>
        <span>남은 분석</span>
        <span class="credit-count" id="navCredit">1회</span>
      </div>
      <button class="charge-btn" id="chargeBtn">+ 충전</button>
      <a href="auth.html" class="user-avatar-btn" id="userAvatarBtn" aria-label="사용자 프로필">
        <span id="userAvatarIcon">👤</span>
      </a>
    </div>
  </nav>

  <!-- 메인 컨테이너 -->
  <div class="app-container">
    
    <!-- ===== 대시보드 ===== -->
    <div class="dashboard-view" id="dashboardView">

      <!-- 🔴 최신 당첨 결과 섹션 (신뢰도 기능 1) -->
      <div class="latest-result-section" id="latestResultSection">
        <div class="latest-header">
          <div class="live-badge">
            <span class="live-dot"></span>
            LIVE 최신 당첨 결과
          </div>
          <span class="latest-round" id="latestRound">1201회차</span>
        </div>
        <div class="latest-balls" id="latestBalls">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="latest-info">
          <span class="latest-prize" id="latestPrize">
            추첨일: <span id="latestDate">2025-12-06</span> (토)
          </span>
          <a href="https://dhlottery.co.kr" target="_blank" rel="noopener" class="latest-link">
            동행복권에서 확인 →
          </a>
        </div>
      </div>

      <!-- 📋 AI vs 실제 비교 섹션 (신뢰도 기능 2) -->
      <div class="ai-comparison-section" id="aiComparisonSection">
        <div class="comparison-header">
          <span class="comparison-icon">📋</span>
          <span class="comparison-title">지난 주 AI 추천 vs 실제 결과</span>
        </div>
        <div class="comparison-rows" id="comparisonRows">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="comparison-result" id="comparisonResult">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="comparison-disclaimer">
          ⚠️ AI는 당첨을 보장하지 않습니다. 통계 기반 참고 정보입니다.
        </div>
      </div>

      <!-- ✅ 신뢰 배지 (신뢰도 기능 5) -->
      <div class="trust-section">
        <div class="trust-item">
          <span class="trust-check">✓</span>
          동행복권 공식 데이터 사용
        </div>
        <div class="trust-item">
          <span class="trust-check">✓</span>
          <span id="trustRound">1,201</span>회차 실시간 반영
        </div>
        <div class="trust-item">
          <span class="trust-check">✓</span>
          23년간 7,206개 번호 분석
        </div>
        <div class="trust-item">
          <span class="trust-check">✓</span>
          알고리즘 100% 투명 공개
        </div>
      </div>
      
      <!-- 환영 메시지 -->
      <div class="welcome-section">
        <div class="user-avatar" id="userAvatar">김</div>
        <div class="welcome-text">
          <h1 id="welcomeTitle">👋 김** 님, 환영합니다!</h1>
          <p id="welcomeSubtitle">1,201회차 데이터로 분석해 드릴게요</p>
        </div>
      </div>

      <!-- 분석 스타일 선택 -->
      <div class="style-section">
        <div class="style-title">
          📊 분석 스타일 선택
          <span class="style-multi-badge">복수 선택 가능</span>
        </div>
        <div class="style-buttons-grid">
          <button class="style-btn active" data-style="hotcold">
            <span class="style-icon">🔥</span>
            <div>
              <span class="style-name">Hot/Cold</span>
              <span class="style-desc">과출/미출 패턴 분석</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn active" data-style="balance">
            <span class="style-icon">⚖️</span>
            <div>
              <span class="style-name">홀짝/고저</span>
              <span class="style-desc">균형 비율 최적화</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="color">
            <span class="style-icon">🎨</span>
            <div>
              <span class="style-name">색상볼 통계</span>
              <span class="style-desc">노랑/파랑/빨강 분포</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="correlation">
            <span class="style-icon">🧠</span>
            <div>
              <span class="style-name">상관관계</span>
              <span class="style-desc">동반출현 패턴</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="ac">
            <span class="style-icon">🧮</span>
            <div>
              <span class="style-name">AC값 분석</span>
              <span class="style-desc">숫자 다양성 지수</span>
            </div>
            <span class="style-check">✓</span>
          </button>
        </div>
        <div class="style-selected-count">
          <span id="selectedCount">2</span>개 스타일 선택됨
        </div>
      </div>

      <!-- 분석 시작 버튼 -->
      <div class="analyze-section">
        <button class="analyze-btn" id="analyzeBtn">
          🔮 AI 분석 시작하기
        </button>
        <p class="analyze-cost">무료 분석 <span>1회</span> 사용</p>
      </div>

      <!-- 이전 분석 내역 -->
      <div class="history-section">
        <div class="history-title">📜 이전 분석 내역</div>
        <div class="history-empty">
          아직 분석 내역이 없습니다.<br>
          첫 번째 분석을 시작해보세요! 🎯
        </div>
      </div>

    </div>

    <!-- ===== 분석 결과 ===== -->
    <div class="result-view" id="resultView">
      
      <!-- 결과 인트로 -->
      <div class="result-intro">
        <div class="result-badge">
          ✨ AI 분석 완료
        </div>
        <h2>당신만의 맞춤 조합입니다</h2>
        <p id="resultSubtitle">5개 스타일 · 1,201회차 데이터 기반</p>
      </div>

      <!-- 결과 네비게이션 -->
      <div class="result-nav" id="resultNav">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 결과 카드 컨테이너 -->
      <div class="result-cards-container" id="resultCardsContainer">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 결과 인디케이터 -->
      <div class="result-indicators" id="resultIndicators">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 액션 버튼 -->
      <div class="result-actions">
        <button class="action-btn action-btn-primary" id="reanalyzeBtn">
          🔄 다시 분석하기
        </button>
        <button class="action-btn action-btn-secondary">
          💾 저장하기
        </button>
      </div>

      <!-- 면책 조항 -->
      <div class="disclaimer">
        <p><strong>⚠️ 중요 안내</strong></p>
        <ul>
          <li>통계 패턴 기반 참고 정보</li>
          <li>모든 조합 확률 동일 (1/8,145,060)</li>
          <li>당첨 보장/예측 아님</li>
          <li>만 19세 이상 이용</li>
          <li>동행복권에서만 구매</li>
        </ul>
      </div>

      <!-- 대시보드로 돌아가기 -->
      <button class="action-btn action-btn-secondary" style="width: 100%;" id="backBtn">
        ← 대시보드로 돌아가기
      </button>

    </div>
  </div>

  <!-- 로딩 모달 -->
  <div class="loading-modal" id="loadingModal">
    <div class="loading-content">
      <div class="loading-spinner"></div>
      <div class="loading-text" id="loadingText">AI가 패턴을 분석 중...</div>
      <div class="loading-progress">
        <div class="loading-bar" id="loadingBar"></div>
      </div>
      
      <div class="loading-data" id="loadingData">
        <div class="data-header">
          <span class="data-source">📡 동행복권 데이터</span>
          <span class="data-update" id="dataUpdate">업데이트: --</span>
        </div>
        <div class="data-stats" id="dataStats">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="recent-numbers" id="recentNumbers">
          <!-- 최근 당첨 번호 표시 -->
        </div>
      </div>
    </div>
  </div>

  <!-- 번호 생성 엔진 -->
  <script src="/scripts/lotto-data.js"></script>
  <script src="/scripts/lotto-generator.js"></script>
  
  <script>
    // ===== 스토리지 매니저 =====
    const StorageManager = {
      KEYS: {
        USER: 'lottoinsight_user',
        USER_INFO: 'lottoinsight_user_info',
        FREE_CREDIT: 'lottoinsight_free',
        PAID_CREDIT: 'lottoinsight_paid',
        LOGIN_TYPE: 'lottoinsight_login_type',
        HISTORY: 'lottoinsight_history',
        SAVED: 'lottoinsight_saved'
      },
      
      get(key, defaultValue = null) {
        try {
          const value = localStorage.getItem(key);
          return value ? JSON.parse(value) : defaultValue;
        } catch {
          return localStorage.getItem(key) || defaultValue;
        }
      },
      
      set(key, value) {
        try {
          localStorage.setItem(key, typeof value === 'object' ? JSON.stringify(value) : value);
        } catch (e) {
          console.error('저장 실패:', e);
        }
      },
      
      // 히스토리 관리
      getHistory() {
        return this.get(this.KEYS.HISTORY, []);
      },
      
      addHistory(item) {
        const history = this.getHistory();
        history.unshift({
          ...item,
          id: Date.now(),
          date: new Date().toISOString()
        });
        // 최대 50개까지만 저장
        this.set(this.KEYS.HISTORY, history.slice(0, 50));
      },
      
      // 저장된 번호 관리
      getSaved() {
        return this.get(this.KEYS.SAVED, []);
      },
      
      addSaved(item) {
        const saved = this.getSaved();
        saved.unshift({
          ...item,
          id: Date.now(),
          savedAt: new Date().toISOString()
        });
        this.set(this.KEYS.SAVED, saved.slice(0, 100));
        return true;
      },
      
      removeSaved(id) {
        const saved = this.getSaved();
        this.set(this.KEYS.SAVED, saved.filter(s => s.id !== id));
      },
      
      // 크레딧 관리
      getCredits() {
        return {
          free: parseInt(localStorage.getItem(this.KEYS.FREE_CREDIT) || '1'),
          paid: parseInt(localStorage.getItem(this.KEYS.PAID_CREDIT) || '0')
        };
      },
      
      setCredits(free, paid) {
        localStorage.setItem(this.KEYS.FREE_CREDIT, String(free));
        localStorage.setItem(this.KEYS.PAID_CREDIT, String(paid));
      },
      
      useCredit(amount) {
        let { free, paid } = this.getCredits();
        for (let i = 0; i < amount; i++) {
          if (free > 0) free--;
          else if (paid > 0) paid--;
        }
        this.setCredits(free, paid);
        return { free, paid };
      }
    };
    
    // ===== 로그인 상태 체크 =====
    function checkLoginStatus() {
      const loggedUser = localStorage.getItem(StorageManager.KEYS.USER);
      const userInfoStr = localStorage.getItem(StorageManager.KEYS.USER_INFO);
      const loginType = localStorage.getItem(StorageManager.KEYS.LOGIN_TYPE);
      const { free, paid } = StorageManager.getCredits();
      
      let userInfo = null;
      try {
        userInfo = userInfoStr ? JSON.parse(userInfoStr) : null;
      } catch (e) {
        console.error('사용자 정보 파싱 실패:', e);
      }
      
      return {
        isLoggedIn: !!loggedUser,
        freeCredit: free,
        paidCredit: paid,
        userName: userInfo?.nickname || '게스트',
        profileImage: userInfo?.profileImage || null,
        userId: loggedUser || null,
        loginType: loginType || null
      };
    }
    
    const loginStatus = checkLoginStatus();
    
    let state = {
      isLoggedIn: loginStatus.isLoggedIn,
      freeCredit: loginStatus.freeCredit,
      paidCredit: loginStatus.paidCredit,
      userName: loginStatus.userName,
      profileImage: loginStatus.profileImage,
      userId: loginStatus.userId,
      loginType: loginStatus.loginType,
      selectedStyles: ['hotcold', 'balance'],
      history: StorageManager.getHistory(),
      results: []
    };

    // ===== DOM 요소 =====
    const dashboardView = document.getElementById('dashboardView');
    const resultView = document.getElementById('resultView');
    const loadingModal = document.getElementById('loadingModal');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const reanalyzeBtn = document.getElementById('reanalyzeBtn');
    const backBtn = document.getElementById('backBtn');
    const styleButtons = document.querySelectorAll('.style-btn');
    const selectedCountEl = document.getElementById('selectedCount');

    // ===== 최신 당첨 결과 표시 =====
    function renderLatestResult() {
      const data = LottoDataLoader.data;
      const latest = data.latestResult;
      
      // 회차 업데이트
      document.getElementById('latestRound').textContent = `${latest.round}회차`;
      document.getElementById('latestDate').textContent = latest.date;
      document.getElementById('trustRound').textContent = data.currentRound.toLocaleString();
      
      // 공 렌더링
      const ballsContainer = document.getElementById('latestBalls');
      let ballsHTML = latest.numbers.map(n => 
        `<div class="latest-ball ball-${getBallColor(n)}">${n}</div>`
      ).join('');
      ballsHTML += `<span class="bonus-separator">+</span>`;
      ballsHTML += `<div class="latest-ball ball-${getBallColor(latest.bonus)}">${latest.bonus}</div>`;
      ballsContainer.innerHTML = ballsHTML;
    }

    // ===== AI vs 실제 비교 표시 =====
    function renderAiComparison() {
      const data = LottoDataLoader.data;
      const prediction = data.lastAiPrediction;
      const actual = data.latestResult;
      
      // 비교 행 렌더링
      const rowsContainer = document.getElementById('comparisonRows');
      rowsContainer.innerHTML = `
        <div class="comparison-row">
          <span class="comparison-label">AI 추천</span>
          <div class="comparison-balls">
            ${prediction.numbers.map(n => {
              const isMatched = prediction.matchedNumbers.includes(n);
              return `<div class="mini-ball ball-${getBallColor(n)} ${isMatched ? 'matched' : ''}">${n}</div>`;
            }).join('')}
          </div>
        </div>
        <div class="comparison-row">
          <span class="comparison-label">실제 당첨</span>
          <div class="comparison-balls">
            ${actual.numbers.map(n => {
              const isMatched = prediction.matchedNumbers.includes(n);
              return `<div class="mini-ball ball-${getBallColor(n)} ${isMatched ? 'matched' : ''}">${n}</div>`;
            }).join('')}
          </div>
        </div>
      `;
      
      // 결과 표시
      const resultContainer = document.getElementById('comparisonResult');
      resultContainer.innerHTML = `
        <span class="match-count">✅ ${prediction.matchedCount}개 일치</span>
        <span class="match-numbers">(${prediction.matchedNumbers.join(', ')})</span>
      `;
    }

    // ===== 스타일 선택 =====
    styleButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const style = btn.dataset.style;
        
        if (btn.classList.contains('active')) {
          if (state.selectedStyles.length > 1) {
            btn.classList.remove('active');
            state.selectedStyles = state.selectedStyles.filter(s => s !== style);
          }
        } else {
          btn.classList.add('active');
          state.selectedStyles.push(style);
        }
        
        if (selectedCountEl) {
          selectedCountEl.textContent = state.selectedStyles.length;
        }
        updateAnalyzeCost();
      });
    });

    // ===== 네비게이션 업데이트 =====
    function updateNavbar() {
      const navCredit = document.getElementById('navCredit');
      const userAvatarBtn = document.getElementById('userAvatarBtn');
      const userAvatarIcon = document.getElementById('userAvatarIcon');
      
      // 크레딧 표시
      const totalCredits = state.freeCredit + state.paidCredit;
      if (navCredit) {
        navCredit.textContent = `${totalCredits}회`;
      }
      
      // 사용자 아바타 표시
      if (userAvatarBtn && userAvatarIcon) {
        if (state.isLoggedIn) {
          userAvatarBtn.classList.add('logged-in');
          
          if (state.profileImage) {
            userAvatarIcon.innerHTML = `<img src="${state.profileImage}" alt="프로필">`;
          } else {
            const firstChar = state.userName.charAt(0);
            userAvatarIcon.textContent = firstChar === '게' ? '👤' : firstChar;
            userAvatarIcon.style.color = 'var(--primary-dark)';
            userAvatarIcon.style.fontWeight = '700';
          }
        } else {
          userAvatarBtn.classList.remove('logged-in');
          userAvatarIcon.textContent = '👤';
        }
      }
    }

    // ===== 환영 메시지 =====
    function updateWelcomeMessage() {
      const avatar = document.getElementById('userAvatar');
      const title = document.getElementById('welcomeTitle');
      const subtitle = document.getElementById('welcomeSubtitle');
      
      // 로그인 상태에 따른 표시
      let displayName = '게스트';
      if (state.isLoggedIn) {
        displayName = state.userName || '회원';
      }
      
      const firstChar = displayName.charAt(0).toUpperCase();
      
      // 아바타 표시
      if (state.profileImage) {
        avatar.innerHTML = `<img src="${state.profileImage}" alt="프로필" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
      } else {
        avatar.textContent = firstChar === '게' ? '🎱' : firstChar;
      }
      
      // 환영 메시지
      if (state.isLoggedIn) {
        title.innerHTML = `👋 ${displayName}님, 환영합니다!`;
        subtitle.textContent = `${LottoDataLoader.data.currentRound.toLocaleString()}회차 데이터로 분석해 드릴게요`;
      } else {
        title.innerHTML = `🎱 AI 로또 분석`;
        subtitle.innerHTML = `<a href="auth.html" style="color:var(--accent-gold);text-decoration:underline;">로그인</a>하면 분석 결과를 저장할 수 있어요`;
      }
    }

    // ===== 분석 시작 =====
    analyzeBtn.addEventListener('click', startAnalysis);
    reanalyzeBtn.addEventListener('click', startAnalysis);

    async function startAnalysis() {
      const requiredCredits = state.selectedStyles.length;
      const totalCredits = state.freeCredit + state.paidCredit;
      
      if (totalCredits < requiredCredits) {
        alert(`크레딧이 부족합니다!\n필요: ${requiredCredits}크레딧\n보유: ${totalCredits}크레딧`);
        return;
      }

      if (!lottoGenerator.ready) {
        await lottoGenerator.init();
      }

      showLoading();
    }

    function showLoading() {
      loadingModal.classList.add('active');
      const loadingBar = document.getElementById('loadingBar');
      const loadingText = document.getElementById('loadingText');
      const dataStats = document.getElementById('dataStats');
      const recentNumbers = document.getElementById('recentNumbers');
      const dataUpdate = document.getElementById('dataUpdate');
      
      const messages = [
        "📊 동행복권 데이터 로딩 중...",
        "🔍 최근 100회 당첨 패턴 분석...",
        "📈 과출/미출 번호 계산 중...",
        "⚖️ 홀짝/고저 균형 최적화...",
        "✨ 최종 조합 선별 중..."
      ];

      let progress = 0;
      let msgIndex = 0;
      let dataShown = false;

      const interval = setInterval(() => {
        progress += Math.random() * 12 + 4;
        if (progress > 100) progress = 100;
        
        loadingBar.style.width = progress + '%';
        
        if (progress > msgIndex * 20 && msgIndex < messages.length) {
          loadingText.textContent = messages[msgIndex];
          msgIndex++;
        }

        if (progress > 30 && !dataShown && lottoGenerator.dataLoader?.data) {
          dataShown = true;
          showRealData(dataStats, recentNumbers, dataUpdate);
        }

        if (progress >= 100) {
          clearInterval(interval);
          setTimeout(() => completeAnalysis(), 500);
        }
      }, 300);
    }

    function showRealData(dataStats, recentNumbers, dataUpdate) {
      const data = lottoGenerator.dataLoader.data;
      if (!data) return;

      dataUpdate.textContent = `업데이트: ${data.lastUpdate}`;

      const history = data.history;
      const allNumbers = history.flatMap(h => h.numbers);
      
      const freq = {};
      allNumbers.forEach(n => freq[n] = (freq[n] || 0) + 1);
      const hotNum = Object.entries(freq).sort((a, b) => b[1] - a[1])[0];
      
      const recent = {};
      for (let i = 1; i <= 45; i++) recent[i] = history.length;
      history.forEach((h, idx) => {
        h.numbers.forEach(n => { if (recent[n] > idx) recent[n] = idx; });
      });
      const coldNum = Object.entries(recent).sort((a, b) => b[1] - a[1])[0];

      dataStats.innerHTML = `
        <div class="stat-item">
          <div class="stat-value">${data.currentRound}회</div>
          <div class="stat-label">현재 회차</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">${hotNum[0]}번</div>
          <div class="stat-label">최다 출현 (${hotNum[1]}회)</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">${coldNum[0]}번</div>
          <div class="stat-label">${coldNum[1]}회 연속 미출</div>
        </div>
      `;

      const recentThree = history.slice(0, 3);
      recentNumbers.innerHTML = `
        <div class="recent-title">
          <span>🎱 최근 당첨 번호</span>
          <span class="analyzing-badge">분석 중</span>
        </div>
        ${recentThree.map((h, idx) => `
          <div class="recent-row" style="animation-delay: ${idx * 0.15}s">
            <span class="recent-round">${h.round}회</span>
            <div class="recent-balls">
              ${h.numbers.map(n => `<span class="mini-ball ball-${getBallColor(n)}">${n}</span>`).join('')}
            </div>
          </div>
        `).join('')}
      `;
    }

    // ===== 스타일 정보 =====
    const styleInfo = {
      'hotcold': { icon: '🔥', name: 'Hot/Cold', genStyle: 'hot', desc: '과출/미출 번호 중심' },
      'balance': { icon: '⚖️', name: '홀짝/고저', genStyle: 'balanced', desc: '균형 비율 최적화' },
      'color': { icon: '🎨', name: '색상볼', genStyle: 'color', desc: '색상 분포 균형' },
      'correlation': { icon: '🧠', name: '상관관계', genStyle: 'pair', desc: '동반출현 패턴' },
      'ac': { icon: '🧮', name: 'AC값', genStyle: 'balanced', desc: '다양성 지수 최적화' }
    };

    async function completeAnalysis() {
      const stylesToAnalyze = state.selectedStyles.length;
      
      // 크레딧 차감 및 저장
      const { free, paid } = StorageManager.useCredit(stylesToAnalyze);
      state.freeCredit = free;
      state.paidCredit = paid;
      updateCreditDisplay();

      state.results = [];
      
      for (const style of state.selectedStyles) {
        const info = styleInfo[style] || { icon: '📊', name: style, genStyle: 'balanced' };
        
        let result;
        try {
          result = lottoGenerator.generate(info.genStyle);
        } catch (error) {
          console.error('생성 오류:', error);
          result = lottoGenerator.generateBasic();
        }
        
        const resultItem = {
          style: style,
          info: info,
          ...result
        };
        
        state.results.push(resultItem);
        
        // 히스토리에 추가 (localStorage에도 저장)
        const historyItem = {
          numbers: result.numbers,
          style: style,
          styleName: info.name,
          score: result.score,
          round: LottoDataLoader.data.currentRound
        };
        
        StorageManager.addHistory(historyItem);
        state.history = StorageManager.getHistory();
      }

      renderAllResults();

      loadingModal.classList.remove('active');
      dashboardView.classList.add('hidden');
      resultView.classList.add('visible');
    }

    // ===== 결과 렌더링 =====
    function renderAllResults() {
      const resultNav = document.getElementById('resultNav');
      const resultCardsContainer = document.getElementById('resultCardsContainer');
      const resultIndicators = document.getElementById('resultIndicators');
      const resultSubtitle = document.getElementById('resultSubtitle');
      
      if (resultSubtitle) {
        resultSubtitle.textContent = `${state.results.length}개 스타일 · ${LottoDataLoader.data.currentRound.toLocaleString()}회차 데이터 기반`;
      }
      
      resultNav.innerHTML = state.results.map((r, idx) => `
        <button class="result-nav-btn ${idx === 0 ? 'active' : ''}" data-index="${idx}">
          <span class="nav-icon">${r.info.icon}</span>
          <span>${r.info.name}</span>
        </button>
      `).join('');
      
      resultCardsContainer.innerHTML = state.results.map((r, idx) => {
        const report = lottoGenerator.generateReport(r);
        
        return `
        <div class="result-card ${idx === 0 ? 'active' : ''}" data-index="${idx}">
          <div class="result-card-header">
            <div class="result-card-style">
              <span class="result-card-style-icon">${r.info.icon}</span>
              <span class="result-card-style-name">${r.info.name} 분석</span>
            </div>
            <span class="result-card-number">${idx + 1}/${state.results.length}</span>
          </div>
          
          <div class="balls-container">
            ${r.numbers.map((num, i) => `
              <div class="ball-3d ball-${getBallColor(num)}" style="animation-delay: ${i * 0.1}s">${num}</div>
            `).join('')}
          </div>
          
          <div class="number-stories">
            <div class="story-title">📋 번호별 선정 이유</div>
            <div class="story-grid">
              ${r.stories.map(story => `
                <div class="story-item">
                  <span class="story-ball ball-${getBallColor(story.number)}">${story.number}</span>
                  <div class="story-content">
                    <span class="story-tag ${story.type === 'hot' ? 'tag-hot' : story.type === 'cold' ? 'tag-cold' : 'tag-balance'}">${story.label}</span>
                    <span class="story-desc">${story.description}</span>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>

          <div class="report-summary">
            ${report.summary.map(s => `<span class="summary-tag">${s}</span>`).join('')}
          </div>
          <div class="report-insights">
            ${report.insights.join('<br>')}
          </div>

          <div class="balance-section">
            <div class="balance-header">
              <span class="balance-label">📊 균형 점수</span>
              <span class="balance-value">${r.score}<span style="font-size: 0.8rem;">점</span></span>
            </div>
            <div class="balance-bar">
              <div class="balance-fill" style="--target-width: ${r.score}%; animation: fillBar 1.5s ease 0.3s forwards;"></div>
            </div>
            <div class="balance-details">
              <div class="balance-item">
                <div class="balance-item-icon">⚖️</div>
                <div class="balance-item-value">${r.numbers.filter(n => n > 22).length}:${r.numbers.filter(n => n <= 22).length}</div>
                <div class="balance-item-label">고/저</div>
              </div>
              <div class="balance-item">
                <div class="balance-item-icon">🎲</div>
                <div class="balance-item-value">${r.numbers.filter(n => n % 2 === 1).length}:${r.numbers.filter(n => n % 2 === 0).length}</div>
                <div class="balance-item-label">홀/짝</div>
              </div>
              <div class="balance-item">
                <div class="balance-item-icon">📊</div>
                <div class="balance-item-value">상위${Math.round((100 - r.score) / 2 + 5)}%</div>
                <div class="balance-item-label">유사도</div>
              </div>
            </div>
          </div>
        </div>
      `}).join('');
      
      resultIndicators.innerHTML = state.results.map((_, idx) => `
        <div class="result-indicator ${idx === 0 ? 'active' : ''}" data-index="${idx}"></div>
      `).join('');
      
      setupResultNavigation();
    }

    function setupResultNavigation() {
      const navBtns = document.querySelectorAll('.result-nav-btn');
      const cards = document.querySelectorAll('.result-card');
      const indicators = document.querySelectorAll('.result-indicator');
      
      function showResult(index) {
        navBtns.forEach(b => b.classList.remove('active'));
        cards.forEach(c => c.classList.remove('active'));
        indicators.forEach(i => i.classList.remove('active'));
        
        navBtns[index]?.classList.add('active');
        cards[index]?.classList.add('active');
        indicators[index]?.classList.add('active');
      }
      
      navBtns.forEach(btn => {
        btn.addEventListener('click', () => showResult(parseInt(btn.dataset.index)));
      });
      
      indicators.forEach(ind => {
        ind.addEventListener('click', () => showResult(parseInt(ind.dataset.index)));
      });
    }

    function updateCreditDisplay() {
      document.getElementById('navCredit').textContent = (state.freeCredit + state.paidCredit) + '회';
      updateAnalyzeCost();
    }
    
    function updateAnalyzeCost() {
      const analyzeCost = document.querySelector('.analyze-cost');
      const requiredCredits = state.selectedStyles.length;
      const totalCredits = state.freeCredit + state.paidCredit;
      
      if (totalCredits >= requiredCredits) {
        if (state.freeCredit >= requiredCredits) {
          analyzeCost.innerHTML = `무료 분석 <span>${requiredCredits}회</span> 사용`;
        } else if (state.freeCredit > 0) {
          analyzeCost.innerHTML = `무료 <span>${state.freeCredit}회</span> + 유료 <span>${requiredCredits - state.freeCredit}회</span> 사용`;
        } else {
          analyzeCost.innerHTML = `유료 크레딧 <span>${requiredCredits}회</span> 사용`;
        }
      } else {
        analyzeCost.innerHTML = `<span style="color: #ef4444;">크레딧 부족</span> - ${requiredCredits}회 필요 (보유: ${totalCredits}회)`;
      }
    }

    backBtn.addEventListener('click', () => {
      resultView.classList.remove('visible');
      dashboardView.classList.remove('hidden');
      updateHistoryDisplay();
    });

    function updateHistoryDisplay() {
      const historySection = document.querySelector('.history-section');
      state.history = StorageManager.getHistory();
      
      if (state.history.length === 0) {
        historySection.innerHTML = `
          <div class="history-title">📜 이전 분석 내역</div>
          <div class="history-empty">
            아직 분석 내역이 없습니다.<br>
            첫 번째 분석을 시작해보세요! 🎯
          </div>
        `;
      } else {
        const historyItems = state.history.slice(0, 5).map(item => {
          const timeAgo = getTimeAgo(item.date);
          return `
            <div class="history-item" data-numbers="${item.numbers.join(',')}">
              <div style="display: flex; flex-direction: column; gap: 4px;">
                <span class="history-numbers">${item.numbers.join(' · ')}</span>
                <span style="font-size: 0.7rem; color: var(--text-muted);">${item.styleName || item.style} · 점수 ${item.score}점</span>
              </div>
              <span class="history-meta">${timeAgo}</span>
            </div>
          `;
        }).join('');
        
        historySection.innerHTML = `
          <div class="history-title">📜 이전 분석 내역 <span style="font-size: 0.75rem; color: var(--text-muted);">(${state.history.length}개)</span></div>
          <div class="history-list">${historyItems}</div>
        `;
      }
    }
    
    // 시간 경과 표시
    function getTimeAgo(dateStr) {
      const date = new Date(dateStr);
      const now = new Date();
      const diff = Math.floor((now - date) / 1000);
      
      if (diff < 60) return '방금 전';
      if (diff < 3600) return `${Math.floor(diff / 60)}분 전`;
      if (diff < 86400) return `${Math.floor(diff / 3600)}시간 전`;
      if (diff < 604800) return `${Math.floor(diff / 86400)}일 전`;
      return date.toLocaleDateString('ko-KR');
    }
    
    // ===== 저장하기 기능 =====
    function saveCurrentResult(index = 0) {
      const result = state.results[index];
      if (!result) {
        showToast('저장할 결과가 없습니다.', 'error');
        return;
      }
      
      const saved = StorageManager.addSaved({
        numbers: result.numbers,
        style: result.style,
        styleName: result.info.name,
        score: result.score,
        round: LottoDataLoader.data.currentRound
      });
      
      if (saved) {
        showToast('번호가 저장되었습니다! 💾', 'success');
      }
    }
    
    // ===== 토스트 메시지 =====
    function showToast(message, type = 'info') {
      // 기존 토스트 제거
      const existingToast = document.querySelector('.toast-message');
      if (existingToast) existingToast.remove();
      
      const toast = document.createElement('div');
      toast.className = 'toast-message';
      toast.innerHTML = message;
      toast.style.cssText = `
        position: fixed;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        padding: 14px 24px;
        background: ${type === 'success' ? 'rgba(0, 224, 164, 0.95)' : type === 'error' ? 'rgba(239, 68, 68, 0.95)' : 'rgba(59, 130, 246, 0.95)'};
        color: white;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        z-index: 10000;
        animation: toastIn 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      `;
      
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
      }, 2500);
    }
    
    // 토스트 애니메이션 스타일 추가
    if (!document.getElementById('toast-styles')) {
      const style = document.createElement('style');
      style.id = 'toast-styles';
      style.textContent = `
        @keyframes toastIn {
          from { opacity: 0; transform: translateX(-50%) translateY(20px); }
          to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        @keyframes toastOut {
          from { opacity: 1; transform: translateX(-50%) translateY(0); }
          to { opacity: 0; transform: translateX(-50%) translateY(-20px); }
        }
      `;
      document.head.appendChild(style);
    }
    
    // ===== 충전 모달 =====
    let selectedPayment = null;
    
    function showChargeModal() {
      const modal = document.createElement('div');
      modal.id = 'chargeModal';
      modal.innerHTML = `
        <div class="charge-modal-backdrop" onclick="closeChargeModal()"></div>
        <div class="charge-modal-content">
          <div class="charge-modal-header">
            <h3>🔋 크레딧 충전</h3>
            <button class="charge-modal-close" onclick="closeChargeModal()">×</button>
          </div>
          <div class="charge-modal-body">
            <div class="charge-current">
              <div class="charge-current-label">현재 보유 크레딧</div>
              <div class="charge-current-value">${state.freeCredit + state.paidCredit}<span>회</span></div>
            </div>
            
            <div class="charge-section-title">충전 패키지 선택</div>
            <div class="charge-options">
              <div class="charge-option" data-amount="5" data-price="1000" onclick="selectChargeOption(this)">
                <div class="charge-option-left">
                  <div class="charge-amount">5회</div>
                  <div class="charge-per">회당 200원</div>
                </div>
                <div class="charge-option-right">
                  <div class="charge-price">₩1,000</div>
                </div>
              </div>
              
              <div class="charge-option popular selected" data-amount="15" data-price="2500" data-bonus="5" onclick="selectChargeOption(this)">
                <div class="charge-badge">🔥 인기</div>
                <div class="charge-option-left">
                  <div class="charge-amount">15회 <span class="charge-bonus-inline">+5회</span></div>
                  <div class="charge-per">회당 125원 <span class="charge-discount">38% 할인</span></div>
                </div>
                <div class="charge-option-right">
                  <div class="charge-price">₩2,500</div>
                  <div class="charge-original">₩4,000</div>
                </div>
              </div>
              
              <div class="charge-option best" data-amount="35" data-price="5000" data-bonus="15" onclick="selectChargeOption(this)">
                <div class="charge-badge gold">💎 베스트</div>
                <div class="charge-option-left">
                  <div class="charge-amount">35회 <span class="charge-bonus-inline">+15회</span></div>
                  <div class="charge-per">회당 100원 <span class="charge-discount">50% 할인</span></div>
                </div>
                <div class="charge-option-right">
                  <div class="charge-price">₩5,000</div>
                  <div class="charge-original">₩10,000</div>
                </div>
              </div>
            </div>
            
            <div class="charge-section-title">결제 수단</div>
            <div class="payment-methods">
              <div class="payment-method selected" data-method="kakao" onclick="selectPaymentMethod(this)">
                <span class="payment-icon">💬</span>
                <span>카카오페이</span>
              </div>
              <div class="payment-method" data-method="toss" onclick="selectPaymentMethod(this)">
                <span class="payment-icon">💙</span>
                <span>토스페이</span>
              </div>
              <div class="payment-method" data-method="card" onclick="selectPaymentMethod(this)">
                <span class="payment-icon">💳</span>
                <span>카드결제</span>
              </div>
            </div>
            
            <button class="charge-submit-btn" onclick="processPayment()">
              <span id="chargeSubmitText">₩2,500 결제하기</span>
            </button>
            
            <div class="charge-notice">
              <p>✓ 결제 완료 후 즉시 크레딧이 충전됩니다</p>
              <p>✓ 미사용 크레딧은 7일 이내 환불 가능</p>
              <p>✓ 문의: support@lottoinsight.ai</p>
            </div>
          </div>
        </div>
      `;
      
      modal.style.cssText = `
        position: fixed;
        inset: 0;
        z-index: 10001;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease;
      `;
      
      document.body.appendChild(modal);
      document.body.style.overflow = 'hidden';
      
      // 기본 선택 설정
      selectedPayment = { amount: 15, price: 2500, bonus: 5, method: 'kakao' };
      
      // 모달 스타일 추가
      if (!document.getElementById('charge-modal-styles')) {
        const style = document.createElement('style');
        style.id = 'charge-modal-styles';
        style.textContent = `
          .charge-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
          }
          .charge-modal-content {
            position: relative;
            width: 90%;
            max-width: 420px;
            max-height: 90vh;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            overflow-y: auto;
            animation: scaleIn 0.3s ease;
          }
          @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
          }
          .charge-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: sticky;
            top: 0;
            background: var(--primary);
            z-index: 1;
          }
          .charge-modal-header h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
          }
          .charge-modal-close {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
          }
          .charge-modal-close:hover {
            background: rgba(255,255,255,0.15);
            color: var(--text-primary);
          }
          .charge-modal-body {
            padding: 24px;
          }
          .charge-current {
            text-align: center;
            padding: 20px;
            background: rgba(0,224,164,0.08);
            border: 1px solid rgba(0,224,164,0.2);
            border-radius: 16px;
            margin-bottom: 24px;
          }
          .charge-current-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 4px;
          }
          .charge-current-value {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--accent-cyan);
          }
          .charge-current-value span {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-left: 4px;
          }
          .charge-section-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }
          .charge-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 24px;
          }
          .charge-option {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: rgba(255,255,255,0.02);
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
          }
          .charge-option:hover {
            border-color: rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.04);
          }
          .charge-option.selected {
            border-color: var(--accent-cyan);
            background: rgba(0,224,164,0.08);
          }
          .charge-option.selected::after {
            content: '✓';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background: var(--accent-cyan);
            color: var(--primary-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
          }
          .charge-option-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
          }
          .charge-option-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
            margin-right: 36px;
          }
          .charge-badge {
            position: absolute;
            top: -10px;
            left: 16px;
            padding: 4px 12px;
            background: var(--gradient-cyan);
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--primary-dark);
          }
          .charge-badge.gold {
            background: linear-gradient(135deg, #FFD75F 0%, #FF9F43 100%);
          }
          .charge-amount {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
          }
          .charge-bonus-inline {
            font-size: 0.85rem;
            color: var(--accent-gold);
            font-weight: 600;
          }
          .charge-per {
            font-size: 0.75rem;
            color: var(--text-muted);
          }
          .charge-discount {
            color: var(--accent-cyan);
            font-weight: 600;
          }
          .charge-price {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
          }
          .charge-original {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-decoration: line-through;
          }
          .payment-methods {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
          }
          .payment-method {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 8px;
            background: rgba(255,255,255,0.03);
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.75rem;
            color: var(--text-secondary);
          }
          .payment-method:hover {
            border-color: rgba(255,255,255,0.15);
          }
          .payment-method.selected {
            border-color: var(--accent-cyan);
            background: rgba(0,224,164,0.08);
            color: var(--text-primary);
          }
          .payment-icon {
            font-size: 1.5rem;
          }
          .charge-submit-btn {
            width: 100%;
            padding: 18px;
            background: var(--gradient-cyan);
            border: none;
            border-radius: 14px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-dark);
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 30px rgba(0,224,164,0.3);
          }
          .charge-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,224,164,0.4);
          }
          .charge-submit-btn:active {
            transform: translateY(0);
          }
          .charge-notice {
            margin-top: 16px;
            padding: 16px;
            background: rgba(255,255,255,0.02);
            border-radius: 12px;
          }
          .charge-notice p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 6px;
          }
          .charge-notice p:last-child {
            margin-bottom: 0;
          }
        `;
        document.head.appendChild(style);
      }
    }
    
    function selectChargeOption(el) {
      // 이전 선택 해제
      document.querySelectorAll('.charge-option').forEach(opt => opt.classList.remove('selected'));
      // 새 선택
      el.classList.add('selected');
      
      const amount = parseInt(el.dataset.amount);
      const price = parseInt(el.dataset.price);
      const bonus = parseInt(el.dataset.bonus || 0);
      
      selectedPayment = { ...selectedPayment, amount, price, bonus };
      
      // 버튼 텍스트 업데이트
      document.getElementById('chargeSubmitText').textContent = `₩${price.toLocaleString()} 결제하기`;
    }
    
    function selectPaymentMethod(el) {
      document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
      el.classList.add('selected');
      selectedPayment.method = el.dataset.method;
    }
    
    function processPayment() {
      if (!selectedPayment) {
        showToast('충전 패키지를 선택해주세요.', 'error');
        return;
      }
      
      const { amount, price, bonus, method } = selectedPayment;
      const totalAmount = amount + (bonus || 0);
      
      // 결제 처리 시뮬레이션
      const btn = document.querySelector('.charge-submit-btn');
      btn.innerHTML = `
        <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
          <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
        </svg>
        결제 처리 중...
      `;
      btn.disabled = true;
      
      // 2초 후 결과 표시 (실제로는 결제 API 연동)
      setTimeout(() => {
        // 데모: 크레딧 추가
        state.paidCredit += totalAmount;
        StorageManager.setCredits(state.freeCredit, state.paidCredit);
        updateNavbar();
        
        closeChargeModal();
        
        showToast(`🎉 ${totalAmount}회 크레딧이 충전되었습니다!`, 'success');
        
        // 충전 성공 모달
        setTimeout(() => {
          showSuccessModal(totalAmount, price);
        }, 300);
      }, 2000);
    }
    
    function showSuccessModal(amount, price) {
      const successModal = document.createElement('div');
      successModal.id = 'successModal';
      successModal.innerHTML = `
        <div class="success-backdrop" onclick="this.parentElement.remove(); document.body.style.overflow=''"></div>
        <div class="success-content">
          <div class="success-icon">🎉</div>
          <h3>충전 완료!</h3>
          <p class="success-amount">${amount}회</p>
          <p class="success-price">₩${price.toLocaleString()}</p>
          <p class="success-total">총 보유: <strong>${state.freeCredit + state.paidCredit}회</strong></p>
          <button onclick="this.closest('#successModal').remove(); document.body.style.overflow=''">
            확인
          </button>
        </div>
      `;
      successModal.style.cssText = `
        position: fixed;
        inset: 0;
        z-index: 10002;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease;
      `;
      
      // 성공 모달 스타일
      const style = document.createElement('style');
      style.textContent = `
        .success-backdrop {
          position: absolute;
          inset: 0;
          background: rgba(0,0,0,0.8);
        }
        .success-content {
          position: relative;
          padding: 40px;
          background: var(--primary);
          border: 1px solid rgba(0,224,164,0.3);
          border-radius: 24px;
          text-align: center;
          animation: scaleIn 0.3s ease;
        }
        .success-icon {
          font-size: 4rem;
          margin-bottom: 16px;
        }
        .success-content h3 {
          font-family: 'Outfit', sans-serif;
          font-size: 1.5rem;
          margin-bottom: 20px;
          color: var(--accent-cyan);
        }
        .success-amount {
          font-family: 'Outfit', sans-serif;
          font-size: 3rem;
          font-weight: 800;
          color: var(--text-primary);
        }
        .success-price {
          font-size: 1rem;
          color: var(--text-muted);
          margin-bottom: 16px;
        }
        .success-total {
          font-size: 0.9rem;
          color: var(--text-secondary);
          margin-bottom: 24px;
        }
        .success-total strong {
          color: var(--accent-gold);
        }
        .success-content button {
          padding: 14px 48px;
          background: var(--gradient-cyan);
          border: none;
          border-radius: 12px;
          font-weight: 700;
          color: var(--primary-dark);
          cursor: pointer;
        }
      `;
      document.head.appendChild(style);
      document.body.appendChild(successModal);
    }
    
    function closeChargeModal() {
      const modal = document.getElementById('chargeModal');
      if (modal) {
        modal.style.animation = 'fadeOut 0.2s ease forwards';
        setTimeout(() => {
          modal.remove();
          document.body.style.overflow = '';
        }, 200);
      }
    }
    
    function selectCharge(amount, price) {
      // Legacy function - redirect to new modal
      showChargeModal();
    }
    
    // ===== 이벤트 리스너 설정 =====
    function setupEventListeners() {
      // 충전 버튼
      const chargeBtn = document.querySelector('.charge-btn');
      if (chargeBtn) {
        chargeBtn.addEventListener('click', showChargeModal);
      }
      
      // 저장하기 버튼
      const saveBtn = document.querySelector('.action-btn-secondary');
      if (saveBtn && saveBtn.textContent.includes('저장')) {
        saveBtn.addEventListener('click', () => {
          const activeCard = document.querySelector('.result-card.active');
          const index = activeCard ? parseInt(activeCard.dataset.index) : 0;
          saveCurrentResult(index);
        });
      }
    }

    // ===== 초기화 실행 =====
    document.addEventListener('DOMContentLoaded', () => {
      renderLatestResult();
      renderAiComparison();
      updateNavbar();
      updateWelcomeMessage();
      updateAnalyzeCost();
      updateHistoryDisplay();
      setupEventListeners();
      
      // 충전 버튼 이벤트
      const chargeBtn = document.getElementById('chargeBtn');
      if (chargeBtn) {
        chargeBtn.addEventListener('click', showChargeModal);
      }
    });
  </script>
</body>
</html>


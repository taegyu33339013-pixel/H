<?php
	include_once('./common.php'); // 경로는 result.php 위치에 맞게 조정
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  
  <!-- Primary Meta Tags -->
  <title>AI 분석 | 로또인사이트 - 이번 주 분석 번호</title>
  <meta name="title" content="AI 분석 | 로또인사이트 - 이번 주 분석 번호">
  <meta name="description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 1,180회차 데이터 기반 패턴 분석 리포트와 균형 점수를 제공합니다.">
  <meta name="robots" content="noindex, nofollow">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#0B132B">

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
    :root {
      --ball-yellow: linear-gradient(145deg, #ffd700, #f0b429);
      --ball-blue: linear-gradient(145deg, #3b82f6, #2563eb);
      --ball-red: linear-gradient(145deg, #ef4444, #dc2626);
      --ball-purple: linear-gradient(145deg, #a855f7, #9333ea);
      --ball-green: linear-gradient(145deg, #22c55e, #16a34a);
    }

    html, body {
      overflow-x: hidden;
    }
    
    body {
      background: var(--primary-dark);
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
      height: 64px;
      background: rgba(11, 19, 43, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
    }

    .app-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--text-primary);
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .app-logo-icon {
      width: 32px;
      height: 32px;
      background: var(--gradient-cyan);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .app-logo-icon svg {
      stroke: var(--primary-dark);
    }

    .navbar-right {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .credit-badge {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 30px;
      font-size: 0.8rem;
      color: var(--text-secondary);
    }

    .credit-count {
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .charge-btn {
      padding: 8px 16px;
      background: var(--gradient-gold);
      border: none;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--primary-dark);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* ===== 메인 컨테이너 ===== */
    .app-container {
      max-width: 560px;
      margin: 0 auto;
      padding: 84px 20px 120px;
    }

    /* ===== Step 1: 대시보드 상태 ===== */
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

    /* 크레딧 카드 */
    .credit-card {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 20px;
      padding: 24px;
      margin-bottom: 24px;
    }

    .credit-status {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 20px;
    }

    .credit-item {
      text-align: center;
      padding: 16px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 14px;
    }

    .credit-item-label {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-bottom: 6px;
    }

    .credit-item-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
    }

    .credit-item-value.free {
      color: var(--accent-cyan);
    }

    .credit-item-value.paid {
      color: var(--accent-gold);
    }

    .credit-cta {
      width: 100%;
      padding: 14px;
      background: rgba(255, 215, 95, 0.1);
      border: 1px solid rgba(255, 215, 95, 0.3);
      border-radius: 12px;
      color: var(--accent-gold);
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .credit-cta:hover {
      background: rgba(255, 215, 95, 0.15);
    }

    /* 분석 스타일 선택 (복수 선택) */
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

    /* ===== Step 2: 분석 결과 상태 ===== */
    .result-view {
      display: none;
    }

    .result-view.visible {
      display: block;
    }

    /* 결과 인트로 */
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

    /* 메인 결과 카드 */
    .result-card {
      background: rgba(13, 24, 41, 0.95);
      border: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 24px;
      padding: 28px 24px;
      margin-bottom: 20px;
    }

    .result-card-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      background: rgba(255, 215, 95, 0.15);
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--accent-gold);
      margin-bottom: 20px;
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
    .ball-purple { background: var(--ball-purple); box-shadow: 0 6px 20px rgba(168, 85, 247, 0.4); }
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

    /* 결과 네비게이션 (탭) */
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

    .result-nav-btn .nav-icon {
      font-size: 1rem;
    }

    /* 결과 카드 컨테이너 */
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
      display: flex;
      align-items: center;
      gap: 6px;
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
      --target-width: 87%;
    }

    @keyframes fillBar {
      to { width: var(--target-width, 87%); }
    }

    /* 리포트 요약 태그 */
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
      margin-bottom: 12px;
      line-height: 1.5;
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

    /* 다시 분석하기 */
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

    /* 실제 데이터 표시 영역 */
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

    .mini-ball {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.6rem;
      font-weight: 700;
      color: #fff;
    }

    .mini-ball.yellow { background: linear-gradient(145deg, #ffd700, #f0b429); }
    .mini-ball.blue { background: linear-gradient(145deg, #3b82f6, #2563eb); }
    .mini-ball.red { background: linear-gradient(145deg, #ef4444, #dc2626); }
    .mini-ball.purple { background: linear-gradient(145deg, #a855f7, #9333ea); }
    .mini-ball.green { background: linear-gradient(145deg, #22c55e, #16a34a); }

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
      .app-navbar {
        padding: 0 16px;
        height: 56px;
      }

      .app-container {
        padding: 76px 16px 100px;
      }

      .style-buttons-grid {
        grid-template-columns: 1fr;
      }

      .style-buttons-grid .style-btn:last-child {
        grid-column: span 1;
      }

      .style-btn {
        padding: 12px 14px;
      }

      .style-icon {
        font-size: 1.3rem;
      }
      
      .style-check {
        width: 20px;
        height: 20px;
      }

      .balls-container {
        gap: 8px;
        padding: 16px;
      }

      .ball-3d {
        width: 44px;
        height: 44px;
        font-size: 1rem;
      }

      .story-grid {
        grid-template-columns: 1fr;
      }

      .result-actions {
        grid-template-columns: 1fr;
      }

      .disclaimer ul {
        grid-template-columns: 1fr;
      }
    }
    
    /* ===== 초소형 화면 (iPhone SE, Galaxy Fold) ===== */
    @media (max-width: 375px) {
      .app-container {
        padding: 66px 12px 90px;
      }
      
      .dashboard-title {
        font-size: 1.3rem;
      }
      
      .ball-3d {
        width: 38px;
        height: 38px;
        font-size: 0.9rem;
      }
      
      .balls-container {
        gap: 6px;
        padding: 12px;
      }
      
      .style-btn {
        padding: 12px 14px;
      }
      
      .analyze-btn {
        padding: 16px;
        font-size: 1rem;
      }
    }
    
    /* ===== 극소형 화면 (320px 이하) ===== */
    @media (max-width: 320px) {
      .app-container {
        padding: 60px 8px 85px;
      }
      
      .ball-3d {
        width: 34px;
        height: 34px;
        font-size: 0.85rem;
      }
      
      .dashboard-title {
        font-size: 1.15rem;
      }
      
      .user-name {
        font-size: 0.9rem;
      }
    }
    
    /* ===== 가로 모드 ===== */
    @media (orientation: landscape) and (max-height: 500px) {
      .app-container {
        padding: 66px 16px 60px;
      }
      
      .style-buttons {
        grid-template-columns: repeat(3, 1fr);
      }
    }
  </style>
</head>
<body>
  <!-- 상단 네비게이션 -->
  <nav class="app-navbar">
    <a href="<?php echo G5_URL;?>" class="app-logo">
      <div class="app-logo-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <circle cx="9" cy="9" r="5" fill="#FFD75F"/>
          <circle cx="13" cy="12" r="4" stroke="currentColor" stroke-width="2" fill="none"/>
          <line x1="16" y1="15" x2="20" y2="19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
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
      <button class="charge-btn">+ 충전</button>
    </div>
  </nav>

  <!-- 메인 컨테이너 -->
  <div class="app-container">
    
    <!-- ===== Step 1: 대시보드 ===== -->
    <div class="dashboard-view" id="dashboardView">
      
      <!-- 환영 메시지 -->
      <div class="welcome-section">
        <div class="user-avatar" id="userAvatar">김</div>
        <div class="welcome-text">
          <h1 id="welcomeTitle">👋 김** 님, 환영합니다!</h1>
          <p id="welcomeSubtitle">이번 주 행운의 번호를 분석해보세요</p>
        </div>
      </div>

      <!-- 크레딧 현황 -->
      <div class="credit-card">
        <div class="credit-status">
          <div class="credit-item">
            <div class="credit-item-label">무료 분석</div>
            <div class="credit-item-value free" id="freeCredit">1회</div>
          </div>
          <div class="credit-item">
            <div class="credit-item-label">유료 크레딧</div>
            <div class="credit-item-value paid" id="paidCredit">0회</div>
          </div>
        </div>
        <button class="credit-cta">💰 크레딧 충전하기 (1회 200원)</button>
      </div>

      <!-- 분석 스타일 선택 (복수 선택 가능) -->
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
            <span class="style-icon">📊</span>
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

    <!-- ===== Step 2: 분석 결과 ===== -->
    <div class="result-view" id="resultView">
      
      <!-- 결과 인트로 -->
      <div class="result-intro">
        <div class="result-badge">
          ✨ AI 분석 완료
        </div>
        <h2>당신만의 맞춤 조합입니다</h2>
        <p id="resultSubtitle">5개 스타일 · 최근 100회 데이터 기반</p>
      </div>

      <!-- 결과 네비게이션 -->
      <div class="result-nav" id="resultNav">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 결과 카드 컨테이너 (슬라이더) -->
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
          <li>모든 조합 확률 동일</li>
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

  <!-- 로딩 모달 (실제 데이터 표시) -->
  <div class="loading-modal" id="loadingModal">
    <div class="loading-content">
      <div class="loading-spinner"></div>
      <div class="loading-text" id="loadingText">AI가 패턴을 분석 중...</div>
      <div class="loading-progress">
        <div class="loading-bar" id="loadingBar"></div>
      </div>
      
      <!-- 실제 데이터 표시 영역 -->
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
  <script src="/scripts/lotto-generator.js"></script>
  
  <script>
    // 어드민 계정 설정
    const ADMIN_ACCOUNT = {
      id: 'admin',
      pw: '1234'
    };
    
    // 로그인 체크 및 상태 초기화
    function checkLoginStatus() {
      const loggedUser = localStorage.getItem('lottoinsight_user');
      
      if (loggedUser === ADMIN_ACCOUNT.id) {
        // 어드민 계정 - 무제한 크레딧
        return {
          isAdmin: true,
          freeCredit: 999999,
          paidCredit: 0,
          userName: '관리자'
        };
      }
      
      // 일반 사용자
      return {
        isAdmin: false,
        freeCredit: parseInt(localStorage.getItem('lottoinsight_free') || '1'),
        paidCredit: parseInt(localStorage.getItem('lottoinsight_paid') || '0'),
        userName: loggedUser || '게스트'
      };
    }
    
    const loginStatus = checkLoginStatus();
    
    // 상태 관리
    let state = {
      freeCredit: loginStatus.freeCredit,
      paidCredit: loginStatus.paidCredit,
      isAdmin: loginStatus.isAdmin,
      userName: loginStatus.userName,
      selectedStyles: ['hotcold', 'balance'], // 복수 선택
      history: [],
      currentResult: null
    };

    // DOM 요소
    const dashboardView = document.getElementById('dashboardView');
    const resultView = document.getElementById('resultView');
    const loadingModal = document.getElementById('loadingModal');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const reanalyzeBtn = document.getElementById('reanalyzeBtn');
    const backBtn = document.getElementById('backBtn');
    const styleButtons = document.querySelectorAll('.style-btn');
    const selectedCountEl = document.getElementById('selectedCount');

    // 스타일 선택 (복수 선택 가능)
    styleButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const style = btn.dataset.style;
        
        if (btn.classList.contains('active')) {
          // 최소 1개는 선택되어 있어야 함
          if (state.selectedStyles.length > 1) {
            btn.classList.remove('active');
            state.selectedStyles = state.selectedStyles.filter(s => s !== style);
          }
        } else {
          btn.classList.add('active');
          state.selectedStyles.push(style);
        }
        
        // 선택 개수 업데이트
        if (selectedCountEl) {
          selectedCountEl.textContent = state.selectedStyles.length;
        }
        
        // 분석 비용 업데이트
        updateAnalyzeCost();
        
        console.log('선택된 스타일:', state.selectedStyles);
      });
    });
    
    // 초기 비용 표시 (함수 정의 후 실행)
    setTimeout(() => updateAnalyzeCost(), 100);
    
    // 환영 메시지 업데이트
    function updateWelcomeMessage() {
      const avatar = document.getElementById('userAvatar');
      const title = document.getElementById('welcomeTitle');
      const subtitle = document.getElementById('welcomeSubtitle');
      
      if (state.isAdmin) {
        avatar.textContent = '👑';
        avatar.style.background = 'linear-gradient(135deg, #FFD700, #FFA500)';
        title.innerHTML = '🔐 관리자님, 환영합니다!';
        subtitle.innerHTML = '<span style="color: var(--accent-gold);">무제한 크레딧</span> 활성화됨';
      } else {
        const firstChar = state.userName.charAt(0).toUpperCase();
        avatar.textContent = firstChar;
        title.innerHTML = `👋 ${state.userName}님, 환영합니다!`;
      }
    }
    
    updateWelcomeMessage();

    // 분석 시작
    analyzeBtn.addEventListener('click', startAnalysis);
    reanalyzeBtn.addEventListener('click', startAnalysis);

    async function startAnalysis() {
      const requiredCredits = state.selectedStyles.length;
      const totalCredits = state.freeCredit + state.paidCredit;
      
      // 크레딧 확인
      if (totalCredits < requiredCredits) {
        alert(`크레딧이 부족합니다!\n필요: ${requiredCredits}크레딧\n보유: ${totalCredits}크레딧\n\n충전 후 이용해주세요.`);
        return;
      }

      // 엔진 초기화 확인
      if (!lottoGenerator.ready) {
        console.log('🔄 엔진 초기화 중...');
        await lottoGenerator.init();
      }

      // 로딩 시작
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

        // 30% 진행 시 실제 데이터 표시
        if (progress > 30 && !dataShown && lottoGenerator.dataLoader?.data) {
          dataShown = true;
          showRealData(dataStats, recentNumbers, dataUpdate);
        }

        if (progress >= 100) {
          clearInterval(interval);
          setTimeout(() => {
            completeAnalysis();
          }, 500);
        }
      }, 300);
    }

    // 실제 데이터 표시 함수
    function showRealData(dataStats, recentNumbers, dataUpdate) {
      const data = lottoGenerator.dataLoader.data;
      if (!data) return;

      // 업데이트 날짜
      dataUpdate.textContent = `업데이트: ${data.lastUpdate}`;

      // 통계 표시
      const history = data.history;
      const allNumbers = history.flatMap(h => h.numbers);
      
      // 가장 많이 나온 번호
      const freq = {};
      allNumbers.forEach(n => freq[n] = (freq[n] || 0) + 1);
      const hotNum = Object.entries(freq).sort((a, b) => b[1] - a[1])[0];
      
      // 가장 안 나온 번호 (최근 기준)
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

      // 최근 당첨 번호 3개
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
              ${h.numbers.map(n => `<span class="mini-ball ${getBallColor(n)}">${n}</span>`).join('')}
            </div>
          </div>
        `).join('')}
      `;
    }

    // 스타일 정보
    const styleInfo = {
      'hotcold': { icon: '🔥', name: 'Hot/Cold', genStyle: 'hot' },
      'balance': { icon: '⚖️', name: '홀짝/고저', genStyle: 'balanced' },
      'color': { icon: '📊', name: '색상볼', genStyle: 'balanced' },
      'correlation': { icon: '🧠', name: '상관관계', genStyle: 'cold' },
      'ac': { icon: '🧮', name: 'AC값', genStyle: 'balanced' }
    };

    async function completeAnalysis() {
      // 크레딧 차감 (스타일 수만큼)
      const stylesToAnalyze = state.selectedStyles.length;
      
      for (let i = 0; i < stylesToAnalyze; i++) {
        if (state.freeCredit > 0) {
          state.freeCredit--;
        } else if (state.paidCredit > 0) {
          state.paidCredit--;
        }
      }
      updateCreditDisplay();

      // 선택된 스타일들에 대해 각각 결과 생성
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
        
        state.results.push({
          style: style,
          info: info,
          ...result
        });
        
        // 히스토리에 추가
        state.history.unshift({
          numbers: result.numbers,
          date: new Date(),
          style: style,
          score: result.score
        });
      }

      console.log(`🎯 ${state.selectedStyles.length}개 스타일 분석 완료:`, state.results);

      // 결과 화면 렌더링
      renderAllResults();

      // 로딩 숨기고 결과 표시
      loadingModal.classList.remove('active');
      dashboardView.classList.add('hidden');
      resultView.classList.add('visible');
    }

    // 전체 결과 렌더링
    function renderAllResults() {
      const resultNav = document.getElementById('resultNav');
      const resultCardsContainer = document.getElementById('resultCardsContainer');
      const resultIndicators = document.getElementById('resultIndicators');
      const resultSubtitle = document.getElementById('resultSubtitle');
      
      // 서브타이틀 업데이트
      if (resultSubtitle) {
        resultSubtitle.textContent = `${state.results.length}개 스타일 · 최근 100회 데이터 기반`;
      }
      
      // 네비게이션 탭 생성
      resultNav.innerHTML = state.results.map((r, idx) => `
        <button class="result-nav-btn ${idx === 0 ? 'active' : ''}" data-index="${idx}">
          <span class="nav-icon">${r.info.icon}</span>
          <span>${r.info.name}</span>
        </button>
      `).join('');
      
      // 결과 카드 생성
      resultCardsContainer.innerHTML = state.results.map((r, idx) => `
        <div class="result-card ${idx === 0 ? 'active' : ''}" data-index="${idx}">

      // 결과 카드 생성 (+ 통계 리포트 요약 반영)
      resultCardsContainer.innerHTML = state.results.map((r, idx) => {
        // lotto-generator.js의 고급 리포트 생성기 사용
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

          <!-- 통계 요약 태그 -->
          <div class="report-summary">
            ${report.summary.map(s => `<span class="summary-tag">${s}</span>`).join('')}
          </div>
          <!-- 인사이트 문장들 (한두 줄 설명) -->
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
      `).join('');
      
      // 인디케이터 생성
      resultIndicators.innerHTML = state.results.map((_, idx) => `
        <div class="result-indicator ${idx === 0 ? 'active' : ''}" data-index="${idx}"></div>
      `).join('');
      
      // 탭/인디케이터 클릭 이벤트
      setupResultNavigation();
    }

    // 결과 네비게이션 설정
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
      document.getElementById('freeCredit').textContent = state.freeCredit + '회';
      document.getElementById('paidCredit').textContent = state.paidCredit + '회';
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

    // 대시보드로 돌아가기
    backBtn.addEventListener('click', () => {
      resultView.classList.remove('visible');
      dashboardView.classList.remove('hidden');
      updateHistoryDisplay();
    });

    function updateHistoryDisplay() {
      const historySection = document.querySelector('.history-section');
      if (state.history.length === 0) {
        historySection.innerHTML = `
          <div class="history-title">📜 이전 분석 내역</div>
          <div class="history-empty">
            아직 분석 내역이 없습니다.<br>
            첫 번째 분석을 시작해보세요! 🎯
          </div>
        `;
      } else {
        const historyItems = state.history.slice(0, 5).map(item => `
          <div class="history-item">
            <span class="history-numbers">${item.numbers.join(' · ')}</span>
            <span class="history-meta">방금 전</span>
          </div>
        `).join('');
        
        historySection.innerHTML = `
          <div class="history-title">📜 이전 분석 내역</div>
          <div class="history-list">${historyItems}</div>
        `;
      }
    }
  </script>
</body>
</html>

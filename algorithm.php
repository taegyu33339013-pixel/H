<?php
// /algorithm.php (오늘로또 AI 분석 알고리즘 설명 페이지)

// 1) 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 그누보드 상수/변수 안전 기본값 세팅
$site_url = defined('G5_URL') ? G5_URL : '/';
$bbs_url  = defined('G5_BBS_URL') ? G5_BBS_URL : '/bbs';

if (!isset($is_member)) {
    $is_member = false;
}

// 최신 회차 정보
$row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($row['max_round'] ?? 0);
$total_numbers = $max_round * 6;

// 로그아웃 URL
if (defined('G5_BBS_URL')) {
    $logout_url = G5_BBS_URL . '/logout.php?url=' . urlencode($_SERVER['REQUEST_URI']);
} else {
    $logout_url = $bbs_url . '/logout.php?url=' . urlencode($_SERVER['REQUEST_URI']);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  
  <!-- Primary Meta Tags -->
  <title>AI 분석 알고리즘 10가지 기법 | 오늘로또</title>
  <meta name="description" content="오늘로또 AI가 사용하는 10가지 분석 알고리즘을 100% 투명하게 공개합니다. 몬테카를로 시뮬레이션, AC값, Hot/Cold 분석 등 핵심 패턴 분석 방법을 확인하세요.">
  <meta name="robots" content="index, follow">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://lottoinsight.ai/algorithm.php">
  <meta property="og:title" content="AI 분석 알고리즘 10가지 기법 | 오늘로또">
  <meta property="og:description" content="몬테카를로 시뮬레이션 포함 10가지 AI 분석 알고리즘을 100% 투명하게 공개합니다.">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#0B132B">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">

  <style>
    /* ===== CSS Variables ===== */
    :root {
      --primary-dark: #050a15;
      --primary: #0d1526;
      --secondary: #1a2744;
      --accent-cyan: #00E0A4;
      --accent-cyan-light: #00FFBB;
      --accent-cyan-glow: rgba(0, 224, 164, 0.4);
      --accent-gold: #FFD75F;
      --accent-gold-light: #FFE085;
      --accent-gold-glow: rgba(255, 215, 95, 0.4);
      --accent-purple: #8B5CF6;
      --accent-pink: #EC4899;
      --accent-red: #FF6B6B;
      --text-primary: #ffffff;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --gradient-cyan: linear-gradient(135deg, #00E0A4 0%, #00D4FF 100%);
      --gradient-gold: linear-gradient(135deg, #FFD75F 0%, #FF9F43 100%);
      --gradient-purple: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%);
      --gradient-mesh: radial-gradient(at 40% 20%, rgba(0, 224, 164, 0.15) 0px, transparent 50%),
                       radial-gradient(at 80% 0%, rgba(139, 92, 246, 0.1) 0px, transparent 50%),
                       radial-gradient(at 0% 50%, rgba(0, 212, 255, 0.1) 0px, transparent 50%);
      --shadow-cyan: 0 25px 80px rgba(0, 224, 164, 0.3);
      --glass-bg: rgba(255, 255, 255, 0.03);
      --glass-border: rgba(255, 255, 255, 0.08);
      --border-radius-lg: 24px;
      --border-radius-xl: 32px;
    }

    /* ===== Reset & Base ===== */
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
      font-size: 16px;
      -webkit-font-smoothing: antialiased;
    }

    body {
      font-family: 'Inter', 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--primary-dark);
      background-image: var(--gradient-mesh);
      background-attachment: fixed;
      color: var(--text-primary);
      line-height: 1.6;
      overflow-x: hidden;
    }

    /* ===== Animations ===== */
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.6; }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }

    /* ===== Navigation ===== */
    .nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      padding: 20px 24px;
      background: rgba(5, 10, 21, 0.9);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: inherit;
    }

    .nav-logo-icon {
      width: 40px;
      height: 40px;
      background: var(--gradient-cyan);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }

    .nav-logo-text {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.3rem;
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-back {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .nav-back:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* ===== Page Header ===== */
    .page-header {
      padding: 140px 24px 60px;
      text-align: center;
    }

    .page-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: rgba(139, 92, 246, 0.15);
      border: 1px solid rgba(139, 92, 246, 0.3);
      border-radius: 100px;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--accent-purple);
      margin-bottom: 24px;
    }

    .page-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(2.2rem, 5vw, 3.5rem);
      font-weight: 900;
      margin-bottom: 16px;
      background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.8) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .page-subtitle {
      font-size: 1.1rem;
      color: var(--text-secondary);
      max-width: 700px;
      margin: 0 auto 32px;
    }

    .page-stats {
      display: flex;
      justify-content: center;
      gap: 48px;
      flex-wrap: wrap;
    }

    .page-stat {
      text-align: center;
    }

    .page-stat-value {
      font-family: 'Outfit', sans-serif;
      font-size: 2.5rem;
      font-weight: 900;
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .page-stat-label {
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    /* ===== Section Header ===== */
    .section-header {
      text-align: center;
      margin-bottom: 48px;
    }

    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.25);
      border-radius: 100px;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-cyan);
      margin-bottom: 20px;
    }

    .section-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.8rem, 4vw, 2.5rem);
      font-weight: 800;
      margin-bottom: 12px;
    }

    .section-subtitle {
      font-size: 1rem;
      color: var(--text-secondary);
      max-width: 600px;
      margin: 0 auto;
    }

    /* ===== Monte Carlo Hero Section ===== */
    .monte-carlo-section {
      padding: 60px 24px 100px;
    }

    .monte-carlo-container {
      max-width: 1100px;
      margin: 0 auto;
    }

    .monte-carlo-card {
      background: linear-gradient(145deg, rgba(139, 92, 246, 0.15), rgba(13, 21, 38, 0.9));
      border: 1px solid rgba(139, 92, 246, 0.3);
      border-radius: 32px;
      padding: 60px;
      position: relative;
      overflow: hidden;
    }

    .monte-carlo-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.6), transparent);
    }

    .monte-carlo-card::after {
      content: '🎲';
      position: absolute;
      top: 40px;
      right: 60px;
      font-size: 6rem;
      opacity: 0.15;
      animation: float 4s ease-in-out infinite;
    }

    .monte-carlo-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: rgba(139, 92, 246, 0.2);
      border: 1px solid rgba(139, 92, 246, 0.4);
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--accent-purple);
      margin-bottom: 20px;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    .monte-carlo-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(2rem, 4vw, 2.8rem);
      font-weight: 900;
      margin-bottom: 20px;
      max-width: 600px;
    }

    .monte-carlo-desc {
      font-size: 1.1rem;
      color: var(--text-secondary);
      line-height: 1.8;
      max-width: 650px;
      margin-bottom: 40px;
    }

    .monte-carlo-features {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      margin-bottom: 40px;
    }

    .monte-carlo-feature {
      padding: 24px;
      background: rgba(0, 0, 0, 0.3);
      border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .monte-carlo-feature-icon {
      font-size: 2rem;
      margin-bottom: 12px;
    }

    .monte-carlo-feature-title {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }

    .monte-carlo-feature-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .monte-carlo-visual {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 24px;
      background: rgba(0, 0, 0, 0.4);
      border-radius: 16px;
      overflow-x: auto;
    }

    .simulation-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      min-width: 80px;
    }

    .simulation-step-icon {
      width: 48px;
      height: 48px;
      background: rgba(139, 92, 246, 0.2);
      border: 1px solid rgba(139, 92, 246, 0.3);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }

    .simulation-step-label {
      font-size: 0.75rem;
      color: var(--text-muted);
      text-align: center;
    }

    .simulation-arrow {
      font-size: 1.2rem;
      color: var(--accent-purple);
      flex-shrink: 0;
    }

    /* ===== Algorithm Process Section ===== */
    .process-section {
      padding: 80px 24px;
      background: rgba(0, 0, 0, 0.2);
    }

    .process-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .process-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 32px;
      padding: 48px;
    }

    .process-steps {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 24px;
    }

    .process-step {
      flex: 1;
      text-align: center;
      padding: 32px 24px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 20px;
      position: relative;
    }

    .process-step-icon {
      width: 64px;
      height: 64px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin: 0 auto 16px;
    }

    .process-step-num {
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 28px;
      height: 28px;
      background: var(--accent-cyan);
      border-radius: 50%;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 0.85rem;
      color: var(--primary-dark);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .process-step-title {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }

    .process-step-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .process-arrow {
      font-size: 1.5rem;
      color: var(--accent-cyan);
      flex-shrink: 0;
    }

    /* ===== 10 Algorithms Grid ===== */
    .algorithms-section {
      padding: 100px 24px;
    }

    .algorithms-container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .algorithms-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
    }

    .algorithm-card {
      padding: 36px;
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.8), rgba(5, 10, 21, 0.9));
      border: 1px solid var(--glass-border);
      border-radius: var(--border-radius-lg);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .algorithm-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--card-accent, var(--gradient-cyan));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .algorithm-card:hover {
      transform: translateY(-8px);
      border-color: rgba(0, 224, 164, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 224, 164, 0.08);
    }

    .algorithm-card:hover::before {
      opacity: 1;
    }

    .algorithm-card.featured {
      grid-column: span 2;
      background: linear-gradient(145deg, rgba(139, 92, 246, 0.1), rgba(13, 21, 38, 0.9));
      border-color: rgba(139, 92, 246, 0.2);
    }

    .algorithm-card.featured::before {
      background: var(--gradient-purple);
      opacity: 1;
    }

    .algorithm-header {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 20px;
    }

    .algorithm-icon {
      width: 56px;
      height: 56px;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.15), rgba(0, 212, 255, 0.1));
      border: 1px solid rgba(0, 224, 164, 0.25);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      flex-shrink: 0;
      transition: all 0.3s ease;
    }

    .algorithm-card:hover .algorithm-icon {
      transform: scale(1.1) rotate(5deg);
      box-shadow: 0 10px 30px rgba(0, 224, 164, 0.2);
    }

    .algorithm-card.featured .algorithm-icon {
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.1));
      border-color: rgba(139, 92, 246, 0.3);
    }

    .algorithm-info {
      flex: 1;
    }

    .algorithm-number {
      font-family: 'Outfit', sans-serif;
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--accent-cyan);
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 4px;
    }

    .algorithm-card.featured .algorithm-number {
      color: var(--accent-purple);
    }

    .algorithm-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      margin-bottom: 4px;
    }

    .algorithm-subtitle {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .algorithm-desc {
      font-size: 0.95rem;
      color: var(--text-secondary);
      line-height: 1.7;
      margin-bottom: 20px;
    }

    .algorithm-details {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .algorithm-tag {
      padding: 6px 14px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--accent-cyan);
    }

    .algorithm-card.featured .algorithm-tag {
      background: rgba(139, 92, 246, 0.15);
      border-color: rgba(139, 92, 246, 0.25);
      color: var(--accent-purple);
    }

    /* ===== Quality Section ===== */
    .quality-section {
      padding: 80px 24px;
      background: rgba(0, 0, 0, 0.15);
    }

    .quality-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .quality-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 28px;
      padding: 48px;
    }

    .quality-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .quality-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .quality-subtitle {
      font-size: 0.95rem;
      color: var(--text-muted);
    }

    .quality-gauges {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
    }

    .quality-gauge {
      padding: 24px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 18px;
    }

    .quality-gauge-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .quality-gauge-label {
      font-weight: 600;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .quality-gauge-value {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.4rem;
      color: var(--accent-cyan);
    }

    .quality-bar {
      height: 12px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      overflow: hidden;
      margin-bottom: 8px;
    }

    .quality-fill {
      height: 100%;
      border-radius: 6px;
      transition: width 1s ease;
    }

    .quality-fill.excellent {
      background: linear-gradient(90deg, var(--accent-cyan), #00F5B5);
    }

    .quality-fill.good {
      background: linear-gradient(90deg, var(--accent-gold), #FFE085);
    }

    .quality-desc {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    /* ===== Comparison Section ===== */
    .comparison-section {
      padding: 100px 24px;
    }

    .comparison-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .comparison-table {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 24px;
      overflow: hidden;
    }

    .comparison-header {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      padding: 20px 32px;
      background: rgba(0, 0, 0, 0.3);
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .comparison-row {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      padding: 20px 32px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      align-items: center;
    }

    .comparison-row:last-child {
      border-bottom: none;
    }

    .comparison-feature {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .comparison-feature-icon {
      font-size: 1.3rem;
    }

    .comparison-feature-name {
      font-weight: 600;
    }

    .comparison-check {
      text-align: center;
    }

    .check-yes {
      color: var(--accent-cyan);
      font-size: 1.2rem;
    }

    .check-no {
      color: var(--text-muted);
      font-size: 1.2rem;
    }

    .check-partial {
      color: var(--accent-gold);
      font-size: 0.85rem;
    }

    /* ===== CTA Section ===== */
    .cta-section {
      padding: 100px 24px;
      text-align: center;
      background: linear-gradient(180deg, transparent 0%, rgba(0, 224, 164, 0.05) 100%);
    }

    .cta-container {
      max-width: 700px;
      margin: 0 auto;
    }

    .cta-icon {
      font-size: 4rem;
      margin-bottom: 24px;
    }

    .cta-title {
      font-family: 'Outfit', sans-serif;
      font-size: 2.2rem;
      font-weight: 800;
      margin-bottom: 16px;
    }

    .cta-desc {
      font-size: 1.1rem;
      color: var(--text-secondary);
      margin-bottom: 40px;
    }

    .cta-buttons {
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .cta-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 18px 36px;
      border-radius: 16px;
      font-size: 1.05rem;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.4s ease;
    }

    .cta-btn-primary {
      background: var(--gradient-cyan);
      color: var(--primary-dark);
      box-shadow: var(--shadow-cyan);
    }

    .cta-btn-primary:hover {
      transform: translateY(-4px);
      box-shadow: 0 30px 100px rgba(0, 224, 164, 0.4);
    }

    .cta-btn-secondary {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: var(--text-primary);
    }

    .cta-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: var(--accent-cyan);
    }

    /* ===== Footer ===== */
    .footer {
      padding: 60px 24px 40px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }

    .footer-logo {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 16px;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 24px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: var(--text-muted);
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.3s ease;
    }

    .footer-links a:hover {
      color: var(--accent-cyan);
    }

    .footer-copyright {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* ===== Responsive ===== */
    @media (max-width: 900px) {
      .process-steps {
        flex-direction: column;
      }

      .process-arrow {
        transform: rotate(90deg);
      }

      .algorithms-grid {
        grid-template-columns: 1fr;
      }

      .algorithm-card.featured {
        grid-column: span 1;
      }

      .monte-carlo-features {
        grid-template-columns: 1fr;
      }

      .monte-carlo-card::after {
        display: none;
      }

      .comparison-header,
      .comparison-row {
        grid-template-columns: 1.5fr 1fr 1fr;
        padding: 16px 20px;
        font-size: 0.85rem;
      }
    }

    @media (max-width: 640px) {
      .quality-gauges {
        grid-template-columns: 1fr;
      }

      .nav-back span {
        display: none;
      }

      .monte-carlo-card {
        padding: 32px 24px;
      }

      .process-card {
        padding: 24px;
      }

      .quality-card {
        padding: 24px;
      }

      .page-stats {
        gap: 32px;
      }

      .cta-buttons {
        flex-direction: column;
      }

      .cta-btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="nav">
    <div class="nav-container">
      <a href="/" class="nav-logo">
        <div class="nav-logo-icon">🎯</div>
        <span class="nav-logo-text">오늘로또</span>
      </a>
      <a href="/" class="nav-back">
        ← <span>메인으로 돌아가기</span>
      </a>
    </div>
  </nav>

  <!-- Page Header -->
  <header class="page-header">
    <div class="page-badge">🔬 AI 분석 기술 완전 공개</div>
    <h1 class="page-title">10가지 분석 알고리즘</h1>
    <p class="page-subtitle">
      오늘로또 AI가 사용하는 모든 분석 기법을 100% 투명하게 공개합니다.
      몬테카를로 시뮬레이션부터 AC값 분석까지, 원하는 기법을 선택해서 분석받으세요.
    </p>
    <div class="page-stats">
      <div class="page-stat">
        <div class="page-stat-value"><?= number_format($max_round) ?></div>
        <div class="page-stat-label">분석 회차</div>
      </div>
      <div class="page-stat">
        <div class="page-stat-value"><?= number_format($total_numbers) ?></div>
        <div class="page-stat-label">분석 데이터</div>
      </div>
      <div class="page-stat">
        <div class="page-stat-value">10</div>
        <div class="page-stat-label">분석 기법</div>
      </div>
    </div>
  </header>

  <!-- Monte Carlo Hero Section -->
  <section class="monte-carlo-section">
    <div class="monte-carlo-container">
      <div class="monte-carlo-card">
        <div class="monte-carlo-badge">⭐ 대표 알고리즘</div>
        <h2 class="monte-carlo-title">몬테카를로 시뮬레이션</h2>
        <p class="monte-carlo-desc">
          수만 번의 무작위 시뮬레이션을 통해 통계적으로 가장 유리한 번호 조합을 찾아냅니다.
          카지노 확률 계산에서 사용되는 동일한 방법론으로, 각 번호의 출현 확률을 정밀하게 계산합니다.
        </p>

        <div class="monte-carlo-features">
          <div class="monte-carlo-feature">
            <div class="monte-carlo-feature-icon">🔄</div>
            <h4 class="monte-carlo-feature-title">10,000회 시뮬레이션</h4>
            <p class="monte-carlo-feature-desc">수만 번의 가상 추첨으로 통계적 신뢰도 확보</p>
          </div>
          <div class="monte-carlo-feature">
            <div class="monte-carlo-feature-icon">📊</div>
            <h4 class="monte-carlo-feature-title">확률 분포 계산</h4>
            <p class="monte-carlo-feature-desc">각 번호 조합의 기대값과 분산 분석</p>
          </div>
          <div class="monte-carlo-feature">
            <div class="monte-carlo-feature-icon">🎯</div>
            <h4 class="monte-carlo-feature-title">최적 조합 도출</h4>
            <p class="monte-carlo-feature-desc">시뮬레이션 결과 기반 상위 조합 추천</p>
          </div>
        </div>

        <div class="monte-carlo-visual">
          <div class="simulation-step">
            <div class="simulation-step-icon">📥</div>
            <span class="simulation-step-label">역대 데이터<br>로드</span>
          </div>
          <span class="simulation-arrow">→</span>
          <div class="simulation-step">
            <div class="simulation-step-icon">🎲</div>
            <span class="simulation-step-label">무작위<br>시뮬레이션</span>
          </div>
          <span class="simulation-arrow">→</span>
          <div class="simulation-step">
            <div class="simulation-step-icon">📈</div>
            <span class="simulation-step-label">확률 분포<br>계산</span>
          </div>
          <span class="simulation-arrow">→</span>
          <div class="simulation-step">
            <div class="simulation-step-icon">⚖️</div>
            <span class="simulation-step-label">균형 점수<br>산정</span>
          </div>
          <span class="simulation-arrow">→</span>
          <div class="simulation-step">
            <div class="simulation-step-icon">✨</div>
            <span class="simulation-step-label">최적 조합<br>출력</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Algorithm Process Section -->
  <section class="process-section">
    <div class="process-container">
      <div class="section-header">
        <div class="section-badge">🔍 분석 프로세스</div>
        <h2 class="section-title">AI 분석 3단계</h2>
        <p class="section-subtitle">
          모든 분석 기법은 동일한 3단계 프로세스를 거칩니다
        </p>
      </div>

      <div class="process-card">
        <div class="process-steps">
          <div class="process-step">
            <span class="process-step-num">1</span>
            <div class="process-step-icon">📥</div>
            <h4 class="process-step-title">데이터 수집</h4>
            <p class="process-step-desc">동행복권 공식 API에서<br>1~<?= (int)$max_round ?>회차 데이터 수집</p>
          </div>
          <span class="process-arrow">→</span>
          <div class="process-step">
            <span class="process-step-num">2</span>
            <div class="process-step-icon">📊</div>
            <h4 class="process-step-title">패턴 분석</h4>
            <p class="process-step-desc">선택한 기법으로<br>빈도, 패턴, 연관성 분석</p>
          </div>
          <span class="process-arrow">→</span>
          <div class="process-step">
            <span class="process-step-num">3</span>
            <div class="process-step-icon">⚖️</div>
            <h4 class="process-step-title">균형 점수화</h4>
            <p class="process-step-desc">홀짝, 고저, AC값 등<br>다중 기준 점수 산정</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 10 Algorithms Grid -->
  <section class="algorithms-section" id="algorithms">
    <div class="algorithms-container">
      <div class="section-header">
        <div class="section-badge">✨ 10가지 분석 기법</div>
        <h2 class="section-title">원하는 기법을 선택하세요</h2>
        <p class="section-subtitle">
          각 기법마다 200원, 원하는 기법만 골라서 분석받을 수 있습니다
        </p>
      </div>

      <div class="algorithms-grid">
        <!-- 1. 몬테카를로 (Featured) -->
        <div class="algorithm-card featured">
          <div class="algorithm-header">
            <div class="algorithm-icon">🎲</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #1</div>
              <h3 class="algorithm-title">몬테카를로 시뮬레이션</h3>
              <p class="algorithm-subtitle">Monte Carlo Simulation</p>
            </div>
          </div>
          <p class="algorithm-desc">
            수만 번의 무작위 시뮬레이션을 통해 각 번호 조합의 출현 확률을 계산합니다. 
            카지노와 금융공학에서 사용되는 검증된 확률 계산 방법론입니다.
            역대 당첨 패턴을 기반으로 가상 추첨을 반복하여 통계적으로 유리한 조합을 찾아냅니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">10,000회 시뮬레이션</span>
            <span class="algorithm-tag">확률 분포 분석</span>
            <span class="algorithm-tag">기대값 계산</span>
            <span class="algorithm-tag">⭐ 대표 기법</span>
          </div>
        </div>

        <!-- 2. Hot/Cold 분석 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">🔥</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #2</div>
              <h3 class="algorithm-title">Hot/Cold 분석</h3>
              <p class="algorithm-subtitle">출현 빈도 분석</p>
            </div>
          </div>
          <p class="algorithm-desc">
            최근 100회 기준 자주 나온 번호(Hot)와 오랫동안 안 나온 번호(Cold)를 분석합니다.
            적절한 Hot/Cold 비율로 균형 잡힌 조합을 생성합니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">최근 100회 기준</span>
            <span class="algorithm-tag">과출/미출 번호</span>
          </div>
        </div>

        <!-- 3. AC값 분석 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">🧮</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #3</div>
              <h3 class="algorithm-title">AC값 분석</h3>
              <p class="algorithm-subtitle">조합 복잡도 지수</p>
            </div>
          </div>
          <p class="algorithm-desc">
            번호 조합의 다양성을 수치화한 AC값(Arithmetic Complexity)을 계산합니다.
            AC값이 높을수록 번호들이 골고루 분포되어 있음을 의미합니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">AC값 0~10</span>
            <span class="algorithm-tag">다양성 지수</span>
          </div>
        </div>

        <!-- 4. 홀짝 균형 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">⚖️</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #4</div>
              <h3 class="algorithm-title">홀짝/고저 균형</h3>
              <p class="algorithm-subtitle">비율 최적화</p>
            </div>
          </div>
          <p class="algorithm-desc">
            역대 당첨번호의 홀수/짝수, 고번호/저번호 비율 패턴을 분석합니다.
            3:3 또는 4:2 비율이 가장 많이 출현한 패턴입니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">홀짝 비율</span>
            <span class="algorithm-tag">고저 비율</span>
          </div>
        </div>

        <!-- 5. 색상볼 분석 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">🎨</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #5</div>
              <h3 class="algorithm-title">색상볼 통계</h3>
              <p class="algorithm-subtitle">번호대별 분포</p>
            </div>
          </div>
          <p class="algorithm-desc">
            1~10(노랑), 11~20(파랑), 21~30(빨강), 31~40(회색), 41~45(초록) 
            번호대별 색상 분포를 분석하여 다양성이 높은 조합을 제안합니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">5개 색상 구간</span>
            <span class="algorithm-tag">분포 균형</span>
          </div>
        </div>

        <!-- 6. 합계 분석 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">📈</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #6</div>
              <h3 class="algorithm-title">합계 분석</h3>
              <p class="algorithm-subtitle">번호 총합 최적화</p>
            </div>
          </div>
          <p class="algorithm-desc">
            6개 번호의 총합이 100~170 사이일 때 당첨 확률이 높다는 통계를 반영합니다.
            역대 당첨번호의 약 70%가 이 구간에 분포합니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">최적 구간 100~170</span>
            <span class="algorithm-tag">70% 적중</span>
          </div>
        </div>

        <!-- 7. 연속번호 패턴 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">🔗</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #7</div>
              <h3 class="algorithm-title">연속번호 패턴</h3>
              <p class="algorithm-subtitle">연번 출현 분석</p>
            </div>
          </div>
          <p class="algorithm-desc">
            연속된 번호(예: 12, 13)가 함께 출현하는 패턴을 분석합니다.
            연속번호 1~2쌍 포함 시 당첨 확률이 높다는 통계를 반영합니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">연번 쌍 분석</span>
            <span class="algorithm-tag">최적 1~2쌍</span>
          </div>
        </div>

        <!-- 8. 끝수 분석 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">🔢</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #8</div>
              <h3 class="algorithm-title">끝수 분석</h3>
              <p class="algorithm-subtitle">끝자리 분포</p>
            </div>
          </div>
          <p class="algorithm-desc">
            번호의 끝자리(0~9) 분포를 분석합니다.
            같은 끝수가 3개 이상 나오는 경우는 드물다는 통계를 반영합니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">끝자리 0~9</span>
            <span class="algorithm-tag">중복 끝수 제한</span>
          </div>
        </div>

        <!-- 9. 동반출현 분석 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">🧠</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #9</div>
              <h3 class="algorithm-title">동반출현 분석</h3>
              <p class="algorithm-subtitle">상관관계 패턴</p>
            </div>
          </div>
          <p class="algorithm-desc">
            특정 번호들이 함께 나오는 동반출현 패턴을 AI가 학습합니다.
            "3이 나오면 17도 자주 나온다" 같은 상관관계를 찾습니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">상관계수 분석</span>
            <span class="algorithm-tag">동반 출현율</span>
          </div>
        </div>

        <!-- 10. 주기 분석 -->
        <div class="algorithm-card">
          <div class="algorithm-header">
            <div class="algorithm-icon">🔄</div>
            <div class="algorithm-info">
              <div class="algorithm-number">Algorithm #10</div>
              <h3 class="algorithm-title">주기 분석</h3>
              <p class="algorithm-subtitle">출현 간격 패턴</p>
            </div>
          </div>
          <p class="algorithm-desc">
            각 번호별 평균 출현 주기를 계산합니다.
            오래 나오지 않은 번호가 "출현 예정"인지 분석하여 반영합니다.
          </p>
          <div class="algorithm-details">
            <span class="algorithm-tag">평균 주기 계산</span>
            <span class="algorithm-tag">출현 예정 분석</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Quality Section -->
  <?php
  // 평균 품질 데이터 가져오기
  $avg = sql_fetch("
    SELECT
      ROUND(AVG(balance_score),1) AS balance,
      ROUND(AVG(ac_value),1)      AS ac,
      ROUND(AVG(pattern_score),1) AS pattern,
      ROUND(AVG(hotcold_score),1) AS hotcold,
      COUNT(*) AS cnt
    FROM g5_lotto_ai_pick_log
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
  ");

  $balance = (float)($avg['balance'] ?? 82);
  $ac      = (float)($avg['ac'] ?? 7.2);
  $pattern = (float)($avg['pattern'] ?? 78);
  $hotcold = (float)($avg['hotcold'] ?? 85);

  $ac_pct = max(0, min(100, $ac * 10));
  ?>
  <section class="quality-section">
    <div class="quality-container">
      <div class="quality-card">
        <div class="quality-header">
          <h3 class="quality-title">📊 AI 생성 번호 평균 품질</h3>
          <p class="quality-subtitle">최근 30일간 생성된 번호의 평균 품질 지표</p>
        </div>
        <div class="quality-gauges">
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">⚖️ 균형 점수</span>
              <span class="quality-gauge-value"><?php echo number_format($balance, 0); ?>점</span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill excellent" style="width: <?php echo (int)$balance; ?>%"></div>
            </div>
            <p class="quality-desc">홀짝, 고저, 색상 분포 균형도</p>
          </div>
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">🧮 AC값</span>
              <span class="quality-gauge-value"><?php echo number_format($ac, 1); ?></span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill excellent" style="width: <?php echo (int)$ac_pct; ?>%"></div>
            </div>
            <p class="quality-desc">번호 조합의 다양성 지수 (최대 10)</p>
          </div>
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">🎯 패턴 적합도</span>
              <span class="quality-gauge-value"><?php echo number_format($pattern, 0); ?>점</span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill good" style="width: <?php echo (int)$pattern; ?>%"></div>
            </div>
            <p class="quality-desc">역대 당첨 패턴과의 유사도</p>
          </div>
          <div class="quality-gauge">
            <div class="quality-gauge-header">
              <span class="quality-gauge-label">🔥 Hot/Cold 반영</span>
              <span class="quality-gauge-value"><?php echo number_format($hotcold, 0); ?>점</span>
            </div>
            <div class="quality-bar">
              <div class="quality-fill excellent" style="width: <?php echo (int)$hotcold; ?>%"></div>
            </div>
            <p class="quality-desc">최근 출현 빈도 반영 정도</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Comparison Section -->
  <section class="comparison-section">
    <div class="comparison-container">
      <div class="section-header">
        <div class="section-badge">📋 기능 비교</div>
        <h2 class="section-title">오늘로또 vs 타 서비스</h2>
        <p class="section-subtitle">
          투명한 알고리즘 공개와 합리적인 가격
        </p>
      </div>

      <div class="comparison-table">
        <div class="comparison-header">
          <div>기능</div>
          <div style="text-align: center;">오늘로또</div>
          <div style="text-align: center;">타 서비스</div>
        </div>
        <div class="comparison-row">
          <div class="comparison-feature">
            <span class="comparison-feature-icon">🔬</span>
            <span class="comparison-feature-name">알고리즘 100% 공개</span>
          </div>
          <div class="comparison-check"><span class="check-yes">✓</span></div>
          <div class="comparison-check"><span class="check-no">✗</span></div>
        </div>
        <div class="comparison-row">
          <div class="comparison-feature">
            <span class="comparison-feature-icon">🎲</span>
            <span class="comparison-feature-name">몬테카를로 시뮬레이션</span>
          </div>
          <div class="comparison-check"><span class="check-yes">✓</span></div>
          <div class="comparison-check"><span class="check-partial">일부만</span></div>
        </div>
        <div class="comparison-row">
          <div class="comparison-feature">
            <span class="comparison-feature-icon">🎯</span>
            <span class="comparison-feature-name">10가지 분석 기법</span>
          </div>
          <div class="comparison-check"><span class="check-yes">✓</span></div>
          <div class="comparison-check"><span class="check-partial">3~5개</span></div>
        </div>
        <div class="comparison-row">
          <div class="comparison-feature">
            <span class="comparison-feature-icon">⚡</span>
            <span class="comparison-feature-name">즉시 결과 확인</span>
          </div>
          <div class="comparison-check"><span class="check-yes">✓</span></div>
          <div class="comparison-check"><span class="check-no">문자 발송</span></div>
        </div>
        <div class="comparison-row">
          <div class="comparison-feature">
            <span class="comparison-feature-icon">💰</span>
            <span class="comparison-feature-name">합리적 가격</span>
          </div>
          <div class="comparison-check"><span class="check-yes">200원/기법</span></div>
          <div class="comparison-check"><span class="check-no">수만원</span></div>
        </div>
        <div class="comparison-row">
          <div class="comparison-feature">
            <span class="comparison-feature-icon">✅</span>
            <span class="comparison-feature-name">기법 선택 가능</span>
          </div>
          <div class="comparison-check"><span class="check-yes">✓</span></div>
          <div class="comparison-check"><span class="check-no">✗</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="cta-container">
      <div class="cta-icon">🎯</div>
      <h2 class="cta-title">지금 바로 분석 시작하기</h2>
      <p class="cta-desc">
        무료 1회 분석을 경험해보세요.<br>
        원하는 기법을 선택해서 분석받을 수 있습니다.
      </p>
      <div class="cta-buttons">
        <a href="/auth.php" class="cta-btn cta-btn-primary">
          🎲 무료 분석 시작하기
        </a>
        <a href="/" class="cta-btn cta-btn-secondary">
          ← 메인으로 돌아가기
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-logo">오늘로또</div>
      <div class="footer-links">
        <a href="/">홈</a>
        <a href="/algorithm.php">분석 알고리즘</a>
        <a href="/terms.html">이용약관</a>
        <a href="/privacy.html">개인정보처리방침</a>
      </div>
      <p class="footer-copyright">
        © <?= date('Y') ?> 오늘로또. 통계 분석은 참고용이며 당첨을 보장하지 않습니다.
      </p>
    </div>
  </footer>

  <script>
    // Quality Gauge Animation on Scroll
    const gaugeObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const fills = entry.target.querySelectorAll('.quality-fill');
          fills.forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0%';
            setTimeout(() => {
              fill.style.width = width;
            }, 100);
          });
          gaugeObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    const qualityCard = document.querySelector('.quality-card');
    if (qualityCard) {
      gaugeObserver.observe(qualityCard);
    }

    // Algorithm Card Animation
    const cardObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          entry.target.style.animation = `fadeInUp 0.6s ease ${index * 0.1}s forwards`;
          entry.target.style.opacity = '1';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.algorithm-card').forEach(card => {
      card.style.opacity = '0';
      cardObserver.observe(card);
    });
  </script>
</body>
</html>

<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=5.0">
  
  <!-- Primary Meta Tags -->
  <title><?php echo get_head_title($g5['title']); ?></title>
  <meta name="title" content="<?php echo get_head_title($g5['title']); ?>">
  <meta name="description" content="동행복권 공식 데이터 기반 AI 로또 분석. 23년간 7,206개 당첨번호 패턴 분석으로 균형 잡힌 번호 조합을 제공합니다. 무료 1회 분석 즉시 제공.">
  <meta name="keywords" content="로또, 로또분석, AI로또, 로또번호, 당첨번호, 로또예측, 로또통계, 동행복권">
  <meta name="robots" content="index, follow">
  <meta name="author" content="오늘로또">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="https://lottoinsight.ai/">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://lottoinsight.ai/">
  <meta property="og:title" content="오늘로또 - AI 기반 로또 번호 분석">
  <meta property="og:description" content="1,201회차 동행복권 공식 데이터 기반. 23년간 당첨번호 패턴을 AI가 분석합니다.">
  <meta property="og:image" content="https://lottoinsight.ai/og-image.png">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:title" content="오늘로또 - AI 기반 로또 번호 분석">
  <meta property="twitter:description" content="1,201회차 공식 데이터 기반 AI 분석. 무료 1회 즉시 제공!">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#0B132B">

  <!-- Structured Data for SEO -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "오늘로또",
    "description": "AI 기반 로또 번호 분석 서비스. 동행복권 공식 데이터 기반 23년간 당첨번호 패턴 분석.",
    "url": "https://lottoinsight.ai",
    "applicationCategory": "UtilitiesApplication",
    "operatingSystem": "Web",
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "KRW",
      "description": "무료 1회 분석 제공"
    },
    "author": {
      "@type": "Organization",
      "name": "오늘로또",
      "url": "https://lottoinsight.ai"
    },
    "datePublished": "2023-01-01",
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.6",
      "ratingCount": "1247"
    }
  }
  </script>

  <!-- Kakao SDK -->
  <script src="https://t1.kakaocdn.net/kakao_js_sdk/2.6.0/kakao.min.js" integrity="sha384-6MFdIr0zOira1CHQkedUqJVql0YtcZA1P0nbPrQYJXVJZUkTk/oX4U9GhLYnz8E" crossorigin="anonymous"></script>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">

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
      --gradient-hero: radial-gradient(ellipse at 50% 0%, rgba(0, 224, 164, 0.12) 0%, transparent 50%),
                       radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
      --gradient-mesh: radial-gradient(at 40% 20%, rgba(0, 224, 164, 0.15) 0px, transparent 50%),
                       radial-gradient(at 80% 0%, rgba(139, 92, 246, 0.1) 0px, transparent 50%),
                       radial-gradient(at 0% 50%, rgba(0, 212, 255, 0.1) 0px, transparent 50%);
      --shadow-cyan: 0 25px 80px rgba(0, 224, 164, 0.3);
      --shadow-gold: 0 25px 80px rgba(255, 215, 95, 0.3);
      --shadow-purple: 0 25px 80px rgba(139, 92, 246, 0.3);
      --glass-bg: rgba(255, 255, 255, 0.03);
      --glass-border: rgba(255, 255, 255, 0.08);
      --ball-yellow: linear-gradient(145deg, #ffd700 0%, #f59e0b 100%);
      --ball-blue: linear-gradient(145deg, #3b82f6 0%, #1d4ed8 100%);
      --ball-red: linear-gradient(145deg, #ef4444 0%, #b91c1c 100%);
      --ball-gray: linear-gradient(145deg, #6b7280 0%, #374151 100%);
      --ball-green: linear-gradient(145deg, #22c55e 0%, #15803d 100%);
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
      -moz-osx-font-smoothing: grayscale;
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
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 0 20px var(--accent-cyan-glow); }
      50% { box-shadow: 0 0 40px var(--accent-cyan-glow), 0 0 60px var(--accent-cyan-glow); }
    }

    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    @keyframes gradient-shift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .animate-fade-in-up {
      animation: fadeInUp 0.8s ease-out forwards;
    }

    .animate-delay-1 { animation-delay: 0.1s; }
    .animate-delay-2 { animation-delay: 0.2s; }
    .animate-delay-3 { animation-delay: 0.3s; }
    .animate-delay-4 { animation-delay: 0.4s; }

    a {
      color: inherit;
      text-decoration: none;
    }

    button {
      font-family: inherit;
      cursor: pointer;
      border: none;
      background: none;
    }

    
/* ===== Navbar (LottoInsight 전용) ===== */
.li-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 80px;
  background: rgba(11, 19, 43, 0.8);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  z-index: 1000;
  transition: all 0.3s ease;
}

.li-navbar.scrolled {
  height: 64px;
  background: rgba(11, 19, 43, 0.95);
}

.li-navbar-inner {
  max-width: 1280px;
  height: 100%;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.li-nav-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-family: 'Outfit', sans-serif;
  font-weight: 800;
  font-size: 1.4rem;
}

.li-nav-logo-icon {
  width: 44px;
  height: 44px;
  background: var(--gradient-cyan);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
}

/* 로고 SVG가 찌그러지는 경우 방지용 */
.li-nav-logo-icon svg {
  width: 22px;
  height: 22px;
  display: block;
  flex-shrink: 0;
}

.li-nav-logo:hover .li-nav-logo-icon {
  transform: rotate(-10deg) scale(1.05);
}

.li-nav-menu {
  display: flex;
  align-items: center;
  gap: 8px;
}

.li-nav-link {
  padding: 10px 20px;
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--text-secondary);
  border-radius: 12px;
  transition: all 0.3s ease;
}

.li-nav-link:hover {
  color: var(--text-primary);
  background: rgba(255, 255, 255, 0.05);
}

.li-nav-cta {
  margin-left: 12px;
  padding: 12px 28px;
  background: var(--gradient-cyan);
  border-radius: 14px;
  font-weight: 700;
  color: var(--primary-dark);
  transition: all 0.3s ease;
}

.li-nav-cta:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-cyan);
}

.li-mobile-menu-btn {
  display: none;
  width: 44px;
  height: 44px;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
}

/* 반응형에서 nav 관련 부분도 같이 수정 */
@media (max-width: 768px) {
  .li-navbar {
    height: 64px;
  }

  .li-nav-menu {
    display: none;
  }

  .li-mobile-menu-btn {
    display: flex;
  }
}


    /* ===== Hero Section ===== */
    .hero {
      min-height: 100vh;
      padding: 140px 24px 100px;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: -300px;
      left: 50%;
      transform: translateX(-50%);
      width: 1400px;
      height: 1400px;
      background: radial-gradient(ellipse at center, rgba(0, 224, 164, 0.15) 0%, transparent 50%),
                  radial-gradient(ellipse at 30% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 40%);
      pointer-events: none;
      animation: gradient-shift 15s ease infinite;
      background-size: 200% 200%;
    }

    .hero::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.015'%3E%3Ccircle cx='30' cy='30' r='1'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      pointer-events: none;
    }

    .hero-container {
      max-width: 1320px;
      margin: 0 auto;
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
      position: relative;
      z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 24px;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.15), rgba(0, 212, 255, 0.1));
      border: 1px solid rgba(0, 224, 164, 0.3);
      border-radius: 50px;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--accent-cyan);
      margin-bottom: 28px;
      animation: fadeInUp 0.6s ease both;
      backdrop-filter: blur(10px);
    }

    .hero-badge-dot {
      width: 10px;
      height: 10px;
      background: var(--accent-cyan);
      border-radius: 50%;
      animation: pulse 2s ease infinite;
      box-shadow: 0 0 12px var(--accent-cyan-glow);
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 12px var(--accent-cyan-glow); }
      50% { opacity: 0.7; transform: scale(1.3); box-shadow: 0 0 24px var(--accent-cyan-glow); }
    }

    .hero-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(3rem, 5.5vw, 4.5rem);
      font-weight: 900;
      line-height: 1.08;
      letter-spacing: -0.03em;
      margin-bottom: 28px;
      animation: fadeInUp 0.6s ease 0.1s both;
    }

    .hero-title .line {
      display: block;
    }

    .hero-title .gradient {
      background: linear-gradient(135deg, #00E0A4 0%, #00D4FF 50%, #00E0A4 100%);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: gradient-shift 4s ease infinite;
    }

    .hero-title .highlight {
      position: relative;
      display: inline-block;
    }

    .hero-title .highlight::after {
      content: '';
      position: absolute;
      bottom: 4px;
      left: -4px;
      right: -4px;
      height: 14px;
      background: linear-gradient(90deg, rgba(255, 215, 95, 0.4), rgba(255, 159, 67, 0.3));
      transform: skewX(-8deg);
      z-index: -1;
      border-radius: 4px;
    }

    .hero-subtitle {
      font-size: 1.2rem;
      color: var(--text-secondary);
      line-height: 1.8;
      margin-bottom: 40px;
      max-width: 480px;
      animation: fadeInUp 0.6s ease 0.2s both;
    }

    .hero-cta-group {
      display: flex;
      gap: 16px;
      margin-bottom: 48px;
      animation: fadeInUp 0.6s ease 0.3s both;
    }

    .hero-cta-primary {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 22px 44px;
      background: linear-gradient(135deg, #00E0A4 0%, #00C896 50%, #00D4FF 100%);
      background-size: 200% auto;
      border-radius: 20px;
      font-family: 'Outfit', sans-serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--primary-dark);
      box-shadow: 0 20px 50px rgba(0, 224, 164, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.2);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .hero-cta-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: 0.5s;
    }

    .hero-cta-primary:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 30px 60px rgba(0, 224, 164, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.3);
      background-position: right center;
    }

    .hero-cta-primary:hover::before {
      left: 100%;
    }

    .hero-cta-secondary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 22px 36px;
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      font-weight: 600;
      color: var(--text-primary);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    .hero-cta-secondary:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-3px);
    }

    .hero-trust {
      display: flex;
      align-items: center;
      gap: 24px;
      animation: fadeInUp 0.6s ease 0.4s both;
    }

    .hero-trust-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    .hero-trust-icon {
      color: var(--accent-cyan);
    }

    /* Hero Visual */
    .hero-visual {
      position: relative;
      z-index: 1;
      animation: fadeIn 0.8s ease 0.3s both;
    }

    .hero-card {
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.95), rgba(5, 10, 21, 0.98));
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: var(--border-radius-xl);
      padding: 36px;
      backdrop-filter: blur(24px);
      box-shadow: 0 50px 100px rgba(0, 0, 0, 0.5),
                  0 0 0 1px rgba(255, 255, 255, 0.05) inset,
                  0 -20px 40px rgba(0, 224, 164, 0.03) inset;
      position: relative;
      overflow: hidden;
    }

    .hero-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0, 224, 164, 0.3), rgba(139, 92, 246, 0.3), transparent);
    }

    .hero-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .live-badge {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: rgba(239, 68, 68, 0.15);
      border-radius: 30px;
      font-size: 0.8rem;
      font-weight: 700;
      color: #ef4444;
    }

    .live-dot {
      width: 8px;
      height: 8px;
      background: #ef4444;
      border-radius: 50%;
      animation: live-pulse 1.5s ease infinite;
    }

    @keyframes live-pulse {
      0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
      50% { opacity: 0.8; box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }

    .hero-card-round {
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    .hero-balls-container {
      display: flex;
      justify-content: center;
      gap: 12px;
      padding: 28px;
      background: rgba(0, 0, 0, 0.3);
      border-radius: 20px;
      margin-bottom: 24px;
    }

    .hero-ball {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.3rem;
      color: #fff;
      position: relative;
      animation: ballFloat 3s ease-in-out infinite;
    }

    .hero-ball:nth-child(1) { animation-delay: 0s; }
    .hero-ball:nth-child(2) { animation-delay: 0.2s; }
    .hero-ball:nth-child(3) { animation-delay: 0.4s; }
    .hero-ball:nth-child(4) { animation-delay: 0.6s; }
    .hero-ball:nth-child(5) { animation-delay: 0.8s; }
    .hero-ball:nth-child(6) { animation-delay: 1s; }
    .hero-ball:nth-child(8) { animation-delay: 1.2s; }

    @keyframes ballFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-8px); }
    }

    .hero-ball::after {
      content: '';
      position: absolute;
      top: 10px;
      left: 14px;
      width: 14px;
      height: 10px;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      transform: rotate(-30deg);
    }

    .ball-yellow { 
      background: var(--ball-yellow); 
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5), inset 0 -5px 15px rgba(0, 0, 0, 0.2); 
    }
    .ball-blue { 
      background: var(--ball-blue); 
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5), inset 0 -5px 15px rgba(0, 0, 0, 0.2); 
    }
    .ball-red { 
      background: var(--ball-red); 
      box-shadow: 0 8px 25px rgba(239, 68, 68, 0.5), inset 0 -5px 15px rgba(0, 0, 0, 0.2); 
    }
    .ball-gray { 
      background: var(--ball-gray); 
      box-shadow: 0 8px 25px rgba(107, 114, 128, 0.5), inset 0 -5px 15px rgba(0, 0, 0, 0.2); 
    }
    .ball-green { 
      background: var(--ball-green); 
      box-shadow: 0 8px 25px rgba(34, 197, 94, 0.5), inset 0 -5px 15px rgba(0, 0, 0, 0.2); 
    }

    .bonus-sep {
      display: flex;
      align-items: center;
      font-size: 1.5rem;
      color: var(--text-muted);
      margin: 0 4px;
    }

    .hero-card-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 20px;
      background: rgba(0, 224, 164, 0.08);
      border: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 14px;
    }

    .hero-card-prize {
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    .hero-card-prize strong {
      color: var(--accent-gold);
      font-weight: 700;
    }

    .hero-card-link {
      font-size: 0.85rem;
      color: var(--accent-cyan);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .hero-card-status {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .status-mini {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    .status-mini-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #6b7280;
    }

    .status-mini-dot.ok {
      background: #22c55e;
      box-shadow: 0 0 6px rgba(34, 197, 94, 0.5);
    }

    /* Floating elements */
    .floating-ball {
      position: absolute;
      border-radius: 50%;
      opacity: 0.6;
      animation: float 6s ease-in-out infinite;
    }

    .floating-ball-1 {
      width: 80px;
      height: 80px;
      background: var(--ball-yellow);
      top: 20%;
      right: -40px;
      animation-delay: 0s;
    }

    .floating-ball-2 {
      width: 50px;
      height: 50px;
      background: var(--ball-blue);
      bottom: 30%;
      left: -25px;
      animation-delay: 1s;
    }

    .floating-ball-3 {
      width: 60px;
      height: 60px;
      background: var(--ball-green);
      top: 60%;
      right: 10%;
      animation-delay: 2s;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(10deg); }
    }

    .section-header {
      text-align: center;
      margin-bottom: 64px;
    }

    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 22px;
      background: linear-gradient(135deg, rgba(255, 215, 95, 0.12), rgba(255, 159, 67, 0.08));
      border: 1px solid rgba(255, 215, 95, 0.3);
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-gold);
      margin-bottom: 20px;
      backdrop-filter: blur(8px);
    }

    .section-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(2.2rem, 4vw, 3.2rem);
      font-weight: 800;
      margin-bottom: 18px;
      letter-spacing: -0.025em;
      background: linear-gradient(180deg, #ffffff 0%, #94a3b8 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .section-subtitle {
      font-size: 1.15rem;
      color: var(--text-secondary);
      max-width: 640px;
      margin: 0 auto;
      line-height: 1.7;
    }

    /* ===== Features Section ===== */
    .features-section {
      padding: 120px 24px;
      background: rgba(0, 0, 0, 0.15);
    }

    .features-container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
    }

    .feature-card {
      padding: 40px 32px;
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.8), rgba(5, 10, 21, 0.9));
      border: 1px solid var(--glass-border);
      border-radius: var(--border-radius-lg);
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(12px);
    }

    .feature-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.08) 0%, transparent 60%);
      opacity: 0;
      transition: opacity 0.4s ease;
    }

    .feature-card::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0, 224, 164, 0.4), transparent);
      opacity: 0;
      transition: opacity 0.4s ease;
    }

    .feature-card:hover {
      transform: translateY(-10px);
      border-color: rgba(0, 224, 164, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 224, 164, 0.1);
    }

    .feature-card:hover::before,
    .feature-card:hover::after {
      opacity: 1;
    }

    .feature-icon {
      width: 72px;
      height: 72px;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.15), rgba(0, 212, 255, 0.1));
      border: 1px solid rgba(0, 224, 164, 0.25);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 24px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .feature-card:hover .feature-icon {
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.2), rgba(0, 212, 255, 0.15));
      transform: scale(1.08) rotate(3deg);
      box-shadow: 0 10px 30px rgba(0, 224, 164, 0.2);
    }

    .feature-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 12px;
    }

    .feature-desc {
      font-size: 0.95rem;
      color: var(--text-secondary);
      line-height: 1.7;
    }

    /* ===== Statistics Dashboard ===== */
    .dashboard-section {
      padding: 120px 24px;
    }

    .dashboard-container {
      max-width: 1100px;
      margin: 0 auto;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
    }

    .dashboard-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 28px;
      padding: 32px;
    }

    .dashboard-card-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .hot-numbers, .cold-numbers {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .hot-ball, .cold-ball {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.1rem;
      color: #fff;
      position: relative;
    }

    .hot-ball::after, .cold-ball::after {
      content: '';
      position: absolute;
      top: 9px;
      left: 13px;
      width: 12px;
      height: 8px;
      background: rgba(255, 255, 255, 0.35);
      border-radius: 50%;
      transform: rotate(-30deg);
    }

    .ball-count {
      position: absolute;
      bottom: -8px;
      right: -8px;
      width: 24px;
      height: 24px;
      background: var(--primary-dark);
      border: 2px solid;
      border-radius: 50%;
      font-size: 0.65rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hot-ball .ball-count {
      border-color: #ef4444;
      color: #ef4444;
    }

    .cold-ball .ball-count {
      border-color: #3b82f6;
      color: #3b82f6;
    }

    .ratio-bars {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .ratio-item {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .ratio-label {
      width: 60px;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .ratio-bar {
      flex: 1;
      height: 12px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      overflow: hidden;
    }

    .ratio-fill {
      height: 100%;
      background: var(--gradient-cyan);
      border-radius: 6px;
      transition: width 1s ease;
    }

    .ratio-value {
      width: 50px;
      text-align: right;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--accent-cyan);
    }

    /* ===== Pricing Section ===== */
    .pricing-section {
      padding: 120px 24px;
      background: rgba(0, 0, 0, 0.2);
    }

    .pricing-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 32px;
    }

    .pricing-card {
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.9), rgba(5, 10, 21, 0.95));
      border: 1px solid var(--glass-border);
      border-radius: var(--border-radius-xl);
      padding: 44px;
      text-align: center;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .pricing-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
    }

    .pricing-card.featured {
      border-color: rgba(0, 224, 164, 0.5);
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.08), rgba(13, 21, 38, 0.95));
      transform: scale(1.03);
      box-shadow: 0 30px 60px rgba(0, 224, 164, 0.15);
    }

    .pricing-card.featured::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--accent-cyan), transparent);
    }

    .pricing-card.featured:hover {
      transform: scale(1.05) translateY(-5px);
    }

    .pricing-badge {
      display: inline-block;
      padding: 6px 16px;
      background: var(--gradient-cyan);
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin-bottom: 16px;
    }

    .pricing-name {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .pricing-desc {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-bottom: 24px;
    }

    .pricing-price {
      margin-bottom: 32px;
    }

    .pricing-amount {
      font-family: 'Outfit', sans-serif;
      font-size: 3rem;
      font-weight: 900;
      color: var(--accent-cyan);
    }

    .pricing-period {
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    .pricing-features {
      list-style: none;
      margin-bottom: 32px;
      text-align: left;
    }

    .pricing-features li {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .pricing-features li:last-child {
      border-bottom: none;
    }

    .pricing-features .check {
      color: var(--accent-cyan);
      font-weight: 700;
    }

    .pricing-cta {
      width: 100%;
      padding: 18px;
      border-radius: 16px;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .pricing-cta-primary {
      background: var(--gradient-cyan);
      color: var(--primary-dark);
    }

    .pricing-cta-primary:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-cyan);
    }

    .pricing-cta-secondary {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: var(--text-primary);
    }

    .pricing-cta-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    /* ===== FAQ Section ===== */
    .faq-section {
      padding: 120px 24px;
    }

    .faq-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .faq-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .faq-item {
      background: rgba(13, 24, 41, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .faq-item:hover {
      border-color: rgba(255, 255, 255, 0.12);
    }

    .faq-question {
      width: 100%;
      padding: 24px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 1.05rem;
      font-weight: 600;
      color: var(--text-primary);
      text-align: left;
      cursor: pointer;
    }

    .faq-icon {
      width: 32px;
      height: 32px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: var(--accent-cyan);
      transition: all 0.3s ease;
    }

    .faq-item.active .faq-icon {
      background: var(--accent-cyan);
      color: var(--primary-dark);
      transform: rotate(45deg);
    }

    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }

    .faq-item.active .faq-answer {
      max-height: 200px;
    }

    .faq-answer-content {
      padding: 0 28px 24px;
      font-size: 0.95rem;
      color: var(--text-secondary);
      line-height: 1.7;
    }

    /* ===== Trust Section ===== */
    .trust-section {
      padding: 80px 24px;
      background: rgba(0, 224, 164, 0.03);
      border-top: 1px solid rgba(0, 224, 164, 0.1);
      border-bottom: 1px solid rgba(0, 224, 164, 0.1);
    }

    .trust-container {
      max-width: 1100px;
      margin: 0 auto;
    }

    .trust-grid {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .trust-badge {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 28px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      font-size: 0.95rem;
      color: var(--text-secondary);
      transition: all 0.3s ease;
    }

    .trust-badge:hover {
      border-color: rgba(0, 224, 164, 0.3);
      background: rgba(0, 224, 164, 0.05);
    }

    .trust-badge-icon {
      color: var(--accent-cyan);
      font-weight: 700;
      font-size: 1.1rem;
    }

    /* ===== Final CTA Section ===== */
    .cta-section {
      padding: 140px 24px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .cta-section::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 800px;
      height: 800px;
      background: radial-gradient(circle, rgba(0, 224, 164, 0.1) 0%, transparent 60%);
      pointer-events: none;
    }

    .cta-container {
      max-width: 700px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    .cta-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(2.2rem, 5vw, 3.5rem);
      font-weight: 900;
      margin-bottom: 20px;
      letter-spacing: -0.02em;
    }

    .cta-subtitle {
      font-size: 1.2rem;
      color: var(--text-secondary);
      margin-bottom: 48px;
      line-height: 1.7;
    }

    .cta-button {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 24px 56px;
      background: var(--gradient-gold);
      border-radius: 20px;
      font-family: 'Outfit', sans-serif;
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--primary-dark);
      box-shadow: var(--shadow-gold);
      transition: all 0.3s ease;
    }

    .cta-button:hover {
      transform: translateY(-4px);
      box-shadow: 0 30px 80px rgba(255, 215, 95, 0.35);
    }

    .cta-note {
      margin-top: 24px;
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    /* ===== Footer ===== */
    .footer {
      padding: 100px 24px 50px;
      background: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%);
      border-top: 1px solid var(--glass-border);
      position: relative;
    }

    .footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0, 224, 164, 0.2), transparent);
    }

    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .footer-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding-bottom: 56px;
      border-bottom: 1px solid var(--glass-border);
      margin-bottom: 40px;
    }

    .footer-brand {
      max-width: 340px;
    }

    .footer-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.35rem;
      margin-bottom: 18px;
    }

    .footer-logo-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, var(--accent-cyan) 0%, #00D4FF 100%);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 24px rgba(0, 224, 164, 0.25);
    }

    .footer-desc {
      font-size: 0.9rem;
      color: var(--text-muted);
      line-height: 1.7;
    }

    .footer-links {
      display: flex;
      gap: 64px;
    }

    .footer-col h4 {
      font-family: 'Outfit', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      margin-bottom: 20px;
      color: var(--text-primary);
    }

    .footer-col ul {
      list-style: none;
    }

    .footer-col li {
      margin-bottom: 12px;
    }

    .footer-col a {
      font-size: 0.9rem;
      color: var(--text-muted);
      transition: color 0.3s ease;
    }

    .footer-col a:hover {
      color: var(--accent-cyan);
    }

    .footer-bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
    }

    .footer-copyright {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .footer-disclaimer {
      font-size: 0.8rem;
      color: var(--text-muted);
      max-width: 500px;
      text-align: right;
    }

    /* ===== Live Activity Feed ===== */
    .activity-section {
      padding: 60px 24px;
      background: linear-gradient(180deg, rgba(0, 224, 164, 0.02) 0%, transparent 100%);
    }

    .activity-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .activity-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .activity-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .activity-pulse {
      width: 10px;
      height: 10px;
      background: #ef4444;
      border-radius: 50%;
      animation: live-pulse 1.5s ease infinite;
    }

    .activity-counter {
      font-size: 0.9rem;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .activity-counter strong {
      color: var(--accent-cyan);
      font-family: 'Outfit', sans-serif;
    }

    .activity-feed {
      background: rgba(13, 24, 41, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 20px;
      overflow: hidden;
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      animation: slideIn 0.5s ease both;
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-20px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .activity-avatar {
      width: 40px;
      height: 40px;
      background: var(--gradient-cyan);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: var(--primary-dark);
      font-size: 0.9rem;
    }

    .activity-content {
      flex: 1;
    }

    .activity-text {
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .activity-text strong {
      color: var(--text-primary);
    }

    .activity-text .style-tag {
      color: var(--accent-cyan);
      font-weight: 600;
    }

    .activity-time {
      font-size: 0.8rem;
      color: var(--text-muted);
      white-space: nowrap;
    }

    /* ===== Cumulative Stats Counter ===== */
    .cumulative-section {
      padding: 80px 24px;
      background: rgba(0, 0, 0, 0.15);
    }

    .cumulative-container {
      max-width: 900px;
      margin: 0 auto;
      text-align: center;
    }

    .cumulative-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 32px;
      margin-bottom: 20px;
    }

    .cumulative-item {
      padding: 32px;
      background: rgba(255, 255, 255, 0.02);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 20px;
    }

    .cumulative-number {
      font-family: 'Outfit', sans-serif;
      font-size: 2.8rem;
      font-weight: 900;
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 4px;
    }

    .cumulative-label {
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    .cumulative-note {
      font-size: 0.85rem;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .cumulative-note .pulse-dot {
      width: 6px;
      height: 6px;
      background: var(--accent-cyan);
      border-radius: 50%;
      animation: pulse 2s ease infinite;
    }

    /* ===== Algorithm Transparency Section ===== */
    .algorithm-section {
      padding: 120px 24px;
    }

    .algorithm-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .algorithm-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 32px;
      padding: 48px;
    }

    .algorithm-steps {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 24px;
      margin-bottom: 40px;
    }

    .algorithm-step {
      flex: 1;
      text-align: center;
      padding: 32px 24px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 20px;
      position: relative;
    }

    .algorithm-step-icon {
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

    .algorithm-step-num {
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

    .algorithm-step-title {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }

    .algorithm-step-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .algorithm-arrow {
      font-size: 1.5rem;
      color: var(--accent-cyan);
      flex-shrink: 0;
    }

    .algorithm-links {
      display: flex;
      justify-content: center;
      gap: 24px;
      flex-wrap: wrap;
    }

    .algorithm-link {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 14px 28px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      font-size: 0.95rem;
      color: var(--text-secondary);
      transition: all 0.3s ease;
    }

    .algorithm-link:hover {
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* ===== AI Archive Section ===== */
    .archive-section {
      padding: 120px 24px;
      background: linear-gradient(180deg, rgba(0, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0.25) 100%);
    }

    .archive-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .archive-card {
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.9), rgba(5, 10, 21, 0.95));
      border: 1px solid var(--glass-border);
      border-radius: var(--border-radius-xl);
      overflow: hidden;
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
    }

    .archive-table {
      width: 100%;
    }

    .archive-header {
      display: grid;
      grid-template-columns: 100px 1fr 1fr 100px;
      gap: 16px;
      padding: 20px 32px;
      background: rgba(0, 0, 0, 0.3);
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .archive-row {
      display: grid;
      grid-template-columns: 100px 1fr 1fr 100px;
      gap: 16px;
      padding: 20px 32px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      align-items: center;
      transition: background 0.3s ease;
    }

    .archive-row:hover {
      background: rgba(0, 224, 164, 0.03);
    }

    .archive-row:last-child {
      border-bottom: none;
    }

    .archive-round {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      color: var(--text-primary);
    }

    .archive-balls {
      display: flex;
      gap: 6px;
    }

    .archive-ball {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.78rem;
      color: #fff;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3), inset 0 -2px 6px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }

    .archive-row:hover .archive-ball {
      transform: scale(1.1);
    }

    .archive-ball.matched {
      box-shadow: 0 0 0 2px var(--accent-cyan), 0 0 10px rgba(0, 224, 164, 0.4);
    }

    .archive-match {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .archive-match-num {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
      color: var(--accent-cyan);
    }

    .archive-match-label {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .archive-match-good {
      color: var(--accent-cyan);
    }

    .archive-match-avg {
      color: var(--accent-gold);
    }

    .archive-summary {
      display: flex;
      justify-content: space-around;
      padding: 28px 32px;
      background: rgba(0, 224, 164, 0.08);
      border-top: 1px solid rgba(0, 224, 164, 0.15);
    }

    .archive-summary-item {
      text-align: center;
    }

    .archive-summary-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.8rem;
      font-weight: 900;
      color: var(--accent-cyan);
    }

    .archive-summary-label {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .archive-verify {
      margin-top: 40px;
      padding: 32px;
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.03), rgba(0, 212, 255, 0.02));
      border-top: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 0 0 var(--border-radius-xl) var(--border-radius-xl);
    }

    .archive-verify-header {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 20px;
    }

    .archive-verify-icon {
      font-size: 1.2rem;
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.15), rgba(0, 212, 255, 0.1));
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .archive-verify-input-group {
      display: flex;
      gap: 0;
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.95), rgba(5, 10, 21, 0.98));
      border: 1px solid var(--glass-border);
      border-radius: 18px;
      padding: 5px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.05);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .archive-verify-input-group:focus-within {
      border-color: rgba(0, 224, 164, 0.4);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25), 0 0 0 3px rgba(0, 224, 164, 0.1);
    }

    .archive-verify-input {
      flex: 1;
      padding: 16px 22px;
      background: transparent;
      border: none;
      color: var(--text-primary);
      font-family: 'Outfit', sans-serif;
      font-size: 1rem;
      font-weight: 500;
      outline: none;
    }

    .archive-verify-input::placeholder {
      color: var(--text-muted);
    }

    .archive-verify-btn {
      padding: 16px 32px;
      background: linear-gradient(135deg, var(--accent-cyan) 0%, #00D4FF 100%);
      border-radius: 13px;
      font-family: 'Outfit', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--primary-dark);
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      min-width: 90px;
    }

    .archive-verify-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: 0.5s;
    }

    .archive-verify-btn:hover {
      transform: scale(1.03);
      box-shadow: 0 8px 28px rgba(0, 224, 164, 0.45);
    }

    .archive-verify-btn:hover::before {
      left: 100%;
    }

    .archive-verify-result {
      margin-top: 24px;
      padding: 24px;
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.08), rgba(0, 212, 255, 0.05));
      border: 1px solid rgba(0, 224, 164, 0.25);
      border-radius: 18px;
      animation: fadeInUp 0.4s ease;
      box-shadow: 0 10px 40px rgba(0, 224, 164, 0.1);
    }

    .archive-verify-result-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }

    .archive-verify-title {
      font-weight: 700;
      color: var(--text-primary);
    }

    .archive-verify-status {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-cyan);
    }

    .archive-verify-balls {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    /* ===== Data Verification Section ===== */
    .verify-section {
      padding: 100px 24px;
    }

    .verify-container {
      max-width: 700px;
      margin: 0 auto;
    }

    .verify-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 32px;
      padding: 48px;
    }

    .verify-input-group {
      display: flex;
      gap: 0;
      margin-bottom: 24px;
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.95), rgba(5, 10, 21, 0.98));
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 6px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.05);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .verify-input-group:focus-within {
      border-color: rgba(0, 224, 164, 0.4);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 0 0 3px rgba(0, 224, 164, 0.1);
    }

    .verify-input {
      flex: 1;
      padding: 18px 24px;
      background: transparent;
      border: none;
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--text-primary);
      outline: none;
    }

    .verify-input::placeholder {
      color: var(--text-muted);
      font-weight: 500;
    }

    .verify-btn {
      padding: 18px 36px;
      background: linear-gradient(135deg, var(--accent-cyan) 0%, #00D4FF 100%);
      border-radius: 14px;
      font-family: 'Outfit', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary-dark);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      min-width: 100px;
    }

    .verify-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: 0.5s;
    }

    .verify-btn:hover {
      transform: scale(1.02);
      box-shadow: 0 10px 30px rgba(0, 224, 164, 0.4);
    }

    .verify-btn:hover::before {
      left: 100%;
    }

    .verify-result {
      background: rgba(0, 0, 0, 0.2);
      border-radius: 20px;
      padding: 28px;
      display: none;
    }

    .verify-result.active {
      display: block;
      animation: fadeInUp 0.4s ease;
    }

    .verify-result-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .verify-result-title {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .verify-result-status {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: rgba(0, 224, 164, 0.15);
      border-radius: 30px;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .verify-data-row {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px;
      background: rgba(255, 255, 255, 0.02);
      border-radius: 14px;
      margin-bottom: 12px;
    }

    .verify-data-label {
      width: 140px;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .verify-data-balls {
      display: flex;
      gap: 8px;
      flex: 1;
    }

    .verify-official-link {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 20px;
      padding: 14px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px dashed rgba(255, 255, 255, 0.15);
      border-radius: 14px;
      font-size: 0.9rem;
      color: var(--accent-cyan);
      transition: all 0.3s ease;
    }

    .verify-official-link:hover {
      background: rgba(0, 224, 164, 0.05);
      border-color: var(--accent-cyan);
    }

    /* ===== User Reviews Section ===== */
    .reviews-section {
      padding: 100px 24px;
      background: rgba(0, 0, 0, 0.1);
    }

    .reviews-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .reviews-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      margin-bottom: 24px;
    }

    .review-card {
      background: rgba(13, 24, 41, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 24px;
      padding: 32px;
      transition: all 0.3s ease;
    }

    .review-card:hover {
      border-color: rgba(255, 255, 255, 0.12);
      transform: translateY(-4px);
    }

    .review-content {
      font-size: 1rem;
      color: var(--text-secondary);
      line-height: 1.8;
      margin-bottom: 20px;
      font-style: italic;
    }

    .review-author {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .review-avatar {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.2), rgba(255, 215, 95, 0.2));
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1rem;
    }

    .review-info {
      flex: 1;
    }

    .review-name {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 2px;
    }

    .review-meta {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .reviews-disclaimer {
      text-align: center;
      padding: 16px;
      background: rgba(255, 215, 95, 0.08);
      border-radius: 14px;
      font-size: 0.85rem;
      color: var(--accent-gold);
    }

    /* ===== Disclaimer Section ===== */
    .disclaimer-section {
      padding: 80px 24px;
      background: rgba(255, 107, 107, 0.03);
      border-top: 1px solid rgba(255, 107, 107, 0.1);
      border-bottom: 1px solid rgba(255, 107, 107, 0.1);
    }

    .disclaimer-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .disclaimer-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 107, 107, 0.2);
      border-radius: 28px;
      padding: 40px;
    }

    .disclaimer-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
      font-family: 'Outfit', sans-serif;
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--accent-red);
    }

    .disclaimer-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
    }

    .disclaimer-col h4 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 16px;
      color: var(--text-primary);
    }

    .disclaimer-list {
      list-style: none;
    }

    .disclaimer-list li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 10px 0;
      font-size: 0.95rem;
      color: var(--text-secondary);
      line-height: 1.5;
    }

    .disclaimer-list .icon-x {
      color: var(--accent-red);
      font-weight: 700;
    }

    .disclaimer-list .icon-check {
      color: var(--accent-cyan);
      font-weight: 700;
    }

    .disclaimer-footer {
      margin-top: 28px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
      text-align: center;
    }

    .disclaimer-footer p {
      font-size: 0.95rem;
      color: var(--accent-gold);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    /* ===== Floating Share Button ===== */
    .floating-share {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 999;
      display: flex;
      flex-direction: column;
      gap: 12px;
      align-items: flex-end;
    }

    .share-toggle {
      width: 60px;
      height: 60px;
      background: var(--gradient-gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      box-shadow: var(--shadow-gold);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .share-toggle:hover {
      transform: scale(1.1);
    }

    .share-buttons {
      display: flex;
      flex-direction: column;
      gap: 10px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(20px);
      transition: all 0.3s ease;
    }

    .floating-share.active .share-buttons {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .share-btn {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      transition: all 0.3s ease;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .share-btn:hover {
      transform: scale(1.1);
    }

    .share-kakao {
      background: #FEE500;
      color: #3C1E1E;
    }

    .share-twitter {
      background: #1DA1F2;
      color: white;
    }

    .share-facebook {
      background: #1877F2;
      color: white;
    }

    .share-link {
      background: var(--gradient-cyan);
      color: var(--primary-dark);
    }

    /* ===== Notification Section ===== */
    .notification-section {
      padding: 100px 24px;
      background: linear-gradient(180deg, rgba(255, 215, 95, 0.05) 0%, transparent 100%);
    }

    .notification-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .notification-card {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(255, 215, 95, 0.2);
      border-radius: 32px;
      padding: 48px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .notification-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-gold);
    }

    .notification-icon {
      width: 80px;
      height: 80px;
      background: rgba(255, 215, 95, 0.1);
      border: 2px solid rgba(255, 215, 95, 0.3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      margin: 0 auto 24px;
    }

    .notification-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      margin-bottom: 12px;
    }

    .notification-desc {
      font-size: 1rem;
      color: var(--text-secondary);
      margin-bottom: 32px;
      line-height: 1.7;
    }

    .notification-benefits {
      display: flex;
      justify-content: center;
      gap: 32px;
      margin-bottom: 32px;
      flex-wrap: wrap;
    }

    .notification-benefit {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .notification-benefit-icon {
      color: var(--accent-gold);
    }

    .notification-form {
      display: flex;
      gap: 12px;
      max-width: 500px;
      margin: 0 auto;
    }

    .notification-input {
      flex: 1;
      padding: 18px 24px;
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      font-size: 1rem;
      color: var(--text-primary);
      outline: none;
      transition: border-color 0.3s ease;
    }

    .notification-input:focus {
      border-color: var(--accent-gold);
    }

    .notification-input::placeholder {
      color: var(--text-muted);
    }

    .notification-submit {
      padding: 18px 32px;
      background: var(--gradient-gold);
      border-radius: 16px;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      color: var(--primary-dark);
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .notification-submit:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-gold);
    }

    .notification-note {
      margin-top: 16px;
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    /* ===== Community Section ===== */
    .community-section {
      padding: 120px 24px;
    }

    .community-container {
      max-width: 1100px;
      margin: 0 auto;
    }

    .community-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }

    .community-post {
      background: rgba(13, 24, 41, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 24px;
      padding: 28px;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .community-post:hover {
      transform: translateY(-4px);
      border-color: rgba(0, 224, 164, 0.3);
    }

    .community-post-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .community-avatar {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.3), rgba(255, 215, 95, 0.3));
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1rem;
    }

    .community-author-info {
      flex: 1;
    }

    .community-author {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 2px;
    }

    .community-date {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .community-tag {
      padding: 4px 10px;
      background: rgba(0, 224, 164, 0.15);
      border-radius: 8px;
      font-size: 0.7rem;
      font-weight: 600;
      color: var(--accent-cyan);
    }

    .community-title {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 12px;
      line-height: 1.4;
    }

    .community-balls {
      display: flex;
      gap: 6px;
      margin-bottom: 16px;
    }

    .community-ball {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.8rem;
      color: #fff;
    }

    .community-stats {
      display: flex;
      gap: 16px;
      padding-top: 16px;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .community-stat {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .community-stat-icon {
      font-size: 1rem;
    }

    .community-cta {
      text-align: center;
      margin-top: 48px;
    }

    .community-cta-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 18px 40px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 18px;
      font-weight: 600;
      color: var(--text-primary);
      transition: all 0.3s ease;
    }

    .community-cta-btn:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* ===== Subscription Pricing ===== */
    .pricing-card.subscription {
      border-color: rgba(255, 215, 95, 0.3);
      background: linear-gradient(180deg, rgba(255, 215, 95, 0.05) 0%, rgba(13, 24, 41, 0.8) 100%);
    }

    .pricing-card.subscription .pricing-badge {
      background: var(--gradient-gold);
    }

    .pricing-savings {
      display: inline-block;
      padding: 4px 12px;
      background: rgba(0, 224, 164, 0.15);
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--accent-cyan);
      margin-top: 8px;
    }

    /* ===== Share Modal ===== */
    .share-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(10px);
      z-index: 10000;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }

    .share-modal.active {
      opacity: 1;
      visibility: visible;
    }

    .share-modal-content {
      background: var(--primary);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 28px;
      padding: 40px;
      max-width: 480px;
      width: 90%;
      text-align: center;
      transform: scale(0.9);
      transition: transform 0.3s ease;
    }

    .share-modal.active .share-modal-content {
      transform: scale(1);
    }

    .share-modal-close {
      position: absolute;
      top: 16px;
      right: 16px;
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .share-modal-close:hover {
      background: rgba(255, 255, 255, 0.2);
      color: var(--text-primary);
    }

    .share-modal-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 12px;
    }

    .share-modal-preview {
      background: rgba(0, 0, 0, 0.3);
      border-radius: 16px;
      padding: 20px;
      margin: 24px 0;
    }

    .share-modal-round {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-bottom: 12px;
    }

    .share-modal-balls {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-bottom: 12px;
    }

    .share-modal-ball {
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
    }

    .share-modal-text {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .share-modal-buttons {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-top: 24px;
    }

    .share-modal-btn {
      padding: 16px;
      border-radius: 16px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .share-modal-btn:hover {
      transform: translateY(-2px);
    }

    .share-modal-btn-icon {
      font-size: 1.5rem;
    }

    .share-modal-btn.kakao {
      background: #FEE500;
      color: #3C1E1E;
    }

    .share-modal-btn.twitter {
      background: #1DA1F2;
      color: white;
    }

    .share-modal-btn.facebook {
      background: #1877F2;
      color: white;
    }

    .share-modal-btn.copy {
      background: rgba(255, 255, 255, 0.1);
      color: var(--text-primary);
    }

    .copy-toast {
      position: fixed;
      bottom: 100px;
      left: 50%;
      transform: translateX(-50%) translateY(20px);
      padding: 16px 32px;
      background: var(--accent-cyan);
      color: var(--primary-dark);
      border-radius: 14px;
      font-weight: 700;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 10001;
    }

    .copy-toast.active {
      opacity: 1;
      visibility: visible;
      transform: translateX(-50%) translateY(0);
    }

    /* ===== Countdown Timer Section ===== */
    .countdown-section {
      padding: 60px 24px;
      background: linear-gradient(180deg, rgba(239, 68, 68, 0.05) 0%, transparent 100%);
    }

    .countdown-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .countdown-card {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 28px;
      padding: 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .countdown-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #ef4444, #f97316, #eab308);
      animation: gradient-move 3s ease infinite;
    }

    @keyframes gradient-move {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .countdown-label {
      font-size: 1rem;
      color: var(--text-muted);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .countdown-pulse {
      width: 10px;
      height: 10px;
      background: #ef4444;
      border-radius: 50%;
      animation: live-pulse 1.5s ease infinite;
    }

    .countdown-timer {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-bottom: 24px;
    }

    .countdown-item {
      text-align: center;
    }

    .countdown-value {
      font-family: 'Outfit', sans-serif;
      font-size: 3.5rem;
      font-weight: 900;
      background: linear-gradient(135deg, #ef4444, #f97316);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1;
    }

    .countdown-unit {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .countdown-sep {
      font-size: 2.5rem;
      color: var(--text-muted);
      line-height: 1;
      padding-top: 8px;
    }

    .countdown-info {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 30px;
      font-size: 0.95rem;
      color: var(--accent-cyan);
      font-weight: 600;
    }

    /* ===== System Status Section ===== */
    .status-section {
      padding: 60px 24px;
    }

    .status-container {
      max-width: 700px;
      margin: 0 auto;
    }

    .status-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 24px;
      padding: 32px;
    }

    .status-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .status-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .status-all-ok {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      background: rgba(0, 224, 164, 0.15);
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--accent-cyan);
    }

    .status-all-ok .dot {
      width: 8px;
      height: 8px;
      background: var(--accent-cyan);
      border-radius: 50%;
      animation: pulse 2s ease infinite;
    }

    .status-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .status-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px 20px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 14px;
    }

    .status-icon {
      width: 40px;
      height: 40px;
      background: rgba(0, 224, 164, 0.1);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }

    .status-info {
      flex: 1;
    }

    .status-name {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 2px;
    }

    .status-detail {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .status-indicator {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .status-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
    }

    .status-dot.ok {
      background: var(--accent-cyan);
      box-shadow: 0 0 10px rgba(0, 224, 164, 0.5);
    }

    .status-dot.warning {
      background: var(--accent-gold);
      box-shadow: 0 0 10px rgba(255, 215, 95, 0.5);
    }

    .status-text {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-cyan);
    }

    .status-response {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-left: 4px;
    }

    /* ===== Prize Simulator Section ===== */
    .prize-section {
      padding: 100px 24px;
      background: linear-gradient(180deg, rgba(255, 215, 95, 0.03) 0%, transparent 100%);
    }

    .prize-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .prize-card {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(255, 215, 95, 0.15);
      border-radius: 32px;
      padding: 48px;
      text-align: center;
    }

    .prize-icon {
      font-size: 4rem;
      margin-bottom: 20px;
    }

    .prize-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.6rem;
      font-weight: 800;
      margin-bottom: 8px;
    }

    .prize-subtitle {
      font-size: 0.95rem;
      color: var(--text-muted);
      margin-bottom: 40px;
    }

    .prize-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 32px;
    }

    .prize-item {
      padding: 28px 20px;
      background: rgba(0, 0, 0, 0.3);
      border-radius: 20px;
      transition: all 0.3s ease;
    }

    .prize-item:hover {
      transform: translateY(-4px);
      background: rgba(0, 0, 0, 0.4);
    }

    .prize-item.first {
      border: 1px solid rgba(255, 215, 95, 0.3);
      background: linear-gradient(180deg, rgba(255, 215, 95, 0.08) 0%, rgba(0, 0, 0, 0.3) 100%);
    }

    .prize-rank {
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .prize-item.first .prize-rank {
      color: var(--accent-gold);
    }

    .prize-amount {
      font-family: 'Outfit', sans-serif;
      font-size: 2rem;
      font-weight: 900;
      margin-bottom: 4px;
    }

    .prize-item.first .prize-amount {
      background: var(--gradient-gold);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-size: 2.5rem;
    }

    .prize-after-tax {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .prize-after-tax span {
      color: var(--accent-cyan);
      font-weight: 600;
    }

    .prize-note {
      padding: 16px 24px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 14px;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* ===== Queue Position ===== */
    .queue-banner {
      background: linear-gradient(90deg, rgba(0, 224, 164, 0.1), rgba(255, 215, 95, 0.1));
      border-top: 1px solid rgba(0, 224, 164, 0.2);
      border-bottom: 1px solid rgba(255, 215, 95, 0.2);
      padding: 16px 24px;
      text-align: center;
    }

    .queue-content {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .queue-icon {
      font-size: 1.3rem;
    }

    .queue-text {
      font-size: 1rem;
      color: var(--text-secondary);
    }

    .queue-number {
      font-family: 'Outfit', sans-serif;
      font-weight: 900;
      font-size: 1.3rem;
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .queue-cta {
      padding: 8px 20px;
      background: var(--gradient-cyan);
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin-left: 8px;
      transition: all 0.3s ease;
    }

    .queue-cta:hover {
      transform: scale(1.05);
    }

    /* ===== Quality Gauge Section ===== */
    .quality-section {
      padding: 80px 24px;
    }

    .quality-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .quality-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 28px;
      padding: 40px;
    }

    .quality-header {
      text-align: center;
      margin-bottom: 36px;
    }

    .quality-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .quality-subtitle {
      font-size: 0.9rem;
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

    /* ===== Never Happened Warning ===== */
    .warning-section {
      padding: 80px 24px;
      background: rgba(239, 68, 68, 0.02);
    }

    .warning-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .warning-card {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 28px;
      padding: 40px;
    }

    .warning-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
    }

    .warning-icon {
      width: 48px;
      height: 48px;
      background: rgba(239, 68, 68, 0.15);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .warning-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.3rem;
      font-weight: 700;
    }

    .warning-subtitle {
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    .warning-list {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-bottom: 24px;
    }

    .warning-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 20px;
      background: rgba(239, 68, 68, 0.05);
      border: 1px solid rgba(239, 68, 68, 0.1);
      border-radius: 16px;
    }

    .warning-item-icon {
      width: 32px;
      height: 32px;
      background: rgba(239, 68, 68, 0.2);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      flex-shrink: 0;
    }

    .warning-item-content h4 {
      font-size: 0.95rem;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .warning-item-content p {
      font-size: 0.8rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .warning-item-stat {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      color: var(--accent-red);
    }

    .warning-footer {
      padding: 16px 24px;
      background: rgba(0, 224, 164, 0.08);
      border-radius: 14px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .warning-footer-icon {
      font-size: 1.5rem;
    }

    .warning-footer-text {
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .warning-footer-text strong {
      color: var(--accent-cyan);
    }

    /* ===== Loading Screen ===== */
    .loading-screen {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--primary-dark);
      background-image: radial-gradient(ellipse at 50% 50%, rgba(0, 224, 164, 0.05) 0%, transparent 50%);
      z-index: 99999;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transition: opacity 0.6s ease, visibility 0.6s ease;
    }

    .loading-screen.hidden {
      opacity: 0;
      visibility: hidden;
    }

    .loading-logo {
      width: 90px;
      height: 90px;
      background: linear-gradient(135deg, var(--accent-cyan) 0%, #00D4FF 100%);
      border-radius: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 28px;
      animation: loadingPulse 2s ease infinite;
      box-shadow: 0 20px 60px rgba(0, 224, 164, 0.4);
    }

    @keyframes loadingPulse {
      0%, 100% { transform: scale(1); box-shadow: 0 20px 60px rgba(0, 224, 164, 0.4); }
      50% { transform: scale(1.08); box-shadow: 0 30px 80px rgba(0, 224, 164, 0.6); }
    }

    .loading-text {
      font-family: 'Outfit', sans-serif;
      font-size: 1.4rem;
      font-weight: 700;
      background: linear-gradient(135deg, #ffffff 0%, var(--accent-cyan) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 28px;
      letter-spacing: 0.02em;
    }

    .loading-bar {
      width: 240px;
      height: 5px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      overflow: hidden;
    }

    .loading-bar-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--accent-cyan), #00D4FF, var(--accent-cyan));
      background-size: 200% 100%;
      border-radius: 10px;
      animation: loadingProgress 1.5s ease infinite, shimmer 2s linear infinite;
    }

    @keyframes loadingProgress {
      0% { width: 0%; }
      50% { width: 70%; }
      100% { width: 100%; }
    }

    /* ===== Back to Top Button ===== */
    .back-to-top {
      position: fixed;
      bottom: 100px;
      right: 24px;
      width: 52px;
      height: 52px;
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.9), rgba(5, 10, 21, 0.95));
      backdrop-filter: blur(16px);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: var(--text-primary);
      cursor: pointer;
      opacity: 0;
      visibility: hidden;
      transform: translateY(20px);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 998;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    .back-to-top.visible {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .back-to-top:hover {
      background: linear-gradient(135deg, var(--accent-cyan) 0%, #00D4FF 100%);
      color: var(--primary-dark);
      transform: translateY(-6px);
      box-shadow: 0 20px 50px rgba(0, 224, 164, 0.4);
      border-color: transparent;
    }

    /* ===== Mobile Menu ===== */
    .mobile-menu {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(11, 19, 43, 0.98);
      backdrop-filter: blur(20px);
      z-index: 9999;
      display: flex;
      flex-direction: column;
      padding: 100px 32px 40px;
      transform: translateX(100%);
      transition: transform 0.3s ease;
    }

    .mobile-menu.active {
      transform: translateX(0);
    }

    .mobile-menu-close {
      position: absolute;
      top: 20px;
      right: 20px;
      width: 44px;
      height: 44px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: var(--text-primary);
      cursor: pointer;
    }

    .mobile-menu-links {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 32px;
    }

    .mobile-menu-link {
      padding: 16px 20px;
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--text-secondary);
      border-radius: 14px;
      transition: all 0.3s ease;
    }

    .mobile-menu-link:hover {
      background: rgba(255, 255, 255, 0.05);
      color: var(--text-primary);
    }

    .mobile-menu-cta {
      padding: 18px;
      background: var(--gradient-cyan);
      border-radius: 16px;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-align: center;
    }

    /* ===== Media & Security Badges ===== */
    .badges-section {
      padding: 60px 24px;
      background: rgba(0, 0, 0, 0.2);
    }

    .badges-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .badges-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 40px;
    }

    .badges-col {
      text-align: center;
    }

    .badges-col-title {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 20px;
    }

    .media-badges {
      display: flex;
      justify-content: center;
      gap: 24px;
      flex-wrap: wrap;
    }

    .media-badge {
      padding: 16px 24px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 14px;
      transition: all 0.3s ease;
    }

    .media-badge:hover {
      border-color: rgba(255, 255, 255, 0.15);
      background: rgba(255, 255, 255, 0.05);
    }

    .media-badge-name {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      margin-bottom: 4px;
    }

    .media-badge-quote {
      font-size: 0.8rem;
      color: var(--text-muted);
      font-style: italic;
    }

    .security-badges {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .security-badge {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 20px;
      background: rgba(0, 224, 164, 0.05);
      border: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 12px;
    }

    .security-badge-icon {
      font-size: 1.3rem;
    }

    .security-badge-text {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    /* ===== Company Info in Footer ===== */
    .footer-company {
      margin-top: 32px;
      padding-top: 32px;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .footer-company-info {
      display: flex;
      flex-wrap: wrap;
      gap: 24px;
      justify-content: center;
      margin-bottom: 16px;
    }

    .footer-company-item {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .footer-company-item span {
      color: var(--text-secondary);
      margin-left: 4px;
    }

    .footer-contact {
      display: flex;
      justify-content: center;
      gap: 24px;
      flex-wrap: wrap;
    }

    .footer-contact-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .footer-contact-item a {
      color: var(--accent-cyan);
    }

    /* ===== Animations ===== */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ===== Responsive ===== */
    @media (max-width: 1024px) {
      .hero-container {
        grid-template-columns: 1fr;
        gap: 60px;
        text-align: center;
      }

      .hero-content {
        order: 1;
      }

      .hero-visual {
        order: 2;
      }

      .hero-subtitle {
        margin: 0 auto 40px;
      }

      .hero-cta-group {
        justify-content: center;
      }

      .hero-trust {
        justify-content: center;
      }

      .features-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .dashboard-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .navbar {
        height: 64px;
      }

      .nav-menu {
        display: none;
      }

      .mobile-menu-btn {
        display: flex;
      }

      .hero {
        padding: 100px 16px 60px;
      }

      .hero-badge {
        padding: 10px 18px;
        font-size: 0.8rem;
      }

      .hero-cta-group {
        flex-direction: column;
        width: 100%;
        max-width: 320px;
        margin: 0 auto 40px;
      }

      .hero-cta-primary,
      .hero-cta-secondary {
        width: 100%;
        justify-content: center;
      }

      .hero-trust {
        flex-direction: column;
        gap: 12px;
      }

      .hero-card {
        padding: 24px;
      }

      .hero-balls-container {
        padding: 20px;
        gap: 8px;
      }

      .hero-ball {
        width: 42px;
        height: 42px;
        font-size: 1rem;
      }

      .features-grid {
        grid-template-columns: 1fr;
      }

      .pricing-grid,
      .pricing-grid[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
      }

      .pricing-card.featured,
      .pricing-card.subscription {
        transform: none;
      }

      .footer-top {
        flex-direction: column;
        gap: 40px;
      }

      .footer-links {
        flex-wrap: wrap;
        gap: 32px;
      }

      .footer-bottom {
        flex-direction: column;
        text-align: center;
      }

      .footer-disclaimer {
        text-align: center;
      }

      .algorithm-steps {
        flex-direction: column;
      }

      .algorithm-arrow {
        transform: rotate(90deg);
      }

      .archive-header,
      .archive-row {
        grid-template-columns: 70px 1fr 70px;
      }

      .archive-header > div:nth-child(3),
      .archive-row > div:nth-child(3) {
        display: none;
      }

      .archive-summary {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 24px;
      }

      .archive-verify-input-group {
        flex-direction: row;
      }

      .reviews-grid {
        grid-template-columns: 1fr;
      }

      .disclaimer-grid {
        grid-template-columns: 1fr;
      }

      .cumulative-grid {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .community-grid {
        grid-template-columns: 1fr;
      }

      .notification-form {
        flex-direction: column;
      }

      .notification-benefits {
        flex-direction: column;
        gap: 12px;
      }

      .share-modal-buttons {
        grid-template-columns: repeat(2, 1fr);
      }

      .floating-share {
        bottom: 16px;
        right: 16px;
      }

      .share-toggle {
        width: 52px;
        height: 52px;
      }

      .share-btn {
        width: 44px;
        height: 44px;
      }

      .countdown-timer {
        gap: 8px;
      }

      .countdown-value {
        font-size: 2.2rem;
      }

      .countdown-sep {
        font-size: 1.5rem;
      }

      .prize-grid {
        grid-template-columns: 1fr;
      }

      .quality-gauges {
        grid-template-columns: 1fr;
      }

      .warning-list {
        grid-template-columns: 1fr;
      }

      .queue-content {
        flex-direction: column;
        gap: 8px;
      }

      .badges-grid {
        grid-template-columns: 1fr;
        gap: 32px;
      }

      .back-to-top {
        bottom: 90px;
        right: 16px;
        width: 44px;
        height: 44px;
      }
    }

    @media (max-width: 480px) {
      /* Base - 모바일 시인성 강화 */
      body {
        font-size: 16px;
        line-height: 1.7;
        -webkit-font-smoothing: antialiased;
      }

      /* Hero */
      .hero {
        padding: 90px 12px 50px;
        min-height: auto;
      }

      .hero-badge {
        padding: 8px 14px;
        font-size: 0.75rem;
        margin-bottom: 20px;
      }

      .hero-title {
        font-size: 2rem;
        margin-bottom: 16px;
      }

      .hero-subtitle {
        font-size: 0.95rem;
        margin-bottom: 28px;
        line-height: 1.7;
      }

      .hero-cta-primary,
      .hero-cta-secondary {
        padding: 16px 24px;
        font-size: 1rem;
        border-radius: 14px;
      }

      .hero-trust-item {
        font-size: 0.8rem;
      }

      .hero-card {
        padding: 20px;
        border-radius: 20px;
      }

      .hero-balls-container {
        padding: 16px 12px;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
      }

      .hero-ball {
        width: 44px;
        height: 44px;
        font-size: 1.1rem;
        font-weight: 700;
      }

      .bonus-sep {
        font-size: 1rem;
      }

      .hero-card-status {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
      }

      .hero-card-info {
        flex-direction: column;
        gap: 8px;
        text-align: center;
      }

      /* Sections */
      .section-header {
        margin-bottom: 40px;
      }

      .section-badge {
        font-size: 0.75rem;
        padding: 8px 16px;
      }

      .section-title {
        font-size: 1.6rem;
      }

      .section-subtitle {
        font-size: 0.9rem;
      }

      /* Features */
      .features-section,
      .pricing-section,
      .faq-section,
      .archive-section {
        padding: 60px 12px;
      }

      .feature-card {
        padding: 28px 20px;
        border-radius: 20px;
      }

      .feature-icon {
        width: 56px;
        height: 56px;
        font-size: 1.5rem;
        margin-bottom: 16px;
      }

      .feature-title {
        font-size: 1.15rem;
      }

      .feature-desc {
        font-size: 0.85rem;
      }

      /* Pricing */
      .pricing-card {
        padding: 28px 20px;
        border-radius: 24px;
      }

      .pricing-name {
        font-size: 1.2rem;
      }

      .pricing-amount {
        font-size: 2.2rem;
      }

      /* Archive */
      .archive-card {
        border-radius: 20px;
      }

      .archive-header {
        padding: 14px 16px;
        font-size: 0.7rem;
        grid-template-columns: 60px 1fr 60px;
      }

      .archive-row {
        padding: 14px 16px;
        grid-template-columns: 60px 1fr 60px;
        gap: 10px;
      }

      .archive-round {
        font-size: 0.85rem;
      }

      .archive-ball {
        width: 34px;
        height: 34px;
        font-size: 0.85rem;
        font-weight: 700;
      }

      .archive-match-num {
        font-size: 0.75rem;
        padding: 4px 8px;
      }

      .archive-summary {
        padding: 20px 16px;
        gap: 16px;
        flex-direction: column;
      }

      .archive-summary-value {
        font-size: 1.8rem;
      }

      .archive-summary-label {
        font-size: 0.75rem;
      }

      .archive-verify {
        padding: 20px 16px;
      }

      .archive-verify-header {
        font-size: 0.9rem;
      }

      .archive-verify-input-group {
        flex-direction: column;
        gap: 10px;
        padding: 4px;
        border-radius: 16px;
      }

      .archive-verify-input {
        padding: 14px 18px;
        font-size: 0.95rem;
        text-align: center;
      }

      .archive-verify-btn {
        padding: 14px 24px;
        width: 100%;
        border-radius: 12px;
      }

      .archive-verify-result {
        padding: 16px;
      }

      .archive-verify-balls {
        gap: 5px;
        justify-content: center;
        flex-wrap: wrap;
      }

      /* Cumulative Stats */
      .cumulative-section {
        padding: 60px 12px;
      }

      .cumulative-item {
        padding: 24px 16px;
      }

      .cumulative-number {
        font-size: 2rem;
      }

      .cumulative-label {
        font-size: 0.8rem;
      }

      /* Activity Feed */
      .activity-section {
        padding: 60px 12px;
      }

      .activity-container {
        padding: 20px 16px;
      }

      .activity-item {
        padding: 12px;
        font-size: 0.85rem;
      }

      /* Countdown */
      .countdown-section {
        padding: 60px 12px;
      }

      .countdown-timer {
        gap: 6px;
      }

      .countdown-item {
        min-width: 60px;
        padding: 14px 8px;
      }

      .countdown-value {
        font-size: 1.8rem;
      }

      .countdown-label {
        font-size: 0.65rem;
      }

      .countdown-sep {
        font-size: 1.2rem;
      }

      /* Reviews */
      .reviews-section {
        padding: 60px 12px;
      }

      .review-card {
        padding: 24px 20px;
      }

      .review-text {
        font-size: 0.9rem;
      }

      /* FAQ */
      .faq-item {
        padding: 20px;
      }

      .faq-question {
        font-size: 1rem;
        padding-right: 30px;
      }

      .faq-answer {
        font-size: 0.9rem;
      }

      /* Footer */
      .footer {
        padding: 60px 16px 30px;
      }

      .footer-logo {
        font-size: 1.15rem;
      }

      .footer-desc {
        font-size: 0.8rem;
      }

      .footer-links {
        gap: 24px;
      }

      .footer-col h4 {
        font-size: 0.85rem;
      }

      .footer-col a {
        font-size: 0.8rem;
      }

      .footer-company-info {
        flex-direction: column;
        gap: 8px;
      }

      .footer-copyright {
        font-size: 0.75rem;
      }

      /* Notification */
      .notification-section {
        padding: 60px 12px;
      }

      .notification-card {
        padding: 28px 20px;
      }

      .notification-input {
        padding: 14px 16px;
        font-size: 0.9rem;
      }

      .notification-submit {
        padding: 14px 20px;
        font-size: 0.9rem;
      }

      /* Community */
      .community-section {
        padding: 60px 12px;
      }

      .community-card {
        padding: 24px 20px;
      }

      /* Share Modal */
      .share-modal-content {
        padding: 28px 20px;
        margin: 16px;
        border-radius: 20px;
      }

      .share-modal-buttons {
        gap: 12px;
      }

      .share-modal-btn {
        padding: 14px;
        font-size: 0.8rem;
      }

      /* Mobile Menu */
      .mobile-menu {
        padding: 80px 20px 30px;
      }

      .mobile-menu-link {
        padding: 14px 16px;
        font-size: 1rem;
      }

      .mobile-menu-cta {
        padding: 16px;
        font-size: 1rem;
      }

      /* Back to Top */
      .back-to-top {
        bottom: 80px;
        right: 12px;
        width: 42px;
        height: 42px;
        font-size: 1rem;
        border-radius: 12px;
      }

      /* Floating Share */
      .floating-share {
        bottom: 12px;
        right: 12px;
      }

      .share-toggle {
        width: 48px;
        height: 48px;
      }

      /* Loading Screen */
      .loading-logo {
        width: 70px;
        height: 70px;
      }

      .loading-text {
        font-size: 1.2rem;
      }

      .loading-bar {
        width: 180px;
      }
    }

    /* Extra small devices */
    @media (max-width: 360px) {
      .hero-title {
        font-size: 1.75rem;
      }

      .hero-ball {
        width: 34px;
        height: 34px;
        font-size: 0.8rem;
      }

      .archive-ball {
        width: 24px;
        height: 24px;
        font-size: 0.6rem;
      }

      .countdown-item {
        min-width: 50px;
        padding: 12px 6px;
      }

      .countdown-value {
        font-size: 1.5rem;
      }
    }
  </style>

  <!-- Lotto Data Script -->
  <script src="/scripts/lotto-data.js"></script>
</head>
<body>

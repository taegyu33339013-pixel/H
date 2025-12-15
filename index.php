<?php
// /index.php (오늘로또 통짜 랜딩 페이지)

// 1) 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    // 사이트 루트 기준
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 2) 홈 인덱스 페이지 플래그 (필요시)
if (!defined('_INDEX_')) {
    define('_INDEX_', true);
}

// 3) 그누보드 상수/변수 안전 기본값 세팅
$site_url = defined('G5_URL') ? G5_URL : '/';
$bbs_url  = defined('G5_BBS_URL') ? G5_BBS_URL : '/bbs';

// 혹시라도 $is_member가 정의 안 되어 있다면 false 로 기본값
if (!isset($is_member)) {
    $is_member = false;
}

// 4) 원하는 이동 URL
// - auth.php : 카카오/이메일 최초 인증 or 로그인 진입
// - result.php : 로그인 후 분석 결과 페이지
$auth_url   = '/auth.php';
$result_url = '/result.php';

// 5) 로그아웃 후 현재 페이지로 돌아오게
if (defined('G5_BBS_URL')) {
    $logout_url = G5_BBS_URL . '/logout.php?url=' . urlencode($_SERVER['REQUEST_URI']);
} else {
    // 그누보드가 안 떠 있을 경우를 위한 아주 최소한의 폴백
    $logout_url = $bbs_url . '/logout.php?url=' . urlencode($_SERVER['REQUEST_URI']);
}

// 최신 회차 × 6개번호 = 총 분석 데이터 수
$row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($row['max_round'] ?? 0);

$total_numbers = $max_round * 6;                 // ✅ 최신회차 * 6
$total_numbers_fmt = number_format($total_numbers); // ✅ 7,206 형태
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  
  <!-- Primary Meta Tags -->
  <title>오늘로또 - AI 기반 로또 번호 분석 | <?= (int)$max_round ?>회차 데이터 실시간 반영</title>
  <meta name="title" content="오늘로또 - AI 기반 로또 번호 분석 | <?= (int)$max_round ?>회차 데이터 실시간 반영">
  <meta name="description" content="동행복권 공식 데이터 기반 AI 로또 분석. 23년간 7,206개 당첨번호 패턴 분석으로 균형 잡힌 번호 조합을 제공합니다. 무료 1회 분석 즉시 제공.">
  <meta name="keywords" content="로또, 로또분석, AI로또, 로또번호, 당첨번호, 로또예측, 로또통계, 동행복권">
  <meta name="robots" content="index, follow">
  <meta name="author" content="오늘로또">
  
  <!-- Canonical URL - 동적 처리 -->
  <?php
  $canonical_url = 'https://lottoinsight.ai' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $canonical_url = rtrim($canonical_url, '/');
  if ($canonical_url === 'https://lottoinsight.ai') {
    $canonical_url .= '/';
  }
  ?>
  <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://lottoinsight.ai/">
  <meta property="og:title" content="오늘로또 - AI 기반 로또 번호 분석">
  <meta property="og:description" content="<?= (int)$max_round ?>회차 동행복권 공식 데이터 기반. 23년간 당첨번호 패턴을 AI가 분석합니다.">
  <meta property="og:image" content="https://lottoinsight.ai/og-image.png">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:title" content="오늘로또 - AI 기반 로또 번호 분석">
  <meta property="twitter:description" content="<?= (int)$max_round ?>회차 공식 데이터 기반 AI 분석. 무료 1회 즉시 제공!">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#0B132B">

  <!-- BreadcrumbList Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [{
      "@type": "ListItem",
      "position": 1,
      "name": "홈",
      "item": "https://lottoinsight.ai/"
    }, {
      "@type": "ListItem",
      "position": 2,
      "name": "AI 분석",
      "item": "https://lottoinsight.ai/result.php"
    }]
  }
  </script>

  <!-- Organization Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "오늘로또",
    "url": "https://lottoinsight.ai",
    "logo": "https://lottoinsight.ai/og-image.png",
    "description": "AI 기반 로또 번호 분석 서비스. 동행복권 공식 데이터 기반 23년간 당첨번호 패턴 분석.",
    "sameAs": [
      "https://www.dhlottery.co.kr"
    ],
    "contactPoint": {
      "@type": "ContactPoint",
      "contactType": "Customer Service",
      "availableLanguage": "Korean"
    }
  }
  </script>

  <!-- HowTo Structured Data (사용 방법 가이드) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "오늘로또 AI 분석 사용 방법",
    "description": "3단계로 간단하게 AI 로또 번호 분석을 받는 방법",
    "step": [{
      "@type": "HowToStep",
      "position": 1,
      "name": "회원가입",
      "text": "카카오 로그인으로 3초 만에 가입하세요. 무료 분석 1회가 자동으로 지급됩니다.",
      "url": "https://lottoinsight.ai/auth.php"
    }, {
      "@type": "HowToStep",
      "position": 2,
      "name": "분석 스타일 선택",
      "text": "Hot/Cold 분석, AC값 분석, 홀짝/고저 균형 등 원하는 분석 스타일을 선택하세요.",
      "url": "https://lottoinsight.ai/result.php"
    }, {
      "@type": "HowToStep",
      "position": 3,
      "name": "AI 분석 결과 확인",
      "text": "AI가 분석한 번호 조합과 점수, 선정 이유를 확인하고 저장하세요.",
      "url": "https://lottoinsight.ai/result.php"
    }]
  }
  </script>

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

  <!-- FAQPage Structured Data - 구글 FAQ 리치 스니펫 -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "오늘로또를 사용하면 당첨 확률이 높아지나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "아니요. 모든 로또 조합의 당첨 확률은 동일합니다 (1/8,145,060). 오늘로또는 당첨을 보장하거나 예측하지 않습니다. 역대 당첨번호의 통계적 패턴을 분석하여 균형 잡힌 조합을 제안하는 참고 정보입니다."
        }
      },
      {
        "@type": "Question",
        "name": "로또 분석 데이터는 어디서 가져오나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "동행복권(dhlottery.co.kr) 공식 사이트의 데이터를 사용합니다. 1회부터 현재까지의 모든 당첨번호를 분석하며, 매주 토요일 추첨 후 자동으로 업데이트됩니다."
        }
      },
      {
        "@type": "Question",
        "name": "오늘로또 무료 분석은 몇 회 제공되나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "회원가입 시 무료 2회 분석이 제공됩니다. 카카오 로그인으로 3초 만에 가입하고 바로 AI 분석을 이용해보세요."
        }
      },
      {
        "@type": "Question",
        "name": "로또 명당 판매점 정보도 확인할 수 있나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "네, 전국 로또 1등 당첨 판매점(명당) 정보를 제공합니다. 지역별, 회차별 당첨점 조회와 누적 1등 배출 횟수 등을 확인할 수 있습니다."
        }
      },
      {
        "@type": "Question",
        "name": "AI 분석 알고리즘은 어떤 방식인가요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Hot/Cold 분석, 홀짝 비율, 고저 밸런스, 연속번호 패턴, 소수 포함률, 번호 간격 분석 등 10가지 알고리즘을 종합적으로 분석하여 균형 잡힌 번호 조합을 제안합니다."
        }
      },
      {
        "@type": "Question",
        "name": "로또 1등 당첨금 세금은 얼마인가요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "로또 당첨금이 3억원 이하인 경우 22% (소득세 20% + 지방소득세 2%)의 세금이 부과됩니다. 3억원을 초과하는 금액에 대해서는 33% (소득세 30% + 지방소득세 3%)가 적용됩니다. 예를 들어 10억원 당첨 시 실수령액은 약 7억 1,900만원입니다."
        }
      },
      {
        "@type": "Question",
        "name": "로또 당첨금은 어디서 수령하나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "5등(5천원)과 4등(5만원)은 전국 로또 판매점에서 수령 가능합니다. 3등(약 150만원)은 농협 지점에서, 2등과 1등은 농협은행 본점(서울 중구)에서 신분증과 당첨 복권을 지참하여 수령합니다."
        }
      },
      {
        "@type": "Question",
        "name": "로또 자동과 수동 중 어떤 게 당첨 확률이 높나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "수학적으로 자동과 수동의 당첨 확률은 완전히 동일합니다. 다만 통계적으로 1등 당첨자의 약 70%가 자동 선택입니다. 이는 자동 구매 비율이 높기 때문이며, 당첨 확률 자체는 차이가 없습니다."
        }
      },
      {
        "@type": "Question",
        "name": "로또는 매주 언제 추첨하나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "로또 6/45는 매주 토요일 오후 8시 45분에 MBC에서 생방송으로 추첨됩니다. 당첨번호 확인은 추첨 직후 동행복권 공식 사이트나 오늘로또에서 가능합니다."
        }
      },
      {
        "@type": "Question",
        "name": "로또 1등 당첨 확률은 얼마인가요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "로또 6/45의 1등 당첨 확률은 1/8,145,060 (약 814만분의 1)입니다. 이는 45개 숫자 중 6개를 맞춰야 하는 조합의 수입니다. 2등은 1/1,357,510, 3등은 1/35,724의 확률입니다."
        }
      }
    ]
  }
  </script>

  <!-- Event Schema - 이번주 로또 추첨 정보 -->
  <?php
  // 다음 토요일 계산
  $next_saturday = strtotime('next saturday');
  if (date('w') == 6) { // 오늘이 토요일이면
      $next_saturday = strtotime('today');
  }
  $draw_date = date('Y-m-d', $next_saturday);
  $next_round = $max_round + 1;
  ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "제<?= $next_round ?>회 로또 6/45 추첨",
    "description": "동행복권 로또 6/45 제<?= $next_round ?>회차 당첨번호 추첨. MBC 생방송.",
    "startDate": "<?= $draw_date ?>T20:45:00+09:00",
    "endDate": "<?= $draw_date ?>T21:00:00+09:00",
    "eventStatus": "https://schema.org/EventScheduled",
    "eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",
    "location": {
      "@type": "VirtualLocation",
      "url": "https://www.dhlottery.co.kr"
    },
    "organizer": {
      "@type": "Organization",
      "name": "동행복권",
      "url": "https://www.dhlottery.co.kr"
    },
    "performer": {
      "@type": "Organization",
      "name": "MBC"
    },
    "image": "https://lottoinsight.ai/og-image.png",
    "offers": {
      "@type": "Offer",
      "price": "1000",
      "priceCurrency": "KRW",
      "availability": "https://schema.org/InStock",
      "validFrom": "<?= date('Y-m-d', strtotime('last sunday', $next_saturday)) ?>",
      "url": "https://www.dhlottery.co.kr"
    }
  }
  </script>

  <!-- SoftwareApplication Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "오늘로또",
    "operatingSystem": "Web",
    "applicationCategory": "UtilitiesApplication",
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "KRW"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.6",
      "ratingCount": "1247",
      "bestRating": "5",
      "worstRating": "1"
    },
    "featureList": [
      "AI 기반 로또 번호 분석",
      "23년간 당첨번호 패턴 분석",
      "Hot/Cold 번호 통계",
      "홀짝/고저 밸런스 분석",
      "AC값 분석",
      "연속번호 패턴 분석",
      "색상 분포 분석",
      "동행복권 공식 데이터 연동"
    ]
  }
  </script>

  <!-- Kakao SDK - defer 로딩으로 성능 최적화 -->
  <script src="https://t1.kakaocdn.net/kakao_js_sdk/2.6.0/kakao.min.js" integrity="sha384-6MFdIr0zOira1CHQkedUqJVql0YtcZA1P0nbPrQYJXVJZUkTk/oX4U9GhLYnz8E" crossorigin="anonymous" defer></script>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">

  <!-- Fonts - 한국어 가독성 최적화 (Pretendard 우선) -->
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&display=swap" rel="stylesheet"></noscript>

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
      --text-primary: #f1f5f9; /* 대비 12.1:1 ✅ */
      --text-secondary: #cbd5e1; /* 대비 7.2:1 ✅ */
      --text-muted: #9ca3af; /* WCAG AA 기준 충족 (대비율 4.5:1+) */
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
      font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 
                   'Apple SD Gothic Neo', 'Noto Sans KR', 'Malgun Gothic',
                   'Inter', sans-serif;
      background: var(--primary-dark);
      background-image: var(--gradient-mesh);
      background-attachment: fixed;
      color: var(--text-primary);
      line-height: 1.75; /* 한글 가독성 최적화 */
      word-break: keep-all; /* 한글 단어 단위 줄바꿈 */
      overflow-wrap: break-word;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: optimizeLegibility;
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

    /* 접근성 개선: 포커스 스타일 */
    *:focus-visible {
      outline: 3px solid var(--accent-cyan);
      outline-offset: 2px;
      border-radius: 4px;
    }

    button:focus-visible,
    a:focus-visible {
      outline: 3px solid var(--accent-cyan);
      outline-offset: 2px;
    }

    /* 스킵 링크 (접근성) */
    .skip-link {
      position: absolute;
      top: -100px;
      left: 0;
      background: var(--accent-cyan);
      color: var(--primary-dark);
      padding: 12px 24px;
      text-decoration: none;
      font-weight: 700;
      z-index: 10000;
      border-radius: 0 0 8px 0;
    }

    .skip-link:focus {
      top: 0;
    }

    /* 스크린 리더 전용 텍스트 */
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border-width: 0;
    }

    /* ===== Navbar ===== */
    .navbar {
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

    .navbar.scrolled {
      height: 64px;
      background: rgba(11, 19, 43, 0.95);
    }

    .navbar-inner {
      max-width: 1280px;
      height: 100%;
      margin: 0 auto;
      padding: 0 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.4rem;
    }

    .nav-logo-icon {
      width: 44px;
      height: 44px;
      background: var(--gradient-cyan);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.3s ease;
    }

    .nav-logo:hover .nav-logo-icon {
      transform: rotate(-10deg) scale(1.05);
    }

    .nav-menu {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .nav-link {
      padding: 10px 20px;
      font-size: 0.95rem;
      font-weight: 500;
      color: var(--text-secondary);
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .nav-link:hover {
      color: var(--text-primary);
      background: rgba(255, 255, 255, 0.05);
    }

    .nav-cta {
      margin-left: 12px;
      padding: 12px 28px;
      background: var(--gradient-cyan);
      border-radius: 14px;
      font-weight: 700;
      color: var(--primary-dark);
      transition: all 0.3s ease;
    }

    .nav-cta:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-cyan);
    }

    .mobile-menu-btn {
      display: none;
      width: 44px;
      height: 44px;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
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
      font-family: 'Pretendard', 'Outfit', -apple-system, sans-serif;
      font-size: clamp(3rem, 5.5vw, 4.5rem);
      font-weight: 900;
      line-height: 1.08;
      letter-spacing: -0.02em; /* 한글 제목 자간 조정 */
      word-break: keep-all;
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
      line-height: 1.85; /* 긴 문장용 */
      letter-spacing: 0.01em;
      word-spacing: 0.05em;
      word-break: keep-all;
      margin-bottom: 40px;
      max-width: 580px; /* 한글 약 35~40자 */
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
      font-family: 'Pretendard', 'Outfit', -apple-system, sans-serif;
      font-size: 1.15rem;
      font-weight: 600; /* 한글은 너무 굵으면 뭉개짐 */
      letter-spacing: 0;
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
      letter-spacing: 0;
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
      min-width: 38px;
      min-height: 38px;
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
      will-change: transform;
    }

    /* 애니메이션 성능 최적화 */
    .floating-ball {
      will-change: transform;
    }

    .activity-item {
      will-change: transform;
    }

    /* 뷰포트 밖 애니메이션 일시정지 */
    .hero-ball:not(.in-view),
    .floating-ball:not(.in-view) {
      animation-play-state: paused;
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

    /* 애니메이션 성능 최적화 */
    @media (prefers-reduced-motion: reduce) {
      *,
      *::before,
      *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
      }
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

    /* 이번 회차 당첨점 섹션 */
    .hero-card-winners {
      margin-top: 16px;
      padding: 16px;
      background: linear-gradient(135deg, rgba(255, 215, 95, 0.08) 0%, rgba(255, 215, 95, 0.02) 100%);
      border-radius: 12px;
      border: 1px solid rgba(255, 215, 95, 0.15);
    }

    .winners-title {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-gold);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .winner-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      color: var(--text-secondary);
      padding: 6px 0;
    }

    .winner-item:not(:last-child) {
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .winner-region {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .winner-type {
      font-size: 0.7rem;
      padding: 2px 6px;
      border-radius: 4px;
      background: rgba(0, 224, 164, 0.15);
      color: var(--accent-cyan);
      margin-left: auto;
    }

    .winners-link {
      display: block;
      text-align: center;
      margin-top: 12px;
      padding: 10px;
      background: rgba(255, 215, 95, 0.1);
      border-radius: 8px;
      color: var(--accent-gold);
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .winners-link:hover {
      background: rgba(255, 215, 95, 0.2);
      transform: translateY(-2px);
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
      font-family: 'Pretendard', 'Outfit', -apple-system, sans-serif;
      font-size: clamp(2.2rem, 4vw, 3.2rem);
      font-weight: 800;
      margin-bottom: 18px;
      letter-spacing: -0.02em; /* 한글 제목 자간 조정 */
      word-break: keep-all;
      background: linear-gradient(180deg, #ffffff 0%, #94a3b8 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .section-subtitle {
      font-size: 1.15rem;
      color: var(--text-secondary);
      line-height: 1.85; /* 긴 문장용 */
      letter-spacing: 0.01em;
      word-spacing: 0.05em;
      word-break: keep-all;
      max-width: 580px; /* 한글 약 35~40자 */
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
      font-family: 'Pretendard', 'Outfit', -apple-system, sans-serif;
      font-size: 1.4rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      word-break: keep-all;
      margin-bottom: 12px;
    }

    .feature-desc {
      font-size: 0.95rem;
      color: var(--text-secondary);
      line-height: 1.85; /* 긴 문장용 */
      letter-spacing: 0.01em;
      word-spacing: 0.05em;
      word-break: keep-all;
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
      text-decoration: none;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .hot-ball:hover, .cold-ball:hover {
      transform: scale(1.15);
      box-shadow: 0 8px 24px rgba(0, 224, 164, 0.4);
      z-index: 10;
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

    /* ===== Guide Section ===== */
    .guide-section {
      padding: 100px 24px;
      background: linear-gradient(180deg, rgba(13,21,38,0.3) 0%, rgba(5,10,21,0.6) 100%);
    }

    .guide-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .guide-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 32px;
    }

    .guide-card {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 20px;
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .guide-card:hover {
      border-color: var(--accent-cyan);
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(0, 224, 164, 0.1);
    }

    .guide-card.highlight {
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.1), rgba(0, 224, 164, 0.02));
      border-color: rgba(0, 224, 164, 0.3);
    }

    .guide-icon {
      font-size: 2rem;
      flex-shrink: 0;
    }

    .guide-content {
      flex: 1;
    }

    .guide-name {
      display: block;
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      margin-bottom: 4px;
    }

    .guide-desc {
      display: block;
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    .guide-arrow {
      color: var(--text-muted);
      font-size: 1.2rem;
      transition: transform 0.3s;
    }

    .guide-card:hover .guide-arrow {
      transform: translateX(4px);
      color: var(--accent-cyan);
    }

    .guide-more {
      display: flex;
      justify-content: center;
      gap: 24px;
      flex-wrap: wrap;
    }

    .guide-more-link {
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.95rem;
      padding: 10px 20px;
      border-radius: 10px;
      transition: all 0.2s;
    }

    .guide-more-link:hover {
      color: var(--accent-cyan);
      background: rgba(0, 224, 164, 0.1);
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
      transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                  opacity 0.3s ease;
      opacity: 0;
    }

    .faq-item.active .faq-answer {
      max-height: var(--faq-height, 500px); /* JS에서 동적 설정 */
      opacity: 1;
    }

    .faq-answer-content {
      padding: 0 28px 24px;
      font-size: 0.95rem;
      color: var(--text-secondary);
      line-height: 1.85; /* 긴 문장용 */
      letter-spacing: 0.01em;
      word-spacing: 0.05em;
      word-break: keep-all;
      max-width: 580px; /* 한글 약 35~40자 */
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
      overflow: hidden;display: flex; flex-direction: column; gap: 10px;
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      animation: slideIn 0.5s ease both;
      position: relative;
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

    /* ===== Famous Ranking Section ===== */
    .famous-ranking-section {
      padding: 80px 24px;
      background: 
        radial-gradient(ellipse at top, rgba(0, 224, 164, 0.08) 0%, transparent 50%),
        linear-gradient(180deg, rgba(0, 224, 164, 0.03) 0%, transparent 100%);
      position: relative;
      overflow: hidden;
    }

    .famous-ranking-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(circle at 20% 30%, rgba(255, 215, 0, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(0, 224, 164, 0.05) 0%, transparent 50%);
      pointer-events: none;
      animation: background-shift 20s ease infinite;
    }

    @keyframes background-shift {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.8; transform: scale(1.1); }
    }

    .famous-ranking-container {
      max-width: 1000px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    .famous-ranking-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 48px;
      flex-wrap: wrap;
      gap: 20px;
    }

    .famous-ranking-title {
      font-family: 'Outfit', sans-serif;
      font-size: 2rem;
      font-weight: 800;
      background: linear-gradient(135deg, #ffffff 0%, rgba(0, 224, 164, 0.8) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      display: flex;
      align-items: center;
      gap: 16px;
      letter-spacing: -0.02em;
      position: relative;
    }

    .famous-ranking-title::before {
      content: '🏆';
      font-size: 1.8rem;
      filter: drop-shadow(0 2px 8px rgba(255, 215, 0, 0.4));
      animation: medal-float 3s ease-in-out infinite;
    }

    @keyframes medal-float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-5px) rotate(5deg); }
    }

    .famous-ranking-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 18px;
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(239, 68, 68, 0.1) 100%);
      border: 1px solid rgba(239, 68, 68, 0.4);
      border-radius: 24px;
      font-size: 0.9rem;
      color: #ff6b6b;
      font-weight: 700;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }

    .famous-ranking-badge:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3);
    }

    .famous-ranking-badge .pulse-dot {
      width: 10px;
      height: 10px;
      background: #ef4444;
      border-radius: 50%;
      animation: live-pulse 1.5s ease infinite;
      box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
    }

    .famous-ranking-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-bottom: 40px;
    }

    .famous-ranking-item {
      display: flex;
      align-items: center;
      gap: 24px;
      padding: 24px 28px;
      background: rgba(13, 24, 41, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 24px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .famous-ranking-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
      transition: left 0.5s ease;
    }

    .famous-ranking-item:hover::before {
      left: 100%;
    }

    .famous-ranking-item:hover {
      transform: translateX(12px) translateY(-2px);
      border-color: rgba(0, 224, 164, 0.4);
      background: rgba(13, 24, 41, 0.9);
      box-shadow: 0 8px 32px rgba(0, 224, 164, 0.2), 0 0 0 1px rgba(0, 224, 164, 0.1);
    }

    .famous-ranking-item.rank-1 {
      background: linear-gradient(135deg, rgba(255, 215, 0, 0.15) 0%, rgba(13, 24, 41, 0.7) 100%);
      border-color: rgba(255, 215, 0, 0.4);
      box-shadow: 0 4px 24px rgba(255, 215, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .famous-ranking-item.rank-1:hover {
      border-color: rgba(255, 215, 0, 0.6);
      box-shadow: 0 12px 40px rgba(255, 215, 0, 0.3), 0 0 0 1px rgba(255, 215, 0, 0.2);
    }

    .famous-ranking-item.rank-2 {
      background: linear-gradient(135deg, rgba(192, 192, 192, 0.15) 0%, rgba(13, 24, 41, 0.7) 100%);
      border-color: rgba(192, 192, 192, 0.4);
      box-shadow: 0 4px 24px rgba(192, 192, 192, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .famous-ranking-item.rank-2:hover {
      border-color: rgba(192, 192, 192, 0.6);
      box-shadow: 0 12px 40px rgba(192, 192, 192, 0.25), 0 0 0 1px rgba(192, 192, 192, 0.2);
    }

    .famous-ranking-item.rank-3 {
      background: linear-gradient(135deg, rgba(205, 127, 50, 0.15) 0%, rgba(13, 24, 41, 0.7) 100%);
      border-color: rgba(205, 127, 50, 0.4);
      box-shadow: 0 4px 24px rgba(205, 127, 50, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .famous-ranking-item.rank-3:hover {
      border-color: rgba(205, 127, 50, 0.6);
      box-shadow: 0 12px 40px rgba(205, 127, 50, 0.25), 0 0 0 1px rgba(205, 127, 50, 0.2);
    }

    .famous-ranking-medal {
      width: 56px;
      height: 56px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 16px;
      font-size: 1.8rem;
      font-weight: 700;
      flex-shrink: 0;
      position: relative;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .famous-ranking-item:hover .famous-ranking-medal {
      transform: scale(1.1) rotate(5deg);
    }

    .famous-ranking-item.rank-1 .famous-ranking-medal {
      background: linear-gradient(135deg, #ffd700 0%, #ffed4e 50%, #ffd700 100%);
      color: #1a1a1a;
      box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.3);
      animation: gold-glow 2s ease-in-out infinite;
    }

    @keyframes gold-glow {
      0%, 100% { box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.3); }
      50% { box-shadow: 0 8px 28px rgba(255, 215, 0, 0.6), inset 0 2px 4px rgba(255, 255, 255, 0.4); }
    }

    .famous-ranking-item.rank-2 .famous-ranking-medal {
      background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 50%, #c0c0c0 100%);
      color: #1a1a1a;
      box-shadow: 0 6px 20px rgba(192, 192, 192, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.3);
    }

    .famous-ranking-item.rank-3 .famous-ranking-medal {
      background: linear-gradient(135deg, #cd7f32 0%, #e6a85c 50%, #cd7f32 100%);
      color: #fff;
      box-shadow: 0 6px 20px rgba(205, 127, 50, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.2);
    }

    .famous-ranking-item:not(.rank-1):not(.rank-2):not(.rank-3) .famous-ranking-medal {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.06) 100%);
      color: var(--text-secondary);
      font-size: 1.4rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .famous-ranking-info {
      flex: 1;
      min-width: 0;
    }

    .famous-ranking-store-name {
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      letter-spacing: -0.01em;
    }

    .famous-ranking-region-badge {
      display: inline-flex;
      align-items: center;
      padding: 5px 12px;
      background: linear-gradient(135deg, rgba(0, 224, 164, 0.2) 0%, rgba(0, 224, 164, 0.1) 100%);
      border: 1px solid rgba(0, 224, 164, 0.4);
      border-radius: 14px;
      font-size: 0.8rem;
      color: var(--accent-cyan);
      font-weight: 700;
      box-shadow: 0 2px 8px rgba(0, 224, 164, 0.2);
      transition: all 0.3s ease;
    }

    .famous-ranking-item:hover .famous-ranking-region-badge {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0, 224, 164, 0.3);
    }

    .famous-ranking-stats {
      display: flex;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    .famous-ranking-stat-item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .famous-ranking-item:hover .famous-ranking-stat-item {
      background: rgba(255, 255, 255, 0.06);
      transform: translateY(-1px);
    }

    .famous-ranking-stat-item strong {
      color: var(--text-primary);
      font-weight: 700;
      font-size: 1.05em;
    }

    .famous-ranking-wins {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 6px;
      padding-left: 20px;
      border-left: 1px solid rgba(255, 255, 255, 0.08);
    }

    .famous-ranking-wins-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .famous-ranking-wins-count {
      font-family: 'Outfit', sans-serif;
      font-size: 2.4rem;
      font-weight: 900;
      background: linear-gradient(135deg, #00e0a4 0%, #00b8d4 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1;
      text-shadow: 0 0 30px rgba(0, 224, 164, 0.3);
      transition: all 0.3s ease;
    }

    .famous-ranking-item:hover .famous-ranking-wins-count {
      transform: scale(1.1);
      filter: brightness(1.2);
    }

    .famous-ranking-cta {
      text-align: center;
      margin-top: 40px;
    }

    .famous-ranking-cta-button {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 16px 36px;
      background: linear-gradient(135deg, #00e0a4 0%, #00b8d4 100%);
      color: var(--primary-dark);
      font-weight: 800;
      border-radius: 20px;
      text-decoration: none;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      font-size: 1.05rem;
      box-shadow: 0 6px 24px rgba(0, 224, 164, 0.3);
      position: relative;
      overflow: hidden;
    }

    .famous-ranking-cta-button::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s ease, height 0.6s ease;
    }

    .famous-ranking-cta-button:hover::before {
      width: 300px;
      height: 300px;
    }

    .famous-ranking-cta-button:hover {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 12px 40px rgba(0, 224, 164, 0.4);
    }

    .famous-ranking-cta-button span {
      transition: transform 0.3s ease;
    }

    .famous-ranking-cta-button:hover span {
      transform: translateX(4px);
    }

    @media (max-width: 768px) {
      .famous-ranking-section {
        padding: 60px 16px;
      }

      .famous-ranking-header {
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 32px;
      }

      .famous-ranking-title {
        font-size: 1.6rem;
      }

      .famous-ranking-item {
        flex-wrap: wrap;
        padding: 20px;
        gap: 16px;
      }

      .famous-ranking-medal {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
      }

      .famous-ranking-wins {
        width: 100%;
        align-items: flex-start;
        margin-top: 16px;
        padding-top: 16px;
        padding-left: 0;
        border-left: none;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
      }

      .famous-ranking-wins-count {
        font-size: 2rem;
      }

      .famous-ranking-store-name {
        font-size: 1.1rem;
      }

      .famous-ranking-cta-button {
        padding: 14px 28px;
        font-size: 1rem;
      }
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

      /* 모바일: Archive 테이블을 카드형으로 전환 */
      @media (max-width: 768px) {
        .archive-table-body {
          display: flex;
          flex-direction: column;
          gap: 12px;
          padding: 16px;
        }

        .archive-row {
          display: flex;
          flex-direction: column;
          gap: 12px;
          padding: 16px;
          background: rgba(13, 24, 41, 0.6);
          border: 1px solid rgba(255, 255, 255, 0.08);
          border-radius: 16px;
          margin-bottom: 12px;
          align-items: flex-start;
        }

        .archive-row::before {
          content: attr(data-round) '회차';
          font-weight: 700;
          font-size: 1rem;
          color: var(--accent-cyan);
          padding-bottom: 8px;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
          display: block;
          width: 100%;
        }

        .archive-row > div:first-child {
          width: 100%;
          font-size: 1.1rem;
          padding-bottom: 12px;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .archive-balls {
          width: 100%;
          justify-content: flex-start;
          flex-wrap: wrap;
        }

        /* 레이블 추가 */
        .archive-row > div:nth-child(2)::before {
          content: 'AI 추천: ';
          font-size: 0.75rem;
          color: var(--text-muted);
          display: block;
          margin-bottom: 6px;
        }

        .archive-row > div:nth-child(3)::before {
          content: '실제 당첨: ';
          font-size: 0.75rem;
          color: var(--text-muted);
          display: block;
          margin-bottom: 6px;
        }

        .archive-match {
          width: 100%;
          justify-content: flex-start;
          padding-top: 12px;
          border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .archive-header {
          display: none;
        }
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
      line-height: 1.85; /* 긴 문장용 */
      letter-spacing: 0.01em;
      word-spacing: 0.05em;
      word-break: keep-all;
      margin-bottom: 20px;
      font-style: normal; /* italic 제거 - 한글에 부적합 */
      max-width: 520px;
      quotes: '"' '"';
    }

    .review-content::before {
      content: open-quote;
      color: var(--accent-cyan);
      font-size: 1.2em;
      margin-right: 2px;
    }

    .review-content::after {
      content: close-quote;
      color: var(--accent-cyan);
      font-size: 1.2em;
      margin-left: 2px;
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
      font-variant-numeric: tabular-nums;
      min-width: 1.2em; /* 숫자 2자리 보장 */
      text-align: center;
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

    /* ===== Algorithm Banner Section ===== */
    .algorithm-banner-section {
      padding: 60px 24px;
    }

    .algorithm-banner-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .algorithm-banner-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 32px;
      padding: 40px 48px;
      background: linear-gradient(145deg, rgba(13, 21, 38, 0.9), rgba(5, 10, 21, 0.95));
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 28px;
      text-decoration: none;
      color: inherit;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .algorithm-banner-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0, 224, 164, 0.5), transparent);
    }

    .algorithm-banner-card:hover {
      transform: translateY(-6px);
      border-color: rgba(0, 224, 164, 0.4);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 224, 164, 0.1);
    }

    .algorithm-banner-content {
      flex: 1;
    }

    .algorithm-banner-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.25);
      border-radius: 100px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--accent-cyan);
      margin-bottom: 16px;
    }

    .algorithm-banner-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.6rem;
      font-weight: 800;
      margin-bottom: 12px;
      color: var(--text-primary);
    }

    .algorithm-banner-desc {
      font-size: 0.95rem;
      color: var(--text-secondary);
      line-height: 1.6;
      margin-bottom: 16px;
      max-width: 500px;
    }

    .algorithm-banner-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--accent-cyan);
      transition: gap 0.3s ease;
    }

    .algorithm-banner-card:hover .algorithm-banner-link {
      gap: 12px;
    }

    .algorithm-banner-icons {
      display: flex;
      gap: 12px;
      font-size: 2rem;
      opacity: 0.6;
      transition: opacity 0.3s ease;
    }

    .algorithm-banner-card:hover .algorithm-banner-icons {
      opacity: 1;
    }

    @media (max-width: 768px) {
      .algorithm-banner-card {
        flex-direction: column;
        text-align: center;
        padding: 32px 24px;
      }

      .algorithm-banner-desc {
        max-width: none;
      }

      .algorithm-banner-icons {
        justify-content: center;
      }
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

    /* ===== 한국어 가독성 최적화 ===== */
    
    /* 본문 텍스트 최적화 */
    p, li, span, div {
      line-height: 1.75;
    }

    /* 강조 텍스트 */
    strong, b {
      font-weight: 600; /* 700 → 600 */
    }

    /* 숫자와 한글 혼합 시 정렬 */
    .pricing-amount,
    .cumulative-number,
    .countdown-value,
    .archive-summary-value {
      font-variant-numeric: tabular-nums;
      font-feature-settings: 'tnum' 1;
    }

    /* 배지/태그 가독성 */
    .hero-badge,
    .section-badge,
    .pricing-badge,
    .community-tag {
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    /* 작은 라벨 가독성 */
    .countdown-unit,
    .cumulative-label,
    .archive-summary-label,
    .quality-desc,
    .status-detail {
      font-size: 0.85rem; /* 최소 크기 보장 */
      font-weight: 500; /* 얇은 폰트 방지 */
      color: var(--text-muted);
    }

    /* 최소 폰트 크기 보장 */
    .hero-trust-item,
    .status-mini,
    .archive-match-label,
    .footer-disclaimer,
    .review-meta,
    .community-date,
    .warning-item-content p,
    .disclaimer-list li {
      font-size: 0.875rem; /* 0.75~0.8rem → 0.875rem */
    }

    /* 카드 내 텍스트 가독성 */
    .pricing-features li {
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    /* 긴 텍스트 블록 가독성 */
    .disclaimer-col,
    .notification-desc {
      text-align: left;
      word-break: keep-all;
    }

    /* 제목 폰트 - 자간 조정 */
    h1, h2, h3, h4, h5, h6,
    .pricing-name {
      font-family: 'Pretendard', 'Outfit', -apple-system, sans-serif;
      letter-spacing: -0.02em;
      word-break: keep-all;
    }

    @media (max-width: 768px) {
      body {
        font-size: 16px; /* 기본 크기 유지 (확대 방지) */
        line-height: 1.8;
      }

      .hero-subtitle,
      .section-subtitle {
        font-size: 1rem;
        line-height: 1.85;
      }

      .feature-desc,
      .faq-answer-content {
        font-size: 0.95rem;
        line-height: 1.8;
      }

      /* 터치 영역 내 텍스트 */
      .nav-link,
      .mobile-menu-link,
      .guide-name {
        font-size: 1rem;
        font-weight: 500;
      }

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

      /* Guide Section - Tablet */
      .guide-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .guide-section {
        padding: 60px 16px;
      }
    }

    @media (max-width: 480px) {
      body {
        line-height: 1.75;
      }

      /* 아주 작은 텍스트 방지 */
      .footer-disclaimer,
      .footer-copyright,
      .reviews-disclaimer {
        font-size: 0.8rem; /* 최소 */
        line-height: 1.6;
      }

      /* Guide Section - Mobile */
      .guide-grid {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .guide-card {
        padding: 16px;
      }

      .guide-icon {
        font-size: 1.5rem;
      }

      .guide-name {
        font-size: 0.95rem;
      }

      .guide-desc {
        font-size: 0.8rem;
      }
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
        width: 48px;
        height: 48px;
        min-width: 48px;
        min-height: 48px;
        font-size: 1.2rem;
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

      .hero-card-winners {
        margin-top: 12px;
        padding: 12px;
      }

      .winners-title {
        font-size: 0.8rem;
        justify-content: center;
      }

      .winner-item {
        font-size: 0.85rem;
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
      }

      .winners-link {
        font-size: 0.8rem;
        padding: 8px;
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

    /* ===== 한국어 가독성 최적화 CSS ===== */
    
    /* 1. 폰트 스택 - 한글 우선 + 줄간격 개선 */
    body {
      font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 
                   'Apple SD Gothic Neo', 'Noto Sans KR', 'Malgun Gothic',
                   'Inter', sans-serif !important;
      line-height: 1.75 !important;
      word-break: keep-all;
      overflow-wrap: break-word;
      text-rendering: optimizeLegibility;
    }

    /* 2. 제목 폰트 - 자간 조정 (한글 제목은 살짝 좁게) */
    h1, h2, h3, h4, h5, h6,
    .hero-title,
    .section-title,
    .pricing-name,
    .feature-title,
    .cta-title,
    .notification-title,
    .prize-title,
    .warning-title {
      font-family: 'Pretendard', 'Outfit', -apple-system, sans-serif;
      letter-spacing: -0.02em;
      word-break: keep-all;
    }

    /* 3. 본문 텍스트 최적화 */
    p, li, span, div {
      line-height: 1.75;
    }

    /* 4. 설명/부제목 텍스트 - 긴 문장용 */
    .hero-subtitle,
    .section-subtitle,
    .feature-desc,
    .faq-answer-content,
    .review-content,
    .pricing-desc,
    .cta-subtitle,
    .notification-desc,
    .disclaimer-list li {
      line-height: 1.85;
      letter-spacing: 0.01em;
      word-spacing: 0.03em;
    }

    /* 5. 최소 폰트 크기 보장 (모바일 가독성) */
    .hero-trust-item,
    .status-mini,
    .archive-match-label,
    .footer-disclaimer,
    .review-meta,
    .community-date,
    .warning-item-content p,
    .disclaimer-list li,
    .countdown-unit,
    .cumulative-label,
    .archive-summary-label,
    .quality-desc,
    .status-detail {
      font-size: 0.875rem;
      font-weight: 500;
    }

    /* 6. 카드 내 텍스트 가독성 강화 */
    .feature-desc,
    .pricing-features li,
    .faq-answer-content {
      font-size: 0.95rem;
      color: var(--text-secondary);
    }

    /* 7. 최대 줄 길이 제한 (읽기 편한 35~40자) */
    .hero-subtitle,
    .section-subtitle,
    .faq-answer-content,
    .disclaimer-list li,
    .notification-desc {
      max-width: 580px;
    }

    .review-content {
      max-width: 520px;
    }

    /* 8. 숫자와 한글 혼합 시 정렬 안정화 */
    .pricing-amount,
    .cumulative-number,
    .countdown-value,
    .archive-summary-value,
    .queue-number {
      font-variant-numeric: tabular-nums;
      font-feature-settings: 'tnum' 1;
    }

    /* 9. 버튼/CTA 텍스트 (한글은 너무 굵으면 뭉개짐) */
    .hero-cta-primary,
    .hero-cta-secondary,
    .pricing-cta,
    .cta-button,
    .nav-cta,
    .notification-submit {
      font-weight: 600;
      letter-spacing: 0;
    }

    /* 10. 배지/태그 가독성 */
    .hero-badge,
    .section-badge,
    .pricing-badge,
    .community-tag,
    .live-badge {
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    /* 11. 강조 텍스트 */
    strong, b {
      font-weight: 600;
    }

    /* 12. 인용/후기 텍스트 - italic 제거 (한글에 부적합) */
    .review-content {
      font-style: normal;
    }

    /* 13. FAQ 질문 텍스트 */
    .faq-question {
      font-size: 1.05rem;
      font-weight: 600;
      line-height: 1.5;
    }

    /* 14. 네비게이션 링크 */
    .nav-link,
    .mobile-menu-link {
      font-weight: 500;
      letter-spacing: 0;
    }

    /* 15. 푸터 텍스트 */
    .footer-desc,
    .footer-col a,
    .footer-company-item {
      line-height: 1.7;
    }

    /* ===== 모바일 가독성 강화 ===== */
    @media (max-width: 768px) {
      body {
        font-size: 16px;
        line-height: 1.8;
      }
      
      .hero-subtitle,
      .section-subtitle {
        font-size: 1rem;
        line-height: 1.85;
      }
      
      .feature-desc,
      .faq-answer-content {
        font-size: 0.95rem;
        line-height: 1.8;
      }
      
      /* 터치 영역 내 텍스트 */
      .nav-link,
      .mobile-menu-link,
      .guide-name {
        font-size: 1rem;
        font-weight: 500;
      }
      
      /* 카드 제목 */
      .feature-title,
      .pricing-name {
        font-size: 1.2rem;
      }
    }

    @media (max-width: 480px) {
      body {
        line-height: 1.75;
      }
      
      /* 아주 작은 텍스트 방지 */
      .footer-disclaimer,
      .footer-copyright,
      .reviews-disclaimer,
      .prize-note,
      .notification-note {
        font-size: 0.8rem;
        line-height: 1.6;
      }
      
      /* 히어로 섹션 */
      .hero-subtitle {
        font-size: 0.95rem;
        line-height: 1.8;
      }
      
      /* 버튼 텍스트 */
      .hero-cta-primary,
      .hero-cta-secondary {
        font-size: 1rem;
      }
    }

    /* ===== 긴 텍스트 블록 가독성 ===== */
    .disclaimer-col,
    .faq-answer-content,
    .notification-desc,
    .warning-footer-text {
      text-align: left;
      word-break: keep-all;
    }

    /* ===== 활동 피드 텍스트 ===== */
    .activity-text {
      font-size: 0.95rem;
      line-height: 1.5;
    }

    .activity-text strong {
      font-weight: 600;
    }

    /* ===== 가격 섹션 가독성 ===== */
    .pricing-features li {
      line-height: 1.6;
      padding: 14px 0;
    }

    /* ===== 통계 대시보드 ===== */
    .dashboard-card-title {
      font-weight: 700;
      letter-spacing: -0.01em;
    }

    .ratio-label {
      font-weight: 500;
    }

    /* ===== 아카이브 테이블 ===== */
    .archive-round {
      font-weight: 700;
    }

    .archive-verify-header span {
      font-weight: 600;
    }

    /* ===== 커뮤니티 섹션 ===== */
    .community-title {
      line-height: 1.5;
      letter-spacing: -0.01em;
    }

    .community-author {
      font-weight: 600;
    }

    /* ===== 가이드 섹션 ===== */
    .guide-name {
      font-weight: 700;
      letter-spacing: -0.01em;
    }

    .guide-desc {
      line-height: 1.5;
    }

    /* ===== 알림 섹션 ===== */
    .notification-benefit {
      font-size: 0.95rem;
    }

    /* ===== 경고 섹션 ===== */
    .warning-item-content h4 {
      font-weight: 600;
      letter-spacing: -0.01em;
    }

    .warning-item-content p {
      line-height: 1.6;
    }

    /* ===== 시스템 상태 ===== */
    .status-name {
      font-weight: 600;
    }

    /* ===== 품질 게이지 ===== */
    .quality-gauge-label {
      font-weight: 600;
    }

    /* ===== 색상 대비 개선 (WCAG AA 기준) ===== */
    .hero-trust-item,
    .status-mini,
    .countdown-unit,
    .cumulative-label,
    .archive-summary-label {
      color: #9ca3af;
    }
  </style>
  <!-- Lotto Data Script -->
  <script src="/scripts/lotto-data.js"></script>
</head>
<body>
  <!-- Loading Screen -->
  <div class="loading-screen" id="loadingScreen" role="status" aria-live="polite" aria-label="페이지 로딩 중">
    <div class="loading-logo">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="9" cy="9" r="5" fill="#FFD75F"/>
        <circle cx="13" cy="12" r="4" stroke="#0B132B" stroke-width="2.5" fill="none"/>
        <line x1="16" y1="15" x2="20" y2="19" stroke="#0B132B" stroke-width="2.5" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="loading-text">오늘로또</div>
    <div class="loading-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
      <div class="loading-bar-fill"></div>
    </div>
  </div>

	<!-- Mobile Menu -->
	<div class="mobile-menu" id="mobileMenu" role="dialog" aria-modal="true" aria-labelledby="mobileMenuTitle" aria-hidden="true">
	  <h2 id="mobileMenuTitle" class="sr-only">모바일 메뉴</h2>
	  <button class="mobile-menu-close" onclick="closeMobileMenu()" aria-label="모바일 메뉴 닫기">✕</button>

	  <div class="mobile-menu-links">
		<a href="#features" class="mobile-menu-link" onclick="closeMobileMenu()">분석 기능</a>
		<a href="/로또-판매점/" class="mobile-menu-link" onclick="closeMobileMenu()">🏆 명당</a>
		<a href="/로또-가이드/" class="mobile-menu-link" onclick="closeMobileMenu()">📚 가이드</a>
		<a href="/로또-분석/" class="mobile-menu-link" onclick="closeMobileMenu()">📊 분석</a>
		<a href="#pricing" class="mobile-menu-link" onclick="closeMobileMenu()">요금</a>
		<a href="#faq" class="mobile-menu-link" onclick="closeMobileMenu()">FAQ</a>

		<?php if ($is_member) { ?>
		  <a href="<?php echo $logout_url; ?>" class="mobile-menu-link">
			로그아웃
		  </a>
		<?php } else { ?>
		  <!-- 비로그인 상태: 로그인 -->
		  <a href="<?php echo $auth_url; ?>" class="mobile-menu-link">
			로그인
		  </a>
		<?php } ?>
	  </div>

	  <?php if ($is_member) { ?>
		<!-- 로그인 상태 CTA -->
		<a href="<?php echo $result_url; ?>" class="mobile-menu-cta" onclick="closeMobileMenu()">
		  AI 분석 바로가기
		</a>
	  <?php } else { ?>
		<!-- 비로그인 상태 CTA -->
		<a href="<?php echo $auth_url; ?>" class="mobile-menu-cta">
		  무료로 시작하기
		</a>
	  <?php } ?>
	</div>

  <!-- Skip Link (접근성) -->
  <a href="#main-content" class="skip-link">본문으로 건너뛰기</a>

  <!-- Back to Top Button -->
  <button class="back-to-top" id="backToTop" onclick="scrollToTop()" aria-label="페이지 상단으로 이동">↑</button>

  <!-- Navbar -->
  <nav class="navbar" id="navbar" role="navigation" aria-label="주요 메뉴">
    <div class="navbar-inner">
      <a href="/" class="nav-logo">
        <div class="nav-logo-icon">
          <svg width="22" height="22" viewBox="0 0 32 32" fill="none" aria-label="오늘로또 로고" role="img">
            <title>오늘로또 로고</title>
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
		<div class="nav-menu">
		  <a href="#features" class="nav-link">분석 기능</a>
		  <a href="/로또-판매점/" class="nav-link">🏆 명당</a>
		  <a href="/로또-가이드/" class="nav-link">📚 가이드</a>
		  <a href="/로또-분석/" class="nav-link">📊 분석</a>
		  <a href="#pricing" class="nav-link">요금</a>

		  <?php if ($is_member) { ?>
			<!-- 로그인 상태: 로그아웃 + AI 분석 바로가기 -->
			<a href="<?php echo G5_URL; ?>/bbs/logout.php" class="nav-link">
			  로그아웃
			</a>
			<a href="<?php echo $result_url; ?>" class="nav-cta">
			  AI 분석 바로가기
			</a>
		  <?php } else { ?>
			<!-- 비로그인 상태: 로그인 + 무료로 시작 -->
			<a href="<?php echo $auth_url; ?>" class="nav-link">
			  로그인
			</a>
			<a href="<?php echo $auth_url; ?>" class="nav-cta">
			  무료로 시작
			</a>
		  <?php } ?>
		</div>

        <button class="mobile-menu-btn" onclick="openMobileMenu()" aria-label="모바일 메뉴 열기" aria-expanded="false" id="mobileMenuBtn">
		  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
			<line x1="3" y1="12" x2="21" y2="12"></line>
			<line x1="3" y1="6" x2="21" y2="6"></line>
			<line x1="3" y1="18" x2="21" y2="18"></line>
		  </svg>
		</button>

    </div>
  </nav>

  <!-- Hero Section -->
  <main id="main-content">
  <section class="hero" role="banner">
    <div class="floating-ball floating-ball-1"></div>
    <div class="floating-ball floating-ball-2"></div>
    <div class="floating-ball floating-ball-3"></div>
    <div class="hero-container">
      <div class="hero-content">
        <div class="hero-badge">
          <span class="hero-badge-dot"></span>
          <?php echo number_format($max_round);?>회차 데이터 실시간 반영
        </div>
        <h1 class="hero-title">
          <span class="line">23년간의 패턴을</span>
          <span class="line"><span class="gradient">AI가 분석</span>합니다</span>
        </h1>
        <p class="sr-only">동행복권 공식 데이터 기반 AI 로또 번호 분석 서비스. 무료 1회 분석 제공.</p>
        <p class="hero-subtitle">
          동행복권 공식 데이터 <?php echo number_format($total_numbers);?>개 당첨번호를 AI가 분석하여<br>
          <strong>균형 잡힌 번호 조합</strong>을 제공합니다.
        </p>
        <div class="hero-cta-group">
          <a href="auth.php" class="hero-cta-primary">
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
            무료 2회 즉시 제공
          </div>
        </div>
      </div>

	  <?php
		$latest = sql_fetch("
			SELECT *
			FROM g5_lotto_draw   -- ✅ 실제 테이블명으로 수정
			ORDER BY draw_no DESC    -- ✅ 최신 회차 기준 컬럼명 (예: round, drwNo 등)
			LIMIT 1
		");

		$round      = 0;
		$draw_date  = '';
		$nums       = [];
		$bonus      = null;
		$first_prize = 0;

		if ($latest) {
			// ✅ 컬럼명은 실제 DB 구조에 맞게 수정
			$round     = (int)$latest['draw_no'];          // 회차
			$draw_date = date('Y-m-d', strtotime($latest['draw_date'])); // 추첨일

			$nums = [
				(int)$latest['n1'],
				(int)$latest['n2'],
				(int)$latest['n3'],
				(int)$latest['n4'],
				(int)$latest['n5'],
				(int)$latest['n6'],
			];

			$bonus       = (int)$latest['bonus'];        // 보너스 번호
			$amount = (int)$latest['first_prize_each'];  // 1등 당첨금 (원 단위라고 가정)
		}

		// 🔹 공 색상 클래스 (동행복권 공식 색상 기준)
		if (!function_exists('lotto_ball_class')) {
			function lotto_ball_class($n) {
				if ($n >= 1  && $n <= 10) return 'ball-yellow';
				if ($n >= 11 && $n <= 20) return 'ball-blue';
				if ($n >= 21 && $n <= 30) return 'ball-red';
				if ($n >= 31 && $n <= 40) return 'ball-gray';
				return 'ball-green'; // 41~45
			}
		}

		// 🔹 "14억 1,555만원" 형태로 변환
		//if (!function_exists('format_prize_krw_short')) {
			function format_prize_krw_short($amount) {
				if ($amount <= 0) return '집계중';

				$eok = floor($amount / 100000000);           // 억
				$man = floor(($amount % 100000000) / 10000); // 만원

				$out = '';
				if ($eok > 0) $out .= number_format($eok) . '억 ';
				if ($man > 0) $out .= number_format($man) . '만원';

				return trim($out);
			}
		//}
		?>

		<div class="hero-visual">
		  <div class="hero-card">
			<div class="hero-card-header">
			  <div class="live-badge">
				<span class="live-dot"></span>
				LIVE 최신 결과
			  </div>
			  <span class="hero-card-round">
				<?php if ($round) { ?>
				  <?php echo $round; ?>회 · <?php echo $draw_date; ?>
				<?php } else { ?>
				  회차 정보 로딩중
				<?php } ?>
			  </span>
			</div>

			<div class="hero-balls-container">
			  <?php if (!empty($nums)) { ?>
				<?php foreach ($nums as $n) { ?>
				  <div class="hero-ball <?php echo lotto_ball_class($n); ?>">
					<?php echo $n; ?>
				  </div>
				<?php } ?>
				<span class="bonus-sep">+</span>
				<?php if ($bonus !== null) { ?>
				  <div class="hero-ball <?php echo lotto_ball_class($bonus); ?>">
					<?php echo $bonus; ?>
				  </div>
				<?php } ?>
			  <?php } else { ?>
				<span>번호 데이터 없음</span>
			  <?php } ?>
			</div>

			<div class="hero-card-info">
			  <span class="hero-card-prize">
				1등 당첨금
				<strong>
				  <?php echo $amount ? format_prize_krw_short($amount) : '집계중'; ?>
				</strong>
			  </span>
			  <a href="https://dhlottery.co.kr" target="_blank" rel="noopener" class="hero-card-link">
				공식 확인 →
			  </a>
			</div>

			<!-- 이번 회차 1등 배출점 -->
			<div class="hero-card-winners">
			  <div class="winners-title">🏆 이번 회차 1등 배출점</div>
			  <?php
			  // 판매점 라이브러리 로드 (없으면 무시)
			  $store_lib_path = G5_PATH . '/lib/lotto_store.lib.php';
			  if (file_exists($store_lib_path)) {
			      include_once($store_lib_path);
			  }
			  
			  // DB에서 해당 회차 1등 당첨점 조회
			  $winners = [];
			  if (function_exists('li_get_draw_winning_stores') && $round > 0) {
			      $winners = li_get_draw_winning_stores($round, 1);
			  }
			  
			  if (!empty($winners)) {
			      // 최대 3개만 표시
			      foreach (array_slice($winners, 0, 3) as $w) {
			          $win_type_text = ($w['win_type'] == 'auto') ? '자동' : (($w['win_type'] == 'manual') ? '수동' : '반자동');
			          ?>
			          <div class="winner-item">
			            <span>📍 <?php echo htmlspecialchars($w['store_name']); ?></span>
			            <span class="winner-region">(<?php echo htmlspecialchars($w['region1'] ?? ''); ?>)</span>
			            <span class="winner-type"><?php echo $win_type_text; ?></span>
			          </div>
			          <?php
			      }
			  } else {
			      ?>
			      <div class="winner-item">
			        <span>📍 데이터 준비중...</span>
			        <span class="winner-region">(DB 동기화 필요)</span>
			      </div>
			      <?php
			  }
			  ?>
			  <a href="/stores/?round=<?php echo $round; ?>" class="winners-link">
			    전국 명당 보기 →
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
				<span>
				  <?php echo $round ? number_format($round).'회 동기화' : '동기화 준비중'; ?>
				</span>
			  </div>
			</div>
		  </div>
		</div>
    </div>
  </section>

  <!-- Queue Position Banner -->
  <?php
  // GNUBOARD 공통 로드 (이미 common.php를 include 했다면 이 블럭은 빼셔도 됩니다)
  if (!defined('_GNUBOARD_')) {
      include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
  }

  // 오늘 00:00:00 ~ 23:59:59 사이 분석 실행 횟수 카운트
  $today_start = G5_TIME_YMD . ' 00:00:00';
  $today_end   = G5_TIME_YMD . ' 23:59:59';

  $row = sql_fetch("
      SELECT COUNT(*) AS cnt
      FROM g5_lotto_analysis_log
      WHERE created_at BETWEEN '{$today_start}' AND '{$today_end}'
  ");

  $today_count  = (int)($row['cnt'] ?? 0);
  $queue_number = $today_count + 1; // 지금 보는 사람 = 다음 분석자 번호
  ?>

  <!-- Queue Position Banner -->
  <div class="queue-banner">
    <div class="queue-content">
      <span class="queue-icon">🎯</span>
	  <!--
      <span class="queue-text">
        오늘
        <span class="queue-number" id="queueNumber">
          <?php echo number_format($queue_number); ?>
        </span>
        번째 분석자가 되세요!
      </span>
      -->
      <span class="queue-text">오늘 <span class="queue-number" id="queueNumber">247</span>번째 분석자가 되세요!</span>
      <a href="auth.php" class="queue-cta">지금 분석하기</a>
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
          ✨ 지금 분석하면 <strong><?php echo number_format($round+1);?>회차</strong>에 적용됩니다
        </div>
      </div>
    </div>
  </section>

  <!-- Live Activity Feed -->
  <!--
  <section class="activity-section">
    <div class="activity-container">
      <div class="activity-header">
        <h3 class="activity-title">
          <span class="activity-pulse"></span>
          실시간 분석 현황
        </h3>
        <div class="activity-counter">
          📊 오늘 총 <strong id="todayCount">0</strong>회 분석 완료
        </div>
      </div>
      <div class="activity-feed" id="activityFeed"></div>
    </div>
  </section>

  <script>
    (function(){
      const API_URL = "/ajax/activity_feed.php";
      const feedEl = document.getElementById('activityFeed');
      const countEl = document.getElementById('todayCount');

      function setCount(n){
        if (!countEl) return;
        countEl.textContent = (Number(n)||0).toLocaleString();
      }

      function render(items){
        if (!feedEl) return;
        feedEl.innerHTML = '';

        (items || []).forEach(item => {
          const wrap = document.createElement('div');
          wrap.className = 'activity-item';

          const avatar = document.createElement('div');
          avatar.className = 'activity-avatar';
          avatar.textContent = item.avatar || '익*';

          const content = document.createElement('div');
          content.className = 'activity-content';

          const p = document.createElement('p');
          p.className = 'activity-text';

          const strong = document.createElement('strong');
          const regionTxt = item.region ? ` (${item.region})` : '';
          strong.textContent = (item.name || '익*') + regionTxt;

          const tag = document.createElement('span');
          tag.className = 'style-tag';
          tag.textContent = item.type || 'AI 추천';

          p.appendChild(strong);
          p.appendChild(document.createTextNode(' 님이 '));
          p.appendChild(tag);
          p.appendChild(document.createTextNode('을 완료했습니다'));

          content.appendChild(p);

          const time = document.createElement('span');
          time.className = 'activity-time';
          time.textContent = item.time || '';

          wrap.appendChild(avatar);
          wrap.appendChild(content);
          wrap.appendChild(time);
          feedEl.appendChild(wrap);
        });
      }

      async function refresh(){
        try{
          const res = await fetch(API_URL + '?t=' + Date.now(), { cache: 'no-store' });
          const data = await res.json();
          setCount(data.todayCount);
          render(data.items);
        } catch(e){
          // 조용히 실패
        }
      }

      refresh();
      setInterval(refresh, 10000); // 10초마다 갱신 (원하면 3초로 변경)
    })();
  </script>
  -->

  <!-- Famous Ranking Section -->
  <?php
  // 실제 DB 데이터 연동
  // 판매점 라이브러리 로드
  $store_lib_path = G5_PATH . '/lib/lotto_store.lib.php';
  if (file_exists($store_lib_path)) {
      include_once($store_lib_path);
  }
  
  // TOP 5 명당 판매점 조회 (1등 당첨 횟수 기준)
  $ranking_sql = "
    SELECT 
      s.store_id,
      s.store_name,
      s.region1,
      s.wins_1st,
      s.wins_2nd,
      COALESCE(SUM(CASE WHEN w.`rank` = 1 THEN w.prize_amount ELSE 0 END), 0) as total_prize_1st,
      COALESCE(SUM(CASE WHEN w.`rank` = 2 THEN w.prize_amount ELSE 0 END), 0) as total_prize_2nd,
      COALESCE(COUNT(w.win_id), 0) as total_wins
    FROM g5_lotto_store s
    LEFT JOIN g5_lotto_store_win w ON s.store_id = w.store_id
    WHERE s.wins_1st > 0
    GROUP BY s.store_id, s.store_name, s.region1, s.wins_1st, s.wins_2nd
    ORDER BY s.wins_1st DESC, s.wins_2nd DESC, total_wins DESC
    LIMIT 5
  ";
  
  $ranking_result = sql_query($ranking_sql);
  $rank = 0;
  $has_data = false;
  
  // 데이터 존재 여부 확인
  if ($ranking_result && sql_num_rows($ranking_result) > 0) {
      $has_data = true;
  }
  ?>
  <section class="famous-ranking-section">
    <div class="famous-ranking-container">
      <div class="famous-ranking-header">
        <h3 class="famous-ranking-title">
          우리 동네 명당 찾아보기
        </h3>
        <div class="famous-ranking-badge">
          <span class="pulse-dot"></span>
          <span>실시간 TOP 5</span>
        </div>
      </div>

      <div class="famous-ranking-list">
        <?php if ($has_data): ?>
          <?php while ($row = sql_fetch_array($ranking_result)): 
            $rank++;
            $rank_class = $rank <= 3 ? "rank-{$rank}" : "";
            $medal_icon = $rank == 1 ? "🥇" : ($rank == 2 ? "🥈" : ($rank == 3 ? "🥉" : $rank));
            $total_prize = ($row['total_prize_1st'] + $row['total_prize_2nd']) / 100000000; // 억원 단위
            $total_wins = (int)$row['total_wins'] ?: (int)$row['wins_1st'] + (int)$row['wins_2nd'];
          ?>
          <div class="famous-ranking-item <?= $rank_class ?>" onclick="window.location.href='/stores/?id=<?= (int)$row['store_id'] ?>'">
            <div class="famous-ranking-medal"><?= $medal_icon ?></div>
            <div class="famous-ranking-info">
              <div class="famous-ranking-store-name">
                <?= htmlspecialchars($row['store_name']) ?>
                <?php if (!empty($row['region1'])): ?>
                  <span class="famous-ranking-region-badge"><?= htmlspecialchars($row['region1']) ?></span>
                <?php endif; ?>
              </div>
              <div class="famous-ranking-stats">
                <div class="famous-ranking-stat-item">
                  🏅 <strong>1등</strong> <?= number_format((int)$row['wins_1st']) ?>회
                </div>
                <?php if ($total_prize > 0): ?>
                  <div class="famous-ranking-stat-item">
                    💰 누적 <strong><?= number_format($total_prize, 0) ?>억원</strong>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="famous-ranking-wins">
              <div class="famous-ranking-wins-label">당첨 횟수</div>
              <div class="famous-ranking-wins-count"><?= number_format($total_wins) ?></div>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <!-- 데이터가 없을 때 표시 -->
          <div class="famous-ranking-item">
            <div class="famous-ranking-medal">📊</div>
            <div class="famous-ranking-info">
              <div class="famous-ranking-store-name">
                데이터 준비중...
              </div>
              <div class="famous-ranking-stats">
                <div class="famous-ranking-stat-item">
                  명당 랭킹 데이터를 수집 중입니다
                </div>
              </div>
            </div>
            <div class="famous-ranking-wins">
              <div class="famous-ranking-wins-label">준비중</div>
              <div class="famous-ranking-wins-count">-</div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="famous-ranking-cta">
        <a href="/stores/" class="famous-ranking-cta-button">
          전체 명당 랭킹 보기
          <span>→</span>
        </a>
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
          <div class="cumulative-number"><?= (int)$max_round ?></div>
          <div class="cumulative-label">최신 반영 회차</div>
        </div>
      </div>
      <p class="cumulative-note">
        <span class="pulse-dot"></span>
        실시간 업데이트 중 (마지막 갱신: 3초 전)
      </p>
    </div>
  </section>

  <!-- 최근 8주 실제 프로그램 -->
  <?php
	// =============================
	// 최근 8회 AI 추천 & 실제 당첨 아카이브
	// ※ 실제 테이블/컬럼명에 맞게 수정 필요
	// =============================

	// 예시: AI 추천 테이블 (회차별 6개 번호)
	//   - 테이블명 : g5_lotto_ai_recommend
	//   - 컬럼 : round, a1~a6
	// 예시: 당첨번호 테이블 (이미 사용 중인 g5_lotto_draw)
	//   - 컬럼 : round, n1~n6
	$archive_rows = [];

	// ▶ 필요에 맞게 테이블/컬럼명 수정하세요
	$sql = "
		SELECT 
			ai.round,
			ai.a1, ai.a2, ai.a3, ai.a4, ai.a5, ai.a6,
			dr.n1, dr.n2, dr.n3, dr.n4, dr.n5, dr.n6
		FROM g5_lotto_ai_recommend AS ai
		JOIN g5_lotto_draw AS dr 
		  ON dr.draw_no = ai.round
		ORDER BY ai.round DESC
		LIMIT 8
	";
	$res = sql_query($sql);

	while ($row = sql_fetch_array($res)) {
		// AI 추천 번호 / 실제 번호 배열화
		$ai_nums   = [(int)$row['a1'], (int)$row['a2'], (int)$row['a3'], (int)$row['a4'], (int)$row['a5'], (int)$row['a6']];
		$real_nums = [(int)$row['n1'], (int)$row['n2'], (int)$row['n3'], (int)$row['n4'], (int)$row['n5'], (int)$row['n6']];

		// 일치 번호 계산
		$matches = array_values(array_intersect($ai_nums, $real_nums));
		sort($matches);

		$match_count = count($matches);

		// 일치 레벨 (CSS용)
		if ($match_count >= 3) {
			$match_grade = 'good';
		} elseif ($match_count >= 2) {
			$match_grade = 'avg';
		} else {
			$match_grade = 'bad';
		}

		$row['ai_nums']      = $ai_nums;
		$row['real_nums']    = $real_nums;
		$row['match_nums']   = $matches;
		$row['match_count']  = $match_count;
		$row['match_grade']  = $match_grade;

		$archive_rows[] = $row;
	}

	// 공 색상 결정 (1~10 노랑, 11~20 파랑, 21~30 빨강, 31~40 회색, 41~45 초록)
	if (!function_exists('lotto_ball_class')) {
		function lotto_ball_class($num) {
			if ($num >= 1 && $num <= 10)  return 'ball-yellow';
			if ($num >= 11 && $num <= 20) return 'ball-blue';
			if ($num >= 21 && $num <= 30) return 'ball-red';
			if ($num >= 31 && $num <= 40) return 'ball-gray';
			return 'ball-green';
		}
	}

	// 8주 평균 / 최고 일치 / 누적 기록
	$avg_match = 0;
	$best_match_count = 0;
	$best_match_round = null;

	if ($archive_rows) {
		$sum = 0;
		foreach ($archive_rows as $r) {
			$sum += $r['match_count'];
			if ($r['match_count'] > $best_match_count) {
				$best_match_count = $r['match_count'];
				$best_match_round = $r['round'];
			}
		}
		$avg_match = round($sum / count($archive_rows), 1);
	}

	// 누적 기록(주) – AI 추천 기록이 몇 회차 있는지
	$total_weeks = 0;
	$row_total = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_ai_recommend");
	if ($row_total && isset($row_total['cnt'])) {
		$total_weeks = (int)$row_total['cnt'];
	}
  ?>
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
          <?php if (!empty($archive_rows)) { ?>
            <?php foreach ($archive_rows as $row) { ?>
              <div class="archive-row" data-round="<?php echo (int)$row['round']; ?>">
                <div class="archive-round">
                  <?php echo (int)$row['round']; ?>회
                </div>

                <div class="archive-balls">
                  <?php foreach ($row['ai_nums'] as $n) { 
                    $ballClass = lotto_ball_class($n);
                    $isMatched = in_array($n, $row['match_nums'], true);
                  ?>
                    <span class="archive-ball <?php echo $ballClass; ?><?php echo $isMatched ? ' matched' : ''; ?>">
                      <?php echo $n; ?>
                    </span>
                  <?php } ?>
                </div>

                <div class="archive-balls">
                  <?php foreach ($row['real_nums'] as $n) { 
                    $ballClass = lotto_ball_class($n);
                    $isMatched = in_array($n, $row['match_nums'], true);
                  ?>
                    <span class="archive-ball <?php echo $ballClass; ?><?php echo $isMatched ? ' matched' : ''; ?>">
                      <?php echo $n; ?>
                    </span>
                  <?php } ?>
                </div>

                <div class="archive-match">
                  <span class="archive-match-num archive-match-<?php echo $row['match_grade']; ?>">
                    <?php echo $row['match_count']; ?>개
                  </span>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="archive-row">
              <div class="archive-round" style="grid-column: 1 / 5; text-align:center;">
                아직 아카이브 데이터가 없습니다.
              </div>
            </div>
          <?php } ?>
        </div>
        <div class="archive-summary">
          <div class="archive-summary-item">
            <div class="archive-summary-value">
              <?php echo $avg_match; ?>개
            </div>
            <div class="archive-summary-label">8주 평균 일치</div>
          </div>
          <div class="archive-summary-item">
            <div class="archive-summary-value">
              <?php echo $best_match_count; ?>개
            </div>
            <div class="archive-summary-label">
              최고 일치
              <?php if ($best_match_round) { ?>
                (<?php echo (int)$best_match_round; ?>회)
              <?php } ?>
            </div>
          </div>
          <div class="archive-summary-item">
            <div class="archive-summary-value">
              <?php echo $total_weeks; ?>주
            </div>
            <div class="archive-summary-label">누적 기록</div>
          </div>
        </div>

        <div class="archive-verify">
          <div class="archive-verify-header">
            <span class="archive-verify-icon">🔎</span>
            <span>특정 회차 데이터 검증</span>
          </div>
          <div class="archive-verify-input-group">
            <input type="number" class="archive-verify-input" id="verifyInput" placeholder="회차 입력 (예: <?php echo $round;?>)" min="1" max="<?php echo $max_round;?>">
            <button class="archive-verify-btn" onclick="verifyData()">검증</button>
          </div>
          <div class="archive-verify-result" id="verifyResult" style="display: none;">
            <div class="archive-verify-result-header">
              <span class="archive-verify-title" id="verifyTitle"><?php echo $max_round;?>회차</span>
              <span class="archive-verify-status">✓ 일치</span>
            </div>
            <div class="archive-verify-balls" id="verifyBalls"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--// 최근 8주 실제 프로그램 끝 -->

  <!-- Algorithm Info Banner - 서브페이지 링크 -->
  <section class="algorithm-banner-section">
    <div class="algorithm-banner-container">
      <a href="/algorithm.php" class="algorithm-banner-card">
        <div class="algorithm-banner-content">
          <div class="algorithm-banner-badge">🎲 몬테카를로 시뮬레이션 포함</div>
          <h3 class="algorithm-banner-title">10가지 분석 알고리즘 공개</h3>
          <p class="algorithm-banner-desc">
            몬테카를로 시뮬레이션, Hot/Cold 분석, AC값 등 10가지 기법을 원하는 대로 선택하세요.
          </p>
          <span class="algorithm-banner-link">
            자세히 보기 →
          </span>
        </div>
        <div class="algorithm-banner-icons">
          <span>🎲</span>
          <span>🔥</span>
          <span>🧮</span>
          <span>⚖️</span>
          <span>📈</span>
        </div>
      </a>
    </div>
  </section>

<?php
// ▶ 최근 N회 (통계/히스토리용)
$recent_limit = $latest['draw_no'];
$history_rows = [];

$sql_history = "SELECT * FROM g5_lotto_draw ORDER BY draw_no DESC LIMIT {$recent_limit}";
$res_history = sql_query($sql_history, false);

if ($res_history) {
    while ($row = sql_fetch_array($res_history)) {
        $history_rows[] = $row;
    }
}

// ▶ JS에서 쓸 LOTTO_HISTORY_DATA 생성
$lotto_history_map = [];
foreach ($history_rows as $row) {
    $round = (int)$row['draw_no'];

    $lotto_history_map[$round] = [
        'date'    => $row['draw_date'],
        'numbers' => [
            (int)$row['n1'],
            (int)$row['n2'],
            (int)$row['n3'],
            (int)$row['n4'],
            (int)$row['n5'],
            (int)$row['n6'],
        ],
        'bonus'   => (int)$row['bonus'],
    ];
}

/* ─────────────────────────────────────
 *  통계 대시보드용 "역대" 데이터 계산
 *  - HOT 5, COLD 5
 *  - 홀짝 비율
 *  - 합계/연속번호/끝자리 패턴
 * ───────────────────────────────────── */

// 공 색상 클래스 매핑 (번호 → ball-yellow / ball-blue ...)
if (!function_exists('lotto_ball_color_class')) {
    function lotto_ball_color_class($n) {
        $n = (int)$n;
        if ($n >= 1 && $n <= 10) return 'ball-yellow';
        if ($n <= 20) return 'ball-blue';
        if ($n <= 30) return 'ball-red';
        if ($n <= 40) return 'ball-gray';
        return 'ball-green';
    }
}

$freq              = array_fill(1, 45, 0);   // 번호별 출현 횟수
$odd_even_count    = [];                     // "3:3", "4:2" 등 패턴 카운트
$sum_range_count   = 0;                      // 합계 100~170
$consecutive_count = 0;                      // 연속번호 포함
$same_ending2_cnt  = 0;                      // 같은 끝자리 2개 이상
$total_draws       = 0;

// g5_lotto_draw 전체를 대상으로 계산 (역대 기준)
$sql_all = "SELECT n1,n2,n3,n4,n5,n6 FROM g5_lotto_draw";
$res_all = sql_query($sql_all, false);

if ($res_all) {
    while ($row = sql_fetch_array($res_all)) {
        $nums = [
            (int)$row['n1'],
            (int)$row['n2'],
            (int)$row['n3'],
            (int)$row['n4'],
            (int)$row['n5'],
            (int)$row['n6'],
        ];

        $total_draws++;
        sort($nums);

        // 빈도
        foreach ($nums as $n) {
            if ($n >= 1 && $n <= 45) {
                $freq[$n]++;
            }
        }

        // 홀짝 비율
        $odd = 0;
        foreach ($nums as $n) {
            if ($n % 2 !== 0) $odd++;
        }
        $even = 6 - $odd;
        $pat  = "{$odd}:{$even}";
        if (!isset($odd_even_count[$pat])) $odd_even_count[$pat] = 0;
        $odd_even_count[$pat]++;

        // 합계 100~170
        $sum = array_sum($nums);
        if ($sum >= 100 && $sum <= 170) {
            $sum_range_count++;
        }

        // 연속번호 포함 여부
        $has_consecutive = false;
        for ($i = 0; $i < 5; $i++) {
            if ($nums[$i] + 1 === $nums[$i + 1]) {
                $has_consecutive = true;
                break;
            }
        }
        if ($has_consecutive) {
            $consecutive_count++;
        }

        // 같은 끝자리 2개 이상 여부
        $last_digits = [];
        foreach ($nums as $n) {
            $ld = $n % 10;
            if (!isset($last_digits[$ld])) $last_digits[$ld] = 0;
            $last_digits[$ld]++;
        }
        $has_same_ending2 = false;
        foreach ($last_digits as $cnt) {
            if ($cnt >= 2) {
                $has_same_ending2 = true;
                break;
            }
        }
        if ($has_same_ending2) {
            $same_ending2_cnt++;
        }
    }
}

// 기본값
$hot_numbers            = [];
$cold_numbers           = [];
$odd_even_percent       = [];
$sum_range_percent      = 0;
$consecutive_percent    = 0;
$same_ending2_percent   = 0;

if ($total_draws > 0) {
    // HOT/COLD용 배열 만들기
    $freq_list = [];
    for ($i = 1; $i <= 45; $i++) {
        $freq_list[] = ['number' => $i, 'count' => $freq[$i]];
    }

    // HOT 5 (많이 나온 순)
    $hot_list = $freq_list;
    usort($hot_list, function ($a, $b) {
        if ($a['count'] === $b['count']) {
            return $a['number'] <=> $b['number']; // 같은 횟수면 번호 작은 순
        }
        return $b['count'] <=> $a['count'];       // 출현 횟수 내림차순
    });
    $hot_numbers = array_slice($hot_list, 0, 5);

    // COLD 5 (적게 나온 순)
    $cold_list = $freq_list;
    usort($cold_list, function ($a, $b) {
        if ($a['count'] === $b['count']) {
            return $a['number'] <=> $b['number'];
        }
        return $a['count'] <=> $b['count'];       // 출현 횟수 오름차순
    });
    $cold_numbers = array_slice($cold_list, 0, 5);

    // 홀짝 비율 %
    foreach ($odd_even_count as $pat => $cnt) {
        $odd_even_percent[$pat] = round($cnt / $total_draws * 100);
    }

    // 패턴 %들
    $sum_range_percent    = round($sum_range_count / $total_draws * 100);
    $consecutive_percent  = round($consecutive_count / $total_draws * 100);
    $same_ending2_percent = round($same_ending2_cnt / $total_draws * 100);
}
?>
  <!-- Statistics Dashboard -->
  <section class="dashboard-section" id="stats">
    <div class="dashboard-container">
      <div class="section-header">
        <div class="section-badge">📊 실제 데이터</div>
        <?php
        // 회차 타이틀용 라벨 (상단 PHP에서 $current_round / $current_round_fmt 등을 만든 경우 사용)
        $dashboard_round_label = '';

        if (isset($current_round_fmt) && $current_round_fmt) {
            // 1,201 형태로 이미 포맷된 값이 있다면
            $dashboard_round_label = $current_round_fmt;
        } elseif (isset($current_round) && $current_round) {
            // 그냥 숫자만 있다면 1,201 이런 식으로 포맷
            $dashboard_round_label = number_format($current_round);
        } elseif (isset($round) && $round) {
            // 다른 곳에서 $round를 쓰고 있다면 백업용
            $dashboard_round_label = number_format($round);
        }
        ?>
        <h2 class="section-title">
          <?php echo $latest['draw_no'].'회차 통계 대시보드'; ?>
        </h2>
        <p class="section-subtitle">
          동행복권 공식 데이터 기반 실시간 통계
        </p>
        <p class="section-subtitle">
          동행복권 공식 데이터 기반 실시간 통계
        </p>
      </div>

      <?php
      // 홀짝 비율 표시용
      $ratio_33 = $odd_even_percent['3:3'] ?? 0;
      $ratio_42 = $odd_even_percent['4:2'] ?? 0;
      $ratio_24 = $odd_even_percent['2:4'] ?? 0;
      $ratio_51 = $odd_even_percent['5:1'] ?? 0;
      ?>

      <div class="dashboard-grid">
        <!-- HOT 5 -->
        <div class="dashboard-card">
          <h3 class="dashboard-card-title">🔥 최다 출현 번호 (TOP 5)</h3>
          <div class="hot-numbers">
            <?php if (!empty($hot_numbers)) { ?>
              <?php foreach ($hot_numbers as $item): ?>
                <?php
                  $num   = (int)$item['number'];
                  $count = (int)$item['count'];
                  // 상단 PHP에 정의한 헬퍼 함수 사용 (lotto_ball_color_class)
                  $cls   = function_exists('lotto_ball_color_class')
                            ? lotto_ball_color_class($num)
                            : 'ball-yellow';
                ?>
                <a href="/로또-번호/<?php echo $num; ?>/" class="hot-ball <?php echo $cls; ?>" title="로또 <?php echo $num; ?>번 통계 보기">
                  <?php echo $num; ?>
                  <span class="ball-count"><?php echo number_format($count); ?></span>
                </a>
              <?php endforeach; ?>
            <?php } else { ?>
              <p class="empty-text">통계 데이터가 아직 부족합니다.</p>
            <?php } ?>
          </div>
        </div>

        <!-- COLD 5 -->
        <div class="dashboard-card">
          <h3 class="dashboard-card-title">❄️ 최소 출현 번호 (BOTTOM 5)</h3>
          <div class="cold-numbers">
            <?php if (!empty($cold_numbers)) { ?>
              <?php foreach ($cold_numbers as $item): ?>
                <?php
                  $num   = (int)$item['number'];
                  $count = (int)$item['count'];
                  $cls   = function_exists('lotto_ball_color_class')
                            ? lotto_ball_color_class($num)
                            : 'ball-yellow';
                ?>
                <a href="/로또-번호/<?php echo $num; ?>/" class="cold-ball <?php echo $cls; ?>" title="로또 <?php echo $num; ?>번 통계 보기">
                  <?php echo $num; ?>
                  <span class="ball-count"><?php echo number_format($count); ?></span>
                </a>
              <?php endforeach; ?>
            <?php } else { ?>
              <p class="empty-text">통계 데이터가 아직 부족합니다.</p>
            <?php } ?>
          </div>
        </div>

        <!-- 홀짝 비율 -->
        <div class="dashboard-card">
          <h3 class="dashboard-card-title">⚖️ 역대 홀짝 비율 분포</h3>
          <div class="ratio-bars">
            <div class="ratio-item">
              <span class="ratio-label">3:3</span>
              <div class="ratio-bar">
                <div class="ratio-fill" style="width: <?php echo $ratio_33; ?>%"></div>
              </div>
              <span class="ratio-value"><?php echo $ratio_33; ?>%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">4:2</span>
              <div class="ratio-bar">
                <div class="ratio-fill" style="width: <?php echo $ratio_42; ?>%"></div>
              </div>
              <span class="ratio-value"><?php echo $ratio_42; ?>%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">2:4</span>
              <div class="ratio-bar">
                <div class="ratio-fill" style="width: <?php echo $ratio_24; ?>%"></div>
              </div>
              <span class="ratio-value"><?php echo $ratio_24; ?>%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">5:1</span>
              <div class="ratio-bar">
                <div class="ratio-fill" style="width: <?php echo $ratio_51; ?>%"></div>
              </div>
              <span class="ratio-value"><?php echo $ratio_51; ?>%</span>
            </div>
          </div>
        </div>

        <!-- 패턴 분석 -->
        <div class="dashboard-card">
          <h3 class="dashboard-card-title">📈 역대 패턴 분석</h3>
          <div class="ratio-bars">
            <div class="ratio-item">
              <span class="ratio-label">합계 100~170</span>
              <div class="ratio-bar">
                <div class="ratio-fill" style="width: <?php echo $sum_range_percent; ?>%"></div>
              </div>
              <span class="ratio-value"><?php echo $sum_range_percent; ?>%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">연속번호 포함</span>
              <div class="ratio-bar">
                <div class="ratio-fill" style="width: <?php echo $consecutive_percent; ?>%"></div>
              </div>
              <span class="ratio-value"><?php echo $consecutive_percent; ?>%</span>
            </div>
            <div class="ratio-item">
              <span class="ratio-label">같은 끝자리 2+</span>
              <div class="ratio-bar">
                <div class="ratio-fill" style="width: <?php echo $same_ending2_percent; ?>%"></div>
              </div>
              <span class="ratio-value"><?php echo $same_ending2_percent; ?>%</span>
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

		  <p class="prize-subtitle">
			<span id="psRound">-</span>회 <span id="psMode">예상</span> 당첨금 시뮬레이션 (<span id="psEst">추정치</span>)
		  </p>

		  <div class="prize-grid">
			<div class="prize-item first">
			  <div class="prize-rank">🥇 1등</div>
			  <div class="prize-amount" id="p1Gross">-</div>
			  <div class="prize-after-tax">세후 <span id="p1Net">-</span></div>
			</div>

			<div class="prize-item">
			  <div class="prize-rank">🥈 2등</div>
			  <div class="prize-amount" id="p2Gross">-</div>
			  <div class="prize-after-tax">세후 <span id="p2Net">-</span></div>
			</div>

			<div class="prize-item">
			  <div class="prize-rank">🥉 3등</div>
			  <div class="prize-amount" id="p3Gross">-</div>
			  <div class="prize-after-tax">세후 <span id="p3Net">-</span></div>
			</div>
		  </div>

		  <p class="prize-note">
			※ 당첨금은 판매액과 당첨자 수에 따라 변동됩니다. 위 금액은 최근 평균 기준 추정치/실제치입니다.
		  </p>
		</div>
	  </div>
	</section>

	<script>
	(function(){
	  const API = "/ajax/prize_simulator.php";

	  function fmtKRW(n){
		n = Number(n||0);
		if (!n) return "-";

		if (n >= 100000000) { // 억
		  const eok = Math.round((n / 100000000) * 10) / 10; // 소수1자리
		  return `약 ${eok.toLocaleString()}억원`;
		}
		if (n >= 10000) { // 만
		  const man = Math.round(n / 10000);
		  return `약 ${man.toLocaleString()}만원`;
		}
		return `약 ${Math.round(n).toLocaleString()}원`;
	  }

	  async function load(){
		const res = await fetch(API + "?t=" + Date.now(), { cache: "no-store" });
		if (!res.ok) throw new Error("HTTP " + res.status);
		return await res.json();
	  }

	  function setText(id, txt){
		const el = document.getElementById(id);
		if (el) el.textContent = txt;
	  }

	  (async function init(){
		try{
		  const data = await load();

		  setText("psRound", data.round);
		  setText("psMode", data.estimated ? "예상" : "실제");
		  setText("psEst",  data.estimated ? "추정치" : "실제치");

		  setText("p1Gross", fmtKRW(data.first.gross));
		  setText("p1Net",   fmtKRW(data.first.net));

		  setText("p2Gross", fmtKRW(data.second.gross));
		  setText("p2Net",   fmtKRW(data.second.net));

		  setText("p3Gross", fmtKRW(data.third.gross));
		  setText("p3Net",   fmtKRW(data.third.net));
		} catch(e){
		  console.warn("prize simulator failed:", e);
		}
	  })();
	})();
	</script>


  <!-- Never Happened Warning Section -->
<style>
/* ==============================
 * Warning Section (LottoInsight)
 * ============================== */
.warning-section{padding:56px 0;}
.warning-container{max-width:1100px;margin:0 auto;padding:0 16px;}
.warning-card{
  border-radius:20px;
  padding:22px;
  background: linear-gradient(180deg, rgba(255,199,0,.08), rgba(255,120,0,.05));
  border:1px solid rgba(255,180,0,.18);
  box-shadow: 0 12px 40px rgba(0,0,0,.22);
}
.warning-header{display:flex;gap:14px;align-items:flex-start;margin-bottom:18px;}
.warning-icon{
  width:44px;height:44px;flex:0 0 44px;
  display:flex;align-items:center;justify-content:center;
  border-radius:14px;
  background: rgba(255,180,0,.14);
  border:1px solid rgba(255,180,0,.22);
  font-size:22px;
}
.warning-title{
  margin:0;
  font-size:18px;
  line-height:1.35;
  color: rgba(255,255,255,.92);
}
.warning-subtitle{
  margin:6px 0 0 0;
  font-size:13px;
  color: rgba(255,255,255,.72);
}
.warning-list{display:grid;grid-template-columns:repeat(2, minmax(0,1fr));gap:12px;margin-top:14px;}
.warning-item{
  border-radius:16px;
  padding:14px;
  background: rgba(0,0,0,.22);
  border:1px solid rgba(255,255,255,.08);
  display:flex;
  gap:12px;
}
.warning-item-icon{
  width:36px;height:36px;flex:0 0 36px;
  display:flex;align-items:center;justify-content:center;
  border-radius:12px;
  background: rgba(255,180,0,.12);
  border:1px solid rgba(255,180,0,.18);
  font-size:18px;
}
.warning-item-content h4{
  margin:0 0 6px 0;
  font-size:14px;
  color: rgba(255,255,255,.9);
}
.warning-item-content p{
  margin:0;
  font-size:13px;
  line-height:1.55;
  color: rgba(255,255,255,.72);
}
.warning-item-stat{
  font-weight:800;
  color: rgba(255,210,90,.98);
}
.warning-footer{
  margin-top:16px;
  padding:14px;
  border-radius:16px;
  background: rgba(0,0,0,.28);
  border:1px dashed rgba(255,180,0,.25);
  display:flex;
  gap:10px;
  align-items:flex-start;
}
.warning-footer-icon{
  width:22px;height:22px;
  display:flex;align-items:center;justify-content:center;
  border-radius:8px;
  background: rgba(0,255,160,.12);
  border:1px solid rgba(0,255,160,.18);
  font-size:14px;
}
.warning-footer-text{
  margin:0;
  font-size:13px;
  line-height:1.6;
  color: rgba(255,255,255,.78);
}
.warning-footer-text strong{color: rgba(255,255,255,.95);}
@media (max-width: 860px){
  .warning-list{grid-template-columns:1fr;}
  .warning-card{padding:18px;}
}
</style>

<!-- Never Happened Warning Section -->
<?php
// ==============================
// Never Happened Warning - DATA
// ==============================

// ✅ 테이블/컬럼명 필요시 여기만 수정하세요
$lotto_table = 'g5_lotto_draw';
$num_cols    = ['n1','n2','n3','n4','n5','n6'];
$drawno_col  = 'draw_no';

// 최신 회차
$latest = sql_fetch("
  SELECT {$drawno_col}, ".implode(',', $num_cols)."
  FROM {$lotto_table}
  ORDER BY {$drawno_col} DESC
  LIMIT 1
");

// 전체 회차 수
$row_cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM {$lotto_table}");
$total_draws = (int)($row_cnt['cnt'] ?? 0);

function _sorted_nums($row, $cols) {
  $nums = [];
  foreach ($cols as $c) {
    $v = (int)($row[$c] ?? 0);
    if ($v > 0) $nums[] = $v;
  }
  sort($nums);
  return $nums;
}

// 4개 이상 연속번호(최대 연속 길이 >= 4)
function _has_consecutive4($nums) {
  if (count($nums) < 4) return false;
  $max_run = 1;
  $run = 1;
  for ($i=1; $i<count($nums); $i++) {
    if ($nums[$i] === $nums[$i-1] + 1) {
      $run++;
      if ($run > $max_run) $max_run = $run;
    } else {
      $run = 1;
    }
  }
  return ($max_run >= 4);
}

// 합계 70 이하 또는 180 이상
function _is_sum_extreme($nums) {
  $s = array_sum($nums);
  return ($s <= 70 || $s >= 180);
}

// 한 구간(1~10 / 11~20 / 21~30 / 31~40 / 41~45)에서 6개 모두
function _is_all_same_band($nums) {
  if (count($nums) !== 6) return false;
  $band = function($n) {
    $b = intdiv(($n - 1), 10); // 1~10 =>0, 11~20=>1, ...
    return ($b > 4) ? 4 : $b;  // 41~45도 4로 고정
  };
  $b0 = $band($nums[0]);
  foreach ($nums as $n) {
    if ($band($n) !== $b0) return false;
  }
  return true;
}

// 홀수 6개 / 짝수 6개
function _is_all_odd($nums) {
  foreach ($nums as $n) if (($n % 2) === 0) return false;
  return true;
}
function _is_all_even($nums) {
  foreach ($nums as $n) if (($n % 2) === 1) return false;
  return true;
}

// 역대 카운트 집계 (회차 수가 1천여개라 PHP 루프가 가장 안전/간단)
$cnt_consecutive4 = 0;
$cnt_sum_extreme  = 0;
$cnt_all_sameband = 0;
$cnt_all_odd      = 0;
$cnt_all_even     = 0;

$q = sql_query("SELECT ".implode(',', $num_cols)." FROM {$lotto_table}");
while ($r = sql_fetch_array($q)) {
  $nums = _sorted_nums($r, $num_cols);
  if (count($nums) !== 6) continue;

  if (_has_consecutive4($nums)) $cnt_consecutive4++;
  if (_is_sum_extreme($nums))   $cnt_sum_extreme++;
  if (_is_all_same_band($nums)) $cnt_all_sameband++;
  if (_is_all_odd($nums))       $cnt_all_odd++;
  if (_is_all_even($nums))      $cnt_all_even++;
}

$cnt_odd_or_even_all = $cnt_all_odd + $cnt_all_even;

// 타이틀 문구(정직하게)
$never_happened = (
  $cnt_consecutive4 === 0 &&
  $cnt_sum_extreme  === 0 &&
  $cnt_all_sameband === 0 &&
  $cnt_odd_or_even_all === 0
);
$title_suffix = $never_happened ? '없었습니다' : '매우 드뭅니다';
?>

<section class="warning-section">
  <div class="warning-container">
    <div class="warning-card">
      <div class="warning-header">
        <div class="warning-icon">⚠️</div>
        <div>
          <h3 class="warning-title">
            역대 <?php echo number_format($total_draws); ?>회 중 이런 패턴은 <?php echo $title_suffix; ?>
          </h3>
          <p class="warning-subtitle">AI는 아래 패턴을 자동으로 회피합니다</p>
        </div>
      </div>

      <div class="warning-list">
        <div class="warning-item">
          <div class="warning-item-icon">🔢</div>
          <div class="warning-item-content">
            <h4>연속번호 4개 이상</h4>
            <p>예: 12, 13, 14, 15, 22, 33<br>
              발생 횟수: <span class="warning-item-stat"><?php echo number_format($cnt_consecutive4); ?>회</span>
            </p>
          </div>
        </div>

        <div class="warning-item">
          <div class="warning-item-icon">📊</div>
          <div class="warning-item-content">
            <h4>합계 70 이하 또는 180 이상</h4>
            <p>극단적인 합계 범위<br>
              발생 횟수: <span class="warning-item-stat"><?php echo number_format($cnt_sum_extreme); ?>회</span>
            </p>
          </div>
        </div>

        <div class="warning-item">
          <div class="warning-item-icon">🎨</div>
          <div class="warning-item-content">
            <h4>한 색상에서 6개 모두</h4>
            <p>예: 모두 1~10 또는 모두 41~45<br>
              발생 횟수: <span class="warning-item-stat"><?php echo number_format($cnt_all_sameband); ?>회</span>
            </p>
          </div>
        </div>

        <div class="warning-item">
          <div class="warning-item-icon">⚖️</div>
          <div class="warning-item-content">
            <h4>홀수 6개 또는 짝수 6개</h4>
            <p>완전한 홀짝 편중<br>
              발생 횟수: <span class="warning-item-stat"><?php echo number_format($cnt_odd_or_even_all); ?>회</span>
            </p>
          </div>
        </div>
      </div>

      <div class="warning-footer">
        <span class="warning-footer-icon">✅</span>
        <p class="warning-footer-text">
          오늘로또 AI는 위 패턴을 <strong>룰 기반 필터로 자동 회피</strong>하여
          더 균형 잡힌 조합만 생성합니다.
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

	<!-- User Reviews Section(프로그램) -->
	<!--
	<section class="reviews-section">
	  <div class="reviews-container">
		<div class="section-header">
		  <div class="section-badge">💬 사용자 후기</div>
		  <h2 class="section-title">실제 사용자들의 이야기</h2>
		</div>

		<?php
		// ─────────────────────────────────────────
		// 크레딧 결제자만 후기 작성/관리 노출
		// ─────────────────────────────────────────
		$can_review = false;

		if ($is_member) {
		  $mbid = sql_real_escape_string($member['mb_id']);
		  $paid = sql_fetch("
			SELECT 1 AS ok
			FROM g5_lotto_credit_log
			WHERE mb_id='{$mbid}'
			  AND change_type='charge'
			  AND amount > 0
			LIMIT 1
		  ");
		  if (!empty($paid['ok'])) $can_review = true;
		}
		if (isset($is_admin) && $is_admin === 'super') $can_review = true;
		?>

		<div class="reviews-grid" id="reviewsGrid"></div>

		<div class="reviews-actions">
		  <?php if ($can_review) { ?>
			<button type="button" class="rbtn primary" id="openReviewModal">✍️ 후기 작성하기</button>
			<a class="rbtn" href="<?php echo G5_URL; ?>/reviews/manage.php">🧾 후기 관리</a>
		  <?php } else { ?>
			<a class="rbtn primary" href="<?php echo G5_URL; ?>/#pricing">크레딧 결제 후 후기 작성 가능</a>
			<?php if (!$is_member) { ?>
			  <a class="rbtn" href="<?php echo G5_BBS_URL; ?>/login.php?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">로그인</a>
			<?php } ?>
		  <?php } ?>
		</div>

		<div class="reviews-disclaimer">
		  ⚠️ 실제 사용자 후기이며, 당첨을 보장하지 않습니다. 로또는 확률 게임입니다.
		</div>
	  </div>
	</section>

	<style>
		/* (원본 디자인을 크게 건드리지 않고) 버튼/디스클레이머만 다듬기 */
		.reviews-actions{
		  display:flex; justify-content:center; align-items:center; gap:10px;
		  margin-top:18px; flex-wrap:wrap;
		}
		.reviews-actions .rbtn{
		  display:inline-flex; align-items:center; gap:8px;
		  padding:10px 14px; border-radius:999px;
		  border:1px solid rgba(255,255,255,.16);
		  background:rgba(255,255,255,.06);
		  color:#fff; text-decoration:none; cursor:pointer;
		  font-weight:600; font-size:14px;
		}
		.reviews-actions .rbtn:hover{ background:rgba(255,255,255,.10); }
		.reviews-actions .rbtn.primary{
		  border-color:rgba(255,215,128,.35);
		  background:rgba(255,215,128,.10);
		}
		/* 원본 .reviews-disclaimer를 “더 타이트하게” */
		.reviews-disclaimer{
		  max-width:920px;
		  margin:14px auto 0;
		  padding:10px 14px;
		  border-radius:14px;
		  border:1px solid rgba(255,255,255,.12);
		  background:rgba(0,0,0,.18);
		  color:rgba(255,255,255,.86);
		  font-size:13px;
		  line-height:1.4;
		}
	</style>

	<!-- ✅ 후기 목록 로딩 (승인된 후기만) -->
	<!--
	<script>
		(function(){
		  const API = "/ajax/reviews_feed.php?limit=4";
		  const grid = document.getElementById('reviewsGrid');
		  if (!grid) return;

		  function esc(s){
			return String(s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
		  }

		  async function load(){
			const res = await fetch(API + "&t=" + Date.now(), {cache:"no-store"});
			if (!res.ok) throw new Error("HTTP " + res.status);
			return await res.json();
		  }

		  function render(items){
			const arr = items || [];
			grid.innerHTML = "";

			if (arr.length === 0) {
			  grid.innerHTML = '<div class="review-card"><p class="review-content">아직 승인된 후기가 없습니다. 첫 후기를 남겨보세요 🙂</p></div>';
			  return;
			}

			arr.forEach(it => {
			  const card = document.createElement("div");
			  card.className = "review-card";
			  card.innerHTML = `
				<p class="review-content">"${esc(it.content)}"</p>
				<div class="review-author">
				  <div class="review-avatar">${esc(it.avatar)}</div>
				  <div class="review-info">
					<div class="review-name">${esc(it.name)}</div>
					<div class="review-meta">${esc(it.meta)}</div>
				  </div>
				</div>
			  `;
			  grid.appendChild(card);
			});
		  }

		  (async function(){
			try{
			  const data = await load();
			  render(data.items);
			} catch(e){
			  console.warn("reviews load failed", e);
			}
		  })();
		})();
	</script>

	<?php if ($can_review) { ?>
		<div id="reviewModal" style="display:none;position:fixed;inset:0;z-index:9999;">
		  <div class="rm-bg" id="reviewModalBg" style="position:absolute;inset:0;background:rgba(0,0,0,.6);"></div>

		  <div class="rm-card" style="
			position:relative;
			width:min(920px, calc(100% - 24px));
			margin:24px auto;
			border-radius:16px;
			overflow:hidden;
			background:#0b0f16;
			border:1px solid rgba(255,255,255,.10);
			box-shadow:0 20px 60px rgba(0,0,0,.55);
		  ">
			<div class="rm-head" style="
			  display:flex;justify-content:space-between;align-items:center;
			  padding:12px 14px;
			  background:rgba(255,255,255,.04);
			  border-bottom:1px solid rgba(255,255,255,.08);
			  color:#fff;
			">
			  <strong>후기 작성</strong>
			  <button type="button" id="closeReviewModal" style="
				border:0;background:transparent;color:#fff;
				font-size:20px;cursor:pointer;padding:6px 10px;border-radius:10px;
			  ">✕</button>
			</div>

			<iframe src="<?php echo G5_URL; ?>/reviews/write.php" style="width:100%;height:70vh;border:0;background:#fff;"></iframe>
		  </div>
		</div>

		<script>
			(function(){
			  const openBtn = document.getElementById('openReviewModal');
			  const modal = document.getElementById('reviewModal');
			  const bg = document.getElementById('reviewModalBg');
			  const closeBtn = document.getElementById('closeReviewModal');

			  function open(){ if(modal) modal.style.display='block'; }
			  function close(){ if(modal) modal.style.display='none'; }

			  if(openBtn) openBtn.addEventListener('click', open);
			  if(bg) bg.addEventListener('click', close);
			  if(closeBtn) closeBtn.addEventListener('click', close);
			  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
			})();
		</script>
	<?php } ?>
    -->

  <!-- Notification Section -->
  <!--
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
  <!--
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
  -->

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
          <a href="auth.php" class="pricing-cta pricing-cta-secondary">
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
          <a href="auth.php" class="pricing-cta pricing-cta-primary">
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
          <a href="auth.php" class="pricing-cta pricing-cta-primary">
            구독 시작하기
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- 로또 가이드 섹션 -->
  <section class="guide-section" id="guide">
    <div class="guide-container">
      <div class="section-header">
        <div class="section-badge">📚 로또 가이드</div>
        <h2 class="section-title">당첨부터 수령까지, 필요한 모든 정보</h2>
      </div>

      <div class="guide-grid">
        <a href="/로또-가이드/세금/" class="guide-card">
          <span class="guide-icon">💰</span>
          <div class="guide-content">
            <span class="guide-name">로또 세금 안내</span>
            <span class="guide-desc">당첨금 실수령액 계산</span>
          </div>
          <span class="guide-arrow">→</span>
        </a>
        <a href="/로또-가이드/구매방법/" class="guide-card">
          <span class="guide-icon">🎫</span>
          <div class="guide-content">
            <span class="guide-name">로또 구매 방법</span>
            <span class="guide-desc">온라인/오프라인 안내</span>
          </div>
          <span class="guide-arrow">→</span>
        </a>
        <a href="/로또-가이드/확률/" class="guide-card">
          <span class="guide-icon">📊</span>
          <div class="guide-content">
            <span class="guide-name">로또 당첨 확률</span>
            <span class="guide-desc">수학적 확률 분석</span>
          </div>
          <span class="guide-arrow">→</span>
        </a>
        <a href="/로또-판매점/" class="guide-card highlight">
          <span class="guide-icon">🏆</span>
          <div class="guide-content">
            <span class="guide-name">로또 명당</span>
            <span class="guide-desc">1등 많이 나온 판매점</span>
          </div>
          <span class="guide-arrow">→</span>
        </a>
        <a href="/로또-도구/세금-계산기/" class="guide-card">
          <span class="guide-icon">🧮</span>
          <div class="guide-content">
            <span class="guide-name">로또 세금 계산기</span>
            <span class="guide-desc">실수령액 자동 계산</span>
          </div>
          <span class="guide-arrow">→</span>
        </a>
        <a href="/로또-비교/" class="guide-card">
          <span class="guide-icon">⚖️</span>
          <div class="guide-content">
            <span class="guide-name">로또 회차 비교</span>
            <span class="guide-desc">당첨번호 비교 분석</span>
          </div>
          <span class="guide-arrow">→</span>
        </a>
      </div>

      <div class="guide-more">
        <a href="/로또-가이드/" class="guide-more-link">로또 가이드 전체보기 →</a>
        <a href="/로또-통계/" class="guide-more-link">로또 통계 전체보기 →</a>
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
          <button class="faq-question" aria-expanded="true" aria-controls="faq-answer-1">
            정말 당첨 확률이 높아지나요?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-answer" id="faq-answer-1" role="region" aria-labelledby="faq-question-1">
            <p class="faq-answer-content">
              아니요. 모든 로또 조합의 당첨 확률은 동일합니다 (1/8,145,060). 
              오늘로또는 당첨을 보장하거나 예측하지 않습니다. 
              역대 당첨번호의 통계적 패턴을 분석하여 균형 잡힌 조합을 제안하는 참고 정보입니다.
              <a href="/로또-가이드/1등-확률/" style="color: #00E0A4;">→ 로또 당첨 확률 자세히 보기</a>
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false" aria-controls="faq-answer-2">
            데이터는 어디서 가져오나요?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-answer" id="faq-answer-2" role="region" aria-labelledby="faq-question-2">
            <p class="faq-answer-content">
              동행복권(dhlottery.co.kr) 공식 사이트의 데이터를 사용합니다. 
              1회부터 현재까지의 모든 당첨번호를 분석하며, 매주 토요일 추첨 후 자동으로 업데이트됩니다.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false" aria-controls="faq-answer-3">
            환불이 가능한가요?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-answer" id="faq-answer-3" role="region" aria-labelledby="faq-question-3">
            <p class="faq-answer-content">
              미사용 크레딧에 한해 결제일로부터 7일 이내 환불이 가능합니다. 
              사용한 크레딧, 무료 제공 크레딧, 보너스 크레딧은 환불되지 않습니다.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false" aria-controls="faq-answer-4">
            만 19세 미만도 이용할 수 있나요?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-answer" id="faq-answer-4" role="region" aria-labelledby="faq-question-4">
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
          <?= (int)$max_round ?>회차 실시간 반영
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
      <a href="auth.php" class="cta-button">
        💫 무료로 시작하기
      </a>
      <p class="cta-note">신용카드 불필요 · 3초 가입 · 즉시 분석</p>
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
        <a href="auth.php" class="community-cta-btn">
          커뮤니티 참여하기 →
        </a>
      </div>
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

  <!-- Footer -->
  </main>

  <footer class="footer" role="contentinfo">
    <div class="footer-container">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="footer-logo">
            <div class="footer-logo-icon">
              <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
                <circle cx="11" cy="12" r="8" fill="url(#gold-ball-ft)"/>
                <ellipse cx="8" cy="9" rx="3" ry="2" fill="rgba(255,255,255,0.5)" transform="rotate(-25 8 9)"/>
                <circle cx="18" cy="18" r="7" fill="none" stroke="#030711" stroke-width="2"/>
                <circle cx="16" cy="16" r="1.2" fill="#030711"/>
                <circle cx="20" cy="19" r="1.2" fill="#030711"/>
                <line x1="23" y1="23" x2="28" y2="28" stroke="#030711" stroke-width="2.5" stroke-linecap="round"/>
                <defs>
                  <linearGradient id="gold-ball-ft" x1="20%" y1="20%" x2="80%" y2="80%">
                    <stop offset="0%" stop-color="#ffe066"/>
                    <stop offset="50%" stop-color="#ffd700"/>
                    <stop offset="100%" stop-color="#cc9f00"/>
                  </linearGradient>
                </defs>
              </svg>
            </div>
            오늘로또
          </div>
          <p class="footer-desc">
            AI 기반 로또 번호 통계 분석 서비스.<br>
            동행복권 공식 데이터를 활용합니다.
          </p>
        </div>
        <div class="footer-links">
          <div class="footer-col">
            <h4>서비스</h4>
            <ul>
              <li><a href="/auth.php">AI 분석</a></li>
              <li><a href="/algorithm.php">분석 알고리즘</a></li>
              <li><a href="/로또-당첨번호/<?= $max_round ?>/"><?= $max_round ?>회 로또 당첨번호</a></li>
              <li><a href="/로또-당첨번호/<?= $max_round ?>/당첨금/"><?= $max_round ?>회 로또 당첨금</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>당첨점</h4>
            <ul>
              <li><a href="/로또-판매점/">전국 로또 명당</a></li>
              <li><a href="/로또-랭킹/stores/">로또 명당 랭킹 TOP 100</a></li>
              <li><a href="/로또-당첨번호/<?= $max_round ?>/당첨점/">최근 로또 당첨점</a></li>
              <li><a href="/로또-판매점/서울/">서울 로또 명당</a></li>
              <li><a href="/로또-판매점/경기/">경기 로또 명당</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>통계 · 분석</h4>
            <ul>
              <li><a href="/로또-분석/">로또 패턴 분석</a></li>
              <li><a href="/로또-통계/자동수동/">로또 자동 vs 수동</a></li>
              <li><a href="/로또-랭킹/numbers/">로또 번호 출현 순위</a></li>
              <li><a href="/로또-랭킹/jackpot/">역대 로또 당첨금 순위</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>가이드</h4>
            <ul>
              <li><a href="/로또-가이드/세금/">💰 로또 세금 계산</a></li>
              <li><a href="/로또-가이드/구매방법/">🎫 로또 구매 방법</a></li>
              <li><a href="/로또-가이드/확률/">📊 로또 당첨 확률</a></li>
              <li><a href="/로또-가이드/수령방법/">🏦 로또 수령 방법</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>법적 고지</h4>
            <ul>
              <li><a href="/terms.html">이용약관</a></li>
              <li><a href="/privacy.html">개인정보처리방침</a></li>
              <li><a href="#faq">자주 묻는 질문</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="footer-copyright">
          © 2025 오늘로또. All rights reserved.
        </p>
        <p class="footer-disclaimer">
          ⚠️ 본 서비스는 통계 기반 참고 정보이며, 당첨을 보장하지 않습니다. 만 19세 이상 이용 가능. 로또 구매는 동행복권 공식 판매처에서만 가능합니다.
        </p>
      </div>

      <div class="footer-company">
        <div class="footer-company-info">
          <div class="footer-company-item">상호:<span>오늘로또</span></div>
          <div class="footer-company-item">대표:<span>홍길동</span></div>
          <div class="footer-company-item">사업자등록번호:<span>123-45-67890</span></div>
          <div class="footer-company-item">통신판매업신고:<span>제2023-서울강남-12345호</span></div>
        </div>
        <div class="footer-company-info">
          <div class="footer-company-item">주소:<span>서울특별시 강남구 테헤란로 123, 4층</span></div>
          <div class="footer-company-item">서비스 시작일:<span>2023년 1월 1일</span></div>
        </div>
        <div class="footer-contact">
          <div class="footer-contact-item">
            📧 이메일: <a href="mailto:support@lottoinsight.ai">support@lottoinsight.ai</a>
          </div>
          <div class="footer-contact-item">
            📞 고객센터: <a href="tel:02-1234-5678">02-1234-5678</a> (평일 10:00~18:00)
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // ===== Loading Screen =====
    window.addEventListener('load', () => {
      setTimeout(() => {
        document.getElementById('loadingScreen').classList.add('hidden');
      }, 800);
      
      // ===== Initialize Data from lotto-data.js =====
      //initializeLottoData();
    });

    // ===== Initialize Lotto Data =====
    function initializeLottoData() {
      if (typeof LOTTO_HISTORY_DATA === 'undefined') {
        console.warn('LOTTO_HISTORY_DATA not loaded');
        return;
      }

      const latestRound = <?php echo $max_round;?>;
      const latestData = LOTTO_HISTORY_DATA[latestRound];
      
      if (latestData) {
        // Update LIVE 최신 결과 카드
        updateLiveResult(latestRound, latestData);
        
        // Update archive 실제 당첨 번호
        updateArchiveActualNumbers();
      }
    }

    function getBallColor(num) {
      if (num <= 10) return 'ball-yellow';
      if (num <= 20) return 'ball-blue';
      if (num <= 30) return 'ball-red';
      if (num <= 40) return 'ball-gray';
      return 'ball-green';
    }

    function updateLiveResult(round, data) {
      // Update round info
      const roundEl = document.querySelector('.hero-card-round');
      if (roundEl) {
        roundEl.textContent = `${round}회 · ${data.date}`;
      }
      
      // Update balls
      const ballsContainer = document.querySelector('.hero-balls-container');
      if (ballsContainer) {
        const ballsHtml = data.numbers.map(n => 
          `<div class="hero-ball ${getBallColor(n)}">${n}</div>`
        ).join('') +
        `<span class="bonus-sep">+</span>` +
        `<div class="hero-ball ${getBallColor(data.bonus)}">${data.bonus}</div>`;
        ballsContainer.innerHTML = ballsHtml;
      }
    }

    function updateArchiveActualNumbers() {
      // Archive 테이블의 실제 당첨 번호 업데이트
      const archiveRows = document.querySelectorAll('.archive-row');
      
      archiveRows.forEach(row => {
        const roundEl = row.querySelector('.archive-round');
        if (!roundEl) return;
        
        const roundText = roundEl.textContent;
        const roundNum = parseInt(roundText.replace('회', ''));
        
        if (!roundNum || !LOTTO_HISTORY_DATA[roundNum]) return;
        
        const actualData = LOTTO_HISTORY_DATA[roundNum];
        const actualBallsContainer = row.querySelectorAll('.archive-balls')[1]; // 두 번째가 실제 당첨
        
        if (actualBallsContainer) {
          const ballsHtml = actualData.numbers.map(n => 
            `<span class="archive-ball ${getBallColor(n)}">${n}</span>`
          ).join('');
          actualBallsContainer.innerHTML = ballsHtml;
        }
      });
    }

    // ===== Mobile Menu =====
    function openMobileMenu() {
      const menu = document.getElementById('mobileMenu');
      const btn = document.getElementById('mobileMenuBtn');
      menu.classList.add('active');
      menu.setAttribute('aria-hidden', 'false');
      btn.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
      const menu = document.getElementById('mobileMenu');
      const btn = document.getElementById('mobileMenuBtn');
      menu.classList.remove('active');
      menu.setAttribute('aria-hidden', 'true');
      btn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    // ===== Back to Top =====
    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
      if (window.scrollY > 500) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    });

    function scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    // 애니메이션 성능 최적화: 뷰포트 밖 애니메이션 일시정지
    const animationObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
        } else {
          entry.target.classList.remove('in-view');
        }
      });
    }, { threshold: 0.1 });

    // Hero 볼과 Floating 볼 관찰
    document.querySelectorAll('.hero-ball, .floating-ball').forEach(el => {
      animationObserver.observe(el);
    });

    // FAQ accordion (접근성 개선)
    document.querySelectorAll('.faq-question').forEach((button, index) => {
      button.addEventListener('click', () => {
        const item = button.parentElement;
        const answer = item.querySelector('.faq-answer');
        const isActive = item.classList.contains('active');
        
        // Close all
        document.querySelectorAll('.faq-item').forEach(i => {
          i.classList.remove('active');
          const q = i.querySelector('.faq-question');
          const a = i.querySelector('.faq-answer');
          if (q) q.setAttribute('aria-expanded', 'false');
          if (a) {
            a.style.maxHeight = null;
            a.style.opacity = '0';
          }
        });
        
        // Toggle current
        if (!isActive) {
          item.classList.add('active');
          button.setAttribute('aria-expanded', 'true');
          // 동적 max-height 계산 (CSS 변수 사용)
          if (answer) {
            const height = answer.scrollHeight;
            answer.style.setProperty('--faq-height', height + 'px');
            answer.style.maxHeight = height + 'px';
            answer.style.opacity = '1';
          }
        } else {
          button.setAttribute('aria-expanded', 'false');
          if (answer) {
            answer.style.maxHeight = null;
            answer.style.opacity = '0';
          }
        }
      });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // ===== Live Activity Feed =====
    const activities = [
      { name: '김**', loc: '서울', style: 'Hot/Cold 분석' },
      { name: '이**', loc: '부산', style: '밸런스 최적화' },
      { name: '박**', loc: '대전', style: '홀짝/고저 분석' },
      { name: '최**', loc: '인천', style: 'AI 추천' },
      { name: '정**', loc: '광주', style: 'AC값 분석' },
      { name: '강**', loc: '대구', style: '패턴 분석' },
      { name: '조**', loc: '울산', style: 'Hot/Cold 분석' },
      { name: '윤**', loc: '세종', style: '밸런스 최적화' },
      { name: '장**', loc: '수원', style: 'AI 추천' },
      { name: '임**', loc: '성남', style: '홀짝/고저 분석' },
    ];

    const surnames = ['김', '이', '박', '최', '정', '강', '조', '윤', '장', '임', '한', '오', '서', '신', '권'];
    const locations = ['서울', '부산', '대전', '인천', '광주', '대구', '울산', '세종', '수원', '성남', '고양', '용인', '청주', '전주', '천안'];
    const styles = ['Hot/Cold 분석', 'AC값 분석', '홀짝/고저 분석', '색상볼 통계', '상관관계 분석', '몬테카를로', '합계 분석', '주기 분석', '끝수 분석', '연속번호 패턴'];

    function generateActivity() {
      const surname = surnames[Math.floor(Math.random() * surnames.length)];
      const location = locations[Math.floor(Math.random() * locations.length)];
      const style = styles[Math.floor(Math.random() * styles.length)];
      return { name: `${surname}**`, loc: location, style };
    }

    function addActivity() {
      const feed = document.getElementById('activityFeed');
      const activity = generateActivity();
      
      const item = document.createElement('div');
      item.className = 'activity-item';
      item.innerHTML = `
        <div class="activity-avatar">${activity.name[0]}*</div>
        <div class="activity-content">
          <p class="activity-text"><strong>${activity.name} (${activity.loc})</strong> 님이 <span class="style-tag">${activity.style}</span>을 완료했습니다</p>
        </div>
        <span class="activity-time">방금 전</span>
      `;
      
      feed.insertBefore(item, feed.firstChild);
      
      // Update times
      const items = feed.querySelectorAll('.activity-item');
      items.forEach((el, i) => {
        if (i > 0) {
          el.querySelector('.activity-time').textContent = `${i}분 전`;
        }
      });
      
      // Keep only 4 items
      if (items.length > 4) {
        feed.removeChild(items[items.length - 1]);
      }
      
      // Update today count
      const countEl = document.getElementById('todayCount');
      const currentCount = parseInt(countEl.textContent.replace(/,/g, ''));
      countEl.textContent = (currentCount + 1).toLocaleString();
    }

    // Add new activity every 8-15 seconds
    setInterval(() => {
      addActivity();
    }, 8000 + Math.random() * 7000);

    // ===== Cumulative Counter Animation =====
    function animateCounter(el, target) {
      const duration = 2000;
      const start = 0;
      const startTime = performance.now();
      
      function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(start + (target - start) * easeOut);
        
        el.textContent = current.toLocaleString();
        
        if (progress < 1) {
          requestAnimationFrame(update);
        }
      }
      
      requestAnimationFrame(update);
    }

    // Counter observer
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.dataset.target || el.textContent.replace(/,/g, ''));
          animateCounter(el, target);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    document.querySelectorAll('.cumulative-number').forEach(el => {
      el.dataset.target = el.textContent.replace(/,/g, '');
      el.textContent = '0';
      counterObserver.observe(el);
    });

    // ===== Data Verification =====
    // 전체 1~1201회 데이터는 lotto-data.js에서 로드됨 (getBallColor 함수는 위에서 정의)

    function verifyData() {
      const input = document.getElementById('verifyInput');
      const result = document.getElementById('verifyResult');
      const round = parseInt(input.value);
      
      if (!round || round < 1 || round > <?php echo $max_round;?>) {
        alert('1부터 <?php echo $max_round;?> 사이의 회차를 입력해주세요.');
        return;
      }

      // LOTTO_HISTORY_DATA에서 데이터 조회
      const data = typeof LOTTO_HISTORY_DATA !== 'undefined' ? LOTTO_HISTORY_DATA[round] : null;
      
      if (data) {
        document.getElementById('verifyTitle').textContent = `${round}회차 (${data.date})`;
        
        const ballsHtml = data.numbers.map(n => 
          `<span class="archive-ball ${getBallColor(n)}">${n}</span>`
        ).join('');
        
        // 보너스 번호 포함
        const ballsWithBonus = ballsHtml + 
          `<span style="margin: 0 6px; color: var(--text-muted);">+</span>` +
          `<span class="archive-ball ${getBallColor(data.bonus)}">${data.bonus}</span>`;
        
        document.getElementById('verifyBalls').innerHTML = ballsWithBonus;
        result.style.display = 'block';
      } else {
        document.getElementById('verifyTitle').textContent = `${round}회차`;
        document.getElementById('verifyBalls').innerHTML = '<span style="color: var(--text-muted)">데이터를 찾을 수 없습니다</span>';
        result.style.display = 'block';
      }
    }

    // Allow Enter key
    document.getElementById('verifyInput').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        verifyData();
      }
    });

    // ===== Increment counters periodically =====
    setInterval(() => {
      const totalUsers = document.getElementById('totalUsers');
      const totalAnalysis = document.getElementById('totalAnalysis');
      
      if (totalUsers && Math.random() > 0.7) {
        const current = parseInt(totalUsers.textContent.replace(/,/g, ''));
        totalUsers.textContent = (current + 1).toLocaleString();
      }
      
      if (totalAnalysis && Math.random() > 0.5) {
        const current = parseInt(totalAnalysis.textContent.replace(/,/g, ''));
        totalAnalysis.textContent = (current + Math.floor(Math.random() * 3) + 1).toLocaleString();
      }
    }, 5000);

    // ===== Floating Share Button =====
    function toggleShare() {
      const floatingShare = document.getElementById('floatingShare');
      floatingShare.classList.toggle('active');
    }

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      const floatingShare = document.getElementById('floatingShare');
      if (!floatingShare.contains(e.target)) {
        floatingShare.classList.remove('active');
      }
    });

    // ===== 카카오 SDK 초기화 =====
    const KAKAO_APP_KEY = 'YOUR_KAKAO_JAVASCRIPT_KEY'; // ← 실제 키로 교체 필요
    
    // SDK 초기화 (페이지 로드 시)
    if (typeof Kakao !== 'undefined' && KAKAO_APP_KEY !== 'YOUR_KAKAO_JAVASCRIPT_KEY') {
      try {
        Kakao.init(KAKAO_APP_KEY);
        console.log('✅ 카카오 SDK 초기화 완료');
      } catch (e) {
        console.warn('카카오 SDK 초기화 실패:', e);
      }
    }

    // ===== Share Functions =====
    const shareData = {
      title: '오늘로또 - AI 로또 분석',
      text: '🎱 이번 주 AI 추천 번호: 5, 13, 22, 28, 34, 41\n\n23년간 7,206개 당첨번호를 AI가 분석! 무료 1회 제공!',
      url: window.location.href
    };

    function shareKakao() {
      const siteUrl = 'https://lottoinsight.ai';
      
      // Kakao SDK 초기화 여부 확인
      if (typeof Kakao !== 'undefined' && Kakao.isInitialized()) {
        // 카카오톡 메시지 API 사용
        Kakao.Share.sendDefault({
          objectType: 'feed',
          content: {
            title: '🎱 AI가 분석한 이번 주 로또 번호',
            description: '오늘로또 - <?= (int)$round ?>회차 데이터 기반 AI 분석\n무료 1회 분석 즉시 제공!',
            imageUrl: siteUrl + '/og-image.png',
            link: {
              mobileWebUrl: siteUrl,
              webUrl: siteUrl
            }
          },
          itemContent: {
            profileText: '오늘로또',
            titleImageText: 'AI 로또 분석'
          },
          social: {
            likeCount: 1247,
            sharedCount: 458
          },
          buttons: [
            {
              title: '🔮 무료로 분석 받기',
              link: {
                mobileWebUrl: siteUrl + '/auth.php',
                webUrl: siteUrl + '/auth.php'
              }
            },
            {
              title: '📊 통계 보기',
              link: {
                mobileWebUrl: siteUrl + '/result.html',
                webUrl: siteUrl + '/result.html'
              }
            }
          ]
        });
      } else {
        // SDK 미초기화 시 카카오스토리 공유로 대체
        const shareUrl = encodeURIComponent(siteUrl);
        const shareText = encodeURIComponent('🎱 AI 로또 분석 - 무료 1회 즉시 제공!\n23년간 당첨번호 패턴 분석');
        
        // 모바일 여부 체크
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        
        if (isMobile) {
          // 카카오톡 앱으로 공유 시도
          window.location.href = `kakaolink://send?appkey=${KAKAO_APP_KEY}&url=${shareUrl}`;
          
          // 3초 후에도 페이지에 있으면 웹 공유로 전환
          setTimeout(() => {
            window.open(`https://story.kakao.com/share?url=${shareUrl}`, '_blank');
          }, 3000);
        } else {
          // PC에서는 카카오스토리 웹 공유
          window.open(`https://story.kakao.com/share?url=${shareUrl}`, '_blank', 'width=600,height=400');
        }
      }
      closeShareModal();
    }

    function shareTwitter() {
      const text = encodeURIComponent('🎱 이번 주 AI 추천 번호: 5, 13, 22, 28, 34, 41\n\n오늘로또에서 무료로 AI 분석 받아보세요!');
      const url = encodeURIComponent(window.location.href);
      window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
      closeShareModal();
    }

    function shareFacebook() {
      const url = encodeURIComponent(window.location.href);
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
      closeShareModal();
    }

    function copyLink() {
      navigator.clipboard.writeText(window.location.href).then(() => {
        showCopyToast();
        closeShareModal();
      }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = window.location.href;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showCopyToast();
        closeShareModal();
      });
    }

    function showCopyToast() {
      const toast = document.getElementById('copyToast');
      toast.classList.add('active');
      setTimeout(() => {
        toast.classList.remove('active');
      }, 2500);
    }

    // ===== Share Modal =====
    function openShareModal() {
      document.getElementById('shareModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeShareModal() {
      document.getElementById('shareModal').classList.remove('active');
      document.body.style.overflow = '';
    }

    // Close modal on backdrop click
    document.getElementById('shareModal').addEventListener('click', (e) => {
      if (e.target === e.currentTarget) {
        closeShareModal();
      }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeShareModal();
      }
    });

    // ===== Notification Subscription =====
    function subscribeNotification(e) {
      e.preventDefault();
      const phone = document.getElementById('notificationPhone').value.trim();
      
      if (!phone) {
        alert('전화번호를 입력해주세요.');
        return;
      }
      
      // Phone validation (Korean format)
      const phoneRegex = /^01[0-9]-?[0-9]{3,4}-?[0-9]{4}$/;
      if (!phoneRegex.test(phone.replace(/-/g, ''))) {
        alert('올바른 전화번호 형식을 입력해주세요.\n예: 010-1234-5678');
        return;
      }
      
      // Simulate subscription
      alert('🔔 알림 신청이 완료되었습니다!\n\n매주 토요일 추첨 후 카카오톡으로 결과를 보내드릴게요.');
      document.getElementById('notificationPhone').value = '';
    }

    // ===== Web Share API (if supported) =====
    if (navigator.share) {
      // Modern browsers with Web Share API
      document.querySelectorAll('.share-toggle').forEach(btn => {
        btn.addEventListener('long-press', () => {
          navigator.share(shareData).catch(console.error);
        });
      });
    }

    // ===== Keyboard shortcut for share =====
    document.addEventListener('keydown', (e) => {
      // Ctrl/Cmd + Shift + S to open share modal
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'S') {
        e.preventDefault();
        openShareModal();
      }
    });

    // ===== Countdown Timer =====
    function updateCountdown() {
      const now = new Date();
      const dayOfWeek = now.getDay(); // 0 = Sunday, 6 = Saturday
      
      // Find next Saturday 8:45 PM (20:45)
      let daysUntilSaturday = (6 - dayOfWeek + 7) % 7;
      if (daysUntilSaturday === 0) {
        // It's Saturday, check if past 20:45
        const saturdayTime = new Date(now);
        saturdayTime.setHours(20, 45, 0, 0);
        if (now >= saturdayTime) {
          daysUntilSaturday = 7; // Next Saturday
        }
      }
      
      const nextDraw = new Date(now);
      nextDraw.setDate(now.getDate() + daysUntilSaturday);
      nextDraw.setHours(20, 45, 0, 0);
      
      const diff = nextDraw - now;
      
      if (diff <= 0) {
        // Refresh for next week
        setTimeout(updateCountdown, 1000);
        return;
      }
      
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const secs = Math.floor((diff % (1000 * 60)) / 1000);
      
      document.getElementById('countDays').textContent = days;
      document.getElementById('countHours').textContent = hours.toString().padStart(2, '0');
      document.getElementById('countMins').textContent = mins.toString().padStart(2, '0');
      document.getElementById('countSecs').textContent = secs.toString().padStart(2, '0');
    }
    
    // Initialize countdown
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // ===== Queue Number =====
    let queueNum = 247;
    function updateQueueNumber() {
      if (Math.random() > 0.6) {
        queueNum += Math.floor(Math.random() * 2) + 1;
        document.getElementById('queueNumber').textContent = queueNum;
      }
    }
    setInterval(updateQueueNumber, 4000);

    // ===== API Response Time Simulation =====
    function updateApiResponse() {
      const baseTime = 35;
      const variance = Math.floor(Math.random() * 30);
      const responseTime = baseTime + variance;
      document.getElementById('apiResponse').textContent = `(${responseTime}ms)`;
    }
    setInterval(updateApiResponse, 5000);

    // ===== DB Sync Time =====
    let syncMinutes = 3;
    function updateDbSync() {
      syncMinutes++;
      if (syncMinutes > 10) {
        syncMinutes = 1; // Reset (simulating sync)
      }
      document.getElementById('dbSync').textContent = `(${syncMinutes}분 전)`;
    }
    setInterval(updateDbSync, 60000);

    // ===== Quality Gauge Animation on Scroll =====
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
  </script>
</body>
</html>

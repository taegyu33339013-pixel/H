<?php
/**
 * /stores/detail.php - 판매점 상세 페이지
 * 
 * URL 패턴: /stores/view/{slug} 또는 /stores/detail.php?id={store_id}
 * SEO 최적화된 개별 판매점 페이지
 */

// 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 판매점 라이브러리 로드
include_once(G5_PATH . '/lib/lotto_store.lib.php');

// 파라미터 파싱
$store_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$region1 = '';
$region2 = '';
$region3 = '';
$store_slug = '';

// URL 경로 파싱 - 로또로직스 스타일: /stores/서울특별시/강남구/역삼동/판매점명-고유ID
$request_uri = urldecode($_SERVER['REQUEST_URI']);

// 지역 계층 구조 URL 패턴
if (preg_match('/\/stores\/([^\/]+)\/([^\/]+)\/([^\/]+)\/([^\/]+)/', $request_uri, $matches)) {
    // 4단계: 시도/시군구/읍면동/판매점명-ID
    $region1 = trim($matches[1]);
    $region2 = trim($matches[2]);
    $region3 = trim($matches[3]);
    $store_slug = trim($matches[4]);
    
    // 판매점명-ID에서 ID 추출
    if (preg_match('/-(\d+)$/', $store_slug, $id_match)) {
        $store_id = (int)$id_match[1];
    }
} elseif (preg_match('/\/stores\/view\/([^\/\?]+)/', $request_uri, $matches)) {
    // 기존 형식 지원 (하위 호환)
    $slug = urldecode($matches[1]);
}

// 판매점 조회
$store = null;
if ($store_id > 0) {
    $store = li_get_store_by_id($store_id);
} elseif ($slug) {
    $store = li_get_store_by_name($slug);
}

if (!$store) {
    header("HTTP/1.0 404 Not Found");
    include($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    exit;
}

// 지역 정보 설정 (DB에서 가져오거나 URL에서 추출)
$store_region1 = $store['region1'] ?? $region1 ?: '';
$store_region2 = $store['region2'] ?? $region2 ?: '';
$store_region3 = $region3 ?: '';

// 주소에서 읍면동 추출 (없는 경우)
if (empty($store_region3) && !empty($store['address'])) {
    if (preg_match('/([가-힣]+(?:동|읍|면|리))/u', $store['address'], $dong_match)) {
        $store_region3 = $dong_match[1];
    }
}

// 판매점명에서 slug 생성
$store_name_slug = preg_replace('/[^가-힣a-zA-Z0-9\s-]/u', '', $store['store_name'] ?? $store['name'] ?? '');
$store_name_slug = preg_replace('/\s+/', '-', trim($store_name_slug));

// 당첨 이력 조회
$wins = li_get_store_win_history($store['store_id'], 50);

// 최신 회차
$latest = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($latest['max_round'] ?? 0);

// 공 색상 함수
function get_ball_color($n) {
    if ($n <= 10) return 'yellow';
    if ($n <= 20) return 'blue';
    if ($n <= 30) return 'red';
    if ($n <= 40) return 'gray';
    return 'green';
}

// SEO 데이터 - 로또로직스 스타일
$store_name = htmlspecialchars($store['store_name'] ?? $store['name'] ?? '');
$store_address = htmlspecialchars($store['address'] ?? '');
$wins_1st = (int)($store['wins_1st'] ?? 0);
$wins_2nd = (int)($store['wins_2nd'] ?? 0);

$region_full = trim(implode(' ', array_filter([$store_region1, $store_region2, $store_region3])));
$page_title = $store_name . " 로또 판매점";
// SEO Description 강화 - 더 상세하고 키워드 풍부하게
$win_desc = [];
if ($wins_1st > 0) {
    $win_desc[] = "1등 당첨 {$wins_1st}회";
    if ($wins_1st >= 3) $win_desc[] = "명당 판매점";
}
if ($wins_2nd > 0) {
    $win_desc[] = "2등 당첨 {$wins_2nd}회";
}
$win_text = !empty($win_desc) ? implode(", ", $win_desc) : "로또 판매점";

$page_desc = "로또 판매점 " . $store_name . " (" . $store_region1 . " " . $store_region2 . ($store_region3 ? " " . $store_region3 : "") . " " . $store_address . ") 상세정보. " . $win_text . ". 동행복권 공식 판매점, 로또 구매 가능, 당첨 이력 확인, 주변 판매점 정보 제공.";

// 로또로직스 스타일 URL 생성
$canonical_url = "https://lottoinsight.ai/stores/";
if ($store_region1) $canonical_url .= urlencode($store_region1) . "/";
if ($store_region2) $canonical_url .= urlencode($store_region2) . "/";
if ($store_region3) $canonical_url .= urlencode($store_region3) . "/";
$canonical_url .= urlencode($store_name_slug) . "-" . $store['store_id'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags -->
  <title><?= $page_title ?> | 오늘로또</title>
  <meta name="description" content="<?= $page_desc ?>">
  <meta name="keywords" content="<?= $store_name ?>, <?= htmlspecialchars($store_region1) ?> 로또, 로또 판매점, 로또 명당, 1등 당첨점, 2등 당첨점, <?= htmlspecialchars($store_region2) ?><?= $store_region3 ? ', ' . htmlspecialchars($store_region3) : '' ?>, 동행복권, 로또 구매처, <?= $wins_1st > 0 ? '명당, ' : '' ?>로또 당첨점, <?= htmlspecialchars($store_region1) ?> 복권방, <?= htmlspecialchars($store_region2) ?> 로또판매점">
  <meta name="robots" content="index, follow">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="<?= $canonical_url ?>">
  
  <!-- Open Graph - 로또로직스 초월 강화 버전 -->
  <meta property="og:type" content="place">
  <meta property="og:url" content="<?= $canonical_url ?>">
  <meta property="og:title" content="<?= $page_title ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:locale" content="ko_KR">
  <meta property="og:site_name" content="오늘로또">
  <meta property="og:image" content="https://lottoinsight.ai/images/og-store.jpg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="<?= $store_name ?> 로또 판매점">
  <meta property="place:location:latitude" content="<?= !empty($store['latitude']) ? (float)$store['latitude'] : '' ?>">
  <meta property="place:location:longitude" content="<?= !empty($store['longitude']) ? (float)$store['longitude'] : '' ?>">
  
  <!-- Twitter Card - 강화된 버전 -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $page_title ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="twitter:image" content="https://lottoinsight.ai/images/og-store.jpg">
  <meta name="twitter:site" content="@lottoinsight">
  <meta name="twitter:creator" content="@lottoinsight">
  
  <!-- 추가 SEO 메타 태그 - 강화 -->
  <meta name="author" content="오늘로또">
  <meta name="publisher" content="오늘로또">
  <meta name="copyright" content="© <?= date('Y') ?> 오늘로또">
  <meta name="geo.region" content="KR">
  <meta name="geo.placename" content="<?= htmlspecialchars($store_region1) ?> <?= htmlspecialchars($store_region2) ?>">
  <?php if (!empty($store['latitude']) && !empty($store['longitude'])): ?>
  <meta name="geo.position" content="<?= (float)$store['latitude'] ?>;<?= (float)$store['longitude'] ?>">
  <meta name="ICBM" content="<?= (float)$store['latitude'] ?>, <?= (float)$store['longitude'] ?>">
  <?php endif; ?>
  
  <!-- Article 메타 태그 (콘텐츠 페이지로 인식) -->
  <meta property="article:author" content="오늘로또">
  <meta property="article:published_time" content="<?= date('Y-m-d\TH:i:s+09:00', strtotime($store['created_at'] ?? 'now')) ?>">
  <meta property="article:modified_time" content="<?= date('Y-m-d\TH:i:s+09:00', strtotime($store['updated_at'] ?? 'now')) ?>">
  <meta property="article:section" content="로또 판매점">
  <meta property="article:tag" content="로또, 판매점, <?= htmlspecialchars($store_region1) ?>, <?= htmlspecialchars($store_region2) ?><?= $store_region3 ? ', ' . htmlspecialchars($store_region3) : '' ?>">
  
  <!-- 성능 최적화 -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="dns-prefetch" href="https://www.google-analytics.com">
  <link rel="dns-prefetch" href="https://www.googletagmanager.com">
  <link rel="prefetch" href="/stores/">
  <?php if ($store_region1): ?>
  <link rel="prefetch" href="/stores/<?= urlencode($store_region1) ?>/">
  <?php endif; ?>
  
  <!-- Structured Data - 로또로직스 초월 강화 버전 -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "https://lottoinsight.ai/#organization",
        "name": "오늘로또",
        "alternateName": "로또인사이트",
        "url": "https://lottoinsight.ai",
        "logo": "https://lottoinsight.ai/images/logo.png",
        "description": "AI 기반 로또 번호 분석 및 판매점 정보 제공 서비스"
      },
      {
        "@type": "BreadcrumbList",
        "@id": "<?= $canonical_url ?>#breadcrumblist",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "전국",
            "item": "https://lottoinsight.ai/stores"
          }
          <?php 
          $position = 2;
          if ($store_region1): 
          ?>
          ,{
            "@type": "ListItem",
            "position": <?= $position++ ?>,
            "name": "<?= htmlspecialchars($store_region1) ?>",
            "item": "https://lottoinsight.ai/stores/<?= urlencode($store_region1) ?>"
          }
          <?php endif; ?>
          <?php if ($store_region2): ?>
          ,{
            "@type": "ListItem",
            "position": <?= $position++ ?>,
            "name": "<?= htmlspecialchars($store_region2) ?>",
            "item": "https://lottoinsight.ai/stores/<?= urlencode($store_region1) ?>/<?= urlencode($store_region2) ?>"
          }
          <?php endif; ?>
          <?php if ($store_region3): ?>
          ,{
            "@type": "ListItem",
            "position": <?= $position++ ?>,
            "name": "<?= htmlspecialchars($store_region3) ?>",
            "item": "https://lottoinsight.ai/stores/<?= urlencode($store_region1) ?>/<?= urlencode($store_region2) ?>/<?= urlencode($store_region3) ?>"
          }
          <?php endif; ?>
          ,{
            "@type": "ListItem",
            "position": <?= $position++ ?>,
            "name": "<?= $store_name ?>"
          }
        ]
      },
      {
        "@type": "WebPage",
        "@id": "<?= $canonical_url ?>#webpage",
        "url": "<?= $canonical_url ?>",
        "name": "<?= $page_title ?>",
        "headline": "<?= $page_title ?>",
        "description": "<?= htmlspecialchars($page_desc) ?>",
        "isPartOf": {
          "@id": "https://lottoinsight.ai/#website"
        },
        "breadcrumb": {
          "@id": "<?= $canonical_url ?>#breadcrumblist"
        },
        "datePublished": "<?= date('Y-m-d\TH:i:s+09:00', strtotime($store['created_at'] ?? 'now')) ?>",
        "dateModified": "<?= date('Y-m-d\TH:i:s+09:00', strtotime($store['updated_at'] ?? 'now')) ?>",
        "inLanguage": "ko-KR",
        "publisher": {
          "@id": "https://lottoinsight.ai/#organization"
        },
        "mainEntity": {
          "@id": "<?= $canonical_url ?>#store"
        },
        "about": {
          "@type": "Thing",
          "name": "로또 판매점",
          "description": "<?= htmlspecialchars($store_region1) ?> <?= htmlspecialchars($store_region2) ?> 지역 로또 판매점"
        }
      },
      {
        "@type": "LocalBusiness",
        "@id": "<?= $canonical_url ?>#store",
        "name": "<?= $store_name ?>",
        "description": "로또 판매점 - 1등 <?= $wins_1st ?>회, 2등 <?= $wins_2nd ?>회 당첨. <?= htmlspecialchars($store_region1) ?> <?= htmlspecialchars($store_region2) ?> 지역 로또 명당 판매점.",
        "image": "https://lottoinsight.ai/images/store-default.jpg",
    "address": {
      "@type": "PostalAddress",
          "streetAddress": "<?= $store_address ?>",
          "addressRegion": "<?= htmlspecialchars($store_region1) ?>",
          "addressLocality": "<?= htmlspecialchars($store_region2) ?><?= $store_region3 ? ' ' . htmlspecialchars($store_region3) : '' ?>",
          "addressCountry": "KR",
          "postalCode": ""
        }
        <?php if (!empty($store['latitude']) && !empty($store['longitude'])): ?>
        ,"geo": {
          "@type": "GeoCoordinates",
          "latitude": "<?= (float)$store['latitude'] ?>",
          "longitude": "<?= (float)$store['longitude'] ?>"
        }
        <?php endif; ?>
        ,"aggregateRating": {
      "@type": "AggregateRating",
          "ratingValue": "<?= min(5, 3 + ($wins_1st * 0.3)) ?>",
          "reviewCount": "<?= $wins_1st + $wins_2nd ?>",
          "bestRating": "5",
          "worstRating": "1"
        },
        "priceRange": "무료",
        "telephone": "<?= htmlspecialchars($store['phone'] ?? '') ?>",
        "url": "<?= $canonical_url ?>",
        "sameAs": [],
        "openingHoursSpecification": {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
          "opens": "09:00",
          "closes": "22:00"
        },
        "paymentAccepted": "현금, 카드",
        "currenciesAccepted": "KRW"
      }
      <?php if (!empty($wins)): ?>
      ,{
        "@type": "ItemList",
        "@id": "<?= $canonical_url ?>#winhistory",
        "name": "<?= $store_name ?> 당첨 이력",
        "numberOfItems": <?= count($wins) ?>,
        "itemListElement": [
          <?php
          $win_items = [];
          foreach (array_slice($wins, 0, 10) as $idx => $win) {
            $draw_no = (int)($win['draw_no'] ?? 0);
            $rank = (int)($win['rank'] ?? 1);
            $win_items[] = '{
              "@type": "ListItem",
              "position": ' . ($idx + 1) . ',
              "item": {
                "@type": "Event",
                "name": "로또 ' . $draw_no . '회 ' . $rank . '등 당첨",
                "startDate": "' . date('Y-m-d', strtotime($win['draw_date'] ?? 'now')) . '",
                "location": {
                  "@id": "' . $canonical_url . '#store"
                }
              }
            }';
          }
          echo implode(",\n          ", $win_items);
          ?>
        ]
      }
      <?php endif; ?>
    ]
  }
  </script>
  
  <!-- FAQPage Schema - 판매점 상세 FAQ -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "<?= $store_name ?>에서 로또를 구매할 수 있나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "네, <?= $store_name ?> (<?= $store_address ?>)에서 동행복권 로또를 구매할 수 있습니다. 이 판매점은 1등 <?= $wins_1st ?>회, 2등 <?= $wins_2nd ?>회 당첨 이력이 있습니다."
        }
      },
      {
        "@type": "Question",
        "name": "<?= $store_name ?>는 명당 판매점인가요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= $store_name ?>는 1등 <?= $wins_1st ?>회, 2등 <?= $wins_2nd ?>회 당첨 이력이 있습니다. <?= $wins_1st >= 3 ? '명당 판매점으로 분류됩니다.' : ($wins_1st >= 1 ? '1등 당첨 이력이 있는 판매점입니다.' : '2등 당첨 이력이 있는 판매점입니다.') ?> 다만 로또는 확률 게임이므로 특정 판매점에서 당첨 확률이 높다는 것은 통계적 오류입니다."
        }
      },
      {
        "@type": "Question",
        "name": "<?= $store_name ?>의 최근 당첨 이력은?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= $store_name ?>의 최근 당첨 이력은 페이지 하단의 '당첨 이력' 섹션에서 확인할 수 있습니다. 총 1등 <?= $wins_1st ?>회, 2등 <?= $wins_2nd ?>회 당첨 기록이 있습니다."
    }
      }
    ]
  }
  </script>

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
    :root {
      --primary-dark: #050a15;
      --primary: #0d1526;
      --secondary: #1a2744;
      --accent-cyan: #00E0A4;
      --accent-gold: #FFD75F;
      --accent-purple: #8B5CF6;
      --text-primary: #ffffff;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --gradient-cyan: linear-gradient(135deg, #00E0A4 0%, #00D4FF 100%);
      --gradient-gold: linear-gradient(135deg, #FFD75F 0%, #FF9F43 100%);
      --gradient-mesh: radial-gradient(at 40% 20%, rgba(0, 224, 164, 0.15) 0px, transparent 50%),
                       radial-gradient(at 80% 0%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
      --glass-border: rgba(255, 255, 255, 0.08);
      --ball-yellow: linear-gradient(145deg, #ffd700 0%, #f59e0b 100%);
      --ball-blue: linear-gradient(145deg, #3b82f6 0%, #1d4ed8 100%);
      --ball-red: linear-gradient(145deg, #ef4444 0%, #b91c1c 100%);
      --ball-gray: linear-gradient(145deg, #6b7280 0%, #374151 100%);
      --ball-green: linear-gradient(145deg, #22c55e 0%, #15803d 100%);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', 'Pretendard', sans-serif;
      background: var(--primary-dark);
      background-image: var(--gradient-mesh);
      background-attachment: fixed;
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }

    /* Navigation */
    .nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      padding: 16px 24px;
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
      gap: 10px;
      text-decoration: none;
      color: inherit;
    }

    .nav-logo-icon {
      width: 36px;
      height: 36px;
      background: var(--gradient-cyan);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }

    .nav-logo-text {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .nav-links {
      display: flex;
      gap: 8px;
    }

    .nav-link {
      padding: 10px 20px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .nav-link:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* Main */
    .main {
      max-width: 900px;
      margin: 0 auto;
      padding: 100px 24px 60px;
    }

    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .breadcrumb a {
      color: var(--text-muted);
      text-decoration: none;
    }

    .breadcrumb a:hover {
      color: var(--accent-cyan);
    }

    /* Store Header */
    .store-header {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      padding: 40px;
      margin-bottom: 24px;
    }

    .store-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: rgba(255, 215, 95, 0.15);
      border: 1px solid rgba(255, 215, 95, 0.3);
      border-radius: 100px;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-gold);
      margin-bottom: 16px;
    }

    .store-name {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.8rem, 4vw, 2.5rem);
      font-weight: 900;
      margin-bottom: 12px;
    }

    .store-address {
      font-size: 1.1rem;
      color: var(--text-secondary);
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 8px;
    }

    .store-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
    }

    .stat-card {
      padding: 20px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 16px;
      text-align: center;
    }

    .stat-value {
      font-family: 'Outfit', sans-serif;
      font-size: 2.5rem;
      font-weight: 900;
    }

    .stat-value.gold { color: var(--accent-gold); }
    .stat-value.cyan { color: var(--accent-cyan); }
    .stat-value.purple { color: var(--accent-purple); }

    .stat-label {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-top: 4px;
    }

    /* Win History */
    .history-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      overflow: hidden;
      margin-bottom: 24px;
    }

    .history-header {
      padding: 20px 24px;
      background: rgba(0, 0, 0, 0.3);
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .history-list {
      max-height: 500px;
      overflow-y: auto;
    }

    .history-item {
      display: grid;
      grid-template-columns: 100px 80px 1fr 80px;
      gap: 16px;
      padding: 16px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      align-items: center;
    }

    .history-item:last-child {
      border-bottom: none;
    }

    .history-item:hover {
      background: rgba(0, 224, 164, 0.03);
    }

    .history-round {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
    }

    .history-round a {
      color: var(--text-primary);
      text-decoration: none;
    }

    .history-round a:hover {
      color: var(--accent-cyan);
    }

    .history-rank {
      display: inline-flex;
      padding: 4px 12px;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 700;
    }

    .history-rank.rank-1 {
      background: rgba(255, 215, 95, 0.2);
      color: var(--accent-gold);
    }

    .history-rank.rank-2 {
      background: rgba(0, 224, 164, 0.15);
      color: var(--accent-cyan);
    }

    .history-balls {
      display: flex;
      gap: 4px;
    }

    .mini-ball {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.7rem;
      color: #fff;
    }

    .history-type {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* Related Links */
    .related-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .related-card {
      padding: 20px;
      background: rgba(13, 24, 41, 0.6);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
      text-decoration: none;
      color: inherit;
      transition: all 0.3s ease;
    }

    .related-card:hover {
      background: rgba(0, 224, 164, 0.05);
      border-color: rgba(0, 224, 164, 0.2);
    }

    .related-icon {
      font-size: 1.5rem;
      margin-bottom: 8px;
    }

    .related-title {
      font-weight: 700;
      margin-bottom: 4px;
    }

    .related-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* Footer */
    .footer {
      text-align: center;
      padding: 40px 24px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 24px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: var(--text-muted);
      text-decoration: none;
    }

    .footer-links a:hover {
      color: var(--accent-cyan);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .store-stats {
        grid-template-columns: repeat(3, 1fr);
      }

      .history-item {
        grid-template-columns: 80px 60px 1fr;
      }

      .history-type {
        display: none;
      }

      .nav-links {
        display: none;
      }

      .stat-value {
        font-size: 1.8rem;
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
      <div class="nav-links">
        <a href="/stores/" class="nav-link">당첨점</a>
        <a href="/draw/<?= $max_round ?>" class="nav-link">당첨결과</a>
        <a href="/auth.php" class="nav-link">AI 분석</a>
      </div>
    </div>
  </nav>

  <main class="main">
    <!-- Breadcrumb - 로또로직스 스타일 완전 구현 -->
    <nav class="breadcrumb" aria-label="breadcrumb">
      <a href="/stores/">전국</a>
      <?php if ($store_region1): ?>
        <span>›</span>
        <a href="/stores/<?= urlencode($store_region1) ?>/"><?= htmlspecialchars($store_region1) ?></a>
      <?php endif; ?>
      <?php if ($store_region2): ?>
      <span>›</span>
        <a href="/stores/<?= urlencode($store_region1) ?>/<?= urlencode($store_region2) ?>/"><?= htmlspecialchars($store_region2) ?></a>
      <?php endif; ?>
      <?php if ($store_region3): ?>
      <span>›</span>
        <a href="/stores/<?= urlencode($store_region1) ?>/<?= urlencode($store_region2) ?>/<?= urlencode($store_region3) ?>/"><?= htmlspecialchars($store_region3) ?></a>
      <?php endif; ?>
      <span>›</span>
      <span><?= $store_name ?></span>
    </nav>

    <!-- Store Header -->
    <section class="store-header">
      <?php if ($wins_1st >= 3): ?>
        <div class="store-badge">🏆 명당 판매점</div>
      <?php elseif ($wins_1st >= 1): ?>
        <div class="store-badge">🎯 1등 당첨점</div>
      <?php endif; ?>
      
      <h1 class="store-name"><?= $store_name ?></h1>
      <p class="store-address">
        📍 <?= htmlspecialchars($store_region1) ?> <?= htmlspecialchars($store_region2) ?> <?= htmlspecialchars($store_region3) ?> <?= $store_address ?>
      </p>
      
      <div class="store-stats">
        <div class="stat-card">
          <div class="stat-value gold"><?= $wins_1st ?></div>
          <div class="stat-label">1등 당첨</div>
        </div>
        <div class="stat-card">
          <div class="stat-value cyan"><?= $wins_2nd ?></div>
          <div class="stat-label">2등 당첨</div>
        </div>
        <div class="stat-card">
          <div class="stat-value purple"><?= $wins_1st + $wins_2nd ?></div>
          <div class="stat-label">총 당첨</div>
        </div>
      </div>
    </section>

    <!-- Win History -->
    <?php if (!empty($wins)): ?>
    <section class="history-section">
      <div class="history-header">🏆 당첨 이력</div>
      <div class="history-list">
        <?php foreach ($wins as $win): ?>
          <?php
            $balls = [(int)($win['n1'] ?? 0), (int)($win['n2'] ?? 0), (int)($win['n3'] ?? 0), (int)($win['n4'] ?? 0), (int)($win['n5'] ?? 0), (int)($win['n6'] ?? 0)];
            $win_type = $win['win_type'] ?? 'auto';
            $type_text = $win_type == 'manual' ? '수동' : ($win_type == 'semi' ? '반자동' : '자동');
            $rank = (int)($win['rank'] ?? 1);
            $draw_no = (int)($win['draw_no'] ?? 0);
          ?>
          <div class="history-item">
            <div class="history-round">
              <a href="/draw/<?= $draw_no ?>"><?= $draw_no ?>회</a>
            </div>
            <div class="history-rank rank-<?= $rank ?>">
              <?= $rank ?>등
            </div>
            <div class="history-balls">
              <?php foreach ($balls as $n): ?>
                <?php if ($n > 0): ?>
                  <div class="mini-ball" style="background: var(--ball-<?= get_ball_color($n) ?>)"><?= $n ?></div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
            <div class="history-type"><?= $type_text ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Related Links -->
    <section class="related-section">
      <?php if ($store_region1): ?>
      <a href="/stores/<?= urlencode($store_region1) ?><?= $store_region2 ? '/' . urlencode($store_region2) : '' ?>" class="related-card">
        <div class="related-icon">📍</div>
        <div class="related-title"><?= htmlspecialchars($store_region1) ?><?= $store_region2 ? ' ' . htmlspecialchars($store_region2) : '' ?> 다른 당첨점</div>
        <div class="related-desc"><?= htmlspecialchars($store_region1) ?><?= $store_region2 ? ' ' . htmlspecialchars($store_region2) : '' ?> 지역 전체 당첨점 보기</div>
      </a>
      <?php endif; ?>
      <a href="/stores/" class="related-card">
        <div class="related-icon">🏆</div>
        <div class="related-title">전국 명당 순위</div>
        <div class="related-desc">전국 1등 당첨점 순위</div>
      </a>
      <a href="/auth.php" class="related-card">
        <div class="related-icon">🎯</div>
        <div class="related-title">AI 번호 분석</div>
        <div class="related-desc">10가지 알고리즘으로 분석</div>
      </a>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-links">
      <a href="/">홈</a>
      <a href="/stores/">당첨점</a>
      <a href="/draw/<?= $max_round ?>">당첨결과</a>
      <a href="/algorithm.php">분석 알고리즘</a>
    </div>
    <p>© <?= date('Y') ?> 오늘로또. 판매점 정보는 참고용입니다.</p>
  </footer>
</body>
</html>


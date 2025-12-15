<?php
/**
 * /store/index.php - 개별 판매점 상세 페이지 (SEO 핵심)
 * 
 * URL 패턴: /store/{store_id} 또는 /store/{store_name_slug}
 * 
 * "대운복권", "성지로또" 등 판매점 이름 검색 시 노출되도록
 * 각 판매점마다 고유 URL과 상세 정보 페이지 제공
 */

// 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 판매점 라이브러리 로드
$store_lib = G5_PATH . '/lib/lotto_store.lib.php';
if (file_exists($store_lib)) {
    include_once($store_lib);
}

// URL 파싱
$request_uri = urldecode($_SERVER['REQUEST_URI']);
$store_id = 0;
$store_slug = '';

// /store/123 형태
if (preg_match('/\/store\/(\d+)/', $request_uri, $matches)) {
    $store_id = (int)$matches[1];
}
// /store/대운복권 형태 (슬러그)
elseif (preg_match('/\/store\/([^\/\?]+)/', $request_uri, $matches)) {
    $store_slug = trim($matches[1]);
}
// GET 파라미터
elseif (isset($_GET['id'])) {
    $store_id = (int)$_GET['id'];
} elseif (isset($_GET['name'])) {
    $store_slug = trim($_GET['name']);
}

// 판매점 정보 조회 (실제 DB)
$store = null;

if (function_exists('li_get_store_by_id') && $store_id > 0) {
    $store = li_get_store_by_id($store_id);
} elseif (function_exists('li_get_store_by_name') && $store_slug) {
    $store = li_get_store_by_name($store_slug);
} elseif ($store_id > 0) {
    $store = sql_fetch("SELECT * FROM g5_lotto_store WHERE store_id = {$store_id}");
} elseif ($store_slug) {
    $slug_escaped = sql_real_escape_string($store_slug);
    $store = sql_fetch("SELECT * FROM g5_lotto_store WHERE store_name LIKE '%{$slug_escaped}%' ORDER BY wins_1st DESC LIMIT 1");
}

// 404 처리
if (!$store) {
    header("HTTP/1.0 404 Not Found");
    $page_title = '판매점을 찾을 수 없습니다';
    $page_desc = '요청하신 판매점 정보를 찾을 수 없습니다.';
    $not_found = true;
} else {
    $not_found = false;
    $store_name = $store['store_name'];
    $store_address = $store['address'];
    $store_region = ($store['region1'] ?? '') . ' ' . ($store['region2'] ?? '');
    $wins_1st = (int)($store['wins_1st'] ?? 0);
    $wins_2nd = (int)($store['wins_2nd'] ?? 0);
    $store_id = (int)$store['store_id'];
    
    // SEO 메타
    $page_title = "{$store_name} - 로또 판매점 정보";
    $page_desc = "{$store_name}은(는) {$store_address}에 위치한 로또 판매점입니다. 누적 1등 {$wins_1st}회, 2등 {$wins_2nd}회 당첨 기록이 있습니다.";
    
    // 당첨 이력 조회 (실제 DB)
    $win_history = [];
    if (function_exists('li_get_store_win_history')) {
        $win_history = li_get_store_win_history($store_id, 20);
    }
    
    // 같은 지역 다른 판매점 조회
    $related_stores = [];
    if (function_exists('li_get_stores_by_region') && !empty($store['region1'])) {
        $all_region_stores = li_get_stores_by_region($store['region1'], '', 10, 0);
        foreach ($all_region_stores as $rs) {
            if ($rs['store_id'] != $store_id) {
                $related_stores[] = $rs;
            }
            if (count($related_stores) >= 6) break;
        }
    }
}

// 공 색상 함수
function get_ball_color($n) {
    if ($n <= 10) return 'yellow';
    if ($n <= 20) return 'blue';
    if ($n <= 30) return 'red';
    if ($n <= 40) return 'gray';
    return 'green';
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags - 핵심! -->
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($store_name ?? '') ?>, <?= htmlspecialchars($store_name ?? '') ?> 로또, <?= htmlspecialchars($store_region ?? '') ?> 로또 판매점, 로또 명당, 1등 당첨점, 로또 판매점 검색">
  <meta name="robots" content="index, follow">
  
  <?php if (!$not_found): ?>
  <link rel="canonical" href="https://lottoinsight.ai/store/<?= $store_id ?>">
  
  <!-- Open Graph -->
  <meta property="og:type" content="place">
  <meta property="og:url" content="https://lottoinsight.ai/store/<?= $store_id ?>">
  <meta property="og:title" content="<?= htmlspecialchars($store_name) ?> - 로또 판매점">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="place:location:latitude" content="">
  <meta property="place:location:longitude" content="">
  
  <!-- Structured Data - LocalBusiness (검색엔진 최적화 핵심) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Store",
    "name": "<?= htmlspecialchars($store_name) ?>",
    "description": "로또 판매점 - 누적 1등 <?= $wins_1st ?>회, 2등 <?= $wins_2nd ?>회",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= htmlspecialchars($store_address) ?>",
      "addressLocality": "<?= htmlspecialchars($store['region2'] ?? '') ?>",
      "addressRegion": "<?= htmlspecialchars($store['region1'] ?? '') ?>",
      "addressCountry": "KR"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "<?= min(5, 3 + $wins_1st * 0.3) ?>",
      "reviewCount": "<?= max(1, $wins_1st + $wins_2nd) ?>"
    }
  }
  </script>

  <!-- BreadcrumbList Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "홈",
        "item": "https://lottoinsight.ai/"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "당첨점",
        "item": "https://lottoinsight.ai/stores/"
      },
      <?php if (!empty($store['region1'])): ?>
      {
        "@type": "ListItem",
        "position": 3,
        "name": "<?= htmlspecialchars($store['region1']) ?>",
        "item": "https://lottoinsight.ai/stores/<?= urlencode($store['region1']) ?>/"
      },
      <?php endif; ?>
      {
        "@type": "ListItem",
        "position": <?= !empty($store['region1']) ? 4 : 3 ?>,
        "name": "<?= htmlspecialchars($store_name) ?>",
        "item": "https://lottoinsight.ai/store/<?= $store_id ?>"
      }
    ]
  }
  </script>
  <?php endif; ?>

  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

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
      background: rgba(5, 10, 21, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .nav-container {
      max-width: 1100px;
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
      padding: 10px 18px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.85rem;
      transition: all 0.3s ease;
    }

    .nav-link:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* Main Content */
    .main {
      max-width: 900px;
      margin: 0 auto;
      padding: 90px 24px 60px;
    }

    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-bottom: 20px;
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
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      padding: 32px;
      margin-bottom: 24px;
      text-align: center;
    }

    .store-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.2), rgba(139, 92, 246, 0.2));
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      margin: 0 auto 16px;
    }

    .store-name {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.8rem, 5vw, 2.5rem);
      font-weight: 900;
      margin-bottom: 8px;
    }

    .store-address {
      color: var(--text-secondary);
      font-size: 1rem;
      margin-bottom: 16px;
    }

    .store-region-badge {
      display: inline-flex;
      padding: 6px 16px;
      background: rgba(139, 92, 246, 0.15);
      border: 1px solid rgba(139, 92, 246, 0.3);
      border-radius: 20px;
      color: var(--accent-purple);
      font-size: 0.9rem;
      font-weight: 500;
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-bottom: 24px;
    }

    .stat-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
      padding: 24px;
      text-align: center;
    }

    .stat-card.gold {
      border-color: rgba(255, 215, 95, 0.3);
      background: linear-gradient(145deg, rgba(255, 215, 95, 0.05), transparent);
    }

    .stat-card.cyan {
      border-color: rgba(0, 224, 164, 0.3);
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.05), transparent);
    }

    .stat-icon {
      font-size: 2rem;
      margin-bottom: 8px;
    }

    .stat-value {
      font-family: 'Outfit', sans-serif;
      font-size: 3rem;
      font-weight: 900;
      line-height: 1;
      margin-bottom: 4px;
    }

    .stat-card.gold .stat-value { color: var(--accent-gold); }
    .stat-card.cyan .stat-value { color: var(--accent-cyan); }

    .stat-label {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    /* Win History */
    .history-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 24px;
    }

    .section-header {
      padding: 16px 24px;
      background: rgba(0, 0, 0, 0.3);
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .history-list {
      padding: 16px;
    }

    .history-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 12px;
      margin-bottom: 12px;
    }

    .history-item:last-child {
      margin-bottom: 0;
    }

    .history-rank {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
    }

    .history-rank.rank-1 {
      background: linear-gradient(145deg, rgba(255, 215, 95, 0.2), rgba(255, 215, 95, 0.05));
      color: var(--accent-gold);
    }

    .history-rank.rank-2 {
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.2), rgba(0, 224, 164, 0.05));
      color: var(--accent-cyan);
    }

    .history-info {
      flex: 1;
    }

    .history-round {
      font-weight: 600;
      margin-bottom: 4px;
    }

    .history-round a {
      color: inherit;
      text-decoration: none;
    }

    .history-round a:hover {
      color: var(--accent-cyan);
    }

    .history-date {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .history-type {
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .history-type.auto {
      background: rgba(0, 224, 164, 0.15);
      color: var(--accent-cyan);
    }

    .history-type.manual {
      background: rgba(255, 215, 95, 0.15);
      color: var(--accent-gold);
    }

    .history-numbers {
      display: flex;
      gap: 4px;
      flex-wrap: wrap;
    }

    .mini-ball {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 700;
      color: #fff;
    }

    .mini-ball.yellow { background: var(--ball-yellow); }
    .mini-ball.blue { background: var(--ball-blue); }
    .mini-ball.red { background: var(--ball-red); }
    .mini-ball.gray { background: var(--ball-gray); }
    .mini-ball.green { background: var(--ball-green); }

    /* Related Stores */
    .related-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 24px;
    }

    .related-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 12px;
      padding: 16px;
    }

    .related-card {
      display: block;
      padding: 16px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 12px;
      text-decoration: none;
      color: inherit;
      transition: all 0.3s ease;
    }

    .related-card:hover {
      background: rgba(0, 224, 164, 0.1);
      transform: translateY(-2px);
    }

    .related-name {
      font-weight: 600;
      margin-bottom: 4px;
    }

    .related-address {
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .related-wins {
      display: flex;
      gap: 12px;
      font-size: 0.85rem;
    }

    .related-wins span {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* CTA */
    .cta-section {
      text-align: center;
      padding: 40px;
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.05), rgba(139, 92, 246, 0.05));
      border-radius: 20px;
      margin-bottom: 24px;
    }

    .cta-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.4rem;
      font-weight: 800;
      margin-bottom: 12px;
    }

    .cta-desc {
      color: var(--text-secondary);
      margin-bottom: 20px;
    }

    .cta-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: var(--gradient-cyan);
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .cta-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 20px 60px rgba(0, 224, 164, 0.3);
    }

    /* 404 */
    .not-found {
      text-align: center;
      padding: 80px 24px;
    }

    .not-found-icon {
      font-size: 4rem;
      margin-bottom: 16px;
    }

    .not-found-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      margin-bottom: 12px;
    }

    .not-found-desc {
      color: var(--text-muted);
      margin-bottom: 24px;
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
      .stats-grid {
        grid-template-columns: 1fr;
      }

      .history-item {
        flex-direction: column;
        text-align: center;
      }

      .history-numbers {
        justify-content: center;
      }

      .nav-links {
        display: none;
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
        <a href="/" class="nav-link">홈</a>
        <a href="/stores/" class="nav-link">당첨점</a>
        <a href="/draw/latest" class="nav-link">회차별 결과</a>
        <a href="/auth.php" class="nav-link">AI 분석</a>
      </div>
    </div>
  </nav>

  <main class="main">
    <?php if ($not_found): ?>
      <!-- 404 -->
      <div class="not-found">
        <div class="not-found-icon">🔍</div>
        <h1 class="not-found-title">판매점을 찾을 수 없습니다</h1>
        <p class="not-found-desc">요청하신 판매점 정보를 찾을 수 없습니다.</p>
        <a href="/stores/" class="cta-btn">← 당첨점 목록으로</a>
      </div>
    <?php else: ?>
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <a href="/">홈</a>
        <span>›</span>
        <a href="/stores/">당첨점</a>
        <span>›</span>
        <a href="/stores/<?= urlencode($store['region1'] ?? '') ?>/"><?= htmlspecialchars($store['region1'] ?? '') ?></a>
        <span>›</span>
        <span><?= htmlspecialchars($store_name) ?></span>
      </nav>

      <!-- Store Header -->
      <header class="store-header">
        <div class="store-icon">🏪</div>
        <h1 class="store-name"><?= htmlspecialchars($store_name) ?></h1>
        <p class="store-address">📍 <?= htmlspecialchars($store_address) ?></p>
        <span class="store-region-badge"><?= htmlspecialchars(trim($store_region)) ?></span>
      </header>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card gold">
          <div class="stat-icon">🥇</div>
          <div class="stat-value"><?= $wins_1st ?></div>
          <div class="stat-label">누적 1등 당첨</div>
        </div>
        <div class="stat-card cyan">
          <div class="stat-icon">🥈</div>
          <div class="stat-value"><?= $wins_2nd ?></div>
          <div class="stat-label">누적 2등 당첨</div>
        </div>
      </div>

      <!-- Win History -->
      <?php if (!empty($win_history)): ?>
      <section class="history-section">
        <div class="section-header">🏆 당첨 이력</div>
        <div class="history-list">
          <?php foreach ($win_history as $h): ?>
            <div class="history-item">
              <div class="history-rank rank-<?= $h['rank'] ?>"><?= $h['rank'] ?>등</div>
              <div class="history-info">
                <div class="history-round">
                  <a href="/draw/<?= $h['draw_no'] ?>"><?= number_format($h['draw_no']) ?>회</a>
                </div>
                <div class="history-date"><?= date('Y년 n월 j일', strtotime($h['draw_date'])) ?></div>
              </div>
              <span class="history-type <?= $h['win_type'] ?>"><?= $h['win_type'] == 'manual' ? '수동' : '자동' ?></span>
              <div class="history-numbers">
                <?php for ($i = 1; $i <= 6; $i++): 
                  $n = $h["n{$i}"];
                ?>
                  <span class="mini-ball <?= get_ball_color($n) ?>"><?= $n ?></span>
                <?php endfor; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <!-- Related Stores -->
      <?php if (!empty($related_stores)): ?>
      <section class="related-section">
        <div class="section-header">📍 <?= htmlspecialchars($store['region1'] ?? '') ?> 지역 다른 명당</div>
        <div class="related-grid">
          <?php foreach ($related_stores as $r): ?>
            <a href="/store/<?= $r['store_id'] ?>" class="related-card">
              <div class="related-name"><?= htmlspecialchars($r['store_name']) ?></div>
              <div class="related-address"><?= htmlspecialchars($r['region2'] ?? $r['address']) ?></div>
              <div class="related-wins">
                <span>🥇 <?= $r['wins_1st'] ?>회</span>
                <span>🥈 <?= $r['wins_2nd'] ?>회</span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <!-- CTA -->
      <section class="cta-section">
        <h2 class="cta-title">🎯 AI가 분석한 이번주 예상번호</h2>
        <p class="cta-desc">10가지 알고리즘이 분석한 최적의 번호 조합을 받아보세요</p>
        <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작하기</a>
      </section>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-links">
      <a href="/">홈</a>
      <a href="/stores/">당첨점</a>
      <a href="/algorithm.php">분석 알고리즘</a>
      <a href="/terms.html">이용약관</a>
      <a href="/privacy.html">개인정보처리방침</a>
    </div>
    <p>© <?= date('Y') ?> 오늘로또. 판매점 정보는 동행복권 공식 자료 기준입니다.</p>
  </footer>
</body>
</html>


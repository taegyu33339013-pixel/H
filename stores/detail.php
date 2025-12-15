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

// URL 경로에서 slug 추출 (/stores/view/{slug})
$request_uri = $_SERVER['REQUEST_URI'];
if (preg_match('/\/stores\/view\/([^\/\?]+)/', $request_uri, $matches)) {
    $slug = urldecode($matches[1]);
}

// 판매점 조회
$store = null;
if ($store_id > 0) {
    $store = li_get_store($store_id);
} elseif ($slug) {
    $store = li_get_store($slug);
}

if (!$store) {
    header("HTTP/1.0 404 Not Found");
    include($_SERVER['DOCUMENT_ROOT'] . '/404.html');
    exit;
}

// 당첨 이력 조회
$wins = li_get_store_wins($store['store_id'], 50);

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

// SEO 데이터
$page_title = htmlspecialchars($store['name']) . " - " . htmlspecialchars($store['region']) . " 로또 판매점";
$page_desc = htmlspecialchars($store['name']) . " (" . htmlspecialchars($store['address']) . ") - 1등 " . $store['wins_1st'] . "회, 2등 " . $store['wins_2nd'] . "회 당첨. 로또 명당 판매점 상세 정보.";
$canonical_url = "https://lottoinsight.ai/stores/view/" . urlencode($store['slug']);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags -->
  <title><?= $page_title ?> | 오늘로또</title>
  <meta name="description" content="<?= $page_desc ?>">
  <meta name="keywords" content="<?= htmlspecialchars($store['name']) ?>, <?= $store['region'] ?> 로또, 로또 판매점, 로또 명당, 1등 당첨점, <?= $store['district'] ?>">
  <meta name="robots" content="index, follow">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="<?= $canonical_url ?>">
  
  <!-- Open Graph -->
  <meta property="og:type" content="place">
  <meta property="og:url" content="<?= $canonical_url ?>">
  <meta property="og:title" content="<?= $page_title ?>">
  <meta property="og:description" content="<?= $page_desc ?>">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Structured Data - Local Business -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Store",
    "name": "<?= htmlspecialchars($store['name']) ?>",
    "description": "로또 판매점 - 1등 <?= $store['wins_1st'] ?>회, 2등 <?= $store['wins_2nd'] ?>회 당첨",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= htmlspecialchars($store['address']) ?>",
      "addressRegion": "<?= $store['region'] ?>",
      "addressCountry": "KR"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "<?= min(5, 3 + ($store['wins_1st'] * 0.3)) ?>",
      "reviewCount": "<?= $store['wins_1st'] + $store['wins_2nd'] ?>"
    }
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
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <a href="/">홈</a>
      <span>›</span>
      <a href="/stores/">당첨점</a>
      <span>›</span>
      <a href="/stores/<?= urlencode($store['region']) ?>"><?= htmlspecialchars($store['region']) ?></a>
      <span>›</span>
      <span><?= htmlspecialchars($store['name']) ?></span>
    </nav>

    <!-- Store Header -->
    <section class="store-header">
      <?php if ($store['wins_1st'] >= 3): ?>
        <div class="store-badge">🏆 명당 판매점</div>
      <?php elseif ($store['wins_1st'] >= 1): ?>
        <div class="store-badge">🎯 1등 당첨점</div>
      <?php endif; ?>
      
      <h1 class="store-name"><?= htmlspecialchars($store['name']) ?></h1>
      <p class="store-address">
        📍 <?= htmlspecialchars($store['address']) ?>
      </p>
      
      <div class="store-stats">
        <div class="stat-card">
          <div class="stat-value gold"><?= $store['wins_1st'] ?></div>
          <div class="stat-label">1등 당첨</div>
        </div>
        <div class="stat-card">
          <div class="stat-value cyan"><?= $store['wins_2nd'] ?></div>
          <div class="stat-label">2등 당첨</div>
        </div>
        <div class="stat-card">
          <div class="stat-value purple"><?= $store['wins_1st'] + $store['wins_2nd'] ?></div>
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
            $balls = [(int)$win['n1'], (int)$win['n2'], (int)$win['n3'], (int)$win['n4'], (int)$win['n5'], (int)$win['n6']];
            $type_text = $win['win_type'] == 'manual' ? '수동' : ($win['win_type'] == 'semi' ? '반자동' : '자동');
          ?>
          <div class="history-item">
            <div class="history-round">
              <a href="/draw/<?= $win['draw_no'] ?>"><?= $win['draw_no'] ?>회</a>
            </div>
            <div class="history-rank rank-<?= $win['rank'] ?>">
              <?= $win['rank'] ?>등
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
      <a href="/stores/<?= urlencode($store['region']) ?>" class="related-card">
        <div class="related-icon">📍</div>
        <div class="related-title"><?= $store['region'] ?> 다른 당첨점</div>
        <div class="related-desc"><?= $store['region'] ?> 지역 전체 당첨점 보기</div>
      </a>
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


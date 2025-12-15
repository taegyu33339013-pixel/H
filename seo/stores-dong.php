<?php
/**
 * 동 단위 판매점 목록 SEO 페이지
 * URL: /stores/서울/강남구/역삼동/ → "역삼동 로또 판매점"
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

include_once(G5_PATH . '/lib/lotto_store.lib.php');

$region1 = isset($_GET['r1']) ? urldecode(trim($_GET['r1'])) : '';
$region2 = isset($_GET['r2']) ? urldecode(trim($_GET['r2'])) : '';
$region3 = isset($_GET['r3']) ? urldecode(trim($_GET['r3'])) : ''; // 동

if (empty($region1) || empty($region2) || empty($region3)) {
    header("HTTP/1.0 404 Not Found");
    include(G5_PATH . '/404.html');
    exit;
}

// 해당 동의 판매점 조회
$r1_esc = sql_real_escape_string($region1);
$r2_esc = sql_real_escape_string($region2);
$r3_esc = sql_real_escape_string($region3);

$stores = [];
$res = sql_query("
    SELECT * FROM g5_lotto_store 
    WHERE region1 = '{$r1_esc}' 
      AND region2 = '{$r2_esc}' 
      AND address LIKE '%{$r3_esc}%'
    ORDER BY wins_1st DESC, wins_2nd DESC
    LIMIT 100
", false);

if ($res) {
    while ($row = sql_fetch_array($res)) {
        $stores[] = $row;
    }
}

$total_count = count($stores);
$total_wins = array_sum(array_column($stores, 'wins_1st'));

// SEO 메타
$page_title = "{$region1} {$region2} {$region3} 로또 판매점";
$page_desc = "{$region1} {$region2} {$region3} 지역 로또 판매점 {$total_count}곳. 누적 1등 당첨 {$total_wins}회. 명당 판매점 정보와 당첨 이력을 확인하세요.";
$canonical = "https://lottoinsight.ai/stores/" . urlencode($region1) . "/" . urlencode($region2) . "/" . urlencode($region3) . "/";

function get_ball_class($n) {
    if ($n <= 10) return 'ball-yellow';
    if ($n <= 20) return 'ball-blue';
    if ($n <= 30) return 'ball-red';
    if ($n <= 40) return 'ball-gray';
    return 'ball-green';
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($region3) ?> 로또, <?= htmlspecialchars($region3) ?> 로또 판매점, <?= htmlspecialchars($region2) ?> 로또, <?= htmlspecialchars($region1) ?> 로또 명당">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:url" content="<?= $canonical ?>">

  <!-- BreadcrumbList -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "홈", "item": "https://lottoinsight.ai/"},
      {"@type": "ListItem", "position": 2, "name": "당첨점", "item": "https://lottoinsight.ai/stores/"},
      {"@type": "ListItem", "position": 3, "name": "<?= htmlspecialchars($region1) ?>", "item": "https://lottoinsight.ai/stores/<?= urlencode($region1) ?>/"},
      {"@type": "ListItem", "position": 4, "name": "<?= htmlspecialchars($region2) ?>", "item": "https://lottoinsight.ai/stores/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/"},
      {"@type": "ListItem", "position": 5, "name": "<?= htmlspecialchars($region3) ?>", "item": "<?= $canonical ?>"}
    ]
  }
  </script>

  <!-- ItemList -->
  <?php if (!empty($stores)): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "<?= htmlspecialchars($page_title) ?>",
    "description": "<?= htmlspecialchars($page_desc) ?>",
    "numberOfItems": <?= count($stores) ?>,
    "itemListElement": [
      <?php 
      $items = [];
      foreach (array_slice($stores, 0, 20) as $idx => $s) {
        $items[] = '{
          "@type": "ListItem",
          "position": ' . ($idx + 1) . ',
          "item": {
            "@type": "Store",
            "name": "' . htmlspecialchars($s['store_name']) . '",
            "address": {
              "@type": "PostalAddress",
              "streetAddress": "' . htmlspecialchars($s['address']) . '",
              "addressLocality": "' . htmlspecialchars($region3) . '",
              "addressRegion": "' . htmlspecialchars($region1) . '"
            }
          }
        }';
      }
      echo implode(",\n      ", $items);
      ?>
    ]
  }
  </script>
  <?php endif; ?>

  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">

  <style>
    :root {
      --primary-dark: #050a15;
      --primary: #0d1526;
      --accent-cyan: #00E0A4;
      --accent-gold: #FFD75F;
      --text-primary: #ffffff;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --glass-border: rgba(255, 255, 255, 0.08);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', 'Pretendard', sans-serif; background: var(--primary-dark); color: var(--text-primary); line-height: 1.6; }
    .container { max-width: 1000px; margin: 0 auto; padding: 24px; }
    
    .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 16px 24px; background: rgba(5,10,21,0.95); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.05); }
    .nav-container { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
    .nav-logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .nav-logo-text { font-family: 'Outfit', sans-serif; font-weight: 800; background: linear-gradient(135deg, #00E0A4, #00D4FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .main { padding-top: 80px; }
    .breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 24px; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb a:hover { color: var(--accent-cyan); }
    
    .page-header { margin-bottom: 32px; }
    .page-title { font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: 8px; }
    .page-subtitle { color: var(--text-secondary); }
    
    .stats-row { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
    .stat-badge { padding: 8px 16px; background: rgba(0,224,164,0.1); border: 1px solid rgba(0,224,164,0.2); border-radius: 20px; font-size: 0.9rem; }
    .stat-badge strong { color: var(--accent-cyan); }
    
    .store-grid { display: grid; gap: 16px; }
    .store-card { display: block; padding: 20px; background: rgba(13,24,41,0.8); border: 1px solid var(--glass-border); border-radius: 16px; text-decoration: none; color: inherit; transition: all 0.3s; }
    .store-card:hover { border-color: var(--accent-cyan); transform: translateY(-2px); }
    .store-name { font-weight: 700; font-size: 1.1rem; margin-bottom: 4px; }
    .store-address { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 12px; }
    .store-stats { display: flex; gap: 16px; }
    .store-stat { display: flex; align-items: center; gap: 4px; font-size: 0.9rem; }
    .store-stat.gold { color: var(--accent-gold); }
    .store-stat.cyan { color: var(--accent-cyan); }
    
    .empty-state { text-align: center; padding: 60px 24px; color: var(--text-muted); }
    
    .related-section { margin-top: 40px; padding: 24px; background: rgba(13,24,41,0.5); border-radius: 16px; }
    .related-title { font-weight: 700; margin-bottom: 16px; }
    .related-links { display: flex; flex-wrap: wrap; gap: 8px; }
    .related-link { padding: 8px 16px; background: rgba(255,255,255,0.05); border-radius: 8px; color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
    .related-link:hover { background: rgba(0,224,164,0.1); color: var(--accent-cyan); }
    
    .footer { text-align: center; padding: 40px 24px; border-top: 1px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 0.85rem; margin-top: 60px; }
    .footer a { color: var(--text-muted); text-decoration: none; margin: 0 12px; }
  </style>
</head>
<body>
  <nav class="nav">
    <div class="nav-container">
      <a href="/" class="nav-logo">
        <div class="nav-logo-icon">🎯</div>
        <span class="nav-logo-text">오늘로또</span>
      </a>
    </div>
  </nav>

  <main class="main">
    <div class="container">
      <nav class="breadcrumb">
        <a href="/">홈</a> <span>›</span>
        <a href="/stores/">당첨점</a> <span>›</span>
        <a href="/stores/<?= urlencode($region1) ?>/"><?= htmlspecialchars($region1) ?></a> <span>›</span>
        <a href="/stores/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/"><?= htmlspecialchars($region2) ?></a> <span>›</span>
        <span><?= htmlspecialchars($region3) ?></span>
      </nav>

      <header class="page-header">
        <h1 class="page-title">📍 <?= htmlspecialchars($region3) ?> 로또 판매점</h1>
        <p class="page-subtitle"><?= htmlspecialchars($region1) ?> <?= htmlspecialchars($region2) ?> <?= htmlspecialchars($region3) ?> 지역</p>
      </header>

      <div class="stats-row">
        <div class="stat-badge">총 <strong><?= $total_count ?></strong>곳</div>
        <div class="stat-badge">누적 1등 <strong><?= $total_wins ?></strong>회</div>
      </div>

      <?php if (empty($stores)): ?>
      <div class="empty-state">
        <p>😢 해당 지역에 등록된 판매점이 없습니다.</p>
        <p style="margin-top: 8px;"><a href="/stores/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/" style="color: var(--accent-cyan);">← <?= htmlspecialchars($region2) ?> 전체 보기</a></p>
      </div>
      <?php else: ?>
      <div class="store-grid">
        <?php foreach ($stores as $s): ?>
        <a href="/store/<?= $s['store_id'] ?>" class="store-card">
          <div class="store-name"><?= htmlspecialchars($s['store_name']) ?></div>
          <div class="store-address"><?= htmlspecialchars($s['address']) ?></div>
          <div class="store-stats">
            <span class="store-stat gold">🥇 1등 <?= $s['wins_1st'] ?>회</span>
            <span class="store-stat cyan">🥈 2등 <?= $s['wins_2nd'] ?>회</span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- 관련 링크 -->
      <section class="related-section">
        <h2 class="related-title">🔗 주변 지역 보기</h2>
        <div class="related-links">
          <a href="/stores/<?= urlencode($region1) ?>/" class="related-link"><?= htmlspecialchars($region1) ?> 전체</a>
          <a href="/stores/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/" class="related-link"><?= htmlspecialchars($region2) ?> 전체</a>
          <a href="/stores/" class="related-link">전국 명당</a>
          <a href="/ranking/stores/" class="related-link">명당 랭킹</a>
        </div>
      </section>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

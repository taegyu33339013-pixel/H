<?php
/**
 * 랭킹 SEO 페이지
 * URL: /ranking/stores/ → "로또 명당 순위", "1등 많이 나온 판매점"
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

include_once(G5_PATH . '/lib/lotto_store.lib.php');

$type = isset($_GET['type']) ? trim($_GET['type']) : 'stores';

$rankings = [
    'stores' => [
        'title' => '로또 명당 순위 - 1등 많이 나온 판매점 TOP 100',
        'desc' => '전국 로또 1등 당첨 명당 순위. 누적 1등 당첨 횟수 기준 TOP 100 판매점.',
        'icon' => '🏆',
        'keywords' => '로또 명당 순위, 1등 많이 나온 판매점, 로또 명당 TOP, 로또 당첨점 순위'
    ],
    'jackpot' => [
        'title' => '로또 역대 당첨금 순위 - 최고 당첨금 TOP 50',
        'desc' => '로또 역대 1등 당첨금 순위. 가장 높은 당첨금이 나온 회차와 금액.',
        'icon' => '💰',
        'keywords' => '로또 당첨금 순위, 로또 역대 1등, 로또 최고 당첨금'
    ],
    'numbers' => [
        'title' => '로또 번호별 출현 순위 - 가장 많이 나온 번호',
        'desc' => '로또 역대 당첨번호 중 가장 많이 출현한 번호 순위.',
        'icon' => '🔢',
        'keywords' => '로또 번호 순위, 많이 나온 번호, 로또 출현 빈도'
    ],
];

if (!isset($rankings[$type])) {
    $type = 'stores';
}

$info = $rankings[$type];
$page_title = $info['title'];
$page_desc = $info['desc'];
$canonical = "https://lottoinsight.ai/ranking/{$type}/";

// 데이터 조회
$data = [];

if ($type === 'stores') {
    if (function_exists('li_get_top_stores')) {
        $data = li_get_top_stores(100);
    }
} elseif ($type === 'jackpot') {
    $res = sql_query("
        SELECT draw_no, draw_date, first_prize_each, first_winners 
        FROM g5_lotto_draw 
        WHERE first_prize_each > 0 
        ORDER BY first_prize_each DESC 
        LIMIT 50
    ", false);
    if ($res) {
        while ($row = sql_fetch_array($res)) {
            $data[] = $row;
        }
    }
} elseif ($type === 'numbers') {
    // 번호별 출현 횟수
    for ($i = 1; $i <= 45; $i++) {
        $cnt = sql_fetch("
            SELECT COUNT(*) AS cnt FROM g5_lotto_draw 
            WHERE n1={$i} OR n2={$i} OR n3={$i} OR n4={$i} OR n5={$i} OR n6={$i}
        ")['cnt'];
        $data[] = ['number' => $i, 'count' => (int)$cnt];
    }
    usort($data, fn($a, $b) => $b['count'] - $a['count']);
}

function format_prize($amount) {
    if ($amount <= 0) return '-';
    $eok = floor($amount / 100000000);
    $man = floor(($amount % 100000000) / 10000);
    $out = '';
    if ($eok > 0) $out .= number_format($eok) . '억 ';
    if ($man > 0) $out .= number_format($man) . '만';
    return trim($out) . '원';
}

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
  <meta name="keywords" content="<?= htmlspecialchars($info['keywords']) ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">

  <!-- ItemList Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "<?= htmlspecialchars($page_title) ?>",
    "description": "<?= htmlspecialchars($page_desc) ?>",
    "numberOfItems": <?= count($data) ?>,
    "itemListElement": [
      <?php 
      $items = [];
      foreach (array_slice($data, 0, 10) as $idx => $d) {
        if ($type === 'stores') {
          $items[] = '{"@type": "ListItem", "position": ' . ($idx + 1) . ', "item": {"@type": "Store", "name": "' . htmlspecialchars($d['store_name']) . '"}}';
        } elseif ($type === 'jackpot') {
          $items[] = '{"@type": "ListItem", "position": ' . ($idx + 1) . ', "name": "' . $d['draw_no'] . '회 - ' . format_prize($d['first_prize_each']) . '"}';
        } else {
          $items[] = '{"@type": "ListItem", "position": ' . ($idx + 1) . ', "name": "' . $d['number'] . '번 - ' . $d['count'] . '회 출현"}';
        }
      }
      echo implode(",\n      ", $items);
      ?>
    ]
  }
  </script>

  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">

  <style>
    :root {
      --primary-dark: #050a15;
      --accent-cyan: #00E0A4;
      --accent-gold: #FFD75F;
      --accent-purple: #8B5CF6;
      --text-primary: #ffffff;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --glass-border: rgba(255,255,255,0.08);
      --ball-yellow: linear-gradient(145deg, #ffd700, #f59e0b);
      --ball-blue: linear-gradient(145deg, #3b82f6, #1d4ed8);
      --ball-red: linear-gradient(145deg, #ef4444, #b91c1c);
      --ball-gray: linear-gradient(145deg, #6b7280, #374151);
      --ball-green: linear-gradient(145deg, #22c55e, #15803d);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Pretendard', sans-serif; background: var(--primary-dark); color: var(--text-primary); line-height: 1.6; }
    .container { max-width: 1000px; margin: 0 auto; padding: 24px; }
    
    .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 16px 24px; background: rgba(5,10,21,0.95); backdrop-filter: blur(20px); }
    .nav-container { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .nav-logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .nav-logo-text { font-family: 'Outfit', sans-serif; font-weight: 800; background: linear-gradient(135deg, #00E0A4, #00D4FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .main { padding-top: 80px; }
    .breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 24px; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb a:hover { color: var(--accent-cyan); }
    
    .page-header { text-align: center; margin-bottom: 32px; }
    .page-icon { font-size: 4rem; margin-bottom: 16px; }
    .page-title { font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: 8px; }
    .page-desc { color: var(--text-secondary); }
    
    .tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
    .tab { padding: 10px 20px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-secondary); text-decoration: none; }
    .tab:hover { background: rgba(0,224,164,0.1); border-color: var(--accent-cyan); }
    .tab.active { background: rgba(0,224,164,0.15); border-color: var(--accent-cyan); color: var(--accent-cyan); }
    
    .ranking-list { background: rgba(13,24,41,0.8); border-radius: 20px; overflow: hidden; }
    .ranking-header { display: grid; grid-template-columns: 60px 1fr 120px; padding: 16px 24px; background: rgba(0,0,0,0.3); font-weight: 600; font-size: 0.9rem; color: var(--text-muted); }
    .ranking-item { display: grid; grid-template-columns: 60px 1fr 120px; padding: 16px 24px; border-bottom: 1px solid var(--glass-border); align-items: center; }
    .ranking-item:last-child { border-bottom: none; }
    .ranking-item:hover { background: rgba(0,224,164,0.05); }
    .ranking-rank { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.2rem; }
    .ranking-rank.top3 { color: var(--accent-gold); }
    .ranking-info a { color: inherit; text-decoration: none; }
    .ranking-info a:hover { color: var(--accent-cyan); }
    .ranking-name { font-weight: 600; }
    .ranking-sub { font-size: 0.85rem; color: var(--text-muted); }
    .ranking-value { text-align: right; font-weight: 700; color: var(--accent-cyan); }
    
    .ball { display: inline-flex; width: 36px; height: 36px; border-radius: 50%; align-items: center; justify-content: center; font-weight: 700; color: #fff; margin-right: 8px; }
    .ball-yellow { background: var(--ball-yellow); }
    .ball-blue { background: var(--ball-blue); }
    .ball-red { background: var(--ball-red); }
    .ball-gray { background: var(--ball-gray); }
    .ball-green { background: var(--ball-green); }
    
    .footer { text-align: center; padding: 40px; color: var(--text-muted); font-size: 0.85rem; margin-top: 60px; }
    .footer a { color: var(--text-muted); text-decoration: none; margin: 0 12px; }
    
    @media (max-width: 768px) {
      .ranking-header, .ranking-item { grid-template-columns: 50px 1fr 80px; padding: 12px 16px; }
      .ranking-rank { font-size: 1rem; }
    }
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
        <a href="/ranking/stores/">랭킹</a> <span>›</span>
        <span><?= $type === 'stores' ? '명당 순위' : ($type === 'jackpot' ? '당첨금 순위' : '번호 순위') ?></span>
      </nav>

      <header class="page-header">
        <div class="page-icon"><?= $info['icon'] ?></div>
        <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
        <p class="page-desc"><?= htmlspecialchars($page_desc) ?></p>
      </header>

      <div class="tabs">
        <a href="/ranking/stores/" class="tab <?= $type === 'stores' ? 'active' : '' ?>">🏆 명당 순위</a>
        <a href="/ranking/jackpot/" class="tab <?= $type === 'jackpot' ? 'active' : '' ?>">💰 당첨금 순위</a>
        <a href="/ranking/numbers/" class="tab <?= $type === 'numbers' ? 'active' : '' ?>">🔢 번호 순위</a>
      </div>

      <div class="ranking-list">
        <?php if ($type === 'stores'): ?>
        <div class="ranking-header">
          <span>순위</span>
          <span>판매점</span>
          <span>1등 횟수</span>
        </div>
        <?php foreach ($data as $idx => $s): ?>
        <div class="ranking-item">
          <div class="ranking-rank <?= $idx < 3 ? 'top3' : '' ?>"><?= $idx + 1 ?></div>
          <div class="ranking-info">
            <a href="/store/<?= $s['store_id'] ?>">
              <div class="ranking-name"><?= htmlspecialchars($s['store_name']) ?></div>
              <div class="ranking-sub"><?= htmlspecialchars($s['region1'] ?? '') ?> <?= htmlspecialchars($s['region2'] ?? '') ?></div>
            </a>
          </div>
          <div class="ranking-value"><?= $s['wins_1st'] ?>회</div>
        </div>
        <?php endforeach; ?>
        
        <?php elseif ($type === 'jackpot'): ?>
        <div class="ranking-header">
          <span>순위</span>
          <span>회차</span>
          <span>당첨금</span>
        </div>
        <?php foreach ($data as $idx => $d): ?>
        <div class="ranking-item">
          <div class="ranking-rank <?= $idx < 3 ? 'top3' : '' ?>"><?= $idx + 1 ?></div>
          <div class="ranking-info">
            <a href="/lotto/<?= $d['draw_no'] ?>/">
              <div class="ranking-name"><?= number_format($d['draw_no']) ?>회</div>
              <div class="ranking-sub"><?= date('Y.m.d', strtotime($d['draw_date'])) ?> · <?= $d['first_winners'] ?>명 당첨</div>
            </a>
          </div>
          <div class="ranking-value"><?= format_prize($d['first_prize_each']) ?></div>
        </div>
        <?php endforeach; ?>
        
        <?php elseif ($type === 'numbers'): ?>
        <div class="ranking-header">
          <span>순위</span>
          <span>번호</span>
          <span>출현 횟수</span>
        </div>
        <?php foreach ($data as $idx => $d): ?>
        <div class="ranking-item">
          <div class="ranking-rank <?= $idx < 3 ? 'top3' : '' ?>"><?= $idx + 1 ?></div>
          <div class="ranking-info">
            <a href="/number/<?= $d['number'] ?>/">
              <span class="ball <?= get_ball_class($d['number']) ?>"><?= $d['number'] ?></span>
              <span class="ranking-name"><?= $d['number'] ?>번</span>
            </a>
          </div>
          <div class="ranking-value"><?= number_format($d['count']) ?>회</div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

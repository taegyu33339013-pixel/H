<?php
/**
 * 회차별 당첨점 SEO 페이지
 * URL: /lotto/1202/winners/ → "1202회 당첨점", "1202회 1등 당첨 판매점"
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

include_once(G5_PATH . '/lib/lotto_store.lib.php');

$round = isset($_GET['round']) ? (int)$_GET['round'] : 0;

if ($round < 1) {
    header("HTTP/1.0 404 Not Found");
    include(G5_PATH . '/404.html');
    exit;
}

// 회차 정보 조회
$draw = sql_fetch("SELECT * FROM g5_lotto_draw WHERE draw_no = {$round}");
if (!$draw) {
    header("HTTP/1.0 404 Not Found");
    include(G5_PATH . '/404.html');
    exit;
}

$draw_date = $draw['draw_date'];
$nums = [$draw['n1'], $draw['n2'], $draw['n3'], $draw['n4'], $draw['n5'], $draw['n6']];
$bonus = $draw['bonus'];
$first_prize = (int)($draw['first_prize_each'] ?? 0);
$first_winners_count = (int)($draw['first_winners'] ?? 0);

// 당첨점 조회
$first_winners = [];
$second_winners = [];

if (function_exists('li_get_draw_winning_stores')) {
    $first_winners = li_get_draw_winning_stores($round, 1);
    $second_winners = li_get_draw_winning_stores($round, 2);
}

// 당첨금 포맷
function format_prize($amount) {
    if ($amount <= 0) return '집계중';
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

$page_title = "로또 {$round}회 당첨점 - 1등 배출 판매점";
$page_desc = "로또 {$round}회 (" . date('Y년 n월 j일', strtotime($draw_date)) . ") 당첨점 정보. 1등 당첨 " . count($first_winners) . "곳, 2등 당첨 " . count($second_winners) . "곳. 당첨금 " . format_prize($first_prize);
$canonical = "https://lottoinsight.ai/lotto/{$round}/winners/";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?= $page_title ?> | 오늘로또</title>
  <meta name="description" content="<?= $page_desc ?>">
  <meta name="keywords" content="로또 <?= $round ?>회, <?= $round ?>회 당첨점, <?= $round ?>회 1등, 로또 당첨 판매점">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:type" content="article">
  <meta property="og:title" content="<?= $page_title ?>">
  <meta property="og:description" content="<?= $page_desc ?>">

  <!-- BreadcrumbList -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "홈", "item": "https://lottoinsight.ai/"},
      {"@type": "ListItem", "position": 2, "name": "회차별 결과", "item": "https://lottoinsight.ai/lotto/"},
      {"@type": "ListItem", "position": 3, "name": "<?= $round ?>회", "item": "https://lottoinsight.ai/lotto/<?= $round ?>/"},
      {"@type": "ListItem", "position": 4, "name": "당첨점", "item": "<?= $canonical ?>"}
    ]
  }
  </script>

  <!-- Event Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "로또 <?= $round ?>회 추첨",
    "startDate": "<?= $draw_date ?>T20:45:00+09:00",
    "endDate": "<?= $draw_date ?>T21:00:00+09:00",
    "eventStatus": "https://schema.org/EventScheduled",
    "eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",
    "location": {
      "@type": "Place",
      "name": "MBC 스튜디오"
    },
    "organizer": {
      "@type": "Organization",
      "name": "동행복권",
      "url": "https://dhlottery.co.kr"
    },
    "description": "로또 <?= $round ?>회 추첨 결과. 당첨번호: <?= implode(', ', $nums) ?> + <?= $bonus ?>"
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
      --text-primary: #ffffff;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --glass-border: rgba(255, 255, 255, 0.08);
      --ball-yellow: linear-gradient(145deg, #ffd700, #f59e0b);
      --ball-blue: linear-gradient(145deg, #3b82f6, #1d4ed8);
      --ball-red: linear-gradient(145deg, #ef4444, #b91c1c);
      --ball-gray: linear-gradient(145deg, #6b7280, #374151);
      --ball-green: linear-gradient(145deg, #22c55e, #15803d);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Pretendard', sans-serif; background: var(--primary-dark); color: var(--text-primary); line-height: 1.6; }
    .container { max-width: 900px; margin: 0 auto; padding: 24px; }
    
    .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 16px 24px; background: rgba(5,10,21,0.95); backdrop-filter: blur(20px); }
    .nav-container { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
    .nav-logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .nav-logo-text { font-family: 'Outfit', sans-serif; font-weight: 800; background: linear-gradient(135deg, #00E0A4, #00D4FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .main { padding-top: 80px; }
    .breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 24px; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb a:hover { color: var(--accent-cyan); }
    
    .draw-header { text-align: center; padding: 32px; background: rgba(13,24,41,0.8); border-radius: 24px; margin-bottom: 24px; }
    .draw-round { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 900; margin-bottom: 8px; }
    .draw-date { color: var(--text-secondary); margin-bottom: 20px; }
    .draw-balls { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .ball { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; color: #fff; }
    .ball-yellow { background: var(--ball-yellow); }
    .ball-blue { background: var(--ball-blue); }
    .ball-red { background: var(--ball-red); }
    .ball-gray { background: var(--ball-gray); }
    .ball-green { background: var(--ball-green); }
    .bonus-sep { color: var(--text-muted); font-size: 1.5rem; display: flex; align-items: center; }
    .draw-prize { font-size: 1.2rem; color: var(--accent-gold); }
    
    .section { background: rgba(13,24,41,0.8); border-radius: 20px; overflow: hidden; margin-bottom: 24px; }
    .section-header { padding: 16px 24px; background: rgba(0,0,0,0.3); font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .section-header.gold { border-left: 4px solid var(--accent-gold); }
    .section-header.cyan { border-left: 4px solid var(--accent-cyan); }
    
    .winner-list { padding: 16px; }
    .winner-card { display: flex; align-items: center; gap: 16px; padding: 16px; background: rgba(0,0,0,0.2); border-radius: 12px; margin-bottom: 12px; }
    .winner-rank { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; }
    .winner-rank.gold { background: linear-gradient(145deg, rgba(255,215,95,0.2), rgba(255,215,95,0.05)); color: var(--accent-gold); }
    .winner-rank.silver { background: linear-gradient(145deg, rgba(0,224,164,0.2), rgba(0,224,164,0.05)); color: var(--accent-cyan); }
    .winner-info { flex: 1; }
    .winner-name { font-weight: 600; margin-bottom: 4px; }
    .winner-name a { color: inherit; text-decoration: none; }
    .winner-name a:hover { color: var(--accent-cyan); }
    .winner-address { font-size: 0.85rem; color: var(--text-muted); }
    .winner-type { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    .winner-type.auto { background: rgba(0,224,164,0.15); color: var(--accent-cyan); }
    .winner-type.manual { background: rgba(255,215,95,0.15); color: var(--accent-gold); }
    
    .empty-state { padding: 40px; text-align: center; color: var(--text-muted); }
    
    .nav-rounds { display: flex; justify-content: space-between; margin-top: 24px; }
    .nav-round { padding: 12px 24px; background: rgba(255,255,255,0.05); border-radius: 12px; color: var(--text-secondary); text-decoration: none; }
    .nav-round:hover { background: rgba(0,224,164,0.1); color: var(--accent-cyan); }
    
    .footer { text-align: center; padding: 40px; color: var(--text-muted); font-size: 0.85rem; margin-top: 40px; }
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
        <a href="/lotto/">회차별</a> <span>›</span>
        <a href="/lotto/<?= $round ?>/"><?= $round ?>회</a> <span>›</span>
        <span>당첨점</span>
      </nav>

      <header class="draw-header">
        <div class="draw-round"><?= number_format($round) ?>회 당첨점</div>
        <div class="draw-date"><?= date('Y년 n월 j일', strtotime($draw_date)) ?> 추첨</div>
        <div class="draw-balls">
          <?php foreach ($nums as $n): ?>
          <span class="ball <?= get_ball_class($n) ?>"><?= $n ?></span>
          <?php endforeach; ?>
          <span class="bonus-sep">+</span>
          <span class="ball <?= get_ball_class($bonus) ?>"><?= $bonus ?></span>
        </div>
        <div class="draw-prize">1등 당첨금: <?= format_prize($first_prize) ?></div>
      </header>

      <!-- 1등 당첨점 -->
      <section class="section">
        <div class="section-header gold">🥇 1등 당첨점 (<?= count($first_winners) ?>곳)</div>
        <div class="winner-list">
          <?php if (empty($first_winners)): ?>
          <div class="empty-state">당첨점 정보가 없습니다. (데이터 동기화 필요)</div>
          <?php else: ?>
          <?php foreach ($first_winners as $idx => $w): 
            $type_text = ($w['win_type'] == 'auto') ? '자동' : (($w['win_type'] == 'manual') ? '수동' : '반자동');
            $type_class = ($w['win_type'] == 'manual') ? 'manual' : 'auto';
          ?>
          <div class="winner-card">
            <div class="winner-rank gold"><?= $idx + 1 ?></div>
            <div class="winner-info">
              <div class="winner-name"><a href="/store/<?= $w['store_id'] ?>"><?= htmlspecialchars($w['store_name']) ?></a></div>
              <div class="winner-address"><?= htmlspecialchars($w['address']) ?></div>
            </div>
            <span class="winner-type <?= $type_class ?>"><?= $type_text ?></span>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <!-- 2등 당첨점 -->
      <section class="section">
        <div class="section-header cyan">🥈 2등 당첨점 (<?= count($second_winners) ?>곳)</div>
        <div class="winner-list">
          <?php if (empty($second_winners)): ?>
          <div class="empty-state">당첨점 정보가 없습니다.</div>
          <?php else: ?>
          <?php foreach (array_slice($second_winners, 0, 20) as $idx => $w): ?>
          <div class="winner-card">
            <div class="winner-rank silver"><?= $idx + 1 ?></div>
            <div class="winner-info">
              <div class="winner-name"><a href="/store/<?= $w['store_id'] ?>"><?= htmlspecialchars($w['store_name']) ?></a></div>
              <div class="winner-address"><?= htmlspecialchars($w['address']) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (count($second_winners) > 20): ?>
          <div style="text-align: center; padding: 16px; color: var(--text-muted);">외 <?= count($second_winners) - 20 ?>곳</div>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      </section>

      <div class="nav-rounds">
        <?php if ($round > 1): ?>
        <a href="/lotto/<?= $round - 1 ?>/winners/" class="nav-round">← <?= $round - 1 ?>회 당첨점</a>
        <?php else: ?>
        <span></span>
        <?php endif; ?>
        <a href="/lotto/<?= $round + 1 ?>/winners/" class="nav-round"><?= $round + 1 ?>회 당첨점 →</a>
      </div>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

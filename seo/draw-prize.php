<?php
/**
 * 회차별 당첨금 상세 SEO 페이지
 * URL: /lotto/1202/prize/ → "1202회 당첨금", "1202회 1등 당첨금"
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

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

// 당첨금 정보
$first_prize = (int)($draw['first_prize_each'] ?? 0);
$first_winners = (int)($draw['first_winners'] ?? 0);
$first_total = (int)($draw['first_prize_total'] ?? $first_prize * $first_winners);

$second_prize = (int)($draw['second_prize_each'] ?? 0);
$second_winners = (int)($draw['second_winners'] ?? 0);

$third_prize = (int)($draw['third_prize_each'] ?? 0);
$third_winners = (int)($draw['third_winners'] ?? 0);

// 4등, 5등은 고정
$fourth_prize = 50000;
$fourth_winners = (int)($draw['fourth_winners'] ?? 0);

$fifth_prize = 5000;
$fifth_winners = (int)($draw['fifth_winners'] ?? 0);

// 총 판매액 (있다면)
$total_sales = (int)($draw['total_sales'] ?? 0);

// 세금 계산
function calc_tax($amount) {
    if ($amount <= 50000) return 0;
    if ($amount <= 300000000) {
        return $amount * 0.22;
    }
    return (300000000 * 0.22) + (($amount - 300000000) * 0.33);
}

$first_tax = calc_tax($first_prize);
$first_net = $first_prize - $first_tax;

// 포맷 함수
function format_prize($amount) {
    if (!$amount || $amount <= 0) return '-';
    $eok = floor($amount / 100000000);
    $man = floor(($amount % 100000000) / 10000);
    $out = '';
    if ($eok > 0) $out .= number_format($eok) . '억 ';
    if ($man > 0 && $eok < 100) $out .= number_format($man) . '만';
    return trim($out) . '원';
}

function format_full($amount) {
    return number_format($amount) . '원';
}

function get_ball_class($n) {
    if ($n <= 10) return 'ball-yellow';
    if ($n <= 20) return 'ball-blue';
    if ($n <= 30) return 'ball-red';
    if ($n <= 40) return 'ball-gray';
    return 'ball-green';
}

$page_title = "로또 {$round}회 당첨금 - 1등 " . format_prize($first_prize);
$page_desc = "로또 {$round}회 (" . date('Y년 n월 j일', strtotime($draw_date)) . ") 등수별 당첨금 상세. 1등 " . format_prize($first_prize) . " ({$first_winners}명), 실수령액 " . format_prize($first_net) . ".";
$canonical = "https://lottoinsight.ai/lotto/{$round}/prize/";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <title><?= $page_title ?> | 오늘로또</title>
  <meta name="description" content="<?= $page_desc ?>">
  <meta name="keywords" content="로또 <?= $round ?>회 당첨금, <?= $round ?>회 1등 당첨금, 로또 당첨금, 로또 세금">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:type" content="article">
  <meta property="og:title" content="<?= $page_title ?>">
  <meta property="og:description" content="<?= $page_desc ?>">
  
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $page_title ?>">

  <!-- BreadcrumbList -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "홈", "item": "https://lottoinsight.ai/"},
      {"@type": "ListItem", "position": 2, "name": "회차별 결과", "item": "https://lottoinsight.ai/lotto/"},
      {"@type": "ListItem", "position": 3, "name": "<?= $round ?>회", "item": "https://lottoinsight.ai/lotto/<?= $round ?>/"},
      {"@type": "ListItem", "position": 4, "name": "당첨금", "item": "<?= $canonical ?>"}
    ]
  }
  </script>

  <!-- Dataset Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Dataset",
    "name": "로또 <?= $round ?>회 당첨금 데이터",
    "description": "로또 <?= $round ?>회 등수별 당첨금 및 당첨자 수 데이터",
    "url": "<?= $canonical ?>",
    "dateModified": "<?= $draw_date ?>",
    "creator": {"@type": "Organization", "name": "동행복권"},
    "distribution": {
      "@type": "DataDownload",
      "contentUrl": "<?= $canonical ?>",
      "encodingFormat": "text/html"
    },
    "variableMeasured": [
      {"@type": "PropertyValue", "name": "1등 당첨금", "value": "<?= $first_prize ?>"},
      {"@type": "PropertyValue", "name": "1등 당첨자", "value": "<?= $first_winners ?>"},
      {"@type": "PropertyValue", "name": "2등 당첨금", "value": "<?= $second_prize ?>"},
      {"@type": "PropertyValue", "name": "2등 당첨자", "value": "<?= $second_winners ?>"}
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
    .container { max-width: 900px; margin: 0 auto; padding: 24px; }
    
    .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 16px 24px; background: rgba(5,10,21,0.95); backdrop-filter: blur(20px); }
    .nav-container { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .nav-logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .nav-logo-text { font-family: 'Outfit', sans-serif; font-weight: 800; background: linear-gradient(135deg, #00E0A4, #00D4FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .main { padding-top: 80px; }
    .breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 24px; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb a:hover { color: var(--accent-cyan); }
    
    .draw-header { text-align: center; padding: 32px; background: rgba(13,24,41,0.8); border-radius: 24px; margin-bottom: 24px; }
    .draw-round { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 900; margin-bottom: 8px; }
    .draw-date { color: var(--text-secondary); margin-bottom: 20px; }
    .draw-balls { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
    .ball { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; color: #fff; }
    .ball-yellow { background: var(--ball-yellow); }
    .ball-blue { background: var(--ball-blue); }
    .ball-red { background: var(--ball-red); }
    .ball-gray { background: var(--ball-gray); }
    .ball-green { background: var(--ball-green); }
    .bonus-sep { color: var(--text-muted); font-size: 1.5rem; display: flex; align-items: center; }
    
    /* Prize Table */
    .prize-section { background: rgba(13,24,41,0.8); border-radius: 20px; overflow: hidden; margin-bottom: 24px; }
    .prize-header { padding: 16px 24px; background: rgba(0,0,0,0.3); font-weight: 700; }
    .prize-table { width: 100%; border-collapse: collapse; }
    .prize-table th, .prize-table td { padding: 16px 20px; text-align: left; border-bottom: 1px solid var(--glass-border); }
    .prize-table th { background: rgba(0,0,0,0.2); font-weight: 600; color: var(--text-muted); font-size: 0.9rem; }
    .prize-table tr:last-child td { border-bottom: none; }
    .prize-table .rank { font-weight: 800; }
    .prize-table .rank-1 { color: var(--accent-gold); }
    .prize-table .rank-2 { color: var(--accent-cyan); }
    .prize-table .amount { font-family: 'Outfit', sans-serif; font-weight: 700; }
    .prize-table .winners { color: var(--text-secondary); }
    
    /* 1등 상세 */
    .first-detail { background: linear-gradient(145deg, rgba(255,215,95,0.1), rgba(255,215,95,0.02)); border: 1px solid rgba(255,215,95,0.2); border-radius: 20px; padding: 24px; margin-bottom: 24px; }
    .first-title { font-family: 'Outfit', sans-serif; font-size: 1.3rem; font-weight: 800; color: var(--accent-gold); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .first-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .first-item { background: rgba(0,0,0,0.2); border-radius: 12px; padding: 16px; }
    .first-label { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 4px; }
    .first-value { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; }
    .first-value.gold { color: var(--accent-gold); }
    .first-value.cyan { color: var(--accent-cyan); }
    .first-value.red { color: #ef4444; }
    
    /* Tax Info */
    .tax-info { background: rgba(13,24,41,0.8); border-radius: 20px; padding: 24px; margin-bottom: 24px; }
    .tax-title { font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .tax-calc { display: flex; flex-direction: column; gap: 12px; }
    .tax-row { display: flex; justify-content: space-between; padding: 12px 16px; background: rgba(0,0,0,0.2); border-radius: 8px; }
    .tax-row.total { background: rgba(0,224,164,0.1); border: 1px solid rgba(0,224,164,0.2); }
    .tax-row .label { color: var(--text-secondary); }
    .tax-row .value { font-weight: 600; }
    .tax-row.total .value { color: var(--accent-cyan); font-size: 1.2rem; }
    
    .nav-rounds { display: flex; justify-content: space-between; margin-top: 24px; }
    .nav-round { padding: 12px 24px; background: rgba(255,255,255,0.05); border-radius: 12px; color: var(--text-secondary); text-decoration: none; }
    .nav-round:hover { background: rgba(0,224,164,0.1); color: var(--accent-cyan); }
    
    .related-links { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
    .related-link { padding: 10px 16px; background: rgba(255,255,255,0.05); border-radius: 8px; color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; }
    .related-link:hover { background: rgba(0,224,164,0.1); color: var(--accent-cyan); }
    
    .footer { text-align: center; padding: 40px; color: var(--text-muted); font-size: 0.85rem; margin-top: 40px; }
    .footer a { color: var(--text-muted); text-decoration: none; margin: 0 12px; }
    
    @media (max-width: 768px) {
      .draw-round { font-size: 1.8rem; }
      .ball { width: 40px; height: 40px; font-size: 1rem; }
      .prize-table th, .prize-table td { padding: 12px; font-size: 0.9rem; }
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
        <a href="/lotto/">회차별</a> <span>›</span>
        <a href="/lotto/<?= $round ?>/"><?= $round ?>회</a> <span>›</span>
        <span>당첨금</span>
      </nav>

      <header class="draw-header">
        <div class="draw-round"><?= number_format($round) ?>회 당첨금</div>
        <div class="draw-date"><?= date('Y년 n월 j일', strtotime($draw_date)) ?> 추첨</div>
        <div class="draw-balls">
          <?php foreach ($nums as $n): ?>
          <span class="ball <?= get_ball_class($n) ?>"><?= $n ?></span>
          <?php endforeach; ?>
          <span class="bonus-sep">+</span>
          <span class="ball <?= get_ball_class($bonus) ?>"><?= $bonus ?></span>
        </div>
      </header>

      <!-- 1등 상세 -->
      <section class="first-detail">
        <h2 class="first-title">🥇 1등 당첨금 상세</h2>
        <div class="first-grid">
          <div class="first-item">
            <div class="first-label">1인당 당첨금</div>
            <div class="first-value gold"><?= format_prize($first_prize) ?></div>
          </div>
          <div class="first-item">
            <div class="first-label">당첨자 수</div>
            <div class="first-value"><?= number_format($first_winners) ?>명</div>
          </div>
          <div class="first-item">
            <div class="first-label">세금 (<?= $first_prize > 300000000 ? '22%+33%' : '22%' ?>)</div>
            <div class="first-value red">-<?= format_prize($first_tax) ?></div>
          </div>
          <div class="first-item">
            <div class="first-label">실수령액</div>
            <div class="first-value cyan"><?= format_prize($first_net) ?></div>
          </div>
        </div>
      </section>

      <!-- 세금 계산 상세 -->
      <section class="tax-info">
        <h3 class="tax-title">🧮 1등 세금 계산 상세</h3>
        <div class="tax-calc">
          <div class="tax-row">
            <span class="label">당첨금</span>
            <span class="value"><?= format_full($first_prize) ?></span>
          </div>
          <?php if ($first_prize > 300000000): ?>
          <div class="tax-row">
            <span class="label">3억원까지 22%</span>
            <span class="value">-<?= format_full(300000000 * 0.22) ?></span>
          </div>
          <div class="tax-row">
            <span class="label"><?= format_full($first_prize - 300000000) ?> × 33%</span>
            <span class="value">-<?= format_full(($first_prize - 300000000) * 0.33) ?></span>
          </div>
          <?php else: ?>
          <div class="tax-row">
            <span class="label">세금 (22%)</span>
            <span class="value">-<?= format_full($first_tax) ?></span>
          </div>
          <?php endif; ?>
          <div class="tax-row total">
            <span class="label">✅ 실수령액</span>
            <span class="value"><?= format_full($first_net) ?></span>
          </div>
        </div>
      </section>

      <!-- 등수별 당첨금 -->
      <section class="prize-section">
        <div class="prize-header">📊 등수별 당첨금</div>
        <table class="prize-table">
          <thead>
            <tr>
              <th>등수</th>
              <th>당첨 조건</th>
              <th>당첨금</th>
              <th>당첨자</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="rank rank-1">🥇 1등</td>
              <td>6개 번호 일치</td>
              <td class="amount"><?= format_prize($first_prize) ?></td>
              <td class="winners"><?= number_format($first_winners) ?>명</td>
            </tr>
            <tr>
              <td class="rank rank-2">🥈 2등</td>
              <td>5개 + 보너스</td>
              <td class="amount"><?= format_prize($second_prize) ?></td>
              <td class="winners"><?= number_format($second_winners) ?>명</td>
            </tr>
            <tr>
              <td class="rank">🥉 3등</td>
              <td>5개 번호 일치</td>
              <td class="amount"><?= format_prize($third_prize) ?></td>
              <td class="winners"><?= number_format($third_winners) ?>명</td>
            </tr>
            <tr>
              <td class="rank">4등</td>
              <td>4개 번호 일치</td>
              <td class="amount">5만원 (고정)</td>
              <td class="winners"><?= number_format($fourth_winners) ?>명</td>
            </tr>
            <tr>
              <td class="rank">5등</td>
              <td>3개 번호 일치</td>
              <td class="amount">5천원 (고정)</td>
              <td class="winners"><?= number_format($fifth_winners) ?>명</td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- 관련 링크 -->
      <div class="related-links">
        <a href="/lotto/<?= $round ?>/" class="related-link">📋 <?= $round ?>회 상세</a>
        <a href="/lotto/<?= $round ?>/winners/" class="related-link">🏆 <?= $round ?>회 당첨점</a>
        <a href="/guide/세금/" class="related-link">💰 세금 계산기</a>
        <a href="/ranking/jackpot/" class="related-link">📊 역대 당첨금 순위</a>
      </div>

      <div class="nav-rounds">
        <?php if ($round > 1): ?>
        <a href="/lotto/<?= $round - 1 ?>/prize/" class="nav-round">← <?= $round - 1 ?>회 당첨금</a>
        <?php else: ?>
        <span></span>
        <?php endif; ?>
        <a href="/lotto/<?= $round + 1 ?>/prize/" class="nav-round"><?= $round + 1 ?>회 당첨금 →</a>
      </div>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

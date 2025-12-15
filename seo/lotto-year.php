<?php
/**
 * 연도별/월별 당첨번호 페이지
 * URL: /lotto/2024년/, /lotto/2024년/12월/
 * 
 * 시계열 SEO 페이지 - 연도/월별 당첨번호 아카이브
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : 0;

// 유효성 검사
$current_year = (int)date('Y');
if ($year < 2002 || $year > $current_year) {
    $year = $current_year;
}

if ($month < 0 || $month > 12) {
    $month = 0;
}

// 해당 연도/월의 회차 범위 계산
// 로또 1회: 2002-12-07
$base_date = strtotime('2002-12-07');
$weeks_per_year = 52;

// 연도의 첫 번째와 마지막 날짜
$year_start = strtotime("{$year}-01-01");
$year_end = strtotime("{$year}-12-31");

// 월별 필터
if ($month > 0) {
    $month_start = strtotime("{$year}-{$month}-01");
    $month_end = strtotime("{$year}-{$month}-" . date('t', $month_start));
    $date_start = date('Y-m-d', $month_start);
    $date_end = date('Y-m-d', $month_end);
} else {
    $date_start = "{$year}-01-01";
    $date_end = "{$year}-12-31";
}

// 해당 기간 당첨번호 조회
$draws = [];
$res = sql_query("
    SELECT * FROM g5_lotto_draw 
    WHERE draw_date BETWEEN '{$date_start}' AND '{$date_end}'
    ORDER BY draw_no DESC
");

while ($row = sql_fetch_array($res)) {
    $draws[] = $row;
}

// 통계 계산
$total_draws = count($draws);
$number_freq = array_fill(1, 45, 0);
$bonus_freq = array_fill(1, 45, 0);
$total_jackpot = 0;
$total_winners = 0;

foreach ($draws as $d) {
    for ($i = 1; $i <= 6; $i++) {
        $n = (int)$d["n{$i}"];
        if ($n >= 1 && $n <= 45) $number_freq[$n]++;
    }
    $bonus = (int)$d['bonus'];
    if ($bonus >= 1 && $bonus <= 45) $bonus_freq[$bonus]++;
    
    $total_jackpot += (int)$d['first_prize_total'];
    $total_winners += (int)$d['first_winners'];
}

// 최다 출현 번호 TOP 5
arsort($number_freq);
$top_numbers = array_slice($number_freq, 0, 5, true);

// 최소 출현 번호 TOP 5
asort($number_freq);
$bottom_numbers = array_slice($number_freq, 0, 5, true);

// 페이지 정보
if ($month > 0) {
    $page_title = "{$year}년 {$month}월 로또 당첨번호 | 오늘로또";
    $page_desc = "{$year}년 {$month}월 로또 6/45 당첨번호 전체 기록. {$total_draws}회 추첨, 1등 당첨자 총 {$total_winners}명.";
    $canonical = "https://lottoinsight.ai/lotto/{$year}년/{$month}월/";
} else {
    $page_title = "{$year}년 로또 당첨번호 전체 기록 | 오늘로또";
    $page_desc = "{$year}년 로또 6/45 당첨번호 전체 아카이브. {$total_draws}회 추첨, 총 당첨금 " . number_format($total_jackpot / 100000000) . "억원.";
    $canonical = "https://lottoinsight.ai/lotto/{$year}년/";
}

function getBallColor($n) {
    $n = (int)$n;
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
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
  
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: 'Noto Sans KR', sans-serif;
      background: linear-gradient(180deg, #050a15 0%, #0a1628 50%, #0d1f3c 100%);
      color: #e2e8f0;
      min-height: 100vh;
    }
    
    .container { max-width: 1000px; margin: 0 auto; padding: 40px 24px; }
    
    .breadcrumb {
      display: flex; flex-wrap: wrap; gap: 8px;
      margin-bottom: 32px; font-size: 0.9rem;
    }
    .breadcrumb a { color: #64748b; text-decoration: none; }
    .breadcrumb a:hover { color: #00E0A4; }
    .breadcrumb span { color: #475569; }
    
    .page-header { text-align: center; margin-bottom: 48px; }
    .page-icon { font-size: 3rem; margin-bottom: 16px; }
    .page-title {
      font-family: 'Outfit', sans-serif;
      font-size: 2.2rem; font-weight: 800;
      background: linear-gradient(135deg, #fff, #94a3b8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .page-subtitle { color: #94a3b8; margin-top: 12px; }
    
    /* 연도/월 네비게이션 */
    .time-nav {
      display: flex; gap: 12px; flex-wrap: wrap;
      justify-content: center; margin-bottom: 32px;
    }
    .time-nav a {
      padding: 10px 20px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 10px;
      color: #94a3b8;
      text-decoration: none;
      transition: all 0.3s;
    }
    .time-nav a:hover, .time-nav a.active {
      background: rgba(0,224,164,0.1);
      border-color: #00E0A4;
      color: #00E0A4;
    }
    
    /* 통계 카드 */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 40px;
    }
    .stat-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 16px;
      padding: 24px;
      text-align: center;
    }
    .stat-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      color: #00E0A4;
    }
    .stat-label { color: #64748b; font-size: 0.9rem; margin-top: 8px; }
    
    /* 최다/최소 번호 */
    .freq-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-bottom: 40px;
    }
    .freq-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 16px;
      padding: 24px;
    }
    .freq-title { font-weight: 700; margin-bottom: 16px; }
    .freq-balls { display: flex; gap: 8px; flex-wrap: wrap; }
    
    /* 볼 스타일 */
    .ball {
      width: 44px; height: 44px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700; font-size: 0.95rem;
      color: #fff;
      position: relative;
    }
    .ball-yellow { background: linear-gradient(145deg, #fcd34d, #f59e0b); }
    .ball-blue { background: linear-gradient(145deg, #60a5fa, #3b82f6); }
    .ball-red { background: linear-gradient(145deg, #f87171, #ef4444); }
    .ball-gray { background: linear-gradient(145deg, #9ca3af, #6b7280); }
    .ball-green { background: linear-gradient(145deg, #4ade80, #22c55e); }
    .ball-bonus { box-shadow: 0 0 0 3px rgba(255,215,95,0.5); }
    .ball-count {
      position: absolute; bottom: -6px; right: -6px;
      background: #1e293b; border-radius: 50%;
      width: 20px; height: 20px;
      font-size: 0.65rem;
      display: flex; align-items: center; justify-content: center;
      border: 2px solid #00E0A4;
      color: #00E0A4;
    }
    
    /* 당첨번호 리스트 */
    .draws-list { margin-top: 40px; }
    .draws-header {
      font-size: 1.2rem; font-weight: 700;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .draw-row {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px;
      background: rgba(13, 24, 41, 0.5);
      border-radius: 12px;
      margin-bottom: 12px;
      transition: all 0.3s;
    }
    .draw-row:hover {
      background: rgba(0,224,164,0.05);
    }
    
    .draw-info {
      min-width: 100px;
    }
    .draw-round {
      font-weight: 700; color: #fff;
    }
    .draw-date { font-size: 0.85rem; color: #64748b; }
    
    .draw-numbers {
      display: flex; gap: 6px; flex: 1;
    }
    .draw-numbers .ball { width: 36px; height: 36px; font-size: 0.85rem; }
    .draw-numbers .plus { color: #64748b; margin: 0 4px; }
    
    .draw-prize {
      text-align: right; min-width: 120px;
    }
    .prize-amount { font-weight: 600; color: #FFD75F; }
    .prize-winners { font-size: 0.8rem; color: #64748b; }
    
    .draw-link {
      color: #64748b;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 8px;
      transition: all 0.3s;
    }
    .draw-link:hover {
      color: #00E0A4;
      background: rgba(0,224,164,0.1);
    }
    
    /* 월별 네비게이션 */
    .month-nav {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 8px;
      margin-bottom: 32px;
    }
    .month-nav a {
      padding: 12px;
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 10px;
      text-align: center;
      color: #94a3b8;
      text-decoration: none;
      transition: all 0.3s;
    }
    .month-nav a:hover, .month-nav a.active {
      background: rgba(0,224,164,0.1);
      border-color: #00E0A4;
      color: #00E0A4;
    }
    .month-nav a.disabled {
      opacity: 0.3;
      pointer-events: none;
    }
    
    @media (max-width: 768px) {
      .freq-section { grid-template-columns: 1fr; }
      .month-nav { grid-template-columns: repeat(4, 1fr); }
      .draw-row { flex-wrap: wrap; }
      .draw-prize { width: 100%; text-align: left; margin-top: 8px; }
    }
  </style>
  
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Dataset",
    "name": "<?= $year ?>년<?= $month ? " {$month}월" : '' ?> 로또 당첨번호",
    "description": "<?= htmlspecialchars($page_desc) ?>",
    "temporalCoverage": "<?= $year ?><?= $month ? "-" . str_pad($month, 2, '0', STR_PAD_LEFT) : '' ?>",
    "distribution": {
      "@type": "DataDownload",
      "encodingFormat": "text/html",
      "contentUrl": "<?= $canonical ?>"
    }
  }
  </script>
</head>
<body>
  <div class="container">
    <!-- 브레드크럼 -->
    <nav class="breadcrumb">
      <a href="/">홈</a>
      <span>›</span>
      <a href="/lotto/<?= $draws[0]['draw_no'] ?? '' ?>/">당첨번호</a>
      <span>›</span>
      <?php if ($month): ?>
      <a href="/lotto/<?= $year ?>년/"><?= $year ?>년</a>
      <span>›</span>
      <span><?= $month ?>월</span>
      <?php else: ?>
      <span><?= $year ?>년</span>
      <?php endif; ?>
    </nav>
    
    <!-- 헤더 -->
    <header class="page-header">
      <div class="page-icon">📅</div>
      <h1 class="page-title">
        <?= $year ?>년<?= $month ? " {$month}월" : '' ?> 로또 당첨번호
      </h1>
      <p class="page-subtitle"><?= $total_draws ?>회 추첨 기록</p>
    </header>
    
    <!-- 연도 네비게이션 -->
    <nav class="time-nav">
      <?php for ($y = $current_year; $y >= max(2002, $current_year - 5); $y--): ?>
      <a href="/lotto/<?= $y ?>년/" class="<?= $y === $year && !$month ? 'active' : '' ?>"><?= $y ?>년</a>
      <?php endfor; ?>
    </nav>
    
    <?php if (!$month): ?>
    <!-- 월별 네비게이션 (연도 페이지에서만) -->
    <nav class="month-nav">
      <?php for ($m = 1; $m <= 12; $m++): ?>
      <?php
        $m_start = strtotime("{$year}-{$m}-01");
        $is_future = $m_start > time();
      ?>
      <a href="/lotto/<?= $year ?>년/<?= $m ?>월/" 
         class="<?= $is_future ? 'disabled' : '' ?>"><?= $m ?>월</a>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>
    
    <!-- 통계 -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value"><?= number_format($total_draws) ?></div>
        <div class="stat-label">총 추첨 횟수</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= number_format($total_winners) ?></div>
        <div class="stat-label">1등 당첨자 수</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= number_format($total_jackpot / 100000000, 0) ?>억</div>
        <div class="stat-label">총 1등 당첨금</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= $total_winners > 0 ? number_format($total_jackpot / $total_winners / 100000000, 1) : 0 ?>억</div>
        <div class="stat-label">평균 당첨금</div>
      </div>
    </div>
    
    <!-- 최다/최소 출현 번호 -->
    <div class="freq-section">
      <div class="freq-card">
        <h3 class="freq-title">🔥 최다 출현 번호</h3>
        <div class="freq-balls">
          <?php foreach ($top_numbers as $num => $cnt): ?>
          <a href="/number/<?= $num ?>/" class="ball ball-<?= getBallColor($num) ?>">
            <?= $num ?>
            <span class="ball-count"><?= $cnt ?></span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="freq-card">
        <h3 class="freq-title">❄️ 최소 출현 번호</h3>
        <div class="freq-balls">
          <?php foreach ($bottom_numbers as $num => $cnt): ?>
          <a href="/number/<?= $num ?>/" class="ball ball-<?= getBallColor($num) ?>">
            <?= $num ?>
            <span class="ball-count"><?= $cnt ?></span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    
    <!-- 당첨번호 리스트 -->
    <section class="draws-list">
      <h2 class="draws-header">📋 전체 당첨번호 (<?= $total_draws ?>회)</h2>
      
      <?php foreach ($draws as $d): ?>
      <div class="draw-row">
        <div class="draw-info">
          <div class="draw-round"><?= $d['draw_no'] ?>회</div>
          <div class="draw-date"><?= $d['draw_date'] ?></div>
        </div>
        <div class="draw-numbers">
          <?php for ($i = 1; $i <= 6; $i++): ?>
          <span class="ball ball-<?= getBallColor($d["n{$i}"]) ?>"><?= $d["n{$i}"] ?></span>
          <?php endfor; ?>
          <span class="plus">+</span>
          <span class="ball ball-<?= getBallColor($d['bonus']) ?> ball-bonus"><?= $d['bonus'] ?></span>
        </div>
        <div class="draw-prize">
          <div class="prize-amount"><?= number_format($d['first_prize_each'] / 100000000, 1) ?>억</div>
          <div class="prize-winners"><?= $d['first_winners'] ?>명 당첨</div>
        </div>
        <a href="/lotto/<?= $d['draw_no'] ?>/" class="draw-link">상세 →</a>
      </div>
      <?php endforeach; ?>
      
      <?php if (empty($draws)): ?>
      <p style="text-align: center; color: #64748b; padding: 40px;">
        해당 기간의 당첨번호 데이터가 없습니다.
      </p>
      <?php endif; ?>
    </section>
  </div>
  
  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

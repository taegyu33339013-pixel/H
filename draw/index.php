<?php
/**
 * /draw/index.php - 회차별 결과 페이지
 * 
 * URL 패턴:
 * /draw/              → 최신 회차
 * /draw/1148          → 특정 회차
 * /draw/latest        → 최신 회차
 */

if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

$latest_round = 1148;

// URL에서 회차 파싱
$request_uri = urldecode($_SERVER['REQUEST_URI']);
$round = $latest_round;

if (preg_match('/\/draw\/(\d+)/', $request_uri, $matches)) {
    $round = (int)$matches[1];
}
$round = max(1, min($latest_round, $round));

// ============================================
// 회차별 데이터
// ============================================
$draw_data = [
    1148 => [
        'round' => 1148, 'date' => '2024.12.14', 'day' => '토',
        'numbers' => [3, 12, 18, 27, 35, 42], 'bonus' => 7,
        'prizes' => [
            1 => ['count' => 12, 'amount' => 2532417890, 'total' => 30389014680],
            2 => ['count' => 68, 'amount' => 54328760, 'total' => 3694355680],
            3 => ['count' => 2847, 'amount' => 1523450, 'total' => 4338264150],
            4 => ['count' => 142350, 'amount' => 50000, 'total' => 7117500000],
            5 => ['count' => 2385670, 'amount' => 5000, 'total' => 11928350000],
        ],
        'total_sales' => 112700000000, 'sales_change' => 3.2,
        'auto_count' => 8, 'manual_count' => 2, 'semi_count' => 2,
        'regions' => ['서울' => 3, '경기' => 4, '부산' => 2, '대구' => 1, '인천' => 1, '광주' => 1],
    ],
    1147 => [
        'round' => 1147, 'date' => '2024.12.07', 'day' => '토',
        'numbers' => [5, 11, 16, 28, 34, 43], 'bonus' => 21,
        'prizes' => [
            1 => ['count' => 15, 'amount' => 1987654320, 'total' => 29814814800],
            2 => ['count' => 82, 'amount' => 48765430, 'total' => 3998765260],
            3 => ['count' => 3124, 'amount' => 1456780, 'total' => 4551380720],
            4 => ['count' => 156780, 'amount' => 50000, 'total' => 7839000000],
            5 => ['count' => 2567890, 'amount' => 5000, 'total' => 12839450000],
        ],
        'total_sales' => 109200000000, 'sales_change' => -1.5,
        'auto_count' => 10, 'manual_count' => 3, 'semi_count' => 2,
        'regions' => ['서울' => 4, '경기' => 5, '부산' => 3, '대전' => 2, '울산' => 1],
    ],
    1146 => [
        'round' => 1146, 'date' => '2024.11.30', 'day' => '토',
        'numbers' => [2, 9, 17, 25, 38, 44], 'bonus' => 13,
        'prizes' => [
            1 => ['count' => 8, 'amount' => 3125000000, 'total' => 25000000000],
            2 => ['count' => 56, 'amount' => 62500000, 'total' => 3500000000],
            3 => ['count' => 2456, 'amount' => 1587650, 'total' => 3899232400],
            4 => ['count' => 134560, 'amount' => 50000, 'total' => 6728000000],
            5 => ['count' => 2234560, 'amount' => 5000, 'total' => 11172800000],
        ],
        'total_sales' => 110800000000, 'sales_change' => 2.1,
        'auto_count' => 5, 'manual_count' => 2, 'semi_count' => 1,
        'regions' => ['서울' => 2, '경기' => 3, '인천' => 1, '대구' => 1, '세종' => 1],
    ],
    1145 => [
        'round' => 1145, 'date' => '2024.11.23', 'day' => '토',
        'numbers' => [7, 14, 21, 29, 36, 41], 'bonus' => 3,
        'prizes' => [
            1 => ['count' => 18, 'amount' => 1567890123, 'total' => 28222022214],
            2 => ['count' => 95, 'amount' => 42356780, 'total' => 4023894100],
            3 => ['count' => 3567, 'amount' => 1398760, 'total' => 4989347920],
            4 => ['count' => 167890, 'amount' => 50000, 'total' => 8394500000],
            5 => ['count' => 2789450, 'amount' => 5000, 'total' => 13947250000],
        ],
        'total_sales' => 108500000000, 'sales_change' => -0.8,
        'auto_count' => 12, 'manual_count' => 4, 'semi_count' => 2,
        'regions' => ['서울' => 5, '경기' => 6, '부산' => 3, '광주' => 2, '충남' => 2],
    ],
    1144 => [
        'round' => 1144, 'date' => '2024.11.16', 'day' => '토',
        'numbers' => [1, 8, 19, 26, 33, 45], 'bonus' => 11,
        'prizes' => [
            1 => ['count' => 11, 'amount' => 2234567890, 'total' => 24580246790],
            2 => ['count' => 72, 'amount' => 51234560, 'total' => 3688888320],
            3 => ['count' => 2987, 'amount' => 1478900, 'total' => 4418454300],
            4 => ['count' => 145670, 'amount' => 50000, 'total' => 7283500000],
            5 => ['count' => 2456780, 'amount' => 5000, 'total' => 12283900000],
        ],
        'total_sales' => 109500000000, 'sales_change' => 1.2,
        'auto_count' => 7, 'manual_count' => 3, 'semi_count' => 1,
        'regions' => ['서울' => 3, '경기' => 4, '부산' => 2, '대전' => 1, '강원' => 1],
    ],
];

// 현재 회차 데이터
$data = $draw_data[$round] ?? [
    'round' => $round, 'date' => '2024.01.01', 'day' => '토',
    'numbers' => [1, 2, 3, 4, 5, 6], 'bonus' => 7,
    'prizes' => [
        1 => ['count' => 10, 'amount' => 2000000000, 'total' => 20000000000],
        2 => ['count' => 50, 'amount' => 50000000, 'total' => 2500000000],
        3 => ['count' => 2000, 'amount' => 1500000, 'total' => 3000000000],
        4 => ['count' => 100000, 'amount' => 50000, 'total' => 5000000000],
        5 => ['count' => 2000000, 'amount' => 5000, 'total' => 10000000000],
    ],
    'total_sales' => 100000000000, 'sales_change' => 0,
    'auto_count' => 7, 'manual_count' => 2, 'semi_count' => 1,
    'regions' => ['서울' => 3, '경기' => 4, '부산' => 2, '대구' => 1],
];

$numbers = $data['numbers'];
$bonus = $data['bonus'];

// 분석
$range_dist = [0, 0, 0, 0, 0];
foreach ($numbers as $n) {
    if ($n <= 10) $range_dist[0]++;
    elseif ($n <= 20) $range_dist[1]++;
    elseif ($n <= 30) $range_dist[2]++;
    elseif ($n <= 40) $range_dist[3]++;
    else $range_dist[4]++;
}
$odd_count = count(array_filter($numbers, fn($n) => $n % 2 === 1));
$even_count = 6 - $odd_count;
$sum = array_sum($numbers);

// 최근 5회차
$recent_rounds = [];
for ($r = $latest_round; $r >= max(1, $latest_round - 4); $r--) {
    $recent_rounds[] = $draw_data[$r] ?? ['round' => $r, 'numbers' => [1,2,3,4,5,6], 'bonus' => 7, 'date' => ''];
}

function get_ball_class($n) {
    if ($n <= 10) return 'ball-yellow';
    if ($n <= 20) return 'ball-blue';
    if ($n <= 30) return 'ball-red';
    if ($n <= 40) return 'ball-gray';
    return 'ball-green';
}

function format_money($amount) {
    if ($amount >= 100000000) return number_format($amount / 100000000, 1) . '억';
    if ($amount >= 10000) return number_format($amount / 10000, 0) . '만';
    return number_format($amount);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>제 <?= $round ?>회 로또 당첨결과 | 오늘로또</title>
  <meta name="description" content="로또 <?= $round ?>회 당첨번호: <?= implode(', ', $numbers) ?> + <?= $bonus ?>. 1등 <?= $data['prizes'][1]['count'] ?>명">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  <style>
    :root {
      --bg-deep: #080b14; --bg-secondary: #151c2c; --bg-card: #1a2236; --bg-hover: #212b40;
      --gold: #F5B800; --gold-light: #FFD54F; --red: #FF4757; --blue: #00B4D8; --purple: #9D4EDD; --green: #00E676; --pink: #EC4899;
      --text-primary: #fff; --text-secondary: #a8b5c8; --text-muted: #6b7a90; --border: rgba(255,255,255,0.08);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Pretendard', sans-serif; background: var(--bg-deep); background-image: radial-gradient(at 50% 0%, rgba(157,78,221,0.12) 0px, transparent 50%); background-attachment: fixed; color: var(--text-primary); line-height: 1.6; }
    a { color: inherit; text-decoration: none; }
    
    .header { position: sticky; top: 0; z-index: 100; background: rgba(8,11,20,0.95); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border); }
    .header-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; height: 56px; display: flex; align-items: center; justify-content: space-between; }
    .logo { display: flex; align-items: center; gap: 10px; }
    .logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #F5B800, #FF8C00); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .logo-text { font-weight: 800; font-size: 1.2rem; background: linear-gradient(135deg, #F5B800, #FF8C00); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .header-nav { display: flex; gap: 4px; }
    .nav-link { padding: 8px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; color: var(--text-secondary); }
    .nav-link:hover { background: rgba(245,184,0,0.1); color: var(--gold); }
    .nav-link.active { background: rgba(157,78,221,0.1); color: var(--purple); }
    .btn-gold { padding: 10px 20px; border-radius: 10px; background: linear-gradient(135deg, #F5B800, #FF8C00); color: #000; font-weight: 600; }
    
    .main { max-width: 1200px; margin: 0 auto; padding: 24px 20px 100px; }
    
    .round-selector { display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 32px; }
    .round-nav { width: 48px; height: 48px; border-radius: 14px; background: var(--bg-card); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; }
    .round-nav:hover { background: var(--bg-hover); border-color: var(--purple); }
    .round-nav.disabled { opacity: 0.3; pointer-events: none; }
    .round-display { text-align: center; }
    .round-badge { display: inline-flex; padding: 6px 14px; border-radius: 20px; background: linear-gradient(135deg, #9D4EDD, #EC4899); font-size: 0.8rem; font-weight: 700; margin-bottom: 8px; }
    .round-number { font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #9D4EDD, #EC4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .round-date { color: var(--text-muted); font-size: 0.9rem; }
    
    .result-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 24px; overflow: hidden; margin-bottom: 24px; }
    .result-header { padding: 40px 32px; background: linear-gradient(180deg, rgba(157,78,221,0.1), transparent); text-align: center; }
    .result-label { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px; }
    .result-balls { display: flex; justify-content: center; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .ball { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; box-shadow: 0 8px 24px rgba(0,0,0,0.3); transition: transform 0.3s; }
    .ball:hover { transform: scale(1.1); }
    .ball-yellow { background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #000; }
    .ball-blue { background: linear-gradient(135deg, #3B82F6, #2563EB); color: #fff; }
    .ball-red { background: linear-gradient(135deg, #EF4444, #DC2626); color: #fff; }
    .ball-gray { background: linear-gradient(135deg, #6B7280, #4B5563); color: #fff; }
    .ball-green { background: linear-gradient(135deg, #22C55E, #16A34A); color: #fff; }
    .ball-bonus { background: linear-gradient(135deg, #9D4EDD, #EC4899); color: #fff; }
    .ball-plus { font-size: 1.5rem; color: var(--text-muted); margin: 0 8px; }
    
    .result-summary { display: flex; justify-content: center; gap: 32px; padding: 16px; background: var(--bg-secondary); border-radius: 16px; max-width: 600px; margin: 0 auto; flex-wrap: wrap; }
    .summary-item { text-align: center; }
    .summary-value { font-size: 1.1rem; font-weight: 700; margin-bottom: 2px; }
    .summary-label { font-size: 0.75rem; color: var(--text-muted); }
    
    .prize-section { padding: 32px; border-top: 1px solid var(--border); }
    .prize-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 20px; }
    .prize-table { width: 100%; border-collapse: collapse; }
    .prize-table th, .prize-table td { padding: 16px 12px; text-align: center; border-bottom: 1px solid var(--border); }
    .prize-table th { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); background: var(--bg-secondary); }
    .prize-table tr:last-child td { border-bottom: none; }
    .prize-rank { width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 800; }
    .prize-rank.r1 { background: linear-gradient(135deg, #F5B800, #FF8C00); color: #000; }
    .prize-rank.r2 { background: linear-gradient(135deg, #C0C0C0, #A0A0A0); color: #000; }
    .prize-rank.r3 { background: linear-gradient(135deg, #CD7F32, #8B4513); color: #fff; }
    .prize-rank.r4, .prize-rank.r5 { background: var(--bg-hover); color: var(--text-secondary); }
    .prize-count { font-size: 1.1rem; font-weight: 700; }
    .prize-amount { font-size: 1.1rem; font-weight: 700; color: var(--gold); }
    .prize-condition { font-size: 0.8rem; color: var(--text-muted); }
    
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 20px; text-align: center; }
    .stat-icon { font-size: 1.5rem; margin-bottom: 8px; }
    .stat-value { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
    .stat-value.gold { color: var(--gold); }
    .stat-value.blue { color: var(--blue); }
    .stat-value.purple { color: var(--purple); }
    .stat-value.green { color: var(--green); }
    .stat-label { font-size: 0.8rem; color: var(--text-muted); }
    .stat-change { font-size: 0.85rem; margin-top: 4px; }
    .stat-change.up { color: var(--green); }
    .stat-change.down { color: var(--red); }
    
    .analysis-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px; }
    .analysis-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 24px; }
    .analysis-title { font-size: 1rem; font-weight: 700; margin-bottom: 16px; }
    
    .range-bars { display: flex; flex-direction: column; gap: 12px; }
    .range-item { display: flex; align-items: center; gap: 12px; }
    .range-label { width: 60px; font-size: 0.85rem; color: var(--text-secondary); }
    .range-bar-bg { flex: 1; height: 24px; background: var(--bg-secondary); border-radius: 12px; overflow: hidden; }
    .range-bar { height: 100%; border-radius: 12px; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; font-size: 0.75rem; font-weight: 700; color: #fff; min-width: 30px; }
    .range-bar.yellow { background: linear-gradient(90deg, #FBBF24, #F59E0B); }
    .range-bar.blue { background: linear-gradient(90deg, #3B82F6, #2563EB); }
    .range-bar.red { background: linear-gradient(90deg, #EF4444, #DC2626); }
    .range-bar.gray { background: linear-gradient(90deg, #6B7280, #4B5563); }
    .range-bar.green { background: linear-gradient(90deg, #22C55E, #16A34A); }
    
    .ratio-display { display: flex; gap: 16px; }
    .ratio-item { flex: 1; padding: 20px; border-radius: 16px; background: var(--bg-secondary); text-align: center; }
    .ratio-value { font-size: 2rem; font-weight: 800; margin-bottom: 4px; }
    .ratio-label { font-size: 0.85rem; color: var(--text-muted); }
    
    .sum-display { text-align: center; padding: 24px; background: var(--bg-secondary); border-radius: 16px; margin-top: 20px; }
    .sum-value { font-size: 3rem; font-weight: 800; color: var(--purple); }
    .sum-label { font-size: 0.9rem; color: var(--text-muted); }
    
    .winner-map { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 24px; margin-bottom: 24px; }
    .winner-map-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; }
    .winner-regions { display: flex; flex-wrap: wrap; gap: 8px; }
    .winner-region { padding: 10px 16px; border-radius: 10px; background: var(--bg-secondary); font-size: 0.9rem; }
    .winner-region .count { font-weight: 700; color: var(--gold); margin-left: 4px; }
    
    .recent-section { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; }
    .recent-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .recent-title { font-size: 1.1rem; font-weight: 700; }
    .recent-more { font-size: 0.85rem; color: var(--text-muted); }
    .recent-item { display: flex; align-items: center; gap: 16px; padding: 16px 24px; border-bottom: 1px solid var(--border); }
    .recent-item:hover { background: var(--bg-hover); }
    .recent-item:last-child { border-bottom: none; }
    .recent-item.active { background: rgba(157,78,221,0.1); }
    .recent-round { width: 60px; font-weight: 700; color: var(--purple); }
    .recent-balls { flex: 1; display: flex; gap: 6px; flex-wrap: wrap; }
    .recent-ball { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }
    .recent-date { font-size: 0.85rem; color: var(--text-muted); width: 100px; text-align: right; }
    .recent-prize { font-size: 0.9rem; font-weight: 600; color: var(--gold); width: 80px; text-align: right; }
    
    .bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(8,11,20,0.95); backdrop-filter: blur(10px); border-top: 1px solid var(--border); z-index: 90; }
    .bottom-bar-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; height: 56px; display: flex; align-items: center; justify-content: space-between; }
    .bottom-stats { display: flex; gap: 32px; font-size: 0.85rem; }
    .bottom-actions { display: flex; gap: 12px; }
    .action-btn { padding: 10px 20px; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; display: flex; align-items: center; gap: 6px; }
    .action-btn.primary { background: linear-gradient(135deg, #9D4EDD, #EC4899); color: #fff; }
    .action-btn.secondary { background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary); }
    
    @media (max-width: 768px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .analysis-grid { grid-template-columns: 1fr; }
      .ball { width: 48px; height: 48px; font-size: 1.1rem; }
      .header-nav { display: none; }
      .bottom-stats { display: none; }
    }
  </style>
</head>
<body>

<header class="header">
  <div class="header-inner">
    <a href="/" class="logo"><div class="logo-icon">🎰</div><span class="logo-text">오늘로또</span></a>
    <nav class="header-nav">
      <a href="/" class="nav-link">홈</a>
      <a href="/stores/" class="nav-link">당첨점</a>
      <a href="/draw/" class="nav-link active">회차별 결과</a>
      <a href="/ai/" class="nav-link">AI 분석</a>
    </nav>
    <a href="/stores/" class="btn-gold">✨ 당첨점</a>
  </div>
</header>

<main class="main">
  <div class="round-selector">
    <a href="/draw/<?= $round - 1 ?>" class="round-nav <?= $round <= 1 ? 'disabled' : '' ?>">◀</a>
    <div class="round-display">
      <div class="round-badge"><?= $round === $latest_round ? '🆕 최신' : '📅 과거' ?></div>
      <div class="round-number">제 <?= $round ?>회</div>
      <div class="round-date"><?= $data['date'] ?> (<?= $data['day'] ?>) 추첨</div>
    </div>
    <a href="/draw/<?= $round + 1 ?>" class="round-nav <?= $round >= $latest_round ? 'disabled' : '' ?>">▶</a>
  </div>
  
  <div class="result-card">
    <div class="result-header">
      <div class="result-label">🎱 당첨번호</div>
      <div class="result-balls">
        <?php foreach ($numbers as $n): ?><div class="ball <?= get_ball_class($n) ?>"><?= $n ?></div><?php endforeach; ?>
        <span class="ball-plus">+</span>
        <div class="ball ball-bonus"><?= $bonus ?></div>
      </div>
      <div class="result-summary">
        <div class="summary-item"><div class="summary-value" style="color: var(--gold);"><?= format_money($data['prizes'][1]['amount']) ?></div><div class="summary-label">1등 당첨금</div></div>
        <div class="summary-item"><div class="summary-value"><?= $data['prizes'][1]['count'] ?>명</div><div class="summary-label">1등 당첨자</div></div>
        <div class="summary-item"><div class="summary-value" style="color: var(--blue);"><?= format_money($data['total_sales']) ?></div><div class="summary-label">총 판매금액</div></div>
      </div>
    </div>
    
    <div class="prize-section">
      <h3 class="prize-title">🏆 등위별 당첨 현황</h3>
      <table class="prize-table">
        <thead><tr><th>등위</th><th>당첨 조건</th><th>당첨자 수</th><th>1인당 당첨금</th><th>총 당첨금</th></tr></thead>
        <tbody>
          <tr><td><div class="prize-rank r1">1등</div></td><td class="prize-condition">6개 번호 일치</td><td class="prize-count"><?= number_format($data['prizes'][1]['count']) ?>명</td><td class="prize-amount"><?= format_money($data['prizes'][1]['amount']) ?></td><td style="color: var(--text-secondary);"><?= format_money($data['prizes'][1]['total']) ?></td></tr>
          <tr><td><div class="prize-rank r2">2등</div></td><td class="prize-condition">5개 + 보너스</td><td class="prize-count"><?= number_format($data['prizes'][2]['count']) ?>명</td><td class="prize-amount" style="color: var(--blue);"><?= format_money($data['prizes'][2]['amount']) ?></td><td style="color: var(--text-secondary);"><?= format_money($data['prizes'][2]['total']) ?></td></tr>
          <tr><td><div class="prize-rank r3">3등</div></td><td class="prize-condition">5개 번호 일치</td><td class="prize-count"><?= number_format($data['prizes'][3]['count']) ?>명</td><td class="prize-amount" style="color: var(--purple);"><?= format_money($data['prizes'][3]['amount']) ?></td><td style="color: var(--text-secondary);"><?= format_money($data['prizes'][3]['total']) ?></td></tr>
          <tr><td><div class="prize-rank r4">4등</div></td><td class="prize-condition">4개 번호 일치</td><td class="prize-count"><?= number_format($data['prizes'][4]['count']) ?>명</td><td style="color: var(--text-secondary);">5만원</td><td style="color: var(--text-secondary);"><?= format_money($data['prizes'][4]['total']) ?></td></tr>
          <tr><td><div class="prize-rank r5">5등</div></td><td class="prize-condition">3개 번호 일치</td><td class="prize-count"><?= number_format($data['prizes'][5]['count']) ?>명</td><td style="color: var(--text-secondary);">5천원</td><td style="color: var(--text-secondary);"><?= format_money($data['prizes'][5]['total']) ?></td></tr>
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-value gold"><?= format_money($data['total_sales']) ?></div><div class="stat-label">총 판매액</div><div class="stat-change <?= $data['sales_change'] >= 0 ? 'up' : 'down' ?>"><?= $data['sales_change'] >= 0 ? '▲' : '▼' ?> <?= abs($data['sales_change']) ?>%</div></div>
    <div class="stat-card"><div class="stat-icon">🤖</div><div class="stat-value blue"><?= $data['auto_count'] ?></div><div class="stat-label">자동 당첨</div><div class="stat-change" style="color: var(--text-muted);">1등 기준</div></div>
    <div class="stat-card"><div class="stat-icon">✍️</div><div class="stat-value purple"><?= $data['manual_count'] ?></div><div class="stat-label">수동 당첨</div><div class="stat-change" style="color: var(--text-muted);">1등 기준</div></div>
    <div class="stat-card"><div class="stat-icon">🔄</div><div class="stat-value green"><?= $data['semi_count'] ?></div><div class="stat-label">반자동 당첨</div><div class="stat-change" style="color: var(--text-muted);">1등 기준</div></div>
  </div>
  
  <div class="analysis-grid">
    <div class="analysis-card">
      <h3 class="analysis-title">📊 번호 구간 분포</h3>
      <div class="range-bars">
        <div class="range-item"><div class="range-label">1~10</div><div class="range-bar-bg"><div class="range-bar yellow" style="width: <?= max(15, $range_dist[0] / 6 * 100) ?>%"><?= $range_dist[0] ?></div></div></div>
        <div class="range-item"><div class="range-label">11~20</div><div class="range-bar-bg"><div class="range-bar blue" style="width: <?= max(15, $range_dist[1] / 6 * 100) ?>%"><?= $range_dist[1] ?></div></div></div>
        <div class="range-item"><div class="range-label">21~30</div><div class="range-bar-bg"><div class="range-bar red" style="width: <?= max(15, $range_dist[2] / 6 * 100) ?>%"><?= $range_dist[2] ?></div></div></div>
        <div class="range-item"><div class="range-label">31~40</div><div class="range-bar-bg"><div class="range-bar gray" style="width: <?= max(15, $range_dist[3] / 6 * 100) ?>%"><?= $range_dist[3] ?></div></div></div>
        <div class="range-item"><div class="range-label">41~45</div><div class="range-bar-bg"><div class="range-bar green" style="width: <?= max(15, $range_dist[4] / 6 * 100) ?>%"><?= $range_dist[4] ?></div></div></div>
      </div>
    </div>
    
    <div class="analysis-card">
      <h3 class="analysis-title">⚖️ 홀짝 비율</h3>
      <div class="ratio-display">
        <div class="ratio-item"><div class="ratio-value" style="color: var(--blue);"><?= $odd_count ?></div><div class="ratio-label">홀수</div></div>
        <div class="ratio-item"><div class="ratio-value" style="color: var(--pink);"><?= $even_count ?></div><div class="ratio-label">짝수</div></div>
      </div>
      <div class="sum-display"><div class="sum-value"><?= $sum ?></div><div class="sum-label">번호 합계 (평균: 100~175)</div></div>
    </div>
  </div>
  
  <div class="winner-map">
    <h3 class="winner-map-title">📍 1등 당첨 지역</h3>
    <div class="winner-regions">
      <?php if (isset($data['regions'])): foreach ($data['regions'] as $region => $count): ?>
      <div class="winner-region"><?= $region ?><span class="count"><?= $count ?></span></div>
      <?php endforeach; endif; ?>
    </div>
  </div>
  
  <div class="recent-section">
    <div class="recent-header"><h3 class="recent-title">📅 최근 회차</h3><a href="/draw/history" class="recent-more">전체보기 →</a></div>
    <?php foreach ($recent_rounds as $r): ?>
    <a href="/draw/<?= $r['round'] ?>" class="recent-item <?= $r['round'] === $round ? 'active' : '' ?>">
      <div class="recent-round"><?= $r['round'] ?>회</div>
      <div class="recent-balls">
        <?php foreach ($r['numbers'] as $n): ?><div class="recent-ball <?= get_ball_class($n) ?>"><?= $n ?></div><?php endforeach; ?>
        <span style="margin: 0 4px; color: var(--text-muted);">+</span>
        <div class="recent-ball ball-bonus"><?= $r['bonus'] ?></div>
      </div>
      <div class="recent-date"><?= $r['date'] ?></div>
      <div class="recent-prize"><?= isset($draw_data[$r['round']]) ? format_money($draw_data[$r['round']]['prizes'][1]['amount']) : '-' ?></div>
    </a>
    <?php endforeach; ?>
  </div>
</main>

<div class="bottom-bar">
  <div class="bottom-bar-inner">
    <div class="bottom-stats">
      <span><span style="color: var(--text-muted);">총 판매</span> <strong style="color: var(--gold);"><?= format_money($data['total_sales']) ?></strong></span>
      <span><span style="color: var(--text-muted);">1등</span> <strong><?= $data['prizes'][1]['count'] ?>명</strong></span>
      <span><span style="color: var(--text-muted);">1인당</span> <strong style="color: var(--gold);"><?= format_money($data['prizes'][1]['amount']) ?></strong></span>
    </div>
    <div class="bottom-actions">
      <a href="/stores/?tab=round&round=<?= $round ?>" class="action-btn secondary">🏪 당첨점 보기</a>
      <button class="action-btn primary" onclick="navigator.share ? navigator.share({title:'로또 <?= $round ?>회',url:location.href}) : (navigator.clipboard.writeText(location.href), alert('복사됨!'))">📤 공유</button>
    </div>
  </div>
</div>
</body>
</html>
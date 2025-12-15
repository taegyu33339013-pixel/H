<?php
/**
 * 회차 비교 페이지
 * URL: /compare/1201-vs-1202/
 * 
 * 두 회차의 당첨번호, 당첨금, 통계를 비교
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');

$round1 = isset($_GET['r1']) ? (int)$_GET['r1'] : 0;
$round2 = isset($_GET['r2']) ? (int)$_GET['r2'] : 0;

// 최신 회차
$latest = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($latest['max_round'] ?? 1200);

// 기본값: 최근 2회차
if ($round1 <= 0) $round1 = $max_round - 1;
if ($round2 <= 0) $round2 = $max_round;

// 순서 정렬
if ($round1 > $round2) {
    list($round1, $round2) = [$round2, $round1];
}

// 데이터 조회
$draw1 = sql_fetch("SELECT * FROM g5_lotto_draw WHERE draw_no = '{$round1}'");
$draw2 = sql_fetch("SELECT * FROM g5_lotto_draw WHERE draw_no = '{$round2}'");

if (!$draw1 || !$draw2) {
    header('Location: /');
    exit;
}

// 번호 배열
$nums1 = [(int)$draw1['n1'], (int)$draw1['n2'], (int)$draw1['n3'], (int)$draw1['n4'], (int)$draw1['n5'], (int)$draw1['n6']];
$nums2 = [(int)$draw2['n1'], (int)$draw2['n2'], (int)$draw2['n3'], (int)$draw2['n4'], (int)$draw2['n5'], (int)$draw2['n6']];

// 공통 번호
$common = array_intersect($nums1, $nums2);
$only1 = array_diff($nums1, $nums2);
$only2 = array_diff($nums2, $nums1);

// 통계 비교
function calcStats($nums) {
    $odd = count(array_filter($nums, fn($n) => $n % 2 === 1));
    $even = 6 - $odd;
    $high = count(array_filter($nums, fn($n) => $n > 22));
    $low = 6 - $high;
    $sum = array_sum($nums);
    $consecutive = 0;
    sort($nums);
    for ($i = 0; $i < 5; $i++) {
        if ($nums[$i + 1] - $nums[$i] === 1) $consecutive++;
    }
    return compact('odd', 'even', 'high', 'low', 'sum', 'consecutive');
}

$stats1 = calcStats($nums1);
$stats2 = calcStats($nums2);

function getBallColor($n) {
    $n = (int)$n;
    if ($n <= 10) return 'yellow';
    if ($n <= 20) return 'blue';
    if ($n <= 30) return 'red';
    if ($n <= 40) return 'gray';
    return 'green';
}

$page_title = "{$round1}회 vs {$round2}회 당첨번호 비교 | 오늘로또";
$page_desc = "로또 {$round1}회와 {$round2}회 당첨번호 완벽 비교. 공통 번호 " . count($common) . "개, 당첨금 및 통계 차이 분석.";
$canonical = "https://lottoinsight.ai/compare/{$round1}-vs-{$round2}/";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <link rel="canonical" href="<?= $canonical ?>">
  
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
      font-size: 2rem; font-weight: 800;
      background: linear-gradient(135deg, #fff, #94a3b8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    /* 회차 선택 폼 */
    .compare-form {
      display: flex;
      gap: 16px;
      justify-content: center;
      align-items: center;
      margin-bottom: 40px;
      flex-wrap: wrap;
    }
    .compare-form input {
      width: 100px;
      padding: 12px 16px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 10px;
      color: #fff;
      font-size: 1rem;
      text-align: center;
    }
    .compare-form span {
      font-size: 1.2rem;
      color: #94a3b8;
    }
    .compare-form button {
      padding: 12px 24px;
      background: linear-gradient(135deg, #00E0A4, #00D4FF);
      border: none;
      border-radius: 10px;
      color: #050a15;
      font-weight: 700;
      cursor: pointer;
    }
    
    /* 비교 그리드 */
    .compare-grid {
      display: grid;
      grid-template-columns: 1fr 80px 1fr;
      gap: 24px;
      margin-bottom: 40px;
    }
    
    .compare-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 20px;
      padding: 24px;
      text-align: center;
    }
    
    .compare-card.left { border-color: rgba(59, 130, 246, 0.3); }
    .compare-card.right { border-color: rgba(0, 224, 164, 0.3); }
    
    .compare-vs {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .vs-badge {
      background: linear-gradient(135deg, #FFD75F, #FFA500);
      color: #000;
      padding: 16px;
      border-radius: 50%;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
    }
    
    .round-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 8px;
    }
    .round-date { color: #64748b; font-size: 0.9rem; margin-bottom: 20px; }
    
    /* 번호 볼 */
    .balls-row {
      display: flex;
      gap: 8px;
      justify-content: center;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
    .ball {
      width: 44px; height: 44px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700; font-size: 0.95rem;
      color: #fff;
    }
    .ball-yellow { background: linear-gradient(145deg, #fcd34d, #f59e0b); }
    .ball-blue { background: linear-gradient(145deg, #60a5fa, #3b82f6); }
    .ball-red { background: linear-gradient(145deg, #f87171, #ef4444); }
    .ball-gray { background: linear-gradient(145deg, #9ca3af, #6b7280); }
    .ball-green { background: linear-gradient(145deg, #4ade80, #22c55e); }
    .ball-bonus { box-shadow: 0 0 0 3px rgba(255,215,95,0.5); }
    .ball-common { box-shadow: 0 0 0 3px rgba(0,224,164,0.8); }
    
    .plus { color: #64748b; font-size: 1.2rem; }
    
    /* 당첨금 */
    .prize-info {
      padding: 16px;
      background: rgba(0,0,0,0.2);
      border-radius: 12px;
    }
    .prize-amount {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      color: #FFD75F;
    }
    .prize-winners { color: #64748b; font-size: 0.85rem; }
    
    /* 공통 번호 섹션 */
    .common-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(0,224,164,0.3);
      border-radius: 20px;
      padding: 32px;
      text-align: center;
      margin-bottom: 40px;
    }
    .common-title {
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .common-count {
      font-family: 'Outfit', sans-serif;
      font-size: 3rem;
      font-weight: 800;
      color: #00E0A4;
    }
    .common-label { color: #64748b; margin-bottom: 20px; }
    
    /* 통계 비교 */
    .stats-compare {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 20px;
      padding: 32px;
      margin-bottom: 40px;
    }
    .stats-title {
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 24px;
      text-align: center;
    }
    
    .stat-row {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      gap: 24px;
      align-items: center;
      padding: 16px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .stat-row:last-child { border-bottom: none; }
    
    .stat-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.3rem;
      font-weight: 700;
    }
    .stat-value.left { text-align: right; color: #60a5fa; }
    .stat-value.right { text-align: left; color: #00E0A4; }
    .stat-label {
      text-align: center;
      color: #94a3b8;
      font-size: 0.9rem;
    }
    
    .stat-winner {
      position: relative;
    }
    .stat-winner::after {
      content: '👑';
      position: absolute;
      top: -8px;
      right: -20px;
    }
    
    /* 관련 비교 */
    .related-section {
      margin-top: 48px;
    }
    .related-title {
      font-size: 1.1rem;
      color: #94a3b8;
      margin-bottom: 16px;
    }
    .related-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
    }
    .related-link {
      display: block;
      padding: 16px;
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 12px;
      text-decoration: none;
      text-align: center;
      transition: all 0.3s;
      color: #94a3b8;
    }
    .related-link:hover {
      border-color: #00E0A4;
      background: rgba(0,224,164,0.05);
      color: #00E0A4;
    }
    
    @media (max-width: 768px) {
      .compare-grid {
        grid-template-columns: 1fr;
      }
      .compare-vs {
        order: -1;
      }
      .vs-badge {
        padding: 12px 24px;
        border-radius: 20px;
      }
      .stat-row {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- 브레드크럼 -->
    <nav class="breadcrumb">
      <a href="/">홈</a>
      <span>›</span>
      <a href="/compare/">비교</a>
      <span>›</span>
      <span><?= $round1 ?>회 vs <?= $round2 ?>회</span>
    </nav>
    
    <!-- 헤더 -->
    <header class="page-header">
      <div class="page-icon">⚖️</div>
      <h1 class="page-title"><?= $round1 ?>회 vs <?= $round2 ?>회 비교</h1>
    </header>
    
    <!-- 회차 선택 -->
    <form class="compare-form" method="get" action="/compare/">
      <input type="number" name="r1" value="<?= $round1 ?>" min="1" max="<?= $max_round ?>" placeholder="1회차">
      <span>VS</span>
      <input type="number" name="r2" value="<?= $round2 ?>" min="1" max="<?= $max_round ?>" placeholder="2회차">
      <button type="submit">비교하기</button>
    </form>
    
    <!-- 비교 그리드 -->
    <div class="compare-grid">
      <!-- 왼쪽 (이전 회차) -->
      <div class="compare-card left">
        <h2 class="round-title"><?= $round1 ?>회</h2>
        <p class="round-date"><?= $draw1['draw_date'] ?></p>
        
        <div class="balls-row">
          <?php for ($i = 1; $i <= 6; $i++): 
            $n = (int)$draw1["n{$i}"];
            $isCommon = in_array($n, $common);
          ?>
          <span class="ball ball-<?= getBallColor($n) ?> <?= $isCommon ? 'ball-common' : '' ?>"><?= $n ?></span>
          <?php endfor; ?>
          <span class="plus">+</span>
          <span class="ball ball-<?= getBallColor($draw1['bonus']) ?> ball-bonus"><?= $draw1['bonus'] ?></span>
        </div>
        
        <div class="prize-info">
          <div class="prize-amount"><?= number_format($draw1['first_prize_each'] / 100000000, 1) ?>억</div>
          <div class="prize-winners">1등 <?= $draw1['first_winners'] ?>명</div>
        </div>
      </div>
      
      <!-- VS -->
      <div class="compare-vs">
        <div class="vs-badge">VS</div>
      </div>
      
      <!-- 오른쪽 (최근 회차) -->
      <div class="compare-card right">
        <h2 class="round-title"><?= $round2 ?>회</h2>
        <p class="round-date"><?= $draw2['draw_date'] ?></p>
        
        <div class="balls-row">
          <?php for ($i = 1; $i <= 6; $i++): 
            $n = (int)$draw2["n{$i}"];
            $isCommon = in_array($n, $common);
          ?>
          <span class="ball ball-<?= getBallColor($n) ?> <?= $isCommon ? 'ball-common' : '' ?>"><?= $n ?></span>
          <?php endfor; ?>
          <span class="plus">+</span>
          <span class="ball ball-<?= getBallColor($draw2['bonus']) ?> ball-bonus"><?= $draw2['bonus'] ?></span>
        </div>
        
        <div class="prize-info">
          <div class="prize-amount"><?= number_format($draw2['first_prize_each'] / 100000000, 1) ?>억</div>
          <div class="prize-winners">1등 <?= $draw2['first_winners'] ?>명</div>
        </div>
      </div>
    </div>
    
    <!-- 공통 번호 -->
    <section class="common-section">
      <h3 class="common-title">🔗 공통 번호</h3>
      <div class="common-count"><?= count($common) ?></div>
      <div class="common-label">개 일치</div>
      
      <?php if (count($common) > 0): ?>
      <div class="balls-row">
        <?php foreach ($common as $n): ?>
        <span class="ball ball-<?= getBallColor($n) ?> ball-common"><?= $n ?></span>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p style="color: #64748b;">두 회차에 공통 번호가 없습니다.</p>
      <?php endif; ?>
    </section>
    
    <!-- 통계 비교 -->
    <section class="stats-compare">
      <h3 class="stats-title">📊 패턴 비교</h3>
      
      <div class="stat-row">
        <div class="stat-value left"><?= $stats1['odd'] ?>:<?= $stats1['even'] ?></div>
        <div class="stat-label">홀짝 비율</div>
        <div class="stat-value right"><?= $stats2['odd'] ?>:<?= $stats2['even'] ?></div>
      </div>
      
      <div class="stat-row">
        <div class="stat-value left"><?= $stats1['high'] ?>:<?= $stats1['low'] ?></div>
        <div class="stat-label">고저 비율</div>
        <div class="stat-value right"><?= $stats2['high'] ?>:<?= $stats2['low'] ?></div>
      </div>
      
      <div class="stat-row">
        <div class="stat-value left"><?= $stats1['sum'] ?></div>
        <div class="stat-label">번호 합계</div>
        <div class="stat-value right"><?= $stats2['sum'] ?></div>
      </div>
      
      <div class="stat-row">
        <div class="stat-value left"><?= $stats1['consecutive'] ?></div>
        <div class="stat-label">연속번호 쌍</div>
        <div class="stat-value right"><?= $stats2['consecutive'] ?></div>
      </div>
      
      <div class="stat-row">
        <div class="stat-value left <?= $draw1['first_prize_each'] > $draw2['first_prize_each'] ? 'stat-winner' : '' ?>">
          <?= number_format($draw1['first_prize_each'] / 100000000, 1) ?>억
        </div>
        <div class="stat-label">1등 당첨금</div>
        <div class="stat-value right <?= $draw2['first_prize_each'] > $draw1['first_prize_each'] ? 'stat-winner' : '' ?>">
          <?= number_format($draw2['first_prize_each'] / 100000000, 1) ?>억
        </div>
      </div>
    </section>
    
    <!-- 관련 비교 -->
    <section class="related-section">
      <h3 class="related-title">🔗 다른 비교해보기</h3>
      <div class="related-grid">
        <a href="/compare/<?= $round1 - 1 ?>-vs-<?= $round1 ?>/" class="related-link">
          <?= $round1 - 1 ?>회 vs <?= $round1 ?>회
        </a>
        <a href="/compare/<?= $round2 ?>-vs-<?= $round2 + 1 ?>/" class="related-link">
          <?= $round2 ?>회 vs <?= $round2 + 1 ?>회
        </a>
        <a href="/lotto/<?= $round1 ?>/" class="related-link">
          <?= $round1 ?>회 상세
        </a>
        <a href="/lotto/<?= $round2 ?>/" class="related-link">
          <?= $round2 ?>회 상세
        </a>
      </div>
    </section>
  </div>
  
  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

<?php
/**
 * 번호별 통계 SEO 페이지
 * URL: /number/7/ → "로또 7번 통계", "로또 7번 출현 횟수"
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

$num = isset($_GET['num']) ? (int)$_GET['num'] : 0;

// 유효성 검사 (1~45)
if ($num < 1 || $num > 45) {
    header("HTTP/1.0 404 Not Found");
    include(G5_PATH . '/404.html');
    exit;
}

// 번호 통계 조회
$total_draws = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_draw")['cnt'];

$appear_count = sql_fetch("
    SELECT COUNT(*) AS cnt FROM g5_lotto_draw 
    WHERE n1 = {$num} OR n2 = {$num} OR n3 = {$num} 
       OR n4 = {$num} OR n5 = {$num} OR n6 = {$num}
")['cnt'];

$bonus_count = sql_fetch("
    SELECT COUNT(*) AS cnt FROM g5_lotto_draw WHERE bonus = {$num}
")['cnt'];

// 최근 출현 회차
$recent_appears = [];
$res = sql_query("
    SELECT draw_no, draw_date, n1, n2, n3, n4, n5, n6, bonus
    FROM g5_lotto_draw 
    WHERE n1 = {$num} OR n2 = {$num} OR n3 = {$num} 
       OR n4 = {$num} OR n5 = {$num} OR n6 = {$num} OR bonus = {$num}
    ORDER BY draw_no DESC 
    LIMIT 20
");
while ($row = sql_fetch_array($res)) {
    $recent_appears[] = $row;
}

// 출현 확률
$appear_rate = $total_draws > 0 ? round(($appear_count / $total_draws) * 100, 2) : 0;

// 평균 출현 간격
$avg_interval = $appear_count > 1 ? round($total_draws / $appear_count, 1) : 0;

// 공 색상
function get_ball_class($n) {
    if ($n <= 10) return 'ball-yellow';
    if ($n <= 20) return 'ball-blue';
    if ($n <= 30) return 'ball-red';
    if ($n <= 40) return 'ball-gray';
    return 'ball-green';
}

$ball_class = get_ball_class($num);

// SEO 메타
$page_title = "로또 {$num}번 통계 - 출현 횟수 {$appear_count}회";
$page_desc = "로또 {$num}번 번호 통계. 역대 {$total_draws}회 중 {$appear_count}회 출현 (출현율 {$appear_rate}%). 평균 {$avg_interval}회차마다 출현. 최근 출현 회차 및 패턴 분석.";
$canonical = "https://lottoinsight.ai/number/{$num}/";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags -->
  <title><?= $page_title ?> | 오늘로또</title>
  <meta name="description" content="<?= $page_desc ?>">
  <meta name="keywords" content="로또 <?= $num ?>번, 로또 <?= $num ?>번 통계, 로또 <?= $num ?>번 출현, 로또 번호 분석, 로또 번호별 통계">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <!-- Open Graph -->
  <meta property="og:type" content="article">
  <meta property="og:title" content="<?= $page_title ?>">
  <meta property="og:description" content="<?= $page_desc ?>">
  <meta property="og:url" content="<?= $canonical ?>">
  
  <!-- BreadcrumbList -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "홈", "item": "https://lottoinsight.ai/"},
      {"@type": "ListItem", "position": 2, "name": "번호 통계", "item": "https://lottoinsight.ai/number/"},
      {"@type": "ListItem", "position": 3, "name": "<?= $num ?>번", "item": "<?= $canonical ?>"}
    ]
  }
  </script>
  
  <!-- Dataset Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Dataset",
    "name": "로또 <?= $num ?>번 출현 통계 데이터",
    "description": "로또 <?= $num ?>번 번호의 역대 출현 횟수, 출현율, 평균 출현 간격 등 통계 데이터",
    "url": "<?= $canonical ?>",
    "keywords": ["로또", "<?= $num ?>번", "번호 통계", "출현 횟수"],
    "creator": {"@type": "Organization", "name": "오늘로또"},
    "temporalCoverage": "2002-12-07/..",
    "variableMeasured": [
      {"@type": "PropertyValue", "name": "출현 횟수", "value": "<?= $appear_count ?>"},
      {"@type": "PropertyValue", "name": "출현율", "value": "<?= $appear_rate ?>%"},
      {"@type": "PropertyValue", "name": "평균 출현 간격", "value": "<?= $avg_interval ?>회"}
    ]
  }
  </script>

  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }
    .container { max-width: 900px; margin: 0 auto; padding: 24px; }
    
    /* Navigation */
    .nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      padding: 16px 24px;
      background: rgba(5, 10, 21, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .nav-container { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
    .nav-logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .nav-logo-text { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.2rem; background: linear-gradient(135deg, #00E0A4, #00D4FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .nav-links { display: flex; gap: 8px; }
    .nav-link { padding: 10px 18px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: var(--text-secondary); text-decoration: none; font-size: 0.85rem; transition: all 0.3s; }
    .nav-link:hover { background: rgba(0, 224, 164, 0.1); border-color: var(--accent-cyan); color: var(--accent-cyan); }
    
    .main { padding-top: 80px; }
    
    /* Breadcrumb */
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 24px; flex-wrap: wrap; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb a:hover { color: var(--accent-cyan); }
    
    /* Hero */
    .hero-section {
      text-align: center;
      padding: 40px;
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      margin-bottom: 24px;
    }
    .hero-ball {
      width: 120px; height: 120px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-size: 3rem; font-weight: 900;
      color: #fff;
      margin: 0 auto 24px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .ball-yellow { background: var(--ball-yellow); }
    .ball-blue { background: var(--ball-blue); }
    .ball-red { background: var(--ball-red); }
    .ball-gray { background: var(--ball-gray); }
    .ball-green { background: var(--ball-green); }
    
    .hero-title { font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: 8px; }
    .hero-subtitle { color: var(--text-secondary); font-size: 1.1rem; }
    
    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
      padding: 24px;
      text-align: center;
    }
    .stat-value { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 900; color: var(--accent-cyan); }
    .stat-label { color: var(--text-muted); font-size: 0.9rem; margin-top: 4px; }
    
    /* History */
    .history-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 24px;
    }
    .section-header { padding: 16px 24px; background: rgba(0,0,0,0.3); font-weight: 700; font-size: 1.1rem; }
    .history-list { padding: 16px; }
    .history-item {
      display: flex; align-items: center; gap: 16px;
      padding: 12px; background: rgba(0,0,0,0.2); border-radius: 12px; margin-bottom: 8px;
    }
    .history-round { font-weight: 600; min-width: 80px; }
    .history-round a { color: inherit; text-decoration: none; }
    .history-round a:hover { color: var(--accent-cyan); }
    .history-date { color: var(--text-muted); font-size: 0.85rem; min-width: 100px; }
    .history-balls { display: flex; gap: 4px; flex-wrap: wrap; }
    .mini-ball {
      width: 32px; height: 32px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 0.8rem; font-weight: 700; color: #fff;
    }
    .mini-ball.highlight { box-shadow: 0 0 0 3px var(--accent-gold), 0 0 20px rgba(255,215,95,0.5); }
    
    /* Related Numbers */
    .related-section { margin-bottom: 24px; }
    .related-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; }
    .number-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    .number-link {
      width: 48px; height: 48px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; color: #fff; text-decoration: none;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .number-link:hover { transform: scale(1.1); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    .number-link.current { box-shadow: 0 0 0 3px var(--accent-cyan); }
    
    /* CTA */
    .cta-section { text-align: center; padding: 40px; background: linear-gradient(145deg, rgba(0,224,164,0.05), rgba(139,92,246,0.05)); border-radius: 20px; }
    .cta-title { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; margin-bottom: 12px; }
    .cta-btn {
      display: inline-flex; padding: 14px 28px;
      background: linear-gradient(135deg, #00E0A4, #00D4FF);
      border-radius: 12px; font-weight: 700; color: var(--primary-dark);
      text-decoration: none; transition: all 0.3s;
    }
    .cta-btn:hover { transform: translateY(-3px); box-shadow: 0 20px 60px rgba(0,224,164,0.3); }
    
    /* Footer */
    .footer { text-align: center; padding: 40px 24px; border-top: 1px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 0.85rem; margin-top: 40px; }
    .footer a { color: var(--text-muted); text-decoration: none; margin: 0 12px; }
    .footer a:hover { color: var(--accent-cyan); }
    
    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hero-ball { width: 80px; height: 80px; font-size: 2rem; }
      .hero-title { font-size: 1.5rem; }
      .history-item { flex-direction: column; text-align: center; }
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
      <div class="nav-links">
        <a href="/" class="nav-link">홈</a>
        <a href="/stores/" class="nav-link">명당</a>
        <a href="/draw/" class="nav-link">회차별 결과</a>
        <a href="/auth.php" class="nav-link">AI 분석</a>
      </div>
    </div>
  </nav>

  <main class="main">
    <div class="container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <a href="/">홈</a> <span>›</span>
        <a href="/number/">번호 통계</a> <span>›</span>
        <span><?= $num ?>번</span>
      </nav>

      <!-- Hero -->
      <section class="hero-section">
        <div class="hero-ball <?= $ball_class ?>"><?= $num ?></div>
        <h1 class="hero-title">로또 <?= $num ?>번 통계</h1>
        <p class="hero-subtitle">역대 <?= number_format($total_draws) ?>회 추첨 기준 분석 데이터</p>
      </section>

      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value"><?= number_format($appear_count) ?></div>
          <div class="stat-label">총 출현 횟수</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?= $appear_rate ?>%</div>
          <div class="stat-label">출현율</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?= $avg_interval ?></div>
          <div class="stat-label">평균 출현 간격 (회)</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?= number_format($bonus_count) ?></div>
          <div class="stat-label">보너스 출현</div>
        </div>
      </div>

      <!-- Recent History -->
      <section class="history-section">
        <div class="section-header">📊 최근 출현 회차 (최근 20회)</div>
        <div class="history-list">
          <?php foreach ($recent_appears as $row): 
            $nums = [$row['n1'], $row['n2'], $row['n3'], $row['n4'], $row['n5'], $row['n6']];
          ?>
          <div class="history-item">
            <div class="history-round"><a href="/lotto/<?= $row['draw_no'] ?>/"><?= number_format($row['draw_no']) ?>회</a></div>
            <div class="history-date"><?= date('Y.m.d', strtotime($row['draw_date'])) ?></div>
            <div class="history-balls">
              <?php foreach ($nums as $n): 
                $is_highlight = ($n == $num);
              ?>
              <span class="mini-ball <?= get_ball_class($n) ?> <?= $is_highlight ? 'highlight' : '' ?>"><?= $n ?></span>
              <?php endforeach; ?>
              <span style="margin: 0 4px; color: var(--text-muted);">+</span>
              <span class="mini-ball <?= get_ball_class($row['bonus']) ?> <?= $row['bonus'] == $num ? 'highlight' : '' ?>"><?= $row['bonus'] ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Related Numbers -->
      <section class="related-section">
        <h2 class="related-title">🔢 다른 번호 통계 보기</h2>
        <div class="number-grid">
          <?php for ($i = 1; $i <= 45; $i++): ?>
          <a href="/number/<?= $i ?>/" class="number-link <?= get_ball_class($i) ?> <?= $i == $num ? 'current' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      </section>

      <!-- CTA -->
      <section class="cta-section">
        <h2 class="cta-title">🎯 AI가 분석한 이번주 예상번호</h2>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">10가지 알고리즘이 분석한 최적의 번호 조합</p>
        <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작하기</a>
      </section>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

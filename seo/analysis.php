<?php
/**
 * 분석 유형별 SEO 페이지
 * URL: /analysis/홀짝/ → "로또 홀짝 분석"
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

$type = isset($_GET['type']) ? urldecode(trim($_GET['type'])) : '';

// 지원하는 분석 타입
$analysis_types = [
    '홀짝' => [
        'title' => '로또 홀짝 분석',
        'desc' => '역대 로또 당첨번호의 홀수/짝수 비율 분석. 가장 많이 나온 홀짝 조합과 통계 데이터를 확인하세요.',
        'icon' => '⚖️',
        'keywords' => '로또 홀짝, 로또 홀수 짝수, 로또 홀짝 비율, 로또 홀짝 분석'
    ],
    '고저' => [
        'title' => '로또 고저 분석',
        'desc' => '로또 당첨번호의 저번호(1-22)/고번호(23-45) 비율 분석. 균형 잡힌 번호 선택을 위한 통계.',
        'icon' => '📊',
        'keywords' => '로또 고저, 로또 저번호 고번호, 로또 고저 분석'
    ],
    '연속번호' => [
        'title' => '로또 연속번호 패턴 분석',
        'desc' => '로또 당첨번호에서 연속번호가 나오는 빈도와 패턴 분석. 연속번호 출현 확률 통계.',
        'icon' => '🔢',
        'keywords' => '로또 연속번호, 로또 연번, 로또 연속번호 확률'
    ],
    '소수' => [
        'title' => '로또 소수 분석',
        'desc' => '로또 당첨번호 중 소수(2,3,5,7,11,13,17,19,23,29,31,37,41,43)의 출현 통계.',
        'icon' => '🔬',
        'keywords' => '로또 소수, 로또 소수 번호, 로또 소수 분석'
    ],
    '합계' => [
        'title' => '로또 번호 합계 분석',
        'desc' => '6개 당첨번호 합계 분포 분석. 가장 많이 나온 합계 구간과 평균값 통계.',
        'icon' => '➕',
        'keywords' => '로또 합계, 로또 번호 합, 로또 총합 분석'
    ],
    '끝수' => [
        'title' => '로또 끝수 분석',
        'desc' => '당첨번호의 끝자리(0-9) 분포 분석. 끝수별 출현 빈도와 패턴.',
        'icon' => '🔚',
        'keywords' => '로또 끝수, 로또 끝자리, 로또 끝수 분석'
    ],
    'AC값' => [
        'title' => '로또 AC값 분석',
        'desc' => 'AC값(Arithmetic Complexity)으로 본 당첨번호 복잡도 분석. AC값별 당첨 빈도.',
        'icon' => '📈',
        'keywords' => '로또 AC값, 로또 복잡도, 로또 AC 분석'
    ],
];

if (!isset($analysis_types[$type])) {
    // 분석 인덱스 페이지
    $page_title = '로또 분석 - 패턴 분석 모음';
    $page_desc = '로또 당첨번호 분석 모음. 홀짝, 고저, 연속번호, 소수, 합계, 끝수, AC값 등 다양한 패턴 분석.';
    $show_index = true;
} else {
    $info = $analysis_types[$type];
    $page_title = $info['title'];
    $page_desc = $info['desc'];
    $show_index = false;
}

$canonical = "https://lottoinsight.ai/analysis/" . ($type ? urlencode($type) . "/" : "");

// 통계 데이터 조회 (예: 홀짝)
$stats_data = [];
if ($type === '홀짝') {
    // 홀짝 비율 통계
    $res = sql_query("SELECT n1,n2,n3,n4,n5,n6 FROM g5_lotto_draw ORDER BY draw_no DESC LIMIT 100");
    $odd_even_counts = [];
    while ($row = sql_fetch_array($res)) {
        $nums = [$row['n1'], $row['n2'], $row['n3'], $row['n4'], $row['n5'], $row['n6']];
        $odd = count(array_filter($nums, fn($n) => $n % 2 == 1));
        $key = "{$odd}:{" . (6 - $odd) . "}";
        $odd_even_counts[$key] = ($odd_even_counts[$key] ?? 0) + 1;
    }
    arsort($odd_even_counts);
    $stats_data = $odd_even_counts;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="keywords" content="<?= $show_index ? '로또 분석, 로또 패턴, 로또 통계' : htmlspecialchars($info['keywords']) ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">

  <!-- HowTo Structured Data -->
  <?php if (!$show_index): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "<?= htmlspecialchars($page_title) ?> 방법",
    "description": "<?= htmlspecialchars($page_desc) ?>",
    "step": [
      {
        "@type": "HowToStep",
        "name": "데이터 수집",
        "text": "역대 로또 당첨번호 데이터를 수집합니다."
      },
      {
        "@type": "HowToStep", 
        "name": "패턴 분석",
        "text": "<?= htmlspecialchars($type) ?> 패턴을 분석합니다."
      },
      {
        "@type": "HowToStep",
        "name": "통계 확인",
        "text": "분석 결과와 통계 데이터를 확인합니다."
      }
    ]
  }
  </script>
  <?php endif; ?>

  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
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
    
    .page-header { text-align: center; margin-bottom: 40px; }
    .page-icon { font-size: 4rem; margin-bottom: 16px; }
    .page-title { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 800; margin-bottom: 12px; }
    .page-desc { color: var(--text-secondary); max-width: 600px; margin: 0 auto; }
    
    .analysis-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
    .analysis-card {
      display: block; padding: 24px;
      background: rgba(13,24,41,0.8); border: 1px solid var(--glass-border);
      border-radius: 16px; text-decoration: none; color: inherit;
      transition: all 0.3s;
    }
    .analysis-card:hover { border-color: var(--accent-cyan); transform: translateY(-4px); }
    .analysis-card-icon { font-size: 2rem; margin-bottom: 12px; }
    .analysis-card-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 8px; }
    .analysis-card-desc { color: var(--text-muted); font-size: 0.9rem; }
    
    .stats-section { background: rgba(13,24,41,0.8); border-radius: 20px; padding: 24px; margin-top: 32px; }
    .stats-title { font-weight: 700; margin-bottom: 16px; }
    .stats-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .stats-label { min-width: 80px; font-weight: 600; }
    .stats-bar-fill { height: 24px; background: linear-gradient(90deg, var(--accent-cyan), var(--accent-purple)); border-radius: 4px; }
    .stats-value { min-width: 50px; text-align: right; color: var(--text-muted); }
    
    .cta-section { text-align: center; padding: 40px; background: linear-gradient(145deg, rgba(0,224,164,0.05), rgba(139,92,246,0.05)); border-radius: 20px; margin-top: 40px; }
    .cta-btn { display: inline-block; padding: 14px 28px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 12px; font-weight: 700; color: var(--primary-dark); text-decoration: none; }
    
    .footer { text-align: center; padding: 40px; color: var(--text-muted); font-size: 0.85rem; margin-top: 60px; }
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
        <a href="/analysis/">분석</a>
        <?php if (!$show_index): ?>
        <span>›</span> <span><?= htmlspecialchars($type) ?></span>
        <?php endif; ?>
      </nav>

      <header class="page-header">
        <div class="page-icon"><?= $show_index ? '📊' : $info['icon'] ?></div>
        <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
        <p class="page-desc"><?= htmlspecialchars($page_desc) ?></p>
      </header>

      <?php if ($show_index): ?>
      <!-- 분석 목록 -->
      <div class="analysis-grid">
        <?php foreach ($analysis_types as $key => $a): ?>
        <a href="/analysis/<?= urlencode($key) ?>/" class="analysis-card">
          <div class="analysis-card-icon"><?= $a['icon'] ?></div>
          <div class="analysis-card-title"><?= $a['title'] ?></div>
          <div class="analysis-card-desc"><?= mb_substr($a['desc'], 0, 50) ?>...</div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <!-- 개별 분석 -->
      <?php if (!empty($stats_data)): ?>
      <div class="stats-section">
        <h2 class="stats-title">📊 최근 100회 <?= htmlspecialchars($type) ?> 분포</h2>
        <?php 
        $max_val = max($stats_data);
        foreach ($stats_data as $label => $count): 
          $width = ($count / $max_val) * 100;
        ?>
        <div class="stats-bar">
          <span class="stats-label"><?= $label ?></span>
          <div style="flex: 1;">
            <div class="stats-bar-fill" style="width: <?= $width ?>%;"></div>
          </div>
          <span class="stats-value"><?= $count ?>회</span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      
      <!-- 다른 분석 링크 -->
      <div class="stats-section">
        <h2 class="stats-title">🔗 다른 분석 보기</h2>
        <div class="analysis-grid" style="margin-top: 16px;">
          <?php foreach ($analysis_types as $key => $a): if ($key === $type) continue; ?>
          <a href="/analysis/<?= urlencode($key) ?>/" class="analysis-card" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
              <span style="font-size: 1.5rem;"><?= $a['icon'] ?></span>
              <span class="analysis-card-title" style="margin: 0;"><?= $a['title'] ?></span>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <section class="cta-section">
        <h2 style="font-family: 'Outfit'; font-size: 1.4rem; font-weight: 800; margin-bottom: 12px;">🎯 AI가 분석한 이번주 번호</h2>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">모든 패턴을 종합 분석한 최적의 번호 조합</p>
        <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작</a>
      </section>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

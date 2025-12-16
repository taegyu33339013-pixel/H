<?php
/**
 * /ai/index.php - AI 번호 분석 페이지 (실제 알고리즘 적용)
 */

if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 알고리즘 클래스 로드
require_once __DIR__ . '/LottoAnalyzer.php';

// 로그인 체크
$is_logged_in = isset($member['mb_id']) && $member['mb_id'];
$member_name = $is_logged_in ? $member['mb_name'] : '';
$member_id = $is_logged_in ? $member['mb_id'] : '';

// 테스트용
if (!$is_logged_in) {
    $is_logged_in = true;
    $member_id = 'data2412';
}

$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'ai';

// ============================================
// AI 분석 실행
// ============================================
// DB 연동 시: $analyzer = new LottoAnalyzer($g5['connect_db'], 100);
$analyzer = new LottoAnalyzer(null, 100);  // 테스트용 더미 데이터

// 데이터 로드
$recommendation = $analyzer->getAIRecommendation();
$hotcold = $analyzer->getHotColdNumbers(20);
$patterns = $analyzer->getPatternStats(100);
$algorithms = $recommendation['algorithms'];

$next_round = $recommendation['next_round'];
$next_date = '2024.12.21';
$next_day = '토';
$next_time = '20:45';

// 다음 추첨까지 남은 시간 계산
$next_draw = strtotime('next Saturday 20:45');
$now = time();
$diff = $next_draw - $now;
$days_left = floor($diff / 86400);
$hours_left = floor(($diff % 86400) / 3600);

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
  <title>AI 번호 분석 | 오늘로또</title>
  <meta name="description" content="<?= count($algorithms) ?>가지 AI 알고리즘이 분석한 이번 주 최적의 로또 번호 조합. 평균 적중률 <?= $recommendation['accuracy'] ?>%">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  <style>
    :root {
      --bg-deep: #080b14; --bg-secondary: #151c2c; --bg-card: #1a2236; --bg-hover: #212b40;
      --gold: #F5B800; --gold-light: #FFD54F; --red: #FF4757; --blue: #00B4D8; --purple: #9D4EDD; --green: #00E676; --pink: #EC4899;
      --text-primary: #fff; --text-secondary: #a8b5c8; --text-muted: #6b7a90; --border: rgba(255,255,255,0.08);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Pretendard', sans-serif; background: var(--bg-deep); background-image: radial-gradient(at 50% 0%, rgba(245,184,0,0.08) 0px, transparent 50%); background-attachment: fixed; color: var(--text-primary); line-height: 1.6; }
    a { color: inherit; text-decoration: none; }
    
    .header { position: sticky; top: 0; z-index: 100; background: rgba(8,11,20,0.95); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border); }
    .header-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; height: 56px; display: flex; align-items: center; justify-content: space-between; }
    .logo { display: flex; align-items: center; gap: 10px; }
    .logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #F5B800, #FF8C00); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .logo-text { font-weight: 800; font-size: 1.2rem; background: linear-gradient(135deg, #F5B800, #FF8C00); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .header-nav { display: flex; gap: 4px; }
    .nav-link { padding: 8px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; color: var(--text-secondary); }
    .nav-link:hover { background: rgba(245,184,0,0.1); color: var(--gold); }
    .nav-link.active { background: rgba(245,184,0,0.15); color: var(--gold); }
    .header-right { display: flex; align-items: center; gap: 16px; }
    .user-info { font-size: 0.9rem; color: var(--text-secondary); }
    .user-info strong { color: var(--gold); }
    .btn-logout { padding: 8px 16px; border-radius: 8px; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; }
    
    .main { max-width: 1200px; margin: 0 auto; padding: 24px 20px 100px; }
    
    .hero { text-align: center; padding: 60px 20px; background: linear-gradient(180deg, rgba(245,184,0,0.05) 0%, transparent 100%); border-radius: 24px; margin-bottom: 32px; }
    .hero-badge { display: inline-flex; align-items: center; gap: 6px; padding: 8px 20px; border-radius: 24px; background: linear-gradient(135deg, rgba(245,184,0,0.2), rgba(255,140,0,0.2)); border: 1px solid rgba(245,184,0,0.3); font-size: 0.9rem; font-weight: 600; color: var(--gold); margin-bottom: 24px; }
    .hero-title { font-size: 2.5rem; font-weight: 800; margin-bottom: 16px; background: linear-gradient(135deg, #F5B800, #FF8C00); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .hero-subtitle { font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 32px; }
    .hero-info { display: inline-flex; background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .hero-info-item { padding: 20px 40px; text-align: center; }
    .hero-info-item + .hero-info-item { border-left: 1px solid var(--border); }
    .hero-info-label { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px; }
    .hero-info-value { font-size: 1.5rem; font-weight: 800; }
    .hero-info-value.gold { color: var(--gold); }
    
    .tabs { display: flex; gap: 8px; margin-bottom: 32px; flex-wrap: wrap; }
    .tab { display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; background: var(--bg-card); border: 1px solid var(--border); font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .tab:hover { background: var(--bg-hover); }
    .tab.active { background: linear-gradient(135deg, #F5B800, #FF8C00); color: #000; border-color: transparent; }
    .tab-badge { padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 700; background: var(--red); color: #fff; }
    
    .section { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; margin-bottom: 24px; overflow: hidden; }
    .section-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .section-title { font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .section-more { font-size: 0.85rem; color: var(--gold); }
    .section-content { padding: 24px; }
    
    .lotto-balls { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    .lotto-ball { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; box-shadow: 0 6px 20px rgba(0,0,0,0.3); transition: transform 0.3s; }
    .lotto-ball:hover { transform: scale(1.1); }
    .ball-yellow { background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #000; }
    .ball-blue { background: linear-gradient(135deg, #3B82F6, #2563EB); color: #fff; }
    .ball-red { background: linear-gradient(135deg, #EF4444, #DC2626); color: #fff; }
    .ball-gray { background: linear-gradient(135deg, #6B7280, #4B5563); color: #fff; }
    .ball-green { background: linear-gradient(135deg, #22C55E, #16A34A); color: #fff; }
    
    .ai-main-card { margin-bottom: 24px; }
    .ai-main-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
    .ai-main-icon { width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #F5B800, #FF8C00); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .ai-main-info h3 { font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }
    .ai-main-info p { font-size: 0.9rem; color: var(--text-muted); }
    .ai-main-accuracy { margin-left: auto; text-align: right; }
    .ai-main-accuracy-label { font-size: 0.8rem; color: var(--text-muted); }
    .ai-main-accuracy-value { font-size: 2rem; font-weight: 800; color: var(--gold); }
    
    .algo-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .algo-card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 16px; padding: 20px; transition: all 0.2s; cursor: pointer; }
    .algo-card:hover { background: var(--bg-hover); border-color: var(--gold); transform: translateY(-2px); }
    .algo-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .algo-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .algo-name { font-weight: 700; margin-bottom: 2px; }
    .algo-desc { font-size: 0.8rem; color: var(--text-muted); }
    .algo-numbers { display: flex; gap: 6px; margin-bottom: 12px; flex-wrap: wrap; }
    .algo-ball { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; }
    .algo-stats { display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid var(--border); }
    .algo-stat { text-align: center; }
    .algo-stat-value { font-weight: 700; font-size: 1rem; }
    .algo-stat-label { font-size: 0.7rem; color: var(--text-muted); }
    
    .hot-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
    .hot-section h4 { font-size: 1rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .hot-list { display: flex; flex-direction: column; gap: 12px; }
    .hot-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: var(--bg-secondary); border-radius: 12px; }
    .hot-rank { font-weight: 700; color: var(--text-muted); width: 24px; }
    .hot-number { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }
    .hot-count { flex: 1; }
    .hot-count-value { font-weight: 700; }
    .hot-count-label { font-size: 0.75rem; color: var(--text-muted); }
    .hot-trend { font-size: 0.9rem; font-weight: 600; }
    .hot-trend.up { color: var(--green); }
    .hot-trend.down { color: var(--red); }
    .hot-trend.same { color: var(--text-muted); }
    
    .pattern-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .pattern-card { background: var(--bg-secondary); border-radius: 16px; padding: 20px; }
    .pattern-title { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px; }
    .pattern-value { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
    .pattern-sub { font-size: 0.8rem; color: var(--text-muted); }
    .pattern-bar { height: 8px; background: var(--bg-hover); border-radius: 4px; margin-top: 12px; overflow: hidden; }
    .pattern-bar-fill { height: 100%; border-radius: 4px; }
    
    .history-table { width: 100%; border-collapse: collapse; }
    .history-table th, .history-table td { padding: 16px; text-align: center; border-bottom: 1px solid var(--border); }
    .history-table th { font-size: 0.8rem; color: var(--text-muted); background: var(--bg-secondary); }
    .history-table tr:last-child td { border-bottom: none; }
    .history-round { font-weight: 700; color: var(--purple); }
    .history-balls { display: flex; gap: 4px; justify-content: center; }
    .history-ball { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }
    
    .bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(8,11,20,0.95); backdrop-filter: blur(10px); border-top: 1px solid var(--border); z-index: 90; }
    .bottom-bar-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; height: 56px; display: flex; align-items: center; justify-content: space-between; }
    .bottom-stats { display: flex; gap: 32px; font-size: 0.85rem; }
    .bottom-stats strong { color: var(--gold); }
    .countdown { display: flex; gap: 8px; align-items: center; }
    .countdown-item { padding: 8px 16px; background: var(--bg-card); border-radius: 8px; text-align: center; }
    .countdown-value { font-size: 1.25rem; font-weight: 800; color: var(--gold); }
    .countdown-label { font-size: 0.7rem; color: var(--text-muted); }
    
    /* Probability Distribution Chart */
    .prob-chart { display: flex; gap: 4px; align-items: flex-end; height: 120px; padding: 16px 0; }
    .prob-bar { flex: 1; min-width: 16px; background: var(--bg-hover); border-radius: 4px 4px 0 0; position: relative; transition: all 0.3s; cursor: pointer; }
    .prob-bar:hover { opacity: 0.8; }
    .prob-bar-fill { position: absolute; bottom: 0; left: 0; right: 0; border-radius: 4px 4px 0 0; }
    .prob-bar-label { position: absolute; bottom: -20px; left: 50%; transform: translateX(-50%); font-size: 0.65rem; color: var(--text-muted); }
    
    @media (max-width: 768px) {
      .algo-grid { grid-template-columns: 1fr; }
      .hot-grid { grid-template-columns: 1fr; }
      .pattern-grid { grid-template-columns: repeat(2, 1fr); }
      .hero-info { flex-direction: column; }
      .hero-info-item + .hero-info-item { border-left: none; border-top: 1px solid var(--border); }
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
      <a href="/draw/" class="nav-link">회차별 결과</a>
      <a href="/ai/" class="nav-link active">AI 분석</a>
    </nav>
    <div class="header-right">
      <?php if ($is_logged_in): ?>
      <span class="user-info">안녕하세요, <strong><?= htmlspecialchars($member_id) ?></strong>님</span>
      <button class="btn-logout" onclick="location.href='/bbs/logout.php'">로그아웃</button>
      <?php else: ?>
      <a href="/bbs/login.php" class="btn-logout">로그인</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="main">
  <div class="hero">
    <div class="hero-badge">🤖 AI 분석 시스템</div>
    <h1 class="hero-title">AI 번호 추천</h1>
    <p class="hero-subtitle"><?= count($algorithms) ?>가지 알고리즘이 분석한 이번 주 최적의 번호 조합</p>
    <div class="hero-info">
      <div class="hero-info-item">
        <div class="hero-info-label">다음 회차</div>
        <div class="hero-info-value gold"><?= $next_round ?>회</div>
      </div>
      <div class="hero-info-item">
        <div class="hero-info-label">추첨일</div>
        <div class="hero-info-value"><?= $next_date ?> (<?= $next_day ?>) <?= $next_time ?></div>
      </div>
    </div>
  </div>
  
  <div class="tabs">
    <a href="/ai/" class="tab <?= $tab === 'ai' ? 'active' : '' ?>">🤖 AI 추천</a>
    <a href="/ai/?tab=hot" class="tab <?= $tab === 'hot' ? 'active' : '' ?>">🔥 핫넘버 <span class="tab-badge">NEW</span></a>
    <a href="/ai/?tab=pattern" class="tab <?= $tab === 'pattern' ? 'active' : '' ?>">📊 패턴 분석</a>
    <a href="/ai/?tab=history" class="tab <?= $tab === 'history' ? 'active' : '' ?>">📈 적중 내역</a>
  </div>
  
  <?php if ($tab === 'ai'): ?>
  <!-- AI 추천 탭 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">🤖 AI 종합 추천</h2>
      <a href="#algorithms" class="section-more">전체 알고리즘 분석 결과 →</a>
    </div>
    <div class="section-content">
      <div class="ai-main-card">
        <div class="ai-main-header">
          <div class="ai-main-icon">🤖</div>
          <div class="ai-main-info">
            <h3><?= $next_round ?>회 AI 추천번호</h3>
            <p><?= $recommendation['algorithm_count'] ?>개 알고리즘 종합 분석 (<?= number_format($recommendation['data_rounds']) ?>회차 데이터)</p>
          </div>
          <div class="ai-main-accuracy">
            <div class="ai-main-accuracy-label">평균 적중률</div>
            <div class="ai-main-accuracy-value"><?= number_format($recommendation['accuracy'], 1) ?>%</div>
          </div>
        </div>
        <div class="lotto-balls">
          <?php foreach ($recommendation['numbers'] as $n): ?>
          <div class="lotto-ball <?= get_ball_class($n) ?>"><?= $n ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 개별 알고리즘 -->
  <div class="section" id="algorithms">
    <div class="section-header">
      <h2 class="section-title">📊 개별 알고리즘 분석</h2>
      <span class="section-more"><?= count($algorithms) ?>개 알고리즘</span>
    </div>
    <div class="section-content">
      <div class="algo-grid">
        <?php foreach ($algorithms as $algo): ?>
        <div class="algo-card">
          <div class="algo-header">
            <div class="algo-icon" style="background: <?= $algo['color'] ?>20; color: <?= $algo['color'] ?>;"><?= $algo['icon'] ?></div>
            <div>
              <div class="algo-name"><?= $algo['name'] ?></div>
              <div class="algo-desc"><?= $algo['description'] ?></div>
            </div>
          </div>
          <div class="algo-numbers">
            <?php foreach ($algo['numbers'] as $n): ?>
            <div class="algo-ball <?= get_ball_class($n) ?>"><?= $n ?></div>
            <?php endforeach; ?>
          </div>
          <div class="algo-stats">
            <div class="algo-stat">
              <div class="algo-stat-value" style="color: <?= $algo['color'] ?>;"><?= number_format($algo['accuracy'], 1) ?>%</div>
              <div class="algo-stat-label">적중률</div>
            </div>
            <div class="algo-stat">
              <div class="algo-stat-value"><?= $algo['confidence'] ?>%</div>
              <div class="algo-stat-label">신뢰도</div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  
  <?php elseif ($tab === 'hot'): ?>
  <!-- 핫넘버 탭 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">🔥 핫넘버 & 콜드넘버</h2>
      <span class="section-more">최근 <?= $hotcold['range'] ?>회차 기준</span>
    </div>
    <div class="section-content">
      <div class="hot-grid">
        <div class="hot-section">
          <h4>🔥 HOT 번호 (자주 출현)</h4>
          <div class="hot-list">
            <?php foreach (array_slice($hotcold['hot'], 0, 8) as $i => $h): ?>
            <div class="hot-item">
              <div class="hot-rank"><?= $i + 1 ?></div>
              <div class="hot-number <?= get_ball_class($h['number']) ?>"><?= $h['number'] ?></div>
              <div class="hot-count">
                <div class="hot-count-value"><?= $h['count'] ?>회</div>
                <div class="hot-count-label">출현</div>
              </div>
              <div class="hot-trend <?= $h['trend'] ?>">
                <?php if ($h['trend'] === 'up'): ?>▲ +<?= $h['change'] ?>
                <?php elseif ($h['trend'] === 'down'): ?>▼ <?= abs($h['change']) ?>
                <?php else: ?>- 동일<?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div class="hot-section">
          <h4>❄️ COLD 번호 (미출현)</h4>
          <div class="hot-list">
            <?php foreach (array_slice($hotcold['cold'], 0, 8) as $i => $c): ?>
            <div class="hot-item">
              <div class="hot-rank"><?= $i + 1 ?></div>
              <div class="hot-number <?= get_ball_class($c['number']) ?>"><?= $c['number'] ?></div>
              <div class="hot-count">
                <div class="hot-count-value"><?= $c['count'] ?>회</div>
                <div class="hot-count-label">출현</div>
              </div>
              <div class="hot-trend" style="color: var(--blue);">
                <?= $c['gap'] ?>회 미출현
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 번호별 출현 확률 차트 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">📊 번호별 출현 빈도</h2>
    </div>
    <div class="section-content">
      <div class="prob-chart">
        <?php 
        $freq_result = $analyzer->analyzeFrequency(50);
        $max_freq = max($freq_result['frequency']);
        for ($n = 1; $n <= 45; $n++): 
          $freq = $freq_result['frequency'][$n];
          $height = ($freq / $max_freq) * 100;
          $color = match(true) {
            $n <= 10 => '#F59E0B',
            $n <= 20 => '#3B82F6',
            $n <= 30 => '#EF4444',
            $n <= 40 => '#6B7280',
            default => '#22C55E'
          };
        ?>
        <div class="prob-bar" style="height: 100%;" title="<?= $n ?>번: <?= $freq ?>회">
          <div class="prob-bar-fill" style="height: <?= $height ?>%; background: <?= $color ?>;"></div>
          <?php if ($n % 5 === 0 || $n === 1): ?>
          <span class="prob-bar-label"><?= $n ?></span>
          <?php endif; ?>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
  
  <?php elseif ($tab === 'pattern'): ?>
  <!-- 패턴 분석 탭 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">📊 패턴 분석</h2>
      <span class="section-more"><?= number_format($recommendation['data_rounds']) ?>회차 데이터 기반</span>
    </div>
    <div class="section-content">
      <div class="pattern-grid">
        <div class="pattern-card">
          <div class="pattern-title">번호 합계 구간</div>
          <div class="pattern-value" style="color: var(--gold);"><?= $patterns['sum_range']['optimal'][0] ?>~<?= $patterns['sum_range']['optimal'][1] ?></div>
          <div class="pattern-sub">평균: <?= $patterns['sum_range']['average'] ?></div>
          <div class="pattern-bar"><div class="pattern-bar-fill" style="width: 75%; background: linear-gradient(90deg, var(--gold), var(--gold-light));"></div></div>
        </div>
        
        <div class="pattern-card">
          <div class="pattern-title">홀짝 비율</div>
          <div class="pattern-value" style="color: var(--purple);"><?= $patterns['odd_even']['optimal'] ?>:<?= 6 - $patterns['odd_even']['optimal'] ?></div>
          <div class="pattern-sub">확률: <?= $patterns['odd_even']['optimal_probability'] ?>%</div>
          <div class="pattern-bar"><div class="pattern-bar-fill" style="width: <?= $patterns['odd_even']['optimal_probability'] ?>%; background: linear-gradient(90deg, var(--purple), var(--pink));"></div></div>
        </div>
        
        <div class="pattern-card">
          <div class="pattern-title">고저 비율 (1-22:23-45)</div>
          <div class="pattern-value" style="color: var(--blue);"><?= $patterns['high_low']['optimal'] ?>:<?= 6 - $patterns['high_low']['optimal'] ?></div>
          <div class="pattern-sub">확률: <?= $patterns['high_low']['optimal_probability'] ?>%</div>
          <div class="pattern-bar"><div class="pattern-bar-fill" style="width: <?= $patterns['high_low']['optimal_probability'] ?>%; background: linear-gradient(90deg, var(--blue), var(--purple));"></div></div>
        </div>
        
        <div class="pattern-card">
          <div class="pattern-title">연속번호 포함</div>
          <div class="pattern-value" style="color: var(--green);"><?= $patterns['consecutive']['optimal'] ?>쌍</div>
          <div class="pattern-sub">확률: <?= $patterns['consecutive']['optimal_probability'] ?>%</div>
          <div class="pattern-bar"><div class="pattern-bar-fill" style="width: <?= $patterns['consecutive']['optimal_probability'] ?>%; background: linear-gradient(90deg, var(--green), var(--blue));"></div></div>
        </div>
        
        <div class="pattern-card">
          <div class="pattern-title">같은 10단위</div>
          <div class="pattern-value" style="color: var(--pink);"><?= $patterns['same_decade']['optimal'] ?>개</div>
          <div class="pattern-sub">확률: <?= $patterns['same_decade']['optimal_probability'] ?>%</div>
          <div class="pattern-bar"><div class="pattern-bar-fill" style="width: <?= $patterns['same_decade']['optimal_probability'] ?>%; background: linear-gradient(90deg, var(--pink), var(--red));"></div></div>
        </div>
        
        <div class="pattern-card">
          <div class="pattern-title">AC값 (조합복잡도)</div>
          <div class="pattern-value" style="color: var(--gold);"><?= $patterns['ac_value']['optimal'] ?></div>
          <div class="pattern-sub">평균: <?= $patterns['ac_value']['average'] ?></div>
          <div class="pattern-bar"><div class="pattern-bar-fill" style="width: 85%; background: linear-gradient(90deg, var(--gold), var(--gold-light));"></div></div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 홀짝 분포 상세 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">⚖️ 홀짝 비율 분포</h2>
    </div>
    <div class="section-content">
      <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        <?php for ($i = 0; $i <= 6; $i++): 
          $data = $patterns['odd_even'][$i] ?? ['count' => 0, 'probability' => 0];
          $is_optimal = ($i === $patterns['odd_even']['optimal']);
        ?>
        <div style="flex: 1; min-width: 100px; background: <?= $is_optimal ? 'linear-gradient(135deg, var(--purple), var(--pink))' : 'var(--bg-secondary)' ?>; border-radius: 12px; padding: 16px; text-align: center;">
          <div style="font-size: 1.5rem; font-weight: 800; <?= $is_optimal ? 'color: #fff;' : '' ?>"><?= $i ?>:<?= 6-$i ?></div>
          <div style="font-size: 0.85rem; <?= $is_optimal ? 'color: rgba(255,255,255,0.8);' : 'color: var(--text-muted);' ?>">홀:짝</div>
          <div style="font-size: 1.1rem; font-weight: 700; margin-top: 8px; <?= $is_optimal ? 'color: #fff;' : 'color: var(--gold);' ?>"><?= $data['probability'] ?>%</div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
  
  <?php elseif ($tab === 'history'): ?>
  <!-- 적중 내역 탭 (테스트용 더미 데이터) -->
  <?php
  $hit_history = [
      ['round' => 1148, 'predicted' => [3, 12, 18, 27, 35, 42], 'actual' => [3, 12, 18, 27, 35, 42], 'hits' => 4, 'prize' => 4],
      ['round' => 1147, 'predicted' => [5, 11, 16, 28, 34, 43], 'actual' => [5, 11, 16, 28, 34, 43], 'hits' => 3, 'prize' => 5],
      ['round' => 1146, 'predicted' => [2, 9, 17, 25, 38, 44], 'actual' => [2, 9, 17, 25, 38, 44], 'hits' => 5, 'prize' => 3],
      ['round' => 1145, 'predicted' => [7, 14, 21, 29, 36, 41], 'actual' => [7, 14, 21, 29, 36, 41], 'hits' => 4, 'prize' => 4],
      ['round' => 1144, 'predicted' => [1, 8, 19, 26, 33, 45], 'actual' => [1, 8, 19, 26, 33, 45], 'hits' => 3, 'prize' => 5],
  ];
  ?>
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">📈 AI 적중 내역</h2>
      <span class="section-more">최근 50회차</span>
    </div>
    <div class="section-content" style="overflow-x: auto;">
      <table class="history-table">
        <thead><tr><th>회차</th><th>AI 예측</th><th>실제 당첨</th><th>적중</th><th>등위</th></tr></thead>
        <tbody>
          <?php foreach ($hit_history as $h): ?>
          <tr>
            <td class="history-round"><?= $h['round'] ?>회</td>
            <td><div class="history-balls"><?php foreach ($h['predicted'] as $n): ?><div class="history-ball <?= get_ball_class($n) ?>"><?= $n ?></div><?php endforeach; ?></div></td>
            <td><div class="history-balls"><?php foreach ($h['actual'] as $n): ?><div class="history-ball <?= get_ball_class($n) ?>"><?= $n ?></div><?php endforeach; ?></div></td>
            <td style="font-weight: 700; color: <?= $h['hits'] >= 5 ? 'var(--gold)' : ($h['hits'] >= 4 ? 'var(--blue)' : 'var(--text-muted)') ?>;"><?= $h['hits'] ?>개</td>
            <td><span style="padding: 4px 12px; border-radius: 8px; font-weight: 700; background: <?= $h['prize'] <= 3 ? 'linear-gradient(135deg, #F5B800, #FF8C00)' : 'var(--bg-hover)' ?>; color: <?= $h['prize'] <= 3 ? '#000' : 'var(--text-muted)' ?>;"><?= $h['prize'] ?>등</span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <!-- 통계 요약 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">📊 적중 통계 요약</h2>
    </div>
    <div class="section-content">
      <div class="pattern-grid" style="grid-template-columns: repeat(4, 1fr);">
        <div class="pattern-card"><div class="pattern-title">평균 적중 개수</div><div class="pattern-value" style="color: var(--gold);">3.8개</div><div class="pattern-sub">50회차 평균</div></div>
        <div class="pattern-card"><div class="pattern-title">5개 이상 적중</div><div class="pattern-value" style="color: var(--green);">8회</div><div class="pattern-sub">16%</div></div>
        <div class="pattern-card"><div class="pattern-title">4등 (4개)</div><div class="pattern-value" style="color: var(--blue);">15회</div><div class="pattern-sub">30%</div></div>
        <div class="pattern-card"><div class="pattern-title">5등 (3개)</div><div class="pattern-value" style="color: var(--purple);">22회</div><div class="pattern-sub">44%</div></div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</main>

<div class="bottom-bar">
  <div class="bottom-bar-inner">
    <div class="bottom-stats">
      <span>📊 AI 분석 <strong><?= count($algorithms) ?>종</strong></span>
      <span>평균 적중률 <strong><?= number_format($recommendation['accuracy'], 1) ?>%</strong></span>
      <span>분석 데이터 <strong><?= number_format($recommendation['data_rounds']) ?>회차</strong></span>
      <span>다음 추첨 <strong style="color: var(--gold);"><?= $days_left ?>일 <?= $hours_left ?>시간</strong></span>
    </div>
    <div class="countdown">
      <div class="countdown-item"><div class="countdown-value"><?= $days_left ?></div><div class="countdown-label">일</div></div>
      <div class="countdown-item"><div class="countdown-value"><?= $hours_left ?></div><div class="countdown-label">시간</div></div>
    </div>
  </div>
</div>
</body>
</html>
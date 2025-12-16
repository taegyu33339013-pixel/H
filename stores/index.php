<?php
/**
 * /stores/index.php - 로또 판매점 (UX 개선 버전)
 * 
 * ✅ 1. 스켈레톤 로딩
 * ✅ 2. 모바일 최적화
 * ✅ 3. 마이크로 인터랙션
 * ✅ 4. 온보딩
 */

if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'default';
$region1 = isset($_GET['region']) ? trim($_GET['region']) : '';
$regions = ['서울', '부산', '대구', '인천', '광주', '대전', '울산', '세종', '경기', '강원', '충북', '충남', '전북', '전남', '경북', '경남', '제주'];
$latest_round = 1148;

$is_hot = ($tab === 'hot');
$is_predict = ($tab === 'predict');
$is_default = (!$is_hot && !$is_predict);

// 더미 데이터
$all_time_stores = [
    ['store_id' => 101, 'store_name' => '노다지복권방', 'region1' => '서울', 'address' => '서울 노원구 동일로 1234', 'wins_1st' => 15, 'wins_2nd' => 42, 'last_win' => 1089, 'years' => 12],
    ['store_id' => 102, 'store_name' => '황금알복권', 'region1' => '서울', 'address' => '서울 강남구 테헤란로 567', 'wins_1st' => 12, 'wins_2nd' => 38, 'last_win' => 1102, 'years' => 15],
    ['store_id' => 103, 'store_name' => '명품당', 'region1' => '경기', 'address' => '경기 부천시 길주로 890', 'wins_1st' => 11, 'wins_2nd' => 35, 'last_win' => 1095, 'years' => 18],
    ['store_id' => 104, 'store_name' => '돈방석복권', 'region1' => '서울', 'address' => '서울 송파구 올림픽로 234', 'wins_1st' => 10, 'wins_2nd' => 31, 'last_win' => 1078, 'years' => 14],
    ['store_id' => 105, 'store_name' => '전설의복권', 'region1' => '부산', 'address' => '부산 해운대구 해운대로 456', 'wins_1st' => 9, 'wins_2nd' => 28, 'last_win' => 1112, 'years' => 16],
];

$hot_stores = [
    ['store_id' => 201, 'store_name' => '대박로또', 'region1' => '서울', 'address' => '서울 강남구 역삼로 123', 'wins_1st' => 3, 'wins_2nd' => 8, 'last_win_round' => 1148, 'trend' => 15.2],
    ['store_id' => 202, 'store_name' => '신흥복권', 'region1' => '경기', 'address' => '경기 화성시 동탄대로 456', 'wins_1st' => 2, 'wins_2nd' => 5, 'last_win_round' => 1148, 'trend' => 12.8],
    ['store_id' => 203, 'store_name' => '떡상복권방', 'region1' => '부산', 'address' => '부산 동래구 충렬대로 789', 'wins_1st' => 1, 'wins_2nd' => 3, 'last_win_round' => 1147, 'trend' => 9.5],
];

$predicted_stores = [
    ['store_id' => 301, 'store_name' => '로또킹', 'region1' => '서울', 'address' => '서울 마포구 홍대입구역 2번출구', 'wins_1st' => 4, 'wins_2nd' => 12, 'last_win' => 1098, 'ai_score' => 94.2],
    ['store_id' => 302, 'store_name' => '대운복권', 'region1' => '경기', 'address' => '경기 수원시 팔달구 인계로 123', 'wins_1st' => 6, 'wins_2nd' => 18, 'last_win' => 1086, 'ai_score' => 91.8],
    ['store_id' => 303, 'store_name' => '행운의집', 'region1' => '부산', 'address' => '부산 부산진구 서면로 456', 'wins_1st' => 5, 'wins_2nd' => 15, 'last_win' => 1091, 'ai_score' => 89.5],
    ['store_id' => 304, 'store_name' => '복권천국', 'region1' => '대구', 'address' => '대구 중구 동성로 789', 'wins_1st' => 3, 'wins_2nd' => 9, 'last_win' => 1094, 'ai_score' => 87.3],
    ['store_id' => 305, 'store_name' => '황금마차', 'region1' => '인천', 'address' => '인천 남동구 구월로 234', 'wins_1st' => 4, 'wins_2nd' => 11, 'last_win' => 1088, 'ai_score' => 85.1],
];

if ($is_hot) {
    $stores = $hot_stores;
} elseif ($is_predict) {
    $stores = $predicted_stores;
} else {
    $stores = $all_time_stores;
}

$total_count = count($stores);
$hot_count = count($hot_stores);
$predict_count = count($predicted_stores);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="theme-color" content="#080b14">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <title><?= $is_predict ? 'AI 예측 명당' : ($is_hot ? 'HOT 판매점' : '로또 명당') ?> | 오늘로또</title>
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  <style>
    :root {
      --bg-deep: #080b14; --bg-secondary: #151c2c; --bg-card: #1a2236; --bg-hover: #212b40;
      --gold: #F5B800; --red: #FF4757; --blue: #00B4D8; --purple: #9D4EDD; --green: #22C55E; --cyan: #06B6D4;
      --text-primary: #fff; --text-secondary: #a8b5c8; --text-muted: #6b7a90; --border: rgba(255,255,255,0.08);
      <?php if ($is_hot): ?>
      --theme-color: var(--red); --theme-gradient: linear-gradient(135deg, #FF4757, #FF8C00);
      <?php elseif ($is_predict): ?>
      --theme-color: var(--cyan); --theme-gradient: linear-gradient(135deg, #06B6D4, #9D4EDD);
      <?php else: ?>
      --theme-color: var(--gold); --theme-gradient: linear-gradient(135deg, #F5B800, #FF8C00);
      <?php endif; ?>
      --safe-bottom: env(safe-area-inset-bottom, 0px);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    html { scroll-behavior: smooth; }
    body { 
      font-family: 'Pretendard', -apple-system, sans-serif; 
      background: var(--bg-deep); 
      color: var(--text-primary); 
      line-height: 1.6;
      overflow-x: hidden;
      padding-bottom: calc(70px + var(--safe-bottom));
    }
    a { color: inherit; text-decoration: none; }
    button { font-family: inherit; }
    
    /* ================================
       헤더 - 모바일 최적화
       ================================ */
    .header { 
      position: sticky; top: 0; z-index: 100; 
      background: rgba(8,11,20,0.95); 
      backdrop-filter: blur(20px); 
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border); 
    }
    .header-inner { 
      max-width: 1200px; margin: 0 auto; padding: 0 16px; 
      height: 56px; display: flex; align-items: center; justify-content: space-between; 
    }
    .logo { display: flex; align-items: center; gap: 10px; }
    .logo-icon { 
      width: 36px; height: 36px; 
      background: linear-gradient(135deg, #F5B800, #FF8C00); 
      border-radius: 10px; 
      display: flex; align-items: center; justify-content: center; 
    }
    .logo-text { 
      font-weight: 800; font-size: 1.2rem; 
      background: linear-gradient(135deg, #F5B800, #FF8C00); 
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; 
    }
    .header-actions { display: flex; gap: 8px; }
    .header-btn {
      width: 40px; height: 40px; border-radius: 12px;
      background: var(--bg-secondary); border: none;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem; cursor: pointer;
      transition: all 0.2s;
    }
    .header-btn:active { transform: scale(0.95); }
    
    .main { max-width: 1200px; margin: 0 auto; padding: 16px; }
    
    /* ================================
       탭 - 모바일 스크롤
       ================================ */
    .tabs { 
      display: flex; gap: 8px; 
      overflow-x: auto; 
      scrollbar-width: none;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .tabs::-webkit-scrollbar { display: none; }
    .tab { 
      flex-shrink: 0;
      padding: 10px 16px; 
      border-radius: 20px;
      background: var(--bg-card);
      border: 1px solid var(--border);
      font-weight: 600; 
      font-size: 0.9rem;
      color: var(--text-muted);
      display: flex; align-items: center; gap: 6px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .tab:active { transform: scale(0.97); }
    .tab.active { 
      background: var(--theme-gradient); 
      color: #fff; 
      border: none;
      box-shadow: 0 4px 15px rgba(6,182,212,0.3);
    }
    .tab-badge { 
      padding: 2px 6px; border-radius: 8px; 
      font-size: 0.65rem; font-weight: 700; 
      background: rgba(255,255,255,0.2);
    }
    
    /* ================================
       배너 - 터치 친화적
       ================================ */
    .info-banner { 
      padding: 20px; border-radius: 20px; margin-bottom: 20px; 
      background: linear-gradient(145deg, rgba(255,255,255,0.03), transparent); 
      border: 1px solid rgba(6,182,212,0.3);
      display: flex; align-items: center; gap: 16px;
    }
    .info-banner-icon { 
      width: 52px; height: 52px; min-width: 52px;
      border-radius: 16px; 
      background: var(--theme-gradient); 
      display: flex; align-items: center; justify-content: center; 
      font-size: 1.4rem; 
    }
    .info-banner h2 { font-size: 1.1rem; font-weight: 800; margin-bottom: 2px; }
    .info-banner p { font-size: 0.85rem; color: var(--text-secondary); }
    
    /* ================================
       지역 필터 - 스크롤 가능
       ================================ */
    .filters { 
      display: flex; gap: 8px; 
      overflow-x: auto;
      scrollbar-width: none;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 8px;
      margin-bottom: 16px;
    }
    .filters::-webkit-scrollbar { display: none; }
    .filter-btn { 
      flex-shrink: 0;
      padding: 8px 14px; 
      border-radius: 16px; 
      background: var(--bg-secondary); 
      color: var(--text-secondary); 
      border: 1px solid var(--border); 
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s; 
    }
    .filter-btn:active { transform: scale(0.95); }
    .filter-btn.active { 
      background: var(--theme-gradient); 
      color: #fff; 
      border: none; 
    }
    
    .result-count { 
      display: flex; justify-content: space-between; 
      margin-bottom: 12px; 
      font-size: 0.85rem;
      color: var(--text-muted); 
    }
    .result-count strong { color: var(--theme-color); }
    .live-indicator { display: flex; align-items: center; gap: 6px; }
    .live-dot { 
      width: 6px; height: 6px; 
      background: var(--green); 
      border-radius: 50%; 
      animation: pulse 2s infinite; 
    }
    
    /* ================================
       스켈레톤 로딩
       ================================ */
    .skeleton { 
      background: linear-gradient(90deg, #2a3548 25%, #3a4558 50%, #2a3548 75%);
      background-size: 200% 100%;
      animation: shimmer 1.5s infinite;
      border-radius: 8px;
    }
    @keyframes shimmer {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }
    .skeleton-row {
      display: flex; align-items: center; gap: 12px;
      padding: 16px; 
      border-bottom: 1px solid var(--border);
    }
    .skeleton-rank { width: 24px; height: 24px; }
    .skeleton-icon { width: 48px; height: 48px; border-radius: 12px; }
    .skeleton-text { flex: 1; }
    .skeleton-title { width: 50%; height: 18px; margin-bottom: 8px; }
    .skeleton-sub { width: 70%; height: 14px; }
    .skeleton-score { width: 50px; height: 32px; }
    
    /* ================================
       판매점 리스트 - 모바일 최적화
       ================================ */
    .store-list { 
      background: var(--bg-card); 
      border: 1px solid var(--border); 
      border-radius: 20px; 
      overflow: hidden; 
    }
    .store-row { 
      display: flex; align-items: center; gap: 12px;
      padding: 16px; 
      border-bottom: 1px solid var(--border); 
      cursor: pointer;
      transition: all 0.2s;
      -webkit-user-select: none;
      user-select: none;
    }
    .store-row:active { background: var(--bg-hover); }
    .store-row:last-child { border-bottom: none; }
    .store-row.fade-in { animation: fadeIn 0.4s ease-out; }
    
    .store-rank { 
      width: 24px;
      font-weight: 700; font-size: 1rem; 
      color: var(--text-muted); 
      text-align: center;
    }
    .store-rank.top { color: var(--theme-color); }
    .store-image { 
      width: 48px; height: 48px; min-width: 48px;
      border-radius: 12px; 
      background: var(--bg-secondary); 
      display: flex; align-items: center; justify-content: center; 
      font-size: 1.3rem;
      transition: transform 0.2s;
    }
    .store-row:active .store-image { transform: scale(0.95); }
    .store-image.themed { background: var(--theme-gradient); }
    .store-info { flex: 1; min-width: 0; }
    .store-name-row { 
      display: flex; align-items: center; gap: 6px; 
      margin-bottom: 4px; 
      flex-wrap: wrap; 
    }
    .store-name { font-weight: 600; font-size: 0.95rem; }
    .region-badge { 
      padding: 2px 6px; 
      background: rgba(6,182,212,0.2); 
      border-radius: 4px; 
      font-size: 0.7rem; 
      color: var(--cyan); 
    }
    .store-address { 
      font-size: 0.8rem; color: var(--text-muted); 
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap; 
    }
    .store-score { 
      display: flex; flex-direction: column; align-items: flex-end;
      gap: 2px;
    }
    .score-value { 
      font-size: 1.1rem; font-weight: 800; 
      padding: 4px 10px;
      border-radius: 8px;
    }
    .score-label { font-size: 0.65rem; color: var(--text-muted); }
    
    /* 마이크로 인터랙션 - 좋아요 버튼 */
    .favorite-btn {
      width: 40px; height: 40px;
      border-radius: 12px;
      background: var(--bg-secondary);
      border: none;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .favorite-btn:active { transform: scale(0.9); }
    .favorite-btn.active { 
      background: rgba(255,71,87,0.2);
      animation: heartBeat 0.5s;
    }
    @keyframes heartBeat {
      0%, 100% { transform: scale(1); }
      25% { transform: scale(1.2); }
      50% { transform: scale(1); }
      75% { transform: scale(1.2); }
    }
    
    /* ================================
       하단 네비게이션 - 모바일 필수
       ================================ */
    .bottom-nav { 
      position: fixed; bottom: 0; left: 0; right: 0; 
      height: calc(70px + var(--safe-bottom));
      padding-bottom: var(--safe-bottom);
      background: rgba(8,11,20,0.98); 
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-top: 1px solid var(--border); 
      z-index: 100;
      display: flex;
      justify-content: space-around;
      align-items: flex-start;
      padding-top: 8px;
    }
    .nav-item { 
      display: flex; flex-direction: column; align-items: center; gap: 4px;
      padding: 8px 16px;
      background: none; border: none;
      cursor: pointer;
      transition: all 0.2s;
    }
    .nav-item:active { transform: scale(0.95); }
    .nav-icon { font-size: 1.4rem; }
    .nav-label { 
      font-size: 0.65rem; font-weight: 500;
      color: var(--text-muted);
    }
    .nav-item.active .nav-label { color: var(--theme-color); font-weight: 700; }
    .nav-item.active .nav-icon { transform: scale(1.1); }
    
    /* ================================
       온보딩 모달
       ================================ */
    .onboarding-overlay {
      position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.95);
      z-index: 1000;
      display: flex; align-items: center; justify-content: center;
      padding: 20px;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
    }
    .onboarding-overlay.show { opacity: 1; visibility: visible; }
    .onboarding-card {
      background: var(--bg-card);
      border-radius: 28px;
      padding: 40px 32px;
      max-width: 360px;
      width: 100%;
      text-align: center;
      transform: scale(0.9);
      transition: transform 0.3s;
    }
    .onboarding-overlay.show .onboarding-card { transform: scale(1); }
    .onboarding-icon { font-size: 4rem; margin-bottom: 24px; animation: bounce 1s infinite; }
    .onboarding-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 12px; }
    .onboarding-desc { color: var(--text-secondary); margin-bottom: 32px; line-height: 1.6; }
    .onboarding-dots { display: flex; justify-content: center; gap: 8px; margin-bottom: 24px; }
    .onboarding-dot {
      width: 8px; height: 8px; border-radius: 4px;
      background: var(--bg-secondary);
      transition: all 0.3s;
    }
    .onboarding-dot.active { width: 24px; background: var(--theme-gradient); }
    .onboarding-btns { display: flex; gap: 12px; }
    .onboarding-btn {
      flex: 1; padding: 14px;
      border-radius: 14px;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.2s;
    }
    .onboarding-btn:active { transform: scale(0.97); }
    .onboarding-btn.secondary {
      background: transparent;
      border: 1px solid var(--border);
      color: var(--text-secondary);
    }
    .onboarding-btn.primary {
      background: var(--theme-gradient);
      border: none;
      color: #fff;
    }
    
    /* ================================
       토스트 메시지
       ================================ */
    .toast {
      position: fixed;
      bottom: 90px;
      left: 50%;
      transform: translateX(-50%) translateY(20px);
      padding: 12px 24px;
      background: rgba(34,197,94,0.95);
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.9rem;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
      z-index: 200;
      white-space: nowrap;
    }
    .toast.show {
      opacity: 1;
      visibility: visible;
      transform: translateX(-50%) translateY(0);
    }
    
    /* ================================
       애니메이션
       ================================ */
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    /* ================================
       반응형 - 태블릿/데스크톱
       ================================ */
    @media (min-width: 768px) {
      .main { padding: 24px; }
      .store-row { padding: 20px; }
      .store-image { width: 56px; height: 56px; }
      .info-banner { padding: 24px; }
      .bottom-nav { display: none; }
      body { padding-bottom: 0; }
      
      /* 데스크톱 호버 효과 */
      .store-row:hover { background: var(--bg-hover); }
      .filter-btn:hover { background: var(--bg-hover); }
      .tab:hover { background: var(--bg-hover); }
    }
  </style>
</head>
<body>

<!-- 헤더 -->
<header class="header">
  <div class="header-inner">
    <a href="/" class="logo">
      <div class="logo-icon">🎰</div>
      <span class="logo-text">오늘로또</span>
    </a>
    <div class="header-actions">
      <button class="header-btn" onclick="showOnboarding()" title="도움말">❓</button>
      <button class="header-btn" onclick="toggleNotification()" title="알림">🔔</button>
    </div>
  </div>
</header>

<main class="main">
  <!-- 탭 - 스크롤 가능 -->
  <div class="tabs">
    <a href="/stores/" class="tab <?= $is_default ? 'active' : '' ?>">🏪 전체 명당</a>
    <a href="/stores/?tab=hot" class="tab <?= $is_hot ? 'active' : '' ?>">🔥 HOT <span class="tab-badge"><?= $hot_count ?></span></a>
    <a href="/stores/?tab=predict" class="tab <?= $is_predict ? 'active' : '' ?>">🎯 AI 예측 <span class="tab-badge">NEW</span></a>
    <a href="/draw/" class="tab">🎱 회차별</a>
    <a href="/ai/" class="tab">🤖 AI 번호</a>
  </div>
  
  <!-- 배너 -->
  <div class="info-banner">
    <div class="info-banner-icon"><?= $is_predict ? '🎯' : ($is_hot ? '🔥' : '🏆') ?></div>
    <div>
      <h2><?= $is_predict ? 'AI 예측 명당' : ($is_hot ? 'HOT 판매점' : '역대 명당 TOP') ?></h2>
      <p><?= $is_predict ? 'AI가 분석한 다음 당첨 예상 판매점' : ($is_hot ? '최근 30일 내 당첨된 판매점' : '누적 1등 당첨 횟수 기준') ?></p>
    </div>
  </div>
  
  <!-- 지역 필터 -->
  <div class="filters">
    <button class="filter-btn <?= !$region1 ? 'active' : '' ?>" onclick="filterRegion('')">전체</button>
    <?php foreach ($regions as $r): ?>
    <button class="filter-btn <?= $region1 === $r ? 'active' : '' ?>" onclick="filterRegion('<?= $r ?>')"><?= $r ?></button>
    <?php endforeach; ?>
  </div>
  
  <div class="result-count">
    <span>총 <strong><?= $total_count ?></strong>개 판매점</span>
    <div class="live-indicator"><div class="live-dot"></div><span>실시간</span></div>
  </div>
  
  <!-- 판매점 리스트 -->
  <div class="store-list" id="storeList">
    <!-- 스켈레톤 로딩 -->
    <div id="skeletonLoader">
      <?php for ($i = 0; $i < 5; $i++): ?>
      <div class="skeleton-row">
        <div class="skeleton skeleton-rank"></div>
        <div class="skeleton skeleton-icon"></div>
        <div class="skeleton-text">
          <div class="skeleton skeleton-title"></div>
          <div class="skeleton skeleton-sub"></div>
        </div>
        <div class="skeleton skeleton-score"></div>
      </div>
      <?php endfor; ?>
    </div>
    
    <!-- 실제 콘텐츠 -->
    <div id="storeContent" style="display: none;">
      <?php foreach ($stores as $i => $s): 
        $rank = $i + 1;
        $gap = isset($s['last_win']) ? $latest_round - $s['last_win'] : 0;
      ?>
      <div class="store-row fade-in" onclick="goToStore(<?= $s['store_id'] ?>)" style="animation-delay: <?= $i * 0.05 ?>s;">
        <div class="store-rank <?= $rank <= 3 ? 'top' : '' ?>"><?= $rank ?></div>
        <div class="store-image <?= $rank <= 3 ? 'themed' : '' ?>">
          <?= $is_predict ? '🎯' : ($is_hot ? '🔥' : ($rank <= 3 ? '🏆' : '🏪')) ?>
        </div>
        <div class="store-info">
          <div class="store-name-row">
            <span class="region-badge"><?= htmlspecialchars($s['region1']) ?></span>
            <span class="store-name"><?= htmlspecialchars($s['store_name']) ?></span>
          </div>
          <div class="store-address"><?= htmlspecialchars($s['address']) ?></div>
        </div>
        <div class="store-score">
          <?php if ($is_predict): ?>
          <span class="score-value" style="background: <?= $s['ai_score'] >= 90 ? 'rgba(34,197,94,0.2)' : 'rgba(6,182,212,0.2)' ?>; color: <?= $s['ai_score'] >= 90 ? 'var(--green)' : 'var(--cyan)' ?>;"><?= $s['ai_score'] ?></span>
          <span class="score-label">AI점수</span>
          <?php elseif ($is_hot): ?>
          <span class="score-value" style="background: rgba(255,71,87,0.2); color: var(--red);">+<?= $s['trend'] ?>%</span>
          <span class="score-label">인기도</span>
          <?php else: ?>
          <span class="score-value" style="background: rgba(245,184,0,0.2); color: var(--gold);"><?= $s['wins_1st'] ?></span>
          <span class="score-label">1등</span>
          <?php endif; ?>
        </div>
        <button class="favorite-btn" onclick="toggleFavorite(event, <?= $s['store_id'] ?>)" data-id="<?= $s['store_id'] ?>">🤍</button>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<!-- 하단 네비게이션 -->
<nav class="bottom-nav">
  <button class="nav-item active" onclick="location.href='/'">
    <span class="nav-icon">🏠</span>
    <span class="nav-label">홈</span>
  </button>
  <button class="nav-item" onclick="location.href='/stores/?tab=predict'">
    <span class="nav-icon">🎯</span>
    <span class="nav-label">AI예측</span>
  </button>
  <button class="nav-item" onclick="location.href='/stores/'">
    <span class="nav-icon">🏪</span>
    <span class="nav-label">명당</span>
  </button>
  <button class="nav-item" onclick="location.href='/ai/'">
    <span class="nav-icon">🔢</span>
    <span class="nav-label">AI번호</span>
  </button>
  <button class="nav-item" onclick="location.href='/my/'">
    <span class="nav-icon">👤</span>
    <span class="nav-label">MY</span>
  </button>
</nav>

<!-- 온보딩 모달 -->
<div class="onboarding-overlay" id="onboardingOverlay">
  <div class="onboarding-card">
    <div class="onboarding-icon" id="onboardingIcon">🎰</div>
    <h2 class="onboarding-title" id="onboardingTitle">환영합니다!</h2>
    <p class="onboarding-desc" id="onboardingDesc">오늘로또는 AI가 분석한 로또 정보를 제공해요</p>
    <div class="onboarding-dots" id="onboardingDots">
      <div class="onboarding-dot active"></div>
      <div class="onboarding-dot"></div>
      <div class="onboarding-dot"></div>
      <div class="onboarding-dot"></div>
    </div>
    <div class="onboarding-btns">
      <button class="onboarding-btn secondary" onclick="skipOnboarding()">건너뛰기</button>
      <button class="onboarding-btn primary" onclick="nextOnboarding()">다음</button>
    </div>
  </div>
</div>

<!-- 토스트 -->
<div class="toast" id="toast">❤️ 관심 판매점에 추가했어요!</div>

<script>
// ================================
// 1. 스켈레톤 로딩
// ================================
document.addEventListener('DOMContentLoaded', function() {
  // 스켈레톤 로딩 시뮬레이션 (실제로는 API 호출 후)
  setTimeout(function() {
    document.getElementById('skeletonLoader').style.display = 'none';
    document.getElementById('storeContent').style.display = 'block';
  }, 800);
  
  // 첫 방문 시 온보딩
  if (!localStorage.getItem('onboarding_done')) {
    setTimeout(showOnboarding, 1000);
  }
  
  // 저장된 좋아요 복원
  restoreFavorites();
});

// ================================
// 2. 페이지 이동
// ================================
function goToStore(id) {
  // 리플 효과 후 이동
  location.href = '/stores/detail/' + id;
}

function filterRegion(region) {
  const url = new URL(window.location);
  if (region) {
    url.searchParams.set('region', region);
  } else {
    url.searchParams.delete('region');
  }
  location.href = url.toString();
}

// ================================
// 3. 마이크로 인터랙션 - 좋아요
// ================================
function toggleFavorite(event, id) {
  event.stopPropagation();
  const btn = event.currentTarget;
  const isActive = btn.classList.contains('active');
  
  if (isActive) {
    btn.classList.remove('active');
    btn.innerHTML = '🤍';
    removeFavorite(id);
  } else {
    btn.classList.add('active');
    btn.innerHTML = '❤️';
    addFavorite(id);
    showToast('❤️ 관심 판매점에 추가했어요!');
  }
}

function addFavorite(id) {
  let favs = JSON.parse(localStorage.getItem('favorites') || '[]');
  if (!favs.includes(id)) {
    favs.push(id);
    localStorage.setItem('favorites', JSON.stringify(favs));
  }
}

function removeFavorite(id) {
  let favs = JSON.parse(localStorage.getItem('favorites') || '[]');
  favs = favs.filter(f => f !== id);
  localStorage.setItem('favorites', JSON.stringify(favs));
}

function restoreFavorites() {
  const favs = JSON.parse(localStorage.getItem('favorites') || '[]');
  favs.forEach(id => {
    const btn = document.querySelector(`.favorite-btn[data-id="${id}"]`);
    if (btn) {
      btn.classList.add('active');
      btn.innerHTML = '❤️';
    }
  });
}

// ================================
// 4. 토스트 메시지
// ================================
function showToast(msg) {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2000);
}

// ================================
// 5. 온보딩
// ================================
const onboardingSteps = [
  { icon: '🎰', title: '환영합니다! 👋', desc: '오늘로또는 AI가 분석한 로또 정보를 제공해요' },
  { icon: '🎯', title: 'AI 예측 명당', desc: '6가지 알고리즘으로 다음 당첨 예상 판매점을 분석해요' },
  { icon: '🔢', title: 'AI 번호 추천', desc: '10가지 패턴 분석으로 최적의 번호를 추천해요' },
  { icon: '🚀', title: '시작해볼까요?', desc: '지금 바로 이번 주 추천을 확인하세요!' }
];
let currentStep = 0;

function showOnboarding() {
  currentStep = 0;
  updateOnboardingUI();
  document.getElementById('onboardingOverlay').classList.add('show');
}

function skipOnboarding() {
  document.getElementById('onboardingOverlay').classList.remove('show');
  localStorage.setItem('onboarding_done', 'true');
}

function nextOnboarding() {
  currentStep++;
  if (currentStep >= onboardingSteps.length) {
    skipOnboarding();
  } else {
    updateOnboardingUI();
  }
}

function updateOnboardingUI() {
  const step = onboardingSteps[currentStep];
  document.getElementById('onboardingIcon').textContent = step.icon;
  document.getElementById('onboardingTitle').textContent = step.title;
  document.getElementById('onboardingDesc').textContent = step.desc;
  
  // 도트 업데이트
  const dots = document.querySelectorAll('.onboarding-dot');
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === currentStep);
  });
  
  // 마지막 스텝이면 버튼 텍스트 변경
  const primaryBtn = document.querySelector('.onboarding-btn.primary');
  primaryBtn.textContent = currentStep === onboardingSteps.length - 1 ? '시작하기' : '다음';
}

// ================================
// 6. 알림
// ================================
function toggleNotification() {
  showToast('🔔 알림 설정이 곧 추가될 예정이에요!');
}

// ================================
// 7. 풀투리프레시 (당겨서 새로고침)
// ================================
let touchStartY = 0;
let touchEndY = 0;

document.addEventListener('touchstart', e => {
  touchStartY = e.changedTouches[0].screenY;
}, { passive: true });

document.addEventListener('touchend', e => {
  touchEndY = e.changedTouches[0].screenY;
  if (window.scrollY === 0 && touchEndY - touchStartY > 100) {
    location.reload();
  }
}, { passive: true });
</script>

</body>
</html>
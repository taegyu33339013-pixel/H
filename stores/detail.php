<?php
/**
 * /stores/detail.php - 로또 판매점 상세 페이지
 * 
 * URL 예시:
 * /stores/서울/강남구/대박로또-201
 * /stores/경기/화성시/신흥복권-202
 */

if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// URL에서 store_id 추출 (index.php에서 파싱됨)
// $store_id, $region1, $region2 변수가 전달됨

// ============================================
// 판매점 상세 데이터 (더미)
// ============================================
$stores_detail = [
    101 => [
        'store_id' => 101,
        'store_name' => '노다지복권방',
        'region1' => '서울',
        'region2' => '노원구',
        'address' => '서울 노원구 동일로 1234',
        'address_detail' => '노원역 4번출구 도보 3분',
        'phone' => '02-1234-5678',
        'lat' => 37.6555,
        'lng' => 127.0616,
        'wins_1st' => 15,
        'wins_2nd' => 42,
        'wins_total' => 57,
        'last_win_round' => 1089,
        'years' => 12,
        'open_time' => '09:00',
        'close_time' => '22:00',
        'is_open' => true,
        'features' => ['주차가능', '연중무휴', '카드결제'],
        'trend' => 3.2,
        'rank_national' => 1,
        'rank_regional' => 1,
    ],
    201 => [
        'store_id' => 201,
        'store_name' => '대박로또',
        'region1' => '서울',
        'region2' => '강남구',
        'address' => '서울 강남구 역삼로 123',
        'address_detail' => '역삼역 3번출구 도보 1분',
        'phone' => '02-555-1234',
        'lat' => 37.5007,
        'lng' => 127.0365,
        'wins_1st' => 3,
        'wins_2nd' => 8,
        'wins_total' => 11,
        'last_win_round' => 1148,
        'years' => 5,
        'open_time' => '08:00',
        'close_time' => '23:00',
        'is_open' => true,
        'features' => ['24시간', '주차가능', '카드결제', 'ATM'],
        'trend' => 15.2,
        'rank_national' => 45,
        'rank_regional' => 3,
        'is_hot' => true,
    ],
    202 => [
        'store_id' => 202,
        'store_name' => '신흥복권',
        'region1' => '경기',
        'region2' => '화성시',
        'address' => '경기 화성시 동탄대로 456',
        'address_detail' => '동탄역 1번출구 도보 5분',
        'phone' => '031-888-9999',
        'lat' => 37.2006,
        'lng' => 127.0977,
        'wins_1st' => 2,
        'wins_2nd' => 5,
        'wins_total' => 7,
        'last_win_round' => 1148,
        'years' => 3,
        'open_time' => '09:00',
        'close_time' => '21:00',
        'is_open' => true,
        'features' => ['주차가능', '카드결제'],
        'trend' => 12.8,
        'rank_national' => 78,
        'rank_regional' => 5,
        'is_hot' => true,
    ],
];

// 당첨 이력 데이터
$win_history = [
    201 => [
        ['round' => 1148, 'rank' => 1, 'type' => 'auto', 'prize' => 2532417890, 'date' => '2024.12.14', 'numbers' => [3, 12, 18, 27, 35, 42], 'bonus' => 7],
        ['round' => 1142, 'rank' => 1, 'type' => 'manual', 'prize' => 1876543210, 'date' => '2024.11.02', 'numbers' => [7, 14, 21, 28, 35, 42], 'bonus' => 3],
        ['round' => 1135, 'rank' => 2, 'type' => 'auto', 'prize' => 54328760, 'date' => '2024.09.14', 'numbers' => [5, 11, 23, 31, 38, 44], 'bonus' => 17],
        ['round' => 1128, 'rank' => 1, 'type' => 'semi', 'prize' => 2198765430, 'date' => '2024.07.27', 'numbers' => [2, 9, 18, 27, 36, 45], 'bonus' => 11],
        ['round' => 1120, 'rank' => 2, 'type' => 'auto', 'prize' => 48765430, 'date' => '2024.06.01', 'numbers' => [6, 13, 20, 27, 34, 41], 'bonus' => 8],
        ['round' => 1115, 'rank' => 2, 'type' => 'auto', 'prize' => 52341890, 'date' => '2024.04.27', 'numbers' => [1, 8, 15, 22, 29, 36], 'bonus' => 43],
    ],
    101 => [
        ['round' => 1089, 'rank' => 1, 'type' => 'auto', 'prize' => 1987654320, 'date' => '2023.10.14', 'numbers' => [4, 11, 18, 25, 32, 39], 'bonus' => 45],
        ['round' => 1078, 'rank' => 1, 'type' => 'manual', 'prize' => 2345678900, 'date' => '2023.07.29', 'numbers' => [3, 10, 17, 24, 31, 38], 'bonus' => 42],
        ['round' => 1065, 'rank' => 2, 'type' => 'auto', 'prize' => 48765430, 'date' => '2023.04.29', 'numbers' => [7, 14, 21, 28, 35, 42], 'bonus' => 1],
    ],
];

// 주변 판매점 데이터
$nearby_stores = [
    ['store_id' => 203, 'store_name' => '행운복권', 'distance' => 0.3, 'wins_1st' => 2, 'wins_2nd' => 5],
    ['store_id' => 204, 'store_name' => '대길복권방', 'distance' => 0.5, 'wins_1st' => 1, 'wins_2nd' => 3],
    ['store_id' => 205, 'store_name' => '황금로또', 'distance' => 0.8, 'wins_1st' => 3, 'wins_2nd' => 7],
    ['store_id' => 206, 'store_name' => '복권나라', 'distance' => 1.2, 'wins_1st' => 1, 'wins_2nd' => 2],
];

// 현재 판매점 데이터 가져오기
$store = $stores_detail[$store_id] ?? $stores_detail[201];
$history = $win_history[$store_id] ?? $win_history[201];
$is_hot = isset($store['is_hot']) && $store['is_hot'];

$page_title = $store['store_name'] . ' - 로또 명당';
$page_desc = $store['address'] . ' | 1등 ' . $store['wins_1st'] . '회, 2등 ' . $store['wins_2nd'] . '회 당첨';

function get_ball_class($n) {
    if ($n <= 10) return 'ball-yellow';
    if ($n <= 20) return 'ball-blue';
    if ($n <= 30) return 'ball-red';
    if ($n <= 40) return 'ball-gray';
    return 'ball-green';
}

function format_prize($amount) {
    if ($amount >= 100000000) {
        return number_format($amount / 100000000, 1) . '억';
    } else {
        return number_format($amount / 10000000, 0) . '천만';
    }
}

function get_type_label($type) {
    switch ($type) {
        case 'auto': return '자동';
        case 'manual': return '수동';
        case 'semi': return '반자동';
        default: return $type;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="theme-color" content="#080b14">
  
  <!-- OG Tags -->
  <meta property="og:title" content="<?= htmlspecialchars($store['store_name']) ?> - 로또 명당">
  <meta property="og:description" content="1등 <?= $store['wins_1st'] ?>회 당첨! <?= $store['address'] ?>">
  <meta property="og:type" content="website">
  
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <style>
    :root {
      --bg-deep: #080b14;
      --bg-primary: #0d1220;
      --bg-secondary: #151c2c;
      --bg-card: #1a2236;
      --bg-hover: #212b40;
      
      --gold: #F5B800;
      --gold-light: #FFD54F;
      --red: #FF4757;
      --orange: #FF8C00;
      --blue: #00B4D8;
      --purple: #9D4EDD;
      --green: #00E676;
      
      --text-primary: #ffffff;
      --text-secondary: #a8b5c8;
      --text-muted: #6b7a90;
      --border: rgba(255, 255, 255, 0.08);
      
      --theme-color: <?= $is_hot ? 'var(--red)' : 'var(--gold)' ?>;
      --theme-gradient: <?= $is_hot ? 'linear-gradient(135deg, #FF4757 0%, #FF8C00 100%)' : 'linear-gradient(135deg, #F5B800 0%, #FF8C00 100%)' ?>;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: 'Pretendard', -apple-system, sans-serif;
      background: var(--bg-deep);
      background-image: <?= $is_hot 
        ? 'radial-gradient(at 50% 0%, rgba(255, 71, 87, 0.15) 0px, transparent 50%)'
        : 'radial-gradient(at 50% 0%, rgba(245, 184, 0, 0.12) 0px, transparent 50%)'
      ?>;
      background-attachment: fixed;
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }
    
    a { color: inherit; text-decoration: none; }
    
    /* Header */
    .header {
      position: sticky; top: 0; z-index: 100;
      background: rgba(8, 11, 20, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }
    .header-inner {
      max-width: 1200px; margin: 0 auto; padding: 0 20px;
      height: 56px; display: flex; align-items: center; justify-content: space-between;
    }
    .back-btn {
      width: 40px; height: 40px; border-radius: 10px;
      background: var(--bg-secondary); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem; cursor: pointer;
    }
    .back-btn:hover { background: var(--bg-hover); }
    .header-title { font-weight: 700; font-size: 1.1rem; }
    .share-btn {
      width: 40px; height: 40px; border-radius: 10px;
      background: var(--bg-secondary); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem; cursor: pointer;
    }
    
    .main { max-width: 800px; margin: 0 auto; padding: 24px 20px 100px; }
    
    /* Hero Section */
    .hero {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
      margin-bottom: 24px;
    }
    .hero-header {
      padding: 32px 24px;
      background: linear-gradient(180deg, var(--bg-hover) 0%, var(--bg-card) 100%);
      text-align: center;
      position: relative;
    }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 6px 14px; border-radius: 20px;
      font-size: 0.8rem; font-weight: 700;
      margin-bottom: 16px;
    }
    .hero-badge.hot { background: var(--theme-gradient); color: #fff; }
    .hero-badge.gold { background: rgba(245, 184, 0, 0.2); color: var(--gold); }
    .hero-icon {
      width: 80px; height: 80px; border-radius: 24px;
      background: var(--theme-gradient);
      display: flex; align-items: center; justify-content: center;
      font-size: 2.5rem; margin: 0 auto 16px;
      box-shadow: 0 12px 32px <?= $is_hot ? 'rgba(255, 71, 87, 0.4)' : 'rgba(245, 184, 0, 0.4)' ?>;
    }
    .hero-name { font-size: 1.75rem; font-weight: 800; margin-bottom: 8px; }
    .hero-address { color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 4px; }
    .hero-address-detail { color: var(--text-muted); font-size: 0.85rem; }
    
    .hero-stats {
      display: grid; grid-template-columns: repeat(3, 1fr);
      border-top: 1px solid var(--border);
    }
    .hero-stat {
      padding: 24px 16px; text-align: center;
      border-right: 1px solid var(--border);
    }
    .hero-stat:last-child { border-right: none; }
    .hero-stat-value { font-size: 2rem; font-weight: 800; margin-bottom: 4px; }
    .hero-stat-value.gold { color: var(--gold); }
    .hero-stat-value.blue { color: var(--blue); }
    .hero-stat-value.purple { color: var(--purple); }
    .hero-stat-label { font-size: 0.8rem; color: var(--text-muted); }
    
    /* Quick Actions */
    .quick-actions {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;
      margin-bottom: 24px;
    }
    .quick-action {
      padding: 16px 12px; background: var(--bg-card);
      border: 1px solid var(--border); border-radius: 16px;
      text-align: center; cursor: pointer; transition: all 0.2s;
    }
    .quick-action:hover { background: var(--bg-hover); border-color: var(--theme-color); }
    .quick-action-icon { font-size: 1.5rem; margin-bottom: 8px; }
    .quick-action-label { font-size: 0.8rem; color: var(--text-secondary); }
    
    /* Section */
    .section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 20px;
      margin-bottom: 20px;
      overflow: hidden;
    }
    .section-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .section-title {
      font-size: 1.1rem; font-weight: 700;
      display: flex; align-items: center; gap: 8px;
    }
    .section-more { font-size: 0.85rem; color: var(--text-muted); }
    .section-content { padding: 20px 24px; }
    
    /* Lotto Balls */
    .lotto-balls { display: flex; gap: 6px; align-items: center; }
    .lotto-ball {
      width: 32px; height: 32px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 0.8rem; color: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    .ball-yellow { background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #000; }
    .ball-blue { background: linear-gradient(135deg, #3B82F6, #2563EB); }
    .ball-red { background: linear-gradient(135deg, #EF4444, #DC2626); }
    .ball-gray { background: linear-gradient(135deg, #6B7280, #4B5563); }
    .ball-green { background: linear-gradient(135deg, #22C55E, #16A34A); }
    .ball-bonus { background: linear-gradient(135deg, #9D4EDD, #EC4899); }
    
    /* Win History */
    .win-item {
      display: flex; align-items: center; gap: 16px;
      padding: 16px 0;
      border-bottom: 1px solid var(--border);
    }
    .win-item:last-child { border-bottom: none; }
    .win-rank {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem;
    }
    .win-rank.gold { background: linear-gradient(135deg, #F5B800, #FF8C00); }
    .win-rank.blue { background: linear-gradient(135deg, #00B4D8, #9D4EDD); }
    .win-info { flex: 1; min-width: 0; }
    .win-round { font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
    .win-type {
      padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;
    }
    .win-type.gold { background: rgba(245, 184, 0, 0.2); color: var(--gold); }
    .win-type.blue { background: rgba(0, 180, 216, 0.2); color: var(--blue); }
    .win-date { font-size: 0.85rem; color: var(--text-muted); }
    .win-numbers { margin-top: 8px; }
    .win-prize { text-align: right; }
    .win-prize-amount { font-size: 1.25rem; font-weight: 800; }
    .win-prize-amount.gold { color: var(--gold); }
    .win-prize-amount.blue { color: var(--blue); }
    .win-prize-label { font-size: 0.75rem; color: var(--text-muted); }
    
    /* Info List */
    .info-list { display: flex; flex-direction: column; gap: 16px; }
    .info-item { display: flex; align-items: center; gap: 12px; }
    .info-icon {
      width: 40px; height: 40px; border-radius: 10px;
      background: var(--bg-secondary);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem;
    }
    .info-content { flex: 1; }
    .info-label { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 2px; }
    .info-value { font-weight: 600; }
    .info-action {
      padding: 8px 16px; border-radius: 8px;
      background: var(--theme-gradient); color: <?= $is_hot ? '#fff' : '#000' ?>;
      font-size: 0.85rem; font-weight: 600;
    }
    
    /* Features */
    .features { display: flex; flex-wrap: wrap; gap: 8px; }
    .feature-tag {
      padding: 8px 14px; border-radius: 20px;
      background: var(--bg-secondary); border: 1px solid var(--border);
      font-size: 0.85rem; color: var(--text-secondary);
    }
    
    /* Map */
    .map-container {
      width: 100%; height: 200px; border-radius: 16px;
      background: var(--bg-secondary);
      display: flex; align-items: center; justify-content: center;
      color: var(--text-muted); font-size: 0.9rem;
      margin-bottom: 16px;
    }
    .map-actions { display: flex; gap: 12px; }
    .map-btn {
      flex: 1; padding: 14px; border-radius: 12px;
      background: var(--bg-secondary); border: 1px solid var(--border);
      text-align: center; font-weight: 600; font-size: 0.9rem;
      cursor: pointer; transition: all 0.2s;
    }
    .map-btn:hover { background: var(--bg-hover); border-color: var(--theme-color); }
    
    /* Nearby Stores */
    .nearby-item {
      display: flex; align-items: center; gap: 12px;
      padding: 14px 0;
      border-bottom: 1px solid var(--border);
    }
    .nearby-item:last-child { border-bottom: none; }
    .nearby-icon {
      width: 44px; height: 44px; border-radius: 12px;
      background: var(--bg-secondary);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem;
    }
    .nearby-info { flex: 1; }
    .nearby-name { font-weight: 600; margin-bottom: 2px; }
    .nearby-meta { font-size: 0.85rem; color: var(--text-muted); }
    .nearby-wins {
      display: flex; gap: 12px; text-align: center;
    }
    .nearby-win-value { font-weight: 700; font-size: 1rem; }
    .nearby-win-value.gold { color: var(--gold); }
    .nearby-win-value.blue { color: var(--blue); }
    .nearby-win-label { font-size: 0.7rem; color: var(--text-muted); }
    
    /* Ranking Card */
    .ranking-card {
      display: flex; gap: 16px;
    }
    .ranking-item {
      flex: 1; padding: 20px; border-radius: 16px;
      background: var(--bg-secondary); text-align: center;
    }
    .ranking-value { font-size: 2rem; font-weight: 800; color: var(--theme-color); margin-bottom: 4px; }
    .ranking-label { font-size: 0.85rem; color: var(--text-muted); }
    .ranking-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
    
    /* Trend */
    .trend-positive { color: var(--red); }
    .trend-negative { color: var(--blue); }
    
    /* Status Badge */
    .status-open { color: var(--green); }
    .status-closed { color: var(--red); }
    
    /* Bottom CTA */
    .bottom-cta {
      position: fixed; bottom: 0; left: 0; right: 0;
      padding: 16px 20px;
      background: rgba(8, 11, 20, 0.95);
      backdrop-filter: blur(10px);
      border-top: 1px solid var(--border);
      z-index: 90;
    }
    .bottom-cta-inner {
      max-width: 800px; margin: 0 auto;
      display: flex; gap: 12px;
    }
    .cta-btn {
      flex: 1; padding: 16px; border-radius: 14px;
      font-size: 1rem; font-weight: 700; text-align: center;
      cursor: pointer; border: none; transition: all 0.2s;
    }
    .cta-btn.primary {
      background: var(--theme-gradient);
      color: <?= $is_hot ? '#fff' : '#000' ?>;
    }
    .cta-btn.secondary {
      background: var(--bg-card);
      border: 1px solid var(--border);
      color: var(--text-primary);
    }
    
    @media (max-width: 768px) {
      .quick-actions { grid-template-columns: repeat(2, 1fr); }
      .hero-stat-value { font-size: 1.5rem; }
      .ranking-card { flex-direction: column; gap: 12px; }
    }
  </style>
</head>
<body>

<header class="header">
  <div class="header-inner">
    <a href="/stores/" class="back-btn">←</a>
    <span class="header-title"><?= htmlspecialchars($store['store_name']) ?></span>
    <button class="share-btn" onclick="shareStore()">📤</button>
  </div>
</header>

<main class="main">
  <!-- Hero Section -->
  <div class="hero">
    <div class="hero-header">
      <?php if ($is_hot): ?>
      <div class="hero-badge hot">🔥 HOT 판매점</div>
      <?php else: ?>
      <div class="hero-badge gold">🏆 전국 <?= $store['rank_national'] ?>위 명당</div>
      <?php endif; ?>
      
      <div class="hero-icon"><?= $is_hot ? '🔥' : '🏆' ?></div>
      <h1 class="hero-name"><?= htmlspecialchars($store['store_name']) ?></h1>
      <p class="hero-address"><?= htmlspecialchars($store['address']) ?></p>
      <p class="hero-address-detail"><?= htmlspecialchars($store['address_detail']) ?></p>
    </div>
    
    <div class="hero-stats">
      <div class="hero-stat">
        <div class="hero-stat-value gold"><?= $store['wins_1st'] ?></div>
        <div class="hero-stat-label">1등 당첨</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-value blue"><?= $store['wins_2nd'] ?></div>
        <div class="hero-stat-label">2등 당첨</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-value purple"><?= $store['wins_total'] ?></div>
        <div class="hero-stat-label">총 당첨</div>
      </div>
    </div>
  </div>
  
  <!-- Quick Actions -->
  <div class="quick-actions">
    <a href="tel:<?= $store['phone'] ?>" class="quick-action">
      <div class="quick-action-icon">📞</div>
      <div class="quick-action-label">전화하기</div>
    </a>
    <a href="https://map.kakao.com/link/to/<?= urlencode($store['store_name']) ?>,<?= $store['lat'] ?>,<?= $store['lng'] ?>" class="quick-action" target="_blank">
      <div class="quick-action-icon">🗺️</div>
      <div class="quick-action-label">길찾기</div>
    </a>
    <button class="quick-action" onclick="shareStore()">
      <div class="quick-action-icon">📤</div>
      <div class="quick-action-label">공유</div>
    </button>
    <button class="quick-action" onclick="toggleFavorite()">
      <div class="quick-action-icon">⭐</div>
      <div class="quick-action-label">즐겨찾기</div>
    </button>
  </div>
  
  <!-- 랭킹 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">📊 랭킹</h2>
      <span class="trend-positive">+<?= number_format($store['trend'], 1) ?>% 🚀</span>
    </div>
    <div class="section-content">
      <div class="ranking-card">
        <div class="ranking-item">
          <div class="ranking-value"><?= $store['rank_national'] ?>위</div>
          <div class="ranking-label">전국 순위</div>
          <div class="ranking-sub">상위 <?= round($store['rank_national'] / 100 * 100, 1) ?>%</div>
        </div>
        <div class="ranking-item">
          <div class="ranking-value"><?= $store['rank_regional'] ?>위</div>
          <div class="ranking-label"><?= $store['region1'] ?> <?= $store['region2'] ?> 순위</div>
          <div class="ranking-sub">지역 내 TOP <?= $store['rank_regional'] ?></div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 당첨 이력 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">🏆 당첨 이력</h2>
      <span class="section-more">총 <?= count($history) ?>건</span>
    </div>
    <div class="section-content">
      <?php foreach (array_slice($history, 0, 5) as $win): 
        $is_1st = $win['rank'] === 1;
      ?>
      <div class="win-item">
        <div class="win-rank <?= $is_1st ? 'gold' : 'blue' ?>">
          <?= $is_1st ? '🏆' : '🥈' ?>
        </div>
        <div class="win-info">
          <div class="win-round">
            <?= $win['round'] ?>회
            <span class="win-type <?= $is_1st ? 'gold' : 'blue' ?>">
              <?= $win['rank'] ?>등 <?= get_type_label($win['type']) ?>
            </span>
          </div>
          <div class="win-date"><?= $win['date'] ?></div>
          <div class="win-numbers">
            <div class="lotto-balls">
              <?php foreach ($win['numbers'] as $num): ?>
              <div class="lotto-ball <?= get_ball_class($num) ?>"><?= $num ?></div>
              <?php endforeach; ?>
              <span style="margin: 0 4px; color: var(--text-muted);">+</span>
              <div class="lotto-ball ball-bonus"><?= $win['bonus'] ?></div>
            </div>
          </div>
        </div>
        <div class="win-prize">
          <div class="win-prize-amount <?= $is_1st ? 'gold' : 'blue' ?>">
            <?= format_prize($win['prize']) ?>
          </div>
          <div class="win-prize-label">당첨금</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- 매장 정보 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">🏪 매장 정보</h2>
      <span class="<?= $store['is_open'] ? 'status-open' : 'status-closed' ?>">
        <?= $store['is_open'] ? '● 영업중' : '● 영업종료' ?>
      </span>
    </div>
    <div class="section-content">
      <div class="info-list">
        <div class="info-item">
          <div class="info-icon">📍</div>
          <div class="info-content">
            <div class="info-label">주소</div>
            <div class="info-value"><?= htmlspecialchars($store['address']) ?></div>
          </div>
          <button class="info-action" onclick="copyAddress()">복사</button>
        </div>
        <div class="info-item">
          <div class="info-icon">📞</div>
          <div class="info-content">
            <div class="info-label">전화번호</div>
            <div class="info-value"><?= $store['phone'] ?></div>
          </div>
          <a href="tel:<?= $store['phone'] ?>" class="info-action">전화</a>
        </div>
        <div class="info-item">
          <div class="info-icon">🕐</div>
          <div class="info-content">
            <div class="info-label">영업시간</div>
            <div class="info-value"><?= $store['open_time'] ?> ~ <?= $store['close_time'] ?></div>
          </div>
        </div>
        <div class="info-item">
          <div class="info-icon">📅</div>
          <div class="info-content">
            <div class="info-label">운영기간</div>
            <div class="info-value"><?= $store['years'] ?>년</div>
          </div>
        </div>
      </div>
      
      <div style="margin-top: 20px;">
        <div class="info-label" style="margin-bottom: 12px;">편의시설</div>
        <div class="features">
          <?php foreach ($store['features'] as $feature): ?>
          <span class="feature-tag"><?= $feature ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 지도 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">🗺️ 위치</h2>
    </div>
    <div class="section-content">
      <div class="map-container" id="map">
        지도 로딩중...
      </div>
      <div class="map-actions">
        <a href="https://map.kakao.com/link/to/<?= urlencode($store['store_name']) ?>,<?= $store['lat'] ?>,<?= $store['lng'] ?>" class="map-btn" target="_blank">
          🗺️ 카카오맵
        </a>
        <a href="https://map.naver.com/v5/directions/-/-/-/transit?c=<?= $store['lng'] ?>,<?= $store['lat'] ?>,15,0,0,0,dh" class="map-btn" target="_blank">
          🗺️ 네이버맵
        </a>
      </div>
    </div>
  </div>
  
  <!-- 주변 판매점 -->
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">📍 주변 판매점</h2>
      <a href="/stores/<?= urlencode($store['region1']) ?>/" class="section-more">더보기 →</a>
    </div>
    <div class="section-content">
      <?php foreach ($nearby_stores as $nearby): ?>
      <a href="/stores/<?= urlencode($store['region1']) ?>/<?= urlencode($nearby['store_name']) ?>-<?= $nearby['store_id'] ?>" class="nearby-item">
        <div class="nearby-icon">🏪</div>
        <div class="nearby-info">
          <div class="nearby-name"><?= htmlspecialchars($nearby['store_name']) ?></div>
          <div class="nearby-meta"><?= $nearby['distance'] ?>km 거리</div>
        </div>
        <div class="nearby-wins">
          <div>
            <div class="nearby-win-value gold"><?= $nearby['wins_1st'] ?></div>
            <div class="nearby-win-label">1등</div>
          </div>
          <div>
            <div class="nearby-win-value blue"><?= $nearby['wins_2nd'] ?></div>
            <div class="nearby-win-label">2등</div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<!-- Bottom CTA -->
<div class="bottom-cta">
  <div class="bottom-cta-inner">
    <a href="tel:<?= $store['phone'] ?>" class="cta-btn secondary">📞 전화하기</a>
    <a href="https://map.kakao.com/link/to/<?= urlencode($store['store_name']) ?>,<?= $store['lat'] ?>,<?= $store['lng'] ?>" class="cta-btn primary" target="_blank">🗺️ 길찾기</a>
  </div>
</div>

<script>
function shareStore() {
  const shareData = {
    title: '<?= addslashes($store['store_name']) ?> - 로또 명당',
    text: '1등 <?= $store['wins_1st'] ?>회 당첨! <?= addslashes($store['address']) ?>',
    url: window.location.href
  };
  
  if (navigator.share) {
    navigator.share(shareData);
  } else {
    navigator.clipboard.writeText(window.location.href);
    alert('링크가 복사되었습니다!');
  }
}

function copyAddress() {
  navigator.clipboard.writeText('<?= addslashes($store['address']) ?>');
  alert('주소가 복사되었습니다!');
}

function toggleFavorite() {
  // 즐겨찾기 토글 로직
  alert('즐겨찾기에 추가되었습니다!');
}

// 카카오맵 초기화 (카카오맵 API 키 필요)
/*
kakao.maps.load(function() {
  var container = document.getElementById('map');
  var options = {
    center: new kakao.maps.LatLng(<?= $store['lat'] ?>, <?= $store['lng'] ?>),
    level: 3
  };
  var map = new kakao.maps.Map(container, options);
  
  var marker = new kakao.maps.Marker({
    position: new kakao.maps.LatLng(<?= $store['lat'] ?>, <?= $store['lng'] ?>)
  });
  marker.setMap(map);
});
*/
</script>

</body>
</html>
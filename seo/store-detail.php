<?php
/**
 * 판매점 상세 SEO 페이지 (동 단위 URL) - 강화 버전
 * URL: /stores/서울/강남구/역삼동/대박복권-123/
 * 
 * 추가 기능:
 * - 명당 점수 시스템
 * - 당첨 패턴 분석
 * - 연도별 타임라인 (당첨번호 포함)
 * - FAQ Schema
 * - 즐겨찾기/공유 기능
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

include_once(G5_PATH . '/lib/lotto_store.lib.php');

$region1 = isset($_GET['r1']) ? urldecode(trim($_GET['r1'])) : '';
$region2 = isset($_GET['r2']) ? urldecode(trim($_GET['r2'])) : '';
$region3 = isset($_GET['r3']) ? urldecode(trim($_GET['r3'])) : ''; // 동
$store_slug = isset($_GET['store']) ? urldecode(trim($_GET['store'])) : '';

// 슬러그에서 store_id 추출 (예: "대박복권-123" → 123)
$store_id = 0;
if (preg_match('/-(\d+)$/', $store_slug, $m)) {
    $store_id = (int)$m[1];
}

if ($store_id <= 0) {
    header("HTTP/1.0 404 Not Found");
    include(G5_PATH . '/404.html');
    exit;
}

// 판매점 정보 조회
$store = null;
if (function_exists('li_get_store_by_id')) {
    $store = li_get_store_by_id($store_id);
}

if (!$store) {
    // DB에서 직접 조회
    $store = sql_fetch("SELECT * FROM g5_lotto_store WHERE store_id = {$store_id}");
}

if (!$store) {
    header("HTTP/1.0 404 Not Found");
    include(G5_PATH . '/404.html');
    exit;
}

// 당첨 이력 조회 (당첨번호, 당첨금 포함)
$win_history = [];
$res = sql_query("
    SELECT w.*, d.draw_date, d.n1, d.n2, d.n3, d.n4, d.n5, d.n6, d.bonus, 
           d.first_prize_each, d.second_prize_each
    FROM g5_lotto_store_win w
    LEFT JOIN g5_lotto_draw d ON w.draw_no = d.draw_no
    WHERE w.store_id = {$store_id}
    ORDER BY w.draw_no DESC
    LIMIT 50
", false);
if ($res) {
    while ($row = sql_fetch_array($res)) {
        $win_history[] = $row;
    }
}

// 1등, 2등 카운트
$first_wins = array_filter($win_history, fn($w) => $w['rank'] == 1);
$second_wins = array_filter($win_history, fn($w) => $w['rank'] == 2);

// 주변 판매점 (같은 구/군)
$nearby_stores = [];
$r2_esc = sql_real_escape_string($store['region2'] ?? '');
if ($r2_esc) {
    $res = sql_query("
        SELECT * FROM g5_lotto_store 
        WHERE region2 = '{$r2_esc}' AND store_id != {$store_id}
        ORDER BY wins_1st DESC
        LIMIT 5
    ", false);
    if ($res) {
        while ($row = sql_fetch_array($res)) {
            $nearby_stores[] = $row;
        }
    }
}

// 기본 정보
$store_name = $store['store_name'] ?? '판매점';
$address = $store['address'] ?? '';
$wins_1st = (int)($store['wins_1st'] ?? 0);
$wins_2nd = (int)($store['wins_2nd'] ?? 0);

// ============================================
// 명당 점수 & 패턴 분석 계산
// ============================================

// 현재 최신 회차
$current_round_row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$current_round = (int)($current_round_row['max_round'] ?? 1202);

// 1) 명당 점수 계산
$store_score = 0;
$store_score += $wins_1st * 100;  // 1등당 100점
$store_score += $wins_2nd * 30;   // 2등당 30점

// 최근성 가중치
$latest_round = isset($win_history[0]['draw_no']) ? (int)$win_history[0]['draw_no'] : 0;
$rounds_since = $latest_round > 0 ? $current_round - $latest_round : 999;

if ($rounds_since <= 10) $store_score += 50;
elseif ($rounds_since <= 30) $store_score += 30;
elseif ($rounds_since <= 52) $store_score += 10;
elseif ($rounds_since > 104) $store_score -= 50;

$store_score = max(0, $store_score);

// 등급 판정
if ($store_score >= 500) { $grade = 'S'; $stars = 5; $grade_label = '전설의 명당'; }
elseif ($store_score >= 300) { $grade = 'A'; $stars = 4; $grade_label = '검증된 명당'; }
elseif ($store_score >= 150) { $grade = 'B'; $stars = 3; $grade_label = '주목할 명당'; }
elseif ($store_score >= 50) { $grade = 'C'; $stars = 2; $grade_label = '기대되는 곳'; }
else { $grade = 'D'; $stars = 1; $grade_label = '새로운 도전'; }

// 2) 당첨 패턴 분석
$total_prize = 0;
$auto_count = 0;
$manual_count = 0;
$win_rounds = [];

foreach ($win_history as $w) {
    // 당첨금 계산 (1등 또는 2등)
    if ($w['rank'] == 1) {
        $total_prize += (int)($w['prize_amount'] ?? $w['first_prize_each'] ?? 0);
    } else {
        $total_prize += (int)($w['prize_amount'] ?? $w['second_prize_each'] ?? 0);
    }
    
    if (($w['win_type'] ?? '') == 'auto') $auto_count++;
    elseif (($w['win_type'] ?? '') == 'manual') $manual_count++;
    else $auto_count++; // 반자동은 자동으로 분류
    
    $win_rounds[] = (int)$w['draw_no'];
}

// 평균 주기 계산
$avg_cycle = 0;
if (count($win_rounds) >= 2) {
    $cycles = [];
    for ($i = 0; $i < count($win_rounds) - 1; $i++) {
        $cycles[] = $win_rounds[$i] - $win_rounds[$i + 1];
    }
    $avg_cycle = round(array_sum($cycles) / count($cycles));
}

$total_wins = $auto_count + $manual_count;
$auto_ratio = $total_wins > 0 ? round($auto_count / $total_wins * 100) : 0;

// 3) 온도계 상태
if ($rounds_since <= 5) { 
    $temp_icon = '🔥🔥🔥'; $temp_text = '지금 뜨거운 명당!'; $temp_class = 'hot'; 
} elseif ($rounds_since <= 15) { 
    $temp_icon = '🔥'; $temp_text = '최근 당첨'; $temp_class = 'warm'; 
} elseif ($rounds_since <= 52) { 
    $temp_icon = '💤'; $temp_text = '준비 중'; $temp_class = 'cool'; 
} else { 
    $temp_icon = '❄️'; $temp_text = '오랜 침묵'; $temp_class = 'cold'; 
}

// 4) 연도별 당첨 그룹핑 (타임라인용)
$wins_by_year = [];
foreach ($win_history as $w) {
    $year = $w['draw_date'] ? date('Y', strtotime($w['draw_date'])) : '미상';
    if (!isset($wins_by_year[$year])) $wins_by_year[$year] = [];
    $wins_by_year[$year][] = $w;
}
krsort($wins_by_year); // 최신 연도 먼저

// SEO 메타
$page_title = "{$store_name} - {$region1} {$region2} 로또 명당";
$page_desc = "{$store_name} ({$address}) 로또 판매점 상세 정보. 누적 1등 {$wins_1st}회, 2등 {$wins_2nd}회 당첨. 명당 점수 {$store_score}점. 당첨 이력 및 위치 정보.";
$canonical = "https://lottoinsight.ai/로또-판매점/" . urlencode($region1) . "/" . urlencode($region2) . "/" . urlencode($region3) . "/" . urlencode($store_name) . "-{$store_id}/";

function format_prize($amount) {
    if (!$amount || $amount <= 0) return '-';
    $eok = floor($amount / 100000000);
    $man = floor(($amount % 100000000) / 10000);
    $out = '';
    if ($eok > 0) $out .= number_format($eok) . '억 ';
    if ($man > 0 && $eok < 100) $out .= number_format($man) . '만';
    return trim($out) ?: number_format($amount) . '원';
}

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
  
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($store_name) ?>, <?= htmlspecialchars($region3) ?> 로또, <?= htmlspecialchars($region2) ?> 명당, 로또 판매점, 명당 점수">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:type" content="place">
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:url" content="<?= $canonical ?>">
  
  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">

  <!-- BreadcrumbList -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "홈", "item": "https://lottoinsight.ai/"},
      {"@type": "ListItem", "position": 2, "name": "로또 판매점", "item": "https://lottoinsight.ai/로또-판매점/"},
      {"@type": "ListItem", "position": 3, "name": "<?= htmlspecialchars($region1) ?>", "item": "https://lottoinsight.ai/로또-판매점/<?= urlencode($region1) ?>/"},
      {"@type": "ListItem", "position": 4, "name": "<?= htmlspecialchars($region2) ?>", "item": "https://lottoinsight.ai/로또-판매점/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/"},
      {"@type": "ListItem", "position": 5, "name": "<?= htmlspecialchars($region3) ?>", "item": "https://lottoinsight.ai/로또-판매점/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/<?= urlencode($region3) ?>/"},
      {"@type": "ListItem", "position": 6, "name": "<?= htmlspecialchars($store_name) ?>", "item": "<?= $canonical ?>"}
    ]
  }
  </script>

  <!-- LocalBusiness Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Store",
    "name": "<?= htmlspecialchars($store_name) ?>",
    "description": "로또 판매점. 누적 1등 <?= $wins_1st ?>회 배출. 명당 점수 <?= $store_score ?>점.",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= htmlspecialchars($address) ?>",
      "addressLocality": "<?= htmlspecialchars($region3) ?>",
      "addressRegion": "<?= htmlspecialchars($region1) ?>",
      "addressCountry": "KR"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": "<?= $store['lat'] ?? '' ?>",
      "longitude": "<?= $store['lng'] ?? '' ?>"
    },
    "url": "<?= $canonical ?>",
    "image": "https://lottoinsight.ai/favicon.svg",
    "priceRange": "₩1,000"
  }
  </script>

  <!-- FAQPage Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "<?= htmlspecialchars($store_name) ?>은(는) 몇 번 1등이 나왔나요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= htmlspecialchars($store_name) ?>에서는 총 <?= $wins_1st ?>회의 로또 1등, <?= $wins_2nd ?>회의 2등이 배출되었습니다. 누적 당첨금은 약 <?= format_prize($total_prize) ?>입니다."
        }
      },
      {
        "@type": "Question",
        "name": "<?= htmlspecialchars($store_name) ?> 위치가 어디인가요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= htmlspecialchars($address) ?>에 위치해 있습니다."
        }
      },
      {
        "@type": "Question",
        "name": "<?= htmlspecialchars($store_name) ?>의 명당 점수는 몇 점인가요?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= htmlspecialchars($store_name) ?>의 명당 점수는 <?= $store_score ?>점으로, <?= $grade_label ?> 등급입니다."
        }
      }
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
      --primary: #0d1526;
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
    
    .store-header { background: rgba(13,24,41,0.8); border-radius: 24px; padding: 32px; margin-bottom: 24px; }
    .store-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: linear-gradient(135deg, rgba(255,215,95,0.2), rgba(255,215,95,0.05)); border-radius: 20px; font-size: 0.85rem; color: var(--accent-gold); margin-bottom: 16px; }
    .store-name { font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: 8px; }
    .store-address { color: var(--text-secondary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    
    .stats-row { display: flex; gap: 16px; flex-wrap: wrap; }
    .stat-item { flex: 1; min-width: 100px; padding: 16px; background: rgba(0,0,0,0.3); border-radius: 12px; text-align: center; }
    .stat-value { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800; }
    .stat-value.gold { color: var(--accent-gold); }
    .stat-value.cyan { color: var(--accent-cyan); }
    .stat-label { color: var(--text-muted); font-size: 0.85rem; margin-top: 4px; }
    
    .section { background: rgba(13,24,41,0.8); border-radius: 20px; overflow: hidden; margin-bottom: 24px; }
    .section-header { padding: 16px 24px; background: rgba(0,0,0,0.3); font-weight: 700; display: flex; align-items: center; gap: 8px; justify-content: space-between; }
    .section-body { padding: 16px 24px; }
    
    /* 명당 점수 카드 */
    .score-card {
      background: linear-gradient(145deg, rgba(255,215,95,0.15), rgba(255,215,95,0.02));
      border: 1px solid rgba(255,215,95,0.3);
      border-radius: 16px;
      padding: 20px;
      margin-bottom: 20px;
      text-align: center;
    }
    .score-value {
      font-family: 'Outfit', sans-serif;
      font-size: 2.5rem;
      font-weight: 900;
      background: linear-gradient(135deg, #FFD75F, #FF9F43);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .score-stars { font-size: 1.3rem; margin: 8px 0; letter-spacing: 2px; }
    .score-label { color: var(--accent-gold); font-weight: 600; font-size: 1.1rem; }
    .score-bar {
      height: 8px;
      background: rgba(255,255,255,0.1);
      border-radius: 4px;
      margin-top: 12px;
      overflow: hidden;
    }
    .score-bar-fill {
      height: 100%;
      background: linear-gradient(90deg, #FFD75F, #FF9F43);
      border-radius: 4px;
      transition: width 1s ease;
    }

    /* 온도계 */
    .temp-badge { 
      display: inline-flex; 
      align-items: center; 
      gap: 8px; 
      padding: 8px 16px; 
      border-radius: 20px; 
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 16px;
    }
    .temp-badge.hot { background: rgba(239,68,68,0.2); color: #f87171; }
    .temp-badge.warm { background: rgba(251,146,60,0.2); color: #fb923c; }
    .temp-badge.cool { background: rgba(148,163,184,0.2); color: #94a3b8; }
    .temp-badge.cold { background: rgba(56,189,248,0.2); color: #38bdf8; }

    /* 액션 버튼 */
    .action-buttons {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 20px;
    }
    .action-btn {
      flex: 1;
      min-width: 90px;
      padding: 12px 16px;
      background: rgba(255,255,255,0.05);
      border: 1px solid var(--glass-border);
      border-radius: 12px;
      color: var(--text-primary);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 0.9rem;
    }
    .action-btn:hover { background: rgba(0,224,164,0.1); border-color: var(--accent-cyan); }
    .action-btn.favorited { background: rgba(255,215,95,0.15); border-color: var(--accent-gold); color: var(--accent-gold); }

    /* 패턴 분석 */
    .analysis-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 16px;
    }
    .analysis-item {
      background: rgba(0,0,0,0.3);
      border-radius: 12px;
      padding: 16px;
      text-align: center;
    }
    .analysis-icon { font-size: 1.5rem; margin-bottom: 8px; }
    .analysis-value { 
      font-family: 'Outfit', sans-serif; 
      font-size: 1.3rem; 
      font-weight: 700; 
      color: var(--text-primary);
    }
    .analysis-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }

    /* 비율 바 */
    .ratio-bar {
      display: flex;
      height: 28px;
      border-radius: 14px;
      overflow: hidden;
      margin-top: 12px;
    }
    .ratio-auto { background: var(--accent-cyan); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600; color: #000; }
    .ratio-manual { background: var(--accent-gold); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600; color: #000; }

    /* 타임라인 */
    .timeline-item {
      background: rgba(0,0,0,0.2);
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 16px;
    }
    .timeline-year-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .timeline-year-label {
      font-family: 'Outfit', sans-serif;
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--accent-cyan);
    }
    .timeline-year-count {
      font-size: 0.85rem;
      color: var(--text-muted);
    }
    .timeline-win {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px;
      background: rgba(0,0,0,0.2);
      border-radius: 8px;
      margin-bottom: 8px;
      flex-wrap: wrap;
    }
    .timeline-rank {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.9rem;
    }
    .timeline-rank.first { background: linear-gradient(145deg, rgba(255,215,95,0.3), rgba(255,215,95,0.1)); color: var(--accent-gold); }
    .timeline-rank.second { background: linear-gradient(145deg, rgba(0,224,164,0.3), rgba(0,224,164,0.1)); color: var(--accent-cyan); }
    .timeline-info { flex: 1; min-width: 100px; }
    .timeline-round { font-weight: 600; font-size: 0.95rem; }
    .timeline-round a { color: inherit; text-decoration: none; }
    .timeline-round a:hover { color: var(--accent-cyan); }
    .timeline-date { font-size: 0.8rem; color: var(--text-muted); }
    .timeline-balls { display: flex; gap: 3px; flex-wrap: wrap; }
    .timeline-prize { font-weight: 600; color: var(--accent-gold); font-size: 0.9rem; }
    
    /* 당첨 이력 */
    .history-item { display: flex; align-items: center; gap: 16px; padding: 16px; background: rgba(0,0,0,0.2); border-radius: 12px; margin-bottom: 12px; flex-wrap: wrap; }
    .history-rank { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; }
    .history-rank.first { background: linear-gradient(145deg, rgba(255,215,95,0.3), rgba(255,215,95,0.1)); color: var(--accent-gold); }
    .history-rank.second { background: linear-gradient(145deg, rgba(0,224,164,0.3), rgba(0,224,164,0.1)); color: var(--accent-cyan); }
    .history-info { flex: 1; min-width: 120px; }
    .history-round { font-weight: 600; }
    .history-round a { color: inherit; text-decoration: none; }
    .history-round a:hover { color: var(--accent-cyan); }
    .history-date { font-size: 0.85rem; color: var(--text-muted); }
    .history-balls { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
    .mini-ball { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: #fff; }
    .ball-yellow { background: var(--ball-yellow); }
    .ball-blue { background: var(--ball-blue); }
    .ball-red { background: var(--ball-red); }
    .ball-gray { background: var(--ball-gray); }
    .ball-green { background: var(--ball-green); }
    .history-prize { font-weight: 600; color: var(--accent-gold); min-width: 100px; text-align: right; }
    .history-type { padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    .history-type.auto { background: rgba(0,224,164,0.15); color: var(--accent-cyan); }
    .history-type.manual { background: rgba(255,215,95,0.15); color: var(--accent-gold); }
    
    .nearby-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
    .nearby-card { display: block; padding: 16px; background: rgba(0,0,0,0.2); border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.3s; }
    .nearby-card:hover { background: rgba(0,224,164,0.1); }
    .nearby-name { font-weight: 600; margin-bottom: 4px; }
    .nearby-stats { font-size: 0.85rem; color: var(--text-muted); }
    
    .map-placeholder { height: 200px; background: rgba(0,0,0,0.3); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); flex-direction: column; gap: 8px; }
    .map-address { margin-top: 12px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .copy-btn { padding: 8px 16px; background: rgba(255,255,255,0.1); border: none; border-radius: 8px; color: var(--text-primary); cursor: pointer; font-size: 0.85rem; }
    .copy-btn:hover { background: rgba(0,224,164,0.2); }
    
    /* FAQ */
    .faq-item { margin-bottom: 12px; }
    .faq-question {
      font-weight: 600;
      padding: 14px 16px;
      background: rgba(0,0,0,0.3);
      border-radius: 10px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: background 0.3s;
    }
    .faq-question:hover { background: rgba(0,0,0,0.4); }
    .faq-answer {
      padding: 14px 16px;
      color: var(--text-secondary);
      line-height: 1.7;
      display: none;
      background: rgba(0,0,0,0.15);
      border-radius: 0 0 10px 10px;
      margin-top: -4px;
    }
    .faq-item.open .faq-answer { display: block; }
    .faq-item.open .faq-toggle { transform: rotate(180deg); }
    .faq-toggle { transition: transform 0.3s; }
    
    .cta-section { text-align: center; padding: 40px; background: linear-gradient(145deg, rgba(0,224,164,0.05), rgba(139,92,246,0.05)); border-radius: 20px; margin-top: 24px; }
    .cta-btn { display: inline-block; padding: 14px 28px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 12px; font-weight: 700; color: var(--primary-dark); text-decoration: none; }
    
    @media (max-width: 768px) {
      .store-name { font-size: 1.5rem; }
      .score-value { font-size: 2rem; }
      .history-item { flex-direction: column; text-align: center; }
      .history-prize { text-align: center; }
      .timeline-win { flex-direction: column; text-align: center; }
      .action-btn { padding: 10px 12px; font-size: 0.85rem; }
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
        <a href="/로또-판매점/">판매점</a> <span>›</span>
        <a href="/로또-판매점/<?= urlencode($region1) ?>/"><?= htmlspecialchars($region1) ?></a> <span>›</span>
        <a href="/로또-판매점/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/"><?= htmlspecialchars($region2) ?></a> <span>›</span>
        <?php if ($region3): ?>
        <a href="/로또-판매점/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/<?= urlencode($region3) ?>/"><?= htmlspecialchars($region3) ?></a> <span>›</span>
        <?php endif; ?>
        <span><?= htmlspecialchars($store_name) ?></span>
      </nav>

      <!-- Store Header (강화) -->
      <header class="store-header">
        
        <!-- 명당 점수 카드 -->
        <div class="score-card">
          <div class="score-value"><?= number_format($store_score) ?>점</div>
          <div class="score-stars"><?= str_repeat('⭐', $stars) ?><?= str_repeat('☆', 5 - $stars) ?></div>
          <div class="score-label"><?= $grade_label ?></div>
          <div class="score-bar">
            <div class="score-bar-fill" style="width: <?= min(100, $store_score / 5) ?>%"></div>
          </div>
        </div>
        
        <?php if ($wins_1st > 0): ?>
        <div class="store-badge">🏆 1등 배출 명당</div>
        <?php endif; ?>
        
        <h1 class="store-name"><?= htmlspecialchars($store_name) ?></h1>
        <div class="store-address">
          <span>📍</span>
          <span><?= htmlspecialchars($address) ?></span>
        </div>
        
        <!-- 온도계 -->
        <?php if ($latest_round > 0): ?>
        <div class="temp-badge <?= $temp_class ?>">
          <?= $temp_icon ?> <?= $temp_text ?>
          <span style="opacity:0.7">(<?= $rounds_since ?>회차 전)</span>
        </div>
        <?php endif; ?>
        
        <div class="stats-row">
          <div class="stat-item">
            <div class="stat-value gold"><?= $wins_1st ?></div>
            <div class="stat-label">1등 당첨</div>
          </div>
          <div class="stat-item">
            <div class="stat-value cyan"><?= $wins_2nd ?></div>
            <div class="stat-label">2등 당첨</div>
          </div>
          <div class="stat-item">
            <div class="stat-value"><?= $wins_1st + $wins_2nd ?></div>
            <div class="stat-label">총 당첨</div>
          </div>
        </div>
        
        <!-- 액션 버튼 -->
        <div class="action-buttons">
          <button id="favoriteBtn" class="action-btn" onclick="toggleFavorite(<?= $store_id ?>)">
            <span id="favIcon">☆</span> 즐겨찾기
          </button>
          <button class="action-btn" onclick="shareKakao()">💬 공유</button>
          <button class="action-btn" onclick="copyLink()">🔗 링크</button>
        </div>
      </header>

      <!-- 당첨 패턴 분석 -->
      <?php if (!empty($win_history)): ?>
      <section class="section">
        <div class="section-header">📈 당첨 패턴 분석</div>
        <div class="section-body">
          <div class="analysis-grid">
            
            <div class="analysis-item">
              <div class="analysis-icon">💰</div>
              <div class="analysis-value"><?= format_prize($total_prize) ?></div>
              <div class="analysis-label">누적 배출 당첨금</div>
            </div>
            
            <div class="analysis-item">
              <div class="analysis-icon">🔄</div>
              <div class="analysis-value"><?= $avg_cycle > 0 ? "약 {$avg_cycle}회차" : '-' ?></div>
              <div class="analysis-label">평균 당첨 주기</div>
            </div>
            
            <div class="analysis-item">
              <div class="analysis-icon">📅</div>
              <div class="analysis-value"><?= $latest_round > 0 ? "{$latest_round}회" : '-' ?></div>
              <div class="analysis-label">마지막 당첨</div>
            </div>
            
          </div>
          
          <!-- 자동/수동 비율 -->
          <?php if ($total_wins > 0): ?>
          <div style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
              <span>🎲 자동 <?= $auto_ratio ?>%</span>
              <span>✍️ 수동 <?= 100 - $auto_ratio ?>%</span>
            </div>
            <div class="ratio-bar">
              <?php if ($auto_ratio > 0): ?>
              <div class="ratio-auto" style="width: <?= $auto_ratio ?>%"><?= $auto_count ?>회</div>
              <?php endif; ?>
              <?php if ($auto_ratio < 100): ?>
              <div class="ratio-manual" style="width: <?= 100 - $auto_ratio ?>%"><?= $manual_count ?>회</div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          
        </div>
      </section>
      <?php endif; ?>

      <!-- 연도별 당첨 타임라인 (과거 당첨번호 포함) -->
      <?php if (!empty($wins_by_year)): ?>
      <section class="section">
        <div class="section-header">🗓️ 연도별 당첨 타임라인</div>
        <div class="section-body">
          <?php foreach ($wins_by_year as $year => $year_wins): 
            $year_1st = count(array_filter($year_wins, fn($w) => $w['rank'] == 1));
            $year_2nd = count(array_filter($year_wins, fn($w) => $w['rank'] == 2));
          ?>
          <div class="timeline-item">
            <div class="timeline-year-header">
              <div class="timeline-year-label"><?= $year ?>년</div>
              <div class="timeline-year-count">
                <?php if ($year_1st > 0): ?>🥇 <?= $year_1st ?>회<?php endif; ?>
                <?php if ($year_2nd > 0): ?> 🥈 <?= $year_2nd ?>회<?php endif; ?>
              </div>
            </div>
            
            <?php foreach ($year_wins as $w): 
              $is_first = ($w['rank'] == 1);
              $prize = $is_first ? ($w['first_prize_each'] ?? 0) : ($w['second_prize_each'] ?? 0);
            ?>
            <div class="timeline-win">
              <div class="timeline-rank <?= $is_first ? 'first' : 'second' ?>">
                <?= $is_first ? '1등' : '2등' ?>
              </div>
              <div class="timeline-info">
                <div class="timeline-round">
                  <a href="/로또-당첨번호/<?= $w['draw_no'] ?>/"><?= number_format($w['draw_no']) ?>회</a>
                </div>
                <div class="timeline-date"><?= $w['draw_date'] ? date('Y.m.d', strtotime($w['draw_date'])) : '' ?></div>
              </div>
              
              <!-- 당첨번호 표시 -->
              <?php if ($w['n1']): ?>
              <div class="timeline-balls">
                <?php for ($i = 1; $i <= 6; $i++): $n = (int)$w["n{$i}"]; ?>
                <span class="mini-ball <?= get_ball_class($n) ?>"><?= $n ?></span>
                <?php endfor; ?>
                <span style="color: var(--text-muted); margin: 0 2px;">+</span>
                <span class="mini-ball <?= get_ball_class((int)$w['bonus']) ?>"><?= (int)$w['bonus'] ?></span>
              </div>
              <?php endif; ?>
              
              <?php if ($prize > 0): ?>
              <div class="timeline-prize"><?= format_prize($prize) ?></div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <!-- 당첨 이력 (상세) -->
      <?php if (!empty($win_history)): ?>
      <section class="section">
        <div class="section-header">🏆 당첨 이력 상세</div>
        <div class="section-body">
          <?php foreach ($win_history as $w): 
            $is_first = ($w['rank'] == 1);
            $type_text = ($w['win_type'] == 'auto') ? '자동' : (($w['win_type'] == 'manual') ? '수동' : '반자동');
            $type_class = ($w['win_type'] == 'manual') ? 'manual' : 'auto';
            $prize = $is_first ? ($w['first_prize_each'] ?? 0) : ($w['second_prize_each'] ?? 0);
          ?>
          <div class="history-item">
            <div class="history-rank <?= $is_first ? 'first' : 'second' ?>">
              <?= $is_first ? '1등' : '2등' ?>
            </div>
            <div class="history-info">
              <div class="history-round">
                <a href="/로또-당첨번호/<?= $w['draw_no'] ?>/"><?= number_format($w['draw_no']) ?>회</a>
              </div>
              <div class="history-date"><?= $w['draw_date'] ? date('Y.m.d', strtotime($w['draw_date'])) : '' ?></div>
            </div>
            <?php if ($w['n1']): ?>
            <div class="history-balls">
              <?php for ($i = 1; $i <= 6; $i++): $n = (int)$w["n{$i}"]; ?>
              <span class="mini-ball <?= get_ball_class($n) ?>"><?= $n ?></span>
              <?php endfor; ?>
              <span style="color: var(--text-muted); margin: 0 4px;">+</span>
              <span class="mini-ball <?= get_ball_class((int)$w['bonus']) ?>"><?= (int)$w['bonus'] ?></span>
            </div>
            <?php endif; ?>
            <?php if ($prize > 0): ?>
            <div class="history-prize"><?= format_prize($prize) ?></div>
            <?php endif; ?>
            <span class="history-type <?= $type_class ?>"><?= $type_text ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <!-- Map -->
      <section class="section">
        <div class="section-header">📍 위치</div>
        <div class="section-body">
          <div class="map-placeholder">
            <span style="font-size: 2rem;">🗺️</span>
            <span>지도 준비 중</span>
          </div>
          <div class="map-address">
            <span>📍 <?= htmlspecialchars($address) ?></span>
            <button class="copy-btn" onclick="copyAddress()">📋 주소 복사</button>
          </div>
        </div>
      </section>

      <!-- Nearby Stores -->
      <?php if (!empty($nearby_stores)): ?>
      <section class="section">
        <div class="section-header">🏪 <?= htmlspecialchars($region2) ?> 주변 명당</div>
        <div class="section-body">
          <div class="nearby-grid">
            <?php foreach ($nearby_stores as $ns): ?>
            <a href="/로또-판매점/<?= urlencode($ns['region1'] ?? $region1) ?>/<?= urlencode($ns['region2'] ?? $region2) ?>/<?= urlencode($ns['region3'] ?? '') ?>/<?= urlencode($ns['store_name']) ?>-<?= $ns['store_id'] ?>/" class="nearby-card">
              <div class="nearby-name"><?= htmlspecialchars($ns['store_name']) ?></div>
              <div class="nearby-stats">🥇 <?= $ns['wins_1st'] ?>회 · 🥈 <?= $ns['wins_2nd'] ?>회</div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- FAQ -->
      <section class="section">
        <div class="section-header">❓ 자주 묻는 질문</div>
        <div class="section-body">
          
          <div class="faq-item" onclick="this.classList.toggle('open')">
            <div class="faq-question">
              <?= htmlspecialchars($store_name) ?>은(는) 몇 번 1등이 나왔나요?
              <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
              <?= htmlspecialchars($store_name) ?>에서는 현재까지 총 <strong><?= $wins_1st ?>회</strong>의 로또 1등이 배출되었습니다. 
              2등은 <?= $wins_2nd ?>회 배출되었으며, 누적 당첨금은 약 <strong><?= format_prize($total_prize) ?></strong>입니다.
              명당 점수는 <?= number_format($store_score) ?>점으로 "<?= $grade_label ?>" 등급입니다.
            </div>
          </div>
          
          <div class="faq-item" onclick="this.classList.toggle('open')">
            <div class="faq-question">
              이 판매점의 당첨 주기는 어떻게 되나요?
              <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
              <?php if ($avg_cycle > 0): ?>
              역대 당첨 데이터 분석 결과, <?= htmlspecialchars($store_name) ?>의 평균 당첨 주기는 약 <strong><?= $avg_cycle ?>회차</strong> (약 <?= round($avg_cycle / 4.3) ?>개월)입니다.
              마지막 당첨은 <?= $rounds_since ?>회차 전(<?= $latest_round ?>회)입니다.
              <?php else: ?>
              아직 충분한 당첨 데이터가 없어 주기를 분석하기 어렵습니다.
              <?php endif; ?>
            </div>
          </div>
          
          <div class="faq-item" onclick="this.classList.toggle('open')">
            <div class="faq-question">
              자동과 수동 중 어떤 방식이 더 많이 당첨되었나요?
              <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
              <?php if ($total_wins > 0): ?>
              <?= htmlspecialchars($store_name) ?>에서의 당첨 분석 결과, 
              <strong>자동 <?= $auto_ratio ?>%</strong> (<?= $auto_count ?>회), 
              <strong>수동 <?= 100 - $auto_ratio ?>%</strong> (<?= $manual_count ?>회)로 나타났습니다.
              <?php else: ?>
              당첨 이력이 없어 분석할 수 없습니다.
              <?php endif; ?>
            </div>
          </div>
          
        </div>
      </section>

      <section class="cta-section">
        <h2 style="font-family: 'Outfit'; font-size: 1.4rem; font-weight: 800; margin-bottom: 12px;">🎯 AI가 분석한 이번주 번호</h2>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">명당에서 사기 전, AI 추천 번호를 확인하세요</p>
        <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작</a>
      </section>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>

  <script>
  // 즐겨찾기
  function toggleFavorite(storeId) {
    let favs = JSON.parse(localStorage.getItem('lotto_favorites') || '[]');
    const idx = favs.indexOf(storeId);
    const btn = document.getElementById('favoriteBtn');
    const icon = document.getElementById('favIcon');
    
    if (idx > -1) {
      favs.splice(idx, 1);
      icon.textContent = '☆';
      btn.classList.remove('favorited');
      alert('즐겨찾기에서 제거되었습니다');
    } else {
      favs.push(storeId);
      icon.textContent = '★';
      btn.classList.add('favorited');
      alert('즐겨찾기에 추가되었습니다');
    }
    localStorage.setItem('lotto_favorites', JSON.stringify(favs));
  }

  // 페이지 로드시 즐겨찾기 상태 확인
  document.addEventListener('DOMContentLoaded', function() {
    let favs = JSON.parse(localStorage.getItem('lotto_favorites') || '[]');
    if (favs.includes(<?= $store_id ?>)) {
      document.getElementById('favIcon').textContent = '★';
      document.getElementById('favoriteBtn').classList.add('favorited');
    }
  });

  // 링크 복사
  function copyLink() {
    const url = '<?= $canonical ?>';
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(() => alert('링크가 복사되었습니다!'));
    } else {
      fallbackCopy(url);
    }
  }

  // 주소 복사
  function copyAddress() {
    const addr = '<?= htmlspecialchars($address) ?>';
    if (navigator.clipboard) {
      navigator.clipboard.writeText(addr).then(() => alert('주소가 복사되었습니다!'));
    } else {
      fallbackCopy(addr);
    }
  }

  function fallbackCopy(text) {
    const ta = document.createElement('textarea');
    ta.value = text;
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    alert('복사되었습니다!');
  }

  // 카카오 공유
  function shareKakao() {
    if (typeof Kakao !== 'undefined' && Kakao.isInitialized()) {
      Kakao.Share.sendDefault({
        objectType: 'feed',
        content: {
          title: '<?= htmlspecialchars($store_name) ?> 🏆',
          description: '누적 1등 <?= $wins_1st ?>회 배출! 명당 점수 <?= $store_score ?>점 (<?= $grade_label ?>)',
          imageUrl: 'https://lottoinsight.ai/og-store.png',
          link: { mobileWebUrl: '<?= $canonical ?>', webUrl: '<?= $canonical ?>' }
        },
        buttons: [{ title: '명당 보기', link: { mobileWebUrl: '<?= $canonical ?>', webUrl: '<?= $canonical ?>' } }]
      });
    } else {
      // 카카오 SDK 미로드시 링크 복사
      copyLink();
    }
  }
  </script>
</body>
</html>

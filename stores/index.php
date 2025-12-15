<?php
/**
 * /stores/index.php - 로또 판매점/당첨점 페이지 (로또로직스 스타일)
 * 
 * URL 패턴: 
 * - /stores/ (전체 명당)
 * - /stores/서울 (지역별)
 * - /stores/서울/강남구 (세부 지역)
 */

// 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 판매점 라이브러리 로드
$store_lib = G5_PATH . '/lib/lotto_store.lib.php';
if (file_exists($store_lib)) {
    include_once($store_lib);
}

// URL 파싱
$request_uri = urldecode($_SERVER['REQUEST_URI']);
$region1 = '';
$region2 = '';

if (preg_match('/\/stores\/([^\/\?]+)\/?([^\/\?]*)/', $request_uri, $matches)) {
    $region1 = trim($matches[1] ?? '');
    $region2 = trim($matches[2] ?? '');
}

// GET 파라미터 (회차별 당첨점 조회)
$round = isset($_GET['round']) ? (int)$_GET['round'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 30;
$offset = ($page - 1) * $per_page;

// 지역 목록
$regions = [
    '서울' => ['강남구', '강동구', '강북구', '강서구', '관악구', '광진구', '구로구', '금천구', '노원구', '도봉구', '동대문구', '동작구', '마포구', '서대문구', '서초구', '성동구', '성북구', '송파구', '양천구', '영등포구', '용산구', '은평구', '종로구', '중구', '중랑구'],
    '부산' => ['강서구', '금정구', '기장군', '남구', '동구', '동래구', '부산진구', '북구', '사상구', '사하구', '서구', '수영구', '연제구', '영도구', '중구', '해운대구'],
    '대구' => ['남구', '달서구', '달성군', '동구', '북구', '서구', '수성구', '중구'],
    '인천' => ['강화군', '계양구', '남동구', '동구', '미추홀구', '부평구', '서구', '연수구', '옹진군', '중구'],
    '광주' => ['광산구', '남구', '동구', '북구', '서구'],
    '대전' => ['대덕구', '동구', '서구', '유성구', '중구'],
    '울산' => ['남구', '동구', '북구', '울주군', '중구'],
    '세종' => [],
    '경기' => ['가평군', '고양시', '과천시', '광명시', '광주시', '구리시', '군포시', '김포시', '남양주시', '동두천시', '부천시', '성남시', '수원시', '시흥시', '안산시', '안성시', '안양시', '양주시', '양평군', '여주시', '연천군', '오산시', '용인시', '의왕시', '의정부시', '이천시', '파주시', '평택시', '포천시', '하남시', '화성시'],
    '강원' => ['강릉시', '고성군', '동해시', '삼척시', '속초시', '양구군', '양양군', '영월군', '원주시', '인제군', '정선군', '철원군', '춘천시', '태백시', '평창군', '홍천군', '화천군', '횡성군'],
    '충북' => ['괴산군', '단양군', '보은군', '영동군', '옥천군', '음성군', '제천시', '증평군', '진천군', '청주시', '충주시'],
    '충남' => ['계룡시', '공주시', '금산군', '논산시', '당진시', '보령시', '부여군', '서산시', '서천군', '아산시', '예산군', '천안시', '청양군', '태안군', '홍성군'],
    '전북' => ['고창군', '군산시', '김제시', '남원시', '무주군', '부안군', '순창군', '완주군', '익산시', '임실군', '장수군', '전주시', '정읍시', '진안군'],
    '전남' => ['강진군', '고흥군', '곡성군', '광양시', '구례군', '나주시', '담양군', '목포시', '무안군', '보성군', '순천시', '신안군', '여수시', '영광군', '영암군', '완도군', '장성군', '장흥군', '진도군', '함평군', '해남군', '화순군'],
    '경북' => ['경산시', '경주시', '고령군', '구미시', '군위군', '김천시', '문경시', '봉화군', '상주시', '성주군', '안동시', '영덕군', '영양군', '영주시', '영천시', '예천군', '울릉군', '울진군', '의성군', '청도군', '청송군', '칠곡군', '포항시'],
    '경남' => ['거제시', '거창군', '고성군', '김해시', '남해군', '밀양시', '사천시', '산청군', '양산시', '의령군', '진주시', '창녕군', '창원시', '통영시', '하동군', '함안군', '함양군', '합천군'],
    '제주' => ['서귀포시', '제주시'],
];

// 데이터 조회
$stores = [];
$total_count = 0;
$page_title = '로또 당첨점';
$page_desc = '로또 1등, 2등 당첨점 조회. 전국 명당 판매점 정보.';

if ($round > 0) {
    // 회차별 당첨점
    $page_title = "로또 {$round}회 당첨점";
    $page_desc = "로또 {$round}회 1등, 2등 당첨 판매점 정보.";
    
    // DB에서 조회
    if (function_exists('sql_query')) {
        $check_table = sql_query("SHOW TABLES LIKE 'g5_lotto_store_win'", false);
        if ($check_table && sql_num_rows($check_table) > 0) {
            $res = sql_query("
                SELECT s.*, w.rank, w.win_type, w.prize_amount
                FROM g5_lotto_store_win w
                JOIN g5_lotto_store s ON s.store_id = w.store_id
                WHERE w.draw_no = {$round}
                ORDER BY w.rank ASC, s.wins_1st DESC
                LIMIT {$offset}, {$per_page}
            ");
            while ($row = sql_fetch_array($res)) {
                $stores[] = [
                    'store_id' => $row['store_id'],
                    'name' => $row['store_name'],
                    'address' => $row['address'],
                    'region1' => $row['region1'],
                    'region2' => $row['region2'],
                    'wins_1st' => $row['wins_1st'],
                    'wins_2nd' => $row['wins_2nd'],
                    'rank' => $row['rank'],
                    'win_type' => $row['win_type'],
                ];
            }
            
            $cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store_win WHERE draw_no = {$round}");
            $total_count = (int)$cnt['cnt'];
        }
    }
} elseif ($region1) {
    // 지역별
    $page_title = "로또 당첨점 - {$region1}" . ($region2 ? " {$region2}" : '');
    $page_desc = "{$region1}" . ($region2 ? " {$region2}" : '') . " 지역 로또 1등, 2등 당첨 판매점 정보.";
    
    if (function_exists('li_get_stores_by_region')) {
        $stores = li_get_stores_by_region($region1, $region2, $per_page, $offset);
        $total_count = li_count_stores_by_region($region1, $region2);
    }
} else {
    // 전체 명당
    $page_title = '로또 명당 판매점';
    $page_desc = '전국 로또 1등 당첨 명당 판매점 정보. 누적 1등 당첨 횟수 기준.';
    
    if (function_exists('li_get_top_stores')) {
        $stores = li_get_top_stores(100);
        $total_count = count($stores);
    }
}

// 데이터가 없는 경우 (DB 동기화 필요)
// 데이터 수집 명령: php cron/lotto_store_sync.php all
// 당첨점 동기화: php cron/lotto_store_sync.php 1 1202

// 페이지네이션
$total_pages = ceil($total_count / $per_page);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags -->
  <title><?= $page_title ?> | 오늘로또</title>
  <meta name="description" content="<?= $page_desc ?>">
  <meta name="keywords" content="로또 당첨점, 로또 판매점, 로또 명당, <?= $region1 ?> 로또, 1등 당첨점">
  <meta name="robots" content="index, follow">
  
  <link rel="canonical" href="https://lottoinsight.ai/stores/<?= $region1 ? urlencode($region1) . '/' : '' ?><?= $region2 ? urlencode($region2) . '/' : '' ?>">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= $page_title ?>">
  <meta property="og:description" content="<?= $page_desc ?>">

  <!-- BreadcrumbList Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "홈",
        "item": "https://lottoinsight.ai/"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "당첨점",
        "item": "https://lottoinsight.ai/stores/"
      }
      <?php if ($region1): ?>
      ,{
        "@type": "ListItem",
        "position": 3,
        "name": "<?= htmlspecialchars($region1) ?>",
        "item": "https://lottoinsight.ai/stores/<?= urlencode($region1) ?>/"
      }
      <?php endif; ?>
      <?php if ($region2): ?>
      ,{
        "@type": "ListItem",
        "position": 4,
        "name": "<?= htmlspecialchars($region2) ?>",
        "item": "https://lottoinsight.ai/stores/<?= urlencode($region1) ?>/<?= urlencode($region2) ?>/"
      }
      <?php endif; ?>
    ]
  }
  </script>

  <!-- ItemList Structured Data for Store Listings -->
  <?php if (!empty($stores)): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "<?= htmlspecialchars($page_title) ?>",
    "numberOfItems": <?= count($stores) ?>,
    "itemListElement": [
      <?php 
      $json_items = [];
      foreach (array_slice($stores, 0, 10) as $idx => $s) {
        $json_items[] = '{
          "@type": "ListItem",
          "position": ' . ($idx + 1) . ',
          "item": {
            "@type": "Store",
            "name": "' . htmlspecialchars($s['name'] ?? $s['store_name'] ?? '') . '",
            "address": "' . htmlspecialchars($s['address'] ?? '') . '"
          }
        }';
      }
      echo implode(",\n      ", $json_items);
      ?>
    ]
  }
  </script>
  <?php endif; ?>
  
  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <!-- Fonts - 한국어 가독성 최적화 (Pretendard 우선) -->
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&display=swap" rel="stylesheet"></noscript>

  <style>
    :root {
      --primary-dark: #050a15;
      --primary: #0d1526;
      --secondary: #1a2744;
      --accent-cyan: #00E0A4;
      --accent-gold: #FFD75F;
      --accent-purple: #8B5CF6;
      --text-primary: #ffffff;
      --text-secondary: #b8c5d6; /* 밝기 개선: #94a3b8 → #b8c5d6 */
      --text-muted: #9ca3af; /* WCAG AA 기준 충족 */
      --gradient-cyan: linear-gradient(135deg, #00E0A4 0%, #00D4FF 100%);
      --gradient-mesh: radial-gradient(at 40% 20%, rgba(0, 224, 164, 0.15) 0px, transparent 50%),
                       radial-gradient(at 80% 0%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
      --glass-border: rgba(255, 255, 255, 0.08);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html {
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    body {
      /* 1. 폰트 스택 - 한글 우선 */
      font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 
                   'Apple SD Gothic Neo', 'Noto Sans KR', 'Malgun Gothic',
                   'Inter', sans-serif;
      background: var(--primary-dark);
      background-image: var(--gradient-mesh);
      background-attachment: fixed;
      color: var(--text-primary);
      /* 2. 줄간격 개선 */
      line-height: 1.75;
      /* 3. 한글 단어 단위 줄바꿈 */
      word-break: keep-all;
      overflow-wrap: break-word;
      min-height: 100vh;
      text-rendering: optimizeLegibility;
    }

    /* ===== Navigation ===== */
    .nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      padding: 16px 24px;
      background: rgba(5, 10, 21, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .nav-container {
      max-width: 1100px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: inherit;
    }

    .nav-logo-icon {
      width: 36px;
      height: 36px;
      background: var(--gradient-cyan);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }

    .nav-logo-text {
      font-family: 'Pretendard', 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
      letter-spacing: -0.02em;
      background: var(--gradient-cyan);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .nav-links {
      display: flex;
      gap: 8px;
    }

    .nav-link {
      padding: 10px 18px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .nav-link:hover, .nav-link.active {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* ===== Main Content ===== */
    .main {
      max-width: 1100px;
      margin: 0 auto;
      padding: 90px 24px 60px;
    }

    /* ===== Breadcrumb ===== */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-bottom: 20px;
      flex-wrap: wrap;
      line-height: 1.6;
    }

    .breadcrumb a {
      color: var(--text-muted);
      text-decoration: none;
      transition: color 0.2s;
    }

    .breadcrumb a:hover {
      color: var(--accent-cyan);
    }

    /* ===== Hero ===== */
    .store-hero {
      text-align: center;
      margin-bottom: 32px;
    }

    .store-title {
      font-family: 'Pretendard', 'Outfit', sans-serif;
      font-size: clamp(1.8rem, 4vw, 2.5rem);
      font-weight: 800;
      letter-spacing: -0.02em;
      margin-bottom: 12px;
      line-height: 1.3;
    }

    .store-desc {
      color: var(--text-secondary);
      font-size: 1rem;
      line-height: 1.7;
      max-width: 500px;
      margin: 0 auto;
    }

    /* ===== Region Filter ===== */
    .region-filter {
      margin-bottom: 24px;
    }

    .region-main {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 12px;
    }

    .region-btn {
      padding: 10px 16px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      border-radius: 10px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .region-btn:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    .region-btn.active {
      background: rgba(0, 224, 164, 0.15);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
      font-weight: 600;
    }

    .region-sub {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      padding: 16px;
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 12px;
    }

    .region-sub-btn {
      padding: 8px 14px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 6px;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .region-sub-btn:hover, .region-sub-btn.active {
      background: rgba(0, 224, 164, 0.1);
      color: var(--accent-cyan);
    }

    /* ===== Store List ===== */
    .store-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 24px;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 24px;
      background: rgba(0, 0, 0, 0.3);
    }

    .section-title {
      font-family: 'Pretendard', 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      letter-spacing: -0.01em;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .store-count {
      color: var(--text-muted);
      font-size: 0.9rem;
      font-weight: 500;
    }

    .store-table-header {
      display: grid;
      grid-template-columns: 50px 1fr 100px 100px;
      padding: 12px 24px;
      background: rgba(0, 0, 0, 0.2);
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .store-row {
      display: grid;
      grid-template-columns: 50px 1fr 100px 100px;
      padding: 16px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      align-items: center;
      text-decoration: none;
      color: inherit;
      transition: background 0.2s ease;
    }

    .store-row:hover {
      background: rgba(0, 224, 164, 0.03);
    }

    .store-row:last-child {
      border-bottom: none;
    }

    .store-rank {
      font-family: 'Pretendard', 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      font-variant-numeric: tabular-nums;
    }

    .store-rank.top3 {
      color: var(--accent-gold);
    }

    .store-info {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .store-name {
      font-weight: 600;
      font-size: 0.95rem;
      letter-spacing: -0.01em;
    }

    .store-address {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .store-region {
      display: inline-flex;
      padding: 3px 10px;
      background: rgba(139, 92, 246, 0.15);
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--accent-purple);
      margin-right: 8px;
    }

    .store-wins {
      text-align: center;
    }

    .store-wins-count {
      font-family: 'Pretendard', 'Outfit', sans-serif;
      font-size: 1.3rem;
      font-weight: 800;
      font-variant-numeric: tabular-nums;
    }

    .store-wins-count.gold {
      color: var(--accent-gold);
    }

    .store-wins-count.cyan {
      color: var(--accent-cyan);
    }

    .store-wins-label {
      font-size: 0.75rem;
      font-weight: 500;
      color: var(--text-muted);
      margin-top: 2px;
    }

    /* 빈 상태 */
    .empty-state {
      padding: 60px;
      text-align: center;
      color: var(--text-muted);
      font-size: 0.95rem;
      line-height: 1.7;
    }

    /* ===== Pagination ===== */
    .pagination {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 24px;
    }

    .page-btn {
      padding: 10px 16px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      border-radius: 8px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .page-btn:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
    }

    .page-btn.active {
      background: var(--accent-cyan);
      color: var(--primary-dark);
      font-weight: 600;
    }

    /* ===== Quick Links ===== */
    .quick-links {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
    }

    .quick-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 20px;
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
      text-decoration: none;
      color: inherit;
      transition: all 0.3s ease;
    }

    .quick-link:hover {
      border-color: var(--accent-cyan);
      transform: translateY(-2px);
    }

    .quick-link-icon {
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 224, 164, 0.1);
      border-radius: 12px;
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .quick-link-text {
      flex: 1;
    }

    .quick-link-title {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 4px;
      letter-spacing: -0.01em;
    }

    .quick-link-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    /* ===== CTA ===== */
    .cta-section {
      text-align: center;
      padding: 40px;
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.05), rgba(139, 92, 246, 0.05));
      border-radius: 20px;
      margin-bottom: 24px;
    }

    .cta-title {
      font-family: 'Pretendard', 'Outfit', sans-serif;
      font-size: 1.4rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      margin-bottom: 12px;
    }

    .cta-desc {
      color: var(--text-secondary);
      margin-bottom: 20px;
      font-size: 0.95rem;
      line-height: 1.7;
    }

    .cta-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: var(--gradient-cyan);
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 600;
      color: var(--primary-dark);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .cta-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 20px 60px rgba(0, 224, 164, 0.3);
    }

    /* ===== Footer ===== */
    .footer {
      text-align: center;
      padding: 40px 24px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      color: var(--text-muted);
      font-size: 0.9rem;
      line-height: 1.7;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 24px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: var(--text-muted);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s;
    }

    .footer-links a:hover {
      color: var(--accent-cyan);
    }

    /* ===== 반응형 ===== */
    @media (max-width: 768px) {
      body {
        font-size: 16px;
        line-height: 1.8;
      }

      .main {
        padding: 80px 16px 40px;
      }

      .store-title {
        font-size: 1.6rem;
      }

      .store-desc {
        font-size: 0.95rem;
      }

      .store-table-header,
      .store-row {
        grid-template-columns: 40px 1fr 70px 70px;
        padding: 12px 16px;
        font-size: 0.85rem;
      }

      .store-wins-count {
        font-size: 1.1rem;
      }

      .store-name {
        font-size: 0.9rem;
      }

      .store-address {
        font-size: 0.8rem;
      }

      .nav-links {
        display: none;
      }

      .region-btn {
        padding: 8px 12px;
        font-size: 0.85rem;
      }

      .region-sub-btn {
        padding: 6px 10px;
        font-size: 0.8rem;
      }

      .quick-link {
        padding: 16px;
      }

      .quick-link-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
      }

      .quick-link-title {
        font-size: 0.9rem;
      }

      .quick-link-desc {
        font-size: 0.8rem;
      }

      .cta-section {
        padding: 32px 20px;
      }

      .cta-title {
        font-size: 1.2rem;
      }

      .cta-desc {
        font-size: 0.9rem;
      }

      .cta-btn {
        padding: 12px 24px;
        font-size: 0.95rem;
      }
    }

    @media (max-width: 480px) {
      .store-table-header,
      .store-row {
        grid-template-columns: 36px 1fr 60px 60px;
        padding: 10px 12px;
        gap: 8px;
      }

      .store-rank {
        font-size: 1rem;
      }

      .store-wins-count {
        font-size: 1rem;
      }

      .store-wins-label {
        font-size: 0.7rem;
      }

      .store-region {
        font-size: 0.7rem;
        padding: 2px 6px;
      }

      .breadcrumb {
        font-size: 0.85rem;
      }

      .page-btn {
        padding: 8px 12px;
        font-size: 0.85rem;
      }
    }

    /* ===== 접근성 ===== */
    *:focus-visible {
      outline: 3px solid var(--accent-cyan);
      outline-offset: 2px;
    }

    /* 스크린리더 전용 */
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="nav">
    <div class="nav-container">
      <a href="/" class="nav-logo">
        <div class="nav-logo-icon">🎯</div>
        <span class="nav-logo-text">오늘로또</span>
      </a>
      <div class="nav-links">
        <a href="/" class="nav-link">홈</a>
        <a href="/stores/" class="nav-link active">당첨점</a>
        <a href="/draw/latest" class="nav-link">회차별 결과</a>
        <a href="/auth.php" class="nav-link">AI 분석</a>
      </div>
    </div>
  </nav>

  <main class="main">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <a href="/">홈</a>
      <span>›</span>
      <a href="/stores/">당첨점</a>
      <?php if ($region1): ?>
        <span>›</span>
        <a href="/stores/<?= urlencode($region1) ?>/"><?= $region1 ?></a>
      <?php endif; ?>
      <?php if ($region2): ?>
        <span>›</span>
        <span><?= $region2 ?></span>
      <?php endif; ?>
      <?php if ($round > 0): ?>
        <span>›</span>
        <span><?= $round ?>회</span>
      <?php endif; ?>
    </nav>

    <!-- Hero -->
    <section class="store-hero">
      <h1 class="store-title"><?= $page_title ?></h1>
      <p class="store-desc"><?= $page_desc ?></p>
    </section>

    <!-- Quick Links -->
    <div class="quick-links">
      <a href="/draw/latest" class="quick-link">
        <div class="quick-link-icon">🎱</div>
        <div class="quick-link-text">
          <div class="quick-link-title">최신 당첨번호</div>
          <div class="quick-link-desc">이번주 당첨 결과 확인</div>
        </div>
      </a>
      <a href="/auth.php" class="quick-link">
        <div class="quick-link-icon">🎯</div>
        <div class="quick-link-text">
          <div class="quick-link-title">AI 번호 추천</div>
          <div class="quick-link-desc">10가지 알고리즘 분석</div>
        </div>
      </a>
      <a href="/algorithm.php" class="quick-link">
        <div class="quick-link-icon">📊</div>
        <div class="quick-link-text">
          <div class="quick-link-title">분석 알고리즘</div>
          <div class="quick-link-desc">AI 분석 방법 설명</div>
        </div>
      </a>
    </div>

    <!-- Region Filter -->
    <div class="region-filter">
      <div class="region-main">
        <a href="/stores/" class="region-btn <?= !$region1 ? 'active' : '' ?>">전체</a>
        <?php foreach (array_keys($regions) as $r): ?>
          <a href="/stores/<?= urlencode($r) ?>/" class="region-btn <?= $region1 == $r ? 'active' : '' ?>"><?= $r ?></a>
        <?php endforeach; ?>
      </div>
      
      <?php if ($region1 && isset($regions[$region1]) && !empty($regions[$region1])): ?>
        <div class="region-sub">
          <a href="/stores/<?= urlencode($region1) ?>/" class="region-sub-btn <?= !$region2 ? 'active' : '' ?>">전체</a>
          <?php foreach ($regions[$region1] as $r2): ?>
            <a href="/stores/<?= urlencode($region1) ?>/<?= urlencode($r2) ?>/" class="region-sub-btn <?= $region2 == $r2 ? 'active' : '' ?>"><?= $r2 ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Store List -->
    <section class="store-section">
      <div class="section-header">
        <h2 class="section-title">🏆 <?= $round > 0 ? "{$round}회 당첨점" : '명당 판매점' ?></h2>
        <span class="store-count">총 <?= number_format($total_count) ?>개</span>
      </div>
      
      <div class="store-table-header">
        <div>순위</div>
        <div>판매점</div>
        <div style="text-align: center;">1등</div>
        <div style="text-align: center;">2등</div>
      </div>
      
      <?php foreach ($stores as $i => $store): 
        $store_link = '/store/' . urlencode($store['store_name']);
        if (!empty($store['store_id'])) {
          $store_link = '/store/' . $store['store_id'];
        }
      ?>
        <a href="<?= $store_link ?>" class="store-row">
          <div class="store-rank <?= $i < 3 ? 'top3' : '' ?>"><?= $offset + $i + 1 ?></div>
          <div class="store-info">
            <div>
              <?php if (!empty($store['region1'])): ?>
                <span class="store-region"><?= $store['region1'] ?></span>
              <?php endif; ?>
              <span class="store-name"><?= htmlspecialchars($store['store_name']) ?></span>
            </div>
            <div class="store-address"><?= htmlspecialchars($store['address']) ?></div>
          </div>
          <div class="store-wins">
            <div class="store-wins-count gold"><?= $store['wins_1st'] ?? 0 ?></div>
            <div class="store-wins-label">1등</div>
          </div>
          <div class="store-wins">
            <div class="store-wins-count cyan"><?= $store['wins_2nd'] ?? 0 ?></div>
            <div class="store-wins-label">2등</div>
          </div>
        </a>
      <?php endforeach; ?>
      
      <?php if (empty($stores)): ?>
        <div style="padding: 60px; text-align: center; color: var(--text-muted);">
          해당 조건의 판매점 정보가 없습니다.
        </div>
      <?php endif; ?>
    </section>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?><?= $round ? "&round={$round}" : '' ?>" class="page-btn">← 이전</a>
        <?php endif; ?>
        
        <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
          <a href="?page=<?= $p ?><?= $round ? "&round={$round}" : '' ?>" class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>
        
        <?php if ($page < $total_pages): ?>
          <a href="?page=<?= $page + 1 ?><?= $round ? "&round={$round}" : '' ?>" class="page-btn">다음 →</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- CTA -->
    <section class="cta-section">
      <h2 class="cta-title">🎯 AI가 분석한 이번주 예상번호</h2>
      <p class="cta-desc">10가지 알고리즘이 분석한 최적의 번호 조합을 받아보세요</p>
      <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작하기</a>
    </section>
  </main>

  <?php include(G5_PATH . '/seo/_footer.php'); ?>
</body>
</html>

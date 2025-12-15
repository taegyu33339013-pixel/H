<?php
/**
 * /draw/index.php - 회차별 당첨결과 페이지 (로또로직스 스타일)
 * 
 * URL 패턴: /draw/1202
 * - 당첨번호, 공 나온 순서
 * - 당첨금 테이블 (1~5등)
 * - 1등/2등 판매점 목록
 * - 상세 번호 분석
 */

// 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 판매점 라이브러리 로드 (있으면)
$store_lib = G5_PATH . '/lib/lotto_store.lib.php';
if (file_exists($store_lib)) {
    include_once($store_lib);
}

// 회차 번호 파싱
$request_uri = $_SERVER['REQUEST_URI'];
$round = 0;

if (preg_match('/\/draw\/(\d+)/', $request_uri, $matches)) {
    $round = (int)$matches[1];
} elseif (isset($_GET['round'])) {
    $round = (int)$_GET['round'];
}

// 최신 회차 조회
$latest = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($latest['max_round'] ?? 0);

if ($round <= 0 || $round > $max_round) {
    $round = $max_round;
}

// 해당 회차 데이터 조회
$draw = sql_fetch("SELECT * FROM g5_lotto_draw WHERE draw_no = {$round}");

if (!$draw) {
    header("HTTP/1.0 404 Not Found");
    echo "해당 회차 정보를 찾을 수 없습니다.";
    exit;
}

// 당첨번호 배열
$numbers = [
    (int)$draw['n1'],
    (int)$draw['n2'],
    (int)$draw['n3'],
    (int)$draw['n4'],
    (int)$draw['n5'],
    (int)$draw['n6']
];
$bonus = (int)$draw['bonus'];
$draw_date = $draw['draw_date'] ?? '';

// 당첨금 정보
$total_sales = (int)($draw['total_sales'] ?? 0);
$first_winners = (int)($draw['first_winners'] ?? 0);
$first_prize_each = (int)($draw['first_prize_each'] ?? 0);
$second_winners = (int)($draw['second_winners'] ?? 0);
$second_prize_each = (int)($draw['second_prize_each'] ?? 0);
$third_winners = (int)($draw['third_winners'] ?? 0);
$third_prize_each = (int)($draw['third_prize_each'] ?? 0);

// 4등, 5등 (고정금액)
$fourth_prize = 50000;
$fifth_prize = 5000;

// 공 색상 함수
function get_ball_color($n) {
    if ($n <= 10) return 'yellow';
    if ($n <= 20) return 'blue';
    if ($n <= 30) return 'red';
    if ($n <= 40) return 'gray';
    return 'green';
}

// ===== 번호 분석 함수들 =====

// AC값 계산
function calc_ac($nums) {
    sort($nums);
    $diffs = [];
    for ($i = 0; $i < 6; $i++) {
        for ($j = $i + 1; $j < 6; $j++) {
            $diffs[$nums[$j] - $nums[$i]] = true;
        }
    }
    return count($diffs) - 5;
}

// 홀짝
function count_odd($nums) {
    $odd = 0;
    foreach ($nums as $n) {
        if ($n % 2 == 1) $odd++;
    }
    return $odd;
}

// 고저 (23 이상이 높은 수)
function count_high($nums) {
    $high = 0;
    foreach ($nums as $n) {
        if ($n >= 23) $high++;
    }
    return $high;
}

// 소수 개수
function count_prime($nums) {
    $primes = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43];
    $count = 0;
    foreach ($nums as $n) {
        if (in_array($n, $primes)) $count++;
    }
    return $count;
}

// 합성수 (1과 자신 외의 약수가 있는 수, 1 제외, 소수 제외)
function count_composite($nums) {
    $primes = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43];
    $count = 0;
    foreach ($nums as $n) {
        if ($n > 1 && !in_array($n, $primes)) $count++;
    }
    return $count;
}

// 연속번호 쌍 개수
function count_consecutive($nums) {
    sort($nums);
    $count = 0;
    for ($i = 0; $i < 5; $i++) {
        if ($nums[$i + 1] - $nums[$i] == 1) $count++;
    }
    return $count;
}

// 최대 연속번호 길이
function max_consecutive($nums) {
    sort($nums);
    $max = 0;
    $current = 1;
    for ($i = 0; $i < 5; $i++) {
        if ($nums[$i + 1] - $nums[$i] == 1) {
            $current++;
            $max = max($max, $current);
        } else {
            $current = 1;
        }
    }
    return $max > 1 ? $max : 0;
}

// 완전제곱수 (1, 4, 9, 16, 25, 36)
function count_perfect_square($nums) {
    $squares = [1, 4, 9, 16, 25, 36];
    $count = 0;
    foreach ($nums as $n) {
        if (in_array($n, $squares)) $count++;
    }
    return $count;
}

// 모서리수 (로또 용지 기준)
function count_corner($nums) {
    $corners = [1, 7, 8, 14, 15, 21, 22, 28, 29, 35, 36, 42, 43, 45];
    $count = 0;
    foreach ($nums as $n) {
        if (in_array($n, $corners)) $count++;
    }
    return $count;
}

// N배수 개수
function count_multiple($nums, $n) {
    $count = 0;
    foreach ($nums as $num) {
        if ($num % $n == 0) $count++;
    }
    return $count;
}

// 전멸번호대 (10단위 구간 중 하나도 없는 구간 수)
function count_empty_range($nums) {
    $ranges = [0, 0, 0, 0, 0]; // 1-10, 11-20, 21-30, 31-40, 41-45
    foreach ($nums as $n) {
        if ($n <= 10) $ranges[0]++;
        elseif ($n <= 20) $ranges[1]++;
        elseif ($n <= 30) $ranges[2]++;
        elseif ($n <= 40) $ranges[3]++;
        else $ranges[4]++;
    }
    $empty = 0;
    foreach ($ranges as $r) {
        if ($r == 0) $empty++;
    }
    return $empty;
}

// 가로라인 (로또 용지 7열 기준)
function count_horizontal_lines($nums) {
    $lines = [];
    foreach ($nums as $n) {
        $line = ceil($n / 7);
        $lines[$line] = true;
    }
    return count($lines);
}

// 세로라인 (로또 용지 7열 기준)
function count_vertical_lines($nums) {
    $lines = [];
    foreach ($nums as $n) {
        $line = (($n - 1) % 7) + 1;
        $lines[$line] = true;
    }
    return count($lines);
}

// 총합
function calc_sum($nums) {
    return array_sum($nums);
}

// 단수합 (각 자릿수의 합을 한 자리가 될 때까지)
function calc_digit_sum($nums) {
    $sum = array_sum($nums);
    while ($sum >= 10) {
        $sum = array_sum(str_split($sum));
    }
    return $sum;
}

// 시작수합 (십의 자리 합)
function calc_tens_sum($nums) {
    $sum = 0;
    foreach ($nums as $n) {
        $sum += floor($n / 10);
    }
    return $sum;
}

// 끝수합 (일의 자리 합)
function calc_ones_sum($nums) {
    $sum = 0;
    foreach ($nums as $n) {
        $sum += $n % 10;
    }
    return $sum;
}

// 번호 간격합
function calc_gap_sum($nums) {
    sort($nums);
    $sum = 0;
    for ($i = 0; $i < 5; $i++) {
        $sum += $nums[$i + 1] - $nums[$i];
    }
    return $sum;
}

// 첫끝합
function calc_first_last_sum($nums) {
    sort($nums);
    return $nums[0] + $nums[5];
}

// 일련번호 (6자리 숫자를 이어붙인 것)
function calc_serial($nums) {
    $sorted = $nums;
    sort($sorted);
    return implode('', array_map(function($n) { return str_pad($n, 1, '0', STR_PAD_LEFT); }, $sorted));
}

// ===== 분석 계산 =====
$analysis = [
    'ac' => calc_ac($numbers),
    'even' => 6 - count_odd($numbers),
    'high' => count_high($numbers),
    'prime' => count_prime($numbers),
    'composite' => count_composite($numbers),
    'consecutive' => count_consecutive($numbers),
    'max_consecutive' => max_consecutive($numbers),
    'perfect_square' => count_perfect_square($numbers),
    'corner' => count_corner($numbers),
    'multiple_3' => count_multiple($numbers, 3),
    'multiple_4' => count_multiple($numbers, 4),
    'multiple_5' => count_multiple($numbers, 5),
    'empty_range' => count_empty_range($numbers),
    'horizontal' => count_horizontal_lines($numbers),
    'vertical' => count_vertical_lines($numbers),
    'sum' => calc_sum($numbers),
    'digit_sum' => calc_digit_sum($numbers),
    'tens_sum' => calc_tens_sum($numbers),
    'ones_sum' => calc_ones_sum($numbers),
    'gap_sum' => calc_gap_sum($numbers),
    'first_last_sum' => calc_first_last_sum($numbers),
    'serial' => calc_serial($numbers),
];

// 이전/다음 회차
$prev_round = $round > 1 ? $round - 1 : null;
$next_round = $round < $max_round ? $round + 1 : null;

// 1등 당첨점 샘플 데이터 (실제로는 DB에서 조회)
$winning_stores_1st = [];
$winning_stores_2nd = [];

// DB에서 당첨점 조회 시도
if (function_exists('sql_query')) {
    $check_table = sql_query("SHOW TABLES LIKE 'g5_lotto_store_win'", false);
    if ($check_table && sql_num_rows($check_table) > 0) {
        // 1등 당첨점
        $res = sql_query("
            SELECT s.*, w.win_type 
            FROM g5_lotto_store_win w 
            JOIN g5_lotto_store s ON s.store_id = w.store_id 
            WHERE w.draw_no = {$round} AND w.rank = 1
            ORDER BY s.wins_1st DESC
        ");
        while ($row = sql_fetch_array($res)) {
            $winning_stores_1st[] = $row;
        }
        
        // 2등 당첨점
        $res = sql_query("
            SELECT s.*, w.win_type 
            FROM g5_lotto_store_win w 
            JOIN g5_lotto_store s ON s.store_id = w.store_id 
            WHERE w.draw_no = {$round} AND w.rank = 2
            ORDER BY s.wins_1st DESC
            LIMIT 20
        ");
        while ($row = sql_fetch_array($res)) {
            $winning_stores_2nd[] = $row;
        }
    }
}

// 실제 DB 데이터 사용 - 당첨점 동기화 필요: php cron/lotto_store_sync.php {회차}
// li_get_draw_winning_stores 함수 사용
if (empty($winning_stores_1st) && function_exists('li_get_draw_winning_stores')) {
    $winning_stores_1st = li_get_draw_winning_stores($round, 1);
}
if (empty($winning_stores_2nd) && function_exists('li_get_draw_winning_stores')) {
    $winning_stores_2nd = li_get_draw_winning_stores($round, 2);
}

// SEO
$numbers_str = implode(', ', $numbers);
$page_title = "로또 {$round}회 당첨번호 - {$numbers_str}";
$page_desc = "로또 {$round}회 당첨번호 {$numbers_str} + 보너스 {$bonus}. " . ($draw_date ? date('Y년 n월 j일', strtotime($draw_date)) : '') . " 추첨. 1등 {$first_winners}명, 당첨금 " . number_format($first_prize_each) . "원. AC값 {$analysis['ac']}, 총합 {$analysis['sum']}.";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags -->
  <title><?= $page_title ?> | 오늘로또</title>
  <meta name="description" content="<?= $page_desc ?>">
  <meta name="keywords" content="로또 <?= $round ?>회, 로또 당첨번호, <?= implode(' ', $numbers) ?>, 로또 분석, 로또 결과, <?= $round ?>회 당첨점">
  <meta name="robots" content="index, follow">
  
  <link rel="canonical" href="https://lottoinsight.ai/draw/<?= $round ?>">
  
  <!-- Open Graph -->
  <meta property="og:type" content="article">
  <meta property="og:url" content="https://lottoinsight.ai/draw/<?= $round ?>">
  <meta property="og:title" content="<?= $page_title ?>">
  <meta property="og:description" content="<?= $page_desc ?>">
  
  <!-- Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "로또 <?= $round ?>회 당첨번호",
    "description": "<?= $page_desc ?>",
    "datePublished": "<?= $draw_date ?>",
    "author": {"@type": "Organization", "name": "오늘로또"}
  }
  </script>

  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
      --gradient-cyan: linear-gradient(135deg, #00E0A4 0%, #00D4FF 100%);
      --gradient-mesh: radial-gradient(at 40% 20%, rgba(0, 224, 164, 0.15) 0px, transparent 50%),
                       radial-gradient(at 80% 0%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
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
      background-image: var(--gradient-mesh);
      background-attachment: fixed;
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }

    /* Navigation */
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
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
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
      font-size: 0.85rem;
      transition: all 0.3s ease;
    }

    .nav-link:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* Main Content */
    .main {
      max-width: 1100px;
      margin: 0 auto;
      padding: 90px 24px 60px;
    }

    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-bottom: 20px;
    }

    .breadcrumb a {
      color: var(--text-muted);
      text-decoration: none;
    }

    .breadcrumb a:hover {
      color: var(--accent-cyan);
    }

    /* Round Nav */
    .round-nav {
      display: flex;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .round-nav-btn {
      padding: 10px 20px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      border-radius: 10px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .round-nav-btn:hover {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    .round-nav-btn.disabled {
      opacity: 0.4;
      pointer-events: none;
    }

    /* Hero */
    .draw-hero {
      text-align: center;
      margin-bottom: 32px;
    }

    .draw-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.8rem, 4vw, 2.5rem);
      font-weight: 900;
      margin-bottom: 8px;
    }

    .draw-date {
      color: var(--text-muted);
      font-size: 1rem;
    }

    /* Numbers Card */
    .numbers-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 32px;
      margin-bottom: 24px;
    }

    .numbers-main {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .ball {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.4rem;
      color: #fff;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3), inset 0 -3px 8px rgba(0, 0, 0, 0.2);
    }

    .ball.yellow { background: var(--ball-yellow); }
    .ball.blue { background: var(--ball-blue); }
    .ball.red { background: var(--ball-red); }
    .ball.gray { background: var(--ball-gray); }
    .ball.green { background: var(--ball-green); }

    .ball-plus {
      font-size: 1.8rem;
      color: var(--text-muted);
    }

    .ball.bonus {
      position: relative;
    }

    .ball.bonus::after {
      content: 'BONUS';
      position: absolute;
      bottom: -22px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.6rem;
      font-weight: 700;
      color: var(--accent-gold);
    }

    .ball-order {
      text-align: center;
      padding-top: 16px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      margin-top: 16px;
    }

    .ball-order-label {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-bottom: 12px;
    }

    .ball-order-balls {
      display: flex;
      justify-content: center;
      gap: 8px;
    }

    .mini-ball {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.85rem;
      color: #fff;
    }

    /* Prize Table */
    .prize-section {
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
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .section-link {
      color: var(--accent-cyan);
      text-decoration: none;
      font-size: 0.85rem;
    }

    .prize-table {
      width: 100%;
    }

    .prize-table-header {
      display: grid;
      grid-template-columns: 80px 1fr 1fr;
      padding: 12px 24px;
      background: rgba(0, 0, 0, 0.2);
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
    }

    .prize-row {
      display: grid;
      grid-template-columns: 80px 1fr 1fr;
      padding: 14px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      align-items: center;
    }

    .prize-row:last-child {
      border-bottom: none;
    }

    .prize-rank {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
    }

    .prize-rank.gold { color: var(--accent-gold); }
    .prize-rank.cyan { color: var(--accent-cyan); }

    .prize-winners {
      text-align: right;
      padding-right: 24px;
    }

    .prize-amount {
      text-align: right;
      font-family: 'Outfit', sans-serif;
      font-weight: 600;
    }

    .prize-total {
      padding: 16px 24px;
      background: rgba(0, 224, 164, 0.05);
      text-align: right;
      font-weight: 600;
    }

    /* Store Tabs */
    .store-tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
    }

    .store-tab {
      padding: 12px 24px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      border-radius: 10px;
      color: var(--text-secondary);
      cursor: pointer;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .store-tab:hover {
      background: rgba(0, 224, 164, 0.1);
    }

    .store-tab.active {
      background: rgba(0, 224, 164, 0.15);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
      font-weight: 600;
    }

    /* Store List */
    .store-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 24px;
    }

    .store-table-header {
      display: grid;
      grid-template-columns: 50px 1fr 80px 80px;
      padding: 12px 24px;
      background: rgba(0, 0, 0, 0.3);
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
    }

    .store-row {
      display: grid;
      grid-template-columns: 50px 1fr 80px 80px;
      padding: 14px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      align-items: center;
      text-decoration: none;
      color: inherit;
      transition: background 0.2s ease;
    }

    .store-row:hover {
      background: rgba(0, 224, 164, 0.05);
    }

    .store-no {
      font-family: 'Outfit', sans-serif;
      font-weight: 600;
      color: var(--text-muted);
    }

    .store-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .store-type-badge {
      display: inline-flex;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.7rem;
      font-weight: 600;
      margin-right: 8px;
    }

    .store-type-badge.auto {
      background: rgba(0, 224, 164, 0.15);
      color: var(--accent-cyan);
    }

    .store-type-badge.manual {
      background: rgba(255, 215, 95, 0.15);
      color: var(--accent-gold);
    }

    .store-name {
      font-weight: 600;
    }

    .store-address {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .store-wins {
      text-align: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 600;
    }

    .store-wins.gold { color: var(--accent-gold); }

    /* Analysis Section */
    .analysis-section {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 24px;
    }

    .analysis-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1px;
      background: rgba(255, 255, 255, 0.04);
    }

    .analysis-group {
      background: var(--primary);
      padding: 20px;
    }

    .analysis-group-title {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .analysis-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .analysis-row:last-child {
      border-bottom: none;
    }

    .analysis-label {
      color: var(--text-secondary);
      font-size: 0.9rem;
    }

    .analysis-value {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    /* CTA Section */
    .cta-section {
      text-align: center;
      padding: 40px;
      background: linear-gradient(145deg, rgba(0, 224, 164, 0.05), rgba(139, 92, 246, 0.05));
      border-radius: 20px;
      margin-bottom: 24px;
    }

    .cta-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.4rem;
      font-weight: 800;
      margin-bottom: 12px;
    }

    .cta-desc {
      color: var(--text-secondary);
      margin-bottom: 20px;
    }

    .cta-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: var(--gradient-cyan);
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .cta-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 20px 60px rgba(0, 224, 164, 0.3);
    }

    /* Footer */
    .footer {
      text-align: center;
      padding: 40px 24px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      color: var(--text-muted);
      font-size: 0.85rem;
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
    }

    .footer-links a:hover {
      color: var(--accent-cyan);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .ball {
        width: 48px;
        height: 48px;
        font-size: 1.1rem;
      }

      .numbers-main {
        gap: 8px;
      }

      .prize-table-header,
      .prize-row {
        grid-template-columns: 60px 1fr 1fr;
        padding: 12px 16px;
        font-size: 0.85rem;
      }

      .store-table-header,
      .store-row {
        grid-template-columns: 40px 1fr 60px 60px;
        padding: 12px 16px;
        font-size: 0.85rem;
      }

      .analysis-grid {
        grid-template-columns: 1fr;
      }

      .nav-links {
        display: none;
      }
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
        <a href="/stores/" class="nav-link">당첨점</a>
        <a href="/algorithm.php" class="nav-link">분석 알고리즘</a>
        <a href="/auth.php" class="nav-link">AI 분석</a>
      </div>
    </div>
  </nav>

  <main class="main">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <a href="/">홈</a>
      <span>›</span>
      <a href="/draw/<?= $max_round ?>">회차별 당첨번호</a>
      <span>›</span>
      <span><?= $round ?>회</span>
    </nav>

    <!-- Round Navigation -->
    <div class="round-nav">
      <?php if ($prev_round): ?>
        <a href="/draw/<?= $prev_round ?>" class="round-nav-btn">← <?= $prev_round ?>회</a>
      <?php else: ?>
        <span class="round-nav-btn disabled">← 이전</span>
      <?php endif; ?>
      <?php if ($next_round): ?>
        <a href="/draw/<?= $next_round ?>" class="round-nav-btn"><?= $next_round ?>회 →</a>
      <?php else: ?>
        <span class="round-nav-btn disabled">다음 →</span>
      <?php endif; ?>
    </div>

    <!-- Hero -->
    <section class="draw-hero">
      <h1 class="draw-title">로또 <?= number_format($round) ?>회 당첨번호</h1>
      <p class="draw-date"><?= $draw_date ? date('Y년 n월 j일', strtotime($draw_date)) : '' ?> 추첨</p>
    </section>

    <!-- Numbers Card -->
    <section class="numbers-card">
      <div class="numbers-main">
        <?php foreach ($numbers as $n): ?>
          <div class="ball <?= get_ball_color($n) ?>"><?= $n ?></div>
        <?php endforeach; ?>
        <span class="ball-plus">+</span>
        <div class="ball <?= get_ball_color($bonus) ?> bonus"><?= $bonus ?></div>
      </div>

      <div class="ball-order">
        <div class="ball-order-label">공 나온 순서</div>
        <div class="ball-order-balls">
          <?php 
          // 실제 순서가 있다면 사용, 없으면 정렬된 순서 표시
          foreach ($numbers as $n): ?>
            <div class="mini-ball" style="background: var(--ball-<?= get_ball_color($n) ?>)"><?= $n ?></div>
          <?php endforeach; ?>
          <span style="color: var(--text-muted); padding: 0 4px;">+</span>
          <div class="mini-ball" style="background: var(--ball-<?= get_ball_color($bonus) ?>)"><?= $bonus ?></div>
        </div>
      </div>
    </section>

    <!-- Prize Table -->
    <section class="prize-section">
      <div class="section-header">
        <h2 class="section-title">🏆 로또 <?= $round ?>회 당첨금</h2>
        <a href="/stores/?round=<?= $round ?>" class="section-link">당첨점 조회 →</a>
      </div>
      
      <div class="prize-table">
        <div class="prize-table-header">
          <div>순위</div>
          <div style="text-align: right; padding-right: 24px;">당첨 수</div>
          <div style="text-align: right;">당첨금액</div>
        </div>
        <div class="prize-row">
          <div class="prize-rank gold">1등</div>
          <div class="prize-winners"><?= number_format($first_winners) ?> 개</div>
          <div class="prize-amount"><?= number_format($first_prize_each) ?> 원</div>
        </div>
        <div class="prize-row">
          <div class="prize-rank cyan">2등</div>
          <div class="prize-winners"><?= number_format($second_winners) ?> 개</div>
          <div class="prize-amount"><?= number_format($second_prize_each) ?> 원</div>
        </div>
        <div class="prize-row">
          <div class="prize-rank">3등</div>
          <div class="prize-winners"><?= number_format($third_winners) ?> 개</div>
          <div class="prize-amount"><?= number_format($third_prize_each) ?> 원</div>
        </div>
        <div class="prize-row">
          <div class="prize-rank">4등</div>
          <div class="prize-winners">-</div>
          <div class="prize-amount"><?= number_format($fourth_prize) ?> 원</div>
        </div>
        <div class="prize-row">
          <div class="prize-rank">5등</div>
          <div class="prize-winners">-</div>
          <div class="prize-amount"><?= number_format($fifth_prize) ?> 원</div>
        </div>
      </div>
      
      <div class="prize-total">
        총판매액 : <?= number_format($total_sales) ?> 원
      </div>
    </section>

    <!-- Store Tabs -->
    <div class="store-tabs">
      <div class="store-tab active" onclick="showStoreTab('1st')">1등 판매점</div>
      <div class="store-tab" onclick="showStoreTab('2nd')">2등 판매점</div>
    </div>

    <!-- 1등 당첨점 -->
    <section class="store-section" id="store-1st">
      <div class="section-header">
        <h2 class="section-title">🥇 로또 <?= $round ?>회 1등 판매점</h2>
        <a href="/stores/" class="section-link">전체 명당 보기 →</a>
      </div>
      
      <div class="store-table-header">
        <div>No</div>
        <div>판매점</div>
        <div style="text-align: center;">누적 1등</div>
        <div style="text-align: center;">누적 2등</div>
      </div>
      
      <?php foreach ($winning_stores_1st as $i => $store): 
        $store_link = '/store/' . urlencode($store['store_name']);
      ?>
        <a href="<?= $store_link ?>" class="store-row">
          <div class="store-no"><?= $i + 1 ?></div>
          <div class="store-info">
            <div>
              <span class="store-type-badge <?= ($store['win_type'] ?? 'auto') == 'manual' ? 'manual' : 'auto' ?>">
                <?= ($store['win_type'] ?? 'auto') == 'manual' ? '수동' : '자동' ?>
              </span>
              <span class="store-name"><?= htmlspecialchars($store['store_name']) ?></span>
            </div>
            <div class="store-address"><?= htmlspecialchars($store['address']) ?></div>
          </div>
          <div class="store-wins gold"><?= $store['wins_1st'] ?>명</div>
          <div class="store-wins"><?= $store['wins_2nd'] ?>명</div>
        </a>
      <?php endforeach; ?>
      
      <?php if (empty($winning_stores_1st)): ?>
        <div style="padding: 40px; text-align: center; color: var(--text-muted);">
          당첨점 정보가 없습니다.
        </div>
      <?php endif; ?>
    </section>

    <!-- 2등 당첨점 (숨김) -->
    <section class="store-section" id="store-2nd" style="display: none;">
      <div class="section-header">
        <h2 class="section-title">🥈 로또 <?= $round ?>회 2등 판매점</h2>
      </div>
      
      <div class="store-table-header">
        <div>No</div>
        <div>판매점</div>
        <div style="text-align: center;">누적 1등</div>
        <div style="text-align: center;">누적 2등</div>
      </div>
      
      <?php if (!empty($winning_stores_2nd)): ?>
        <?php foreach ($winning_stores_2nd as $i => $store): ?>
          <div class="store-row">
            <div class="store-no"><?= $i + 1 ?></div>
            <div class="store-info">
              <div class="store-name"><?= htmlspecialchars($store['store_name']) ?></div>
              <div class="store-address"><?= htmlspecialchars($store['address']) ?></div>
            </div>
            <div class="store-wins gold"><?= $store['wins_1st'] ?>명</div>
            <div class="store-wins"><?= $store['wins_2nd'] ?>명</div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="padding: 40px; text-align: center; color: var(--text-muted);">
          2등 당첨점 정보가 없습니다.
        </div>
      <?php endif; ?>
    </section>

    <!-- Analysis Section -->
    <section class="analysis-section">
      <div class="section-header">
        <h2 class="section-title">📊 <?= $round ?>회 로또 번호 분석</h2>
      </div>
      
      <div class="analysis-grid">
        <!-- 기본 -->
        <div class="analysis-group">
          <div class="analysis-group-title">기본</div>
          <div class="analysis-row">
            <span class="analysis-label">AC</span>
            <span class="analysis-value"><?= $analysis['ac'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">짝수</span>
            <span class="analysis-value"><?= $analysis['even'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">높은수</span>
            <span class="analysis-value"><?= $analysis['high'] ?></span>
          </div>
        </div>

        <!-- 숫자 -->
        <div class="analysis-group">
          <div class="analysis-group-title">숫자</div>
          <div class="analysis-row">
            <span class="analysis-label">소수</span>
            <span class="analysis-value"><?= $analysis['prime'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">합성수</span>
            <span class="analysis-value"><?= $analysis['composite'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">연속번호</span>
            <span class="analysis-value"><?= $analysis['consecutive'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">최대 연속번호</span>
            <span class="analysis-value"><?= $analysis['max_consecutive'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">완전제곱수</span>
            <span class="analysis-value"><?= $analysis['perfect_square'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">모서리수</span>
            <span class="analysis-value"><?= $analysis['corner'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">3배수</span>
            <span class="analysis-value"><?= $analysis['multiple_3'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">4배수</span>
            <span class="analysis-value"><?= $analysis['multiple_4'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">5배수</span>
            <span class="analysis-value"><?= $analysis['multiple_5'] ?></span>
          </div>
        </div>

        <!-- 분할/패턴 -->
        <div class="analysis-group">
          <div class="analysis-group-title">분할/패턴</div>
          <div class="analysis-row">
            <span class="analysis-label">전멸번호대</span>
            <span class="analysis-value"><?= $analysis['empty_range'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">가로라인</span>
            <span class="analysis-value"><?= $analysis['horizontal'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">세로라인</span>
            <span class="analysis-value"><?= $analysis['vertical'] ?></span>
          </div>
        </div>

        <!-- 합계 -->
        <div class="analysis-group">
          <div class="analysis-group-title">합계</div>
          <div class="analysis-row">
            <span class="analysis-label">총합</span>
            <span class="analysis-value"><?= $analysis['sum'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">단수합</span>
            <span class="analysis-value"><?= $analysis['digit_sum'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">시작수합</span>
            <span class="analysis-value"><?= $analysis['tens_sum'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">끝수합</span>
            <span class="analysis-value"><?= $analysis['ones_sum'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">번호 간격합</span>
            <span class="analysis-value"><?= $analysis['gap_sum'] ?></span>
          </div>
          <div class="analysis-row">
            <span class="analysis-label">첫끝합</span>
            <span class="analysis-value"><?= $analysis['first_last_sum'] ?></span>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
      <h2 class="cta-title">🎯 이번주 AI 예상번호 받기</h2>
      <p class="cta-desc">10가지 분석 알고리즘으로 최적의 번호 조합을 추천받으세요</p>
      <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작하기</a>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-links">
      <a href="/">홈</a>
      <a href="/stores/">당첨점</a>
      <a href="/algorithm.php">분석 알고리즘</a>
      <a href="/terms.html">이용약관</a>
      <a href="/privacy.html">개인정보처리방침</a>
    </div>
    <p>© <?= date('Y') ?> 오늘로또. 통계 분석은 참고용이며 당첨을 보장하지 않습니다.</p>
  </footer>

  <script>
    function showStoreTab(tab) {
      document.querySelectorAll('.store-tab').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.store-section').forEach(el => el.style.display = 'none');
      
      if (tab === '1st') {
        document.querySelectorAll('.store-tab')[0].classList.add('active');
        document.getElementById('store-1st').style.display = 'block';
      } else {
        document.querySelectorAll('.store-tab')[1].classList.add('active');
        document.getElementById('store-2nd').style.display = 'block';
      }
    }
  </script>
</body>
</html>

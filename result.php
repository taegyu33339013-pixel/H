<?php
// 디버그용(꼭 한 번은 켜서 에러 내용 확인해 보세요. 작업 끝나면 주석 처리)
@ini_set('display_errors', 1);
@ini_set('display_startup_errors', 1);
@error_reporting(E_ALL);

// ─────────────────────────────────────
// GNUBOARD 공통파일 로드
// ─────────────────────────────────────
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';

    if (file_exists($common_path)) {
        include_once($common_path);
    } else {
        // 혹시 루트가 아닐 경우 대비
        include_once(__DIR__ . '/common.php');
    }
}

// 공통이 제대로 안 들어왔을 경우 바로 중단
if (!defined('G5_PATH')) {
    die('G5_PATH가 정의되지 않았습니다. common.php 로드에 실패했습니다.');
}

include_once(G5_LIB_PATH . '/lotto_draw.lib.php');
include_once(G5_LIB_PATH . '/lotto_credit.lib.php');

global $member, $g5;

// ─────────────────────────────────────
// 로그인 체크
// ─────────────────────────────────────
if (empty($is_member)) {
    alert(
        '로그인 후 이용 가능한 서비스입니다.',
        G5_BBS_URL . '/login.php?url=' . urlencode($_SERVER['REQUEST_URI'])
    );
}

// ─────────────────────────────────────
// 현재 회차 및 최근 회차 데이터 로딩
// ─────────────────────────────────────

// 기본값 초기화
$current_round     = 0;      // 분석 대상 "다음 회차" (예측 대상)
$current_round_fmt = '---';
$latest            = null;   // DB 기준 최신 추첨 회차(이미 발표된 회차)
$latest_draw_no    = 0;      // DB에 들어있는 마지막 draw_no

// ✅ 실제 추첨이 끝난 "최신 회차" (LIVE 표시용)
$latest_round      = 0;
$latest_round_fmt  = '---';

// ▶ 최신 회차 1건 (테이블 없을 때 500 안 나게 오류 무시 모드)
$sql_latest  = "SELECT * FROM g5_lotto_draw ORDER BY draw_no DESC LIMIT 1";
$res_latest  = sql_query($sql_latest, false); // 두 번째 인자 false = 에러 발생해도 죽지 않음

if ($res_latest && sql_num_rows($res_latest)) {
    $latest         = sql_fetch_array($res_latest);
    $latest_draw_no = (int)$latest['draw_no'];           // 예: 1201

    // ✅ LIVE/당첨 결과: 실제 최신 회차
    $latest_round     = $latest_draw_no;
    $latest_round_fmt = $latest_round ? number_format($latest_round) : '---';

    // ✅ 예측/AI 분석 대상: 다음 회차
    $current_round     = $latest_draw_no + 1;            // 예: 1202
    $current_round_fmt = $current_round ? number_format($current_round) : '---';
}

// ▶ 최근 N회 (통계/히스토리용)
$recent_limit = 120;
$history_rows = [];

$sql_history = "SELECT * FROM g5_lotto_draw ORDER BY draw_no DESC LIMIT {$recent_limit}";
$res_history = sql_query($sql_history, false);

if ($res_history) {
    while ($row = sql_fetch_array($res_history)) {
        $history_rows[] = $row;
    }
}

// ▶ JS에서 쓸 LOTTO_HISTORY_DATA 생성
$lotto_history_map = [];
foreach ($history_rows as $row) {
    $round = (int)$row['draw_no'];

    $lotto_history_map[$round] = [
        'date'    => $row['draw_date'],
        'numbers' => [
            (int)$row['n1'],
            (int)$row['n2'],
            (int)$row['n3'],
            (int)$row['n4'],
            (int)$row['n5'],
            (int)$row['n6'],
        ],
        'bonus'   => (int)$row['bonus'],
    ];
}

// ─────────────────────────────────────
// 최근 AI 추천 아카이브 (g5_lotto_ai_recommend)
// ─────────────────────────────────────
$ai_archive_rows    = [];
$ai_archive_summary = [
    'avg_match'   => 0,
    'best_match'  => 0,
    'total_weeks' => 0,
];

// 최근 8회 AI 추천 + 실제 당첨번호 JOIN
$archive_sql = "
    SELECT
        ai.round,
        ai.a1, ai.a2, ai.a3, ai.a4, ai.a5, ai.a6,
        dr.n1, dr.n2, dr.n3, dr.n4, dr.n5, dr.n6,
        dr.bonus,
        dr.draw_date
    FROM g5_lotto_ai_recommend AS ai
    JOIN g5_lotto_draw AS dr
      ON dr.draw_no = ai.round
    ORDER BY ai.round DESC
    LIMIT 8
";

$archive_res   = sql_query($archive_sql, false);
$total_rows    = 0;
$sum_matches   = 0;
$best_match    = 0;

if ($archive_res) {
    while ($row = sql_fetch_array($archive_res)) {
        $total_rows++;

        $ai_nums   = [(int)$row['a1'], (int)$row['a2'], (int)$row['a3'], (int)$row['a4'], (int)$row['a5'], (int)$row['a6']];
        $real_nums = [(int)$row['n1'], (int)$row['n2'], (int)$row['n3'], (int)$row['n4'], (int)$row['n5'], (int)$row['n6']];

        sort($ai_nums);
        sort($real_nums);

        $matched    = array_values(array_intersect($ai_nums, $real_nums));
        $match_cnt  = count($matched);
        $sum_matches += $match_cnt;
        if ($match_cnt > $best_match) $best_match = $match_cnt;

        $ai_archive_rows[] = [
            'round'        => (int)$row['round'],
            'draw_date'    => $row['draw_date'],
            'ai_numbers'   => $ai_nums,
            'real_numbers' => $real_nums,
            'bonus'        => (int)$row['bonus'],
            'matched'      => $matched,
            'match_count'  => $match_cnt,
        ];
    }
}

if ($total_rows > 0) {
    $ai_archive_summary['avg_match']  = round($sum_matches / $total_rows, 1);
    $ai_archive_summary['best_match'] = $best_match;
}

// 전체 누적 회차 수 (LIMIT 8과 별개)
$cnt_row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_ai_recommend", false);
if ($cnt_row && isset($cnt_row['cnt'])) {
    $ai_archive_summary['total_weeks'] = (int)$cnt_row['cnt'];
}

// ─────────────────────────────────────
// 로그인 회원의 크레딧 정보
// ─────────────────────────────────────
$credit_row          = lotto_get_credit_row($member['mb_id'], true);
$server_free_credits = (int)($credit_row['free_uses'] ?? 0);
$server_paid_credits = (int)($credit_row['credit_balance'] ?? 0);

// ─────────────────────────────────────
// AJAX 크레딧 사용 요청 처리
// ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['mode'])
    && $_POST['mode'] === 'use_credit') {

    header('Content-Type: application/json; charset=utf-8');

    if (empty($is_member)) {
        echo json_encode([
            'success' => false,
            'reason'  => 'NOT_LOGGED_IN',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ★ 분석 대상 회차 = DB 최신 회차 + 1 (위에서 $current_round 로 계산됨)
    $round_no = (int)$current_round;
    if ($round_no <= 0) {
        echo json_encode([
            'success' => false,
            'reason'  => 'ROUND_NOT_READY',
            'credit_balance' => $server_paid_credits,
            'free_uses'      => $server_free_credits,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $use = lotto_use_one_analysis(
        $member['mb_id'],
        'AI 분석 실행 (회차 '.$round_no.')',
        'round_'.$round_no
    );

    if (empty($use['success'])) {
        echo json_encode([
            'success'        => false,
            'reason'         => $use['reason'] ?? 'ERROR',
            // ✅ 성공/실패 응답 키 통일
            'credit_balance' => $use['credit_balance'] ?? ($use['credit'] ?? 0),
            'free_uses'      => $use['free_uses'] ?? ($use['free'] ?? 0),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ★ 분석 실행 성공 시 로그 테이블에 1건 기록
    $log_mb_id = isset($member['mb_id']) ? trim($member['mb_id']) : '';
    $log_ip    = $_SERVER['REMOTE_ADDR'] ?? '';
    $log_round = (int)$round_no;

    // 쿼리 문자열을 변수로 빼서 에러 시 로그로 남길 수 있게 처리
    $log_sql = "
        INSERT INTO g5_lotto_analysis_log
        SET mb_id     = '".sql_real_escape_string($log_mb_id)."',
            round_no  = '{$log_round}',
            user_ip   = '".sql_real_escape_string($log_ip)."',
            created_at = '".G5_TIME_YMDHIS."'
    ";

    // 두 번째 인자를 false로 주면, 오류가 나도 전체 페이지가 죽지 않습니다.
    $log_res = sql_query($log_sql, false);

    if (!$log_res) {
        // 화면에는 아무 것도 안 보이고, 웹서버 error_log 에만 남습니다.
        @error_log('[lotto] g5_lotto_analysis_log INSERT 실패: ' . $log_sql);
    }

    echo json_encode([
        'success'        => true,
        'used_as'        => $use['used_as'] ?? '',
        'credit_balance' => $use['credit_balance'] ?? 0,
        'free_uses'      => $use['free_uses'] ?? 0,
        // 디버깅 편하게, 실제 기록하려 했던 회차도 내려줌
        'logged_round'   => $log_round,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─────────────────────────────────────
// AJAX: 예측 6개 번호 저장 (g5_lotto_credit_number)
// ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['mode'])
    && $_POST['mode'] === 'save_numbers') {

    header('Content-Type: application/json; charset=utf-8');

    if (empty($is_member) || empty($member['mb_id'])) {
        echo json_encode(['success' => false, 'reason' => 'NOT_LOGGED_IN'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 입력값 파싱
    $round_no = isset($_POST['round_no']) ? (int)$_POST['round_no'] : null; // NULL 허용
    $nums = [];
    for ($i = 1; $i <= 6; $i++) {
        $key = 'n'.$i;
        $nums[] = isset($_POST[$key]) ? (int)$_POST[$key] : 0;
    }

    // 검증: 1~45, 6개 모두 존재, 중복 없음
    foreach ($nums as $n) {
        if ($n < 1 || $n > 45) {
            echo json_encode(['success' => false, 'reason' => 'INVALID_NUMBER'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    if (count(array_unique($nums)) !== 6) {
        echo json_encode(['success' => false, 'reason' => 'DUPLICATED_NUMBER'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    sort($nums);

    $mb_id = sql_real_escape_string(trim($member['mb_id']));
    $ip    = sql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');

    // round_no는 NULL 가능
    $round_sql = ($round_no && $round_no > 0) ? "'".$round_no."'" : "NULL";

    $save_sql = "
        INSERT INTO g5_lotto_credit_number
        SET mb_id      = '{$mb_id}',
            round_no   = {$round_sql},
            n1         = '".(int)$nums[0]."',
            n2         = '".(int)$nums[1]."',
            n3         = '".(int)$nums[2]."',
            n4         = '".(int)$nums[3]."',
            n5         = '".(int)$nums[4]."',
            n6         = '".(int)$nums[5]."',
            ip         = '{$ip}',
            created_at = '".G5_TIME_YMDHIS."'
    ";

    $res = sql_query($save_sql, false);
    if (!$res) {
        @error_log('[lotto] g5_lotto_credit_number INSERT 실패: ' . $save_sql);
        echo json_encode(['success' => false, 'reason' => 'DB_ERROR'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  
  <!-- Primary Meta Tags -->
  <title>로또 번호 AI 분석 - <?php echo $current_round_fmt; ?>회 추천번호 | 오늘로또</title>
  <meta name="title" content="로또 번호 AI 분석 - <?php echo $current_round_fmt; ?>회 추천번호 | 오늘로또">
  <meta name="description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. <?php echo $current_round_fmt; ?>회차 동행복권 공식 데이터 기반 패턴 분석 리포트와 균형 점수를 제공합니다.">

  <meta property="og:title" content="AI 분석 | 오늘로또 - <?php echo $current_round_fmt; ?>회차 데이터 기반 분석">
  <meta property="og:description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 동행복권 공식 데이터 기반 패턴 분석!">
  <meta property="og:title" content="AI 분석 | 오늘로또 - <?php echo $current_round_fmt; ?>회차 데이터 기반 분석">
  <meta property="og:description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 동행복권 공식 데이터 기반 패턴 분석!">

  <meta name="robots" content="index, follow">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo (defined('G5_URL') ? G5_URL : ''); ?><?php echo $_SERVER['REQUEST_URI']; ?>">
   <meta property="og:image" content="https://lottoinsight.ai/og-image.png">
  <meta property="og:image" content="https://lottoinsight.ai/og-image.png">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#ffffff">

  <!-- Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "오늘로또 - AI 로또 번호 분석",
    "applicationCategory": "UtilityApplication",
    "operatingSystem": "Web Browser",
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "KRW"
    },
    "description": "AI가 분석한 로또 번호 추천 서비스. 동행복권 공식 데이터 기반 패턴 분석으로 최적의 번호 조합을 제공합니다.",
    "url": "<?php echo (defined('G5_URL') ? G5_URL : ''); ?><?php echo $_SERVER['REQUEST_URI']; ?>",
    "author": {
      "@type": "Organization",
      "name": "오늘로또"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.5",
      "ratingCount": "1000"
    }
  }
  </script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [{
      "@type": "Question",
      "name": "로또 번호는 어떻게 분석되나요?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "동행복권 공식 데이터를 기반으로 AI가 Hot/Cold 분석, AC값 분석, 홀짝/고저 균형, 색상볼 통계, 상관관계, 몬테카를로 시뮬레이션 등 다양한 알고리즘을 통해 최적의 번호를 추천합니다."
      }
    }, {
      "@type": "Question",
      "name": "분석에 비용이 드나요?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "신규 회원 가입 시 무료 분석 1회를 제공하며, 추가 분석은 크레딧을 충전하여 이용할 수 있습니다."
      }
    }]
  }
  </script>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <!-- Shared Styles -->
  <link rel="stylesheet" href="/styles/shared.css">

  <style>
    /* ===== 밝은 테마 변수 정의 (40~60대 가독성 최적화) ===== */
    :root {
      --bg-primary: #ffffff;
      --bg-secondary: #f8f9fa;
      --bg-tertiary: #f1f3f5;
      --bg-card: #ffffff;
      --bg-card-hover: #f8f9fa;
      
      --text-primary: #1a1a1a;
      --text-secondary: #4a5568;
      --text-muted: #718096;
      --text-dark: #000000;
      
      --border-light: #e2e8f0;
      --border-medium: #cbd5e0;
      --border-dark: #a0aec0;
      
      --accent-primary: #0066cc;
      --accent-secondary: #00a86b;
      --accent-gold: #d97706;
      --accent-red: #dc2626;
      
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
      --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
      
      --font-size-base: 16px;
      --font-size-lg: 18px;
      --font-size-xl: 20px;
      --font-size-2xl: 24px;
      --font-size-3xl: 28px;
      
      --line-height-relaxed: 1.7;
      --line-height-normal: 1.6;
    }
    
    html, body {
      overflow-x: hidden;
      background-color: var(--bg-secondary);
    }
    
    body {
      min-height: 100vh;
      width: 100%;
      max-width: 100vw;
      background-color: var(--bg-secondary);
      color: var(--text-primary);
      font-size: var(--font-size-base);
      line-height: var(--line-height-relaxed);
      font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* ===== 상단 고정 헤더 ===== */
    .app-navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 68px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border-light);
      box-shadow: var(--shadow-sm);
      z-index: var(--z-fixed);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 var(--space-5);
    }

    .app-logo {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      text-decoration: none;
      color: var(--text-primary);
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: var(--font-size-lg);
      transition: transform var(--transition-fast);
    }

    .app-logo:hover {
      transform: scale(1.02);
    }

    .app-logo-icon {
      width: 36px;
      height: 36px;
      background: var(--gradient-cyan);
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(0, 255, 204, 0.25);
      position: relative;
    }

    .app-logo-icon::after {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, transparent 50%);
    }

    .navbar-right {
      display: flex;
      align-items: center;
      gap: var(--space-3);
    }

    .credit-badge {
      display: flex;
      align-items: center;
      gap: var(--space-2);
      padding: var(--space-2) var(--space-4);
      background: var(--bg-tertiary);
      border: 1px solid var(--border-light);
      border-radius: var(--radius-full);
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      transition: all var(--transition-fast);
    }

    .credit-badge:hover {
      background: var(--bg-card-hover);
      border-color: var(--border-medium);
    }

    .credit-count {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      color: var(--accent-primary);
      font-size: var(--font-size-base);
    }

    .user-avatar-btn {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: var(--bg-tertiary);
      border: 2px solid var(--border-light);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      text-decoration: none;
      font-size: var(--font-size-lg);
      overflow: hidden;
      transition: all var(--transition-normal);
    }

    .user-avatar-btn:hover {
      background: var(--bg-card-hover);
      transform: scale(1.05);
      border-color: var(--border-medium);
      box-shadow: var(--shadow-sm);
    }

    .user-avatar-btn.logged-in {
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      border-color: var(--accent-primary);
      box-shadow: var(--shadow-md);
      color: #ffffff;
    }

    .user-avatar-btn img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .charge-btn {
      padding: var(--space-2) var(--space-4);
      background: var(--gradient-gold);
      border: none;
      border-radius: var(--radius-full);
      font-family: 'Outfit', sans-serif;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-dark);
      cursor: pointer;
      transition: all var(--transition-normal);
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.25);
    }

    .charge-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 215, 0, 0.35);
    }

    /* ===== 메인 컨테이너 ===== */
    .app-container {
      max-width: 580px;
      margin: 0 auto;
      padding: 88px var(--space-5) 120px;
      background-color: var(--bg-secondary);
    }

    /* ===== 최신 당첨 결과 섹션 ===== */
    .latest-result-section {
      background: var(--bg-card);
      border: 2px solid var(--accent-red);
      border-radius: var(--radius-2xl);
      padding: var(--space-5);
      margin-bottom: var(--space-5);
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-lg);
    }

    .latest-result-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, 
        transparent, 
        rgba(239, 68, 68, 0.8), 
        rgba(249, 115, 22, 0.8), 
        transparent);
    }

    .latest-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--space-4);
    }

    .live-badge {
      display: inline-flex;
      align-items: center;
      gap: var(--space-2);
      padding: var(--space-2) var(--space-3);
      background: rgba(239, 68, 68, 0.12);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: var(--radius-full);
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--accent-red);
    }

    .live-dot {
      width: 6px;
      height: 6px;
      background: var(--accent-red);
      border-radius: 50%;
      animation: pulse-live 1.5s ease infinite;
    }

    @keyframes pulse-live {
      0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
      50% { opacity: 0.8; transform: scale(1.2); box-shadow: 0 0 0 4px rgba(239, 68, 68, 0); }
    }

    .latest-round {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .latest-balls {
      display: flex;
      justify-content: center;
      gap: var(--space-2);
      margin-bottom: var(--space-4);
      padding: var(--space-4);
      background: var(--bg-tertiary);
      border-radius: var(--radius-xl);
      border: 1px solid var(--border-light);
    }

    .latest-ball {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1rem;
      color: #fff;
      position: relative;
      transform-style: preserve-3d;
      transition: transform var(--transition-bounce);
    }

    .latest-ball:hover {
      transform: translateY(-4px) scale(1.1);
    }

    .latest-ball::before {
      content: '';
      position: absolute;
      top: 8%;
      left: 18%;
      width: 30%;
      height: 25%;
      background: radial-gradient(ellipse at 30% 30%, 
        rgba(255, 255, 255, 0.6) 0%, 
        rgba(255, 255, 255, 0.2) 40%,
        transparent 70%);
      border-radius: 50%;
      transform: rotate(-25deg);
    }

    .bonus-separator {
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-muted);
      font-size: 1.2rem;
      margin: 0 var(--space-1);
    }

    .latest-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: var(--space-3);
      border-top: 1px solid var(--border-light);
    }

    .latest-prize {
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      font-weight: 500;
    }

    .latest-prize strong {
      color: var(--accent-gold);
      font-weight: 700;
      font-size: var(--font-size-lg);
    }

    .latest-link {
      font-size: var(--font-size-base);
      color: var(--accent-primary);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: var(--space-1);
      transition: all var(--transition-fast);
      font-weight: 600;
    }

    .latest-link:hover {
      color: var(--accent-secondary);
      transform: translateX(2px);
      text-decoration: underline;
    }

    /* ===== AI vs 실제 비교 섹션 ===== */
    .ai-comparison-section {
      background: var(--bg-card);
      border: 2px solid var(--accent-primary);
      border-radius: var(--radius-2xl);
      padding: var(--space-5);
      margin-bottom: var(--space-5);
      box-shadow: var(--shadow-lg);
    }

    .comparison-header {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      margin-bottom: var(--space-4);
    }

    .comparison-icon {
      width: 36px;
      height: 36px;
      background: rgba(0, 255, 204, 0.1);
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }

    .comparison-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .comparison-rows {
      display: flex;
      flex-direction: column;
      gap: var(--space-3);
      margin-bottom: var(--space-4);
    }

    .comparison-row {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      padding: var(--space-3);
      background: var(--bg-tertiary);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-light);
    }

    .comparison-label {
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      width: 70px;
      flex-shrink: 0;
      font-weight: 600;
    }

    .comparison-balls {
      display: flex;
      gap: var(--space-2);
      flex: 1;
    }

    .mini-ball {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.7rem;
      color: #fff;
      position: relative;
      transition: transform var(--transition-fast);
    }

    .mini-ball:hover {
      transform: scale(1.15);
    }

    .mini-ball::before {
      content: '';
      position: absolute;
      top: 3px;
      left: 6px;
      width: 6px;
      height: 5px;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      transform: rotate(-25deg);
    }

    .mini-ball.matched {
      box-shadow: 0 0 0 2px var(--accent-cyan), 0 0 15px rgba(0, 255, 204, 0.5);
      animation: ball-matched 0.5s ease;
    }

    @keyframes ball-matched {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.2); }
    }

    .comparison-result {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: var(--space-3) var(--space-4);
      background: rgba(0, 102, 204, 0.08);
      border: 2px solid var(--accent-primary);
      border-radius: var(--radius-lg);
    }

    .match-count {
      font-family: 'Outfit', sans-serif;
      font-size: var(--font-size-lg);
      color: var(--accent-primary);
      font-weight: 700;
    }

    .match-numbers {
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      font-weight: 500;
    }

    .comparison-disclaimer {
      margin-top: 12px;
      padding: 10px 12px;
      background: rgba(255, 215, 95, 0.08);
      border-radius: 10px;
      font-size: 0.75rem;
      color: var(--accent-gold);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* =====================================================
     * ✅ 지난 주 AI 추천 vs 실제 결과: PC에서는 크게 / 모바일에서는 작게
     * - 다른 곳(로딩 모달 등)의 .mini-ball에는 영향 없도록 스코프 제한
     * ===================================================== */
    #aiComparisonSection .comparison-balls{
      display: flex;
      flex-wrap: nowrap; /* ✅ 줄바꿈 없이 한 줄 유지 */
      min-width: 0;
    }

    /* ✅ PC/태블릿(기본): 크게 */
    @media (min-width: 641px){
      #aiComparisonSection .comparison-row{ gap: 12px; }
      #aiComparisonSection .comparison-label{
        width: 100px;
        font-size: 0.8rem;
        white-space: nowrap;
      }
      #aiComparisonSection .comparison-balls{ gap: 6px; }
      #aiComparisonSection .mini-ball{
        width: 45px;
        height: 45px;
        font-size: 0.8rem;
      }
    }

    /* ✅ 모바일: 작게(한 줄 유지 우선) */
    @media (max-width: 640px){
      #aiComparisonSection .comparison-row{ gap: 10px; }
      #aiComparisonSection .comparison-label{
        width: 35px;
        font-size: 0.65rem;
        white-space: nowrap;
      }
      #aiComparisonSection .comparison-balls{ gap: 4px; }
      #aiComparisonSection .mini-ball{
        width: 22px;
        height: 22px;
        font-size: 0.65rem;
      }
    }

    /* ✅ 아주 작은 화면(아이폰 SE급): 더 작게 */
    @media (max-width: 360px){
      #aiComparisonSection .comparison-row{ padding: 10px; }
      #aiComparisonSection .comparison-label{ width: 48px; }
      #aiComparisonSection .comparison-balls{ gap: 3px; }
      #aiComparisonSection .mini-ball{
        width: 20px;
        height: 20px;
        font-size: 0.6rem;
      }
    }

    /* ===== 신뢰 배지 섹션 (신뢰도 기능 5) ===== */
    .trust-section {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 20px;
    }

    .trust-item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 14px;
      background: var(--bg-card);
      border: 1px solid var(--border-light);
      border-radius: 12px;
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      box-shadow: var(--shadow-sm);
    }

    .trust-check {
      color: var(--accent-primary);
      font-weight: 700;
      font-size: var(--font-size-lg);
    }

    /* ===== 대시보드 상태 ===== */
    .dashboard-view {
      display: block;
    }

    .dashboard-view.hidden {
      display: none;
    }

    /* 환영 메시지 */
    .welcome-section {
      text-align: center;
      margin-bottom: 24px;
    }

    .user-avatar {
      width: 64px;
      height: 64px;
      background: var(--gradient-cyan);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 auto 16px;
    }

    .welcome-text h1 {
      font-family: 'Outfit', sans-serif;
      font-size: var(--font-size-2xl);
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--text-primary);
      line-height: 1.4;
    }

    .welcome-text p {
      color: var(--text-secondary);
      font-size: var(--font-size-lg);
      line-height: var(--line-height-relaxed);
    }

    /* 분석 스타일 선택 */
    .style-section {
      background: var(--bg-card);
      border: 2px solid var(--border-light);
      border-radius: 20px;
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-md);
    }

    .style-title {
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .style-multi-badge {
      padding: 4px 10px;
      background: rgba(255, 215, 95, 0.15);
      border: 1px solid rgba(255, 215, 95, 0.3);
      border-radius: 12px;
      font-size: 0.65rem;
      color: var(--accent-gold);
      font-weight: 500;
    }

    .style-buttons-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 12px;
      max-height: 400px;
      overflow-y: auto;
    }

    .style-buttons-grid::-webkit-scrollbar {
      width: 6px;
    }

    .style-buttons-grid::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 3px;
    }

    .style-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 18px;
      background: var(--bg-tertiary);
      border: 2px solid var(--border-light);
      border-radius: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    }

    .style-btn:hover {
      background: var(--bg-card-hover);
      border-color: var(--border-medium);
      box-shadow: var(--shadow-sm);
      transform: translateY(-2px);
    }

    .style-btn.active {
      background: rgba(0, 102, 204, 0.1);
      border-color: var(--accent-primary);
      box-shadow: var(--shadow-sm);
    }

    .style-icon {
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .style-name {
      font-size: var(--font-size-base);
      font-weight: 600;
      color: var(--text-primary);
      display: block;
    }

    .style-btn.active .style-name {
      color: var(--accent-primary);
      font-weight: 700;
    }

    .style-desc {
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      display: block;
      margin-top: 4px;
      line-height: 1.5;
    }

    .style-check {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 22px;
      height: 22px;
      background: transparent;
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      color: transparent;
      transition: all 0.2s ease;
    }

    .style-btn.active .style-check {
      background: var(--accent-primary);
      border-color: var(--accent-primary);
      color: #ffffff;
    }

    .style-selected-count {
      text-align: center;
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      padding-top: 12px;
      border-top: 2px solid var(--border-light);
      font-weight: 600;
    }

    .style-selected-count span {
      color: var(--accent-primary);
      font-weight: 700;
      font-size: var(--font-size-lg);
    }

    /* 분석 시작 버튼 */
    .analyze-section {
      margin-bottom: 24px;
    }

    .analyze-btn {
      width: 100%;
      padding: 22px;
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      border: none;
      border-radius: 16px;
      font-family: 'Outfit', sans-serif;
      font-size: var(--font-size-xl);
      font-weight: 700;
      color: #ffffff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: var(--shadow-lg);
      transition: all 0.3s ease;
      min-height: 60px;
    }

    .analyze-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 50px rgba(0, 102, 204, 0.4);
    }

    .analyze-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }

    .analyze-cost {
      text-align: center;
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      margin-top: 12px;
      font-weight: 500;
    }

    .analyze-cost span {
      color: var(--accent-primary);
      font-weight: 700;
      font-size: var(--font-size-lg);
    }

    /* 이전 분석 내역 */
    .history-section {
      background: var(--bg-card);
      border: 2px solid var(--border-light);
      border-radius: 20px;
      padding: 24px;
      box-shadow: var(--shadow-md);
    }

    .history-title {
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .history-empty {
      text-align: center;
      padding: 40px;
      color: var(--text-secondary);
      font-size: var(--font-size-base);
      line-height: var(--line-height-relaxed);
    }

    .history-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .history-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 18px;
      background: var(--bg-tertiary);
      border: 1px solid var(--border-light);
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .history-item:hover {
      background: var(--bg-card-hover);
      border-color: var(--border-medium);
      box-shadow: var(--shadow-sm);
      transform: translateY(-2px);
    }

    .history-numbers {
      font-family: 'Outfit', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .history-meta {
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    /* ===== 분석 결과 상태 ===== */
    .result-view {
      display: none;
    }

    .result-view.visible {
      display: block;
    }

    .result-intro {
      text-align: center;
      margin-bottom: 20px;
    }

    .result-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 18px;
      background: rgba(0, 102, 204, 0.1);
      border: 2px solid var(--accent-primary);
      border-radius: 50px;
      font-size: var(--font-size-base);
      color: var(--accent-primary);
      margin-bottom: 16px;
      font-weight: 700;
    }

    .result-intro h2 {
      font-family: 'Outfit', sans-serif;
      font-size: var(--font-size-2xl);
      font-weight: 700;
      margin-bottom: 12px;
      color: var(--text-primary);
      line-height: 1.4;
    }

    .result-intro p {
      color: var(--text-secondary);
      font-size: var(--font-size-lg);
      line-height: var(--line-height-relaxed);
    }

    /* 결과 네비게이션 */
    .result-nav {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
      overflow-x: auto;
      padding-bottom: 8px;
      -webkit-overflow-scrolling: touch;
    }

    .result-nav::-webkit-scrollbar {
      display: none;
    }

    .result-nav-btn {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 12px 18px;
      background: var(--bg-tertiary);
      border: 2px solid var(--border-light);
      border-radius: 12px;
      font-size: var(--font-size-base);
      font-weight: 600;
      color: var(--text-secondary);
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .result-nav-btn:hover {
      background: var(--bg-card-hover);
      border-color: var(--border-medium);
    }

    .result-nav-btn.active {
      background: rgba(0, 102, 204, 0.1);
      border-color: var(--accent-primary);
      color: var(--accent-primary);
      font-weight: 700;
    }

    /* 결과 카드 */
    .result-cards-container {
      position: relative;
      margin-bottom: 16px;
    }

    .result-card {
      display: none;
      background: var(--bg-card);
      border: 2px solid var(--border-light);
      border-radius: 20px;
      padding: 28px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      animation: fadeIn 0.3s ease;
      box-shadow: var(--shadow-md);
    }

    .result-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .result-card:hover {
      border-color: var(--accent-primary);
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .result-card:hover::before {
      transform: scaleX(1);
    }

    .result-card.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .result-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .card-badge {
      background: rgba(0, 102, 204, 0.1);
      color: var(--accent-primary);
      padding: 8px 14px;
      border-radius: 8px;
      font-size: var(--font-size-base);
      font-weight: 700;
      border: 1px solid var(--accent-primary);
    }

    .card-menu-btn {
      background: transparent;
      border: none;
      color: var(--text-secondary);
      font-size: 24px;
      cursor: pointer;
      padding: 4px 8px;
      transition: color 0.3s ease;
    }

    .card-menu-btn:hover {
      color: var(--accent-primary);
    }

    .result-card-style {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .result-card-style-icon {
      font-size: 1.3rem;
    }

    .result-card-style-name {
      font-family: 'Outfit', sans-serif;
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: var(--accent-primary);
    }

    .result-card-number {
      padding: 6px 14px;
      background: var(--bg-tertiary);
      border: 1px solid var(--border-light);
      border-radius: 20px;
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      font-weight: 600;
    }

    .card-score {
      margin-bottom: 20px;
    }

    .score-label {
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      margin-bottom: 10px;
      font-weight: 600;
    }

    .score-gauge {
      position: relative;
      height: 36px;
      background: var(--bg-tertiary);
      border: 1px solid var(--border-light);
      border-radius: 18px;
      overflow: hidden;
    }

    .score-fill {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      border-radius: 16px;
      transition: width 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      box-shadow: 0 0 20px currentColor;
    }

    .score-value {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: var(--text-primary);
      z-index: 1;
    }

    .card-insights {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
      margin-bottom: 20px;
    }

    .insight-item {
      display: flex;
      align-items: center;
      gap: 10px;
      background: var(--bg-tertiary);
      border: 1px solid var(--border-light);
      padding: 14px;
      border-radius: 12px;
    }

    .insight-icon {
      font-size: 24px;
    }

    .insight-content {
      flex: 1;
    }

    .insight-label {
      font-size: var(--font-size-base);
      color: var(--text-secondary);
      font-weight: 500;
    }

    .insight-value {
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: var(--accent-primary);
    }

    .card-actions {
      display: flex;
      gap: 12px;
    }

    .btn-primary, .btn-secondary {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      padding: 12px 20px;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      color: #ffffff;
      font-weight: 700;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
    }

    .btn-secondary {
      background: var(--bg-tertiary);
      color: var(--text-primary);
      border: 2px solid var(--border-medium);
      font-weight: 600;
    }

    .btn-secondary:hover {
      background: var(--bg-card-hover);
      border-color: var(--accent-primary);
      color: var(--accent-primary);
    }

    /* 모바일 최적화 */
    @media (max-width: 768px) {
      .result-card {
        padding: 16px;
      }

      .card-insights {
        grid-template-columns: 1fr;
      }

      .card-actions {
        flex-direction: column;
      }
      
      .balls-container {
        gap: 8px;
        padding: 16px;
      }
    }
    
    /* 모바일 터치 최적화 */
    @media (hover: none) and (pointer: coarse) {
      button, .clickable-ball, .credit-charge-btn, .btn-primary, .btn-secondary {
        min-height: 44px; /* 애플 권장 터치 타겟 크기 */
        min-width: 44px;
      }
      
      .ball-3d {
        width: 52px;
        height: 52px;
      }
      
      .result-card {
        touch-action: pan-y; /* 세로 스크롤 허용 */
      }
    }

    /* 3D 볼 */
    .balls-container {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 24px;
      padding: 20px;
      background: rgba(0, 0, 0, 0.25);
      border-radius: 16px;
    }

    .ball-3d {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 24px;
      color: #fff;
      margin: 0 8px;
      position: relative;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      animation: ballAppear 0.5s ease backwards;
      box-shadow: 
        0 4px 8px rgba(0, 0, 0, 0.2),
        inset 0 -2px 4px rgba(0, 0, 0, 0.2),
        inset 0 2px 4px rgba(255, 255, 255, 0.3);
    }

    @keyframes ballAppear {
      0% {
      opacity: 0;
        transform: scale(0) rotateY(180deg);
      }
      60% {
        transform: scale(1.1) rotateY(0deg);
      }
      100% {
        opacity: 1;
        transform: scale(1) rotateY(0deg);
      }
    }

    .ball-3d.clickable-ball {
      cursor: pointer;
    }

    .ball-3d.clickable-ball:hover {
      transform: scale(1.2) rotateY(360deg);
      box-shadow: 
        0 8px 16px rgba(0, 0, 0, 0.3),
        0 0 20px rgba(255, 255, 255, 0.3);
    }

    .ball-3d.clickable-ball::after {
      content: '📊';
      position: absolute;
      top: -8px;
      right: -8px;
      font-size: 0.65rem;
      background: rgba(0,224,164,0.9);
      border-radius: 50%;
      width: 18px;
      height: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.2s;
    }

    .ball-3d.clickable-ball:hover::after {
      opacity: 1;
    }

    .ball-3d::after {
      content: '';
      position: absolute;
      top: 8px;
      left: 12px;
      width: 12px;
      height: 8px;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      transform: rotate(-30deg);
    }

    .ball-3d:nth-child(1) { animation-delay: 0.1s; }
    .ball-3d:nth-child(2) { animation-delay: 0.2s; }
    .ball-3d:nth-child(3) { animation-delay: 0.3s; }
    .ball-3d:nth-child(4) { animation-delay: 0.4s; }
    .ball-3d:nth-child(5) { animation-delay: 0.5s; }
    .ball-3d:nth-child(6) { animation-delay: 0.6s; }

    /* 번호별 색상 (동행복권 공식 색상) - 그라데이션 적용 */
    .ball-yellow { 
      background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
      color: #333;
      box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
    }
    .ball-blue { 
      background: linear-gradient(135deg, #1e90ff 0%, #4169e1 100%);
      box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }
    .ball-red { 
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
      box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }
    .ball-gray { 
      background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
      box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4);
    }
    .ball-green { 
      background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
      box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
    }

    /* 모바일 최적화 */
    @media (max-width: 768px) {
      .ball-3d {
        width: 48px;
        height: 48px;
        font-size: 20px;
        margin: 0 4px;
      }
    }

    /* 번호별 선정 이유 */
    .number-stories {
      margin-bottom: 20px;
      padding: 16px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 14px;
    }

    .story-title {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 12px;
    }

    .story-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }

    .story-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 10px;
    }

    .story-ball {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.75rem;
      color: #fff;
      flex-shrink: 0;
    }

    .story-content {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .story-tag {
      font-size: 0.65rem;
      font-weight: 600;
      padding: 2px 6px;
      border-radius: 4px;
      width: fit-content;
    }

    .tag-hot { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .tag-cold { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .tag-balance { background: rgba(0, 224, 164, 0.15); color: var(--accent-cyan); }

    .story-desc {
      font-size: 0.7rem;
      color: var(--text-muted);
    }

    /* 리포트 요약 */
    .report-summary {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 16px;
    }

    .summary-tag {
      padding: 6px 12px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 20px;
      font-size: 0.8rem;
      color: var(--accent-cyan);
    }

    .report-insights {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-bottom: 16px;
      line-height: 1.6;
      padding: 12px;
      background: rgba(0, 224, 164, 0.05);
      border-radius: 10px;
    }

    /* 균형 점수 */
    .balance-section {
      padding: 16px;
      background: rgba(0, 224, 164, 0.05);
      border: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 14px;
    }

    .balance-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .balance-label {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .balance-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--accent-cyan);
    }

    .balance-bar {
      height: 8px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 4px;
      overflow: hidden;
      margin-bottom: 12px;
    }

    .balance-fill {
      height: 100%;
      background: var(--gradient-cyan);
      border-radius: 4px;
      width: 0;
    }

    @keyframes fillBar {
      to { width: var(--target-width, 87%); }
    }

    .balance-details {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
    }

    .balance-item {
      text-align: center;
      padding: 8px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 8px;
    }

    .balance-item-icon {
      font-size: 1rem;
      margin-bottom: 2px;
    }

    .balance-item-value {
      font-family: 'Outfit', sans-serif;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--text-primary);
    }

    .balance-item-label {
      font-size: 0.6rem;
      color: var(--text-muted);
    }

    /* 결과 인디케이터 */
    .result-indicators {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-bottom: 20px;
    }

    .result-indicator {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--border-medium);
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid var(--border-light);
    }

    .result-indicator.active {
      width: 28px;
      border-radius: 5px;
      background: var(--accent-primary);
      border-color: var(--accent-primary);
    }

    /* 액션 버튼 */
    .result-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 20px;
    }

    .action-btn {
      padding: 16px;
      border-radius: 14px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .action-btn-primary {
      background: var(--gradient-cyan);
      border: none;
      color: var(--primary-dark);
    }

    .action-btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(0, 224, 164, 0.3);
    }

    .action-btn-secondary {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: var(--text-primary);
    }

    .action-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    /* 면책 조항 */
    .disclaimer {
      padding: 16px;
      background: rgba(239, 68, 68, 0.05);
      border: 1px solid rgba(239, 68, 68, 0.15);
      border-radius: 12px;
      margin-bottom: 20px;
    }

    .disclaimer p {
      font-size: 0.7rem;
      color: #ef4444;
      margin-bottom: 6px;
      text-align: center;
    }

    .disclaimer ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 4px;
    }

    .disclaimer li {
      font-size: 0.65rem;
      color: var(--text-muted);
      padding-left: 10px;
      position: relative;
    }

    .disclaimer li::before {
      content: '•';
      position: absolute;
      left: 0;
      color: rgba(239, 68, 68, 0.5);
    }

    /* ===== 로딩 모달 ===== */
    .loading-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }

    .loading-modal.active {
      opacity: 1;
      visibility: visible;
    }

    .loading-container {
      background: var(--bg-card);
      border: 2px solid var(--accent-primary);
      border-radius: 24px;
      padding: 48px 40px;
      max-width: 500px;
      width: 90%;
      box-shadow: var(--shadow-lg);
    }

    .loading-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .loading-spinner {
      width: 64px;
      height: 64px;
      margin: 0 auto 16px;
      border: 4px solid var(--bg-tertiary);
      border-top: 4px solid var(--accent-primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .loading-percentage {
      text-align: center;
      font-size: var(--font-size-2xl);
      font-weight: 700;
      color: var(--accent-primary);
      margin-bottom: 8px;
      font-family: 'Courier New', monospace;
    }

    .loading-progress {
      position: relative;
      width: 100%;
      height: 10px;
      background: var(--bg-tertiary);
      border: 1px solid var(--border-light);
      border-radius: 5px;
      overflow: hidden;
      margin-bottom: 16px;
    }

    .loading-bar {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      background: linear-gradient(90deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
      border-radius: 5px;
      transition: width 0.3s ease-out;
      width: 0%;
    }

    .loading-text {
      text-align: center;
      font-size: var(--font-size-lg);
      color: var(--text-primary);
      min-height: 50px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      transition: opacity 0.15s ease;
      font-weight: 600;
    }

    .loading-icon {
      font-size: 32px;
      animation: bounce 1s ease infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    /* ===== 스켈레톤 로딩 (UX 개선) ===== */
    .skeleton-loader {
      animation: pulse 1.5s ease-in-out infinite;
      padding: 24px;
    }

    .skeleton-header {
      height: 24px;
      width: 60%;
      background: linear-gradient(90deg, var(--bg-tertiary) 25%, var(--bg-card-hover) 50%, var(--bg-tertiary) 75%);
      background-size: 200% 100%;
      animation: shimmer 1.5s infinite;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .skeleton-balls {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 24px;
    }

    .skeleton-ball {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: linear-gradient(90deg, var(--bg-tertiary) 25%, var(--bg-card-hover) 50%, var(--bg-tertiary) 75%);
      background-size: 200% 100%;
      animation: shimmer 1.5s infinite;
    }

    .skeleton-text {
      height: 16px;
      width: 80%;
      background: linear-gradient(90deg, var(--bg-tertiary) 25%, var(--bg-card-hover) 50%, var(--bg-tertiary) 75%);
      background-size: 200% 100%;
      animation: shimmer 1.5s infinite;
      border-radius: 4px;
      margin: 8px auto;
    }

    @keyframes shimmer {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    .loading-text-content {
      font-weight: 500;
    }

    .loading-data {
      margin-top: 28px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      max-width: 340px;
      text-align: left;
      opacity: 0;
      transform: translateY(10px);
      animation: fadeInUp 0.5s ease 0.5s forwards;
    }

    @keyframes fadeInUp {
      to { opacity: 1; transform: translateY(0); }
    }

    .data-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .data-source {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--accent-cyan);
    }

    .data-update {
      font-size: 0.7rem;
      color: var(--text-muted);
    }

    .data-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 16px;
    }

    .stat-item {
      text-align: center;
      padding: 10px 8px;
      background: rgba(0, 224, 164, 0.05);
      border-radius: 10px;
    }

    .stat-value {
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .stat-label {
      font-size: 0.6rem;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .recent-numbers {
      background: rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      padding: 12px;
    }

    .recent-title {
      font-size: 0.7rem;
      color: var(--text-muted);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .recent-row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      opacity: 0;
      animation: slideIn 0.3s ease forwards;
    }

    .recent-row:last-child {
      border-bottom: none;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-10px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .recent-round {
      font-size: 0.7rem;
      color: var(--text-muted);
      width: 50px;
    }

    .recent-balls {
      display: flex;
      gap: 4px;
    }

    .analyzing-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      background: rgba(0, 224, 164, 0.1);
      border-radius: 10px;
      font-size: 0.6rem;
      color: var(--accent-cyan);
      animation: pulse 1.5s ease infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    /* ===== 반응형 개선 (세밀한 브레이크포인트) ===== */
    
    /* 태블릿 (640px ~ 1024px) */
    @media (min-width: 640px) and (max-width: 1024px) {
      .app-container {
        max-width: 768px;
        padding: 88px var(--space-6) 120px;
      }
      
      .balls-container {
        gap: 14px;
      }
      
      .ball-3d {
        width: 54px;
        height: 54px;
        font-size: 1.15rem;
      }

      .style-buttons-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    /* 폴더블 기기 대응 */
    @media (min-width: 540px) and (max-width: 720px) and (min-height: 720px) {
      .app-container {
        max-width: 540px;
      }
    }

    /* 모바일 (640px 이하) */
    @media (max-width: 640px) {
      /* 기본 시인성 강화 */
      body {
        font-size: 16px;
        line-height: 1.7;
      }

      .app-navbar {
        padding: 0 16px;
        height: 60px;
      }

      .app-logo {
        font-size: 1.15rem !important;
      }

      .app-container {
        padding: 80px 16px 100px;
      }

      .trust-section {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .trust-item {
        padding: 16px;
        font-size: 0.95rem;
      }

      .style-buttons-grid {
        grid-template-columns: 1fr;
        gap: 10px;
        max-height: 350px;
      }

      .style-btn {
        padding: 18px 16px;
        min-height: 52px;
        font-size: 1rem;
      }

      .balls-container {
        gap: 10px;
        padding: 20px 16px;
        flex-wrap: wrap;
        justify-content: center;
      }

      .ball-3d {
        width: 48px;
        height: 48px;
        font-size: 1.15rem;
        font-weight: 700;
      }

      .latest-balls {
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
      }

      .latest-ball {
        width: 42px;
        height: 42px;
        font-size: 1rem;
        font-weight: 700;
      }

      .story-grid {
        grid-template-columns: 1fr;
      }

      .result-actions {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .action-btn-primary,
      .action-btn-secondary {
        padding: 16px 20px;
        font-size: 1rem;
        min-height: 52px;
      }

      .disclaimer ul {
        grid-template-columns: 1fr;
      }

      /* 카드 패딩 증가 */
      .section-card,
      .result-card,
      .history-section {
        padding: 24px 20px;
        border-radius: 20px;
      }

      /* 섹션 타이틀 */
      .section-title {
        font-size: 1.1rem;
      }

      /* 분석 버튼 */
      .analyze-btn,
      .reanalyze-btn {
        padding: 18px 24px;
        font-size: 1.1rem;
        min-height: 56px;
      }

      /* 충전 모달 */
      .charge-modal-body {
        padding: 24px 20px;
      }

      .charge-option {
        padding: 18px 16px;
      }

      .charge-submit-btn {
        padding: 18px 24px;
        font-size: 1.05rem;
        min-height: 56px;
      }

      /* 결과 네비게이션 */
      .result-nav {
        gap: 10px;
        padding: 8px 0;
      }

      .result-nav-btn {
        padding: 12px 18px;
        font-size: 0.95rem;
      }

      /* 크레딧 배지 */
      .credit-badge {
        padding: 10px 14px;
        font-size: 0.9rem;
      }

      .charge-btn {
        padding: 10px 16px;
        font-size: 0.9rem;
      }

      /* 히스토리 */
      .history-item {
        padding: 16px;
      }

      .history-numbers {
        font-size: 1rem;
      }
    }

    @media (max-width: 375px) {
      .app-container {
        padding: 72px 12px 90px;
      }

      .ball-3d {
        width: 44px;
        height: 44px;
        font-size: 1.05rem;
      }

      .latest-ball {
        width: 38px;
        height: 38px;
        font-size: 0.95rem;
      }

      .section-card {
        padding: 20px 16px;
      }

      .analyze-btn {
        padding: 16px 20px;
        font-size: 1rem;
      }
    }

	/* ===== 헤더 폭/줄바꿈 방지 + 버튼 높이 통일 ===== */
	.app-navbar { gap: 10px; }

	.app-logo {
	  white-space: nowrap;
	  min-width: 0;
	  max-width: 46%;
	  overflow: hidden;
	  text-overflow: ellipsis;
	}

	.navbar-right{
	  min-width: 0;
	  flex: 1;
	  justify-content: flex-end;
	}

	/* 우측 3개 높이 통일(38px) + 줄바꿈 방지 */
	.credit-badge,
	.charge-btn,
	.user-avatar-btn{
	  height: 38px;
	  flex-shrink: 0;
	}

	.credit-badge{
	  padding: 0 12px;            /* 기존 padding 덮어쓰기 */
	  white-space: nowrap;
	}

	.charge-btn{
	  padding: 0 14px;            /* 기존 padding 덮어쓰기 */
	  white-space: nowrap;
	  min-width: 66px;            /* “+ 충전” 2줄로 안 내려가게 */
	  line-height: 38px;
	}

	/* 크레딧 컨테이너 스타일 */
	.credit-container {
	  display: flex;
	  align-items: center;
	  gap: 12px;
	  background: var(--bg-card);
	  border: 2px solid var(--border-light);
	  border-radius: 16px;
	  padding: 10px 16px;
	  box-shadow: var(--shadow-sm);
	  transition: all 0.3s ease;
	}

	.credit-container:hover {
	  border-color: var(--border-medium);
	  box-shadow: var(--shadow-md);
	}

	.credit-icon {
	  font-size: 24px;
	}

	.credit-details {
	  flex: 1;
	}

	.credit-total {
	  display: flex;
	  justify-content: space-between;
	  align-items: center;
	  margin-bottom: 4px;
	}

	.credit-label {
	  font-size: var(--font-size-base);
	  color: var(--text-secondary);
	  font-weight: 600;
	}

	.credit-value {
	  font-size: var(--font-size-xl);
	  font-weight: 700;
	  color: var(--accent-primary);
	  font-family: 'Outfit', sans-serif;
	}

	.credit-breakdown {
	  display: flex;
	  gap: 8px;
	  font-size: var(--font-size-base);
	}

	.credit-free {
	  background: rgba(0, 102, 204, 0.1);
	  color: var(--accent-primary);
	  padding: 4px 10px;
	  border-radius: 6px;
	  font-weight: 600;
	}

	.credit-paid {
	  background: rgba(0, 168, 107, 0.1);
	  color: var(--accent-secondary);
	  padding: 4px 10px;
	  border-radius: 6px;
	  font-weight: 600;
	}

	.credit-charge-btn {
	  display: flex;
	  align-items: center;
	  gap: 6px;
	  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
	  color: #ffffff;
	  border: none;
	  padding: 10px 18px;
	  border-radius: 12px;
	  font-size: var(--font-size-base);
	  font-weight: 700;
	  cursor: pointer;
	  transition: all 0.3s ease;
	  white-space: nowrap;
	}

	.credit-charge-btn:hover {
	  transform: translateY(-2px);
	  box-shadow: var(--shadow-md);
	}

	.credit-container.credit-empty {
	  border-color: var(--accent-red);
	  animation: shake 0.5s ease;
	}

	.credit-container.credit-low {
	  border-color: var(--accent-gold);
	}

	/* 모바일 최적화 */
	@media (max-width: 768px) {
	  .credit-container {
		padding: 8px 12px;
		gap: 10px;
	  }

	  .credit-value {
		font-size: var(--font-size-lg);
	  }

	  .credit-breakdown {
		font-size: var(--font-size-base);
	  }

	  .credit-charge-btn {
		padding: 8px 14px;
		font-size: var(--font-size-base);
	  }
	}

	/* 아주 좁을 때는 상세 정보 숨기기 */
	@media (max-width: 420px){
	  .credit-breakdown { display: none; }
	  .credit-label { display: none; }
	  .app-logo{ max-width: 40%; }
	}

	/* 크레딧 경고 팝업 */
	.credit-warning {
	  position: fixed;
	  bottom: -200px;
	  left: 50%;
	  transform: translateX(-50%);
	  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
	  color: #fff;
	  padding: 20px 24px;
	  border-radius: 16px;
	  box-shadow: 0 8px 24px rgba(255, 107, 107, 0.3);
	  display: flex;
	  align-items: center;
	  gap: 16px;
	  z-index: 10001;
	  transition: bottom 0.3s ease;
	  max-width: 500px;
	  width: 90%;
	}

	.credit-warning.show {
	  bottom: 24px;
	}

	.warning-icon {
	  font-size: 32px;
	  flex-shrink: 0;
	}

	.warning-text {
	  flex: 1;
	}

	.warning-text strong {
	  display: block;
	  font-size: 16px;
	  margin-bottom: 4px;
	}

	.warning-text p {
	  font-size: 14px;
	  margin: 0;
	  opacity: 0.9;
	}

	.warning-btn {
	  background: #fff;
	  color: #ff6b6b;
	  border: none;
	  padding: 10px 20px;
	  border-radius: 8px;
	  font-weight: 600;
	  cursor: pointer;
	  transition: all 0.3s ease;
	  white-space: nowrap;
	}

	.warning-btn:hover {
	  transform: scale(1.05);
	  box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
	}

	@media (max-width: 768px) {
	  .credit-warning {
		flex-direction: column;
		text-align: center;
		padding: 16px;
	  }

	  .warning-btn {
		width: 100%;
	  }
	}

	/* 공유 모달 */
	.share-modal {
	  position: fixed;
	  top: 0;
	  left: 0;
	  width: 100%;
	  height: 100%;
	  z-index: 10000;
	  display: flex;
	  align-items: center;
	  justify-content: center;
	  opacity: 0;
	  visibility: hidden;
	  transition: all 0.3s ease;
	}

	.share-modal.active {
	  opacity: 1;
	  visibility: visible;
	}

	.share-backdrop {
	  position: absolute;
	  top: 0;
	  left: 0;
	  width: 100%;
	  height: 100%;
	  background: rgba(0, 0, 0, 0.7);
	  backdrop-filter: blur(5px);
	}

	.share-content {
	  position: relative;
	  background: var(--bg-card);
	  border: 2px solid var(--accent-primary);
	  border-radius: 20px;
	  padding: 32px;
	  max-width: 500px;
	  width: 90%;
	  transform: scale(0.9);
	  transition: transform 0.3s ease;
	  box-shadow: var(--shadow-lg);
	}

	.share-modal.active .share-content {
	  transform: scale(1);
	}

	.share-header {
	  display: flex;
	  justify-content: space-between;
	  align-items: center;
	  margin-bottom: 24px;
	}

	.share-header h3 {
	  color: var(--accent-primary);
	  margin: 0;
	  font-size: var(--font-size-xl);
	  font-weight: 700;
	}

	.share-header button {
	  background: transparent;
	  border: none;
	  color: var(--text-secondary);
	  font-size: 32px;
	  cursor: pointer;
	  transition: color 0.3s ease;
	}

	.share-header button:hover {
	  color: var(--accent-red);
	}

	.share-preview {
	  background: var(--bg-tertiary);
	  border: 1px solid var(--border-light);
	  border-radius: 16px;
	  padding: 20px;
	  margin-bottom: 24px;
	  text-align: center;
	}

	.preview-numbers {
	  display: flex;
	  justify-content: center;
	  gap: 8px;
	  margin-bottom: 12px;
	}

	.mini-ball {
	  width: 40px;
	  height: 40px;
	  border-radius: 50%;
	  display: flex;
	  align-items: center;
	  justify-content: center;
	  font-size: 18px;
	  font-weight: 700;
	  color: #fff;
	}

	.preview-score {
	  font-size: var(--font-size-lg);
	  color: var(--accent-primary);
	  font-weight: 700;
	}

	.share-options {
	  display: grid;
	  grid-template-columns: repeat(3, 1fr);
	  gap: 12px;
	}

	.share-btn {
	  display: flex;
	  flex-direction: column;
	  align-items: center;
	  gap: 8px;
	  padding: 16px;
	  background: var(--bg-tertiary);
	  border: 2px solid var(--border-light);
	  border-radius: 12px;
	  color: var(--text-primary);
	  cursor: pointer;
	  transition: all 0.3s ease;
	  font-weight: 600;
	}

	.share-btn:hover {
	  background: var(--bg-card-hover);
	  border-color: var(--accent-primary);
	  transform: translateY(-2px);
	  box-shadow: var(--shadow-sm);
	}

	.share-icon {
	  font-size: 32px;
	}

	.share-btn span:last-child {
	  font-size: 14px;
	}

	@media (max-width: 768px) {
	  .share-options {
		grid-template-columns: 1fr;
	  }
	}

	/* Toast UI */
	.toast-container{
	  position: fixed;
	  left: 50%;
	  bottom: 18px;
	  transform: translateX(-50%);
	  z-index: 99999;
	  display: flex;
	  flex-direction: column;
	  gap: 10px;
	  width: min(520px, calc(100% - 24px));
	  pointer-events: none;
	}
	.toast{
	  pointer-events: auto;
	  display:flex;
	  align-items:center;
	  justify-content: space-between;
	  gap:12px;
	  padding: 12px 14px;
	  border-radius: 12px;
	  background: rgba(20,20,25,0.92);
	  color: #fff;
	  box-shadow: 0 10px 28px rgba(0,0,0,0.25);
	}
	.toast--success{ background: rgba(22, 120, 80, 0.92); }
	.toast--error{ background: rgba(160, 50, 50, 0.92); }
	.toast__msg{ font-size: 14px; line-height: 1.4; }
	.toast__close{
	  border:0; background: transparent; color:#fff;
	  font-size: 18px; line-height: 1;
	  cursor: pointer;
	}
	
	/* Button loading */
	.charge-submit-btn[disabled]{ opacity: .65; cursor: not-allowed; }
	.btn-spinner{
	  display:none;
	  width: 16px; height:16px;
	  border-radius: 50%;
	  border: 2px solid rgba(255,255,255,.35);
	  border-top-color: rgba(255,255,255,1);
	  animation: spin .8s linear infinite;
	  margin-right: 8px;
	  vertical-align: -2px;
	}
	.charge-submit-btn.is-loading .btn-spinner{ display:inline-block; }
	@keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>
  <!-- 상단 네비게이션 -->
  <nav class="app-navbar">
    <a href="<?php echo G5_URL;?>" class="app-logo">
      <div class="app-logo-icon">
        <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
          <!-- 3D Lotto Ball -->
          <circle cx="11" cy="12" r="8" fill="url(#gold-ball-nav)"/>
          <ellipse cx="8" cy="9" rx="3" ry="2" fill="rgba(255,255,255,0.5)" transform="rotate(-25 8 9)"/>
          <!-- AI Analysis Ring -->
          <circle cx="18" cy="18" r="7" fill="none" stroke="#030711" stroke-width="2"/>
          <!-- Neural Nodes -->
          <circle cx="16" cy="16" r="1.2" fill="#030711"/>
          <circle cx="20" cy="19" r="1.2" fill="#030711"/>
          <circle cx="18" cy="14" r="1.2" fill="#030711"/>
          <!-- Handle -->
          <line x1="23" y1="23" x2="28" y2="28" stroke="#030711" stroke-width="2.5" stroke-linecap="round"/>
          <defs>
            <linearGradient id="gold-ball-nav" x1="20%" y1="20%" x2="80%" y2="80%">
              <stop offset="0%" stop-color="#ffe066"/>
              <stop offset="50%" stop-color="#ffd700"/>
              <stop offset="100%" stop-color="#cc9f00"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      오늘로또
    </a>
    <div class="navbar-right">
      <div class="credit-container" id="creditDisplay">
        <div class="credit-icon">💳</div>
        <div class="credit-details">
          <div class="credit-total">
            <span class="credit-label">보유 크레딧</span>
            <span class="credit-value" id="navCredit">1회</span>
      </div>
          <div class="credit-breakdown" id="creditBreakdown">
            <span class="credit-free">무료 1회</span>
          </div>
        </div>
        <button class="credit-charge-btn" id="chargeBtn" onclick="showChargeModal()" aria-label="크레딧 충전">
          <span>충전</span>
          <span class="btn-icon">⚡</span>
        </button>
      </div>
      <a href="auth.php" class="user-avatar-btn" id="userAvatarBtn" aria-label="사용자 프로필">
        <span id="userAvatarIcon">👤</span>
      </a>
    </div>
  </nav>

  <!-- 메인 컨테이너 -->
  <div class="app-container">
    
    <!-- ===== 대시보드 ===== -->
    <div class="dashboard-view" id="dashboardView">

      <!-- 🔴 최신 당첨 결과 섹션 (신뢰도 기능 1) -->
      <div class="latest-result-section" id="latestResultSection">
        <div class="latest-header">
          <div class="live-badge">
            <span class="live-dot"></span>
            LIVE 최신 당첨 결과
          </div>
          <span class="latest-round" id="latestRound">
            <?php echo $latest_round_fmt ? $latest_round_fmt.'회차' : '---'; ?>
          </span>
        </div>
        <div class="latest-balls" id="latestBalls">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="latest-info">
          <span class="latest-prize" id="latestPrize">
            추첨일: <span id="latestDate"><?php echo isset($latest['draw_date']) ? $latest['draw_date'] : '--'; ?></span>
          </span>
          <a href="https://dhlottery.co.kr" target="_blank" rel="noopener" class="latest-link">
            동행복권에서 확인 →
          </a>
        </div>
      </div>

      <!-- 📋 AI vs 실제 비교 섹션 (신뢰도 기능 2) -->
      <div class="ai-comparison-section" id="aiComparisonSection">
        <div class="comparison-header">
          <span class="comparison-icon">📋</span>
          <span class="comparison-title">지난 주 AI 추천 vs 실제 결과</span>
        </div>
        <div class="comparison-rows" id="comparisonRows">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="comparison-result" id="comparisonResult">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="comparison-disclaimer">
          ⚠️ AI는 당첨을 보장하지 않습니다. 통계 기반 참고 정보입니다.
        </div>
      </div>

      <!-- ✅ 신뢰 배지 (신뢰도 기능 5) -->
      <div class="trust-section">
        <div class="trust-item">
          <span class="trust-check">✓</span>
          동행복권 공식 데이터 사용
        </div>

        <div class="trust-item">
          <span class="trust-check">✓</span>
          <span id="trustRound"><?php echo $latest_round_fmt ?: '---'; ?></span>회차 실시간 반영
        </div>

        <?php
          $total_rounds  = count($history_rows);
          $total_numbers = $total_rounds * 6;
        ?>
        <div class="trust-item">
          <span class="trust-check">✓</span>
          최근 <?php echo number_format($total_rounds); ?>회차 ·
          <?php echo number_format($total_numbers); ?>개 번호 분석
        </div>

        <div class="trust-item">
          <span class="trust-check">✓</span>
          알고리즘 100% 투명 공개
        </div>
      </div>
      
      <!-- 환영 메시지 -->
      <div class="welcome-section">
        <div class="user-avatar" id="userAvatar">김</div>
        <div class="welcome-text">
          <h1 id="welcomeTitle">👋 김** 님, 환영합니다!</h1>
          <p id="welcomeSubtitle">
            <?php echo $current_round_fmt ? $current_round_fmt.'회차 데이터로 분석해 드릴게요' : '동행복권 데이터를 기반으로 분석해 드릴게요'; ?>
          </p>
        </div>
      </div>

      <!-- 분석 스타일 선택 -->
      <div class="style-section">
        <div class="style-title">
          📊 분석 스타일 선택
          <span class="style-multi-badge">복수 선택 가능</span>
        </div>
        <div class="style-buttons-grid">
          <button class="style-btn active" data-style="hotcold" aria-label="Hot/Cold 분석 스타일 선택" aria-pressed="true">
            <span class="style-icon">🔥</span>
            <div>
              <span class="style-name">Hot/Cold</span>
              <span class="style-desc">과출/미출 패턴 분석</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn active" data-style="ac" aria-label="AC값 분석 스타일 선택" aria-pressed="true">
            <span class="style-icon">🧮</span>
            <div>
              <span class="style-name">AC값 분석</span>
              <span class="style-desc">숫자 다양성 지수</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="balance" aria-label="홀짝/고저 분석 스타일 선택" aria-pressed="false">
            <span class="style-icon">⚖️</span>
            <div>
              <span class="style-name">홀짝/고저</span>
              <span class="style-desc">균형 비율 최적화</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="color">
            <span class="style-icon">🎨</span>
            <div>
              <span class="style-name">색상볼 통계</span>
              <span class="style-desc">노랑/파랑/빨강 분포</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="correlation">
            <span class="style-icon">🔗</span>
            <div>
              <span class="style-name">상관관계</span>
              <span class="style-desc">동반출현 패턴</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="montecarlo">
            <span class="style-icon">🎲</span>
            <div>
              <span class="style-name">몬테카를로</span>
              <span class="style-desc">확률 시뮬레이션</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="sum">
            <span class="style-icon">➕</span>
            <div>
              <span class="style-name">합계 분석</span>
              <span class="style-desc">총합 구간 최적화</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="cycle">
            <span class="style-icon">🔄</span>
            <div>
              <span class="style-name">주기 분석</span>
              <span class="style-desc">번호별 출현 주기</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="lastdigit">
            <span class="style-icon">🔢</span>
            <div>
              <span class="style-name">끝수 분석</span>
              <span class="style-desc">끝자리 분포 균형</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="consecutive">
            <span class="style-icon">📊</span>
            <div>
              <span class="style-name">연속번호</span>
              <span class="style-desc">연번 패턴 분석</span>
            </div>
            <span class="style-check">✓</span>
          </button>
        </div>
        <div class="style-selected-count">
          <span id="selectedCount">2</span>개 스타일 선택됨
        </div>
      </div>

      <!-- 분석 시작 버튼 -->
      <div class="analyze-section">
        <button class="analyze-btn" id="analyzeBtn" aria-label="AI 번호 분석 시작" aria-describedby="analyzeCostInfo">
          🔮 AI 분석 시작하기
        </button>
        <p class="analyze-cost" id="analyzeCostInfo" role="status" aria-live="polite">지금 가입 시 <span>무료 분석 2회</span> 제공</p>
      </div>

      <!-- 이전 분석 내역 -->
      <div class="history-section">
        <div class="history-title">📜 이전 분석 내역</div>
        <div class="history-empty">
          아직 분석 내역이 없습니다.<br>
          첫 번째 분석을 시작해보세요! 🎯
        </div>
      </div>

    </div>

    <!-- ===== 분석 결과 ===== -->
    <div class="result-view" id="resultView" role="region" aria-label="AI 분석 결과">
      
      <!-- 결과 인트로 -->
      <div class="result-intro">
        <div class="result-badge" role="status" aria-live="polite">
          ✨ AI 분석 완료
        </div>
        <h2>당신만의 맞춤 조합입니다</h2>
        <p id="resultSubtitle">5개 스타일 · <?php echo $current_round_fmt ?: '---'; ?>회차 데이터 기반</p>
      </div>

      <!-- 결과 네비게이션 -->
      <nav class="result-nav" id="resultNav" role="navigation" aria-label="결과 카드 네비게이션">
        <!-- 동적으로 생성됨 -->
      </nav>

      <!-- 결과 카드 컨테이너 -->
      <div class="result-cards-container" id="resultCardsContainer" role="main" aria-label="분석 결과 카드">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 결과 인디케이터 -->
      <div class="result-indicators" id="resultIndicators" role="tablist" aria-label="결과 카드 인디케이터">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 액션 버튼 -->
      <div class="result-actions">
        <button class="action-btn action-btn-primary" id="reanalyzeBtn">
          🔄 다시 분석하기
        </button>
        <button class="action-btn action-btn-secondary">
          💾 저장하기
        </button>
      </div>

      <!-- 면책 조항 -->
      <div class="disclaimer">
        <p><strong>⚠️ 중요 안내</strong></p>
        <ul>
          <li>통계 패턴 기반 참고 정보</li>
          <li>모든 조합 확률 동일 (1/8,145,060)</li>
          <li>당첨 보장/예측 아님</li>
          <li>만 19세 이상 이용</li>
          <li>동행복권에서만 구매</li>
        </ul>
      </div>

      <!-- 대시보드로 돌아가기 -->
      <button class="action-btn action-btn-secondary" style="width: 100%;" id="backBtn">
        ← 대시보드로 돌아가기
      </button>

      <!-- SEO 연결 섹션 -->
      <?php include_once(G5_PATH . '/seo/_result_addons.php'); ?>

    </div>
  </div>

  <!-- 로딩 모달 -->
  <div class="loading-modal" id="loadingModal">
    <div class="loading-container">
      <div class="loading-header">
      <div class="loading-spinner"></div>
        <h3 style="color: var(--accent-primary); margin: 0; font-size: var(--font-size-xl); font-weight: 700;">AI 분석 중</h3>
      </div>
      
      <div class="loading-percentage" id="loadingPercentage">0%</div>
      
      <div class="loading-progress">
        <div class="loading-bar" id="loadingBar"></div>
      </div>
      
      <div class="loading-text" id="loadingText">
        <span class="loading-icon">📊</span>
        <span class="loading-text-content">데이터 준비 중...</span>
      </div>
      
      <div class="loading-data" id="loadingData">
        <div class="data-header">
          <span class="data-source">📡 동행복권 데이터</span>
          <span class="data-update" id="dataUpdate">업데이트: --</span>
        </div>
        <div class="data-stats" id="dataStats">
          <!-- 동적으로 채워짐 -->
        </div>
        <div class="recent-numbers" id="recentNumbers">
          <!-- 최근 당첨 번호 표시 -->
        </div>
      </div>
    </div>
  </div>

	<!-- Toast Container -->
	<div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>

  <!-- 번호 생성 엔진: DB 데이터로 LOTTO_HISTORY_DATA 주입 -->
  <script>
    // lotto-generator.js 에서 사용하는 전역 상수
    window.LOTTO_HISTORY_DATA = <?php echo json_encode($lotto_history_map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    // 최근 8회 AI 추천 vs 실제 데이터 (성과 아카이브용)
    window.LOTTO_ARCHIVE_DATA = <?php echo json_encode($ai_archive_rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    window.LOTTO_ARCHIVE_SUMMARY = <?php echo json_encode($ai_archive_summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  </script>

  <script src="/scripts/lotto-generator.js"></script>

  <script>
    /* =========================================================
     * ✅ 안전장치(중요)
     * - "아무번호도 안 뜸"의 1순위 원인은 JS 런타임 에러로 completeAnalysis가 중단되는 경우입니다.
     *   (예: getBallColor 미정의 / lottoGenerator 전역 미정의 / generate 결과가 비정상)
     * - 아래는 전역 함수/객체가 없을 때도 최소 동작(번호 생성/렌더/저장)하게 만드는 보호 코드입니다.
     * ========================================================= */

    // ===== 유틸리티 함수 =====
    
    // XSS 방지를 위한 HTML 이스케이프 함수
    function escapeHtml(text) {
      if (typeof text !== 'string') return '';
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
        '/': '&#x2F;'
      };
      return String(text).replace(/[&<>"'\/]/g, s => map[s]);
    }

    // 사용자 입력 검증 강화
    function validateRoundInput(value) {
      const round = parseInt(value, 10);
      if (isNaN(round) || round < 1 || round > 9999) {
        return { valid: false, error: '올바른 회차를 입력해주세요 (1-9999)' };
      }
      return { valid: true, value: round };
    }

    // 디바운스 함수 (성능 최적화)
    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    // 쓰로틀 함수 (성능 최적화)
    function throttle(func, limit) {
      let inThrottle;
      return function(...args) {
        if (!inThrottle) {
          func.apply(this, args);
          inThrottle = true;
          setTimeout(() => inThrottle = false, limit);
        }
      };
    }

    // API 호출 래퍼 함수 (에러 처리 개선)
    async function fetchWithErrorHandling(url, options = {}) {
      try {
        const response = await fetch(url, {
          ...options,
          headers: {
            'Content-Type': 'application/json; charset=utf-8',
            ...options.headers
          }
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        return { success: true, data };
      } catch (error) {
        console.error('API Error:', error);
        if (typeof showToast === 'function') {
          showToast('네트워크 오류가 발생했습니다. 잠시 후 다시 시도해주세요.', 'error');
        }
        return { success: false, error: error.message };
      }
    }

    // 전역 에러 핸들러
    window.addEventListener('error', function(event) {
      console.error('Global error:', event.error);
      if (typeof showToast === 'function') {
        showToast('일시적인 오류가 발생했습니다. 페이지를 새로고침해주세요.', 'error');
      }
    });

    window.addEventListener('unhandledrejection', function(event) {
      console.error('Unhandled promise rejection:', event.reason);
      if (typeof showToast === 'function') {
        showToast('일시적인 오류가 발생했습니다. 잠시 후 다시 시도해주세요.', 'error');
      }
      event.preventDefault(); // 기본 에러 로그 방지
    });

    // 웹 분석 이벤트 트래킹 함수
    function trackEvent(eventName, eventParams = {}) {
      // Google Analytics 4
      if (typeof gtag !== 'undefined') {
        gtag('event', eventName, eventParams);
      }
      
      // Google Tag Manager
      if (typeof dataLayer !== 'undefined') {
        dataLayer.push({
          event: eventName,
          ...eventParams
        });
      }

      // 커스텀 분석 (필요시)
      if (window.customAnalytics && typeof window.customAnalytics.track === 'function') {
        window.customAnalytics.track(eventName, eventParams);
      }
    }

    // 온라인/오프라인 상태 처리
    window.addEventListener('online', () => {
      if (typeof showToast === 'function') {
        showToast('인터넷에 다시 연결되었습니다.', 'success');
      }
      if (typeof refreshCreditBalance === 'function') {
        refreshCreditBalance();
      }
      trackEvent('connection_restored');
    });

    window.addEventListener('offline', () => {
      if (typeof showToast === 'function') {
        showToast('인터넷 연결이 끊어졌습니다. 일부 기능이 제한될 수 있습니다.', 'error');
      }
      trackEvent('connection_lost');
    });

    // 서비스 워커 등록 (오프라인 지원)
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
          .then(registration => {
            console.log('Service Worker registered:', registration.scope);
            trackEvent('sw_registered');
          })
          .catch(err => {
            console.log('Service Worker registration failed:', err);
            // 서비스 워커 실패는 치명적이지 않으므로 조용히 처리
          });
      });
    }

    // 1) getBallColor 전역이 없다면 fallback 제공 (렌더링에서 즉시 에러 방지)
    if (typeof window.getBallColor !== 'function') {
      window.getBallColor = function (n) {
        n = Number(n) || 0;
        if (n <= 10) return 'yellow';
        if (n <= 20) return 'blue';
        if (n <= 30) return 'red';
        if (n <= 40) return 'gray';
        return 'green';
      };
    }

    // 2) lottoGenerator 전역이 다르게 노출되는 경우를 흡수
    //    (lotto-generator.js 구현에 따라 전역명이 달라질 수 있어요)
    function pickGlobalGenerator() {
      return (
        window.lottoGenerator ||
        window.LottoGenerator ||
        window.lotto_generator ||
        window.LOTTO_GENERATOR ||
        null
      );
    }

    // 3) 최후의 fallback: 최소 기능 generator (번호 6개 생성 + report/stories)
    function createFallbackGenerator() {
      const genNumbers = () => {
        const set = new Set();
        while (set.size < 6) set.add(Math.floor(Math.random() * 45) + 1);
        return Array.from(set).sort((a, b) => a - b);
      };
      return {
        ready: true,
        async init() { this.ready = true; },
        generate() { return this.generateBasic(); },
        generateBasic() {
          const numbers = genNumbers();
          return {
            numbers,
            score: 80,
            stories: numbers.map(n => ({
              number: n,
              type: 'balance',
              label: '참고',
              description: '기본 생성 조합(엔진 오류 시 대체)'
           }))
         };
        },
        generateReport(r) {
          return {
            summary: ['기본 조합', '참고용', '엔진 대체'],
            insights: ['번호 생성 엔진이 비정상이라 기본 조합으로 대체했습니다.']
          };
        },
        dataLoader: { data: null }
      };
    }

    // 4) generator 준비 보장
    async function ensureGeneratorReady() {
      let g = pickGlobalGenerator();
      if (!g) {
        console.error('[lotto] lottoGenerator 전역을 찾지 못해 fallback generator를 사용합니다.');
        window.lottoGenerator = createFallbackGenerator();
        g = window.lottoGenerator;
      } else {
        // 전역명을 window.lottoGenerator로 통일
        window.lottoGenerator = g;
      }

      try {
        if (!g.ready && typeof g.init === 'function') {
          await g.init();
        }
      } catch (e) {
        console.error('[lotto] generator.init() 실패 → fallback 사용', e);
        window.lottoGenerator = createFallbackGenerator();
      }
      return window.lottoGenerator;
    }
  </script>
  
  <script>
	//무료분석 초기화시 사용
	//localStorage.removeItem('lottoinsight_free');
	//localStorage.removeItem('lottoinsight_paid');

    // PHP 회원 정보 기반 기본 사용자 이름
    // 서버에서 주입한 현재 크레딧 (무료/유료)
    // ✅ PHP → JS 서버 주입값(필수)
    const SERVER_IS_MEMBER = <?php echo !empty($is_member) ? 'true' : 'false'; ?>;
    const SERVER_MB_ID     = <?php echo json_encode($member['mb_id'] ?? '', JSON_UNESCAPED_UNICODE); ?>;

    // 서버에서 주입한 현재 크레딧 (무료/유료)
    const SERVER_FREE_CREDIT = <?php echo $server_free_credits; ?>;
    const SERVER_PAID_CREDIT = <?php echo $server_paid_credits; ?>;

    // ✅ DB에 저장된 "실제 최신 회차"(LIVE·당첨 결과용)
    const DB_LATEST_ROUND = <?php echo (int)$latest_draw_no; ?>;

    const DEFAULT_USER_NAME = <?php
      $default_user_name = '게스트';
      if (isset($member) && is_array($member)) {
          if (!empty($member['mb_nick'])) {
              $default_user_name = $member['mb_nick'];
          } elseif (!empty($member['mb_name'])) {
              $default_user_name = $member['mb_name'];
          }
      }
      echo json_encode($default_user_name, JSON_UNESCAPED_UNICODE);
    ?>;

    // ===== 스토리지 매니저 =====
    const StorageManager = {
      KEYS: {
        USER: 'lottoinsight_user',
        USER_INFO: 'lottoinsight_user_info',
        FREE_CREDIT: 'lottoinsight_free',
        PAID_CREDIT: 'lottoinsight_paid',
        LOGIN_TYPE: 'lottoinsight_login_type',
        HISTORY: 'lottoinsight_history',
        SAVED: 'lottoinsight_saved'
      },
      
      get(key, defaultValue = null) {
        try {
          const value = localStorage.getItem(key);
          return value ? JSON.parse(value) : defaultValue;
        } catch {
          return localStorage.getItem(key) || defaultValue;
        }
      },
      
      set(key, value) {
        try {
          localStorage.setItem(key, typeof value === 'object' ? JSON.stringify(value) : value);
        } catch (e) {
          console.error('저장 실패:', e);
        }
      },
      
      // 히스토리 관리
      getHistory() {
        return this.get(this.KEYS.HISTORY, []);
      },
      
      addHistory(item) {
        const history = this.getHistory();
        history.unshift({
          ...item,
          id: Date.now(),
          date: new Date().toISOString()
        });
        // 최대 50개까지만 저장
        this.set(this.KEYS.HISTORY, history.slice(0, 50));
      },
      
      // 저장된 번호 관리
      getSaved() {
        return this.get(this.KEYS.SAVED, []);
      },
      
      addSaved(item) {
        const saved = this.getSaved();
        saved.unshift({
          ...item,
          id: Date.now(),
          savedAt: new Date().toISOString()
        });
        this.set(this.KEYS.SAVED, saved.slice(0, 100));
        return true;
      },
      
      removeSaved(id) {
        const saved = this.getSaved();
        this.set(this.KEYS.SAVED, saved.filter(s => s.id !== id));
      },
      
      // 크레딧 관리 (서버 값 기준)
      getCredits() {
        // 서버에서 주입한 현재 잔액을 그대로 사용
        return {
          free: SERVER_FREE_CREDIT,
          paid: SERVER_PAID_CREDIT
        };
      },

      // 향후 토스PG/무통장 연동 시 프론트 임시 표시용으로만 사용
      setCredits(free, paid) {
        localStorage.setItem(this.KEYS.FREE_CREDIT, String(free));
        localStorage.setItem(this.KEYS.PAID_CREDIT, String(paid));
      },
      
      // 실제 차감은 서버(lotto_use_one_analysis)가 담당
      useCredit(amount) {
        const { free, paid } = this.getCredits();
        return { free, paid };
      }
    };
    
    // ===== 로그인 상태 체크 =====
    function checkLoginStatus() {
      // ✅ 이 페이지는 PHP에서 이미 로그인 체크를 통과한 상태가 기본
      const loggedUser = (typeof SERVER_MB_ID !== 'undefined' ? SERVER_MB_ID : '') || localStorage.getItem(StorageManager.KEYS.USER);
      const userInfoStr = localStorage.getItem(StorageManager.KEYS.USER_INFO);
      const loginType = localStorage.getItem(StorageManager.KEYS.LOGIN_TYPE);
      const { free, paid } = StorageManager.getCredits();
      
      let userInfo = null;
      try {
        userInfo = userInfoStr ? JSON.parse(userInfoStr) : null;
      } catch (e) {
        console.error('사용자 정보 파싱 실패:', e);
      }
      
      return {
        isLoggedIn: (typeof SERVER_IS_MEMBER !== 'undefined' ? SERVER_IS_MEMBER : false) || !!loggedUser,
        freeCredit: free,
        paidCredit: paid,
        userName: userInfo?.nickname || DEFAULT_USER_NAME,
        profileImage: userInfo?.profileImage || null,
        userId: loggedUser || null,
        loginType: loginType || null
      };
    }
    
    const loginStatus = checkLoginStatus();
    
    let state = {
      isLoggedIn: loginStatus.isLoggedIn,
      freeCredit: loginStatus.freeCredit,
      paidCredit: loginStatus.paidCredit,
      userName: loginStatus.userName,
      profileImage: loginStatus.profileImage,
      userId: loginStatus.userId,
      loginType: loginStatus.loginType,
      selectedStyles: ['hotcold', 'ac'],
      history: StorageManager.getHistory(),
      results: []
    };

    // ===== 상태 관리 개선 (StateManager) =====
    const StateManager = {
      history: [],
      maxHistory: 50,
      
      updateState(newState) {
        // 이전 상태 저장
        if (this.history.length >= this.maxHistory) {
          this.history.shift();
        }
        this.history.push({ ...state, timestamp: Date.now() });
        
        // 새 상태 적용
        Object.assign(state, newState);
        
        // 관련 UI 업데이트
        this.notifySubscribers(newState);
      },
      
      subscribers: [],
      
      subscribe(callback) {
        this.subscribers.push(callback);
        return () => {
          this.subscribers = this.subscribers.filter(cb => cb !== callback);
        };
      },
      
      notifySubscribers(newState) {
        this.subscribers.forEach(callback => {
          try {
            callback(newState);
          } catch (e) {
            console.error('State subscriber error:', e);
          }
        });
      },
      
      undo() {
        if (this.history.length > 0) {
          const previousState = this.history.pop();
          Object.assign(state, previousState);
          this.notifySubscribers(state);
          return true;
        }
        return false;
      },
      
      getState() {
        return { ...state };
      }
    };

    // 크레딧 변경 시 자동 UI 업데이트 구독
    StateManager.subscribe((newState) => {
      if (newState.freeCredit !== undefined || newState.paidCredit !== undefined) {
        if (typeof updateCreditDisplay === 'function') {
          updateCreditDisplay();
        }
      }
    });

    // ─────────────────────────────────────
    // 분석 대상 회차 헬퍼
    //  - PHP에서 이미 $current_round = (DB 최신 draw_no + 1) 로 계산된 값 사용
    // ─────────────────────────────────────
    function getAnalysisRound() {
      // 나중에 JS 쪽 회차 기준을 쓰고 싶으면 아래 주석 해제해서 사용 가능
      // try {
      //   if (window.LottoDataLoader && LottoDataLoader.data && LottoDataLoader.data.currentRound) {
      //     return Number(LottoDataLoader.data.currentRound) + 1;
      //   }
      // } catch (e) {}
      // 기본은 PHP에서 계산된 회차
      return <?php echo (int)$current_round; ?> || 0;
    }

    // ===== DOM 요소 =====
    const dashboardView = document.getElementById('dashboardView');
    const resultView = document.getElementById('resultView');
    const loadingModal = document.getElementById('loadingModal');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const reanalyzeBtn = document.getElementById('reanalyzeBtn');
    const backBtn = document.getElementById('backBtn');
    const styleButtons = document.querySelectorAll('.style-btn');
    const selectedCountEl = document.getElementById('selectedCount');

    // ===== 번호별 색상 반환 함수 (동행복권 기준) =====
    function getBallColor(num) {
      if (num >= 1 && num <= 10) return 'yellow';
      if (num >= 11 && num <= 20) return 'blue';
      if (num >= 21 && num <= 30) return 'red';
      if (num >= 31 && num <= 40) return 'gray';
      return 'green'; // 41-45
    }

    // ===== 최신 당첨 결과 표시 (DB g5_lotto_draw 기준) =====
    function renderLatestResult() {
      const history = window.LOTTO_HISTORY_DATA || {};
      const rounds  = Object.keys(history).map(Number);

      if (!rounds.length) return; // 표시할 데이터 없음

      // 기본은 DB에서 내려준 최신 회차, 없으면 history 키 중 최대값 사용
      let latestRoundNo = DB_LATEST_ROUND || 0;
      if (!latestRoundNo || !history[latestRoundNo]) {
        latestRoundNo = rounds.sort((a, b) => b - a)[0];
      }

      const latest = history[latestRoundNo];
      if (!latest) return;

      // 🔴 LIVE 헤더: 실제 최신 회차
      const latestRoundEl = document.getElementById('latestRound');
      if (latestRoundEl) {
        latestRoundEl.textContent = latestRoundNo.toLocaleString() + '회차';
      }

      // 추첨일
      const latestDateEl = document.getElementById('latestDate');
      if (latestDateEl) {
        latestDateEl.textContent = latest.date || latest.draw_date || '--';
      }

      // 신뢰 배지: "실시간 반영 회차"도 실제 최신 회차로
      const trustRoundEl = document.getElementById('trustRound');
      if (trustRoundEl) {
        trustRoundEl.textContent = latestRoundNo.toLocaleString();
      }

      // 번호 + 보너스 렌더링
      const ballsContainer = document.getElementById('latestBalls');
      if (!ballsContainer) return;

      const nums  = latest.numbers || [];
      const bonus = latest.bonus;

      let html = nums.map(n =>
        `<div class="latest-ball ball-${getBallColor(n)}">${n}</div>`
      ).join('');

      if (bonus) {
        html += `<span class="bonus-separator">+</span>`;
        html += `<div class="latest-ball ball-${getBallColor(bonus)}">${bonus}</div>`;
      }

      ballsContainer.innerHTML = html;
    }

    // ===== AI vs 실제 비교 표시 (DB 아카이브 기반) =====
    function renderAiComparison() {
      const section         = document.getElementById('aiComparisonSection');
      const rowsContainer   = document.getElementById('comparisonRows');
      const resultContainer = document.getElementById('comparisonResult');

      const archive = window.LOTTO_ARCHIVE_DATA || [];

      // 아카이브 데이터/DOM 이 없으면 섹션 숨김
      if (!archive.length || !rowsContainer || !resultContainer) {
        if (section) section.style.display = 'none';
        return;
      }

      // PHP 에서 ORDER BY ai.round DESC 로 내려오므로 0번째가 가장 최근 회차
      const last = archive[0];

      const aiNumbers   = last.ai_numbers   || [];
      const realNumbers = last.real_numbers || [];
      const matchedList = last.matched      || [];
      const matchCount  = (typeof last.match_count === 'number')
        ? last.match_count
        : matchedList.length;

      // 번호가 하나도 없으면 섹션 숨김
      if (!aiNumbers.length || !realNumbers.length) {
        if (section) section.style.display = 'none';
        return;
      }

      const matchedSet = new Set(matchedList);

      // 비교 행 렌더링
      rowsContainer.innerHTML = `
        <div class="comparison-row">
          <span class="comparison-label">AI 추천</span>
          <div class="comparison-balls">
            ${aiNumbers.map(n => {
              const isMatched = matchedSet.has(n);
              return `<div class="mini-ball ball-${getBallColor(n)} ${isMatched ? 'matched' : ''}">${n}</div>`;
            }).join('')}
          </div>
        </div>
        <div class="comparison-row">
          <span class="comparison-label">실제 당첨</span>
          <div class="comparison-balls">
            ${realNumbers.map(n => {
              const isMatched = matchedSet.has(n);
              return `<div class="mini-ball ball-${getBallColor(n)} ${isMatched ? 'matched' : ''}">${n}</div>`;
            }).join('')}
          </div>
        </div>
      `;

      // 결과 표시
      resultContainer.innerHTML = `
        <span class="match-count">✅ ${matchCount}개 일치</span>
        <span class="match-numbers">
          ${matchedList.length ? `(${matchedList.join(', ')})` : '(일치 번호 없음)'}
        </span>
      `;
    }

    // ===== 스타일 선택 =====
    styleButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const style = btn.dataset.style;
        
        if (btn.classList.contains('active')) {
          if (state.selectedStyles.length > 1) {
            btn.classList.remove('active');
            btn.setAttribute('aria-pressed', 'false');
            state.selectedStyles = state.selectedStyles.filter(s => s !== style);
          }
        } else {
          btn.classList.add('active');
          btn.setAttribute('aria-pressed', 'true');
          state.selectedStyles.push(style);
        }
        
        if (selectedCountEl) {
          selectedCountEl.textContent = state.selectedStyles.length;
          selectedCountEl.setAttribute('aria-live', 'polite');
        }
        updateAnalyzeCost();
      });
    });

    // ===== 네비게이션 업데이트 =====
    function updateNavbar() {
      const navCredit = document.getElementById('navCredit');
      const userAvatarBtn = document.getElementById('userAvatarBtn');
      const userAvatarIcon = document.getElementById('userAvatarIcon');
      
      // 크레딧 표시
      const totalCredits = state.freeCredit + state.paidCredit;
      if (navCredit) {
        navCredit.textContent = `${totalCredits}회`;
      }
      
      // 사용자 아바타 표시
      if (userAvatarBtn && userAvatarIcon) {
        if (state.isLoggedIn) {
          userAvatarBtn.classList.add('logged-in');
          
          if (state.profileImage) {
            userAvatarIcon.innerHTML = `<img src="${state.profileImage}" alt="프로필">`;
          } else {
            const firstChar = state.userName.charAt(0);
            userAvatarIcon.textContent = firstChar === '게' ? '👤' : firstChar;
            userAvatarIcon.style.color = 'var(--primary-dark)';
            userAvatarIcon.style.fontWeight = '700';
          }
        } else {
          userAvatarBtn.classList.remove('logged-in');
          userAvatarIcon.textContent = '👤';
        }
      }
    }

    // ===== 환영 메시지 =====
    function updateWelcomeMessage() {
      const avatar = document.getElementById('userAvatar');
      const title = document.getElementById('welcomeTitle');
      const subtitle = document.getElementById('welcomeSubtitle');
      
      // 로그인 상태에 따른 표시
      let displayName = DEFAULT_USER_NAME;
      if (state.isLoggedIn) {
        displayName = state.userName || DEFAULT_USER_NAME;
      }
      
      const firstChar = displayName.charAt(0).toUpperCase();
      
      // 아바타 표시
      if (state.profileImage) {
        avatar.innerHTML = `<img src="${state.profileImage}" alt="프로필" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
      } else {
        avatar.textContent = firstChar === '게' ? '🎱' : firstChar;
      }
      
      // 환영 메시지
      if (state.isLoggedIn) {
        title.innerHTML = `👋 ${displayName}님, 환영합니다!`;
        subtitle.textContent = `${getAnalysisRound().toLocaleString()}회차 데이터로 분석해 드릴게요`;

      } else {
        title.innerHTML = `🎱 AI 로또 분석`;
        subtitle.innerHTML = `<a href="<?php echo G5_URL; ?>/auth.php" style="color:var(--accent-gold);text-decoration:underline;">로그인</a>하면 분석 결과를 저장할 수 있어요`;
      }
    }

    // ===== 분석 시작 =====
    if (analyzeBtn) analyzeBtn.addEventListener('click', startAnalysis);
    if (reanalyzeBtn) reanalyzeBtn.addEventListener('click', startAnalysis);

    async function startAnalysis() {
      const requiredCredits = 1; // 분석 1번 = 크레딧 1개

      // 서버에 크레딧 사용 요청 (실패 시 이후 로직 중단)
      try {
        const result = await fetchWithErrorHandling(location.pathname, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: new URLSearchParams({
            mode: 'use_credit'
          })
        });

        if (!result.success) {
          throw new Error(result.error || '크레딧 사용 실패');
        }

        const data = result.data;

        if (!data.success) {
          // 서버 기준 최신 잔액으로 상태 동기화
          const updates = {};
          if (typeof data.credit_balance !== 'undefined') {
            updates.paidCredit = Number(data.credit_balance) || 0;
          }
          if (typeof data.free_uses !== 'undefined') {
            updates.freeCredit = Number(data.free_uses) || 0;
          }
          if (Object.keys(updates).length > 0) {
            StateManager.updateState(updates);
          }

          if (data.reason === 'NO_CREDIT') {
            alert(
              `분석에 필요한 크레딧이 부족합니다.\n` +
              `필요: ${requiredCredits}크레딧\n` +
              `보유: ${(state.freeCredit + state.paidCredit)}크레딧`
            );
		} else if (data.reason === 'NOT_LOGGED_IN') {
		  alert('로그인 후 이용 가능한 서비스입니다.');
		  location.href = "<?php echo G5_URL; ?>/auth.php";
		} else {
            alert('크레딧 처리 중 오류가 발생했습니다.\n잠시 후 다시 시도해 주세요.');
          }
          return;
        }

        // 성공 시: 서버에서 내려준 최신 잔액 반영
        StateManager.updateState({
          paidCredit: Number(data.credit_balance) || 0,
          freeCredit: Number(data.free_uses) || 0
        });
      } catch (e) {
        console.error('use_credit 요청 오류', e);
        alert('네트워크 오류로 크레딧 검증에 실패했습니다.\n잠시 후 다시 시도해 주세요.');
        return;
      }

      // ✅ 번호 생성 엔진 준비(전역 누락/초기화 실패 대비)
      await ensureGeneratorReady();

      showLoading();
    }


    function showLoading() {
      loadingModal.classList.add('active');
      // 접근성 강화
      loadingModal.setAttribute('role', 'alert');
      loadingModal.setAttribute('aria-live', 'polite');
      loadingModal.setAttribute('aria-busy', 'true');
      loadingModal.setAttribute('aria-label', 'AI 분석 진행 중');
      
      const loadingBar = document.getElementById('loadingBar');
      const loadingText = document.getElementById('loadingText');
      const loadingPercentage = document.getElementById('loadingPercentage');
      const dataStats = document.getElementById('dataStats');
      const recentNumbers = document.getElementById('recentNumbers');
      const dataUpdate = document.getElementById('dataUpdate');
      
      // 분석 시작 이벤트 트래킹
      trackEvent('analysis_started', {
        selected_styles: state.selectedStyles.length,
        has_credit: (state.freeCredit + state.paidCredit) > 0,
        user_id: state.userId || 'guest'
      });
      
      const messages = [
        { text: "📊 1,201회차 데이터 로딩 중...", icon: "📊" },
        { text: "🔍 최근 100회 당첨 패턴 분석...", icon: "🔍" },
        { text: "📈 Hot/Cold 번호 계산 중...", icon: "📈" },
        { text: "⚖️ 홀짝/고저 균형 최적화...", icon: "⚖️" },
        { text: "🎯 Monte Carlo 시뮬레이션...", icon: "🎯" },
        { text: "✨ 최종 조합 선별 중...", icon: "✨" }
      ];

      let progress = 0;
      let msgIndex = 0;
      let dataShown = false;

      const interval = setInterval(() => {
        // 자연스러운 진행률 증가 (느림 → 빠름 → 느림)
        const increment = msgIndex < 3 
          ? Math.random() * 8 + 4 
          : Math.random() * 15 + 8;
        
        progress += increment;
        if (progress > 100) progress = 100;
        
        // 프로그레스 바 업데이트 (부드러운 애니메이션)
        loadingBar.style.width = progress + '%';
        loadingBar.style.transition = 'width 0.3s ease-out';
        
        // 퍼센트 표시 업데이트
        if (loadingPercentage) {
          loadingPercentage.textContent = Math.floor(progress) + '%';
        }
        
        // 메시지 업데이트 (페이드 효과)
        if (progress > msgIndex * 16.67 && msgIndex < messages.length) {
          loadingText.style.opacity = '0';
          
          setTimeout(() => {
            loadingText.innerHTML = `
              <span class="loading-icon">${messages[msgIndex].icon}</span>
              <span class="loading-text-content">${messages[msgIndex].text}</span>
            `;
            loadingText.style.opacity = '1';
          }, 150);
          
          msgIndex++;
        }

        if (progress > 30 && !dataShown && window.lottoGenerator?.dataLoader?.data) {
          dataShown = true;
          showRealData(dataStats, recentNumbers, dataUpdate);
        }

        if (progress >= 100) {
          clearInterval(interval);
          setTimeout(() => completeAnalysis(), 500);
        }
      }, 100);
      
      return interval;
    }

    function showRealData(dataStats, recentNumbers, dataUpdate) {
      const data = window.lottoGenerator?.dataLoader?.data;
      if (!data) return;

      dataUpdate.textContent = `업데이트: ${data.lastUpdate}`;

      const history = data.history;
      const allNumbers = history.flatMap(h => h.numbers);
      
      const freq = {};
      allNumbers.forEach(n => freq[n] = (freq[n] || 0) + 1);
      const hotNum = Object.entries(freq).sort((a, b) => b[1] - a[1])[0];
      
      const recent = {};
      for (let i = 1; i <= 45; i++) recent[i] = history.length;
      history.forEach((h, idx) => {
        h.numbers.forEach(n => { if (recent[n] > idx) recent[n] = idx; });
      });
      const coldNum = Object.entries(recent).sort((a, b) => b[1] - a[1])[0];

      dataStats.innerHTML = `
        <div class="stat-item">
          <div class="stat-value">${data.currentRound}회</div>
          <div class="stat-label">현재 회차</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">${hotNum[0]}번</div>
          <div class="stat-label">최다 출현 (${hotNum[1]}회)</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">${coldNum[0]}번</div>
          <div class="stat-label">${coldNum[1]}회 연속 미출</div>
        </div>
      `;

      const recentThree = history.slice(0, 3);
      recentNumbers.innerHTML = `
        <div class="recent-title">
          <span>🎱 최근 당첨 번호</span>
          <span class="analyzing-badge">분석 중</span>
        </div>
        ${recentThree.map((h, idx) => `
          <div class="recent-row" style="animation-delay: ${idx * 0.15}s">
            <span class="recent-round">${h.round}회</span>
            <div class="recent-balls">
              ${h.numbers.map(n => `<span class="mini-ball ball-${getBallColor(n)}">${n}</span>`).join('')}
            </div>
          </div>
        `).join('')}
      `;
    }

    // ===== 스타일 정보 (10가지 알고리즘) =====
    const styleInfo = {
      'hotcold': { icon: '🔥', name: 'Hot/Cold', genStyle: 'hot', desc: '과출/미출 번호 중심' },
      'ac': { icon: '🧮', name: 'AC값', genStyle: 'balanced', desc: '다양성 지수 최적화' },
      'balance': { icon: '⚖️', name: '홀짝/고저', genStyle: 'balanced', desc: '균형 비율 최적화' },
      'color': { icon: '🎨', name: '색상볼', genStyle: 'color', desc: '색상 분포 균형' },
      'correlation': { icon: '🔗', name: '상관관계', genStyle: 'pair', desc: '동반출현 패턴' },
      'montecarlo': { icon: '🎲', name: '몬테카를로', genStyle: 'montecarlo', desc: '확률 시뮬레이션' },
      'sum': { icon: '➕', name: '합계분석', genStyle: 'sum', desc: '총합 구간 최적화' },
      'cycle': { icon: '🔄', name: '주기분석', genStyle: 'cycle', desc: '출현 주기 반영' },
      'lastdigit': { icon: '🔢', name: '끝수분석', genStyle: 'lastdigit', desc: '끝자리 분포 균형' },
      'consecutive': { icon: '📊', name: '연속번호', genStyle: 'consecutive', desc: '연번 패턴 분석' }
    };

    async function completeAnalysis() {
      // 크레딧 차감은 이미 서버에서 완료됨 (startAnalysis → use_credit)
      // 여기서는 결과 생성/표시만 담당
      state.results = [];

      // ✅ generator 보장 (로딩 도중 스크립트 에러로 전역이 날아간 케이스 방지)
      await ensureGeneratorReady();

      for (const style of state.selectedStyles) {
        const info = styleInfo[style] || { icon: '📊', name: style, genStyle: 'balanced' };
        
        let result;
        try {
          result = window.lottoGenerator.generate(info.genStyle);
        } catch (error) {
          console.error('생성 오류:', error);
          result = window.lottoGenerator.generateBasic();
        }
        
        // ✅ 결과 방어(번호 6개가 아니면 강제 대체)
        if (!result || !Array.isArray(result.numbers) || result.numbers.length !== 6) {
          console.error('[lotto] generate 결과가 비정상 → generateBasic로 대체', result);
          result = window.lottoGenerator.generateBasic();
        }

        const resultItem = {
          style: style,
          info: info,
          ...result
        };
        
        state.results.push(resultItem);
        
        // 히스토리에 추가 (localStorage에도 저장)
        const historyItem = {
          numbers: result.numbers,
          style: style,
          styleName: info.name,
          score: result.score,
          round: getAnalysisRound()
        };
        
        StorageManager.addHistory(historyItem);
        state.history = StorageManager.getHistory();
      }

	  // 대표 조합 1개를 g5_lotto_ai_recommend에 저장 (자동 기록)
	  try {
		const primary = state.results[0];  // 첫 번째 스타일 결과를 대표로 사용
		if (primary && Array.isArray(primary.numbers)) {
		  await fetch('/lotto/save_ai_recommend.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json; charset=utf-8' },
			body: JSON.stringify({
			  round: getAnalysisRound(),
			  numbers: primary.numbers
			})
		  });
		}
	  } catch (e) {
		console.error('AI 추천번호 저장 실패(로그만 남김):', e);
	  }

      // ✅ 사용자별 예측번호 6개를 g5_lotto_credit_number에 저장
      try {
        const primary = state.results[0];
        if (primary && Array.isArray(primary.numbers) && primary.numbers.length === 6) {
          const nums = primary.numbers.map(Number).sort((a,b)=>a-b);

          const saveResp = await fetch(location.pathname, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams({
              mode: 'save_numbers',
              round_no: String(getAnalysisRound() || ''),
              n1: String(nums[0]),
              n2: String(nums[1]),
              n3: String(nums[2]),
              n4: String(nums[3]),
              n5: String(nums[4]),
              n6: String(nums[5])
            })
          });

          // ✅ 저장 응답을 확인(실패해도 조용히 넘어가던 문제 해결)
          let saveJson = null;
          try { saveJson = await saveResp.json(); } catch (e) {}
          if (!saveJson || !saveJson.success) {
            console.error('[lotto] save_numbers 실패:', saveJson);
            showToast('번호 DB 저장 실패(로그 확인 필요)', 'error');
          } else {
            // 원하면 성공 토스트로 바꿔도 됩니다.
            // showToast('번호가 DB에 저장되었습니다! 💾', 'success');
          }
        }
      } catch (e) {
        console.error('예측번호 저장 실패(로그만 남김):', e);
		showToast('번호 저장 중 네트워크 오류', 'error');
      }
      renderAllResults();

      loadingModal.classList.remove('active');
      loadingModal.removeAttribute('aria-busy');
      loadingModal.setAttribute('aria-busy', 'false');
      
      dashboardView.classList.add('hidden');
      resultView.classList.add('visible');
      
      // 분석 완료 이벤트 트래킹
      trackEvent('analysis_completed', {
        result_count: state.results.length,
        styles_used: state.selectedStyles.join(','),
        user_id: state.userId || 'guest'
      });
    }

    // ===== 결과 렌더링 =====
	function renderAllResults() {
	  const resultNav = document.getElementById('resultNav');
	  const resultCardsContainer = document.getElementById('resultCardsContainer');
	  const resultIndicators = document.getElementById('resultIndicators');
	  const resultSubtitle = document.getElementById('resultSubtitle');

	  if (!resultNav || !resultCardsContainer || !resultIndicators) {
		console.error('[lotto] 결과 DOM 요소가 없습니다.');
		return;
	  }

	  if (resultSubtitle) {
		resultSubtitle.textContent = `${state.results.length}개 스타일 · ${getAnalysisRound().toLocaleString()}회차 데이터 기반`;
	  }

	  // 상단 탭
	  resultNav.innerHTML = state.results.map((r, idx) => `
		<button class="result-nav-btn ${idx === 0 ? 'active' : ''}" data-index="${idx}" role="tab" aria-selected="${idx === 0 ? 'true' : 'false'}" aria-controls="result-card-${idx}" id="nav-tab-${idx}">
		  <span class="nav-icon" aria-hidden="true">${r.info.icon}</span>
		  <span>${r.info.name}</span>
		</button>
	  `).join('');

	  // 카드들
      resultCardsContainer.innerHTML = state.results.map((r, idx) => {
        let report = { summary: [], insights: [] };
        try {
          report = window.lottoGenerator.generateReport(r);
        } catch (e) {
          console.error('[lotto] generateReport 실패:', e);
        }
        const stories = Array.isArray(r.stories) ? r.stories : [];

		return `
		  <div class="result-card ${idx === 0 ? 'active' : ''}" data-index="${idx}" role="tabpanel" aria-labelledby="nav-tab-${idx}" id="result-card-${idx}" aria-label="${r.info.name} 분석 결과 ${idx + 1}">
			<div class="result-card-header">
			  <div class="result-card-style">
				<span class="result-card-style-icon" aria-hidden="true">${r.info.icon}</span>
				<span class="result-card-style-name">${r.info.name} 분석</span>
			  </div>
			  <span class="result-card-number" aria-label="결과 ${idx + 1}번째, 전체 ${state.results.length}개 중">${idx + 1}/${state.results.length}</span>
			</div>

			<div class="balls-container" role="list" aria-label="추천 번호">
			  ${r.numbers.map((num, i) => `
				<div class="ball-3d ball-${getBallColor(num)} clickable-ball" 
				     style="animation-delay: ${i * 0.1}s" 
				     onclick="window.open('/로또-번호/${num}/', '_blank')"
				     title="로또 ${num}번 통계 보기"
				     role="listitem"
				     aria-label="추천 번호 ${num}">${num}</div>
			  `).join('')}
			</div>

			<div class="number-stories">
			  <div class="story-title">📋 번호별 선정 이유</div>
              <div class="story-grid">
                ${stories.map(story => `
				  <div class="story-item">
					<span class="story-ball ball-${getBallColor(story.number)}">${story.number}</span>
					<div class="story-content">
					  <span class="story-tag ${story.type === 'hot' ? 'tag-hot' : story.type === 'cold' ? 'tag-cold' : 'tag-balance'}">${story.label}</span>
					  <span class="story-desc">${story.description}</span>
					</div>
				  </div>
				`).join('')}
			  </div>
			</div>

			<div class="report-summary">
			  ${report.summary.map(s => `<span class="summary-tag">${s}</span>`).join('')}
			</div>
			<div class="report-insights">
			  ${report.insights.join('<br>')}
			</div>

			<div class="card-score">
			  <div class="score-label">AI 분석 점수</div>
			  <div class="score-gauge">
				<div class="score-fill" style="width: ${r.score}%; background: ${r.score >= 85 ? '#00e0a4' : r.score >= 70 ? '#00b4d8' : '#ffd700'}"></div>
				<div class="score-value">${r.score}점</div>
			  </div>
			  </div>

			<div class="card-insights">
			  <div class="insight-item">
				<span class="insight-icon">🎯</span>
				<div class="insight-content">
				  <div class="insight-label">홀짝 비율</div>
				  <div class="insight-value">${r.numbers.filter(n => n % 2 === 1).length}:${r.numbers.filter(n => n % 2 === 0).length}</div>
				</div>
				</div>
			  <div class="insight-item">
				<span class="insight-icon">📊</span>
				<div class="insight-content">
				  <div class="insight-label">고저 분포</div>
				  <div class="insight-value">${r.numbers.filter(n => n > 22).length}:${r.numbers.filter(n => n <= 22).length}</div>
				</div>
			  </div>
			  <div class="insight-item">
				<span class="insight-icon">🔢</span>
				<div class="insight-content">
				  <div class="insight-label">합계</div>
				  <div class="insight-value">${r.numbers.reduce((a, b) => a + b, 0)}</div>
				</div>
			  </div>
			  <div class="insight-item">
				<span class="insight-icon">🎨</span>
				<div class="insight-content">
				  <div class="insight-label">색상</div>
				  <div class="insight-value">${new Set(r.numbers.map(n => getBallColor(n))).size}가지</div>
				</div>
			  </div>
			</div>

			<div class="card-actions">
			  <button class="btn-secondary" onclick="shareResult(${idx})" aria-label="결과 공유하기">
				<span aria-hidden="true">🔗</span> 공유
			  </button>
			  <button class="btn-primary" onclick="saveResult(${idx})" aria-label="결과 저장하기">
				<span aria-hidden="true">💾</span> 저장
			  </button>
			</div>
		  </div>
		`;
	  }).join('');

	  // 하단 점 인디케이터
	  resultIndicators.innerHTML = state.results.map((_, idx) => `
		<button class="result-indicator ${idx === 0 ? 'active' : ''}" data-index="${idx}" role="tab" aria-selected="${idx === 0 ? 'true' : 'false'}" aria-label="결과 ${idx + 1}번 보기"></button>
	  `).join('');

	  setupResultNavigation();
	}


    function setupResultNavigation() {
      const navBtns = document.querySelectorAll('.result-nav-btn');
      const cards = document.querySelectorAll('.result-card');
      const indicators = document.querySelectorAll('.result-indicator');
      
      function showResult(index) {
        navBtns.forEach(b => b.classList.remove('active'));
        cards.forEach(c => c.classList.remove('active'));
        indicators.forEach(i => i.classList.remove('active'));
        
        navBtns[index]?.classList.add('active');
        cards[index]?.classList.add('active');
        indicators[index]?.classList.add('active');
      }
      
      navBtns.forEach(btn => {
        btn.addEventListener('click', () => showResult(parseInt(btn.dataset.index)));
      });
      
      indicators.forEach(ind => {
        ind.addEventListener('click', () => showResult(parseInt(ind.dataset.index)));
      });
      
      // 터치 제스처 지원
      const resultCardsContainer = document.getElementById('resultCardsContainer');
      if (resultCardsContainer) {
        let touchStartX = 0;
        let touchEndX = 0;
        
        resultCardsContainer.addEventListener('touchstart', (e) => {
          touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        resultCardsContainer.addEventListener('touchend', (e) => {
          touchEndX = e.changedTouches[0].screenX;
          handleSwipe();
        }, false);
        
        function handleSwipe() {
          const swipeThreshold = 50;
          const diff = touchStartX - touchEndX;
          
          if (Math.abs(diff) > swipeThreshold) {
            const currentIndex = parseInt(document.querySelector('.result-card.active')?.dataset.index || '0');
            if (diff > 0) {
              // 왼쪽 스와이프 (다음 결과)
              const nextIndex = Math.min(currentIndex + 1, state.results.length - 1);
              if (nextIndex !== currentIndex) showResult(nextIndex);
            } else {
              // 오른쪽 스와이프 (이전 결과)
              const prevIndex = Math.max(currentIndex - 1, 0);
              if (prevIndex !== currentIndex) showResult(prevIndex);
            }
          }
        }
      }
      
      // 키보드 단축키 지원
      document.addEventListener('keydown', function(e) {
        // ESC: 모달 닫기
        if (e.key === 'Escape') {
          closeShareModal();
          const chargeModal = document.getElementById('chargeModal');
          if (chargeModal) closeChargeModal();
        }
        
        // 화살표 키: 결과 카드 네비게이션 (결과 화면일 때만)
        const resultView = document.getElementById('resultView');
        if (resultView && resultView.classList.contains('visible')) {
          if (e.key === 'ArrowLeft') {
            const currentIndex = parseInt(document.querySelector('.result-card.active')?.dataset.index || '0');
            const prevIndex = Math.max(currentIndex - 1, 0);
            if (prevIndex !== currentIndex) showResult(prevIndex);
          } else if (e.key === 'ArrowRight') {
            const currentIndex = parseInt(document.querySelector('.result-card.active')?.dataset.index || '0');
            const nextIndex = Math.min(currentIndex + 1, state.results.length - 1);
            if (nextIndex !== currentIndex) showResult(nextIndex);
          }
        }
        
        // Ctrl/Cmd + Enter: 분석 시작
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
          e.preventDefault();
          const analyzeBtn = document.getElementById('analyzeBtn');
          if (analyzeBtn && !analyzeBtn.disabled) {
            startAnalysis();
          }
        }
        
        // Ctrl/Cmd + S: 결과 저장
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
          e.preventDefault();
          const resultView = document.getElementById('resultView');
          if (state.results.length > 0 && resultView && resultView.classList.contains('visible')) {
            const currentIndex = parseInt(document.querySelector('.result-card.active')?.dataset.index || '0');
            saveResult(currentIndex);
          }
        }
      });
    }

    // 디바운스된 크레딧 업데이트 (성능 최적화)
    const debouncedCreditUpdate = debounce(function() {
      updateCreditDisplay();
    }, 300);

    function updateCreditDisplay() {
      const freeCredit = state.freeCredit || 0;
      const paidCredit = state.paidCredit || 0;
      const total = freeCredit + paidCredit;
      
      const navCredit = document.getElementById('navCredit');
      const creditBreakdown = document.getElementById('creditBreakdown');
      const creditDisplay = document.getElementById('creditDisplay');
      
      if (navCredit) {
        navCredit.textContent = total + '회';
      }
      
      // 크레딧 상세 정보 업데이트
      if (creditBreakdown) {
        let breakdownHtml = '';
        if (freeCredit > 0) {
          breakdownHtml += `<span class="credit-free">무료 ${freeCredit}회</span>`;
        }
        if (paidCredit > 0) {
          breakdownHtml += `<span class="credit-paid">유료 ${paidCredit}회</span>`;
        }
        creditBreakdown.innerHTML = breakdownHtml || '<span style="color: rgba(255,255,255,0.5);">크레딧 없음</span>';
      }
      
      // 크레딧 부족 경고
      if (creditDisplay) {
        if (total === 0) {
          creditDisplay.classList.add('credit-empty');
          showCreditWarning();
        } else if (total <= 2) {
          creditDisplay.classList.add('credit-low');
          creditDisplay.classList.remove('credit-empty');
        } else {
          creditDisplay.classList.remove('credit-empty', 'credit-low');
        }
      }
      
      // 애니메이션 효과
      if (creditDisplay) {
        creditDisplay.style.transform = 'scale(1.05)';
        creditDisplay.style.transition = 'transform 0.2s ease';
        setTimeout(() => {
          creditDisplay.style.transform = 'scale(1)';
        }, 200);
      }
      
      updateAnalyzeCost();
    }
    
    // 크레딧 부족 경고 팝업
    function showCreditWarning() {
      // 이미 경고가 표시 중이면 중복 생성 방지
      if (document.querySelector('.credit-warning')) return;
      
      const warning = document.createElement('div');
      warning.className = 'credit-warning';
      warning.innerHTML = `
        <div class="warning-icon">⚠️</div>
        <div class="warning-text">
          <strong>크레딧이 부족합니다</strong>
          <p>분석을 계속하려면 크레딧을 충전해주세요</p>
        </div>
        <button onclick="showChargeModal()" class="warning-btn">
          충전하기
        </button>
      `;
      
      document.body.appendChild(warning);
      
      setTimeout(() => {
        warning.classList.add('show');
      }, 100);
      
      // 5초 후 자동 제거
      setTimeout(() => {
        warning.classList.remove('show');
        setTimeout(() => warning.remove(), 300);
      }, 5000);
    }
    
    // 충전 모달 표시 함수 (기존 함수가 있으면 사용, 없으면 기본 동작)
    function showChargeModal() {
      const chargeBtn = document.getElementById('chargeBtn');
      if (chargeBtn && chargeBtn.onclick) {
        chargeBtn.click();
      } else {
        // 기본 동작: 충전 페이지로 이동
        window.location.href = '/payments/toss/';
      }
    }
		
	function updateAnalyzeCost() {
	  const el = document.querySelector('.analyze-cost');
	  if (!el) return;

	  const free = state.freeCredit;
	  const paid = state.paidCredit;
	  const total = free + paid;
	  const required = 1; // 분석 1번 = 1크레딧

	  const analyzeBtnEl = document.getElementById('analyzeBtn');
	  const reanalyzeBtnEl = document.getElementById('reanalyzeBtn');

	  // 크레딧 0일 때: 버튼 비활성 + 안내 문구
	  if (total < required) {
		el.innerHTML =
		  `<span style="color:#ef4444;">무료 분석 0회 (소진)</span><br>충전 후 다시 이용해 주세요.`;

		if (analyzeBtnEl) analyzeBtnEl.disabled = true;
		if (reanalyzeBtnEl) reanalyzeBtnEl.disabled = true;
		return;
	  }

	  // 크레딧 있을 때는 버튼 활성화
	  if (analyzeBtnEl) analyzeBtnEl.disabled = false;
	  if (reanalyzeBtnEl) reanalyzeBtnEl.disabled = false;

	  // 안내 문구: “무료 몇 회 남음 / 유료 몇 회 남음” 형태로
	  if (free > 0) {
		// 예: 무료 2 → 1 → 0 으로 딱 보이게
		el.innerHTML = `무료 분석 <span>${free}</span>회 남음`;
	  } else {
		el.innerHTML = `유료 크레딧 <span>${paid}</span>회 남음`;
	  }
	}

    backBtn.addEventListener('click', () => {
      resultView.classList.remove('visible');
      dashboardView.classList.remove('hidden');
      updateHistoryDisplay();
    });

    function updateHistoryDisplay() {
      const historySection = document.querySelector('.history-section');
      state.history = StorageManager.getHistory();
      
      if (state.history.length === 0) {
        historySection.innerHTML = `
          <div class="history-title">📜 이전 분석 내역</div>
          <div class="history-empty">
            아직 분석 내역이 없습니다.<br>
            첫 번째 분석을 시작해보세요! 🎯
          </div>
        `;
      } else {
        const historyItems = state.history.slice(0, 5).map(item => {
          const timeAgo = getTimeAgo(item.date);
          return `
            <div class="history-item" data-numbers="${item.numbers.join(',')}">
              <div style="display: flex; flex-direction: column; gap: 4px;">
                <span class="history-numbers">${item.numbers.join(' · ')}</span>
                <span style="font-size: 0.7rem; color: var(--text-muted);">${item.styleName || item.style} · 점수 ${item.score}점</span>
              </div>
              <span class="history-meta">${timeAgo}</span>
            </div>
          `;
        }).join('');
        
        historySection.innerHTML = `
          <div class="history-title">📜 이전 분석 내역 <span style="font-size: 0.75rem; color: var(--text-muted);">(${state.history.length}개)</span></div>
          <div class="history-list">${historyItems}</div>
        `;
      }
    }
    
    // 시간 경과 표시
    function getTimeAgo(dateStr) {
      const date = new Date(dateStr);
      const now = new Date();
      const diff = Math.floor((now - date) / 1000);
      
      if (diff < 60) return '방금 전';
      if (diff < 3600) return `${Math.floor(diff / 60)}분 전`;
      if (diff < 86400) return `${Math.floor(diff / 3600)}시간 전`;
      if (diff < 604800) return `${Math.floor(diff / 86400)}일 전`;
      return date.toLocaleDateString('ko-KR');
    }
    
    // ===== 저장하기 기능 =====
    function saveCurrentResult(index = 0) {
      const result = state.results[index];
      if (!result) {
        showToast('저장할 결과가 없습니다.', 'error');
        return;
      }
      
      const saved = StorageManager.addSaved({
        numbers: result.numbers,
        style: result.style,
        styleName: result.info.name,
        score: result.score,
        round: getAnalysisRound()
      });
      
      if (saved) {
        showToast('번호가 저장되었습니다! 💾', 'success');
      }
    }
    
    // ===== 토스트 메시지 =====
    function showToast(message, type = 'info') {
      // 기존 토스트 제거
      const existingToast = document.querySelector('.toast-message');
      if (existingToast) existingToast.remove();
      
      const toast = document.createElement('div');
      toast.className = 'toast-message';
      toast.innerHTML = message;
      toast.style.cssText = `
        position: fixed;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        padding: 14px 24px;
        background: ${type === 'success' ? 'rgba(0, 224, 164, 0.95)' : type === 'error' ? 'rgba(239, 68, 68, 0.95)' : 'rgba(59, 130, 246, 0.95)'};
        color: white;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        z-index: 10000;
        animation: toastIn 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      `;
      
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
      }, 2500);
    }
    
    // 토스트 애니메이션 스타일 추가
    if (!document.getElementById('toast-styles')) {
      const style = document.createElement('style');
      style.id = 'toast-styles';
      style.textContent = `
        @keyframes toastIn {
          from { opacity: 0; transform: translateX(-50%) translateY(20px); }
          to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        @keyframes toastOut {
          from { opacity: 1; transform: translateX(-50%) translateY(0); }
          to { opacity: 0; transform: translateX(-50%) translateY(-20px); }
        }
      `;
      document.head.appendChild(style);
    }
    
    // ===== 충전 모달 =====
    let selectedPayment = null;

    // ✅ 모달 HTML에서 onclick으로 호출하므로 반드시 전역 함수가 있어야 함
    function closeChargeModal() {
      const modal = document.getElementById('chargeModal');
      if (modal) modal.remove();
      document.body.style.overflow = '';
    }

    // ✅ 충전 버튼 연결(없으면 눌러도 아무 일도 안 함)
    const chargeBtnEl = document.getElementById('chargeBtn');
    if (chargeBtnEl) chargeBtnEl.addEventListener('click', showChargeModal);

    // ✅ processPayment가 파일 끝에서 끊겨있으면 JS 전체가 죽습니다.
    //    우선 “문법 오류 방지용” 최소 구현(나중에 PG 연동으로 교체)
    function processPayment() {
      try {
        if (!selectedPayment) {
          showToast('충전 패키지를 선택해주세요.', 'error');
          return;
        }
        showToast('결제 연동은 아직 연결되지 않았습니다(개발 중).', 'error');
      } catch (e) {
        console.error('[lotto] processPayment 오류:', e);
        showToast('결제 처리 중 오류가 발생했습니다.', 'error');
      }
    }
    
    function showChargeModal() {
      const modal = document.createElement('div');
      modal.id = 'chargeModal';
      modal.innerHTML = `
        <div class="charge-modal-backdrop" onclick="closeChargeModal()"></div>
        <div class="charge-modal-content">
          <div class="charge-modal-header">
            <h3>🔋 크레딧 충전</h3>
            <button class="charge-modal-close" onclick="closeChargeModal()">×</button>
          </div>
          <div class="charge-modal-body">
            <div class="charge-current">
              <div class="charge-current-label">현재 보유 크레딧</div>
              <div class="charge-current-value">${state.freeCredit + state.paidCredit}<span>회</span></div>
            </div>
            
            <div class="charge-section-title">충전 패키지 선택</div>
            <div class="charge-options">
				<div class="charge-option" data-product="CREDIT_5" data-amount="5" data-price="1000" onclick="selectChargeOption(this)">
                <div class="charge-option-left">
                  <div class="charge-amount">5회</div>
                  <div class="charge-per">회당 200원</div>
                </div>
                <div class="charge-option-right">
                  <div class="charge-price">₩1,000</div>
                </div>
              </div>
              
			  <div class="charge-option popular selected" data-product="CREDIT_20" data-amount="15" data-price="2500" data-bonus="5" onclick="selectChargeOption(this)">
                <div class="charge-badge">🔥 인기</div>
                <div class="charge-option-left">
                  <div class="charge-amount">15회 <span class="charge-bonus-inline">+5회</span></div>
                  <div class="charge-per">회당 125원 <span class="charge-discount">38% 할인</span></div>
                </div>
                <div class="charge-option-right">
                  <div class="charge-price">₩2,500</div>
                  <div class="charge-original">₩4,000</div>
                </div>
              </div>
              
			  <div class="charge-option best" data-product="CREDIT_50" data-amount="35" data-price="5000" data-bonus="15" onclick="selectChargeOption(this)">
                <div class="charge-badge gold">💎 베스트</div>
                <div class="charge-option-left">
                  <div class="charge-amount">35회 <span class="charge-bonus-inline">+15회</span></div>
                  <div class="charge-per">회당 100원 <span class="charge-discount">50% 할인</span></div>
                </div>
                <div class="charge-option-right">
                  <div class="charge-price">₩5,000</div>
                  <div class="charge-original">₩10,000</div>
                </div>
              </div>
            </div>
            
            <div class="charge-section-title">결제 수단</div>
            <div class="payment-methods">
              <div class="payment-method selected" data-method="kakao" onclick="selectPaymentMethod(this)">
                <span class="payment-icon">💬</span>
                <span>카카오페이</span>
              </div>
              <div class="payment-method" data-method="toss" onclick="selectPaymentMethod(this)">
                <span class="payment-icon">💙</span>
                <span>토스페이</span>
              </div>
              <div class="payment-method" data-method="card" onclick="selectPaymentMethod(this)">
                <span class="payment-icon">💳</span>
                <span>카드결제</span>
              </div>
            </div>
            
            <button class="charge-submit-btn" id="chargeSubmitBtn" onclick="processPayment()">
              <span class="btn-spinner" id="chargeBtnSpinner" aria-hidden="true"></span>
              <span id="chargeSubmitText">₩2,500 결제하기</span>
            </button>            

            <div class="charge-notice">
              <p>✓ 결제 완료 후 즉시 크레딧이 충전됩니다</p>
              <p>✓ 미사용 크레딧은 7일 이내 환불 가능</p>
              <p>✓ 문의: support@lottoinsight.ai</p>
            </div>
          </div>
        </div>
      `;
      
      modal.style.cssText = `
        position: fixed;
        inset: 0;
        z-index: 10001;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease;
      `;
      
      document.body.appendChild(modal);
      document.body.style.overflow = 'hidden';
      
      // 기본 선택 설정
      selectedPayment = { amount: 15, price: 2500, bonus: 5, method: 'kakao' };
      
      // 모달 스타일 추가
      if (!document.getElementById('charge-modal-styles')) {
        const style = document.createElement('style');
        style.id = 'charge-modal-styles';
        style.textContent = `
          .charge-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
          }
          .charge-modal-content {
            position: relative;
            width: 90%;
            max-width: 420px;
            max-height: 90vh;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            overflow-y: auto;
            animation: scaleIn 0.3s ease;
          }
          @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
          }
          .charge-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: sticky;
            top: 0;
            background: var(--primary);
            z-index: 1;
          }
          .charge-modal-header h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
          }
          .charge-modal-close {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
          }
          .charge-modal-close:hover {
            background: rgba(255,255,255,0.15);
            color: var(--text-primary);
          }
          .charge-modal-body {
            padding: 24px;
          }
          .charge-current {
            text-align: center;
            padding: 20px;
            background: rgba(0,224,164,0.08);
            border: 1px solid rgba(0,224,164,0.2);
            border-radius: 16px;
            margin-bottom: 24px;
          }
          .charge-current-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 4px;
          }
          .charge-current-value {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--accent-cyan);
          }
          .charge-current-value span {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-left: 4px;
          }
          .charge-section-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }
          .charge-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 24px;
          }
          .charge-option {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: rgba(255,255,255,0.02);
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
          }
          .charge-option:hover {
            border-color: rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.04);
          }
          .charge-option.selected {
            border-color: var(--accent-cyan);
            background: rgba(0,224,164,0.08);
          }
          .charge-option.selected::after {
            content: '✓';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background: var(--accent-cyan);
            color: var(--primary-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
          }
          .charge-option-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
          }
          .charge-option-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
            margin-right: 36px;
          }
          .charge-badge {
            position: absolute;
            top: -10px;
            left: 16px;
            padding: 4px 12px;
            background: var(--gradient-cyan);
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--primary-dark);
          }
          .charge-badge.gold {
            background: linear-gradient(135deg, #FFD75F 0%, #FF9F43 100%);
          }
          .charge-amount {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
          }
          .charge-bonus-inline {
            font-size: 0.85rem;
            color: var(--accent-gold);
            font-weight: 600;
          }
          .charge-per {
            font-size: 0.75rem;
            color: var(--text-muted);
          }
          .charge-discount {
            color: var(--accent-cyan);
            font-weight: 600;
          }
          .charge-price {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
          }
          .charge-original {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-decoration: line-through;
          }
          .payment-methods {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
          }
          .payment-method {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 8px;
            background: rgba(255,255,255,0.03);
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.75rem;
            color: var(--text-secondary);
          }
          .payment-method:hover {
            border-color: rgba(255,255,255,0.15);
          }
          .payment-method.selected {
            border-color: var(--accent-cyan);
            background: rgba(0,224,164,0.08);
            color: var(--text-primary);
          }
          .payment-icon {
            font-size: 1.5rem;
          }
          .charge-submit-btn {
            width: 100%;
            padding: 18px;
            background: var(--gradient-cyan);
            border: none;
            border-radius: 14px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-dark);
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 30px rgba(0,224,164,0.3);
          }
          .charge-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,224,164,0.4);
          }
          .charge-submit-btn:active {
            transform: translateY(0);
          }
          .charge-notice {
            margin-top: 16px;
            padding: 16px;
            background: rgba(255,255,255,0.02);
            border-radius: 12px;
          }
          .charge-notice p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 6px;
          }
          .charge-notice p:last-child {
            margin-bottom: 0;
          }
        `;
        document.head.appendChild(style);
      }
    }
    
    function selectChargeOption(el) {
      // 이전 선택 해제
      document.querySelectorAll('.charge-option').forEach(opt => opt.classList.remove('selected'));
      // 새 선택
      el.classList.add('selected');
      
      const amount = parseInt(el.dataset.amount);
      const price = parseInt(el.dataset.price);
      const bonus = parseInt(el.dataset.bonus || 0);
      
      selectedPayment = { ...selectedPayment, amount, price, bonus };
      
      // 버튼 텍스트 업데이트
      document.getElementById('chargeSubmitText').textContent = `₩${price.toLocaleString()} 결제하기`;
    }
    
    function selectPaymentMethod(el) {
      document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
      el.classList.add('selected');
      selectedPayment.method = el.dataset.method;
    }
    
    function processPayment() {
      if (!selectedPayment) {
        showToast('충전 패키지를 선택해주세요.', 'error');
        return;
      }
      
      const { amount, price, bonus, method } = selectedPayment;
      const totalAmount = amount + (bonus || 0);
      
      // 결제 처리 시뮬레이션
      const btn = document.querySelector('.charge-submit-btn');
      btn.innerHTML = `
        <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
          <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
        </svg>
        결제 처리 중...
      `;
      btn.disabled = true;
      
      // 2초 후 결과 표시 (실제로는 결제 API 연동)
      setTimeout(() => {
        // 데모: 크레딧 추가
        state.paidCredit += totalAmount;
        StorageManager.setCredits(state.freeCredit, state.paidCredit);
        updateNavbar();
        
        closeChargeModal();
        
        showToast(`🎉 ${totalAmount}회 크레딧이 충전되었습니다!`, 'success');
        
        // 충전 성공 모달
        setTimeout(() => {
          showSuccessModal(totalAmount, price);
        }, 300);
      }, 2000);
    }
    
    function showSuccessModal(amount, price) {
      const successModal = document.createElement('div');
      successModal.id = 'successModal';
      successModal.innerHTML = `
        <div class="success-backdrop" onclick="this.parentElement.remove(); document.body.style.overflow=''"></div>
        <div class="success-content">
          <div class="success-icon">🎉</div>
          <h3>충전 완료!</h3>
          <p class="success-amount">${amount}회</p>
          <p class="success-price">₩${price.toLocaleString()}</p>
          <p class="success-total">총 보유: <strong>${state.freeCredit + state.paidCredit}회</strong></p>
          <button onclick="this.closest('#successModal').remove(); document.body.style.overflow=''">
            확인
          </button>
        </div>
      `;
      successModal.style.cssText = `
        position: fixed;
        inset: 0;
        z-index: 10002;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease;
      `;
      
      // 성공 모달 스타일
      const style = document.createElement('style');
      style.textContent = `
        .success-backdrop {
          position: absolute;
          inset: 0;
          background: rgba(0,0,0,0.8);
        }
        .success-content {
          position: relative;
          padding: 40px;
          background: var(--primary);
          border: 1px solid rgba(0,224,164,0.3);
          border-radius: 24px;
          text-align: center;
          animation: scaleIn 0.3s ease;
        }
        .success-icon {
          font-size: 4rem;
          margin-bottom: 16px;
        }
        .success-content h3 {
          font-family: 'Outfit', sans-serif;
          font-size: 1.5rem;
          margin-bottom: 20px;
          color: var(--accent-cyan);
        }
        .success-amount {
          font-family: 'Outfit', sans-serif;
          font-size: 3rem;
          font-weight: 800;
          color: var(--text-primary);
        }
        .success-price {
          font-size: 1rem;
          color: var(--text-muted);
          margin-bottom: 16px;
        }
        .success-total {
          font-size: 0.9rem;
          color: var(--text-secondary);
          margin-bottom: 24px;
        }
        .success-total strong {
          color: var(--accent-gold);
        }
        .success-content button {
          padding: 14px 48px;
          background: var(--gradient-cyan);
          border: none;
          border-radius: 12px;
          font-weight: 700;
          color: var(--primary-dark);
          cursor: pointer;
        }
      `;
      document.head.appendChild(style);
      document.body.appendChild(successModal);
    }
    
    function closeChargeModal() {
      const modal = document.getElementById('chargeModal');
      if (modal) {
        modal.style.animation = 'fadeOut 0.2s ease forwards';
        setTimeout(() => {
          modal.remove();
          document.body.style.overflow = '';
        }, 200);
      }
    }
    
    function selectCharge(amount, price) {
      // Legacy function - redirect to new modal
      showChargeModal();
    }
    
    // ===== 공유 기능 =====
    async function shareResult(index) {
      const result = state.results[index];
      if (!result) return;
      
      const shareData = {
        title: '오늘로또 AI 분석 결과',
        text: `AI 분석 점수 ${result.score}점\n추천 번호: ${result.numbers.join(', ')}`,
        url: window.location.href
      };
      
      // 공유 시작 이벤트 트래킹
      trackEvent('share_started', {
        result_index: index,
        score: result.score,
        method: 'web_share_api'
      });
      
      // Web Share API 지원 확인
      if (navigator.share) {
        try {
          await navigator.share(shareData);
          showToast('✅ 공유가 완료되었습니다!', 'success');
          trackEvent('share_completed', {
            result_index: index,
            method: 'web_share_api'
          });
        } catch (err) {
          if (err.name !== 'AbortError') {
            console.error('공유 오류:', err);
            showShareModal(result);
            trackEvent('share_failed', {
              error: err.message,
              method: 'web_share_api'
            });
          }
        }
      } else {
        // Fallback: 커스텀 공유 모달
        showShareModal(result);
      }
    }
    
    // 공유 모달 표시
    function showShareModal(result) {
      // 이미 모달이 있으면 제거
      const existingModal = document.querySelector('.share-modal');
      if (existingModal) existingModal.remove();
      
      const modal = document.createElement('div');
      modal.className = 'share-modal';
      modal.innerHTML = `
        <div class="share-backdrop" onclick="closeShareModal()"></div>
        <div class="share-content">
          <div class="share-header">
            <h3>결과 공유하기</h3>
            <button onclick="closeShareModal()">×</button>
          </div>
          
          <div class="share-preview">
            <div class="preview-numbers">
              ${result.numbers.map(n => {
                const color = getBallColor(n);
                return `<div class="mini-ball ball-${color}">${n}</div>`;
              }).join('')}
            </div>
            <div class="preview-score">AI 점수: ${result.score}점</div>
          </div>
          
          <div class="share-options">
            <button class="share-btn kakao" onclick="shareToKakao(${result.numbers.join(',')})">
              <span class="share-icon">💬</span>
              <span>카카오톡</span>
            </button>
            <button class="share-btn twitter" onclick="shareToTwitter(${result.numbers.join(',')}, ${result.score})">
              <span class="share-icon">🐦</span>
              <span>트위터</span>
            </button>
            <button class="share-btn copy" onclick="copyToClipboard(${result.numbers.join(',')}, ${result.score})">
              <span class="share-icon">📋</span>
              <span>링크 복사</span>
            </button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      setTimeout(() => modal.classList.add('active'), 10);
    }
    
    function closeShareModal() {
      const modal = document.querySelector('.share-modal');
      if (modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.remove(), 300);
      }
    }
    
    // 카카오톡 공유
    function shareToKakao(numbers) {
      const text = `오늘로또 AI 분석 결과\n번호: ${numbers}\n${window.location.href}`;
      const url = `https://story.kakao.com/share?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(text)}`;
      window.open(url, '_blank');
      closeShareModal();
    }
    
    // 트위터 공유
    function shareToTwitter(numbers, score) {
      const text = `오늘로또 AI 분석 결과 (${score}점)\n번호: ${numbers}\n${window.location.href}`;
      const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`;
      window.open(url, '_blank');
      closeShareModal();
    }
    
    // 클립보드 복사
    async function copyToClipboard(numbers, score) {
      const text = `오늘로또 AI 분석 결과\n점수: ${score}점\n번호: ${numbers}\n${window.location.href}`;
      
      try {
        await navigator.clipboard.writeText(text);
        showToast('✅ 클립보드에 복사되었습니다!', 'success');
        closeShareModal();
      } catch (err) {
        console.error('복사 실패:', err);
        showToast('❌ 복사에 실패했습니다', 'error');
      }
    }
    
    // 저장 기능
    function saveResult(index) {
      const result = state.results[index];
      if (!result) return;
      
      try {
        StorageManager.addSaved({
          numbers: result.numbers,
          score: result.score,
          styleName: result.info.name,
          styleIcon: result.info.icon,
          date: new Date().toISOString()
        });
        
        showToast('✅ 결과가 저장되었습니다!', 'success');
        
        // 저장 이벤트 트래킹
        trackEvent('result_saved', {
          result_index: index,
          score: result.score,
          style: result.info.name,
          user_id: state.userId || 'guest'
        });
      } catch (err) {
        console.error('저장 실패:', err);
        showToast('❌ 저장에 실패했습니다', 'error');
        trackEvent('result_save_failed', { error: err.message });
      }
    }
    
    // ===== 이벤트 리스너 설정 =====
    function setupEventListeners() {
      // 충전 버튼
      const chargeBtn = document.querySelector('.charge-btn');
      if (chargeBtn) {
        chargeBtn.addEventListener('click', showChargeModal);
      }
      
      // 저장하기 버튼
      const saveBtn = document.querySelector('.action-btn-secondary');
      if (saveBtn && saveBtn.textContent.includes('저장')) {
        saveBtn.addEventListener('click', () => {
          const activeCard = document.querySelector('.result-card.active');
          const index = activeCard ? parseInt(activeCard.dataset.index) : 0;
          saveCurrentResult(index);
        });
      }
    }

    // ===== 초기화 실행 =====
    document.addEventListener('DOMContentLoaded', () => {
      // ✅ 렌더링 중 JS 에러가 나면 이후 로직(분석/저장)도 꼬일 수 있어서 보호
      try { renderLatestResult(); } catch(e){ console.error('[lotto] renderLatestResult error', e); }
      try { renderAiComparison(); } catch(e){ console.error('[lotto] renderAiComparison error', e); }
 
      updateNavbar();
      updateWelcomeMessage();
      updateAnalyzeCost();
      updateHistoryDisplay();
      setupEventListeners();
      
      // 충전 버튼 이벤트
      const chargeBtn = document.getElementById('chargeBtn');
      if (chargeBtn) {
        chargeBtn.addEventListener('click', showChargeModal);
      }
    });


    // ─────────────────────────────────────
    // 성과 아카이브(최근 8회) 렌더링 + 회차 검증
    // ─────────────────────────────────────

    function renderArchiveSection() {
      if (!Array.isArray(window.LOTTO_ARCHIVE_DATA) || !window.LOTTO_ARCHIVE_DATA.length) {
        return;
      }

      const bodyEl = document.querySelector('.archive-table-body');
      if (bodyEl) {
        bodyEl.innerHTML = window.LOTTO_ARCHIVE_DATA.map(row => {
          const realSet = new Set(row.real_numbers || []);
          const aiSet   = new Set(row.ai_numbers || []);

          const aiHtml = (row.ai_numbers || []).map(n => {
            const matched = realSet.has(n);
            return `<span class="archive-ball ball-${getBallColor(n)}${matched ? ' matched' : ''}">${n}</span>`;
          }).join('');

          const realHtml = (row.real_numbers || []).map(n => {
            const matched = aiSet.has(n);
            return `<span class="archive-ball ball-${getBallColor(n)}${matched ? ' matched' : ''}">${n}</span>`;
          }).join('');

          let matchClass = 'archive-match-avg';
          if (row.match_count >= 4) matchClass = 'archive-match-good';
          if (row.match_count >= 5) matchClass = 'archive-match-good'; // 필요시 별도 best 클래스 사용

          return `
            <div class="archive-row">
              <div class="archive-round">${row.round}회</div>
              <div class="archive-balls">
                ${aiHtml}
              </div>
              <div class="archive-balls">
                ${realHtml}
              </div>
              <div class="archive-match">
                <span class="archive-match-num ${matchClass}">${row.match_count}개</span>
              </div>
            </div>
          `;
        }).join('');
      }

      // 요약(평균/최고/누적) 업데이트
      const summary = window.LOTTO_ARCHIVE_SUMMARY || {};
      const summaryValues = document.querySelectorAll('.archive-summary .archive-summary-value');

      if (summaryValues.length >= 1 && typeof summary.avg_match !== 'undefined') {
        summaryValues[0].textContent = summary.avg_match + '개';
      }
      if (summaryValues.length >= 2 && typeof summary.best_match !== 'undefined') {
        summaryValues[1].textContent = summary.best_match + '개';
      }
      if (summaryValues.length >= 3 && typeof summary.total_weeks !== 'undefined') {
        summaryValues[2].textContent = summary.total_weeks + '주';
      }
    }

    // 버튼 onclick="verifyData()" 에서 사용하는 함수
    function verifyData() {
      const inputEl  = document.getElementById('verifyInput');
      const resultEl = document.getElementById('verifyResult');
      const ballsEl  = document.getElementById('verifyBalls');
      const titleEl  = document.getElementById('verifyTitle');

      if (!inputEl || !resultEl || !ballsEl) {
        alert('검증 UI 요소를 찾을 수 없습니다.');
        return;
      }
      if (!Array.isArray(window.LOTTO_ARCHIVE_DATA) || !window.LOTTO_ARCHIVE_DATA.length) {
        alert('검증할 아카이브 데이터가 없습니다.');
        return;
      }

      const round = parseInt(inputEl.value, 10);
      if (!round) {
        alert('회차를 정확히 입력해 주세요 (예: 1201)');
        inputEl.focus();
        return;
      }

      const row = window.LOTTO_ARCHIVE_DATA.find(r => r.round === round);
      titleEl.textContent = round + '회차';
      resultEl.style.display = 'block';

      if (!row) {
        ballsEl.innerHTML = `
          <p style="font-size:0.85rem;color:var(--text-muted);">
            해당 회차의 AI 추천 기록이 없습니다.
          </p>
        `;
        return;
      }

      const realSet = new Set(row.real_numbers || []);
      const aiSet   = new Set(row.ai_numbers || []);

      const aiHtml = (row.ai_numbers || []).map(n => {
        const matched = realSet.has(n);
        return `<span class="archive-ball ball-${getBallColor(n)}${matched ? ' matched' : ''}">${n}</span>`;
      }).join('');

      const realHtml = (row.real_numbers || []).map(n => {
        const matched = aiSet.has(n);
        return `<span class="archive-ball ball-${getBallColor(n)}${matched ? ' matched' : ''}">${n}</span>`;
      }).join('');

      ballsEl.innerHTML = `
        <div style="display:flex;flex-direction:column;gap:10px;">
          <div>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:4px;">AI 추천</div>
            <div class="archive-balls">${aiHtml}</div>
          </div>
          <div>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:4px;">실제 당첨</div>
            <div class="archive-balls">${realHtml}</div>
          </div>
          <div style="margin-top:4px;font-size:0.8rem;color:var(--accent-cyan);">
            일치 수: <strong>${row.match_count}개</strong>
          </div>
        </div>
      `;
    }

    // DOMContentLoaded 시 아카이브도 같이 렌더링
    document.addEventListener('DOMContentLoaded', () => {
      renderArchiveSection();
    });
  </script>
	<script src="https://js.tosspayments.com/v1/payment"></script>
	<script>
		function showToast(message, type='success', timeout=2600){
		  const container = document.getElementById('toastContainer');
		  if (!container) return alert(message);
		
		  const toast = document.createElement('div');
		  toast.className = `toast toast--${type}`;
		  toast.innerHTML = `
		    <div class="toast__msg">${String(message).replace(/[<>&"]/g, s=>({ '<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;' }[s]))}</div>
		    <button class="toast__close" type="button" aria-label="닫기">×</button>
		  `;
		  container.appendChild(toast);
		
		  const remove = ()=>{ if(toast && toast.parentNode) toast.parentNode.removeChild(toast); };
		  toast.querySelector('.toast__close').addEventListener('click', remove);
		  setTimeout(remove, timeout);
		}
		
		function setPayButtonLoading(isLoading){
		  const btn = document.getElementById('chargeSubmitBtn');
		  if(!btn) return;
		  if(isLoading){
		    btn.disabled = true;
		    btn.classList.add('is-loading');
		  } else {
		    btn.disabled = false;
		    btn.classList.remove('is-loading');
		  }
		}
		
		async function refreshCreditBalance(){
		  try{
		    // 전용 크레딧 시스템 API 사용
		    const res = await fetch('/api/get_credits.php', { credentials:'same-origin' });
		    const data = await res.json();
		    if(!data.success) return;
		
		    // 전용 크레딧 시스템 응답 형식
		    const freeUses = Number(data.free_uses || 0);
		    const creditBalance = Number(data.credit_balance || 0);
		    const total = Number(data.total || (freeUses + creditBalance));
		
		    // 상태 업데이트
		    if (typeof state !== 'undefined') {
		      state.freeCredit = freeUses;
		      state.paidCredit = creditBalance;
		      updateCreditDisplay();
		    }
		
		    // ✅ 업데이트 대상: id="creditBalance" 또는 data-credit-balance 속성
		    const el1 = document.getElementById('creditBalance');
		    if(el1) el1.textContent = total.toLocaleString();
		
		    document.querySelectorAll('[data-credit-balance]').forEach(el=>{
		      el.textContent = total.toLocaleString();
		    });
		
		    // 무료/유료 분리 표시 요소가 있다면 업데이트
		    document.querySelectorAll('[data-free-credits]').forEach(el=>{
		      el.textContent = freeUses.toLocaleString();
		    });
		    document.querySelectorAll('[data-paid-credits]').forEach(el=>{
		      el.textContent = creditBalance.toLocaleString();
		    });
		  }catch(e){
		    console.error('크레딧 갱신 실패:', e);
		  }
		}

		async function payCredit(productCode) {
		  const res = await fetch('/api/toss/create_order.php', {
			method: 'POST',
			headers: {'Content-Type':'application/json'},
			body: JSON.stringify({ product: productCode })
		  });

		  const data = await res.json();
		  if (!data.ok) { alert(data.message || '주문 생성 실패'); return; }

		  const tossPayments = TossPayments(data.clientKey);

		  // ✅ 카드 결제만 (requestPayment의 결제수단 문자열은 '카드' 사용) :contentReference[oaicite:2]{index=2}
		  await tossPayments.requestPayment('카드', {
			amount: data.amount,
			orderId: data.orderId,
			orderName: data.orderName,
			customerName: data.customerName,
			customerEmail: data.customerEmail,
			successUrl: data.successUrl,
			failUrl: data.failUrl
		  });
		}

		// ✅ 기존 processPayment()가 있다면, 이 로직만 넣거나 교체
		function processPayment() {
		  const selected = document.querySelector('.charge-option.selected');
		  if (!selected) { showToast('충전 패키지를 선택해주세요.', 'error'); return; }
		
		  const productCode = selected.dataset.product;
		  if (!productCode) { showToast('상품 코드가 없습니다. (data-product 확인)', 'error'); return; }
		
		  // ✅ 중복 클릭 방지 + 로딩 표시
		  setPayButtonLoading(true);
		
		  // 결제창 호출(이후 successUrl로 이동하므로 보통 여기로 돌아오지 않습니다)
		  payCredit(productCode).catch((e)=>{
		    // 사용자가 결제창을 닫거나 오류가 발생한 경우 대비
		    showToast('결제가 취소되었거나 오류가 발생했습니다.', 'error');
		    setPayButtonLoading(false);
		  });
		}
		
		// ✅ 결제 완료/실패 후 돌아온 경우 처리 (success.php/fail.php가 쿼리 붙여줌)
		document.addEventListener('DOMContentLoaded', async ()=>{
		  const params = new URLSearchParams(location.search);
		  const pay = params.get('pay');
		  if(pay === 'success'){
		    showToast('결제가 완료되었습니다. 잔액을 갱신합니다.', 'success');
		    await refreshCreditBalance();
		    // URL 정리
		    params.delete('pay'); params.delete('orderId');
		    const newUrl = location.pathname + (params.toString() ? ('?'+params.toString()) : '');
		    history.replaceState({}, '', newUrl);
		    // 버튼 상태 복구(혹시 남아있을 때 대비)
		    setPayButtonLoading(false);
		  } else if(pay === 'fail'){
		    const msg = params.get('message') || '결제 실패';
		    showToast(msg, 'error', 3200);
		    params.delete('pay'); params.delete('message');
		    const newUrl = location.pathname + (params.toString() ? ('?'+params.toString()) : '');
		    history.replaceState({}, '', newUrl);
		    setPayButtonLoading(false);
		  }
		});
	</script>
</body>
</html>


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

    $use = lotto_use_one_analysis(
        $member['mb_id'],
        'AI 분석 실행 (회차 '.$round_no.')',
        'round_'.$round_no
    );

    if (empty($use['success'])) {
        echo json_encode([
            'success'        => false,
            'reason'         => $use['reason'] ?? 'ERROR',
            'credit_balance' => $use['credit'] ?? 0,
            'free_uses'      => $use['free'] ?? 0,
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
  <title>AI 분석 | 로또인사이트 - <?php echo $current_round_fmt; ?>회차 데이터 기반 분석</title>
  <meta name="title" content="AI 분석 | 로또인사이트 - <?php echo $current_round_fmt; ?>회차 데이터 기반 분석">
  <meta name="description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. <?php echo $current_round_fmt; ?>회차 동행복권 공식 데이터 기반 패턴 분석 리포트와 균형 점수를 제공합니다.">
@@
  <meta property="og:title" content="AI 분석 | 로또인사이트 - 1,201회차 데이터 기반 분석">
  <meta property="og:description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 동행복권 공식 데이터 기반 패턴 분석!">
  <meta property="og:title" content="AI 분석 | 로또인사이트 - <?php echo $current_round_fmt; ?>회차 데이터 기반 분석">
  <meta property="og:description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 동행복권 공식 데이터 기반 패턴 분석!">

  <meta name="robots" content="index, follow">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://lottoinsight.ai/result">
  <meta property="og:title" content="AI 분석 | 로또인사이트 - 1,201회차 데이터 기반 분석">
  <meta property="og:description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 동행복권 공식 데이터 기반 패턴 분석!">
  <meta property="og:image" content="https://lottoinsight.ai/og-image.png">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#030711">

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
    html, body {
      overflow-x: hidden;
    }
    
    body {
      min-height: 100vh;
      width: 100%;
      max-width: 100vw;
    }

    /* ===== 상단 고정 헤더 ===== */
    .app-navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 68px;
      background: rgba(3, 7, 17, 0.85);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
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
      font-size: 1.1rem;
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
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: var(--radius-full);
      font-size: 0.8rem;
      color: var(--text-secondary);
      transition: all var(--transition-fast);
    }

    .credit-badge:hover {
      background: rgba(255, 255, 255, 0.06);
      border-color: rgba(255, 255, 255, 0.12);
    }

    .credit-count {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .user-avatar-btn {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      text-decoration: none;
      font-size: 1rem;
      overflow: hidden;
      transition: all var(--transition-normal);
    }

    .user-avatar-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: scale(1.05);
      border-color: rgba(255, 255, 255, 0.15);
    }

    .user-avatar-btn.logged-in {
      background: var(--gradient-cyan);
      border-color: var(--accent-cyan);
      box-shadow: 0 4px 15px rgba(0, 255, 204, 0.3);
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
    }

    /* ===== 최신 당첨 결과 섹션 ===== */
    .latest-result-section {
      background: linear-gradient(145deg, 
        rgba(15, 23, 42, 0.9) 0%, 
        rgba(3, 7, 17, 0.95) 100%);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: var(--radius-2xl);
      padding: var(--space-5);
      margin-bottom: var(--space-5);
      position: relative;
      overflow: hidden;
      box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.03);
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
      background: rgba(0, 0, 0, 0.25);
      border-radius: var(--radius-xl);
      border: 1px solid rgba(255, 255, 255, 0.03);
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
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .latest-prize {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .latest-prize strong {
      color: var(--accent-gold);
      font-weight: 700;
    }

    .latest-link {
      font-size: 0.8rem;
      color: var(--accent-cyan);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: var(--space-1);
      transition: all var(--transition-fast);
    }

    .latest-link:hover {
      color: var(--accent-cyan-light);
      transform: translateX(2px);
    }

    /* ===== AI vs 실제 비교 섹션 ===== */
    .ai-comparison-section {
      background: linear-gradient(145deg, 
        rgba(15, 23, 42, 0.9) 0%, 
        rgba(3, 7, 17, 0.95) 100%);
      border: 1px solid rgba(0, 255, 204, 0.15);
      border-radius: var(--radius-2xl);
      padding: var(--space-5);
      margin-bottom: var(--space-5);
      box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.3),
        0 0 40px rgba(0, 255, 204, 0.03);
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
      background: rgba(0, 0, 0, 0.2);
      border-radius: var(--radius-lg);
      border: 1px solid rgba(255, 255, 255, 0.03);
    }

    .comparison-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      width: 70px;
      flex-shrink: 0;
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
      background: rgba(0, 255, 204, 0.08);
      border: 1px solid rgba(0, 255, 204, 0.15);
      border-radius: var(--radius-lg);
    }

    .match-count {
      font-family: 'Outfit', sans-serif;
      font-size: 0.95rem;
      color: var(--accent-cyan);
      font-weight: 700;
    }

    .match-numbers {
      font-size: 0.85rem;
      color: var(--text-secondary);
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
        width: 15px;
        height: 15px;
        font-size: 0.55rem;
      }
    }

    /* ✅ 아주 작은 화면(아이폰 SE급): 더 작게 */
    @media (max-width: 360px){
      #aiComparisonSection .comparison-row{ padding: 10px; }
      #aiComparisonSection .comparison-label{ width: 48px; }
      #aiComparisonSection .comparison-balls{ gap: 3px; }
      #aiComparisonSection .mini-ball{
        width: 18px;
        height: 18px;
        font-size: 0.5rem;
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
      padding: 12px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 12px;
      font-size: 0.75rem;
      color: var(--text-secondary);
    }

    .trust-check {
      color: var(--accent-cyan);
      font-weight: 700;
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
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .welcome-text p {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    /* 분석 스타일 선택 */
    .style-section {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 24px;
      margin-bottom: 24px;
    }

    .style-title {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 16px;
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
    }

    .style-buttons-grid .style-btn:last-child {
      grid-column: span 2;
    }

    .style-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      background: rgba(255, 255, 255, 0.03);
      border: 2px solid rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    }

    .style-btn:hover {
      background: rgba(255, 255, 255, 0.06);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .style-btn.active {
      background: rgba(0, 224, 164, 0.1);
      border-color: var(--accent-cyan);
    }

    .style-icon {
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .style-name {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-primary);
      display: block;
    }

    .style-btn.active .style-name {
      color: var(--accent-cyan);
    }

    .style-desc {
      font-size: 0.65rem;
      color: var(--text-muted);
      display: block;
      margin-top: 2px;
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
      background: var(--accent-cyan);
      border-color: var(--accent-cyan);
      color: var(--primary-dark);
    }

    .style-selected-count {
      text-align: center;
      font-size: 0.8rem;
      color: var(--text-muted);
      padding-top: 8px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .style-selected-count span {
      color: var(--accent-cyan);
      font-weight: 700;
    }

    /* 분석 시작 버튼 */
    .analyze-section {
      margin-bottom: 24px;
    }

    .analyze-btn {
      width: 100%;
      padding: 20px;
      background: var(--gradient-cyan);
      border: none;
      border-radius: 16px;
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 10px 40px rgba(0, 224, 164, 0.3);
      transition: all 0.3s ease;
    }

    .analyze-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 50px rgba(0, 224, 164, 0.4);
    }

    .analyze-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }

    .analyze-cost {
      text-align: center;
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-top: 10px;
    }

    .analyze-cost span {
      color: var(--accent-cyan);
      font-weight: 600;
    }

    /* 이전 분석 내역 */
    .history-section {
      background: rgba(13, 24, 41, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 24px;
    }

    .history-title {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .history-empty {
      text-align: center;
      padding: 32px;
      color: var(--text-muted);
      font-size: 0.85rem;
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
      padding: 14px 16px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .history-item:hover {
      background: rgba(255, 255, 255, 0.06);
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
      padding: 8px 16px;
      background: rgba(0, 224, 164, 0.1);
      border: 1px solid rgba(0, 224, 164, 0.2);
      border-radius: 50px;
      font-size: 0.85rem;
      color: var(--accent-cyan);
      margin-bottom: 16px;
    }

    .result-intro h2 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .result-intro p {
      color: var(--text-muted);
      font-size: 0.85rem;
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
      padding: 10px 16px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-secondary);
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .result-nav-btn:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .result-nav-btn.active {
      background: rgba(0, 224, 164, 0.15);
      border-color: var(--accent-cyan);
      color: var(--accent-cyan);
    }

    /* 결과 카드 */
    .result-cards-container {
      position: relative;
      margin-bottom: 16px;
    }

    .result-card {
      display: none;
      background: rgba(13, 24, 41, 0.95);
      border: 1px solid rgba(0, 224, 164, 0.15);
      border-radius: 24px;
      padding: 24px 20px;
      animation: fadeIn 0.3s ease;
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
      margin-bottom: 16px;
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
      font-size: 1rem;
      font-weight: 700;
      color: var(--accent-cyan);
    }

    .result-card-number {
      padding: 4px 12px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      font-size: 0.75rem;
      color: var(--text-muted);
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
      width: 52px;
      height: 52px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.2rem;
      color: #fff;
      position: relative;
      opacity: 0;
      animation: ballPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
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

    @keyframes ballPop {
      0% { opacity: 0; transform: scale(0); }
      100% { opacity: 1; transform: scale(1); }
    }

    .ball-yellow { background: var(--ball-yellow); box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4); }
    .ball-blue { background: var(--ball-blue); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }
    .ball-red { background: var(--ball-red); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4); }
    .ball-gray { background: var(--ball-gray); box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4); }
    .ball-green { background: var(--ball-green); box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4); }

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
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .result-indicator.active {
      width: 24px;
      border-radius: 4px;
      background: var(--accent-cyan);
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
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(11, 19, 43, 0.98);
      backdrop-filter: blur(10px);
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }

    .loading-modal.active {
      display: flex;
    }

    .loading-content {
      text-align: center;
      padding: 40px;
    }

    .loading-spinner {
      width: 64px;
      height: 64px;
      border: 4px solid rgba(0, 224, 164, 0.2);
      border-top-color: var(--accent-cyan);
      border-radius: 50%;
      margin: 0 auto 24px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .loading-text {
      font-size: 1rem;
      color: var(--text-secondary);
      margin-bottom: 20px;
    }

    .loading-progress {
      width: 200px;
      height: 4px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 2px;
      margin: 0 auto;
      overflow: hidden;
    }

    .loading-bar {
      height: 100%;
      background: var(--gradient-cyan);
      border-radius: 2px;
      width: 0;
      transition: width 0.3s ease;
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

    /* ===== 모바일 반응형 ===== */
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
        gap: 12px;
      }

      .style-buttons-grid .style-btn:last-child {
        grid-column: span 1;
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

	/* 아주 좁을 때는 '남은 분석' 글자 숨겨서 공간 확보 */
	@media (max-width: 420px){
	  .credit-badge span:nth-child(2){ display:none; } /* '남은 분석' */
	  .app-logo{ max-width: 40%; }
	}
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
      로또인사이트
    </a>
    <div class="navbar-right">
      <div class="credit-badge">
        <span>🔋</span>
        <span>남은 분석</span>
        <span class="credit-count" id="navCredit">1회</span>
      </div>
      <button class="charge-btn" id="chargeBtn">+ 충전</button>
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
          <button class="style-btn active" data-style="hotcold">
            <span class="style-icon">🔥</span>
            <div>
              <span class="style-name">Hot/Cold</span>
              <span class="style-desc">과출/미출 패턴 분석</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn active" data-style="balance">
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
            <span class="style-icon">🧠</span>
            <div>
              <span class="style-name">상관관계</span>
              <span class="style-desc">동반출현 패턴</span>
            </div>
            <span class="style-check">✓</span>
          </button>
          <button class="style-btn" data-style="ac">
            <span class="style-icon">🧮</span>
            <div>
              <span class="style-name">AC값 분석</span>
              <span class="style-desc">숫자 다양성 지수</span>
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
        <button class="analyze-btn" id="analyzeBtn">
          🔮 AI 분석 시작하기
        </button>
        <p class="analyze-cost">지금 가입 시 <span>무료 분석 2회</span> 제공</p>
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
    <div class="result-view" id="resultView">
      
      <!-- 결과 인트로 -->
      <div class="result-intro">
        <div class="result-badge">
          ✨ AI 분석 완료
        </div>
        <h2>당신만의 맞춤 조합입니다</h2>
        <p id="resultSubtitle">5개 스타일 · <?php echo $current_round_fmt ?: '---'; ?>회차 데이터 기반</p>
      </div>

      <!-- 결과 네비게이션 -->
      <div class="result-nav" id="resultNav">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 결과 카드 컨테이너 -->
      <div class="result-cards-container" id="resultCardsContainer">
        <!-- 동적으로 생성됨 -->
      </div>

      <!-- 결과 인디케이터 -->
      <div class="result-indicators" id="resultIndicators">
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

    </div>
  </div>

  <!-- 로딩 모달 -->
  <div class="loading-modal" id="loadingModal">
    <div class="loading-content">
      <div class="loading-spinner"></div>
      <div class="loading-text" id="loadingText">AI가 패턴을 분석 중...</div>
      <div class="loading-progress">
        <div class="loading-bar" id="loadingBar"></div>
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
      const loggedUser = localStorage.getItem(StorageManager.KEYS.USER);
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
        isLoggedIn: !!loggedUser,
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
      selectedStyles: ['hotcold', 'balance'],
      history: StorageManager.getHistory(),
      results: []
    };

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
            state.selectedStyles = state.selectedStyles.filter(s => s !== style);
          }
        } else {
          btn.classList.add('active');
          state.selectedStyles.push(style);
        }
        
        if (selectedCountEl) {
          selectedCountEl.textContent = state.selectedStyles.length;
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
        const resp = await fetch(location.pathname, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: new URLSearchParams({
            mode: 'use_credit'
          })
        });

        const data = await resp.json();

        if (!data.success) {
          // 서버 기준 최신 잔액으로 상태 동기화
          if (typeof data.credit_balance !== 'undefined') {
            state.paidCredit = Number(data.credit_balance) || 0;
          }
          if (typeof data.free_uses !== 'undefined') {
            state.freeCredit = Number(data.free_uses) || 0;
          }
          updateCreditDisplay();

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
        state.paidCredit = Number(data.credit_balance) || 0;
        state.freeCredit = Number(data.free_uses) || 0;
        updateCreditDisplay();
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
      const loadingBar = document.getElementById('loadingBar');
      const loadingText = document.getElementById('loadingText');
      const dataStats = document.getElementById('dataStats');
      const recentNumbers = document.getElementById('recentNumbers');
      const dataUpdate = document.getElementById('dataUpdate');
      
      const messages = [
        "📊 동행복권 데이터 로딩 중...",
        "🔍 최근 100회 당첨 패턴 분석...",
        "📈 과출/미출 번호 계산 중...",
        "⚖️ 홀짝/고저 균형 최적화...",
        "✨ 최종 조합 선별 중..."
      ];

      let progress = 0;
      let msgIndex = 0;
      let dataShown = false;

      const interval = setInterval(() => {
        progress += Math.random() * 12 + 4;
        if (progress > 100) progress = 100;
        
        loadingBar.style.width = progress + '%';
        
        if (progress > msgIndex * 20 && msgIndex < messages.length) {
          loadingText.textContent = messages[msgIndex];
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
      }, 300);
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

    // ===== 스타일 정보 =====
    const styleInfo = {
      'hotcold': { icon: '🔥', name: 'Hot/Cold', genStyle: 'hot', desc: '과출/미출 번호 중심' },
      'balance': { icon: '⚖️', name: '홀짝/고저', genStyle: 'balanced', desc: '균형 비율 최적화' },
      'color': { icon: '🎨', name: '색상볼', genStyle: 'color', desc: '색상 분포 균형' },
      'correlation': { icon: '🧠', name: '상관관계', genStyle: 'pair', desc: '동반출현 패턴' },
      'ac': { icon: '🧮', name: 'AC값', genStyle: 'balanced', desc: '다양성 지수 최적화' }
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
      dashboardView.classList.add('hidden');
      resultView.classList.add('visible');
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
		<button class="result-nav-btn ${idx === 0 ? 'active' : ''}" data-index="${idx}">
		  <span class="nav-icon">${r.info.icon}</span>
		  <span>${r.info.name}</span>
		</button>
	  `).join('');

	  // 카드들
	  resultCardsContainer.innerHTML = state.results.map((r, idx) => {
		const report = window.lottoGenerator.generateReport(r);

		return `
		  <div class="result-card ${idx === 0 ? 'active' : ''}" data-index="${idx}">
			<div class="result-card-header">
			  <div class="result-card-style">
				<span class="result-card-style-icon">${r.info.icon}</span>
				<span class="result-card-style-name">${r.info.name} 분석</span>
			  </div>
			  <span class="result-card-number">${idx + 1}/${state.results.length}</span>
			</div>

			<div class="balls-container">
			  ${r.numbers.map((num, i) => `
				<div class="ball-3d ball-${getBallColor(num)}" style="animation-delay: ${i * 0.1}s">${num}</div>
			  `).join('')}
			</div>

			<div class="number-stories">
			  <div class="story-title">📋 번호별 선정 이유</div>
			  <div class="story-grid">
				${r.stories.map(story => `
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

			<div class="balance-section">
			  <div class="balance-header">
				<span class="balance-label">📊 균형 점수</span>
				<span class="balance-value">${r.score}<span style="font-size: 0.8rem;">점</span></span>
			  </div>
			  <div class="balance-bar">
				<div class="balance-fill" style="--target-width: ${r.score}%; animation: fillBar 1.5s ease 0.3s forwards;"></div>
			  </div>
			  <div class="balance-details">
				<div class="balance-item">
				  <div class="balance-item-icon">⚖️</div>
				  <div class="balance-item-value">${r.numbers.filter(n => n > 22).length}:${r.numbers.filter(n => n <= 22).length}</div>
				  <div class="balance-item-label">고/저</div>
				</div>
				<div class="balance-item">
				  <div class="balance-item-icon">🎲</div>
				  <div class="balance-item-value">${r.numbers.filter(n => n % 2 === 1).length}:${r.numbers.filter(n => n % 2 === 0).length}</div>
				  <div class="balance-item-label">홀/짝</div>
				</div>
				<div class="balance-item">
				  <div class="balance-item-icon">📊</div>
				  <div class="balance-item-value">상위${Math.round((100 - r.score) / 2 + 5)}%</div>
				  <div class="balance-item-label">유사도</div>
				</div>
			  </div>
			</div>
		  </div>
		`;
	  }).join('');

	  // 하단 점 인디케이터
	  resultIndicators.innerHTML = state.results.map((_, idx) => `
		<div class="result-indicator ${idx === 0 ? 'active' : ''}" data-index="${idx}"></div>
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
    }

    function updateCreditDisplay() {
      document.getElementById('navCredit').textContent = (state.freeCredit + state.paidCredit) + '회';
      updateAnalyzeCost();
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
              <div class="charge-option" data-amount="5" data-price="1000" onclick="selectChargeOption(this)">
                <div class="charge-option-left">
                  <div class="charge-amount">5회</div>
                  <div class="charge-per">회당 200원</div>
                </div>
                <div class="charge-option-right">
                  <div class="charge-price">₩1,000</div>
                </div>
              </div>
              
              <div class="charge-option popular selected" data-amount="15" data-price="2500" data-bonus="5" onclick="selectChargeOption(this)">
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
              
              <div class="charge-option best" data-amount="35" data-price="5000" data-bonus="15" onclick="selectChargeOption(this)">
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
            
            <button class="charge-submit-btn" onclick="processPayment()">
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
</body>
</html>


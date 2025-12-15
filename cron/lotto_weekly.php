<?php
/**
 * /cron/lotto_weekly.php
 *
 * - DB에 저장된 마지막 회차 이후의 회차를
 *   동행복권 API에서 가져와 g5_lotto_draw에 적재
 * - 주간 cron에서 정기 실행용
 * - 중간에 cron이 몇 주 빠져도, "빈 회차"가 있으면 최대 N회까지 한 번에 채움
 */

$common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
if (file_exists($common_path)) {
    include_once($common_path);
} else {
    die("common.php not found\n");
}

// lotto 유틸
include_once(G5_PATH . '/lib/lotto_draw.lib.php');

echo "=== Lotto Weekly Start : " . date('Y-m-d H:i:s') . " ===\n";

// ─────────────────────────────────────
// 1) 현재 DB에 저장된 최대 회차 조회
// ─────────────────────────────────────
$row = sql_fetch("SELECT MAX(draw_no) AS max_draw_no FROM g5_lotto_draw");
$max_draw_no = (int)($row['max_draw_no'] ?? 0);

if ($max_draw_no <= 0) {
    // 아직 한 번도 적재된 적이 없는 경우: 1회차부터 시작
    $next_draw_no = 1;
    echo "[INFO] 현재 저장된 회차가 없습니다. 1회차부터 시도합니다.\n";
} else {
    $next_draw_no = $max_draw_no + 1;
    echo "[INFO] 현재 저장된 최대 회차: {$max_draw_no}, 다음 시도 회차: {$next_draw_no}\n";
}

// ─────────────────────────────────────
// 2) 새 회차 채우기
//    - 한 번 실행 시 최대 $max_gap 회차까지 시도
//    - 동행복권에서 아직 발표 안 된 회차를 만나면 거기서 STOP
// ─────────────────────────────────────
$max_gap = 10;   // 혹시 cron이 몇 주 빠져도, 최대 10주치까지 한 번에 채워 넣도록
$inserted = 0;

for ($drw = $next_draw_no; $drw < $next_draw_no + $max_gap; $drw++) {
    echo "[INFO] 회차 {$drw} 데이터 가져오기 시도...\n";

    $ok = li_fetch_and_save_lotto_draw($drw);

    if (!$ok) {
        // 보통은 "아직 발표 안 된 회차"일 때 여기로 들어오게 됨
        echo "[INFO] 회차 {$drw} 는 아직 동행복권에 데이터가 없거나, API 실패. 여기서 종료합니다.\n";
        break;
    }

    echo "[OK] 회차 {$drw} 저장 완료.\n";
    $inserted++;

    // API/서버 부담 줄이기 위해 약간 딜레이
    usleep(200000); // 0.2초
}

// ─────────────────────────────────────
// 3) 결과 로그
// ─────────────────────────────────────
if ($inserted === 0) {
    echo "[INFO] 새로 저장된 회차가 없습니다. (이미 최신 상태일 수 있음)\n";
} else {
    echo "[INFO] 총 {$inserted}개 회차가 새로 저장되었습니다.\n";
}

echo "=== Lotto Weekly End : " . date('Y-m-d H:i:s') . " ===\n";

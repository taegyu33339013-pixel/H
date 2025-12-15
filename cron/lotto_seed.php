<?php
/**
 * /cron/lotto_seed.php
 *
 * - 동행복권 JSON API에서 로또 6/45 회차별 정보를 읽어와
 *   g5_lotto_draw 테이블에 1회차부터 최신 회차까지 적재하는 스크립트
 * - 최초 1번 실행용, 또는 중간에 끊겼을 때 다시 실행해도 안전하게 이어서 저장 가능
 */

$common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
if (file_exists($common_path)) {
    include_once($common_path);
} else {
    die("common.php not found\n");
}

// 로또 유틸 함수 (li_fetch_and_save_lotto_draw 등)
include_once(G5_PATH . '/lib/lotto_draw.lib.php');

echo "=== Lotto Seed Start : " . date('Y-m-d H:i:s') . " ===\n";

// ─────────────────────────────────────
// 1) 현재 DB에 저장된 최대 회차 조회
// ─────────────────────────────────────
$row = sql_fetch("SELECT MAX(draw_no) AS max_draw_no FROM g5_lotto_draw");
$max_draw_no = (int)($row['max_draw_no'] ?? 0);

if ($max_draw_no <= 0) {
    // 아직 데이터가 하나도 없으면 1회차부터 시작
    $start_draw_no = 1;
    echo "[INFO] 현재 저장된 회차가 없습니다. 1회차부터 적재를 시작합니다.\n";
} else {
    // 일부만 들어있으면, 그 다음 회차부터 이어서
    $start_draw_no = $max_draw_no + 1;
    echo "[INFO] 현재 저장된 최대 회차: {$max_draw_no}회. {$start_draw_no}회차부터 추가 적재를 시작합니다.\n";
}

// 안전장치: 이 스크립트가 시도할 최대 회차 번호 (여유 있게 2000 정도)
$max_draw_limit = 2000;

// 실제로 새로 저장된 회차 수
$inserted = 0;

// ─────────────────────────────────────
// 2) 1회차(또는 현재 최대+1)부터 최신 회차까지 쭉 적재
//    - 동행복권에 데이터가 없는 회차를 처음 만나면 거기서 STOP
// ─────────────────────────────────────
for ($drw = $start_draw_no; $drw <= $max_draw_limit; $drw++) {
    echo "[INFO] 회차 {$drw} 데이터 가져오기 시도...\n";

    // 이미 DB에 들어있는지 혹시 체크 (중복 방지용)
    $exists = sql_fetch("SELECT 1 AS ok FROM g5_lotto_draw WHERE draw_no = '{$drw}'");
    if ($exists && (int)$exists['ok'] === 1) {
        echo "[INFO] 회차 {$drw} 는 이미 DB에 존재합니다. 건너뜁니다.\n";
        continue;
    }

    // 동행복권 API 호출 + DB 저장 (lib 함수 재사용)
    $ok = li_fetch_and_save_lotto_draw($drw);

    if (!$ok) {
        // 보통은 "아직 발표되지 않은 회차"에 도달했을 때 여기로 들어옴
        echo "[INFO] 회차 {$drw} 에 대한 동행복권 데이터가 없습니다. 여기서 적재를 종료합니다.\n";
        break;
    }

    echo "[OK] 회차 {$drw} 저장 완료.\n";
    $inserted++;

    // API / 서버 부담 줄이기 위해 0.2초 딜레이
    usleep(200000);
}

// ─────────────────────────────────────
// 3) 결과 출력
// ─────────────────────────────────────
if ($inserted === 0) {
    echo "[INFO] 새로 저장된 회차가 없습니다. (이미 모든 회차가 저장되어 있을 수 있습니다.)\n";
} else {
    echo "[INFO] 총 {$inserted}개 회차가 새로 저장되었습니다.\n";
}

echo "=== Lotto Seed End : " . date('Y-m-d H:i:s') . " ===\n";

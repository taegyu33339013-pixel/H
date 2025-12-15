<?php
// /ajax/prize_simulator.php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

function li_net_amount(int $gross): int
{
    // 동행복권/안내 기준: 200만원 초과 당첨금 과세(2023-01-01~), 3억 초과분 33%, 그 이하는 22% :contentReference[oaicite:1]{index=1}
    if ($gross <= 2000000) return $gross;

    $tax = 0;

    // 3억원 이하 구간 22%
    $tier1 = min($gross, 300000000);
    $tax += (int) floor($tier1 * 0.22);

    // 3억원 초과 구간 33%
    if ($gross > 300000000) {
        $tax += (int) floor(($gross - 300000000) * 0.33);
    }

    $net = $gross - $tax;
    return ($net < 0) ? 0 : $net;
}

function li_avg_recent_prizes(int $n = 10): array
{
    $n = max(1, min(50, $n));
    $avg = sql_fetch("
        SELECT
            ROUND(AVG(first_prize_each))  AS first_each,
            ROUND(AVG(second_prize_each)) AS second_each,
            ROUND(AVG(third_prize_each))  AS third_each
        FROM (
            SELECT first_prize_each, second_prize_each, third_prize_each
            FROM g5_lotto_draw
            WHERE first_prize_each IS NOT NULL AND first_prize_each > 0
            ORDER BY draw_no DESC
            LIMIT {$n}
        ) t
    ");

    return [
        'first'  => (int)($avg['first_each'] ?? 0),
        'second' => (int)($avg['second_each'] ?? 0),
        'third'  => (int)($avg['third_each'] ?? 0),
    ];
}

$reqRound = (int)($_GET['round'] ?? 0);

// 최신 회차
$latest = sql_fetch("SELECT MAX(draw_no) AS max_no FROM g5_lotto_draw");
$maxNo  = (int)($latest['max_no'] ?? 0);

if ($maxNo <= 0) {
    echo json_encode(['error' => 'no draw data'], JSON_UNESCAPED_UNICODE);
    exit;
}

// round가 없으면 “다음 회차(예상)”로
$round = $reqRound > 0 ? $reqRound : ($maxNo + 1);

// DB에 실제 회차가 있는지 확인
$row = sql_fetch("
    SELECT draw_no, draw_date,
           first_prize_each, second_prize_each, third_prize_each
    FROM g5_lotto_draw
    WHERE draw_no = {$round}
    LIMIT 1
");

$estimated = true;
$drawDate = null;

if ($row && (int)$row['draw_no'] > 0 && (int)$row['first_prize_each'] > 0) {
    // ✅ 실제값
    $estimated = false;
    $drawDate = (string)$row['draw_date'];

    $first  = (int)$row['first_prize_each'];
    $second = (int)$row['second_prize_each'];
    $third  = (int)$row['third_prize_each'];
} else {
    // ✅ 추정치(최근 평균)
    $avg = li_avg_recent_prizes(10);
    $first  = $avg['first'];
    $second = $avg['second'];
    $third  = $avg['third'];
}

echo json_encode([
    'round'     => $round,
    'draw_date' => $drawDate,
    'estimated' => $estimated,
    'first'  => ['gross' => $first,  'net' => li_net_amount($first)],
    'second' => ['gross' => $second, 'net' => li_net_amount($second)],
    'third'  => ['gross' => $third,  'net' => li_net_amount($third)],
], JSON_UNESCAPED_UNICODE);
exit;

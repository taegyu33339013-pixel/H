<?php
/**
 * 크레딧 시스템 통일 검증 스크립트
 * 
 * 마이그레이션 후 데이터 무결성 검증
 * 
 * 사용법:
 * php verify_credits.php
 */

require_once __DIR__ . '/common.php';

if (!defined('_GNUBOARD_')) {
    die("❌ common.php 로드 실패\n");
}

require_once G5_LIB_PATH . '/lotto_credit.lib.php';

echo "═══════════════════════════════════════════════════════\n";
echo "🔍 크레딧 시스템 검증\n";
echo "═══════════════════════════════════════════════════════\n\n";

// 1. 마이그레이션 후 크레딧 총합 확인
echo "📊 1. 크레딧 총합 확인\n";
echo "───────────────────────────────────────────────────────\n";

$sql_total = "SELECT 
    COUNT(*) AS total_users,
    SUM(free_uses) AS total_free,
    SUM(credit_balance) AS total_paid
FROM {$g5['lotto_credit_table']}";
$total_row = sql_fetch($sql_total);

echo "✅ 총 회원 수: " . number_format($total_row['total_users']) . "명\n";
echo "✅ 총 무료 크레딧: " . number_format($total_row['total_free']) . "회\n";
echo "✅ 총 유료 크레딧: " . number_format($total_row['total_paid']) . "회\n";
echo "✅ 총 크레딧: " . number_format($total_row['total_free'] + $total_row['total_paid']) . "회\n\n";

// 2. 100포인트 이상 남은 회원 확인 (마이그레이션 누락)
echo "🔍 2. GNUBOARD 포인트 잔액 확인\n";
echo "───────────────────────────────────────────────────────\n";

$sql_remaining = "SELECT mb_id, mb_point FROM {$g5['member_table']} WHERE mb_point >= 100";
$result_remaining = sql_query($sql_remaining);
$remaining_count = sql_num_rows($result_remaining);

if ($remaining_count > 0) {
    echo "⚠️  아직 100포인트 이상 보유 회원: {$remaining_count}명\n";
    echo "   (마이그레이션 필요)\n\n";
    
    $remaining_users = [];
    while ($row = sql_fetch_array($result_remaining)) {
        $remaining_users[] = $row;
        echo "   - {$row['mb_id']}: {$row['mb_point']}포인트\n";
    }
    echo "\n";
} else {
    echo "✅ 100포인트 이상 보유 회원 없음 (마이그레이션 완료)\n\n";
}

// 3. 크레딧 사용 로그 확인
echo "📝 3. 최근 크레딧 사용 로그 (최근 7일)\n";
echo "───────────────────────────────────────────────────────\n";

$sql_log = "SELECT 
    change_type,
    COUNT(*) AS count,
    SUM(ABS(amount)) AS total_amount
FROM {$g5['lotto_credit_log_table']}
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY change_type
ORDER BY count DESC";
$result_log = sql_query($sql_log);

if (sql_num_rows($result_log) > 0) {
    echo "변경 유형 | 건수 | 총량\n";
    echo "───────────────────────────────────────────────────────\n";
    while ($row = sql_fetch_array($result_log)) {
        $type_name = [
            'free' => '무료 사용',
            'use' => '유료 사용',
            'charge' => '충전',
            'admin_adjust' => '관리자 조정',
            'migration' => '마이그레이션'
        ];
        echo sprintf(
            "%-10s | %5s | %s회\n",
            $type_name[$row['change_type']] ?? $row['change_type'],
            number_format($row['count']),
            number_format($row['total_amount'])
        );
    }
    echo "\n";
} else {
    echo "⚠️  최근 7일간 로그 없음\n\n";
}

// 4. 무료 크레딧 2회 제공 확인 (신규 회원)
echo "🎁 4. 신규 회원 무료 크레딧 확인 (최근 1일)\n";
echo "───────────────────────────────────────────────────────\n";

$sql_new = "SELECT mb_id, free_uses, created_at
FROM {$g5['lotto_credit_table']}
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY created_at DESC
LIMIT 10";
$result_new = sql_query($sql_new);

$new_count = sql_num_rows($result_new);
if ($new_count > 0) {
    echo "✅ 최근 1일 신규 회원: {$new_count}명\n";
    $all_have_free = true;
    while ($row = sql_fetch_array($result_new)) {
        $has_free = (int)$row['free_uses'] >= 2;
        $status = $has_free ? "✅" : "❌";
        if (!$has_free) $all_have_free = false;
        echo "   {$status} {$row['mb_id']}: 무료 {$row['free_uses']}회 (생성: {$row['created_at']})\n";
    }
    if ($all_have_free) {
        echo "\n✅ 모든 신규 회원이 무료 크레딧 2회를 보유하고 있습니다.\n\n";
    } else {
        echo "\n⚠️  일부 신규 회원이 무료 크레딧 2회를 받지 못했습니다.\n\n";
    }
} else {
    echo "ℹ️  최근 1일간 신규 회원 없음\n\n";
}

// 5. 크레딧 불일치 확인
echo "🔍 5. 크레딧 불일치 확인\n";
echo "───────────────────────────────────────────────────────\n";

// 크레딧은 있는데 회원이 없는 경우
$sql_orphan = "SELECT c.mb_id, c.free_uses, c.credit_balance
FROM {$g5['lotto_credit_table']} c
LEFT JOIN {$g5['member_table']} m ON c.mb_id = m.mb_id
WHERE m.mb_id IS NULL";
$result_orphan = sql_query($sql_orphan);
$orphan_count = sql_num_rows($result_orphan);

if ($orphan_count > 0) {
    echo "⚠️  회원이 삭제되었지만 크레딧이 남아있는 경우: {$orphan_count}건\n";
    while ($row = sql_fetch_array($result_orphan)) {
        echo "   - {$row['mb_id']}: 무료 {$row['free_uses']}회, 유료 {$row['credit_balance']}회\n";
    }
    echo "\n";
} else {
    echo "✅ 모든 크레딧이 유효한 회원과 연결되어 있습니다.\n\n";
}

// 6. 통계 요약
echo "📈 6. 통계 요약\n";
echo "───────────────────────────────────────────────────────\n";

// 크레딧 보유 회원 분포
$sql_dist = "SELECT 
    CASE 
        WHEN free_uses > 0 AND credit_balance > 0 THEN '무료+유료'
        WHEN free_uses > 0 THEN '무료만'
        WHEN credit_balance > 0 THEN '유료만'
        ELSE '없음'
    END AS credit_type,
    COUNT(*) AS count
FROM {$g5['lotto_credit_table']}
GROUP BY credit_type";
$result_dist = sql_query($sql_dist);

echo "크레딧 보유 유형별 회원 수:\n";
while ($row = sql_fetch_array($result_dist)) {
    echo "   - {$row['credit_type']}: {$row['count']}명\n";
}
echo "\n";

// 평균 크레딧
$sql_avg = "SELECT 
    AVG(free_uses) AS avg_free,
    AVG(credit_balance) AS avg_paid,
    AVG(free_uses + credit_balance) AS avg_total
FROM {$g5['lotto_credit_table']}";
$avg_row = sql_fetch($sql_avg);

echo "평균 크레딧:\n";
echo "   - 무료: " . number_format($avg_row['avg_free'], 2) . "회\n";
echo "   - 유료: " . number_format($avg_row['avg_paid'], 2) . "회\n";
echo "   - 총합: " . number_format($avg_row['avg_total'], 2) . "회\n\n";

echo "═══════════════════════════════════════════════════════\n";
echo "✅ 검증 완료!\n";
echo "═══════════════════════════════════════════════════════\n";

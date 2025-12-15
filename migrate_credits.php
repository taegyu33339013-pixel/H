<?php
/**
 * 크레딧 시스템 통일 마이그레이션 스크립트
 * 
 * GNUBOARD 포인트 → 전용 크레딧 시스템으로 변환
 * 
 * 사용법:
 * php migrate_credits.php [--dry-run] [--force]
 * 
 * --dry-run: 실제 변환 없이 미리보기만
 * --force: 확인 없이 바로 실행
 */

// 공통 파일 로드
require_once __DIR__ . '/common.php';

if (!defined('_GNUBOARD_')) {
    die("❌ common.php 로드 실패\n");
}

// 라이브러리 로드
require_once G5_LIB_PATH . '/lotto_credit.lib.php';

// 옵션 파싱
$dry_run = in_array('--dry-run', $argv);
$force = in_array('--force', $argv);

echo "═══════════════════════════════════════════════════════\n";
echo "🔄 크레딧 시스템 마이그레이션 시작\n";
echo "═══════════════════════════════════════════════════════\n\n";

if ($dry_run) {
    echo "⚠️  DRY-RUN 모드: 실제 변환 없이 미리보기만 합니다.\n\n";
}

// Step 1: 현황 파악
echo "📊 Step 1: 현황 파악\n";
echo "───────────────────────────────────────────────────────\n";

// GNUBOARD 포인트 보유 회원
$sql_point = "SELECT mb_id, mb_point FROM {$g5['member_table']} WHERE mb_point >= 100";
$result_point = sql_query($sql_point);
$point_users = [];
$total_points = 0;

while ($row = sql_fetch_array($result_point)) {
    $point_users[] = $row;
    $total_points += $row['mb_point'];
}

echo "✅ GNUBOARD 포인트 보유 회원: " . count($point_users) . "명\n";
echo "   총 포인트: " . number_format($total_points) . "점\n";
echo "   변환 예상 크레딧: " . number_format(floor($total_points / 100)) . "회\n\n";

// 전용 크레딧 보유 회원
$sql_credit = "SELECT mb_id, free_uses, credit_balance FROM {$g5['lotto_credit_table']}";
$result_credit = sql_query($sql_credit);
$credit_users = [];
$total_free = 0;
$total_paid = 0;

while ($row = sql_fetch_array($result_credit)) {
    $credit_users[] = $row;
    $total_free += $row['free_uses'];
    $total_paid += $row['credit_balance'];
}

echo "✅ 전용 크레딧 보유 회원: " . count($credit_users) . "명\n";
echo "   총 무료 크레딧: " . number_format($total_free) . "회\n";
echo "   총 유료 크레딧: " . number_format($total_paid) . "회\n\n";

// 중복 회원 확인
$point_mb_ids = array_column($point_users, 'mb_id');
$credit_mb_ids = array_column($credit_users, 'mb_id');
$duplicate_ids = array_intersect($point_mb_ids, $credit_mb_ids);

if (count($duplicate_ids) > 0) {
    echo "⚠️  중복 회원 발견: " . count($duplicate_ids) . "명\n";
    echo "   (포인트와 크레딧 모두 보유)\n";
    foreach ($duplicate_ids as $mb_id) {
        $point_row = array_filter($point_users, function($u) use ($mb_id) { return $u['mb_id'] === $mb_id; });
        $credit_row = array_filter($credit_users, function($u) use ($mb_id) { return $u['mb_id'] === $mb_id; });
        $point_val = reset($point_row)['mb_point'];
        $credit_val = reset($credit_row);
        echo "   - {$mb_id}: 포인트 {$point_val}점, 크레딧 무료{$credit_val['free_uses']}회/유료{$credit_val['credit_balance']}회\n";
    }
    echo "\n";
}

// 확인
if (!$force && !$dry_run) {
    echo "⚠️  위 회원들의 GNUBOARD 포인트를 전용 크레딧으로 변환합니다.\n";
    echo "계속하시겠습니까? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($line) !== 'yes') {
        echo "\n❌ 마이그레이션 취소됨\n";
        exit;
    }
    echo "\n";
}

// Step 2: 데이터 마이그레이션
echo "🔄 Step 2: 데이터 마이그레이션\n";
echo "───────────────────────────────────────────────────────\n";

$migrated_count = 0;
$migrated_credits = 0;
$errors = [];

foreach ($point_users as $user) {
    $mb_id = $user['mb_id'];
    $point = (int)$user['mb_point'];
    
    // 100포인트 = 1크레딧으로 변환
    $credits = floor($point / 100);
    
    if ($credits <= 0) {
        continue;
    }
    
    // 중복 회원 처리: 기존 크레딧에 추가
    $existing_credit = lotto_get_credit_row($mb_id, false);
    $before_free = (int)($existing_credit['free_uses'] ?? 0);
    $before_paid = (int)($existing_credit['credit_balance'] ?? 0);
    
    try {
        if (!$dry_run) {
            // 전용 크레딧으로 충전
            $charge_result = lotto_charge_credit(
                $mb_id,
                $credits,
                'GNUBOARD 포인트 마이그레이션 (자동)',
                'migration_' . date('YmdHis') . '_' . $mb_id,
                'migration'
            );
            
            if (!$charge_result['success']) {
                $errors[] = "{$mb_id}: 크레딧 충전 실패 - " . ($charge_result['reason'] ?? '알 수 없음');
                continue;
            }
            
            // GNUBOARD 포인트 차감
            $point_result = insert_point(
                $mb_id,
                -($credits * 100),
                '전용 크레딧 시스템으로 전환 (마이그레이션)',
                '@migration',
                $mb_id,
                '마이그레이션'
            );
            
            if (!$point_result) {
                $errors[] = "{$mb_id}: 포인트 차감 실패";
                continue;
            }
        }
        
        $migrated_count++;
        $migrated_credits += $credits;
        
        echo "✅ {$mb_id}: {$point}포인트 → {$credits}크레딧 변환";
        if (count($duplicate_ids) > 0 && in_array($mb_id, $duplicate_ids)) {
            echo " (기존 크레딧에 추가)";
        }
        echo "\n";
        
    } catch (Exception $e) {
        $errors[] = "{$mb_id}: 예외 발생 - " . $e->getMessage();
    }
}

echo "\n";

// Step 3: 결과 요약
echo "📈 Step 3: 마이그레이션 결과\n";
echo "───────────────────────────────────────────────────────\n";

if ($dry_run) {
    echo "⚠️  DRY-RUN 모드였으므로 실제 변환은 수행되지 않았습니다.\n\n";
} else {
    echo "✅ 마이그레이션 완료: {$migrated_count}명\n";
    echo "✅ 변환된 크레딧: " . number_format($migrated_credits) . "회\n\n";
}

if (count($errors) > 0) {
    echo "❌ 오류 발생: " . count($errors) . "건\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
    echo "\n";
}

// Step 4: 검증
echo "🔍 Step 4: 데이터 검증\n";
echo "───────────────────────────────────────────────────────\n";

// 100포인트 이상 남은 회원 확인
$sql_remaining = "SELECT mb_id, mb_point FROM {$g5['member_table']} WHERE mb_point >= 100";
$result_remaining = sql_query($sql_remaining);
$remaining_count = sql_num_rows($result_remaining);

if ($remaining_count > 0) {
    echo "⚠️  아직 100포인트 이상 보유 회원: {$remaining_count}명\n";
    echo "   (마이그레이션 누락 가능성)\n\n";
} else {
    echo "✅ 100포인트 이상 보유 회원 없음 (마이그레이션 완료)\n\n";
}

// 크레딧 총합 확인
$sql_total = "SELECT 
    COUNT(*) AS total_users,
    SUM(free_uses) AS total_free,
    SUM(credit_balance) AS total_paid
FROM {$g5['lotto_credit_table']}";
$total_row = sql_fetch($sql_total);

echo "📊 전용 크레딧 시스템 현황:\n";
echo "   총 회원 수: " . number_format($total_row['total_users']) . "명\n";
echo "   총 무료 크레딧: " . number_format($total_row['total_free']) . "회\n";
echo "   총 유료 크레딧: " . number_format($total_row['total_paid']) . "회\n";
echo "   총 크레딧: " . number_format($total_row['total_free'] + $total_row['total_paid']) . "회\n\n";

echo "═══════════════════════════════════════════════════════\n";
if ($dry_run) {
    echo "✅ DRY-RUN 완료 (실제 변환 없음)\n";
} else {
    echo "✅ 마이그레이션 완료!\n";
}
echo "═══════════════════════════════════════════════════════\n";

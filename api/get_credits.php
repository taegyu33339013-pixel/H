<?php
/**
 * 회원 크레딧 조회 API (전용 크레딧 시스템)
 */
include_once('../_common.php');

// 전용 크레딧 라이브러리 로드
require_once G5_LIB_PATH . '/lotto_credit.lib.php';

header('Content-Type: application/json; charset=utf-8');

if (!defined('_GNUBOARD_')) {
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// 로그인 체크
if (!$is_member) {
    echo json_encode([
        'success' => false,
        'error' => 'Not logged in',
        'message' => '로그인이 필요합니다.',
        'free_uses' => 0,
        'credit_balance' => 0,
        'total' => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 전용 크레딧 조회 (없으면 자동 생성 - 무료 크레딧 없음, 회원가입 시에만 지급)
$credit = lotto_get_credit_row($member['mb_id'], true);

$free_uses = (int)($credit['free_uses'] ?? 0);
$credit_balance = (int)($credit['credit_balance'] ?? 0);
$total = $free_uses + $credit_balance;

echo json_encode([
    'success' => true,
    'user_id' => $member['mb_id'],
    'user_name' => $member['mb_name'] ?? '',
    'profile_image' => $member['mb_1'] ?? '',
    'free_uses' => $free_uses,
    'credit_balance' => $credit_balance,
    'total' => $total,
    'analysis_count' => $total, // 총 분석 가능 횟수
    'is_premium' => $credit_balance > 0 || ($member['mb_level'] ?? 0) >= 5
], JSON_UNESCAPED_UNICODE);
?>


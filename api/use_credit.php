<?php
/**
 * 크레딧 사용 API (전용 크레딧 시스템)
 * 분석 1회당 크레딧 1회 차감 (무료 → 유료 순서)
 */
include_once('../_common.php');

// 전용 크레딧 라이브러리 로드
require_once G5_LIB_PATH . '/lotto_credit.lib.php';

header('Content-Type: application/json; charset=utf-8');

if (!defined('_GNUBOARD_')) {
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit;
}

// 로그인 체크
if (!$is_member) {
    echo json_encode([
        'success' => false,
        'error' => 'Not logged in',
        'message' => '로그인이 필요합니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 회차 정보 (선택사항)
$round_no = isset($_POST['round_no']) ? (int)$_POST['round_no'] : 0;
$memo = $round_no > 0 ? "AI 분석 실행 (회차 {$round_no})" : 'AI 분석 실행';
$ref_key = $round_no > 0 ? "round_{$round_no}" : 'api_' . time();

// 전용 크레딧 사용
$use = lotto_use_one_analysis(
    $member['mb_id'],
    $memo,
    $ref_key
);

if (!$use['success']) {
    $reason_map = [
        'INVALID_MEMBER' => '회원 정보가 올바르지 않습니다.',
        'NO_CREDIT' => '사용 가능한 크레딧이 없습니다.',
        'DB_ERROR' => '데이터베이스 오류가 발생했습니다.'
    ];
    
    echo json_encode([
        'success' => false,
        'error' => $use['reason'] ?? 'ERROR',
        'message' => $reason_map[$use['reason']] ?? '크레딧 사용에 실패했습니다.',
        'free_uses' => $use['free_uses'] ?? 0,
        'credit_balance' => $use['credit_balance'] ?? 0,
        'total' => ($use['free_uses'] ?? 0) + ($use['credit_balance'] ?? 0)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 성공 응답
echo json_encode([
    'success' => true,
    'message' => '크레딧 사용 완료',
    'used_as' => $use['used_as'], // 'free' or 'paid'
    'free_uses' => $use['free_uses'],
    'credit_balance' => $use['credit_balance'],
    'total' => $use['free_uses'] + $use['credit_balance']
], JSON_UNESCAPED_UNICODE);
?>


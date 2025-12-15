<?php
/**
 * 크레딧 잔액 조회 API (전용 크레딧 시스템)
 * 하위 호환성을 위해 유지 (get_credits.php 사용 권장)
 */
@ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
require_once G5_LIB_PATH . '/lotto_credit.lib.php';

if (!defined('_GNUBOARD_')) { 
    echo json_encode(['ok'=>false,'message'=>'common.php 로드 실패']); 
    exit; 
}

if (!$is_member) { 
    echo json_encode(['ok'=>false,'message'=>'로그인 필요']); 
    exit; 
}

// 전용 크레딧 조회 (없으면 자동 생성)
$credit = lotto_get_credit_row($member['mb_id'], true);

$free_uses = (int)($credit['free_uses'] ?? 0);
$credit_balance = (int)($credit['credit_balance'] ?? 0);
$total = $free_uses + $credit_balance;

echo json_encode([
    'ok'=>true,
    'success'=>true,
    'free_uses'=>$free_uses,
    'credit_balance'=>$credit_balance,
    'total'=>$total
], JSON_UNESCAPED_UNICODE);

<?php
include_once('../common.php');
header('Content-Type: application/json; charset=utf-8');

// 최소 권한 체크 (원하시면 관리자만, 또는 로그인 회원만 허용)
if (!$is_member) {
    echo json_encode(['success' => false, 'message' => 'NOT_LOGGED_IN']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$round = (int)($data['round'] ?? 0);
$nums  = $data['numbers'] ?? [];

if ($round <= 0 || !is_array($nums) || count($nums) !== 6) {
    echo json_encode(['success' => false, 'message' => 'INVALID_DATA']);
    exit;
}

$nums = array_map('intval', $nums);
sort($nums);

// INSERT ... ON DUPLICATE KEY UPDATE
$sql = "
    INSERT INTO g5_lotto_ai_recommend
    SET round = '{$round}',
        a1 = '{$nums[0]}',
        a2 = '{$nums[1]}',
        a3 = '{$nums[2]}',
        a4 = '{$nums[3]}',
        a5 = '{$nums[4]}',
        a6 = '{$nums[5]}',
        created_at = '".G5_TIME_YMDHIS."'
    ON DUPLICATE KEY UPDATE
        a1 = VALUES(a1),
        a2 = VALUES(a2),
        a3 = VALUES(a3),
        a4 = VALUES(a4),
        a5 = VALUES(a5),
        a6 = VALUES(a6),
        updated_at = '".G5_TIME_YMDHIS."'
";
sql_query($sql);

echo json_encode(['success' => true]);

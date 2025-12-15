<?php
/**
 * 분석 결과 저장 API
 */
include_once('../_common.php');

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
        'error' => 'Not logged in'
    ]);
    exit;
}

// JSON 입력 받기
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['numbers'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid data'
    ]);
    exit;
}

$numbers = $input['numbers']; // 배열 [1,2,3,4,5,6]
$round = $input['round'] ?? 0;
$score = $input['score'] ?? 0;
$strategy = $input['strategy'] ?? '';

// 번호 유효성 검사
if (!is_array($numbers) || count($numbers) !== 6) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid numbers'
    ]);
    exit;
}

// 저장 테이블 확인 (없으면 생성)
$table_name = $g5['table_prefix'] . 'lotto_analysis';

$check_table = sql_query("SHOW TABLES LIKE '{$table_name}'");
if (!sql_num_rows($check_table)) {
    // 테이블 생성
    $create_sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `mb_id` varchar(100) NOT NULL,
        `lotto_round` int(11) DEFAULT 0,
        `numbers` varchar(50) NOT NULL,
        `score` int(11) DEFAULT 0,
        `strategy` varchar(100) DEFAULT '',
        `is_winner` tinyint(1) DEFAULT 0,
        `match_count` int(11) DEFAULT 0,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `mb_id` (`mb_id`),
        KEY `lotto_round` (`lotto_round`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    sql_query($create_sql);
}

// 데이터 저장
$numbers_str = implode(',', $numbers);
$mb_id = $member['mb_id'];
$created_at = G5_TIME_YMDHIS;

$sql = "INSERT INTO `{$table_name}` (mb_id, lotto_round, numbers, score, strategy, created_at) 
        VALUES ('{$mb_id}', {$round}, '{$numbers_str}', {$score}, '{$strategy}', '{$created_at}')";

$result = sql_query($sql);

if ($result) {
    $insert_id = sql_insert_id();
    
    echo json_encode([
        'success' => true,
        'message' => 'Analysis saved',
        'id' => $insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save'
    ]);
}
?>


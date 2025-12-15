<?php
/**
 * 분석 히스토리 조회 API
 */
include_once('../_common.php');

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
        'history' => []
    ]);
    exit;
}

$table_name = $g5['table_prefix'] . 'lotto_analysis';
$mb_id = $member['mb_id'];

// 페이지네이션
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset = ($page - 1) * $limit;

// 테이블 존재 확인
$check_table = sql_query("SHOW TABLES LIKE '{$table_name}'");
if (!sql_num_rows($check_table)) {
    echo json_encode([
        'success' => true,
        'history' => [],
        'total' => 0,
        'page' => $page,
        'limit' => $limit
    ]);
    exit;
}

// 전체 개수 조회
$count_sql = "SELECT COUNT(*) as total FROM `{$table_name}` WHERE mb_id = '{$mb_id}'";
$count_result = sql_fetch($count_sql);
$total = $count_result['total'];

// 히스토리 조회
$sql = "SELECT * FROM `{$table_name}` 
        WHERE mb_id = '{$mb_id}' 
        ORDER BY created_at DESC 
        LIMIT {$offset}, {$limit}";

$result = sql_query($sql);
$history = [];

while ($row = sql_fetch_array($result)) {
    $history[] = [
        'id' => $row['id'],
        'round' => $row['lotto_round'],
        'numbers' => explode(',', $row['numbers']),
        'score' => $row['score'],
        'strategy' => $row['strategy'],
        'is_winner' => (bool)$row['is_winner'],
        'match_count' => $row['match_count'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode([
    'success' => true,
    'history' => $history,
    'total' => (int)$total,
    'page' => $page,
    'limit' => $limit,
    'total_pages' => ceil($total / $limit)
]);
?>


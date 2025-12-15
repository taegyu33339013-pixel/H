<?php
// /ajax/activity_feed.php (DIAG VERSION)
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$limit = 5;

// ====== 0) DB 연결/테이블 존재/데이터 존재 확인용 ======
$dbinfo = sql_fetch("SELECT DATABASE() AS db, NOW() AS now");

$total = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_credit_log");
$byType = [];
$r = sql_query("SELECT change_type, COUNT(*) AS cnt FROM g5_lotto_credit_log GROUP BY change_type");
while ($row = sql_fetch_array($r)) $byType[] = $row;

$latestRows = [];
$r2 = sql_query("
  SELECT id, mb_id, change_type, amount, memo, ref_key, created_at
  FROM g5_lotto_credit_log
  ORDER BY id DESC
  LIMIT 5
");
while ($row = sql_fetch_array($r2)) $latestRows[] = $row;

// ====== 1) 실서비스용 (일단은 'use'만 보지 말고, 최근 로그를 그대로 보여줌) ======
$items = [];
$r3 = sql_query("
  SELECT id, mb_id, change_type, memo, ref_key, created_at
  FROM g5_lotto_credit_log
  ORDER BY id DESC
  LIMIT {$limit}
");
while ($row = sql_fetch_array($r3)) {
  $mb_id = (string)($row['mb_id'] ?? '');

  // 회원정보 (없어도 동작)
  $m = sql_fetch("SELECT mb_name, mb_nick, mb_sido FROM {$g5['member_table']} WHERE mb_id='".sql_real_escape_string($mb_id)."' ");
  $name = trim((string)($m['mb_nick'] ?? ''));
  if ($name === '') $name = trim((string)($m['mb_name'] ?? ''));
  if ($name === '') $name = $mb_id !== '' ? $mb_id : '익명';

  $first = mb_substr($name, 0, 1, 'UTF-8');
  if ($first === '') $first = '익';
  $masked = $first . '*';

  $region = trim((string)($m['mb_sido'] ?? ''));

  // 타입은 일단 memo/ref_key 원문을 보여주게 해서 규칙 파악
  $type = (string)($row['change_type'] ?? '');
  $memo = (string)($row['memo'] ?? '');
  $ref  = (string)($row['ref_key'] ?? '');

  $created = strtotime((string)$row['created_at']);
  $diff = time() - ($created ?: time());
  if ($diff < 60) $timeAgo = '방금 전';
  else if ($diff < 3600) $timeAgo = floor($diff / 60) . '분 전';
  else if ($diff < 86400) $timeAgo = floor($diff / 3600) . '시간 전';
  else $timeAgo = floor($diff / 86400) . '일 전';

  $items[] = [
    'avatar' => $masked,
    'name'   => $masked,
    'region' => $region,
    'type'   => $type,           // 임시: change_type 표시
    'time'   => $timeAgo,
    'memo'   => $memo,           // 임시: 원문 노출(진단용)
    'ref_key'=> $ref,            // 임시: 원문 노출(진단용)
    'id'     => (int)$row['id'],
  ];
}

// ====== 2) todayCount도 일단 전체 로그 기준으로 잡아보기(진단용) ======
$today = sql_fetch("
  SELECT COUNT(*) AS cnt
  FROM g5_lotto_credit_log
  WHERE created_at >= CURDATE()
    AND created_at <  DATE_ADD(CURDATE(), INTERVAL 1 DAY)
");
$todayCount = (int)($today['cnt'] ?? 0);

// ====== 3) debug=1 은 슈퍼관리자만 ======
$isSuperAdmin = (isset($is_admin) && $is_admin === 'super');
$debug = (isset($_GET['debug']) && $_GET['debug'] === '1' && $isSuperAdmin);

$out = [
  'todayCount' => $todayCount,
  'items'      => $items,
];

if ($debug) {
  $out['debug'] = [
    'db' => $dbinfo,
    'total_rows' => (int)($total['cnt'] ?? 0),
    'by_type' => $byType,
    'latest_20' => $latestRows,
  ];
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);
exit;

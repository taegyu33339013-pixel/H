<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$limit = max(1, min(12, (int)($_GET['limit'] ?? 4)));

$q = sql_query("
  SELECT id, mb_id, guest_name, age_group, job, region, use_months, rating, content, is_featured, created_at
  FROM g5_lotto_review
  WHERE status='approved'
  ORDER BY is_featured DESC, id DESC
  LIMIT {$limit}
");

function li_mask_name($name) {
  $name = trim((string)$name);
  if ($name === '') return '익명';
  $first = mb_substr($name, 0, 1, 'UTF-8');
  if ($first === '') $first = '익';
  return $first . '*';
}

$items = [];
while ($row = sql_fetch_array($q)) {
  // 표시 이름: 회원이면 mb_id/닉/이름을 쓰고 싶을 수 있으나, 메인은 무조건 마스킹만 노출 추천
  $display = $row['guest_name'] ? $row['guest_name'] : ($row['mb_id'] ?: '익명');
  $masked = li_mask_name($display);

  $age = $row['age_group'] ? str_replace('s','대', $row['age_group']) : '';
  $job = trim((string)$row['job']);
  $metaLeft = trim($masked . ($age || $job ? " (" . trim(($age ? $age : '') . ($age && $job ? ', ' : '') . ($job ? $job : '')) . ")" : ''));

  $metaRight = trim((string)$row['region']);
  $useMonths = (int)($row['use_months'] ?? 0);
  if ($useMonths > 0) $metaRight .= ($metaRight ? ' · ' : '') . $useMonths . '개월 사용';

  $items[] = [
    'id' => (int)$row['id'],
    'avatar' => $masked,
    'name' => $metaLeft,
    'meta' => $metaRight,
    'rating' => (int)$row['rating'],
    'content' => (string)$row['content'],
  ];
}

echo json_encode(['items'=>$items], JSON_UNESCAPED_UNICODE);
exit;

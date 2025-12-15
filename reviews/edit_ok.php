<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
header('Content-Type: text/html; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) alert('잘못된 접근입니다.', G5_URL.'/reviews/manage.php');

$row = sql_fetch("SELECT * FROM g5_lotto_review WHERE id='{$id}' LIMIT 1");
if (!$row) alert('후기를 찾을 수 없습니다.', G5_URL.'/reviews/manage.php');

// CSRF
$token = (string)($_POST['token'] ?? '');
$sess  = (string)get_session('li_review_edit_token_'.$id);
if ($token === '' || $sess === '' || $token !== $sess) alert('잘못된 접근입니다.');
set_session('li_review_edit_token_'.$id, '');

$can_review = false;
if ($is_member) {
  $mbid = sql_real_escape_string($member['mb_id']);
  $paid = sql_fetch("SELECT 1 AS ok
                     FROM g5_lotto_credit_log
                     WHERE mb_id='{$mbid}'
                       AND change_type='charge'
                       AND amount > 0
                     LIMIT 1");
  if (!empty($paid['ok'])) $can_review = true;
}
if (isset($is_admin) && $is_admin === 'super') $can_review = true;

if (!$is_member) {
  alert('로그인이 필요합니다.', G5_BBS_URL.'/login.php?url='.urlencode($_SERVER['REQUEST_URI']));
}
if (!$can_review) {
  alert('크레딧 결제 후 이용 가능합니다.', G5_URL.'/#pricing');
}

// 권한
$can = false;
if ($is_member) {
  if ($row['mb_id'] && $row['mb_id'] === $member['mb_id']) $can = true;
} else {
  if (get_session('li_review_guest_ok_'.$id) === '1') $can = true;
}
if (!$can) alert('권한이 없습니다.', G5_URL.'/reviews/manage.php');

// 삭제
if (!empty($_POST['do_delete'])) {
  sql_query("DELETE FROM g5_lotto_review WHERE id='{$id}'");
  // 비회원 세션 인증 제거
  set_session('li_review_guest_ok_'.$id, '');
  alert('삭제되었습니다.', G5_URL.'/reviews/manage.php');
}

// 수정
$age_group = trim((string)($_POST['age_group'] ?? ''));
$job       = trim((string)($_POST['job'] ?? ''));
$region    = trim((string)($_POST['region'] ?? ''));
$use_months= (int)($_POST['use_months'] ?? 0);
$rating    = (int)($_POST['rating'] ?? 5);
$content   = trim((string)($_POST['content'] ?? ''));

if ($content === '') alert('후기 내용을 입력해 주세요.');
if ($rating < 1 || $rating > 5) $rating = 5;
$content = mb_substr($content, 0, 2000, 'UTF-8');

// ✅ 정책: 승인된 후기 수정 시 재검수로 돌리기 (원치 않으면 아래 줄 제거)
$next_status_sql = "";
if ($row['status'] === 'approved') {
  $next_status_sql = ", status='pending'";
}

$sql = "
  UPDATE g5_lotto_review
  SET
    age_group = ".($age_group!=='' ? "'".sql_real_escape_string($age_group)."'" : "NULL").",
    job = ".($job!=='' ? "'".sql_real_escape_string($job)."'" : "NULL").",
    region = ".($region!=='' ? "'".sql_real_escape_string($region)."'" : "NULL").",
    use_months = ".($use_months>0 ? "'{$use_months}'" : "NULL").",
    rating = '{$rating}',
    content = '".sql_real_escape_string($content)."'
    {$next_status_sql},
    updated_at = NOW()
  WHERE id='{$id}'
";
sql_query($sql);

alert('수정되었습니다.', G5_URL.'/reviews/manage.php');

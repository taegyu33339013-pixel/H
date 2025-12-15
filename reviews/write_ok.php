<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
header('Content-Type: text/html; charset=utf-8');

$token = (string)($_POST['token'] ?? '');
$sess  = (string)get_session('li_review_token');
if ($token === '' || $sess === '' || $token !== $sess) {
  alert('잘못된 접근입니다.');
}
set_session('li_review_token', ''); // 1회용

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

$mb_id = $is_member ? $member['mb_id'] : null;

$guest_name = trim((string)($_POST['guest_name'] ?? ''));
$guest_pw   = (string)($_POST['guest_pw'] ?? '');

if (!$mb_id) {
  if ($guest_name === '' || $guest_pw === '') alert('이름과 비밀번호를 입력해 주세요.');
  $guest_pw_hash = password_hash($guest_pw, PASSWORD_DEFAULT);
} else {
  $guest_pw_hash = null;
}

$age_group = trim((string)($_POST['age_group'] ?? ''));
$job       = trim((string)($_POST['job'] ?? ''));
$region    = trim((string)($_POST['region'] ?? ''));
$use_months= (int)($_POST['use_months'] ?? 0);
$rating    = (int)($_POST['rating'] ?? 5);
$content   = trim((string)($_POST['content'] ?? ''));

if ($content === '') alert('후기 내용을 입력해 주세요.');
if ($rating < 1 || $rating > 5) $rating = 5;

// 간단한 길이 제한/정리
$content = mb_substr($content, 0, 2000, 'UTF-8');

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255, 'UTF-8');

// (선택) 도배 방지: 동일 IP로 5분 내 1개 제한
$recent = sql_fetch("SELECT COUNT(*) cnt FROM g5_lotto_review WHERE ip='".sql_real_escape_string($ip)."' AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
if ((int)$recent['cnt'] > 0) alert('너무 빠르게 작성하셨습니다. 잠시 후 다시 시도해 주세요.');

$sql = "
  INSERT INTO g5_lotto_review
  (mb_id, guest_name, guest_pw_hash, age_group, job, region, use_months, rating, content, status, ip, user_agent, created_at, updated_at)
  VALUES
  (
    " . ($mb_id ? "'".sql_real_escape_string($mb_id)."'" : "NULL") . ",
    " . ($guest_name !== '' ? "'".sql_real_escape_string($guest_name)."'" : "NULL") . ",
    " . ($guest_pw_hash ? "'".sql_real_escape_string($guest_pw_hash)."'" : "NULL") . ",
    " . ($age_group !== '' ? "'".sql_real_escape_string($age_group)."'" : "NULL") . ",
    " . ($job !== '' ? "'".sql_real_escape_string($job)."'" : "NULL") . ",
    " . ($region !== '' ? "'".sql_real_escape_string($region)."'" : "NULL") . ",
    " . ($use_months > 0 ? "'{$use_months}'" : "NULL") . ",
    '{$rating}',
    '".sql_real_escape_string($content)."',
    'pending',
    '".sql_real_escape_string($ip)."',
    '".sql_real_escape_string($ua)."',
    NOW(), NOW()
  )
";
sql_query($sql);

$new_id = function_exists('sql_insert_id') ? (int)sql_insert_id() : 0;
goto_url(G5_URL.'/reviews/thanks.php?id='.$new_id);
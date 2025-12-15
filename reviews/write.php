<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
$g5['title'] = '사용자 후기 작성';
include_once(G5_THEME_PATH.'/head.php');

$token = md5(uniqid('', true));
set_session('li_review_token', $token);

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
?>

<section style="max-width:720px;margin:40px auto;padding:0 16px;">
  <h2 style="margin:0 0 12px;">사용자 후기 작성</h2>
  <p style="opacity:.8;margin:0 0 16px;">후기는 관리자 승인 후 메인 페이지에 노출됩니다.</p>

  <form method="post" action="<?php echo G5_URL; ?>/reviews/write_ok.php">
    <input type="hidden" name="token" value="<?php echo $token; ?>">

    <?php if (!$is_member) { ?>
      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
        <input type="text" name="guest_name" placeholder="이름(표시용)" required style="flex:1;min-width:200px;">
        <input type="password" name="guest_pw" placeholder="비밀번호(수정/삭제용)" required style="flex:1;min-width:200px;">
      </div>
    <?php } ?>

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
      <select name="age_group" style="flex:1;min-width:160px;">
        <option value="">연령대(선택)</option>
        <option value="10s">10대</option><option value="20s">20대</option><option value="30s">30대</option>
        <option value="40s">40대</option><option value="50s">50대</option><option value="60s">60대</option>
        <option value="etc">기타</option>
      </select>
      <input type="text" name="job" placeholder="직업(선택)" style="flex:1;min-width:160px;">
      <input type="text" name="region" placeholder="지역(선택)" style="flex:1;min-width:160px;">
      <input type="number" name="use_months" placeholder="사용기간(개월)" min="0" max="240" style="width:160px;">
    </div>

    <div style="margin-bottom:12px;">
      <label>만족도(1~5):</label>
      <select name="rating">
        <option value="5" selected>5</option><option value="4">4</option><option value="3">3</option>
        <option value="2">2</option><option value="1">1</option>
      </select>
    </div>

    <div style="margin-bottom:12px;">
      <textarea name="content" rows="6" required placeholder="후기 내용을 작성해 주세요(최대 500자 권장)" style="width:100%;"></textarea>
    </div>

    <button type="submit" class="btn_01">후기 제출</button>
  </form>
</section>
<?php include_once(G5_THEME_PATH.'/tail.php'); ?>

<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
$g5['title'] = '후기 수정/삭제';
include_once(G5_THEME_PATH.'/head.php');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) alert('잘못된 접근입니다.', G5_URL.'/reviews/manage.php');

$row = sql_fetch("SELECT * FROM g5_lotto_review WHERE id='{$id}' LIMIT 1");
if (!$row) alert('후기를 찾을 수 없습니다.', G5_URL.'/reviews/manage.php');

// 권한 체크
$can_edit = false;

if ($is_member) {
  if ($row['mb_id'] && $row['mb_id'] === $member['mb_id']) $can_edit = true;
} else {
  // 비회원: pw 파라미터로 들어오면 즉시 검증 후 세션 인증
  $pw = (string)($_GET['pw'] ?? '');
  $sessKey = 'li_review_guest_ok_'.$id;

  if ($pw !== '') {
    if (!empty($row['guest_pw_hash']) && password_verify($pw, $row['guest_pw_hash'])) {
      set_session($sessKey, '1');
    } else {
      alert('비밀번호가 올바르지 않습니다.', G5_URL.'/reviews/manage.php');
    }
  }

  if (get_session($sessKey) === '1') $can_edit = true;
}

if (!$can_edit) {
  alert('수정 권한이 없습니다.', G5_URL.'/reviews/manage.php');
}

// CSRF 토큰
$token = md5(uniqid('', true));
set_session('li_review_edit_token_'.$id, $token);

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

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
?>
<section style="max-width:820px;margin:40px auto;padding:0 16px;">
  <h2 style="margin:0 0 10px;">후기 수정</h2>
  <p style="opacity:.8;margin:0 0 18px;">상태가 승인된 후기라도 수정하면 재검수(pending)로 돌릴지 여부는 관리자 정책에 따라 달라질 수 있습니다.</p>

  <form method="post" action="<?php echo G5_URL; ?>/reviews/edit_ok.php">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
      <select name="age_group" style="flex:1;min-width:160px;">
        <option value="">연령대(선택)</option>
        <?php
        $ages = ['10s'=>'10대','20s'=>'20대','30s'=>'30대','40s'=>'40대','50s'=>'50대','60s'=>'60대','etc'=>'기타'];
        foreach($ages as $k=>$v){
          $sel = ($row['age_group']===$k) ? 'selected' : '';
          echo "<option value='".h($k)."' {$sel}>".h($v)."</option>";
        }
        ?>
      </select>
      <input type="text" name="job" placeholder="직업(선택)" value="<?php echo h($row['job']); ?>" style="flex:1;min-width:160px;">
      <input type="text" name="region" placeholder="지역(선택)" value="<?php echo h($row['region']); ?>" style="flex:1;min-width:160px;">
      <input type="number" name="use_months" placeholder="사용기간(개월)" min="0" max="240" value="<?php echo (int)$row['use_months']; ?>" style="width:160px;">
    </div>

    <div style="margin-bottom:12px;">
      <label>만족도(1~5):</label>
      <select name="rating">
        <?php
        for($r=5;$r>=1;$r--){
          $sel = ((int)$row['rating']===$r) ? 'selected' : '';
          echo "<option value='{$r}' {$sel}>{$r}</option>";
        }
        ?>
      </select>
    </div>

    <div style="margin-bottom:12px;">
      <textarea name="content" rows="7" required style="width:100%;"><?php echo h($row['content']); ?></textarea>
    </div>

    <button type="submit" class="btn_01">수정 저장</button>
    <a class="btn_02" style="margin-left:8px;" href="<?php echo G5_URL; ?>/reviews/manage.php">목록</a>

    <button type="submit" name="do_delete" value="1" class="btn_04"
            style="margin-left:8px;"
            onclick="return confirm('정말 삭제하시겠습니까?');">
      삭제
    </button>
  </form>
</section>

<?php include_once(G5_THEME_PATH.'/tail.php'); ?>

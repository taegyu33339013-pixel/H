<?php
$sub_menu = "300910";
include_once('./_common.php');
auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '후기 상세/수정';
include_once('./admin.head.php');

$id = (int)($_GET['id'] ?? 0);
if ($id<=0) alert('잘못된 접근입니다.');

$row = sql_fetch("SELECT * FROM g5_lotto_review WHERE id='{$id}'");
if (!$row) alert('데이터가 없습니다.');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $status = (string)($_POST['status'] ?? 'pending');
  if (!in_array($status, ['pending','approved','rejected'], true)) $status='pending';

  $is_featured = (int)($_POST['is_featured'] ?? 0) ? 1 : 0;
  $rating = (int)($_POST['rating'] ?? 5);
  if ($rating<1||$rating>5) $rating=5;

  $age_group = trim((string)($_POST['age_group'] ?? ''));
  $job = trim((string)($_POST['job'] ?? ''));
  $region = trim((string)($_POST['region'] ?? ''));
  $use_months = (int)($_POST['use_months'] ?? 0);

  $content = trim((string)($_POST['content'] ?? ''));
  $content = mb_substr($content, 0, 2000, 'UTF-8');

  $admin_memo = trim((string)($_POST['admin_memo'] ?? ''));
  $admin_memo = mb_substr($admin_memo, 0, 255, 'UTF-8');

  sql_query("
    UPDATE g5_lotto_review
    SET
      status='".sql_real_escape_string($status)."',
      is_featured='{$is_featured}',
      rating='{$rating}',
      age_group=".($age_group!=='' ? "'".sql_real_escape_string($age_group)."'" : "NULL").",
      job=".($job!=='' ? "'".sql_real_escape_string($job)."'" : "NULL").",
      region=".($region!=='' ? "'".sql_real_escape_string($region)."'" : "NULL").",
      use_months=".($use_months>0 ? "'{$use_months}'" : "NULL").",
      content='".sql_real_escape_string($content)."',
      admin_memo=".($admin_memo!=='' ? "'".sql_real_escape_string($admin_memo)."'" : "NULL").",
      updated_at=NOW()
    WHERE id='{$id}'
  ");

  alert('저장되었습니다.', './lotto_review_form.php?id='.$id);
}
?>

<div class="local_ov01 local_ov">
  <a href="./lotto_review_list.php" class="btn_02">목록</a>
</div>

<div class="tbl_frm01 tbl_wrap">
<form method="post">
  <table>
    <tbody>
      <tr><th>후기 ID</th><td><?php echo (int)$row['id']; ?></td></tr>
      <tr><th>작성자</th><td><?php echo h($row['mb_id'] ?: $row['guest_name']); ?></td></tr>
      <tr><th>상태</th>
        <td>
          <select name="status">
            <option value="pending"  <?php echo $row['status']==='pending'?'selected':''; ?>>대기</option>
            <option value="approved" <?php echo $row['status']==='approved'?'selected':''; ?>>승인</option>
            <option value="rejected" <?php echo $row['status']==='rejected'?'selected':''; ?>>반려</option>
          </select>
          <label style="margin-left:10px;">
            <input type="checkbox" name="is_featured" value="1" <?php echo ((int)$row['is_featured']===1)?'checked':''; ?>>
            메인 고정
          </label>
        </td>
      </tr>
      <tr><th>평점</th><td>
        <select name="rating">
          <?php for($r=5;$r>=1;$r--){ $sel=((int)$row['rating']===$r)?'selected':''; echo "<option value='{$r}' {$sel}>{$r}</option>"; } ?>
        </select>
      </td></tr>

      <tr><th>메타</th><td style="display:flex;gap:8px;flex-wrap:wrap;">
        <input name="age_group" value="<?php echo h($row['age_group']); ?>" placeholder="30s" style="width:90px;">
        <input name="job" value="<?php echo h($row['job']); ?>" placeholder="직업" style="width:140px;">
        <input name="region" value="<?php echo h($row['region']); ?>" placeholder="지역" style="width:140px;">
        <input name="use_months" value="<?php echo (int)$row['use_months']; ?>" placeholder="개월" style="width:90px;">
      </td></tr>

      <tr><th>내용</th><td><textarea name="content" rows="7" style="width:100%;"><?php echo h($row['content']); ?></textarea></td></tr>
      <tr><th>관리자 메모</th><td><input type="text" name="admin_memo" value="<?php echo h($row['admin_memo']); ?>" style="width:100%;"></td></tr>
      <tr><th>작성일</th><td><?php echo h($row['created_at']); ?></td></tr>
      <tr><th>IP</th><td><?php echo h($row['ip']); ?></td></tr>
    </tbody>
  </table>

  <div class="btn_confirm">
    <button type="submit" class="btn_submit">저장</button>
    <a class="btn_cancel" href="./lotto_review_list.php">취소</a>
  </div>
</form>
</div>

<?php include_once('./admin.tail.php'); ?>

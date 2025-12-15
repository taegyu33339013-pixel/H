<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
$g5['title'] = '후기 관리';
include_once(G5_THEME_PATH.'/head.php');

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
<section style="max-width:820px;margin:40px auto;padding:0 16px;">
  <h2 style="margin:0 0 10px;">후기 관리</h2>

  <?php if ($is_member) { ?>
    <p style="opacity:.8;margin:0 0 16px;">내가 작성한 후기를 수정/삭제할 수 있습니다.</p>

    <?php
    $mb_id = sql_real_escape_string($member['mb_id']);
    $q = sql_query("
      SELECT id, status, is_featured, rating, content, created_at
      FROM g5_lotto_review
      WHERE mb_id='{$mb_id}'
      ORDER BY id DESC
      LIMIT 50
    ");
    ?>

    <div class="tbl_head01 tbl_wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>상태</th>
            <th>평점</th>
            <th>내용</th>
            <th>작성일</th>
            <th>관리</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i=0;
          while($row=sql_fetch_array($q)) {
            $i++;
            ?>
            <tr>
              <td class="td_num_c"><?php echo (int)$row['id']; ?></td>
              <td class="td_num_c"><?php echo htmlspecialchars($row['status']); ?></td>
              <td class="td_num_c"><?php echo (int)$row['rating']; ?></td>
              <td class="td_text_l" style="max-width:420px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?php echo htmlspecialchars($row['content']); ?>
              </td>
              <td class="td_date"><?php echo htmlspecialchars($row['created_at']); ?></td>
              <td class="td_mngsmall">
                <a class="btn_03" href="<?php echo G5_URL; ?>/reviews/edit.php?id=<?php echo (int)$row['id']; ?>">수정</a>
              </td>
            </tr>
            <?php
          }
          if ($i===0) echo '<tr><td colspan="6" class="empty_table">작성한 후기가 없습니다.</td></tr>';
          ?>
        </tbody>
      </table>
    </div>

  <?php } else { ?>
    <p style="opacity:.8;margin:0 0 16px;">비회원은 후기 ID와 작성 시 비밀번호로 수정/삭제할 수 있습니다.</p>

    <form method="get" action="<?php echo G5_URL; ?>/reviews/edit.php" style="display:flex;gap:10px;flex-wrap:wrap;">
      <input type="number" name="id" placeholder="후기 ID" required style="width:180px;">
      <input type="password" name="pw" placeholder="비밀번호" required style="width:220px;">
      <button class="btn_01" type="submit">확인</button>
      <a class="btn_02" href="<?php echo G5_URL; ?>/reviews/write.php">후기 새로 작성</a>
    </form>
  <?php } ?>
</section>
<?php include_once(G5_THEME_PATH.'/tail.php'); ?>

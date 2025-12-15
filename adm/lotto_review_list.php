<?php
$sub_menu = "300910"; // 임의. 메뉴 붙일 때만 의미 있음
include_once('./_common.php');
auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '오늘로또 후기 관리';
include_once('./admin.head.php');

$st = trim((string)($_GET['st'] ?? 'pending')); // pending/approved/rejected/all
$sc = trim((string)($_GET['sc'] ?? ''));        // search
$page = max(1, (int)($_GET['page'] ?? 1));
$rows = 30;
$from = ($page-1)*$rows;

$where = "1=1";
if ($st !== '' && $st !== 'all') $where .= " AND status='".sql_real_escape_string($st)."'";
if ($sc !== '') {
  $esc = sql_real_escape_string($sc);
  $where .= " AND (content LIKE '%{$esc}%' OR mb_id LIKE '%{$esc}%' OR guest_name LIKE '%{$esc}%')";
}

$total = sql_fetch("SELECT COUNT(*) cnt FROM g5_lotto_review WHERE {$where}");
$total_count = (int)$total['cnt'];
$total_page = $total_count > 0 ? ceil($total_count / $rows) : 1;

$list = sql_query("
  SELECT *
  FROM g5_lotto_review
  WHERE {$where}
  ORDER BY is_featured DESC, id DESC
  LIMIT {$from}, {$rows}
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ids = (array)($_POST['ids'] ?? []);
  $act = (string)($_POST['act'] ?? '');
  $ids = array_map('intval', $ids);
  $ids = array_values(array_filter($ids));

  if ($ids && in_array($act, ['approve','reject','delete','feature_on','feature_off'], true)) {
    $in = implode(',', $ids);

    if ($act === 'approve') sql_query("UPDATE g5_lotto_review SET status='approved' WHERE id IN ({$in})");
    if ($act === 'reject')  sql_query("UPDATE g5_lotto_review SET status='rejected' WHERE id IN ({$in})");
    if ($act === 'delete')  sql_query("DELETE FROM g5_lotto_review WHERE id IN ({$in})");
    if ($act === 'feature_on')  sql_query("UPDATE g5_lotto_review SET is_featured=1 WHERE id IN ({$in})");
    if ($act === 'feature_off') sql_query("UPDATE g5_lotto_review SET is_featured=0 WHERE id IN ({$in})");

    goto_url($_SERVER['REQUEST_URI']);
  }
}
?>

<form method="get" style="margin:0 0 12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
  <select name="st">
    <option value="pending" <?php echo $st==='pending'?'selected':''; ?>>대기</option>
    <option value="approved" <?php echo $st==='approved'?'selected':''; ?>>승인</option>
    <option value="rejected" <?php echo $st==='rejected'?'selected':''; ?>>반려</option>
    <option value="all" <?php echo $st==='all'?'selected':''; ?>>전체</option>
  </select>
  <input type="text" name="sc" value="<?php echo htmlspecialchars($sc,ENT_QUOTES); ?>" placeholder="내용/아이디/이름 검색">
  <button class="btn_02">검색</button>
</form>

<form method="post">
  <div style="margin:0 0 10px;display:flex;gap:6px;flex-wrap:wrap;">
    <button name="act" value="approve" class="btn_01">선택 승인</button>
    <button name="act" value="reject" class="btn_02">선택 반려</button>
    <button name="act" value="feature_on" class="btn_03">메인 고정</button>
    <button name="act" value="feature_off" class="btn_02">고정 해제</button>
    <button name="act" value="delete" class="btn_04" onclick="return confirm('삭제하시겠습니까?');">선택 삭제</button>
  </div>

  <div class="tbl_head01 tbl_wrap">
    <table>
      <thead>
        <tr>
          <th><input type="checkbox" onclick="document.querySelectorAll('input[name=&quot;ids[]&quot;]').forEach(x=>x.checked=this.checked)"></th>
          <th>ID</th>
          <th>상태</th>
          <th>고정</th>
          <th>작성자</th>
          <th>메타</th>
          <th>평점</th>
          <th>내용</th>
		  <th>상세</th>
          <th>작성일</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $i=0;
      while($row = sql_fetch_array($list)) {
        $i++;
        $writer = $row['mb_id'] ? $row['mb_id'] : ($row['guest_name'] ?: 'guest');
        $meta = trim(
          ($row['region'] ?: '') .
          (($row['region'] && $row['use_months']) ? ' · ' : '') .
          ($row['use_months'] ? $row['use_months'].'개월' : '')
        );
        ?>
        <tr>
          <td><input type="checkbox" name="ids[]" value="<?php echo (int)$row['id']; ?>"></td>
          <td><?php echo (int)$row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['status']); ?></td>
          <td><?php echo ((int)$row['is_featured']===1) ? 'Y' : '-'; ?></td>
          <td><?php echo htmlspecialchars($writer); ?></td>
          <td><?php echo htmlspecialchars($meta); ?></td>
          <td><?php echo (int)$row['rating']; ?></td>
          <td style="max-width:520px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <?php echo htmlspecialchars($row['content']); ?>
          </td>
		  <td class="td_mngsmall">
		    <a class="btn_03" href="./lotto_review_form.php?id=<?php echo (int)$row['id']; ?>">상세</a>
		  </td>
          <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php
      }
      if ($i===0) echo '<tr><td colspan="10" class="empty_table">데이터가 없습니다.</td></tr>';
      ?>
      </tbody>
    </table>
  </div>
</form>

<?php
echo get_paging(10, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?st='.urlencode($st).'&sc='.urlencode($sc).'&page=');
include_once('./admin.tail.php');

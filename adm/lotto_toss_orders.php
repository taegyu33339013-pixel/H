*** /dev/null
--- b/adm/lotto_toss_orders.php
@@
<?php
// /adm/lotto_toss_orders.php
include_once('./_common.php');

// 서브메뉴 번호는 운영 중인 구조에 맞게 조정하세요.
$sub_menu = "990910";
auth_check($auth[$sub_menu], 'r');

$g5['title'] = '오늘로또 · 토스 결제내역';
include_once('./admin.head.php');

$sfl = $_GET['sfl'] ?? '';
$stx = trim($_GET['stx'] ?? '');
$status = $_GET['status'] ?? '';
$fr_date = $_GET['fr_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$where = " WHERE 1 ";
if ($status) $where .= " AND status='".sql_real_escape_string($status)."' ";
if ($fr_date) $where .= " AND created_at >= '".sql_real_escape_string($fr_date)." 00:00:00' ";
if ($to_date) $where .= " AND created_at <= '".sql_real_escape_string($to_date)." 23:59:59' ";

if ($stx && $sfl) {
  $sfl = preg_replace('/[^a-zA-Z0-9_]/', '', $sfl);
  $where .= " AND {$sfl} LIKE '%".sql_real_escape_string($stx)."%' ";
}

$sql_cnt = "SELECT COUNT(*) AS cnt FROM g5_lotto_toss_orders {$where}";
$row_cnt = sql_fetch($sql_cnt);
$total_count = (int)$row_cnt['cnt'];

$rows = 30;
$page = max(1, (int)($_GET['page'] ?? 1));
$from_record = ($page - 1) * $rows;

$sql = "SELECT * FROM g5_lotto_toss_orders
        {$where}
        ORDER BY id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
  총 <?php echo number_format($total_count); ?>건
</div>

<form class="local_sch01 local_sch" method="get">
  <label class="sound_only" for="status">상태</label>
  <select name="status" id="status">
    <option value="">전체</option>
    <option value="READY" <?php echo get_selected($status,'READY'); ?>>READY</option>
    <option value="DONE"  <?php echo get_selected($status,'DONE'); ?>>DONE</option>
    <option value="FAIL"  <?php echo get_selected($status,'FAIL'); ?>>FAIL</option>
  </select>
  <input type="date" name="fr_date" value="<?php echo htmlspecialchars($fr_date); ?>">
  <input type="date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
  <select name="sfl">
    <option value="order_id" <?php echo get_selected($sfl,'order_id'); ?>>order_id</option>
    <option value="mb_id" <?php echo get_selected($sfl,'mb_id'); ?>>mb_id</option>
    <option value="product_code" <?php echo get_selected($sfl,'product_code'); ?>>product_code</option>
    <option value="payment_key" <?php echo get_selected($sfl,'payment_key'); ?>>payment_key</option>
  </select>
  <input type="text" name="stx" value="<?php echo htmlspecialchars($stx); ?>" placeholder="검색어">
  <input type="submit" value="검색" class="btn_submit">
</form>

<div class="tbl_head01 tbl_wrap">
  <table>
    <thead>
      <tr>
        <th>id</th>
        <th>생성일</th>
        <th>상태</th>
        <th>mb_id</th>
        <th>상품</th>
        <th>금액</th>
        <th>크레딧</th>
        <th>order_id</th>
        <th>승인일</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $i=0;
    while($row = sql_fetch_array($result)) {
      $view_url = './lotto_toss_order_view.php?id='.(int)$row['id'];
    ?>
      <tr>
        <td class="td_num"><?php echo (int)$row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td><?php echo htmlspecialchars($row['mb_id']); ?></td>
        <td><?php echo htmlspecialchars($row['product_code']); ?></td>
        <td class="td_num"><?php echo number_format((int)$row['amount']); ?></td>
        <td class="td_num"><?php echo number_format((int)$row['credit_qty']); ?></td>
        <td><a href="<?php echo $view_url; ?>"><?php echo htmlspecialchars($row['order_id']); ?></a></td>
        <td><?php echo htmlspecialchars($row['approved_at']); ?></td>
      </tr>
    <?php
      $i;
    }
    if($i===0){
      echo '<tr><td colspan="9" class="empty_table">데이터가 없습니다.</td></tr>';
    }
    ?>
    </tbody>
  </table>
</div>

<?php
echo get_paging(G5_IS_MOBILE ? 5 : 10, $page, ceil($total_count/$rows), '?'.http_build_query(array_merge($_GET, ['page'=>''])));
include_once('./admin.tail.php');

*** /dev/null
--- b/adm/lotto_toss_order_view.php
@@
<?php
// /adm/lotto_toss_order_view.php
include_once('./_common.php');
$sub_menu = "990910";
auth_check($auth[$sub_menu], 'r');

$id = (int)($_GET['id'] ?? 0);
$row = sql_fetch("SELECT * FROM g5_lotto_toss_orders WHERE id={$id}");
if(!$row) alert('데이터가 없습니다.', './lotto_toss_orders.php');

$g5['title'] = '토스 결제 상세';
include_once('./admin.head.php');
?>

<div class="tbl_head01 tbl_wrap">
  <table>
    <tbody>
      <tr><th>id</th><td><?php echo (int)$row['id']; ?></td></tr>
      <tr><th>status</th><td><?php echo htmlspecialchars($row['status']); ?></td></tr>
      <tr><th>mb_id</th><td><?php echo htmlspecialchars($row['mb_id']); ?></td></tr>
      <tr><th>product_code</th><td><?php echo htmlspecialchars($row['product_code']); ?></td></tr>
      <tr><th>amount</th><td><?php echo number_format((int)$row['amount']); ?></td></tr>
      <tr><th>credit_qty</th><td><?php echo number_format((int)$row['credit_qty']); ?></td></tr>
      <tr><th>order_id</th><td><?php echo htmlspecialchars($row['order_id']); ?></td></tr>
      <tr><th>payment_key</th><td><?php echo htmlspecialchars($row['payment_key']); ?></td></tr>
      <tr><th>approved_at</th><td><?php echo htmlspecialchars($row['approved_at']); ?></td></tr>
      <tr><th>fail_code</th><td><?php echo htmlspecialchars($row['fail_code']); ?></td></tr>
      <tr><th>fail_message</th><td><?php echo htmlspecialchars($row['fail_message']); ?></td></tr>
      <tr><th>created_at</th><td><?php echo htmlspecialchars($row['created_at']); ?></td></tr>
    </tbody>
  </table>
</div>

<h2 style="margin:20px 0 10px;">raw_json</h2>
<textarea style="width:100%;height:320px;"><?php echo htmlspecialchars($row['raw_json']); ?></textarea>

<div class="btn_fixed_top">
  <a href="./lotto_toss_orders.php" class="btn btn_02">목록</a>
</div>

<?php include_once('./admin.tail.php'); ?>

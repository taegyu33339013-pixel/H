<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
$g5['title'] = '후기 접수 완료';
include_once(G5_THEME_PATH.'/head.php');

$id = (int)($_GET['id'] ?? 0);
?>
<section style="max-width:720px;margin:40px auto;padding:0 16px;">
  <h2 style="margin:0 0 10px;">후기가 접수되었습니다 ✅</h2>
  <p style="opacity:.8;margin:0 0 16px;">관리자 승인 후 메인 페이지에 노출됩니다.</p>

  <?php if ($id > 0) { ?>
    <div style="padding:12px;border:1px solid #ddd;border-radius:10px;margin-bottom:16px;">
      <div style="font-weight:700;margin-bottom:6px;">내 후기 ID</div>
      <div style="font-size:22px;"><?php echo number_format($id); ?></div>
      <div style="opacity:.75;margin-top:8px;">비회원은 이 ID와 작성 시 비밀번호로 수정/삭제할 수 있습니다.</div>
    </div>
    <a class="btn_01" href="<?php echo G5_URL; ?>/reviews/manage.php">후기 관리하기</a>
  <?php } ?>

  <a class="btn_02" style="margin-left:8px;" href="<?php echo G5_URL; ?>/">메인으로</a>
</section>
<?php include_once(G5_THEME_PATH.'/tail.php'); ?>

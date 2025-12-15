<?php
// /adm/lotto_draw_form_update.php
$sub_menu = "300900";
include_once('./_common.php');
auth_check_menu($auth, $sub_menu, 'w');

include_once(G5_PATH . '/lib/lotto_draw.lib.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert('잘못된 접근입니다.');
}

$draw_no = (int)($_POST['draw_no'] ?? 0);
$draw_date = trim($_POST['draw_date'] ?? '');

if ($draw_no <= 0 || $draw_date === '') {
    alert('회차와 추첨일은 필수입니다.');
}

$n = [];
for ($i = 1; $i <= 6; $i++) {
    $v = (int)($_POST["n{$i}"] ?? 0);
    if ($v < 1 || $v > 45) {
        alert('당첨번호는 1~45 범위여야 합니다.');
    }
    $n[] = $v;
}

if (count(array_unique($n)) < 6) {
    alert('당첨번호 6개는 서로 달라야 합니다.');
}

$bonus = (int)($_POST['bonus'] ?? 0);
if ($bonus < 1 || $bonus > 45) {
    alert('보너스 번호는 1~45 범위여야 합니다.');
}
if (in_array($bonus, $n)) {
    alert('보너스 번호는 당첨번호 6개와 달라야 합니다.');
}

// 선택 항목
$total_sales       = (int)($_POST['total_sales']       ?? 0);
$first_prize_total = (int)($_POST['first_prize_total'] ?? 0);
$first_winners     = (int)($_POST['first_winners']     ?? 0);
$first_prize_each  = (int)($_POST['first_prize_each']  ?? 0);

$data = [
    'draw_no'           => $draw_no,
    'draw_date'         => $draw_date,
    'n1'                => $n[0],
    'n2'                => $n[1],
    'n3'                => $n[2],
    'n4'                => $n[3],
    'n5'                => $n[4],
    'n6'                => $n[5],
    'bonus'             => $bonus,
    'total_sales'       => $total_sales,
    'first_prize_total' => $first_prize_total,
    'first_winners'     => $first_winners,
    'first_prize_each'  => $first_prize_each,
];

if (!li_save_lotto_draw($data)) {
    alert('저장 중 오류가 발생했습니다.');
}

alert('저장되었습니다.', './lotto_draw_form.php?draw_no=' . $draw_no);

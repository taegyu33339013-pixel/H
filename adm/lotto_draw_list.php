<?php
// /adm/lotto_draw_list.php
$sub_menu = "300900";
include_once('./_common.php');
auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '로또 당첨번호 목록';
include_once('./admin.head.php');

// 페이징
$page = max(1, (int)($_GET['page'] ?? 1));
$rows = 30;
$from_record = ($page - 1) * $rows;

$total = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_draw")['cnt'];
$total_page = $total > 0 ? ceil($total / $rows) : 1;

$result = sql_query("SELECT * FROM g5_lotto_draw ORDER BY draw_no DESC LIMIT {$from_record}, {$rows}");
?>

<div class="local_ov01 local_ov">
    <a href="./lotto_draw_form.php" class="btn_01">새 회차 등록</a>
    <a href="./lotto_draw_sync.php" class="btn_02">동행복권 최신 회차 동기화</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>로또 당첨번호 목록</caption>
        <thead>
        <tr>
            <th scope="col">회차</th>
            <th scope="col">추첨일</th>
            <th scope="col">번호</th>
            <th scope="col">보너스</th>
            <th scope="col">1등 인원</th>
            <th scope="col">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
            $nums = "{$row['n1']}, {$row['n2']}, {$row['n3']}, {$row['n4']}, {$row['n5']}, {$row['n6']}";
            ?>
            <tr>
                <td class="td_num_c"><?php echo number_format($row['draw_no']); ?></td>
                <td class="td_date"><?php echo $row['draw_date']; ?></td>
                <td class="td_text_l"><?php echo $nums; ?></td>
                <td class="td_num_c"><?php echo $row['bonus']; ?></td>
                <td class="td_num_c"><?php echo $row['first_winners'] ?: '-'; ?></td>
                <td class="td_mngsmall">
                    <a href="./lotto_draw_form.php?draw_no=<?php echo $row['draw_no']; ?>" class="btn_03">수정</a>
                </td>
            </tr>
            <?php
        }
        if ($i == 0) {
            echo '<tr><td colspan="6" class="empty_table">등록된 회차가 없습니다.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<?php
echo get_paging(10, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?page=');
include_once('./admin.tail.php');

<?php
// /adm/lotto_draw_form.php
$sub_menu = "900900"; // 아무 안 쓰는 코드 하나 잡고, 관리자 메뉴에 등록하면 됩니다.
include_once('./_common.php');
auth_check_menu($auth, $sub_menu, 'w');

include_once(G5_PATH . '/lib/lotto_draw.lib.php');

$g5['title'] = '로또 당첨번호 관리';
include_once('./admin.head.php');

// 수정 모드용: GET draw_no 있으면 해당 회차 불러오기
$draw_no = isset($_GET['draw_no']) ? (int)$_GET['draw_no'] : 0;
$row = null;

if ($draw_no > 0) {
    $row = sql_fetch("SELECT * FROM g5_lotto_draw WHERE draw_no = '{$draw_no}'");
}

?>
<div class="local_ov01 local_ov">
    <span class="btn_ov01">
        <a href="./lotto_draw_list.php">목록으로</a>
    </span>
</div>

<form name="flotto" id="flotto" method="post" action="./lotto_draw_form_update.php" onsubmit="return flottdo_submit(this);">
    <input type="hidden" name="draw_no_old" value="<?php echo $row['draw_no'] ?? ''; ?>">

    <div class="tbl_frm01 tbl_wrap">
        <table>
            <caption>로또 당첨번호 입력</caption>
            <colgroup>
                <col class="grid_4">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th scope="row"><label for="draw_no">회차</label></th>
                <td>
                    <input type="number" name="draw_no" id="draw_no" class="frm_input" required
                           value="<?php echo $row['draw_no'] ?? ''; ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="draw_date">추첨일</label></th>
                <td>
                    <input type="date" name="draw_date" id="draw_date" class="frm_input" required
                           value="<?php echo isset($row['draw_date']) ? $row['draw_date'] : ''; ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">당첨번호 6개</th>
                <td>
                    <?php
                    for ($i = 1; $i <= 6; $i++) {
                        $k = "n{$i}";
                        $val = isset($row[$k]) ? $row[$k] : '';
                        echo "<input type=\"number\" name=\"n{$i}\" class=\"frm_input\" required min=\"1\" max=\"45\" style=\"width:60px; margin-right:5px;\" value=\"{$val}\">";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bonus">보너스 번호</label></th>
                <td>
                    <input type="number" name="bonus" id="bonus" class="frm_input" required min="1" max="45"
                           value="<?php echo $row['bonus'] ?? ''; ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="total_sales">총 판매금액 (선택)</label></th>
                <td>
                    <input type="number" name="total_sales" id="total_sales" class="frm_input"
                           value="<?php echo $row['total_sales'] ?? ''; ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="first_prize_total">1등 총 당첨금 (선택)</label></th>
                <td>
                    <input type="number" name="first_prize_total" id="first_prize_total" class="frm_input"
                           value="<?php echo $row['first_prize_total'] ?? ''; ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="first_winners">1등 당첨자 수 (선택)</label></th>
                <td>
                    <input type="number" name="first_winners" id="first_winners" class="frm_input"
                           value="<?php echo $row['first_winners'] ?? ''; ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="first_prize_each">1등 1인당 당첨금 (선택)</label></th>
                <td>
                    <input type="number" name="first_prize_each" id="first_prize_each" class="frm_input"
                           value="<?php echo $row['first_prize_each'] ?? ''; ?>">
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="저장" class="btn_submit">
    </div>
</form>

<script>
function flottdo_submit(f) {
    // 번호 중복/범위 체크 간단 버전 (프론트)
    let nums = [];
    for (let i = 1; i <= 6; i++) {
        let v = parseInt(f['n' + i].value, 10);
        if (isNaN(v) || v < 1 || v > 45) {
            alert('당첨번호는 1~45 사이 정수여야 합니다.');
            f['n' + i].focus();
            return false;
        }
        if (nums.indexOf(v) !== -1) {
            alert('당첨번호 6개는 서로 달라야 합니다.');
            f['n' + i].focus();
            return false;
        }
        nums.push(v);
    }
    let b = parseInt(f.bonus.value, 10);
    if (isNaN(b) || b < 1 || b > 45) {
        alert('보너스 번호는 1~45 사이 정수여야 합니다.');
        f.bonus.focus();
        return false;
    }
    if (nums.indexOf(b) !== -1) {
        alert('보너스 번호는 당첨번호 6개와 달라야 합니다.');
        f.bonus.focus();
        return false;
    }
    return true;
}
</script>

<?php
include_once('./admin.tail.php');

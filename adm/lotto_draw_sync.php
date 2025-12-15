<?php
// /adm/lotto_draw_sync.php

$sub_menu = "300900"; // lotto_draw_list.php / lotto_draw_form.php와 동일 코드 사용
include_once('./_common.php');
auth_check_menu($auth, $sub_menu, 'w');

include_once(G5_PATH . '/lib/lotto_draw.lib.php');

$g5['title'] = '로또 당첨번호 동행복권 동기화';
include_once('./admin.head.php');

// ─────────────────────────────────────
// 1) 현재 DB에 저장된 최대 회차 조회
// ─────────────────────────────────────
$row = sql_fetch("SELECT MAX(draw_no) AS max_draw_no FROM g5_lotto_draw");
$max_draw_no = (int)($row['max_draw_no'] ?? 0);

if ($max_draw_no <= 0) {
    $next_draw_no = 1;
    $msg_header = "현재 저장된 회차가 없습니다. 1회차부터 동기화합니다.";
} else {
    $next_draw_no = $max_draw_no + 1;
    $msg_header = "현재 저장된 최대 회차는 <strong>{$max_draw_no}회</strong> 입니다. 다음 회차부터 동기화합니다.";
}

// 한 번에 시도할 최대 회차 수 (cron 없으니 10회면 충분)
$max_gap = 50;
$inserted = 0;
$logs = [];

for ($drw = $next_draw_no; $drw < $next_draw_no + $max_gap; $drw++) {

    // DB에 이미 있을 수도 있으니, 한 번 더 체크 (혹시 중복 요청 등을 대비)
    $exists = sql_fetch("SELECT 1 AS ok FROM g5_lotto_draw WHERE draw_no = '{$drw}'");
    if ($exists && $exists['ok']) {
        $logs[] = "회차 {$drw} : 이미 DB에 존재합니다. 건너뜁니다.";
        continue;
    }

    $err = '';
    $ok  = li_fetch_and_save_lotto_draw($drw, $err);

    if (!$ok) {
        // 보통 여기서 "아직 발표 안 된 회차"를 만남
        $logs[] = "회차 {$drw} : " . ($err ?: '동행복권에 데이터가 없거나 API 실패. 여기서 중단합니다.');
        break;
    }

    $logs[] = "회차 {$drw} : 동행복권 데이터 가져와 저장 완료.";
    $inserted++;

    // API/서버 부담 줄이기
    usleep(200000); // 0.2초
}

?>
<div class="local_ov01 local_ov">
    <a href="./lotto_draw_list.php" class="btn_02">목록으로</a>
    <a href="./lotto_draw_sync.php" class="btn_01">다시 동기화 실행</a>
</div>

<div class="tbl_frm01 tbl_wrap">
    <table>
        <caption>로또 당첨번호 동기화 결과</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">동기화 기준</th>
            <td><?php echo $msg_header; ?></td>
        </tr>
        <tr>
            <th scope="row">실행 결과</th>
            <td>
                <?php if ($inserted === 0) { ?>
                    새로 저장된 회차가 없습니다. (이미 최신이거나, 아직 발표 전 회차입니다.)
                <?php } else { ?>
                    총 <strong><?php echo $inserted; ?>개</strong> 회차가 새로 저장되었습니다.
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row">상세 로그</th>
            <td>
                <div style="max-height:300px; overflow:auto; background:#f9f9f9; padding:10px; border:1px solid #ddd; font-family:monospace; font-size:12px;">
                    <?php
                    if (!empty($logs)) {
                        echo nl2br(htmlspecialchars(implode("\n", $logs), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
                    } else {
                        echo '로그가 없습니다.';
                    }
                    ?>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<?php
include_once('./admin.tail.php');

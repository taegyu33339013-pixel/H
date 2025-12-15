<?php
// /lib/lotto_draw.lib.php
if (!defined('_GNUBOARD_')) exit;

libxml_use_internal_errors(true);

/**
 * 동행복권 API(getLottoNumber)에서 특정 회차 로또 정보를 JSON으로 가져오기
 * 성공 시 배열 리턴, 실패 시 null 리턴 + $error에 사유 기록
 */
function li_get_lotto_api_json($drwNo, &$error = '')
{
    $error = '';
    $drwNo = (int)$drwNo;
    if ($drwNo <= 0) {
        $error = '잘못된 회차 번호입니다.';
        return null;
    }

    $url = 'https://www.dhlottery.co.kr/common.do?method=getLottoNumber&drwNo=' . $drwNo;

    $raw = '';

    // 1) cURL 우선 사용
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // Cafe24 환경에서 가끔 SSL 이슈 -> 기존 코드 스타일 유지
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (LottoInsight Bot)',
        ]);
        $raw = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = 'cURL 오류: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            $error = "HTTP 상태코드 {$http_code}";
            return null;
        }
    }
    // 2) cURL 없으면 file_get_contents 시도
    else if (ini_get('allow_url_fopen')) {
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => "User-Agent: LottoInsight Bot\r\n",
                'timeout' => 10,
            ]
        ]);
        $raw = @file_get_contents($url, false, $context);
        if ($raw === false) {
            $error = 'file_get_contents()로 API 호출 실패';
            return null;
        }
    } else {
        $error = 'cURL과 allow_url_fopen이 모두 비활성화되어 있습니다.';
        return null;
    }

    if ($raw === '' || $raw === false) {
        $error = 'API 응답이 비어 있습니다.';
        return null;
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $error = 'JSON 디코드 실패: ' . mb_substr($raw, 0, 200, 'UTF-8');
        return null;
    }

    if (!isset($data['returnValue']) || $data['returnValue'] !== 'success') {
        // 발표 전 회차이거나 잘못된 회차일 때 보통 여기로 들어옴
        $error = 'API returnValue = ' . ($data['returnValue'] ?? '없음');
        return null;
    }

    return $data;
}

/**
 * 동행복권 회차별 당첨결과 페이지(byWin) HTML 가져오기
 * 여기서 2/3등 당첨자수/1인당 당첨금 파싱
 */
function li_get_lotto_bywin_html($drwNo, &$error = '')
{
    $error = '';
    $drwNo = (int)$drwNo;
    if ($drwNo <= 0) {
        $error = '잘못된 회차 번호입니다.';
        return null;
    }

    $url = 'https://www.dhlottery.co.kr/gameResult.do?method=byWin&drwNo=' . $drwNo;

    $raw = '';
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (LottoInsight Bot)',
        ]);
        $raw = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = 'cURL 오류: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            $error = "HTTP 상태코드 {$http_code}";
            return null;
        }
    } else if (ini_get('allow_url_fopen')) {
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => "User-Agent: LottoInsight Bot\r\n",
                'timeout' => 15,
            ]
        ]);
        $raw = @file_get_contents($url, false, $context);
        if ($raw === false) {
            $error = 'file_get_contents()로 byWin 호출 실패';
            return null;
        }
    } else {
        $error = 'cURL과 allow_url_fopen이 모두 비활성화되어 있습니다.';
        return null;
    }

    if ($raw === '' || $raw === false) {
        $error = 'byWin 응답이 비어 있습니다.';
        return null;
    }

    return $raw;
}

function li_to_int($s)
{
    $n = preg_replace('/[^0-9]/', '', (string)$s);
    return (int)($n === '' ? 0 : $n);
}

/**
 * byWin HTML에서 2/3등 당첨자 수 / 1인당 당첨금 파싱
 *
 * 기대하는 표 구조(대체로):
 *  [등위][총당첨금][당첨게임수(=당첨자수)][1게임당 당첨금]
 *
 * 페이지 구조가 바뀌면 DOM 파싱이 실패할 수 있어 fallback 정규식도 포함.
 */
function li_parse_prize_23_from_bywin($html, &$error = '')
{
    $error = '';
    $out = [
        'second_winners' => 0,
        'second_prize_each' => 0,
        'third_winners' => 0,
        'third_prize_each' => 0,
    ];

    // 1) DOM 파싱 시도
    $dom = new DOMDocument();
    $loaded = @$dom->loadHTML($html);
    if ($loaded) {
        $xpath = new DOMXPath($dom);
        $trs = $xpath->query('//tr');

        foreach ($trs as $tr) {
            $tds = $tr->getElementsByTagName('td');
            if ($tds->length < 3) continue;

            $cells = [];
            foreach ($tds as $td) {
                $cells[] = trim(preg_replace('/\s+/', ' ', $td->textContent));
            }

            $rank = $cells[0] ?? '';
            if ($rank === '') continue;

            // 일반적으로: 0=등위, 1=총액, 2=당첨자수, 3=1인당
            if (mb_strpos($rank, '2등') !== false) {
                $w = li_to_int($cells[2] ?? '');
                $e = li_to_int($cells[3] ?? '');

                // 구조가 다르면 "마지막 원(원)" 칸을 1인당으로 보는 보정
                if ($w <= 0 && isset($cells[1])) $w = li_to_int($cells[1]);
                if ($e <= 0) {
                    // 마지막 칸이 1인당인 경우가 많음
                    $last = end($cells);
                    $e = li_to_int($last);
                }

                if ($w > 0) $out['second_winners'] = $w;
                if ($e > 0) $out['second_prize_each'] = $e;
            }

            if (mb_strpos($rank, '3등') !== false) {
                $w = li_to_int($cells[2] ?? '');
                $e = li_to_int($cells[3] ?? '');

                if ($w <= 0 && isset($cells[1])) $w = li_to_int($cells[1]);
                if ($e <= 0) {
                    $last = end($cells);
                    $e = li_to_int($last);
                }

                if ($w > 0) $out['third_winners'] = $w;
                if ($e > 0) $out['third_prize_each'] = $e;
            }
        }
    }

    // 2) fallback: 텍스트 정규식
    // DOM이 실패하거나 일부 값이 0이면 텍스트 기반으로 보강
    if ($out['second_winners'] <= 0 || $out['second_prize_each'] <= 0 ||
        $out['third_winners'] <= 0  || $out['third_prize_each'] <= 0) {

        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);

        // 패턴: "2등 ... 84 ... 53,326,506원"
        if ($out['second_winners'] <= 0 || $out['second_prize_each'] <= 0) {
            if (preg_match('/2등[^0-9]*[0-9,]+원[^0-9]*([0-9,]+)[^0-9]*([0-9,]+)원/u', $text, $m)) {
                $out['second_winners'] = li_to_int($m[1]);
                $out['second_prize_each'] = li_to_int($m[2]);
            }
        }

        if ($out['third_winners'] <= 0 || $out['third_prize_each'] <= 0) {
            if (preg_match('/3등[^0-9]*[0-9,]+원[^0-9]*([0-9,]+)[^0-9]*([0-9,]+)원/u', $text, $m)) {
                $out['third_winners'] = li_to_int($m[1]);
                $out['third_prize_each'] = li_to_int($m[2]);
            }
        }
    }

    if ($out['second_winners'] <= 0 || $out['second_prize_each'] <= 0 ||
        $out['third_winners'] <= 0  || $out['third_prize_each'] <= 0) {
        $error = '2/3등 파싱 실패(페이지 구조 변경 가능).';
        return null;
    }

    return $out;
}

/**
 * API에서 받아온 배열(+ byWin 파싱 결과)을 g5_lotto_draw 테이블에 저장
 */
function li_save_lotto_draw(array $data, &$error = '')
{
    global $g5;
    $error = '';

    // 필수 값 매핑
    $draw_no   = (int)($data['drwNo'] ?? 0);
    $draw_date = trim((string)($data['drwNoDate'] ?? ''));

    if (!$draw_no || !$draw_date) {
        $error = '회차 또는 추첨일 정보가 없습니다.';
        return false;
    }

    // 날짜 포맷 정리 (YYYY-MM-DD만 남기기)
    $draw_date = preg_replace('/[^0-9\-]/', '', $draw_date);

    $n1    = (int)($data['drwtNo1'] ?? 0);
    $n2    = (int)($data['drwtNo2'] ?? 0);
    $n3    = (int)($data['drwtNo3'] ?? 0);
    $n4    = (int)($data['drwtNo4'] ?? 0);
    $n5    = (int)($data['drwtNo5'] ?? 0);
    $n6    = (int)($data['drwtNo6'] ?? 0);
    $bonus = (int)($data['bnusNo']  ?? 0);

    $total_sales       = isset($data['totSellamnt'])    ? (int)$data['totSellamnt']    : null;
    $first_prize_total = isset($data['firstAccumamnt']) ? (int)$data['firstAccumamnt'] : null;
    $first_winners     = isset($data['firstPrzwnerCo']) ? (int)$data['firstPrzwnerCo'] : null;
    $first_prize_each  = isset($data['firstWinamnt'])   ? (int)$data['firstWinamnt']   : null;

    // byWin 파싱 결과(없어도 됨: null로 저장)
    $second_winners    = isset($data['second_winners']) ? (int)$data['second_winners'] : null;
    $second_prize_each = isset($data['second_prize_each']) ? (int)$data['second_prize_each'] : null;
    $third_winners     = isset($data['third_winners']) ? (int)$data['third_winners'] : null;
    $third_prize_each  = isset($data['third_prize_each']) ? (int)$data['third_prize_each'] : null;

    $table = 'g5_lotto_draw';

    // INSERT ... ON DUPLICATE KEY UPDATE
    $sql = "
        INSERT INTO {$table}
        (draw_no, draw_date,
         n1, n2, n3, n4, n5, n6, bonus,
         total_sales, first_prize_total, first_winners, first_prize_each,
         second_winners, second_prize_each, third_winners, third_prize_each,
         created_at, updated_at)
        VALUES (
            '{$draw_no}', '{$draw_date}',
            '{$n1}', '{$n2}', '{$n3}', '{$n4}', '{$n5}', '{$n6}', '{$bonus}',
            " . ($total_sales       === null ? "NULL" : "'{$total_sales}'") . ",
            " . ($first_prize_total === null ? "NULL" : "'{$first_prize_total}'") . ",
            " . ($first_winners     === null ? "NULL" : "'{$first_winners}'") . ",
            " . ($first_prize_each  === null ? "NULL" : "'{$first_prize_each}'") . ",
            " . ($second_winners    === null ? "NULL" : "'{$second_winners}'") . ",
            " . ($second_prize_each === null ? "NULL" : "'{$second_prize_each}'") . ",
            " . ($third_winners     === null ? "NULL" : "'{$third_winners}'") . ",
            " . ($third_prize_each  === null ? "NULL" : "'{$third_prize_each}'") . ",
            NOW(), NOW()
        )
        ON DUPLICATE KEY UPDATE
            draw_date         = VALUES(draw_date),
            n1                = VALUES(n1),
            n2                = VALUES(n2),
            n3                = VALUES(n3),
            n4                = VALUES(n4),
            n5                = VALUES(n5),
            n6                = VALUES(n6),
            bonus             = VALUES(bonus),
            total_sales       = VALUES(total_sales),
            first_prize_total = VALUES(first_prize_total),
            first_winners     = VALUES(first_winners),
            first_prize_each  = VALUES(first_prize_each),
            second_winners    = VALUES(second_winners),
            second_prize_each = VALUES(second_prize_each),
            third_winners     = VALUES(third_winners),
            third_prize_each  = VALUES(third_prize_each),
            updated_at        = NOW()
    ";

    $res = sql_query($sql, false);
    if (!$res) {
        $error = 'DB 저장 실패: ' . (function_exists('sql_error') ? sql_error() : 'sql_query 실패');
        return false;
    }

    return true;
}

/**
 * 회차 하나를 JSON(getLottoNumber) + HTML(byWin)에서 받아와서 DB에 저장하는 통합 함수
 * 성공 시 true, 실패 시 false + $error에 사유
 */
function li_fetch_and_save_lotto_draw($drwNo, &$error = '')
{
    $error = '';

    // 1) JSON: 번호/1등/판매액
    $data = li_get_lotto_api_json($drwNo, $error);
    if (!$data) {
        return false;
    }

    // 2) HTML: 2/3등(당첨자수/1인당)
    $err2 = '';
    $html = li_get_lotto_bywin_html($drwNo, $err2);
    if ($html) {
        $err3 = '';
        $p = li_parse_prize_23_from_bywin($html, $err3);
        if ($p) {
            $data['second_winners'] = $p['second_winners'];
            $data['second_prize_each'] = $p['second_prize_each'];
            $data['third_winners'] = $p['third_winners'];
            $data['third_prize_each'] = $p['third_prize_each'];
        }
        // 파싱 실패해도 1등 데이터는 저장 진행(원하시면 여기서 return false로 바꿔도 됨)
    }
    // byWin 호출 실패해도 1등 데이터는 저장 진행

    return li_save_lotto_draw($data, $error);
}

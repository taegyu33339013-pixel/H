<?php
/**
 * 로또 판매점/당첨점 데이터 라이브러리
 * 동행복권 실제 데이터 수집
 * 
 * 동행복권 API 엔드포인트:
 * - 판매점 검색: https://www.dhlottery.co.kr/store.do?method=sellerInfo645Result
 * - 당첨점 조회: https://www.dhlottery.co.kr/store.do?method=topStore
 */

if (!defined('_GNUBOARD_')) exit;

/**
 * 동행복권 시/도 코드 매핑
 */
function li_get_sido_codes() {
    return [
        '서울' => '11',
        '부산' => '26',
        '대구' => '27',
        '인천' => '28',
        '광주' => '29',
        '대전' => '30',
        '울산' => '31',
        '세종' => '36',
        '경기' => '41',
        '강원' => '42',
        '충북' => '43',
        '충남' => '44',
        '전북' => '45',
        '전남' => '46',
        '경북' => '47',
        '경남' => '48',
        '제주' => '50',
    ];
}

/**
 * HTTP 요청 유틸리티 (cURL)
 */
function li_http_request($url, $post_data = null, $is_json = false) {
    $ch = curl_init();
    
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
    ];
    
    if ($is_json) {
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
    } elseif ($post_data) {
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
    }
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_ENCODING => 'gzip, deflate',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_REFERER => 'https://www.dhlottery.co.kr/',
        CURLOPT_COOKIEJAR => '/tmp/dhlottery_cookie.txt',
        CURLOPT_COOKIEFILE => '/tmp/dhlottery_cookie.txt',
    ]);
    
    if ($post_data) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($post_data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("[lotto_store] cURL error: {$error}");
        return false;
    }
    
    if ($http_code != 200) {
        error_log("[lotto_store] HTTP error: {$http_code}");
        return false;
    }
    
    // EUC-KR → UTF-8 변환
    if (strpos($response, 'charset=euc-kr') !== false || 
        strpos($response, 'charset="euc-kr"') !== false ||
        strpos($response, 'EUC-KR') !== false) {
        $response = @iconv('EUC-KR', 'UTF-8//IGNORE', $response);
    }
    
    return $response;
}

/**
 * 동행복권 판매점 전체 목록 수집 (지역별)
 * 
 * @param string $sido 시/도명 (예: '서울', '경기')
 * @param string $gugun 시/군/구명 (선택)
 * @return array 판매점 목록
 */
function li_fetch_all_stores_by_region($sido, $gugun = '') {
    $stores = [];
    $sido_codes = li_get_sido_codes();
    
    if (!isset($sido_codes[$sido])) {
        error_log("[lotto_store] Unknown sido: {$sido}");
        return $stores;
    }
    
    $sido_code = $sido_codes[$sido];
    $page = 1;
    $max_pages = 100; // 안전장치
    
    while ($page <= $max_pages) {
        $post_data = [
            'searchType' => 'SIDO',
            'SIDO' => $sido_code,
            'SIGUGUN' => $gugun,
            'DONG' => '',
            'BESSION' => '',
            'nowPage' => $page,
            'sltSIDO2' => $sido_code,
            'sltGUGUN2' => $gugun,
        ];
        
        $url = 'https://www.dhlottery.co.kr/store.do?method=sellerInfo645Result';
        $html = li_http_request($url, $post_data);
        
        if (!$html) {
            break;
        }
        
        $page_stores = li_parse_store_list($html, $sido);
        
        if (empty($page_stores)) {
            break;
        }
        
        $stores = array_merge($stores, $page_stores);
        
        // 다음 페이지 존재 여부 확인
        if (strpos($html, 'nowPage=' . ($page + 1)) === false && 
            count($page_stores) < 10) {
            break;
        }
        
        $page++;
        
        // 서버 부하 방지
        usleep(300000); // 0.3초 대기
    }
    
    return $stores;
}

/**
 * 판매점 목록 HTML 파싱
 */
function li_parse_store_list($html, $sido) {
    $stores = [];
    
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    // 판매점 테이블 행 찾기
    $rows = $xpath->query("//table[contains(@class, 'tbl_data')]//tbody//tr");
    
    if ($rows->length == 0) {
        // 대체 선택자
        $rows = $xpath->query("//div[@class='result_store']//table//tr[td]");
    }
    
    foreach ($rows as $row) {
        $cells = $xpath->query(".//td", $row);
        
        if ($cells->length >= 3) {
            $store = [];
            
            // 상호명
            $store['store_name'] = trim($cells->item(0)->textContent ?? '');
            
            // 주소
            $store['address'] = trim($cells->item(2)->textContent ?? '');
            
            // 전화번호 (있으면)
            if ($cells->length >= 4) {
                $store['phone'] = trim($cells->item(3)->textContent ?? '');
            }
            
            if (empty($store['store_name']) || empty($store['address'])) {
                continue;
            }
            
            // 지역 추출
            $region = li_extract_region_from_address($store['address']);
            $store['region1'] = $region['region1'] ?: $sido;
            $store['region2'] = $region['region2'];
            $store['region3'] = $region['region3'] ?? '';
            
            $stores[] = $store;
        }
    }
    
    return $stores;
}

/**
 * 주소에서 지역 정보 추출 (region1, region2, region3)
 */
function li_extract_region_from_address($address) {
    $region1 = '';
    $region2 = '';
    $region3 = '';
    
    // 시/도 추출
    $sido_patterns = [
        '서울특별시' => '서울', '서울시' => '서울', '서울' => '서울',
        '부산광역시' => '부산', '부산시' => '부산', '부산' => '부산',
        '대구광역시' => '대구', '대구시' => '대구', '대구' => '대구',
        '인천광역시' => '인천', '인천시' => '인천', '인천' => '인천',
        '광주광역시' => '광주', '광주시' => '광주', '광주' => '광주',
        '대전광역시' => '대전', '대전시' => '대전', '대전' => '대전',
        '울산광역시' => '울산', '울산시' => '울산', '울산' => '울산',
        '세종특별자치시' => '세종', '세종시' => '세종', '세종' => '세종',
        '경기도' => '경기', '경기' => '경기',
        '강원특별자치도' => '강원', '강원도' => '강원', '강원' => '강원',
        '충청북도' => '충북', '충북' => '충북',
        '충청남도' => '충남', '충남' => '충남',
        '전라북도' => '전북', '전북' => '전북',
        '전라남도' => '전남', '전남' => '전남',
        '경상북도' => '경북', '경북' => '경북',
        '경상남도' => '경남', '경남' => '경남',
        '제주특별자치도' => '제주', '제주도' => '제주', '제주' => '제주',
    ];
    
    foreach ($sido_patterns as $pattern => $name) {
        if (mb_strpos($address, $pattern) !== false) {
            $region1 = $name;
            break;
        }
    }
    
    // 시/군/구 추출
    if (preg_match('/(?:서울|부산|대구|인천|광주|대전|울산|경기|강원|충북|충남|전북|전남|경북|경남|제주|세종)[^\s]*\s*([가-힣]+(?:시|구|군))/u', $address, $matches)) {
        $region2 = $matches[1];
    }
    
    // 읍/면/동 추출 (region3)
    if (preg_match('/([가-힣]+(?:동|읍|면|리))/u', $address, $dong_matches)) {
        $region3 = $dong_matches[1];
    }
    
    return ['region1' => $region1, 'region2' => $region2, 'region3' => $region3];
}

/**
 * 동행복권 당첨점 조회 (회차별)
 * 
 * @param int $draw_no 회차 번호
 * @param int $rank 등수 (1 또는 2)
 * @return array 당첨점 목록
 */
function li_fetch_winning_stores($draw_no, $rank = 1) {
    $stores = [];
    
    $url = "https://www.dhlottery.co.kr/store.do?method=topStore&drwNo={$draw_no}&gameNo=5882";
    if ($rank == 2) {
        $url .= "&rank=2";
    }
    
    $html = li_http_request($url);
    
    if (!$html) {
        return $stores;
    }
    
    return li_parse_winning_stores($html, $rank);
}

/**
 * 당첨점 HTML 파싱
 */
function li_parse_winning_stores($html, $rank) {
    $stores = [];
    
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    // 당첨점 테이블 행
    $rows = $xpath->query("//table[@class='tbl_data tbl_data_col']//tbody//tr");
    
    if ($rows->length == 0) {
        $rows = $xpath->query("//table[contains(@class, 'tbl_data')]//tbody//tr");
    }
    
    foreach ($rows as $row) {
        $cells = $xpath->query(".//td", $row);
        
        if ($cells->length >= 4) {
            $store = [];
            
            // 순번
            $store['no'] = (int)trim($cells->item(0)->textContent ?? '0');
            
            // 상호명
            $store['store_name'] = trim($cells->item(1)->textContent ?? '');
            
            // 판매 방법 (자동/수동/반자동)
            $method = trim($cells->item(2)->textContent ?? '');
            if (mb_strpos($method, '자동') !== false) {
                $store['win_type'] = 'auto';
            } elseif (mb_strpos($method, '반자동') !== false) {
                $store['win_type'] = 'semi';
            } else {
                $store['win_type'] = 'manual';
            }
            
            // 주소
            $store['address'] = trim($cells->item(3)->textContent ?? '');
            
            if (empty($store['store_name'])) {
                continue;
            }
            
            // 지역 추출
            $region = li_extract_region_from_address($store['address']);
            $store['region1'] = $region['region1'];
            $store['region2'] = $region['region2'];
            $store['region3'] = $region['region3'] ?? '';
            
            $store['rank'] = $rank;
            
            $stores[] = $store;
        }
    }
    
    return $stores;
}

/**
 * 동행복권 당첨금 정보 조회
 */
function li_fetch_draw_prize_info($draw_no) {
    $url = "https://www.dhlottery.co.kr/gameResult.do?method=byWin&drwNo={$draw_no}";
    $html = li_http_request($url);
    
    if (!$html) {
        return null;
    }
    
    return li_parse_draw_prize_info($html);
}

/**
 * 당첨금 정보 파싱
 */
function li_parse_draw_prize_info($html) {
    $info = [
        'total_sales' => 0,
        'first_winners' => 0,
        'first_prize_each' => 0,
        'second_winners' => 0,
        'second_prize_each' => 0,
        'third_winners' => 0,
        'third_prize_each' => 0,
    ];
    
    // 총 판매금액
    if (preg_match('/총\s*판매금액[^\d]*(\d[\d,]*)\s*원/u', $html, $m)) {
        $info['total_sales'] = (int)str_replace(',', '', $m[1]);
    }
    
    // DOM 파싱으로 정확한 데이터 추출
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    // 등수별 당첨 정보 테이블
    $rows = $xpath->query("//table[@class='tbl_data tbl_data_col']//tbody//tr");
    
    foreach ($rows as $row) {
        $cells = $xpath->query(".//td", $row);
        if ($cells->length >= 4) {
            $rank_text = trim($cells->item(0)->textContent ?? '');
            $winners_text = trim($cells->item(2)->textContent ?? '');
            $prize_text = trim($cells->item(3)->textContent ?? '');
            
            $winners = (int)str_replace([',', '명', '게임'], '', $winners_text);
            $prize = (int)str_replace([',', '원'], '', preg_replace('/[^0-9,]/', '', $prize_text));
            
            if (mb_strpos($rank_text, '1등') !== false) {
                $info['first_winners'] = $winners;
                $info['first_prize_each'] = $prize;
            } elseif (mb_strpos($rank_text, '2등') !== false) {
                $info['second_winners'] = $winners;
                $info['second_prize_each'] = $prize;
            } elseif (mb_strpos($rank_text, '3등') !== false) {
                $info['third_winners'] = $winners;
                $info['third_prize_each'] = $prize;
            }
        }
    }
    
    return $info;
}

// ========== DB 저장 함수들 ==========

/**
 * 판매점 저장 또는 업데이트
 * @return int store_id
 */
function li_save_store($store) {
    global $g5;
    
    $name = sql_real_escape_string($store['store_name']);
    $address = sql_real_escape_string($store['address']);
    $region1 = sql_real_escape_string($store['region1'] ?? '');
    $region2 = sql_real_escape_string($store['region2'] ?? '');
    $region3 = sql_real_escape_string($store['region3'] ?? '');
    $phone = sql_real_escape_string($store['phone'] ?? '');
    
    // 기존 판매점 확인 (이름 + 주소로 중복 체크)
    $row = sql_fetch("SELECT store_id, wins_1st, wins_2nd FROM g5_lotto_store 
                      WHERE store_name = '{$name}' AND address = '{$address}' LIMIT 1");
    
    if ($row['store_id']) {
        // 기존 데이터 업데이트 (지역 정보 갱신)
        $update_sql = "UPDATE g5_lotto_store SET 
                   region1 = '{$region1}', 
                   region2 = '{$region2}', 
                   phone = '{$phone}',
                   updated_at = NOW()";
        
        // region3 필드가 존재하면 업데이트
        $check_region3 = sql_query("SHOW COLUMNS FROM g5_lotto_store LIKE 'region3'", false);
        if ($check_region3 && sql_num_rows($check_region3) > 0) {
            $update_sql .= ", region3 = '{$region3}'";
        }
        
        $update_sql .= " WHERE store_id = {$row['store_id']}";
        sql_query($update_sql);
        
        return (int)$row['store_id'];
    }
    
    // 새로 등록
    // region3 필드 존재 여부 확인
    $check_region3 = sql_query("SHOW COLUMNS FROM g5_lotto_store LIKE 'region3'", false);
    $has_region3 = ($check_region3 && sql_num_rows($check_region3) > 0);
    
    if ($has_region3) {
        sql_query("INSERT INTO g5_lotto_store 
                   (store_name, address, region1, region2, region3, phone, wins_1st, wins_2nd, created_at, updated_at) 
                   VALUES ('{$name}', '{$address}', '{$region1}', '{$region2}', '{$region3}', '{$phone}', 0, 0, NOW(), NOW())");
    } else {
        sql_query("INSERT INTO g5_lotto_store 
                   (store_name, address, region1, region2, phone, wins_1st, wins_2nd, created_at, updated_at) 
                   VALUES ('{$name}', '{$address}', '{$region1}', '{$region2}', '{$phone}', 0, 0, NOW(), NOW())");
    }
    
    return sql_insert_id();
}

/**
 * 회차별 당첨점 기록 저장
 */
function li_save_store_win($draw_no, $store_id, $rank, $win_type, $prize_amount = 0) {
    $draw_no = (int)$draw_no;
    $store_id = (int)$store_id;
    $rank = (int)$rank;
    $prize_amount = (int)$prize_amount;
    $win_type_sql = sql_real_escape_string($win_type);
    
    // 중복 체크
    $exists = sql_fetch("SELECT win_id FROM g5_lotto_store_win 
                         WHERE draw_no = {$draw_no} AND store_id = {$store_id} AND `rank` = {$rank}");
    
    if ($exists['win_id']) {
        return (int)$exists['win_id'];
    }
    
    sql_query("INSERT INTO g5_lotto_store_win 
               (draw_no, store_id, `rank`, win_type, prize_amount, created_at)
               VALUES ({$draw_no}, {$store_id}, {$rank}, '{$win_type_sql}', {$prize_amount}, NOW())");
    
    $win_id = sql_insert_id();
    
    // 판매점 누적 당첨 횟수 업데이트
    if ($rank == 1) {
        sql_query("UPDATE g5_lotto_store SET wins_1st = wins_1st + 1, updated_at = NOW() WHERE store_id = {$store_id}");
    } else {
        sql_query("UPDATE g5_lotto_store SET wins_2nd = wins_2nd + 1, updated_at = NOW() WHERE store_id = {$store_id}");
    }
    
    return $win_id;
}

/**
 * 당첨금 정보 업데이트
 */
function li_update_draw_prize_info($draw_no, $info) {
    $draw_no = (int)$draw_no;
    $total_sales = (int)$info['total_sales'];
    $first_winners = (int)$info['first_winners'];
    $first_prize_each = (int)$info['first_prize_each'];
    $second_winners = (int)$info['second_winners'];
    $second_prize_each = (int)$info['second_prize_each'];
    $third_winners = (int)$info['third_winners'];
    $third_prize_each = (int)$info['third_prize_each'];
    
    sql_query("UPDATE g5_lotto_draw SET 
               total_sales = {$total_sales},
               first_winners = {$first_winners},
               first_prize_each = {$first_prize_each},
               second_winners = {$second_winners},
               second_prize_each = {$second_prize_each},
               third_winners = {$third_winners},
               third_prize_each = {$third_prize_each}
               WHERE draw_no = {$draw_no}");
}

// ========== 동기화 함수들 ==========

/**
 * 전국 모든 판매점 수집 (전체 동기화)
 * 주의: 시간이 오래 걸림 (약 30분~1시간)
 */
function li_sync_all_stores() {
    $sido_list = array_keys(li_get_sido_codes());
    $total_count = 0;
    $results = [];
    
    foreach ($sido_list as $sido) {
        echo "[{$sido}] 수집 중...\n";
        
        $stores = li_fetch_all_stores_by_region($sido);
        $saved = 0;
        
        foreach ($stores as $store) {
            $store_id = li_save_store($store);
            if ($store_id) $saved++;
        }
        
        $results[$sido] = $saved;
        $total_count += $saved;
        
        echo "[{$sido}] {$saved}개 저장 완료\n";
        
        // 서버 부하 방지
        sleep(1);
    }
    
    return ['total' => $total_count, 'by_region' => $results];
}

/**
 * 특정 회차의 당첨점 정보 동기화
 */
function li_sync_draw_winning_stores($draw_no) {
    $result = ['first' => 0, 'second' => 0];
    
    // 당첨금 정보 조회 및 저장
    $prize_info = li_fetch_draw_prize_info($draw_no);
    if ($prize_info) {
        li_update_draw_prize_info($draw_no, $prize_info);
    }
    
    // 1등 당첨점 조회 및 저장
    $first_stores = li_fetch_winning_stores($draw_no, 1);
    foreach ($first_stores as $store) {
        $store_id = li_save_store($store);
        if ($store_id) {
            li_save_store_win($draw_no, $store_id, 1, $store['win_type'], $prize_info['first_prize_each'] ?? 0);
            $result['first']++;
        }
    }
    
    // 2등 당첨점 조회 및 저장
    $second_stores = li_fetch_winning_stores($draw_no, 2);
    foreach ($second_stores as $store) {
        $store_id = li_save_store($store);
        if ($store_id) {
            li_save_store_win($draw_no, $store_id, 2, $store['win_type'], $prize_info['second_prize_each'] ?? 0);
            $result['second']++;
        }
    }
    
    return $result;
}

/**
 * 여러 회차의 당첨점 일괄 동기화
 */
function li_sync_multiple_draws($start_round, $end_round) {
    $results = [];
    
    for ($round = $start_round; $round <= $end_round; $round++) {
        echo "[{$round}회] 동기화 중...\n";
        
        $result = li_sync_draw_winning_stores($round);
        $results[$round] = $result;
        
        echo "[{$round}회] 1등 {$result['first']}개, 2등 {$result['second']}개\n";
        
        // API 요청 간격
        usleep(500000); // 0.5초
    }
    
    return $results;
}

// ========== 조회 함수들 ==========

/**
 * 판매점 ID로 상세 정보 조회
 */
function li_get_store_by_id($store_id) {
    $store_id = (int)$store_id;
    return sql_fetch("SELECT * FROM g5_lotto_store WHERE store_id = {$store_id}");
}

/**
 * 판매점명으로 검색
 */
function li_get_store_by_name($name) {
    $name = sql_real_escape_string($name);
    return sql_fetch("SELECT * FROM g5_lotto_store WHERE store_name LIKE '%{$name}%' ORDER BY wins_1st DESC LIMIT 1");
}

/**
 * 지역별 판매점 목록 조회
 */
function li_get_stores_by_region($region1 = '', $region2 = '', $limit = 50, $offset = 0) {
    $where = "1=1";
    
    if ($region1) {
        $region1 = sql_real_escape_string($region1);
        $where .= " AND region1 = '{$region1}'";
    }
    
    if ($region2) {
        $region2 = sql_real_escape_string($region2);
        $where .= " AND region2 = '{$region2}'";
    }
    
    $limit = (int)$limit;
    $offset = (int)$offset;
    
    $sql = "SELECT * FROM g5_lotto_store WHERE {$where} ORDER BY wins_1st DESC, wins_2nd DESC LIMIT {$offset}, {$limit}";
    
    $stores = [];
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $stores[] = $row;
    }
    
    return $stores;
}

/**
 * 지역별 판매점 수
 */
function li_count_stores_by_region($region1 = '', $region2 = '') {
    $where = "1=1";
    
    if ($region1) {
        $region1 = sql_real_escape_string($region1);
        $where .= " AND region1 = '{$region1}'";
    }
    
    if ($region2) {
        $region2 = sql_real_escape_string($region2);
        $where .= " AND region2 = '{$region2}'";
    }
    
    $row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store WHERE {$where}");
    return (int)$row['cnt'];
}

/**
 * 명당 판매점 (1등 당첨 횟수 기준 TOP)
 */
function li_get_top_stores($limit = 50) {
    $limit = (int)$limit;
    $sql = "SELECT * FROM g5_lotto_store WHERE wins_1st > 0 ORDER BY wins_1st DESC, wins_2nd DESC LIMIT {$limit}";
    
    $stores = [];
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $stores[] = $row;
    }
    
    return $stores;
}

/**
 * 특정 회차의 당첨점 목록
 */
function li_get_draw_winning_stores($draw_no, $rank = null) {
    $draw_no = (int)$draw_no;
    $where = "w.draw_no = {$draw_no}";
    
    if ($rank) {
        $rank = (int)$rank;
        $where .= " AND w.`rank` = {$rank}";
    }
    
    $sql = "SELECT s.*, w.`rank`, w.win_type, w.prize_amount
            FROM g5_lotto_store_win w
            JOIN g5_lotto_store s ON s.store_id = w.store_id
            WHERE {$where}
            ORDER BY w.`rank` ASC, s.wins_1st DESC";
    
    $stores = [];
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $stores[] = $row;
    }
    
    return $stores;
}

/**
 * 특정 판매점의 당첨 이력
 */
function li_get_store_win_history($store_id, $limit = 50) {
    $store_id = (int)$store_id;
    $limit = (int)$limit;
    
    $sql = "SELECT w.*, d.n1, d.n2, d.n3, d.n4, d.n5, d.n6, d.bonus, d.draw_date
            FROM g5_lotto_store_win w
            JOIN g5_lotto_draw d ON d.draw_no = w.draw_no
            WHERE w.store_id = {$store_id}
            ORDER BY w.draw_no DESC
            LIMIT {$limit}";
    
    $history = [];
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $history[] = $row;
    }
    
    return $history;
}

/**
 * 판매점 검색 (이름/주소)
 */
function li_search_stores($keyword, $limit = 30) {
    $keyword = sql_real_escape_string($keyword);
    $limit = (int)$limit;
    
    $sql = "SELECT * FROM g5_lotto_store 
            WHERE store_name LIKE '%{$keyword}%' OR address LIKE '%{$keyword}%'
            ORDER BY wins_1st DESC, wins_2nd DESC
            LIMIT {$limit}";
    
    $stores = [];
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $stores[] = $row;
    }
    
    return $stores;
}

/**
 * 전체 판매점 수
 */
function li_count_all_stores() {
    $row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store");
    return (int)$row['cnt'];
}

/**
 * 전체 1등 당첨점 수
 */
function li_count_first_winners() {
    $row = sql_fetch("SELECT SUM(wins_1st) AS cnt FROM g5_lotto_store");
    return (int)$row['cnt'];
}

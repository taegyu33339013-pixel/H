<?php
/**
 * /stores/index.php - 로또 판매점 페이지 (토스증권 스타일)
 * 
 * URL 패턴:
 * - /stores/                          → 전체 명당 리스트
 * - /stores/서울/                     → 서울 지역 리스트
 * - /stores/서울/강남구/              → 강남구 리스트
 * - /stores/서울/강남구/대박복권방-123 → 상세 페이지
 * - /stores/?round=1148               → 1148회 당첨점
 */

// 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// ✅ 내부 링크를 항상 루트 쿼리(/?view=stores...)로 강제할지 여부
// - true  : 지역/상세 클릭 시 URL이 /?view=stores&region1=... 형태로 유지됨
// - false : SEO 경로(/stores/서울/...) 사용
if (!defined('STORES_FORCE_ROOT')) define('STORES_FORCE_ROOT', true);

// URL 파싱 및 페이지 타입 결정
// ============================================
$request_uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); // ✅ path만 사용
$region1 = '';
$region2 = '';
$region3 = '';
$store_id = 0;
$store_slug = '';
$page_type = 'list'; // list 또는 detail

// URL 패턴 분석
if (preg_match('/\/stores\/([^\/\?]+)\/?([^\/\?]*)\/?([^\/\?]*)/', $request_uri, $matches)) {
    $segment1 = trim($matches[1] ?? '');
    $segment2 = trim($matches[2] ?? '');
    $segment3 = trim($matches[3] ?? '');
    
    // 상세 페이지 판단: 마지막 세그먼트에 -숫자 패턴이 있으면
    if ($segment3 && preg_match('/^(.+)-(\d+)$/', $segment3, $detail_match)) {
        $region1 = $segment1;
        $region2 = $segment2;
        $store_slug = $detail_match[1];
        $store_id = (int)$detail_match[2];
        $page_type = 'detail';
    } elseif ($segment2 && preg_match('/^(.+)-(\d+)$/', $segment2, $detail_match)) {
        $region1 = $segment1;
        $store_slug = $detail_match[1];
        $store_id = (int)$detail_match[2];
        $page_type = 'detail';
    } elseif ($segment1 && preg_match('/^(.+)-(\d+)$/', $segment1, $detail_match)) {
        $store_slug = $detail_match[1];
        $store_id = (int)$detail_match[2];
        $page_type = 'detail';
    } else {
        // 리스트 페이지
        $region1 = $segment1;
        $region2 = $segment2;
        $region3 = $segment3;
    }
}

// GET 파라미터
$round = isset($_GET['round']) ? (int)$_GET['round'] : 0;
$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'default';

// 허용 탭(안전)
$allowed_tabs = ['default','hot','predict'];
if (!in_array($tab, $allowed_tabs, true)) $tab = 'default';

// 검색어
$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$q = preg_replace('/\s+/', ' ', $q); // 공백 정리(선택)

// ============================================
// ✅ 루트(/)에서 view=stores로 렌더링할 때는
//   지역/상세를 GET 기반으로도 받을 수 있게 처리
// ============================================
$path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$is_root_view = (isset($_GET['view']) && $_GET['view'] === 'stores' && !preg_match('~^/stores(/|$)~', $path_only));

if ($is_root_view) {
    // 루트에서는 region 파라미터로 리스트 필터
    if (isset($_GET['region1'])) $region1 = trim((string)$_GET['region1']);
    if (isset($_GET['region2'])) $region2 = trim((string)$_GET['region2']);
    if (isset($_GET['region3'])) $region3 = trim((string)$_GET['region3']);

    // 루트에서는 store_id로 상세 진입도 가능
    if (isset($_GET['store_id'])) {
        $store_id  = (int)$_GET['store_id'];
        $page_type = ($store_id > 0) ? 'detail' : 'list';
    }
}

// ✅ 판매점명 앞에 붙은 "숫자 + 공백(또는 구분자)" 제거 (표시용)
function stores_display_name($name) {
    $name = (string)$name;
    // 예: "1 스파", "2 로또", "10 당첨" → "스파", "로또", "당첨"
    // (확장) "1. 스파", "2) 로또", "3- 당첨" 같은 케이스도 처리
    $name = preg_replace('/^\s*\d+\s*[\.\)\-]?\s*/u', '', $name);
    return trim($name);
}

// ✅ 링크 생성 헬퍼: 현재 모드에 따라 /stores/... or /?view=stores... 로 생성
function stores_list_url($region1='', $region2='', $region3='', $extraQuery=[]) {
    $path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $is_root_view = (
        (defined('STORES_FORCE_ROOT') && STORES_FORCE_ROOT) ||
        (isset($_GET['view']) && $_GET['view'] === 'stores' && !preg_match('~^/stores(/|$)~', $path_only))
    );

    $extraQuery = array_filter((array)$extraQuery, function($v){
        return !($v === null || $v === '' || $v === 'default');
    });

    if ($is_root_view) {
        $q = array_merge(['view'=>'stores'], $extraQuery);
        if ($region1 !== '') $q['region1'] = $region1;
        if ($region2 !== '') $q['region2'] = $region2;
        if ($region3 !== '') $q['region3'] = $region3;
        return '/?' . http_build_query($q);
    }

    $url = '/stores/';
    if ($region1 !== '') $url .= urlencode($region1) . '/';
    if ($region2 !== '') $url .= urlencode($region2) . '/';
    if ($region3 !== '') $url .= urlencode($region3) . '/';
    if (!empty($extraQuery)) $url .= '?' . http_build_query($extraQuery);
    return $url;
}

function stores_detail_url($store) {
    $path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $is_root_view = (
        (defined('STORES_FORCE_ROOT') && STORES_FORCE_ROOT) ||
        (isset($_GET['view']) && $_GET['view'] === 'stores' && !preg_match('~^/stores(/|$)~', $path_only))
    );
    if ($is_root_view) {
        return '/?' . http_build_query(['view'=>'stores','store_id'=>(int)$store['store_id']]);
    }
    // 기존 SEO형
    $url = '/stores/';
    if (!empty($store['region1'])) $url .= urlencode($store['region1']) . '/';
    if (!empty($store['region2'])) $url .= urlencode($store['region2']) . '/';
    $url .= urlencode($store['store_name']) . '-' . (int)$store['store_id'];
    return $url;
}

// ✅ 페이징 정책
// - "전체"(지역 미선택) : 상위 50개만, 페이징 숨김 (전체 명당 / HOT / 명당예측 공통)
// - region 선택 OR 검색 OR 회차별 : 페이징 사용
$is_region_paging = (!empty($region1) || $round > 0 || $q !== '');
$per_page = $is_region_paging ? 30 : 50;

// 전체(지역 미선택)에서는 page 파라미터가 와도 항상 1페이지로 고정
$page = $is_region_paging
    ? (isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1)
    : 1;

$offset = ($page - 1) * $per_page;

// ============================================================
// ✅ 동행복권(인터넷 복권판매사이트) : 리스트에서는 "최소 순위 1개"만 노출
// - 정렬이 wins_1st DESC, wins_2nd DESC 이므로
//   최소 순위 = wins_1st ASC, wins_2nd ASC 기준으로 1개 선택
// ============================================================
$dhl_keep_store_id = 0;
if (function_exists('sql_fetch')) {
    $row = sql_fetch("
        SELECT store_id
        FROM g5_lotto_store
        WHERE store_name LIKE '%인터넷 복권판매사이트%'
           OR store_name LIKE '%동행복권%'
           OR address   LIKE '%dhlottery.co.kr%'
        ORDER BY wins_1st ASC, wins_2nd ASC, store_id ASC
        LIMIT 1
    ");
    $dhl_keep_store_id = $row ? (int)$row['store_id'] : 0;
}

// 리스트 쿼리에 붙일 where 조각 생성
if (!function_exists('stores_dhl_min_only_where')) {
    function stores_dhl_min_only_where($alias='s', $keep_id=0) {
        if ((int)$keep_id <= 0) return '';
        $is_official = "({$alias}.store_name LIKE '%인터넷 복권판매사이트%'
                        OR {$alias}.store_name LIKE '%동행복권%'
                        OR {$alias}.address LIKE '%dhlottery.co.kr%')";
        return " AND (NOT {$is_official} OR {$alias}.store_id = {$keep_id}) ";
    }
}

$dhl_where_s = stores_dhl_min_only_where('s', $dhl_keep_store_id);

// 지역 목록
$regions = [
    '서울' => ['강남구', '강동구', '강북구', '강서구', '관악구', '광진구', '구로구', '금천구', '노원구', '도봉구', '동대문구', '동작구', '마포구', '서대문구', '서초구', '성동구', '성북구', '송파구', '양천구', '영등포구', '용산구', '은평구', '종로구', '중구', '중랑구'],
    '부산' => ['강서구', '금정구', '기장군', '남구', '동구', '동래구', '부산진구', '북구', '사상구', '사하구', '서구', '수영구', '연제구', '영도구', '중구', '해운대구'],
    '대구' => ['남구', '달서구', '달성군', '동구', '북구', '서구', '수성구', '중구'],
    '인천' => ['강화군', '계양구', '남동구', '동구', '미추홀구', '부평구', '서구', '연수구', '옹진군', '중구'],
    '광주' => ['광산구', '남구', '동구', '북구', '서구'],
    '대전' => ['대덕구', '동구', '서구', '유성구', '중구'],
    '울산' => ['남구', '동구', '북구', '울주군', '중구'],
    '세종' => [],
    '경기' => ['가평군', '고양시', '과천시', '광명시', '광주시', '구리시', '군포시', '김포시', '남양주시', '동두천시', '부천시', '성남시', '수원시', '시흥시', '안산시', '안성시', '안양시', '양주시', '양평군', '여주시', '연천군', '오산시', '용인시', '의왕시', '의정부시', '이천시', '파주시', '평택시', '포천시', '하남시', '화성시'],
    '강원' => ['강릉시', '고성군', '동해시', '삼척시', '속초시', '양구군', '양양군', '영월군', '원주시', '인제군', '정선군', '철원군', '춘천시', '태백시', '평창군', '홍천군', '화천군', '횡성군'],
    '충북' => ['괴산군', '단양군', '보은군', '영동군', '옥천군', '음성군', '제천시', '증평군', '진천군', '청주시', '충주시'],
    '충남' => ['계룡시', '공주시', '금산군', '논산시', '당진시', '보령시', '부여군', '서산시', '서천군', '아산시', '예산군', '천안시', '청양군', '태안군', '홍성군'],
    '전북' => ['고창군', '군산시', '김제시', '남원시', '무주군', '부안군', '순창군', '완주군', '익산시', '임실군', '장수군', '전주시', '정읍시', '진안군'],
    '전남' => ['강진군', '고흥군', '곡성군', '광양시', '구례군', '나주시', '담양군', '목포시', '무안군', '보성군', '순천시', '신안군', '여수시', '영광군', '영암군', '완도군', '장성군', '장흥군', '진도군', '함평군', '해남군', '화순군'],
    '경북' => ['경산시', '경주시', '고령군', '구미시', '군위군', '김천시', '문경시', '봉화군', '상주시', '성주군', '안동시', '영덕군', '영양군', '영주시', '영천시', '예천군', '울릉군', '울진군', '의성군', '청도군', '청송군', '칠곡군', '포항시'],
    '경남' => ['거제시', '거창군', '고성군', '김해시', '남해군', '밀양시', '사천시', '산청군', '양산시', '의령군', '진주시', '창녕군', '창원시', '통영시', '하동군', '함안군', '함양군', '합천군'],
    '제주' => ['서귀포시', '제주시'],
];

// ============================================
// 데이터 조회
// ============================================
$stores = [];
$store = null;
$win_history = [];
$total_count = 0;
$latest_round = 1; // 기본값

// ✅ draw 테이블 기준 최신 회차
$latest = sql_fetch("SELECT MAX(draw_no) AS latest FROM g5_lotto_draw");
if ($latest && $latest['latest']) $latest_round = (int)$latest['latest'];
// draw 테이블이 비어있을 때만 기존 win 테이블로 fallback
if (!$latest_round) {
	$latest2 = sql_fetch("SELECT MAX(draw_no) AS latest FROM g5_lotto_store_win");
	if ($latest2 && $latest2['latest']) $latest_round = (int)$latest2['latest'];
}

// ============================================
// 상단 배너용 최신 회차 데이터
// ============================================
$latest_draw = null;
$prev_draw   = null;
$banner_numbers = [3, 12, 18, 27, 35, 42]; // fallback
$banner_bonus   = 7;                        // fallback
$banner_first_each = null;
$banner_first_winners = null;
$banner_total_sales = null;
$banner_sales_delta = null;

if (function_exists('sql_query') && $latest_round > 0) {
    $latest_draw = sql_fetch("SELECT * FROM g5_lotto_draw WHERE draw_no={$latest_round} LIMIT 1");
    if ($latest_draw) {
        $banner_numbers = [
            (int)$latest_draw['n1'], (int)$latest_draw['n2'], (int)$latest_draw['n3'],
            (int)$latest_draw['n4'], (int)$latest_draw['n5'], (int)$latest_draw['n6'],
        ];
        $banner_bonus = (int)$latest_draw['bonus'];
        $banner_first_each     = isset($latest_draw['first_prize_each']) ? (int)$latest_draw['first_prize_each'] : null;
        $banner_first_winners  = isset($latest_draw['first_winners']) ? (int)$latest_draw['first_winners'] : null;
        $banner_total_sales    = isset($latest_draw['total_sales']) ? (int)$latest_draw['total_sales'] : null;
    }

    // 전주 데이터(총판매금액 증감)
    if ($latest_round > 1) {
        $prev_round = $latest_round - 1;
        $prev_draw = sql_fetch("SELECT total_sales FROM g5_lotto_draw WHERE draw_no={$prev_round} LIMIT 1");
        if ($prev_draw && (int)$prev_draw['total_sales'] > 0 && $banner_total_sales !== null) {
            $banner_sales_delta = (($banner_total_sales - (int)$prev_draw['total_sales']) / (int)$prev_draw['total_sales']) * 100;
        }
    }
}

// 금액 포맷(억 단위)
function fmt_eok($won, $dec=0) {
    if ($won === null || $won === '' || (int)$won <= 0) return '-';
    $eok = ((float)$won) / 100000000;
    return number_format($eok, $dec) . '억';
}

// 다음 추첨(토요일 20:45) 카운트다운 텍스트
function next_draw_countdown_kst() {
    try {
        $tz = new DateTimeZone('Asia/Seoul');
        $now = new DateTime('now', $tz);
        $next = new DateTime('now', $tz);
        $next->setTime(20, 45, 0);
        // 1=월 ... 6=토 7=일
        $dow = (int)$now->format('N');
        $daysToSat = 6 - $dow;
        if ($daysToSat < 0) $daysToSat += 7;
        $next->modify("+{$daysToSat} days");
        if ($next <= $now) $next->modify("+7 days");
        $diff = $now->diff($next);
        return [$diff->days . '일 ' . $diff->h . '시간', '토요일 20:45'];
    } catch (Exception $e) {
        return ['-', '토요일 20:45'];
    }
}
[$next_draw_left, $next_draw_label] = next_draw_countdown_kst();


// ============================================
// 상세 페이지 데이터
// ============================================
if ($page_type === 'detail' && $store_id > 0) {
    if (function_exists('sql_query')) {
        // 판매점 기본 정보
        $store = sql_fetch("
            SELECT * FROM g5_lotto_store WHERE store_id = {$store_id}
        ");
        
        // 당첨 이력
        $win_result = sql_query("
            SELECT w.*, 
                   (SELECT draw_date FROM g5_lotto_draw WHERE draw_no = w.draw_no LIMIT 1) AS draw_date
            FROM g5_lotto_store_win w
            WHERE w.store_id = {$store_id}
            ORDER BY w.draw_no DESC
            LIMIT 50
        ");
        while ($row = sql_fetch_array($win_result)) {
            $win_history[] = $row;
        }
    }
    
    if (!$store) {
        // 404 처리 또는 리스트로 리다이렉트
        header("Location: /stores/");
        exit;
    }
    
    $page_title = $store['store_name'] . ' - 로또 판매점';
    $page_desc = $store['address'] . ' | 1등 ' . $store['wins_1st'] . '회, 2등 ' . $store['wins_2nd'] . '회 당첨';
}
// ============================================
// 리스트 페이지 데이터
// ============================================
else {
    if ($round > 0) {
        // 회차별 당첨점
        $page_title = "로또 {$round}회 당첨점";
        $page_desc = "로또 {$round}회 1등, 2등 당첨 판매점 정보";
        
        if (function_exists('sql_query')) {
            $whereQ = '';
            if ($q !== '') {
                $qs = sql_real_escape_string($q);
                $whereQ = " AND (s.store_name LIKE '%{$qs}%' OR s.address LIKE '%{$qs}%')";
            }

            // ✅ 같은 주소(address)는 1개로 묶고(rank 합산), 합산 rank_sum이 낮을수록 위로
            $res = sql_query("
                SELECT
                    MIN(s.store_id)    AS store_id,
                    MIN(s.store_name)  AS store_name,
                    MIN(s.region1)     AS region1,
                    MIN(s.region2)     AS region2,
                    MIN(s.region3)     AS region3,
                    MIN(s.wins_1st)    AS wins_1st,
                    MIN(s.wins_2nd)    AS wins_2nd,
                    MIN(s.latitude)    AS latitude,
                    MIN(s.longitude)   AS longitude,
                    MIN(s.phone)       AS phone,
                    MIN(s.opening_hours) AS opening_hours,
                    s.address          AS address,
                    SUM(w.rank)        AS rank_sum,      -- ✅ 순위(1/2) 합산값
                    MIN(w.rank)        AS best_rank,     -- 참고용(배지 색 등)
                    SUM(CASE WHEN w.rank=1 THEN 1 ELSE 0 END) AS win1_cnt,
                    SUM(CASE WHEN w.rank=2 THEN 1 ELSE 0 END) AS win2_cnt,
                    SUM(w.prize_amount) AS prize_sum,
                    MAX(w.prize_amount) AS prize_max
                FROM g5_lotto_store_win w
                INNER JOIN g5_lotto_store s ON s.store_id = w.store_id
                WHERE w.draw_no = {$round}
                  AND w.rank IN (1,2)
                  {$whereQ}
                  {$dhl_where_s}
                GROUP BY s.address
                ORDER BY rank_sum ASC, prize_max DESC
                LIMIT {$offset}, {$per_page}
            ");
            while ($row = sql_fetch_array($res)) {
                $stores[] = $row;
            }
                       
            // ✅ 주소 기준으로 몇 개 그룹인지 카운트
            $cnt = sql_fetch("
                SELECT COUNT(DISTINCT s.address) AS cnt
                FROM g5_lotto_store_win w
                INNER JOIN g5_lotto_store s ON s.store_id = w.store_id
                WHERE w.draw_no = {$round}
                  AND w.rank IN (1,2)
                  {$whereQ}
                  {$dhl_where_s}
            ");
            $total_count = (int)($cnt['cnt'] ?? 0);
        }
    } elseif ($tab === 'predict') {
        // ✅ 명당 예측(최근성 가중치 기반 랭킹)
        $page_title = '🔮 명당 예측';
        $page_desc  = '최근 당첨 이력을 시간가중치로 점수화한 “유력 판매점” 랭킹입니다.';

        if (function_exists('sql_query')) {
            // 대표(주소 기준) store 선택 정렬 규칙(중복 store_id 대비)
            $repOrder = "ORDER BY ss.wins_1st DESC, ss.wins_2nd DESC, ss.store_id ASC";

            // 검색
            $whereQ = '';
            if ($q !== '') {
                $qs = sql_real_escape_string($q);
                $whereQ = " AND (
                    s.address LIKE '%{$qs}%'
                    OR (
                        SELECT ss.store_name
                        FROM g5_lotto_store ss
                        WHERE ss.address = s.address
                        {$repOrder}
                        LIMIT 1
                    ) LIKE '%{$qs}%'
                )";
            }

            // 지역(대표 store region 기준)
            $whereR = '';
            if (!empty($region1)) {
                $r1 = sql_real_escape_string($region1);
                $whereR .= " AND (
                    SELECT ss.region1
                    FROM g5_lotto_store ss
                    WHERE ss.address = s.address
                    {$repOrder}
                    LIMIT 1
                ) = '{$r1}'";
            }
            if (!empty($region2)) {
                $r2 = sql_real_escape_string($region2);
                $whereR .= " AND (
                    SELECT ss.region2
                    FROM g5_lotto_store ss
                    WHERE ss.address = s.address
                    {$repOrder}
                    LIMIT 1
                ) = '{$r2}'";
            }
            if (!empty($region3)) {
                $r3 = sql_real_escape_string($region3);
                $whereR .= " AND (
                    SELECT ss.region3
                    FROM g5_lotto_store ss
                    WHERE ss.address = s.address
                    {$repOrder}
                    LIMIT 1
                ) = '{$r3}'";
            }

            // ✅ 점수 정책
            // - 1등=5점, 2등=2점
            // - 시간감쇠: 0.94 ^ weeks_ago (최근일수록 점수가 큼)
            $res = sql_query("
                SELECT
                    (SELECT ss.store_id
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS store_id,
                    (SELECT ss.store_name
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS store_name,
                    (SELECT ss.region1
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS region1,
                    (SELECT ss.region2
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS region2,
                    (SELECT ss.region3
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS region3,
                    s.address AS address,
                    (SELECT ss.wins_1st
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS wins_1st,
                    (SELECT ss.wins_2nd
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS wins_2nd,
                    (SELECT ss.latitude
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS latitude,
                    (SELECT ss.longitude
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS longitude,
                    (SELECT ss.phone
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS phone,
                    (SELECT ss.opening_hours
                       FROM g5_lotto_store ss
                      WHERE ss.address = s.address
                      {$repOrder}
                      LIMIT 1) AS opening_hours,

                    /* ✅ 예측 점수(시간가중치) */
                    SUM(
                        (CASE WHEN w.rank=1 THEN 5 ELSE 2 END)
                        * POW(0.94, TIMESTAMPDIFF(WEEK, d.draw_date, CURDATE()))
                    ) AS pred_score,

                    /* 참고용 메타 */
                    SUM(CASE WHEN d.draw_date >= DATE_SUB(CURDATE(), INTERVAL 180 DAY) THEN 1 ELSE 0 END) AS recent_hits,
                    MAX(w.draw_no) AS last_win_round
                FROM g5_lotto_store_win w
                INNER JOIN g5_lotto_store s ON s.store_id = w.store_id
                INNER JOIN g5_lotto_draw d ON d.draw_no = w.draw_no
                WHERE w.rank IN (1,2)
                  {$whereQ} {$whereR} {$dhl_where_s}
                GROUP BY s.address
                ORDER BY pred_score DESC, recent_hits DESC, last_win_round DESC
                LIMIT {$offset}, {$per_page}
            ");
            while ($row = sql_fetch_array($res)) {
                $stores[] = $row;
            }

            $cnt = sql_fetch("
                SELECT COUNT(DISTINCT s.address) AS cnt
                FROM g5_lotto_store_win w
                INNER JOIN g5_lotto_store s ON s.store_id = w.store_id
                INNER JOIN g5_lotto_draw d ON d.draw_no = w.draw_no
                WHERE w.rank IN (1,2)
                  {$whereQ} {$whereR} {$dhl_where_s}
            ");
            $total_count = (int)($cnt['cnt'] ?? 0);
        }
    } elseif ($tab === 'hot') {
        // HOT 판매점
        $page_title = '🔥 HOT 판매점';
        $page_desc = '최근 30일 내 당첨된 인기 판매점';
        
        if (function_exists('sql_query')) {
            // ✅ 대표(주소 기준) store 선택 정렬 규칙(중복 store_id 대비)
            $repOrder = "ORDER BY ss.wins_1st DESC, ss.wins_2nd DESC, ss.store_id ASC";

            $whereQ = '';
            if ($q !== '') {
                $qs = sql_real_escape_string($q);
                // ✅ HOT은 대표 store_name 기준으로 검색(중복 store_id 대비)
                $whereQ = " AND (
                    s.address LIKE '%{$qs}%'
                    OR (
                        SELECT ss.store_name
                        FROM g5_lotto_store ss
                        WHERE ss.address = s.address
                        {$repOrder}
                        LIMIT 1
                    ) LIKE '%{$qs}%'
                )";
             }

             // ✅ HOT 지역 필터(대표 store의 region 기준으로 필터)
             $whereR = '';
             if (!empty($region1)) {
                $r1 = sql_real_escape_string($region1);
                $whereR .= " AND (
                    SELECT ss.region1
                    FROM g5_lotto_store ss
                    WHERE ss.address = s.address
                    {$repOrder}
                    LIMIT 1
                ) = '{$r1}'";
             }
             if (!empty($region2)) {
                $r2 = sql_real_escape_string($region2);
                $whereR .= " AND (
                    SELECT ss.region2
                    FROM g5_lotto_store ss
                    WHERE ss.address = s.address
                    {$repOrder}
                    LIMIT 1
                ) = '{$r2}'";
             }
             if (!empty($region3)) {
                $r3 = sql_real_escape_string($region3);
                $whereR .= " AND (
                    SELECT ss.region3
                    FROM g5_lotto_store ss
                    WHERE ss.address = s.address
                    {$repOrder}
                    LIMIT 1
                ) = '{$r3}'";
             }
             $res = sql_query("
               SELECT
                   /* ✅ 주소 기준 '대표 store'를 고정(누적 1등/2등 최다) */
                   (SELECT ss.store_id
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS store_id,
                   (SELECT ss.store_name
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS store_name,
                   (SELECT ss.region1
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS region1,
                   (SELECT ss.region2
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS region2,
                   (SELECT ss.region3
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS region3,
                   s.address AS address,
                   (SELECT ss.wins_1st
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS wins_1st,
                   (SELECT ss.wins_2nd
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS wins_2nd,
                   (SELECT ss.latitude
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS latitude,
                   (SELECT ss.longitude
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS longitude,
                   (SELECT ss.phone
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS phone,
                   (SELECT ss.opening_hours
                      FROM g5_lotto_store ss
                     WHERE ss.address = s.address
                     {$repOrder}
                     LIMIT 1) AS opening_hours,

                   /* ✅ HOT 기준(최근 30일) */
                   COUNT(*) AS recent_wins,
                   MAX(w.draw_no) AS last_win_round
               FROM g5_lotto_store_win w
               INNER JOIN g5_lotto_store s ON s.store_id = w.store_id
               INNER JOIN g5_lotto_draw d ON d.draw_no = w.draw_no
               WHERE d.draw_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 AND w.rank IN (1,2)
                 {$whereQ} {$whereR} {$dhl_where_s}
               GROUP BY s.address
               ORDER BY recent_wins DESC, last_win_round DESC
               LIMIT {$offset}, {$per_page}
           ");
            while ($row = sql_fetch_array($res)) {
                $stores[] = $row;
            }

            $cnt = sql_fetch("
               SELECT COUNT(DISTINCT s.address) AS cnt
               FROM g5_lotto_store_win w
               INNER JOIN g5_lotto_store s ON s.store_id = w.store_id
               INNER JOIN g5_lotto_draw d ON d.draw_no = w.draw_no
               WHERE d.draw_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 AND w.rank IN (1,2)
                 {$whereQ} {$whereR} {$dhl_where_s}
            ");
            $total_count = (int)$cnt['cnt'];
        }
    } elseif ($tab === 'predict') {
        // ============================================
        // 🔮 명당 예측 (최근 1년 흐름 + 누적 + 최근 당첨 회차)
        // - 전체(지역 미선택): 상위 50개(페이징 숨김)
        // - 지역/검색: 30개 페이징
        // ============================================
        $page_title = '🔮 명당 예측';
        $page_desc  = '최근 1년 당첨 흐름 + 누적 당첨 + 최근 당첨 회차를 반영한 예측 순위';

        if (function_exists('sql_query')) {
            // ✅ 최근 1년(약 52회차) 기준
            $predict_window = 52;
            $from_round = max(1, (int)$latest_round - $predict_window + 1);

            // ✅ 대표 store 선택 정렬 규칙(주소 중복 store_id 대비)
            $repOrder = "ORDER BY ss.wins_1st DESC, ss.wins_2nd DESC, ss.store_id ASC";

            // 검색어(대표 store_name + 주소)
            $whereQ = '';
            if ($q !== '') {
                $qs = sql_real_escape_string($q);
                $whereQ = " AND (rep.store_name LIKE '%{$qs}%' OR rep.address LIKE '%{$qs}%') ";
            }

            // 지역 필터(대표 store의 region 기준으로 필터)
            $whereR = '';
            if (!empty($region1)) {
                $r1 = sql_real_escape_string($region1);
                $whereR .= " AND rep.region1 = '{$r1}' ";
            }
            if (!empty($region2)) {
                $r2 = sql_real_escape_string($region2);
                $whereR .= " AND rep.region2 = '{$r2}' ";
            }
            if (!empty($region3)) {
                $r3 = sql_real_escape_string($region3);
                $whereR .= " AND rep.region3 = '{$r3}' ";
            }

            // ✅ 주소 단위로 대표 store를 고정하고(=rep), 같은 주소의 다른 store_id 당첨 이력까지 합산
            $res = sql_query("
                SELECT
                    rep.store_id      AS store_id,
                    rep.store_name    AS store_name,
                    rep.region1       AS region1,
                    rep.region2       AS region2,
                    rep.region3       AS region3,
                    rep.address       AS address,
                    rep.wins_1st      AS wins_1st,
                    rep.wins_2nd      AS wins_2nd,
                    rep.latitude      AS latitude,
                    rep.longitude     AS longitude,
                    rep.phone         AS phone,
                    rep.opening_hours AS opening_hours,

                    /* ✅ 최근 1년(회차 기준) 당첨 흐름 */
                    SUM(CASE WHEN w.draw_no >= {$from_round} AND w.rank=1 THEN 1 ELSE 0 END) AS recent_win1,
                    SUM(CASE WHEN w.draw_no >= {$from_round} AND w.rank=2 THEN 1 ELSE 0 END) AS recent_win2,
                    SUM(CASE WHEN w.draw_no >= {$from_round} THEN 1 ELSE 0 END) AS recent_wins,
                    MAX(w.draw_no) AS last_win_round,

                    /* ✅ 예측 점수(가중치) */
                    (
                        /* 최근 1년 1등/2등 가중 */
                        SUM(CASE WHEN w.draw_no >= {$from_round} AND w.rank=1 THEN 1 ELSE 0 END) * 200
                      + SUM(CASE WHEN w.draw_no >= {$from_round} AND w.rank=2 THEN 1 ELSE 0 END) * 80
                        /* 누적(대표 store 기준) */
                      + rep.wins_1st * 30
                      + rep.wins_2nd * 10
                        /* 최근 당첨이 오래되면 감점 */
                      - IFNULL(({$latest_round} - MAX(w.draw_no)), 999) * 2
                    ) AS predict_score
                FROM (
                    SELECT DISTINCT address
                    FROM g5_lotto_store
                ) a
                INNER JOIN g5_lotto_store rep
                    ON rep.store_id = (
                        SELECT ss.store_id
                        FROM g5_lotto_store ss
                        WHERE ss.address = a.address
                        {$repOrder}
                        LIMIT 1
                    )
                INNER JOIN g5_lotto_store s
                    ON s.address = a.address
                LEFT JOIN g5_lotto_store_win w
                    ON w.store_id = s.store_id
                   AND w.rank IN (1,2)
                WHERE 1=1
                    {$whereQ}
                    {$whereR}
                    {$dhl_where_rep}
                GROUP BY a.address
                HAVING rep.wins_1st > 0
                ORDER BY predict_score DESC, recent_wins DESC, last_win_round DESC
                LIMIT {$offset}, {$per_page}
            ");
            while ($row = sql_fetch_array($res)) {
                $stores[] = $row;
            }

            // ✅ 총 개수(주소 단위)
            $cnt = sql_fetch("
                SELECT COUNT(*) AS cnt
                FROM (
                    SELECT a.address
                    FROM (SELECT DISTINCT address FROM g5_lotto_store) a
                    INNER JOIN g5_lotto_store rep
                        ON rep.store_id = (
                            SELECT ss.store_id
                            FROM g5_lotto_store ss
                            WHERE ss.address = a.address
                            {$repOrder}
                            LIMIT 1
                        )
                    WHERE rep.wins_1st > 0
                      {$whereQ}
                      {$whereR}
                      {$dhl_where_rep}
                ) t
            ");
            $total_count = (int)($cnt['cnt'] ?? 0);
        }
    } elseif ($region1) {
        // 지역별
        $region_full = $region1 . ($region2 ? ' ' . $region2 : '') . ($region3 ? ' ' . $region3 : '');
        $page_title = "{$region_full} 로또 판매점";
        $page_desc = "{$region_full} 지역 로또 1등, 2등 당첨 판매점 정보";
        
        if (function_exists('sql_query')) {
            $where = "s.region1 = '" . sql_real_escape_string($region1) . "'";
            if ($region2) $where .= " AND s.region2 = '" . sql_real_escape_string($region2) . "'";
            if ($region3) $where .= " AND s.region3 = '" . sql_real_escape_string($region3) . "'";
            if ($q !== '') {
                $qs = sql_real_escape_string($q);
                $where .= " AND (s.store_name LIKE '%{$qs}%' OR s.address LIKE '%{$qs}%')";
            }

            $res = sql_query("
                SELECT
                    MIN(s.store_id) AS store_id,
                    MIN(s.store_name) AS store_name,
                    MIN(s.region1) AS region1,
                    MIN(s.region2) AS region2,
                    MIN(s.region3) AS region3,
                    s.address AS address,
                    SUM(s.wins_1st) AS wins_1st,
                    SUM(s.wins_2nd) AS wins_2nd,
                    MIN(s.latitude) AS latitude,
                    MIN(s.longitude) AS longitude,
                    MIN(s.phone) AS phone,
                    MIN(s.opening_hours) AS opening_hours
                FROM g5_lotto_store s
                WHERE {$where} {$dhl_where_s}
                GROUP BY s.address
                ORDER BY wins_1st DESC, wins_2nd DESC
                LIMIT {$offset}, {$per_page}
            ");
            while ($row = sql_fetch_array($res)) {
                $stores[] = $row;
            }            
            $cnt = sql_fetch("SELECT COUNT(DISTINCT s.address) AS cnt FROM g5_lotto_store s WHERE {$where} {$dhl_where_s}");
            $total_count = (int)$cnt['cnt'];
        }
    } else {
        // 전체 명당
        $page_title = '로또 명당 판매점';
        $page_desc = '전국 로또 1등 당첨 명당 판매점 정보. 누적 1등 당첨 횟수 기준.';
        
        if (function_exists('sql_query')) {
            $where = "s.wins_1st > 0";
            if ($q !== '') {
                $qs = sql_real_escape_string($q);
                $where .= " AND (s.store_name LIKE '%{$qs}%' OR s.address LIKE '%{$qs}%')";
            }
            $res = sql_query("
                SELECT
                    MIN(s.store_id) AS store_id,
                    MIN(s.store_name) AS store_name,
                    MIN(s.region1) AS region1,
                    MIN(s.region2) AS region2,
                    MIN(s.region3) AS region3,
                    s.address AS address,
                    SUM(s.wins_1st) AS wins_1st,
                    SUM(s.wins_2nd) AS wins_2nd,
                    MIN(s.latitude) AS latitude,
                    MIN(s.longitude) AS longitude,
                    MIN(s.phone) AS phone,
                    MIN(s.opening_hours) AS opening_hours
                FROM g5_lotto_store s
                WHERE {$where} {$dhl_where_s}
                GROUP BY s.address
                ORDER BY wins_1st DESC, wins_2nd DESC
                LIMIT {$offset}, {$per_page}
            ");
            while ($row = sql_fetch_array($res)) {
                $stores[] = $row;
            }
            $cnt = sql_fetch("SELECT COUNT(DISTINCT s.address) AS cnt FROM g5_lotto_store s WHERE {$where} {$dhl_where_s}");
            $total_count = (int)$cnt['cnt'];
        }
    }
}

// ✅ 전체(지역 미선택)에서는 페이징을 사용하지 않음
if (!$is_region_paging) {
    $total_pages = 1;
} else {
    $total_pages = (int)ceil($total_count / $per_page);
}

// (기존 get_store_url은 stores_detail_url로 대체)
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="robots" content="index, follow">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:site_name" content="오늘로또">
  
  <meta name="theme-color" content="#080b14">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  
  <!-- Fonts -->
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <style>
    /* ============================================
       Lucky Fortune 컬러 팔레트 + 토스증권 스타일
       ============================================ */
    :root {
      --bg-deep: #080b14;
      --bg-primary: #0d1220;
      --bg-secondary: #151c2c;
      --bg-card: #1a2236;
      --bg-hover: #212b40;
      
      --gold: #F5B800;
      --gold-light: #FFD54F;
      --gold-dark: #C99700;
      --gradient-gold: linear-gradient(135deg, #F5B800 0%, #FF8C00 100%);
      
      --red: #FF4757;
      --red-light: #FF6B7A;
      --blue: #00B4D8;
      --blue-light: #48CAE4;
      --purple: #9D4EDD;
      --green: #00E676;
      
      --text-primary: #ffffff;
      --text-secondary: #a8b5c8;
      --text-muted: #6b7a90;
      
      --border: rgba(255, 255, 255, 0.08);
      --border-gold: rgba(245, 184, 0, 0.3);
      
      --mesh-gradient: 
        radial-gradient(at 20% 0%, rgba(245, 184, 0, 0.12) 0px, transparent 50%),
        radial-gradient(at 80% 10%, rgba(157, 78, 221, 0.08) 0px, transparent 50%);
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    html {
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    
    body {
      font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-deep);
      background-image: var(--mesh-gradient);
      background-attachment: fixed;
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }
    
    a { color: inherit; text-decoration: none; }
    
    /* ===== Header ===== */
    .header {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(8, 11, 20, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }
    
    .header-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      height: 56px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .logo-icon {
      width: 36px;
      height: 36px;
      background: var(--gradient-gold);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    
    .logo-text {
      font-weight: 800;
      font-size: 1.2rem;
      background: var(--gradient-gold);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .header-nav {
      display: flex;
      align-items: center;
      gap: 4px;
    }
    
    .nav-link {
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-secondary);
      transition: all 0.2s;
    }
    
    .nav-link:hover, .nav-link.active {
      background: rgba(245, 184, 0, 0.1);
      color: var(--gold);
    }
    
    .header-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .btn {
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .btn-primary {
      background: var(--gradient-gold);
      color: #000;
    }
    
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 20px rgba(245, 184, 0, 0.3);
    }
    
    .btn-secondary {
      background: var(--bg-secondary);
      color: var(--text-secondary);
    }
    
    .search-box {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: var(--bg-secondary);
      border: 1px solid var(--border);
      border-radius: 10px;
    }
    
    .search-box input {
      background: transparent;
      border: none;
      outline: none;
      color: var(--text-secondary);
      font-size: 0.9rem;
      width: 140px;
    }
    
    .search-box svg {
      width: 16px;
      height: 16px;
      color: var(--text-muted);
    }
    
    /* ===== Main Container ===== */
    .main {
      max-width: 1200px;
      margin: 0 auto;
      padding: 24px 20px 100px;
    }
    
    /* ===== 마켓 배너 (리스트 페이지) ===== */
    .market-banner {
      padding: 20px 0;
      border-bottom: 1px solid var(--border);
      margin-bottom: 24px;
    }
    
    .market-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 16px;
    }
    
    .badge {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 700;
    }
    
    .badge-gold {
      background: var(--gradient-gold);
      color: #000;
    }
    
    .badge-purple {
      background: rgba(157, 78, 221, 0.2);
      color: var(--purple);
    }
    
    .badge-blue {
      background: rgba(0, 180, 216, 0.2);
      color: var(--blue);
    }
    
    .badge-green {
      background: rgba(0, 230, 118, 0.2);
      color: var(--green);
    }
    
    .market-cards {
      display: flex;
      gap: 16px;
      overflow-x: auto;
      padding-bottom: 8px;
    }
    
    .market-card {
      flex-shrink: 0;
      padding: 20px;
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      min-width: 160px;
    }
    
    .market-card-label {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-bottom: 4px;
    }
    
    .market-card-value {
      font-size: 1.5rem;
      font-weight: 800;
    }
    
    .market-card-sub {
      font-size: 0.75rem;
      margin-top: 4px;
    }
    
    /* 당첨번호 카드 */
    .lotto-balls {
      display: flex;
      gap: 6px;
      align-items: center;
    }
    
    .lotto-ball {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.85rem;
      color: #fff;
    }
    
    .ball-yellow { background: #FBBF24; color: #000; }
    .ball-blue { background: #3B82F6; }
    .ball-red { background: #EF4444; }
    .ball-gray { background: #6B7280; }
    .ball-green { background: #22C55E; }
    .ball-bonus { background: linear-gradient(135deg, #9D4EDD, #EC4899); }
    
    /* ===== 탭 & 필터 ===== */
    .tabs {
      display: flex;
      gap: 24px;
      border-bottom: 1px solid var(--border);
      margin-bottom: 20px;
    }
    
    .tab {
      padding: 12px 0;
      font-weight: 600;
      color: var(--text-muted);
      position: relative;
      cursor: pointer;
      transition: color 0.2s;
    }
    
    .tab:hover, .tab.active {
      color: var(--text-primary);
    }
    
    .tab.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--gradient-gold);
    }
    
    .filters {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 20px;
    }
    
    .filter-btn {
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      background: var(--bg-secondary);
      color: var(--text-secondary);
      border: 1px solid var(--border);
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .filter-btn:hover, .filter-btn.active {
      background: var(--gradient-gold);
      color: #000;
      border-color: transparent;
    }
    
    .sub-filters {
      display: flex;
      gap: 8px;
      margin-bottom: 20px;
    }
    
    .sub-filter {
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 600;
      background: var(--bg-secondary);
      color: var(--text-secondary);
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .sub-filter.active {
      background: var(--gold);
      color: #000;
    }
    
    /* ===== 판매점 리스트 ===== */
    .store-list {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
    }
    
    .store-list-header {
      display: grid;
      grid-template-columns: 50px 60px 1fr 80px 80px 80px;
      gap: 16px;
      padding: 12px 20px;
      background: var(--bg-secondary);
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
    }
    
    .store-row {
      display: grid;
      grid-template-columns: 50px 60px 1fr 80px 80px 80px;
      gap: 16px;
      padding: 16px 20px;
      align-items: center;
      border-bottom: 1px solid var(--border);
      transition: background 0.2s;
    }
    
    .store-row:hover {
      background: var(--bg-hover);
    }
    
    .store-row:last-child {
      border-bottom: none;
    }
    
    .store-rank {
      font-weight: 700;
      font-size: 1.1rem;
    }
    
    .store-rank.top {
      color: var(--gold);
    }
    
    .store-image {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--bg-secondary), var(--bg-hover));
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }
    
    .store-image.hot {
      background: linear-gradient(135deg, var(--red), #FF8C00);
    }
    
    .store-info {
      min-width: 0;
    }
    
    .store-name-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 4px;
      flex-wrap: wrap;
    }
    
    .store-name {
      font-weight: 600;
      font-size: 0.95rem;
    }
    
    .store-address {
      font-size: 0.85rem;
      color: var(--text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .store-wins {
      text-align: center;
    }
    
    .store-wins-count {
      font-size: 1.25rem;
      font-weight: 800;
    }
    
    .store-wins-count.gold { color: var(--gold); }
    .store-wins-count.blue { color: var(--blue); }
    
    .store-wins-label {
      font-size: 0.7rem;
      color: var(--text-muted);
    }
    
    .store-trend {
      text-align: right;
      font-size: 0.85rem;
      font-weight: 600;
    }
    
    .trend-up { color: var(--red); }
    .trend-down { color: var(--blue); }
    
    /* 빈 상태 */
    .empty-state {
      padding: 60px 20px;
      text-align: center;
      color: var(--text-muted);
    }
    
    /* 페이지네이션 */
    .pagination {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 24px;
    }
    
    .page-btn {
      padding: 10px 16px;
      background: var(--bg-secondary);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text-secondary);
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .page-btn:hover, .page-btn.active {
      background: var(--gold);
      color: #000;
      border-color: transparent;
    }
    
    .page-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
    
    /* ============================================
       상세 페이지 스타일
       ============================================ */
    
    /* 상단 판매점 정보 */
    .detail-hero {
      background: var(--bg-primary);
      border-bottom: 1px solid var(--border);
      padding: 24px 0;
    }
    
    .detail-hero-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 20px;
    }
    
    .detail-hero-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .detail-icon {
      width: 64px;
      height: 64px;
      background: var(--gradient-gold);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      box-shadow: 0 8px 32px rgba(245, 184, 0, 0.3);
    }
    
    .detail-title-area h1 {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .detail-stats {
      display: flex;
      align-items: baseline;
      gap: 16px;
    }
    
    .detail-stat-main {
      font-size: 2rem;
      font-weight: 800;
      color: var(--gold);
    }
    
    .detail-stat-sub {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--blue);
    }
    
    .detail-stat-label {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-left: 4px;
    }
    
    .detail-meta {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 8px;
      font-size: 0.9rem;
    }
    
    .detail-hero-actions {
      display: flex;
      gap: 8px;
    }
    
    .action-btn {
      width: 44px;
      height: 44px;
      background: var(--bg-secondary);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .action-btn:hover {
      background: var(--bg-hover);
    }
    
    .action-btn svg {
      width: 20px;
      height: 20px;
      color: var(--text-muted);
    }
    
    .action-btn.liked svg {
      color: var(--gold);
      fill: var(--gold);
    }
    
    /* 상세 탭 */
    .detail-tabs {
      background: var(--bg-primary);
      border-bottom: 1px solid var(--border);
    }
    
    .detail-tabs-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      gap: 24px;
    }
    
    /* 상세 컨텐츠 그리드 */
    .detail-content {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 24px;
    }
    
    /* 차트 섹션 */
    .chart-section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px;
    }
    
    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }
    
    .chart-periods {
      display: flex;
      gap: 4px;
    }
    
    .chart-period {
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .chart-period.active {
      background: var(--gold);
      color: #000;
    }
    
    .chart-legend {
      display: flex;
      gap: 16px;
      margin-bottom: 16px;
      font-size: 0.75rem;
    }
    
    .legend-item {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--text-muted);
    }
    
    .legend-dot {
      width: 10px;
      height: 10px;
      border-radius: 2px;
    }
    
    .chart-area {
      height: 200px;
      display: flex;
      align-items: flex-end;
      gap: 2px;
    }
    
    .chart-bar {
      flex: 1;
      border-radius: 2px 2px 0 0;
      min-height: 4px;
      transition: opacity 0.2s;
      cursor: pointer;
      position: relative;
    }
    
    .chart-bar:hover {
      opacity: 0.8;
    }
    
    .chart-bar.gold { background: var(--gold); }
    .chart-bar.blue { background: var(--blue); }
    .chart-bar.empty { background: var(--bg-secondary); opacity: 0.3; }
    
    .chart-labels {
      display: flex;
      justify-content: space-between;
      margin-top: 8px;
      font-size: 0.75rem;
      color: var(--text-muted);
    }
    
    /* 커뮤니티 섹션 */
    .community-section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      margin-top: 24px;
    }
    
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
    }
    
    .section-title {
      font-weight: 700;
    }
    
    .comment-item {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      transition: background 0.2s;
    }
    
    .comment-item:hover {
      background: rgba(255,255,255,0.02);
    }
    
    .comment-item:last-child {
      border-bottom: none;
    }
    
    .comment-header {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
    }
    
    .comment-avatar {
      width: 32px;
      height: 32px;
      background: var(--bg-secondary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .comment-user {
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    .comment-time {
      font-size: 0.75rem;
      color: var(--text-muted);
    }
    
    .comment-content {
      font-size: 0.9rem;
      color: var(--text-secondary);
      line-height: 1.5;
    }
    
    /* 우측: 당첨이력 & 정보 */
    .detail-sidebar {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
    
    .history-section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
    }
    
    .history-header {
      display: grid;
      grid-template-columns: 1fr 60px 1fr;
      padding: 12px 20px;
      background: var(--bg-secondary);
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-muted);
    }
    
    .history-list {
      max-height: 320px;
      overflow-y: auto;
    }
    
    .history-item {
      display: grid;
      grid-template-columns: 1fr 60px 1fr;
      padding: 12px 20px;
      border-bottom: 1px solid var(--border);
      align-items: center;
    }
    
    .history-item:last-child {
      border-bottom: none;
    }
    
    .history-round {
      font-weight: 600;
    }
    
    .history-date {
      font-size: 0.75rem;
      color: var(--text-muted);
    }
    
    .history-rank {
      text-align: center;
    }
    
    .history-prize {
      text-align: right;
      font-weight: 600;
    }
    
    /* 판매점 정보 섹션 */
    .info-section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
    }
    
    .map-placeholder {
      height: 160px;
      background: var(--bg-secondary);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    
    .map-placeholder-icon {
      font-size: 2rem;
    }
    
    .map-placeholder-text {
      font-size: 0.85rem;
      color: var(--text-muted);
    }
    
    .info-list {
      padding: 20px;
    }
    
    .info-item {
      display: flex;
      gap: 12px;
      margin-bottom: 16px;
    }
    
    .info-item:last-child {
      margin-bottom: 0;
    }
    
    .info-icon {
      font-size: 1rem;
      color: var(--text-muted);
    }
    
    .info-label {
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 2px;
    }
    
    .info-value {
      font-size: 0.85rem;
      color: var(--text-secondary);
    }
    
    .info-value.highlight {
      color: var(--blue);
    }
    
    .info-value.gold {
      color: var(--gold);
    }
    
    .directions-btn {
      display: block;
      width: calc(100% - 40px);
      margin: 0 20px 20px;
      padding: 16px;
      background: var(--gradient-gold);
      border: none;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
      color: #000;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .directions-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(245, 184, 0, 0.3);
    }
    
    /* 하단 통계 바 */
    .bottom-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(8, 11, 20, 0.95);
      backdrop-filter: blur(10px);
      border-top: 1px solid var(--border);
      z-index: 90;
    }
    
    .bottom-bar-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      height: 48px;
      display: flex;
      align-items: center;
      gap: 32px;
      font-size: 0.85rem;
    }
    
    .bottom-stat {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .bottom-stat-label {
      color: var(--text-muted);
    }
    
    .bottom-stat-value {
      font-weight: 600;
    }
    
    .bottom-stat-value.gold {
      color: var(--gold);
    }
    
    .bottom-stat-value.green {
      color: var(--green);
    }
    
    /* ===== 반응형 ===== */
    @media (max-width: 1024px) {
      .detail-content {
        grid-template-columns: 1fr;
      }
      
      .detail-sidebar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
      }
    }
    
    @media (max-width: 768px) {
      .header-nav {
        display: none;
      }
      
      .search-box {
        display: none;
      }
      
      .store-list-header,
      .store-row {
        grid-template-columns: 40px 50px 1fr 60px 60px;
      }
      
      .store-row > *:last-child {
        display: none;
      }
      
      .store-list-header > *:last-child {
        display: none;
      }
      
      .detail-hero-inner {
        flex-direction: column;
      }
      
      .detail-sidebar {
        grid-template-columns: 1fr;
      }
      
      .bottom-bar-inner {
        gap: 16px;
        font-size: 0.75rem;
        overflow-x: auto;
      }
    }
    
    @media (max-width: 480px) {
      .market-cards {
        gap: 12px;
      }
      
      .market-card {
        min-width: 140px;
        padding: 16px;
      }
      
      .filters {
        gap: 6px;
      }
      
      .filter-btn {
        padding: 6px 12px;
        font-size: 0.8rem;
      }
    }

/* ✅ 세부 지역(구) 필터 예쁘게: PC=랩 / 모바일=가로스크롤 */
.sub-filters{
  display:flex;
  flex-wrap:wrap;           /* PC: 여러 줄로 자연스럽게 */
  gap:8px;
  margin-bottom:20px;
  align-items:center;
}

.sub-filter{
  flex: 0 0 auto;           /* ✅ 억지로 줄어들지 않게 */
  white-space: nowrap;      /* ✅ 한 글자씩 줄바꿈 금지 */
  word-break: keep-all;     /* ✅ 한국어 단어 깨짐 방지 */
  padding: 8px 14px;
  border-radius: 10px;
  min-height: 36px;
  line-height: 20px;
  border: 1px solid var(--border);
  background: var(--bg-secondary);
  color: var(--text-secondary);
  transition: all .2s;
}

.sub-filter:hover{
  background: var(--bg-hover);
}

.sub-filter.active{
  background: var(--gold);
  color: #000;
  border-color: transparent;
}

/* ✅ 모바일: 가로 스크롤로 깔끔하게 */
@media (max-width: 768px){
  .sub-filters{
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 6px;
  }
  .sub-filters::-webkit-scrollbar{ height: 6px; }
  .sub-filters::-webkit-scrollbar-thumb{
    background: rgba(255,255,255,.12);
    border-radius: 999px;
  }
}

/* ✅ PC에서는 지역 버튼을 한 줄로 고정(제주도 위로) */
@media (min-width: 1024px) {
  .filters{
    flex-wrap: nowrap;          /* 한 줄 고정 */
    overflow-x: auto;           /* 버튼이 많으면 가로 스크롤 */
    -webkit-overflow-scrolling: touch;
    gap: 15px;                   /* 살짝 촘촘하게 */
    padding-bottom: 6px;        /* 스크롤바 공간 */
    scrollbar-width: none;      /* Firefox 스크롤바 숨김 */
  }
  .filters::-webkit-scrollbar{  /* Chrome/Safari 스크롤바 숨김 */
    display: none;
  }

  .filter-btn{
    flex: 0 0 auto;             /* 줄어들며 깨지지 않게 */
    white-space: nowrap;        /* 글자 줄바꿈 방지 */
    padding: 7px 12px;          /* 한 줄에 더 잘 들어가게 */
    font-size: 0.82rem;
  }
}

  </style>
</head>
<body>

<!-- ========== Header ========== -->
<header class="header">
  <div class="header-inner">
    <a href="/" class="logo">
      <div class="logo-icon">🎰</div>
      <span class="logo-text">오늘로또</span>
    </a>
    
    <nav class="header-nav">
	<!--
      <a href="/" class="nav-link active">홈</a>
      <a href="/stores/<?= ($round>0 ? '?round='.(int)$round : ($tab!=='default' ? '?tab='.urlencode($tab) : '')) ?>" class="nav-link">당첨점</a>

      <a href="/draw/latest" class="nav-link">회차별 결과</a>
      <a href="/auth.php" class="nav-link">AI 분석</a>
	-->
    </nav>
    
    <div class="header-actions">
      <form class="search-box" method="get" action="<?= htmlspecialchars(stores_list_url($region1, $region2, $region3)) ?>">
 
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="판매점 검색(이름/주소)" />
        <?php if ($tab !== 'default'): ?>
          <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
        <?php endif; ?>
        <?php if ($round > 0): ?>
          <input type="hidden" name="round" value="<?= (int)$round ?>">
        <?php endif; ?>
      </form>

      <a href="<?= stores_list_url('', '', '', ['tab'=>$tab, 'round'=>($round>0?$round:'')]) ?>" class="btn btn-primary">당첨점</a>
    </div>
  </div>
</header>

<?php if ($page_type === 'detail' && $store): ?>
<!-- ============================================
     상세 페이지
     ============================================ -->

<!-- 판매점 Hero -->
<div class="detail-hero">
  <div class="detail-hero-inner">
    <div class="detail-hero-left">
      <div class="detail-icon">🏆</div>
      <div class="detail-title-area">
        <h1>
		  <?= htmlspecialchars(stores_display_name($store['store_name'])) ?>
          <span class="badge badge-purple"><?= htmlspecialchars($store['region1']) ?> <?= htmlspecialchars($store['region2']) ?></span>
        </h1>
        <div class="detail-stats">
          <span class="detail-stat-main"><?= $store['wins_1st'] ?>회</span>
          <span class="detail-stat-label">1등 당첨</span>
          <span class="detail-stat-sub"><?= $store['wins_2nd'] ?>회</span>
          <span class="detail-stat-label">2등</span>
        </div>
        <div class="detail-meta">
          <span style="color: var(--text-muted)">최근 당첨</span>
          <?php if (!empty($win_history)): ?>
          <span style="color: var(--gold); font-weight: 600;"><?= $win_history[0]['draw_no'] ?>회</span>
          <?php endif; ?>
          <?php 
          // 총 당첨금 계산
          $total_prize = 0;
          foreach ($win_history as $w) {
              $total_prize += (int)$w['prize_amount'];
          }
          if ($total_prize > 0):
          ?>
          <span class="badge badge-green">누적 <?= number_format($total_prize / 100000000, 1) ?>억원</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <div class="detail-hero-actions">
      <div class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
      </div>
      <div class="action-btn liked">
        <svg viewBox="0 0 24 24">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
      </div>
      <div class="action-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
        </svg>
      </div>
    </div>
  </div>
</div>

<!-- 탭 -->
<div class="detail-tabs">
  <div class="detail-tabs-inner">
    <div class="tabs" style="border-bottom: none; margin-bottom: 0;">
      <a href="?tab=chart" class="tab active">차트 · 당첨이력</a>
      <a href="?tab=info" class="tab">상세정보</a>
      <a href="?tab=news" class="tab">뉴스 · 공시</a>
      <a href="?tab=community" class="tab">커뮤니티</a>
    </div>
  </div>
</div>

<main class="main">
  <div class="detail-content">
    <!-- 좌측: 차트 + 커뮤니티 -->
    <div>
      <!-- 차트 섹션 -->
      <div class="chart-section">
        <div class="chart-header">
          <div class="chart-periods">
            <span class="chart-period">1개월</span>
            <span class="chart-period">3개월</span>
            <span class="chart-period">6개월</span>
            <span class="chart-period active">1년</span>
            <span class="chart-period">전체</span>
          </div>
        </div>
        
        <div class="chart-legend">
          <div class="legend-item">
            <div class="legend-dot" style="background: var(--gold)"></div>
            <span>1등 당첨</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot" style="background: var(--blue)"></div>
            <span>2등 당첨</span>
          </div>
        </div>
        
        <div class="chart-area">
          <?php
          // 최근 50회차 차트 데이터 생성
          $win_rounds = [];
          foreach ($win_history as $w) {
              $win_rounds[$w['draw_no']] = (int)$w['rank'];
          }
          
          for ($i = 49; $i >= 0; $i--):
              $r = $latest_round - $i;
              $rank = $win_rounds[$r] ?? null;
              $height = ($rank === 1) ? 100 : (($rank === 2) ? 50 : 5);
              $class  = ($rank === 1) ? 'gold' : (($rank === 2) ? 'blue' : 'empty');
          ?>
          <div class="chart-bar <?= $class ?>" style="height: <?= $height ?>%" title="<?= $r ?>회<?= ($rank ? " {$rank}등" : '') ?>"></div>
          <?php endfor; ?>
        </div>
        
        <div class="chart-labels">
          <span><?= $latest_round - 49 ?>회</span>
          <span><?= $latest_round ?>회</span>
        </div>
      </div>
      
      <!-- 커뮤니티 섹션 -->
      <div class="community-section">
        <div class="section-header">
          <h3 class="section-title">커뮤니티</h3>
          <select style="background: var(--bg-secondary); border: none; color: var(--text-secondary); padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
            <option>최신순</option>
            <option>인기순</option>
          </select>
        </div>
        
        <div class="comment-item">
          <div class="comment-header">
            <div class="comment-avatar">😀</div>
            <span class="comment-user">로또왕</span>
            <span class="badge badge-gold" style="font-size: 0.7rem;">신대륙파</span>
            <span class="comment-time">방금 전</span>
          </div>
          <p class="comment-content">여기서 1등 나왔다는데 진짜임?? 대박 ㅋㅋ</p>
        </div>
        
        <div class="comment-item">
          <div class="comment-header">
            <div class="comment-avatar">🎯</div>
            <span class="comment-user">행운드림</span>
            <span class="comment-time">3분 전</span>
          </div>
          <p class="comment-content">저번주에 갔는데 사장님이 친절하심</p>
        </div>
        
        <div class="comment-item">
          <div class="comment-header">
            <div class="comment-avatar">🔥</div>
            <span class="comment-user">복권매니아</span>
            <span class="badge badge-purple" style="font-size: 0.7rem;">고수</span>
            <span class="comment-time">12분 전</span>
          </div>
          <p class="comment-content">주차는 근처 공영주차장 이용하세요</p>
        </div>
        
        <div style="padding: 12px 20px; text-align: center; border-top: 1px solid var(--border);">
          <a href="#" style="color: var(--gold); font-size: 0.9rem; font-weight: 600;">전체 보기 →</a>
        </div>
      </div>
    </div>
    
    <!-- 우측: 당첨이력 + 정보 -->
    <div class="detail-sidebar">
      <!-- 당첨 이력 -->
      <div class="history-section">
        <div class="section-header">
          <h3 class="section-title">당첨 이력</h3>
          <div style="display: flex; gap: 4px;">
            <span class="sub-filter active">전체</span>
            <span class="sub-filter">1등만</span>
          </div>
        </div>
        
        <div class="history-header">
          <div>회차</div>
          <div style="text-align: center;">등수</div>
          <div style="text-align: right;">당첨금</div>
        </div>
        
        <div class="history-list">
          <?php if (empty($win_history)): ?>
          <div style="padding: 40px 20px; text-align: center; color: var(--text-muted);">
            당첨 이력이 없습니다.
          </div>
          <?php else: ?>
          <?php foreach ($win_history as $win): ?>
          <div class="history-item">
            <div>
              <div class="history-round"><?= $win['draw_no'] ?>회</div>
              <div class="history-date"><?= $win['draw_date'] ?? '' ?></div>
            </div>
            <div class="history-rank">
              <span class="badge <?= $win['rank'] == 1 ? 'badge-gold' : 'badge-blue' ?>"><?= $win['rank'] ?>등</span>
            </div>
            <div class="history-prize" style="color: <?= $win['rank'] == 1 ? 'var(--gold)' : 'var(--blue)' ?>">
              <?= number_format($win['prize_amount']) ?>원
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- 판매점 정보 -->
      <div class="info-section">
        <div class="section-header">
          <h3 class="section-title">판매점 정보</h3>
        </div>
        
        <div class="map-placeholder">
          <div class="map-placeholder-icon">📍</div>
          <div class="map-placeholder-text">지도를 보려면 로그인이 필요해요</div>
          <a href="/auth.php" class="btn btn-secondary" style="margin-top: 8px; padding: 8px 16px; font-size: 0.85rem;">로그인하기</a>
        </div>
        
        <div class="info-list">
          <div class="info-item">
            <span class="info-icon">📍</span>
            <div>
              <div class="info-label">주소</div>
              <div class="info-value"><?= htmlspecialchars($store['address']) ?></div>
            </div>
          </div>
          
          <?php if (!empty($store['phone'])): ?>
          <div class="info-item">
            <span class="info-icon">📞</span>
            <div>
              <div class="info-label">전화번호</div>
              <div class="info-value highlight"><?= htmlspecialchars($store['phone']) ?></div>
            </div>
          </div>
          <?php endif; ?>
          
          <?php if (!empty($store['opening_hours'])): ?>
          <div class="info-item">
            <span class="info-icon">🕐</span>
            <div>
              <div class="info-label">영업시간</div>
              <div class="info-value"><?= htmlspecialchars($store['opening_hours']) ?></div>
            </div>
          </div>
          <?php endif; ?>
          
          <?php if (!empty($store['review_rating'])): ?>
          <div class="info-item">
            <span class="info-icon">⭐</span>
            <div>
              <div class="info-label">리뷰</div>
              <div class="info-value gold"><?= number_format($store['review_rating'], 1) ?> (<?= number_format($store['review_count']) ?>개)</div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        
        <button class="directions-btn" onclick="window.open('https://map.kakao.com/link/to/<?= urlencode(stores_display_name($store['store_name'])) ?>,<?= $store['latitude'] ?>,<?= $store['longitude'] ?>')">
          🗺️ 길찾기
        </button>
      </div>
    </div>
  </div>
</main>

<?php else: ?>
<!-- ============================================
     리스트 페이지
     ============================================ -->

<main class="main">
  <!-- 마켓 배너 -->
  <div class="market-banner">
    <div class="market-badge">
      <span class="badge badge-gold">이번주</span>
      <span style="color: var(--text-secondary)">제 <strong style="color: var(--gold)"><?= $latest_round ?></strong>회 당첨결과</span>
      <span class="badge badge-green">NEW</span>
    </div>
    
    <div class="market-cards">
      <!-- 당첨번호 카드 -->
      <div class="market-card" style="min-width: 280px;">
        <div class="market-card-label">당첨번호</div>
        <div class="lotto-balls" style="margin-top: 8px;">
          <?php
          foreach ($banner_numbers as $num):
              $class = $num <= 10 ? 'ball-yellow' : ($num <= 20 ? 'ball-blue' : ($num <= 30 ? 'ball-red' : ($num <= 40 ? 'ball-gray' : 'ball-green')));
          ?>
          <div class="lotto-ball <?= $class ?>"><?= $num ?></div>
          <?php endforeach; ?>
          <span style="margin: 0 4px; color: var(--text-muted);">+</span>
          <div class="lotto-ball ball-bonus"><?= (int)$banner_bonus ?></div>
        </div>
      </div>
      
      <div class="market-card">
        <div class="market-card-label">1등 당첨금</div>
        <div class="market-card-value" style="color: var(--gold)">
          <?= ($banner_first_each ? fmt_eok($banner_first_each, 0) . '원' : '-') ?>
        </div>
        <div class="market-card-sub" style="color: var(--gold-light)">
          <?= ($banner_first_winners !== null ? number_format($banner_first_winners) . '명 당첨' : '-') ?>
        </div>
      </div>
      
      <div class="market-card">
        <div class="market-card-label">총 판매금액</div>
        <div class="market-card-value"><?= ($banner_total_sales ? fmt_eok($banner_total_sales, 0) : '-') ?></div>
        <div class="market-card-sub" style="color: var(--blue)">
          <?php if ($banner_sales_delta !== null): ?>
            <?= ($banner_sales_delta >= 0 ? '+' : '') . number_format($banner_sales_delta, 1) ?>% 전주대비
          <?php else: ?>
            전주대비 -
          <?php endif; ?>
        </div>
      </div>
      
      <div class="market-card">
        <div class="market-card-label">다음 추첨</div>
        <div class="market-card-value" style="color: var(--blue)"><?= htmlspecialchars($next_draw_left) ?></div>
        <div class="market-card-sub" style="color: var(--text-muted)"><?= htmlspecialchars($next_draw_label) ?></div>     
      </div>
      
      <a href="/auth.php" class="market-card" style="background: linear-gradient(145deg, var(--bg-card), rgba(157, 78, 221, 0.1)); border-color: rgba(157, 78, 221, 0.3);">
        <div class="market-card-label">AI 번호 추천</div>
        <div class="market-card-value" style="color: var(--purple); font-size: 1.1rem;">분석 보기 →</div>
        <div class="market-card-sub" style="color: var(--purple)">10가지 알고리즘</div>
      </a>
    </div>
  </div>
  
  <!-- 탭 -->
  <div class="tabs">
    <a href="<?= stores_list_url('', '', '', []) ?>"
       class="tab <?= (!$round && $tab === 'default') ? 'active' : '' ?>">전체 명당</a>

    <a href="<?= stores_list_url('', '', '', ['tab'=>'hot']) ?>"
       class="tab <?= ($tab === 'hot') ? 'active' : '' ?>">HOT 판매점</a>

    <a href="<?= stores_list_url('', '', '', ['tab'=>'predict']) ?>"
       class="tab <?= ($tab === 'predict') ? 'active' : '' ?>">명당 예측</a>

    <a href="/draw/latest"
       class="tab <?= ($round > 0) ? 'active' : '' ?>">회차별 결과</a>

    <a href="/auth.php" class="tab">AI 예측</a>
  </div>
  
  <!-- 지역 필터 -->
  <div class="filters">
    <a href="<?= stores_list_url('', '', '', [
          'tab'   => $tab,
          'round' => ($round > 0 ? $round : '')
        ]) ?>" class="filter-btn <?= !$region1 ? 'active' : '' ?>">전체</a>
    <?php foreach (array_keys($regions) as $r): ?>
    <a href="<?= stores_list_url($r, '', '', [
          'tab'   => $tab,
          'round' => ($round > 0 ? $round : '')
        ]) ?>" class="filter-btn <?= $region1 === $r ? 'active' : '' ?>"><?= $r ?></a>
    <?php endforeach; ?>
  </div>
  
  <?php if ($region1 && isset($regions[$region1]) && !empty($regions[$region1])): ?>
  <!-- 세부 지역 필터 -->
  <div class="sub-filters">
    <a href="<?= stores_list_url($region1, '', '', [
          'tab'   => $tab,
          'round' => ($round > 0 ? $round : '')
        ]) ?>" class="sub-filter <?= !$region2 ? 'active' : '' ?>">전체</a>
    <?php foreach ($regions[$region1] as $r2): ?>
    <a href="<?= stores_list_url($region1, $r2, '', [
          'tab'   => $tab,
          'round' => ($round > 0 ? $round : '')
        ]) ?>" class="sub-filter <?= $region2 === $r2 ? 'active' : '' ?>"><?= $r2 ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  
  <!-- 결과 카운트 -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <p style="color: var(--text-muted); font-size: 0.9rem;">
      총 <strong style="color: var(--gold)"><?= number_format($total_count) ?></strong>개 판매점
    </p>
    <div style="display: flex; align-items: center; gap: 8px;">
      <div style="width: 8px; height: 8px; background: var(--gold); border-radius: 50%; animation: pulse 2s infinite;"></div>
      <span style="color: var(--text-muted); font-size: 0.85rem;">실시간</span>
    </div>
  </div>
  
  <!-- 판매점 리스트 -->
  <div class="store-list">
    <div class="store-list-header">
      <div>순위</div>
      <div></div>
      <div>판매점</div>
      <div style="text-align: center;">1등</div>
      <div style="text-align: center;">2등</div>
      <div style="text-align: right;"><?= ($tab==='predict' ? '예측' : '등락') ?></div>
    </div>
    
    <?php if (empty($stores)): ?>
    <div class="empty-state">
      <div style="font-size: 3rem; margin-bottom: 16px;">🔍</div>
      <p>조건에 맞는 판매점이 없습니다.</p>
    </div>
    <?php else: ?>
    <?php foreach ($stores as $i => $s): 
      // ✅ 회차별 당첨점에서는 주소 그룹의 rank_sum을 "순위 숫자 합산"으로 표시
      $rank = ($round > 0 && isset($s['rank_sum'])) ? (int)$s['rank_sum'] : ($offset + $i + 1);
      $is_hot = isset($s['recent_wins']) && $s['recent_wins'] >= 2;
	  $is_pick = ($tab === 'predict' && isset($s['pred_score']) && (float)$s['pred_score'] >= 3.0);
      $store_url = stores_detail_url($s); // ✅ 대체 함수 사용
    ?>
    <a href="<?= $store_url ?>" class="store-row">
      <div class="store-rank <?= $rank <= 3 ? 'top' : '' ?>"><?= $rank ?></div>
      <div class="store-image <?= ($is_hot||$is_pick) ? 'hot' : '' ?>"><?= ($is_hot||$is_pick) ? '🔥' : '🏪' ?></div>
      <div class="store-info">
        <div class="store-name-row">
          <?php if (!empty($s['region1'])): ?>
          <span class="badge badge-purple"><?= htmlspecialchars($s['region1']) ?></span>
          <?php endif; ?>
		  <span class="store-name"><?= htmlspecialchars(stores_display_name($s['store_name'])) ?></span>

          <?php if ($tab === 'predict'): ?>
            <?php if (isset($s['recent_wins'])): ?>
              <span class="badge badge-blue">1년내 <?= (int)$s['recent_wins'] ?>회</span>
            <?php endif; ?>
            <?php if (!empty($s['last_win_round'])): ?>
              <span class="badge badge-green">최근 <?= (int)$s['last_win_round'] ?>회</span>
            <?php endif; ?>
          <?php endif; ?>

          <?php if ($is_hot): ?>
          <span class="badge" style="background: var(--gradient-gold); color: #000;">HOT</span>
          <?php endif; ?>

          <?php if ($is_pick): ?>
          <span class="badge badge-green">PICK</span>
          <?php endif; ?>

          <?php if ($round > 0 && (isset($s['win1_cnt']) || isset($s['win2_cnt']))): ?>
            <?php if ((int)($s['win1_cnt'] ?? 0) > 0): ?>
              <span class="badge badge-gold">1등 ×<?= (int)$s['win1_cnt'] ?></span>
            <?php endif; ?>
            <?php if ((int)($s['win2_cnt'] ?? 0) > 0): ?>
              <span class="badge badge-blue">2등 ×<?= (int)$s['win2_cnt'] ?></span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <div class="store-address"><?= htmlspecialchars($s['address']) ?></div>
      </div>
      <div class="store-wins">
        <div class="store-wins-count gold">
          <?= ($round > 0 ? (int)($s['win1_cnt'] ?? 0) : (int)$s['wins_1st']) ?>
        </div>
        <div class="store-wins-label">1등</div>
      </div>
      <div class="store-wins">
        <div class="store-wins-count blue">
          <?= ($round > 0 ? (int)($s['win2_cnt'] ?? 0) : (int)$s['wins_2nd']) ?>
        </div>
        <div class="store-wins-label">2등</div>
      </div>

      <div class="store-trend">
      <?php if ($tab === 'predict' && isset($s['pred_score'])): ?>
          <?php
            $ps = (float)$s['pred_score'];
            $trend_class = ($ps >= 6.0) ? 'trend-up' : 'trend-down';
          ?>
          <span class="<?= $trend_class ?>"><?= number_format($ps, 2) ?>점</span>
      <?php else: ?>
          <?php 
            // (기존) 임의의 트렌드 표시
            $trend = rand(-5, 10) / 10;
            $trend_class = $trend >= 0 ? 'trend-up' : 'trend-down';
            $trend_sign = $trend >= 0 ? '+' : '';
          ?>
          <span class="<?= $trend_class ?>"><?= $trend_sign ?><?= number_format($trend, 1) ?>%</span>
      <?php endif; ?>
      </div>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
  
  <!-- 페이지네이션 -->
  <?php if ($is_region_paging && $total_pages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="<?= stores_list_url($region1, $region2, $region3, [
            'page'  => $page - 1,
            'round' => ($round > 0 ? $round : ''),
            'tab'   => $tab
          ]) ?>" class="page-btn">← 이전</a>
    <?php endif; ?>

    <?php 
      $start = max(1, $page - 2);
      $end = min($total_pages, $page + 2);
      for ($p = $start; $p <= $end; $p++): 
    ?>
      <a href="<?= stores_list_url($region1, $region2, $region3, [
            'page'  => $p,
            'round' => ($round > 0 ? $round : ''),
            'tab'   => $tab
          ]) ?>" class="page-btn <?= $p == $page ? 'active' : '' ?>">
        <?= $p ?>
      </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
      <a href="<?= stores_list_url($region1, $region2, $region3, [
            'page'  => $page + 1,
            'round' => ($round > 0 ? $round : ''),
            'tab'   => $tab
          ]) ?>" class="page-btn">다음 →</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

    
</main>

<?php endif; ?>

<!-- 하단 통계 바 -->
<div class="bottom-bar">
  <div class="bottom-bar-inner">
    <div class="bottom-stat">
      <span class="bottom-stat-label">통계</span>
    </div>
	<?php
		$total_first_wins = null;

		if (function_exists('sql_query')) {
			$row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store_win WHERE rank = 1");
			$total_first_wins = $row ? (int)$row['cnt'] : null;
		}
	?>
    <div class="bottom-stat">
      <span class="bottom-stat-label">전국 1등</span>
      <span class="bottom-stat-value"><?= ($total_first_wins !== null ? number_format($total_first_wins).'회' : '-') ?></span>
    </div>
    <div class="bottom-stat">
      <span class="bottom-stat-label">이번주 판매</span>
      <span class="bottom-stat-value"><?= ($banner_total_sales ? fmt_eok($banner_total_sales, 0) : '-') ?></span>
      <span class="bottom-stat-value green">
        <?= ($banner_sales_delta !== null ? (($banner_sales_delta >= 0 ? '+' : '') . number_format($banner_sales_delta, 1) . '%') : '-') ?>
      </span>
    </div>
    <div class="bottom-stat">
      <span class="bottom-stat-label">1등 당첨금</span>
      <span class="bottom-stat-value"><?= ($banner_first_each ? fmt_eok($banner_first_each, 0) : '-') ?></span>
    </div>
    <div class="bottom-stat">
      <span class="bottom-stat-label">다음 추첨</span>
      <span class="bottom-stat-value gold"><?= htmlspecialchars($next_draw_left) ?></span>
    </div>
  </div>
</div>

<style>
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
</style>

</body>
</html>
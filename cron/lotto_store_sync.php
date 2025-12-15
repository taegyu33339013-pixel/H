<?php
/**
 * 로또 당첨점 정보 동기화 크론잡
 * 
 * 동행복권에서 회차별 1등/2등 당첨점 정보를 수집하여 DB에 저장
 * 
 * 사용법:
 *   php lotto_store_sync.php                    # 최신 회차만 동기화
 *   php lotto_store_sync.php 1202               # 특정 회차만 동기화
 *   php lotto_store_sync.php 1 1202             # 1~1202회 전체 동기화
 *   php lotto_store_sync.php all                # 전체 판매점 수집 (1회 실행)
 * 
 * 크론탭 설정 (매주 일요일 새벽 2시):
 *   0 2 * * 0 php /path/to/cron/lotto_store_sync.php >> /var/log/lotto_sync.log 2>&1
 */

// CLI 모드 체크
$is_cli = (php_sapi_name() === 'cli');

// 실행 시간 제한 해제 (CLI)
if ($is_cli) {
    set_time_limit(0);
    ini_set('memory_limit', '512M');
}

// 그누보드 환경 로드
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
define('G5_PATH', $_SERVER['DOCUMENT_ROOT']);

$common_path = G5_PATH . '/common.php';
if (!file_exists($common_path)) {
    die("Error: common.php not found\n");
}
include_once($common_path);

// 판매점 라이브러리 로드
$store_lib = G5_PATH . '/lib/lotto_store.lib.php';
if (!file_exists($store_lib)) {
    die("Error: lotto_store.lib.php not found\n");
}
include_once($store_lib);

// 로그 함수
function sync_log($message) {
    global $is_cli;
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] {$message}";
    
    if ($is_cli) {
        echo $log . "\n";
    }
    
    // 파일 로그
    $log_file = G5_PATH . '/data/log/lotto_store_sync_' . date('Ymd') . '.log';
    @file_put_contents($log_file, $log . "\n", FILE_APPEND);
}

// 테이블 존재 여부 확인
function check_tables() {
    $tables = ['g5_lotto_store', 'g5_lotto_store_win'];
    
    foreach ($tables as $table) {
        $result = sql_query("SHOW TABLES LIKE '{$table}'", false);
        if (!$result || sql_num_rows($result) == 0) {
            return false;
        }
    }
    
    return true;
}

// 테이블 생성
function create_tables() {
    // g5_lotto_store 테이블
    sql_query("CREATE TABLE IF NOT EXISTS `g5_lotto_store` (
        `store_id` int(11) NOT NULL AUTO_INCREMENT,
        `store_name` varchar(100) NOT NULL COMMENT '판매점 이름',
        `address` varchar(255) NOT NULL COMMENT '주소',
        `region1` varchar(20) DEFAULT NULL COMMENT '시/도',
        `region2` varchar(50) DEFAULT NULL COMMENT '시/군/구',
        `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동',
        `phone` varchar(20) DEFAULT NULL COMMENT '전화번호',
        `opening_hours` varchar(100) DEFAULT NULL COMMENT '영업시간 (예: 09:00-22:00)',
        `store_image` varchar(255) DEFAULT NULL COMMENT '판매점 이미지 URL',
        `latitude` decimal(10,7) DEFAULT NULL COMMENT '위도',
        `longitude` decimal(10,7) DEFAULT NULL COMMENT '경도',
        `wins_1st` int(11) DEFAULT 0 COMMENT '누적 1등 당첨 횟수',
        `wins_2nd` int(11) DEFAULT 0 COMMENT '누적 2등 당첨 횟수',
        `review_rating` decimal(3,2) DEFAULT NULL COMMENT '평균 리뷰 평점 (0.00-5.00)',
        `review_count` int(11) DEFAULT 0 COMMENT '리뷰 개수',
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`store_id`),
        KEY `idx_name` (`store_name`),
        KEY `idx_region` (`region1`, `region2`),
        KEY `idx_region3` (`region3`),
        KEY `idx_wins` (`wins_1st` DESC, `wins_2nd` DESC),
        KEY `idx_address` (`address`(100))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='로또 판매점 정보'");
    
    // 기존 테이블에 region3 컬럼 추가 (없는 경우)
    $check_region3 = sql_query("SHOW COLUMNS FROM g5_lotto_store LIKE 'region3'", false);
    if (!$check_region3 || sql_num_rows($check_region3) == 0) {
        sql_query("ALTER TABLE g5_lotto_store 
                   ADD COLUMN `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동' AFTER `region2`");
        sync_log("region3 컬럼 추가 완료");
    }
    
    // region3 인덱스 추가 (없는 경우)
    $check_idx_region3 = sql_query("SHOW INDEXES FROM g5_lotto_store WHERE Key_name = 'idx_region3'", false);
    if (!$check_idx_region3 || sql_num_rows($check_idx_region3) == 0) {
        sql_query("ALTER TABLE g5_lotto_store ADD INDEX `idx_region3` (`region3`)");
        sync_log("idx_region3 인덱스 추가 완료");
    }
    
    // 이미지 및 추가 필드 추가 (없는 경우)
    $additional_fields = [
        'opening_hours' => "ADD COLUMN opening_hours varchar(100) DEFAULT NULL COMMENT '영업시간 (예: 09:00-22:00)' AFTER phone",
        'store_image' => "ADD COLUMN store_image varchar(255) DEFAULT NULL COMMENT '판매점 이미지 URL' AFTER opening_hours",
        'review_rating' => "ADD COLUMN review_rating decimal(3,2) DEFAULT NULL COMMENT '평균 리뷰 평점 (0.00-5.00)' AFTER wins_2nd",
        'review_count' => "ADD COLUMN review_count int(11) DEFAULT 0 COMMENT '리뷰 개수' AFTER review_rating"
    ];
    
    foreach ($additional_fields as $field => $sql_part) {
        $check = sql_query("SHOW COLUMNS FROM g5_lotto_store LIKE '{$field}'", false);
        if (!$check || sql_num_rows($check) == 0) {
            sql_query("ALTER TABLE g5_lotto_store {$sql_part}");
            sync_log("{$field} 컬럼 추가 완료");
        }
    }
    
    // g5_lotto_store_win 테이블
    sql_query("CREATE TABLE IF NOT EXISTS `g5_lotto_store_win` (
        `win_id` int(11) NOT NULL AUTO_INCREMENT,
        `draw_no` int(11) NOT NULL COMMENT '회차',
        `store_id` int(11) NOT NULL COMMENT '판매점 ID',
        `rank` tinyint(1) NOT NULL COMMENT '당첨 등수 (1, 2)',
        `win_type` enum('auto','manual','semi') DEFAULT 'auto' COMMENT '자동/수동/반자동',
        `prize_amount` bigint(20) DEFAULT 0 COMMENT '당첨금',
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`win_id`),
        UNIQUE KEY `uk_draw_store_rank` (`draw_no`, `store_id`, `rank`),
        KEY `idx_draw_no` (`draw_no`),
        KEY `idx_store_id` (`store_id`),
        KEY `idx_rank` (`rank`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회차별 당첨점 기록'");
    
    // g5_lotto_draw 테이블에 컬럼 추가 (없으면)
    $columns = ['total_sales', 'first_winners', 'first_prize_each', 'second_winners', 'second_prize_each', 'third_winners', 'third_prize_each'];
    
    foreach ($columns as $col) {
        $check = sql_query("SHOW COLUMNS FROM g5_lotto_draw LIKE '{$col}'", false);
        if ($check && sql_num_rows($check) == 0) {
            $type = ($col == 'total_sales' || strpos($col, 'prize') !== false) ? 'BIGINT(20)' : 'INT(11)';
            sql_query("ALTER TABLE g5_lotto_draw ADD COLUMN `{$col}` {$type} DEFAULT 0");
        }
    }
    
    sync_log("테이블 생성/확인 완료");
}

// 메인 실행
sync_log("=== 로또 판매점/당첨점 동기화 시작 ===");

// 테이블 확인 및 생성
if (!check_tables()) {
    sync_log("테이블이 없습니다. 생성합니다...");
    create_tables();
}

// 최신 회차 조회
$latest = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($latest['max_round'] ?? 0);

if ($max_round == 0) {
    sync_log("Error: g5_lotto_draw 테이블에 데이터가 없습니다.");
    exit(1);
}

sync_log("최신 회차: {$max_round}회");

// 인자 파싱
$mode = 'latest'; // 기본: 최신 회차만
$start_round = $max_round;
$end_round = $max_round;

if (isset($argv[1])) {
    if ($argv[1] === 'all') {
        // 전체 판매점 수집 모드
        $mode = 'all_stores';
    } elseif (is_numeric($argv[1])) {
        if (isset($argv[2]) && is_numeric($argv[2])) {
            // 범위 동기화: php script.php 1 1202
            $mode = 'range';
            $start_round = (int)$argv[1];
            $end_round = (int)$argv[2];
        } else {
            // 단일 회차: php script.php 1202
            $mode = 'single';
            $start_round = (int)$argv[1];
            $end_round = (int)$argv[1];
        }
    }
}

// 범위 검증
if ($start_round < 1) $start_round = 1;
if ($end_round > $max_round) $end_round = $max_round;
if ($start_round > $end_round) {
    $tmp = $start_round;
    $start_round = $end_round;
    $end_round = $tmp;
}

// 실행
$total_first = 0;
$total_second = 0;
$processed = 0;
$errors = 0;

if ($mode === 'all_stores') {
    // 전체 판매점 수집 (지역별)
    sync_log("=== 전체 판매점 수집 모드 ===");
    sync_log("주의: 이 작업은 약 30분~1시간이 소요됩니다.");
    
    $result = li_sync_all_stores();
    
    sync_log("=== 전체 판매점 수집 완료 ===");
    sync_log("총 {$result['total']}개 판매점 저장");
    
    foreach ($result['by_region'] as $region => $count) {
        sync_log("  - {$region}: {$count}개");
    }
    
} else {
    // 당첨점 동기화
    sync_log("=== 당첨점 동기화 모드 ===");
    sync_log("범위: {$start_round}회 ~ {$end_round}회");
    
    for ($round = $start_round; $round <= $end_round; $round++) {
        sync_log("[{$round}회] 처리 중...");
        
        try {
            $saved = li_sync_draw_winning_stores($round);
            
            $total_first += $saved['first'];
            $total_second += $saved['second'];
            $processed++;
            
            sync_log("[{$round}회] 1등 {$saved['first']}개, 2등 {$saved['second']}개 저장");
            
            // API 요청 간격 (0.5초)
            usleep(500000);
            
        } catch (Exception $e) {
            $errors++;
            sync_log("[{$round}회] 오류: " . $e->getMessage());
        }
    }
    
    sync_log("=== 당첨점 동기화 완료 ===");
    sync_log("처리 회차: {$processed}개");
    sync_log("1등 당첨점: {$total_first}개");
    sync_log("2등 당첨점: {$total_second}개");
    sync_log("오류: {$errors}개");
}

// 통계 출력
$total_stores = li_count_all_stores();
$total_first_wins = li_count_first_winners();
sync_log("=== 현재 DB 통계 ===");
sync_log("전체 판매점: {$total_stores}개");
sync_log("전체 1등 당첨: {$total_first_wins}회");

sync_log("=== 동기화 종료 ===");

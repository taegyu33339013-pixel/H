<?php
/**
 * 카카오 API를 사용한 판매점 정보 보강 스크립트
 * 
 * 사용법:
 *   php kakao_store_enrich.php                    # 위도/경도가 없는 판매점 100개 처리
 *   php kakao_store_enrich.php 500               # 500개 처리
 *   php kakao_store_enrich.php all                # 전체 처리 (주의: 시간 오래 걸림)
 * 
 * API 할당량:
 * - 무료: 월 300,000건
 * - 권장: 일일 10,000건 이하
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

// 라이브러리 로드
$store_lib = G5_PATH . '/lib/lotto_store.lib.php';
if (!file_exists($store_lib)) {
    die("Error: lotto_store.lib.php not found\n");
}
include_once($store_lib);

$kakao_lib = G5_PATH . '/lib/kakao_api.lib.php';
if (!file_exists($kakao_lib)) {
    die("Error: kakao_api.lib.php not found\n");
}
include_once($kakao_lib);

// 로그 함수
function enrich_log($message) {
    global $is_cli;
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] {$message}";
    
    if ($is_cli) {
        echo $log . "\n";
    }
    
    // 파일 로그
    $log_file = G5_PATH . '/data/log/kakao_enrich_' . date('Ymd') . '.log';
    @file_put_contents($log_file, $log . "\n", FILE_APPEND);
}

// API 키 확인
$api_key = li_get_kakao_api_key();
if (empty($api_key)) {
    enrich_log("Error: 카카오 API 키가 설정되지 않았습니다.");
    enrich_log("data/kakao_config.php 파일을 생성하고 API 키를 설정하세요.");
    exit(1);
}

enrich_log("=== 카카오 API 판매점 정보 보강 시작 ===");
enrich_log("API 키 확인 완료");

// 처리할 개수 결정
$limit = 100; // 기본값
if (isset($argv[1])) {
    if ($argv[1] === 'all') {
        $limit = 999999; // 전체 처리
    } elseif (is_numeric($argv[1])) {
        $limit = (int)$argv[1];
    }
}

// 위도/경도가 없는 판매점 조회
$where = "latitude IS NULL OR longitude IS NULL";
$sql = "SELECT store_id, store_name, address, phone FROM g5_lotto_store 
        WHERE {$where} 
        ORDER BY wins_1st DESC, store_id ASC 
        LIMIT {$limit}";

$result = sql_query($sql);
$stores = [];

while ($row = sql_fetch_array($result)) {
    $stores[] = $row;
}

$total = count($stores);
enrich_log("처리 대상: {$total}개");

if ($total == 0) {
    enrich_log("처리할 데이터가 없습니다.");
    exit(0);
}

// 배치 처리
$success = 0;
$failed = 0;
$updated = 0;

foreach ($stores as $store) {
    $store_id = (int)$store['store_id'];
    enrich_log("[{$store_id}] 처리 중: {$store['store_name']}");
    
    try {
        // 카카오 API로 정보 수집
        $enriched = li_kakao_enrich_store_data($store);
        
        // 데이터베이스 업데이트
        if (li_kakao_update_store($store_id, $enriched)) {
            $updated++;
            enrich_log("[{$store_id}] 업데이트 완료");
            
            if ($enriched['latitude']) {
                enrich_log("  → 좌표: {$enriched['latitude']}, {$enriched['longitude']}");
            }
            if ($enriched['phone']) {
                enrich_log("  → 전화번호: {$enriched['phone']}");
            }
        } else {
            enrich_log("[{$store_id}] 업데이트할 데이터 없음");
        }
        
        $success++;
        
    } catch (Exception $e) {
        $failed++;
        enrich_log("[{$store_id}] 오류: " . $e->getMessage());
    }
    
    // API 할당량 고려 (100ms 대기)
    usleep(100000);
    
    // 진행 상황 출력 (10개마다)
    if (($success + $failed) % 10 == 0) {
        enrich_log("진행: " . ($success + $failed) . "/{$total} (성공: {$success}, 실패: {$failed}, 업데이트: {$updated})");
    }
}

enrich_log("=== 처리 완료 ===");
enrich_log("총 처리: {$total}개");
enrich_log("성공: {$success}개");
enrich_log("실패: {$failed}개");
enrich_log("업데이트: {$updated}개");

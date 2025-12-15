<?php
/**
 * 카카오 API 사용 예시
 * 
 * 이 파일은 예시용이며, 실제 사용 시에는 각 함수를 적절히 호출하세요.
 */

// 그누보드 환경 로드
include_once('../common.php');
include_once('../lib/kakao_api.lib.php');
include_once('../lib/lotto_store.lib.php');

// ============================================
// 예시 1: 주소를 좌표로 변환
// ============================================
function example_geocode() {
    $address = "서울특별시 강남구 역삼동 123";
    
    echo "=== 주소 → 좌표 변환 ===\n";
    echo "주소: {$address}\n";
    
    $result = li_kakao_geocode($address);
    
    if ($result) {
        echo "위도: {$result['latitude']}\n";
        echo "경도: {$result['longitude']}\n";
        echo "도로명 주소: {$result['road_address']}\n";
        echo "시/도: {$result['region_1depth']}\n";
        echo "시/군/구: {$result['region_2depth']}\n";
        echo "읍/면/동: {$result['region_3depth']}\n";
    } else {
        echo "좌표 변환 실패\n";
    }
}

// ============================================
// 예시 2: 장소 검색
// ============================================
function example_search_place() {
    $query = "GS25 강남구";
    
    echo "\n=== 장소 검색 ===\n";
    echo "검색어: {$query}\n";
    
    $result = li_kakao_search_place($query, [
        'size' => 5,
        'category_group_code' => 'CS2' // 편의점
    ]);
    
    if ($result && !empty($result['places'])) {
        echo "총 {$result['total_count']}개 결과\n\n";
        
        foreach ($result['places'] as $idx => $place) {
            echo ($idx + 1) . ". {$place['place_name']}\n";
            echo "   주소: {$place['address_name']}\n";
            echo "   전화번호: " . ($place['phone'] ?: '없음') . "\n";
            echo "   카테고리: {$place['category_name']}\n";
            echo "\n";
        }
    } else {
        echo "검색 결과 없음\n";
    }
}

// ============================================
// 예시 3: 판매점 정보 보강
// ============================================
function example_enrich_store() {
    $store = [
        'store_name' => 'GS25 역삼점',
        'address' => '서울특별시 강남구 역삼동 123'
    ];
    
    echo "\n=== 판매점 정보 보강 ===\n";
    echo "판매점명: {$store['store_name']}\n";
    echo "주소: {$store['address']}\n\n";
    
    $enriched = li_kakao_enrich_store_data($store);
    
    echo "수집 결과:\n";
    if ($enriched['latitude']) {
        echo "  좌표: {$enriched['latitude']}, {$enriched['longitude']}\n";
    }
    if ($enriched['phone']) {
        echo "  전화번호: {$enriched['phone']}\n";
    }
    if ($enriched['place_url']) {
        echo "  플레이스 URL: {$enriched['place_url']}\n";
    }
    if ($enriched['category_name']) {
        echo "  카테고리: {$enriched['category_name']}\n";
    }
}

// ============================================
// 예시 4: 단일 판매점 업데이트
// ============================================
function example_update_single_store() {
    $store_id = 1; // 실제 판매점 ID
    
    echo "\n=== 단일 판매점 업데이트 ===\n";
    
    // 판매점 정보 조회
    $store = li_get_store_by_id($store_id);
    
    if (!$store) {
        echo "판매점을 찾을 수 없습니다.\n";
        return;
    }
    
    echo "판매점 ID: {$store_id}\n";
    echo "판매점명: {$store['store_name']}\n";
    echo "주소: {$store['address']}\n\n";
    
    // 카카오 API로 정보 수집
    $enriched = li_kakao_enrich_store_data($store);
    
    // 데이터베이스 업데이트
    if (li_kakao_update_store($store_id, $enriched)) {
        echo "✅ 업데이트 완료\n";
    } else {
        echo "❌ 업데이트할 데이터 없음\n";
    }
}

// ============================================
// 예시 5: 배치 처리
// ============================================
function example_batch_enrich() {
    echo "\n=== 배치 처리 ===\n";
    
    // 위도/경도가 없는 판매점 10개 조회
    $sql = "SELECT store_id, store_name, address FROM g5_lotto_store 
            WHERE latitude IS NULL OR longitude IS NULL 
            LIMIT 10";
    
    $result = sql_query($sql);
    $stores = [];
    
    while ($row = sql_fetch_array($result)) {
        $stores[] = $row;
    }
    
    echo "처리 대상: " . count($stores) . "개\n\n";
    
    // 배치 처리
    $batch_result = li_kakao_enrich_stores_batch($stores, 100000); // 100ms 대기
    
    echo "처리 완료:\n";
    echo "  총: {$batch_result['total']}개\n";
    echo "  성공: {$batch_result['success']}개\n";
    echo "  실패: {$batch_result['failed']}개\n";
    
    // 각 결과를 데이터베이스에 업데이트
    foreach ($batch_result['results'] as $item) {
        if ($item['success'] && !empty($item['data'])) {
            li_kakao_update_store($item['store_id'], $item['data']);
        }
    }
}

// ============================================
// 예시 6: API 사용량 통계
// ============================================
function example_usage_stats() {
    echo "\n=== API 사용량 통계 ===\n";
    
    $stats = li_kakao_get_usage_stats();
    
    echo "오늘 요청 수: {$stats['today_requests']}건\n";
    echo "성공: {$stats['today_success']}건\n";
    echo "실패: {$stats['today_failed']}건\n";
    echo "로그 파일: {$stats['log_file']}\n";
    echo "\n{$stats['note']}\n";
}

// ============================================
// 실행 (CLI 모드에서만)
// ============================================
if (php_sapi_name() === 'cli') {
    // API 키 확인
    $api_key = li_get_kakao_api_key();
    if (empty($api_key)) {
        echo "❌ 카카오 API 키가 설정되지 않았습니다.\n";
        echo "data/kakao_config.php 파일을 생성하고 API 키를 설정하세요.\n";
        exit(1);
    }
    
    echo "카카오 API 키 확인 완료\n\n";
    
    // 예시 실행
    // example_geocode();
    // example_search_place();
    // example_enrich_store();
    // example_update_single_store();
    // example_batch_enrich();
    // example_usage_stats();
    
    echo "예시 함수를 주석 해제하여 실행하세요.\n";
}

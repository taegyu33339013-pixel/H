<?php
/**
 * 카카오 API 데이터 수집 라이브러리
 * 
 * 사용 API:
 * - 주소 → 좌표 변환: https://dapi.kakao.com/v2/local/search/address.json
 * - 장소 검색: https://dapi.kakao.com/v2/local/search/keyword.json
 * 
 * API 키 설정:
 * - config.php 또는 환경변수에 KAKAO_REST_API_KEY 설정 필요
 */

if (!defined('_GNUBOARD_')) exit;

/**
 * 카카오 REST API 키 가져오기
 */
function li_get_kakao_api_key() {
    // 1. 환경변수에서 확인
    $key = getenv('KAKAO_REST_API_KEY');
    if ($key) {
        return $key;
    }
    
    // 2. config.php에서 확인 (사용자 정의)
    if (defined('KAKAO_REST_API_KEY')) {
        return KAKAO_REST_API_KEY;
    }
    
    // 3. 별도 설정 파일에서 확인
    $config_file = G5_PATH . '/data/kakao_config.php';
    if (file_exists($config_file)) {
        include_once($config_file);
        if (isset($kakao_api_key)) {
            return $kakao_api_key;
        }
    }
    
    error_log("[kakao_api] API 키를 찾을 수 없습니다. KAKAO_REST_API_KEY를 설정하세요.");
    return '';
}

/**
 * 카카오 API HTTP 요청
 */
function li_kakao_api_request($url, $method = 'GET', $params = []) {
    $api_key = li_get_kakao_api_key();
    
    if (empty($api_key)) {
        error_log("[kakao_api] API 키가 설정되지 않았습니다.");
        return null;
    }
    
    $ch = curl_init();
    
    // GET 요청인 경우 URL에 파라미터 추가
    if ($method === 'GET' && !empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    $headers = [
        "Authorization: KakaoAK {$api_key}",
        "Content-Type: application/json; charset=UTF-8"
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("[kakao_api] cURL error: {$error}");
        return null;
    }
    
    if ($http_code !== 200) {
        error_log("[kakao_api] HTTP error: {$http_code}, Response: " . substr($response, 0, 200));
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("[kakao_api] JSON decode error: " . json_last_error_msg());
        return null;
    }
    
    return $data;
}

/**
 * 주소를 좌표로 변환 (Geocoding)
 * 
 * @param string $address 주소
 * @return array|null ['latitude' => float, 'longitude' => float] 또는 null
 */
function li_kakao_geocode($address) {
    $url = 'https://dapi.kakao.com/v2/local/search/address.json';
    $params = [
        'query' => $address,
        'size' => 1
    ];
    
    $data = li_kakao_api_request($url, 'GET', $params);
    
    if (!$data || empty($data['documents'])) {
        return null;
    }
    
    $doc = $data['documents'][0];
    
    return [
        'latitude' => (float)$doc['y'],
        'longitude' => (float)$doc['x'],
        'address_type' => $doc['address_type'] ?? '',
        'road_address' => $doc['road_address']['address_name'] ?? '',
        'region_1depth' => $doc['address']['region_1depth_name'] ?? '',
        'region_2depth' => $doc['address']['region_2depth_name'] ?? '',
        'region_3depth' => $doc['address']['region_3depth_name'] ?? '',
    ];
}

/**
 * 장소 검색 (판매점 정보 수집)
 * 
 * @param string $query 검색어 (판매점명 + 주소)
 * @param array $options 추가 옵션
 * @return array|null
 */
function li_kakao_search_place($query, $options = []) {
    $url = 'https://dapi.kakao.com/v2/local/search/keyword.json';
    
    $params = [
        'query' => $query,
        'size' => isset($options['size']) ? (int)$options['size'] : 1,
        'page' => isset($options['page']) ? (int)$options['page'] : 1,
    ];
    
    // 카테고리 필터 (편의점, 마트 등)
    if (isset($options['category_group_code'])) {
        $params['category_group_code'] = $options['category_group_code'];
    }
    
    // 좌표 기반 검색 (선택)
    if (isset($options['x']) && isset($options['y'])) {
        $params['x'] = $options['x'];
        $params['y'] = $options['y'];
        $params['radius'] = isset($options['radius']) ? (int)$options['radius'] : 2000; // 미터
    }
    
    $data = li_kakao_api_request($url, 'GET', $params);
    
    if (!$data || empty($data['documents'])) {
        return null;
    }
    
    $places = [];
    foreach ($data['documents'] as $doc) {
        $place = [
            'place_name' => $doc['place_name'] ?? '',
            'address_name' => $doc['address_name'] ?? '',
            'road_address_name' => $doc['road_address_name'] ?? '',
            'phone' => $doc['phone'] ?? '',
            'category_name' => $doc['category_name'] ?? '',
            'place_url' => $doc['place_url'] ?? '',
            'latitude' => isset($doc['y']) ? (float)$doc['y'] : null,
            'longitude' => isset($doc['x']) ? (float)$doc['x'] : null,
            'distance' => isset($doc['distance']) ? (int)$doc['distance'] : null,
        ];
        
        $places[] = $place;
    }
    
    return [
        'places' => $places,
        'total_count' => $data['meta']['total_count'] ?? 0,
        'pageable_count' => $data['meta']['pageable_count'] ?? 0,
    ];
}

/**
 * 판매점 정보 수집 (이름 + 주소로 검색)
 * 
 * @param string $store_name 판매점명
 * @param string $address 주소
 * @return array|null
 */
function li_kakao_fetch_store_info($store_name, $address) {
    // 검색 쿼리: 판매점명 + 주소
    $query = trim($store_name . ' ' . $address);
    
    $result = li_kakao_search_place($query, [
        'size' => 3, // 상위 3개 결과 확인
    ]);
    
    if (!$result || empty($result['places'])) {
        return null;
    }
    
    // 가장 유사한 결과 찾기 (이름 매칭)
    $best_match = null;
    $best_score = 0;
    
    foreach ($result['places'] as $place) {
        $score = 0;
        
        // 이름 일치도 체크
        $place_name = mb_strtolower($place['place_name']);
        $search_name = mb_strtolower($store_name);
        
        if (mb_strpos($place_name, $search_name) !== false || 
            mb_strpos($search_name, $place_name) !== false) {
            $score += 50;
        }
        
        // 주소 일치도 체크
        if (mb_strpos($place['address_name'], $address) !== false ||
            mb_strpos($place['road_address_name'], $address) !== false) {
            $score += 30;
        }
        
        // 거리 (가까울수록 높은 점수)
        if ($place['distance'] !== null) {
            $score += max(0, 20 - ($place['distance'] / 100));
        }
        
        if ($score > $best_score) {
            $best_score = $score;
            $best_match = $place;
        }
    }
    
    if ($best_match && $best_score >= 30) {
        return $best_match;
    }
    
    // 매칭 실패 시 첫 번째 결과 반환
    return $result['places'][0] ?? null;
}

/**
 * 카카오 플레이스 URL에서 영업시간 정보 추출 (간접)
 * 
 * 주의: 카카오 API는 직접 영업시간을 제공하지 않으므로,
 * 플레이스 URL을 반환하고 별도 크롤링이 필요할 수 있습니다.
 * 
 * @param string $place_url 카카오 플레이스 URL
 * @return array|null
 */
function li_kakao_get_place_details($place_url) {
    // 카카오 API는 영업시간을 직접 제공하지 않음
    // 플레이스 URL을 반환하여 사용자가 확인하도록 함
    return [
        'place_url' => $place_url,
        'note' => '영업시간 정보는 카카오 플레이스 페이지에서 확인 필요'
    ];
}

/**
 * 판매점의 위도/경도 및 추가 정보 수집
 * 
 * @param array $store 판매점 정보 (store_name, address 필수)
 * @return array 업데이트할 정보
 */
function li_kakao_enrich_store_data($store) {
    $result = [
        'latitude' => null,
        'longitude' => null,
        'phone' => null,
        'place_url' => null,
        'category_name' => null,
        'road_address' => null,
    ];
    
    $store_name = $store['store_name'] ?? '';
    $address = $store['address'] ?? '';
    
    if (empty($store_name) || empty($address)) {
        return $result;
    }
    
    // 1. 주소로 좌표 변환 시도
    $geocode = li_kakao_geocode($address);
    if ($geocode) {
        $result['latitude'] = $geocode['latitude'];
        $result['longitude'] = $geocode['longitude'];
        $result['road_address'] = $geocode['road_address'];
    }
    
    // 2. 장소 검색으로 추가 정보 수집
    $place_info = li_kakao_fetch_store_info($store_name, $address);
    if ($place_info) {
        // 좌표가 없었으면 장소 검색 결과에서 가져오기
        if (!$result['latitude'] && $place_info['latitude']) {
            $result['latitude'] = $place_info['latitude'];
            $result['longitude'] = $place_info['longitude'];
        }
        
        // 전화번호
        if (!empty($place_info['phone'])) {
            $result['phone'] = $place_info['phone'];
        }
        
        // 플레이스 URL
        if (!empty($place_info['place_url'])) {
            $result['place_url'] = $place_info['place_url'];
        }
        
        // 카테고리
        if (!empty($place_info['category_name'])) {
            $result['category_name'] = $place_info['category_name'];
        }
    }
    
    return $result;
}

/**
 * 배치로 여러 판매점의 정보 수집
 * 
 * @param array $stores 판매점 배열
 * @param int $delay 요청 간 지연 시간 (마이크로초, 기본 100ms)
 * @return array 결과 배열
 */
function li_kakao_enrich_stores_batch($stores, $delay = 100000) {
    $results = [];
    $success = 0;
    $failed = 0;
    
    foreach ($stores as $store) {
        $store_id = $store['store_id'] ?? 0;
        
        try {
            $enriched = li_kakao_enrich_store_data($store);
            
            $results[] = [
                'store_id' => $store_id,
                'success' => true,
                'data' => $enriched
            ];
            
            $success++;
            
        } catch (Exception $e) {
            error_log("[kakao_api] Store ID {$store_id} 처리 실패: " . $e->getMessage());
            
            $results[] = [
                'store_id' => $store_id,
                'success' => false,
                'error' => $e->getMessage()
            ];
            
            $failed++;
        }
        
        // API 할당량 고려하여 지연
        usleep($delay);
    }
    
    return [
        'results' => $results,
        'total' => count($stores),
        'success' => $success,
        'failed' => $failed
    ];
}

/**
 * 판매점 정보를 데이터베이스에 업데이트
 * 
 * @param int $store_id 판매점 ID
 * @param array $data 업데이트할 데이터
 * @return bool
 */
function li_kakao_update_store($store_id, $data) {
    $store_id = (int)$store_id;
    
    if ($store_id <= 0) {
        return false;
    }
    
    $updates = [];
    
    if (isset($data['latitude']) && $data['latitude'] !== null) {
        $lat = (float)$data['latitude'];
        $updates[] = "latitude = {$lat}";
    }
    
    if (isset($data['longitude']) && $data['longitude'] !== null) {
        $lng = (float)$data['longitude'];
        $updates[] = "longitude = {$lng}";
    }
    
    if (isset($data['phone']) && !empty($data['phone'])) {
        $phone = sql_real_escape_string($data['phone']);
        $updates[] = "phone = '{$phone}'";
    }
    
    // region3 업데이트 (카카오 API에서 추출한 경우)
    if (isset($data['region3']) && !empty($data['region3'])) {
        $region3 = sql_real_escape_string($data['region3']);
        // region3 컬럼 존재 여부 확인
        $check_region3 = sql_query("SHOW COLUMNS FROM g5_lotto_store LIKE 'region3'", false);
        if ($check_region3 && sql_num_rows($check_region3) > 0) {
            $updates[] = "region3 = '{$region3}'";
        }
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $sql = "UPDATE g5_lotto_store SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE store_id = {$store_id}";
    sql_query($sql);
    
    return true;
}

/**
 * 카카오 API 사용량 통계 조회
 * 
 * @return array 통계 정보
 */
function li_kakao_get_usage_stats() {
    // API 사용량은 카카오 개발자 콘솔에서 확인해야 함
    // 여기서는 로컬 로그 기반 통계만 제공
    $log_file = G5_PATH . '/data/log/kakao_enrich_' . date('Ymd') . '.log';
    
    $stats = [
        'today_requests' => 0,
        'today_success' => 0,
        'today_failed' => 0,
        'log_file' => $log_file,
        'note' => '실제 API 사용량은 카카오 개발자 콘솔에서 확인하세요'
    ];
    
    if (file_exists($log_file)) {
        $log_content = file_get_contents($log_file);
        $stats['today_success'] = substr_count($log_content, '업데이트 완료');
        $stats['today_failed'] = substr_count($log_content, '오류:');
        $stats['today_requests'] = $stats['today_success'] + $stats['today_failed'];
    }
    
    return $stats;
}

/**
 * API 재시도 로직이 포함된 요청 함수
 * 
 * @param string $url API URL
 * @param string $method HTTP 메서드
 * @param array $params 파라미터
 * @param int $max_retries 최대 재시도 횟수
 * @return array|null
 */
function li_kakao_api_request_with_retry($url, $method = 'GET', $params = [], $max_retries = 3) {
    $attempt = 0;
    $last_error = null;
    
    while ($attempt < $max_retries) {
        $result = li_kakao_api_request($url, $method, $params);
        
        if ($result !== null) {
            return $result;
        }
        
        $attempt++;
        $last_error = "시도 {$attempt} 실패";
        
        // 재시도 전 대기 (지수 백오프)
        if ($attempt < $max_retries) {
            $delay = pow(2, $attempt) * 100000; // 0.1초, 0.2초, 0.4초...
            usleep($delay);
        }
    }
    
    error_log("[kakao_api] 최대 재시도 횟수 초과: {$url}");
    return null;
}

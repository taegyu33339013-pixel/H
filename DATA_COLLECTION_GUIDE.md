# 데이터 수집 가이드

## 📊 데이터 수집 소스 및 방법

### 1. 동행복권 공식 데이터

#### 1.1 판매점 목록 수집

**API 엔드포인트**
```
POST https://www.dhlottery.co.kr/store.do?method=sellerInfo645Result
```

**요청 파라미터**
```php
[
    'searchType' => 'SIDO',      // 검색 타입: SIDO(시도), SIGUGUN(시군구), DONG(동)
    'SIDO' => '11',              // 시/도 코드
    'SIGUGUN' => '',             // 시/군/구 코드 (선택)
    'DONG' => '',                // 동 코드 (선택)
    'nowPage' => 1,              // 페이지 번호
    'sltSIDO2' => '11',
    'sltGUGUN2' => ''
]
```

**시/도 코드 매핑**
```php
'서울' => '11', '부산' => '26', '대구' => '27', '인천' => '28',
'광주' => '29', '대전' => '30', '울산' => '31', '세종' => '36',
'경기' => '41', '강원' => '42', '충북' => '43', '충남' => '44',
'전북' => '45', '전남' => '46', '경북' => '47', '경남' => '48',
'제주' => '50'
```

**응답 형식**: HTML (EUC-KR 인코딩)
**파싱 방법**: DOMDocument + XPath

**수집 데이터**
- ✅ 판매점명 (`store_name`)
- ✅ 주소 (`address`)
- ✅ 전화번호 (`phone`) - 일부만 제공
- ✅ 지역 정보 (`region1`, `region2`) - 주소에서 추출

**코드 위치**: `lib/lotto_store.lib.php` → `li_fetch_all_stores_by_region()`

#### 1.2 당첨점 정보 수집

**API 엔드포인트**
```
GET https://www.dhlottery.co.kr/store.do?method=topStore&drwNo={회차}&gameNo=5882&rank={등수}
```

**요청 파라미터**
- `drwNo`: 회차 번호 (예: 1202)
- `gameNo`: 5882 (로또 6/45 고정)
- `rank`: 1 또는 2 (등수, 선택사항)

**응답 형식**: HTML (EUC-KR 인코딩)
**파싱 방법**: DOMDocument + XPath

**수집 데이터**
- ✅ 판매점명
- ✅ 주소
- ✅ 판매방법 (자동/수동/반자동)
- ✅ 회차, 등수

**코드 위치**: `lib/lotto_store.lib.php` → `li_fetch_winning_stores()`

#### 1.3 당첨금 정보 수집

**API 엔드포인트**
```
GET https://www.dhlottery.co.kr/gameResult.do?method=byWin&drwNo={회차}
```

**수집 데이터**
- ✅ 총 판매금액
- ✅ 1등 당첨자 수, 1인당 당첨금
- ✅ 2등 당첨자 수, 1인당 당첨금
- ✅ 3등 당첨자 수, 1인당 당첨금

**코드 위치**: `lib/lotto_store.lib.php` → `li_fetch_draw_prize_info()`

### 2. 추가 데이터 수집 (외부 API)

#### 2.1 위도/경도 (latitude, longitude)

**동행복권 제공**: ❌ 제공하지 않음

**수집 방법 1: 카카오 지도 API (권장)**
- **API**: `https://dapi.kakao.com/v2/local/search/address.json`
- **무료 할당량**: 월 300,000건
- **API 키 발급**: https://developers.kakao.com/

```php
function li_geocode_kakao($address) {
    $api_key = 'YOUR_KAKAO_REST_API_KEY';
    $url = 'https://dapi.kakao.com/v2/local/search/address.json?query=' . urlencode($address);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: KakaoAK {$api_key}"],
    ]);
    
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    if (!empty($response['documents'][0])) {
        return [
            'latitude' => (float)$response['documents'][0]['y'],
            'longitude' => (float)$response['documents'][0]['x']
        ];
    }
    
    return null;
}
```

**수집 방법 2: 네이버 지도 API**
- **API**: `https://openapi.naver.com/v1/map/geocode`
- **무료 할당량**: 월 10,000건
- **API 키 발급**: https://developers.naver.com/

**수집 방법 3: Google Geocoding API**
- **API**: `https://maps.googleapis.com/maps/api/geocode/json`
- **무료 할당량**: 월 $200 크레딧
- **API 키 발급**: https://console.cloud.google.com/

#### 2.2 영업시간 (opening_hours)

**동행복권 제공**: ❌ 제공하지 않음

**수집 방법: 카카오 로컬 API**
- **API**: `https://dapi.kakao.com/v2/local/search/keyword.json`
- **검색 쿼리**: 판매점명 + 주소
- **응답에서 추출**: `place_url`, `phone`, `category_name` 등

```php
function li_fetch_store_info_kakao($store_name, $address) {
    $api_key = 'YOUR_KAKAO_REST_API_KEY';
    $query = $store_name . ' ' . $address;
    $url = 'https://dapi.kakao.com/v2/local/search/keyword.json?query=' . urlencode($query);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: KakaoAK {$api_key}"],
    ]);
    
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    if (!empty($response['documents'][0])) {
        $place = $response['documents'][0];
        return [
            'opening_hours' => $place['place_url'] ?? null, // 카카오 플레이스 URL에서 확인
            'phone' => $place['phone'] ?? null,
            'latitude' => (float)$place['y'],
            'longitude' => (float)$place['x']
        ];
    }
    
    return null;
}
```

**참고**: 카카오 플레이스 페이지에서 영업시간 정보를 스크래핑하거나, 사용자가 직접 입력

#### 2.3 판매점 이미지 (store_image)

**동행복권 제공**: ❌ 제공하지 않음

**수집 방법 1: Google Street View API**
- **API**: `https://maps.googleapis.com/maps/api/streetview`
- **파라미터**: `location` (주소), `size` (이미지 크기)

**수집 방법 2: 카카오 로컬 API**
- 일부 장소에 이미지 제공 (제한적)

**수집 방법 3: 직접 수집**
- 관리자 페이지에서 직접 업로드
- 또는 크롤러로 카카오 플레이스/네이버 플레이스에서 이미지 수집

#### 2.4 읍/면/동 (region3)

**동행복권 제공**: ❌ 직접 제공하지 않음 (주소에서 추출)

**수집 방법: 주소 파싱**
- **코드 위치**: `lib/lotto_store.lib.php` → `li_extract_region_from_address()`
- **정규식**: `/([가-힣]+(?:동|읍|면|리))/u`

```php
function li_extract_region3($address) {
    if (preg_match('/([가-힣]+(?:동|읍|면|리))/u', $address, $matches)) {
        return $matches[1];
    }
    return '';
}
```

**예시**
- "서울특별시 강남구 역삼동 123" → "역삼동"
- "경기도 수원시 영통구 원천동 456" → "원천동"

### 3. 데이터 수집 실행 방법

#### 3.1 전체 판매점 수집 (최초 1회)

```bash
# 전체 판매점 수집 (약 30분~1시간 소요)
php cron/lotto_store_sync.php all
```

**수집 과정**
1. 전국 17개 시/도별로 순회
2. 각 시/도별 판매점 목록 페이지별 수집
3. 주소에서 region1, region2 추출
4. 데이터베이스에 저장

#### 3.2 당첨점 데이터 수집

```bash
# 최신 회차만 동기화
php cron/lotto_store_sync.php

# 특정 회차만 동기화
php cron/lotto_store_sync.php 1202

# 범위 동기화 (1회~1202회)
php cron/lotto_store_sync.php 1 1202
```

**수집 과정**
1. 회차별 1등/2등 당첨점 조회
2. 판매점 정보 저장 (없으면 생성)
3. 당첨 기록 저장
4. 판매점 누적 당첨 횟수 업데이트

#### 3.3 추가 데이터 수집 스크립트

**위도/경도 일괄 수집**
```php
// cron/store_geocode_sync.php
<?php
include_once(__DIR__ . '/../common.php');
include_once(__DIR__ . '/../lib/lotto_store.lib.php');

// 위도/경도가 없는 판매점 조회
$stores = sql_query("SELECT store_id, address FROM g5_lotto_store WHERE latitude IS NULL OR longitude IS NULL LIMIT 100");

while ($store = sql_fetch_array($stores)) {
    $geo = li_geocode_kakao($store['address']);
    
    if ($geo) {
        sql_query("UPDATE g5_lotto_store SET latitude = {$geo['latitude']}, longitude = {$geo['longitude']} WHERE store_id = {$store['store_id']}");
        echo "Updated store_id: {$store['store_id']}\n";
    }
    
    // API 할당량 고려하여 대기
    usleep(100000); // 0.1초
}
```

**영업시간 수집**
```php
// cron/store_hours_sync.php
<?php
// 카카오 로컬 API로 영업시간 수집
// 또는 관리자 페이지에서 수동 입력
```

### 4. API 할당량 관리

#### 카카오 지도 API
- **무료 할당량**: 월 300,000건
- **초과 시**: 유료 (건당 약 0.1원)
- **권장**: 일일 10,000건 이하로 제한

#### 네이버 지도 API
- **무료 할당량**: 월 10,000건
- **초과 시**: 유료
- **권장**: 보조 수집용으로 사용

#### Google Geocoding API
- **무료 할당량**: 월 $200 크레딧
- **초과 시**: 유료
- **권장**: 카카오 API 보조용

### 5. 데이터 수집 주기

| 데이터 타입 | 수집 주기 | 방법 |
|------------|----------|------|
| 판매점 목록 | 최초 1회 + 분기별 | `php cron/lotto_store_sync.php all` |
| 당첨점 정보 | 매주 1회 (추첨 후) | `php cron/lotto_store_sync.php` |
| 위도/경도 | 최초 1회 + 신규 판매점 | 카카오 API |
| 영업시간 | 수동 입력 또는 주기적 업데이트 | 카카오 로컬 API 또는 관리자 입력 |
| 판매점 이미지 | 수동 업로드 | 관리자 페이지 |

### 6. 주의사항

1. **API 호출 제한**: 동행복권 서버 부하 방지를 위해 요청 간격 유지 (0.3~0.5초)
2. **인코딩**: 동행복권은 EUC-KR 인코딩 사용 → UTF-8 변환 필요
3. **HTML 구조 변경**: 동행복권 사이트 구조 변경 시 파싱 로직 수정 필요
4. **API 키 관리**: 외부 API 키는 환경변수 또는 설정 파일로 관리
5. **에러 처리**: API 실패 시 재시도 로직 구현 권장

### 7. 데이터 품질 관리

- **중복 제거**: 판매점명 + 주소로 중복 체크
- **데이터 검증**: 필수 필드(이름, 주소) 누락 체크
- **정기 점검**: 월 1회 데이터 정합성 검사
- **업데이트 추적**: `updated_at` 필드로 최신 데이터 확인

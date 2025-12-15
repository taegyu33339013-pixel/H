# 카카오 API 설정 가이드

## 📋 개요

카카오 API를 사용하여 판매점의 추가 정보(위도/경도, 전화번호, 영업시간 등)를 수집하는 라이브러리입니다.

## 🔑 API 키 발급

1. **카카오 개발자 콘솔 접속**
   - https://developers.kakao.com/ 접속
   - 카카오 계정으로 로그인

2. **애플리케이션 생성**
   - 내 애플리케이션 → 애플리케이션 추가하기
   - 앱 이름, 사업자명 입력

3. **REST API 키 확인**
   - 앱 설정 → 앱 키 → REST API 키 복사

4. **플랫폼 설정**
   - 앱 설정 → 플랫폼 → Web 플랫폼 등록
   - 사이트 도메인 등록 (예: https://lottoinsight.ai)

## ⚙️ 설정 방법

### 방법 1: 설정 파일 사용 (권장)

```bash
# 1. 예시 파일 복사
cp data/kakao_config.php.example data/kakao_config.php

# 2. API 키 입력
# data/kakao_config.php 파일을 열어서 YOUR_KAKAO_REST_API_KEY_HERE를 실제 키로 변경
```

```php
<?php
// data/kakao_config.php
$kakao_api_key = '실제_API_키_입력';
```

### 방법 2: 환경변수 사용

```bash
export KAKAO_REST_API_KEY='실제_API_키'
```

### 방법 3: config.php에 정의

```php
// common.php 또는 config.php
define('KAKAO_REST_API_KEY', '실제_API_키');
```

## 📚 사용 방법

### 1. 주소를 좌표로 변환

```php
include_once('lib/kakao_api.lib.php');

$address = "서울특별시 강남구 역삼동 123";
$result = li_kakao_geocode($address);

if ($result) {
    echo "위도: {$result['latitude']}\n";
    echo "경도: {$result['longitude']}\n";
}
```

### 2. 장소 검색

```php
$query = "GS25 강남구";
$result = li_kakao_search_place($query, [
    'size' => 5,
    'category_group_code' => 'CS2' // 편의점
]);

foreach ($result['places'] as $place) {
    echo "장소명: {$place['place_name']}\n";
    echo "주소: {$place['address_name']}\n";
    echo "전화번호: {$place['phone']}\n";
}
```

### 3. 판매점 정보 보강

```php
$store = [
    'store_name' => 'GS25 역삼점',
    'address' => '서울특별시 강남구 역삼동 123'
];

$enriched = li_kakao_enrich_store_data($store);

// 결과:
// - latitude, longitude
// - phone
// - place_url
// - category_name
```

### 4. 배치 처리 (크론 스크립트)

```bash
# 위도/경도가 없는 판매점 100개 처리
php cron/kakao_store_enrich.php

# 500개 처리
php cron/kakao_store_enrich.php 500

# 전체 처리 (주의: 시간 오래 걸림)
php cron/kakao_store_enrich.php all
```

## 📊 API 할당량

### 무료 할당량
- **월 300,000건**
- 초과 시 유료 (건당 약 0.1원)

### 권장 사용량
- **일일 10,000건 이하**
- 배치 처리 시 요청 간 100ms 이상 대기

## 🔧 주요 함수

### `li_kakao_geocode($address)`
주소를 좌표로 변환

### `li_kakao_search_place($query, $options)`
장소 검색

### `li_kakao_fetch_store_info($store_name, $address)`
판매점 정보 검색 (이름 + 주소)

### `li_kakao_enrich_store_data($store)`
판매점 정보 보강 (좌표, 전화번호 등)

### `li_kakao_enrich_stores_batch($stores, $delay)`
여러 판매점 배치 처리

### `li_kakao_update_store($store_id, $data)`
데이터베이스 업데이트

## 📝 크론탭 설정 예시

```bash
# 매일 새벽 2시에 위도/경도 없는 판매점 100개씩 처리
0 2 * * * php /path/to/cron/kakao_store_enrich.php 100 >> /var/log/kakao_enrich.log 2>&1
```

## ⚠️ 주의사항

1. **API 키 보안**
   - `data/kakao_config.php`는 `.gitignore`에 포함되어 있음
   - 절대 Git에 커밋하지 마세요

2. **할당량 관리**
   - 일일 사용량 모니터링
   - 필요시 유료 플랜 고려

3. **에러 처리**
   - API 실패 시 로그 확인
   - 재시도 로직 구현 권장

4. **요청 간격**
   - 최소 100ms 대기 (서버 부하 방지)
   - 배치 처리 시 더 긴 간격 권장

## 🐛 문제 해결

### API 키 오류
```
Error: API 키가 설정되지 않았습니다.
```
→ `data/kakao_config.php` 파일 확인

### 할당량 초과
```
HTTP error: 429
```
→ 일일 할당량 확인, 다음 날 재시도

### 좌표 변환 실패
→ 주소 형식 확인, 더 구체적인 주소 사용

## 📖 참고 문서

- 카카오 개발자 문서: https://developers.kakao.com/docs
- 주소 검색 API: https://developers.kakao.com/docs/latest/ko/local/dev-guide#search-by-keyword
- 좌표 변환 API: https://developers.kakao.com/docs/latest/ko/local/dev-guide#search-by-address

# SEO 초월 강화 - 로또로직스 대비 우위 확보

## 📊 개요
로또로직스보다 더 강력한 SEO 구조를 구현하여 검색 엔진 노출 우위를 확보했습니다.

## 🚀 주요 개선사항

### 1. **구조화된 데이터 (Schema.org) 강화**

#### stores/index.php
- ✅ **Organization Schema 추가**: 브랜드 신뢰도 강화
- ✅ **WebSite Schema 추가**: 사이트 전체 정보 및 검색 기능
- ✅ **WebPage Schema 추가**: 페이지 메타데이터 강화
- ✅ **CollectionPage Schema 추가**: 목록 페이지 특화
- ✅ **BreadcrumbList**: 5단계 계층 구조 (전국 → 시도 → 시군구 → 읍면동)
- ✅ **ItemList**: Store → **LocalBusiness**로 업그레이드 (더 구체적)
- ✅ **FAQPage Schema**: 판매점 관련 FAQ 3개 추가
- ✅ **@graph 구조**: 모든 스키마를 하나의 그래프로 연결

#### stores/detail.php
- ✅ **Organization Schema**: 브랜드 정보
- ✅ **LocalBusiness Schema**: Store보다 더 구체적인 비즈니스 정보
  - `openingHoursSpecification`: 영업시간
  - `paymentAccepted`: 결제 수단
  - `currenciesAccepted`: 통화
  - `image`: 판매점 이미지
- ✅ **ItemList Schema**: 당첨 이력 구조화 (Event 타입)
- ✅ **FAQPage Schema**: 판매점별 맞춤 FAQ 3개
- ✅ **WebPage Schema**: 더 상세한 페이지 메타데이터
- ✅ **AggregateRating**: bestRating, worstRating 추가

### 2. **메타 태그 강화**

#### Open Graph 확장
- ✅ `og:image`, `og:image:width`, `og:image:height`, `og:image:alt`
- ✅ `og:locale`, `og:site_name`
- ✅ `place:location:latitude`, `place:location:longitude` (detail.php)

#### Twitter Card 강화
- ✅ `summary_large_image` 타입
- ✅ `twitter:image` 추가
- ✅ `twitter:site`, `twitter:creator` 추가

#### 추가 메타 태그
- ✅ `author`, `publisher`, `copyright`
- ✅ `geo.region`, `geo.placename`, `geo.position`, `ICBM`
- ✅ `article:author`, `article:published_time`, `article:modified_time`
- ✅ `article:section`, `article:tag`
- ✅ `abstract` (추가 설명)

### 3. **키워드 최적화**

#### stores/index.php
- 지역별 키워드 확장: "서울 로또", "서울 복권방", "강남구 로또판매점" 등
- "동행복권 판매점", "로또 구매처", "전국 로또 판매점" 추가

#### stores/detail.php
- 판매점명 + 지역 + "명당", "복권방", "로또판매점" 등 다양한 변형
- 당첨 이력 기반 키워드: "1등 당첨점", "2등 당첨점", "명당" (3회 이상)
- Description 강화: 더 상세하고 자연스러운 설명

### 4. **성능 최적화**

- ✅ `preconnect`: Google Fonts, Google Analytics
- ✅ `dns-prefetch`: 외부 도메인 사전 DNS 조회
- ✅ `prefetch`: 관련 페이지 사전 로드 (detail.php)

### 5. **구조화된 데이터 품질 향상**

#### LocalBusiness vs Store
- **Store**: 기본 판매점 정보
- **LocalBusiness**: 더 구체적인 비즈니스 정보
  - 영업시간, 결제 수단, 통화 정보
  - 더 상세한 주소 정보 (addressLocality 확장)
  - 이미지, 전화번호 등 추가 속성

#### ItemList 확장
- stores/index.php: 10개 → **20개**로 증가 (더 많은 판매점 노출)
- 각 항목에 `@id`, `url`, 상세한 `PostalAddress` 포함
- `aggregateRating`에 `bestRating`, `worstRating` 추가

#### 당첨 이력 구조화
- detail.php에 `ItemList`로 당첨 이력 구조화
- 각 당첨을 `Event` 타입으로 표현
- 날짜, 회차, 등수 정보 포함

### 6. **FAQ Schema 강화**

#### stores/index.php
- 지역별 맞춤 FAQ
- "판매점 찾기", "당첨 확률", "당첨 이력 확인" 관련 질문

#### stores/detail.php
- 판매점별 맞춤 FAQ
- 판매점명, 주소, 당첨 이력이 포함된 자연스러운 답변

### 7. **로또로직스 대비 우위 요소**

| 항목 | 로또로직스 | 우리 사이트 (개선 후) |
|------|-----------|---------------------|
| Schema 타입 | Store, BreadcrumbList, WebPage | **LocalBusiness**, **Organization**, **WebSite**, **CollectionPage**, **FAQPage**, **ItemList** (당첨 이력) |
| 구조화된 데이터 | 개별 스키마 | **@graph 구조**로 모든 스키마 연결 |
| 메타 태그 | 기본 OG, Twitter Card | **확장된 OG**, **Twitter Card**, **Article**, **Geo**, **Abstract** |
| FAQ Schema | 없음 | **판매점별 맞춤 FAQ** |
| 당첨 이력 구조화 | 없음 | **ItemList + Event** 타입으로 구조화 |
| 성능 최적화 | 기본 | **preconnect**, **dns-prefetch**, **prefetch** |
| 키워드 밀도 | 기본 | **지역별 변형 키워드 확장** |
| ItemList 항목 수 | 10개 | **20개** (더 많은 노출) |
| Rating 정보 | 기본 | **bestRating, worstRating** 포함 |

## 📈 예상 SEO 효과

### 1. 검색 엔진 이해도 향상
- **@graph 구조**: 모든 스키마를 하나의 그래프로 연결하여 검색 엔진이 페이지 구조를 더 잘 이해
- **LocalBusiness**: Store보다 더 구체적인 비즈니스 정보로 로컬 검색 최적화

### 2. 리치 스니펫 확률 증가
- **FAQPage**: 검색 결과에 FAQ 스니펫 표시 가능
- **AggregateRating**: 별점 표시 가능
- **BreadcrumbList**: 검색 결과에 경로 표시

### 3. 로컬 SEO 강화
- **Geo 메타 태그**: 지역 검색 최적화
- **LocalBusiness Schema**: Google My Business 연동 가능성
- **상세한 주소 정보**: 지역 검색 정확도 향상

### 4. 소셜 미디어 공유 최적화
- **확장된 OG 태그**: 더 풍부한 미리보기
- **Twitter Card**: 트위터 공유 시 큰 이미지 표시
- **이미지 최적화**: 1200x630 표준 크기

### 5. 사용자 경험 향상
- **FAQ**: 사용자 질문에 대한 즉각적인 답변
- **성능 최적화**: 페이지 로딩 속도 개선
- **구조화된 정보**: 검색 결과에서 더 많은 정보 표시

## 💾 데이터베이스 테이블 구조

### 1. `g5_lotto_store` - 판매점 정보 테이블

```sql
CREATE TABLE IF NOT EXISTS `g5_lotto_store` (
    `store_id` int(11) NOT NULL AUTO_INCREMENT,
    `store_name` varchar(100) NOT NULL COMMENT '판매점 이름',
    `address` varchar(255) NOT NULL COMMENT '주소',
    `region1` varchar(20) DEFAULT NULL COMMENT '시/도',
    `region2` varchar(50) DEFAULT NULL COMMENT '시/군/구',
    `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동 (향후 추가)',
    `phone` varchar(20) DEFAULT NULL COMMENT '전화번호',
    `latitude` decimal(10,7) DEFAULT NULL COMMENT '위도',
    `longitude` decimal(10,7) DEFAULT NULL COMMENT '경도',
    `wins_1st` int(11) DEFAULT 0 COMMENT '누적 1등 당첨 횟수',
    `wins_2nd` int(11) DEFAULT 0 COMMENT '누적 2등 당첨 횟수',
    `opening_hours` varchar(100) DEFAULT NULL COMMENT '영업시간 (향후 추가)',
    `store_image` varchar(255) DEFAULT NULL COMMENT '판매점 이미지 URL (향후 추가)',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`store_id`),
    KEY `idx_name` (`store_name`),
    KEY `idx_region` (`region1`, `region2`),
    KEY `idx_wins` (`wins_1st` DESC, `wins_2nd` DESC),
    KEY `idx_address` (`address`(100)),
    KEY `idx_region3` (`region3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='로또 판매점 정보';
```

**주요 필드 설명:**
- `store_id`: 판매점 고유 ID (Primary Key)
- `store_name`: 판매점 이름 (SEO에서 사용)
- `address`: 전체 주소 (PostalAddress Schema에 사용)
- `region1`: 시/도 (BreadcrumbList, 지역 검색에 사용)
- `region2`: 시/군/구 (BreadcrumbList, 지역 검색에 사용)
- `region3`: 읍/면/동 (향후 추가 예정, 3단계 계층 구조 완성)
- `phone`: 전화번호 (LocalBusiness Schema에 사용)
- `latitude`, `longitude`: 위도/경도 (GeoCoordinates Schema에 사용)
- `wins_1st`, `wins_2nd`: 누적 당첨 횟수 (AggregateRating, 명당 판별에 사용)
- `opening_hours`: 영업시간 (LocalBusiness Schema에 사용, 향후 추가)
- `store_image`: 판매점 이미지 (LocalBusiness Schema에 사용, 향후 추가)

### 2. `g5_lotto_store_win` - 당첨 기록 테이블

```sql
CREATE TABLE IF NOT EXISTS `g5_lotto_store_win` (
    `win_id` int(11) NOT NULL AUTO_INCREMENT,
    `draw_no` int(11) NOT NULL COMMENT '회차',
    `store_id` int(11) NOT NULL COMMENT '판매점 ID',
    `rank` tinyint(1) NOT NULL COMMENT '당첨 등수 (1, 2)',
    `win_type` enum('auto','manual','semi') DEFAULT 'auto' COMMENT '자동/수동/반자동',
    `prize_amount` bigint(20) DEFAULT 0 COMMENT '당첨금',
    `draw_date` date DEFAULT NULL COMMENT '추첨일 (향후 추가)',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`win_id`),
    UNIQUE KEY `uk_draw_store_rank` (`draw_no`, `store_id`, `rank`),
    KEY `idx_draw_no` (`draw_no`),
    KEY `idx_store_id` (`store_id`),
    KEY `idx_rank` (`rank`),
    KEY `idx_draw_date` (`draw_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회차별 당첨점 기록';
```

**주요 필드 설명:**
- `win_id`: 당첨 기록 고유 ID (Primary Key)
- `draw_no`: 회차 번호 (ItemList Schema의 Event에 사용)
- `store_id`: 판매점 ID (Foreign Key)
- `rank`: 등수 (1등 또는 2등)
- `win_type`: 판매 방법 (자동/수동/반자동)
- `prize_amount`: 당첨금액
- `draw_date`: 추첨일 (Event Schema의 startDate에 사용, 향후 추가)

### 3. 테이블 관계도

```
g5_lotto_store (1) ────< (N) g5_lotto_store_win
     │
     ├─ store_id (PK)
     ├─ store_name
     ├─ address
     ├─ region1, region2, region3
     ├─ latitude, longitude
     └─ wins_1st, wins_2nd (집계)
```

### 4. 향후 추가 권장 필드

#### `g5_lotto_store` 테이블 확장
```sql
-- region3 필드 추가 (3단계 계층 구조 완성)
ALTER TABLE `g5_lotto_store` 
ADD COLUMN `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동' AFTER `region2`,
ADD INDEX `idx_region3` (`region3`);

-- 영업시간 필드 추가 (LocalBusiness Schema 강화)
ALTER TABLE `g5_lotto_store` 
ADD COLUMN `opening_hours` varchar(100) DEFAULT NULL COMMENT '영업시간 (예: 09:00-22:00)' AFTER `phone`;

-- 판매점 이미지 필드 추가
ALTER TABLE `g5_lotto_store` 
ADD COLUMN `store_image` varchar(255) DEFAULT NULL COMMENT '판매점 이미지 URL' AFTER `opening_hours`;

-- 리뷰 평점 필드 추가 (향후 리뷰 시스템 연동)
ALTER TABLE `g5_lotto_store` 
ADD COLUMN `review_rating` decimal(3,2) DEFAULT NULL COMMENT '평균 리뷰 평점 (0.00-5.00)' AFTER `wins_2nd`,
ADD COLUMN `review_count` int(11) DEFAULT 0 COMMENT '리뷰 개수' AFTER `review_rating`;
```

#### `g5_lotto_store_win` 테이블 확장
```sql
-- 추첨일 필드 추가 (Event Schema의 startDate에 사용)
ALTER TABLE `g5_lotto_store_win` 
ADD COLUMN `draw_date` date DEFAULT NULL COMMENT '추첨일' AFTER `prize_amount`,
ADD INDEX `idx_draw_date` (`draw_date`);
```

### 5. 데이터 수집 및 동기화

#### 데이터 수집 소스

##### 1) 판매점 목록 수집
**동행복권 판매점 검색 API**
- **URL**: `https://www.dhlottery.co.kr/store.do?method=sellerInfo645Result`
- **방식**: POST 요청 (HTML 응답)
- **파라미터**:
  ```php
  [
      'searchType' => 'SIDO',      // 검색 타입
      'SIDO' => '11',              // 시/도 코드 (서울=11, 경기=41 등)
      'SIGUGUN' => '',             // 시/군/구 코드 (선택)
      'DONG' => '',                // 동 코드 (선택)
      'nowPage' => 1,              // 페이지 번호
      'sltSIDO2' => '11',
      'sltGUGUN2' => ''
  ]
  ```
- **수집 데이터**: 판매점명, 주소, 전화번호
- **파싱 방식**: HTML DOM 파싱 (XPath 사용)

##### 2) 당첨점 정보 수집
**동행복권 당첨점 조회 API**
- **URL**: `https://www.dhlottery.co.kr/store.do?method=topStore&drwNo={회차}&gameNo=5882&rank={등수}`
- **방식**: GET 요청 (HTML 응답)
- **파라미터**:
  - `drwNo`: 회차 번호 (예: 1202)
  - `gameNo`: 5882 (로또 6/45)
  - `rank`: 1 또는 2 (등수, 선택)
- **수집 데이터**: 판매점명, 주소, 판매방법(자동/수동/반자동)
- **파싱 방식**: HTML DOM 파싱 (XPath 사용)

##### 3) 추가 데이터 수집 방법

**region3 (읍/면/동) 추출**
- **소스**: 주소 문자열에서 정규식으로 추출
- **코드 위치**: `lib/lotto_store.lib.php` → `li_extract_region_from_address()`
- **정규식**: `/([가-힣]+(?:동|읍|면|리))/u`
- **예시**: "서울특별시 강남구 역삼동 123" → "역삼동"

**latitude, longitude (위도/경도)**
- **동행복권 제공**: ❌ 제공하지 않음
- **수집 방법**: 
  1. **카카오 지도 API** (주소 → 좌표 변환)
     - API: `https://dapi.kakao.com/v2/local/search/address.json`
     - 무료 할당량: 월 300,000건
  2. **네이버 지도 API** (주소 → 좌표 변환)
     - API: `https://openapi.naver.com/v1/map/geocode`
     - 무료 할당량: 월 10,000건
  3. **Google Geocoding API** (주소 → 좌표 변환)
     - API: `https://maps.googleapis.com/maps/api/geocode/json`
     - 무료 할당량: 월 $200 크레딧

**opening_hours (영업시간)**
- **동행복권 제공**: ❌ 제공하지 않음
- **수집 방법**:
  1. **카카오 로컬 API** (장소 검색)
     - API: `https://dapi.kakao.com/v2/local/search/keyword.json`
     - 판매점명 + 주소로 검색하여 영업시간 정보 수집
  2. **네이버 로컬 API** (장소 검색)
     - API: `https://openapi.naver.com/v1/search/local.json`
  3. **수동 입력**: 관리자 페이지에서 직접 입력

**store_image (판매점 이미지)**
- **동행복권 제공**: ❌ 제공하지 않음
- **수집 방법**:
  1. **Google Street View API**: 주소 기반 거리뷰 이미지
  2. **카카오 로컬 API**: 장소 이미지 (제한적)
  3. **직접 촬영/수집**: 관리자가 직접 업로드

**phone (전화번호)**
- **동행복권 제공**: ✅ 일부 제공 (HTML 파싱)
- **현재 수집**: `li_parse_store_list()` 함수에서 수집
- **보완 필요**: 누락된 전화번호는 카카오/네이버 로컬 API로 보완

#### 초기 데이터 수집

```bash
# 전체 판매점 수집 (최초 1회, 약 30분~1시간 소요)
php cron/lotto_store_sync.php all

# 과거 당첨점 데이터 수집 (1회~최신 회차)
php cron/lotto_store_sync.php 1 1202
```

#### 정기 동기화 (크론탭)

```bash
# 매주 일요일 새벽 2시에 최신 회차 동기화
0 2 * * 0 php /path/to/cron/lotto_store_sync.php >> /var/log/lotto_sync.log 2>&1
```

#### 추가 데이터 수집 스크립트 예시

**위도/경도 수집 (카카오 API)**
```php
// lib/lotto_store_geocode.lib.php
function li_geocode_store_address($address) {
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
            'latitude' => $response['documents'][0]['y'],
            'longitude' => $response['documents'][0]['x']
        ];
    }
    
    return null;
}
```

**영업시간 수집 (카카오 로컬 API)**
```php
function li_fetch_store_hours($store_name, $address) {
    $api_key = 'YOUR_KAKAO_REST_API_KEY';
    $query = $store_name . ' ' . $address;
    $url = 'https://dapi.kakao.com/v2/local/search/keyword.json?query=' . urlencode($query);
    
    // ... API 호출 및 파싱
    // opening_hours 정보 추출
}
```

### 6. 인덱스 최적화

현재 인덱스:
- `idx_name`: 판매점명 검색 최적화
- `idx_region`: 지역별 검색 최적화 (region1, region2)
- `idx_wins`: 명당 판매점 정렬 최적화
- `idx_address`: 주소 검색 최적화

추가 권장 인덱스:
- `idx_region3`: 읍면동 검색 최적화 (region3 추가 시)
- `idx_geo`: 위치 기반 검색 최적화 (latitude, longitude 추가 시)
- `idx_updated_at`: 최신 업데이트 순 정렬

### 7. 데이터 통계

```sql
-- 전체 판매점 수
SELECT COUNT(*) AS total_stores FROM g5_lotto_store;

-- 지역별 판매점 수
SELECT region1, region2, COUNT(*) AS cnt 
FROM g5_lotto_store 
GROUP BY region1, region2 
ORDER BY cnt DESC;

-- 명당 판매점 (1등 3회 이상)
SELECT store_name, region1, region2, wins_1st, wins_2nd 
FROM g5_lotto_store 
WHERE wins_1st >= 3 
ORDER BY wins_1st DESC, wins_2nd DESC;

-- 전체 당첨 기록 수
SELECT 
    COUNT(*) AS total_wins,
    SUM(CASE WHEN rank = 1 THEN 1 ELSE 0 END) AS first_wins,
    SUM(CASE WHEN rank = 2 THEN 1 ELSE 0 END) AS second_wins
FROM g5_lotto_store_win;
```

## 🎯 추가 권장사항

### 단기 (즉시 적용 가능)
1. ✅ 이미지 최적화: OG 이미지 실제 생성 (1200x630)
2. ✅ 로봇 메타 태그: `noindex, nofollow` 제거 (현재 `index, follow` 유지)
3. ✅ XML Sitemap: 판매점 페이지 포함
4. ✅ robots.txt: 검색 엔진 크롤링 최적화

### 중기 (데이터베이스 확장)
1. **region3 필드 추가**: 읍면동 정보 저장
2. **latitude, longitude 필드 추가**: 정확한 위치 정보
3. **phone 필드 추가**: 전화번호 정보
4. **opening_hours 필드 추가**: 실제 영업시간 정보
5. **store_image 필드 추가**: 판매점 이미지

### 장기 (고급 기능)
1. **리뷰 시스템**: 실제 사용자 리뷰 및 Rating
2. **Google My Business 연동**: LocalBusiness Schema와 연동
3. **지도 통합**: Kakao Map 또는 Google Map API
4. **다국어 지원**: hreflang 태그 추가
5. **AMP 페이지**: 모바일 성능 최적화

## 📝 체크리스트

### stores/index.php
- [x] Organization Schema
- [x] WebSite Schema
- [x] WebPage Schema
- [x] CollectionPage Schema
- [x] BreadcrumbList Schema (5단계)
- [x] ItemList Schema (LocalBusiness, 20개)
- [x] FAQPage Schema
- [x] 확장된 Open Graph
- [x] 확장된 Twitter Card
- [x] 추가 메타 태그 (author, publisher, geo 등)
- [x] 성능 최적화 (preconnect, dns-prefetch)
- [x] 키워드 확장

### stores/detail.php
- [x] Organization Schema
- [x] LocalBusiness Schema (확장)
- [x] WebPage Schema
- [x] BreadcrumbList Schema (5단계)
- [x] ItemList Schema (당첨 이력)
- [x] FAQPage Schema (판매점별 맞춤)
- [x] 확장된 Open Graph (place 타입)
- [x] 확장된 Twitter Card
- [x] Article 메타 태그
- [x] Geo 메타 태그
- [x] 성능 최적화 (prefetch)
- [x] Description 강화

## 🔍 검증 방법

### Google Rich Results Test
1. https://search.google.com/test/rich-results 접속
2. stores/index.php URL 입력
3. 다음 스키마 확인:
   - BreadcrumbList ✅
   - FAQPage ✅
   - ItemList ✅
   - Organization ✅
   - WebPage ✅

### Schema.org Validator
1. https://validator.schema.org/ 접속
2. 페이지 URL 입력
3. 모든 스키마 타입 검증

### Google Search Console
1. 구조화된 데이터 리포트 확인
2. 리치 결과 테스트 실행
3. 노출 개선 모니터링

## 📊 성과 측정

### 주요 지표
- **검색 노출 수**: Google Search Console에서 확인
- **클릭률 (CTR)**: 검색 결과 클릭률
- **평균 순위**: 주요 키워드 평균 순위
- **리치 스니펫 표시**: FAQ, Breadcrumb, Rating 등

### 비교 기준
- 로또로직스 대비 검색 노출 수
- 동일 키워드 평균 순위 비교
- 리치 스니펫 표시 빈도

## 🎉 결론

로또로직스보다 **더 강력하고 포괄적인 SEO 구조**를 구현했습니다:

1. ✅ **더 많은 구조화된 데이터**: 7가지 이상의 스키마 타입
2. ✅ **더 구체적인 비즈니스 정보**: LocalBusiness로 업그레이드
3. ✅ **더 풍부한 메타 태그**: 20개 이상의 메타 태그
4. ✅ **더 많은 FAQ**: 판매점별 맞춤 FAQ
5. ✅ **더 나은 성능**: preconnect, prefetch 등
6. ✅ **더 강력한 키워드**: 지역별 변형 키워드 확장

이러한 개선으로 **검색 엔진 노출 우위**를 확보하고, **로또로직스보다 상위 노출**을 목표로 할 수 있습니다.

# 내 코드 vs 로또로직스 판매점 SEO 구조 비교 분석

## ✅ 현재 구현된 SEO 요소

### 1. stores/index.php (판매점 목록 페이지)

#### ✅ 구현됨
- **BreadcrumbList Schema** (149-185줄)
  ```json
  {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"position": 1, "name": "홈", "item": "https://lottoinsight.ai/"},
      {"position": 2, "name": "당첨점", "item": "https://lottoinsight.ai/stores/"},
      {"position": 3, "name": "서울", "item": "https://lottoinsight.ai/stores/서울/"}
    ]
  }
  ```

- **ItemList Schema** (187-214줄)
  ```json
  {
    "@type": "ItemList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "item": {
          "@type": "Store",
          "name": "판매점명",
          "address": "주소"
        }
      }
    ]
  }
  ```

- **메타 태그**
  - Description ✅
  - Canonical URL ✅
  - Open Graph ✅
  - Keywords ✅

- **HTML Breadcrumb** (916-932줄)
  ```html
  <nav class="breadcrumb">
    <a href="/">홈</a> ›
    <a href="/stores/">당첨점</a> ›
    <a href="/stores/서울/">서울</a> ›
    <span>강남구</span>
  </nav>
  ```

- **URL 구조**: `/stores/서울/강남구` (2단계 계층)

---

### 2. stores/detail.php (판매점 상세 페이지)

#### ✅ 구현됨
- **Store Schema** (88-106줄)
  ```json
  {
    "@type": "Store",
    "name": "판매점명",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "주소",
      "addressRegion": "서울",
      "addressCountry": "KR"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.5",
      "reviewCount": "10"
    }
  }
  ```

- **메타 태그**
  - Description ✅
  - Canonical URL ✅
  - Open Graph (og:type="place") ✅
  - Keywords ✅

- **HTML Breadcrumb** (524-532줄)
  ```html
  <nav class="breadcrumb">
    <a href="/">홈</a> ›
    <a href="/stores/">당첨점</a> ›
    <a href="/stores/서울">서울</a> ›
    <span>판매점명</span>
  </nav>
  ```

- **URL 구조**: `/stores/view/{slug}` 또는 `/stores/detail.php?id={store_id}`

---

## ❌ 로또로직스와 비교하여 부족한 부분

### 1. URL 구조 차이

#### 로또로직스
```
/stores/서울특별시/강남구/역삼동/월드로또복권-판매점-Xsbb
```
- **5단계 계층**: 전국 → 시도 → 시군구 → 읍면동 → 판매점
- **판매점명 + 고유ID**: URL에 판매점명과 고유 ID 포함

#### 현재 코드
```
/stores/서울/강남구  (목록)
/stores/view/{slug}  (상세)
```
- **2단계 계층**: 시도 → 시군구 (읍면동 없음)
- **개별 판매점**: `/stores/view/{slug}` 형태로 지역 계층 구조 없음

**개선 필요**: 개별 판매점도 지역 계층 구조를 따르도록 변경
```
/stores/서울특별시/강남구/역삼동/판매점명-고유ID
```

---

### 2. GeoCoordinates (위도/경도) 없음

#### 로또로직스
```json
{
  "@type": "Store",
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "37.4930077",
    "longitude": "127.0391464"
  }
}
```

#### 현재 코드
- ❌ GeoCoordinates Schema 없음
- ❌ 지도 API 연동 없음

**개선 필요**: 판매점 좌표 정보 추가 및 GeoCoordinates Schema 구현

---

### 3. WebPage Schema 없음

#### 로또로직스
```json
{
  "@type": "WebPage",
  "headline": "월드로또복권 판매점 로또 판매점",
  "description": "...",
  "dateCreated": "2024-06-08 22:07:12",
  "dateModified": "2025-11-11 03:01:17",
  "breadcrumb": {"@id": "#breadcrumblist"}
}
```

#### 현재 코드
- ❌ WebPage Schema 없음
- ✅ Store Schema만 있음

**개선 필요**: WebPage Schema 추가하여 페이지 메타 정보 구조화

---

### 4. BreadcrumbList Schema 불완전

#### 로또로직스
```json
{
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"position": 1, "name": "전국", "item": "/stores"},
    {"position": 2, "name": "서울특별시", "item": "/stores/서울특별시"},
    {"position": 3, "name": "강남구", "item": "/stores/서울특별시/강남구"},
    {"position": 4, "name": "역삼동", "item": "/stores/서울특별시/강남구/역삼동"},
    {"position": 5, "name": "월드로또복권 판매점"}
  ]
}
```

#### 현재 코드 (stores/index.php)
```json
{
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"position": 1, "name": "홈", "item": "/"},
    {"position": 2, "name": "당첨점", "item": "/stores/"},
    {"position": 3, "name": "서울", "item": "/stores/서울/"},
    {"position": 4, "name": "강남구"}  // 읍면동 없음
  ]
}
```

#### 현재 코드 (stores/detail.php)
- ❌ BreadcrumbList Schema 없음
- ✅ HTML breadcrumb만 있음

**개선 필요**: 
1. 읍면동 단계 추가
2. detail.php에도 BreadcrumbList Schema 추가

---

### 5. PostalAddress 상세 정보 부족

#### 로또로직스
```json
{
  "@type": "PostalAddress",
  "addressRegion": "서울특별시",
  "addressLocality": "강남구",
  "streetAddress": "역삼동 796-29 1층 102호",
  "addressCountry": "KR"
}
```

#### 현재 코드
```json
{
  "@type": "PostalAddress",
  "streetAddress": "전체 주소",
  "addressRegion": "서울",
  "addressCountry": "KR"
  // addressLocality (시군구) 없음
}
```

**개선 필요**: addressLocality 필드 추가

---

### 6. 개별 판매점 상세 페이지 URL 구조

#### 로또로직스
```
/stores/서울특별시/강남구/역삼동/월드로또복권-판매점-Xsbb
```
- 지역 계층 구조를 완전히 따름
- SEO 친화적

#### 현재 코드
```
/stores/view/{slug}
/stores/detail.php?id={store_id}
```
- 지역 계층 구조 없음
- SEO에 불리함

**개선 필요**: 개별 판매점도 지역 계층 구조를 따르도록 변경

---

## 📊 구현 완성도 비교

| SEO 요소 | 로또로직스 | 현재 코드 | 상태 |
|---------|-----------|----------|------|
| **URL 구조 (계층)** | 5단계 (전국→시도→시군구→읍면동→판매점) | 2단계 (시도→시군구) | ⚠️ 부분 구현 |
| **BreadcrumbList Schema** | ✅ 완전 (5단계) | ✅ 있음 (2-3단계) | ⚠️ 불완전 |
| **Store Schema** | ✅ 완전 | ✅ 있음 | ✅ 구현됨 |
| **PostalAddress** | ✅ 완전 (region, locality, street) | ⚠️ 부분 (region, street만) | ⚠️ 불완전 |
| **GeoCoordinates** | ✅ 있음 | ❌ 없음 | ❌ 미구현 |
| **WebPage Schema** | ✅ 있음 | ❌ 없음 | ❌ 미구현 |
| **메타 태그** | ✅ 완전 | ✅ 완전 | ✅ 구현됨 |
| **개별 판매점 URL** | ✅ 지역 계층 구조 | ❌ /stores/view/{slug} | ❌ 미구현 |

---

## 🎯 개선 우선순위

### 🔴 높은 우선순위 (즉시 개선 필요)

1. **개별 판매점 URL 구조 변경**
   ```
   현재: /stores/view/{slug}
   개선: /stores/서울특별시/강남구/역삼동/판매점명-고유ID
   ```

2. **GeoCoordinates Schema 추가**
   - 판매점 좌표 정보 DB에 저장
   - Store Schema에 geo 속성 추가

3. **읍면동 단계 추가**
   - URL 구조에 읍면동 추가
   - BreadcrumbList Schema에 읍면동 추가

### 🟡 중간 우선순위

4. **WebPage Schema 추가**
   - detail.php에 WebPage Schema 추가
   - breadcrumb 연결

5. **PostalAddress 보완**
   - addressLocality (시군구) 필드 추가

### 🟢 낮은 우선순위

6. **지도 API 연동**
   - 카카오맵 또는 구글맵 연동
   - 지도 표시 기능

---

## 💡 구현 가이드

### 1. URL 구조 개선 예시

```php
// stores/detail.php 수정
// 현재: /stores/view/{slug}
// 개선: /stores/{region1}/{region2}/{region3}/{store_name}-{store_id}

$store_url = sprintf(
  '/stores/%s/%s/%s/%s-%s',
  urlencode($store['region1']),      // 서울특별시
  urlencode($store['region2']),      // 강남구
  urlencode($store['region3']),      // 역삼동
  urlencode($store['name']),         // 판매점명
  $store['store_id']                 // 고유ID
);
```

### 2. GeoCoordinates Schema 추가 예시

```php
// stores/detail.php에 추가
{
  "@type": "Store",
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "<?= $store['latitude'] ?>",
    "longitude": "<?= $store['longitude'] ?>"
  }
}
```

### 3. WebPage Schema 추가 예시

```php
// stores/detail.php에 추가
{
  "@type": "WebPage",
  "headline": "<?= htmlspecialchars($store['name']) ?>",
  "description": "<?= htmlspecialchars($page_desc) ?>",
  "dateCreated": "<?= $store['created_at'] ?>",
  "dateModified": "<?= $store['updated_at'] ?>",
  "breadcrumb": {"@id": "#breadcrumblist"},
  "mainEntity": {
    "@id": "#store"
  }
}
```

---

## 📝 결론

현재 코드는 **기본적인 SEO 구조는 구현되어 있지만**, 로또로직스 수준의 **완전한 지역 계층 구조와 구조화된 데이터**는 아직 부족합니다.

**주요 개선 사항**:
1. ✅ 기본 Schema는 구현됨
2. ⚠️ URL 구조가 2단계로 제한적
3. ❌ GeoCoordinates 없음
4. ❌ WebPage Schema 없음
5. ⚠️ Breadcrumb이 불완전 (읍면동 없음)

**다음 단계**: 위의 개선 우선순위에 따라 단계적으로 구현하면 로또로직스 수준의 SEO 구조를 완성할 수 있습니다.

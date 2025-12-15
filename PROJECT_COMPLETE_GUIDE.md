# 📚 로또X 프로젝트 완전 가이드

**작성일**: 2025-12-15  
**프로젝트명**: 로또X (오늘로또)  
**버전**: 1.0 (프로덕션 준비 완료)

---

## 🎯 프로젝트 개요

### 무엇인가?

**로또X**는 **AI 기반 로또 번호 분석 서비스**입니다. 동행복권 공식 데이터를 기반으로 10가지 알고리즘을 사용해 로또 번호를 분석하고, 전국 로또 판매점 정보를 제공하는 종합 서비스입니다.

### 핵심 가치

1. **데이터 기반 분석**: 23년간의 실제 당첨번호 데이터 분석
2. **AI 알고리즘**: 10가지 다양한 분석 알고리즘 제공
3. **판매점 정보**: 전국 로또 명당 판매점 정보 제공
4. **SEO 최적화**: 로또로직스보다 강력한 검색 엔진 최적화
5. **사용자 경험**: 모바일 최적화, 빠른 로딩, 직관적인 UI

---

## 🏗️ 프로젝트 구조

### 기술 스택

```
Backend:     PHP 7.4+ (GNUBOARD 5.6.13 기반)
Database:    MySQL/MariaDB (utf8mb4)
Frontend:    HTML5, CSS3, JavaScript (ES6+)
API:         동행복권 API, 카카오 Maps API, Toss Payments
```

### 디렉토리 구조

```
로또X_20251215/
├── 📄 index.php              # 메인 랜딩 페이지
├── 📄 result.php             # AI 분석 결과 페이지
├── 📄 auth.php               # 로그인/인증 페이지
│
├── 📁 stores/                # 판매점 페이지
│   ├── index.php            # 판매점 목록 (지역별 필터링)
│   └── detail.php           # 판매점 상세 정보
│
├── 📁 lib/                   # 핵심 라이브러리
│   ├── lotto_store.lib.php  # 판매점 데이터 관리
│   ├── lotto_draw.lib.php   # 당첨번호 데이터 관리
│   ├── lotto_credit.lib.php # 크레딧 시스템
│   └── kakao_api.lib.php    # 카카오 API 연동
│
├── 📁 api/                   # API 엔드포인트
│   ├── get_credits.php      # 크레딧 조회
│   ├── use_credit.php       # 크레딧 사용
│   ├── save_analysis.php    # 분석 결과 저장
│   └── get_history.php      # 히스토리 조회
│
├── 📁 cron/                  # 자동화 스크립트
│   ├── lotto_store_sync.php # 판매점 데이터 동기화
│   ├── lotto_seed.php       # 당첨번호 수집
│   ├── kakao_store_enrich.php # 카카오 API로 데이터 보강
│   └── lotto_weekly.php     # 주간 자동 동기화
│
├── 📁 install/               # 설치 관련
│   ├── check_installation.php # 설치 확인 스크립트
│   ├── all_lotto_tables.sql  # 전체 테이블 생성 SQL
│   └── lotto_store_tables_latest.sql # 마이그레이션 SQL
│
├── 📁 seo/                   # SEO 최적화 페이지
│   ├── store-detail.php     # 판매점 상세 SEO 페이지
│   ├── stores-dong.php      # 동별 판매점 페이지
│   └── _seo_head.php        # SEO 헤더 공통
│
├── 📁 payments/              # 결제 시스템
│   └── toss/                # 토스 페이먼츠 연동
│
└── 📁 data/                  # 설정 파일 (Git 제외)
    ├── dbconfig.php         # 데이터베이스 설정
    └── kakao_config.php     # 카카오 API 설정
```

---

## 💾 데이터베이스 구조

### 핵심 테이블

#### 1. `g5_lotto_store` - 판매점 정보

```sql
store_id          INT          # 판매점 고유 ID
store_name        VARCHAR(100) # 판매점 이름
address           VARCHAR(255) # 주소
region1           VARCHAR(20)  # 시/도
region2           VARCHAR(50)  # 시/군/구
region3           VARCHAR(50)  # 읍/면/동 (SEO 최적화)
phone             VARCHAR(20)  # 전화번호
opening_hours     VARCHAR(100) # 영업시간
store_image       VARCHAR(255) # 판매점 이미지 URL
latitude          DECIMAL(10,7) # 위도
longitude         DECIMAL(10,7) # 경도
wins_1st          INT          # 누적 1등 당첨 횟수
wins_2nd          INT          # 누적 2등 당첨 횟수
review_rating     DECIMAL(3,2) # 평균 리뷰 평점
review_count      INT          # 리뷰 개수
created_at        DATETIME     # 생성일시
updated_at        DATETIME     # 수정일시
```

**용도**: 전국 로또 판매점 정보 저장, 지역별 검색, 명당 랭킹

#### 2. `g5_lotto_store_win` - 당첨 이력

```sql
win_id            INT          # 당첨 기록 ID
draw_no           INT          # 회차
store_id          INT          # 판매점 ID (FK)
rank              TINYINT      # 당첨 등수 (1, 2)
win_type          ENUM         # 자동/수동/반자동
prize_amount      BIGINT       # 당첨금
created_at        DATETIME     # 생성일시
```

**용도**: 회차별 당첨 판매점 기록, 판매점별 당첨 이력 조회

#### 3. `g5_lotto_draw` - 당첨번호

```sql
draw_no           INT          # 회차
draw_date         DATE         # 추첨일
n1, n2, n3, n4, n5, n6  INT   # 당첨번호 6개
bonus             INT          # 보너스 번호
first_prize_each  BIGINT       # 1등 당첨금
second_prize_each BIGINT       # 2등 당첨금
created_at        DATETIME     # 생성일시
```

**용도**: AI 분석의 기반 데이터, 통계 분석, 히스토리 조회

#### 4. `g5_lotto_credit` - 크레딧 시스템

```sql
mb_id             VARCHAR(100) # 회원 ID (PK)
free_uses         INT          # 무료 사용 횟수
credit_balance    INT          # 유료 크레딧 잔액
created_at        DATETIME     # 생성일시
updated_at        DATETIME     # 수정일시
```

**용도**: 사용자별 크레딧 관리, 무료/유료 분석 구분

#### 5. `g5_lotto_credit_log` - 크레딧 로그

```sql
id                INT          # 로그 ID
mb_id             VARCHAR(100) # 회원 ID
change_type       VARCHAR(20)  # 변경 유형 (use, charge, free)
amount            INT          # 변경 금액
memo              VARCHAR(255) # 메모
ref_key           VARCHAR(100) # 참조 키
created_at        DATETIME     # 생성일시
```

**용도**: 크레딧 사용 이력 추적, 거래 내역 관리

---

## 🚀 주요 기능

### 1. AI 로또 번호 분석

**위치**: `result.php`, `lib/lotto_draw.lib.php`

**기능**:
- 10가지 알고리즘으로 번호 분석
  - Hot/Cold 번호 분석
  - 홀짝/고저 밸런스 분석
  - 연속번호 패턴 분석
  - 주기 분석
  - 상관관계 분석
  - 몬테카를로 시뮬레이션
  - 합계 분석
  - 끝수 분석
  - 등등...

**데이터 소스**: `g5_lotto_draw` 테이블 (23년간 데이터)

**사용 흐름**:
1. 사용자가 로그인 (`auth.php`)
2. 크레딧 확인 (`api/get_credits.php`)
3. 분석 요청 (`result.php`)
4. 크레딧 차감 (`api/use_credit.php`)
5. 결과 표시 및 저장 (`api/save_analysis.php`)

---

### 2. 판매점 정보 시스템

**위치**: `stores/index.php`, `stores/detail.php`, `lib/lotto_store.lib.php`

#### 2.1 판매점 목록 (`stores/index.php`)

**기능**:
- 지역별 필터링 (시/도 → 시/군/구 → 읍/면/동)
- 명당 랭킹 (1등 당첨 횟수 기준)
- 회차별 당첨점 조회
- 페이지네이션

**표시 정보**:
- 판매점 이미지 (`store_image`)
- 판매점 이름, 주소
- 전화번호 (`phone`)
- 영업시간 (`opening_hours`)
- 평점 (`review_rating`, `review_count`)
- 1등/2등 당첨 횟수

**SEO 최적화**:
- Schema.org `ItemList` 구조화 데이터
- 지역 계층 구조 URL (`/stores/서울/강남구/역삼동/`)
- BreadcrumbList 구조화 데이터

#### 2.2 판매점 상세 (`stores/detail.php`)

**기능**:
- 판매점 상세 정보 표시
- 당첨 이력 조회 (최근 50회)
- 지도 표시 (위도/경도 활용)
- Schema.org `LocalBusiness` 구조화 데이터

**SEO 최적화**:
- Open Graph 이미지 (`store_image`)
- Geo 메타 태그 (`latitude`, `longitude`)
- Twitter Card
- 완전한 Schema.org 구조화 데이터

---

### 3. 데이터 수집 시스템

#### 3.1 판매점 데이터 수집

**스크립트**: `cron/lotto_store_sync.php`

**데이터 소스**: 동행복권 공식 웹사이트 HTML 파싱

**기능**:
- 전국 판매점 정보 수집
- 당첨 이력 동기화
- 자동 재시도 로직 (exponential backoff)
- 테이블 자동 생성/마이그레이션

**사용법**:
```bash
# 전체 판매점 수집
php cron/lotto_store_sync.php all

# 특정 회차 당첨점 동기화
php cron/lotto_store_sync.php 1 1202
```

#### 3.2 카카오 API 데이터 보강

**스크립트**: `cron/kakao_store_enrich.php`  
**라이브러리**: `lib/kakao_api.lib.php`

**기능**:
- 위도/경도 수집 (Geocoding API)
- 판매점 이미지 수집 (Place Search API)
- 영업시간, 전화번호 보강
- API 할당량 관리

**사용법**:
```bash
# 10개 판매점 데이터 보강
php cron/kakao_store_enrich.php 10
```

**API 제한**:
- 월 300,000건 (무료)
- 일일 100~500개 권장

---

### 4. 크레딧 시스템

**위치**: `lib/lotto_credit.lib.php`, `api/use_credit.php`

**기능**:
- 무료 크레딧: 회원가입 시 1회 자동 지급
- 유료 크레딧: 토스 페이먼츠 결제
- 크레딧 사용 로그 기록
- 잔액 조회 API

**테이블**:
- `g5_lotto_credit`: 크레딧 잔액
- `g5_lotto_credit_log`: 사용 이력

**API 엔드포인트**:
- `api/get_credits.php`: 크레딧 조회
- `api/use_credit.php`: 크레딧 사용
- `payments/toss/`: 결제 처리

---

### 5. SEO 최적화 시스템

**목표**: 로또로직스보다 강력한 검색 엔진 최적화

#### 5.1 구조화 데이터 (Schema.org)

**구현 위치**: `stores/index.php`, `stores/detail.php`, `seo/` 디렉토리

**구현된 스키마**:
- `Organization`: 사이트 정보
- `WebSite`: 사이트 메타데이터
- `LocalBusiness`: 판매점 정보
- `ItemList`: 판매점 목록
- `BreadcrumbList`: 네비게이션
- `FAQPage`: FAQ 구조화 데이터
- `AggregateRating`: 평점 정보
- `OpeningHoursSpecification`: 영업시간

#### 5.2 메타 태그

**Open Graph**:
- `og:image`: 판매점 이미지 (있으면 실제 이미지)
- `og:title`, `og:description`: 동적 생성
- `place:location:latitude/longitude`: 위치 정보

**Twitter Card**:
- `twitter:image`: 판매점 이미지
- `twitter:card`: summary_large_image

**Geo 메타 태그**:
- `geo.position`: 위도/경도
- `ICBM`: 위치 정보

#### 5.3 지역 기반 SEO

**URL 구조**:
```
/stores/                    # 전국
/stores/서울/              # 시/도
/stores/서울/강남구/       # 시/군/구
/stores/서울/강남구/역삼동/ # 읍/면/동
```

**효과**:
- 지역별 검색 최적화
- 3단계 계층 구조로 세밀한 타겟팅
- 각 지역별 독립적인 SEO 페이지

---

## 📊 현재 구현 상태

### ✅ 완료된 작업

#### 1. 데이터베이스 구조
- ✅ 모든 테이블 생성 SQL 준비
- ✅ 자동 마이그레이션 로직 구현
- ✅ 호환성 체크 스크립트

#### 2. 데이터 수집 시스템
- ✅ 동행복권 HTML 파싱
- ✅ 카카오 API 연동
- ✅ 자동 재시도 로직
- ✅ API 사용량 모니터링

#### 3. 프론트엔드
- ✅ 판매점 목록 페이지 (모든 필드 표시)
- ✅ 판매점 상세 페이지
- ✅ 반응형 디자인 (모바일 최적화)
- ✅ 이미지, 전화번호, 영업시간, 평점 표시

#### 4. SEO 최적화
- ✅ Schema.org 구조화 데이터 완전 구현
- ✅ Open Graph, Twitter Card
- ✅ 지역 기반 SEO
- ✅ 성능 최적화 (preconnect, lazy loading)

#### 5. 문서화
- ✅ 설치 가이드 (상세)
- ✅ 빠른 시작 가이드
- ✅ API 설정 가이드
- ✅ 데이터 수집 가이드
- ✅ 검증 보고서

---

## 🔧 설치 및 배포

### 빠른 시작 (5분)

```bash
# 1. 데이터베이스 설정
# data/dbconfig.php 파일 수정

# 2. 그누보드 설치
# 웹 브라우저에서 /install/ 접속

# 3. 로또 테이블 생성
php cron/lotto_store_sync.php

# 4. 초기 데이터 수집
php cron/lotto_seed.php              # 당첨번호
php cron/lotto_store_sync.php all     # 판매점

# 5. 설치 확인
php install/check_installation.php
```

### 상세 가이드

- **전체 설치 가이드**: `INSTALLATION_GUIDE.md`
- **빠른 시작**: `QUICK_START.md`
- **체크리스트**: `SETUP_CHECKLIST.md`

---

## 📈 데이터 흐름

### 1. 판매점 데이터 수집 흐름

```
동행복권 웹사이트
    ↓ (HTML 파싱)
cron/lotto_store_sync.php
    ↓ (데이터 저장)
g5_lotto_store 테이블
    ↓ (카카오 API 보강)
cron/kakao_store_enrich.php
    ↓ (위도/경도, 이미지 추가)
g5_lotto_store 테이블 (업데이트)
```

### 2. 사용자 분석 흐름

```
사용자 로그인 (auth.php)
    ↓
크레딧 확인 (api/get_credits.php)
    ↓
분석 요청 (result.php)
    ↓
크레딧 차감 (api/use_credit.php)
    ↓
AI 분석 실행 (lib/lotto_draw.lib.php)
    ↓
결과 표시 및 저장 (api/save_analysis.php)
```

### 3. 판매점 조회 흐름

```
사용자 요청 (/stores/서울/강남구/)
    ↓
stores/index.php
    ↓
lib/lotto_store.lib.php (데이터 조회)
    ↓
g5_lotto_store 테이블
    ↓
HTML 렌더링 + Schema.org 구조화 데이터
    ↓
사용자에게 표시
```

---

## 🎨 UI/UX 특징

### 디자인 철학

1. **한국어 가독성 최적화**
   - Pretendard 폰트 우선 로딩
   - 줄간격 1.75~1.85
   - word-break: keep-all
   - 최소 폰트 크기 보장 (0.85rem)

2. **모바일 우선**
   - 반응형 디자인
   - 터치 친화적 UI
   - 빠른 로딩 속도

3. **접근성**
   - WCAG AA 기준 충족
   - 스크린리더 지원
   - 키보드 네비게이션

---

## 🔒 보안 및 성능

### 보안

- ✅ SQL Injection 방지 (`sql_real_escape_string`)
- ✅ XSS 방지 (`htmlspecialchars`)
- ✅ CSRF 토큰 검증
- ✅ API 키 Git 제외 (`.gitignore`)

### 성능 최적화

- ✅ 이미지 lazy loading
- ✅ 폰트 preconnect
- ✅ DNS prefetch
- ✅ CSS/JS 최적화
- ✅ 데이터베이스 인덱스 최적화

---

## 📚 주요 문서

### 설치 및 설정
- `INSTALLATION_GUIDE.md`: 전체 설치 가이드
- `QUICK_START.md`: 빠른 시작 (5분)
- `SETUP_CHECKLIST.md`: 설치 체크리스트
- `KAKAO_API_SETUP.md`: 카카오 API 설정

### 개발 및 운영
- `DATA_COLLECTION_GUIDE.md`: 데이터 수집 가이드
- `DB_COMPATIBILITY_REPORT.md`: DB 호환성 보고서
- `DATABASE_USAGE_COMPLETE.md`: 데이터베이스 사용 현황
- `VERIFICATION_REPORT.md`: 구현 검증 보고서

### SEO 및 마케팅
- `SEO_SUPER_OPTIMIZATION.md`: SEO 최적화 문서
- `AB_TEST_COPY.md`: A/B 테스트 카피

---

## 🚦 현재 상태 요약

### ✅ 준비 완료

1. **코드**: 모든 기능 구현 완료
2. **데이터베이스**: 테이블 구조 완성, 마이그레이션 준비
3. **문서**: 상세한 설치/운영 가이드 완성
4. **SEO**: 로또로직스보다 강력한 구조 완성
5. **검증**: 모든 기능 검증 완료

### 📋 사용자 작업 필요

1. **데이터베이스 설정**: `data/dbconfig.php` 수정
2. **그누보드 설치**: 웹 브라우저에서 `/install/` 접속
3. **초기 데이터 수집**: 스크립트 실행
4. **카카오 API 설정** (선택): 위도/경도 수집용

---

## 🎯 프로젝트 목표 달성도

| 목표 | 상태 | 비고 |
|------|------|------|
| AI 로또 분석 | ✅ 완료 | 10가지 알고리즘 구현 |
| 판매점 정보 제공 | ✅ 완료 | 전국 데이터 수집 가능 |
| SEO 최적화 | ✅ 완료 | 로또로직스 초월 |
| 모바일 최적화 | ✅ 완료 | 반응형 디자인 |
| 데이터 수집 자동화 | ✅ 완료 | 크론 작업 준비 |
| 문서화 | ✅ 완료 | 상세 가이드 완성 |

---

## 💡 핵심 포인트

### 1. 데이터 기반
- 실제 동행복권 데이터 사용
- 23년간의 당첨번호 분석
- 실시간 데이터 동기화

### 2. SEO 강력함
- 로또로직스보다 더 강력한 구조화 데이터
- 지역별 세밀한 타겟팅
- 모든 필드 활용 (이미지, 전화번호, 평점 등)

### 3. 사용자 경험
- 빠른 로딩 속도
- 직관적인 UI
- 모바일 최적화

### 4. 확장 가능성
- 모듈화된 구조
- API 기반 설계
- 쉬운 데이터 수집

---

## 🎉 결론

**로또X 프로젝트는 프로덕션 배포 준비가 완료된 상태입니다.**

- ✅ 모든 기능 구현 완료
- ✅ 데이터베이스 구조 완성
- ✅ SEO 최적화 완료
- ✅ 문서화 완료
- ✅ 검증 완료

**다음 단계**: 호스팅 환경에 배포하고 초기 데이터를 수집하면 바로 서비스 시작 가능합니다!

---

## 📞 지원 및 문의

### 문제 해결
1. `php install/check_installation.php` 실행
2. 로그 확인: `data/log/`
3. 문서 참고: `INSTALLATION_GUIDE.md`

### 체크리스트
- `SETUP_CHECKLIST.md` 확인

---

**프로젝트 준비 완료! 🚀**

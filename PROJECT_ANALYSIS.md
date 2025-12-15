# 오늘로또 프로젝트 상세 분석 문서

## 📋 목차
1. [프로젝트 개요](#프로젝트-개요)
2. [시스템 아키텍처](#시스템-아키텍처)
3. [데이터베이스 구조](#데이터베이스-구조)
4. [핵심 기능 상세 분석](#핵심-기능-상세-분석)
5. [API 엔드포인트](#api-엔드포인트)
6. [프론트엔드 구조](#프론트엔드-구조)
7. [크레딧 시스템](#크레딧-시스템)
8. [결제 시스템](#결제-시스템)
9. [데이터 수집 파이프라인](#데이터-수집-파이프라인)
10. [로또 분석 알고리즘](#로또-분석-알고리즘)

---

## 프로젝트 개요

### 기본 정보
- **프로젝트명**: 오늘로또 (LottoInsight.ai)
- **기반 프레임워크**: GNUBOARD 5.6.13
- **주요 기능**: AI 기반 로또 번호 분석 서비스
- **데이터 소스**: 동행복권 공식 API (1회차 ~ 1,201회차)
- **언어**: PHP 7.4+, JavaScript (ES6+)
- **데이터베이스**: MySQL/MariaDB

### 서비스 특징
- 동행복권 공식 데이터 기반 통계 분석
- 10가지 이상의 분석 알고리즘 제공
- 무료/유료 크레딧 시스템
- 카카오 간편 로그인
- 토스페이먼츠 결제 연동
- 실시간 회차 업데이트 (주간 크론)

---

## 시스템 아키텍처

### 디렉토리 구조

```
/
├── index.php              # 메인 랜딩 페이지
├── auth.php               # 카카오 로그인 페이지
├── result.php             # 분석 결과 페이지 (메인 기능)
├── common.php             # GNUBOARD 공통 파일
│
├── api/                   # RESTful API 엔드포인트
│   ├── save_analysis.php  # 분석 결과 저장
│   ├── get_history.php    # 분석 히스토리 조회
│   ├── get_credits.php    # 크레딧 조회
│   ├── use_credit.php     # 크레딧 사용
│   └── toss/              # 토스 결제 API
│
├── lib/                   # 핵심 라이브러리
│   ├── lotto_draw.lib.php      # 로또 회차 데이터 처리
│   ├── lotto_credit.lib.php    # 크레딧 관리
│   └── lotto_store.lib.php     # 판매점 정보 관리
│
├── cron/                  # 스케줄 작업
│   ├── lotto_weekly.php        # 주간 회차 업데이트
│   ├── lotto_store_sync.php    # 판매점 데이터 동기화
│   └── lotto_seed.php          # 초기 데이터 시딩
│
├── scripts/               # 프론트엔드 JavaScript
│   ├── lotto-generator.js  # AI 분석 엔진 (클라이언트)
│   ├── lotto-data.js      # 로또 히스토리 데이터
│   └── gnuboard-api.js    # GNUBOARD API 래퍼
│
├── styles/                # CSS 스타일시트
│   └── shared.css         # 공통 스타일
│
├── bbs/                   # GNUBOARD 게시판
├── adm/                   # 관리자 페이지
├── mobile/                # 모바일 페이지
└── theme/                 # 테마 파일
```

### 페이지 플로우

```
사용자 접속
    ↓
[index.php] 랜딩 페이지
    ├─ SEO 최적화 메타데이터
    ├─ 최신 회차 정보 표시
    └─ 카카오 로그인 유도
    ↓
[auth.php] 로그인 페이지
    ├─ 카카오 SDK 로그인
    └─ GNUBOARD 회원 연동
    ↓
[result.php] 분석 결과 페이지
    ├─ 크레딧 확인
    ├─ AI 분석 실행 (JavaScript)
    ├─ 결과 저장 (API 호출)
    └─ 히스토리 조회
```

---

## 데이터베이스 구조

### 주요 테이블

#### 1. `g5_lotto_draw` - 로또 회차 데이터
```sql
CREATE TABLE g5_lotto_draw (
    draw_no INT PRIMARY KEY,           -- 회차 번호 (1~1201)
    draw_date DATE,                    -- 추첨일
    n1, n2, n3, n4, n5, n6 INT,        -- 당첨번호 6개
    bonus INT,                         -- 보너스 번호
    first_prize_amount BIGINT,        -- 1등 당첨금
    first_prize_winners INT,           -- 1등 당첨자 수
    second_prize_amount BIGINT,        -- 2등 당첨금
    second_prize_winners INT,          -- 2등 당첨자 수
    third_prize_amount BIGINT,         -- 3등 당첨금
    third_prize_winners INT,           -- 3등 당첨자 수
    created_at DATETIME,
    updated_at DATETIME
);
```

#### 2. `g5_lotto_credit` - 회원 크레딧
```sql
CREATE TABLE g5_lotto_credit (
    mb_id VARCHAR(100) PRIMARY KEY,    -- 회원 ID
    free_uses INT DEFAULT 2,           -- 무료 사용 횟수 (신규 2회)
    credit_balance INT DEFAULT 0,      -- 유료 크레딧 잔액
    created_at DATETIME,
    updated_at DATETIME
);
```

#### 3. `g5_lotto_credit_log` - 크레딧 사용 로그
```sql
CREATE TABLE g5_lotto_credit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mb_id VARCHAR(100),
    change_type VARCHAR(20),           -- 'free', 'use', 'charge', 'admin_adjust'
    amount INT,                        -- 변경량 (-1, +10 등)
    before_balance INT,                -- 변경 전 잔액
    after_balance INT,                 -- 변경 후 잔액
    memo VARCHAR(255),                 -- 메모
    ref_key VARCHAR(100),              -- 참조키 (회차 등)
    ip VARCHAR(50),
    created_at DATETIME
);
```

#### 4. `g5_lotto_analysis` - 사용자 분석 결과
```sql
CREATE TABLE g5_lotto_analysis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mb_id VARCHAR(100),                -- 회원 ID
    lotto_round INT,                   -- 분석 대상 회차
    numbers VARCHAR(50),               -- 추천 번호 (콤마 구분)
    score INT,                         -- 분석 점수 (50~100)
    strategy VARCHAR(100),              -- 사용된 전략
    is_winner TINYINT(1) DEFAULT 0,   -- 당첨 여부
    match_count INT DEFAULT 0,        -- 일치 개수
    created_at DATETIME
);
```

#### 5. `g5_lotto_analysis_log` - 분석 실행 로그
```sql
CREATE TABLE g5_lotto_analysis_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mb_id VARCHAR(100),
    round_no INT,                      -- 분석한 회차
    user_ip VARCHAR(50),
    created_at DATETIME
);
```

#### 6. `g5_lotto_ai_recommend` - AI 추천 번호 (주간)
```sql
CREATE TABLE g5_lotto_ai_recommend (
    round INT PRIMARY KEY,             -- 회차
    a1, a2, a3, a4, a5, a6 INT,        -- AI 추천 번호
    created_at DATETIME
);
```

#### 7. `g5_lotto_store` - 로또 판매점 정보
```sql
CREATE TABLE g5_lotto_store (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_name VARCHAR(255),           -- 판매점명
    address VARCHAR(500),               -- 주소
    phone VARCHAR(50),
    lat DECIMAL(10,8),                 -- 위도
    lng DECIMAL(11,8),                 -- 경도
    win_count INT DEFAULT 0,           -- 1등 당첨 횟수
    created_at DATETIME,
    updated_at DATETIME
);
```

#### 8. `g5_lotto_toss_orders` - 토스 결제 주문
```sql
CREATE TABLE g5_lotto_toss_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100) UNIQUE,      -- 주문번호
    mb_id VARCHAR(100),                -- 회원 ID
    product_code VARCHAR(50),           -- 상품 코드
    amount INT,                        -- 결제 금액
    credit_qty INT,                    -- 충전 크레딧 수
    status VARCHAR(20),                -- 'READY', 'SUCCESS', 'FAIL'
    created_at DATETIME,
    updated_at DATETIME
);
```

---

## 핵심 기능 상세 분석

### 1. 메인 랜딩 페이지 (`index.php`)

**주요 기능:**
- SEO 최적화 (메타 태그, 구조화된 데이터, FAQ 스키마)
- 최신 회차 정보 동적 표시
- 카카오 로그인 유도
- 실시간 대기열 번호 표시

**핵심 코드:**
```php
// 최신 회차 조회
$row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($row['max_round'] ?? 0);
$total_numbers = $max_round * 6; // 총 분석 데이터 수
```

**SEO 요소:**
- Open Graph 메타 태그
- Twitter Card 메타 태그
- FAQPage 구조화된 데이터 (JSON-LD)
- WebApplication 스키마
- Canonical URL 설정

### 2. 로그인 페이지 (`auth.php`)

**주요 기능:**
- 카카오 SDK 로그인 연동
- GNUBOARD 회원 시스템 통합
- 로그인 후 `result.php`로 리다이렉트

**인증 플로우:**
1. 카카오 SDK 초기화
2. 사용자 로그인 요청
3. 카카오 액세스 토큰 획득
4. GNUBOARD 회원 정보 연동/생성
5. 세션 생성 및 리다이렉트

### 3. 분석 결과 페이지 (`result.php`)

**주요 기능:**
- 크레딧 확인 및 사용
- AI 분석 실행 (JavaScript)
- 분석 결과 저장 (AJAX)
- 히스토리 조회
- 최근 AI 추천 아카이브 표시

**크레딧 사용 플로우:**
```php
// POST 요청 처리
if ($_POST['mode'] === 'use_credit') {
    $use = lotto_use_one_analysis(
        $member['mb_id'],
        'AI 분석 실행 (회차 '.$round_no.')',
        'round_'.$round_no
    );
    
    // 분석 로그 기록
    sql_query("INSERT INTO g5_lotto_analysis_log ...");
}
```

**데이터 로딩:**
- 최근 120회차 데이터 로드
- AI 추천 아카이브 (최근 8회)
- 회원 크레딧 정보
- JavaScript에 데이터 전달 (`LOTTO_HISTORY_DATA`)

---

## API 엔드포인트

### 1. 분석 결과 저장
**엔드포인트**: `POST /api/save_analysis.php`

**요청:**
```json
{
    "numbers": [1, 2, 3, 4, 5, 6],
    "round": 1202,
    "score": 85,
    "strategy": "balanced"
}
```

**응답:**
```json
{
    "success": true,
    "message": "Analysis saved",
    "id": 12345
}
```

**기능:**
- 번호 유효성 검사 (1~45, 6개, 중복 없음)
- 테이블 자동 생성 (없을 경우)
- 회원별 분석 결과 저장

### 2. 분석 히스토리 조회
**엔드포인트**: `GET /api/get_history.php?page=1&limit=20`

**응답:**
```json
{
    "success": true,
    "history": [
        {
            "id": 123,
            "round": 1201,
            "numbers": [1, 2, 3, 4, 5, 6],
            "score": 85,
            "strategy": "balanced",
            "created_at": "2025-12-15 10:30:00"
        }
    ],
    "total": 50,
    "page": 1,
    "limit": 20,
    "total_pages": 3
}
```

### 3. 크레딧 조회
**엔드포인트**: `GET /api/get_credits.php`

**응답:**
```json
{
    "success": true,
    "user_id": "user123",
    "credits": 500,
    "analysis_count": 5,
    "is_premium": false
}
```

**참고:** 이 API는 GNUBOARD 포인트 시스템을 사용합니다 (100포인트 = 1회 분석)

### 4. 크레딧 사용
**엔드포인트**: `POST /api/use_credit.php`

**기능:**
- 100포인트 차감
- GNUBOARD `insert_point()` 함수 사용
- 잔액 확인 및 차감

### 5. 토스 결제 주문 생성
**엔드포인트**: `POST /api/toss/create_order.php`

**요청:**
```json
{
    "product": "CREDIT_20"
}
```

**상품 목록:**
- `CREDIT_5`: 1,000원 → 5회
- `CREDIT_20`: 2,500원 → 20회
- `CREDIT_50`: 5,000원 → 50회

**응답:**
```json
{
    "ok": true,
    "clientKey": "test_ck_...",
    "orderId": "LC20251215123456...",
    "amount": 2500,
    "orderName": "크레딧 20회 충전",
    "successUrl": "https://lottoinsight.ai/payments/toss/success.php",
    "failUrl": "https://lottoinsight.ai/payments/toss/fail.php"
}
```

---

## 프론트엔드 구조

### 1. 로또 분석 엔진 (`scripts/lotto-generator.js`)

**주요 클래스:**

#### `LottoDataLoader`
- 로또 히스토리 데이터 로드 및 처리
- 통계 계산 (빈도, 홀짝, 고저, 연속번호 등)
- Hot/Cold 번호 추출

**핵심 메서드:**
```javascript
// 전체 통계 계산
_calculateAllTimeStats(rounds) {
    // 빈도 분석
    // 홀짝 비율 (3:3, 4:2 등)
    // 고저 비율 (23 기준)
    // 연속번호 패턴
    // 끝자리 분포
    // Hot/Cold 번호 추출
}
```

#### `lottoGenerator`
- 번호 생성 엔진
- 10가지 이상의 생성 전략 제공

**생성 전략:**
1. **balanced** (기본): Hot 2개 + Cold 2개 + 랜덤 2개
2. **hot**: Hot 번호 중심 (4개)
3. **cold**: 미출현 번호 중심 (4개)
4. **pair**: 동반 출현 쌍 기반
5. **color**: 색상 균형 (5색상 분산)
6. **montecarlo**: 몬테카를로 시뮬레이션 (1000회)
7. **sum**: 합계 최적화 (100~170)
8. **cycle**: 출현 주기 기반
9. **lastdigit**: 끝자리 균형
10. **consecutive**: 연속번호 패턴 인식

**점수 계산 알고리즘:**
```javascript
calculateScore(numbers) {
    let score = 100;
    
    // 1. 홀짝 균형 (3:3 최적)
    // 2. 고저 균형 (3:3 최적)
    // 3. 합계 범위 (100~170 이상적)
    // 4. 연속번호 체크 (1쌍 OK, 2쌍 이상 감점)
    // 5. AC값 (다양성 지수, 7 이상 좋음)
    // 6. 색상 분포 (최소 3색 이상)
    // 7. 끝자리 다양성
    
    return Math.max(50, Math.min(100, score));
}
```

**번호별 스토리 생성:**
- 🔥 Hot 번호: 최근 100회 중 출현 빈도 높음
- ❄️ Cold 번호: 오래 미출현
- 🤝 동반 번호: 자주 함께 출현하는 쌍
- ⚖️ 균형 번호: 평균 수준

### 2. 데이터 파일 (`scripts/lotto-data.js`)

**구조:**
```javascript
const LOTTO_HISTORY_DATA = {
    1: { date: '2002-12-07', numbers: [10,23,29,33,37,40], bonus: 16 },
    2: { date: '2002-12-14', numbers: [9,13,21,25,32,42], bonus: 2 },
    // ... 1,201개 회차
};
```

**특징:**
- 정적 데이터 파일 (서버에서 동적 생성)
- 1회차부터 최신 회차까지 전체 데이터 포함
- 날짜, 번호, 보너스 번호 포함

---

## 크레딧 시스템

### 이중 크레딧 시스템

프로젝트에는 **두 가지 크레딧 시스템**이 혼재되어 있습니다:

#### 1. 전용 크레딧 시스템 (`lib/lotto_credit.lib.php`)
- 테이블: `g5_lotto_credit`, `g5_lotto_credit_log`
- 무료/유료 분리 관리
- 신규 회원: 무료 2회 제공
- 사용 우선순위: 무료 → 유료

**주요 함수:**
```php
// 크레딧 조회 (없으면 생성)
lotto_get_credit_row($mb_id, $create_if_not_exists = true)

// 분석 1회 사용
lotto_use_one_analysis($mb_id, $memo, $ref_key)

// 유료 크레딧 충전
lotto_charge_credit($mb_id, $amount, $memo, $ref_key, $change_type)
```

**사용 예시:**
```php
$use = lotto_use_one_analysis(
    $member['mb_id'],
    'AI 분석 실행 (회차 1202)',
    'round_1202'
);

if ($use['success']) {
    echo "사용 타입: " . $use['used_as']; // 'free' or 'paid'
    echo "남은 무료: " . $use['free_uses'];
    echo "남은 유료: " . $use['credit_balance'];
}
```

#### 2. GNUBOARD 포인트 시스템 (`api/use_credit.php`)
- GNUBOARD 기본 포인트 사용
- 100포인트 = 1회 분석
- `insert_point()` 함수 사용

**현재 상태:**
- `result.php`에서는 전용 크레딧 시스템 사용
- `api/use_credit.php`는 GNUBOARD 포인트 사용
- **통일 필요**: 한 가지 시스템으로 통합 권장

### 크레딧 충전 플로우

```
사용자 결제 요청
    ↓
[토스 결제 API] 주문 생성
    ↓
[토스 결제창] 결제 진행
    ↓
[payments/toss/success.php] 결제 완료
    ├─ 주문 상태 확인
    ├─ lotto_charge_credit() 호출
    └─ 크레딧 충전 완료
```

---

## 결제 시스템

### 토스페이먼츠 연동

**주요 파일:**
- `api/toss/create_order.php`: 주문 생성
- `payments/toss/success.php`: 결제 성공 처리
- `payments/toss/fail.php`: 결제 실패 처리
- `api/toss/_toss_config.php`: 설정 파일

**결제 플로우:**

1. **주문 생성**
```php
// 주문번호 생성: LC + 날짜시간 + 랜덤
$orderId = 'LC'.date('YmdHis').bin2hex(random_bytes(6));

// 주문 정보 DB 저장
INSERT INTO g5_lotto_toss_orders ...

// 토스 결제창 정보 반환
{
    "clientKey": "...",
    "orderId": "...",
    "amount": 2500,
    "orderName": "크레딧 20회 충전"
}
```

2. **결제 완료 처리**
```php
// 결제 검증
// 주문 상태 업데이트: READY → SUCCESS
// 크레딧 충전: lotto_charge_credit()
// 사용자 리다이렉트
```

**상품 구성:**
- 크레딧 5회: 1,000원 (200원/회)
- 크레딧 20회: 2,500원 (125원/회) ← 인기
- 크레딧 50회: 5,000원 (100원/회) ← 최저가

---

## 데이터 수집 파이프라인

### 주간 크론 작업 (`cron/lotto_weekly.php`)

**실행 주기:** 매주 토요일 추첨 후 (예: 일요일 새벽)

**작업 내용:**
1. DB 최대 회차 조회
2. 다음 회차부터 최대 10회까지 시도
3. 동행복권 API 호출
4. 데이터 파싱 및 저장
5. 실패 시 중단 (아직 발표 안 된 회차)

**핵심 함수:**
```php
// 동행복권 API 호출
li_get_lotto_api_json($drwNo, &$error)

// 데이터 저장
li_fetch_and_save_lotto_draw($drwNo)
```

**에러 처리:**
- API 실패: 로그 기록 후 중단
- 발표 전 회차: 정상 종료 (다음 주 재시도)
- 네트워크 오류: 재시도 로직 (현재 없음)

### 동행복권 API

**엔드포인트:**
```
https://www.dhlottery.co.kr/common.do?method=getLottoNumber&drwNo={회차}
```

**응답 예시:**
```json
{
    "returnValue": "success",
    "drwNo": 1201,
    "drwNoDate": "2025-12-06",
    "drwtNo1": 7,
    "drwtNo2": 9,
    "drwtNo3": 24,
    "drwtNo4": 27,
    "drwtNo5": 35,
    "drwtNo6": 36,
    "bnusNo": 37,
    "firstAccumamnt": 1000000000,
    "firstWinamnt": 1000000000,
    "firstPrzwnerCo": 5,
    "totSellamnt": 10000000000
}
```

**추가 데이터 수집:**
- 2/3등 당첨자 수 및 당첨금: `byWin` 페이지 HTML 파싱
- 판매점 정보: 별도 크론 (`lotto_store_sync.php`)

---

## 로또 분석 알고리즘

### 통계 분석 항목

#### 1. 빈도 분석
- 각 번호(1~45)의 출현 횟수 계산
- Hot 번호: 최근 100회 중 출현 빈도 상위
- Cold 번호: 최근 100회 중 출현 빈도 하위

#### 2. 홀짝 비율
- 홀수/짝수 분포 분석
- 역대 패턴: 3:3이 가장 많음 (약 30%)
- 4:2, 2:4도 빈번 (각 약 25%)

#### 3. 고저 비율
- 23을 기준으로 고/저 분류
- 고(24~45) / 저(1~22) 분포 분석
- 역대 패턴: 3:3이 가장 많음

#### 4. 합계 분석
- 6개 번호의 합계 분포
- 역대 평균: 약 138
- 이상적 범위: 100~170

#### 5. 연속번호 패턴
- 연속된 번호 쌍 분석
- 역대 약 42%에 연속번호 포함
- 1쌍은 정상, 2쌍 이상은 드뭄

#### 6. AC값 (다양성 지수)
- 번호 간 간격의 다양성 측정
- AC = (서로 다른 간격의 개수) - 5
- 이상적: 7 이상

#### 7. 색상 분포
- 동행복권 공식 색상 기준
  - 1~10: 노랑
  - 11~20: 파랑
  - 21~30: 빨강
  - 31~40: 회색
  - 41~45: 초록
- 이상적: 최소 3색 이상

#### 8. 끝자리 분포
- 0~9 끝자리 다양성
- 같은 끝자리 2개 이상 지양

#### 9. 동반 출현 분석
- 번호 쌍의 함께 출현 빈도
- 자주 함께 나오는 쌍 우선 선택

#### 10. 출현 주기 분석
- 각 번호의 평균 출현 주기 계산
- 주기가 평균에 가까운 번호 우선

### 점수 계산 로직

**기본 점수:** 100점

**감점/가점 항목:**
- 홀짝 균형: 3:3 = 0점, 2:4/4:2 = -3점, 1:5/5:1 = -8점, 6:0/0:6 = -15점
- 고저 균형: 3:3 = 0점, 2:4/4:2 = -3점, 1:5/5:1 = -8점, 6:0/0:6 = -15점
- 합계 범위: 100~170 = +5점, 80~190 = -5점, 그 외 = -15점
- 연속번호: 0쌍 = 0점, 1쌍 = +2점, 2쌍 = -5점, 3쌍 이상 = -15점
- AC값: 9 이상 = +5점, 7~8 = +2점, 5~6 = -3점, 4 이하 = -10점
- 색상 분포: 4색 이상 = +3점, 3색 = 0점, 2색 이하 = -5점
- 끝자리: 5개 이상 = +3점, 4개 = 0점, 2개 이하 = -8점

**최종 점수:** 50~100점 범위로 제한

---

## 보안 및 최적화

### 보안 조치
- SQL Injection 방지: `sql_real_escape_string()` 사용
- XSS 방지: 출력 시 `htmlspecialchars()` 사용
- CSRF 방지: 토큰 검증 (일부 페이지)
- 세션 관리: GNUBOARD 세션 시스템 활용

### 성능 최적화
- 데이터 캐싱: JavaScript 데이터 로더 캐시
- 쿼리 최적화: 인덱스 활용 (회차, 회원 ID)
- 이미지 최적화: SVG 아이콘 사용
- CDN 활용: 카카오 SDK, 폰트 등

### 개선 권장 사항
1. **크레딧 시스템 통일**: 전용 크레딧 또는 GNUBOARD 포인트 중 하나로 통일
2. **에러 로깅**: 전역 에러 로깅 시스템 구축
3. **API 버전 관리**: `/api/v1/` 형태로 버전 관리
4. **캐싱 전략**: Redis/Memcached 도입 검토
5. **테스트 코드**: 단위 테스트 및 통합 테스트 추가

---

## 배포 및 운영

### 환경 요구사항
- PHP 7.4 이상
- MySQL 5.7 이상 / MariaDB 10.3 이상
- Apache/Nginx 웹서버
- cURL 확장 모듈
- DOM 확장 모듈 (HTML 파싱용)

### 크론 설정 예시
```bash
# 매주 일요일 새벽 2시 실행
0 2 * * 0 /usr/bin/php /path/to/cron/lotto_weekly.php >> /var/log/lotto_weekly.log 2>&1
```

### 모니터링 포인트
- 크론 실행 로그 확인
- API 호출 실패율 모니터링
- 데이터베이스 성능 모니터링
- 결제 실패율 추적

---

## 결론

이 프로젝트는 **GNUBOARD 5 기반의 로또 분석 서비스**로, 다음과 같은 특징을 가집니다:

1. **데이터 기반 분석**: 동행복권 공식 데이터 활용
2. **다양한 알고리즘**: 10가지 이상의 번호 생성 전략
3. **사용자 친화적**: 카카오 로그인, 간편 결제
4. **확장 가능한 구조**: 모듈화된 코드 구조

**주요 강점:**
- 체계적인 데이터 수집 파이프라인
- 상세한 통계 분석 알고리즘
- 사용자 경험 최적화 (UX/UI)

**개선 여지:**
- 크레딧 시스템 통일
- 에러 처리 강화
- 테스트 코드 추가
- API 문서화

---

**작성일**: 2025-12-15  
**버전**: 1.0

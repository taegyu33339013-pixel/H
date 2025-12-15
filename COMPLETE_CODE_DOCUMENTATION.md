# 오늘로또 프로젝트 전체 코드 상세 설명서

## 📋 목차

1. [프로젝트 개요](#1-프로젝트-개요)
2. [시스템 아키텍처](#2-시스템-아키텍처)
3. [핵심 파일 상세 분석](#3-핵심-파일-상세-분석)
4. [데이터 플로우](#4-데이터-플로우)
5. [주요 함수 및 클래스](#5-주요-함수-및-클래스)
6. [API 엔드포인트 상세](#6-api-엔드포인트-상세)
7. [프론트엔드 구조](#7-프론트엔드-구조)
8. [데이터베이스 스키마](#8-데이터베이스-스키마)
9. [보안 및 최적화](#9-보안-및-최적화)

---

## 1. 프로젝트 개요

### 1.1 프로젝트 정보

- **프로젝트명**: 오늘로또 (LottoInsight.ai)
- **기반 프레임워크**: GNUBOARD 5.6.13
- **주요 언어**: PHP 7.4+, JavaScript (ES6+)
- **데이터베이스**: MySQL/MariaDB
- **서비스 유형**: AI 기반 로또 번호 분석 서비스

### 1.2 핵심 기능

1. **로또 데이터 수집**: 동행복권 공식 API 연동 (1~1,201회차)
2. **AI 번호 분석**: 10가지 이상의 통계 알고리즘
3. **크레딧 시스템**: 무료/유료 크레딧 관리
4. **결제 연동**: 토스페이먼츠 결제 시스템
5. **회원 관리**: 카카오 간편 로그인

---

## 2. 시스템 아키텍처

### 2.1 전체 구조도

```
┌─────────────────────────────────────────────────────────┐
│                    사용자 (브라우저)                      │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              프론트엔드 (HTML/CSS/JavaScript)            │
│  - index.php (랜딩 페이지)                               │
│  - auth.php (로그인 페이지)                              │
│  - result.php (분석 결과 페이지)                         │
│  - scripts/lotto-generator.js (AI 엔진)                  │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              API 레이어 (RESTful API)                     │
│  - api/get_credits.php                                  │
│  - api/use_credit.php                                   │
│  - api/save_analysis.php                                │
│  - api/toss/create_order.php                            │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│            비즈니스 로직 레이어 (PHP Library)             │
│  - lib/lotto_credit.lib.php (크레딧 관리)                │
│  - lib/lotto_draw.lib.php (회차 데이터 처리)             │
│  - lib/lotto_store.lib.php (판매점 정보)                 │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              데이터 레이어 (MySQL)                        │
│  - g5_lotto_draw (회차 데이터)                           │
│  - g5_lotto_credit (크레딧)                              │
│  - g5_lotto_analysis (분석 결과)                         │
│  - g5_member (회원 정보)                                 │
└─────────────────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│            외부 서비스 연동                                │
│  - 동행복권 API (데이터 수집)                             │
│  - 토스페이먼츠 API (결제)                                │
│  - 카카오 로그인 API (인증)                               │
└─────────────────────────────────────────────────────────┘
```

### 2.2 디렉토리 구조 상세

```
/
├── index.php                    # 메인 랜딩 페이지 (8,077줄)
├── auth.php                     # 카카오 로그인 페이지 (1,132줄)
├── result.php                   # 분석 결과 페이지 (4,394줄)
├── common.php                   # GNUBOARD 공통 파일
│
├── api/                         # RESTful API 엔드포인트
│   ├── get_credits.php         # 크레딧 조회
│   ├── use_credit.php          # 크레딧 사용
│   ├── save_analysis.php       # 분석 결과 저장
│   ├── get_history.php         # 분석 히스토리 조회
│   ├── credit/
│   │   └── balance.php         # 크레딧 잔액 조회 (하위 호환)
│   └── toss/                   # 토스 결제 API
│       ├── create_order.php   # 주문 생성
│       ├── success.php        # 결제 성공
│       └── fail.php           # 결제 실패
│
├── lib/                         # 핵심 라이브러리
│   ├── lotto_credit.lib.php    # 크레딧 관리 (305줄)
│   ├── lotto_draw.lib.php      # 회차 데이터 처리 (386줄)
│   └── lotto_store.lib.php     # 판매점 정보 (779줄)
│
├── cron/                        # 스케줄 작업
│   ├── lotto_weekly.php        # 주간 회차 업데이트 (74줄)
│   ├── lotto_store_sync.php    # 판매점 데이터 동기화
│   └── lotto_seed.php          # 초기 데이터 시딩
│
├── scripts/                      # 프론트엔드 JavaScript
│   ├── lotto-generator.js      # AI 분석 엔진 (980줄)
│   ├── lotto-data.js           # 로또 히스토리 데이터 (1,207줄)
│   └── gnuboard-api.js         # GNUBOARD API 래퍼 (195줄)
│
├── payments/                     # 결제 처리
│   └── toss/
│       ├── success.php         # 결제 성공 처리
│       └── fail.php            # 결제 실패 처리
│
├── bbs/                         # GNUBOARD 게시판
├── adm/                         # 관리자 페이지
├── mobile/                      # 모바일 페이지
└── theme/                       # 테마 파일
```

---

## 3. 핵심 파일 상세 분석

### 3.1 `index.php` - 메인 랜딩 페이지

#### 파일 구조

```php
<?php
// 1. GNUBOARD 환경 로드
if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

// 2. 최신 회차 정보 조회
$row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($row['max_round'] ?? 0);
$total_numbers = $max_round * 6;

// 3. HTML 출력 (SEO 최적화 포함)
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <!-- SEO 메타 태그 -->
    <!-- Open Graph -->
    <!-- Structured Data (JSON-LD) -->
</head>
<body>
    <!-- 랜딩 페이지 콘텐츠 -->
</body>
</html>
```

#### 주요 기능

1. **SEO 최적화**
   - 메타 태그 (title, description, keywords)
   - Open Graph 태그
   - Twitter Card 태그
   - FAQPage 구조화된 데이터 (JSON-LD)
   - WebApplication 스키마

2. **동적 콘텐츠**
   - 최신 회차 정보 표시
   - 총 분석 데이터 수 계산
   - 실시간 대기열 번호

3. **사용자 유도**
   - 카카오 로그인 버튼
   - 무료 분석 혜택 강조
   - CTA (Call To Action) 버튼

#### 코드 예시

```php
// 최신 회차 조회
$row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($row['max_round'] ?? 0);
$total_numbers = $max_round * 6; // 총 분석 데이터 수

// SEO 메타 태그에 동적 삽입
<title>오늘로또 - AI 기반 로또 번호 분석 | <?= (int)$max_round ?>회차 데이터 실시간 반영</title>
```

---

### 3.2 `auth.php` - 로그인 페이지

#### 파일 구조

```php
<?php
// 1. GNUBOARD 환경 로드
// 2. 카카오 SDK 초기화
// 3. 로그인 상태 확인
// 4. HTML 출력
?>
```

#### 주요 기능

1. **카카오 로그인 연동**
   - 카카오 SDK 초기화
   - 로그인 버튼 렌더링
   - 콜백 처리

2. **사용자 경험 최적화**
   - 애니메이션 배경
   - 반응형 디자인
   - 로딩 상태 표시

#### 카카오 로그인 플로우

```javascript
// 1. 카카오 SDK 초기화
Kakao.init('YOUR_KAKAO_REST_API_KEY');

// 2. 로그인 요청
Kakao.Auth.login({
    success: function(authObj) {
        // 3. 사용자 정보 요청
        Kakao.API.request({
            url: '/v2/user/me',
            success: function(res) {
                // 4. 서버로 전송
                window.location.href = '/kakao_login.php?code=...';
            }
        });
    }
});
```

---

### 3.3 `result.php` - 분석 결과 페이지

#### 파일 구조 (4,394줄)

```php
<?php
// ─────────────────────────────────────
// 1. GNUBOARD 공통 파일 로드
// ─────────────────────────────────────
include_once('./common.php');
include_once(G5_LIB_PATH . '/lotto_draw.lib.php');
include_once(G5_LIB_PATH . '/lotto_credit.lib.php');

// ─────────────────────────────────────
// 2. 로그인 체크
// ─────────────────────────────────────
if (empty($is_member)) {
    alert('로그인 후 이용 가능한 서비스입니다.', ...);
}

// ─────────────────────────────────────
// 3. 현재 회차 및 최근 회차 데이터 로딩
// ─────────────────────────────────────
$current_round = $latest_draw_no + 1; // 분석 대상 회차
$latest_round = $latest_draw_no;      // 최신 추첨 회차

// ─────────────────────────────────────
// 4. 회원 크레딧 정보
// ─────────────────────────────────────
$credit_row = lotto_get_credit_row($member['mb_id'], true);
$server_free_credits = (int)($credit_row['free_uses'] ?? 0);
$server_paid_credits = (int)($credit_row['credit_balance'] ?? 0);

// ─────────────────────────────────────
// 5. AJAX 크레딧 사용 요청 처리
// ─────────────────────────────────────
if ($_POST['mode'] === 'use_credit') {
    // 크레딧 사용 처리
    // JSON 응답 반환
}

// ─────────────────────────────────────
// 6. HTML 출력
// ─────────────────────────────────────
?>
<!DOCTYPE html>
<html>
<head>
    <!-- 메타 태그 -->
    <!-- 스타일시트 -->
    <!-- JavaScript -->
</head>
<body>
    <!-- 페이지 콘텐츠 -->
    <script>
        // 클라이언트 사이드 로직
    </script>
</body>
</html>
```

#### 주요 섹션

1. **서버 사이드 처리 (PHP)**
   - 로그인 체크
   - 크레딧 조회
   - 회차 데이터 로드
   - AJAX 요청 처리

2. **클라이언트 사이드 로직 (JavaScript)**
   - 번호 생성 엔진 초기화
   - 분석 실행
   - 결과 표시
   - 크레딧 관리

#### 핵심 함수

##### `startAnalysis()` - 분석 시작

```javascript
async function startAnalysis() {
    // 1. 크레딧 사용 요청
    const resp = await fetch(location.pathname, {
        method: 'POST',
        body: new URLSearchParams({ mode: 'use_credit' })
    });
    
    const data = await resp.json();
    if (!data.success) {
        // 크레딧 부족 등 에러 처리
        return;
    }
    
    // 2. 번호 생성 엔진 준비
    await ensureGeneratorReady();
    
    // 3. 로딩 화면 표시
    showLoading();
    
    // 4. 번호 생성 및 결과 표시
    generateAndDisplay();
}
```

##### `generateAndDisplay()` - 번호 생성 및 표시

```javascript
async function generateAndDisplay() {
    const generator = await ensureGeneratorReady();
    
    // 선택된 스타일로 번호 생성
    const selectedStyles = state.selectedStyles;
    const results = [];
    
    for (const style of selectedStyles) {
        const result = generator.generate(style);
        results.push(result);
    }
    
    // 결과 렌더링
    renderResults(results);
    
    // 저장 기능 활성화
    enableSaveFunction();
}
```

---

### 3.4 `lib/lotto_draw.lib.php` - 회차 데이터 처리

#### 주요 함수

##### `li_get_lotto_api_json()` - 동행복권 API 호출

```php
function li_get_lotto_api_json($drwNo, &$error = '')
{
    $url = 'https://www.dhlottery.co.kr/common.do?method=getLottoNumber&drwNo=' . $drwNo;
    
    // cURL 또는 file_get_contents 사용
    $raw = curl_exec($ch);
    
    // JSON 파싱
    $data = json_decode($raw, true);
    
    // 응답 검증
    if ($data['returnValue'] !== 'success') {
        $error = 'API returnValue = ' . ($data['returnValue'] ?? '없음');
        return null;
    }
    
    return $data;
}
```

**응답 형식:**
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

##### `li_get_lotto_bywin_html()` - 2/3등 정보 수집

```php
function li_get_lotto_bywin_html($drwNo, &$error = '')
{
    $url = 'https://www.dhlottery.co.kr/gameResult.do?method=byWin&drwNo=' . $drwNo;
    
    // HTML 가져오기
    $raw = curl_exec($ch);
    
    return $raw;
}
```

##### `li_parse_prize_23_from_bywin()` - HTML 파싱

```php
function li_parse_prize_23_from_bywin($html, &$error = '')
{
    // 1. DOM 파싱 시도
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    
    // 2등/3등 정보 추출
    foreach ($trs as $tr) {
        if (mb_strpos($rank, '2등') !== false) {
            $out['second_winners'] = li_to_int($cells[2]);
            $out['second_prize_each'] = li_to_int($cells[3]);
        }
        if (mb_strpos($rank, '3등') !== false) {
            $out['third_winners'] = li_to_int($cells[2]);
            $out['third_prize_each'] = li_to_int($cells[3]);
        }
    }
    
    // 3. Fallback: 정규식 파싱
    if ($out['second_winners'] <= 0) {
        preg_match('/2등[^0-9]*([0-9,]+)[^0-9]*([0-9,]+)원/u', $text, $m);
        // ...
    }
    
    return $out;
}
```

##### `li_save_lotto_draw()` - DB 저장

```php
function li_save_lotto_draw(array $data, &$error = '')
{
    // 데이터 매핑
    $draw_no = (int)($data['drwNo'] ?? 0);
    $draw_date = trim($data['drwNoDate'] ?? '');
    $n1 = (int)($data['drwtNo1'] ?? 0);
    // ... n2~n6, bonus
    
    // INSERT ... ON DUPLICATE KEY UPDATE
    $sql = "
        INSERT INTO g5_lotto_draw
        (draw_no, draw_date, n1, n2, n3, n4, n5, n6, bonus, ...)
        VALUES (...)
        ON DUPLICATE KEY UPDATE
            draw_date = VALUES(draw_date),
            n1 = VALUES(n1),
            ...
    ";
    
    sql_query($sql);
    return true;
}
```

##### `li_fetch_and_save_lotto_draw()` - 통합 함수

```php
function li_fetch_and_save_lotto_draw($drwNo, &$error = '')
{
    // 1. JSON API 호출 (번호/1등 정보)
    $data = li_get_lotto_api_json($drwNo, $error);
    if (!$data) return false;
    
    // 2. HTML 파싱 (2/3등 정보)
    $html = li_get_lotto_bywin_html($drwNo, $err2);
    if ($html) {
        $p = li_parse_prize_23_from_bywin($html, $err3);
        if ($p) {
            $data['second_winners'] = $p['second_winners'];
            $data['second_prize_each'] = $p['second_prize_each'];
            $data['third_winners'] = $p['third_winners'];
            $data['third_prize_each'] = $p['third_prize_each'];
        }
    }
    
    // 3. DB 저장
    return li_save_lotto_draw($data, $error);
}
```

---

### 3.5 `lib/lotto_credit.lib.php` - 크레딧 관리

#### 주요 함수

##### `lotto_get_credit_row()` - 크레딧 조회

```php
function lotto_get_credit_row($mb_id, $create_if_not_exists = false)
{
    // 1. 기존 크레딧 조회
    $row = sql_fetch("SELECT * FROM g5_lotto_credit WHERE mb_id = '{$mb_id}'");
    
    if ($row) {
        return $row; // 기존 데이터 반환
    }
    
    // 2. 없으면 생성 (무료 크레딧 0회 - 회원가입 시에만 지급)
    if ($create_if_not_exists) {
        sql_query("INSERT INTO g5_lotto_credit 
                   SET mb_id = '{$mb_id}', 
                       free_uses = 0, 
                       credit_balance = 0, ...");
    }
    
    return ['mb_id' => $mb_id, 'free_uses' => 0, 'credit_balance' => 0];
}
```

##### `lotto_use_one_analysis()` - 크레딧 사용

```php
function lotto_use_one_analysis($mb_id, $memo = '', $ref_key = '')
{
    // 1. 크레딧 조회
    $row = lotto_get_credit_row($mb_id, true);
    $free = (int)$row['free_uses'];
    $paid = (int)$row['credit_balance'];
    
    // 2. 크레딧 부족 체크
    if ($free <= 0 && $paid <= 0) {
        return ['success' => false, 'reason' => 'NO_CREDIT'];
    }
    
    // 3. 사용 우선순위: 무료 → 유료
    $used_as = 'free';
    if ($free > 0) {
        $free--;
    } else {
        $used_as = 'paid';
        $paid--;
    }
    
    // 4. 트랜잭션 처리
    sql_query("BEGIN");
    
    // 크레딧 업데이트
    sql_query("UPDATE g5_lotto_credit 
               SET free_uses = {$free}, 
                   credit_balance = {$paid}, ...");
    
    // 로그 기록
    sql_query("INSERT INTO g5_lotto_credit_log 
               SET change_type = '{$used_as}', 
                   amount = -1, ...");
    
    sql_query("COMMIT");
    
    return ['success' => true, 'used_as' => $used_as, ...];
}
```

##### `lotto_charge_credit()` - 크레딧 충전

```php
function lotto_charge_credit($mb_id, $amount, $memo = '', $ref_key = '', $change_type = 'charge')
{
    // 1. 현재 크레딧 조회
    $row = lotto_get_credit_row($mb_id, true);
    $before_paid = (int)$row['credit_balance'];
    $after_paid = $before_paid + $amount;
    
    // 2. 트랜잭션 처리
    sql_query("BEGIN");
    
    // 크레딧 업데이트
    sql_query("UPDATE g5_lotto_credit 
               SET credit_balance = {$after_paid}, ...");
    
    // 로그 기록
    sql_query("INSERT INTO g5_lotto_credit_log 
               SET change_type = 'charge', 
                   amount = {$amount}, ...");
    
    sql_query("COMMIT");
    
    return ['success' => true, 'credit_balance' => $after_paid, ...];
}
```

##### `lotto_grant_welcome_credit()` - 신규 회원 무료 크레딧 지급

```php
function lotto_grant_welcome_credit($mb_id, $memo = '신규 회원 가입 축하')
{
    // 1. 중복 지급 방지: welcome 로그 확인
    $welcome_log = sql_fetch("SELECT id FROM g5_lotto_credit_log 
                               WHERE mb_id = '{$mb_id}' 
                               AND change_type = 'welcome'");
    if ($welcome_log) {
        return ['success' => false, 'reason' => 'ALREADY_GRANTED'];
    }
    
    // 2. 크레딧 행 확인/생성
    $existing = sql_fetch("SELECT * FROM g5_lotto_credit WHERE mb_id = '{$mb_id}'");
    
    if ($existing) {
        // 기존 행에 무료 크레딧 1회 추가
        sql_query("UPDATE g5_lotto_credit SET free_uses = free_uses + 1");
    } else {
        // 새 행 생성 (무료 1회)
        sql_query("INSERT INTO g5_lotto_credit 
                   SET mb_id = '{$mb_id}', free_uses = 1, credit_balance = 0");
    }
    
    // 3. 로그 기록 (change_type = 'welcome')
    sql_query("INSERT INTO g5_lotto_credit_log 
               SET change_type = 'welcome', amount = 1, ...");
    
    return ['success' => true, 'free_uses' => 1];
}
```

---

### 3.6 `scripts/lotto-generator.js` - AI 분석 엔진

#### 클래스 구조

```javascript
// 1. LottoDataLoader - 데이터 로더
const LottoDataLoader = {
    _cache: null,
    
    get data() {
        // LOTTO_HISTORY_DATA 처리 및 통계 계산
    },
    
    _calculateAllTimeStats(rounds) {
        // 전체 통계 계산
        // - 빈도 분석
        // - 홀짝 비율
        // - 고저 비율
        // - 연속번호 패턴
        // - Hot/Cold 번호
    }
};

// 2. lottoGenerator - 번호 생성 엔진
const lottoGenerator = {
    ready: false,
    dataLoader: LottoDataLoader,
    
    async init() {
        // 엔진 초기화
    },
    
    analyzeStats() {
        // 통계 분석
    },
    
    calculateScore(numbers) {
        // 점수 계산 (50~100점)
    },
    
    generate(style = 'balanced') {
        // 번호 생성 (10가지 스타일)
    },
    
    generateReport(result) {
        // 리포트 생성
    }
};
```

#### 번호 생성 알고리즘

##### 1. Balanced (균형) - 기본 전략

```javascript
_generateBalanced(stats) {
    const numbers = [];
    
    // Hot 번호 2개
    const hotPool = [...stats.hotNumbers];
    while (numbers.length < 2 && hotPool.length > 0) {
        numbers.push(hotPool.splice(randomIndex, 1)[0]);
    }
    
    // Cold/Overdue 번호 2개
    const coldPool = stats.overdueNumbers.filter(n => !numbers.includes(n));
    while (numbers.length < 4 && coldPool.length > 0) {
        numbers.push(coldPool.splice(randomIndex, 1)[0]);
    }
    
    // 나머지 2개는 랜덤
    while (numbers.length < 6) {
        const n = Math.floor(Math.random() * 45) + 1;
        if (!numbers.includes(n)) numbers.push(n);
    }
    
    // 균형 체크 (홀짝, 고저, 합계, 색상)
    // 기준 충족 시 반환
    return numbers.sort((a, b) => a - b);
}
```

##### 2. Monte Carlo 시뮬레이션

```javascript
_generateMonteCarlo(stats) {
    const simulations = 1000;
    const candidateSets = [];
    
    // 1000번 시뮬레이션
    for (let i = 0; i < simulations; i++) {
        const numbers = [];
        
        // 빈도 가중치 기반 선택
        while (numbers.length < 6) {
            const weights = [];
            for (let n = 1; n <= 45; n++) {
                if (!numbers.includes(n)) {
                    const freq = stats.freq[n] || 1;
                    weights.push({ num: n, weight: freq });
                }
            }
            
            // 가중치 기반 랜덤 선택
            const selected = weightedRandomSelect(weights);
            numbers.push(selected);
        }
        
        const score = this.calculateScore(numbers);
        candidateSets.push({ numbers, score });
    }
    
    // 점수 상위 10%에서 랜덤 선택
    candidateSets.sort((a, b) => b.score - a.score);
    const topTen = candidateSets.slice(0, Math.ceil(simulations * 0.1));
    return topTen[randomIndex].numbers;
}
```

##### 3. 점수 계산 알고리즘

```javascript
calculateScore(numbers) {
    let score = 100;
    const sorted = [...numbers].sort((a, b) => a - b);
    
    // 1. 홀짝 균형 (3:3 최적)
    const odd = numbers.filter(n => n % 2 === 1).length;
    if (odd === 3) score += 0;
    else if (odd === 2 || odd === 4) score -= 3;
    else if (odd === 1 || odd === 5) score -= 8;
    else score -= 15;
    
    // 2. 고저 균형 (23 기준, 3:3 최적)
    const high = numbers.filter(n => n > 22).length;
    if (high === 3) score += 0;
    else if (high === 2 || high === 4) score -= 3;
    else if (high === 1 || high === 5) score -= 8;
    else score -= 15;
    
    // 3. 합계 범위 (100~170 이상적)
    const sum = numbers.reduce((a, b) => a + b, 0);
    if (sum >= 100 && sum <= 170) score += 5;
    else if (sum >= 80 && sum <= 190) score -= 5;
    else score -= 15;
    
    // 4. 연속번호 체크 (1쌍 OK, 2쌍 이상 감점)
    let consecutive = 0;
    for (let i = 0; i < sorted.length - 1; i++) {
        if (sorted[i + 1] - sorted[i] === 1) consecutive++;
    }
    if (consecutive === 0) score += 0;
    else if (consecutive === 1) score += 2;
    else if (consecutive === 2) score -= 5;
    else score -= 15;
    
    // 5. AC값 (다양성 지수, 7 이상 좋음)
    const diffs = new Set();
    for (let i = 0; i < sorted.length; i++) {
        for (let j = i + 1; j < sorted.length; j++) {
            diffs.add(sorted[j] - sorted[i]);
        }
    }
    const ac = diffs.size - 5;
    if (ac >= 9) score += 5;
    else if (ac >= 7) score += 2;
    else if (ac >= 5) score -= 3;
    else score -= 10;
    
    // 6. 색상 분포 (최소 3색 이상)
    const colors = new Set(numbers.map(n => this.dataLoader.getBallColor(n)));
    if (colors.size >= 4) score += 3;
    else if (colors.size >= 3) score += 0;
    else score -= 5;
    
    // 7. 끝자리 다양성
    const lastDigits = new Set(numbers.map(n => n % 10));
    if (lastDigits.size >= 5) score += 3;
    else if (lastDigits.size >= 4) score += 0;
    else if (lastDigits.size <= 2) score -= 8;
    
    return Math.max(50, Math.min(100, score));
}
```

---

### 3.7 `cron/lotto_weekly.php` - 주간 데이터 업데이트

#### 실행 플로우

```php
<?php
// 1. 공통 파일 로드
require_once $_SERVER['DOCUMENT_ROOT'] . '/common.php';
require_once G5_LIB_PATH . '/lotto_draw.lib.php';

// 2. 현재 DB 최대 회차 조회
$row = sql_fetch("SELECT MAX(draw_no) AS max_draw_no FROM g5_lotto_draw");
$max_draw_no = (int)($row['max_draw_no'] ?? 0);
$next_draw_no = $max_draw_no + 1;

// 3. 최대 10회까지 시도 (cron 누락 대비)
$max_gap = 10;
$inserted = 0;

for ($drw = $next_draw_no; $drw < $next_draw_no + $max_gap; $drw++) {
    echo "[INFO] 회차 {$drw} 데이터 가져오기 시도...\n";
    
    // 동행복권 API 호출 및 저장
    $ok = li_fetch_and_save_lotto_draw($drw);
    
    if (!$ok) {
        // 아직 발표 안 된 회차이면 중단
        echo "[INFO] 회차 {$drw} 는 아직 데이터가 없습니다.\n";
        break;
    }
    
    echo "[OK] 회차 {$drw} 저장 완료.\n";
    $inserted++;
    
    // API 부담 줄이기 위해 0.2초 딜레이
    usleep(200000);
}

// 4. 결과 로그
echo "[INFO] 총 {$inserted}개 회차가 새로 저장되었습니다.\n";
?>
```

#### 크론 설정 예시

```bash
# 매주 일요일 새벽 2시 실행
0 2 * * 0 /usr/bin/php /path/to/cron/lotto_weekly.php >> /var/log/lotto_weekly.log 2>&1
```

---

## 4. 데이터 플로우

### 4.1 로또 데이터 수집 플로우

```
동행복권 API
    ↓
[cron/lotto_weekly.php]
    ├─ 최대 회차 조회
    ├─ 다음 회차부터 최대 10회 시도
    └─ li_fetch_and_save_lotto_draw() 호출
        ↓
[lib/lotto_draw.lib.php]
    ├─ li_get_lotto_api_json() - JSON API 호출
    │   └─ 번호, 1등 정보
    ├─ li_get_lotto_bywin_html() - HTML 페이지 호출
    │   └─ 2/3등 정보
    └─ li_parse_prize_23_from_bywin() - HTML 파싱
        ↓
[li_save_lotto_draw()]
    └─ INSERT ... ON DUPLICATE KEY UPDATE
        ↓
[g5_lotto_draw 테이블]
```

### 4.2 사용자 분석 플로우

```
사용자 (브라우저)
    ↓
[result.php] 분석 버튼 클릭
    ↓
[JavaScript] startAnalysis()
    ├─ 크레딧 사용 요청 (POST /result.php?mode=use_credit)
    │   └─ [lib/lotto_credit.lib.php] lotto_use_one_analysis()
    │       ├─ 무료 크레딧 확인
    │       ├─ 없으면 유료 크레딧 사용
    │       └─ 로그 기록
    │
    ├─ 번호 생성 엔진 준비
    │   └─ [scripts/lotto-generator.js] lottoGenerator.init()
    │
    └─ 번호 생성 및 표시
        ├─ [lottoGenerator.generate(style)]
        │   ├─ 통계 분석
        │   ├─ 번호 생성 (10가지 전략)
        │   └─ 점수 계산
        │
        └─ 결과 렌더링
            ├─ 번호 표시
            ├─ 점수 표시
            ├─ 스토리 생성
            └─ 리포트 생성
    ↓
[저장 버튼 클릭]
    ↓
[api/save_analysis.php]
    └─ INSERT INTO g5_lotto_analysis
```

### 4.3 결제 플로우

```
사용자 (브라우저)
    ↓
[result.php] 충전 버튼 클릭
    ↓
[JavaScript] showChargeModal()
    ├─ 패키지 선택
    └─ processPayment()
        ↓
[api/toss/create_order.php]
    ├─ 주문 생성
    │   └─ INSERT INTO g5_lotto_toss_orders
    └─ 토스 결제창 정보 반환
        ↓
[토스페이먼츠 결제창]
    ├─ 결제 진행
    └─ 결제 완료/실패
        ↓
[payments/toss/success.php] 또는 [fail.php]
    ├─ 결제 검증
    ├─ 크레딧 충전
    │   └─ [lib/lotto_credit.lib.php] lotto_charge_credit()
    └─ 리다이렉트
        ↓
[result.php] (결제 완료 후)
    └─ refreshCreditBalance() - 크레딧 갱신
```

---

## 5. 주요 함수 및 클래스

### 5.1 PHP 함수 목록

#### 크레딧 관련 (`lib/lotto_credit.lib.php`)

| 함수명 | 설명 | 파라미터 | 반환값 |
|--------|------|----------|--------|
| `lotto_get_credit_row()` | 크레딧 조회 | `$mb_id`, `$create_if_not_exists` | 배열 |
| `lotto_use_one_analysis()` | 크레딧 사용 | `$mb_id`, `$memo`, `$ref_key` | 배열 |
| `lotto_charge_credit()` | 크레딧 충전 | `$mb_id`, `$amount`, `$memo`, `$ref_key`, `$change_type` | 배열 |
| `lotto_grant_welcome_credit()` | 신규 회원 무료 크레딧 지급 | `$mb_id`, `$memo` | 배열 |

#### 회차 데이터 관련 (`lib/lotto_draw.lib.php`)

| 함수명 | 설명 | 파라미터 | 반환값 |
|--------|------|----------|--------|
| `li_get_lotto_api_json()` | 동행복권 JSON API 호출 | `$drwNo`, `&$error` | 배열 또는 null |
| `li_get_lotto_bywin_html()` | 동행복권 HTML 페이지 호출 | `$drwNo`, `&$error` | 문자열 또는 null |
| `li_parse_prize_23_from_bywin()` | HTML 파싱 (2/3등 정보) | `$html`, `&$error` | 배열 또는 null |
| `li_save_lotto_draw()` | DB 저장 | `$data`, `&$error` | bool |
| `li_fetch_and_save_lotto_draw()` | 통합 함수 (API 호출 + 저장) | `$drwNo`, `&$error` | bool |

### 5.2 JavaScript 클래스/객체

#### `LottoDataLoader` - 데이터 로더

```javascript
const LottoDataLoader = {
    _cache: null,
    
    // 속성 접근자
    get data() {
        if (this._cache) return this._cache;
        // LOTTO_HISTORY_DATA 처리
        this._cache = this._processData();
        return this._cache;
    },
    
    // 데이터 처리
    _processData() {
        // 최근 100회차 히스토리 생성
        // 전체 통계 계산
        // Hot/Cold 번호 추출
    },
    
    // 통계 계산
    _calculateAllTimeStats(rounds) {
        // 빈도 분석
        // 홀짝/고저 비율
        // 연속번호 패턴
        // Hot/Cold 번호
    },
    
    // 번호 색상 반환
    getBallColor(num) {
        // 동행복권 공식 색상 기준
        // 1~10: 노랑, 11~20: 파랑, ...
    }
};
```

#### `lottoGenerator` - 번호 생성 엔진

```javascript
const lottoGenerator = {
    ready: false,
    dataLoader: LottoDataLoader,
    
    // 초기화
    async init() {
        this.ready = true;
        return this;
    },
    
    // 통계 분석
    analyzeStats() {
        // 최근 100회 빈도 분석
        // Hot/Cold 번호
        // 동반 출현 분석
        // 연속번호 패턴
        // 색상 분포
    },
    
    // 점수 계산
    calculateScore(numbers) {
        // 7가지 항목 평가
        // 50~100점 범위
    },
    
    // 번호 생성
    generate(style = 'balanced') {
        // 10가지 스타일 지원
        // 점수 70점 이상 채택
    },
    
    // 리포트 생성
    generateReport(result) {
        // 요약 정보
        // 인사이트
        // 색상 분포
    }
};
```

#### `GnuboardAPI` - API 래퍼

```javascript
const GnuboardAPI = {
    baseUrl: typeof G5_URL !== 'undefined' ? G5_URL : '',
    
    // 크레딧 조회
    async getCredits() {
        const response = await fetch(`${this.baseUrl}/api/get_credits.php`);
        const data = await response.json();
        // localStorage 동기화
        return data;
    },
    
    // 크레딧 사용
    async useCredit(round_no = 0) {
        const response = await fetch(`${this.baseUrl}/api/use_credit.php`, {
            method: 'POST',
            body: round_no > 0 ? `round_no=${round_no}` : ''
        });
        const data = await response.json();
        // localStorage 업데이트
        return data;
    },
    
    // 분석 결과 저장
    async saveAnalysis(numbers, round, score, strategy) {
        // ...
    },
    
    // 히스토리 조회
    async getHistory(page = 1, limit = 20) {
        // ...
    }
};
```

---

## 6. API 엔드포인트 상세

### 6.1 크레딧 관련 API

#### `GET /api/get_credits.php`

**요청:**
```
GET /api/get_credits.php
Cookie: PHPSESSID=...
```

**응답:**
```json
{
    "success": true,
    "user_id": "user123",
    "user_name": "홍길동",
    "profile_image": "https://...",
    "free_uses": 1,
    "credit_balance": 5,
    "total": 6,
    "analysis_count": 6,
    "is_premium": true
}
```

**처리 로직:**
```php
// 1. 로그인 체크
if (!$is_member) {
    return ['success' => false, 'error' => 'Not logged in'];
}

// 2. 크레딧 조회 (없으면 자동 생성)
$credit = lotto_get_credit_row($member['mb_id'], true);

// 3. 응답 반환
return [
    'success' => true,
    'free_uses' => $credit['free_uses'],
    'credit_balance' => $credit['credit_balance'],
    'total' => $credit['free_uses'] + $credit['credit_balance']
];
```

#### `POST /api/use_credit.php`

**요청:**
```
POST /api/use_credit.php
Content-Type: application/x-www-form-urlencoded

round_no=1202
```

**응답:**
```json
{
    "success": true,
    "message": "크레딧 사용 완료",
    "used_as": "free",
    "free_uses": 0,
    "credit_balance": 5,
    "total": 5
}
```

**처리 로직:**
```php
// 1. 로그인 체크
if (!$is_member) {
    return ['success' => false, 'error' => 'Not logged in'];
}

// 2. 크레딧 사용
$use = lotto_use_one_analysis(
    $member['mb_id'],
    'AI 분석 실행 (회차 ' . $round_no . ')',
    'round_' . $round_no
);

// 3. 응답 반환
return [
    'success' => $use['success'],
    'used_as' => $use['used_as'],
    'free_uses' => $use['free_uses'],
    'credit_balance' => $use['credit_balance']
];
```

### 6.2 분석 관련 API

#### `POST /api/save_analysis.php`

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

**처리 로직:**
```php
// 1. 로그인 체크
// 2. JSON 입력 파싱
// 3. 번호 유효성 검사 (1~45, 6개, 중복 없음)
// 4. 테이블 자동 생성 (없을 경우)
// 5. 데이터 저장
sql_query("INSERT INTO g5_lotto_analysis 
           (mb_id, lotto_round, numbers, score, strategy, created_at) 
           VALUES (...)");
```

### 6.3 결제 관련 API

#### `POST /api/toss/create_order.php`

**요청:**
```json
{
    "product": "CREDIT_20"
}
```

**응답:**
```json
{
    "ok": true,
    "clientKey": "test_ck_...",
    "orderId": "LC20251215123456...",
    "amount": 2500,
    "orderName": "크레딧 20회 충전",
    "customerName": "홍길동",
    "customerEmail": "user@example.com",
    "successUrl": "https://lottoinsight.ai/payments/toss/success.php",
    "failUrl": "https://lottoinsight.ai/payments/toss/fail.php"
}
```

**처리 로직:**
```php
// 1. 상품 정보 매핑
$PRODUCTS = [
    'CREDIT_5' => ['amount' => 1000, 'credit' => 5],
    'CREDIT_20' => ['amount' => 2500, 'credit' => 20],
    'CREDIT_50' => ['amount' => 5000, 'credit' => 50]
];

// 2. 주문번호 생성
$orderId = 'LC' . date('YmdHis') . bin2hex(random_bytes(6));

// 3. 주문 정보 DB 저장
sql_query("INSERT INTO g5_lotto_toss_orders 
           (order_id, mb_id, product_code, amount, credit_qty, status) 
           VALUES (...)");

// 4. 토스 결제창 정보 반환
```

---

## 7. 프론트엔드 구조

### 7.1 `result.php` 클라이언트 사이드 로직

#### 상태 관리

```javascript
let state = {
    isLoggedIn: false,
    freeCredit: 0,
    paidCredit: 0,
    userName: '게스트',
    profileImage: null,
    userId: null,
    selectedStyles: ['hotcold', 'ac'],
    currentResults: [],
    history: []
};
```

#### 주요 함수

##### `startAnalysis()` - 분석 시작

```javascript
async function startAnalysis() {
    // 1. 크레딧 사용 요청
    const resp = await fetch(location.pathname, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ mode: 'use_credit' })
    });
    
    const data = await resp.json();
    
    if (!data.success) {
        // 에러 처리
        updateCreditDisplay();
        return;
    }
    
    // 2. 크레딧 상태 업데이트
    state.freeCredit = data.free_uses;
    state.paidCredit = data.credit_balance;
    updateCreditDisplay();
    
    // 3. 번호 생성 엔진 준비
    await ensureGeneratorReady();
    
    // 4. 로딩 화면 표시
    showLoading();
    
    // 5. 번호 생성 및 표시
    setTimeout(async () => {
        await generateAndDisplay();
        hideLoading();
    }, 2000); // 시각적 효과를 위한 딜레이
}
```

##### `generateAndDisplay()` - 번호 생성 및 표시

```javascript
async function generateAndDisplay() {
    const generator = await ensureGeneratorReady();
    const selectedStyles = state.selectedStyles;
    const results = [];
    
    // 선택된 스타일로 번호 생성
    for (const style of selectedStyles) {
        const result = generator.generate(style);
        results.push(result);
    }
    
    // 결과 저장
    state.currentResults = results;
    
    // 결과 렌더링
    renderResults(results);
    
    // 저장 기능 활성화
    enableSaveFunction();
}
```

##### `renderResults()` - 결과 렌더링

```javascript
function renderResults(results) {
    const container = document.getElementById('resultsContainer');
    
    results.forEach((result, index) => {
        const card = document.createElement('div');
        card.className = 'result-card';
        card.dataset.index = index;
        
        // 번호 표시
        const numbersHtml = result.numbers.map(n => {
            const color = getBallColor(n);
            return `<span class="ball ball-${color}">${n}</span>`;
        }).join('');
        
        // 점수 표시
        const scoreHtml = `<div class="score">${result.score}점</div>`;
        
        // 스토리 표시
        const storiesHtml = result.stories.map(s => {
            return `<div class="story">
                <span class="story-label">${s.label}</span>
                <span class="story-desc">${s.description}</span>
            </div>`;
        }).join('');
        
        card.innerHTML = `
            <div class="result-numbers">${numbersHtml}</div>
            ${scoreHtml}
            <div class="result-stories">${storiesHtml}</div>
        `;
        
        container.appendChild(card);
    });
}
```

##### `saveCurrentResult()` - 결과 저장

```javascript
async function saveCurrentResult(index) {
    const result = state.currentResults[index];
    if (!result) return;
    
    try {
        const response = await fetch('/api/save_analysis.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                numbers: result.numbers,
                round: getAnalysisRound(),
                score: result.score,
                strategy: result.style
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ 분석 결과가 저장되었습니다!', 'success');
            updateHistoryDisplay();
        } else {
            showToast('저장 실패: ' + (data.error || '알 수 없는 오류'), 'error');
        }
    } catch (e) {
        console.error('저장 오류:', e);
        showToast('네트워크 오류로 저장에 실패했습니다.', 'error');
    }
}
```

### 7.2 UI 컴포넌트

#### 로딩 모달

```javascript
function showLoading() {
    const modal = document.getElementById('loadingModal');
    modal.classList.add('active');
    
    const loadingBar = document.getElementById('loadingBar');
    const loadingText = document.getElementById('loadingText');
    
    const messages = [
        "📊 동행복권 데이터 로딩 중...",
        "🔍 최근 100회 당첨 패턴 분석...",
        "📈 과출/미출 번호 계산 중...",
        "⚖️ 홀짝/고저 균형 최적화...",
        "✨ 최종 조합 선별 중..."
    ];
    
    let progress = 0;
    let msgIndex = 0;
    
    const interval = setInterval(() => {
        progress += Math.random() * 12 + 4;
        if (progress > 100) progress = 100;
        
        loadingBar.style.width = progress + '%';
        
        if (progress > msgIndex * 20 && msgIndex < messages.length) {
            loadingText.textContent = messages[msgIndex];
            msgIndex++;
        }
        
        if (progress >= 100) {
            clearInterval(interval);
        }
    }, 100);
}
```

#### 토스트 알림

```javascript
function showToast(message, type = 'info', duration = 2500) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        padding: 16px 24px;
        background: var(--primary);
        border: 1px solid rgba(0,224,164,0.3);
        border-radius: 12px;
        z-index: 10000;
        animation: toastIn 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}
```

#### 충전 모달

```javascript
function showChargeModal() {
    const modal = document.createElement('div');
    modal.id = 'chargeModal';
    modal.innerHTML = `
        <div class="charge-modal-backdrop" onclick="closeChargeModal()"></div>
        <div class="charge-modal-content">
            <div class="charge-modal-header">
                <h3>🔋 크레딧 충전</h3>
                <button onclick="closeChargeModal()">×</button>
            </div>
            <div class="charge-modal-body">
                <div class="charge-current">
                    <div>현재 보유 크레딧</div>
                    <div>${state.freeCredit + state.paidCredit}회</div>
                </div>
                
                <div class="charge-options">
                    <div class="charge-option" data-product="CREDIT_5" onclick="selectChargeOption(this)">
                        <div>5회</div>
                        <div>₩1,000</div>
                    </div>
                    <div class="charge-option popular" data-product="CREDIT_20" onclick="selectChargeOption(this)">
                        <div>20회</div>
                        <div>₩2,500</div>
                    </div>
                    <div class="charge-option" data-product="CREDIT_50" onclick="selectChargeOption(this)">
                        <div>50회</div>
                        <div>₩5,000</div>
                    </div>
                </div>
                
                <button onclick="processPayment()">결제하기</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}
```

---

## 8. 데이터베이스 스키마

### 8.1 주요 테이블 상세

#### `g5_lotto_draw` - 로또 회차 데이터

```sql
CREATE TABLE g5_lotto_draw (
    draw_no INT PRIMARY KEY,                    -- 회차 번호 (1~1201)
    draw_date DATE NOT NULL,                    -- 추첨일 (YYYY-MM-DD)
    
    -- 당첨번호
    n1 INT NOT NULL,                            -- 1번째 번호
    n2 INT NOT NULL,                            -- 2번째 번호
    n3 INT NOT NULL,                            -- 3번째 번호
    n4 INT NOT NULL,                            -- 4번째 번호
    n5 INT NOT NULL,                            -- 5번째 번호
    n6 INT NOT NULL,                            -- 6번째 번호
    bonus INT NOT NULL,                         -- 보너스 번호
    
    -- 1등 정보
    first_prize_total BIGINT NULL,              -- 1등 총 당첨금
    first_prize_each BIGINT NULL,               -- 1등 1인당 당첨금
    first_winners INT NULL,                     -- 1등 당첨자 수
    
    -- 2등 정보
    second_winners INT NULL,                    -- 2등 당첨자 수
    second_prize_each BIGINT NULL,              -- 2등 1인당 당첨금
    
    -- 3등 정보
    third_winners INT NULL,                     -- 3등 당첨자 수
    third_prize_each BIGINT NULL,               -- 3등 1인당 당첨금
    
    -- 판매 정보
    total_sales BIGINT NULL,                    -- 총 판매액
    
    -- 타임스탬프
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    
    INDEX idx_draw_date (draw_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**데이터 예시:**
```sql
INSERT INTO g5_lotto_draw VALUES (
    1201, '2025-12-06',
    7, 9, 24, 27, 35, 36, 37,
    1000000000, 1000000000, 5,
    84, 53326506,
    2589, 1500000,
    10000000000,
    '2025-12-07 10:00:00', '2025-12-07 10:00:00'
);
```

#### `g5_lotto_credit` - 회원 크레딧

```sql
CREATE TABLE g5_lotto_credit (
    mb_id VARCHAR(100) PRIMARY KEY,             -- 회원 ID
    free_uses INT DEFAULT 0,                   -- 무료 사용 횟수
    credit_balance INT DEFAULT 0,              -- 유료 크레딧 잔액
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    
    INDEX idx_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**데이터 예시:**
```sql
INSERT INTO g5_lotto_credit VALUES (
    'user123',
    1,      -- 무료 1회 (신규 회원 가입 시 지급)
    5,      -- 유료 5회 (결제로 충전)
    '2025-12-10 10:00:00',
    '2025-12-15 14:30:00'
);
```

#### `g5_lotto_credit_log` - 크레딧 사용 로그

```sql
CREATE TABLE g5_lotto_credit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mb_id VARCHAR(100) NOT NULL,                -- 회원 ID
    change_type VARCHAR(20) NOT NULL,          -- 'free', 'use', 'charge', 'welcome', 'admin_adjust'
    amount INT NOT NULL,                       -- 변경량 (-1, +10 등)
    before_balance INT NOT NULL,               -- 변경 전 잔액
    after_balance INT NOT NULL,                -- 변경 후 잔액
    memo VARCHAR(255) DEFAULT '',              -- 메모
    ref_key VARCHAR(100) DEFAULT '',            -- 참조키 (회차 등)
    ip VARCHAR(50) DEFAULT '',                 -- IP 주소
    created_at DATETIME NOT NULL,
    
    INDEX idx_mb_id (mb_id),
    INDEX idx_change_type (change_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**로그 타입:**
- `free`: 무료 크레딧 사용
- `use`: 유료 크레딧 사용
- `charge`: 크레딧 충전
- `welcome`: 신규 회원 무료 크레딧 지급
- `admin_adjust`: 관리자 조정

#### `g5_lotto_analysis` - 사용자 분석 결과

```sql
CREATE TABLE g5_lotto_analysis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mb_id VARCHAR(100) NOT NULL,                -- 회원 ID
    lotto_round INT DEFAULT 0,                 -- 분석 대상 회차
    numbers VARCHAR(50) NOT NULL,              -- 추천 번호 (콤마 구분: "1,2,3,4,5,6")
    score INT DEFAULT 0,                       -- 분석 점수 (50~100)
    strategy VARCHAR(100) DEFAULT '',          -- 사용된 전략
    is_winner TINYINT(1) DEFAULT 0,           -- 당첨 여부
    match_count INT DEFAULT 0,                 -- 일치 개수
    created_at DATETIME NOT NULL,
    
    INDEX idx_mb_id (mb_id),
    INDEX idx_lotto_round (lotto_round),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### `g5_lotto_toss_orders` - 토스 결제 주문

```sql
CREATE TABLE g5_lotto_toss_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100) UNIQUE NOT NULL,      -- 주문번호 (LC + 날짜시간 + 랜덤)
    mb_id VARCHAR(100) NOT NULL,               -- 회원 ID
    product_code VARCHAR(50) NOT NULL,         -- 상품 코드 (CREDIT_5, CREDIT_20, CREDIT_50)
    amount INT NOT NULL,                       -- 결제 금액
    credit_qty INT NOT NULL,                  -- 충전 크레딧 수
    status VARCHAR(20) DEFAULT 'READY',        -- 'READY', 'SUCCESS', 'FAIL', 'DONE'
    payment_key VARCHAR(200) DEFAULT '',       -- 토스 결제 키
    fail_code VARCHAR(50) DEFAULT '',          -- 실패 코드
    fail_message VARCHAR(255) DEFAULT '',     -- 실패 메시지
    raw_json TEXT,                             -- 토스 API 응답 원문
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    approved_at DATETIME NULL,                 -- 결제 승인 시간
    
    INDEX idx_mb_id (mb_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 9. 보안 및 최적화

### 9.1 보안 조치

#### SQL Injection 방지

```php
// ✅ 올바른 방법
$mb_id_esc = sql_real_escape_string($mb_id);
$sql = "SELECT * FROM g5_member WHERE mb_id = '{$mb_id_esc}'";

// ❌ 잘못된 방법 (사용 금지)
$sql = "SELECT * FROM g5_member WHERE mb_id = '{$mb_id}'";
```

#### XSS 방지

```php
// ✅ 올바른 방법
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// HTML 출력 시
<div><?= htmlspecialchars($data['memo'], ENT_QUOTES, 'UTF-8') ?></div>
```

#### CSRF 방지

```php
// 세션 기반 토큰 사용 (일부 페이지)
$token = md5(uniqid(rand(), true));
$_SESSION['csrf_token'] = $token;

// 폼 제출 시 검증
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF 토큰 불일치');
}
```

#### 세션 관리

```php
// GNUBOARD 세션 시스템 활용
set_session('ss_mb_id', $member['mb_id']);
set_session('ss_mb_key', md5($member['mb_datetime'] . $member['mb_ip']));

// 세션 검증
if (get_session('ss_mb_id') !== $member['mb_id']) {
    // 세션 불일치 처리
}
```

### 9.2 성능 최적화

#### 데이터베이스 최적화

```sql
-- 인덱스 추가
CREATE INDEX idx_mb_id ON g5_lotto_credit(mb_id);
CREATE INDEX idx_draw_no ON g5_lotto_draw(draw_no);
CREATE INDEX idx_created_at ON g5_lotto_credit_log(created_at);

-- 쿼리 최적화
-- ❌ 비효율적
SELECT * FROM g5_lotto_draw ORDER BY draw_no DESC LIMIT 1;

-- ✅ 효율적 (인덱스 활용)
SELECT * FROM g5_lotto_draw WHERE draw_no = (SELECT MAX(draw_no) FROM g5_lotto_draw);
```

#### 캐싱 전략

```javascript
// JavaScript 데이터 캐싱
const LottoDataLoader = {
    _cache: null,
    
    get data() {
        if (this._cache) return this._cache;
        this._cache = this._processData();
        return this._cache;
    },
    
    clearCache() {
        this._cache = null;
    }
};
```

#### API 호출 최적화

```javascript
// 중복 요청 방지
let creditRequestInProgress = false;

async function getCredits() {
    if (creditRequestInProgress) {
        return; // 이미 요청 중이면 대기
    }
    
    creditRequestInProgress = true;
    try {
        const data = await fetch('/api/get_credits.php');
        return await data.json();
    } finally {
        creditRequestInProgress = false;
    }
}
```

---

## 10. 에러 처리 및 로깅

### 10.1 에러 처리 패턴

#### PHP 에러 처리

```php
// 함수 레벨 에러 처리
function li_get_lotto_api_json($drwNo, &$error = '')
{
    $error = '';
    
    try {
        $ch = curl_init();
        // ...
        $raw = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = 'cURL 오류: ' . curl_error($ch);
            return null;
        }
        
        // ...
    } catch (Exception $e) {
        $error = '예외 발생: ' . $e->getMessage();
        return null;
    }
}
```

#### JavaScript 에러 처리

```javascript
// try-catch로 에러 처리
async function startAnalysis() {
    try {
        const resp = await fetch(...);
        const data = await resp.json();
        
        if (!data.success) {
            // 에러 메시지 표시
            showToast(data.message || '오류가 발생했습니다.', 'error');
            return;
        }
        
        // 성공 처리
    } catch (e) {
        console.error('분석 오류:', e);
        showToast('네트워크 오류가 발생했습니다.', 'error');
    }
}
```

### 10.2 로깅

#### 크레딧 사용 로그

```php
// 모든 크레딧 변경 사항 로그 기록
sql_query("INSERT INTO g5_lotto_credit_log
           SET mb_id = '{$mb_id}',
               change_type = '{$change_type}',
               amount = {$amount},
               before_balance = {$before},
               after_balance = {$after},
               memo = '{$memo}',
               ref_key = '{$ref_key}',
               ip = '{$ip}',
               created_at = NOW()");
```

#### 분석 실행 로그

```php
// 분석 실행 시 로그 기록
sql_query("INSERT INTO g5_lotto_analysis_log
           SET mb_id = '{$mb_id}',
               round_no = {$round_no},
               user_ip = '{$ip}',
               created_at = NOW()");
```

---

## 11. 배포 및 운영

### 11.1 환경 요구사항

- **PHP**: 7.4 이상
- **MySQL**: 5.7 이상 / MariaDB 10.3 이상
- **웹서버**: Apache 2.4+ / Nginx 1.18+
- **필수 확장 모듈**:
  - cURL (API 호출용)
  - DOM (HTML 파싱용)
  - JSON (데이터 처리용)
  - PDO/MySQLi (데이터베이스)

### 11.2 크론 설정

```bash
# 매주 일요일 새벽 2시 실행
0 2 * * 0 /usr/bin/php /path/to/cron/lotto_weekly.php >> /var/log/lotto_weekly.log 2>&1

# 매일 새벽 3시 판매점 데이터 동기화
0 3 * * * /usr/bin/php /path/to/cron/lotto_store_sync.php >> /var/log/lotto_store.log 2>&1
```

### 11.3 모니터링 포인트

1. **크론 실행 로그**
   - 주간 회차 업데이트 성공 여부
   - API 호출 실패율

2. **에러 로그**
   - PHP 에러 로그 (`error_log`)
   - JavaScript 콘솔 에러

3. **성능 모니터링**
   - 데이터베이스 쿼리 시간
   - API 응답 시간
   - 페이지 로딩 시간

---

## 12. 코드 품질 및 유지보수

### 12.1 코딩 컨벤션

#### PHP 코딩 스타일

```php
// 함수명: snake_case
function lotto_get_credit_row($mb_id, $create_if_not_exists = false)
{
    // 변수명: snake_case
    $mb_id_esc = sql_real_escape_string($mb_id);
    
    // 상수: UPPER_CASE
    define('G5_LOTTO_CREDIT_TABLE', 'g5_lotto_credit');
    
    // 배열 키: snake_case
    return [
        'success' => true,
        'free_uses' => 0,
        'credit_balance' => 0
    ];
}
```

#### JavaScript 코딩 스타일

```javascript
// 변수명: camelCase
const lottoGenerator = {
    // 함수명: camelCase
    async generateNumbers(style) {
        // 상수: UPPER_CASE
        const MAX_ATTEMPTS = 1000;
        
        // ...
    }
};
```

### 12.2 주석 규칙

```php
/**
 * 함수 설명
 * 
 * @param string $mb_id 회원 아이디
 * @param bool $create_if_not_exists 없으면 생성 여부
 * @return array
 *  - success bool
 *  - free_uses int
 *  - credit_balance int
 */
function lotto_get_credit_row($mb_id, $create_if_not_exists = false)
{
    // 인라인 주석: 간단한 설명
    $mb_id_esc = sql_real_escape_string($mb_id); // SQL Injection 방지
}
```

---

## 13. 확장 가능성

### 13.1 새로운 분석 알고리즘 추가

```javascript
// lotto-generator.js에 새로운 전략 추가
_generateNewStrategy(stats) {
    // 새로운 알고리즘 구현
    const numbers = [];
    // ...
    return numbers.sort((a, b) => a - b);
}

// generate() 함수에 추가
case 'new_strategy':
    numbers = this._generateNewStrategy(stats);
    break;
```

### 13.2 새로운 결제 수단 추가

```php
// api/payment/create_order.php
$payment_methods = [
    'toss' => 'TossPayments',
    'kakao' => 'KakaoPay',  // 새로 추가
    'naver' => 'NaverPay'   // 새로 추가
];

$method = $_POST['method'] ?? 'toss';
$handler = $payment_methods[$method];

require_once "api/{$handler}/create_order.php";
```

---

## 14. 문제 해결 가이드

### 14.1 일반적인 문제

#### 문제: 크레딧이 차감되지 않음

**원인**: 트랜잭션 실패 또는 중복 요청

**해결**:
```php
// 트랜잭션 확인
sql_query("BEGIN");
$ok1 = sql_query("UPDATE ...");
$ok2 = sql_query("INSERT ...");
if ($ok1 && $ok2) {
    sql_query("COMMIT");
} else {
    sql_query("ROLLBACK");
    // 에러 로그 기록
}
```

#### 문제: 번호 생성이 느림

**원인**: 알고리즘 복잡도 또는 데이터 크기

**해결**:
```javascript
// 캐싱 활용
if (this._cache) return this._cache;

// 최적화된 알고리즘 사용
// 불필요한 반복 제거
```

---

## 15. 참고 자료

### 15.1 외부 API 문서

- **동행복권 API**: https://www.dhlottery.co.kr/common.do?method=getLottoNumber
- **토스페이먼츠 API**: https://docs.tosspayments.com/
- **카카오 로그인 API**: https://developers.kakao.com/docs/latest/ko/kakaologin/rest-api

### 15.2 GNUBOARD 문서

- **공식 사이트**: https://sir.kr/
- **매뉴얼**: https://sir.kr/g5_manual

---

**작성일**: 2025-12-15  
**버전**: 2.0  
**작성자**: AI Assistant

---

## 부록: 주요 파일 라인 수

| 파일 | 라인 수 | 설명 |
|------|---------|------|
| `index.php` | 8,077 | 메인 랜딩 페이지 |
| `result.php` | 4,394 | 분석 결과 페이지 |
| `scripts/lotto-generator.js` | 980 | AI 분석 엔진 |
| `scripts/lotto-data.js` | 1,207 | 로또 히스토리 데이터 |
| `lib/lotto_store.lib.php` | 779 | 판매점 정보 관리 |
| `lib/lotto_draw.lib.php` | 386 | 회차 데이터 처리 |
| `lib/lotto_credit.lib.php` | 305 | 크레딧 관리 |
| `auth.php` | 1,132 | 로그인 페이지 |

**총 코드 라인 수**: 약 17,000+ 줄

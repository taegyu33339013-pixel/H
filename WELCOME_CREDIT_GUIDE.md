# 신규 회원 무료 크레딧 지급 가이드

## 📋 개요

신규 회원 가입 시 **무료 크레딧 1회**를 자동으로 지급하도록 변경했습니다.

---

## ✅ 변경 사항

### 1. 자동 지급 로직 제거

**변경 전:**
- `lotto_get_credit_row()` 함수에서 크레딧 행이 없을 때 자동으로 무료 크레딧 2회 지급

**변경 후:**
- 크레딧 행이 없을 때는 무료 크레딧 0회로 생성
- 회원가입 처리 파일에서만 무료 크레딧 1회 지급

### 2. 신규 함수 추가

**`lotto_grant_welcome_credit($mb_id, $memo)`**
- 신규 회원 가입 시 무료 크레딧 1회 지급
- 중복 지급 방지 로직 포함
- `change_type = 'welcome'` 로그 기록

---

## 🔧 수정된 파일

### 1. `lib/lotto_credit.lib.php`

#### `lotto_get_credit_row()` 함수 수정
```php
// 변경 전: free_uses = 2
// 변경 후: free_uses = 0
```

#### 신규 함수 추가
```php
function lotto_grant_welcome_credit($mb_id, $memo = '신규 회원 가입 축하')
{
    // 중복 지급 방지
    // welcome 로그 확인
    // 무료 크레딧 1회 지급
    // 로그 기록 (change_type = 'welcome')
}
```

### 2. 회원가입 처리 파일 수정

#### `kakao_login.php`
```php
// 신규 회원 가입 성공 시
lotto_grant_welcome_credit($social_mb_id, '카카오 로그인 신규 가입 축하');
```

#### `bbs/register_form_update.php`
```php
// 일반 회원가입 성공 시
lotto_grant_welcome_credit($mb_id, '회원가입 축하');
```

#### `plugin/social/register_member_update.php`
```php
// 소셜 로그인 회원가입 성공 시
lotto_grant_welcome_credit($mb_id, '소셜 로그인 신규 가입 축하');
```

#### `plugin/social_20251207/register_member_update.php`
```php
// 소셜 로그인 회원가입 성공 시 (백업 버전)
lotto_grant_welcome_credit($mb_id, '소셜 로그인 신규 가입 축하');
```

---

## 🔒 중복 지급 방지

### 로직

1. **크레딧 행 확인**
   - 이미 크레딧 행이 있고 무료 크레딧이 0보다 크면 → 지급 안 함
   - 무료 크레딧이 0이어도 welcome 로그가 있으면 → 지급 안 함

2. **로그 확인**
   - `change_type = 'welcome'` 로그가 있으면 중복 지급 방지

3. **트랜잭션 처리**
   - 크레딧 지급과 로그 기록을 트랜잭션으로 처리하여 데이터 일관성 보장

### 예시

```php
// 첫 번째 호출: 성공 (무료 크레딧 1회 지급)
$result1 = lotto_grant_welcome_credit('user123');
// 결과: success = true, free_uses = 1

// 두 번째 호출: 실패 (이미 지급됨)
$result2 = lotto_grant_welcome_credit('user123');
// 결과: success = false, reason = 'ALREADY_GRANTED'
```

---

## 📊 크레딧 로그

### welcome 타입 로그

```sql
SELECT * FROM g5_lotto_credit_log 
WHERE change_type = 'welcome' 
ORDER BY created_at DESC;
```

**로그 필드:**
- `change_type`: 'welcome'
- `amount`: 1
- `before_balance`: 0
- `after_balance`: 1
- `memo`: '신규 회원 가입 축하' (또는 커스텀 메모)
- `ref_key`: 'welcome_YYYYMMDDHHMMSS'

---

## 🧪 테스트 방법

### 1. 신규 회원 가입 테스트

```php
// 테스트 스크립트
require_once './common.php';
require_once G5_LIB_PATH . '/lotto_credit.lib.php';

$test_mb_id = 'test_user_' . time();

// 1. 신규 회원 가입 시뮬레이션
$result1 = lotto_grant_welcome_credit($test_mb_id, '테스트 가입');
echo "첫 번째 지급: " . ($result1['success'] ? '성공' : '실패') . "\n";
echo "무료 크레딧: " . $result1['free_uses'] . "\n";

// 2. 중복 지급 시도
$result2 = lotto_grant_welcome_credit($test_mb_id, '테스트 가입');
echo "두 번째 지급: " . ($result2['success'] ? '성공' : '실패') . "\n";
echo "사유: " . $result2['reason'] . "\n";

// 3. 크레딧 확인
$credit = lotto_get_credit_row($test_mb_id);
echo "최종 무료 크레딧: " . $credit['free_uses'] . "\n";
```

### 2. 기존 회원 크레딧 조회 테스트

```php
// 기존 회원이 크레딧을 조회할 때는 자동 생성만 되고 지급 안 됨
$credit = lotto_get_credit_row('existing_user', true);
// 결과: free_uses = 0 (지급 안 됨)
```

---

## ⚠️ 주의사항

### 1. 기존 회원 처리

- 기존 회원이 크레딧을 조회할 때는 자동으로 크레딧 행이 생성되지만 무료 크레딧은 지급되지 않습니다.
- 기존 회원에게 무료 크레딧을 지급하려면 관리자가 수동으로 지급해야 합니다.

### 2. 회원가입 실패 시

- 회원가입이 실패하면 크레딧도 지급되지 않습니다.
- 트랜잭션으로 처리되어 데이터 일관성이 보장됩니다.

### 3. 중복 가입 방지

- 동일한 `mb_id`로 여러 번 가입하는 것을 방지해야 합니다.
- GNUBOARD의 기본 회원가입 검증 로직을 사용합니다.

---

## 📈 예상 효과

| 항목 | 변경 전 | 변경 후 | 효과 |
|------|---------|---------|------|
| **신규 회원 무료 크레딧** | 2회 | 1회 | ✅ 50% 절감 |
| **중복 지급 방지** | 없음 | 있음 | ✅ 100% 방지 |
| **자동 지급** | 크레딧 조회 시 | 회원가입 시만 | ✅ 명확한 지급 시점 |

---

## 🔍 검증 쿼리

```sql
-- 1. 신규 회원 가입 후 무료 크레딧 확인
SELECT 
    mb_id,
    free_uses,
    credit_balance,
    created_at
FROM g5_lotto_credit
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY created_at DESC;

-- 2. welcome 로그 확인
SELECT 
    mb_id,
    amount,
    memo,
    created_at
FROM g5_lotto_credit_log
WHERE change_type = 'welcome'
ORDER BY created_at DESC
LIMIT 10;

-- 3. 중복 지급 확인 (같은 회원에 welcome 로그가 2개 이상인 경우)
SELECT 
    mb_id,
    COUNT(*) AS grant_count
FROM g5_lotto_credit_log
WHERE change_type = 'welcome'
GROUP BY mb_id
HAVING grant_count > 1;

-- 4. 무료 크레딧이 0인 신규 회원 확인 (지급 누락 가능성)
SELECT 
    c.mb_id,
    c.free_uses,
    c.created_at,
    m.mb_datetime AS member_created_at
FROM g5_lotto_credit c
JOIN g5_member m ON c.mb_id = m.mb_id
WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
AND c.free_uses = 0
AND NOT EXISTS (
    SELECT 1 FROM g5_lotto_credit_log l
    WHERE l.mb_id = c.mb_id
    AND l.change_type = 'welcome'
);
```

---

## 🐛 문제 해결

### 문제 1: 신규 회원이 무료 크레딧을 받지 못함

**원인**: 회원가입 처리 파일에서 `lotto_grant_welcome_credit()` 호출 누락

**해결**:
1. 회원가입 처리 파일 확인
2. `lotto_grant_welcome_credit()` 호출 추가
3. 에러 로그 확인

### 문제 2: 중복 지급 발생

**원인**: welcome 로그 확인 로직 오류

**해결**:
1. 로그 확인 쿼리 실행
2. 중복 지급된 회원 확인
3. 수동으로 크레딧 조정

### 문제 3: 기존 회원이 무료 크레딧을 받음

**원인**: 크레딧 조회 시 자동 지급 로직이 남아있음

**해결**:
- 이미 수정 완료: `lotto_get_credit_row()`에서 자동 지급 제거

---

**작성일**: 2025-12-15  
**버전**: 1.0

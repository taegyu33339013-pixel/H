# 프론트엔드 업데이트 가이드

## 📋 개요

크레딧 시스템 통일로 인한 API 응답 형식 변경에 맞춰 프론트엔드 코드를 업데이트했습니다.

---

## ✅ 완료된 수정 사항

### 1. `scripts/gnuboard-api.js` 수정 완료

#### `getCredits()` 함수
**변경 전:**
```javascript
if (data.success) {
    localStorage.setItem('userCredits', data.credits);
    localStorage.setItem('analysisCount', data.analysis_count);
}
```

**변경 후:**
```javascript
if (data.success) {
    const freeUses = data.free_uses || 0;
    const creditBalance = data.credit_balance || 0;
    const total = data.total || (freeUses + creditBalance);
    
    // localStorage 동기화 (하위 호환성 유지)
    localStorage.setItem('userCredits', total);
    localStorage.setItem('analysisCount', total);
    localStorage.setItem('freeCredits', freeUses);
    localStorage.setItem('paidCredits', creditBalance);
}
```

#### `useCredit()` 함수
**변경 전:**
```javascript
if (data.success) {
    localStorage.setItem('userCredits', data.remaining_credits);
    localStorage.setItem('analysisCount', data.analysis_count);
}
```

**변경 후:**
```javascript
if (data.success) {
    const freeUses = data.free_uses || 0;
    const creditBalance = data.credit_balance || 0;
    const total = data.total || (freeUses + creditBalance);
    
    localStorage.setItem('userCredits', total);
    localStorage.setItem('analysisCount', total);
    localStorage.setItem('freeCredits', freeUses);
    localStorage.setItem('paidCredits', creditBalance);
}
```

**추가 기능:**
- `round_no` 파라미터 지원 (선택사항)

### 2. `result.php` 수정 완료

#### `refreshCreditBalance()` 함수
**변경 전:**
```javascript
const res = await fetch('/api/credit/balance.php', ...);
const data = await res.json();
if(el1) el1.textContent = Number(data.free_uses).toLocaleString();
```

**변경 후:**
```javascript
const res = await fetch('/api/get_credits.php', ...);
const data = await res.json();
const freeUses = Number(data.free_uses || 0);
const creditBalance = Number(data.credit_balance || 0);
const total = Number(data.total || (freeUses + creditBalance));

// 상태 업데이트
if (typeof state !== 'undefined') {
    state.freeCredit = freeUses;
    state.paidCredit = creditBalance;
    updateCreditDisplay();
}

// 총 크레딧 표시
if(el1) el1.textContent = total.toLocaleString();

// 무료/유료 분리 표시 지원
document.querySelectorAll('[data-free-credits]').forEach(el=>{
    el.textContent = freeUses.toLocaleString();
});
document.querySelectorAll('[data-paid-credits]').forEach(el=>{
    el.textContent = creditBalance.toLocaleString();
});
```

### 3. `api/credit/balance.php` 수정 완료

전용 크레딧 시스템을 사용하도록 수정했습니다.

**변경 전:**
```php
$row = sql_fetch("SELECT free_uses FROM g5_lotto_credit ...");
$balance = $row ? (int)$row['free_uses'] : 0;
echo json_encode(['ok'=>true,'free_uses'=>$balance]);
```

**변경 후:**
```php
$credit = lotto_get_credit_row($member['mb_id'], true);
$free_uses = (int)($credit['free_uses'] ?? 0);
$credit_balance = (int)($credit['credit_balance'] ?? 0);
$total = $free_uses + $credit_balance;

echo json_encode([
    'ok'=>true,
    'success'=>true,
    'free_uses'=>$free_uses,
    'credit_balance'=>$credit_balance,
    'total'=>$total
]);
```

---

## 📊 API 응답 형식 변경 요약

### `GET /api/get_credits.php`

**변경 전:**
```json
{
    "success": true,
    "credits": 500,
    "analysis_count": 5
}
```

**변경 후:**
```json
{
    "success": true,
    "free_uses": 2,
    "credit_balance": 5,
    "total": 7,
    "analysis_count": 7
}
```

### `POST /api/use_credit.php`

**변경 전:**
```json
{
    "success": true,
    "used_credits": 100,
    "remaining_credits": 400,
    "analysis_count": 4
}
```

**변경 후:**
```json
{
    "success": true,
    "used_as": "free",
    "free_uses": 1,
    "credit_balance": 5,
    "total": 6
}
```

---

## 🎨 UI 업데이트 가이드

### 크레딧 표시 방법

#### 1. 총 크레딧만 표시 (기존 방식)
```html
<span id="navCredit">0회</span>
```

**JavaScript:**
```javascript
const totalCredits = state.freeCredit + state.paidCredit;
document.getElementById('navCredit').textContent = `${totalCredits}회`;
```

#### 2. 무료/유료 분리 표시 (권장)
```html
<span data-free-credits>0</span>회 (무료) + 
<span data-paid-credits>0</span>회 (유료) = 
<span id="totalCredits">0</span>회
```

**JavaScript:**
```javascript
document.querySelectorAll('[data-free-credits]').forEach(el => {
    el.textContent = state.freeCredit;
});
document.querySelectorAll('[data-paid-credits]').forEach(el => {
    el.textContent = state.paidCredit;
});
document.getElementById('totalCredits').textContent = 
    state.freeCredit + state.paidCredit;
```

#### 3. 사용된 크레딧 타입 표시
```html
<div id="creditUsedInfo"></div>
```

**JavaScript:**
```javascript
// 크레딧 사용 후
if (data.used_as === 'free') {
    document.getElementById('creditUsedInfo').textContent = 
        '무료 크레딧 1회 사용';
} else if (data.used_as === 'paid') {
    document.getElementById('creditUsedInfo').textContent = 
        '유료 크레딧 1회 사용';
}
```

---

## 🔄 크레딧 갱신 패턴

### 1. 페이지 로드 시
```javascript
// result.php에서 이미 서버에서 주입
const SERVER_FREE_CREDIT = <?php echo $server_free_credits; ?>;
const SERVER_PAID_CREDIT = <?php echo $server_paid_credits; ?>;

state.freeCredit = SERVER_FREE_CREDIT;
state.paidCredit = SERVER_PAID_CREDIT;
```

### 2. 크레딧 사용 후
```javascript
// API 응답에서 직접 업데이트
const data = await GnuboardAPI.useCredit();
if (data.success) {
    state.freeCredit = data.free_uses;
    state.paidCredit = data.credit_balance;
    updateCreditDisplay();
}
```

### 3. 수동 갱신
```javascript
// refreshCreditBalance() 함수 사용
await refreshCreditBalance();
```

### 4. 결제 완료 후
```javascript
// payments/toss/success.php에서 리다이렉트 후
// result.php의 refreshCreditBalance() 자동 호출
// 또는 페이지 새로고침
location.reload();
```

---

## 📝 추가 수정이 필요한 경우

### 다른 페이지에서 크레딧 표시

만약 다른 페이지에서도 크레딧을 표시해야 한다면:

```javascript
// 1. API 호출
const response = await fetch('/api/get_credits.php', {
    credentials: 'include'
});
const data = await response.json();

if (data.success) {
    // 2. UI 업데이트
    const freeEl = document.getElementById('freeCredits');
    const paidEl = document.getElementById('paidCredits');
    const totalEl = document.getElementById('totalCredits');
    
    if (freeEl) freeEl.textContent = data.free_uses;
    if (paidEl) paidEl.textContent = data.credit_balance;
    if (totalEl) totalEl.textContent = data.total;
}
```

---

## ⚠️ 주의사항

### 1. 하위 호환성
- 기존 `localStorage` 키(`userCredits`, `analysisCount`)는 유지
- 새로운 키(`freeCredits`, `paidCredits`) 추가

### 2. 에러 처리
```javascript
const data = await GnuboardAPI.getCredits();
if (!data.success) {
    // 기본값 사용
    state.freeCredit = 0;
    state.paidCredit = 0;
    console.error('크레딧 조회 실패:', data.error);
}
```

### 3. 서버 동기화
- 프론트엔드의 크레딧 값은 참고용
- 실제 크레딧은 서버가 관리
- 중요한 작업 전에는 항상 서버에서 최신 값 조회

---

## 🧪 테스트 체크리스트

- [ ] 크레딧 조회 API 정상 동작
- [ ] 크레딧 사용 API 정상 동작
- [ ] UI에 크레딧 정상 표시
- [ ] 무료 크레딧 사용 시 UI 업데이트
- [ ] 유료 크레딧 사용 시 UI 업데이트
- [ ] 크레딧 부족 시 에러 메시지 표시
- [ ] 결제 완료 후 크레딧 갱신
- [ ] 페이지 새로고침 시 크레딧 유지
- [ ] localStorage 동기화 확인

---

## 📞 문제 해결

### 문제 1: 크레딧이 표시되지 않음

**원인**: API 응답 형식 불일치

**해결**:
```javascript
// 디버깅
const data = await GnuboardAPI.getCredits();
console.log('API 응답:', data);
console.log('free_uses:', data.free_uses);
console.log('credit_balance:', data.credit_balance);
```

### 문제 2: 크레딧 사용 후 UI가 업데이트되지 않음

**원인**: 상태 업데이트 누락

**해결**:
```javascript
const data = await GnuboardAPI.useCredit();
if (data.success) {
    // 상태 업데이트 필수
    state.freeCredit = data.free_uses;
    state.paidCredit = data.credit_balance;
    updateCreditDisplay(); // UI 업데이트 함수 호출
}
```

### 문제 3: localStorage 값이 최신이 아님

**원인**: 서버 동기화 누락

**해결**:
```javascript
// 페이지 로드 시 서버에서 최신 값 조회
await GnuboardAPI.getCredits();
```

---

**작성일**: 2025-12-15  
**버전**: 1.0

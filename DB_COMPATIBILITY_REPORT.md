# 데이터베이스 호환성 체크 리포트

## 📊 개요

프로젝트 코드와 데이터베이스 테이블 간의 호환성을 확인하고 수정한 내용을 정리합니다.

## 🔍 체크 항목

### 1. g5_lotto_store 테이블

#### 코드에서 사용하는 컬럼
- ✅ `store_id` (필수)
- ✅ `store_name` (필수)
- ✅ `address` (필수)
- ✅ `region1` (필수)
- ✅ `region2` (필수)
- ⚠️ `region3` (선택 - SEO 최적화용, 코드에서 사용하지만 테이블에 없었음)
- ⚠️ `phone` (선택)
- ⚠️ `latitude` (선택 - 카카오 API)
- ⚠️ `longitude` (선택 - 카카오 API)
- ✅ `wins_1st` (필수)
- ✅ `wins_2nd` (필수)
- ✅ `created_at` (필수)
- ✅ `updated_at` (필수)

#### 수정 사항
1. **region3 컬럼 추가**: SEO 3단계 계층 구조 지원
2. **idx_region3 인덱스 추가**: region3 검색 성능 향상
3. **자동 마이그레이션**: `cron/lotto_store_sync.php` 실행 시 자동 추가

### 2. g5_lotto_store_win 테이블

#### 코드에서 사용하는 컬럼
- ✅ `win_id` (필수)
- ✅ `draw_no` (필수)
- ✅ `store_id` (필수)
- ✅ `rank` (필수)
- ✅ `win_type` (필수)
- ✅ `prize_amount` (필수)
- ✅ `created_at` (필수)

#### 참고
- `draw_date`는 `g5_lotto_draw` 테이블에서 JOIN으로 가져옴 (정상)

### 3. g5_lotto_draw 테이블

#### 코드에서 사용하는 컬럼
- ✅ `draw_no` (필수)
- ✅ `draw_date` (필수)
- ✅ `n1`, `n2`, `n3`, `n4`, `n5`, `n6` (필수)
- ✅ `bonus` (필수)
- ⚠️ `total_sales` (선택 - 동기화 시 자동 추가)
- ⚠️ `first_winners`, `first_prize_each` (선택)
- ⚠️ `second_winners`, `second_prize_each` (선택)
- ⚠️ `third_winners`, `third_prize_each` (선택)
- ✅ `created_at`, `updated_at` (필수)

#### 참고
- 당첨금 관련 컬럼은 `cron/lotto_store_sync.php`에서 자동 추가됨

## ✅ 수정 완료 사항

### 1. 테이블 생성 스크립트 업데이트
**파일**: `cron/lotto_store_sync.php`

- `region3` 컬럼 추가
- `idx_region3` 인덱스 추가
- 기존 테이블 자동 마이그레이션 로직 추가

### 2. 코드 호환성 개선
**파일**: `lib/lotto_store.lib.php`

- `li_save_store()`: region3 컬럼 존재 여부 확인 후 저장
- `li_extract_region_from_address()`: region3 추출 로직 추가
- 하위 호환성 유지 (region3 컬럼 없어도 동작)

### 3. 호환성 체크 스크립트 생성
**파일**: `db_compatibility_check.php`

- 테이블 존재 여부 확인
- 필수/선택 컬럼 체크
- 인덱스 체크
- 권장 수정 사항 제시

## 🔧 실행 방법

### 1. 호환성 체크
```bash
php db_compatibility_check.php
```

### 2. 테이블 자동 생성/업데이트
```bash
# 테이블 생성 및 region3 컬럼 자동 추가
php cron/lotto_store_sync.php
```

### 3. 수동 마이그레이션 (필요시)
```sql
-- region3 컬럼 추가
ALTER TABLE g5_lotto_store 
ADD COLUMN `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동' AFTER `region2`;

-- region3 인덱스 추가
ALTER TABLE g5_lotto_store 
ADD INDEX `idx_region3` (`region3`);
```

## 📋 체크리스트

### g5_lotto_store
- [x] 필수 컬럼 모두 존재
- [x] region3 컬럼 추가 (자동 마이그레이션)
- [x] region3 인덱스 추가
- [x] 코드에서 안전하게 사용 가능 (하위 호환성)

### g5_lotto_store_win
- [x] 필수 컬럼 모두 존재
- [x] 인덱스 정상

### g5_lotto_draw
- [x] 필수 컬럼 모두 존재
- [x] 선택 컬럼 자동 추가 로직 있음

## 🎯 결론

모든 코드와 데이터베이스 테이블이 호환되도록 수정 완료:

1. ✅ **region3 컬럼**: 자동 마이그레이션으로 기존 테이블에도 추가됨
2. ✅ **하위 호환성**: region3 컬럼이 없어도 코드가 정상 동작
3. ✅ **자동화**: 테이블 생성 스크립트 실행 시 자동으로 최신 구조로 업데이트
4. ✅ **체크 도구**: `db_compatibility_check.php`로 언제든지 확인 가능

## 📝 참고

- 기존 데이터베이스에 region3 컬럼이 없는 경우, `cron/lotto_store_sync.php` 실행 시 자동 추가됨
- 새로 수집되는 판매점 데이터는 자동으로 region3 정보가 저장됨
- 기존 판매점의 region3는 `cron/update_region3.php`로 업데이트 가능 (파일 삭제됨, 필요시 재생성)

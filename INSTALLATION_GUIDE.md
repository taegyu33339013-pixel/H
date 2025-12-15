# 🚀 프로젝트 설치 가이드 (완전판)

## 📋 목차
1. [시스템 요구사항](#시스템-요구사항)
2. [데이터베이스 설정](#데이터베이스-설정)
3. [파일 권한 설정](#파일-권한-설정)
4. [API 키 설정](#api-키-설정)
5. [데이터베이스 테이블 생성](#데이터베이스-테이블-생성)
6. [초기 데이터 수집](#초기-데이터-수집)
7. [크론 작업 설정](#크론-작업-설정)
8. [환경 설정 확인](#환경-설정-확인)
9. [문제 해결](#문제-해결)

---

## 1. 시스템 요구사항

### PHP 요구사항
- **PHP 버전**: 7.4 이상 (권장: 8.0+)
- **필수 확장 모듈**:
  - `mysqli` 또는 `mysql` (데이터베이스 연결)
  - `curl` (API 호출)
  - `json` (JSON 처리)
  - `mbstring` (다국어 지원)
  - `xml` (HTML 파싱)
  - `dom` (DOMDocument 사용)
  - `libxml` (XML 파싱)

### 데이터베이스 요구사항
- **MySQL/MariaDB**: 5.5 이상 (utf8mb4 지원)
- **권장**: MySQL 5.7+ 또는 MariaDB 10.2+

### 서버 요구사항
- **웹 서버**: Apache 2.4+ 또는 Nginx
- **메모리**: 최소 512MB (권장: 1GB+)
- **디스크**: 최소 500MB (데이터 수집 시 더 필요)

---

## 2. 데이터베이스 설정

### 2.1 데이터베이스 생성

```sql
CREATE DATABASE `lottoinsight` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2.2 데이터베이스 연결 설정

**파일**: `data/dbconfig.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
define('G5_MYSQL_HOST', 'localhost');        // 데이터베이스 호스트
define('G5_MYSQL_USER', 'your_db_user');     // 데이터베이스 사용자명
define('G5_MYSQL_PASSWORD', 'your_password'); // 데이터베이스 비밀번호
define('G5_MYSQL_DB', 'lottoinsight');        // 데이터베이스명
define('G5_MYSQL_SET_MODE', true);
define('G5_TABLE_PREFIX', 'g5_');
```

**⚠️ 보안 주의**: `data/dbconfig.php`는 `.gitignore`에 포함되어 있어 Git에 커밋되지 않습니다.

---

## 3. 파일 권한 설정

### 3.1 필수 디렉토리 권한

```bash
# 데이터 디렉토리 (쓰기 권한 필요)
chmod 755 data/
chmod 755 data/log/
chmod 755 data/file/

# 업로드 디렉토리
chmod 755 data/editor/
chmod 755 data/file/

# 세션 디렉토리
chmod 755 data/session/
```

### 3.2 크론 스크립트 실행 권한

```bash
chmod +x cron/*.php
```

---

## 4. API 키 설정

### 4.1 카카오 API 키 (선택사항 - 위도/경도 수집용)

**파일**: `data/kakao_config.php`

```bash
# 예시 파일 복사
cp data/kakao_config.php.example data/kakao_config.php

# 파일 편집하여 API 키 입력
```

```php
<?php
// data/kakao_config.php
$kakao_api_key = 'YOUR_KAKAO_REST_API_KEY_HERE';
```

**API 키 발급**: https://developers.kakao.com/

### 4.2 토스 페이먼츠 API 키 (결제용)

**파일**: `api/toss/_toss_config.php`

```php
<?php
define('TOSS_CLIENT_KEY', 'your_toss_client_key');
define('TOSS_SECRET_KEY', 'your_toss_secret_key');
```

**API 키 발급**: https://developers.tosspayments.com/

### 4.3 카카오 로그인 API 키

**파일**: `kakao_login.php` 또는 `config.php`

```php
define('KAKAO_REST_API_KEY', 'your_kakao_rest_api_key');
define('KAKAO_REDIRECT_URI', 'https://yourdomain.com/kakao_login.php');
```

---

## 5. 데이터베이스 테이블 생성

### 5.1 그누보드 기본 테이블 설치

1. 웹 브라우저에서 접속:
   ```
   https://yourdomain.com/install/
   ```

2. 설치 마법사 따라하기

### 5.2 로또 관련 테이블 생성

**방법 1: SQL 파일 실행 (권장)**

```bash
mysql -u your_user -p your_database < CREATE_TABLES.sql
```

**방법 2: PHP 스크립트 실행**

```bash
# 테이블 자동 생성 및 region3 컬럼 추가
php cron/lotto_store_sync.php
```

### 5.3 테이블 생성 확인

```bash
php db_compatibility_check.php
```

---

## 6. 초기 데이터 수집

### 6.1 로또 당첨번호 데이터 수집

```bash
# 1회차부터 최신 회차까지 수집
php cron/lotto_seed.php

# 또는 관리자 페이지에서
# https://yourdomain.com/adm/lotto_draw_sync.php
```

### 6.2 판매점 데이터 수집

```bash
# 전체 판매점 수집 (최초 1회, 약 30분~1시간 소요)
php cron/lotto_store_sync.php all

# 특정 회차의 당첨점만 수집
php cron/lotto_store_sync.php 1202

# 범위 수집
php cron/lotto_store_sync.php 1 1202
```

### 6.3 카카오 API로 추가 정보 수집 (선택사항)

```bash
# 위도/경도가 없는 판매점 100개 처리
php cron/kakao_store_enrich.php

# 500개 처리
php cron/kakao_store_enrich.php 500
```

**⚠️ 주의**: 카카오 API 할당량 (월 300,000건) 확인 필요

---

## 7. 크론 작업 설정

### 7.1 크론탭 설정

```bash
crontab -e
```

### 7.2 권장 크론 작업

```bash
# 매주 일요일 새벽 2시: 최신 회차 당첨번호 동기화
0 2 * * 0 /usr/bin/php /path/to/project/cron/lotto_weekly.php >> /var/log/lotto_weekly.log 2>&1

# 매주 일요일 새벽 3시: 최신 회차 당첨점 동기화
0 3 * * 0 /usr/bin/php /path/to/project/cron/lotto_store_sync.php >> /var/log/lotto_store.log 2>&1

# 매일 새벽 2시: 카카오 API로 위도/경도 수집 (100개씩)
0 2 * * * /usr/bin/php /path/to/project/cron/kakao_store_enrich.php 100 >> /var/log/kakao_enrich.log 2>&1
```

**경로 수정 필요**: `/path/to/project`를 실제 프로젝트 경로로 변경

---

## 8. 환경 설정 확인

### 8.1 PHP 확장 모듈 확인

```bash
php -m | grep -E "mysqli|curl|json|mbstring|xml|dom"
```

### 8.2 데이터베이스 연결 테스트

```bash
php -r "
include 'common.php';
echo 'DB 연결 성공!' . PHP_EOL;
"
```

### 8.3 전체 호환성 체크

```bash
php db_compatibility_check.php
```

---

## 9. 문제 해결

### 9.1 데이터베이스 연결 오류

**증상**: `MySQL Connect Error!!!`

**해결**:
1. `data/dbconfig.php` 파일 확인
2. 데이터베이스 사용자 권한 확인
3. 호스트 접근 권한 확인

### 9.2 테이블이 없다는 오류

**증상**: `Table 'xxx' doesn't exist`

**해결**:
```bash
# 테이블 생성 스크립트 실행
php cron/lotto_store_sync.php
```

### 9.3 API 호출 실패

**증상**: 카카오 API 오류

**해결**:
1. `data/kakao_config.php` 파일 확인
2. API 키 유효성 확인
3. 할당량 확인 (카카오 개발자 콘솔)

### 9.4 파일 권한 오류

**증상**: 파일 쓰기 실패

**해결**:
```bash
chmod -R 755 data/
chown -R www-data:www-data data/  # Apache 사용자
```

---

## 10. 빠른 시작 체크리스트

### 필수 단계

- [ ] 1. 데이터베이스 생성
- [ ] 2. `data/dbconfig.php` 설정
- [ ] 3. 그누보드 설치 (`/install/`)
- [ ] 4. 로또 테이블 생성 (`php cron/lotto_store_sync.php`)
- [ ] 5. 당첨번호 데이터 수집 (`php cron/lotto_seed.php`)
- [ ] 6. 판매점 데이터 수집 (`php cron/lotto_store_sync.php all`)

### 선택 단계

- [ ] 7. 카카오 API 키 설정 (`data/kakao_config.php`)
- [ ] 8. 카카오 API로 위도/경도 수집 (`php cron/kakao_store_enrich.php`)
- [ ] 9. 토스 페이먼츠 API 키 설정
- [ ] 10. 크론 작업 설정

---

## 11. 설치 후 확인

### 11.1 웹 페이지 접속 확인

```
https://yourdomain.com/          # 메인 페이지
https://yourdomain.com/stores/    # 판매점 목록
https://yourdomain.com/result.php # 분석 결과
```

### 11.2 데이터 확인

```sql
-- 당첨번호 데이터 확인
SELECT COUNT(*) FROM g5_lotto_draw;

-- 판매점 데이터 확인
SELECT COUNT(*) FROM g5_lotto_store;

-- 당첨점 데이터 확인
SELECT COUNT(*) FROM g5_lotto_store_win;
```

### 11.3 API 테스트

```bash
# 크레딧 조회 API
curl https://yourdomain.com/api/get_credits.php

# 분석 저장 API (로그인 필요)
curl -X POST https://yourdomain.com/api/save_analysis.php \
  -H "Content-Type: application/json" \
  -d '{"numbers":[1,2,3,4,5,6],"round":1202}'
```

---

## 12. 보안 체크리스트

- [ ] `data/dbconfig.php` 파일 권한: 600 (소유자만 읽기)
- [ ] `data/kakao_config.php` 파일 권한: 600
- [ ] `.htaccess` 파일로 API 디렉토리 보호
- [ ] HTTPS 사용 (프로덕션 환경)
- [ ] `G5_DEBUG` 설정: `false` (프로덕션)
- [ ] 관리자 페이지 접근 제한

---

## 13. 성능 최적화

### 13.1 데이터베이스 인덱스 확인

```bash
php db_compatibility_check.php
```

### 13.2 캐시 설정

- 그누보드 캐시 활성화
- OPcache 활성화 (PHP 7.0+)

### 13.3 크론 작업 최적화

- 대량 데이터 수집은 야간에 실행
- API 호출 간격 유지 (서버 부하 방지)

---

## 14. 백업

### 14.1 데이터베이스 백업

```bash
mysqldump -u user -p database_name > backup_$(date +%Y%m%d).sql
```

### 14.2 파일 백업

```bash
tar -czf backup_files_$(date +%Y%m%d).tar.gz \
  --exclude='data/log/*' \
  --exclude='data/session/*' \
  .
```

---

## 15. 업데이트

### 15.1 코드 업데이트

```bash
git pull origin main
```

### 15.2 데이터베이스 마이그레이션

```bash
# 호환성 체크
php db_compatibility_check.php

# 자동 마이그레이션 (region3 등)
php cron/lotto_store_sync.php
```

---

## 📞 지원

문제가 발생하면 다음을 확인하세요:

1. 로그 파일: `data/log/`
2. PHP 에러 로그
3. 데이터베이스 에러 로그
4. `db_compatibility_check.php` 실행 결과

---

## ✅ 설치 완료 확인

모든 단계를 완료했다면 다음 명령어로 확인:

```bash
php install/check_installation.php
```

이 스크립트는 모든 필수 설정을 확인하고 문제가 있으면 알려줍니다.

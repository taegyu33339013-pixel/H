# ✅ 설치 체크리스트

## 📋 필수 설정 (반드시 완료)

### 데이터베이스
- [ ] 데이터베이스 생성 (`lottoinsight`)
- [ ] `data/dbconfig.php` 파일 설정
  - [ ] G5_MYSQL_HOST
  - [ ] G5_MYSQL_USER
  - [ ] G5_MYSQL_PASSWORD
  - [ ] G5_MYSQL_DB

### 그누보드 설치
- [ ] 웹 브라우저에서 `/install/` 접속
- [ ] 설치 마법사 완료
- [ ] 관리자 계정 생성

### 로또 테이블 생성
- [ ] `php cron/lotto_store_sync.php` 실행
- [ ] `g5_lotto_store` 테이블 생성 확인
- [ ] `g5_lotto_store_win` 테이블 생성 확인
- [ ] `region3` 컬럼 자동 추가 확인

### 초기 데이터 수집
- [ ] 당첨번호 데이터 수집 (`php cron/lotto_seed.php`)
- [ ] 판매점 데이터 수집 (`php cron/lotto_store_sync.php all`)

### 파일 권한
- [ ] `data/` 디렉토리 쓰기 권한 (755)
- [ ] `data/log/` 디렉토리 쓰기 권한
- [ ] `data/file/` 디렉토리 쓰기 권한

---

## 🔧 선택 설정 (기능 확장용)

### 카카오 API (위도/경도 수집)
- [ ] 카카오 개발자 콘솔에서 API 키 발급
- [ ] `data/kakao_config.php` 파일 생성
- [ ] API 키 입력
- [ ] 테스트: `php cron/kakao_store_enrich.php 10`

### 토스 페이먼츠 (결제)
- [ ] 토스 개발자 콘솔에서 API 키 발급
- [ ] `api/toss/_toss_config.php` 파일 설정
- [ ] TOSS_CLIENT_KEY 설정
- [ ] TOSS_SECRET_KEY 설정

### 카카오 로그인
- [ ] 카카오 개발자 콘솔에서 앱 등록
- [ ] 리다이렉트 URI 설정
- [ ] `kakao_login.php`에서 API 키 설정

### 크론 작업
- [ ] `crontab -e` 실행
- [ ] 주간 당첨번호 동기화 추가
- [ ] 주간 판매점 동기화 추가
- [ ] (선택) 일일 카카오 API 데이터 수집 추가

---

## 🧪 설치 확인

### 자동 체크
```bash
php install/check_installation.php
```

### 수동 확인
- [ ] 웹 페이지 접속: `https://yourdomain.com/`
- [ ] 판매점 목록: `https://yourdomain.com/stores/`
- [ ] 데이터베이스 연결 테스트
- [ ] API 엔드포인트 테스트

---

## 📊 데이터 확인

```sql
-- 당첨번호 데이터
SELECT COUNT(*) AS draw_count, MAX(draw_no) AS latest_round FROM g5_lotto_draw;

-- 판매점 데이터
SELECT COUNT(*) AS store_count FROM g5_lotto_store;

-- 당첨점 데이터
SELECT COUNT(*) AS win_count FROM g5_lotto_store_win;

-- region3 컬럼 확인
SELECT COUNT(*) AS has_region3 FROM g5_lotto_store WHERE region3 IS NOT NULL;
```

---

## 🚨 문제 해결

### 데이터베이스 연결 실패
→ `data/dbconfig.php` 확인

### 테이블이 없다는 오류
→ `php cron/lotto_store_sync.php` 실행

### API 호출 실패
→ API 키 설정 확인

### 파일 쓰기 권한 오류
→ `chmod 755 data/` 실행

---

## 📚 참고 문서

- **전체 설치 가이드**: `INSTALLATION_GUIDE.md`
- **빠른 시작**: `QUICK_START.md`
- **데이터베이스 호환성**: `DB_COMPATIBILITY_REPORT.md`
- **카카오 API 설정**: `KAKAO_API_SETUP.md`
- **데이터 수집 가이드**: `DATA_COLLECTION_GUIDE.md`

# ⚡ 빠른 시작 가이드

## 🎯 5분 안에 시작하기

### 1단계: 데이터베이스 설정 (1분)

```bash
# 데이터베이스 생성
mysql -u root -p
CREATE DATABASE lottoinsight DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

**파일 수정**: `data/dbconfig.php`
```php
define('G5_MYSQL_HOST', 'localhost');
define('G5_MYSQL_USER', 'your_user');
define('G5_MYSQL_PASSWORD', 'your_password');
define('G5_MYSQL_DB', 'lottoinsight');
```

### 2단계: 그누보드 설치 (2분)

웹 브라우저에서 접속:
```
https://yourdomain.com/install/
```

설치 마법사 따라하기

### 3단계: 로또 테이블 생성 (30초)

```bash
php cron/lotto_store_sync.php
```

### 4단계: 데이터 수집 (1분 - 백그라운드)

```bash
# 당첨번호 데이터 수집 (백그라운드)
php cron/lotto_seed.php > /dev/null 2>&1 &

# 판매점 데이터 수집 (백그라운드, 시간 오래 걸림)
php cron/lotto_store_sync.php all > /dev/null 2>&1 &
```

### 5단계: 확인 (30초)

```bash
php install/check_installation.php
```

---

## ✅ 완료!

이제 웹 브라우저에서 접속:
- 메인 페이지: `https://yourdomain.com/`
- 판매점 목록: `https://yourdomain.com/stores/`

---

## 📋 상세 가이드

전체 설치 가이드는 `INSTALLATION_GUIDE.md`를 참고하세요.

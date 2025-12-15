# 🚀 프로젝트 설치 및 사용 가이드

## 📖 시작하기

이 프로젝트는 **로또 분석 서비스**로, 동행복권 데이터를 기반으로 AI 분석과 판매점 정보를 제공합니다.

---

## ⚡ 빠른 시작 (5분)

### 1. 데이터베이스 설정
```bash
# data/dbconfig.php 파일 수정
# G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD, G5_MYSQL_DB 설정
```

### 2. 그누보드 설치
웹 브라우저에서 접속:
```
https://yourdomain.com/install/
```

### 3. 로또 테이블 생성
```bash
php cron/lotto_store_sync.php
```

### 4. 데이터 수집
```bash
php cron/lotto_seed.php              # 당첨번호
php cron/lotto_store_sync.php all     # 판매점
```

### 5. 확인
```bash
php install/check_installation.php
```

**완료!** 이제 웹 브라우저에서 접속하세요.

---

## 📚 상세 가이드

### 설치 가이드
- **전체 설치 가이드**: [`INSTALLATION_GUIDE.md`](INSTALLATION_GUIDE.md) - 모든 설정 상세 설명
- **빠른 시작**: [`QUICK_START.md`](QUICK_START.md) - 5분 안에 시작하기
- **체크리스트**: [`SETUP_CHECKLIST.md`](SETUP_CHECKLIST.md) - 단계별 체크리스트

### 기능 문서
- **SEO 최적화**: [`SEO_SUPER_OPTIMIZATION.md`](SEO_SUPER_OPTIMIZATION.md)
- **데이터 수집**: [`DATA_COLLECTION_GUIDE.md`](DATA_COLLECTION_GUIDE.md)
- **카카오 API**: [`KAKAO_API_SETUP.md`](KAKAO_API_SETUP.md)
- **DB 호환성**: [`DB_COMPATIBILITY_REPORT.md`](DB_COMPATIBILITY_REPORT.md)

### 프로젝트 상태
- **현재 상태**: [`PROJECT_STATUS.md`](PROJECT_STATUS.md)

---

## 🛠️ 주요 기능

### 1. 로또 분석
- AI 기반 번호 분석 (10가지 알고리즘)
- 23년간 당첨번호 패턴 분석
- Hot/Cold 번호 분석
- 홀짝/고저 밸런스 분석

### 2. 판매점 정보
- 전국 판매점 검색
- 지역별 필터링 (시/도, 시/군/구, 읍/면/동)
- 명당 판매점 랭킹
- 당첨 이력 조회

### 3. SEO 최적화
- Schema.org 구조화 데이터
- 지역 기반 SEO (region3 활용)
- 성능 최적화
- 모바일 최적화

---

## 📋 시스템 요구사항

- **PHP**: 7.4 이상 (권장: 8.0+)
- **데이터베이스**: MySQL 5.7+ 또는 MariaDB 10.2+
- **필수 확장 모듈**: mysqli, curl, json, mbstring, xml, dom

---

## 🔧 설치 확인

### 자동 확인
```bash
php install/check_installation.php
```

이 스크립트는 다음을 확인합니다:
- ✅ PHP 버전 및 확장 모듈
- ✅ 데이터베이스 연결
- ✅ 필수 테이블 존재 여부
- ✅ 데이터 존재 여부
- ✅ API 키 설정
- ✅ 파일 권한

---

## 📁 주요 디렉토리

```
/
├── stores/              # 판매점 페이지
├── lib/                 # 라이브러리
│   ├── lotto_store.lib.php
│   └── kakao_api.lib.php
├── cron/                # 크론 작업
│   ├── lotto_store_sync.php
│   └── kakao_store_enrich.php
├── api/                 # API 엔드포인트
├── install/             # 설치 스크립트
│   └── check_installation.php
└── data/                # 설정 파일
    └── dbconfig.php
```

---

## 🚨 문제 해결

### 데이터베이스 연결 실패
→ `data/dbconfig.php` 확인

### 테이블이 없다는 오류
→ `php cron/lotto_store_sync.php` 실행

### API 호출 실패
→ API 키 설정 확인 (`data/kakao_config.php`)

### 파일 쓰기 권한 오류
→ `chmod 755 data/` 실행

---

## 📞 지원

문제가 발생하면:
1. `php install/check_installation.php` 실행
2. 로그 확인: `data/log/`
3. 문서 참고: `INSTALLATION_GUIDE.md`

---

## ✅ 다음 단계

1. **설치 확인**: `php install/check_installation.php`
2. **데이터 수집**: `php cron/lotto_seed.php`
3. **웹 접속**: `https://yourdomain.com/`

**시작하기**: [`QUICK_START.md`](QUICK_START.md) 참고

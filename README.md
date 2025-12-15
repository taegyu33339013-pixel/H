# 오늘로또 - AI 기반 로또 번호 분석 서비스

동행복권 공식 데이터 기반 AI 로또 번호 분석 서비스입니다.

## 주요 기능

- 🤖 AI 기반 로또 번호 분석 (10가지 알고리즘)
- 📊 23년간 당첨번호 패턴 분석
- 🏆 로또 명당 판매점 정보
- 📈 실시간 통계 대시보드
- 🎯 Hot/Cold 번호 분석
- ⚖️ 홀짝/고저 밸런스 분석

## 기술 스택

- **Backend**: PHP 7.4+ (GNUBOARD 5.6.13)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **API**: 동행복권 API, Toss Payments, Kakao Login

## 최근 개선 사항

### 🚀 성능 개선
- Kakao SDK defer 로딩
- Google Fonts weight 최적화
- 애니메이션 성능 최적화 (will-change, IntersectionObserver)
- 카운트다운 숫자 점핑 방지

### 🎨 디자인 개선
- 색상 대비 개선 (WCAG AA 기준 충족)
- Archive 테이블 모바일 카드형 전환
- FAQ 동적 높이 계산
- 모바일 공 크기 최소값 보장

### 🇰🇷 한국어 가독성 최적화
- Pretendard 폰트 우선 로딩
- 줄간격 1.75~1.85로 증가
- word-break: keep-all 적용
- 최대 줄 길이 제한 (580px)
- 최소 폰트 크기 보장 (0.85rem)

### 🔍 SEO 개선
- 구조화된 데이터 추가 (BreadcrumbList, Organization, HowTo, SoftwareApplication)
- Canonical URL 동적 처리
- 시맨틱 HTML 개선
- 이미지/SVG 접근성 개선

## 설치 방법

### 빠른 시작 (5분)
```bash
# 1. 데이터베이스 설정
# data/dbconfig.php 파일 수정

# 2. 그누보드 설치
# 웹 브라우저에서 /install/ 접속

# 3. 로또 테이블 생성
php cron/lotto_store_sync.php

# 4. 데이터 수집
php cron/lotto_seed.php              # 당첨번호
php cron/lotto_store_sync.php all     # 판매점

# 5. 설치 확인
php install/check_installation.php
```

### 상세 가이드
- **전체 설치 가이드**: [`INSTALLATION_GUIDE.md`](INSTALLATION_GUIDE.md)
- **빠른 시작**: [`QUICK_START.md`](QUICK_START.md)
- **체크리스트**: [`SETUP_CHECKLIST.md`](SETUP_CHECKLIST.md)

## 주요 파일 구조

```
/
├── index.php              # 메인 랜딩 페이지
├── result.php             # AI 분석 결과 페이지
├── auth.php               # 인증 페이지
├── stores/                # 판매점 페이지
│   └── index.php
├── lib/                   # 라이브러리
│   ├── lotto_draw.lib.php
│   └── lotto_credit.lib.php
├── api/                   # API 엔드포인트
│   ├── get_credits.php
│   └── use_credit.php
├── payments/              # 결제 처리
│   └── toss/
└── scripts/               # JavaScript 파일
    ├── gnuboard-api.js
    └── lotto-data.js
```

## 환경 요구사항

- PHP 7.4 이상
- MySQL 5.7 이상 / MariaDB 10.2 이상
- Apache/Nginx 웹서버
- SSL 인증서 (HTTPS 권장)

## 라이선스

프로젝트 라이선스 정보

## 문의

프로젝트 관련 문의사항이 있으시면 이슈를 등록해주세요.

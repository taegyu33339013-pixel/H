# 로또인사이트 - 그누보드 설치 가이드

## 📁 파일 구조

```
lottoinsight/
├── _common.php          ← 그누보드 공통 파일 (기존)
├── index.php            ← 메인 페이지
├── result.php           ← 분석 결과 페이지
├── auth.php             ← 로그인 페이지
├── kakao_login.php      ← 카카오 로그인 처리
├── kakao_logout.php     ← 로그아웃 처리
├── api/
│   ├── get_credits.php  ← 크레딧 조회 API
│   ├── use_credit.php   ← 크레딧 사용 API
│   ├── save_analysis.php← 분석 저장 API
│   └── get_history.php  ← 히스토리 조회 API
├── scripts/
│   ├── lotto-data.js    ← 로또 데이터
│   └── lotto-generator.js← 번호 생성 엔진
├── styles/
│   └── shared.css       ← 공통 스타일
└── (기타 정적 파일들)
```

---

## 🔧 설치 방법

### 1. 그누보드 설치
그누보드 5.x가 설치된 서버에 이 폴더를 업로드합니다.

### 2. 카카오 개발자 설정

1. [카카오 개발자 콘솔](https://developers.kakao.com) 접속
2. 애플리케이션 생성
3. **플랫폼 > Web** 사이트 도메인 등록
4. **제품 설정 > 카카오 로그인** 활성화
5. **동의항목** 설정:
   - 닉네임 (필수)
   - 프로필 사진 (선택)
   - 카카오계정(이메일) (선택)
6. Redirect URI 등록: `https://your-domain.com/kakao_login.php`

### 3. 설정 파일 수정

`kakao_login.php` 파일에서 API 키 설정:

```php
define('KAKAO_REST_API_KEY', 'YOUR_KAKAO_REST_API_KEY');
define('KAKAO_REDIRECT_URI', G5_URL.'/kakao_login.php');
```

또는 별도의 `config.local.php` 파일 생성:

```php
<?php
// config.local.php
define('KAKAO_REST_API_KEY', 'your-rest-api-key');
define('KAKAO_JS_KEY', 'your-javascript-key');
```

### 4. HTML 파일 내용 병합

각 PHP 파일의 `<body>` 태그 안에 해당 HTML 파일의 body 내용을 복사:

| PHP 파일 | HTML 파일 |
|---------|----------|
| index.php | index.html의 `<body>` 내용 |
| result.php | result.html의 `<body>` 내용 |
| auth.php | auth.html의 `<body>` 내용 |

---

## 🗄️ 데이터베이스

### 자동 생성 테이블

`api/save_analysis.php`가 최초 실행 시 테이블을 자동 생성합니다:

```sql
CREATE TABLE IF NOT EXISTS `g5_lotto_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mb_id` varchar(100) NOT NULL,
  `lotto_round` int(11) DEFAULT 0,
  `numbers` varchar(50) NOT NULL,
  `score` int(11) DEFAULT 0,
  `strategy` varchar(100) DEFAULT '',
  `is_winner` tinyint(1) DEFAULT 0,
  `match_count` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mb_id` (`mb_id`),
  KEY `lotto_round` (`lotto_round`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 💰 크레딧 시스템

그누보드의 **포인트 시스템**을 활용합니다:

| 액션 | 포인트 |
|------|--------|
| 카카오 신규 가입 | +100 (무료 1회) |
| 분석 1회 사용 | -100 |

관리자 페이지에서 포인트 설정 가능.

---

## 📱 API 엔드포인트

### GET /api/get_credits.php
```json
{
  "success": true,
  "user_id": "kakao_123456",
  "user_name": "홍길동",
  "credits": 300,
  "analysis_count": 3
}
```

### POST /api/use_credit.php
```json
{
  "success": true,
  "used_credits": 100,
  "remaining_credits": 200
}
```

### POST /api/save_analysis.php
```json
// Request
{
  "numbers": [7, 13, 24, 31, 38, 42],
  "round": 1201,
  "score": 87,
  "strategy": "balanced"
}

// Response
{
  "success": true,
  "id": 123
}
```

### GET /api/get_history.php?page=1&limit=20
```json
{
  "success": true,
  "history": [...],
  "total": 45,
  "page": 1,
  "total_pages": 3
}
```

---

## 🔐 보안 체크리스트

- [ ] KAKAO_REST_API_KEY를 환경 변수 또는 config.local.php로 분리
- [ ] API 폴더에 직접 접근 제한 (.htaccess)
- [ ] HTTPS 사용
- [ ] CSRF 토큰 적용 (그누보드 기본 제공)

---

## 📞 문의

문제 발생 시 그누보드 포럼 또는 카카오 개발자 문서를 참고하세요.


# 로또 판매점 이미지 가져오기 가이드

## 📋 개요
데이터베이스에 저장된 로또 판매점 주소 정보를 기반으로 Google Street View API를 사용하여 이미지를 자동으로 가져오는 스크립트입니다.

## 🔑 사전 준비

### 1. Google API 키 발급
1. [Google Cloud Console](https://console.cloud.google.com/) 접속
2. 새 프로젝트 생성 또는 기존 프로젝트 선택
3. **APIs & Services > Library**에서 다음 API 활성화:
   - **Street View Static API**
4. **APIs & Services > Credentials**에서 API 키 생성
5. 생성된 API 키를 `fetch_store_images_config.php`에 입력

### 2. 데이터베이스 확인
- 테이블명: `g5_lotto_store`
- 필수 컬럼: `store_id`, `address`, `latitude`, `longitude`
- 자동 추가 컬럼: `store_image` (이미지 경로 저장)

## 📁 파일 구조

```
/
├── fetch_store_images.php          # 기본 버전
├── fetch_store_images_advanced.php  # 고급 버전 (권장)
├── fetch_store_images_config.php    # 설정 파일
├── images/
│   └── stores/                     # 이미지 저장 폴더
└── logs/                           # 로그 파일
```

## 🚀 사용 방법

### 방법 1: 웹 브라우저에서 실행
```
http://your-domain.com/fetch_store_images_advanced.php?limit=50&start=0
```

### 방법 2: 터미널에서 실행
```bash
php fetch_store_images_advanced.php
```

### 파라미터
- `limit`: 한 번에 처리할 개수 (기본값: 50)
- `start`: 시작 위치 (기본값: 0)
- `force`: 이미 있는 이미지도 다시 가져오기 (1로 설정)

### 예시
```bash
# 처음 50개 처리
php fetch_store_images_advanced.php

# 다음 50개 처리
php fetch_store_images_advanced.php limit=50 start=50

# 강제 업데이트
php fetch_store_images_advanced.php force=1
```

## ⚙️ 설정 변경

`fetch_store_images_config.php` 파일을 수정하여 설정을 변경할 수 있습니다:

```php
'google' => [
    'api_key' => 'YOUR_API_KEY',
    'street_view' => [
        'size' => '800x600',  // 이미지 크기
        'fov' => 90,          // 시야각
        'heading' => 0,       // 방향 (0-360)
        'pitch' => 0,         // 각도
    ]
],
```

## 📊 처리 과정

1. 데이터베이스에서 주소 정보 조회
2. Google Street View API로 이미지 요청
3. 이미지를 `/images/stores/` 폴더에 저장
4. 데이터베이스에 이미지 경로 업데이트
5. 로그 파일에 결과 기록

## ⚠️ 주의사항

1. **API 할당량**: Google Street View API는 무료 할당량이 제한적입니다
   - 월 28,000회 요청 (무료)
   - 초당 10회 제한

2. **처리 시간**: 대량 데이터는 배치로 나누어 처리하세요
   - 1000개 처리 시 약 16분 소요 (1초 간격)

3. **에러 처리**: 
   - 주소가 정확하지 않으면 이미지를 가져올 수 없습니다
   - 좌표(latitude, longitude)가 있으면 더 정확합니다

## 🔍 문제 해결

### 이미지가 가져와지지 않는 경우
1. API 키가 올바른지 확인
2. Street View Static API가 활성화되었는지 확인
3. 주소가 정확한지 확인
4. 로그 파일 확인: `/logs/image_fetch_YYYY-MM-DD.log`

### 데이터베이스 연결 오류
- 호스트, 포트, 사용자명, 비밀번호 확인
- 데이터베이스 이름 확인

## 📈 진행 상황 확인

로그 파일을 확인하여 진행 상황을 추적할 수 있습니다:
```bash
tail -f logs/image_fetch_2024-12-16.log
```

## 💡 팁

1. **대량 처리**: 한 번에 너무 많이 처리하지 말고 배치로 나누세요
2. **재시도**: 네트워크 오류 시 자동으로 재시도합니다
3. **중단 후 재개**: `start` 파라미터로 중단한 지점부터 재개 가능


# 🚀 GitHub 푸시 가이드

## ✅ 완료된 작업

- Git 저장소 초기화 완료
- 원격 저장소 추가 완료: `https://github.com/taegyu33339013-pixel/H.git`
- 초기 커밋 완료 (3,350개 파일)
- README.md 추가 완료

## 📋 푸시 방법 (3가지)

### 방법 1: Personal Access Token 사용 (가장 간단) ⭐

1. **토큰 생성**
   - https://github.com/settings/tokens 접속
   - "Generate new token" → "Generate new token (classic)" 클릭
   - Note: `lotto-project` 입력
   - Expiration: `90 days` (또는 원하는 기간)
   - Select scopes: **`repo`** 체크 (전체 repo 권한)
   - "Generate token" 클릭
   - ⚠️ **토큰을 즉시 복사하세요!** (한 번만 표시됨)

2. **터미널에서 푸시**
   ```bash
   cd "/Users/h/Downloads/로또X_20251215"
   git push -u origin main
   ```
   
   - Username: `taegyu33339013-pixel` 입력
   - Password: **생성한 토큰** 입력 (비밀번호가 아님!)

### 방법 2: GitHub Desktop 사용

1. GitHub Desktop 설치: https://desktop.github.com/
2. File → Add Local Repository
3. 프로젝트 폴더 선택: `/Users/h/Downloads/로또X_20251215`
4. Publish repository 클릭
5. Repository name: `H` (또는 원하는 이름)
6. Publish 클릭

### 방법 3: SSH 키 설정 (고급)

1. **SSH 키 생성**
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   # 엔터 3번 (기본값 사용)
   ```

2. **공개키 복사**
   ```bash
   cat ~/.ssh/id_ed25519.pub
   ```

3. **GitHub에 SSH 키 추가**
   - https://github.com/settings/keys 접속
   - "New SSH key" 클릭
   - Title: `MacBook Pro`
   - Key: 위에서 복사한 공개키 붙여넣기
   - "Add SSH key" 클릭

4. **원격 저장소 URL 변경**
   ```bash
   cd "/Users/h/Downloads/로또X_20251215"
   git remote set-url origin git@github.com:taegyu33339013-pixel/H.git
   git push -u origin main
   ```

## 📊 현재 상태

```
저장소: https://github.com/taegyu33339013-pixel/H.git
브랜치: main
커밋: 2개
  - Initial commit: 오늘로또 프로젝트 - 한국어 가독성 최적화 및 성능 개선 완료
  - docs: README.md 추가
```

## 💡 추천 방법

**방법 1 (Personal Access Token)**을 추천합니다. 가장 빠르고 간단합니다.

토큰 생성 후 아래 명령어만 실행하시면 됩니다:

```bash
cd "/Users/h/Downloads/로또X_20251215"
git push -u origin main
```

## 🔗 저장소 링크

푸시 완료 후 확인:
https://github.com/taegyu33339013-pixel/H

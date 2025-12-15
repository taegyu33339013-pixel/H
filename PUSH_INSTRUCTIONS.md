# GitHub 푸시 방법

## 현재 상태

- ✅ Git 저장소 초기화 완료
- ✅ 원격 저장소 추가 완료: `https://github.com/taegyu33339013-pixel/H.git`
- ⏳ 푸시 대기 중 (인증 필요)

## 푸시 방법

### 방법 1: Personal Access Token 사용 (HTTPS)

1. **토큰 생성**
   - GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
   - "Generate new token (classic)" 클릭
   - Note: `lotto-project` 입력
   - Expiration: 원하는 기간 선택
   - Select scopes: **repo** 체크
   - "Generate token" 클릭
   - 생성된 토큰 복사 (한 번만 표시됨!)

2. **푸시 실행**
   ```bash
   cd "/Users/h/Downloads/로또X_20251215"
   git push -u origin main
   ```
   - Username: `taegyu33339013-pixel` 입력
   - Password: 생성한 토큰 입력

### 방법 2: SSH 키 사용

1. **SSH 키 확인**
   ```bash
   ls -la ~/.ssh/id_ed25519.pub
   # 또는
   ls -la ~/.ssh/id_rsa.pub
   ```

2. **SSH 키가 없는 경우 생성**
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   # 엔터 3번 (기본값 사용)
   ```

3. **공개키 복사**
   ```bash
   cat ~/.ssh/id_ed25519.pub
   ```

4. **GitHub에 SSH 키 추가**
   - GitHub → Settings → SSH and GPG keys → New SSH key
   - Title: `MacBook Pro` (또는 원하는 이름)
   - Key: 위에서 복사한 공개키 붙여넣기
   - "Add SSH key" 클릭

5. **원격 저장소 URL 변경 (이미 완료됨)**
   ```bash
   git remote set-url origin git@github.com:taegyu33339013-pixel/H.git
   ```

6. **푸시**
   ```bash
   git push -u origin main
   ```

### 방법 3: GitHub CLI 사용

```bash
# GitHub CLI 설치 (없는 경우)
brew install gh

# 로그인
gh auth login

# 푸시
git push -u origin main
```

## 저장소 정보

- **저장소 URL**: https://github.com/taegyu33339013-pixel/H.git
- **브랜치**: main
- **커밋 수**: 2개
  - Initial commit: 오늘로또 프로젝트 - 한국어 가독성 최적화 및 성능 개선 완료
  - docs: README.md 추가

## 참고

- Personal Access Token은 비밀번호가 아닙니다. 토큰을 사용하세요.
- SSH 키를 사용하면 매번 인증할 필요가 없습니다.
- 푸시가 완료되면 GitHub에서 프로젝트를 확인할 수 있습니다.

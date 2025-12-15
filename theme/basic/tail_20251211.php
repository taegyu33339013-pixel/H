

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-logo">
                <span>🎱</span> 로또인사이트
            </div>
            <p class="footer-text">© 2025 로또인사이트. AI 기반 로또 패턴 분석 서비스</p>
            
            <!-- 법적 면책 조항 -->
            <div class="legal-disclaimer">
                <p><strong>⚠️ 중요 안내</strong></p>
                <ul>
                    <li>본 서비스는 로또 번호 선택을 위한 <strong>참고 정보</strong>를 제공하며, 당첨을 보장하거나 예측하지 않습니다.</li>
                    <li>모든 로또 조합의 당첨 확률은 <strong>동일</strong>합니다 (1/8,145,060).</li>
                    <li>과도한 복권 구매는 재정적 문제를 야기할 수 있습니다.</li>
                    <li>본 서비스는 <strong>만 19세 이상</strong> 이용 가능합니다.</li>
                    <li>복권은 <strong>동행복권 공식 판매처</strong>에서만 구매하세요.</li>
                </ul>
            </div>
            
            <div class="footer-links">
                <a href="terms.html">이용약관</a>
                <span>|</span>
                <a href="privacy.html">개인정보처리방침</a>
                <span>|</span>
                <a href="https://www.kcgp.or.kr" target="_blank" rel="noopener">책임도박</a>
                <span>|</span>
                <a href="https://dhlottery.co.kr" target="_blank" rel="noopener">동행복권 공식</a>
            </div>
        </div>
    </footer>
    
    <!-- 챗봇 상담 버튼 -->
    <button class="chatbot-btn" id="chatbot-btn" aria-label="상담하기">
        <span class="chatbot-icon">💬</span>
        <span class="chatbot-badge">1</span>
    </button>
    
    <!-- PWA 설치 배너 -->
    <div class="pwa-install-banner" id="pwa-banner">
        <div class="pwa-content">
            <div class="pwa-icon">🎱</div>
            <div class="pwa-text">
                <strong>로또인사이트 앱 설치</strong>
                <span>홈 화면에 추가하고 더 빠르게 사용하세요</span>
            </div>
        </div>
        <div class="pwa-actions">
            <button class="pwa-install-btn" id="pwa-install">설치하기</button>
            <button class="pwa-close-btn" id="pwa-close">✕</button>
        </div>
    </div>

    <!-- 모바일 하단 고정 CTA -->
    <div class="mobile-fixed-cta">
        <div class="cta-wrapper">
            <a href="auth.html" class="btn-mobile-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.54Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.54Z"/></svg>
                AI 분석 번호 확인하기 →
            </a>
        </div>
    </div>

    <!-- 이탈 방지 팝업 (간소화 버전) -->
    <div class="exit-popup" id="exit-popup">
        <div class="popup-overlay"></div>
        <div class="popup-content popup-compact">
            <button class="popup-close" id="popup-close">✕</button>
            <div class="popup-badge-top">💬 카카오 3초 시작 즉시 무료 1회</div>
            <h3 class="popup-title">AI 분석 번호<br>확인하셨나요?</h3>
            <a href="auth.html" class="popup-cta">
                지금 확인하기 →
            </a>
            <button class="popup-dismiss" id="popup-dismiss">나중에</button>
        </div>
    </div>

    <style>
        /* Exit Popup Styles */
        .exit-popup {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .exit-popup.active {
            display: flex;
        }

        .popup-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
        }

        .popup-content {
            position: relative;
            background: linear-gradient(180deg, #0d1829 0%, #162136 100%);
            border: 1px solid rgba(0, 212, 170, 0.3);
            border-radius: 24px;
            padding: 40px 32px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(0, 212, 170, 0.1);
            animation: popupIn 0.3s ease;
        }

        @keyframes popupIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .popup-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 32px;
            height: 32px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: var(--text-muted);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .popup-close:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
        }

        .popup-compact {
            padding: 32px 28px;
        }

        .popup-badge-top {
            display: inline-block;
            padding: 6px 14px;
            background: linear-gradient(135deg, rgba(0, 224, 164, 0.15) 0%, rgba(0, 224, 164, 0.08) 100%);
            border: 1px solid rgba(0, 224, 164, 0.3);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--accent-cyan);
            margin-bottom: 16px;
        }

        .popup-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--text-primary);
            line-height: 1.4;
        }

        .popup-cta {
            display: block;
            width: 100%;
            padding: 16px 24px;
            background: var(--gradient-cyan);
            border: none;
            border-radius: 14px;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            cursor: pointer;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .popup-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 170, 0.3);
        }

        .popup-dismiss {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 0.85rem;
            cursor: pointer;
            padding: 8px;
        }

        .popup-dismiss:hover {
            color: var(--text-secondary);
        }

        @media (max-width: 640px) {
            .popup-content {
                padding: 32px 24px;
                border-radius: 20px;
            }

            .popup-icon {
                font-size: 2.5rem;
            }

            .popup-title {
                font-size: 1.4rem;
            }

            .popup-desc {
                font-size: 0.9rem;
            }

            .popup-cta {
                padding: 14px 20px;
                font-size: 0.95rem;
            }
        }

        /* ===== 결제 수단 섹션 ===== */
        .payment-methods {
            background: var(--primary);
            padding: 32px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .payment-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .payment-label {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .payment-icons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .payment-icon {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 0.8rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .payment-icon:hover {
            border-color: rgba(0, 212, 170, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }

        .payment-icon.toss { border-color: rgba(49, 130, 246, 0.3); }
        .payment-icon.kakao { border-color: rgba(254, 229, 0, 0.3); }
        .payment-icon.naver { border-color: rgba(3, 199, 90, 0.3); }

        /* ===== Footer Links ===== */
        .footer-links {
            margin-top: 16px;
            display: flex;
            gap: 12px;
            justify-content: center;
            font-size: 0.8rem;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--accent-cyan);
        }

        .footer-links span {
            color: rgba(255, 255, 255, 0.2);
        }

        /* ===== 챗봇 버튼 ===== */
        .chatbot-btn {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: var(--gradient-cyan);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 212, 170, 0.4);
            z-index: 998;
            transition: all 0.3s ease;
        }

        .chatbot-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(0, 212, 170, 0.5);
        }

        .chatbot-icon {
            font-size: 1.5rem;
        }

        .chatbot-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 22px;
            height: 22px;
            background: #ff6b6b;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }

        @media (max-width: 768px) {
            .chatbot-btn {
                bottom: 180px;
                width: 54px;
                height: 54px;
            }

            .chatbot-icon {
                font-size: 1.3rem;
            }
        }

        /* ===== PWA 설치 배너 ===== */
        .pwa-install-banner {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            border-top: 1px solid rgba(0, 212, 170, 0.3);
            z-index: 1001;
            animation: slideUp 0.3s ease;
        }

        .pwa-install-banner.show {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .pwa-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pwa-icon {
            width: 44px;
            height: 44px;
            background: var(--gradient-cyan);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .pwa-text strong {
            display: block;
            font-size: 0.95rem;
            color: var(--text-primary);
        }

        .pwa-text span {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .pwa-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pwa-install-btn {
            padding: 10px 20px;
            background: var(--gradient-cyan);
            border: none;
            border-radius: 10px;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pwa-install-btn:hover {
            transform: scale(1.05);
        }

        .pwa-close-btn {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50%;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.9rem;
        }

        @media (max-width: 640px) {
            .pwa-install-banner {
                flex-direction: column;
                gap: 12px;
                padding-bottom: 24px;
            }

            .pwa-actions {
                width: 100%;
            }

            .pwa-install-btn {
                flex: 1;
            }
        }
    </style>

    <!-- Kakao SDK (공유 기능) - 지연 로딩 -->
    <script src="https://t1.kakaocdn.net/kakao_js_sdk/2.1.0/kakao.min.js" crossorigin="anonymous" defer></script>
    
    <!-- Shared Scripts - 지연 로딩 -->
    <script src="/scripts/shared.js" defer></script>
    
    <script>
        // ===== 한국 시장 맞춤 기능 =====
        
        // 로또 추첨일 카운트다운 (매주 토요일 8:45 PM)
        function initDrawCountdown() {
            const countdownEl = document.getElementById('draw-countdown');
            const roundEl = document.getElementById('current-round');
            if (!countdownEl) return;
            
            function getNextDrawTime() {
                const now = new Date();
                const dayOfWeek = now.getDay(); // 0=일, 6=토
                let daysUntilSaturday = (6 - dayOfWeek + 7) % 7;
                
                // 토요일이고 20:45 이후면 다음 주 토요일
                if (dayOfWeek === 6 && now.getHours() >= 21) {
                    daysUntilSaturday = 7;
                }
                
                const nextDraw = new Date(now);
                nextDraw.setDate(now.getDate() + daysUntilSaturday);
                nextDraw.setHours(20, 45, 0, 0);
                
                return nextDraw;
            }
            
            function updateDrawCountdown() {
                const now = new Date();
                const nextDraw = getNextDrawTime();
                const diff = nextDraw - now;
                
                if (diff <= 0) {
                    countdownEl.textContent = '추첨 중!';
                    return;
                }
                
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                let text = '';
                if (days > 0) text += `${days}일 `;
                text += `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                
                countdownEl.textContent = text;
            }
            
            // 현재 회차 계산 (2002년 12월 7일 1회차 기준)
            function getCurrentRound() {
                const firstDraw = new Date(2002, 11, 7);
                const now = new Date();
                const weeksDiff = Math.floor((now - firstDraw) / (7 * 24 * 60 * 60 * 1000));
                return weeksDiff + 1;
            }
            
            if (roundEl) {
                roundEl.textContent = `제 ${getCurrentRound()}회`;
            }
            
            updateDrawCountdown();
            setInterval(updateDrawCountdown, 1000);
        }
        
        // 카카오톡 공유
        function shareKakao() {
            if (typeof Kakao === 'undefined') {
                alert('카카오 SDK를 불러오는 중입니다. 잠시 후 다시 시도해주세요.');
                return;
            }
            
            if (!Kakao.isInitialized()) {
                // 실제 서비스에서는 실제 JavaScript 키로 교체 필요
                Kakao.init('YOUR_KAKAO_JAVASCRIPT_KEY');
            }
            
            Kakao.Share.sendDefault({
                objectType: 'feed',
                content: {
                    title: '로또인사이트 - AI가 분석한 이번 주 번호',
                    description: '1,180회차 당첨 데이터 기반 AI 패턴 분석! 카카오 3초 시작 즉시 무료 1회 🎁',
                    imageUrl: 'https://lottoinsight.ai/og-image.png',
                    link: {
                        mobileWebUrl: 'https://lottoinsight.ai',
                        webUrl: 'https://lottoinsight.ai',
                    },
                },
                buttons: [
                    {
                        title: '무료로 분석받기',
                        link: {
                            mobileWebUrl: 'https://lottoinsight.ai/auth.html',
                            webUrl: 'https://lottoinsight.ai/auth.html',
                        },
                    },
                ],
            });
        }
        
        // 링크 복사
        function copyLink() {
            const url = 'https://lottoinsight.ai?ref=share';
            navigator.clipboard.writeText(url).then(() => {
                showToast({
                    icon: '✅',
                    title: '링크가 복사되었습니다!',
                    subtitle: '친구에게 공유해보세요',
                    duration: 3000
                });
            }).catch(() => {
                // 폴백: 구형 브라우저
                const input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                alert('링크가 복사되었습니다!');
            });
        }
        
        // PWA 설치 프롬프트
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // 5초 후 설치 배너 표시
            setTimeout(() => {
                if (!sessionStorage.getItem('pwaBannerDismissed')) {
                    document.getElementById('pwa-banner')?.classList.add('show');
                }
            }, 5000);
        });
        
        function initPWABanner() {
            const banner = document.getElementById('pwa-banner');
            const installBtn = document.getElementById('pwa-install');
            const closeBtn = document.getElementById('pwa-close');
            
            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        if (outcome === 'accepted') {
                            showToast({
                                icon: '🎉',
                                title: '설치 완료!',
                                subtitle: '홈 화면에서 로또인사이트를 찾아보세요',
                                duration: 4000
                            });
                        }
                        deferredPrompt = null;
                        banner?.classList.remove('show');
                    }
                });
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    banner?.classList.remove('show');
                    sessionStorage.setItem('pwaBannerDismissed', 'true');
                });
            }
        }
        
        // 챗봇 버튼
        function initChatbot() {
            const chatbotBtn = document.getElementById('chatbot-btn');
            if (chatbotBtn) {
                chatbotBtn.addEventListener('click', () => {
                    // 실제 서비스에서는 채널톡, 카카오 상담 등으로 연결
                    alert('💬 상담 기능은 준비 중입니다.\n\n문의: support@lottoinsight.ai');
                });
            }
        }
        
        // 실시간 업데이트 시간 표시
        function initLastUpdate() {
            const el = document.getElementById('last-update');
            if (el) {
                const now = new Date();
                const minutes = Math.floor(Math.random() * 5) + 1;
                el.textContent = `${minutes}분 전`;
            }
        }
        
        // AI 분석 단계 아코디언 (SEO 최적화된 상세 설명)
        function initStepCards() {
            const stepCards = document.querySelectorAll('.step-card');
            
            stepCards.forEach(card => {
                const header = card.querySelector('.step-header');
                if (!header) return;
                
                // 접근성 속성 추가
                header.setAttribute('role', 'button');
                header.setAttribute('tabindex', '0');
                header.setAttribute('aria-expanded', 'false');
                
                function toggle() {
                    const isActive = card.classList.contains('active');
                    
                    // 다른 카드 닫기 (선택 사항: 동시에 여러 개 열려면 이 부분 제거)
                    stepCards.forEach(other => {
                        if (other !== card) {
                            other.classList.remove('active');
                            const otherHeader = other.querySelector('.step-header');
                            if (otherHeader) otherHeader.setAttribute('aria-expanded', 'false');
                        }
                    });
                    
                    // 현재 카드 토글
                    card.classList.toggle('active');
                    header.setAttribute('aria-expanded', !isActive ? 'true' : 'false');
                    
                    // 스크롤 (열릴 때)
                    if (!isActive) {
            setTimeout(() => {
                            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 100);
                    }
                }
                
                header.addEventListener('click', toggle);
                header.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggle();
                    }
                });
            });
            
            // 첫 번째 카드 자동 열기 (선택 사항)
            if (stepCards.length > 0) {
            setTimeout(() => {
                    stepCards[0].classList.add('active');
                    const firstHeader = stepCards[0].querySelector('.step-header');
                    if (firstHeader) firstHeader.setAttribute('aria-expanded', 'true');
                }, 500);
            }
        }
        
        // 실시간 카운터 애니메이션
        function animateCounter(el) {
            const target = parseFloat(el.dataset.count);
            const suffix = el.dataset.suffix || '';
            const prefix = el.dataset.prefix || '';
            const decimal = parseInt(el.dataset.decimal) || 0;
            const duration = 2000;
            const start = performance.now();
            
            function update(currentTime) {
                const elapsed = currentTime - start;
                const progress = Math.min(elapsed / duration, 1);
                
                // Ease out cubic
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                const current = target * easeProgress;
                
                if (decimal > 0) {
                    el.textContent = prefix + current.toFixed(decimal) + suffix;
                } else {
                    el.textContent = prefix + Math.floor(current).toLocaleString() + suffix;
                }
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }
            
            requestAnimationFrame(update);
        }
        
        function initCounterAnimations() {
            const counters = document.querySelectorAll('.stat-number[data-count]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                        entry.target.classList.add('counted');
                        animateCounter(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            counters.forEach(counter => observer.observe(counter));
        }
        
        // 실시간 분석 카운터 (랜덤하게 증가)
        function initLiveCounter() {
            const counter = document.getElementById('live-analysis-count');
            if (!counter) return;
            
            let count = 52847;
            setInterval(() => {
                count += Math.floor(Math.random() * 3) + 1;
                counter.textContent = count.toLocaleString();
            }, 3000 + Math.random() * 2000);
        }
        
        // 페이지별 초기화
        document.addEventListener('DOMContentLoaded', () => {
            // 한국 시장 맞춤 기능 초기화
            initDrawCountdown();
            initPWABanner();
            initChatbot();
            initLastUpdate();
            initStepCards();
            initCounterAnimations();
            initLiveCounter();
            
            // FAQ 아코디언 초기화
            initFaqAccordion();
            
            // 카운트다운 타이머 초기화
            initCountdownTimer('countdown-timer');
            
            // 이탈 방지 팝업 초기화
            initPopupListeners('exit-popup');
            
            // 이탈 감지
                if (!sessionStorage.getItem('exitPopupShown')) {
                initExitIntent(() => {
                    if (!sessionStorage.getItem('exitPopupShown')) {
                        showPopup('exit-popup');
                        sessionStorage.setItem('exitPopupShown', 'true');
                    }
                });
            }

            // 소셜 프루프 피드 초기화
            initSocialProofFeed({
                containerId: 'winner-feed',
                data: [
                    { region: '서울', name: '김**', action: '분석을 완료', time: 1 },
                    { region: '경기', name: '이**', action: '새 조합을 받았습니다', time: 2 },
                    { region: '부산', name: '박**', action: '분석을 완료', time: 3 },
                    { region: '인천', name: '최**', action: '회원가입', time: 4 },
                    { region: '대구', name: '정**', action: '3회 연속 분석 중', time: 5 },
                    { region: '광주', name: '강**', action: '새 조합을 받았습니다', time: 6 },
                    { region: '대전', name: '조**', action: '분석을 완료', time: 8 },
                    { region: '울산', name: '윤**', action: '크레딧 충전', time: 10 },
                    { region: '세종', name: '장**', action: '분석을 완료', time: 12 },
                    { region: '제주', name: '임**', action: '5회 연속 이용 중', time: 15 }
                ],
                initialDelay: 3000,
                minInterval: 8000,
                maxInterval: 15000
            });
        });
    </script>

    <style>
        /* Activity Toast */
        .activity-toast {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--secondary);
            border: 1px solid rgba(0, 212, 170, 0.3);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            z-index: 900;
            transform: translateX(-120%);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 300px;
        }

        .activity-toast.active {
            transform: translateX(0);
            opacity: 1;
        }

        .activity-icon {
            font-size: 1.3rem;
        }

        .activity-content {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .activity-content strong {
            color: var(--text-primary);
        }

        .activity-time {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .activity-toast {
                left: 16px;
                right: 16px;
                bottom: 100px;
                max-width: none;
                padding: 12px 16px;
                border-radius: 14px;
                background: linear-gradient(135deg, var(--secondary) 0%, rgba(22, 33, 54, 0.98) 100%);
                border: 1px solid rgba(0, 212, 170, 0.4);
                box-shadow: 0 8px 32px rgba(0, 212, 170, 0.15);
            }

            .activity-icon {
                font-size: 1.2rem;
            }

            .activity-content {
                font-size: 0.85rem;
            }

            .activity-time {
                font-size: 0.75rem;
            }
        }
        
        /* Page Wrapper - 모바일 가로 스크롤 방지 */
        .page-wrapper {
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
            position: relative;
        }
        
        /* ===== 초소형 화면 대응 (iPhone SE, Galaxy Fold) ===== */
        @media (max-width: 375px) {
            .hero-title {
                font-size: 2rem !important;
            }
            
            .hero-subtitle {
                font-size: 0.95rem;
            }
            
            .mini-balls {
                gap: 5px;
            }
            
            .mini-ball {
                width: 30px;
                height: 30px;
                font-size: 0.75rem;
            }
            
            .result-preview-wrapper {
                padding-top: 40px;
            }
            
            .compare-emoji {
                font-size: 2rem;
            }
            
            .compare-title {
                font-size: 1.1rem;
            }
            
            .step-header h3 {
                font-size: 0.95rem;
            }
            
            .faq-question span {
                font-size: 0.9rem;
            }
        }
        
        /* ===== 극소형 화면 (320px 이하) ===== */
        @media (max-width: 320px) {
            .hero-title {
                font-size: 1.7rem !important;
            }
            
            .cta-buttons {
                flex-direction: column;
            }
            
            .cta-buttons .btn-primary {
                width: 100%;
            }
            
            .mini-ball {
                width: 26px;
                height: 26px;
                font-size: 0.65rem;
            }
            
            .stat-number {
                font-size: 1.8rem;
            }
        }
        
        /* ===== 가로 모드 대응 ===== */
        @media (orientation: landscape) and (max-height: 500px) {
            .hero {
                min-height: auto;
                padding: 60px 0;
            }
            
            .mobile-fixed-cta {
                padding: 8px 16px;
            }
            
            .mobile-fixed-cta .btn-mobile-primary {
                height: 44px;
                font-size: 0.95rem;
            }
        }
        
        /* ===== 폴더블 디바이스 대응 ===== */
        @media (max-width: 300px) {
            .container {
                padding: 0 8px;
            }
            
            .section {
                padding: 30px 0;
            }
            
            .hero-title {
                font-size: 1.5rem !important;
                line-height: 1.3;
            }
            
            .mini-balls {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

		/* 아주 작은 모바일에서 카드 가로 살짝 줄이기 */
		@media (max-width: 480px) {
			.hero-visual {
				justify-content: center;
			}

			.lotto-display {
				width: 100%;
				max-width: 320px;   /* 필요하면 330~340px로 조절해 보셔도 됩니다 */
				margin: 0 auto;
			}
		}
    </style>
</div><!-- .page-wrapper -->
</body>
</html>


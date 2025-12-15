
    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-logo">
                <span>🎱</span> LottoAI
            </div>
            <p class="footer-text">© 2025 LottoAI. AI 기반 로또 분석 서비스 | 통계 기반 추천, 당첨 보장 아님</p>
        </div>
    </footer>

    <!-- 모바일 하단 고정 CTA -->
    <div class="mobile-fixed-cta">
        <div class="cta-wrapper">
            <a href="auth.html" class="btn-mobile-primary">
                AI 추천번호 받기 (무료 2회) →
            </a>
            <a href="#how" class="btn-mobile-secondary">
                AI 분석 방식 보기
            </a>
        </div>
    </div>

    <!-- 이탈 방지 팝업 -->
    <div class="exit-popup" id="exit-popup">
        <div class="popup-overlay"></div>
        <div class="popup-content">
            <button class="popup-close" id="popup-close">✕</button>
            <div class="popup-icon">🎱</div>
            <h3 class="popup-title">잠깐만요!</h3>
            <p class="popup-desc">
                이번 주 <span class="highlight">특이 패턴</span>이 아직 공개되지 않았습니다.<br>
                지금이 가장 유리한 시점일 수 있어요.
            </p>
            <div class="popup-badge">🎁 지금 가입하면 무료 2회 제공</div>
            <a href="auth.html" class="popup-cta">
                무료로 패턴 확인하기 →
            </a>
            <button class="popup-dismiss" id="popup-dismiss">다음에 할게요</button>
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

        .popup-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        .popup-title {
            font-size: 1.6rem;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .popup-desc {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .popup-desc .highlight {
            color: var(--accent-gold);
            font-weight: 600;
        }

        .popup-badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(255, 215, 0, 0.15);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--accent-gold);
            margin-bottom: 24px;
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
    </style>

    <script>
        // Navbar scroll effect
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Reveal on scroll
        const reveals = document.querySelectorAll('.reveal');
        
        const revealOnScroll = () => {
            reveals.forEach(element => {
                const windowHeight = window.innerHeight;
                const elementTop = element.getBoundingClientRect().top;
                const revealPoint = 150;

                if (elementTop < windowHeight - revealPoint) {
                    element.classList.add('active');
                }
            });
        };

        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('load', revealOnScroll);

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Ball animation restart on scroll into view
        const observerOptions = {
            threshold: 0.5
        };

        const ballObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const balls = entry.target.querySelectorAll('.ball');
                    balls.forEach((ball, index) => {
                        ball.style.animation = 'none';
                        ball.offsetHeight; // Trigger reflow
                        ball.style.animation = `ballPop 0.5s ease ${index * 0.1}s forwards`;
                    });
                }
            });
        }, observerOptions);

        document.querySelectorAll('.lotto-balls, .example-balls').forEach(container => {
            ballObserver.observe(container);
        });

        // Exit Intent Popup
        const exitPopup = document.getElementById('exit-popup');
        const popupClose = document.getElementById('popup-close');
        const popupDismiss = document.getElementById('popup-dismiss');
        let popupShown = false;

        // Show popup when mouse leaves viewport (desktop)
        document.addEventListener('mouseout', (e) => {
            if (!popupShown && e.clientY < 10 && !e.relatedTarget) {
                showExitPopup();
            }
        });

        // Show popup after 30 seconds if not interacted (mobile fallback)
        setTimeout(() => {
            if (!popupShown && !sessionStorage.getItem('exitPopupShown')) {
                showExitPopup();
            }
        }, 30000);

        function showExitPopup() {
            if (sessionStorage.getItem('exitPopupShown')) return;
            exitPopup.classList.add('active');
            popupShown = true;
            sessionStorage.setItem('exitPopupShown', 'true');
        }

        function hideExitPopup() {
            exitPopup.classList.remove('active');
        }

        popupClose.addEventListener('click', hideExitPopup);
        popupDismiss.addEventListener('click', hideExitPopup);
        
        // Close on overlay click
        exitPopup.querySelector('.popup-overlay').addEventListener('click', hideExitPopup);

        // FAQ Toggle
        document.querySelectorAll('.faq-item').forEach(item => {
            item.querySelector('.faq-question').addEventListener('click', () => {
                // Close others
                document.querySelectorAll('.faq-item').forEach(other => {
                    if (other !== item) other.classList.remove('active');
                });
                // Toggle current
                item.classList.toggle('active');
            });
        });

        // Live Activity Toast (Social Proof)
        const names = ['김**', '이**', '박**', '최**', '정**', '강**', '조**', '윤**', '장**', '임**'];
        const actions = ['분석을 완료했습니다', '새 조합을 받았습니다', '가입했습니다'];
        
        function showActivityToast() {
            const name = names[Math.floor(Math.random() * names.length)];
            const action = actions[Math.floor(Math.random() * actions.length)];
            const time = Math.floor(Math.random() * 3) + 1;
            
            const toast = document.createElement('div');
            toast.className = 'activity-toast';
            toast.innerHTML = `
                <div class="activity-icon">🎱</div>
                <div class="activity-content">
                    <strong>${name}</strong>님이 방금 ${action}
                    <span class="activity-time">${time}분 전</span>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('active'), 100);
            
            setTimeout(() => {
                toast.classList.remove('active');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Show activity toast every 15-25 seconds
        function scheduleActivityToast() {
            const delay = (Math.random() * 10000) + 15000; // 15-25 seconds
            setTimeout(() => {
                if (!sessionStorage.getItem('exitPopupShown')) {
                    showActivityToast();
                }
                scheduleActivityToast();
            }, delay);
        }

        // Start after 8 seconds
        setTimeout(scheduleActivityToast, 8000);
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
                bottom: 140px;
                max-width: none;
                padding: 10px 14px;
                border-radius: 10px;
            }

            .activity-icon {
                font-size: 1.1rem;
            }

            .activity-content {
                font-size: 0.8rem;
            }

            .activity-time {
                font-size: 0.7rem;
            }
        }
    </style>
</body>
</html>


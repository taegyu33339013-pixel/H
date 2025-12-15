  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="footer-logo">
            <div class="footer-logo-icon">
              <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
                <circle cx="11" cy="12" r="8" fill="url(#gold-ball-ft)"/>
                <ellipse cx="8" cy="9" rx="3" ry="2" fill="rgba(255,255,255,0.5)" transform="rotate(-25 8 9)"/>
                <circle cx="18" cy="18" r="7" fill="none" stroke="#030711" stroke-width="2"/>
                <circle cx="16" cy="16" r="1.2" fill="#030711"/>
                <circle cx="20" cy="19" r="1.2" fill="#030711"/>
                <line x1="23" y1="23" x2="28" y2="28" stroke="#030711" stroke-width="2.5" stroke-linecap="round"/>
                <defs>
                  <linearGradient id="gold-ball-ft" x1="20%" y1="20%" x2="80%" y2="80%">
                    <stop offset="0%" stop-color="#ffe066"/>
                    <stop offset="50%" stop-color="#ffd700"/>
                    <stop offset="100%" stop-color="#cc9f00"/>
                  </linearGradient>
                </defs>
              </svg>
            </div>
            오늘로또
          </div>
          <p class="footer-desc">
            AI 기반 로또 번호 통계 분석 서비스.<br>
            동행복권 공식 데이터를 활용합니다.
          </p>
        </div>
        <div class="footer-links">
          <div class="footer-col">
            <h4>서비스</h4>
            <ul>
              <li><a href="result.html">AI 분석</a></li>
              <li><a href="#stats">통계 대시보드</a></li>
              <li><a href="#pricing">요금 안내</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>지원</h4>
            <ul>
              <li><a href="#faq">자주 묻는 질문</a></li>
              <li><a href="mailto:support@lottoinsight.ai">문의하기</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>법적 고지</h4>
            <ul>
              <li><a href="terms.html">이용약관</a></li>
              <li><a href="privacy.html">개인정보처리방침</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="footer-copyright">
          © 2025 오늘로또. All rights reserved.
        </p>
        <p class="footer-disclaimer">
          ⚠️ 본 서비스는 통계 기반 참고 정보이며, 당첨을 보장하지 않습니다. 만 19세 이상 이용 가능. 로또 구매는 동행복권 공식 판매처에서만 가능합니다.
        </p>
      </div>

      <div class="footer-company">
        <div class="footer-company-info">
          <div class="footer-company-item">상호:<span>오늘로또</span></div>
          <div class="footer-company-item">대표:<span>홍길동</span></div>
          <div class="footer-company-item">사업자등록번호:<span>123-45-67890</span></div>
          <div class="footer-company-item">통신판매업신고:<span>제2023-서울강남-12345호</span></div>
        </div>
        <div class="footer-company-info">
          <div class="footer-company-item">주소:<span>서울특별시 강남구 테헤란로 123, 4층</span></div>
          <div class="footer-company-item">서비스 시작일:<span>2023년 1월 1일</span></div>
        </div>
        <div class="footer-contact">
          <div class="footer-contact-item">
            📧 이메일: <a href="mailto:support@lottoinsight.ai">support@lottoinsight.ai</a>
          </div>
          <div class="footer-contact-item">
            📞 고객센터: <a href="tel:02-1234-5678">02-1234-5678</a> (평일 10:00~18:00)
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // ===== Loading Screen =====
    window.addEventListener('load', () => {
      setTimeout(() => {
        document.getElementById('loadingScreen').classList.add('hidden');
      }, 800);
      
      // ===== Initialize Data from lotto-data.js =====
      initializeLottoData();
    });

    // ===== Initialize Lotto Data =====
    function initializeLottoData() {
      if (typeof LOTTO_HISTORY_DATA === 'undefined') {
        console.warn('LOTTO_HISTORY_DATA not loaded');
        return;
      }

      const latestRound = 1201;
      const latestData = LOTTO_HISTORY_DATA[latestRound];
      
      if (latestData) {
        // Update LIVE 최신 결과 카드
        updateLiveResult(latestRound, latestData);
        
        // Update archive 실제 당첨 번호
        updateArchiveActualNumbers();
      }
    }

    function getBallColor(num) {
      if (num <= 10) return 'ball-yellow';
      if (num <= 20) return 'ball-blue';
      if (num <= 30) return 'ball-red';
      if (num <= 40) return 'ball-gray';
      return 'ball-green';
    }

    function updateLiveResult(round, data) {
      // Update round info
      const roundEl = document.querySelector('.hero-card-round');
      if (roundEl) {
        roundEl.textContent = `${round}회 · ${data.date}`;
      }
      
      // Update balls
      const ballsContainer = document.querySelector('.hero-balls-container');
      if (ballsContainer) {
        const ballsHtml = data.numbers.map(n => 
          `<div class="hero-ball ${getBallColor(n)}">${n}</div>`
        ).join('') +
        `<span class="bonus-sep">+</span>` +
        `<div class="hero-ball ${getBallColor(data.bonus)}">${data.bonus}</div>`;
        ballsContainer.innerHTML = ballsHtml;
      }
    }

    function updateArchiveActualNumbers() {
      // Archive 테이블의 실제 당첨 번호 업데이트
      const archiveRows = document.querySelectorAll('.archive-row');
      
      archiveRows.forEach(row => {
        const roundEl = row.querySelector('.archive-round');
        if (!roundEl) return;
        
        const roundText = roundEl.textContent;
        const roundNum = parseInt(roundText.replace('회', ''));
        
        if (!roundNum || !LOTTO_HISTORY_DATA[roundNum]) return;
        
        const actualData = LOTTO_HISTORY_DATA[roundNum];
        const actualBallsContainer = row.querySelectorAll('.archive-balls')[1]; // 두 번째가 실제 당첨
        
        if (actualBallsContainer) {
          const ballsHtml = actualData.numbers.map(n => 
            `<span class="archive-ball ${getBallColor(n)}">${n}</span>`
          ).join('');
          actualBallsContainer.innerHTML = ballsHtml;
        }
      });
    }

    // ===== Mobile Menu =====
    function openMobileMenu() {
      document.getElementById('mobileMenu').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
      document.getElementById('mobileMenu').classList.remove('active');
      document.body.style.overflow = '';
    }

    // ===== Back to Top =====
    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
      if (window.scrollY > 500) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    });

    function scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    // FAQ accordion
    document.querySelectorAll('.faq-question').forEach(button => {
      button.addEventListener('click', () => {
        const item = button.parentElement;
        const isActive = item.classList.contains('active');
        
        // Close all
        document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
        
        // Open clicked if wasn't active
        if (!isActive) {
          item.classList.add('active');
        }
      });
    });

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

    // ===== Live Activity Feed =====
    const activities = [
      { name: '김**', loc: '서울', style: 'Hot/Cold 분석' },
      { name: '이**', loc: '부산', style: '밸런스 최적화' },
      { name: '박**', loc: '대전', style: '홀짝/고저 분석' },
      { name: '최**', loc: '인천', style: 'AI 추천' },
      { name: '정**', loc: '광주', style: 'AC값 분석' },
      { name: '강**', loc: '대구', style: '패턴 분석' },
      { name: '조**', loc: '울산', style: 'Hot/Cold 분석' },
      { name: '윤**', loc: '세종', style: '밸런스 최적화' },
      { name: '장**', loc: '수원', style: 'AI 추천' },
      { name: '임**', loc: '성남', style: '홀짝/고저 분석' },
    ];

    const surnames = ['김', '이', '박', '최', '정', '강', '조', '윤', '장', '임', '한', '오', '서', '신', '권'];
    const locations = ['서울', '부산', '대전', '인천', '광주', '대구', '울산', '세종', '수원', '성남', '고양', '용인', '청주', '전주', '천안'];
    const styles = ['Hot/Cold 분석', 'AC값 분석', '홀짝/고저 분석', '색상볼 통계', '상관관계 분석', '몬테카를로', '합계 분석', '주기 분석', '끝수 분석', '연속번호 패턴'];

    function generateActivity() {
      const surname = surnames[Math.floor(Math.random() * surnames.length)];
      const location = locations[Math.floor(Math.random() * locations.length)];
      const style = styles[Math.floor(Math.random() * styles.length)];
      return { name: `${surname}**`, loc: location, style };
    }

    function addActivity() {
      const feed = document.getElementById('activityFeed');
      const activity = generateActivity();
      
      const item = document.createElement('div');
      item.className = 'activity-item';
      item.innerHTML = `
        <div class="activity-avatar">${activity.name[0]}*</div>
        <div class="activity-content">
          <p class="activity-text"><strong>${activity.name} (${activity.loc})</strong> 님이 <span class="style-tag">${activity.style}</span>을 완료했습니다</p>
        </div>
        <span class="activity-time">방금 전</span>
      `;
      
      feed.insertBefore(item, feed.firstChild);
      
      // Update times
      const items = feed.querySelectorAll('.activity-item');
      items.forEach((el, i) => {
        if (i > 0) {
          el.querySelector('.activity-time').textContent = `${i}분 전`;
        }
      });
      
      // Keep only 4 items
      if (items.length > 4) {
        feed.removeChild(items[items.length - 1]);
      }
      
      // Update today count
      const countEl = document.getElementById('todayCount');
      const currentCount = parseInt(countEl.textContent.replace(/,/g, ''));
      countEl.textContent = (currentCount + 1).toLocaleString();
    }

    // Add new activity every 8-15 seconds
    setInterval(() => {
      addActivity();
    }, 8000 + Math.random() * 7000);

    // ===== Cumulative Counter Animation =====
    function animateCounter(el, target) {
      const duration = 2000;
      const start = 0;
      const startTime = performance.now();
      
      function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(start + (target - start) * easeOut);
        
        el.textContent = current.toLocaleString();
        
        if (progress < 1) {
          requestAnimationFrame(update);
        }
      }
      
      requestAnimationFrame(update);
    }

    // Counter observer
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.dataset.target || el.textContent.replace(/,/g, ''));
          animateCounter(el, target);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    document.querySelectorAll('.cumulative-number').forEach(el => {
      el.dataset.target = el.textContent.replace(/,/g, '');
      el.textContent = '0';
      counterObserver.observe(el);
    });

    // ===== Data Verification =====
    // 전체 1~1201회 데이터는 lotto-data.js에서 로드됨 (getBallColor 함수는 위에서 정의)

    function verifyData() {
      const input = document.getElementById('verifyInput');
      const result = document.getElementById('verifyResult');
      const round = parseInt(input.value);
      
      if (!round || round < 1 || round > 1201) {
        alert('1부터 1201 사이의 회차를 입력해주세요.');
        return;
      }

      // LOTTO_HISTORY_DATA에서 데이터 조회
      const data = typeof LOTTO_HISTORY_DATA !== 'undefined' ? LOTTO_HISTORY_DATA[round] : null;
      
      if (data) {
        document.getElementById('verifyTitle').textContent = `${round}회차 (${data.date})`;
        
        const ballsHtml = data.numbers.map(n => 
          `<span class="archive-ball ${getBallColor(n)}">${n}</span>`
        ).join('');
        
        // 보너스 번호 포함
        const ballsWithBonus = ballsHtml + 
          `<span style="margin: 0 6px; color: var(--text-muted);">+</span>` +
          `<span class="archive-ball ${getBallColor(data.bonus)}">${data.bonus}</span>`;
        
        document.getElementById('verifyBalls').innerHTML = ballsWithBonus;
        result.style.display = 'block';
      } else {
        document.getElementById('verifyTitle').textContent = `${round}회차`;
        document.getElementById('verifyBalls').innerHTML = '<span style="color: var(--text-muted)">데이터를 찾을 수 없습니다</span>';
        result.style.display = 'block';
      }
    }

    // Allow Enter key
    document.getElementById('verifyInput').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        verifyData();
      }
    });

    // ===== Increment counters periodically =====
    setInterval(() => {
      const totalUsers = document.getElementById('totalUsers');
      const totalAnalysis = document.getElementById('totalAnalysis');
      
      if (totalUsers && Math.random() > 0.7) {
        const current = parseInt(totalUsers.textContent.replace(/,/g, ''));
        totalUsers.textContent = (current + 1).toLocaleString();
      }
      
      if (totalAnalysis && Math.random() > 0.5) {
        const current = parseInt(totalAnalysis.textContent.replace(/,/g, ''));
        totalAnalysis.textContent = (current + Math.floor(Math.random() * 3) + 1).toLocaleString();
      }
    }, 5000);

    // ===== Floating Share Button =====
    function toggleShare() {
      const floatingShare = document.getElementById('floatingShare');
      floatingShare.classList.toggle('active');
    }

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      const floatingShare = document.getElementById('floatingShare');
      if (!floatingShare.contains(e.target)) {
        floatingShare.classList.remove('active');
      }
    });

    // ===== 카카오 SDK 초기화 =====
    const KAKAO_APP_KEY = 'YOUR_KAKAO_JAVASCRIPT_KEY'; // ← 실제 키로 교체 필요
    
    // SDK 초기화 (페이지 로드 시)
    if (typeof Kakao !== 'undefined' && KAKAO_APP_KEY !== 'YOUR_KAKAO_JAVASCRIPT_KEY') {
      try {
        Kakao.init(KAKAO_APP_KEY);
        console.log('✅ 카카오 SDK 초기화 완료');
      } catch (e) {
        console.warn('카카오 SDK 초기화 실패:', e);
      }
    }

    // ===== Share Functions =====
    const shareData = {
      title: '오늘로또 - AI 로또 분석',
      text: '🎱 이번 주 AI 추천 번호: 5, 13, 22, 28, 34, 41\n\n23년간 7,206개 당첨번호를 AI가 분석! 무료 1회 제공!',
      url: window.location.href
    };

    function shareKakao() {
      const siteUrl = 'https://lottoinsight.ai';
      
      // Kakao SDK 초기화 여부 확인
      if (typeof Kakao !== 'undefined' && Kakao.isInitialized()) {
        // 카카오톡 메시지 API 사용
        Kakao.Share.sendDefault({
          objectType: 'feed',
          content: {
            title: '🎱 AI가 분석한 이번 주 로또 번호',
            description: '오늘로또 - 1,201회차 데이터 기반 AI 분석\n무료 1회 분석 즉시 제공!',
            imageUrl: siteUrl + '/og-image.png',
            link: {
              mobileWebUrl: siteUrl,
              webUrl: siteUrl
            }
          },
          itemContent: {
            profileText: '오늘로또',
            titleImageText: 'AI 로또 분석'
          },
          social: {
            likeCount: 1247,
            sharedCount: 458
          },
          buttons: [
            {
              title: '🔮 무료로 분석 받기',
              link: {
                mobileWebUrl: siteUrl + '/auth.html',
                webUrl: siteUrl + '/auth.html'
              }
            },
            {
              title: '📊 통계 보기',
              link: {
                mobileWebUrl: siteUrl + '/result.html',
                webUrl: siteUrl + '/result.html'
              }
            }
          ]
        });
      } else {
        // SDK 미초기화 시 카카오스토리 공유로 대체
        const shareUrl = encodeURIComponent(siteUrl);
        const shareText = encodeURIComponent('🎱 AI 로또 분석 - 무료 1회 즉시 제공!\n23년간 당첨번호 패턴 분석');
        
        // 모바일 여부 체크
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        
        if (isMobile) {
          // 카카오톡 앱으로 공유 시도
          window.location.href = `kakaolink://send?appkey=${KAKAO_APP_KEY}&url=${shareUrl}`;
          
          // 3초 후에도 페이지에 있으면 웹 공유로 전환
          setTimeout(() => {
            window.open(`https://story.kakao.com/share?url=${shareUrl}`, '_blank');
          }, 3000);
        } else {
          // PC에서는 카카오스토리 웹 공유
          window.open(`https://story.kakao.com/share?url=${shareUrl}`, '_blank', 'width=600,height=400');
        }
      }
      closeShareModal();
    }

    function shareTwitter() {
      const text = encodeURIComponent('🎱 이번 주 AI 추천 번호: 5, 13, 22, 28, 34, 41\n\n오늘로또에서 무료로 AI 분석 받아보세요!');
      const url = encodeURIComponent(window.location.href);
      window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
      closeShareModal();
    }

    function shareFacebook() {
      const url = encodeURIComponent(window.location.href);
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
      closeShareModal();
    }

    function copyLink() {
      navigator.clipboard.writeText(window.location.href).then(() => {
        showCopyToast();
        closeShareModal();
      }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = window.location.href;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showCopyToast();
        closeShareModal();
      });
    }

    function showCopyToast() {
      const toast = document.getElementById('copyToast');
      toast.classList.add('active');
      setTimeout(() => {
        toast.classList.remove('active');
      }, 2500);
    }

    // ===== Share Modal =====
    function openShareModal() {
      document.getElementById('shareModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeShareModal() {
      document.getElementById('shareModal').classList.remove('active');
      document.body.style.overflow = '';
    }

    // Close modal on backdrop click
    document.getElementById('shareModal').addEventListener('click', (e) => {
      if (e.target === e.currentTarget) {
        closeShareModal();
      }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeShareModal();
      }
    });

    // ===== Notification Subscription =====
    function subscribeNotification(e) {
      e.preventDefault();
      const phone = document.getElementById('notificationPhone').value.trim();
      
      if (!phone) {
        alert('전화번호를 입력해주세요.');
        return;
      }
      
      // Phone validation (Korean format)
      const phoneRegex = /^01[0-9]-?[0-9]{3,4}-?[0-9]{4}$/;
      if (!phoneRegex.test(phone.replace(/-/g, ''))) {
        alert('올바른 전화번호 형식을 입력해주세요.\n예: 010-1234-5678');
        return;
      }
      
      // Simulate subscription
      alert('🔔 알림 신청이 완료되었습니다!\n\n매주 토요일 추첨 후 카카오톡으로 결과를 보내드릴게요.');
      document.getElementById('notificationPhone').value = '';
    }

    // ===== Web Share API (if supported) =====
    if (navigator.share) {
      // Modern browsers with Web Share API
      document.querySelectorAll('.share-toggle').forEach(btn => {
        btn.addEventListener('long-press', () => {
          navigator.share(shareData).catch(console.error);
        });
      });
    }

    // ===== Keyboard shortcut for share =====
    document.addEventListener('keydown', (e) => {
      // Ctrl/Cmd + Shift + S to open share modal
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'S') {
        e.preventDefault();
        openShareModal();
      }
    });

    // ===== Countdown Timer =====
    function updateCountdown() {
      const now = new Date();
      const dayOfWeek = now.getDay(); // 0 = Sunday, 6 = Saturday
      
      // Find next Saturday 8:45 PM (20:45)
      let daysUntilSaturday = (6 - dayOfWeek + 7) % 7;
      if (daysUntilSaturday === 0) {
        // It's Saturday, check if past 20:45
        const saturdayTime = new Date(now);
        saturdayTime.setHours(20, 45, 0, 0);
        if (now >= saturdayTime) {
          daysUntilSaturday = 7; // Next Saturday
        }
      }
      
      const nextDraw = new Date(now);
      nextDraw.setDate(now.getDate() + daysUntilSaturday);
      nextDraw.setHours(20, 45, 0, 0);
      
      const diff = nextDraw - now;
      
      if (diff <= 0) {
        // Refresh for next week
        setTimeout(updateCountdown, 1000);
        return;
      }
      
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const secs = Math.floor((diff % (1000 * 60)) / 1000);
      
      document.getElementById('countDays').textContent = days;
      document.getElementById('countHours').textContent = hours.toString().padStart(2, '0');
      document.getElementById('countMins').textContent = mins.toString().padStart(2, '0');
      document.getElementById('countSecs').textContent = secs.toString().padStart(2, '0');
    }
    
    // Initialize countdown
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // ===== Queue Number =====
    let queueNum = 247;
    function updateQueueNumber() {
      if (Math.random() > 0.6) {
        queueNum += Math.floor(Math.random() * 2) + 1;
        document.getElementById('queueNumber').textContent = queueNum;
      }
    }
    setInterval(updateQueueNumber, 4000);

    // ===== API Response Time Simulation =====
    function updateApiResponse() {
      const baseTime = 35;
      const variance = Math.floor(Math.random() * 30);
      const responseTime = baseTime + variance;
      document.getElementById('apiResponse').textContent = `(${responseTime}ms)`;
    }
    setInterval(updateApiResponse, 5000);

    // ===== DB Sync Time =====
    let syncMinutes = 3;
    function updateDbSync() {
      syncMinutes++;
      if (syncMinutes > 10) {
        syncMinutes = 1; // Reset (simulating sync)
      }
      document.getElementById('dbSync').textContent = `(${syncMinutes}분 전)`;
    }
    setInterval(updateDbSync, 60000);

    // ===== Quality Gauge Animation on Scroll =====
    const gaugeObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const fills = entry.target.querySelectorAll('.quality-fill');
          fills.forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0%';
            setTimeout(() => {
              fill.style.width = width;
            }, 100);
          });
          gaugeObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    const qualityCard = document.querySelector('.quality-card');
    if (qualityCard) {
      gaugeObserver.observe(qualityCard);
    }
  </script>
</body>
</html>
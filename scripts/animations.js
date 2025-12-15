/**
 * 로또인사이트 - Animation System
 * GSAP 기반 애니메이션 + 폴백
 */

const AnimationManager = {
  gsapLoaded: false,
  scrollTriggerLoaded: false,
  
  /**
   * 초기화
   */
  init() {
    // GSAP 로드 확인
    this.checkGSAP();
    
    // Intersection Observer 기반 애니메이션 (폴백)
    this.initScrollAnimations();
    
    // 네비게이션 스크롤 효과
    this.initNavbarScroll();
    
    // 스태거 애니메이션
    this.initStaggerAnimations();
    
    console.log('🎬 Animation Manager initialized', {
      gsap: this.gsapLoaded,
      scrollTrigger: this.scrollTriggerLoaded
    });
  },
  
  /**
   * GSAP 로드 확인
   */
  checkGSAP() {
    this.gsapLoaded = typeof gsap !== 'undefined';
    this.scrollTriggerLoaded = this.gsapLoaded && typeof ScrollTrigger !== 'undefined';
    
    if (this.gsapLoaded) {
      gsap.config({ nullTargetWarn: false });
      
      if (this.scrollTriggerLoaded) {
        gsap.registerPlugin(ScrollTrigger);
      }
    }
  },
  
  /**
   * 스크롤 기반 애니메이션 (Intersection Observer)
   */
  initScrollAnimations() {
    const animatedElements = document.querySelectorAll('[data-animate]');
    
    if (animatedElements.length === 0) return;
    
    // Reduced motion 체크
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      animatedElements.forEach(el => el.classList.add('animated'));
      return;
    }
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const delay = parseInt(el.dataset.delay) || 0;
          
          setTimeout(() => {
            if (this.gsapLoaded) {
              this.animateWithGSAP(el);
            } else {
              el.classList.add('animated');
            }
          }, delay);
          
          observer.unobserve(el);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    animatedElements.forEach(el => observer.observe(el));
  },
  
  /**
   * GSAP 애니메이션 적용
   */
  animateWithGSAP(el) {
    const type = el.dataset.animate;
    const duration = parseFloat(el.dataset.duration) || 0.6;
    
    const animations = {
      'fade-up': { y: 30, opacity: 0 },
      'fade-down': { y: -30, opacity: 0 },
      'fade-left': { x: -30, opacity: 0 },
      'fade-right': { x: 30, opacity: 0 },
      'scale': { scale: 0.9, opacity: 0 },
      'pop': { scale: 0.5, opacity: 0 }
    };
    
    const from = animations[type] || { opacity: 0 };
    
    gsap.from(el, {
      ...from,
      duration,
      ease: 'expo.out',
      clearProps: 'all',
      onComplete: () => el.classList.add('animated')
    });
  },
  
  /**
   * 네비게이션 스크롤 효과
   */
  initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    
    let lastScroll = 0;
    let ticking = false;
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const currentScroll = window.scrollY;
          
          // 스크롤 다운 시 축소
          if (currentScroll > 50) {
            navbar.classList.add('scrolled');
          } else {
            navbar.classList.remove('scrolled');
          }
          
          // 스크롤 방향에 따라 숨김/표시 (선택적)
          // if (currentScroll > lastScroll && currentScroll > 200) {
          //   navbar.classList.add('hidden');
          // } else {
          //   navbar.classList.remove('hidden');
          // }
          
          lastScroll = currentScroll;
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  },
  
  /**
   * 스태거 애니메이션
   */
  initStaggerAnimations() {
    const staggerContainers = document.querySelectorAll('[data-stagger]');
    
    if (staggerContainers.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const container = entry.target;
          
          if (this.gsapLoaded) {
            this.staggerWithGSAP(container);
          } else {
            container.classList.add('animated');
          }
          
          observer.unobserve(container);
        }
      });
    }, {
      threshold: 0.1
    });
    
    staggerContainers.forEach(el => observer.observe(el));
  },
  
  /**
   * GSAP 스태거 애니메이션
   */
  staggerWithGSAP(container) {
    const children = container.children;
    const staggerDelay = parseFloat(container.dataset.stagger) || 0.1;
    
    gsap.from(children, {
      y: 20,
      opacity: 0,
      duration: 0.5,
      ease: 'expo.out',
      stagger: staggerDelay,
      onComplete: () => container.classList.add('animated')
    });
  },
  
  /**
   * 로또볼 등장 애니메이션
   */
  animateBalls(container, balls) {
    if (this.gsapLoaded) {
      gsap.from(balls, {
        y: -100,
        opacity: 0,
        scale: 0.5,
        duration: 0.6,
        ease: 'bounce.out',
        stagger: 0.1
      });
    } else {
      balls.forEach((ball, i) => {
        ball.style.animationDelay = `${i * 100}ms`;
        ball.classList.add('lotto-ball-bounce');
      });
    }
  },
  
  /**
   * 점수 카운트업 애니메이션
   */
  animateScore(element, targetScore) {
    const duration = 1500;
    const startTime = performance.now();
    const startScore = 0;
    
    const updateScore = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      
      // Ease out quad
      const easeProgress = 1 - (1 - progress) * (1 - progress);
      const currentScore = Math.round(startScore + (targetScore - startScore) * easeProgress);
      
      element.textContent = currentScore;
      
      if (progress < 1) {
        requestAnimationFrame(updateScore);
      }
    };
    
    requestAnimationFrame(updateScore);
  },
  
  /**
   * 페이지 전환 애니메이션
   */
  pageTransition(url) {
    const main = document.querySelector('main');
    if (!main) {
      window.location.href = url;
      return;
    }
    
    if (this.gsapLoaded) {
      gsap.to(main, {
        opacity: 0,
        y: 20,
        duration: 0.3,
        ease: 'power2.in',
        onComplete: () => {
          window.location.href = url;
        }
      });
    } else {
      main.classList.add('page-exit-active');
      setTimeout(() => {
        window.location.href = url;
      }, 300);
    }
  },
  
  /**
   * 토스트 알림 애니메이션
   */
  showToast(message, type = 'success') {
    const container = document.querySelector('.toast-container') || this.createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'polite');
    toast.innerHTML = `
      <span class="toast-icon">${type === 'success' ? '✓' : '✕'}</span>
      <span class="toast-message">${message}</span>
      <button class="toast-close" aria-label="닫기">×</button>
    `;
    
    container.appendChild(toast);
    
    // 닫기 버튼
    toast.querySelector('.toast-close').addEventListener('click', () => {
      this.hideToast(toast);
    });
    
    // 자동 닫기
    setTimeout(() => this.hideToast(toast), 5000);
  },
  
  hideToast(toast) {
    if (this.gsapLoaded) {
      gsap.to(toast, {
        x: 100,
        opacity: 0,
        duration: 0.3,
        onComplete: () => toast.remove()
      });
    } else {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100px)';
      setTimeout(() => toast.remove(), 300);
    }
  },
  
  createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container';
    container.setAttribute('aria-label', '알림');
    document.body.appendChild(container);
    return container;
  }
};

// DOM 로드 시 초기화
document.addEventListener('DOMContentLoaded', () => {
  AnimationManager.init();
});

// 전역 접근
window.AnimationManager = AnimationManager;

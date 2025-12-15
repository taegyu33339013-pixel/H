/**
 * 로또인사이트 - Accessibility Utilities
 * WCAG 2.1 AA 준수를 위한 접근성 기능
 */

const A11y = {
  /**
   * 초기화
   */
  init() {
    this.setupSkipLink();
    this.setupFocusManagement();
    this.setupLiveRegions();
    this.setupKeyboardNavigation();
    this.setupReducedMotion();
    this.announcePageLoad();
    
    console.log('♿ Accessibility features initialized');
  },
  
  /**
   * 스킵 링크 설정
   */
  setupSkipLink() {
    // 스킵 링크가 없으면 생성
    if (!document.querySelector('.skip-link')) {
      const skipLink = document.createElement('a');
      skipLink.href = '#main-content';
      skipLink.className = 'skip-link';
      skipLink.textContent = '메인 콘텐츠로 건너뛰기';
      document.body.insertBefore(skipLink, document.body.firstChild);
    }
  },
  
  /**
   * 포커스 관리
   */
  setupFocusManagement() {
    // 포커스 가시성 - 마우스 클릭 시 아웃라인 제거, 키보드 시 표시
    document.body.addEventListener('mousedown', () => {
      document.body.classList.add('using-mouse');
    });
    
    document.body.addEventListener('keydown', (e) => {
      if (e.key === 'Tab') {
        document.body.classList.remove('using-mouse');
      }
    });
    
    // 포커스 트랩 (모달용)
    this.focusTrap = {
      element: null,
      firstFocusable: null,
      lastFocusable: null,
      
      activate(element) {
        this.element = element;
        const focusables = element.querySelectorAll(
          'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        this.firstFocusable = focusables[0];
        this.lastFocusable = focusables[focusables.length - 1];
        
        if (this.firstFocusable) {
          this.firstFocusable.focus();
        }
        
        element.addEventListener('keydown', this.handleKeyDown.bind(this));
      },
      
      deactivate() {
        if (this.element) {
          this.element.removeEventListener('keydown', this.handleKeyDown.bind(this));
        }
        this.element = null;
      },
      
      handleKeyDown(e) {
        if (e.key !== 'Tab') return;
        
        if (e.shiftKey) {
          if (document.activeElement === this.firstFocusable) {
            e.preventDefault();
            this.lastFocusable.focus();
          }
        } else {
          if (document.activeElement === this.lastFocusable) {
            e.preventDefault();
            this.firstFocusable.focus();
          }
        }
      }
    };
  },
  
  /**
   * ARIA Live Region 설정
   */
  setupLiveRegions() {
    // 동적 알림을 위한 live region 생성
    if (!document.getElementById('aria-live-polite')) {
      const polite = document.createElement('div');
      polite.id = 'aria-live-polite';
      polite.setAttribute('aria-live', 'polite');
      polite.setAttribute('aria-atomic', 'true');
      polite.className = 'sr-only';
      document.body.appendChild(polite);
    }
    
    if (!document.getElementById('aria-live-assertive')) {
      const assertive = document.createElement('div');
      assertive.id = 'aria-live-assertive';
      assertive.setAttribute('aria-live', 'assertive');
      assertive.setAttribute('aria-atomic', 'true');
      assertive.className = 'sr-only';
      document.body.appendChild(assertive);
    }
  },
  
  /**
   * 스크린리더에 메시지 전달
   */
  announce(message, priority = 'polite') {
    const region = document.getElementById(`aria-live-${priority}`);
    if (region) {
      region.textContent = '';
      // 약간의 딜레이로 스크린리더가 변경을 감지하도록
      setTimeout(() => {
        region.textContent = message;
      }, 100);
    }
  },
  
  /**
   * 키보드 네비게이션 설정
   */
  setupKeyboardNavigation() {
    // 드롭다운/메뉴 키보드 네비게이션
    document.addEventListener('keydown', (e) => {
      // ESC로 모달/드롭다운 닫기
      if (e.key === 'Escape') {
        const openModal = document.querySelector('.modal[aria-hidden="false"]');
        if (openModal) {
          this.closeModal(openModal);
        }
        
        const openDropdown = document.querySelector('[aria-expanded="true"]');
        if (openDropdown) {
          this.closeDropdown(openDropdown);
        }
      }
    });
    
    // 화살표 키로 메뉴 탐색
    document.querySelectorAll('[role="menu"]').forEach(menu => {
      menu.addEventListener('keydown', (e) => {
        const items = menu.querySelectorAll('[role="menuitem"]');
        const currentIndex = Array.from(items).indexOf(document.activeElement);
        
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          const nextIndex = (currentIndex + 1) % items.length;
          items[nextIndex].focus();
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          const prevIndex = (currentIndex - 1 + items.length) % items.length;
          items[prevIndex].focus();
        } else if (e.key === 'Home') {
          e.preventDefault();
          items[0].focus();
        } else if (e.key === 'End') {
          e.preventDefault();
          items[items.length - 1].focus();
        }
      });
    });
  },
  
  /**
   * Reduced Motion 설정
   */
  setupReducedMotion() {
    const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    
    const handleReducedMotion = (e) => {
      if (e.matches) {
        document.body.classList.add('reduced-motion');
      } else {
        document.body.classList.remove('reduced-motion');
      }
    };
    
    handleReducedMotion(mediaQuery);
    mediaQuery.addEventListener('change', handleReducedMotion);
  },
  
  /**
   * 페이지 로드 알림
   */
  announcePageLoad() {
    const pageTitle = document.title;
    this.announce(`${pageTitle} 페이지가 로드되었습니다.`);
  },
  
  /**
   * 모달 열기
   */
  openModal(modal) {
    const backdrop = document.querySelector('.modal-backdrop');
    
    // 현재 포커스 저장
    this.previousFocus = document.activeElement;
    
    // 모달 표시
    modal.setAttribute('aria-hidden', 'false');
    if (backdrop) {
      backdrop.setAttribute('aria-hidden', 'false');
    }
    
    // 배경 스크롤 방지
    document.body.style.overflow = 'hidden';
    
    // 포커스 트랩 활성화
    this.focusTrap.activate(modal);
    
    // 알림
    const title = modal.querySelector('.modal-title');
    this.announce(`${title?.textContent || '대화상자'}가 열렸습니다.`);
  },
  
  /**
   * 모달 닫기
   */
  closeModal(modal) {
    const backdrop = document.querySelector('.modal-backdrop');
    
    modal.setAttribute('aria-hidden', 'true');
    if (backdrop) {
      backdrop.setAttribute('aria-hidden', 'true');
    }
    
    document.body.style.overflow = '';
    
    // 포커스 트랩 비활성화
    this.focusTrap.deactivate();
    
    // 이전 포커스로 복귀
    if (this.previousFocus) {
      this.previousFocus.focus();
    }
    
    this.announce('대화상자가 닫혔습니다.');
  },
  
  /**
   * 드롭다운 닫기
   */
  closeDropdown(trigger) {
    trigger.setAttribute('aria-expanded', 'false');
    const menu = document.getElementById(trigger.getAttribute('aria-controls'));
    if (menu) {
      menu.setAttribute('aria-hidden', 'true');
    }
  },
  
  /**
   * 로딩 상태 알림
   */
  announceLoading(isLoading, context = '') {
    if (isLoading) {
      this.announce(`${context} 로딩 중입니다. 잠시만 기다려 주세요.`, 'polite');
    } else {
      this.announce(`${context} 로딩이 완료되었습니다.`, 'polite');
    }
  },
  
  /**
   * 에러 알림
   */
  announceError(message) {
    this.announce(`오류: ${message}`, 'assertive');
  },
  
  /**
   * 결과 알림 (로또 번호 등)
   */
  announceResults(numbers, score) {
    const numbersText = numbers.join(', ');
    this.announce(
      `AI 분석 결과: ${numbersText}. 균형 점수 ${score}점입니다.`,
      'polite'
    );
  }
};

// 포커스 아웃라인 스타일 (using-mouse 클래스일 때 숨김)
const focusStyle = document.createElement('style');
focusStyle.textContent = `
  body.using-mouse *:focus {
    outline: none !important;
  }
  
  body:not(.using-mouse) *:focus-visible {
    outline: none;
    box-shadow: var(--focus-ring);
  }
`;
document.head.appendChild(focusStyle);

// DOM 로드 시 초기화
document.addEventListener('DOMContentLoaded', () => {
  A11y.init();
});

// 전역 접근
window.A11y = A11y;

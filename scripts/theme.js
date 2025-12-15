/**
 * 로또인사이트 - Theme Toggle System
 * 다크/라이트 모드 전환
 */

const ThemeManager = {
  STORAGE_KEY: 'lottoinsight-theme',
  THEMES: {
    DARK: 'dark',
    LIGHT: 'light',
    SYSTEM: 'system'
  },
  
  /**
   * 초기화
   */
  init() {
    // 저장된 테마 또는 시스템 설정 적용
    const savedTheme = this.getSavedTheme();
    this.applyTheme(savedTheme);
    
    // 시스템 테마 변경 감지
    this.watchSystemTheme();
    
    // 토글 버튼 이벤트 바인딩
    this.bindToggleButtons();
    
    console.log('🎨 Theme Manager initialized:', this.getCurrentTheme());
  },
  
  /**
   * 저장된 테마 가져오기
   */
  getSavedTheme() {
    try {
      return localStorage.getItem(this.STORAGE_KEY) || this.THEMES.SYSTEM;
    } catch {
      return this.THEMES.SYSTEM;
    }
  },
  
  /**
   * 테마 저장
   */
  saveTheme(theme) {
    try {
      localStorage.setItem(this.STORAGE_KEY, theme);
    } catch (e) {
      console.warn('Failed to save theme preference:', e);
    }
  },
  
  /**
   * 현재 적용된 테마 가져오기
   */
  getCurrentTheme() {
    return document.documentElement.getAttribute('data-theme') || this.THEMES.DARK;
  },
  
  /**
   * 시스템 테마 감지
   */
  getSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
      return this.THEMES.LIGHT;
    }
    return this.THEMES.DARK;
  },
  
  /**
   * 테마 적용
   */
  applyTheme(theme) {
    let actualTheme = theme;
    
    // 시스템 설정 사용 시 실제 테마 결정
    if (theme === this.THEMES.SYSTEM) {
      actualTheme = this.getSystemTheme();
    }
    
    // HTML 속성 설정
    document.documentElement.setAttribute('data-theme', actualTheme);
    
    // 메타 테마 컬러 업데이트
    this.updateMetaThemeColor(actualTheme);
    
    // 토글 버튼 상태 업데이트
    this.updateToggleButtons(actualTheme);
    
    // 커스텀 이벤트 발생
    window.dispatchEvent(new CustomEvent('themechange', { 
      detail: { theme: actualTheme } 
    }));
    
    this.saveTheme(theme);
  },
  
  /**
   * 테마 토글
   */
  toggle() {
    const current = this.getCurrentTheme();
    const newTheme = current === this.THEMES.DARK ? this.THEMES.LIGHT : this.THEMES.DARK;
    this.applyTheme(newTheme);
  },
  
  /**
   * 메타 테마 컬러 업데이트
   */
  updateMetaThemeColor(theme) {
    const metaThemeColor = document.querySelector('meta[name="theme-color"]');
    if (metaThemeColor) {
      const color = theme === this.THEMES.LIGHT ? '#ffffff' : '#030711';
      metaThemeColor.setAttribute('content', color);
    }
  },
  
  /**
   * 토글 버튼 상태 업데이트
   */
  updateToggleButtons(theme) {
    document.querySelectorAll('.theme-toggle').forEach(btn => {
      const sunIcon = btn.querySelector('.icon-sun');
      const moonIcon = btn.querySelector('.icon-moon');
      
      if (sunIcon && moonIcon) {
        if (theme === this.THEMES.LIGHT) {
          sunIcon.style.display = 'none';
          moonIcon.style.display = 'block';
        } else {
          sunIcon.style.display = 'block';
          moonIcon.style.display = 'none';
        }
      }
      
      // ARIA 레이블 업데이트
      const label = theme === this.THEMES.LIGHT ? '다크 모드로 전환' : '라이트 모드로 전환';
      btn.setAttribute('aria-label', label);
    });
  },
  
  /**
   * 토글 버튼 이벤트 바인딩
   */
  bindToggleButtons() {
    document.querySelectorAll('.theme-toggle').forEach(btn => {
      btn.addEventListener('click', () => this.toggle());
      
      // 키보드 접근성
      btn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.toggle();
        }
      });
    });
  },
  
  /**
   * 시스템 테마 변경 감지
   */
  watchSystemTheme() {
    if (window.matchMedia) {
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const savedTheme = this.getSavedTheme();
        if (savedTheme === this.THEMES.SYSTEM) {
          this.applyTheme(this.THEMES.SYSTEM);
        }
      });
    }
  }
};

// DOM 로드 시 초기화
document.addEventListener('DOMContentLoaded', () => {
  ThemeManager.init();
});

// 전역 접근
window.ThemeManager = ThemeManager;

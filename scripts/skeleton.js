/**
 * 로또인사이트 - Skeleton UI Utilities
 * 로딩 상태를 위한 스켈레톤 UI 생성
 */

const Skeleton = {
  /**
   * 기본 스켈레톤 요소 생성
   */
  create(type, options = {}) {
    const el = document.createElement('div');
    el.className = `skeleton skeleton-${type}`;
    el.setAttribute('aria-hidden', 'true');
    
    if (options.width) el.style.width = options.width;
    if (options.height) el.style.height = options.height;
    if (options.className) el.classList.add(options.className);
    
    return el;
  },
  
  /**
   * 텍스트 스켈레톤
   */
  text(lines = 3, options = {}) {
    const container = document.createElement('div');
    container.className = 'skeleton-paragraph';
    
    for (let i = 0; i < lines; i++) {
      const line = this.create('text', {
        width: i === lines - 1 ? '80%' : '100%',
        ...options
      });
      container.appendChild(line);
    }
    
    return container;
  },
  
  /**
   * 로또볼 스켈레톤
   */
  balls(count = 6, options = {}) {
    const container = document.createElement('div');
    container.className = 'skeleton-balls';
    container.setAttribute('aria-label', '로또 번호 로딩 중');
    
    for (let i = 0; i < count; i++) {
      const ball = this.create('ball', {
        ...options
      });
      container.appendChild(ball);
    }
    
    return container;
  },
  
  /**
   * 카드 스켈레톤
   */
  card(options = {}) {
    const { showHeader = true, showBody = true, lines = 3 } = options;
    
    const card = document.createElement('div');
    card.className = 'skeleton-card';
    
    if (showHeader) {
      const header = document.createElement('div');
      header.className = 'skeleton-card-header';
      
      const avatar = this.create('avatar');
      const title = this.create('text', { width: '60%' });
      
      header.appendChild(avatar);
      header.appendChild(title);
      card.appendChild(header);
    }
    
    if (showBody) {
      const body = document.createElement('div');
      body.className = 'skeleton-card-body';
      body.appendChild(this.text(lines));
      card.appendChild(body);
    }
    
    return card;
  },
  
  /**
   * 결과 페이지 스켈레톤
   */
  result() {
    const container = document.createElement('div');
    container.className = 'skeleton-result';
    container.setAttribute('role', 'status');
    container.setAttribute('aria-label', '분석 결과 로딩 중');
    
    // 헤더
    const header = document.createElement('div');
    header.className = 'skeleton-result-header';
    header.appendChild(this.create('heading'));
    header.appendChild(this.create('text', { width: '100px' }));
    container.appendChild(header);
    
    // 번호 영역
    const numbers = document.createElement('div');
    numbers.className = 'skeleton-result-numbers';
    numbers.appendChild(this.balls(6));
    container.appendChild(numbers);
    
    // 통계 영역
    const stats = document.createElement('div');
    stats.className = 'skeleton-result-stats';
    
    for (let i = 0; i < 3; i++) {
      const stat = document.createElement('div');
      stat.className = 'skeleton-stat';
      
      const value = this.create('text', { width: '80px', height: '40px' });
      value.className = 'skeleton skeleton-stat-value';
      
      const label = this.create('text', { width: '60px', height: '14px' });
      label.className = 'skeleton skeleton-stat-label';
      
      stat.appendChild(value);
      stat.appendChild(label);
      stats.appendChild(stat);
    }
    
    container.appendChild(stats);
    
    return container;
  },
  
  /**
   * 테이블 스켈레톤
   */
  table(rows = 5, cols = 4) {
    const container = document.createElement('div');
    container.className = 'skeleton-table';
    
    for (let i = 0; i < rows; i++) {
      const row = document.createElement('div');
      row.className = 'skeleton-table-row';
      
      for (let j = 0; j < cols; j++) {
        const cell = this.create('text');
        cell.className = 'skeleton skeleton-table-cell';
        row.appendChild(cell);
      }
      
      container.appendChild(row);
    }
    
    return container;
  },
  
  /**
   * 컨테이너에 스켈레톤 표시
   */
  show(container, type, options = {}) {
    // 기존 콘텐츠 저장
    container.dataset.originalContent = container.innerHTML;
    container.classList.add('skeleton-loading');
    
    // 스켈레톤 생성
    let skeleton;
    switch (type) {
      case 'result':
        skeleton = this.result();
        break;
      case 'card':
        skeleton = this.card(options);
        break;
      case 'balls':
        skeleton = this.balls(options.count || 6, options);
        break;
      case 'table':
        skeleton = this.table(options.rows, options.cols);
        break;
      case 'text':
        skeleton = this.text(options.lines || 3, options);
        break;
      default:
        skeleton = this.create('text');
    }
    
    container.innerHTML = '';
    container.appendChild(skeleton);
    
    // 접근성 알림
    if (window.A11y) {
      A11y.announceLoading(true, options.context || '');
    }
  },
  
  /**
   * 스켈레톤 숨기고 원래 콘텐츠 복원
   */
  hide(container, newContent) {
    container.classList.remove('skeleton-loading');
    
    if (newContent) {
      // 새 콘텐츠로 교체
      if (typeof newContent === 'string') {
        container.innerHTML = newContent;
      } else if (newContent instanceof HTMLElement) {
        container.innerHTML = '';
        container.appendChild(newContent);
      }
    } else if (container.dataset.originalContent) {
      // 원래 콘텐츠 복원
      container.innerHTML = container.dataset.originalContent;
      delete container.dataset.originalContent;
    }
    
    // 접근성 알림
    if (window.A11y) {
      A11y.announceLoading(false);
    }
  },
  
  /**
   * 로딩 오버레이 표시
   */
  showOverlay(container, options = {}) {
    const { text = '로딩 중...' } = options;
    
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.setAttribute('role', 'status');
    overlay.setAttribute('aria-live', 'polite');
    
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    spinner.setAttribute('aria-hidden', 'true');
    
    const label = document.createElement('span');
    label.className = 'sr-only';
    label.textContent = text;
    
    overlay.appendChild(spinner);
    overlay.appendChild(label);
    
    container.style.position = 'relative';
    container.appendChild(overlay);
    
    return overlay;
  },
  
  /**
   * 로딩 오버레이 숨기기
   */
  hideOverlay(container) {
    const overlay = container.querySelector('.loading-overlay');
    if (overlay) {
      overlay.remove();
    }
  },
  
  /**
   * 버튼 로딩 상태
   */
  buttonLoading(button, isLoading) {
    if (isLoading) {
      button.dataset.originalText = button.textContent;
      button.classList.add('btn-loading');
      button.setAttribute('aria-busy', 'true');
      button.disabled = true;
    } else {
      button.classList.remove('btn-loading');
      button.setAttribute('aria-busy', 'false');
      button.disabled = false;
      if (button.dataset.originalText) {
        button.textContent = button.dataset.originalText;
        delete button.dataset.originalText;
      }
    }
  }
};

// 전역 접근
window.Skeleton = Skeleton;

/**
 * 사용 예시:
 * 
 * // 결과 스켈레톤 표시
 * Skeleton.show(resultContainer, 'result', { context: '분석 결과' });
 * 
 * // 로딩 완료 후 실제 콘텐츠로 교체
 * Skeleton.hide(resultContainer, actualContent);
 * 
 * // 버튼 로딩 상태
 * Skeleton.buttonLoading(submitBtn, true);
 * await fetchData();
 * Skeleton.buttonLoading(submitBtn, false);
 */

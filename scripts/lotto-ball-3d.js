/**
 * 로또인사이트 - 3D Lotto Ball Renderer
 * Three.js 기반 3D 볼 렌더링
 */

const LottoBall3D = {
  scene: null,
  camera: null,
  renderer: null,
  balls: [],
  animationId: null,
  container: null,
  isInitialized: false,
  
  // 볼 색상 (동행복권 기준)
  BALL_COLORS: {
    yellow: { main: 0xffd700, light: 0xffeb3b },  // 1-10
    blue: { main: 0x3b82f6, light: 0x60a5fa },    // 11-20
    red: { main: 0xef4444, light: 0xf87171 },     // 21-30
    gray: { main: 0x64748b, light: 0x94a3b8 },    // 31-40
    green: { main: 0x22c55e, light: 0x4ade80 }    // 41-45
  },
  
  /**
   * 번호에 따른 색상 반환
   */
  getColorByNumber(num) {
    if (num >= 1 && num <= 10) return this.BALL_COLORS.yellow;
    if (num >= 11 && num <= 20) return this.BALL_COLORS.blue;
    if (num >= 21 && num <= 30) return this.BALL_COLORS.red;
    if (num >= 31 && num <= 40) return this.BALL_COLORS.gray;
    return this.BALL_COLORS.green;
  },
  
  /**
   * Three.js 사용 가능 여부 체크
   */
  isWebGLAvailable() {
    try {
      const canvas = document.createElement('canvas');
      return !!(window.WebGLRenderingContext && 
        (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
    } catch (e) {
      return false;
    }
  },
  
  /**
   * 초기화
   */
  init(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
      console.warn('3D ball container not found:', containerId);
      return false;
    }
    
    this.container = container;
    
    // WebGL 지원 체크
    if (!this.isWebGLAvailable()) {
      console.warn('WebGL not supported, using CSS fallback');
      document.body.classList.add('no-webgl');
      return false;
    }
    
    // Three.js 로드 체크
    if (typeof THREE === 'undefined') {
      console.warn('Three.js not loaded, using CSS fallback');
      return false;
    }
    
    try {
      this.setupScene();
      this.setupLights();
      this.setupRenderer();
      this.addEventListeners();
      this.isInitialized = true;
      
      console.log('🎱 3D Ball Renderer initialized');
      return true;
    } catch (e) {
      console.error('Failed to initialize 3D renderer:', e);
      return false;
    }
  },
  
  /**
   * 씬 설정
   */
  setupScene() {
    this.scene = new THREE.Scene();
    
    // 카메라 설정
    const aspect = this.container.clientWidth / this.container.clientHeight;
    this.camera = new THREE.PerspectiveCamera(45, aspect, 0.1, 1000);
    this.camera.position.z = 15;
  },
  
  /**
   * 조명 설정
   */
  setupLights() {
    // 환경광
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    this.scene.add(ambientLight);
    
    // 메인 조명
    const mainLight = new THREE.DirectionalLight(0xffffff, 0.8);
    mainLight.position.set(5, 5, 5);
    this.scene.add(mainLight);
    
    // 보조 조명
    const fillLight = new THREE.DirectionalLight(0xffffff, 0.3);
    fillLight.position.set(-5, -5, 5);
    this.scene.add(fillLight);
  },
  
  /**
   * 렌더러 설정
   */
  setupRenderer() {
    this.renderer = new THREE.WebGLRenderer({ 
      antialias: true, 
      alpha: true 
    });
    this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
    this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    this.renderer.setClearColor(0x000000, 0);
    
    // 캔버스에 클래스 추가
    this.renderer.domElement.className = 'ball-3d-canvas';
    this.container.appendChild(this.renderer.domElement);
  },
  
  /**
   * 3D 볼 생성
   */
  createBall(number, position = { x: 0, y: 0, z: 0 }) {
    const colors = this.getColorByNumber(number);
    
    // 볼 지오메트리
    const geometry = new THREE.SphereGeometry(1, 64, 64);
    
    // 머티리얼 (광택 있는 플라스틱 느낌)
    const material = new THREE.MeshPhongMaterial({
      color: colors.main,
      specular: 0xffffff,
      shininess: 100,
      reflectivity: 0.5
    });
    
    const ball = new THREE.Mesh(geometry, material);
    ball.position.set(position.x, position.y, position.z);
    
    // 번호 텍스처 (캔버스 기반)
    this.addNumberTexture(ball, number);
    
    // 메타데이터
    ball.userData = { number, colors };
    
    return ball;
  },
  
  /**
   * 번호 텍스처 추가
   */
  addNumberTexture(ball, number) {
    const canvas = document.createElement('canvas');
    canvas.width = 256;
    canvas.height = 256;
    const ctx = canvas.getContext('2d');
    
    // 흰색 원
    ctx.fillStyle = '#ffffff';
    ctx.beginPath();
    ctx.arc(128, 128, 80, 0, Math.PI * 2);
    ctx.fill();
    
    // 번호 텍스트
    ctx.fillStyle = '#1a1a1a';
    ctx.font = 'bold 80px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(number.toString(), 128, 128);
    
    // 텍스처 생성
    const texture = new THREE.CanvasTexture(canvas);
    
    // 번호 표시를 위한 데칼 또는 스프라이트 (간단한 구현)
    const spriteMaterial = new THREE.SpriteMaterial({ 
      map: texture,
      transparent: true 
    });
    const sprite = new THREE.Sprite(spriteMaterial);
    sprite.scale.set(0.8, 0.8, 1);
    sprite.position.z = 0.9;
    
    ball.add(sprite);
  },
  
  /**
   * 볼 배치
   */
  displayBalls(numbers) {
    // 기존 볼 제거
    this.clearBalls();
    
    const spacing = 2.5;
    const startX = -((numbers.length - 1) * spacing) / 2;
    
    numbers.forEach((num, index) => {
      const ball = this.createBall(num, {
        x: startX + index * spacing,
        y: 0,
        z: 0
      });
      
      this.balls.push(ball);
      this.scene.add(ball);
    });
    
    // 등장 애니메이션
    this.animateEntrance();
  },
  
  /**
   * 볼 제거
   */
  clearBalls() {
    this.balls.forEach(ball => {
      this.scene.remove(ball);
      ball.geometry.dispose();
      ball.material.dispose();
    });
    this.balls = [];
  },
  
  /**
   * 등장 애니메이션
   */
  animateEntrance() {
    this.balls.forEach((ball, index) => {
      const targetY = ball.position.y;
      ball.position.y = 10;
      ball.scale.set(0, 0, 0);
      
      // GSAP 사용 가능 시
      if (typeof gsap !== 'undefined') {
        gsap.to(ball.position, {
          y: targetY,
          duration: 0.8,
          delay: index * 0.1,
          ease: 'bounce.out'
        });
        
        gsap.to(ball.scale, {
          x: 1, y: 1, z: 1,
          duration: 0.5,
          delay: index * 0.1,
          ease: 'back.out'
        });
      } else {
        // 간단한 애니메이션
        setTimeout(() => {
          ball.position.y = targetY;
          ball.scale.set(1, 1, 1);
        }, index * 100);
      }
    });
  },
  
  /**
   * 애니메이션 루프
   */
  animate() {
    this.animationId = requestAnimationFrame(() => this.animate());
    
    // 볼 회전
    this.balls.forEach(ball => {
      ball.rotation.y += 0.005;
    });
    
    this.renderer.render(this.scene, this.camera);
  },
  
  /**
   * 시작
   */
  start() {
    if (!this.isInitialized) return;
    this.animate();
  },
  
  /**
   * 정지
   */
  stop() {
    if (this.animationId) {
      cancelAnimationFrame(this.animationId);
      this.animationId = null;
    }
  },
  
  /**
   * 리사이즈 핸들러
   */
  handleResize() {
    if (!this.isInitialized) return;
    
    const width = this.container.clientWidth;
    const height = this.container.clientHeight;
    
    this.camera.aspect = width / height;
    this.camera.updateProjectionMatrix();
    this.renderer.setSize(width, height);
  },
  
  /**
   * 이벤트 리스너
   */
  addEventListeners() {
    window.addEventListener('resize', () => this.handleResize());
    
    // 페이지 가시성 변경 시 애니메이션 제어
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        this.stop();
      } else {
        this.start();
      }
    });
  },
  
  /**
   * 정리
   */
  dispose() {
    this.stop();
    this.clearBalls();
    
    if (this.renderer) {
      this.renderer.dispose();
      this.container.removeChild(this.renderer.domElement);
    }
    
    this.scene = null;
    this.camera = null;
    this.renderer = null;
    this.isInitialized = false;
  }
};

// 전역 접근
window.LottoBall3D = LottoBall3D;

// CSS Fallback 컴포넌트
const LottoBallCSS = {
  /**
   * CSS 기반 볼 생성
   */
  createBall(number) {
    const color = this.getColorClass(number);
    
    const ball = document.createElement('div');
    ball.className = `lotto-ball lotto-ball-${color}`;
    ball.setAttribute('data-color', color);
    ball.setAttribute('role', 'img');
    ball.setAttribute('aria-label', `로또 번호 ${number}`);
    ball.textContent = number;
    
    return ball;
  },
  
  /**
   * 색상 클래스 반환
   */
  getColorClass(num) {
    if (num >= 1 && num <= 10) return 'yellow';
    if (num >= 11 && num <= 20) return 'blue';
    if (num >= 21 && num <= 30) return 'red';
    if (num >= 31 && num <= 40) return 'gray';
    return 'green';
  },
  
  /**
   * 볼 컨테이너에 렌더링
   */
  render(container, numbers, options = {}) {
    const { animate = true, showBonus = false, bonusNumber = null } = options;
    
    container.innerHTML = '';
    container.className = 'ball-container';
    
    numbers.forEach((num, index) => {
      const ball = this.createBall(num);
      
      if (animate) {
        ball.style.animationDelay = `${index * 100}ms`;
        ball.classList.add('lotto-ball-bounce');
      }
      
      container.appendChild(ball);
    });
    
    // 보너스 번호
    if (showBonus && bonusNumber) {
      const separator = document.createElement('span');
      separator.className = 'ball-separator';
      separator.textContent = '+';
      separator.setAttribute('aria-hidden', 'true');
      container.appendChild(separator);
      
      const bonusBall = this.createBall(bonusNumber);
      bonusBall.classList.add('lotto-ball-bonus');
      bonusBall.setAttribute('aria-label', `보너스 번호 ${bonusNumber}`);
      
      if (animate) {
        bonusBall.style.animationDelay = `${numbers.length * 100}ms`;
        bonusBall.classList.add('lotto-ball-bounce');
      }
      
      container.appendChild(bonusBall);
    }
  }
};

// 전역 접근
window.LottoBallCSS = LottoBallCSS;

/**
 * 그누보드 API 연동 헬퍼
 * 로또인사이트 - 그누보드 회원/포인트 시스템 연동
 */

const GnuboardAPI = {
  // 기본 URL (PHP에서 주입되거나 상대경로 사용)
  baseUrl: typeof G5_URL !== 'undefined' ? G5_URL : '',
  
  /**
   * 로그인 상태 확인
   */
  isLoggedIn: function() {
    return typeof G5_IS_MEMBER !== 'undefined' ? G5_IS_MEMBER : false;
  },
  
  /**
   * 회원 정보 가져오기
   */
  getMemberInfo: function() {
    if (typeof G5_MEMBER_ID !== 'undefined' && G5_MEMBER_ID) {
      return {
        id: G5_MEMBER_ID,
        name: typeof G5_MEMBER_NAME !== 'undefined' ? G5_MEMBER_NAME : '',
        isLoggedIn: true
      };
    }
    return { isLoggedIn: false };
  },
  
  /**
   * 크레딧(포인트) 조회
   */
  async getCredits() {
    try {
      const response = await fetch(`${this.baseUrl}/api/get_credits.php`, {
        method: 'GET',
        credentials: 'include'
      });
      
      if (!response.ok) throw new Error('Network error');
      
      const data = await response.json();
      
      if (data.success) {
        // localStorage 동기화
        localStorage.setItem('userCredits', data.credits);
        localStorage.setItem('analysisCount', data.analysis_count);
      }
      
      return data;
    } catch (error) {
      console.error('Failed to get credits:', error);
      return { success: false, error: error.message };
    }
  },
  
  /**
   * 크레딧 사용 (분석 실행)
   */
  async useCredit() {
    try {
      const response = await fetch(`${this.baseUrl}/api/use_credit.php`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      if (!response.ok) throw new Error('Network error');
      
      const data = await response.json();
      
      if (data.success) {
        // localStorage 업데이트
        localStorage.setItem('userCredits', data.remaining_credits);
        localStorage.setItem('analysisCount', data.analysis_count);
      }
      
      return data;
    } catch (error) {
      console.error('Failed to use credit:', error);
      return { success: false, error: error.message };
    }
  },
  
  /**
   * 분석 결과 저장
   */
  async saveAnalysis(numbers, round, score, strategy) {
    try {
      const response = await fetch(`${this.baseUrl}/api/save_analysis.php`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          numbers: numbers,
          round: round,
          score: score,
          strategy: strategy
        })
      });
      
      if (!response.ok) throw new Error('Network error');
      
      return await response.json();
    } catch (error) {
      console.error('Failed to save analysis:', error);
      return { success: false, error: error.message };
    }
  },
  
  /**
   * 분석 히스토리 조회
   */
  async getHistory(page = 1, limit = 20) {
    try {
      const response = await fetch(
        `${this.baseUrl}/api/get_history.php?page=${page}&limit=${limit}`,
        {
          method: 'GET',
          credentials: 'include'
        }
      );
      
      if (!response.ok) throw new Error('Network error');
      
      return await response.json();
    } catch (error) {
      console.error('Failed to get history:', error);
      return { success: false, error: error.message, history: [] };
    }
  },
  
  /**
   * 카카오 로그인 페이지로 이동
   */
  kakaoLogin(returnUrl) {
    if (returnUrl) {
      // 세션에 리턴 URL 저장 (서버에서 처리)
      document.cookie = `kakao_return_url=${encodeURIComponent(returnUrl)}; path=/`;
    }
    window.location.href = `${this.baseUrl}/kakao_login.php`;
  },
  
  /**
   * 로그아웃
   */
  logout(returnUrl) {
    const url = returnUrl 
      ? `${this.baseUrl}/kakao_logout.php?return_url=${encodeURIComponent(returnUrl)}`
      : `${this.baseUrl}/kakao_logout.php`;
    
    // localStorage 클리어
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('userName');
    localStorage.removeItem('userId');
    localStorage.removeItem('userCredits');
    localStorage.removeItem('analysisCount');
    
    window.location.href = url;
  },
  
  /**
   * 초기화 - 페이지 로드 시 실행
   */
  async init() {
    // 로그인 상태 동기화
    if (this.isLoggedIn()) {
      const memberInfo = this.getMemberInfo();
      localStorage.setItem('isLoggedIn', 'true');
      localStorage.setItem('userId', memberInfo.id);
      localStorage.setItem('userName', memberInfo.name);
      
      // 크레딧 정보 가져오기
      await this.getCredits();
    } else {
      // 비로그인 상태
      localStorage.removeItem('isLoggedIn');
    }
  }
};

// 페이지 로드 시 자동 초기화
document.addEventListener('DOMContentLoaded', function() {
  GnuboardAPI.init();
});

// 전역 접근 가능하도록 export
window.GnuboardAPI = GnuboardAPI;


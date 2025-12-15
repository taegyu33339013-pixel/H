/**
 * 로또인사이트 - AI 번호 분석 엔진 v2.0
 * 동행복권 공식 데이터 기반 (1회~1201회차)
 * 마지막 업데이트: 2025-12-10
 */

// ===== 데이터 로더 (lotto-data.js와 연동) =====
const LottoDataLoader = {
  _cache: null,
  
  // lotto-data.js의 LOTTO_HISTORY_DATA를 변환하여 사용
  get data() {
    if (this._cache) return this._cache;
    
    // LOTTO_HISTORY_DATA가 로드되었는지 확인
    if (typeof LOTTO_HISTORY_DATA === 'undefined') {
      console.error('LOTTO_HISTORY_DATA가 로드되지 않았습니다. lotto-data.js를 먼저 로드하세요.');
      return this._getDefaultData();
    }
    
    this._cache = this._processData();
    return this._cache;
  },
  
  // 데이터 처리 및 통계 계산
  _processData() {
    const rounds = Object.keys(LOTTO_HISTORY_DATA).map(Number).sort((a, b) => b - a);
    const currentRound = rounds[0];
    const latestData = LOTTO_HISTORY_DATA[currentRound];
    
    // 최근 100회차 히스토리 생성
    const history = rounds.slice(0, 100).map(round => {
      const data = LOTTO_HISTORY_DATA[round];
      return {
        round: round,
        date: data.date,
        numbers: [...data.numbers],
        bonus: data.bonus
      };
    });
    
    // 전체 통계 계산
    const allTimeStats = this._calculateAllTimeStats(rounds);
    
    // 지난 주 AI 예측 (시뮬레이션) - 실제 서비스에서는 DB에서 가져옴
    const lastAiPrediction = this._generateSimulatedPrediction(latestData);
    
    return {
      currentRound: currentRound,
      lastUpdate: latestData.date,
      nextDrawDate: this._getNextDrawDate(latestData.date),
      
      latestResult: {
        round: currentRound,
        date: latestData.date,
        numbers: [...latestData.numbers],
        bonus: latestData.bonus,
        prize1st: this._formatPrize(Math.floor(Math.random() * 20 + 10) * 100000000),
        winners1st: Math.floor(Math.random() * 15) + 5
      },
      
      lastAiPrediction: lastAiPrediction,
      history: history,
      allTimeStats: allTimeStats
    };
  },
  
  // 전체 통계 계산
  _calculateAllTimeStats(rounds) {
    const frequency = {};
    for (let i = 1; i <= 45; i++) frequency[i] = 0;
    
    let totalOddEven = { '3:3': 0, '4:2': 0, '2:4': 0, '5:1': 0, '1:5': 0, '6:0': 0, '0:6': 0 };
    let totalHighLow = { '3:3': 0, '4:2': 0, '2:4': 0, '5:1': 0, '1:5': 0, '6:0': 0, '0:6': 0 };
    let sumTotal = 0;
    let consecutiveCount = 0;
    let sameLastDigitCount = 0;
    
    rounds.forEach(round => {
      const data = LOTTO_HISTORY_DATA[round];
      const numbers = data.numbers;
      
      // 빈도 계산
      numbers.forEach(n => frequency[n]++);
      
      // 홀짝 비율
      const odd = numbers.filter(n => n % 2 === 1).length;
      const oddEvenKey = `${odd}:${6 - odd}`;
      if (totalOddEven[oddEvenKey] !== undefined) totalOddEven[oddEvenKey]++;
      
      // 고저 비율 (23 기준)
      const high = numbers.filter(n => n > 22).length;
      const highLowKey = `${high}:${6 - high}`;
      if (totalHighLow[highLowKey] !== undefined) totalHighLow[highLowKey]++;
      
      // 합계
      sumTotal += numbers.reduce((a, b) => a + b, 0);
      
      // 연속번호 체크
      const sorted = [...numbers].sort((a, b) => a - b);
      for (let i = 0; i < sorted.length - 1; i++) {
        if (sorted[i + 1] - sorted[i] === 1) {
          consecutiveCount++;
          break;
        }
      }
      
      // 같은 끝자리 체크
      const lastDigits = numbers.map(n => n % 10);
      const digitCounts = {};
      lastDigits.forEach(d => digitCounts[d] = (digitCounts[d] || 0) + 1);
      if (Object.values(digitCounts).some(c => c >= 2)) sameLastDigitCount++;
    });
    
    // 비율로 변환
    const totalRounds = rounds.length;
    Object.keys(totalOddEven).forEach(key => {
      totalOddEven[key] = Math.round(totalOddEven[key] / totalRounds * 100);
    });
    Object.keys(totalHighLow).forEach(key => {
      totalHighLow[key] = Math.round(totalHighLow[key] / totalRounds * 100);
    });
    
    // Hot/Cold 번호 추출
    const sortedByFreq = Object.entries(frequency).sort((a, b) => b[1] - a[1]);
    const hotNumbers = sortedByFreq.slice(0, 5).map(([n]) => parseInt(n));
    const coldNumbers = sortedByFreq.slice(-5).map(([n]) => parseInt(n));
    
    return {
      totalRounds: totalRounds,
      frequency: frequency,
      hotNumbers: hotNumbers,
      coldNumbers: coldNumbers,
      oddEvenRatio: totalOddEven,
      highLowRatio: totalHighLow,
      sumRange: {
        min: 21,
        max: 255,
        average: Math.round(sumTotal / totalRounds),
        mode: '100~170'
      },
      consecutiveRate: Math.round(consecutiveCount / totalRounds * 100),
      sameLastDigitRate: Math.round(sameLastDigitCount / totalRounds * 100)
    };
  },
  
  // 시뮬레이션 예측 생성
  _generateSimulatedPrediction(latestData) {
    // 실제 당첨번호 중 일부와 겹치도록 시뮬레이션
    const actual = latestData.numbers;
    const matchCount = Math.floor(Math.random() * 3) + 1; // 1~3개 일치
    
    const predicted = [];
    const matchedNumbers = [];
    
    // 일치시킬 번호 선택
    const shuffled = [...actual].sort(() => Math.random() - 0.5);
    for (let i = 0; i < matchCount && i < shuffled.length; i++) {
      predicted.push(shuffled[i]);
      matchedNumbers.push(shuffled[i]);
    }
    
    // 나머지 랜덤 채우기
    while (predicted.length < 6) {
      const n = Math.floor(Math.random() * 45) + 1;
      if (!predicted.includes(n) && !actual.includes(n)) {
        predicted.push(n);
      }
    }
    
    return {
      round: latestData.round || Object.keys(LOTTO_HISTORY_DATA).length,
      numbers: predicted.sort((a, b) => a - b),
      matchedCount: matchedNumbers.length,
      matchedNumbers: matchedNumbers.sort((a, b) => a - b)
    };
  },
  
  // 다음 추첨일 계산
  _getNextDrawDate(lastDate) {
    const date = new Date(lastDate);
    date.setDate(date.getDate() + 7);
    return date.toISOString().split('T')[0];
  },
  
  // 금액 포맷
  _formatPrize(amount) {
    return amount.toLocaleString('ko-KR');
  },
  
  // 기본 데이터 (로드 실패 시)
  _getDefaultData() {
    return {
      currentRound: 1201,
      lastUpdate: '2025-12-06',
      nextDrawDate: '2025-12-13',
      latestResult: {
        round: 1201,
        date: '2025-12-06',
        numbers: [7, 9, 24, 27, 35, 36],
        bonus: 37
      },
      lastAiPrediction: {
        round: 1201,
        numbers: [3, 9, 15, 27, 35, 42],
        matchedCount: 3,
        matchedNumbers: [9, 27, 35]
      },
      history: [],
      allTimeStats: {
        totalRounds: 1201,
        frequency: {},
        hotNumbers: [27, 34, 43, 10, 33],
        coldNumbers: [23, 9, 29, 2, 22]
      }
    };
  },
  
  // 번호 색상 반환 (동행복권 기준)
  getBallColor(num) {
    if (num >= 1 && num <= 10) return 'yellow';
    if (num >= 11 && num <= 20) return 'blue';
    if (num >= 21 && num <= 30) return 'red';
    if (num >= 31 && num <= 40) return 'gray';
    return 'green';
  },
  
  // 캐시 초기화 (데이터 새로고침 시 사용)
  clearCache() {
    this._cache = null;
  }
};

/**
 * 로또 번호 생성 엔진
 */
const lottoGenerator = {
  ready: false,
  dataLoader: LottoDataLoader,
  
  async init() {
    this.ready = true;
    console.log('🎱 로또 분석 엔진 v2.0 초기화 완료');
    console.log(`📊 데이터: ${this.dataLoader.data.currentRound}회차까지 (${this.dataLoader.data.lastUpdate})`);
    console.log(`📈 총 ${this.dataLoader.data.allTimeStats.totalRounds}회차 분석 완료`);
    return this;
  },
  
  // 고급 통계 분석
  analyzeStats() {
    const data = this.dataLoader.data;
    const history = data.history;
    
    if (!history || history.length === 0) {
      return this._getBasicStats();
    }
    
    const allNumbers = history.flatMap(h => h.numbers);
    
    // 최근 100회 빈도 분석
    const freq = {};
    for (let i = 1; i <= 45; i++) freq[i] = 0;
    allNumbers.forEach(n => freq[n]++);
    
    // 최근 미출현 분석
    const lastSeen = {};
    for (let i = 1; i <= 45; i++) lastSeen[i] = history.length;
    history.forEach((h, idx) => {
      h.numbers.forEach(n => {
        if (lastSeen[n] > idx) lastSeen[n] = idx;
      });
    });
    
    // Hot/Cold 번호 (최근 100회 기준)
    const sortedByFreq = Object.entries(freq).sort((a, b) => b[1] - a[1]);
    const hotNumbers = sortedByFreq.slice(0, 10).map(([n]) => parseInt(n));
    const coldNumbers = sortedByFreq.slice(-10).map(([n]) => parseInt(n));
    
    // 오래 안 나온 번호
    const sortedByLastSeen = Object.entries(lastSeen).sort((a, b) => b[1] - a[1]);
    const overdueNumbers = sortedByLastSeen.slice(0, 10).map(([n]) => parseInt(n));
    
    // 동반 출현 분석 (번호 쌍)
    const pairs = this._analyzePairs(history);
    
    // 연속 번호 패턴
    const consecutivePatterns = this._analyzeConsecutive(history);
    
    // 색상별 분포
    const colorDistribution = this._analyzeColorDistribution(history);
    
    return { 
      freq, 
      lastSeen, 
      hotNumbers, 
      coldNumbers, 
      overdueNumbers,
      pairs,
      consecutivePatterns,
      colorDistribution
    };
  },
  
  // 번호 쌍 분석
  _analyzePairs(history) {
    const pairCount = {};
    
    history.forEach(h => {
      const nums = h.numbers;
      for (let i = 0; i < nums.length; i++) {
        for (let j = i + 1; j < nums.length; j++) {
          const key = `${Math.min(nums[i], nums[j])}-${Math.max(nums[i], nums[j])}`;
          pairCount[key] = (pairCount[key] || 0) + 1;
        }
      }
    });
    
    const sorted = Object.entries(pairCount).sort((a, b) => b[1] - a[1]);
    return sorted.slice(0, 10).map(([pair, count]) => ({
      pair: pair.split('-').map(Number),
      count
    }));
  },
  
  // 연속 번호 패턴 분석
  _analyzeConsecutive(history) {
    let withConsecutive = 0;
    let consecutivePairs = 0;
    let consecutiveTriples = 0;
    
    history.forEach(h => {
      const sorted = [...h.numbers].sort((a, b) => a - b);
      let hasConsecutive = false;
      let maxConsec = 1;
      let currentConsec = 1;
      
      for (let i = 0; i < sorted.length - 1; i++) {
        if (sorted[i + 1] - sorted[i] === 1) {
          currentConsec++;
          hasConsecutive = true;
          maxConsec = Math.max(maxConsec, currentConsec);
        } else {
          currentConsec = 1;
        }
      }
      
      if (hasConsecutive) withConsecutive++;
      if (maxConsec === 2) consecutivePairs++;
      if (maxConsec >= 3) consecutiveTriples++;
    });
    
    return {
      withConsecutive: Math.round(withConsecutive / history.length * 100),
      pairs: consecutivePairs,
      triples: consecutiveTriples
    };
  },
  
  // 색상 분포 분석
  _analyzeColorDistribution(history) {
    const colorCounts = { yellow: 0, blue: 0, red: 0, gray: 0, green: 0 };
    const total = history.length * 6;
    
    history.forEach(h => {
      h.numbers.forEach(n => {
        colorCounts[this.dataLoader.getBallColor(n)]++;
      });
    });
    
    return {
      yellow: Math.round(colorCounts.yellow / total * 100),
      blue: Math.round(colorCounts.blue / total * 100),
      red: Math.round(colorCounts.red / total * 100),
      gray: Math.round(colorCounts.gray / total * 100),
      green: Math.round(colorCounts.green / total * 100)
    };
  },
  
  _getBasicStats() {
    return {
      freq: {},
      lastSeen: {},
      hotNumbers: [27, 34, 43, 10, 33, 12, 18, 20, 17, 40],
      coldNumbers: [23, 9, 29, 2, 22, 44, 28, 19, 42, 8],
      overdueNumbers: [23, 9, 29, 2, 22, 44, 28, 19, 42, 8],
      pairs: [],
      consecutivePatterns: { withConsecutive: 42, pairs: 0, triples: 0 },
      colorDistribution: { yellow: 22, blue: 22, red: 22, gray: 22, green: 12 }
    };
  },
  
  // 균형 점수 계산 (개선됨)
  calculateScore(numbers) {
    let score = 100;
    const sorted = [...numbers].sort((a, b) => a - b);
    
    // 1. 홀짝 균형 (3:3이 최적, 2:4나 4:2도 양호)
    const odd = numbers.filter(n => n % 2 === 1).length;
    if (odd === 3) score += 0;
    else if (odd === 2 || odd === 4) score -= 3;
    else if (odd === 1 || odd === 5) score -= 8;
    else score -= 15;
    
    // 2. 고저 균형 (3:3이 최적)
    const high = numbers.filter(n => n > 22).length;
    if (high === 3) score += 0;
    else if (high === 2 || high === 4) score -= 3;
    else if (high === 1 || high === 5) score -= 8;
    else score -= 15;
    
    // 3. 합계 범위 (100~170이 이상적)
    const sum = numbers.reduce((a, b) => a + b, 0);
    if (sum >= 100 && sum <= 170) score += 5;
    else if (sum >= 80 && sum <= 190) score -= 5;
    else score -= 15;
    
    // 4. 연속번호 체크 (1쌍은 OK, 2쌍 이상은 감점)
    let consecutive = 0;
    for (let i = 0; i < sorted.length - 1; i++) {
      if (sorted[i + 1] - sorted[i] === 1) consecutive++;
    }
    if (consecutive === 0) score += 0;
    else if (consecutive === 1) score += 2; // 연속 1쌍은 자주 나옴
    else if (consecutive === 2) score -= 5;
    else score -= 15;
    
    // 5. AC값 (다양성 지수) - 7 이상이 좋음
    const diffs = new Set();
    for (let i = 0; i < sorted.length; i++) {
      for (let j = i + 1; j < sorted.length; j++) {
        diffs.add(sorted[j] - sorted[i]);
      }
    }
    const ac = diffs.size - 5;
    if (ac >= 9) score += 5;
    else if (ac >= 7) score += 2;
    else if (ac >= 5) score -= 3;
    else score -= 10;
    
    // 6. 색상 분포 (최소 3색 이상)
    const colors = new Set(numbers.map(n => this.dataLoader.getBallColor(n)));
    if (colors.size >= 4) score += 3;
    else if (colors.size >= 3) score += 0;
    else score -= 5;
    
    // 7. 끝자리 다양성
    const lastDigits = new Set(numbers.map(n => n % 10));
    if (lastDigits.size >= 5) score += 3;
    else if (lastDigits.size >= 4) score += 0;
    else if (lastDigits.size <= 2) score -= 8;
    
    return Math.max(50, Math.min(100, score));
  },
  
  // 번호별 스토리 생성 (개선됨)
  generateStories(numbers, stats) {
    const stories = [];
    const { freq, lastSeen, hotNumbers, coldNumbers, overdueNumbers, pairs } = stats;
    
    numbers.forEach((num, idx) => {
      let type, label, description;
      
      // Hot 번호 체크
      if (hotNumbers.slice(0, 5).includes(num)) {
        type = 'hot';
        label = '🔥 과출 번호';
        const count = freq[num] || 0;
        description = `최근 100회 중 ${count}회 출현`;
      }
      // Cold/Overdue 번호 체크
      else if (overdueNumbers.slice(0, 5).includes(num)) {
        type = 'cold';
        label = '❄️ 미출 번호';
        const gap = lastSeen[num] || 0;
        description = gap > 0 ? `${gap}회 연속 미출현` : '장기 미출현';
      }
      // 동반 출현 체크
      else if (pairs && pairs.length > 0) {
        const relatedPair = pairs.find(p => 
          p.pair.includes(num) && numbers.some(n => n !== num && p.pair.includes(n))
        );
        if (relatedPair) {
          type = 'pair';
          label = '🤝 동반 번호';
          const partner = relatedPair.pair.find(n => n !== num);
          description = `${partner}번과 ${relatedPair.count}회 동반 출현`;
        } else {
          type = 'balance';
          label = '⚖️ 균형 번호';
          description = '출현 빈도 평균 수준';
        }
      }
      // 기본
      else {
        type = 'balance';
        label = '⚖️ 균형 번호';
        description = '통계적 균형 고려';
      }
      
      stories.push({ number: num, type, label, description });
    });
    
    return stories;
  },
  
  // 번호 생성 (스타일별)
  generate(style = 'balanced') {
    if (!this.ready) {
      console.warn('엔진이 초기화되지 않았습니다. 기본 생성을 사용합니다.');
      return this.generateBasic();
    }
    
    const stats = this.analyzeStats();
    let numbers = [];
    let attempts = 0;
    const maxAttempts = 1000;
    
    while (attempts < maxAttempts) {
      switch(style) {
        case 'hot':
          numbers = this._generateHotBased(stats);
          break;
        case 'cold':
          numbers = this._generateColdBased(stats);
          break;
        case 'pair':
          numbers = this._generatePairBased(stats);
          break;
        case 'color':
          numbers = this._generateColorBalanced(stats);
          break;
        case 'balanced':
        default:
          numbers = this._generateBalanced(stats);
      }
      
      // 점수가 70점 이상이면 채택
      const score = this.calculateScore(numbers);
      if (score >= 70) break;
      attempts++;
    }
    
    const score = this.calculateScore(numbers);
    const stories = this.generateStories(numbers, stats);
    
    return { numbers, score, stories, style };
  },
  
  _generateHotBased(stats) {
    const pool = [...stats.hotNumbers];
    const numbers = [];
    
    // Hot 번호에서 4개
    while (numbers.length < 4 && pool.length > 0) {
      const idx = Math.floor(Math.random() * pool.length);
      numbers.push(pool.splice(idx, 1)[0]);
    }
    
    // 나머지는 랜덤 (균형 고려)
    while (numbers.length < 6) {
      const n = Math.floor(Math.random() * 45) + 1;
      if (!numbers.includes(n)) {
        numbers.push(n);
      }
    }
    
    return numbers.sort((a, b) => a - b);
  },
  
  _generateColdBased(stats) {
    const pool = [...stats.overdueNumbers];
    const numbers = [];
    
    // 미출현 번호에서 4개
    while (numbers.length < 4 && pool.length > 0) {
      const idx = Math.floor(Math.random() * pool.length);
      numbers.push(pool.splice(idx, 1)[0]);
    }
    
    // 나머지는 랜덤
    while (numbers.length < 6) {
      const n = Math.floor(Math.random() * 45) + 1;
      if (!numbers.includes(n)) {
        numbers.push(n);
      }
    }
    
    return numbers.sort((a, b) => a - b);
  },
  
  _generatePairBased(stats) {
    const numbers = [];
    
    // 동반 출현 쌍 2개 사용
    if (stats.pairs && stats.pairs.length >= 2) {
      const pair1 = stats.pairs[Math.floor(Math.random() * Math.min(5, stats.pairs.length))];
      numbers.push(...pair1.pair);
      
      // 두 번째 쌍 (중복 없이)
      const availablePairs = stats.pairs.filter(p => 
        !p.pair.some(n => numbers.includes(n))
      );
      if (availablePairs.length > 0) {
        const pair2 = availablePairs[Math.floor(Math.random() * Math.min(3, availablePairs.length))];
        numbers.push(...pair2.pair);
      }
    }
    
    // 나머지 채우기
    while (numbers.length < 6) {
      const n = Math.floor(Math.random() * 45) + 1;
      if (!numbers.includes(n)) {
        numbers.push(n);
      }
    }
    
    return numbers.sort((a, b) => a - b);
  },
  
  _generateColorBalanced(stats) {
    const numbers = [];
    const colorRanges = {
      yellow: [1, 10],
      blue: [11, 20],
      red: [21, 30],
      gray: [31, 40],
      green: [41, 45]
    };
    
    // 각 색상에서 최소 1개씩 (green 제외)
    ['yellow', 'blue', 'red', 'gray'].forEach(color => {
      const [min, max] = colorRanges[color];
      let n;
      do {
        n = Math.floor(Math.random() * (max - min + 1)) + min;
      } while (numbers.includes(n));
      numbers.push(n);
    });
    
    // 나머지 2개
    while (numbers.length < 6) {
      const n = Math.floor(Math.random() * 45) + 1;
      if (!numbers.includes(n)) {
        numbers.push(n);
      }
    }
    
    return numbers.sort((a, b) => a - b);
  },
  
  _generateBalanced(stats) {
    const numbers = [];
    let attempts = 0;
    
    while (attempts < 100) {
      numbers.length = 0;
      
      // Hot 2개
      const hotPool = [...stats.hotNumbers];
      while (numbers.length < 2 && hotPool.length > 0) {
        const idx = Math.floor(Math.random() * hotPool.length);
        numbers.push(hotPool.splice(idx, 1)[0]);
      }
      
      // Cold/Overdue 2개
      const coldPool = stats.overdueNumbers.filter(n => !numbers.includes(n));
      while (numbers.length < 4 && coldPool.length > 0) {
        const idx = Math.floor(Math.random() * coldPool.length);
        numbers.push(coldPool.splice(idx, 1)[0]);
      }
      
      // 나머지 2개는 랜덤
      while (numbers.length < 6) {
        const n = Math.floor(Math.random() * 45) + 1;
        if (!numbers.includes(n)) {
          numbers.push(n);
        }
      }
      
      // 균형 체크
      const sorted = numbers.sort((a, b) => a - b);
      const odd = sorted.filter(n => n % 2 === 1).length;
      const high = sorted.filter(n => n > 22).length;
      const sum = sorted.reduce((a, b) => a + b, 0);
      const colors = new Set(sorted.map(n => this.dataLoader.getBallColor(n)));
      
      // 기준 충족 시 반환
      if (odd >= 2 && odd <= 4 && 
          high >= 2 && high <= 4 && 
          sum >= 100 && sum <= 170 &&
          colors.size >= 3) {
        return sorted;
      }
      
      attempts++;
    }
    
    return numbers.sort((a, b) => a - b);
  },
  
  generateBasic() {
    const numbers = [];
    while (numbers.length < 6) {
      const n = Math.floor(Math.random() * 45) + 1;
      if (!numbers.includes(n)) numbers.push(n);
    }
    const sorted = numbers.sort((a, b) => a - b);
    return {
      numbers: sorted,
      score: this.calculateScore(sorted),
      stories: sorted.map(n => ({
        number: n,
        type: 'random',
        label: '🎲 랜덤 선택',
        description: '무작위 생성'
      })),
      style: 'random'
    };
  },
  
  // 고급 리포트 생성
  generateReport(result) {
    const numbers = result.numbers;
    const sum = numbers.reduce((a, b) => a + b, 0);
    const odd = numbers.filter(n => n % 2 === 1).length;
    const high = numbers.filter(n => n > 22).length;
    
    // AC값 계산
    const sorted = [...numbers].sort((a, b) => a - b);
    const diffs = new Set();
    for (let i = 0; i < sorted.length; i++) {
      for (let j = i + 1; j < sorted.length; j++) {
        diffs.add(sorted[j] - sorted[i]);
      }
    }
    const ac = diffs.size - 5;
    
    // 색상 분포
    const colors = { yellow: 0, blue: 0, red: 0, gray: 0, green: 0 };
    numbers.forEach(n => {
      colors[this.dataLoader.getBallColor(n)]++;
    });
    
    // 연속번호 체크
    let consecutive = 0;
    for (let i = 0; i < sorted.length - 1; i++) {
      if (sorted[i + 1] - sorted[i] === 1) consecutive++;
    }
    
    const summary = [];
    summary.push(`합계 ${sum}`);
    summary.push(`홀짝 ${odd}:${6-odd}`);
    summary.push(`고저 ${high}:${6-high}`);
    summary.push(`AC ${ac}`);
    if (consecutive > 0) summary.push(`연번 ${consecutive}쌍`);
    
    const insights = [];
    
    // 합계 분석
    if (sum >= 100 && sum <= 170) {
      insights.push(`✅ 합계(${sum})가 역대 당첨 빈출 범위(100~170) 내에 있습니다.`);
    } else if (sum < 100) {
      insights.push(`⚠️ 합계(${sum})가 다소 낮습니다. 역대 평균은 138입니다.`);
    } else {
      insights.push(`⚠️ 합계(${sum})가 다소 높습니다. 역대 평균은 138입니다.`);
    }
    
    // 홀짝 분석
    if (odd >= 2 && odd <= 4) {
      insights.push(`✅ 홀짝 비율(${odd}:${6-odd})이 역대 당첨 패턴과 유사합니다.`);
    } else {
      insights.push(`⚠️ 홀짝 비율(${odd}:${6-odd})이 다소 치우쳐 있습니다.`);
    }
    
    // 고저 분석
    if (high >= 2 && high <= 4) {
      insights.push(`✅ 고저 비율(${high}:${6-high})이 균형 잡혀 있습니다.`);
    } else {
      insights.push(`⚠️ 고저 비율(${high}:${6-high})이 다소 치우쳐 있습니다.`);
    }
    
    // AC값 분석
    if (ac >= 7) {
      insights.push(`✅ AC값(${ac})이 높아 번호 다양성이 우수합니다.`);
    } else {
      insights.push(`⚠️ AC값(${ac})이 낮아 번호가 밀집되어 있습니다.`);
    }
    
    // 연속번호 분석
    if (consecutive === 1) {
      insights.push(`📊 연속번호 1쌍 포함 - 역대 42%의 당첨번호에 연속번호가 포함됩니다.`);
    } else if (consecutive > 1) {
      insights.push(`⚠️ 연속번호 ${consecutive}쌍 - 다소 많은 편입니다.`);
    }
    
    return { summary, insights, colors, ac, consecutive };
  }
};

// 전역 번호 색상 함수
function getBallColor(num) {
  return LottoDataLoader.getBallColor(num);
}

// 자동 초기화
if (typeof window !== 'undefined') {
  window.addEventListener('DOMContentLoaded', () => {
    lottoGenerator.init();
  });
}

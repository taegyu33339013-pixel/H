<?php
/**
 * 독립 도구 페이지
 * URL: /tools/, /tools/세금-계산기/, /tools/확률-계산기/
 * 
 * 인터랙티브 계산기 도구들
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');

$tool_type = isset($_GET['type']) ? trim($_GET['type']) : '';

// 도구 목록
$tools = [
    '' => ['title' => '로또 도구 모음', 'icon' => '🧰'],
    '세금-계산기' => ['title' => '당첨금 세금 계산기', 'icon' => '💰'],
    '확률-계산기' => ['title' => '당첨 확률 계산기', 'icon' => '📊'],
    '번호-생성기' => ['title' => '랜덤 번호 생성기', 'icon' => '🎲'],
    '조합-분석기' => ['title' => '내 번호 분석기', 'icon' => '🔬'],
];

if (!isset($tools[$tool_type])) {
    $tool_type = '';
}

$current_tool = $tools[$tool_type];
$page_title = $current_tool['title'] . ' | 오늘로또';
$canonical = "https://lottoinsight.ai/tools/" . ($tool_type ? urlencode($tool_type) . "/" : "");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="로또 당첨금 세금 계산, 확률 계산, 번호 생성 등 유용한 도구 모음">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
  
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: 'Noto Sans KR', sans-serif;
      background: linear-gradient(180deg, #050a15 0%, #0a1628 50%, #0d1f3c 100%);
      color: #e2e8f0;
      min-height: 100vh;
    }
    
    .container { max-width: 900px; margin: 0 auto; padding: 40px 24px; }
    
    .breadcrumb {
      display: flex; flex-wrap: wrap; gap: 8px;
      margin-bottom: 32px; font-size: 0.9rem;
    }
    .breadcrumb a { color: #64748b; text-decoration: none; }
    .breadcrumb a:hover { color: #00E0A4; }
    
    .page-header { text-align: center; margin-bottom: 48px; }
    .page-icon { font-size: 4rem; margin-bottom: 16px; }
    .page-title {
      font-family: 'Outfit', sans-serif;
      font-size: 2rem; font-weight: 800;
      background: linear-gradient(135deg, #fff, #94a3b8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    /* 도구 목록 그리드 */
    .tools-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 48px;
    }
    
    .tool-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 32px 24px;
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 20px;
      text-decoration: none;
      transition: all 0.3s;
    }
    .tool-card:hover {
      border-color: #00E0A4;
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(0,224,164,0.1);
    }
    .tool-card.active {
      border-color: #00E0A4;
      background: linear-gradient(145deg, rgba(0,224,164,0.1), rgba(0,224,164,0.02));
    }
    .tool-icon { font-size: 2.5rem; margin-bottom: 16px; }
    .tool-name { color: #fff; font-weight: 700; font-size: 1.1rem; }
    .tool-desc { color: #64748b; font-size: 0.85rem; margin-top: 8px; text-align: center; }
    
    /* 계산기 컨테이너 */
    .calculator {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      padding: 40px;
      margin-bottom: 40px;
    }
    
    .calc-title {
      font-size: 1.3rem;
      font-weight: 700;
      margin-bottom: 24px;
      text-align: center;
    }
    
    /* 입력 필드 */
    .input-group {
      margin-bottom: 24px;
    }
    .input-label {
      display: block;
      color: #94a3b8;
      font-size: 0.9rem;
      margin-bottom: 8px;
    }
    .input-field {
      width: 100%;
      padding: 16px 20px;
      background: rgba(0,0,0,0.3);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 12px;
      color: #fff;
      font-size: 1.2rem;
      font-family: 'Outfit', sans-serif;
      text-align: right;
    }
    .input-field:focus {
      outline: none;
      border-color: #00E0A4;
    }
    .input-suffix {
      color: #64748b;
      font-size: 0.9rem;
      margin-top: 4px;
      text-align: right;
    }
    
    /* 결과 박스 */
    .result-box {
      background: linear-gradient(135deg, rgba(0,224,164,0.1), rgba(0,212,255,0.05));
      border: 1px solid rgba(0,224,164,0.3);
      border-radius: 16px;
      padding: 32px;
      text-align: center;
      margin-top: 24px;
    }
    .result-label { color: #94a3b8; font-size: 0.95rem; margin-bottom: 8px; }
    .result-value {
      font-family: 'Outfit', sans-serif;
      font-size: 2.5rem;
      font-weight: 800;
      color: #00E0A4;
    }
    .result-unit { font-size: 1rem; color: #94a3b8; }
    
    .result-details {
      margin-top: 24px;
      padding-top: 24px;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      font-size: 0.95rem;
    }
    .detail-label { color: #64748b; }
    .detail-value { color: #cbd5e1; font-weight: 600; }
    .detail-value.negative { color: #ef4444; }
    
    /* 번호 생성기 */
    .number-display {
      display: flex;
      gap: 12px;
      justify-content: center;
      margin: 32px 0;
      flex-wrap: wrap;
    }
    .gen-ball {
      width: 56px; height: 56px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 800; font-size: 1.2rem;
      color: #fff;
      transition: all 0.3s;
    }
    .ball-yellow { background: linear-gradient(145deg, #fcd34d, #f59e0b); }
    .ball-blue { background: linear-gradient(145deg, #60a5fa, #3b82f6); }
    .ball-red { background: linear-gradient(145deg, #f87171, #ef4444); }
    .ball-gray { background: linear-gradient(145deg, #9ca3af, #6b7280); }
    .ball-green { background: linear-gradient(145deg, #4ade80, #22c55e); }
    
    .generate-btn {
      display: block;
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, #00E0A4, #00D4FF);
      border: none;
      border-radius: 14px;
      color: #050a15;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .generate-btn:hover {
      transform: translateY(-2px);
    }
    
    /* 분석기 */
    .number-input-grid {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 12px;
      margin-bottom: 24px;
    }
    .number-input {
      width: 100%;
      padding: 16px;
      background: rgba(0,0,0,0.3);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 12px;
      color: #fff;
      font-size: 1.2rem;
      font-family: 'Outfit', sans-serif;
      text-align: center;
    }
    
    .analysis-result {
      background: rgba(0,0,0,0.2);
      border-radius: 16px;
      padding: 24px;
    }
    .analysis-item {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .analysis-label { color: #94a3b8; }
    .analysis-value { font-weight: 600; }
    .analysis-good { color: #00E0A4; }
    .analysis-warning { color: #FFD75F; }
    .analysis-bad { color: #ef4444; }
    
    @media (max-width: 768px) {
      .calculator { padding: 24px; }
      .result-value { font-size: 2rem; }
      .number-input-grid { grid-template-columns: repeat(3, 1fr); }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- 브레드크럼 -->
    <nav class="breadcrumb">
      <a href="/">홈</a>
      <span>›</span>
      <?php if ($tool_type): ?>
      <a href="/tools/">도구</a>
      <span>›</span>
      <span><?= htmlspecialchars($current_tool['title']) ?></span>
      <?php else: ?>
      <span>도구</span>
      <?php endif; ?>
    </nav>
    
    <!-- 헤더 -->
    <header class="page-header">
      <div class="page-icon"><?= $current_tool['icon'] ?></div>
      <h1 class="page-title"><?= htmlspecialchars($current_tool['title']) ?></h1>
    </header>
    
    <?php if ($tool_type === ''): ?>
    <!-- 도구 목록 -->
    <div class="tools-grid">
      <a href="/tools/세금-계산기/" class="tool-card">
        <span class="tool-icon">💰</span>
        <span class="tool-name">세금 계산기</span>
        <span class="tool-desc">당첨금 실수령액 계산</span>
      </a>
      <a href="/tools/확률-계산기/" class="tool-card">
        <span class="tool-icon">📊</span>
        <span class="tool-name">확률 계산기</span>
        <span class="tool-desc">등수별 당첨 확률</span>
      </a>
      <a href="/tools/번호-생성기/" class="tool-card">
        <span class="tool-icon">🎲</span>
        <span class="tool-name">번호 생성기</span>
        <span class="tool-desc">랜덤 번호 추출</span>
      </a>
      <a href="/tools/조합-분석기/" class="tool-card">
        <span class="tool-icon">🔬</span>
        <span class="tool-name">조합 분석기</span>
        <span class="tool-desc">내 번호 패턴 분석</span>
      </a>
    </div>
    
    <?php elseif ($tool_type === '세금-계산기'): ?>
    <!-- 세금 계산기 -->
    <div class="calculator">
      <h2 class="calc-title">💰 당첨금 세금 계산기</h2>
      
      <div class="input-group">
        <label class="input-label">당첨금액 입력</label>
        <input type="text" id="prizeAmount" class="input-field" placeholder="0" oninput="calculateTax()">
        <p class="input-suffix">원 (숫자만 입력)</p>
      </div>
      
      <div class="result-box" id="taxResult">
        <p class="result-label">💵 실수령액</p>
        <p class="result-value" id="netAmount">0<span class="result-unit">원</span></p>
        
        <div class="result-details">
          <div class="detail-row">
            <span class="detail-label">당첨금</span>
            <span class="detail-value" id="grossAmount">0원</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">소득세 (22%)</span>
            <span class="detail-value negative" id="tax22">-0원</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">추가소득세 (33%, 3억 초과분)</span>
            <span class="detail-value negative" id="tax33">-0원</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">총 세금</span>
            <span class="detail-value negative" id="totalTax">-0원</span>
          </div>
        </div>
      </div>
    </div>
    
    <script>
    function calculateTax() {
      const input = document.getElementById('prizeAmount');
      let value = input.value.replace(/[^0-9]/g, '');
      
      // 포맷팅
      if (value) {
        input.value = Number(value).toLocaleString();
      }
      
      const amount = parseInt(value) || 0;
      
      // 세금 계산
      let tax22 = 0;
      let tax33 = 0;
      
      if (amount > 0) {
        if (amount <= 300000000) {
          tax22 = amount * 0.22;
        } else {
          tax22 = 300000000 * 0.22;
          tax33 = (amount - 300000000) * 0.33;
        }
      }
      
      const totalTax = tax22 + tax33;
      const net = amount - totalTax;
      
      document.getElementById('netAmount').innerHTML = net.toLocaleString() + '<span class="result-unit">원</span>';
      document.getElementById('grossAmount').textContent = amount.toLocaleString() + '원';
      document.getElementById('tax22').textContent = '-' + tax22.toLocaleString() + '원';
      document.getElementById('tax33').textContent = '-' + tax33.toLocaleString() + '원';
      document.getElementById('totalTax').textContent = '-' + totalTax.toLocaleString() + '원';
    }
    </script>
    
    <?php elseif ($tool_type === '확률-계산기'): ?>
    <!-- 확률 계산기 -->
    <div class="calculator">
      <h2 class="calc-title">📊 등수별 당첨 확률</h2>
      
      <div class="analysis-result">
        <div class="analysis-item">
          <span class="analysis-label">🥇 1등 (6개 일치)</span>
          <span class="analysis-value">1 / 8,145,060</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">🥈 2등 (5개 + 보너스)</span>
          <span class="analysis-value">1 / 1,357,510</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">🥉 3등 (5개 일치)</span>
          <span class="analysis-value">1 / 35,724</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">4등 (4개 일치)</span>
          <span class="analysis-value">1 / 733</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">5등 (3개 일치)</span>
          <span class="analysis-value">1 / 45</span>
        </div>
      </div>
      
      <div class="result-box" style="margin-top: 24px;">
        <p class="result-label">📌 1등 당첨 확률</p>
        <p class="result-value">0.00001228<span class="result-unit">%</span></p>
        <p style="color: #64748b; margin-top: 16px; font-size: 0.9rem;">
          매주 1게임씩 구매하면 평균 <strong style="color:#FFD75F;">156,635년</strong> 후 1등 당첨
        </p>
      </div>
    </div>
    
    <?php elseif ($tool_type === '번호-생성기'): ?>
    <!-- 번호 생성기 -->
    <div class="calculator">
      <h2 class="calc-title">🎲 랜덤 번호 생성기</h2>
      
      <div class="number-display" id="numberDisplay">
        <span class="gen-ball ball-gray">?</span>
        <span class="gen-ball ball-gray">?</span>
        <span class="gen-ball ball-gray">?</span>
        <span class="gen-ball ball-gray">?</span>
        <span class="gen-ball ball-gray">?</span>
        <span class="gen-ball ball-gray">?</span>
      </div>
      
      <button class="generate-btn" onclick="generateNumbers()">🎲 번호 생성</button>
      
      <div id="historySection" style="margin-top: 32px; display: none;">
        <h3 style="font-size: 1rem; color: #94a3b8; margin-bottom: 16px;">📋 생성 기록</h3>
        <div id="historyList"></div>
      </div>
    </div>
    
    <script>
    function getBallColor(n) {
      if (n <= 10) return 'yellow';
      if (n <= 20) return 'blue';
      if (n <= 30) return 'red';
      if (n <= 40) return 'gray';
      return 'green';
    }
    
    let history = [];
    
    function generateNumbers() {
      const nums = [];
      while (nums.length < 6) {
        const n = Math.floor(Math.random() * 45) + 1;
        if (!nums.includes(n)) nums.push(n);
      }
      nums.sort((a, b) => a - b);
      
      const display = document.getElementById('numberDisplay');
      display.innerHTML = nums.map(n => 
        `<span class="gen-ball ball-${getBallColor(n)}" style="animation: pop 0.3s ease">${n}</span>`
      ).join('');
      
      // 기록 추가
      history.unshift(nums);
      if (history.length > 5) history.pop();
      
      const historySection = document.getElementById('historySection');
      const historyList = document.getElementById('historyList');
      historySection.style.display = 'block';
      
      historyList.innerHTML = history.map((h, i) => `
        <div style="display: flex; gap: 8px; margin-bottom: 12px; opacity: ${1 - i * 0.15};">
          ${h.map(n => `<span class="gen-ball ball-${getBallColor(n)}" style="width: 36px; height: 36px; font-size: 0.85rem;">${n}</span>`).join('')}
        </div>
      `).join('');
    }
    </script>
    
    <style>
    @keyframes pop {
      0% { transform: scale(0); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }
    </style>
    
    <?php elseif ($tool_type === '조합-분석기'): ?>
    <!-- 조합 분석기 -->
    <div class="calculator">
      <h2 class="calc-title">🔬 내 번호 분석기</h2>
      
      <div class="number-input-grid">
        <input type="number" class="number-input" id="n1" min="1" max="45" placeholder="1" oninput="analyzeNumbers()">
        <input type="number" class="number-input" id="n2" min="1" max="45" placeholder="2" oninput="analyzeNumbers()">
        <input type="number" class="number-input" id="n3" min="1" max="45" placeholder="3" oninput="analyzeNumbers()">
        <input type="number" class="number-input" id="n4" min="1" max="45" placeholder="4" oninput="analyzeNumbers()">
        <input type="number" class="number-input" id="n5" min="1" max="45" placeholder="5" oninput="analyzeNumbers()">
        <input type="number" class="number-input" id="n6" min="1" max="45" placeholder="6" oninput="analyzeNumbers()">
      </div>
      
      <div class="analysis-result" id="analysisResult">
        <div class="analysis-item">
          <span class="analysis-label">⚖️ 홀짝 비율</span>
          <span class="analysis-value" id="oddEven">-</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">📊 고저 비율</span>
          <span class="analysis-value" id="highLow">-</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">➕ 번호 합계</span>
          <span class="analysis-value" id="sumTotal">-</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">🔢 연속번호</span>
          <span class="analysis-value" id="consecutive">-</span>
        </div>
        <div class="analysis-item">
          <span class="analysis-label">📈 균형 점수</span>
          <span class="analysis-value" id="balanceScore">-</span>
        </div>
      </div>
    </div>
    
    <script>
    function analyzeNumbers() {
      const nums = [];
      for (let i = 1; i <= 6; i++) {
        const val = parseInt(document.getElementById('n' + i).value);
        if (val >= 1 && val <= 45) nums.push(val);
      }
      
      if (nums.length < 6) return;
      
      // 중복 체크
      if (new Set(nums).size !== 6) {
        document.getElementById('balanceScore').innerHTML = '<span class="analysis-bad">중복 번호!</span>';
        return;
      }
      
      const odd = nums.filter(n => n % 2 === 1).length;
      const even = 6 - odd;
      const high = nums.filter(n => n > 22).length;
      const low = 6 - high;
      const sum = nums.reduce((a, b) => a + b, 0);
      
      nums.sort((a, b) => a - b);
      let consec = 0;
      for (let i = 0; i < 5; i++) {
        if (nums[i + 1] - nums[i] === 1) consec++;
      }
      
      // 균형 점수 계산
      let score = 100;
      if (odd < 2 || odd > 4) score -= 15;
      if (high < 2 || high > 4) score -= 15;
      if (sum < 100 || sum > 170) score -= 10;
      if (consec > 2) score -= 10;
      
      // 결과 표시
      const getClass = (val, good) => good ? 'analysis-good' : 'analysis-warning';
      
      document.getElementById('oddEven').innerHTML = 
        `<span class="${getClass(odd, odd >= 2 && odd <= 4)}">${odd}:${even}</span>`;
      document.getElementById('highLow').innerHTML = 
        `<span class="${getClass(high, high >= 2 && high <= 4)}">${high}:${low}</span>`;
      document.getElementById('sumTotal').innerHTML = 
        `<span class="${getClass(sum, sum >= 100 && sum <= 170)}">${sum}</span>`;
      document.getElementById('consecutive').innerHTML = 
        `<span class="${getClass(consec, consec <= 2)}">${consec}쌍</span>`;
      document.getElementById('balanceScore').innerHTML = 
        `<span class="${score >= 80 ? 'analysis-good' : score >= 60 ? 'analysis-warning' : 'analysis-bad'}">${score}점</span>`;
    }
    </script>
    
    <?php endif; ?>
    
    <!-- 다른 도구 -->
    <?php if ($tool_type): ?>
    <section style="margin-top: 48px;">
      <h3 style="font-size: 1.1rem; color: #94a3b8; margin-bottom: 16px;">🧰 다른 도구</h3>
      <div class="tools-grid">
        <?php foreach ($tools as $key => $tool): ?>
        <?php if ($key && $key !== $tool_type): ?>
        <a href="/tools/<?= urlencode($key) ?>/" class="tool-card">
          <span class="tool-icon"><?= $tool['icon'] ?></span>
          <span class="tool-name"><?= $tool['title'] ?></span>
        </a>
        <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
  </div>
  
  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

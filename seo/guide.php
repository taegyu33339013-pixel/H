<?php
/**
 * 가이드 SEO 페이지
 * URL: /guide/세금/ → "로또 당첨금 세금 계산"
 * URL: /guide/구매방법/ → "로또 구매 방법"
 */

if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

$type = isset($_GET['type']) ? urldecode(trim($_GET['type'])) : '';

// 가이드 타입 정의
$guides = [
    '세금' => [
        'title' => '로또 당첨금 세금 계산 - 실수령액 계산기',
        'desc' => '로또 1등, 2등, 3등 당첨금 세금 계산 방법. 3억 초과시 33% 세율 적용. 실수령액 자동 계산기 제공.',
        'icon' => '💰',
        'keywords' => '로또 세금, 로또 당첨금 세금, 로또 실수령액, 로또 세금 계산, 복권 세금',
        'content' => 'tax'
    ],
    '구매방법' => [
        'title' => '로또 구매 방법 가이드 - 온라인/오프라인 완벽 정리',
        'desc' => '로또 6/45 구매 방법 총정리. 오프라인 판매점, 동행복권 온라인 구매, 자동/수동/반자동 선택 방법까지.',
        'icon' => '🎫',
        'keywords' => '로또 구매, 로또 구매방법, 로또 사는법, 동행복권 구매, 로또 자동 수동',
        'content' => 'buy'
    ],
    '당첨확인' => [
        'title' => '로또 당첨 확인 방법 - 1등부터 5등까지',
        'desc' => '로또 당첨번호 확인 방법과 등수별 당첨 조건. QR코드 확인, 동행복권 앱, 판매점 확인 방법.',
        'icon' => '✅',
        'keywords' => '로또 당첨확인, 로또 당첨 조회, 로또 등수, 로또 당첨 조건',
        'content' => 'check'
    ],
    '수령방법' => [
        'title' => '로또 당첨금 수령 방법 - 등수별 수령처 안내',
        'desc' => '로또 당첨금 수령 방법. 5등 편의점, 4등 은행, 1-3등 농협은행 본점 방문. 필요서류와 기한 안내.',
        'icon' => '🏦',
        'keywords' => '로또 수령, 로또 당첨금 수령, 로또 수령 방법, 로또 당첨금 찾기',
        'content' => 'claim'
    ],
    '확률' => [
        'title' => '로또 당첨 확률 - 1등부터 5등까지 수학적 분석',
        'desc' => '로또 6/45 당첨 확률 완벽 분석. 1등 확률 1/8,145,060부터 5등 확률까지. 조합 수학 계산.',
        'icon' => '📊',
        'keywords' => '로또 확률, 로또 당첨 확률, 로또 1등 확률, 로또 조합 수',
        'content' => 'probability'
    ],
];

if (!isset($guides[$type])) {
    // 가이드 인덱스 페이지
    $page_title = '로또 가이드 - 구매부터 수령까지 완벽 정리';
    $page_desc = '로또 6/45 완벽 가이드. 구매 방법, 세금 계산, 당첨 확인, 수령 방법까지 모든 정보를 한눈에.';
    $show_index = true;
} else {
    $info = $guides[$type];
    $page_title = $info['title'];
    $page_desc = $info['desc'];
    $show_index = false;
}

$canonical = "https://lottoinsight.ai/guide/" . ($type ? urlencode($type) . "/" : "");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Core Web Vitals -->
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta name="keywords" content="<?= $show_index ? '로또 가이드, 로또 정보, 로또 안내' : htmlspecialchars($info['keywords']) ?>">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:type" content="article">
  
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($page_desc) ?>">

  <!-- BreadcrumbList -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "홈", "item": "https://lottoinsight.ai/"},
      {"@type": "ListItem", "position": 2, "name": "가이드", "item": "https://lottoinsight.ai/guide/"}
      <?php if (!$show_index): ?>
      ,{"@type": "ListItem", "position": 3, "name": "<?= htmlspecialchars($type) ?>", "item": "<?= $canonical ?>"}
      <?php endif; ?>
    ]
  }
  </script>

  <!-- HowTo Schema (가이드 페이지) -->
  <?php if (!$show_index && $info['content'] === 'tax'): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "로또 당첨금 세금 계산하는 방법",
    "description": "로또 당첨금에서 세금을 계산하고 실수령액을 알아보는 방법",
    "totalTime": "PT2M",
    "step": [
      {
        "@type": "HowToStep",
        "position": 1,
        "name": "당첨금 확인",
        "text": "로또 당첨 등수와 당첨금액을 확인합니다."
      },
      {
        "@type": "HowToStep",
        "position": 2,
        "name": "세율 적용",
        "text": "3억원 이하는 22%, 3억원 초과분은 33% 세율이 적용됩니다."
      },
      {
        "@type": "HowToStep",
        "position": 3,
        "name": "실수령액 계산",
        "text": "당첨금에서 세금을 제외한 실수령액을 계산합니다."
      }
    ]
  }
  </script>
  <?php elseif (!$show_index && $info['content'] === 'buy'): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "로또 구매하는 방법",
    "description": "로또 6/45 복권을 오프라인 판매점이나 온라인에서 구매하는 방법",
    "totalTime": "PT5M",
    "step": [
      {
        "@type": "HowToStep",
        "position": 1,
        "name": "구매처 선택",
        "text": "오프라인 판매점 또는 동행복권 온라인 사이트를 선택합니다."
      },
      {
        "@type": "HowToStep",
        "position": 2,
        "name": "번호 선택",
        "text": "자동(컴퓨터 랜덤), 수동(직접 선택), 반자동(일부 선택) 중 선택합니다."
      },
      {
        "@type": "HowToStep",
        "position": 3,
        "name": "게임 수 선택",
        "text": "1게임(1,000원)부터 5게임(5,000원)까지 선택할 수 있습니다."
      },
      {
        "@type": "HowToStep",
        "position": 4,
        "name": "결제",
        "text": "현금 또는 카드로 결제하고 복권을 받습니다."
      }
    ]
  }
  </script>
  <?php endif; ?>

  <meta name="theme-color" content="#0B132B">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">

  <style>
    :root {
      --primary-dark: #050a15;
      --primary: #0d1526;
      --accent-cyan: #00E0A4;
      --accent-gold: #FFD75F;
      --accent-purple: #8B5CF6;
      --text-primary: #ffffff;
      --text-secondary: #94a3b8;
      --text-muted: #64748b;
      --glass-border: rgba(255,255,255,0.08);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Pretendard', sans-serif; background: var(--primary-dark); color: var(--text-primary); line-height: 1.8; }
    .container { max-width: 800px; margin: 0 auto; padding: 24px; }
    
    .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 16px 24px; background: rgba(5,10,21,0.95); backdrop-filter: blur(20px); }
    .nav-container { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .nav-logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .nav-logo-text { font-family: 'Outfit', sans-serif; font-weight: 800; background: linear-gradient(135deg, #00E0A4, #00D4FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .main { padding-top: 80px; }
    .breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 24px; }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; }
    .breadcrumb a:hover { color: var(--accent-cyan); }
    
    .page-header { text-align: center; margin-bottom: 40px; }
    .page-icon { font-size: 4rem; margin-bottom: 16px; }
    .page-title { font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; margin-bottom: 12px; line-height: 1.3; }
    .page-desc { color: var(--text-secondary); font-size: 1.1rem; }
    
    .guide-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
    .guide-card {
      display: block; padding: 24px;
      background: rgba(13,24,41,0.8); border: 1px solid var(--glass-border);
      border-radius: 16px; text-decoration: none; color: inherit;
      transition: all 0.3s;
    }
    .guide-card:hover { border-color: var(--accent-cyan); transform: translateY(-4px); }
    .guide-card-icon { font-size: 2.5rem; margin-bottom: 12px; }
    .guide-card-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 8px; }
    .guide-card-desc { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; }
    
    /* Article Content */
    .article { background: rgba(13,24,41,0.8); border-radius: 20px; padding: 32px; }
    .article h2 { font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 700; margin: 32px 0 16px; padding-bottom: 8px; border-bottom: 2px solid var(--accent-cyan); }
    .article h2:first-child { margin-top: 0; }
    .article h3 { font-size: 1.2rem; font-weight: 600; margin: 24px 0 12px; color: var(--accent-gold); }
    .article p { margin-bottom: 16px; color: var(--text-secondary); }
    .article ul, .article ol { margin: 16px 0; padding-left: 24px; color: var(--text-secondary); }
    .article li { margin-bottom: 8px; }
    .article strong { color: var(--text-primary); }
    
    .info-box { background: rgba(0,224,164,0.1); border: 1px solid rgba(0,224,164,0.2); border-radius: 12px; padding: 16px 20px; margin: 20px 0; }
    .info-box.warning { background: rgba(255,215,95,0.1); border-color: rgba(255,215,95,0.2); }
    .info-box-title { font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
    
    .tax-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    .tax-table th, .tax-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--glass-border); }
    .tax-table th { background: rgba(0,0,0,0.3); font-weight: 600; }
    .tax-table td { color: var(--text-secondary); }
    
    .calculator { background: rgba(0,0,0,0.3); border-radius: 16px; padding: 24px; margin: 24px 0; }
    .calc-input { width: 100%; padding: 14px 16px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: var(--text-primary); font-size: 1.1rem; margin-bottom: 16px; }
    .calc-input:focus { outline: none; border-color: var(--accent-cyan); }
    .calc-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border: none; border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer; }
    .calc-result { margin-top: 20px; padding: 20px; background: rgba(0,224,164,0.1); border-radius: 12px; display: none; }
    .calc-result.show { display: block; }
    .calc-result-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .calc-result-item:last-child { border: none; font-weight: 700; font-size: 1.2rem; color: var(--accent-cyan); }
    
    .related-guides { margin-top: 40px; padding-top: 24px; border-top: 1px solid var(--glass-border); }
    .related-title { font-weight: 700; margin-bottom: 16px; }
    .related-links { display: flex; flex-wrap: wrap; gap: 8px; }
    .related-link { padding: 10px 16px; background: rgba(255,255,255,0.05); border-radius: 8px; color: var(--text-secondary); text-decoration: none; }
    .related-link:hover { background: rgba(0,224,164,0.1); color: var(--accent-cyan); }
    
    .cta-section { text-align: center; padding: 40px; background: linear-gradient(145deg, rgba(0,224,164,0.05), rgba(139,92,246,0.05)); border-radius: 20px; margin-top: 40px; }
    .cta-btn { display: inline-block; padding: 14px 28px; background: linear-gradient(135deg, #00E0A4, #00D4FF); border-radius: 12px; font-weight: 700; color: var(--primary-dark); text-decoration: none; }
    
    .footer { text-align: center; padding: 40px; color: var(--text-muted); font-size: 0.85rem; margin-top: 60px; }
    .footer a { color: var(--text-muted); text-decoration: none; margin: 0 12px; }
  </style>
</head>
<body>
  <nav class="nav">
    <div class="nav-container">
      <a href="/" class="nav-logo">
        <div class="nav-logo-icon">🎯</div>
        <span class="nav-logo-text">오늘로또</span>
      </a>
    </div>
  </nav>

  <main class="main">
    <div class="container">
      <nav class="breadcrumb">
        <a href="/">홈</a> <span>›</span>
        <a href="/guide/">가이드</a>
        <?php if (!$show_index): ?>
        <span>›</span> <span><?= htmlspecialchars($type) ?></span>
        <?php endif; ?>
      </nav>

      <header class="page-header">
        <div class="page-icon"><?= $show_index ? '📚' : $info['icon'] ?></div>
        <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
        <p class="page-desc"><?= htmlspecialchars($page_desc) ?></p>
      </header>

      <?php if ($show_index): ?>
      <!-- 가이드 목록 -->
      <div class="guide-grid">
        <?php foreach ($guides as $key => $g): ?>
        <a href="/guide/<?= urlencode($key) ?>/" class="guide-card">
          <div class="guide-card-icon"><?= $g['icon'] ?></div>
          <div class="guide-card-title"><?= htmlspecialchars(explode(' - ', $g['title'])[0]) ?></div>
          <div class="guide-card-desc"><?= mb_substr($g['desc'], 0, 60) ?>...</div>
        </a>
        <?php endforeach; ?>
      </div>

      <?php elseif ($info['content'] === 'tax'): ?>
      <!-- 세금 계산 가이드 -->
      <article class="article">
        <h2>💰 로또 당첨금 세금 구조</h2>
        <p>로또 당첨금에는 <strong>소득세와 주민세</strong>가 부과됩니다. 세율은 당첨금액에 따라 달라집니다.</p>
        
        <table class="tax-table">
          <thead>
            <tr>
              <th>당첨금액</th>
              <th>세율</th>
              <th>세금 내역</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>5만원 이하</td>
              <td><strong>0%</strong> (비과세)</td>
              <td>세금 없음</td>
            </tr>
            <tr>
              <td>5만원 초과 ~ 3억원</td>
              <td><strong>22%</strong></td>
              <td>소득세 20% + 주민세 2%</td>
            </tr>
            <tr>
              <td>3억원 초과분</td>
              <td><strong>33%</strong></td>
              <td>소득세 30% + 주민세 3%</td>
            </tr>
          </tbody>
        </table>

        <div class="info-box">
          <div class="info-box-title">💡 알아두세요</div>
          <p style="margin: 0; color: var(--text-secondary);">3억원을 초과하는 경우, 3억원까지는 22%, 초과분에만 33%가 적용됩니다. 전체 금액에 33%가 적용되는 것이 아닙니다!</p>
        </div>

        <h2>🧮 세금 계산기</h2>
        <div class="calculator">
          <label style="display: block; margin-bottom: 8px; font-weight: 600;">당첨금액 (원)</label>
          <input type="text" class="calc-input" id="prizeInput" placeholder="예: 1000000000 (10억)" oninput="formatNumber(this)">
          <button class="calc-btn" onclick="calculateTax()">세금 계산하기</button>
          
          <div class="calc-result" id="calcResult">
            <div class="calc-result-item">
              <span>당첨금</span>
              <span id="resultPrize">-</span>
            </div>
            <div class="calc-result-item">
              <span>22% 구간 세금 (3억 이하)</span>
              <span id="resultTax22">-</span>
            </div>
            <div class="calc-result-item">
              <span>33% 구간 세금 (3억 초과)</span>
              <span id="resultTax33">-</span>
            </div>
            <div class="calc-result-item">
              <span>총 세금</span>
              <span id="resultTotalTax">-</span>
            </div>
            <div class="calc-result-item">
              <span>✅ 실수령액</span>
              <span id="resultNet">-</span>
            </div>
          </div>
        </div>

        <h2>📋 예시: 10억 당첨 시</h2>
        <ul>
          <li>3억원까지: 3억 × 22% = <strong>6,600만원</strong></li>
          <li>3억 초과분 (7억): 7억 × 33% = <strong>2억 3,100만원</strong></li>
          <li>총 세금: <strong>2억 9,700만원</strong></li>
          <li>실수령액: <strong>7억 300만원</strong></li>
        </ul>

        <h2>⚠️ 주의사항</h2>
        <div class="info-box warning">
          <div class="info-box-title">📌 당첨금 수령 시 알아야 할 것</div>
          <ul style="margin: 0; padding-left: 20px;">
            <li>세금은 수령 시 <strong>원천징수</strong>되어 자동으로 공제됩니다</li>
            <li>별도로 종합소득세 신고할 필요가 없습니다</li>
            <li>당첨금은 1년 이내에 수령해야 합니다 (미수령시 복권기금 귀속)</li>
          </ul>
        </div>
      </article>

      <script>
      function formatNumber(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        input.value = value ? parseInt(value).toLocaleString() : '';
      }
      
      function calculateTax() {
        const input = document.getElementById('prizeInput');
        const prize = parseInt(input.value.replace(/[^0-9]/g, '')) || 0;
        
        if (prize <= 0) {
          alert('당첨금액을 입력해주세요.');
          return;
        }
        
        let tax22 = 0, tax33 = 0;
        const threshold = 300000000; // 3억
        
        if (prize <= 50000) {
          // 비과세
          tax22 = 0;
          tax33 = 0;
        } else if (prize <= threshold) {
          tax22 = prize * 0.22;
        } else {
          tax22 = threshold * 0.22;
          tax33 = (prize - threshold) * 0.33;
        }
        
        const totalTax = tax22 + tax33;
        const net = prize - totalTax;
        
        document.getElementById('resultPrize').textContent = prize.toLocaleString() + '원';
        document.getElementById('resultTax22').textContent = Math.floor(tax22).toLocaleString() + '원';
        document.getElementById('resultTax33').textContent = Math.floor(tax33).toLocaleString() + '원';
        document.getElementById('resultTotalTax').textContent = Math.floor(totalTax).toLocaleString() + '원';
        document.getElementById('resultNet').textContent = Math.floor(net).toLocaleString() + '원';
        document.getElementById('calcResult').classList.add('show');
      }
      </script>

      <?php elseif ($info['content'] === 'buy'): ?>
      <!-- 구매 방법 가이드 -->
      <article class="article">
        <h2>🏪 오프라인 판매점 구매</h2>
        <p>전국 약 6,700여 개의 로또 판매점에서 구매할 수 있습니다.</p>
        
        <h3>구매 절차</h3>
        <ol>
          <li><strong>판매점 방문</strong> - 로또 판매점 표시가 있는 곳</li>
          <li><strong>번호 선택 방식 결정</strong>
            <ul>
              <li><strong>자동</strong>: 컴퓨터가 랜덤으로 번호 선택</li>
              <li><strong>수동</strong>: 직접 6개 번호를 선택</li>
              <li><strong>반자동</strong>: 일부 번호만 선택, 나머지는 자동</li>
            </ul>
          </li>
          <li><strong>게임 수 선택</strong> - 1게임(1,000원) ~ 5게임(5,000원)</li>
          <li><strong>결제</strong> - 현금 또는 카드</li>
        </ol>

        <div class="info-box">
          <div class="info-box-title">💡 판매 시간</div>
          <p style="margin: 0;">매주 <strong>일요일 오전 6시 ~ 토요일 오후 8시</strong><br>추첨일(토요일) 오후 8시부터 일요일 오전 6시까지는 판매 중단</p>
        </div>

        <h2>💻 온라인 구매 (동행복권)</h2>
        <p><a href="https://dhlottery.co.kr" target="_blank" rel="noopener" style="color: var(--accent-cyan);">동행복권 공식 사이트</a>에서 온라인 구매가 가능합니다.</p>
        
        <h3>온라인 구매 절차</h3>
        <ol>
          <li><strong>회원가입</strong> - 본인인증 필요</li>
          <li><strong>예치금 충전</strong> - 계좌이체로 충전</li>
          <li><strong>로또 구매</strong> - 자동/수동/반자동 선택</li>
          <li><strong>결제</strong> - 예치금에서 차감</li>
        </ol>

        <div class="info-box warning">
          <div class="info-box-title">⚠️ 온라인 구매 제한</div>
          <ul style="margin: 0; padding-left: 20px;">
            <li>1일 최대 <strong>10만원</strong>까지 구매 가능</li>
            <li>주당 구매 한도가 있을 수 있음</li>
            <li>19세 이상만 구매 가능</li>
          </ul>
        </div>

        <h2>🔢 자동 vs 수동, 어떤 게 좋을까?</h2>
        <p>수학적으로 <strong>당첨 확률은 동일</strong>합니다. 모든 번호 조합의 당첨 확률은 1/8,145,060으로 똑같습니다.</p>
        
        <p>다만, 역대 1등 당첨자 통계를 보면:</p>
        <ul>
          <li><strong>자동</strong>: 약 70% 이상</li>
          <li><strong>수동</strong>: 약 20% 내외</li>
          <li><strong>반자동</strong>: 약 10% 미만</li>
        </ul>
        <p>이는 자동 구매자가 더 많기 때문이며, 당첨 "확률"이 높은 것은 아닙니다.</p>
      </article>

      <?php elseif ($info['content'] === 'probability'): ?>
      <!-- 확률 가이드 -->
      <article class="article">
        <h2>🎯 로또 당첨 확률 완벽 분석</h2>
        <p>로또 6/45는 1부터 45까지의 숫자 중 6개를 맞추는 게임입니다.</p>
        
        <table class="tax-table">
          <thead>
            <tr>
              <th>등수</th>
              <th>당첨 조건</th>
              <th>확률</th>
              <th>평균 당첨금</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>🥇 1등</td>
              <td>6개 번호 일치</td>
              <td><strong>1/8,145,060</strong></td>
              <td>약 20억원</td>
            </tr>
            <tr>
              <td>🥈 2등</td>
              <td>5개 + 보너스</td>
              <td>1/1,357,510</td>
              <td>약 5천만원</td>
            </tr>
            <tr>
              <td>🥉 3등</td>
              <td>5개 번호 일치</td>
              <td>1/35,724</td>
              <td>약 150만원</td>
            </tr>
            <tr>
              <td>4등</td>
              <td>4개 번호 일치</td>
              <td>1/733</td>
              <td>고정 5만원</td>
            </tr>
            <tr>
              <td>5등</td>
              <td>3개 번호 일치</td>
              <td>1/45</td>
              <td>고정 5천원</td>
            </tr>
          </tbody>
        </table>

        <h2>📊 확률 계산 방법</h2>
        <p>로또 1등 확률은 조합(Combination) 공식으로 계산합니다:</p>
        
        <div class="info-box">
          <div style="text-align: center; font-size: 1.2rem; font-family: 'Outfit', sans-serif;">
            <strong>₄₅C₆ = 45! / (6! × 39!) = 8,145,060</strong>
          </div>
          <p style="margin-top: 12px; text-align: center;">45개 중 6개를 뽑는 경우의 수</p>
        </div>

        <h2>💡 확률 비교</h2>
        <ul>
          <li>로또 1등: <strong>1/8,145,060</strong></li>
          <li>벼락 맞을 확률: 약 1/1,000,000</li>
          <li>비행기 사고: 약 1/11,000,000</li>
          <li>동전 던져 23번 연속 앞면: 약 1/8,400,000</li>
        </ul>

        <div class="info-box warning">
          <div class="info-box-title">⚠️ 중요한 사실</div>
          <p style="margin: 0;">어떤 번호 조합을 선택하든, 당첨 확률은 <strong>모두 동일</strong>합니다. 1,2,3,4,5,6을 선택하든, 7,14,21,28,35,42를 선택하든 1등 확률은 1/8,145,060입니다.</p>
        </div>
      </article>

      <?php else: ?>
      <!-- 기타 가이드 (당첨확인, 수령방법) -->
      <article class="article">
        <h2><?= $info['icon'] ?> <?= htmlspecialchars(explode(' - ', $info['title'])[0]) ?></h2>
        <p><?= htmlspecialchars($info['desc']) ?></p>
        <p style="text-align: center; padding: 40px 0; color: var(--text-muted);">
          상세 내용 준비 중입니다...
        </p>
      </article>
      <?php endif; ?>

      <!-- 관련 가이드 -->
      <?php if (!$show_index): ?>
      <div class="related-guides">
        <h2 class="related-title">📚 다른 가이드 보기</h2>
        <div class="related-links">
          <?php foreach ($guides as $key => $g): if ($key === $type) continue; ?>
          <a href="/guide/<?= urlencode($key) ?>/" class="related-link"><?= $g['icon'] ?> <?= htmlspecialchars($key) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <section class="cta-section">
        <h2 style="font-family: 'Outfit'; font-size: 1.4rem; font-weight: 800; margin-bottom: 12px;">🎯 AI 로또 번호 분석</h2>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">23년간 당첨번호 패턴을 AI가 분석합니다</p>
        <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작</a>
      </section>
    </div>
  </main>

  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

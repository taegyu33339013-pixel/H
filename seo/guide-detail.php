<?php
/**
 * 가이드 3단계 상세 페이지
 * URL: /guide/세금/1등/, /guide/세금/2등/, /guide/수령방법/1등/
 * 
 * 더 깊은 콘텐츠 제공으로 롱테일 키워드 SEO 강화
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');

$guide_type = isset($_GET['type']) ? trim($_GET['type']) : '';
$sub_type = isset($_GET['sub']) ? trim($_GET['sub']) : '';

// 유효성 검사
$valid_guides = [
    '세금' => ['1등', '2등', '3등', '연금복권'],
    '수령방법' => ['1등', '2등', '3등', '4등', '5등'],
    '구매방법' => ['오프라인', '온라인', '자동', '수동', '반자동'],
    '확률' => ['1등', '2등', '3등', '전체'],
];

if (!isset($valid_guides[$guide_type]) || !in_array($sub_type, $valid_guides[$guide_type])) {
    header('Location: /guide/');
    exit;
}

// 페이지별 콘텐츠
$page_data = [
    '세금' => [
        '1등' => [
            'title' => '로또 1등 당첨금 세금 계산',
            'description' => '로또 1등 당첨 시 세금은 얼마? 실수령액 계산법과 세율 완벽 정리',
            'content' => [
                'intro' => '로또 1등에 당첨되면 기쁨도 잠시, 세금 계산이 복잡합니다. 1등 당첨금은 금액에 따라 세율이 달라집니다.',
                'tax_rate' => '1등 당첨금 세율: 3억 이하 22%, 3억 초과분 33%',
                'example_title' => '예시: 20억 당첨 시',
                'example' => [
                    '3억원까지: 3억 × 22% = 6,600만원',
                    '3억 초과분: 17억 × 33% = 5억 6,100만원',
                    '총 세금: 6억 2,700만원',
                    '실수령액: 약 13억 7,300만원'
                ],
                'tips' => [
                    '당첨금은 일시불로 지급됩니다',
                    '세금은 지급 시 자동 원천징수됩니다',
                    '별도의 종합소득세 신고는 불필요합니다',
                    '가족에게 증여 시 추가 증여세가 발생합니다'
                ]
            ]
        ],
        '2등' => [
            'title' => '로또 2등 당첨금 세금 계산',
            'description' => '로또 2등 당첨 시 세금과 실수령액. 보통 5천만원~1억원 수준의 당첨금 세금 계산법',
            'content' => [
                'intro' => '로또 2등은 5개 번호 + 보너스 번호를 맞춘 경우입니다. 평균 당첨금은 약 5천만원~8천만원 수준입니다.',
                'tax_rate' => '2등 당첨금 세율: 전액 22% (대부분 3억 이하)',
                'example_title' => '예시: 6,000만원 당첨 시',
                'example' => [
                    '당첨금: 6,000만원',
                    '세금: 6,000만원 × 22% = 1,320만원',
                    '실수령액: 4,680만원'
                ],
                'tips' => [
                    '2등은 보통 매회 80~100명 정도 당첨',
                    '당첨금은 당첨자 수에 따라 변동',
                    '농협은행 본점에서 수령 가능'
                ]
            ]
        ],
        '3등' => [
            'title' => '로또 3등 당첨금 세금 계산',
            'description' => '로또 3등 당첨 시 세금. 평균 150만원 수준의 당첨금, 세금은 얼마?',
            'content' => [
                'intro' => '로또 3등은 5개 번호를 맞춘 경우입니다. 평균 당첨금은 약 150만원 수준입니다.',
                'tax_rate' => '3등 당첨금 세율: 전액 22%',
                'example_title' => '예시: 150만원 당첨 시',
                'example' => [
                    '당첨금: 150만원',
                    '세금: 150만원 × 22% = 33만원',
                    '실수령액: 117만원'
                ],
                'tips' => [
                    '3등은 매회 약 2,500~3,000명 당첨',
                    '가까운 농협 지점에서 수령 가능',
                    '신분증만 있으면 바로 수령'
                ]
            ]
        ],
        '연금복권' => [
            'title' => '연금복권 당첨금 세금 계산',
            'description' => '연금복권 720+ 1등 월 700만원, 20년간 세금은 어떻게 계산될까?',
            'content' => [
                'intro' => '연금복권 720+ 1등은 매월 700만원씩 20년간 지급됩니다. 로또와 달리 매월 세금이 부과됩니다.',
                'tax_rate' => '연금복권 세율: 매월 22% 원천징수',
                'example_title' => '연금복권 1등 실수령액',
                'example' => [
                    '월 당첨금: 700만원',
                    '월 세금: 700만원 × 22% = 154만원',
                    '월 실수령: 546만원',
                    '20년 총 실수령: 약 13억 1,040만원'
                ],
                'tips' => [
                    '20년간 매월 지급 (총 240회)',
                    '사망 시 상속인에게 잔여분 지급',
                    '일시불 수령 옵션 없음',
                    '물가 상승 반영 없이 고정 금액'
                ]
            ]
        ]
    ],
    '수령방법' => [
        '1등' => [
            'title' => '로또 1등 당첨금 수령 방법',
            'description' => '로또 1등 당첨! 어디서 어떻게 수령하나요? 농협은행 본점 방문 수령 절차 안내',
            'content' => [
                'intro' => '로또 1등에 당첨되셨다면 축하드립니다! 1등 당첨금은 반드시 농협은행 본점에서만 수령 가능합니다.',
                'location' => '수령 장소: 농협은행 본점 (서울 중구 충정로 120)',
                'documents' => [
                    '당첨 복권 원본',
                    '신분증 (주민등록증, 운전면허증, 여권)',
                    '본인 명의 농협 통장 (없으면 현장 개설)'
                ],
                'process' => [
                    '1. 당첨 복권 뒷면에 서명 및 연락처 기재',
                    '2. 농협은행 본점 복권업무 담당 방문',
                    '3. 본인 확인 및 복권 진위 확인',
                    '4. 세금 원천징수 후 계좌 입금',
                    '5. 수령 완료 (당일 처리)'
                ],
                'tips' => [
                    '복권에 손상이 가지 않도록 보관',
                    '분실/도난 시 복구 불가',
                    '대리 수령 불가 (본인만 가능)',
                    '당첨일로부터 1년 내 수령 필수'
                ]
            ]
        ],
        '2등' => [
            'title' => '로또 2등 당첨금 수령 방법',
            'description' => '로또 2등 당첨금은 어디서 받나요? 농협은행 본점 또는 지역 본부에서 수령 가능',
            'content' => [
                'intro' => '2등 당첨금은 농협은행 본점 또는 지역농협 본부에서 수령 가능합니다.',
                'location' => '수령 장소: 농협은행 본점 또는 지역농협 본부',
                'documents' => [
                    '당첨 복권 원본',
                    '신분증',
                    '본인 명의 통장'
                ],
                'tips' => [
                    '지역본부 위치는 농협 고객센터 문의',
                    '평일 오전 9시~오후 4시 방문 권장'
                ]
            ]
        ],
        '3등' => [
            'title' => '로또 3등 당첨금 수령 방법',
            'description' => '로또 3등 당첨금은 가까운 농협 지점에서 수령 가능합니다',
            'content' => [
                'intro' => '3등 당첨금은 전국 어느 농협 지점에서나 수령 가능합니다.',
                'location' => '수령 장소: 전국 농협은행 지점',
                'documents' => [
                    '당첨 복권 원본',
                    '신분증'
                ],
                'tips' => [
                    '통장 없이도 현금 수령 가능',
                    '대기 시간 단축을 위해 오전 방문 권장'
                ]
            ]
        ],
        '4등' => [
            'title' => '로또 4등 당첨금 수령 방법',
            'description' => '로또 4등 5만원, 판매점에서 바로 수령하세요',
            'content' => [
                'intro' => '4등 당첨금(5만원)은 가까운 복권 판매점에서 바로 수령 가능합니다.',
                'location' => '수령 장소: 전국 복권 판매점',
                'documents' => ['당첨 복권만 있으면 OK'],
                'tips' => [
                    '신분증 불필요',
                    '현금으로 즉시 지급',
                    '농협 지점에서도 수령 가능'
                ]
            ]
        ],
        '5등' => [
            'title' => '로또 5등 당첨금 수령 방법',
            'description' => '로또 5등 5천원, 판매점에서 바로 현금 수령',
            'content' => [
                'intro' => '5등 당첨금(5,000원)은 어느 복권 판매점에서나 즉시 수령됩니다.',
                'location' => '수령 장소: 전국 복권 판매점',
                'documents' => ['당첨 복권만 있으면 OK'],
                'tips' => [
                    '세금 없음 (5만원 이하 비과세)',
                    '다음 복권 구매에 바로 사용 가능'
                ]
            ]
        ]
    ]
];

// 현재 페이지 데이터
$data = $page_data[$guide_type][$sub_type] ?? null;
if (!$data) {
    header('Location: /guide/' . urlencode($guide_type) . '/');
    exit;
}

$page_title = $data['title'] . ' | 오늘로또';
$page_desc = $data['description'];
$canonical = "https://lottoinsight.ai/guide/" . urlencode($guide_type) . "/" . urlencode($sub_type) . "/";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  <link rel="canonical" href="<?= $canonical ?>">
  
  <!-- Open Graph -->
  <meta property="og:title" content="<?= htmlspecialchars($data['title']) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
  <meta property="og:url" content="<?= $canonical ?>">
  <meta property="og:type" content="article">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
  
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: 'Noto Sans KR', sans-serif;
      background: linear-gradient(180deg, #050a15 0%, #0a1628 50%, #0d1f3c 100%);
      color: #e2e8f0;
      min-height: 100vh;
      line-height: 1.7;
    }
    
    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 40px 24px;
    }
    
    /* 브레드크럼 */
    .breadcrumb {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 32px;
      font-size: 0.9rem;
    }
    
    .breadcrumb a {
      color: #64748b;
      text-decoration: none;
    }
    
    .breadcrumb a:hover { color: #00E0A4; }
    .breadcrumb span { color: #475569; }
    .breadcrumb .current { color: #94a3b8; }
    
    /* 헤더 */
    .page-header {
      text-align: center;
      margin-bottom: 48px;
    }
    
    .page-icon {
      font-size: 4rem;
      margin-bottom: 16px;
    }
    
    .page-title {
      font-family: 'Outfit', sans-serif;
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 12px;
      background: linear-gradient(135deg, #fff, #94a3b8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .page-desc {
      color: #94a3b8;
      font-size: 1.1rem;
    }
    
    /* 콘텐츠 카드 */
    .content-card {
      background: rgba(13, 24, 41, 0.8);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 20px;
      padding: 32px;
      margin-bottom: 24px;
    }
    
    .content-card h2 {
      font-size: 1.3rem;
      font-weight: 700;
      margin-bottom: 16px;
      color: #fff;
    }
    
    .content-card p {
      color: #94a3b8;
      margin-bottom: 16px;
    }
    
    .highlight-box {
      background: linear-gradient(135deg, rgba(0,224,164,0.1), rgba(0,212,255,0.05));
      border: 1px solid rgba(0,224,164,0.3);
      border-radius: 12px;
      padding: 20px;
      margin: 20px 0;
    }
    
    .highlight-box .rate {
      font-size: 1.2rem;
      font-weight: 700;
      color: #00E0A4;
    }
    
    /* 예시 박스 */
    .example-box {
      background: rgba(0,0,0,0.3);
      border-radius: 12px;
      padding: 24px;
      margin: 20px 0;
    }
    
    .example-box h3 {
      font-size: 1rem;
      color: #FFD75F;
      margin-bottom: 16px;
    }
    
    .example-box ul {
      list-style: none;
    }
    
    .example-box li {
      padding: 8px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      color: #cbd5e1;
    }
    
    .example-box li:last-child {
      border-bottom: none;
      font-weight: 700;
      color: #00E0A4;
    }
    
    /* 팁 리스트 */
    .tips-list {
      list-style: none;
    }
    
    .tips-list li {
      padding: 12px 0;
      padding-left: 28px;
      position: relative;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .tips-list li::before {
      content: '💡';
      position: absolute;
      left: 0;
    }
    
    /* 문서 리스트 */
    .doc-list {
      list-style: none;
    }
    
    .doc-list li {
      padding: 10px 0;
      padding-left: 28px;
      position: relative;
    }
    
    .doc-list li::before {
      content: '📄';
      position: absolute;
      left: 0;
    }
    
    /* 프로세스 */
    .process-list {
      list-style: none;
      counter-reset: process;
    }
    
    .process-list li {
      padding: 12px 0 12px 40px;
      position: relative;
      border-left: 2px solid rgba(0,224,164,0.3);
      margin-left: 16px;
    }
    
    .process-list li::before {
      counter-increment: process;
      content: counter(process);
      position: absolute;
      left: -17px;
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #00E0A4, #00D4FF);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: #050a15;
      font-size: 0.9rem;
    }
    
    /* 관련 페이지 */
    .related-section {
      margin-top: 48px;
    }
    
    .related-section h3 {
      font-size: 1.1rem;
      margin-bottom: 16px;
      color: #94a3b8;
    }
    
    .related-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 12px;
    }
    
    .related-link {
      display: block;
      padding: 16px;
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 12px;
      text-decoration: none;
      text-align: center;
      transition: all 0.3s;
    }
    
    .related-link:hover {
      border-color: #00E0A4;
      background: rgba(0,224,164,0.05);
    }
    
    .related-link .icon { font-size: 1.5rem; display: block; margin-bottom: 8px; }
    .related-link .text { color: #94a3b8; font-size: 0.85rem; }
    
    /* CTA */
    .cta-section {
      text-align: center;
      padding: 40px;
      background: linear-gradient(135deg, rgba(0,224,164,0.1), rgba(0,212,255,0.05));
      border-radius: 20px;
      margin-top: 48px;
    }
    
    .cta-btn {
      display: inline-block;
      padding: 16px 32px;
      background: linear-gradient(135deg, #00E0A4, #00D4FF);
      color: #050a15;
      font-weight: 700;
      text-decoration: none;
      border-radius: 12px;
      margin-top: 16px;
    }
    
    @media (max-width: 768px) {
      .page-title { font-size: 1.6rem; }
      .content-card { padding: 24px; }
    }
  </style>

  <!-- Schema.org -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "<?= htmlspecialchars($data['title']) ?>",
    "description": "<?= htmlspecialchars($page_desc) ?>",
    "step": [
      <?php if (isset($data['content']['process'])): ?>
      <?php foreach ($data['content']['process'] as $idx => $step): ?>
      {
        "@type": "HowToStep",
        "position": <?= $idx + 1 ?>,
        "text": "<?= htmlspecialchars($step) ?>"
      }<?= $idx < count($data['content']['process']) - 1 ? ',' : '' ?>
      <?php endforeach; ?>
      <?php endif; ?>
    ]
  }
  </script>
</head>
<body>
  <div class="container">
    <!-- 브레드크럼 -->
    <nav class="breadcrumb">
      <a href="/">홈</a>
      <span>›</span>
      <a href="/guide/">가이드</a>
      <span>›</span>
      <a href="/guide/<?= urlencode($guide_type) ?>/"><?= htmlspecialchars($guide_type) ?></a>
      <span>›</span>
      <span class="current"><?= htmlspecialchars($sub_type) ?></span>
    </nav>
    
    <!-- 헤더 -->
    <header class="page-header">
      <div class="page-icon"><?= $guide_type === '세금' ? '💰' : ($guide_type === '수령방법' ? '🏦' : '📚') ?></div>
      <h1 class="page-title"><?= htmlspecialchars($data['title']) ?></h1>
      <p class="page-desc"><?= htmlspecialchars($page_desc) ?></p>
    </header>
    
    <!-- 메인 콘텐츠 -->
    <main>
      <!-- 소개 -->
      <div class="content-card">
        <h2>📌 개요</h2>
        <p><?= htmlspecialchars($data['content']['intro']) ?></p>
        
        <?php if (isset($data['content']['tax_rate'])): ?>
        <div class="highlight-box">
          <p class="rate"><?= htmlspecialchars($data['content']['tax_rate']) ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($data['content']['location'])): ?>
        <div class="highlight-box">
          <p class="rate">📍 <?= htmlspecialchars($data['content']['location']) ?></p>
        </div>
        <?php endif; ?>
      </div>
      
      <?php if (isset($data['content']['documents'])): ?>
      <!-- 필요 서류 -->
      <div class="content-card">
        <h2>📋 필요 서류</h2>
        <ul class="doc-list">
          <?php foreach ($data['content']['documents'] as $doc): ?>
          <li><?= htmlspecialchars($doc) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      
      <?php if (isset($data['content']['process'])): ?>
      <!-- 수령 절차 -->
      <div class="content-card">
        <h2>🔄 수령 절차</h2>
        <ol class="process-list">
          <?php foreach ($data['content']['process'] as $step): ?>
          <li><?= htmlspecialchars(preg_replace('/^[0-9]+\.\s*/', '', $step)) ?></li>
          <?php endforeach; ?>
        </ol>
      </div>
      <?php endif; ?>
      
      <?php if (isset($data['content']['example'])): ?>
      <!-- 계산 예시 -->
      <div class="content-card">
        <h2>🧮 <?= htmlspecialchars($data['content']['example_title'] ?? '계산 예시') ?></h2>
        <div class="example-box">
          <ul>
            <?php foreach ($data['content']['example'] as $ex): ?>
            <li><?= htmlspecialchars($ex) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>
      
      <?php if (isset($data['content']['tips'])): ?>
      <!-- 알아두세요 -->
      <div class="content-card">
        <h2>💡 알아두세요</h2>
        <ul class="tips-list">
          <?php foreach ($data['content']['tips'] as $tip): ?>
          <li><?= htmlspecialchars($tip) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </main>
    
    <!-- 관련 페이지 -->
    <section class="related-section">
      <h3>🔗 관련 가이드</h3>
      <div class="related-grid">
        <?php foreach ($valid_guides[$guide_type] as $sub): ?>
        <?php if ($sub !== $sub_type): ?>
        <a href="/guide/<?= urlencode($guide_type) ?>/<?= urlencode($sub) ?>/" class="related-link">
          <span class="icon"><?= $guide_type === '세금' ? '💵' : '📄' ?></span>
          <span class="text"><?= htmlspecialchars($sub) ?> <?= $guide_type ?></span>
        </a>
        <?php endif; ?>
        <?php endforeach; ?>
        <a href="/guide/<?= urlencode($guide_type) ?>/" class="related-link">
          <span class="icon">📚</span>
          <span class="text"><?= htmlspecialchars($guide_type) ?> 전체</span>
        </a>
      </div>
    </section>
    
    <!-- CTA -->
    <section class="cta-section">
      <h3>🎯 AI가 분석한 이번주 번호는?</h3>
      <p style="color: #94a3b8; margin-top: 8px;">10가지 알고리즘으로 최적의 번호 조합을 추천받으세요</p>
      <a href="/auth.php" class="cta-btn">🎲 무료 분석 시작하기</a>
    </section>
  </div>
  
  <?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

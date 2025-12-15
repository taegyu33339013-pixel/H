<?php
/**
 * 로또 당첨 확률 상세 페이지
 * URL: /로또-가이드/1등-확률/
 * 
 * 타겟 키워드: "로또 1등 확률", "로또 당첨 확률", "로또 확률 계산"
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');

$seo = [
    'title' => '로또 당첨 확률 완벽 정리 - 1등부터 5등까지 | 오늘로또',
    'desc' => '로또 6/45 당첨 확률을 수학적으로 분석합니다. 1등 확률 1/8,145,060부터 5등까지. 기대값, 역대 최다 1등 배출 등 통계 정보.',
    'url' => 'https://lottoinsight.ai/로또-가이드/1등-확률/',
    'keywords' => '로또 확률, 로또 1등 확률, 로또 당첨 확률, 로또 기대값, 로또 확률 계산'
];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?php include(__DIR__ . '/_seo_head.php'); ?>

<!-- HowTo Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "로또 6/45 당첨 확률 완벽 분석",
  "description": "로또 1등부터 5등까지의 정확한 당첨 확률과 수학적 기대값 분석",
  "author": {
    "@type": "Organization",
    "name": "오늘로또"
  },
  "publisher": {
    "@type": "Organization",
    "name": "오늘로또",
    "logo": {
      "@type": "ImageObject",
      "url": "https://lottoinsight.ai/favicon.svg"
    }
  },
  "datePublished": "2024-01-01",
  "dateModified": "<?= date('Y-m-d') ?>"
}
</script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
  font-family: 'Pretendard', -apple-system, sans-serif;
  background: linear-gradient(135deg, #0B132B 0%, #1C2541 100%);
  color: #fff;
  min-height: 100vh;
  line-height: 1.7;
}
.container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
.breadcrumb { font-size: 14px; color: #888; margin-bottom: 20px; }
.breadcrumb a { color: #00E0A4; text-decoration: none; }

h1 { font-size: 2.2rem; margin-bottom: 20px; line-height: 1.3; }
h2 { font-size: 1.5rem; margin: 40px 0 20px; color: #00E0A4; }
h3 { font-size: 1.2rem; margin: 30px 0 15px; }

.intro {
  background: rgba(0,224,164,0.1);
  padding: 24px;
  border-radius: 16px;
  margin-bottom: 40px;
  border-left: 4px solid #00E0A4;
}

.probability-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  background: rgba(255,255,255,0.05);
  border-radius: 12px;
  overflow: hidden;
}
.probability-table th, .probability-table td {
  padding: 16px;
  text-align: center;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}
.probability-table th {
  background: rgba(0,224,164,0.2);
  font-weight: 600;
}
.probability-table tr:last-child td { border-bottom: none; }
.probability-table .rank-1 { color: #FFD93D; font-weight: 700; }
.probability-table .rank-2 { color: #C0C0C0; }
.probability-table .highlight { color: #00E0A4; font-weight: 600; }

.formula-box {
  background: rgba(139,92,246,0.1);
  padding: 24px;
  border-radius: 12px;
  margin: 20px 0;
  font-family: 'Courier New', monospace;
  text-align: center;
}
.formula { font-size: 1.3rem; color: #8B5CF6; }

.stat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin: 30px 0;
}
.stat-card {
  background: rgba(255,255,255,0.05);
  padding: 24px;
  border-radius: 16px;
  text-align: center;
}
.stat-card .value {
  font-size: 2rem;
  font-weight: 800;
  color: #00E0A4;
}
.stat-card .label { color: #888; margin-top: 8px; }

.comparison-section {
  background: rgba(255,255,255,0.03);
  padding: 30px;
  border-radius: 16px;
  margin: 30px 0;
}

.cta-box {
  text-align: center;
  padding: 40px;
  background: linear-gradient(135deg, rgba(0,224,164,0.2), rgba(0,200,150,0.1));
  border-radius: 20px;
  margin: 40px 0;
}
.cta-btn {
  display: inline-block;
  background: linear-gradient(135deg, #00E0A4, #00C896);
  color: #000;
  font-weight: 700;
  padding: 16px 40px;
  border-radius: 50px;
  text-decoration: none;
  margin-top: 16px;
}

.related-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
  margin-top: 40px;
}
.related-link {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255,255,255,0.05);
  padding: 16px;
  border-radius: 12px;
  text-decoration: none;
  color: #fff;
}
.related-link:hover { background: rgba(0,224,164,0.1); }

p { margin: 16px 0; color: #ddd; }
ul, ol { margin: 16px 0; padding-left: 24px; color: #ddd; }
li { margin: 8px 0; }
strong { color: #fff; }

@media (max-width: 600px) {
  h1 { font-size: 1.6rem; }
  .probability-table { font-size: 14px; }
  .probability-table th, .probability-table td { padding: 10px 8px; }
}
</style>
</head>
<body>

<div class="container">
  <nav class="breadcrumb">
    <a href="/">홈</a> &gt; 
    <a href="/로또-가이드/">로또 가이드</a> &gt; 
    <span>로또 당첨 확률</span>
  </nav>

  <h1>🎯 로또 6/45 당첨 확률 완벽 분석</h1>
  
  <div class="intro">
    <p>로또 6/45는 1부터 45까지의 숫자 중 6개를 선택하는 복권입니다. 
    수학적으로 계산된 정확한 당첨 확률과 기대값을 알아봅니다.</p>
  </div>

  <h2>📊 등위별 당첨 확률</h2>
  
  <table class="probability-table">
    <thead>
      <tr>
        <th>등위</th>
        <th>맞춘 번호</th>
        <th>당첨 확률</th>
        <th>분수 표현</th>
        <th>평균 당첨금</th>
      </tr>
    </thead>
    <tbody>
      <tr class="rank-1">
        <td>🥇 1등</td>
        <td>6개 전부</td>
        <td class="highlight">0.0000123%</td>
        <td>1 / 8,145,060</td>
        <td>~20억원</td>
      </tr>
      <tr class="rank-2">
        <td>🥈 2등</td>
        <td>5개 + 보너스</td>
        <td>0.0000737%</td>
        <td>1 / 1,357,510</td>
        <td>~5천만원</td>
      </tr>
      <tr>
        <td>🥉 3등</td>
        <td>5개</td>
        <td>0.00280%</td>
        <td>1 / 35,724</td>
        <td>~150만원</td>
      </tr>
      <tr>
        <td>4등</td>
        <td>4개</td>
        <td>0.0733%</td>
        <td>1 / 1,366</td>
        <td>5만원 (고정)</td>
      </tr>
      <tr>
        <td>5등</td>
        <td>3개</td>
        <td>1.77%</td>
        <td>1 / 56.5</td>
        <td>5천원 (고정)</td>
      </tr>
    </tbody>
  </table>

  <h2>🔢 1등 확률 계산 공식</h2>
  
  <div class="formula-box">
    <div class="formula">₄₅C₆ = 45! / (6! × 39!) = 8,145,060</div>
    <p style="margin-top: 16px; color: #aaa;">45개 중 6개를 선택하는 조합의 수</p>
  </div>

  <p>로또 1등 당첨 확률 <strong>1/8,145,060</strong>은 다음과 같은 의미입니다:</p>
  <ul>
    <li>매주 1게임씩 구매 시 평균 <strong>15만 6천년</strong> 소요</li>
    <li>매주 5게임씩 구매해도 평균 <strong>3만 1천년</strong> 소요</li>
    <li>한국 성인 인구(약 4천만 명) 중 매주 약 <strong>5명</strong>이 1등 당첨</li>
  </ul>

  <h2>📈 역대 통계</h2>
  
  <div class="stat-grid">
    <div class="stat-card">
      <div class="value">1,200+</div>
      <div class="label">누적 추첨 회차</div>
    </div>
    <div class="stat-card">
      <div class="value">~6,200명</div>
      <div class="label">누적 1등 당첨자</div>
    </div>
    <div class="stat-card">
      <div class="value">~407억원</div>
      <div class="label">역대 최고 1등 당첨금</div>
    </div>
    <div class="stat-card">
      <div class="value">~5.2명</div>
      <div class="label">회차당 평균 1등 당첨자</div>
    </div>
  </div>

  <h2>💰 기대값 분석</h2>
  
  <div class="comparison-section">
    <h3>로또 1게임(1,000원) 기대값</h3>
    <p>모든 등위의 당첨 확률과 평균 당첨금을 곱한 기대값:</p>
    <ul>
      <li>1등 기대값: 20억 × (1/8,145,060) = 약 245원</li>
      <li>2등 기대값: 5천만 × (1/1,357,510) = 약 37원</li>
      <li>3등 기대값: 150만 × (1/35,724) = 약 42원</li>
      <li>4등 기대값: 5만 × (1/1,366) = 약 37원</li>
      <li>5등 기대값: 5천 × (1/56.5) = 약 88원</li>
    </ul>
    <p style="margin-top: 20px;"><strong>총 기대값: 약 449원</strong> (투자금 1,000원의 약 45%)</p>
    <p style="color: #888;">※ 기대값은 판매량과 당첨금 변동에 따라 달라질 수 있습니다.</p>
  </div>

  <h2>🎲 확률 비교</h2>
  
  <table class="probability-table">
    <thead>
      <tr>
        <th>이벤트</th>
        <th>확률</th>
        <th>로또 1등 대비</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>동전 앞면 연속 23번</td>
        <td>1/8,388,608</td>
        <td>거의 동일</td>
      </tr>
      <tr>
        <td>벼락 맞을 확률 (연간)</td>
        <td>1/1,000,000</td>
        <td>8배 높음</td>
      </tr>
      <tr>
        <td>비행기 사고 사망</td>
        <td>1/11,000,000</td>
        <td>0.7배 (더 낮음)</td>
      </tr>
      <tr>
        <td>완벽한 NCAA 브라켓</td>
        <td>1/9,200,000,000,000,000,000</td>
        <td>훨씬 낮음</td>
      </tr>
    </tbody>
  </table>

  <div class="cta-box">
    <h3>🎯 확률은 모두 같지만, 번호 선택은 다릅니다</h3>
    <p>AI가 분석한 균형 잡힌 번호 조합으로 시작해보세요</p>
    <a href="/auth.php" class="cta-btn">무료 AI 분석 받기</a>
  </div>

  <h2>🔗 관련 페이지</h2>
  <div class="related-links">
    <a href="/로또-가이드/세금/" class="related-link">
      <span>💰</span>
      <div>
        <strong>로또 당첨금 세금</strong>
        <p style="color: #888; font-size: 13px;">실수령액 계산 방법</p>
      </div>
    </a>
    <a href="/로또-통계/자동수동/" class="related-link">
      <span>🎰</span>
      <div>
        <strong>자동 vs 수동 통계</strong>
        <p style="color: #888; font-size: 13px;">어떤 방식이 더 나을까?</p>
      </div>
    </a>
    <a href="/로또-분석/" class="related-link">
      <span>📊</span>
      <div>
        <strong>로또 패턴 분석</strong>
        <p style="color: #888; font-size: 13px;">홀짝, 연속번호 등</p>
      </div>
    </a>
    <a href="/로또-도구/세금-계산기/" class="related-link">
      <span>🧮</span>
      <div>
        <strong>세금 계산기</strong>
        <p style="color: #888; font-size: 13px;">당첨금 실수령액 계산</p>
      </div>
    </a>
  </div>
</div>

<?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

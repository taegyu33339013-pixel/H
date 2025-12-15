<?php
/**
 * 이번주 로또 정보 페이지
 * URL: /로또-당첨번호/이번주/
 * 
 * 타겟 키워드: "이번주 로또", "이번주 로또 추첨일", "로또 몇회차"
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');

// 최신 회차 정보
$row = sql_fetch("SELECT * FROM g5_lotto_draw ORDER BY draw_no DESC LIMIT 1");
$latest_round = (int)($row['draw_no'] ?? 0);
$next_round = $latest_round + 1;

// 다음 토요일 계산
$next_saturday = strtotime('next saturday');
if (date('w') == 6 && date('H') < 21) {
    $next_saturday = strtotime('today');
} elseif (date('w') == 6 && date('H') >= 21) {
    $next_saturday = strtotime('next saturday');
}

$draw_date = date('Y년 n월 j일', $next_saturday);
$draw_date_iso = date('Y-m-d', $next_saturday);

// 남은 시간 계산
$draw_time = strtotime($draw_date_iso . ' 20:45:00');
$remaining = $draw_time - time();
$days = floor($remaining / 86400);
$hours = floor(($remaining % 86400) / 3600);
$minutes = floor(($remaining % 3600) / 60);

// 최근 당첨번호
$numbers = [];
if ($row) {
    for ($i = 1; $i <= 6; $i++) {
        $numbers[] = (int)$row["num{$i}"];
    }
    $bonus = (int)$row['bonus'];
}

$seo = [
    'title' => "이번주 로또 제{$next_round}회 추첨 - {$draw_date} | 오늘로또",
    'desc' => "이번주 로또 6/45 제{$next_round}회 추첨일은 {$draw_date} 토요일 오후 8시 45분입니다. 지난주 {$latest_round}회 당첨번호 확인 및 AI 분석 번호 추천.",
    'url' => "https://lottoinsight.ai/로또-당첨번호/이번주/",
    'keywords' => "이번주 로또, 로또 추첨일, 로또 몇회차, {$next_round}회 로또, 로또 당첨번호"
];

include(__DIR__ . '/_seo_head.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?php include(__DIR__ . '/_seo_head.php'); ?>

<!-- Event Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "제<?= $next_round ?>회 로또 6/45 추첨",
  "startDate": "<?= $draw_date_iso ?>T20:45:00+09:00",
  "endDate": "<?= $draw_date_iso ?>T21:00:00+09:00",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",
  "location": {
    "@type": "VirtualLocation",
    "url": "https://www.dhlottery.co.kr"
  },
  "organizer": {
    "@type": "Organization",
    "name": "동행복권"
  }
}
</script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
  font-family: 'Pretendard', -apple-system, sans-serif;
  background: linear-gradient(135deg, #0B132B 0%, #1C2541 100%);
  color: #fff;
  min-height: 100vh;
}
.container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
.breadcrumb { font-size: 14px; color: #888; margin-bottom: 20px; }
.breadcrumb a { color: #00E0A4; text-decoration: none; }

.hero {
  text-align: center;
  padding: 60px 20px;
  background: linear-gradient(135deg, rgba(0,224,164,0.1), rgba(139,92,246,0.1));
  border-radius: 24px;
  margin-bottom: 40px;
}
.hero h1 { font-size: 2.5rem; margin-bottom: 16px; }
.hero .round { color: #00E0A4; }
.hero .date { font-size: 1.3rem; color: #ccc; margin-bottom: 30px; }

.countdown {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin: 30px 0;
}
.countdown-item {
  background: rgba(255,255,255,0.1);
  padding: 20px 30px;
  border-radius: 16px;
  text-align: center;
}
.countdown-number {
  font-size: 3rem;
  font-weight: 800;
  color: #00E0A4;
}
.countdown-label { font-size: 14px; color: #888; }

.section {
  background: rgba(255,255,255,0.05);
  border-radius: 20px;
  padding: 30px;
  margin-bottom: 30px;
}
.section h2 {
  font-size: 1.5rem;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.balls-container {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}
.ball {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.2rem;
  color: #fff;
}
.ball-1-10 { background: linear-gradient(135deg, #FFD93D, #FF9500); }
.ball-11-20 { background: linear-gradient(135deg, #6BCB77, #3DA35D); }
.ball-21-30 { background: linear-gradient(135deg, #FF6B6B, #EE4444); }
.ball-31-40 { background: linear-gradient(135deg, #4D96FF, #0066CC); }
.ball-41-45 { background: linear-gradient(135deg, #9B59B6, #8E44AD); }
.ball-bonus { 
  background: linear-gradient(135deg, #888, #666);
  position: relative;
}
.plus { font-size: 1.5rem; color: #666; }

.cta-section {
  text-align: center;
  padding: 40px;
  background: linear-gradient(135deg, rgba(0,224,164,0.2), rgba(0,200,150,0.1));
  border-radius: 20px;
  margin: 40px 0;
}
.cta-section h3 { font-size: 1.8rem; margin-bottom: 16px; }
.cta-btn {
  display: inline-block;
  background: linear-gradient(135deg, #00E0A4, #00C896);
  color: #000;
  font-weight: 700;
  font-size: 1.1rem;
  padding: 16px 40px;
  border-radius: 50px;
  text-decoration: none;
  margin-top: 20px;
}
.cta-btn:hover { transform: scale(1.05); }

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}
.info-card {
  background: rgba(255,255,255,0.05);
  padding: 20px;
  border-radius: 12px;
  text-align: center;
}
.info-card .icon { font-size: 2rem; margin-bottom: 10px; }
.info-card h4 { color: #00E0A4; margin-bottom: 8px; }
.info-card p { color: #aaa; font-size: 14px; }

.links-section { margin-top: 40px; }
.links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}
.link-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255,255,255,0.05);
  padding: 16px 20px;
  border-radius: 12px;
  text-decoration: none;
  color: #fff;
  transition: all 0.3s;
}
.link-card:hover { background: rgba(0,224,164,0.1); }
.link-card .icon { font-size: 1.5rem; }

@media (max-width: 600px) {
  .hero h1 { font-size: 1.8rem; }
  .countdown { flex-wrap: wrap; }
  .countdown-item { padding: 15px 20px; }
  .countdown-number { font-size: 2rem; }
}
</style>
</head>
<body>

<div class="container">
  <nav class="breadcrumb">
    <a href="/">홈</a> &gt; 
    <a href="/로또-당첨번호/<?= $latest_round ?>/">로또 당첨번호</a> &gt; 
    <span>이번주 로또</span>
  </nav>

  <section class="hero">
    <h1>🎱 이번주 로또 <span class="round">제<?= number_format($next_round) ?>회</span></h1>
    <p class="date">추첨일: <?= $draw_date ?> (토) 오후 8시 45분</p>
    
    <div class="countdown">
      <div class="countdown-item">
        <div class="countdown-number" id="days"><?= $days ?></div>
        <div class="countdown-label">일</div>
      </div>
      <div class="countdown-item">
        <div class="countdown-number" id="hours"><?= $hours ?></div>
        <div class="countdown-label">시간</div>
      </div>
      <div class="countdown-item">
        <div class="countdown-number" id="minutes"><?= $minutes ?></div>
        <div class="countdown-label">분</div>
      </div>
    </div>
    
    <a href="/auth.php" class="cta-btn">🎯 AI 번호 추천받기</a>
  </section>

  <?php if ($row): ?>
  <section class="section">
    <h2>📊 지난주 <?= $latest_round ?>회 당첨번호</h2>
    <div class="balls-container">
      <?php foreach ($numbers as $num): 
        $color = $num <= 10 ? '1-10' : ($num <= 20 ? '11-20' : ($num <= 30 ? '21-30' : ($num <= 40 ? '31-40' : '41-45')));
      ?>
      <a href="/로또-번호/<?= $num ?>/" class="ball ball-<?= $color ?>" title="로또 <?= $num ?>번 통계"><?= $num ?></a>
      <?php endforeach; ?>
      <span class="plus">+</span>
      <div class="ball ball-bonus" title="보너스 번호"><?= $bonus ?></div>
    </div>
    <p style="text-align: center; margin-top: 20px; color: #888;">
      1등 당첨금: <?= number_format($row['prize_1st'] ?? 0) ?>원 
      (<?= $row['winner_1st'] ?? 0 ?>명)
    </p>
  </section>
  <?php endif; ?>

  <section class="section">
    <h2>📅 로또 추첨 정보</h2>
    <div class="info-grid">
      <div class="info-card">
        <div class="icon">🎰</div>
        <h4>추첨 방송</h4>
        <p>MBC 생방송<br>매주 토요일 20:45</p>
      </div>
      <div class="info-card">
        <div class="icon">🎫</div>
        <h4>복권 판매</h4>
        <p>토요일 20:00까지<br>판매점 또는 동행복권</p>
      </div>
      <div class="info-card">
        <div class="icon">💰</div>
        <h4>1등 확률</h4>
        <p>1/8,145,060<br>(약 814만분의 1)</p>
      </div>
      <div class="info-card">
        <div class="icon">📍</div>
        <h4>당첨금 수령</h4>
        <p>1~3등: 농협은행<br>4~5등: 판매점</p>
      </div>
    </div>
  </section>

  <section class="cta-section">
    <h3>🎯 이번주 행운의 번호는?</h3>
    <p>23년간 당첨 데이터 AI 분석으로 균형 잡힌 번호 추천</p>
    <a href="/auth.php" class="cta-btn">무료 AI 분석 시작하기</a>
  </section>

  <section class="links-section">
    <h2 style="margin-bottom: 20px;">🔗 관련 페이지</h2>
    <div class="links-grid">
      <a href="/로또-당첨번호/<?= $latest_round ?>/" class="link-card">
        <span class="icon">🔢</span>
        <div>
          <strong><?= $latest_round ?>회 당첨번호 상세</strong>
          <p style="color: #888; font-size: 13px;">등위별 당첨금 확인</p>
        </div>
      </a>
      <a href="/로또-당첨번호/<?= $latest_round ?>/당첨점/" class="link-card">
        <span class="icon">🏆</span>
        <div>
          <strong><?= $latest_round ?>회 당첨 판매점</strong>
          <p style="color: #888; font-size: 13px;">1등 배출점 확인</p>
        </div>
      </a>
      <a href="/로또-판매점/" class="link-card">
        <span class="icon">📍</span>
        <div>
          <strong>내 주변 로또 명당</strong>
          <p style="color: #888; font-size: 13px;">1등 많이 나온 판매점</p>
        </div>
      </a>
      <a href="/로또-도구/세금-계산기/" class="link-card">
        <span class="icon">🧮</span>
        <div>
          <strong>당첨금 세금 계산기</strong>
          <p style="color: #888; font-size: 13px;">실수령액 자동 계산</p>
        </div>
      </a>
    </div>
  </section>
</div>

<script>
// 실시간 카운트다운
function updateCountdown() {
  const drawTime = new Date('<?= $draw_date_iso ?>T20:45:00+09:00').getTime();
  const now = new Date().getTime();
  const diff = drawTime - now;
  
  if (diff <= 0) {
    document.getElementById('days').textContent = '0';
    document.getElementById('hours').textContent = '0';
    document.getElementById('minutes').textContent = '0';
    return;
  }
  
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  
  document.getElementById('days').textContent = days;
  document.getElementById('hours').textContent = hours;
  document.getElementById('minutes').textContent = minutes;
}

setInterval(updateCountdown, 60000);
</script>

<?php include(__DIR__ . '/_footer.php'); ?>
</body>
</html>

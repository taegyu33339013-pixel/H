<?php
/**
 * 빠른 링크 섹션 컴포넌트
 * 메인, result.php 등에서 사용
 * 
 * 사용법: 
 * $quick_links_style = 'grid'; // 또는 'compact'
 * include(G5_PATH . '/seo/_quick_links.php');
 */

$quick_links_style = $quick_links_style ?? 'grid';

// 최신 회차
$ql_max_round = 0;
if (function_exists('sql_fetch')) {
    $ql_row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
    $ql_max_round = (int)($ql_row['max_round'] ?? 0);
}
?>

<?php if ($quick_links_style === 'grid'): ?>
<!-- 그리드 스타일 (메인 페이지용) -->
<section class="quick-links-section">
  <div class="quick-links-container">
    <h2 class="quick-links-title">📚 로또 완벽 가이드</h2>
    <p class="quick-links-subtitle">당첨부터 수령까지, 필요한 모든 정보</p>
    
    <div class="quick-links-grid">
      <a href="/guide/세금/" class="quick-link-card">
        <span class="ql-icon">💰</span>
        <span class="ql-title">세금 계산기</span>
        <span class="ql-desc">실수령액 자동 계산</span>
      </a>
      <a href="/guide/구매방법/" class="quick-link-card">
        <span class="ql-icon">🎫</span>
        <span class="ql-title">구매 방법</span>
        <span class="ql-desc">온라인/오프라인</span>
      </a>
      <a href="/guide/확률/" class="quick-link-card">
        <span class="ql-icon">📊</span>
        <span class="ql-title">당첨 확률</span>
        <span class="ql-desc">수학적 분석</span>
      </a>
      <a href="/stores/" class="quick-link-card highlight">
        <span class="ql-icon">🏆</span>
        <span class="ql-title">전국 명당</span>
        <span class="ql-desc">1등 많이 나온 곳</span>
      </a>
      <a href="/ranking/stores/" class="quick-link-card">
        <span class="ql-icon">🥇</span>
        <span class="ql-title">명당 랭킹</span>
        <span class="ql-desc">TOP 100</span>
      </a>
      <a href="/analysis/" class="quick-link-card">
        <span class="ql-icon">🔬</span>
        <span class="ql-title">패턴 분석</span>
        <span class="ql-desc">홀짝, 연속번호</span>
      </a>
    </div>
    
    <div class="quick-links-more">
      <a href="/guide/" class="ql-more-link">가이드 전체보기 →</a>
      <a href="/stats/" class="ql-more-link">통계 전체보기 →</a>
    </div>
  </div>
</section>

<style>
.quick-links-section {
  padding: 60px 24px;
  background: linear-gradient(180deg, rgba(13,21,38,0.5) 0%, rgba(5,10,21,0.8) 100%);
}

.quick-links-container {
  max-width: 1000px;
  margin: 0 auto;
  text-align: center;
}

.quick-links-title {
  font-family: 'Outfit', sans-serif;
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: 8px;
  color: #fff;
}

.quick-links-subtitle {
  color: #94a3b8;
  margin-bottom: 32px;
}

.quick-links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.quick-link-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 24px 16px;
  background: rgba(13,24,41,0.8);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px;
  text-decoration: none;
  transition: all 0.3s;
}

.quick-link-card:hover {
  border-color: #00E0A4;
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(0,224,164,0.1);
}

.quick-link-card.highlight {
  background: linear-gradient(145deg, rgba(0,224,164,0.1), rgba(0,224,164,0.02));
  border-color: rgba(0,224,164,0.3);
}

.ql-icon {
  font-size: 2rem;
  margin-bottom: 12px;
}

.ql-title {
  color: #fff;
  font-weight: 700;
  font-size: 1rem;
  margin-bottom: 4px;
}

.ql-desc {
  color: #64748b;
  font-size: 0.8rem;
}

.quick-links-more {
  display: flex;
  justify-content: center;
  gap: 24px;
  flex-wrap: wrap;
}

.ql-more-link {
  color: #94a3b8;
  text-decoration: none;
  font-size: 0.9rem;
  padding: 8px 16px;
  border-radius: 8px;
  transition: all 0.2s;
}

.ql-more-link:hover {
  color: #00E0A4;
  background: rgba(0,224,164,0.1);
}

@media (max-width: 768px) {
  .quick-links-title {
    font-size: 1.4rem;
  }
  
  .quick-links-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  .quick-link-card {
    padding: 16px 12px;
  }
  
  .ql-icon {
    font-size: 1.5rem;
  }
  
  .ql-title {
    font-size: 0.9rem;
  }
}
</style>

<?php else: ?>
<!-- 컴팩트 스타일 (result.php용) -->
<section class="quick-links-compact">
  <div class="qlc-container">
    <h3 class="qlc-title">🔗 더 알아보기</h3>
    
    <div class="qlc-grid">
      <div class="qlc-group">
        <h4>🏪 어디서 살까?</h4>
        <a href="/stores/">전국 명당 보기</a>
        <a href="/ranking/stores/">명당 랭킹 TOP 100</a>
      </div>
      
      <div class="qlc-group">
        <h4>📊 더 깊은 분석</h4>
        <a href="/analysis/홀짝/">홀짝 분석</a>
        <a href="/stats/자동수동/">자동 vs 수동</a>
        <a href="/ranking/numbers/">번호 순위</a>
      </div>
      
      <div class="qlc-group">
        <h4>💰 당첨되면?</h4>
        <a href="/guide/세금/">세금 계산하기</a>
        <a href="/guide/수령방법/">수령 방법</a>
      </div>
      
      <div class="qlc-group">
        <h4>📅 회차 정보</h4>
        <a href="/lotto/<?= $ql_max_round ?>/">최신 당첨번호</a>
        <a href="/lotto/<?= $ql_max_round ?>/winners/">최근 당첨점</a>
        <a href="/lotto/<?= $ql_max_round ?>/prize/">당첨금 상세</a>
      </div>
    </div>
  </div>
</section>

<style>
.quick-links-compact {
  padding: 32px 24px;
  background: rgba(13,24,41,0.5);
  border-radius: 20px;
  margin: 32px 0;
}

.qlc-container {
  max-width: 800px;
  margin: 0 auto;
}

.qlc-title {
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: #fff;
}

.qlc-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 24px;
}

.qlc-group h4 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #94a3b8;
  margin-bottom: 12px;
}

.qlc-group a {
  display: block;
  color: #64748b;
  text-decoration: none;
  font-size: 0.85rem;
  padding: 6px 0;
  transition: color 0.2s;
}

.qlc-group a:hover {
  color: #00E0A4;
}

@media (max-width: 768px) {
  .qlc-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}
</style>
<?php endif; ?>

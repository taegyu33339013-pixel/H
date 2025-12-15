<?php
/**
 * result.php용 추가 섹션 컴포넌트
 * AI 분석 결과 페이지 하단에 표시
 * 
 * 사용법: include(G5_PATH . '/seo/_result_addons.php');
 */

// 판매점 라이브러리 로드
$store_lib_path = G5_PATH . '/lib/lotto_store.lib.php';
if (file_exists($store_lib_path) && !function_exists('li_get_top_stores')) {
    include_once($store_lib_path);
}

// 명당 TOP 3
$top_stores = [];
if (function_exists('li_get_top_stores')) {
    $top_stores = li_get_top_stores(3);
}

// 최신 회차
$addon_max_round = 0;
if (function_exists('sql_fetch')) {
    $addon_row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
    $addon_max_round = (int)($addon_row['max_round'] ?? 0);
}
?>

<!-- 명당 추천 섹션 -->
<section class="addon-section addon-stores">
  <h3 class="addon-title">🏆 이 번호 어디서 살까?</h3>
  <p class="addon-subtitle">1등 많이 나온 전국 명당 판매점</p>
  
  <?php if (!empty($top_stores)): ?>
  <div class="top-stores-grid">
    <?php foreach ($top_stores as $idx => $store): ?>
    <a href="/store/<?= $store['store_id'] ?>" class="top-store-card">
      <span class="store-rank"><?= $idx + 1 ?></span>
      <div class="store-info">
        <span class="store-name"><?= htmlspecialchars($store['store_name']) ?></span>
        <span class="store-region"><?= htmlspecialchars($store['region1'] ?? '') ?> <?= htmlspecialchars($store['region2'] ?? '') ?></span>
      </div>
      <div class="store-wins">
        <span class="wins-badge">🥇 <?= $store['wins_1st'] ?>회</span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p class="addon-empty">명당 데이터를 불러오는 중...</p>
  <?php endif; ?>
  
  <div class="addon-actions">
    <a href="/로또-판매점/" class="addon-btn primary">전국 로또 명당 보기</a>
    <a href="/로또-랭킹/stores/" class="addon-btn secondary">로또 명당 랭킹 TOP 100</a>
  </div>
</section>

<!-- 관련 분석 섹션 -->
<section class="addon-section addon-analysis">
  <h3 class="addon-title">📊 더 깊은 로또 분석</h3>
  
  <div class="analysis-grid">
    <a href="/로또-분석/홀짝/" class="analysis-card">
      <span class="analysis-icon">⚖️</span>
      <span class="analysis-name">로또 홀짝 분석</span>
    </a>
    <a href="/로또-분석/연속번호/" class="analysis-card">
      <span class="analysis-icon">🔢</span>
      <span class="analysis-name">로또 연속번호</span>
    </a>
    <a href="/로또-통계/자동수동/" class="analysis-card">
      <span class="analysis-icon">🎰</span>
      <span class="analysis-name">자동 vs 수동</span>
    </a>
    <a href="/로또-랭킹/numbers/" class="analysis-card">
      <span class="analysis-icon">📈</span>
      <span class="analysis-name">로또 번호 순위</span>
    </a>
  </div>
</section>

<!-- 당첨 후 가이드 -->
<section class="addon-section addon-guide">
  <h3 class="addon-title">💰 로또 당첨되면 어떻게?</h3>
  
  <div class="guide-grid">
    <a href="/로또-가이드/세금/" class="guide-card">
      <span class="guide-icon">🧮</span>
      <div>
        <span class="guide-name">로또 세금 계산기</span>
        <span class="guide-desc">실수령액 자동 계산</span>
      </div>
    </a>
    <a href="/로또-가이드/수령방법/" class="guide-card">
      <span class="guide-icon">🏦</span>
      <div>
        <span class="guide-name">로또 수령 방법</span>
        <span class="guide-desc">등수별 수령처 안내</span>
      </div>
    </a>
    <a href="/로또-당첨번호/<?= $addon_max_round ?>/당첨금/" class="guide-card">
      <span class="guide-icon">💵</span>
      <div>
        <span class="guide-name">이번주 로또 당첨금</span>
        <span class="guide-desc"><?= number_format($addon_max_round) ?>회 상세</span>
      </div>
    </a>
  </div>
</section>

<style>
.addon-section {
  background: rgba(13,24,41,0.6);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 28px;
  margin-bottom: 24px;
}

.addon-title {
  font-family: 'Outfit', sans-serif;
  font-size: 1.2rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 8px;
}

.addon-subtitle {
  color: #64748b;
  font-size: 0.9rem;
  margin-bottom: 20px;
}

.addon-empty {
  color: #64748b;
  text-align: center;
  padding: 20px;
}

/* 명당 카드 */
.top-stores-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 20px;
}

.top-store-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: rgba(0,0,0,0.2);
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s;
}

.top-store-card:hover {
  background: rgba(0,224,164,0.1);
  transform: translateX(4px);
}

.store-rank {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #FFD75F, #FFA500);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  color: #000;
}

.store-info {
  flex: 1;
}

.store-name {
  display: block;
  color: #fff;
  font-weight: 600;
  font-size: 0.95rem;
}

.store-region {
  color: #64748b;
  font-size: 0.8rem;
}

.wins-badge {
  background: rgba(255,215,95,0.15);
  color: #FFD75F;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
}

.addon-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.addon-btn {
  flex: 1;
  min-width: 140px;
  padding: 12px 20px;
  border-radius: 10px;
  text-align: center;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s;
}

.addon-btn.primary {
  background: linear-gradient(135deg, #00E0A4, #00D4FF);
  color: #050a15;
}

.addon-btn.secondary {
  background: rgba(255,255,255,0.05);
  color: #94a3b8;
  border: 1px solid rgba(255,255,255,0.1);
}

.addon-btn:hover {
  transform: translateY(-2px);
}

/* 분석 그리드 */
.analysis-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.analysis-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px 12px;
  background: rgba(0,0,0,0.2);
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s;
}

.analysis-card:hover {
  background: rgba(0,224,164,0.1);
  transform: translateY(-2px);
}

.analysis-icon {
  font-size: 1.5rem;
}

.analysis-name {
  color: #94a3b8;
  font-size: 0.8rem;
  text-align: center;
}

/* 가이드 그리드 */
.guide-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.guide-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: rgba(0,0,0,0.2);
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s;
}

.guide-card:hover {
  background: rgba(0,224,164,0.1);
}

.guide-icon {
  font-size: 1.5rem;
}

.guide-name {
  display: block;
  color: #fff;
  font-weight: 600;
  font-size: 0.9rem;
}

.guide-desc {
  display: block;
  color: #64748b;
  font-size: 0.75rem;
}

@media (max-width: 768px) {
  .addon-section {
    padding: 20px;
  }
  
  .analysis-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .guide-grid {
    grid-template-columns: 1fr;
  }
  
  .addon-actions {
    flex-direction: column;
  }
  
  .addon-btn {
    width: 100%;
  }
}
</style>

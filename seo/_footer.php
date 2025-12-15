<?php
/**
 * 공통 SEO 푸터 컴포넌트
 * 모든 페이지에서 include하여 내부 링크 강화
 * 
 * 사용법: include(G5_PATH . '/seo/_footer.php');
 */

// 최신 회차 (있으면)
$footer_max_round = 0;
if (function_exists('sql_fetch')) {
    $footer_row = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
    $footer_max_round = (int)($footer_row['max_round'] ?? 0);
}
?>
<footer class="seo-footer">
  <div class="footer-container">
    <!-- 로고 & 소개 -->
    <div class="footer-brand">
      <a href="/" class="footer-logo">
        <span class="footer-logo-icon">🎯</span>
        <span class="footer-logo-text">오늘로또</span>
      </a>
      <p class="footer-desc">
        동행복권 공식 데이터 기반 AI 로또 분석 서비스.<br>
        23년간 당첨번호 패턴을 분석합니다.
      </p>
    </div>

    <!-- 링크 그리드 -->
    <div class="footer-links">
      <!-- 서비스 -->
      <div class="footer-col">
        <h4 class="footer-col-title">서비스</h4>
        <a href="/auth.php">AI 번호 분석</a>
        <a href="/algorithm.php">알고리즘 소개</a>
        <a href="/로또-당첨번호/<?= $footer_max_round ?>/">최신 당첨번호</a>
      </div>

      <!-- 당첨점 -->
      <div class="footer-col">
        <h4 class="footer-col-title">로또 당첨점</h4>
        <a href="/로또-판매점/">전국 명당</a>
        <a href="/로또-랭킹/stores/">명당 랭킹 TOP 100</a>
        <a href="/로또-당첨번호/<?= $footer_max_round ?>/당첨점/">최근 당첨점</a>
        <a href="/로또-판매점/서울/">서울 명당</a>
        <a href="/로또-판매점/경기/">경기 명당</a>
      </div>

      <!-- 통계/분석 -->
      <div class="footer-col">
        <h4 class="footer-col-title">로또 통계 · 분석</h4>
        <a href="/로또-분석/">패턴 분석</a>
        <a href="/로또-분석/홀짝/">홀짝 분석</a>
        <a href="/로또-분석/연속번호/">연속번호 분석</a>
        <a href="/로또-통계/자동수동/">자동 vs 수동</a>
        <a href="/로또-랭킹/numbers/">번호 출현 순위</a>
        <a href="/로또-랭킹/jackpot/">역대 당첨금 순위</a>
      </div>

      <!-- 번호 통계 -->
      <div class="footer-col">
        <h4 class="footer-col-title">로또 번호 통계</h4>
        <div class="footer-number-grid">
          <?php for ($i = 1; $i <= 15; $i++): ?>
          <a href="/로또-번호/<?= $i ?>/" class="footer-num"><?= $i ?></a>
          <?php endfor; ?>
          <a href="/로또-번호/1/" class="footer-more">더보기</a>
        </div>
      </div>

      <!-- 가이드 -->
      <div class="footer-col">
        <h4 class="footer-col-title">로또 가이드</h4>
        <a href="/로또-가이드/">가이드 전체</a>
        <a href="/로또-가이드/세금/">💰 당첨금 세금</a>
        <a href="/로또-가이드/1등-확률/">📊 로또 당첨 확률</a>
        <a href="/로또-가이드/수령방법/">🏦 수령 방법</a>
        <a href="/로또-통계/자동-수동-비교/">🎰 자동 vs 수동</a>
        <a href="/로또-당첨번호/이번주/">📅 이번주 로또</a>
      </div>

      <!-- 도구 -->
      <div class="footer-col">
        <h4 class="footer-col-title">로또 도구</h4>
        <a href="/로또-도구/">도구 전체</a>
        <a href="/로또-도구/세금-계산기/">💰 세금 계산기</a>
        <a href="/로또-도구/확률-계산기/">📊 확률 계산기</a>
        <a href="/로또-도구/번호-생성기/">🎲 번호 생성기</a>
        <a href="/로또-도구/조합-분석기/">🔬 조합 분석기</a>
        <a href="/로또-비교/">⚖️ 회차 비교</a>
      </div>
    </div>

    <!-- 하단 -->
    <div class="footer-bottom">
      <div class="footer-legal">
        <a href="/terms.html">이용약관</a>
        <a href="/privacy.html">개인정보처리방침</a>
      </div>
      <p class="footer-copyright">
        © <?= date('Y') ?> 오늘로또. 본 서비스는 당첨을 보장하지 않습니다.<br>
        로또 데이터 출처: <a href="https://dhlottery.co.kr" target="_blank" rel="noopener">동행복권</a>
      </p>
    </div>
  </div>
</footer>

<style>
.seo-footer {
  background: linear-gradient(180deg, #0a0f1a 0%, #050810 100%);
  border-top: 1px solid rgba(255,255,255,0.05);
  padding: 60px 24px 40px;
  margin-top: 60px;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-brand {
  margin-bottom: 40px;
  text-align: center;
}

.footer-logo {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  margin-bottom: 12px;
}

.footer-logo-icon {
  font-size: 1.5rem;
}

.footer-logo-text {
  font-family: 'Outfit', sans-serif;
  font-size: 1.4rem;
  font-weight: 800;
  background: linear-gradient(135deg, #00E0A4, #00D4FF);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.footer-desc {
  color: #64748b;
  font-size: 0.9rem;
  line-height: 1.6;
}

.footer-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 32px;
  padding: 40px 0;
  border-top: 1px solid rgba(255,255,255,0.05);
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.footer-col-title {
  color: #fff;
  font-size: 0.9rem;
  font-weight: 700;
  margin-bottom: 16px;
}

.footer-col a {
  display: block;
  color: #94a3b8;
  text-decoration: none;
  font-size: 0.85rem;
  padding: 6px 0;
  transition: color 0.2s;
}

.footer-col a:hover {
  color: #00E0A4;
}

.footer-number-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.footer-num {
  display: flex !important;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: rgba(255,255,255,0.05);
  border-radius: 50%;
  font-size: 0.75rem;
  padding: 0 !important;
}

.footer-num:hover {
  background: rgba(0,224,164,0.2);
}

.footer-more {
  font-size: 0.75rem !important;
  padding: 4px 8px !important;
}

.footer-bottom {
  padding-top: 24px;
  text-align: center;
}

.footer-legal {
  margin-bottom: 16px;
}

.footer-legal a {
  color: #64748b;
  text-decoration: none;
  font-size: 0.8rem;
  margin: 0 12px;
}

.footer-legal a:hover {
  color: #94a3b8;
}

.footer-copyright {
  color: #475569;
  font-size: 0.8rem;
  line-height: 1.6;
}

.footer-copyright a {
  color: #64748b;
}

@media (max-width: 768px) {
  .seo-footer {
    padding: 40px 16px 30px;
  }
  
  .footer-links {
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }
  
  .footer-col-title {
    font-size: 0.85rem;
  }
  
  .footer-col a {
    font-size: 0.8rem;
    padding: 5px 0;
  }
}

@media (max-width: 480px) {
  .footer-links {
    grid-template-columns: 1fr;
  }
}
</style>

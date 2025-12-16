<?php
/**
 * /ai/index.php - AI 번호 추천 페이지 (토스증권 스타일)
 * 
 * URL 패턴:
 * - /ai/              → AI 분석 메인
 * - /ai/hot           → 핫넘버 분석
 * - /ai/pattern       → 패턴 분석
 * - /ai/history       → 과거 적중 내역
 */

// 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    $common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
    if (file_exists($common_path)) {
        include_once($common_path);
    }
}

// 현재 탭
$current_tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'recommend';
$is_logged_in = isset($member['mb_id']) && $member['mb_id'];

// 최신 회차 정보 (실제로는 DB에서)
$latest_round = 1148;
$next_round = $latest_round + 1;
$draw_date = '2024.12.21';

// AI 알고리즘 목록
$algorithms = [
    [
        'id' => 'frequency',
        'name' => '빈도 분석',
        'icon' => '📊',
        'desc' => '최근 100회차 출현 빈도 기반',
        'accuracy' => 23.5,
        'color' => '#F5B800',
        'numbers' => [7, 12, 18, 27, 34, 42],
    ],
    [
        'id' => 'pattern',
        'name' => '패턴 매칭',
        'icon' => '🔄',
        'desc' => '연속 번호, 끝수 패턴 분석',
        'accuracy' => 21.2,
        'color' => '#00B4D8',
        'numbers' => [3, 11, 19, 28, 35, 43],
    ],
    [
        'id' => 'gap',
        'name' => '출현 간격',
        'icon' => '📏',
        'desc' => '번호별 평균 출현 주기 분석',
        'accuracy' => 19.8,
        'color' => '#9D4EDD',
        'numbers' => [5, 14, 22, 31, 38, 45],
    ],
    [
        'id' => 'sum',
        'name' => '합계 분석',
        'icon' => '➕',
        'desc' => '당첨번호 총합 구간 분석',
        'accuracy' => 18.5,
        'color' => '#00E676',
        'numbers' => [8, 16, 24, 29, 36, 41],
    ],
    [
        'id' => 'ac',
        'name' => 'AC값 분석',
        'icon' => '🎯',
        'desc' => '산술 복잡도 기반 분석',
        'accuracy' => 17.2,
        'color' => '#FF4757',
        'numbers' => [2, 13, 21, 30, 37, 44],
    ],
    [
        'id' => 'ml',
        'name' => 'AI 딥러닝',
        'icon' => '🤖',
        'desc' => 'LSTM 신경망 예측 모델',
        'accuracy' => 24.8,
        'color' => '#FF8C00',
        'numbers' => [6, 15, 23, 32, 39, 45],
    ],
];

// 핫/콜드 번호 (실제로는 DB에서 계산)
$hot_numbers = [7, 12, 18, 27, 34, 42]; // 최근 많이 나온
$cold_numbers = [1, 9, 20, 26, 33, 40]; // 최근 안 나온
$due_numbers = [4, 11, 25, 31, 38, 44]; // 출현 주기 지난

// 번호대별 분포 (1-10, 11-20, ...)
$range_stats = [
    ['range' => '1-10', 'avg' => 1.2, 'last' => 2],
    ['range' => '11-20', 'avg' => 1.3, 'last' => 1],
    ['range' => '21-30', 'avg' => 1.1, 'last' => 1],
    ['range' => '31-40', 'avg' => 1.2, 'last' => 1],
    ['range' => '41-45', 'avg' => 1.2, 'last' => 1],
];

// 최근 당첨번호
$recent_draws = [
    ['round' => 1148, 'numbers' => [3, 12, 18, 27, 35, 42], 'bonus' => 7],
    ['round' => 1147, 'numbers' => [5, 11, 16, 28, 34, 43], 'bonus' => 21],
    ['round' => 1146, 'numbers' => [2, 9, 19, 25, 38, 44], 'bonus' => 15],
    ['round' => 1145, 'numbers' => [8, 14, 22, 31, 37, 45], 'bonus' => 3],
    ['round' => 1144, 'numbers' => [1, 13, 20, 29, 36, 41], 'bonus' => 10],
];

// 볼 컬러 함수
function get_ball_class($num) {
    if ($num <= 10) return 'ball-yellow';
    if ($num <= 20) return 'ball-blue';
    if ($num <= 30) return 'ball-red';
    if ($num <= 40) return 'ball-gray';
    return 'ball-green';
}

$page_title = 'AI 번호 분석';
$page_desc = 'AI가 분석한 로또 번호 추천. 10가지 알고리즘 기반 예측.';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?= htmlspecialchars($page_title) ?> | 오늘로또</title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  
  <meta name="theme-color" content="#080b14">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <style>
    :root {
      --bg-deep: #080b14;
      --bg-primary: #0d1220;
      --bg-secondary: #151c2c;
      --bg-card: #1a2236;
      --bg-hover: #212b40;
      
      --gold: #F5B800;
      --gold-light: #FFD54F;
      --gradient-gold: linear-gradient(135deg, #F5B800 0%, #FF8C00 100%);
      
      --red: #FF4757;
      --blue: #00B4D8;
      --purple: #9D4EDD;
      --green: #00E676;
      --orange: #FF8C00;
      
      --text-primary: #ffffff;
      --text-secondary: #a8b5c8;
      --text-muted: #6b7a90;
      
      --border: rgba(255, 255, 255, 0.08);
      --border-gold: rgba(245, 184, 0, 0.3);
      
      --mesh-gradient: 
        radial-gradient(at 20% 0%, rgba(245, 184, 0, 0.12) 0px, transparent 50%),
        radial-gradient(at 80% 10%, rgba(157, 78, 221, 0.08) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgba(0, 180, 216, 0.05) 0px, transparent 50%);
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-deep);
      background-image: var(--mesh-gradient);
      background-attachment: fixed;
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }
    
    a { color: inherit; text-decoration: none; }
    
    /* Header */
    .header {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(8, 11, 20, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }
    
    .header-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      height: 56px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .logo-icon {
      width: 36px;
      height: 36px;
      background: var(--gradient-gold);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    
    .logo-text {
      font-weight: 800;
      font-size: 1.2rem;
      background: var(--gradient-gold);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .header-nav {
      display: flex;
      align-items: center;
      gap: 4px;
    }
    
    .nav-link {
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-secondary);
      transition: all 0.2s;
    }
    
    .nav-link:hover, .nav-link.active {
      background: rgba(245, 184, 0, 0.1);
      color: var(--gold);
    }
    
    .btn {
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .btn-primary {
      background: var(--gradient-gold);
      color: #000;
    }
    
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 20px rgba(245, 184, 0, 0.3);
    }
    
    .btn-secondary {
      background: var(--bg-secondary);
      color: var(--text-secondary);
    }
    
    /* Main */
    .main {
      max-width: 1200px;
      margin: 0 auto;
      padding: 24px 20px 100px;
    }
    
    /* Hero Section */
    .ai-hero {
      text-align: center;
      padding: 40px 20px;
      background: linear-gradient(180deg, rgba(245, 184, 0, 0.1) 0%, transparent 100%);
      border-radius: 24px;
      margin-bottom: 32px;
      position: relative;
      overflow: hidden;
    }
    
    .ai-hero::before {
      content: '';
      position: absolute;
      top: -50%;
      left: 50%;
      transform: translateX(-50%);
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(245, 184, 0, 0.15) 0%, transparent 60%);
      pointer-events: none;
    }
    
    .ai-hero-content {
      position: relative;
      z-index: 1;
    }
    
    .ai-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: var(--gradient-gold);
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 700;
      color: #000;
      margin-bottom: 16px;
    }
    
    .ai-title {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 12px;
      background: var(--gradient-gold);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .ai-subtitle {
      font-size: 1.1rem;
      color: var(--text-secondary);
      margin-bottom: 24px;
    }
    
    .ai-next-round {
      display: inline-flex;
      align-items: center;
      gap: 16px;
      padding: 16px 32px;
      background: var(--bg-card);
      border: 1px solid var(--border-gold);
      border-radius: 16px;
    }
    
    .next-round-label {
      font-size: 0.9rem;
      color: var(--text-muted);
    }
    
    .next-round-value {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--gold);
    }
    
    .next-round-date {
      font-size: 0.9rem;
      color: var(--text-secondary);
    }
    
    /* 탭 */
    .tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 24px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 16px;
    }
    
    .tab {
      padding: 12px 20px;
      border-radius: 10px;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .tab:hover {
      color: var(--text-secondary);
      background: var(--bg-secondary);
    }
    
    .tab.active {
      background: var(--gradient-gold);
      color: #000;
    }
    
    .tab-badge {
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 0.7rem;
      font-weight: 700;
      background: var(--red);
      color: #fff;
    }
    
    /* 로그인 필요 오버레이 */
    .login-overlay {
      position: relative;
    }
    
    .login-overlay.locked::after {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(8, 11, 20, 0.8);
      backdrop-filter: blur(8px);
      border-radius: 16px;
      z-index: 10;
    }
    
    .login-prompt {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 11;
      text-align: center;
      padding: 32px;
    }
    
    .login-prompt-icon {
      font-size: 3rem;
      margin-bottom: 16px;
    }
    
    .login-prompt-title {
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 8px;
    }
    
    .login-prompt-desc {
      color: var(--text-secondary);
      margin-bottom: 20px;
    }
    
    /* AI 추천 번호 카드 */
    .recommend-section {
      margin-bottom: 32px;
    }
    
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }
    
    .section-title {
      font-size: 1.25rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .section-more {
      font-size: 0.9rem;
      color: var(--gold);
      font-weight: 600;
    }
    
    /* AI 메인 추천 */
    .ai-main-card {
      background: linear-gradient(145deg, var(--bg-card), rgba(245, 184, 0, 0.05));
      border: 1px solid var(--border-gold);
      border-radius: 20px;
      padding: 32px;
      margin-bottom: 24px;
      position: relative;
      overflow: hidden;
    }
    
    .ai-main-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(245, 184, 0, 0.2) 0%, transparent 60%);
      pointer-events: none;
    }
    
    .ai-main-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 24px;
      position: relative;
      z-index: 1;
    }
    
    .ai-main-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .ai-main-icon {
      width: 56px;
      height: 56px;
      background: var(--gradient-gold);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      box-shadow: 0 8px 32px rgba(245, 184, 0, 0.3);
    }
    
    .ai-main-info h2 {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 4px;
    }
    
    .ai-main-info p {
      color: var(--text-secondary);
      font-size: 0.9rem;
    }
    
    .ai-accuracy {
      text-align: right;
    }
    
    .ai-accuracy-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-bottom: 4px;
    }
    
    .ai-accuracy-value {
      font-size: 2rem;
      font-weight: 800;
      color: var(--gold);
    }
    
    .ai-main-numbers {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 24px;
      position: relative;
      z-index: 1;
    }
    
    .lotto-ball {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.25rem;
      color: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      transition: transform 0.2s;
    }
    
    .lotto-ball:hover {
      transform: scale(1.1);
    }
    
    .ball-yellow { background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #000; }
    .ball-blue { background: linear-gradient(135deg, #3B82F6, #2563EB); }
    .ball-red { background: linear-gradient(135deg, #EF4444, #DC2626); }
    .ball-gray { background: linear-gradient(135deg, #6B7280, #4B5563); }
    .ball-green { background: linear-gradient(135deg, #22C55E, #16A34A); }
    
    .ball-sm {
      width: 40px;
      height: 40px;
      font-size: 0.95rem;
    }
    
    .ball-xs {
      width: 32px;
      height: 32px;
      font-size: 0.8rem;
    }
    
    .ai-main-stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      position: relative;
      z-index: 1;
    }
    
    .ai-stat-item {
      text-align: center;
      padding: 16px;
      background: var(--bg-secondary);
      border-radius: 12px;
    }
    
    .ai-stat-label {
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-bottom: 4px;
    }
    
    .ai-stat-value {
      font-size: 1.25rem;
      font-weight: 700;
    }
    
    /* 알고리즘 그리드 */
    .algo-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
    }
    
    .algo-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px;
      transition: all 0.2s;
      cursor: pointer;
    }
    
    .algo-card:hover {
      border-color: var(--border-gold);
      transform: translateY(-2px);
    }
    
    .algo-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 16px;
    }
    
    .algo-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .algo-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
    }
    
    .algo-name {
      font-weight: 700;
      margin-bottom: 2px;
    }
    
    .algo-desc {
      font-size: 0.8rem;
      color: var(--text-muted);
    }
    
    .algo-accuracy {
      text-align: right;
    }
    
    .algo-accuracy-label {
      font-size: 0.7rem;
      color: var(--text-muted);
    }
    
    .algo-accuracy-value {
      font-size: 1.1rem;
      font-weight: 700;
    }
    
    .algo-numbers {
      display: flex;
      gap: 6px;
      justify-content: center;
    }
    
    /* 핫/콜드 섹션 */
    .hot-cold-section {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 32px;
    }
    
    .hot-cold-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px;
    }
    
    .hot-cold-header {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 16px;
    }
    
    .hot-cold-icon {
      font-size: 1.25rem;
    }
    
    .hot-cold-title {
      font-weight: 700;
    }
    
    .hot-cold-numbers {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: center;
    }
    
    /* 번호대별 분포 */
    .range-section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 32px;
    }
    
    .range-bars {
      display: flex;
      gap: 16px;
      margin-top: 20px;
    }
    
    .range-item {
      flex: 1;
      text-align: center;
    }
    
    .range-bar-wrapper {
      height: 120px;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      margin-bottom: 8px;
    }
    
    .range-bar {
      width: 40px;
      border-radius: 6px 6px 0 0;
      transition: height 0.3s;
    }
    
    .range-label {
      font-size: 0.8rem;
      color: var(--text-muted);
    }
    
    .range-value {
      font-size: 0.9rem;
      font-weight: 600;
      margin-top: 4px;
    }
    
    /* 최근 당첨번호 */
    .recent-section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
    }
    
    .recent-header {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .recent-title {
      font-weight: 700;
    }
    
    .recent-list {
      max-height: 400px;
      overflow-y: auto;
    }
    
    .recent-item {
      display: flex;
      align-items: center;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      transition: background 0.2s;
    }
    
    .recent-item:hover {
      background: var(--bg-hover);
    }
    
    .recent-item:last-child {
      border-bottom: none;
    }
    
    .recent-round {
      min-width: 80px;
    }
    
    .recent-round-num {
      font-weight: 700;
    }
    
    .recent-round-date {
      font-size: 0.75rem;
      color: var(--text-muted);
    }
    
    .recent-numbers {
      display: flex;
      gap: 6px;
      align-items: center;
      flex: 1;
      justify-content: center;
    }
    
    .recent-bonus {
      color: var(--text-muted);
      margin: 0 8px;
    }
    
    /* 하단 CTA */
    .bottom-cta {
      background: linear-gradient(145deg, var(--bg-card), rgba(245, 184, 0, 0.05));
      border: 1px solid var(--border-gold);
      border-radius: 20px;
      padding: 40px;
      text-align: center;
      margin-top: 32px;
    }
    
    .bottom-cta-icon {
      font-size: 3rem;
      margin-bottom: 16px;
    }
    
    .bottom-cta-title {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 8px;
    }
    
    .bottom-cta-desc {
      color: var(--text-secondary);
      margin-bottom: 24px;
    }
    
    .bottom-cta .btn {
      padding: 16px 40px;
      font-size: 1.1rem;
    }
    
    /* 하단 바 */
    .bottom-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(8, 11, 20, 0.95);
      backdrop-filter: blur(10px);
      border-top: 1px solid var(--border);
      z-index: 90;
    }
    
    .bottom-bar-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      height: 48px;
      display: flex;
      align-items: center;
      gap: 32px;
      font-size: 0.85rem;
    }
    
    .bottom-stat {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .bottom-stat-label {
      color: var(--text-muted);
    }
    
    .bottom-stat-value {
      font-weight: 600;
    }
    
    .bottom-stat-value.gold { color: var(--gold); }
    .bottom-stat-value.green { color: var(--green); }
    
    /* 반응형 */
    @media (max-width: 1024px) {
      .hot-cold-section {
        grid-template-columns: 1fr;
      }
      
      .ai-main-stats {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    
    @media (max-width: 768px) {
      .header-nav {
        display: none;
      }
      
      .ai-title {
        font-size: 1.75rem;
      }
      
      .algo-grid {
        grid-template-columns: 1fr;
      }
      
      .ai-main-numbers {
        gap: 8px;
      }
      
      .lotto-ball {
        width: 44px;
        height: 44px;
        font-size: 1rem;
      }
      
      .tabs {
        overflow-x: auto;
        padding-bottom: 12px;
      }
      
      .tab {
        white-space: nowrap;
      }
      
      .range-bars {
        flex-wrap: wrap;
      }
      
      .range-item {
        min-width: calc(50% - 8px);
      }
    }
    
    @media (max-width: 480px) {
      .ai-main-stats {
        grid-template-columns: 1fr;
      }
      
      .ai-next-round {
        flex-direction: column;
        gap: 8px;
      }
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
  </style>
</head>
<body>

<!-- Header -->
<header class="header">
  <div class="header-inner">
    <a href="/" class="logo">
      <div class="logo-icon">🎰</div>
      <span class="logo-text">오늘로또</span>
    </a>
    
    <nav class="header-nav">
      <a href="/" class="nav-link">홈</a>
      <a href="/stores/" class="nav-link">당첨점</a>
      <a href="/draw/latest" class="nav-link">회차별 결과</a>
      <a href="/ai/" class="nav-link active">AI 분석</a>
    </nav>
    
    <div style="display: flex; align-items: center; gap: 8px;">
      <?php if ($is_logged_in): ?>
      <span style="color: var(--text-secondary); font-size: 0.9rem;">안녕하세요, <?= $member['mb_name'] ?>님</span>
      <a href="/bbs/logout.php" class="btn btn-secondary">로그아웃</a>
      <?php else: ?>
      <a href="/auth.php" class="btn btn-primary">🔐 로그인</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="main">
  <!-- Hero Section -->
  <section class="ai-hero">
    <div class="ai-hero-content">
      <div class="ai-badge">🤖 AI 분석 시스템</div>
      <h1 class="ai-title">AI 번호 추천</h1>
      <p class="ai-subtitle">10가지 알고리즘이 분석한 이번 주 최적의 번호 조합</p>
      
      <div class="ai-next-round">
        <div>
          <div class="next-round-label">다음 회차</div>
          <div class="next-round-value"><?= $next_round ?>회</div>
        </div>
        <div style="width: 1px; height: 40px; background: var(--border);"></div>
        <div>
          <div class="next-round-label">추첨일</div>
          <div class="next-round-date"><?= $draw_date ?> (토) 20:45</div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- 탭 -->
  <div class="tabs">
    <a href="/ai/" class="tab <?= $current_tab === 'recommend' ? 'active' : '' ?>">
      🎯 AI 추천
    </a>
    <a href="/ai/?tab=hot" class="tab <?= $current_tab === 'hot' ? 'active' : '' ?>">
      🔥 핫넘버
      <span class="tab-badge">NEW</span>
    </a>
    <a href="/ai/?tab=pattern" class="tab <?= $current_tab === 'pattern' ? 'active' : '' ?>">
      📊 패턴 분석
    </a>
    <a href="/ai/?tab=history" class="tab <?= $current_tab === 'history' ? 'active' : '' ?>">
      📈 적중 내역
    </a>
  </div>
  
  <!-- AI 메인 추천 (로그인 필요) -->
  <section class="recommend-section">
    <div class="section-header">
      <h2 class="section-title">🎯 AI 종합 추천</h2>
      <span class="section-more">전체 알고리즘 분석 결과</span>
    </div>
    
    <div class="ai-main-card login-overlay <?= !$is_logged_in ? 'locked' : '' ?>">
      <?php if (!$is_logged_in): ?>
      <div class="login-prompt">
        <div class="login-prompt-icon">🔒</div>
        <div class="login-prompt-title">로그인이 필요합니다</div>
        <p class="login-prompt-desc">AI 분석 번호를 확인하려면 로그인하세요</p>
        <a href="/auth.php" class="btn btn-primary">로그인하기</a>
      </div>
      <?php endif; ?>
      
      <div class="ai-main-header">
        <div class="ai-main-title">
          <div class="ai-main-icon">🤖</div>
          <div class="ai-main-info">
            <h2><?= $next_round ?>회 AI 추천번호</h2>
            <p>6개 알고리즘 종합 분석 결과</p>
          </div>
        </div>
        <div class="ai-accuracy">
          <div class="ai-accuracy-label">평균 적중률</div>
          <div class="ai-accuracy-value">24.8%</div>
        </div>
      </div>
      
      <div class="ai-main-numbers">
        <?php 
        $main_numbers = [6, 12, 18, 27, 35, 42];
        foreach ($main_numbers as $num):
        ?>
        <div class="lotto-ball <?= get_ball_class($num) ?>"><?= $num ?></div>
        <?php endforeach; ?>
      </div>
      
      <div class="ai-main-stats">
        <div class="ai-stat-item">
          <div class="ai-stat-label">총합</div>
          <div class="ai-stat-value"><?= array_sum($main_numbers) ?></div>
        </div>
        <div class="ai-stat-item">
          <div class="ai-stat-label">홀짝 비율</div>
          <div class="ai-stat-value">3:3</div>
        </div>
        <div class="ai-stat-item">
          <div class="ai-stat-label">고저 비율</div>
          <div class="ai-stat-value">3:3</div>
        </div>
        <div class="ai-stat-item">
          <div class="ai-stat-label">AC값</div>
          <div class="ai-stat-value">8</div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- 알고리즘별 추천 -->
  <section class="recommend-section">
    <div class="section-header">
      <h2 class="section-title">📊 알고리즘별 추천</h2>
      <a href="#" class="section-more">더보기 →</a>
    </div>
    
    <div class="algo-grid">
      <?php foreach ($algorithms as $algo): ?>
      <div class="algo-card login-overlay <?= !$is_logged_in ? 'locked' : '' ?>" style="<?= !$is_logged_in ? 'min-height: 160px;' : '' ?>">
        <?php if (!$is_logged_in): ?>
        <div class="login-prompt" style="padding: 16px;">
          <div style="font-size: 1.5rem; margin-bottom: 8px;">🔒</div>
          <a href="/auth.php" style="color: var(--gold); font-size: 0.85rem;">로그인 필요</a>
        </div>
        <?php endif; ?>
        
        <div class="algo-header">
          <div class="algo-title">
            <div class="algo-icon" style="background: <?= $algo['color'] ?>20;">
              <?= $algo['icon'] ?>
            </div>
            <div>
              <div class="algo-name"><?= $algo['name'] ?></div>
              <div class="algo-desc"><?= $algo['desc'] ?></div>
            </div>
          </div>
          <div class="algo-accuracy">
            <div class="algo-accuracy-label">적중률</div>
            <div class="algo-accuracy-value" style="color: <?= $algo['color'] ?>">
              <?= $algo['accuracy'] ?>%
            </div>
          </div>
        </div>
        
        <div class="algo-numbers">
          <?php foreach ($algo['numbers'] as $num): ?>
          <div class="lotto-ball ball-sm <?= get_ball_class($num) ?>"><?= $num ?></div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  
  <!-- 핫/콜드/듀 번호 -->
  <section class="hot-cold-section">
    <div class="hot-cold-card">
      <div class="hot-cold-header">
        <span class="hot-cold-icon">🔥</span>
        <span class="hot-cold-title">핫 넘버</span>
        <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: auto;">최근 자주 출현</span>
      </div>
      <div class="hot-cold-numbers">
        <?php foreach ($hot_numbers as $num): ?>
        <div class="lotto-ball ball-sm <?= get_ball_class($num) ?>"><?= $num ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div class="hot-cold-card">
      <div class="hot-cold-header">
        <span class="hot-cold-icon">❄️</span>
        <span class="hot-cold-title">콜드 넘버</span>
        <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: auto;">최근 미출현</span>
      </div>
      <div class="hot-cold-numbers">
        <?php foreach ($cold_numbers as $num): ?>
        <div class="lotto-ball ball-sm <?= get_ball_class($num) ?>"><?= $num ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div class="hot-cold-card">
      <div class="hot-cold-header">
        <span class="hot-cold-icon">⏰</span>
        <span class="hot-cold-title">출현 예정</span>
        <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: auto;">평균 주기 초과</span>
      </div>
      <div class="hot-cold-numbers">
        <?php foreach ($due_numbers as $num): ?>
        <div class="lotto-ball ball-sm <?= get_ball_class($num) ?>"><?= $num ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  
  <!-- 번호대별 분포 -->
  <section class="range-section">
    <div class="section-header">
      <h2 class="section-title">📈 번호대별 출현 분포</h2>
      <span style="font-size: 0.85rem; color: var(--text-muted);">최근 10회차 기준</span>
    </div>
    
    <div class="range-bars">
      <?php 
      $colors = ['var(--gold)', 'var(--blue)', 'var(--red)', 'var(--purple)', 'var(--green)'];
      foreach ($range_stats as $i => $stat): 
        $height = $stat['avg'] * 50;
      ?>
      <div class="range-item">
        <div class="range-bar-wrapper">
          <div class="range-bar" style="height: <?= $height ?>px; background: <?= $colors[$i] ?>;"></div>
        </div>
        <div class="range-label"><?= $stat['range'] ?></div>
        <div class="range-value">평균 <?= $stat['avg'] ?>개</div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  
  <!-- 최근 당첨번호 -->
  <section class="recent-section">
    <div class="recent-header">
      <h3 class="recent-title">🎱 최근 당첨번호</h3>
      <a href="/draw/latest" style="color: var(--gold); font-size: 0.9rem; font-weight: 600;">전체보기 →</a>
    </div>
    
    <div class="recent-list">
      <?php foreach ($recent_draws as $draw): ?>
      <div class="recent-item">
        <div class="recent-round">
          <div class="recent-round-num"><?= $draw['round'] ?>회</div>
        </div>
        <div class="recent-numbers">
          <?php foreach ($draw['numbers'] as $num): ?>
          <div class="lotto-ball ball-xs <?= get_ball_class($num) ?>"><?= $num ?></div>
          <?php endforeach; ?>
          <span class="recent-bonus">+</span>
          <div class="lotto-ball ball-xs" style="background: linear-gradient(135deg, var(--purple), #EC4899);"><?= $draw['bonus'] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  
  <!-- 하단 CTA -->
  <section class="bottom-cta">
    <div class="bottom-cta-icon">🎰</div>
    <h2 class="bottom-cta-title">나만의 AI 분석 받기</h2>
    <p class="bottom-cta-desc">10가지 알고리즘이 분석한 맞춤 번호를 받아보세요</p>
    <?php if ($is_logged_in): ?>
    <a href="/ai/generate" class="btn btn-primary">🎲 번호 생성하기</a>
    <?php else: ?>
    <a href="/auth.php" class="btn btn-primary">🔐 로그인하고 시작하기</a>
    <?php endif; ?>
  </section>
</main>

<!-- 하단 바 -->
<div class="bottom-bar">
  <div class="bottom-bar-inner">
    <div class="bottom-stat">
      <span class="bottom-stat-label">📊 통계</span>
    </div>
    <div class="bottom-stat">
      <span class="bottom-stat-label">AI 분석</span>
      <span class="bottom-stat-value">6종</span>
    </div>
    <div class="bottom-stat">
      <span class="bottom-stat-label">평균 적중률</span>
      <span class="bottom-stat-value gold">24.8%</span>
    </div>
    <div class="bottom-stat">
      <span class="bottom-stat-label">분석 데이터</span>
      <span class="bottom-stat-value"><?= number_format($latest_round) ?>회차</span>
    </div>
    <div class="bottom-stat">
      <span class="bottom-stat-label">다음 추첨</span>
      <span class="bottom-stat-value gold">5일 12시간</span>
    </div>
  </div>
</div>

</body>
</html>
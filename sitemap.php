<?php
/**
 * 동적 Sitemap 생성기
 * URL: /sitemap.xml, /sitemap-stores.xml, /sitemap-draws.xml, /sitemap-numbers.xml
 * 
 * DB 데이터 기반으로 실시간 sitemap 생성
 * SEO 극대화를 위한 프로그래매틱 URL 생성
 */

// 그누보드 환경 로드
if (!defined('_GNUBOARD_')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');
}

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600'); // 1시간 캐시

$type = isset($_GET['type']) ? $_GET['type'] : 'index';
$base_url = 'https://lottoinsight.ai';
$today = date('Y-m-d');

// 최신 회차 조회
$latest = sql_fetch("SELECT MAX(draw_no) AS max_round FROM g5_lotto_draw");
$max_round = (int)($latest['max_round'] ?? 1200);

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

// ============================================
// Sitemap Index (메인)
// ============================================
if ($type === 'index') {
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc><?= $base_url ?>/sitemap-main.xml</loc>
    <lastmod><?= $today ?></lastmod>
  </sitemap>
  <sitemap>
    <loc><?= $base_url ?>/sitemap-draws.xml</loc>
    <lastmod><?= $today ?></lastmod>
  </sitemap>
  <sitemap>
    <loc><?= $base_url ?>/sitemap-stores.xml</loc>
    <lastmod><?= $today ?></lastmod>
  </sitemap>
  <sitemap>
    <loc><?= $base_url ?>/sitemap-numbers.xml</loc>
    <lastmod><?= $today ?></lastmod>
  </sitemap>
</sitemapindex>
<?php
    exit;
}

// 공통 urlset 시작
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// ============================================
// Main Pages
// ============================================
if ($type === 'main') {
    $pages = [
        ['/', 'daily', '1.0'],
        ['/auth.php', 'weekly', '0.9'],
        ['/algorithm.php', 'monthly', '0.8'],
        ['/로또-판매점/', 'daily', '0.9'],
        ['/로또-분석/', 'weekly', '0.8'],
        ['/로또-통계/', 'weekly', '0.8'],
        ['/로또-랭킹/', 'weekly', '0.8'],
        ['/로또-가이드/', 'monthly', '0.8'],
        ['/로또-도구/', 'monthly', '0.8'],
        ['/로또-비교/', 'weekly', '0.7'],
        ['/terms.html', 'yearly', '0.3'],
        ['/privacy.html', 'yearly', '0.3'],
    ];
    
    foreach ($pages as $p) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}{$p[0]}</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>{$p[1]}</changefreq>\n";
        echo "    <priority>{$p[2]}</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 가이드 페이지
    $guide_types = ['세금', '구매방법', '당첨확인', '수령방법', '확률'];
    foreach ($guide_types as $g) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-가이드/" . urlencode($g) . "/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 통계 페이지
    $stats_types = ['자동수동', '지역별', '요일별', '금액별'];
    foreach ($stats_types as $s) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-통계/" . urlencode($s) . "/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 랭킹 페이지
    $ranking_types = ['stores', 'jackpot', 'numbers'];
    foreach ($ranking_types as $r) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-랭킹/{$r}/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.8</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 번호별 통계 (1~45)
    for ($i = 1; $i <= 45; $i++) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-번호/{$i}/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 분석 페이지
    $analysis_types = ['홀짝', '고저', '연속번호', '소수', '합계', '끝수', 'AC값'];
    foreach ($analysis_types as $t) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-분석/" . urlencode($t) . "/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 도구 페이지
    $tools = ['세금-계산기', '확률-계산기', '번호-생성기', '조합-분석기'];
    echo "  <url>\n";
    echo "    <loc>{$base_url}/로또-도구/</loc>\n";
    echo "    <lastmod>{$today}</lastmod>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";
    foreach ($tools as $tool) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-도구/" . urlencode($tool) . "/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 비교 페이지
    echo "  <url>\n";
    echo "    <loc>{$base_url}/로또-비교/</loc>\n";
    echo "    <lastmod>{$today}</lastmod>\n";
    echo "    <changefreq>daily</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "  </url>\n";
    
    // 롱테일 SEO 페이지
    $longtail_pages = [
        ['/로또-당첨번호/이번주/', 'daily', '0.9'],
        ['/로또-가이드/1등-확률/', 'monthly', '0.8'],
        ['/로또-가이드/당첨-확률/', 'monthly', '0.8'],
        ['/로또-통계/자동-수동-비교/', 'monthly', '0.8'],
        ['/로또-도구/무료-번호-생성/', 'weekly', '0.7'],
        ['/로또-가이드/당첨금-지급-절차/', 'monthly', '0.7'],
    ];
    foreach ($longtail_pages as $lp) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}{$lp[0]}</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>{$lp[1]}</changefreq>\n";
        echo "    <priority>{$lp[2]}</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 연도별 페이지 (최근 5년)
    $current_year = (int)date('Y');
    for ($y = $current_year; $y >= max(2002, $current_year - 5); $y--) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-당첨번호/{$y}년/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.6</priority>\n";
        echo "  </url>\n";
    }
    
    // 로또 가이드 상세 페이지 (3단계)
    $guide_details = [
        '세금' => ['1등', '2등', '3등', '연금복권'],
        '수령방법' => ['1등', '2등', '3등', '4등', '5등'],
    ];
    foreach ($guide_details as $g => $subs) {
        foreach ($subs as $s) {
            echo "  <url>\n";
            echo "    <loc>{$base_url}/로또-가이드/" . urlencode($g) . "/" . urlencode($s) . "/</loc>\n";
            echo "    <lastmod>{$today}</lastmod>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.6</priority>\n";
            echo "  </url>\n";
        }
    }
}

// ============================================
// 로또 당첨번호 (회차별)
// ============================================
if ($type === 'draws') {
    // 최신 회차부터 1회차까지
    $res = sql_query("SELECT draw_no, draw_date FROM g5_lotto_draw ORDER BY draw_no DESC");
    
    while ($row = sql_fetch_array($res)) {
        $round = $row['draw_no'];
        $date = $row['draw_date'];
        $priority = $round > ($max_round - 100) ? '0.8' : '0.6';
        
        // 로또 회차 메인
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-당첨번호/{$round}/</loc>\n";
        echo "    <lastmod>{$date}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>{$priority}</priority>\n";
        echo "  </url>\n";
        
        // 로또 회차 당첨점
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-당첨번호/{$round}/당첨점/</loc>\n";
        echo "    <lastmod>{$date}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.6</priority>\n";
        echo "  </url>\n";
        
        // 로또 회차 당첨금
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-당첨번호/{$round}/당첨금/</loc>\n";
        echo "    <lastmod>{$date}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.6</priority>\n";
        echo "  </url>\n";
    }
}

// ============================================
// 로또 판매점
// ============================================
if ($type === 'stores') {
    // 시/도 목록
    $regions = ['서울', '부산', '대구', '인천', '광주', '대전', '울산', '세종', 
                '경기', '강원', '충북', '충남', '전북', '전남', '경북', '경남', '제주'];
    
    foreach ($regions as $r1) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-판매점/" . urlencode($r1) . "/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.8</priority>\n";
        echo "  </url>\n";
    }
    
    // 구/군 단위 (DB에서 조회)
    $res = sql_query("SELECT DISTINCT region1, region2 FROM g5_lotto_store WHERE region1 != '' AND region2 != '' ORDER BY region1, region2", false);
    if ($res) {
        while ($row = sql_fetch_array($res)) {
            if (empty($row['region1']) || empty($row['region2'])) continue;
            echo "  <url>\n";
            echo "    <loc>{$base_url}/로또-판매점/" . urlencode($row['region1']) . "/" . urlencode($row['region2']) . "/</loc>\n";
            echo "    <lastmod>{$today}</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.7</priority>\n";
            echo "  </url>\n";
        }
    }
    
    // 개별 판매점 (1등 당첨 이력 있는 곳 우선)
    $res = sql_query("SELECT store_id, store_name, region1, updated_at FROM g5_lotto_store WHERE wins_1st > 0 ORDER BY wins_1st DESC LIMIT 500", false);
    if ($res) {
        while ($row = sql_fetch_array($res)) {
            $store_slug = urlencode($row['store_name']) . '-' . $row['store_id'];
            $region1 = urlencode($row['region1'] ?? '');
            $lastmod = $row['updated_at'] ? substr($row['updated_at'], 0, 10) : $today;
            
            echo "  <url>\n";
            echo "    <loc>{$base_url}/store/{$row['store_id']}</loc>\n";
            echo "    <lastmod>{$lastmod}</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }
    }
}

// ============================================
// 로또 번호별 통계
// ============================================
if ($type === 'numbers') {
    for ($i = 1; $i <= 45; $i++) {
        echo "  <url>\n";
        echo "    <loc>{$base_url}/로또-번호/{$i}/</loc>\n";
        echo "    <lastmod>{$today}</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
}

echo '</urlset>';

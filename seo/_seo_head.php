<?php
/**
 * 공통 SEO 헤더 컴포넌트
 * Core Web Vitals 최적화 + 구조화 데이터 + Breadcrumb
 * 
 * 사용법: 
 * $seo = [
 *   'title' => '페이지 제목 | 오늘로또',
 *   'desc' => '페이지 설명',
 *   'url' => 'https://lottoinsight.ai/...',
 *   'breadcrumb' => [
 *     ['name' => '홈', 'url' => '/'],
 *     ['name' => '로또 가이드', 'url' => '/로또-가이드/'],
 *     ['name' => '현재 페이지']
 *   ]
 * ];
 * include(__DIR__ . '/_seo_head.php');
 */

// 기본값 설정
$seo_title = $seo['title'] ?? '오늘로또 - AI 로또 분석';
$seo_desc = $seo['desc'] ?? '동행복권 공식 데이터 기반 AI 로또 분석. 무료 1회 분석 제공.';
$seo_url = $seo['url'] ?? 'https://lottoinsight.ai/';
$seo_image = $seo['image'] ?? 'https://lottoinsight.ai/og-image.png';
$seo_type = $seo['type'] ?? 'website';
$seo_keywords = $seo['keywords'] ?? '로또, 로또분석, AI로또, 로또번호, 당첨번호';
$seo_breadcrumb = $seo['breadcrumb'] ?? [];

// Breadcrumb Schema 생성
$breadcrumb_schema = '';
if (!empty($seo_breadcrumb)) {
    $items = [];
    foreach ($seo_breadcrumb as $i => $bc) {
        $item = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $bc['name']
        ];
        if (isset($bc['url'])) {
            $item['item'] = 'https://lottoinsight.ai' . $bc['url'];
        }
        $items[] = $item;
    }
    $breadcrumb_schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
?>
<!-- ============================================ -->
<!-- Core Web Vitals 최적화 -->
<!-- ============================================ -->

<!-- DNS Prefetch & Preconnect (LCP/FCP 개선) -->
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://dhlottery.co.kr">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">

<!-- Preload Critical Resources -->
<link rel="preload" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" as="style">
<link rel="preload" href="/favicon.svg" as="image" type="image/svg+xml">

<!-- ============================================ -->
<!-- Primary Meta Tags -->
<!-- ============================================ -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<title><?= htmlspecialchars($seo_title) ?></title>
<meta name="title" content="<?= htmlspecialchars($seo_title) ?>">
<meta name="description" content="<?= htmlspecialchars($seo_desc) ?>">
<meta name="keywords" content="<?= htmlspecialchars($seo_keywords) ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="author" content="오늘로또">
<meta name="theme-color" content="#0B132B">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Canonical URL -->
<link rel="canonical" href="<?= htmlspecialchars($seo_url) ?>">

<!-- ============================================ -->
<!-- Open Graph (Facebook, KakaoTalk, etc.) -->
<!-- ============================================ -->
<meta property="og:type" content="<?= $seo_type ?>">
<meta property="og:url" content="<?= htmlspecialchars($seo_url) ?>">
<meta property="og:title" content="<?= htmlspecialchars($seo_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($seo_desc) ?>">
<meta property="og:image" content="<?= htmlspecialchars($seo_image) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="ko_KR">
<meta property="og:site_name" content="오늘로또">

<!-- ============================================ -->
<!-- Twitter Card -->
<!-- ============================================ -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@todaylotto">
<meta name="twitter:creator" content="@todaylotto">
<meta name="twitter:title" content="<?= htmlspecialchars($seo_title) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($seo_desc) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($seo_image) ?>">

<!-- ============================================ -->
<!-- Organization Schema (사이트 전체 적용) -->
<!-- ============================================ -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "오늘로또",
  "alternateName": "TodayLotto",
  "url": "https://lottoinsight.ai",
  "logo": "https://lottoinsight.ai/favicon.svg",
  "description": "AI 기반 로또 번호 분석 서비스. 동행복권 공식 데이터 기반 23년간 당첨번호 패턴 분석.",
  "foundingDate": "2023",
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "customer service",
    "availableLanguage": "Korean"
  },
  "sameAs": []
}
</script>

<!-- ============================================ -->
<!-- WebSite Schema (사이트검색 활성화) -->
<!-- ============================================ -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "오늘로또",
  "url": "https://lottoinsight.ai",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "https://lottoinsight.ai/stores/?q={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<!-- ============================================ -->
<!-- Breadcrumb Schema (검색결과 경로 표시) -->
<!-- ============================================ -->
<?php if ($breadcrumb_schema): ?>
<script type="application/ld+json">
<?= $breadcrumb_schema ?>
</script>
<?php endif; ?>

<!-- Fonts with display:swap (CLS 방지) -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">

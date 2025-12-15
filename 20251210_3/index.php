<?php
include_once('./_common.php');

define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 모바일 체크
$is_mobile = G5_IS_MOBILE;

// 페이지 타이틀
$g5['title'] = '로또인사이트 - AI 로또 분석 서비스';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  
  <!-- Primary Meta Tags -->
  <title><?php echo $g5['title']; ?></title>
  <meta name="title" content="로또인사이트 - AI 기반 로또 번호 분석 서비스">
  <meta name="description" content="1,201회차 동행복권 공식 데이터 기반 AI 로또 번호 분석. 카카오톡 3초 로그인으로 무료 1회 분석을 받아보세요.">
  <meta name="keywords" content="로또, 로또 번호, 로또 분석, AI 로또, 로또 예측, 동행복권, 로또인사이트">
  <meta name="robots" content="index, follow">
  <meta name="author" content="로또인사이트">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#030711">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  
  <!-- Canonical -->
  <link rel="canonical" href="<?php echo G5_URL; ?>">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo G5_URL; ?>">
  <meta property="og:title" content="로또인사이트 - AI 기반 로또 번호 분석 서비스">
  <meta property="og:description" content="1,201회차 동행복권 공식 데이터 기반 AI 로또 번호 분석. 카카오톡 3초 로그인으로 무료 1회!">
  <meta property="og:image" content="<?php echo G5_URL; ?>/og-image.png">
  <meta property="og:locale" content="ko_KR">
  
  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="로또인사이트 - AI 기반 로또 번호 분석">
  <meta name="twitter:description" content="1,201회차 데이터 기반 AI 분석. 카카오톡 3초 시작!">
  <meta name="twitter:image" content="<?php echo G5_URL; ?>/og-image.png">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?php echo G5_URL; ?>/favicon.svg">
  <link rel="apple-touch-icon" href="<?php echo G5_URL; ?>/apple-touch-icon.png">
  <link rel="manifest" href="<?php echo G5_URL; ?>/site.webmanifest">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <!-- Shared CSS -->
  <link rel="stylesheet" href="<?php echo G5_URL; ?>/styles/shared.css">
  
  <!-- Kakao SDK -->
  <script src="https://t1.kakaocdn.net/kakao_js_sdk/2.6.0/kakao.min.js" integrity="sha384-6MFdIr0zOira1CHQkedUqJVql0YtcZA1P0nbPrQYJXVJZUkTk/oX4U9GhMkgDHBf" crossorigin="anonymous"></script>
  
  <!-- 그누보드 CSS/JS -->
  <?php if(defined('G5_CSS_URL')) { ?>
  <link rel="stylesheet" href="<?php echo G5_CSS_URL; ?>/style.css">
  <?php } ?>
</head>
<body>
<?php
// index.html의 body 내용을 include
// 실제 배포 시에는 index.html의 body 내용을 여기에 직접 넣거나,
// 별도의 template 파일로 분리하여 include
?>

<!-- 메인 콘텐츠는 index.html 참조 -->
<script>
  // 그누보드 URL 설정
  const G5_URL = '<?php echo G5_URL; ?>';
  const G5_IS_MEMBER = <?php echo $is_member ? 'true' : 'false'; ?>;
  const G5_MEMBER_ID = '<?php echo $member['mb_id'] ?? ''; ?>';
</script>

<?php
// index.html의 JavaScript와 HTML 콘텐츠를 여기에 포함
include_once(G5_PATH.'/index.html');
?>

</body>
</html>


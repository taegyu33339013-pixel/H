<?php
include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit;

// 이미 로그인된 경우 메인으로 리다이렉트
if ($is_member) {
    goto_url(G5_URL);
    exit;
}

// 페이지 타이틀
$g5['title'] = '카카오 로그인 | 로또인사이트';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  
  <!-- Primary Meta Tags -->
  <title><?php echo $g5['title']; ?></title>
  <meta name="title" content="카카오 로그인 | 로또인사이트 - 3초 시작 무료 1회">
  <meta name="description" content="카카오톡 3초 간편 로그인! AI 로또 분석 무료 1회를 즉시 받아보세요.">
  <meta name="robots" content="index, follow">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#030711">
  
  <!-- Canonical -->
  <link rel="canonical" href="<?php echo G5_URL; ?>/auth.php">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo G5_URL; ?>/auth.php">
  <meta property="og:title" content="카카오 로그인 | 로또인사이트">
  <meta property="og:description" content="카카오 3초 시작! AI 로또 분석 무료 1회 즉시 제공.">
  <meta property="og:image" content="<?php echo G5_URL; ?>/og-image.png">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?php echo G5_URL; ?>/favicon.svg">
  <link rel="apple-touch-icon" href="<?php echo G5_URL; ?>/apple-touch-icon.png">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
  
  <!-- Shared CSS -->
  <link rel="stylesheet" href="<?php echo G5_URL; ?>/styles/shared.css">
  
  <!-- Kakao SDK -->
  <script src="https://t1.kakaocdn.net/kakao_js_sdk/2.6.0/kakao.min.js" crossorigin="anonymous"></script>
</head>
<body>

<script>
  const G5_URL = '<?php echo G5_URL; ?>';
  const G5_BBS_URL = '<?php echo G5_BBS_URL; ?>';
</script>

<?php
// auth.html의 body 내용을 include
// include_once(G5_PATH.'/auth_content.html');
?>

<!-- auth.html의 body 내용이 여기에 들어갑니다 -->

</body>
</html>


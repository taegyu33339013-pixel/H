<?php
include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit;

// 페이지 타이틀
$g5['title'] = 'AI 분석 | 로또인사이트';

// 로그인 체크
$is_logged_in = $is_member;
$user_name = $member['mb_name'] ?? '';
$user_id = $member['mb_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  
  <!-- Primary Meta Tags -->
  <title><?php echo $g5['title']; ?></title>
  <meta name="title" content="AI 분석 | 로또인사이트 - 1,201회차 데이터 기반 분석">
  <meta name="description" content="AI가 분석한 이번 주 로또 번호를 확인하세요. 1,201회차 동행복권 공식 데이터 기반 패턴 분석 리포트와 균형 점수를 제공합니다.">
  <meta name="robots" content="index, follow">
  
  <!-- Theme Color -->
  <meta name="theme-color" content="#030711">
  
  <!-- Canonical -->
  <link rel="canonical" href="<?php echo G5_URL; ?>/result.php">
  
  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo G5_URL; ?>/result.php">
  <meta property="og:title" content="AI 분석 | 로또인사이트">
  <meta property="og:description" content="AI가 분석한 이번 주 로또 번호를 확인하세요.">
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
  // 그누보드 연동 변수
  const G5_URL = '<?php echo G5_URL; ?>';
  const G5_IS_MEMBER = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
  const G5_MEMBER_ID = '<?php echo addslashes($user_id); ?>';
  const G5_MEMBER_NAME = '<?php echo addslashes($user_name); ?>';
  
  // 회원 정보를 localStorage에 동기화
  if (G5_IS_MEMBER) {
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('userName', G5_MEMBER_NAME);
    localStorage.setItem('userId', G5_MEMBER_ID);
  }
</script>

<?php
// result.html의 body 내용을 include
// 아래 주석을 해제하고 실제 경로 설정
// include_once(G5_PATH.'/result_content.html');
?>

<!-- result.html의 body 내용이 여기에 들어갑니다 -->
<!-- 배포 시 result.html의 <body> 내부 콘텐츠를 복사하세요 -->

</body>
</html>


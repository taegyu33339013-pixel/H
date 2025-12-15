<?php
/**
 * 카카오 로그인 콜백 처리
 * 그누보드 회원 시스템과 연동
 */
include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit;

// 카카오 REST API 설정 (config.php에서 설정하세요)
define('KAKAO_REST_API_KEY', 'YOUR_KAKAO_REST_API_KEY');
define('KAKAO_REDIRECT_URI', G5_URL.'/kakao_login.php');

// 에러 처리
if (isset($_GET['error'])) {
    alert('카카오 로그인이 취소되었습니다.', G5_URL);
    exit;
}

// 인가 코드 받기
$code = $_GET['code'] ?? '';

if (empty($code)) {
    // 카카오 로그인 페이지로 리다이렉트
    $kakao_auth_url = 'https://kauth.kakao.com/oauth/authorize';
    $kakao_auth_url .= '?client_id=' . KAKAO_REST_API_KEY;
    $kakao_auth_url .= '&redirect_uri=' . urlencode(KAKAO_REDIRECT_URI);
    $kakao_auth_url .= '&response_type=code';
    
    header('Location: ' . $kakao_auth_url);
    exit;
}

// 토큰 요청
$token_url = 'https://kauth.kakao.com/oauth/token';
$token_data = [
    'grant_type' => 'authorization_code',
    'client_id' => KAKAO_REST_API_KEY,
    'redirect_uri' => KAKAO_REDIRECT_URI,
    'code' => $code
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$token_response = curl_exec($ch);
curl_close($ch);

$token_json = json_decode($token_response, true);

if (!isset($token_json['access_token'])) {
    alert('카카오 로그인에 실패했습니다.', G5_URL);
    exit;
}

$access_token = $token_json['access_token'];

// 사용자 정보 요청
$user_url = 'https://kapi.kakao.com/v2/user/me';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $user_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/x-www-form-urlencoded;charset=utf-8'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$user_response = curl_exec($ch);
curl_close($ch);

$user_info = json_decode($user_response, true);

if (!isset($user_info['id'])) {
    alert('사용자 정보를 가져올 수 없습니다.', G5_URL);
    exit;
}

// 카카오 사용자 정보 추출
$kakao_id = $user_info['id'];
$kakao_account = $user_info['kakao_account'] ?? [];
$profile = $kakao_account['profile'] ?? [];

$nickname = $profile['nickname'] ?? '카카오회원' . substr($kakao_id, -4);
$profile_image = $profile['profile_image_url'] ?? '';
$email = $kakao_account['email'] ?? '';

// 소셜 로그인 ID 생성 (kakao_고유번호)
$social_mb_id = 'kakao_' . $kakao_id;

// 기존 회원 확인
$sql = "SELECT * FROM {$g5['member_table']} WHERE mb_id = '{$social_mb_id}'";
$member = sql_fetch($sql);

if (!$member['mb_id']) {
    // 신규 회원 가입
    $mb = [
        'mb_id' => $social_mb_id,
        'mb_password' => get_encrypt_string(md5(uniqid())), // 랜덤 비밀번호
        'mb_name' => $nickname,
        'mb_nick' => $nickname,
        'mb_email' => $email,
        'mb_level' => 2, // 기본 회원 레벨
        'mb_datetime' => G5_TIME_YMDHIS,
        'mb_ip' => $_SERVER['REMOTE_ADDR'],
        'mb_memo' => '카카오 로그인',
        'mb_1' => $profile_image, // 프로필 이미지 저장
        'mb_2' => $kakao_id, // 카카오 고유 ID 저장
    ];
    
    $fields = implode(', ', array_keys($mb));
    $values = "'" . implode("', '", array_values($mb)) . "'";
    
    sql_query("INSERT INTO {$g5['member_table']} ({$fields}) VALUES ({$values})");
    
    // 신규 회원 전용 크레딧 지급 (무료 1회)
    require_once G5_LIB_PATH . '/lotto_credit.lib.php';
    lotto_grant_welcome_credit($social_mb_id, '카카오 로그인 신규 가입 축하');
    
    $member = sql_fetch("SELECT * FROM {$g5['member_table']} WHERE mb_id = '{$social_mb_id}'");
}

// 로그인 처리
set_session('ss_mb_id', $member['mb_id']);
set_session('ss_mb_key', md5($member['mb_datetime'] . $member['mb_ip'] . $_SERVER['REMOTE_ADDR']));

// 최근 로그인 시간 업데이트
sql_query("UPDATE {$g5['member_table']} SET mb_today_login = '".G5_TIME_YMDHIS."', mb_login_ip = '{$_SERVER['REMOTE_ADDR']}' WHERE mb_id = '{$member['mb_id']}'");

// 리다이렉트
$return_url = get_session('kakao_return_url') ?? G5_URL.'/result.php';
set_session('kakao_return_url', '');

goto_url($return_url);
?>


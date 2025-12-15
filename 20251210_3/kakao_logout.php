<?php
/**
 * 카카오 로그아웃 처리
 */
include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit;

// 그누보드 로그아웃 처리
unset($_SESSION['ss_mb_id']);
unset($_SESSION['ss_mb_key']);
session_destroy();

// 메인 페이지로 리다이렉트
$return_url = $_GET['return_url'] ?? G5_URL;
goto_url($return_url);
?>


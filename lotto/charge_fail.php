<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/common.php');
if (!defined('_GNUBOARD_')) exit;

$code = trim($_GET['code'] ?? '');
$msg  = trim($_GET['message'] ?? '결제가 취소되었거나 실패했습니다.');

alert($msg.($code ? " (code: {$code})" : ''), G5_URL.'/result.php');

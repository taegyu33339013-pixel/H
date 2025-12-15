<?php
/**
 * 설치 완료 확인 스크립트
 * 
 * 사용법:
 *   php install/check_installation.php
 * 
 * 모든 필수 설정과 테이블을 확인합니다.
 */

// CLI 모드 체크
$is_cli = (php_sapi_name() === 'cli');

// 실행 시간 제한 해제 (CLI)
if ($is_cli) {
    set_time_limit(0);
    ini_set('memory_limit', '512M');
}

// 그누보드 환경 로드
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);

// CLI 모드에서 필요한 SERVER 변수 설정
if (!isset($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = 80;
}
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}
if (!isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/install/check_installation.php';
}
if (!isset($_SERVER['SCRIPT_FILENAME'])) {
    $_SERVER['SCRIPT_FILENAME'] = __FILE__;
}
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// G5_PATH 중복 정의 방지
if (!defined('G5_PATH')) {
    define('G5_PATH', $_SERVER['DOCUMENT_ROOT']);
}

$common_path = G5_PATH . '/common.php';
if (!file_exists($common_path)) {
    die("Error: common.php not found\n");
}

// 에러 출력 억제 (체크 중)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// CLI 모드에서 경고 억제
if ($is_cli) {
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
}

include_once($common_path);

// 체크 결과
$checks = [];
$errors = [];
$warnings = [];

function add_check($name, $status, $message = '') {
    global $checks;
    $checks[] = [
        'name' => $name,
        'status' => $status, // 'ok', 'warning', 'error'
        'message' => $message
    ];
}

function add_error($message) {
    global $errors;
    $errors[] = $message;
}

function add_warning($message) {
    global $warnings;
    $warnings[] = $message;
}

echo "=== 설치 확인 시작 ===\n\n";

// ============================================
// 1. PHP 버전 체크
// ============================================
echo "1. PHP 환경 체크\n";
echo str_repeat("-", 50) . "\n";

$php_version = PHP_VERSION;
if (version_compare($php_version, '7.4.0', '>=')) {
    add_check('PHP 버전', 'ok', "PHP {$php_version}");
    echo "✅ PHP 버전: {$php_version}\n";
} else {
    add_check('PHP 버전', 'error', "PHP {$php_version} (7.4 이상 필요)");
    add_error("PHP 7.4 이상이 필요합니다. 현재 버전: {$php_version}");
    echo "❌ PHP 버전: {$php_version} (7.4 이상 필요)\n";
}

// PHP 확장 모듈 체크
$required_extensions = ['mysqli', 'curl', 'json', 'mbstring', 'xml', 'dom', 'libxml'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ {$ext} 확장 모듈\n";
    } else {
        echo "❌ {$ext} 확장 모듈 없음\n";
        $missing_extensions[] = $ext;
        add_error("PHP 확장 모듈 '{$ext}'이 설치되지 않았습니다.");
    }
}

if (empty($missing_extensions)) {
    add_check('PHP 확장 모듈', 'ok', '모든 필수 확장 모듈 설치됨');
} else {
    add_check('PHP 확장 모듈', 'error', '누락: ' . implode(', ', $missing_extensions));
}

echo "\n";

// ============================================
// 2. 데이터베이스 연결 체크
// ============================================
echo "2. 데이터베이스 연결 체크\n";
echo str_repeat("-", 50) . "\n";

$dbconfig_file = G5_DATA_DIR . '/' . G5_DBCONFIG_FILE;

if (!file_exists($dbconfig_file)) {
    add_check('데이터베이스 설정 파일', 'error', 'data/dbconfig.php 없음');
    add_error("데이터베이스 설정 파일이 없습니다: {$dbconfig_file}");
    echo "❌ 데이터베이스 설정 파일 없음\n";
    echo "   → 그누보드 설치를 먼저 실행하세요: /install/\n\n";
} else {
    add_check('데이터베이스 설정 파일', 'ok', '존재함');
    echo "✅ 데이터베이스 설정 파일 존재\n";
    
    // DB 연결 테스트
    try {
        if (defined('G5_MYSQL_HOST') && defined('G5_MYSQL_USER')) {
            $test_connect = @sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD);
            if ($test_connect) {
                add_check('데이터베이스 연결', 'ok', '연결 성공');
                echo "✅ 데이터베이스 연결 성공\n";
            } else {
                add_check('데이터베이스 연결', 'error', '연결 실패');
                add_error("데이터베이스 연결 실패. data/dbconfig.php 확인 필요");
                echo "❌ 데이터베이스 연결 실패\n";
            }
        }
    } catch (Exception $e) {
        add_check('데이터베이스 연결', 'error', $e->getMessage());
        add_error("데이터베이스 연결 오류: " . $e->getMessage());
        echo "❌ 데이터베이스 연결 오류: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// ============================================
// 3. 필수 테이블 체크
// ============================================
echo "3. 필수 테이블 체크\n";
echo str_repeat("-", 50) . "\n";

$required_tables = [
    'g5_config' => '그누보드 기본 설정',
    'g5_member' => '회원 테이블',
    'g5_lotto_draw' => '로또 당첨번호',
    'g5_lotto_store' => '판매점 정보',
    'g5_lotto_store_win' => '당첨점 기록',
];

foreach ($required_tables as $table => $desc) {
    $check = sql_query("SHOW TABLES LIKE '{$table}'", false);
    if ($check && sql_num_rows($check) > 0) {
        add_check($table, 'ok', $desc);
        echo "✅ {$table} ({$desc})\n";
    } else {
        add_check($table, 'error', "{$desc} 테이블 없음");
        add_error("필수 테이블 '{$table}' ({$desc})이 없습니다.");
        echo "❌ {$table} ({$desc}) 없음\n";
    }
}

echo "\n";

// ============================================
// 4. 로또 테이블 구조 체크
// ============================================
echo "4. 로또 테이블 구조 체크\n";
echo str_repeat("-", 50) . "\n";

// g5_lotto_store 테이블 구조 확인
$check_store = sql_query("SHOW TABLES LIKE 'g5_lotto_store'", false);
if ($check_store && sql_num_rows($check_store) > 0) {
    $columns = [];
    $result = sql_query("SHOW COLUMNS FROM g5_lotto_store");
    while ($row = sql_fetch_array($result)) {
        $columns[] = $row['Field'];
    }
    
    $required_cols = ['store_id', 'store_name', 'address', 'region1', 'region2', 'wins_1st', 'wins_2nd'];
    $optional_cols = ['region3', 'phone', 'latitude', 'longitude'];
    
    foreach ($required_cols as $col) {
        if (in_array($col, $columns)) {
            echo "✅ {$col}\n";
        } else {
            echo "❌ {$col} 컬럼 없음\n";
            add_error("g5_lotto_store 테이블에 '{$col}' 컬럼이 없습니다.");
        }
    }
    
    foreach ($optional_cols as $col) {
        if (in_array($col, $columns)) {
            echo "✅ {$col} (선택)\n";
        } else {
            echo "⚠️  {$col} 컬럼 없음 (선택사항)\n";
            add_warning("g5_lotto_store 테이블에 '{$col}' 컬럼이 없습니다. (선택사항)");
        }
    }
} else {
    echo "⚠️  g5_lotto_store 테이블이 없습니다.\n";
}

echo "\n";

// ============================================
// 5. 데이터 존재 여부 체크
// ============================================
echo "5. 데이터 존재 여부 체크\n";
echo str_repeat("-", 50) . "\n";

// 당첨번호 데이터
$check_draw = sql_query("SHOW TABLES LIKE 'g5_lotto_draw'", false);
if ($check_draw && sql_num_rows($check_draw) > 0) {
    $row = sql_fetch("SELECT COUNT(*) AS cnt, MAX(draw_no) AS max_round FROM g5_lotto_draw");
    $draw_count = (int)($row['cnt'] ?? 0);
    $max_round = (int)($row['max_round'] ?? 0);
    
    if ($draw_count > 0) {
        add_check('당첨번호 데이터', 'ok', "{$draw_count}개, 최신 회차: {$max_round}회");
        echo "✅ 당첨번호 데이터: {$draw_count}개 (최신 회차: {$max_round}회)\n";
    } else {
        add_check('당첨번호 데이터', 'warning', '데이터 없음');
        add_warning("당첨번호 데이터가 없습니다. 실행: php cron/lotto_seed.php");
        echo "⚠️  당첨번호 데이터 없음\n";
        echo "   → 실행: php cron/lotto_seed.php\n";
    }
}

// 판매점 데이터
$check_store = sql_query("SHOW TABLES LIKE 'g5_lotto_store'", false);
if ($check_store && sql_num_rows($check_store) > 0) {
    $row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store");
    $store_count = (int)($row['cnt'] ?? 0);
    
    if ($store_count > 0) {
        add_check('판매점 데이터', 'ok', "{$store_count}개");
        echo "✅ 판매점 데이터: {$store_count}개\n";
    } else {
        add_check('판매점 데이터', 'warning', '데이터 없음');
        add_warning("판매점 데이터가 없습니다. 실행: php cron/lotto_store_sync.php all");
        echo "⚠️  판매점 데이터 없음\n";
        echo "   → 실행: php cron/lotto_store_sync.php all\n";
    }
}

// 당첨점 데이터
$check_win = sql_query("SHOW TABLES LIKE 'g5_lotto_store_win'", false);
if ($check_win && sql_num_rows($check_win) > 0) {
    $row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store_win");
    $win_count = (int)($row['cnt'] ?? 0);
    
    if ($win_count > 0) {
        add_check('당첨점 데이터', 'ok', "{$win_count}개");
        echo "✅ 당첨점 데이터: {$win_count}개\n";
    } else {
        add_check('당첨점 데이터', 'warning', '데이터 없음');
        add_warning("당첨점 데이터가 없습니다. 실행: php cron/lotto_store_sync.php 1 1202");
        echo "⚠️  당첨점 데이터 없음\n";
        echo "   → 실행: php cron/lotto_store_sync.php 1 1202\n";
    }
}

echo "\n";

// ============================================
// 6. API 키 설정 체크
// ============================================
echo "6. API 키 설정 체크\n";
echo str_repeat("-", 50) . "\n";

// 카카오 API 키
$kakao_config = G5_PATH . '/data/kakao_config.php';
if (file_exists($kakao_config)) {
    include_once($kakao_config);
    if (isset($kakao_api_key) && !empty($kakao_api_key) && $kakao_api_key !== 'YOUR_KAKAO_REST_API_KEY_HERE') {
        add_check('카카오 API 키', 'ok', '설정됨');
        echo "✅ 카카오 API 키 설정됨\n";
    } else {
        add_check('카카오 API 키', 'warning', '설정 안 됨 (선택사항)');
        add_warning("카카오 API 키가 설정되지 않았습니다. (선택사항 - 위도/경도 수집용)");
        echo "⚠️  카카오 API 키 설정 안 됨 (선택사항)\n";
    }
} else {
    add_check('카카오 API 키', 'warning', '설정 파일 없음 (선택사항)');
    echo "⚠️  카카오 API 설정 파일 없음 (선택사항)\n";
}

// 토스 페이먼츠 API 키
$toss_config = G5_PATH . '/api/toss/_toss_config.php';
if (file_exists($toss_config)) {
    include_once($toss_config);
    if (defined('TOSS_CLIENT_KEY') && !empty(TOSS_CLIENT_KEY)) {
        add_check('토스 페이먼츠 API 키', 'ok', '설정됨');
        echo "✅ 토스 페이먼츠 API 키 설정됨\n";
    } else {
        add_check('토스 페이먼츠 API 키', 'warning', '설정 안 됨 (결제 기능 제한)');
        add_warning("토스 페이먼츠 API 키가 설정되지 않았습니다. (결제 기능 제한)");
        echo "⚠️  토스 페이먼츠 API 키 설정 안 됨\n";
    }
} else {
    add_check('토스 페이먼츠 API 키', 'warning', '설정 파일 없음');
    echo "⚠️  토스 페이먼츠 설정 파일 없음\n";
}

echo "\n";

// ============================================
// 7. 파일 권한 체크
// ============================================
echo "7. 파일 권한 체크\n";
echo str_repeat("-", 50) . "\n";

$writable_dirs = [
    'data' => '데이터 디렉토리',
    'data/log' => '로그 디렉토리',
    'data/file' => '파일 업로드 디렉토리',
];

foreach ($writable_dirs as $dir => $desc) {
    $full_path = G5_PATH . '/' . $dir;
    if (is_dir($full_path)) {
        if (is_writable($full_path)) {
            add_check($dir, 'ok', $desc);
            echo "✅ {$dir} ({$desc}) 쓰기 가능\n";
        } else {
            add_check($dir, 'warning', "{$desc} 쓰기 불가");
            add_warning("디렉토리 '{$dir}'에 쓰기 권한이 없습니다.");
            echo "⚠️  {$dir} ({$desc}) 쓰기 불가\n";
            echo "   → 실행: chmod 755 {$dir}\n";
        }
    } else {
        add_check($dir, 'warning', "{$desc} 디렉토리 없음");
        echo "⚠️  {$dir} 디렉토리 없음\n";
    }
}

echo "\n";

// ============================================
// 8. 크론 작업 체크
// ============================================
echo "8. 크론 작업 체크\n";
echo str_repeat("-", 50) . "\n";

$cron_files = [
    'cron/lotto_weekly.php' => '주간 당첨번호 동기화',
    'cron/lotto_store_sync.php' => '판매점 동기화',
    'cron/kakao_store_enrich.php' => '카카오 API 데이터 수집',
];

foreach ($cron_files as $file => $desc) {
    $full_path = G5_PATH . '/' . $file;
    if (file_exists($full_path)) {
        if (is_executable($full_path)) {
            add_check($file, 'ok', $desc);
            echo "✅ {$file} ({$desc})\n";
        } else {
            add_check($file, 'warning', "{$desc} 실행 권한 없음");
            echo "⚠️  {$file} 실행 권한 없음\n";
            echo "   → 실행: chmod +x {$file}\n";
        }
    } else {
        add_check($file, 'error', "{$desc} 파일 없음");
        add_error("크론 파일 '{$file}'이 없습니다.");
        echo "❌ {$file} 파일 없음\n";
    }
}

echo "\n";

// ============================================
// 9. 종합 결과
// ============================================
echo "\n=== 체크 결과 요약 ===\n";
echo str_repeat("=", 50) . "\n";

$ok_count = 0;
$warning_count = 0;
$error_count = 0;

foreach ($checks as $check) {
    if ($check['status'] === 'ok') {
        $ok_count++;
    } elseif ($check['status'] === 'warning') {
        $warning_count++;
    } elseif ($check['status'] === 'error') {
        $error_count++;
    }
}

echo "✅ 정상: {$ok_count}개\n";
echo "⚠️  경고: {$warning_count}개\n";
echo "❌ 오류: {$error_count}개\n\n";

if ($error_count > 0) {
    echo "=== 오류 목록 ===\n";
    foreach ($errors as $error) {
        echo "❌ {$error}\n";
    }
    echo "\n";
}

if ($warning_count > 0) {
    echo "=== 경고 목록 ===\n";
    foreach ($warnings as $warning) {
        echo "⚠️  {$warning}\n";
    }
    echo "\n";
}

// ============================================
// 10. 권장 사항
// ============================================
if ($error_count == 0 && $warning_count == 0) {
    echo "🎉 모든 체크 통과! 프로젝트가 정상적으로 설정되었습니다.\n\n";
} elseif ($error_count == 0) {
    echo "✅ 필수 설정은 완료되었습니다.\n";
    echo "⚠️  일부 선택 사항이 설정되지 않았지만 기본 기능은 사용 가능합니다.\n\n";
} else {
    echo "❌ 일부 필수 설정이 누락되었습니다. 위 오류를 해결하세요.\n\n";
    
    echo "=== 다음 단계 ===\n";
    echo "1. 데이터베이스 설정: data/dbconfig.php 확인\n";
    echo "2. 테이블 생성: php cron/lotto_store_sync.php\n";
    echo "3. 데이터 수집: php cron/lotto_seed.php\n";
    echo "4. 상세 가이드: INSTALLATION_GUIDE.md 참고\n\n";
}

echo "=== 체크 완료 ===\n";

// 종료 코드
exit($error_count > 0 ? 1 : 0);

<?php
/**
 * 로또 판매점 구글 이미지 가져오기 (고급 버전)
 * 
 * 기능:
 * - Google Street View API 사용
 * - 배치 처리
 * - 재시도 로직
 * - 진행 상황 표시
 * - 에러 로깅
 */

// 설정 파일 로드
$config = require __DIR__ . '/fetch_store_images_config.php';

// ============================================
// 초기화
// ============================================
$DB_HOST = $config['database']['host'];
$DB_USER = $config['database']['user'];
$DB_PASS = $config['database']['pass'];
$DB_NAME = $config['database']['name'];
$DB_PORT = $config['database']['port'];

$GOOGLE_API_KEY = $config['google']['api_key'];
$IMAGE_DIR = $config['images']['directory'];
$IMAGE_URL_PATH = $config['images']['url_path'];

// 이미지 디렉토리 생성
if (!is_dir($IMAGE_DIR)) {
    mkdir($IMAGE_DIR, 0755, true);
}

// 파라미터
$LIMIT = isset($_GET['limit']) ? (int)$_GET['limit'] : $config['batch']['limit'];
$START = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$FORCE = isset($_GET['force']) && $_GET['force'] === '1';

// 로그 파일
$LOG_FILE = __DIR__ . '/logs/image_fetch_' . date('Y-m-d') . '.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

function log_message($message) {
    global $LOG_FILE;
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] {$message}\n";
    file_put_contents($LOG_FILE, $log, FILE_APPEND);
    echo $log;
}

// ============================================
// 데이터베이스 연결
// ============================================
try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    log_message("✅ 데이터베이스 연결 성공");
} catch (PDOException $e) {
    log_message("❌ 데이터베이스 연결 실패: " . $e->getMessage());
    die();
}

// ============================================
// 테이블 구조 확인
// ============================================
try {
    $check = $pdo->query("SHOW COLUMNS FROM g5_lotto_store LIKE 'store_image'");
    if ($check->rowCount() === 0) {
        $pdo->exec("ALTER TABLE g5_lotto_store ADD COLUMN store_image VARCHAR(255) NULL AFTER address");
        log_message("✅ store_image 컬럼 추가 완료");
    }
} catch (PDOException $e) {
    log_message("⚠️ 컬럼 확인: " . $e->getMessage());
}

// ============================================
// 판매점 데이터 조회
// ============================================
$sql = "SELECT store_id, store_name, address, latitude, longitude, store_image 
        FROM g5_lotto_store 
        WHERE address IS NOT NULL AND address != ''";
        
if (!$FORCE) {
    $sql .= " AND (store_image IS NULL OR store_image = '' OR store_image = '')";
}

$sql .= " ORDER BY store_id ASC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $LIMIT, PDO::PARAM_INT);
$stmt->bindValue(':offset', $START, PDO::PARAM_INT);
$stmt->execute();
$stores = $stmt->fetchAll();

$total = count($stores);
log_message("📊 처리할 판매점: {$total}개 (시작: {$START})");

if ($total === 0) {
    log_message("✅ 처리할 데이터가 없습니다.");
    exit;
}

// ============================================
// Google Street View 이미지 가져오기
// ============================================
function fetchStreetViewImage($address, $lat, $lng, $apiKey, $saveDir, $config, $retry = 3) {
    // 좌표 우선 사용
    $location = '';
    if (!empty($lat) && !empty($lng) && is_numeric($lat) && is_numeric($lng)) {
        $location = urlencode("{$lat},{$lng}");
    } else {
        $location = urlencode($address);
    }
    
    $svConfig = $config['google']['street_view'];
    $url = sprintf(
        "https://maps.googleapis.com/maps/api/streetview?size=%s&location=%s&fov=%d&heading=%d&pitch=%d&key=%s",
        $svConfig['size'],
        $location,
        $svConfig['fov'],
        $svConfig['heading'],
        $svConfig['pitch'],
        $apiKey
    );
    
    // 재시도 로직
    for ($i = 0; $i < $retry; $i++) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 200 && $imageData !== false) {
            // JSON 에러 체크
            $json = @json_decode($imageData, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($json['error_message'])) {
                if ($i < $retry - 1) {
                    sleep(2); // 재시도 전 대기
                    continue;
                }
                return ['success' => false, 'error' => $json['error_message']];
            }
            
            // 이미지 저장
            $filename = 'store_' . md5($address . $lat . $lng) . '.jpg';
            $filepath = $saveDir . $filename;
            
            if (file_put_contents($filepath, $imageData) !== false) {
                return [
                    'success' => true,
                    'filename' => $filename,
                    'path' => $config['images']['url_path'] . $filename,
                    'size' => filesize($filepath),
                    'url' => $url
                ];
            } else {
                return ['success' => false, 'error' => '파일 저장 실패'];
            }
        } else {
            if ($i < $retry - 1) {
                sleep(2);
                continue;
            }
            return ['success' => false, 'error' => "HTTP {$httpCode}: " . ($error ?: '알 수 없는 오류')];
        }
    }
    
    return ['success' => false, 'error' => '재시도 횟수 초과'];
}

// ============================================
// 배치 처리
// ============================================
$success_count = 0;
$fail_count = 0;
$errors = [];

foreach ($stores as $index => $store) {
    $store_id = $store['store_id'];
    $store_name = $store['store_name'];
    $address = $store['address'];
    $lat = $store['latitude'];
    $lng = $store['longitude'];
    
    $current = $index + 1;
    $progress = round(($current / $total) * 100, 1);
    
    echo "[{$current}/{$total}] ({$progress}%) {$store_name}... ";
    
    $result = fetchStreetViewImage($address, $lat, $lng, $GOOGLE_API_KEY, $IMAGE_DIR, $config);
    
    if ($result['success']) {
        try {
            $updateStmt = $pdo->prepare("UPDATE g5_lotto_store SET store_image = :image WHERE store_id = :id");
            $updateStmt->execute([
                ':image' => $result['path'],
                ':id' => $store_id
            ]);
            echo "✅ ({$result['filename']}, " . round($result['size']/1024, 2) . "KB)\n";
            log_message("✅ [{$store_id}] {$store_name}: {$result['filename']}");
            $success_count++;
        } catch (PDOException $e) {
            echo "❌ DB 업데이트 실패\n";
            log_message("❌ [{$store_id}] DB 업데이트 실패: " . $e->getMessage());
            $fail_count++;
            $errors[] = "Store ID {$store_id}: DB 업데이트 실패";
        }
    } else {
        echo "❌ {$result['error']}\n";
        log_message("❌ [{$store_id}] {$store_name}: {$result['error']}");
        $fail_count++;
        $errors[] = "Store ID {$store_id}: {$result['error']}";
    }
    
    // API 호출 제한 방지
    sleep($config['batch']['delay']);
}

// ============================================
// 결과 요약
// ============================================
echo "\n";
echo str_repeat("=", 50) . "\n";
echo "처리 완료!\n";
echo str_repeat("=", 50) . "\n";
echo "✅ 성공: {$success_count}개\n";
echo "❌ 실패: {$fail_count}개\n";
echo str_repeat("=", 50) . "\n";

if (!empty($errors)) {
    echo "\n에러 상세:\n";
    foreach (array_slice($errors, 0, 10) as $error) {
        echo "  - {$error}\n";
    }
    if (count($errors) > 10) {
        echo "  ... 외 " . (count($errors) - 10) . "개\n";
    }
}

// 다음 배치 정보
if ($total === $LIMIT) {
    $next_start = $START + $LIMIT;
    echo "\n다음 배치 실행:\n";
    $script = basename(__FILE__);
    echo "?limit={$LIMIT}&start={$next_start}\n";
}

log_message("처리 완료: 성공 {$success_count}개, 실패 {$fail_count}개");


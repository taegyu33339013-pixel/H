<?php
/**
 * 로또 판매점 주소 기반 구글 이미지 가져오기 스크립트
 * 
 * 사용법:
 * php fetch_store_images.php
 * 
 * 또는 웹에서 실행:
 * http://your-domain.com/fetch_store_images.php?limit=10&start=0
 */

// ============================================
// 설정
// ============================================
$DB_HOST = 'kayjem07.mycafe24.com';
$DB_USER = 'kayjem07';
$DB_PASS = 'dorr0501!';
$DB_NAME = 'kayjem07'; // 데이터베이스 이름 (확인 필요)
$DB_PORT = 3306;

// Google Street View Static API 키 (필수)
$GOOGLE_API_KEY = 'YOUR_GOOGLE_API_KEY_HERE'; // https://console.cloud.google.com/ 에서 발급

// 이미지 저장 경로
$IMAGE_DIR = __DIR__ . '/images/stores/';
if (!is_dir($IMAGE_DIR)) {
    mkdir($IMAGE_DIR, 0755, true);
}

// 배치 처리 설정
$LIMIT = isset($_GET['limit']) ? (int)$_GET['limit'] : 50; // 한 번에 처리할 개수
$START = isset($_GET['start']) ? (int)$_GET['start'] : 0; // 시작 위치
$FORCE_UPDATE = isset($_GET['force']) && $_GET['force'] === '1'; // 이미 있는 이미지도 다시 가져오기

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
    echo "✅ 데이터베이스 연결 성공\n";
} catch (PDOException $e) {
    die("❌ 데이터베이스 연결 실패: " . $e->getMessage() . "\n");
}

// ============================================
// 테이블 구조 확인 및 이미지 컬럼 추가
// ============================================
try {
    // store_image 컬럼이 있는지 확인
    $check = $pdo->query("SHOW COLUMNS FROM g5_lotto_store LIKE 'store_image'");
    if ($check->rowCount() === 0) {
        // 컬럼 추가
        $pdo->exec("ALTER TABLE g5_lotto_store ADD COLUMN store_image VARCHAR(255) NULL AFTER address");
        echo "✅ store_image 컬럼 추가 완료\n";
    }
} catch (PDOException $e) {
    echo "⚠️ 컬럼 확인/추가 중 오류 (이미 존재할 수 있음): " . $e->getMessage() . "\n";
}

// ============================================
// 판매점 데이터 가져오기
// ============================================
$sql = "SELECT store_id, store_name, address, latitude, longitude, store_image 
        FROM g5_lotto_store 
        WHERE address IS NOT NULL AND address != ''";
        
if (!$FORCE_UPDATE) {
    $sql .= " AND (store_image IS NULL OR store_image = '')";
}

$sql .= " ORDER BY store_id ASC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $LIMIT, PDO::PARAM_INT);
$stmt->bindValue(':offset', $START, PDO::PARAM_INT);
$stmt->execute();
$stores = $stmt->fetchAll();

$total = count($stores);
echo "📊 처리할 판매점: {$total}개\n\n";

if ($total === 0) {
    echo "✅ 처리할 데이터가 없습니다.\n";
    exit;
}

// ============================================
// 이미지 가져오기 함수
// ============================================
function fetchGoogleStreetViewImage($address, $lat, $lng, $apiKey, $savePath) {
    // 좌표가 있으면 좌표 사용, 없으면 주소 사용
    $location = '';
    if (!empty($lat) && !empty($lng)) {
        $location = urlencode("{$lat},{$lng}");
    } else {
        $location = urlencode($address);
    }
    
    // Google Street View Static API URL
    $url = "https://maps.googleapis.com/maps/api/streetview?size=800x600&location={$location}&fov=90&heading=0&pitch=0&key={$apiKey}";
    
    // 이미지 다운로드
    $imageData = @file_get_contents($url);
    
    if ($imageData === false) {
        return ['success' => false, 'error' => '이미지 다운로드 실패'];
    }
    
    // API 응답이 JSON인 경우 (에러)
    $json = json_decode($imageData, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($json['error_message'])) {
        return ['success' => false, 'error' => $json['error_message']];
    }
    
    // 이미지 저장
    $filename = 'store_' . md5($address . $lat . $lng) . '.jpg';
    $filepath = $savePath . $filename;
    
    if (file_put_contents($filepath, $imageData) === false) {
        return ['success' => false, 'error' => '파일 저장 실패'];
    }
    
    return [
        'success' => true,
        'filename' => $filename,
        'path' => '/images/stores/' . $filename,
        'size' => filesize($filepath)
    ];
}

// ============================================
// 배치 처리
// ============================================
$success_count = 0;
$fail_count = 0;
$skip_count = 0;

foreach ($stores as $index => $store) {
    $store_id = $store['store_id'];
    $store_name = $store['store_name'];
    $address = $store['address'];
    $lat = $store['latitude'];
    $lng = $store['longitude'];
    
    $current = $index + 1;
    echo "[{$current}/{$total}] 처리 중: {$store_name} ({$address})... ";
    
    // 이미지 가져오기
    $result = fetchGoogleStreetViewImage($address, $lat, $lng, $GOOGLE_API_KEY, $IMAGE_DIR);
    
    if ($result['success']) {
        // DB 업데이트
        try {
            $updateStmt = $pdo->prepare("UPDATE g5_lotto_store SET store_image = :image WHERE store_id = :id");
            $updateStmt->execute([
                ':image' => $result['path'],
                ':id' => $store_id
            ]);
            echo "✅ 성공 ({$result['filename']}, " . round($result['size']/1024, 2) . "KB)\n";
            $success_count++;
        } catch (PDOException $e) {
            echo "❌ DB 업데이트 실패: " . $e->getMessage() . "\n";
            $fail_count++;
        }
    } else {
        echo "❌ 실패: " . ($result['error'] ?? '알 수 없는 오류') . "\n";
        $fail_count++;
    }
    
    // API 호출 제한 방지 (초당 1회)
    usleep(1000000); // 1초 대기
}

// ============================================
// 결과 요약
// ============================================
echo "\n";
echo "========================================\n";
echo "처리 완료!\n";
echo "========================================\n";
echo "✅ 성공: {$success_count}개\n";
echo "❌ 실패: {$fail_count}개\n";
echo "⏭️  건너뜀: {$skip_count}개\n";
echo "========================================\n";

// 다음 배치 정보
if ($total === $LIMIT) {
    $next_start = $START + $LIMIT;
    echo "\n다음 배치 실행:\n";
    echo "http://your-domain.com/fetch_store_images.php?limit={$LIMIT}&start={$next_start}\n";
}


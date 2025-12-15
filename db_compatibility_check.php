<?php
/**
 * 데이터베이스 테이블과 코드 호환성 체크 스크립트
 * 
 * 사용법:
 *   php db_compatibility_check.php
 * 
 * 이 스크립트는 코드에서 사용하는 컬럼과 실제 테이블 구조를 비교합니다.
 */

// 그누보드 환경 로드
$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);
define('G5_PATH', $_SERVER['DOCUMENT_ROOT']);

$common_path = G5_PATH . '/common.php';
if (!file_exists($common_path)) {
    die("Error: common.php not found\n");
}
include_once($common_path);

echo "=== 데이터베이스 호환성 체크 시작 ===\n\n";

// ============================================
// 1. g5_lotto_store 테이블 체크
// ============================================
echo "1. g5_lotto_store 테이블 체크\n";
echo str_repeat("-", 50) . "\n";

$table = 'g5_lotto_store';
$check_table = sql_query("SHOW TABLES LIKE '{$table}'", false);

if (!$check_table || sql_num_rows($check_table) == 0) {
    echo "❌ 테이블이 존재하지 않습니다.\n";
    echo "   실행: php cron/lotto_store_sync.php\n\n";
} else {
    // 테이블 구조 조회
    $columns = [];
    $result = sql_query("SHOW COLUMNS FROM {$table}");
    while ($row = sql_fetch_array($result)) {
        $columns[] = $row['Field'];
    }
    
    // 코드에서 사용하는 컬럼 목록
    $required_columns = [
        'store_id',      // 필수
        'store_name',    // 필수
        'address',       // 필수
        'region1',       // 필수
        'region2',       // 필수
        'region3',       // 선택 (SEO 최적화)
        'phone',         // 선택
        'latitude',      // 선택 (카카오 API)
        'longitude',     // 선택 (카카오 API)
        'wins_1st',      // 필수
        'wins_2nd',      // 필수
        'created_at',    // 필수
        'updated_at',    // 필수
    ];
    
    echo "테이블 존재: ✅\n";
    echo "컬럼 수: " . count($columns) . "개\n\n";
    
    // 필수 컬럼 체크
    $missing = [];
    $optional_missing = [];
    
    foreach ($required_columns as $col) {
        if (!in_array($col, $columns)) {
            if (in_array($col, ['region3', 'phone', 'latitude', 'longitude'])) {
                $optional_missing[] = $col;
            } else {
                $missing[] = $col;
            }
        }
    }
    
    if (!empty($missing)) {
        echo "❌ 필수 컬럼 누락:\n";
        foreach ($missing as $col) {
            echo "   - {$col}\n";
        }
        echo "\n";
    } else {
        echo "✅ 필수 컬럼 모두 존재\n\n";
    }
    
    if (!empty($optional_missing)) {
        echo "⚠️  선택 컬럼 누락 (기능 제한):\n";
        foreach ($optional_missing as $col) {
            echo "   - {$col}\n";
            if ($col === 'region3') {
                echo "     → SEO 3단계 계층 구조 사용 불가\n";
            } elseif ($col === 'latitude' || $col === 'longitude') {
                echo "     → 지도 표시 불가, GeoCoordinates Schema 사용 불가\n";
            } elseif ($col === 'phone') {
                echo "     → 전화번호 정보 없음\n";
            }
        }
        echo "\n";
    }
    
    // 사용되지 않는 컬럼 체크
    $unused = array_diff($columns, $required_columns);
    if (!empty($unused)) {
        echo "ℹ️  사용되지 않는 컬럼:\n";
        foreach ($unused as $col) {
            echo "   - {$col}\n";
        }
        echo "\n";
    }
}

// ============================================
// 2. g5_lotto_store_win 테이블 체크
// ============================================
echo "\n2. g5_lotto_store_win 테이블 체크\n";
echo str_repeat("-", 50) . "\n";

$table = 'g5_lotto_store_win';
$check_table = sql_query("SHOW TABLES LIKE '{$table}'", false);

if (!$check_table || sql_num_rows($check_table) == 0) {
    echo "❌ 테이블이 존재하지 않습니다.\n";
    echo "   실행: php cron/lotto_store_sync.php\n\n";
} else {
    $columns = [];
    $result = sql_query("SHOW COLUMNS FROM {$table}");
    while ($row = sql_fetch_array($result)) {
        $columns[] = $row['Field'];
    }
    
    $required_columns = [
        'win_id',        // 필수
        'draw_no',       // 필수
        'store_id',      // 필수
        'rank',          // 필수
        'win_type',      // 필수
        'prize_amount',  // 필수
        'created_at',    // 필수
    ];
    
    echo "테이블 존재: ✅\n";
    echo "컬럼 수: " . count($columns) . "개\n\n";
    
    $missing = [];
    foreach ($required_columns as $col) {
        if (!in_array($col, $columns)) {
            $missing[] = $col;
        }
    }
    
    if (!empty($missing)) {
        echo "❌ 필수 컬럼 누락:\n";
        foreach ($missing as $col) {
            echo "   - {$col}\n";
        }
        echo "\n";
    } else {
        echo "✅ 필수 컬럼 모두 존재\n\n";
    }
    
    // draw_date 체크 (코드에서 JOIN으로 사용)
    if (!in_array('draw_date', $columns)) {
        echo "ℹ️  draw_date 컬럼 없음 (g5_lotto_draw에서 JOIN으로 사용)\n\n";
    }
}

// ============================================
// 3. g5_lotto_draw 테이블 체크
// ============================================
echo "\n3. g5_lotto_draw 테이블 체크\n";
echo str_repeat("-", 50) . "\n";

$table = 'g5_lotto_draw';
$check_table = sql_query("SHOW TABLES LIKE '{$table}'", false);

if (!$check_table || sql_num_rows($check_table) == 0) {
    echo "❌ 테이블이 존재하지 않습니다.\n";
    echo "   실행: php cron/lotto_seed.php 또는 php adm/lotto_draw_sync.php\n\n";
} else {
    $columns = [];
    $result = sql_query("SHOW COLUMNS FROM {$table}");
    while ($row = sql_fetch_array($result)) {
        $columns[] = $row['Field'];
    }
    
    $required_columns = [
        'draw_no',           // 필수
        'draw_date',         // 필수
        'n1', 'n2', 'n3', 'n4', 'n5', 'n6', // 필수
        'bonus',             // 필수
        'total_sales',       // 선택 (동기화 시 추가)
        'first_winners',     // 선택
        'first_prize_each',  // 선택
        'second_winners',    // 선택
        'second_prize_each', // 선택
        'third_winners',     // 선택
        'third_prize_each',  // 선택
        'created_at',        // 필수
        'updated_at',        // 필수
    ];
    
    echo "테이블 존재: ✅\n";
    echo "컬럼 수: " . count($columns) . "개\n\n";
    
    $missing = [];
    $optional_missing = [];
    
    foreach ($required_columns as $col) {
        if (!in_array($col, $columns)) {
            if (in_array($col, ['total_sales', 'first_winners', 'first_prize_each', 
                                'second_winners', 'second_prize_each', 
                                'third_winners', 'third_prize_each'])) {
                $optional_missing[] = $col;
            } else {
                $missing[] = $col;
            }
        }
    }
    
    if (!empty($missing)) {
        echo "❌ 필수 컬럼 누락:\n";
        foreach ($missing as $col) {
            echo "   - {$col}\n";
        }
        echo "\n";
    } else {
        echo "✅ 필수 컬럼 모두 존재\n\n";
    }
    
    if (!empty($optional_missing)) {
        echo "⚠️  선택 컬럼 누락:\n";
        foreach ($optional_missing as $col) {
            echo "   - {$col}\n";
        }
        echo "   → 당첨금 정보 동기화 시 자동 추가됨\n\n";
    }
}

// ============================================
// 4. 인덱스 체크
// ============================================
echo "\n4. 인덱스 체크\n";
echo str_repeat("-", 50) . "\n";

$tables_to_check = [
    'g5_lotto_store' => ['idx_region', 'idx_wins', 'idx_name'],
    'g5_lotto_store_win' => ['idx_draw_no', 'idx_store_id', 'idx_rank'],
];

foreach ($tables_to_check as $table => $indexes) {
    $check_table = sql_query("SHOW TABLES LIKE '{$table}'", false);
    if ($check_table && sql_num_rows($check_table) > 0) {
        $result = sql_query("SHOW INDEXES FROM {$table}");
        $existing_indexes = [];
        while ($row = sql_fetch_array($result)) {
            $existing_indexes[] = $row['Key_name'];
        }
        
        foreach ($indexes as $idx) {
            if (in_array($idx, $existing_indexes)) {
                echo "✅ {$table}.{$idx}\n";
            } else {
                echo "⚠️  {$table}.{$idx} 없음 (성능 저하 가능)\n";
            }
        }
    }
}

// ============================================
// 5. 권장 수정 사항
// ============================================
echo "\n\n5. 권장 수정 사항\n";
echo str_repeat("=", 50) . "\n";

$recommendations = [];

// region3 컬럼 추가
$check_region3 = sql_query("SHOW COLUMNS FROM g5_lotto_store LIKE 'region3'", false);
if (!$check_region3 || sql_num_rows($check_region3) == 0) {
    $recommendations[] = [
        'type' => '추가',
        'table' => 'g5_lotto_store',
        'column' => 'region3',
        'sql' => "ALTER TABLE g5_lotto_store ADD COLUMN region3 varchar(50) DEFAULT NULL COMMENT '읍/면/동' AFTER region2, ADD INDEX idx_region3 (region3);",
        'reason' => 'SEO 3단계 계층 구조 지원'
    ];
}

// region3 인덱스 추가
$check_idx_region3 = sql_query("SHOW INDEXES FROM g5_lotto_store WHERE Key_name = 'idx_region3'", false);
if (!$check_idx_region3 || sql_num_rows($check_idx_region3) == 0) {
    if (empty($recommendations) || !in_array('region3', array_column($recommendations, 'column'))) {
        $check_region3_col = sql_query("SHOW COLUMNS FROM g5_lotto_store LIKE 'region3'", false);
        if ($check_region3_col && sql_num_rows($check_region3_col) > 0) {
            $recommendations[] = [
                'type' => '인덱스 추가',
                'table' => 'g5_lotto_store',
                'column' => 'idx_region3',
                'sql' => "ALTER TABLE g5_lotto_store ADD INDEX idx_region3 (region3);",
                'reason' => 'region3 검색 성능 향상'
            ];
        }
    }
}

if (!empty($recommendations)) {
    echo "다음 SQL을 실행하세요:\n\n";
    foreach ($recommendations as $rec) {
        echo "-- {$rec['reason']}\n";
        echo "{$rec['sql']}\n\n";
    }
} else {
    echo "✅ 추가 수정 사항 없음\n";
}

echo "\n=== 체크 완료 ===\n";

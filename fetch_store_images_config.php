<?php
/**
 * 구글 이미지 가져오기 설정 파일
 * 
 * 이 파일을 수정하여 API 키와 DB 정보를 설정하세요.
 */

return [
    // 데이터베이스 설정
    'database' => [
        'host' => 'kayjem07.mycafe24.com',
        'port' => 3306,
        'name' => 'kayjem07', // DB 이름 (일반적으로 사용자명과 동일)
        'user' => 'kayjem07',
        'pass' => 'dorr0501!',
    ],
    
    // Google API 설정
    'google' => [
        'api_key' => 'YOUR_GOOGLE_API_KEY_HERE', // 필수: https://console.cloud.google.com/
        'street_view' => [
            'size' => '800x600',      // 이미지 크기
            'fov' => 90,              // 시야각 (10-120)
            'heading' => 0,           // 방향 (0-360)
            'pitch' => 0,             // 각도 (-90 to 90)
        ]
    ],
    
    // 이미지 저장 설정
    'images' => [
        'directory' => __DIR__ . '/images/stores/',
        'url_path' => '/images/stores/',
        'format' => 'jpg',
        'quality' => 85,
    ],
    
    // 배치 처리 설정
    'batch' => [
        'limit' => 50,                // 한 번에 처리할 개수
        'delay' => 1,                 // API 호출 간 대기 시간 (초)
        'retry' => 3,                 // 실패 시 재시도 횟수
    ],
];


-- =====================================================
-- 로또 판매점/당첨점 테이블 생성 스크립트 (최신 버전)
-- 실행: MySQL에서 이 SQL 파일을 실행하세요
-- 
-- 최신 업데이트: 2025-12-15
-- - region3 컬럼 추가 (SEO 3단계 계층 구조)
-- - idx_region3 인덱스 추가
-- - 카카오 API 연동을 위한 latitude, longitude 컬럼 포함
-- =====================================================

-- =====================================================
-- 1. g5_lotto_store 테이블 (판매점 정보)
-- =====================================================
CREATE TABLE IF NOT EXISTS `g5_lotto_store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) NOT NULL COMMENT '판매점 이름',
  `address` varchar(255) NOT NULL COMMENT '주소',
  `region1` varchar(20) DEFAULT NULL COMMENT '시/도',
  `region2` varchar(50) DEFAULT NULL COMMENT '시/군/구',
  `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동',
  `phone` varchar(20) DEFAULT NULL COMMENT '전화번호',
  `opening_hours` varchar(100) DEFAULT NULL COMMENT '영업시간 (예: 09:00-22:00)',
  `store_image` varchar(255) DEFAULT NULL COMMENT '판매점 이미지 URL',
  `latitude` decimal(10,7) DEFAULT NULL COMMENT '위도',
  `longitude` decimal(10,7) DEFAULT NULL COMMENT '경도',
  `wins_1st` int(11) DEFAULT 0 COMMENT '누적 1등 당첨 횟수',
  `wins_2nd` int(11) DEFAULT 0 COMMENT '누적 2등 당첨 횟수',
  `review_rating` decimal(3,2) DEFAULT NULL COMMENT '평균 리뷰 평점 (0.00-5.00)',
  `review_count` int(11) DEFAULT 0 COMMENT '리뷰 개수',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`store_id`),
  KEY `idx_name` (`store_name`),
  KEY `idx_region` (`region1`, `region2`),
  KEY `idx_region3` (`region3`),
  KEY `idx_wins` (`wins_1st` DESC, `wins_2nd` DESC),
  KEY `idx_address` (`address`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='로또 판매점 정보';

-- =====================================================
-- 2. g5_lotto_store_win 테이블 (회차별 당첨점 기록)
-- =====================================================
CREATE TABLE IF NOT EXISTS `g5_lotto_store_win` (
  `win_id` int(11) NOT NULL AUTO_INCREMENT,
  `draw_no` int(11) NOT NULL COMMENT '회차',
  `store_id` int(11) NOT NULL COMMENT '판매점 ID',
  `rank` tinyint(1) NOT NULL COMMENT '당첨 등수 (1, 2)',
  `win_type` enum('auto','manual','semi') DEFAULT 'auto' COMMENT '자동/수동/반자동',
  `prize_amount` bigint(20) DEFAULT 0 COMMENT '당첨금',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`win_id`),
  UNIQUE KEY `uk_draw_store_rank` (`draw_no`, `store_id`, `rank`),
  KEY `idx_draw_no` (`draw_no`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_rank` (`rank`),
  CONSTRAINT `fk_store_win_store` FOREIGN KEY (`store_id`) REFERENCES `g5_lotto_store` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회차별 당첨점 기록';

-- =====================================================
-- 3. 기존 테이블 마이그레이션 (region3 컬럼 추가)
-- =====================================================
-- MySQL 5.7+ 또는 MariaDB 10.2+에서만 동작
-- 기존 테이블에 region3 컬럼이 없는 경우 추가
ALTER TABLE `g5_lotto_store` 
  ADD COLUMN IF NOT EXISTS `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동' AFTER `region2`;

-- region3 인덱스 추가 (없는 경우)
-- 주의: IF NOT EXISTS는 인덱스에 직접 지원하지 않으므로 에러 무시
ALTER TABLE `g5_lotto_store` 
  ADD INDEX `idx_region3` (`region3`);

-- =====================================================
-- 4. g5_lotto_draw 테이블 확장 (당첨금 정보 컬럼 추가)
-- =====================================================
-- g5_lotto_draw 테이블에 당첨금 관련 컬럼 추가 (없는 경우)
ALTER TABLE `g5_lotto_draw`
  ADD COLUMN IF NOT EXISTS `total_sales` bigint(20) DEFAULT 0 COMMENT '총판매액',
  ADD COLUMN IF NOT EXISTS `first_winners` int(11) DEFAULT 0 COMMENT '1등 당첨자 수',
  ADD COLUMN IF NOT EXISTS `first_prize_each` bigint(20) DEFAULT 0 COMMENT '1등 1인당 당첨금',
  ADD COLUMN IF NOT EXISTS `second_winners` int(11) DEFAULT 0 COMMENT '2등 당첨자 수',
  ADD COLUMN IF NOT EXISTS `second_prize_each` bigint(20) DEFAULT 0 COMMENT '2등 1인당 당첨금',
  ADD COLUMN IF NOT EXISTS `third_winners` int(11) DEFAULT 0 COMMENT '3등 당첨자 수',
  ADD COLUMN IF NOT EXISTS `third_prize_each` bigint(20) DEFAULT 0 COMMENT '3등 1인당 당첨금`;

-- =====================================================
-- 완료 메시지
-- =====================================================
SELECT '테이블 생성/업데이트 완료!' AS message;

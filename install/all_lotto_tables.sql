-- =====================================================
-- 로또 관련 모든 테이블 CREATE 구문 (통합)
-- 최신 버전 (2025-12-15)
-- =====================================================

-- 이 파일은 모든 로또 관련 테이블을 한 번에 생성합니다.
-- 개별 파일도 사용 가능:
--   - install/lotto_store_tables_latest.sql (판매점/당첨점)
--   - install/lotto_credit_tables.sql (크레딧)

-- =====================================================
-- 1. 판매점/당첨점 테이블
-- =====================================================

-- 1-1. g5_lotto_store 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) NOT NULL COMMENT '판매점 이름',
  `address` varchar(255) NOT NULL COMMENT '주소',
  `region1` varchar(20) DEFAULT NULL COMMENT '시/도',
  `region2` varchar(50) DEFAULT NULL COMMENT '시/군/구',
  `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동',
  `phone` varchar(20) DEFAULT NULL COMMENT '전화번호',
  `latitude` decimal(10,7) DEFAULT NULL COMMENT '위도',
  `longitude` decimal(10,7) DEFAULT NULL COMMENT '경도',
  `wins_1st` int(11) DEFAULT 0 COMMENT '누적 1등 당첨 횟수',
  `wins_2nd` int(11) DEFAULT 0 COMMENT '누적 2등 당첨 횟수',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`store_id`),
  KEY `idx_name` (`store_name`),
  KEY `idx_region` (`region1`, `region2`),
  KEY `idx_region3` (`region3`),
  KEY `idx_wins` (`wins_1st` DESC, `wins_2nd` DESC),
  KEY `idx_address` (`address`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='로또 판매점 정보';

-- 1-2. g5_lotto_store_win 테이블
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
-- 2. 크레딧 관련 테이블
-- =====================================================

-- 2-1. g5_lotto_credit 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_credit` (
  `mb_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `free_uses` int(11) DEFAULT 0 COMMENT '무료 사용 가능 횟수',
  `credit_balance` int(11) DEFAULT 0 COMMENT '유료 크레딧 잔액',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  PRIMARY KEY (`mb_id`),
  KEY `idx_credit_balance` (`credit_balance` DESC),
  KEY `idx_free_uses` (`free_uses` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회원 크레딧 정보';

-- 2-2. g5_lotto_credit_log 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_credit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '로그 ID',
  `mb_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `change_type` enum('free','use','charge','admin_adjust','grant') NOT NULL COMMENT '변경 유형',
  `amount` int(11) NOT NULL DEFAULT 0 COMMENT '변경량',
  `before_balance` int(11) DEFAULT 0 COMMENT '변경 전 잔액',
  `after_balance` int(11) DEFAULT 0 COMMENT '변경 후 잔액',
  `memo` varchar(255) DEFAULT '' COMMENT '메모',
  `ref_key` varchar(100) DEFAULT '' COMMENT '참조키',
  `ip` varchar(50) DEFAULT '' COMMENT 'IP 주소',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  PRIMARY KEY (`id`),
  KEY `idx_mb_id` (`mb_id`),
  KEY `idx_change_type` (`change_type`),
  KEY `idx_created_at` (`created_at` DESC),
  KEY `idx_ref_key` (`ref_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='크레딧 사용 로그';

-- 2-3. g5_lotto_credit_number 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_credit_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `mb_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `lotto_round` int(11) DEFAULT 0 COMMENT '회차',
  `numbers` varchar(50) NOT NULL COMMENT '예측번호',
  `score` int(11) DEFAULT 0 COMMENT '점수',
  `strategy` varchar(100) DEFAULT '' COMMENT '전략',
  `is_winner` tinyint(1) DEFAULT 0 COMMENT '당첨 여부',
  `match_count` int(11) DEFAULT 0 COMMENT '일치 개수',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  PRIMARY KEY (`id`),
  KEY `idx_mb_id` (`mb_id`),
  KEY `idx_lotto_round` (`lotto_round`),
  KEY `idx_created_at` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='예측번호 저장';

-- 2-4. g5_lotto_charge_order 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_charge_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '주문 ID',
  `mb_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `order_id` varchar(100) NOT NULL COMMENT '주문번호',
  `package_code` varchar(20) DEFAULT '' COMMENT '패키지 코드',
  `credits` int(11) DEFAULT 0 COMMENT '크레딧 수',
  `bonus` int(11) DEFAULT 0 COMMENT '보너스 크레딧',
  `amount` int(11) DEFAULT 0 COMMENT '결제 금액',
  `method` varchar(20) DEFAULT '' COMMENT '결제 방법',
  `status` enum('READY','PAID','CANCELLED','FAILED') DEFAULT 'READY' COMMENT '주문 상태',
  `requested_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '주문일시',
  `paid_at` datetime DEFAULT NULL COMMENT '결제일시',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_id` (`order_id`),
  KEY `idx_mb_id` (`mb_id`),
  KEY `idx_status` (`status`),
  KEY `idx_requested_at` (`requested_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='크레딧 충전 주문';

-- =====================================================
-- 3. 마이그레이션 (기존 테이블에 region3 추가)
-- =====================================================

-- region3 컬럼 추가 (없는 경우)
ALTER TABLE `g5_lotto_store` 
  ADD COLUMN IF NOT EXISTS `region3` varchar(50) DEFAULT NULL COMMENT '읍/면/동' AFTER `region2`;

-- region3 인덱스 추가
ALTER TABLE `g5_lotto_store` 
  ADD INDEX `idx_region3` (`region3`);

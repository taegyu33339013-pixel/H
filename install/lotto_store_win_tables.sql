-- 로또 판매점 기본 정보 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) NOT NULL COMMENT '판매점 이름',
  `address` varchar(255) NOT NULL COMMENT '주소',
  `region1` varchar(20) DEFAULT NULL COMMENT '시/도',
  `region2` varchar(50) DEFAULT NULL COMMENT '시/군/구',
  `latitude` decimal(10,7) DEFAULT NULL COMMENT '위도',
  `longitude` decimal(10,7) DEFAULT NULL COMMENT '경도',
  `wins_1st` int(11) DEFAULT 0 COMMENT '누적 1등 당첨 횟수',
  `wins_2nd` int(11) DEFAULT 0 COMMENT '누적 2등 당첨 횟수',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`store_id`),
  UNIQUE KEY `uk_store_address` (`store_name`, `address`(100)),
  KEY `idx_region` (`region1`, `region2`),
  KEY `idx_wins` (`wins_1st` DESC, `wins_2nd` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='로또 판매점 정보';

-- 회차별 당첨점 기록 테이블
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

-- g5_lotto_draw 테이블에 당첨금 컬럼 추가 (없다면)
ALTER TABLE `g5_lotto_draw`
  ADD COLUMN IF NOT EXISTS `total_sales` bigint(20) DEFAULT 0 COMMENT '총판매액',
  ADD COLUMN IF NOT EXISTS `first_winners` int(11) DEFAULT 0 COMMENT '1등 당첨자 수',
  ADD COLUMN IF NOT EXISTS `first_prize_each` bigint(20) DEFAULT 0 COMMENT '1등 1인당 당첨금',
  ADD COLUMN IF NOT EXISTS `second_winners` int(11) DEFAULT 0 COMMENT '2등 당첨자 수',
  ADD COLUMN IF NOT EXISTS `second_prize_each` bigint(20) DEFAULT 0 COMMENT '2등 1인당 당첨금',
  ADD COLUMN IF NOT EXISTS `third_winners` int(11) DEFAULT 0 COMMENT '3등 당첨자 수',
  ADD COLUMN IF NOT EXISTS `third_prize_each` bigint(20) DEFAULT 0 COMMENT '3등 1인당 당첨금';


-- =====================================================
-- 로또인사이트 판매점/당첨점 테이블
-- 실행: MySQL에서 이 SQL 파일을 실행하세요
-- =====================================================

-- 판매점 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '판매점명',
  `address` varchar(255) NOT NULL COMMENT '주소',
  `region` varchar(20) NOT NULL DEFAULT '' COMMENT '시/도 코드 (서울, 경기 등)',
  `district` varchar(50) NOT NULL DEFAULT '' COMMENT '구/군',
  `slug` varchar(150) NOT NULL DEFAULT '' COMMENT 'SEO용 URL slug',
  `lat` decimal(10,7) DEFAULT NULL COMMENT '위도',
  `lng` decimal(10,7) DEFAULT NULL COMMENT '경도',
  `phone` varchar(20) DEFAULT NULL COMMENT '전화번호',
  `wins_1st` int(11) NOT NULL DEFAULT 0 COMMENT '1등 당첨 횟수',
  `wins_2nd` int(11) NOT NULL DEFAULT 0 COMMENT '2등 당첨 횟수',
  `last_win_draw` int(11) DEFAULT NULL COMMENT '마지막 당첨 회차',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '영업중 여부',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`store_id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_region` (`region`),
  KEY `idx_district` (`region`, `district`),
  KEY `idx_wins` (`wins_1st` DESC, `wins_2nd` DESC),
  KEY `idx_name` (`name`),
  FULLTEXT KEY `ft_search` (`name`, `address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='로또 판매점';

-- 당첨 이력 테이블
CREATE TABLE IF NOT EXISTS `g5_lotto_store_win` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL COMMENT '판매점 ID',
  `draw_no` int(11) NOT NULL COMMENT '당첨 회차',
  `rank` tinyint(1) NOT NULL DEFAULT 1 COMMENT '등수 (1, 2)',
  `win_type` varchar(10) NOT NULL DEFAULT 'auto' COMMENT '자동/수동/반자동',
  `prize_amount` bigint(20) DEFAULT NULL COMMENT '당첨금액',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_store_draw_rank` (`store_id`, `draw_no`, `rank`),
  KEY `idx_draw_no` (`draw_no`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_rank` (`rank`),
  CONSTRAINT `fk_store_win_store` FOREIGN KEY (`store_id`) REFERENCES `g5_lotto_store` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='판매점 당첨 이력';

-- 인덱스 추가 (검색 성능 향상)
-- ALTER TABLE g5_lotto_store ADD FULLTEXT INDEX ft_search (name, address);

-- 초기 데이터 (샘플 - 실제 수집 전 테스트용)
-- INSERT INTO g5_lotto_store (name, address, region, district, slug, wins_1st, wins_2nd, created_at, updated_at) VALUES
-- ('대운복권', '경기 성남시 중원구 성남동 4159', '경기', '성남시', '경기-대운복권', 5, 17, NOW(), NOW()),
-- ('선경로또', '충북 제천시 하소동 188-1 1층', '충북', '제천시', '충북-선경로또', 5, 5, NOW(), NOW());


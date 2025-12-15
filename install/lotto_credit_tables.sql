-- =====================================================
-- 로또 크레딧 관련 테이블 CREATE 구문
-- 최신 버전 (2025-12-15)
-- =====================================================

-- =====================================================
-- 1. g5_lotto_credit 테이블 (회원 크레딧 메인)
-- =====================================================
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

-- =====================================================
-- 2. g5_lotto_credit_log 테이블 (크레딧 사용 로그)
-- =====================================================
CREATE TABLE IF NOT EXISTS `g5_lotto_credit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '로그 ID',
  `mb_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `change_type` enum('free','use','charge','admin_adjust','grant') NOT NULL COMMENT '변경 유형 (free=무료사용, use=유료사용, charge=충전, admin_adjust=관리자조정, grant=지급)',
  `amount` int(11) NOT NULL DEFAULT 0 COMMENT '변경량 (음수=사용, 양수=충전)',
  `before_balance` int(11) DEFAULT 0 COMMENT '변경 전 잔액 (무료 또는 유료)',
  `after_balance` int(11) DEFAULT 0 COMMENT '변경 후 잔액 (무료 또는 유료)',
  `memo` varchar(255) DEFAULT '' COMMENT '메모',
  `ref_key` varchar(100) DEFAULT '' COMMENT '참조키 (회차, 주문번호 등)',
  `ip` varchar(50) DEFAULT '' COMMENT 'IP 주소',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  PRIMARY KEY (`id`),
  KEY `idx_mb_id` (`mb_id`),
  KEY `idx_change_type` (`change_type`),
  KEY `idx_created_at` (`created_at` DESC),
  KEY `idx_ref_key` (`ref_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='크레딧 사용 로그';

-- =====================================================
-- 3. g5_lotto_credit_number 테이블 (예측번호 저장)
-- =====================================================
CREATE TABLE IF NOT EXISTS `g5_lotto_credit_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `mb_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `lotto_round` int(11) DEFAULT 0 COMMENT '회차',
  `numbers` varchar(50) NOT NULL COMMENT '예측번호 (쉼표 구분)',
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

-- =====================================================
-- 4. g5_lotto_charge_order 테이블 (결제 주문)
-- =====================================================
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
-- 5. g5_lotto_credit_wallet 테이블 (지갑 - 선택사항)
-- =====================================================
-- 주의: 일부 코드에서 사용되지만, g5_lotto_credit과 중복될 수 있음
-- 필요시에만 생성하세요
CREATE TABLE IF NOT EXISTS `g5_lotto_credit_wallet` (
  `mb_id` varchar(100) NOT NULL COMMENT '회원 ID',
  `balance` int(11) DEFAULT 0 COMMENT '잔액',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  PRIMARY KEY (`mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='크레딧 지갑 (선택사항)';

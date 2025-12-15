-- =====================================================
-- 판매점 이미지 및 추가 필드 마이그레이션
-- 기존 테이블에 이미지 관련 필드 추가
-- =====================================================

-- 영업시간 필드 추가 (LocalBusiness Schema 강화)
ALTER TABLE `g5_lotto_store` 
  ADD COLUMN IF NOT EXISTS `opening_hours` varchar(100) DEFAULT NULL COMMENT '영업시간 (예: 09:00-22:00)' AFTER `phone`;

-- 판매점 이미지 필드 추가
ALTER TABLE `g5_lotto_store` 
  ADD COLUMN IF NOT EXISTS `store_image` varchar(255) DEFAULT NULL COMMENT '판매점 이미지 URL' AFTER `opening_hours`;

-- 리뷰 평점 필드 추가 (향후 리뷰 시스템 연동)
ALTER TABLE `g5_lotto_store` 
  ADD COLUMN IF NOT EXISTS `review_rating` decimal(3,2) DEFAULT NULL COMMENT '평균 리뷰 평점 (0.00-5.00)' AFTER `wins_2nd`,
  ADD COLUMN IF NOT EXISTS `review_count` int(11) DEFAULT 0 COMMENT '리뷰 개수' AFTER `review_rating`;

-- 완료 메시지
SELECT '이미지 및 추가 필드 추가 완료!' AS message;

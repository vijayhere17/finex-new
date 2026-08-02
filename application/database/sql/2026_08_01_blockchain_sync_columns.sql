-- Finex blockchain sync columns
-- Run in phpMyAdmin on database: ginance_admin
-- Safe for MySQL 5.7 / MariaDB (no ADD COLUMN IF NOT EXISTS)

-- users: sponsor wallet breakdown + chain flag
SET @db := DATABASE();

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'sponsor_wallet_total'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `sponsor_wallet_total` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `auto_upgrade_balance`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'auto_upgrade_used'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `auto_upgrade_used` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `sponsor_wallet_total`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'chain_registered'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `chain_registered` TINYINT(1) NOT NULL DEFAULT 0 AFTER `next_slot`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- staked_users: on-chain sync + ROI unlock
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'staked_users' AND COLUMN_NAME = 'onchain_investment_id'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `staked_users` ADD COLUMN `onchain_investment_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `slot_number`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'staked_users' AND COLUMN_NAME = 'unlocked_roi'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `staked_users` ADD COLUMN `unlocked_roi` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `total_roi_paid`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'staked_users' AND COLUMN_NAME = 'withdrawn_roi'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `staked_users` ADD COLUMN `withdrawn_roi` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `unlocked_roi`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'staked_users' AND COLUMN_NAME = 'qualifying_directs'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `staked_users` ADD COLUMN `qualifying_directs` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `withdrawn_roi`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'staked_users' AND COLUMN_NAME = 'chain_tx_hash'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `staked_users` ADD COLUMN `chain_tx_hash` VARCHAR(100) NULL DEFAULT NULL AFTER `qualifying_directs`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `blockchain_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT UNSIGNED NULL,
  `tx_type` VARCHAR(48) NOT NULL,
  `tx_hash` VARCHAR(100) NULL,
  `onchain_investment_id` BIGINT UNSIGNED NULL,
  `offchain_ref_id` BIGINT UNSIGNED NULL,
  `amount` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `status` VARCHAR(24) NOT NULL DEFAULT 'pending',
  `payload` JSON NULL,
  `error_message` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blockchain_transactions_member_id_index` (`member_id`),
  KEY `blockchain_transactions_tx_type_index` (`tx_type`),
  KEY `blockchain_transactions_tx_hash_index` (`tx_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

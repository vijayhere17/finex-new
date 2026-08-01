-- Finex compensation tables / columns (run if artisan migrate is unavailable)
-- Skip any statement that errors with Duplicate column / table exists.

ALTER TABLE `users`
  ADD COLUMN `auto_upgrade_balance` DECIMAL(18,4) NOT NULL DEFAULT 0.0000 AFTER `direct_roi_percent`;

ALTER TABLE `users`
  ADD COLUMN `current_slot` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `auto_upgrade_balance`;

ALTER TABLE `users`
  ADD COLUMN `next_slot` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `current_slot`;

ALTER TABLE `staked_users`
  ADD COLUMN `roi_days_paid` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `total_roi_paid`;

ALTER TABLE `staked_users`
  ADD COLUMN `max_roi_days` INT UNSIGNED NOT NULL DEFAULT 300 AFTER `roi_days_paid`;

ALTER TABLE `staked_users`
  ADD COLUMN `slot_number` TINYINT UNSIGNED NULL AFTER `kit_id`;

CREATE TABLE IF NOT EXISTS `daily_roi_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT UNSIGNED NOT NULL,
  `stake_id` BIGINT UNSIGNED NOT NULL,
  `stake_amount` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `roi_percent` DECIMAL(8,2) NOT NULL DEFAULT 0,
  `amount` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `roi_date` DATE NOT NULL,
  `day_number` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_roi_stake_date_unique` (`stake_id`,`roi_date`),
  KEY `daily_roi_logs_member_id_index` (`member_id`),
  KEY `daily_roi_logs_roi_date_index` (`roi_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `level_roi_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT UNSIGNED NOT NULL,
  `from_id` BIGINT UNSIGNED NOT NULL,
  `daily_roi_log_id` BIGINT UNSIGNED NULL,
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `percent` DECIMAL(8,2) NOT NULL DEFAULT 0,
  `base_amount` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `amount` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `roi_date` DATE NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `level_roi_unique` (`member_id`,`from_id`,`level`,`roi_date`,`daily_roi_log_id`),
  KEY `level_roi_logs_roi_date_index` (`roi_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `auto_upgrade_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(32) NOT NULL,
  `slot_number` TINYINT UNSIGNED NULL,
  `amount` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `balance_after` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `from_id` BIGINT UNSIGNED NULL,
  `stake_id` BIGINT UNSIGNED NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auto_upgrade_logs_member_id_index` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `spillover_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` BIGINT UNSIGNED NOT NULL,
  `from_sponsor_id` BIGINT UNSIGNED NULL,
  `to_sponsor_id` BIGINT UNSIGNED NULL,
  `reason` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spillover_logs_member_id_index` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

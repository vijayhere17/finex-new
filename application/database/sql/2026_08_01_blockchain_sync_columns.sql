-- Finex blockchain sync columns (phpMyAdmin / SQL-only deploy)
-- Safe to re-run: uses information_schema checks via procedure-free IF NOT EXISTS patterns where possible.

ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `sponsor_wallet_total` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `auto_upgrade_balance`,
  ADD COLUMN IF NOT EXISTS `auto_upgrade_used` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `sponsor_wallet_total`,
  ADD COLUMN IF NOT EXISTS `chain_registered` TINYINT(1) NOT NULL DEFAULT 0 AFTER `next_slot`;

ALTER TABLE `staked_users`
  ADD COLUMN IF NOT EXISTS `onchain_investment_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `slot_number`,
  ADD COLUMN IF NOT EXISTS `unlocked_roi` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `total_roi_paid`,
  ADD COLUMN IF NOT EXISTS `withdrawn_roi` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `unlocked_roi`,
  ADD COLUMN IF NOT EXISTS `qualifying_directs` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `withdrawn_roi`,
  ADD COLUMN IF NOT EXISTS `chain_tx_hash` VARCHAR(100) NULL DEFAULT NULL AFTER `qualifying_directs`;

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

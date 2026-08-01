-- =============================================================================
-- MLM FINAL BUSINESS PLAN — RAW MySQL ONLY (phpMyAdmin)
-- NO Laravel migrations. NO seeders. Reuses existing tables only.
-- =============================================================================
-- HOW TO USE
-- 1) Take a DB backup.
-- 2) Run SECTION A (checks). If a column already exists, skip that ALTER.
-- 3) Run SECTION B (ALTER users) — FIRST
-- 4) Run SECTION C (ALTER reward masters/achievers) — SECOND
-- 5) Run SECTION D (indexes) — THIRD
-- 6) Run SECTION E (master reward data) — LAST
-- =============================================================================

-- #############################################################################
-- SECTION A — EXISTENCE CHECKS (run first in phpMyAdmin)
-- If the query returns a row, that column/table ALREADY EXISTS → skip its ALTER.
-- #############################################################################

-- Existing base tables (should already exist in production)
SELECT TABLE_NAME
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('users', 'turnover_reward_masters', 'turnover_reward_achievers', 'ewallet_logs');

-- users columns we need
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'users'
  AND COLUMN_NAME IN (
    'registration_fee',
    'reward_id',
    'locked_reward_bonus',
    'unlocked_reward_bonus',
    'expired_reward_bonus',
    'locked_reward_lock_date',
    'locked_reward_expiry_date',
    'sponsor_unlock_done'
  );

-- turnover_reward_masters columns we need
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'turnover_reward_masters'
  AND COLUMN_NAME IN (
    'title',
    'required_directs',
    'required_team',
    'required_self_business',
    'required_team_business',
    'weekly_salary',
    'turnover_amount',
    'cash_reward',
    'milestone_order'
  );

-- turnover_reward_achievers columns we need
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'turnover_reward_achievers'
  AND COLUMN_NAME IN (
    'weekly_salary',
    'directs_count',
    'team_count',
    'self_business',
    'team_business',
    'return_date',
    'last_paid_at',
    'weeks_paid',
    'status',
    'cash_reward',
    'leg1_business',
    'leg2_business',
    'leg3_business'
  );

-- Index check
SELECT INDEX_NAME
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'turnover_reward_achievers'
  AND INDEX_NAME = 'idx_reward_achievers_status';

-- Current reward master rows
SELECT id, milestone_order, turnover_amount, cash_reward
FROM turnover_reward_masters
ORDER BY milestone_order ASC;


-- #############################################################################
-- SECTION B — FIRST: users columns (run one-by-one; skip if already exists)
-- #############################################################################

ALTER TABLE `users`
  ADD COLUMN `registration_fee` DECIMAL(18,2) NOT NULL DEFAULT 1;

ALTER TABLE `users`
  ADD COLUMN `reward_id` BIGINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `users`
  ADD COLUMN `locked_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `users`
  ADD COLUMN `unlocked_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `users`
  ADD COLUMN `expired_reward_bonus` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `users`
  ADD COLUMN `locked_reward_lock_date` DATETIME NULL DEFAULT NULL;

ALTER TABLE `users`
  ADD COLUMN `locked_reward_expiry_date` DATETIME NULL DEFAULT NULL;

ALTER TABLE `users`
  ADD COLUMN `sponsor_unlock_done` TINYINT NOT NULL DEFAULT 0;


-- #############################################################################
-- SECTION C — SECOND: extend existing reward tables (no new tables)
-- Skip any ALTER that fails with "Duplicate column name"
-- #############################################################################

-- turnover_reward_masters (already exists from prior schema)
ALTER TABLE `turnover_reward_masters`
  ADD COLUMN `title` VARCHAR(100) NULL DEFAULT NULL;

ALTER TABLE `turnover_reward_masters`
  ADD COLUMN `required_directs` INT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_masters`
  ADD COLUMN `required_team` INT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_masters`
  ADD COLUMN `required_self_business` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_masters`
  ADD COLUMN `required_team_business` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_masters`
  ADD COLUMN `weekly_salary` DECIMAL(18,2) NOT NULL DEFAULT 0;

-- turnover_reward_achievers (already exists from prior schema)
ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `weekly_salary` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `directs_count` INT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `team_count` INT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `self_business` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `team_business` DECIMAL(18,2) NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `return_date` DATE NULL DEFAULT NULL;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `last_paid_at` DATETIME NULL DEFAULT NULL;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `weeks_paid` INT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `turnover_reward_achievers`
  ADD COLUMN `status` TINYINT NOT NULL DEFAULT 0;


-- #############################################################################
-- SECTION D — THIRD: indexes
-- Skip if index already exists
-- #############################################################################

ALTER TABLE `turnover_reward_achievers`
  ADD INDEX `idx_reward_achievers_status` (`status`);


-- #############################################################################
-- SECTION E — LAST: Reward master data (Rewards 1–7)
-- Complete INSERT + UPDATE. No manual editing required.
-- Uses existing table: turnover_reward_masters
-- #############################################################################

-- Update existing rows by milestone_order (safe if rows already present)
UPDATE `turnover_reward_masters`
SET
  `title` = 'Reward 1',
  `required_directs` = 5,
  `required_team` = 20,
  `required_self_business` = 100,
  `required_team_business` = 5000,
  `turnover_amount` = 5000,
  `cash_reward` = 10,
  `weekly_salary` = 10,
  `updated_at` = NOW()
WHERE `milestone_order` = 1;

UPDATE `turnover_reward_masters`
SET
  `title` = 'Reward 2',
  `required_directs` = 6,
  `required_team` = 50,
  `required_self_business` = 200,
  `required_team_business` = 12000,
  `turnover_amount` = 12000,
  `cash_reward` = 20,
  `weekly_salary` = 20,
  `updated_at` = NOW()
WHERE `milestone_order` = 2;

UPDATE `turnover_reward_masters`
SET
  `title` = 'Reward 3',
  `required_directs` = 7,
  `required_team` = 100,
  `required_self_business` = 300,
  `required_team_business` = 20000,
  `turnover_amount` = 20000,
  `cash_reward` = 50,
  `weekly_salary` = 50,
  `updated_at` = NOW()
WHERE `milestone_order` = 3;

UPDATE `turnover_reward_masters`
SET
  `title` = 'Reward 4',
  `required_directs` = 8,
  `required_team` = 200,
  `required_self_business` = 500,
  `required_team_business` = 50000,
  `turnover_amount` = 50000,
  `cash_reward` = 100,
  `weekly_salary` = 100,
  `updated_at` = NOW()
WHERE `milestone_order` = 4;

UPDATE `turnover_reward_masters`
SET
  `title` = 'Reward 5',
  `required_directs` = 9,
  `required_team` = 500,
  `required_self_business` = 700,
  `required_team_business` = 100000,
  `turnover_amount` = 100000,
  `cash_reward` = 200,
  `weekly_salary` = 200,
  `updated_at` = NOW()
WHERE `milestone_order` = 5;

UPDATE `turnover_reward_masters`
SET
  `title` = 'Reward 6',
  `required_directs` = 10,
  `required_team` = 1000,
  `required_self_business` = 1000,
  `required_team_business` = 250000,
  `turnover_amount` = 250000,
  `cash_reward` = 300,
  `weekly_salary` = 300,
  `updated_at` = NOW()
WHERE `milestone_order` = 6;

UPDATE `turnover_reward_masters`
SET
  `title` = 'Reward 7',
  `required_directs` = 14,
  `required_team` = 2000,
  `required_self_business` = 1500,
  `required_team_business` = 500000,
  `turnover_amount` = 500000,
  `cash_reward` = 500,
  `weekly_salary` = 500,
  `updated_at` = NOW()
WHERE `milestone_order` = 7;

-- Insert only missing milestone_order rows (no duplicate if already present)
INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 1, 'Reward 1', 5, 20, 100, 5000, 5000, 10, 10, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 1);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 2, 'Reward 2', 6, 50, 200, 12000, 12000, 20, 20, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 2);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 3, 'Reward 3', 7, 100, 300, 20000, 20000, 50, 50, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 3);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 4, 'Reward 4', 8, 200, 500, 50000, 50000, 100, 100, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 4);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 5, 'Reward 5', 9, 500, 700, 100000, 100000, 200, 200, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 5);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 6, 'Reward 6', 10, 1000, 1000, 250000, 250000, 300, 300, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 6);

INSERT INTO `turnover_reward_masters`
  (`milestone_order`, `title`, `required_directs`, `required_team`, `required_self_business`, `required_team_business`, `turnover_amount`, `cash_reward`, `weekly_salary`, `created_at`, `updated_at`)
SELECT 7, 'Reward 7', 14, 2000, 1500, 500000, 500000, 500, 500, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `turnover_reward_masters` WHERE `milestone_order` = 7);

-- Optional cleanup: keep only Rewards 1–7 (remove Reward 8, 9, ...)
DELETE FROM `turnover_reward_masters` WHERE `milestone_order` > 7;

-- Verify master data
SELECT
  milestone_order,
  title,
  required_directs,
  required_team,
  required_self_business,
  required_team_business,
  weekly_salary
FROM turnover_reward_masters
ORDER BY milestone_order ASC;


-- =============================================================================
-- NOTES (no SQL to run)
-- =============================================================================
-- Already existing (DO NOT recreate):
--   users, ewallet_logs, turnover_reward_masters, turnover_reward_achievers,
--   stake_masters, staked_users, roi_tier_masters, salary_master (legacy)
--
-- No new tables created.
-- No migration required.
-- earning_type in ewallet_logs (application-level, no schema change):
--   1 Direct, 2 ROI, 4 ROI Override, 7 Reward Salary, 9 Life Time, 10 Locked Unlock
-- =============================================================================

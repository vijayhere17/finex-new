-- =============================================================================
-- Finex: Replace range-based stake_masters tiers with 12 fixed sequential slots
-- =============================================================================
-- Inspected live schema (phpMyAdmin / stake_masters):
--   id, name, amount, amount_max, coin, percantage, cap_multiplier, months,
--   direct_ref, bonus, limit, ptype, locking, is_admin, is_travel,
--   dmc_commission, left_dmc, right_dmc, dmc, created_at, updated_at
--
-- Existing rows (id 1-9) are range tiers. This script:
--   1) UPDATEs id 1-9  -> Slot 1 .. Slot 9
--   2) INSERTs id 10-12 -> Slot 10 .. Slot 12  (ON DUPLICATE KEY UPDATE safe)
--
-- Preserved from current data:
--   percantage = 3, months = 15, direct_ref = 3, ptype = 2,
--   coin/bonus/limit/locking/is_admin/is_travel/dmc* = 0
-- Cap multiplier continues the existing +0.25 step pattern from 2.00
-- Fixed slots: amount = amount_max = exact slot price (no range)
-- =============================================================================

START TRANSACTION;

-- Slot 1 = $10
UPDATE `stake_masters`
SET
  `name` = 'Slot 1 ($10)',
  `amount` = 10,
  `amount_max` = 10,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 2.00,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 1;

-- Slot 2 = $20
UPDATE `stake_masters`
SET
  `name` = 'Slot 2 ($20)',
  `amount` = 20,
  `amount_max` = 20,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 2.25,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 2;

-- Slot 3 = $40
UPDATE `stake_masters`
SET
  `name` = 'Slot 3 ($40)',
  `amount` = 40,
  `amount_max` = 40,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 2.50,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 3;

-- Slot 4 = $80
UPDATE `stake_masters`
SET
  `name` = 'Slot 4 ($80)',
  `amount` = 80,
  `amount_max` = 80,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 2.75,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 4;

-- Slot 5 = $160
UPDATE `stake_masters`
SET
  `name` = 'Slot 5 ($160)',
  `amount` = 160,
  `amount_max` = 160,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 3.00,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 5;

-- Slot 6 = $320
UPDATE `stake_masters`
SET
  `name` = 'Slot 6 ($320)',
  `amount` = 320,
  `amount_max` = 320,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 3.25,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 6;

-- Slot 7 = $640
UPDATE `stake_masters`
SET
  `name` = 'Slot 7 ($640)',
  `amount` = 640,
  `amount_max` = 640,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 3.50,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 7;

-- Slot 8 = $1280
UPDATE `stake_masters`
SET
  `name` = 'Slot 8 ($1280)',
  `amount` = 1280,
  `amount_max` = 1280,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 3.75,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 8;

-- Slot 9 = $2560
UPDATE `stake_masters`
SET
  `name` = 'Slot 9 ($2560)',
  `amount` = 2560,
  `amount_max` = 2560,
  `coin` = 0.00000000,
  `percantage` = 3,
  `cap_multiplier` = 4.00,
  `months` = 15,
  `direct_ref` = 3,
  `bonus` = 0,
  `limit` = 0,
  `ptype` = 2,
  `locking` = 0,
  `is_admin` = 0,
  `is_travel` = 0,
  `dmc_commission` = 0,
  `left_dmc` = 0,
  `right_dmc` = 0,
  `dmc` = 0,
  `updated_at` = NOW()
WHERE `id` = 9;

-- Slots 10-12 (new rows). Safe to re-run.
INSERT INTO `stake_masters`
(
  `id`, `name`, `amount`, `amount_max`, `coin`, `percantage`, `cap_multiplier`,
  `months`, `direct_ref`, `bonus`, `limit`, `ptype`, `locking`, `is_admin`,
  `is_travel`, `dmc_commission`, `left_dmc`, `right_dmc`, `dmc`,
  `created_at`, `updated_at`
)
VALUES
(
  10, 'Slot 10 ($5120)', 5120, 5120, 0.00000000, 3, 4.25,
  15, 3, 0, 0, 2, 0, 0,
  0, 0, 0, 0, 0,
  NOW(), NOW()
),
(
  11, 'Slot 11 ($10240)', 10240, 10240, 0.00000000, 3, 4.50,
  15, 3, 0, 0, 2, 0, 0,
  0, 0, 0, 0, 0,
  NOW(), NOW()
),
(
  12, 'Slot 12 ($20480)', 20480, 20480, 0.00000000, 3, 4.75,
  15, 3, 0, 0, 2, 0, 0,
  0, 0, 0, 0, 0,
  NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `amount` = VALUES(`amount`),
  `amount_max` = VALUES(`amount_max`),
  `coin` = VALUES(`coin`),
  `percantage` = VALUES(`percantage`),
  `cap_multiplier` = VALUES(`cap_multiplier`),
  `months` = VALUES(`months`),
  `direct_ref` = VALUES(`direct_ref`),
  `bonus` = VALUES(`bonus`),
  `limit` = VALUES(`limit`),
  `ptype` = VALUES(`ptype`),
  `locking` = VALUES(`locking`),
  `is_admin` = VALUES(`is_admin`),
  `is_travel` = VALUES(`is_travel`),
  `dmc_commission` = VALUES(`dmc_commission`),
  `left_dmc` = VALUES(`left_dmc`),
  `right_dmc` = VALUES(`right_dmc`),
  `dmc` = VALUES(`dmc`),
  `updated_at` = NOW();

COMMIT;

-- Verify
SELECT `id`, `name`, `amount`, `amount_max`, `percantage`, `cap_multiplier`, `months`, `direct_ref`, `ptype`, `is_admin`, `is_travel`
FROM `stake_masters`
WHERE `ptype` = 2 AND `is_admin` = 0 AND `is_travel` = 0
ORDER BY `amount` ASC;

-- =============================================================================
-- REQUIRED COMPANION: roi_tier_masters
-- Payment activation resolves kit via roi_tier_masters (amount range) then
-- stake_masters.percantage. With fixed slots, give each slot its own tier row
-- (min_amount = max_amount = slot price, daily_percent = 3 to match percantage).
--
-- Columns used by admin UI + migration:
--   min_amount, max_amount, daily_percent, is_active, created_at, updated_at
-- (Do not include cap_multiplier here — admin form/migration do not use it.)
-- =============================================================================

START TRANSACTION;

-- Deactivate old range tiers (keep history)
UPDATE `roi_tier_masters`
SET `is_active` = 0, `updated_at` = NOW()
WHERE `is_active` = 1;

INSERT INTO `roi_tier_masters`
(`min_amount`, `max_amount`, `daily_percent`, `is_active`, `created_at`, `updated_at`)
VALUES
(10,     10,     3.000, 1, NOW(), NOW()),
(20,     20,     3.000, 1, NOW(), NOW()),
(40,     40,     3.000, 1, NOW(), NOW()),
(80,     80,     3.000, 1, NOW(), NOW()),
(160,    160,    3.000, 1, NOW(), NOW()),
(320,    320,    3.000, 1, NOW(), NOW()),
(640,    640,    3.000, 1, NOW(), NOW()),
(1280,   1280,   3.000, 1, NOW(), NOW()),
(2560,   2560,   3.000, 1, NOW(), NOW()),
(5120,   5120,   3.000, 1, NOW(), NOW()),
(10240,  10240,  3.000, 1, NOW(), NOW()),
(20480,  20480,  3.000, 1, NOW(), NOW());

COMMIT;

SELECT `id`, `min_amount`, `max_amount`, `daily_percent`, `is_active`
FROM `roi_tier_masters`
WHERE `is_active` = 1
ORDER BY `min_amount` ASC;
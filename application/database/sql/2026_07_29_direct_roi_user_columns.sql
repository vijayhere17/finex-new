-- =============================================================================
-- Direct ROI Income — users columns (run in phpMyAdmin if migrate is not used)
-- =============================================================================
-- Skip any ALTER that errors with "Duplicate column name".
-- =============================================================================

ALTER TABLE `users`
  ADD COLUMN `activation_status` VARCHAR(32) NOT NULL DEFAULT 'registered' AFTER `kit_id`;

ALTER TABLE `users`
  ADD COLUMN `qualified_active_directs` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `activation_status`;

ALTER TABLE `users`
  ADD COLUMN `direct_roi_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER `qualified_active_directs`;

UPDATE `users`
SET `activation_status` = 'active'
WHERE `kit_id` > 0;

UPDATE `users`
SET `activation_status` = 'registered'
WHERE (`kit_id` IS NULL OR `kit_id` <= 0)
  AND (`activation_status` IS NULL OR `activation_status` = '');

-- Recalculate stored Direct ROI for every user (1% per active direct, max 12%)
UPDATE `users` u
SET
  u.`qualified_active_directs` = (
    SELECT COUNT(*) FROM (
      SELECT d.id FROM `users` d
      WHERE d.`referral_id` = u.`id` AND d.`activation_status` = 'active'
    ) AS q
  ),
  u.`direct_roi_percent` = LEAST(
    (
      SELECT COUNT(*) FROM (
        SELECT d.id FROM `users` d
        WHERE d.`referral_id` = u.`id` AND d.`activation_status` = 'active'
      ) AS q2
    ),
    12
  );

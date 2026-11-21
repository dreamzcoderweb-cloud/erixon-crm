-- ==============================================================================
-- Erixon CRM - Live Production Database Migration: Branch & Team System
-- Run this in phpMyAdmin or MySQL CLI on the Live Database
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create `branches` table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2. Create `teams` table
CREATE TABLE IF NOT EXISTS `teams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `leader_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teams_code_unique` (`code`),
  KEY `teams_branch_id_foreign` (`branch_id`),
  CONSTRAINT `teams_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 3. Create `branch_user` pivot table (Multi-Branch staff support)
CREATE TABLE IF NOT EXISTS `branch_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_user_user_id_branch_id_unique` (`user_id`,`branch_id`),
  KEY `branch_user_branch_id_foreign` (`branch_id`),
  CONSTRAINT `branch_user_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `branch_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 4. Create `team_user` pivot table (Multi-Team staff support)
CREATE TABLE IF NOT EXISTS `team_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `team_id` bigint(20) unsigned NOT NULL,
  `role_in_team` varchar(50) NOT NULL DEFAULT 'member' COMMENT 'member, leader',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_user_user_id_team_id_unique` (`user_id`,`team_id`),
  KEY `team_user_team_id_foreign` (`team_id`),
  CONSTRAINT `team_user_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `team_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 5. Add branch_id and team_id columns to `users`, `customers`, and `leads` (Safe Add via Stored Procedure)
DROP PROCEDURE IF EXISTS AddBranchTeamColumns;
DELIMITER $$
CREATE PROCEDURE AddBranchTeamColumns()
BEGIN
    -- users table
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'branch_id') THEN
        ALTER TABLE `users` ADD COLUMN `branch_id` bigint(20) unsigned DEFAULT NULL AFTER `status`;
    END IF;
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'team_id') THEN
        ALTER TABLE `users` ADD COLUMN `team_id` bigint(20) unsigned DEFAULT NULL AFTER `branch_id`;
    END IF;

    -- customers table
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customers' AND COLUMN_NAME = 'branch_id') THEN
        ALTER TABLE `customers` ADD COLUMN `branch_id` bigint(20) unsigned DEFAULT NULL AFTER `customer_type`;
    END IF;
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customers' AND COLUMN_NAME = 'team_id') THEN
        ALTER TABLE `customers` ADD COLUMN `team_id` bigint(20) unsigned DEFAULT NULL AFTER `branch_id`;
    END IF;

    -- leads table
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'branch_id') THEN
        ALTER TABLE `leads` ADD COLUMN `branch_id` bigint(20) unsigned DEFAULT NULL AFTER `customer_id`;
    END IF;
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'team_id') THEN
        ALTER TABLE `leads` ADD COLUMN `team_id` bigint(20) unsigned DEFAULT NULL AFTER `branch_id`;
    END IF;
END$$
DELIMITER ;

CALL AddBranchTeamColumns();
DROP PROCEDURE IF EXISTS AddBranchTeamColumns;


-- 6. Insert Default "Main Branch" (BR-MAIN) if not present
INSERT INTO `branches` (`name`, `code`, `phone`, `email`, `city`, `status`, `created_at`, `updated_at`)
SELECT 'Main Branch', 'BR-MAIN', '9876543210', 'info@erixon.com', 'Head Office', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `branches` WHERE `code` = 'BR-MAIN');


-- 7. Insert Default "Main Sales Team" (TM-MAIN) under Main Branch if not present
INSERT INTO `teams` (`branch_id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`)
SELECT b.id, 'Main Sales Team', 'TM-MAIN', 'Default system sales team', 1, NOW(), NOW()
FROM `branches` b
WHERE b.code = 'BR-MAIN'
AND NOT EXISTS (SELECT 1 FROM `teams` WHERE `code` = 'TM-MAIN');


-- 8. Migrate existing Data (Assign default branch and team to records where branch_id is NULL)
SET @default_branch_id = (SELECT `id` FROM `branches` WHERE `code` = 'BR-MAIN' LIMIT 1);
SET @default_team_id   = (SELECT `id` FROM `teams` WHERE `code` = 'TM-MAIN' LIMIT 1);

UPDATE `users` SET `branch_id` = @default_branch_id, `team_id` = @default_team_id WHERE `branch_id` IS NULL;
UPDATE `customers` SET `branch_id` = @default_branch_id, `team_id` = @default_team_id WHERE `branch_id` IS NULL;
UPDATE `leads` SET `branch_id` = @default_branch_id, `team_id` = @default_team_id WHERE `branch_id` IS NULL;


-- 9. Populate Pivot Tables for existing users
INSERT IGNORE INTO `branch_user` (`user_id`, `branch_id`, `is_primary`, `created_at`, `updated_at`)
SELECT `id`, `branch_id`, 1, NOW(), NOW() FROM `users` WHERE `branch_id` IS NOT NULL;

INSERT IGNORE INTO `team_user` (`user_id`, `team_id`, `role_in_team`, `is_primary`, `created_at`, `updated_at`)
SELECT `id`, `team_id`, 'member', 1, NOW(), NOW() FROM `users` WHERE `team_id` IS NOT NULL;


-- 10. Add Foreign Keys safely (Set Null on delete)
DROP PROCEDURE IF EXISTS AddBranchTeamForeignKeys;
DELIMITER $$
CREATE PROCEDURE AddBranchTeamForeignKeys()
BEGIN
    -- users FKs
    IF NOT EXISTS (SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'users_branch_id_foreign') THEN
        ALTER TABLE `users` ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;
    END IF;
    IF NOT EXISTS (SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'users_team_id_foreign') THEN
        ALTER TABLE `users` ADD CONSTRAINT `users_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;
    END IF;

    -- customers FKs
    IF NOT EXISTS (SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customers' AND CONSTRAINT_NAME = 'customers_branch_id_foreign') THEN
        ALTER TABLE `customers` ADD CONSTRAINT `customers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;
    END IF;
    IF NOT EXISTS (SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customers' AND CONSTRAINT_NAME = 'customers_team_id_foreign') THEN
        ALTER TABLE `customers` ADD CONSTRAINT `customers_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;
    END IF;

    -- leads FKs
    IF NOT EXISTS (SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND CONSTRAINT_NAME = 'leads_branch_id_foreign') THEN
        ALTER TABLE `leads` ADD CONSTRAINT `leads_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;
    END IF;
    IF NOT EXISTS (SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND CONSTRAINT_NAME = 'leads_team_id_foreign') THEN
        ALTER TABLE `leads` ADD CONSTRAINT `leads_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;
    END IF;
END$$
DELIMITER ;

CALL AddBranchTeamForeignKeys();
DROP PROCEDURE IF EXISTS AddBranchTeamForeignKeys;


-- 11. Add Spatie Permissions for Branch and Team modules
INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('branches.view', 'web', NOW(), NOW()),
('branches.create', 'web', NOW(), NOW()),
('branches.edit', 'web', NOW(), NOW()),
('branches.delete', 'web', NOW(), NOW()),
('teams.view', 'web', NOW(), NOW()),
('teams.create', 'web', NOW(), NOW()),
('teams.edit', 'web', NOW(), NOW()),
('teams.delete', 'web', NOW(), NOW());

-- Attach new permissions to Super Admin & Admin roles
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id 
FROM `permissions` p
CROSS JOIN `roles` r
WHERE p.name IN (
    'branches.view', 'branches.create', 'branches.edit', 'branches.delete',
    'teams.view', 'teams.create', 'teams.edit', 'teams.delete'
) AND r.name IN ('Super Admin', 'Admin', 'super admin', 'Super-Admin');

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- End of Migration Script
-- ==============================================================================

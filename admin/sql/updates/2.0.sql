ALTER TABLE `#__gfy_activities` CHANGE `info` `content` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__gfy_activities` ADD `title` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Title of the object mentioned in the activity.' AFTER `id`;
ALTER TABLE `#__gfy_notifications` CHANGE `note` `content` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__gfy_notifications` ADD `title` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Title of the object mentioned in the notification.' AFTER `id`;

ALTER TABLE `#__gfy_badges` ADD `description` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;
ALTER TABLE `#__gfy_ranks` ADD `description` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;

ALTER TABLE `#__gfy_userranks` DROP `record_date`;

ALTER TABLE `#__gfy_userbadges` ADD UNIQUE `idx_badgeusrgr_ids` (`badge_id`, `user_id`, `group_id`);
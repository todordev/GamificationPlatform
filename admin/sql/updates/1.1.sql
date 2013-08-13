ALTER TABLE `#__gfy_badges` CHANGE COLUMN `points_type` `points_id` SMALLINT( 5 ) unsigned NOT NULL DEFAULT '0' AFTER published;
ALTER TABLE `#__gfy_badges` ADD `note` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `image` ;

ALTER TABLE `#__gfy_levels` CHANGE COLUMN `points_type` `points_id` SMALLINT( 5 ) unsigned NOT NULL DEFAULT '0' AFTER published;
ALTER TABLE `#__gfy_ranks` CHANGE COLUMN `points_type` `points_id` SMALLINT( 5 ) unsigned NOT NULL DEFAULT '0' AFTER published;

ALTER TABLE `#__gfy_userbadges` DROP PRIMARY KEY;
ALTER TABLE `#__gfy_userbadges` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `#__gfy_userbadges` ADD `group_id` TINYINT UNSIGNED NOT NULL AFTER `user_id`;
ALTER TABLE `#__gfy_userbadges` ADD UNIQUE `idx_usrbgs_ids` ( `user_id`, `group_id`, `badge_id` );
ALTER TABLE `#__gfy_userbadges` ADD `note` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `badge_id`;

ALTER TABLE `#__gfy_userlevels` DROP PRIMARY KEY;
ALTER TABLE `#__gfy_userlevels` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `#__gfy_userlevels` ADD `group_id` TINYINT UNSIGNED NOT NULL AFTER `user_id`;
ALTER TABLE `#__gfy_userlevels` ADD UNIQUE `idx_usrlvs_ids` ( `user_id` , `group_id`, `level_id` );
ALTER TABLE `#__gfy_userlevels` DROP `record_date`;

ALTER TABLE `#__gfy_userpoints` ADD UNIQUE `idx_usrpts_ids` ( `user_id` , `points_id` );

ALTER TABLE `#__gfy_userranks` DROP PRIMARY KEY;
ALTER TABLE `#__gfy_userranks` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `#__gfy_userranks` ADD `group_id` TINYINT UNSIGNED NOT NULL AFTER `user_id`;
ALTER TABLE `#__gfy_userranks` ADD UNIQUE `idx_usrrks_ids` ( `user_id`, `group_id`, `rank_id` );



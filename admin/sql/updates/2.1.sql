ALTER TABLE `#__gfy_badges` ADD `activity_text` VARCHAR(255) NULL DEFAULT NULL AFTER `note`;
ALTER TABLE `#__gfy_badges` ADD `ordering` SMALLINT UNSIGNED NOT NULL DEFAULT '0' AFTER `activity_text`;
ALTER TABLE `#__gfy_badges` ADD `params` VARCHAR(1024) NOT NULL DEFAULT '{}' AFTER `published`;
ALTER TABLE `#__gfy_badges` ADD `custom_data` VARCHAR(255) NOT NULL DEFAULT '{}' AFTER `params`;
ALTER TABLE `#__gfy_badges` CHANGE `points` `points_number` INT(10) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `#__gfy_ranks` ADD `activity_text` VARCHAR(255) NULL DEFAULT NULL AFTER `note`;
ALTER TABLE `#__gfy_ranks` CHANGE `points` `points_number` INT(10) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `#__gfy_levels` CHANGE `points` `points_number` INT(10) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `#__gfy_points_history` ADD `context` VARCHAR(64) NOT NULL DEFAULT '' AFTER `points`;

ALTER TABLE `#__gfy_points_history` DROP INDEX `idx_phistory_user_id`;
ALTER TABLE `#__gfy_points_history` DROP INDEX `idx_phistory_points_id`;
ALTER TABLE `#__gfy_points_history` ADD INDEX `idx_ph_uid_pid` (`user_id`, `points_id`);
ALTER TABLE `#__gfy_points_history` ADD INDEX `idx_ph_uid_pid_context` (`user_id`, `points_id`, `context`);

ALTER TABLE `#__gfy_userpoints` CHANGE `points` `points_number` INT(10) UNSIGNED NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `#__gfy_achievements` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `context` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(64) DEFAULT NULL,
  `image_small` varchar(64) DEFAULT NULL,
  `image_square` varchar(64) DEFAULT NULL,
  `activity_text` varchar(256) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `custom_data` varchar(256) NOT NULL DEFAULT '{}',
  `rewards` varchar(256) NOT NULL DEFAULT '{}',
  `points_number` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `points_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_rewards` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(64) DEFAULT NULL,
  `image_small` varchar(64) DEFAULT NULL,
  `image_square` varchar(64) DEFAULT NULL,
  `activity_text` varchar(256) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `number` tinyint(3) UNSIGNED DEFAULT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `points_number` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `points_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userachievements` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `achievement_id` int(10) UNSIGNED NOT NULL,
  `accomplished` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `accomplished_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userrewards` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `reward_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_reusrgr_ids` (`reward_id`,`user_id`,`group_id`) USING BTREE,
  UNIQUE KEY `idx_usrgrreward_ids` (`user_id`,`group_id`,`reward_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
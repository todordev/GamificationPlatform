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

CREATE TABLE IF NOT EXISTS `#__gfy_activities` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT 'Title of the object mentioned in the activity.',
  `content` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_badges` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `points_number` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(64) NOT NULL DEFAULT '',
  `note` varchar(255) DEFAULT NULL,
  `activity_text` varchar(255) DEFAULT NULL,
  `ordering` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `params` varchar(1024) NOT NULL DEFAULT '{}',
  `custom_data` varchar(255) NOT NULL DEFAULT '{}',
  `points_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_groups` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `note` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_levels` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `points_number` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `value` smallint(5) UNSIGNED NOT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `points_id` smallint(5) UNSIGNED NOT NULL,
  `rank_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT 'Title of the object mentioned in the notification.',
  `content` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_points` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `abbr` varchar(8) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_points_abbr` (`abbr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_points_history` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `points_id` smallint(5) UNSIGNED NOT NULL,
  `points` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `context` varchar(64) NOT NULL DEFAULT '',
  `hash` varchar(32) NOT NULL COMMENT 'Generated with data for identifying the record.',
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_phistory_hash` (`hash`(16)),
  KEY `idx_ph_uid_pid_context` (`user_id`,`points_id`,`context`),
  KEY `idx_ph_uid_pid` (`user_id`,`points_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_ranks` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `points_number` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(64) NOT NULL DEFAULT '',
  `note` varchar(255) DEFAULT NULL,
  `activity_text` varchar(255) DEFAULT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
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

CREATE TABLE IF NOT EXISTS `#__gfy_userbadges` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `badge_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrbgs_ids` (`user_id`,`group_id`,`badge_id`),
  UNIQUE KEY `idx_badgeusrgr_ids` (`badge_id`,`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userlevels` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `level_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrlvs_ids` (`user_id`,`group_id`,`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userpoints` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `points_id` int(10) UNSIGNED NOT NULL,
  `points_number` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrpts_ids` (`user_id`,`points_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userranks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `rank_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrrks_ids` (`user_id`,`group_id`,`rank_id`)
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

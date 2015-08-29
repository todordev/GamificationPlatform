CREATE TABLE IF NOT EXISTS `#__gfy_activities` (
  `id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT 'Title of the object mentioned in the activity.',
  `content` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_badges` (
  `id` smallint(5) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `image` varchar(64) NOT NULL DEFAULT '',
  `note` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `points_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_groups` (
  `id` smallint(5) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `note` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_levels` (
  `id` smallint(5) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `value` smallint(5) unsigned NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `points_id` smallint(5) unsigned NOT NULL,
  `rank_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_notifications` (
  `id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT 'Title of the object mentioned in the notification.',
  `content` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_points` (
  `id` smallint(5) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `abbr` varchar(8) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_points_history` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `points_id` smallint(5) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL COMMENT 'Generated with data for identifying a user.',
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_ranks` (
  `id` smallint(5) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `image` varchar(64) NOT NULL DEFAULT '',
  `note` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `points_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userbadges` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` tinyint(3) unsigned NOT NULL,
  `badge_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userlevels` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` tinyint(3) unsigned NOT NULL,
  `level_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userpoints` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `points_id` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gfy_userranks` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` tinyint(3) unsigned NOT NULL,
  `rank_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `#__gfy_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `#__gfy_badges`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__gfy_groups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__gfy_levels`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__gfy_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `#__gfy_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_points_abbr` (`abbr`);

ALTER TABLE `#__gfy_points_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_phistory_user_id` (`user_id`),
  ADD KEY `idx_phistory_hash` (`hash`(16)),
  ADD KEY `idx_phistory_points_id` (`points_id`);

ALTER TABLE `#__gfy_ranks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__gfy_userbadges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usrbgs_ids` (`user_id`,`group_id`,`badge_id`),
  ADD UNIQUE KEY `idx_badgeusrgr_ids` (`badge_id`,`user_id`,`group_id`);

ALTER TABLE `#__gfy_userlevels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usrlvs_ids` (`user_id`,`group_id`,`level_id`);

ALTER TABLE `#__gfy_userpoints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usrpts_ids` (`user_id`,`points_id`);

ALTER TABLE `#__gfy_userranks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usrrks_ids` (`user_id`,`group_id`,`rank_id`);


ALTER TABLE `#__gfy_activities`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_badges`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_groups`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_levels`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_notifications`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_points`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_points_history`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_ranks`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_userbadges`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_userlevels`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_userpoints`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__gfy_userranks`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;


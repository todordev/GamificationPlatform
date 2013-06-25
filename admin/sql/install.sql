CREATE TABLE IF NOT EXISTS `#__gfy_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `info` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__gfy_badges` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `image` varchar(64) NOT NULL DEFAULT '',
  `note` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `points_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

CREATE TABLE IF NOT EXISTS `#__gfy_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `note` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `#__gfy_levels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `value` smallint(5) unsigned NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `points_id` smallint(5) unsigned NOT NULL,
  `rank_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

CREATE TABLE IF NOT EXISTS `#__gfy_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `note` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'It is a URL to small image.',
  `url` varchar(255) DEFAULT NULL COMMENT 'It is a URL to page.',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__gfy_points` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `abbr` varchar(8) NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `#__gfy_ranks` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `image` varchar(64) NOT NULL DEFAULT '',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `points_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `#__gfy_userbadges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` tinyint(3) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `badge_id` int(10) unsigned NOT NULL,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrbgs_ids` (`group_id`,`user_id`,`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__gfy_userlevels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` tinyint(3) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `level_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrlvs_ids` (`group_id`,`user_id`,`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__gfy_userpoints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` tinyint(3) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `points_id` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrpts_ids` (`group_id`,`user_id`,`points_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__gfy_userranks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` tinyint(3) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rank_id` int(10) unsigned NOT NULL,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usrrks_ids` (`group_id`,`user_id`,`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


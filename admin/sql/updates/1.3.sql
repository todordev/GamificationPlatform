ALTER TABLE `#__gfy_points` ADD `note` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `abbr` ;
ALTER TABLE `#__gfy_ranks` ADD `note` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `image` ;
ALTER TABLE `#__gfy_notifications` CHANGE `read` `status` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
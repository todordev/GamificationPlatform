<?php
/**
 * @package      GamificationPlatform
 * @subpackage   GamificationLibrary
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

if (!defined("GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR")) {
    define("GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_gamification");
}

if (!defined("GAMIFICATION_PATH_COMPONENT_SITE")) {
    define("GAMIFICATION_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_gamification");
}

if (!defined("GAMIFICATION_PATH_LIBRARY")) {
    define("GAMIFICATION_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "gamification");
}

if (!defined("ITPRISM_PATH_LIBRARY")) {
    define("ITPRISM_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "itprism");
}

jimport('joomla.utilities.arrayhelper');

// Gamification Libraries
JLoader::register("GamificationVersion", GAMIFICATION_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");

// Register helpers
JLoader::register("GamificationHelper", GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "gamification.php");
JLoader::register("GamificationHelperRoute", GAMIFICATION_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");

// Include HTML helpers path
JHtml::addIncludePath(GAMIFICATION_PATH_COMPONENT_SITE . '/helpers/html');

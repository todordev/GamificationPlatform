<?php
/**
 * @package      Gamification
 * @subpackage   Initializator
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

if (!defined("GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR")) {
    define("GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . "/components/com_gamification");
}

if (!defined("GAMIFICATION_PATH_COMPONENT_SITE")) {
    define("GAMIFICATION_PATH_COMPONENT_SITE", JPATH_SITE . "/components/com_gamification");
}

if (!defined("GAMIFICATION_PATH_LIBRARY")) {
    define("GAMIFICATION_PATH_LIBRARY", JPATH_LIBRARIES . "/gamification");
}

JLoader::registerNamespace('Gamification', JPATH_LIBRARIES);

// Register helpers
JLoader::register("GamificationHelper", GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR . "/helpers/gamification.php");
JLoader::register("GamificationHelperRoute", GAMIFICATION_PATH_COMPONENT_SITE . "/helpers/route.php");

// Include HTML helpers path
JHtml::addIncludePath(GAMIFICATION_PATH_COMPONENT_SITE . '/helpers/html');

// Load library language.
$lang = JFactory::getLanguage();
$lang->load('lib_gamification', GAMIFICATION_PATH_LIBRARY);

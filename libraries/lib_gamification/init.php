<?php
/**
 * @package      Gamification
 * @subpackage   Initializator
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

if (!defined('GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR')) {
    define('GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_gamification');
}

if (!defined('GAMIFICATION_PATH_COMPONENT_SITE')) {
    define('GAMIFICATION_PATH_COMPONENT_SITE', JPATH_SITE . '/components/com_gamification');
}

if (!defined('GAMIFICATION_PATH_LIBRARY')) {
    define('GAMIFICATION_PATH_LIBRARY', JPATH_LIBRARIES . '/Gamification');
}

JLoader::registerNamespace('Gamification', JPATH_LIBRARIES);

// Register helpers
JLoader::register('GamificationHelper', GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR . '/helpers/gamification.php');
JLoader::register('GamificationHelperRoute', GAMIFICATION_PATH_COMPONENT_SITE . '/helpers/route.php');

// Include HTML helpers path
JHtml::addIncludePath(GAMIFICATION_PATH_COMPONENT_SITE . '/helpers/html');

// Load library language.
$lang = JFactory::getLanguage();
$lang->load('lib_gamification', GAMIFICATION_PATH_COMPONENT_SITE);

JLog::addLogger(
    array(
        'text_file' => 'com_gamification.errors.php'
    ),
    // Sets messages of all log levels to be sent to the file
    JLog::CRITICAL + JLog::EMERGENCY + JLog::ALERT + JLog::ERROR,
    // The log category/categories which should be recorded in this file
    // In this case, it's just the one category from our extension, still
    // we need to put it inside an array
    array('com_gamification')
);

<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

GamificationInstallHelper::addRowHeading(JText::_('COM_GAMIFICATION_MINIMUM_REQUIREMENTS'));

// Display result about verification for GD library
$title = JText::_('COM_GAMIFICATION_GD_LIBRARY');
$info  = '';
if (!extension_loaded('gd') and function_exists('gd_info')) {
    $result = array('type' => 'important', 'text' => JText::_('COM_GAMIFICATION_WARNING'));
} else {
    $result = array('type' => 'success', 'text' => JText::_('JON'));
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about verification Magic Quotes
$title = JText::_('COM_GAMIFICATION_MAGIC_QUOTES');
$info  = '';
if (get_magic_quotes_gpc()) {
    $info   = JText::_('COM_GAMIFICATION_MAGIC_QUOTES_INFO');
    $result = array('type' => 'important', 'text' => JText::_('JON'));
} else {
    $result = array('type' => 'success', 'text' => JText::_('JOFF'));
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about verification FileInfo
$title = JText::_('COM_GAMIFICATION_FILEINFO');
$info  = '';
if (!function_exists('finfo_open')) {
    $info   = JText::_('COM_GAMIFICATION_FILEINFO_INFO');
    $result = array('type' => 'important', 'text' => JText::_('JOFF'));
} else {
    $result = array('type' => 'success', 'text' => JText::_('JON'));
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about verification PHP Intl
$title = JText::_('COM_GAMIFICATION_PHPINTL');
$info  = '';
if (!extension_loaded('intl')) {
    $info   = JText::_('COM_GAMIFICATION_PHPINTL_INFO');
    $result = array('type' => 'important', 'text' => JText::_('JNO'));
} else {
    $result = array('type' => 'success', 'text' => JText::_('JYES'));
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about verification for cURL library
$title = JText::_('COM_GAMIFICATION_CURL_LIBRARY');
$info  = '';
if (!extension_loaded('curl')) {
    $info   = JText::_('COM_GAMIFICATION_PHP_CURL_INFO');
    $result = array('type' => 'important', 'text' => JText::_('JNO'));
} else {
    $currentVersion = Prism\Utilities\NetworkHelper::getCurlVersion();
    $text  = JText::sprintf('COM_GAMIFICATION_CURRENT_V_S', $currentVersion);

    if (version_compare($currentVersion, '7.34.0', '<')) {
        $info   = JText::sprintf('COM_GAMIFICATION_REQUIRES_V_S', '7.34.0+');
        $result = array('type' => 'warning', 'text' => $text);
    } else {
        $result = array('type' => 'success', 'text' => $text);
    }
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about verification Open SSL
$title  = JText::_('COM_GAMIFICATION_OPEN_SSL');
$info  = '';
if (!function_exists('curl_init')) {
    $result = array('type' => 'important', 'text' => JText::_('JNO'));
} else {
    $currentVersion = Prism\Utilities\NetworkHelper::getOpenSslVersion();
    $text  = JText::sprintf('COM_GAMIFICATION_CURRENT_V_S', $currentVersion);

    if (version_compare($currentVersion, '1.0.1.3', '<')) {
        $info   = JText::sprintf('COM_GAMIFICATION_REQUIRES_V_S', '1.0.1.3+');
        $result = array('type' => 'warning', 'text' => $text);
    } else {
        $result = array('type' => 'success', 'text' => $text);
    }
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about PHP version
$title = JText::_('COM_GAMIFICATION_PHP_VERSION');
$info  = '';
if (version_compare(PHP_VERSION, '5.5.19') < 0) {
    $result = array('type' => 'important', 'text' => JText::_('COM_GAMIFICATION_WARNING'));
} else {
    $result = array('type' => 'success', 'text' => JText::_('JYES'));
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about MySQL Version.
$title = JText::_('COM_GAMIFICATION_MYSQL_VERSION');
$info  = '';
$dbVersion = JFactory::getDbo()->getVersion();
if (version_compare($dbVersion, '5.5.3', '<')) {
    $result = array('type' => 'important', 'text' => JText::_('COM_GAMIFICATION_WARNING'));
} else {
    $result = array('type' => 'success', 'text' => JText::_('JYES'));
}
GamificationInstallHelper::addRow($title, $result, $info);

// Display result about verification of installed ITPrism Library
$info  = '';
if (!class_exists('Prism\\Version')) {
    $title  = JText::_('COM_GAMIFICATION_PRISM_LIBRARY');
    $info   = JText::_('COM_GAMIFICATION_PRISM_LIBRARY_DOWNLOAD');
    $result = array('type' => 'important', 'text' => JText::_('JNO'));
} else {
    $prismVersion   = new Prism\Version();
    $text           = JText::sprintf('COM_GAMIFICATION_CURRENT_V_S', $prismVersion->getShortVersion());

    if (class_exists('Gamification\\Version')) {
        $componentVersion = new Gamification\Version();
        $title            = JText::sprintf('COM_GAMIFICATION_PRISM_LIBRARY_S', $componentVersion->requiredPrismVersion);

        if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
            $info   = JText::_('COM_GAMIFICATION_PRISM_LIBRARY_DOWNLOAD');
            $result = array('type' => 'warning', 'text' => $text);
        }

    } else {
        $title  = JText::_('COM_GAMIFICATION_PRISM_LIBRARY');
        $result = array('type' => 'success', 'text' => $text);
    }
}
GamificationInstallHelper::addRow($title, $result, $info);

GamificationInstallHelper::endTable();

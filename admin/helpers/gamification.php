<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * This is the helper class of the extension.
 */
class GamificationHelper
{
    public static $extension = "com_gamification";

    /**
     * Configure the Linkbar.
     *
     * @param    string $vName The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_GROUPS'),
            'index.php?option=' . self::$extension . '&view=groups',
            $vName == 'groups'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_POINTS'),
            'index.php?option=' . self::$extension . '&view=points',
            $vName == 'points'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_BADGES'),
            'index.php?option=' . self::$extension . '&view=badges',
            $vName == 'badges'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_RANKS'),
            'index.php?option=' . self::$extension . '&view=ranks',
            $vName == 'ranks'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_LEVELS'),
            'index.php?option=' . self::$extension . '&view=levels',
            $vName == 'levels'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_PROFILES'),
            'index.php?option=' . self::$extension . '&view=profiles',
            $vName == 'profiles'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_NOTIFICATIONS'),
            'index.php?option=' . self::$extension . '&view=notifications',
            $vName == 'notifications'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_ACTIVITIES'),
            'index.php?option=' . self::$extension . '&view=activities',
            $vName == 'activities'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_GAMIFICATION_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=gamification',
            $vName == 'plugins'
        );
    }
}

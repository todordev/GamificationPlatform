<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is Gamification helper class
 *
 */
class GamificationHelper {
	
    static $currency   = null;
    static $extension  = "com_gamification";
      
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'dashboard') {
	    
	    JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_DASHBOARD'),
			'index.php?option='.self::$extension.'&view=dashboard',
			$vName == 'dashboard'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_GROUPS'),
			'index.php?option='.self::$extension.'&view=groups',
			$vName == 'groups'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_POINTS'),
			'index.php?option='.self::$extension.'&view=points',
			$vName == 'points'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_BADGES'),
			'index.php?option='.self::$extension.'&view=badges',
			$vName == 'badges'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_RANKS'),
			'index.php?option='.self::$extension.'&view=ranks',
			$vName == 'ranks'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_LEVELS'),
			'index.php?option='.self::$extension.'&view=levels',
			$vName == 'levels'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_PROFILES'),
			'index.php?option='.self::$extension.'&view=profiles',
			$vName == 'profiles'
		);
		
		JSubMenuHelper::addEntry(
    		JText::_('COM_GAMIFICATION_NOTIFICATIONS'),
    		'index.php?option='.self::$extension.'&view=notifications',
    		$vName == 'notifications'
        );
		
		JSubMenuHelper::addEntry(
        	JText::_('COM_GAMIFICATION_PLUGINS'),
        	'index.php?option=com_plugins&view=plugins&filter_search=gamification',
        	$vName == 'plugins'
        );
	}
    
	public static function getGroupsOptions() {
	    
	    $db = JFactory::getDbo();
	    $query = $db->getQuery(true);
	    
	    $query
	        ->select("a.id AS value, a.name AS text")
	        ->from($db->quoteName("#__gfy_groups") . " AS a");
	        
        $db->setQuery($query);
        $results = $db->loadAssocList();
        
        if(!$results) {
            $results = array();
        }
        
        return $results;
	}
	
	public static function getRanksOptions() {
	    
	    $db = JFactory::getDbo();
	    $query = $db->getQuery(true);
	    
	    $query
	        ->select("a.id AS value, a.title AS text")
	        ->from($db->quoteName("#__gfy_ranks") . " AS a");
	        
        $db->setQuery($query);
        $results = $db->loadAssocList();
        
        if(!$results) {
            $results = array();
        }
        
        return $results;
	}
	
}
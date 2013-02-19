<?php
/**
 * @package      ITPrism Components
 * @subpackage   Gamification
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

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
			'index.php?option=com_gamification&view=dashboard',
			$vName == 'dashboard'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_GROUPS'),
			'index.php?option=com_gamification&view=groups',
			$vName == 'groups'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_POINTS'),
			'index.php?option=com_gamification&view=points',
			$vName == 'points'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_BADGES'),
			'index.php?option=com_gamification&view=badges',
			$vName == 'badges'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_RANKS'),
			'index.php?option=com_gamification&view=ranks',
			$vName == 'ranks'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMIFICATION_LEVELS'),
			'index.php?option=com_gamification&view=levels',
			$vName == 'levels'
		);
	}
    
	/**
	 * 
	 * Load jQuery library and execute noConflict method.
	 * @param object $params
	 */
	public static function loadJQuery($params) {
	    
	    if($params->get('load_jquery', 0)) {
	        
	        $doc = JFactory::getDocument();
	        
	        switch($params->get('load_jquery', 0)) {
	            
	            case 1: // Localhost
	                $doc->addScript('media/'.self::$extension.'/js/jquery-latest.min.js');
	                break;
	                
	            case 2: // Google CDN
//	                $doc->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
	                $doc->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
	                break;
	                
	            default: // Do not load the library
	                break;
	        }
	        
	        if( $params->get('jquery_noconflict', 0)) {
                $doc->addScript('media/'.self::$extension.'/js/jquery-noconflict.js');
	        }
	        
	    }
	}
	
}
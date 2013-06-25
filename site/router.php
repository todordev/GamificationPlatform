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

defined('_JEXEC') or die;

/**
 * Method to build Route
 * @param array $query
 */
function GamificationBuildRoute(&$query){
    
    $segments = array();
    
    // get a menu item based on Itemid or currently active
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();
    
    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if(empty($query['Itemid'])){
        $menuItem = $menu->getActive();
    }else{
        $menuItem = $menu->getItem($query['Itemid']);
    }
    
    $mOption	= (empty($menuItem->query['option'])) ? null : $menuItem->query['option'];
    $mView	    = (empty($menuItem->query['view']))   ? null : $menuItem->query['view'];
	
	// If is set view and Itemid missing, we have to put the view to the segments
	if (isset($query['view'])) {
		$view = $query['view'];
		
		if (empty($query['Itemid']) OR ($mOption !== "com_gamification")) {
			$segments[] = $query['view'];
		}

		// We need to keep the view for forms since they never have their own menu item
		if ($view != 'form') {
			unset($query['view']);
		}
	};
    
    // are we dealing with a category that is attached to a menu item?
	if (isset($view) AND ($mView == $view)) {
		unset($query['view']);
		return $segments;
	}
	
    // Views
	if(isset($view)) {
	    
    	switch($view) {
    	    
    	    case "notification":
    	        $segments[] = "notification";
    	        unset($query["view"]);
    	        break;
    	        
	        case "notifications":
	            unset($query["view"]);
	            break;
    	        
    	}
        
	}
    
	// Layout
    if (isset($query['layout'])) {
		if (!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {
				unset($query['layout']);
			}
		} else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	};
	
	
    return $segments;
}

/**
 * Method to parse Route
 * @param array $segments
 */
function GamificationParseRoute($segments){
    
    $vars = array();
    
    //Get the active menu item.
    $app        = JFactory::getApplication();
    $menu       = $app->getMenu();
    $item       = $menu->getActive();
    
    // Count route segments
    $count      = count($segments);
    
    // Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
	// the first segment is the view and the last segment is the id of the details, category or payment.
    if(!isset($item)) {
        $vars['view']   = $segments[0];
        return $vars;
    } 
    
    // Category 
	if($count == 1) { 
	    
	    $view  = $segments[$count - 1];
	    
	    switch($view) {
	        
	        case "notification": 
        	    $vars['view']   = 'notification';
	            break;

            case "notifications":
                $vars['view']   = 'notifications';
                break;
    	
	    }
		
		return $vars;
	}
    

    return $vars;
}
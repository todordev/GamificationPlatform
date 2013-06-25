<?php
/**
 * @package      Gamification
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

/**
 * This class provides functionality 
 * for creating accounts used for storing 
 * and managing gamification units.
 *
 * @package		Gamification
 * @subpackage	Plugins
 */
class plgUserGamification extends JPlugin {
	
	/**
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param	array		$user		Holds the new user data.
	 * @param	boolean		$isnew		True if a new user is stored.
	 * @param	boolean		$success	True if user was succesfully stored in the database.
	 * @param	string		$msg		Message.
	 *
	 * @return	void
	 * @since	1.6
	 * @throws	Exception on error.
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg) {
	    
		if ($isnew) {
		    
			$userId = JArrayHelper::getValue($user, 'id');
			
			// Give points
			if($this->params->get("points_give", 0)) {
			    $this->givePoints($userId);
			}
			
		}
		
	}
	
    /**
	 *
	 * Method is called after user log in to the system.
	 *
	 * @param	array		$user		An associative array of JAuthenticateResponse type.
	 * @param	array 		$options    An associative array containing these keys: ["remember"] => bool, ["return"] => string, ["entry_url"] => string.
	 *
	 * @return	void
	 * @since	1.6
	 * @throws	Exception on error.
	 */
	public function onUserLogin($user, $options) {

	    // Get user id
	    $userName = JArrayHelper::getValue($user, 'username');
	     
	    $db       = JFactory::getDbo();
	    $query    = $db->getQuery(true);
	     
	    $query
    	    ->select("a.id")
    	    ->from($db->quoteName("#__users") . " AS a")
    	    ->where("a.username = " .$db->quote($userName));
	     
	    $db->setQuery($query, 0, 1);
	    $userId = $db->loadResult();
	    
	    // Give points
		if($this->params->get("points_give", 0)) {
		    $this->givePoints($userId);
		}
	    
	}
	
	/**
	 * 
	 * Add virtual currency to user account after registration.
	 * 
	 * @param integer $userId
	 */
	protected function givePoints($userId) {
	    
	    $pointsTypesValues = $this->params->get("points_types", 0);
	    
	    // Parse point types
	    $pointsTypes = array();
	    if(!empty($pointsTypesValues)) {
	        $pointsTypes = json_decode($pointsTypesValues);
	    }
	    
	    if(!empty($pointsTypes)) {
	        
	        $this->loadLanguage();
	        
	        jimport("gamification.points");
	        jimport("gamification.userpoints");
	        
	        foreach($pointsTypes as $pointsType) {
    	        
    	        $points     = GamificationPoints::getInstance($pointsType->id);
    	        
    	        if($points->id AND $points->published) {
    	            
    	            $keys = array(
                        "user_id"     => $userId,
                        "points_id"   => $points->id
                    );
    	            
    	            $userPoints  = new GamificationUserPoints($keys);
    	            $userPoints->increase($pointsType->value);
    	            $userPoints->store();
    	            
    	        }
    	        
    	        // Integrate notifier
    	        
    	        // Notification services
    	        $nServices = $this->params->get("integration");
    	        if(!empty($nServices)) {
    
    	            $message = JText::sprintf("PLG_USER_GAMIFICATION_NOTIFICATION_AFTER_REGISTRATION", $pointsType->value, $points->title);
    	            $this->notify($nServices, $message, $userId);
    	            
    	        }
    	        
	        }
	        
	        
	    }
	    
	}
	
	public function notify($nServices, $message, $userId) {
	    
	    switch($nServices) {
	        
	        case "gamification":
	            
	            jimport("itprism.integrate.notification.gamification");
	            $notifier = new ITPrismIntegrateNotificationGamification($userId, $message);
	            $notifier->send();
	            
	            break;
	            
            case "socialcommunity":
	                 
                jimport("itprism.integrate.notification.socialcommunity");
                $notifier = new ITPrismIntegrateNotificationSocialCommunity($userId, $message);
                $notifier->send();
                 
                break;
	                
            default:
                
                break;
                
	    }
	    
	}
	

}

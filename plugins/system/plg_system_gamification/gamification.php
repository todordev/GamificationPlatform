<?php
/**
 * @package      Gamification Platform
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeasVote is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.plugin.plugin');

/**
 * This plugin initializes the job of the button
 * which is used for voting.
 *
 * @package      Gamification Platform
 * @subpackage   Plugins
 */
class plgSystemGamification extends JPlugin {
	

    /**
     * Update some gamifigation mechanics of the user - levels, badges, ranks,...
     */
    public function onAfterRoute() {
         
        $app = JFactory::getApplication();
        /** @var $app JSite **/
    
        if($app->isAdmin()) {
            return;
        }
    
        $document = JFactory::getDocument();
        /** @var $document JDocumentHTML **/
    
        $type = $document->getType();
        if(strcmp("html", $type) != 0) {
            return;
        }
    
        $userId = JFactory::getUser()->id;
        if(!$userId) {
            return;
        }
        
        $this->loadLanguage();
        
        // Initialize notification object
        jimport("gamification.notification");
        $this->notification = new GamificationNotification();
        $this->notification->setUserId($userId);
        
        // Get points
        jimport("gamification.points");
        jimport("gamification.userpoints");
        
        $pointsId = $this->params->get("points");
        $points   = GamificationPoints::getInstance($pointsId);
         
        // Get user points
        $userPoints = null;
        if($points->id AND $points->published) {
             
            $keys = array(
                "user_id"     => $userId,
                "points_id"   => $points->id
            );
             
            $userPoints  = GamificationUserPoints::getInstance($keys);
             
        }
        
        if($this->params->get("enable_leveling", 0)) {
            $this->updateLevel($userPoints, $this->params);
        }
        
    }
    
    protected function updateLevel($userPoints, $params) {
        
        // Get all levels
        jimport("gamification.userlevel.points");

        $level = GamificationUserLevelPoints::getInstance($userPoints);
        
        if(!$level->id) {
            
            $data = array(
                "user_id"   => $userPoints->user_id,
                "group_id"  => $userPoints->group_id
            );
            
            $level->startLeveling($data);
            
        } else {
            if($level->levelUp()) {
                $note = JText::sprintf("PLG_SYSTEM_GAMIFICATION_LEVEL_NOTIFICATION", $level->getLevel());
                $this->notification->send($note);
            }
        }
        
    }
	
}
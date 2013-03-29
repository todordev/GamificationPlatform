<?php
/**
 * @package		 Gamification Platform
 * @subpackage	 Crowdfunding Gamification 
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Crowdfunding Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Crowdfunding Gamification Plugin
 *
 * @package		Gamification Platform
 * @subpackage	Crowdfunding Gamification 
 */
class plgCrowdFundingGamification extends JPlugin {
        
    /**
     * 
     * This trigger is executed when payment is completed
     * @param string $context
     * @param object $data			Transaction data
     * @param object $params		Parameters of the component
     * @param object $project		Project data
     * @param object $reward		Reward data
     */
    public function onAfterPayment($context, $data, $params, $project, $reward) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentRaw **/
        
        // Check document type
        $docType = $doc->getType();
        if(strcmp("raw", $docType) != 0){
            return;
        }
       
        if(strcmp("com_crowdfunding.notify", $context) != 0){
            return;
        }
        
        // Load language strings
        $this->loadLanguage();
        
        $db     = JFactory::getDbo();
		
        // POINTS : Increase the number of points
        
        $pointsId = $this->params->get("points_id");
        
        // DEBUG
        $debug  = "POINTS ID: ". $pointsId;
		
        jimport("gamification.points");
        $points = new GamificationPoints($db);
        
        $keys   = array(
            "user_id"     => $data->investor_id,
            "points_id"   => $pointsId
        );
        $points->load($keys);
        
        // Increase user points
        $numberOfPoints = ceil($data->txn_amount);
        if($numberOfPoints > 0) {
            $points->increase($numberOfPoints);
            $points->store();
        }
        
		// NOTIFICATIONS
		
		// Send to investor
		$note = JText::sprintf("PLG_CROWDFUNDING_GAMIFICATION_NOTE_INVESTOR_POINTS", $numberOfPoints, $project->title);
        $this->addNotification($note, $data->investor_id, $db);
		
        // Send to beneficiary
		$note = JText::sprintf("PLG_CROWDFUNDING_GAMIFICATION_NOTE_BENEFICIARY_POINTS", $project->title);
        $this->addNotification($note, $project->user_id, $db);
        
        // ACTIVITIES
		
		// Store activity of the investor
		$note = JText::sprintf("PLG_CROWDFUNDING_GAMIFICATION_ACTIVITY_INVESTOR", $project->title);
        $this->addActivity($note, $data->investor_id, $db);
		
        
    }
    
    /**
     * 
     * This method adds a notification 
     * about last behavior of the user in the queue.
     * @param string $note
     * @param integer $userId
     * @param object $db
     */
    protected function addNotification($note, $userId, $db) {
        
        $notificationProvider   = $this->params->get("notification_provider", "gamification_platform");
        
        switch($notificationProvider) {
            
            case "social_community":
                jimport("socialcommunity.notification");
                $notification = new SocialCommunityNotification($db);
                $notification->send($note, $userId);
                break;
                
            case "gamification_platform":
                jimport("gamification.notification");
                $notification = new GamificationNotification($db);
                $notification->send($note, $userId);
                break;
                
            default: // NONE
                break;
        }
        
    }
    
    /**
     * 
     * This method adds information about activities of users.
     * @param string  $info
     * @param integer $userId
     * @param object  $db
     */
    protected function addActivity($info, $userId, $db) {
        
        $activityProvider   = $this->params->get("notification_provider", "gamification_platform");
        
        switch($activityProvider) {
            
            case "social_community":
                jimport("socialcommunity.activity");
                $activity           = new SocialCommunityActivity($db);
        		$activity->info     = $info;
        		$activity->user_id  = $userId;
                $activity->store();
                
                break;
                
            case "gamification_platform":
                
                jimport("gamification.activity");
                $activity           = new GamificationActivity($db);
        		$activity->info     = $info;
        		$activity->user_id  = $userId;
                $activity->store();
                
                break;
                
            default: // NONE
                break;
        }
        
    }
    
}

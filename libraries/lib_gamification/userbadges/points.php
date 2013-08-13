<?php
/**
 * @package		 Gamification Platform
 * @subpackage	 Gamification Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('JPATH_PLATFORM') or die;

jimport('gamification.interface.table');
jimport('gamification.userpoints');
jimport('gamification.userbadge');
jimport('gamification.userbadges');

/**
 * This class contains methods that manage user badges based on points.
 */
class GamificationUserBadgesPoints extends GamificationUserBadges {

    /**
     * This is user points object.
     *  
     * @var GamificationUserPoints
     */
    protected $userPoints;
    
    public static $instances = array();
    
    /**
     * Initialize user level
     *
     * @param  mixed $keys
     * @return multitype:
     */
    public static function getInstance(GamificationUserPoints $userPoints)  {
    
        // Prepare keys
        if($userPoints instanceof GamificationUserPoints) {
        
            $keys = array(
                "user_id"  => $userPoints->user_id,
                "group_id" => $userPoints->group_id
            );
            
            $index    = md5($userPoints->user_id.":".$userPoints->group_id);
            
        } else {
            return null;
        }
    
        if (empty(self::$instances[$index])){
            $item = new GamificationUserBadgesPoints($keys);
            $item->setUserPoints($userPoints);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    /**
     * Set the object GamificationUserPoints to the variable.
     *
     * @param GamificationUserPoints $userPoints
     */
    public function setUserPoints($userPoints) {
        $this->userPoints = $userPoints;
    }
    
    /**
     * Give a new badge.
     * 
     * @param  $note    A note about badge.   
     * 
     * @return boolean TRUE if we are giving a new badge. FALSE if we do not giving a new badge.
     */
    public function giveBadge($note = null) {
        
        // Get next badge 
        $actualBadge = $this->findActualBadge();
        
        // Check for existing badge
        $badgeExists = false;
        if(!empty($actualBadge->badge_id)) {
            $badge = $this->getBadge($actualBadge->badge_id, $this->userPoints->group_id);
            if(!empty($badge)) {
                $badgeExists = true;
            }
        }

        // Add the new badge to database
        if(!empty($actualBadge->badge_id) AND (!$badgeExists)) {
            
            $data  = JArrayHelper::fromObject($actualBadge);

            $badge = new GamificationUserBadge();
            $badge->bind($data);
            $badge->setUserId($this->userPoints->user_id);
            
            $note = JString::trim(strip_tags($note));
            if(!empty($note)) {
                $badge->setNote($note);
            }
            $badge->store();

            $this->badges[$this->userPoints->group_id][$actualBadge->badge_id] = $badge;
            
            return $badge;
        }
        
        return null;
    }
    
    /**
     * Find a badge that actual have to be.
     * 
     * @return mixed NULL or array
     */
    public function findActualBadge() {
        
        // Get all levels
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id AS badge_id, a.title, a.points, a.image, a.points_id, a.group_id, a.published")
            ->from($this->db->quoteName("#__gfy_badges") . " AS a")
            ->where("a.points_id = ". (int)$this->userPoints->points_id);
        
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        
        $badge  = null;
        for($i = 0, $max = count($results); $i < $max; $i++ ) {
        
            // Get current item
            $current  = (isset($results[$i])) ? $results[$i] : null;
            
            // Get next item
            $n        = abs($i+1);
            $next     = (isset($results[$n])) ? $results[$n] : null;
            
            if(!empty($next)) {
                
                // Check for coincidence with next item
                if ($this->userPoints->points == $next->points){
                    $badge = $next;
                    break;
                }
                
                // Check for coincidence with current item
                if (
                    ( $this->userPoints->points >= $current->points)
                    AND
                    ( $this->userPoints->points < $next->points )
                ) {
                    
                    $badge = $current;
                    break;
                }
                
            } else { // If there is not next item, we compare with last (current).
                
                if ($this->userPoints->points >= $current->points) {
                    
                    $badge = $current;
                    break;
                }
                
            }
        
        }
        
        return $badge;
        
    }
    
}


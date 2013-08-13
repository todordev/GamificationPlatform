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
jimport('gamification.userlevel');

/**
 * This class contains methods that manage user level based on points.
 */
class GamificationUserLevelPoints extends GamificationUserLevel {

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
            $item = new GamificationUserLevelPoints($keys);
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
     * Update level to new one.
     * 
     * @return boolean Return true if level up or false if not.
     */
    public function levelUp() {
        
        // Get next level 
        $actualLevelId = $this->findActualLevelId();
        
        if($actualLevelId != $this->level_id) {
            
            $this->setLevelId($actualLevelId);
            $this->store();
            
            // Load data
            $keys = array (
                "user_id"  => $this->userPoints->user_id,
                "group_id" => $this->userPoints->group_id,
            );
            
            $this->load($keys);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Find a level that actual have to be.
     * 
     * @return mixed NULL or integer
     */
    public function findActualLevelId() {
        
        // Get all levels
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.points")
            ->from($this->db->quoteName("#__gfy_levels") . " AS a")
            ->where("a.points_id = ". (int)$this->userPoints->points_id);
        
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        
        $levelId  = null;
        for($i = 0, $max = count($results); $i < $max; $i++ ) {
        
            // Get current item
            $current  = (isset($results[$i])) ? $results[$i] : null;
            
            // Get next item
            $n        = abs($i+1);
            $next     = (isset($results[$n])) ? $results[$n] : null;
            
            if(!empty($next)) {
                
                // Check for coincidence with next item
                if ($this->userPoints->points == $next->points){
                    $levelId = $next->id;
                    break;
                }
                
                // Check for coincidence with current item
                if (
                    ( $this->userPoints->points >= $current->points)
                    AND
                    ( $this->userPoints->points < $next->points )
                ) {
                    
                    $levelId = $current->id;
                    break;
                }
                
            } else { // If there is not next item, we compare with last (current).
                
                if ($this->userPoints->points >= $current->points) {
                    $levelId = $current->id;
                    break;
                }
                
            }
        
        }
        
        return $levelId;
        
    }
    
    /**
     * Create a record to the database, adding first level.
     *  
     * @param array $data 
     * 
     * </code>
     * $data = array( 
     *     "user_id"  => $userId,
     *     "group_id" => $groupId
     * );
     * <code>
     * 
     */
    public function startLeveling($data) {
        
        if(empty($data["level_id"])) {
            $data["level_id"] = $this->findActualLevelId();
        }
        
        $this->bind($data);
        $this->store();
        
        // Load data
        $keys = array(
            "user_id"  => $data["user_id"],
            "group_id" => $data["group_id"]
        );
        
        $this->load($keys);
    }
    
}


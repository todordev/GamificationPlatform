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

/**
 * This class contains methods that are used for managing user levels.
 */
class GamificationUserLevels {

    /**
     * Users ID
     * @var integer
     */
    public $userId;
    
    public $levels = array();
    
    /**
     * Database driver
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected static $instances = array();
    
    public function __construct($keys = array()) {
        
        $this->db = JFactory::getDbo();
        if(!empty($keys)) {
            $this->load($keys);
        }
        
    }
    
    /**
     * Initialize user levels
     *
     * @param  array $keys
     * @return mixed NULL or GamificationUserLevels
     */
    public static function getInstance(array $keys)  {
    
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
    
        $index    = md5($userId.":".$groupId);
    
        if (empty(self::$instances[$index])){
            $item = new GamificationUserLevels($keys);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    
    /**
     * Load all user levels and set them to group index.
     * Every user can have only one level for a group.
     * 
     * @param array $keys  
     */
    public function load($keys) {
        
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.level_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.value, b.published, b.points_id, b.rank_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userlevels")  . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_levels") . ' AS b ON a.level_id = b.id')
            ->where("a.user_id  = ". (int)$userId);
        
        if(!empty($groupId)) {
            $query->where("a.group_id = ". (int)$groupId);
        }
        
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();
        
        if(!empty($results)) {
            
            $this->userId = $userId;
            
            foreach($results as $result) {
                $level = new GamificationUserLevel();
                $level->bind($result);
                
                $this->levels[$result["group_id"]][$level->level_id] = $level;
            }
            
        } 
        
    }

    /**
     * Return all levels.
     * 
     * @return array
     */
    public function getLevels() {
        return $this->levels;
    }
    
    /**
     * Get a level by group ID. 
     * Users can have only one level in a group.
     * 
     * @param integer $groupId
     * 
     * @return mixed
     */
    public function getLevel($groupId) {
        return (!isset($this->levels[$groupId])) ? null : $this->levels[$groupId];
    }
}


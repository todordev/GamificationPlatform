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
 * This class contains methods that are used for managing user ranks.
 */
class GamificationUserRanks {

    /**
     * Users ID
     * @var integer
     */
    public $userId;
    
    public $ranks = array();
    
    /**
     * Database driver
     * 
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
     * Initialize user ranks
     *
     * @param  array $keys
     * @return mixed NULL or GamificationUserRanks
     */
    public static function getInstance(array $keys)  {
    
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
    
        $index    = md5($userId.":".$groupId);
    
        if (empty(self::$instances[$index])){
            $item = new GamificationUserRanks($keys);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    /**
     * Load all user ranks and set them to group index.
     * Every user can have only one rank for a group.
     * 
     * @param array $userId  User Id
     */
    public function load($keys) {
        
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.rank_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.image, b.published, b.points_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userranks") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_ranks") . ' AS b ON a.rank_id = b.id')
            ->where("a.user_id = ". (int)$userId);
        
        if(!empty($groupId)) {
            $query->where("a.group_id = ". (int)$groupId);
        }
        
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();
        
        if(!empty($results)) {
            
            $this->userId = $userId;
            
            foreach($results as $result) {
                $rank = new GamificationUserRank();
                $rank->bind($result);
                
                $this->ranks[$result["group_id"]][$rank->rank_id] = $rank;
            }
            
        } 
        
    }

    /**
     * Return all ranks
     * 
     * @return array
     */
    public function getRanks() {
        return $this->ranks;
    }
    
    /**
     * Get a rank by group ID.
     * Users can have only one rank in a group.
     * 
     * @param integer $groupId
     * 
     * @return mixed  NULL or GamificationUserRank
     */
    public function getRank($groupId) {
        return (!isset($this->ranks[$groupId])) ? null : $this->ranks[$groupId];
    }
}


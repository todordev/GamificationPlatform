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
 * This class contains methods that are used for managing user badges.
 */
class GamificationUserBadges {

    /**
     * Users ID
     * 
     * @var integer
     */
    public $userId;
    
    public $badges = array();
    
    /**
     * Database driver
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected static $instances = array();
    
    public function __construct($keys = array()) {
        
        $this->db       = JFactory::getDbo();
        if(!empty($keys)) {
            $this->load($keys);
        }
        
    }
    
    /**
     * Initialize user badge
     *
     * @param  array $keys
     * @return multitype:
     */
    public static function getInstance(array $keys)  {
    
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
    
        $index    = md5($userId.":".$groupId);
    
        if (empty(self::$instances[$index])){
            $item = new GamificationUserBadges($keys);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    /**
     * Load all user badges and set them to group index.
     * Every user can have only one badge for a group.
     * 
     * @param array $keys  The keys that will use to load data.
     */
    public function load($keys) {
        
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.id, a.badge_id, a.user_id, a.group_id, a.note")
            ->select("b.title, b.points, b.image, b.published, b.points_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userbadges") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_badges") . ' AS b ON a.badge_id = b.id')
            ->where("a.user_id = ". (int)$userId);
        
        if(!empty($groupId)) {
            $query->where("a.group_id = ". (int)$groupId);
        }
        
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();
        
        if(!empty($results)) {
            
            $this->userId = $userId;
            
            foreach($results as $result) {
                $badge = new GamificationUserBadge();
                $badge->bind($result);
                
                $this->badges[$result["group_id"]][$badge->badge_id] = $badge;
            }
            
        } 
        
    }

    public function getBadges($groupId = null) {
        return (!is_null($groupId)) ? JArrayHelper::getValue($this->badges, $groupId, array()) : $this->badges;
    }
    
    /**
     * Get a badge.
     * 
     * @param integer $badgeId
     * @param integer $groupId
     * 
     * @return mixed    NULL OR GamificationUserBadge
     */
    public function getBadge($badgeId, $groupId = null) {
        
        if(!empty($groupId)) { // Get an item from a specific group
            
            $item = (!isset($this->badges[$groupId])) ? null : JArrayHelper::getValue($this->badges[$groupId], $badgeId);
        
        } else { // Look in all groups
            
            foreach($this->badges as $group) {
                $item = JArrayHelper::getValue($group, $badgeId);
                if(!empty($item->id)) {
                    return $item;
                }
            }
        }
        
        return $item;
    }
    
}


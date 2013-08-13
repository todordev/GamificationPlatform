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

jimport('gamification.interface.usermechanic');

/**
 * This is an object that represents user level.
 */
class GamificationUserLevel implements GamificationInterfaceUserMechanic {

    /**
     * The ID of the record that contains user level data.
     * @var integer
     */
    public $id;
    
    public $title;
    
    /**
     * This is the number of points needed to be reached this level.
     * @var integer
     */
    public $points;
    
    /**
     * This is the value of the level in numerical value.
     * 
     * @var integer
     */
    public $value;
    public $published;
    
    /**
     * This is the ID of the level record in table "#__gfy_levels".
     *
     * @var integer
     */
    public $level_id;
    
    public $group_id;
    public $user_id;
    
    public $points_id;
    public $rank_id;
    
   
    /**
     * User rank if the level is part of a rank.
     * @var Object
     */
    protected $rank;
    
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
     * Initialize user level
     * 
     * @param  array $keys 
     * @return multitype:
     */
    public static function getInstance(array $keys)  {
    
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
        
        $index    = md5($userId.":".$groupId);
        
        if (empty(self::$instances[$index])){
            $item = new GamificationUserLevel($keys);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    /**
     * Load user level data
     *  
     * @param array $keys
     */
    public function load($keys) {
        
        // Get keys
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.id, a.level_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.value, b.published, b.points_id, b.rank_id")
            ->from($this->db->quoteName("#__gfy_userlevels") . ' AS a')
            ->leftJoin($this->db->quoteName("#__gfy_levels") . ' AS b ON a.level_id = b.id')
            ->where("a.user_id  = ". (int)$userId)
            ->where("a.group_id = ". (int)$groupId);
        
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!empty($result)) { // Set values to variables
            $this->bind($result);
        } 
        
    }
    
    public function bind($data) {
        
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
        
    }
    
    protected function updateObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->update("#__gfy_userlevels")
            ->set("level_id = " . (int)$this->level_id)
            ->where("id     = " . (int)$this->id);
            
        $this->db->setQuery($query);
        $this->db->query();
    }
    
    protected function insertObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->insert("#__gfy_userlevels")
            ->set("user_id   = " .(int)$this->user_id)
            ->set("group_id  = " .(int)$this->group_id)
            ->set("level_id  = " .(int)$this->level_id);
            
        $this->db->setQuery($query);
        $this->db->query();
        
        return $this->db->insertid();
        
    }
    
    public function store() {
        
        if(!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }
    
    /**
     * Return the title of the level
     * 
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Return level value
     * @return integer
     */
    public function getLevel() {
        return (int)$this->value;
    }
    
    /**
     * Set the ID of the level.
     * 
     * @param integer $levelId
     */
    public function setLevelId($levelId) {
        $this->level_id = (int)$levelId;
    }
    
    /**
     * Create a record to the database, adding first level.
     *
     * @param array $data
     *
     * </code>
     * $data = array(
     *     "user_id"  => $userId,
     *     "group_id" => $groupId,
     *     "level_id" => $levelId
     * );
     * <code>
     *
     */
    public function startLeveling($data) {
    
        $this->bind($data);
        $this->store();
        
        // Load data
        $keys = array(
            "user_id"  => $data["user_id"],
            "group_id" => $data["group_id"]
        );
        
        $this->load($keys);
    
    }
    
    /**
     * 
     * Get the rank where the level is positioned.
     * 
     * @return mixed NULL or GamificationRank
     */
    public function getRank() {
        
        if(!$this->rank_id) {
            return null;
        }
        
        if(!$this->rank) {
            jimport("gamification.rank");
            $this->rank = GamificationRank::getInstance($this->rank_id);
        }
        
        return $this->rank;
    }
    
}


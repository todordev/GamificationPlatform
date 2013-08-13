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
 * This is an object that represents user rank.
 */
class GamificationUserRank implements GamificationInterfaceUserMechanic {

    /**
     * The ID of database record in table "#__gfy_userranks". 
     * 
     * @var integer
     */
    public $id;
    
    public $title;
    
    /**
     * This is the number of points needed to be reached this rank.
     * @var integer
     */
    public $points;
    
    public $image;
    public $published;
    
    /**
     * This is the ID of the rank record in table "#__gfy_ranks".
     * 
     * @var integer
     */
    public $rank_id;
    
    public $group_id;
    public $user_id;
    
    public $points_id;
    
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
     * Initialize user rank
     * 
     * @param  array $keys 
     * @return multitype:
     */
    public static function getInstance(array $keys)  {
    
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $groupId  = JArrayHelper::getValue($keys, "group_id");
        
        $index    = md5($userId.":".$groupId);
        
        if (empty(self::$instances[$index])){
            $item = new GamificationUserRank($keys);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    /**
     * Load user rank data
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
            ->select("a.id, a.rank_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.image, b.published, b.points_id")
            ->from($this->db->quoteName("#__gfy_userranks") . ' AS a')
            ->leftJoin($this->db->quoteName("#__gfy_ranks") . ' AS b ON a.rank_id = b.id')
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
            ->update("#__gfy_userranks")
            ->set("rank_id  = " . (int)$this->rank_id)
            ->where("id     = " . (int)$this->id);
            
        $this->db->setQuery($query);
        $this->db->query();
    }
    
    protected function insertObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->insert("#__gfy_userranks")
            ->set("user_id   = " .(int)$this->user_id)
            ->set("group_id  = " .(int)$this->group_id)
            ->set("rank_id   = " .(int)$this->rank_id);
            
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
     * Return the title of the rank
     * 
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Return rank image
     * @return string
     */
    public function getImage() {
        return $this->image;
    }
    
    /**
     * Set the ID of the rank.
     *
     * @param integer $rankId
     */
    public function setRankId($rankId) {
        $this->rank_id = (int)$rankId;
    }
    
    /**
     * This method creates a record in the database.
     * It initializes and adds first rank. 
     * Now, the system will be able to update it.
     *
     * @param array $data
     *
     * </code>
     * $data = array(
     *     "user_id"  => $userId,
     *     "group_id" => $groupId,
     *     "rank_id"  => $rankId
     * );
     * <code>
     *
     */
    public function startRanking($data) {
    
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


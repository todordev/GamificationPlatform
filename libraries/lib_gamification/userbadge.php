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
 * This is an object that represents user badge.
 */
class GamificationUserBadge implements GamificationInterfaceUserMechanic {

    /**
     * The ID of database record in table "#__gfy_userbadges". 
     * 
     * @var integer
     */
    public $id;
    
    /**
     * This is the ID of the badge record in table "#__gfy_badges".
     * 
     * @var integer
     */
    public $badge_id;
    
    public $user_id;
    public $group_id;
    
    public $note;
    
    protected $title;
    
    /**
     * This is the number of points needed to be reached this badge.
     * @var integer
     */
    protected $points;
    
    protected $image;
    protected $published;
    protected $points_id;
    
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
            $item = new GamificationUserBadge($keys);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    /**
     * Load user badge data
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
            ->select("a.id, a.badge_id, a.user_id, a.group_id, a.note")
            ->select("b.title, b.points, b.image, b.published, b.points_id")
            ->from($this->db->quoteName("#__gfy_userbadges") . ' AS a')
            ->leftJoin($this->db->quoteName("#__gfy_badges") . ' AS b ON a.badge_id = b.id')
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
            ->update($this->db->quoteName("#__gfy_userbadges"))
            ->set($this->db->quoteName("badge_id") ."=". (int)$this->badge_id);
            
        // If there is a note, store it. In other hand, 
        // the system will set the column to its default value NULL.
        if(!empty($this->note)) {
            $query->set($this->db->quoteName("note") ."=". $this->db->quote($this->note));
        }
        
        $query->where($this->db->quoteName("id") ."=". (int)$this->id);
        
        $this->db->setQuery($query);
        $this->db->query();
    }
    
    protected function insertObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->insert($this->db->quoteName("#__gfy_userbadges"))
            ->set($this->db->quoteName("user_id")  ."=". (int)$this->user_id)
            ->set($this->db->quoteName("group_id") ."=". (int)$this->group_id)
            ->set($this->db->quoteName("badge_id") ."=". (int)$this->badge_id);
        
        // If there is a note, store it. In other hand, 
        // the system will set the column to its default value NULL.
        if(!empty($this->note)) {
            $query->set($this->db->quoteName("note") ."=". $this->db->quote($this->note));
        }
            
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
     * Return the title of the badge.
     * 
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Return badge image.
     * 
     * @return string
     */
    public function getImage() {
        return $this->image;
    }
    
    /**
     * Return the note about the badge.
     *
     * @return string
     */
    public function getNote() {
        return $this->note;
    }
    
    public function setNote($note) {
        $this->note = $note;
    }
    
    public function setUserId($userId) {
        $this->user_id = (int)$userId;
    }
    
}


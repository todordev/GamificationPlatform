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

class GamificationPoints implements GamificationTable {

    /**
     * Users points ID
     * @var integer
     */
    public $id;
    
    public $title;
    public $abbr;
    public $user_id;
    public $points_id;
    public $points  = 0;
    
    /**
     * Driver of the database
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * 
     * Load user points using some indexs - user_id, abbr or points_id.
     * @param array $keys
     */
    public function load($keys) {
        
        if(!is_array($keys))  {
            return;
        }
        
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $pointsId = JArrayHelper::getValue($keys, "points_id");
        $abbr     = JArrayHelper::getValue($keys, "abbr");
        
        if(!empty($pointsId)) {
            $result = $this->loadByPointsId($userId, $pointsId);
        } else if(!empty($abbr)) {
            $result = $this->loadByAbbrId($userId, $abbr);
        } else {
            return;
        }
        
        if(!empty($result)) { // Set values to variables
            $this->bind($result);
        } 
        
    }
    
    /**
     * 
     * Laod user points by userId and pointsId
     * @param integer $userId
     * @param integer $pointsId
     */
    protected function loadByPointsId($userId, $pointsId) {

        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.id AS points_id, a.title, a.abbr")
            ->from($this->db->quoteName("#__gfy_points") . ' AS a')
            ->where("a.id   = ". (int)$pointsId);
        
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        $resultUserPoints = $this->getUserPoints($userId, $pointsId);
        
        if(!empty($resultUserPoints)) {
            $result = array_merge($result, $resultUserPoints);
        } else {
            $result["user_id"] = (int)$userId;
        }
        
        return $result;
    }
    
	/**
     * 
     * Laod user points by user ID and abbreviation
     * @param integer $userId
     * @param string  $abbr
     */
    protected function loadByAbbrId($userId, $abbr) {

        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.id AS points_id, a.title, a.abbr")
            ->from($this->db->quoteName("#__gfy_points") . ' AS a')
            ->where("a.abbr   = ". $this->db->quote($abbr));
        
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        // Get points ID
        $pointsId = JArrayHelper::getValue($result, "points_id");
        $resultUserPoints = $this->getUserPoints($userId, $pointsId);
        
        if(!empty($resultUserPoints)) {
            $result = array_merge($result, $resultUserPoints);
        } else {
            $result["user_id"] = (int)$userId;
        }
        
        return $result;
    }
    
    protected function getUserPoints($userId, $pointsId) {
        
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.id, a.points, a.user_id")
            ->from($this->db->quoteName("#__gfy_userpoints") . ' AS a')
            ->where("a.user_id=" .(int)$userId . " AND a.points_id = ". (int)$pointsId);
            
        $this->db->setQuery($query);
        return $this->db->loadAssoc();
        
    }
    
    public function bind($data) {
        
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
        
    }
    
    public function increase($points) {
        $this->points += abs($points);
    }
    
    protected function updateObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->update("#__gfy_userpoints")
            ->set("points = " . (int)$this->points)
            ->where("id   = " .(int)$this->id);
            
        $this->db->setQuery($query);
        $this->db->query();
    }
    
    protected function insertObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->insert("#__gfy_userpoints")
            ->set("points    = " .(int)$this->points)
            ->set("user_id   = " .(int)$this->user_id)
            ->set("points_id = " .(int)$this->points_id);
            
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
}


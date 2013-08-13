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
 * This class contains methods that are used for managing leaderboard.
 */
class GamificationActivities implements Iterator, Countable, ArrayAccess {

    public $activities = array();
    
    /**
     * Database driver
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected $position = 0;
    
    public function __construct($options = array(), $userId = 0) {
        
        $this->db       = JFactory::getDbo();
        
        if(!empty($userId)) {
            $this->load($options, $userId);
        }
        
    }
    
    /**
     * Load all user activities.
     * 
     * @param array $userId  The user ID that will use to load data.
     */
    public function load($options = array(), $userId = 0) {
        
        $sortDir  = JArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir  = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";
        
        $limit    = JArrayHelper::getValue($options, "limit", 10, "int");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select(
                "a.info, a.image, a.url, a.created, a.user_id, " .
                "b.name")
            ->from($this->db->quoteName("#__gfy_activities") . ' AS a')
            ->innerJoin($this->db->quoteName("#__users") . ' AS b ON a.user_id = b.id');
        
        if(!empty($userId)) {
            $query->where("a.user_id = ". (int)$userId);
        }
        
        $query->order("a.created ". $sortDir);
        
        $this->db->setQuery($query, 0, $limit);
        $results = $this->db->loadObjectList();
        
        if(!empty($results)) {
            $this->activities = $results;
        } 
        
    }
    
    public function rewind() {
        $this->position = 0;
    }
    
    public function current() {
        return (!isset($this->activities[$this->position])) ? null : $this->activities[$this->position];
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        ++$this->position;
    }
    
    public function valid() {
        return isset($this->activities[$this->position]);
    }
    
    public function count() {
        return (int)count($this->activities);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->activities[] = $value;
        } else {
            $this->activities[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->activities[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->activities[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->activities[$offset]) ? $this->activities[$offset] : null;
    }
    
}


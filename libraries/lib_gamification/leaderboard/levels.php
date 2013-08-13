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
 * This class contains methods that are used for managing leaderboard.
 */
class GamificationLeaderboardLevels implements GamificationInterfaceLeaderboard, Iterator, Countable, ArrayAccess {

    public $units = array();
    
    /**
     * Database driver
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected $position = 0;
    
    public function __construct($keys = array(), $options = array()) {
        
        $this->db       = JFactory::getDbo();
        if(!empty($keys)) {
            $this->load($keys, $options);
        }
        
    }
    
    /**
     * Load the data that will be displayed on the leaderboard.
     * 
     * @param array $keys  The keys that will use to load data.
     */
    public function load($keys, $options = array()) {
        
        $groupId  = JArrayHelper::getValue($keys, "group_id");
        $sortDir  = JArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir  = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";
        
        $limit    = JArrayHelper::getValue($options, "limit", 10, "int");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select(
                "a.user_id, a.level_id, a.group_id, " .
                "b.title, b.value, " . 
                "c.name, " .
                "d.title AS rank")
            ->from($this->db->quoteName("#__gfy_userlevels") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_levels") . ' AS b ON a.level_id = b.id')
            ->innerJoin($this->db->quoteName("#__users") . ' AS c ON a.user_id = c.id')
            ->leftJoin($this->db->quoteName("#__gfy_ranks") . ' AS d ON b.rank_id = d.id')
            ->where("a.group_id = ". (int)$groupId)
            ->order("b.points ". $sortDir);
        
        $this->db->setQuery($query, 0, $limit);
        $results = $this->db->loadObjectList();
        
        if(!empty($results)) {
            $this->units = $results;
        } 
        
    }
    
    public function rewind() {
        $this->position = 0;
    }
    
    public function current() {
        return (!isset($this->units[$this->position])) ? null : $this->units[$this->position];
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        ++$this->position;
    }
    
    public function valid() {
        return isset($this->units[$this->position]);
    }
    
    public function count() {
        return (int)count($this->units);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->units[] = $value;
        } else {
            $this->units[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->units[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->units[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->units[$offset]) ? $this->units[$offset] : null;
    }
    
}


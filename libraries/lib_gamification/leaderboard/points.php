<?php
/**
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('gamification.interface.usermechanic');

/**
 * This class contains methods that are used for managing leaderboard data.
 * The data is based on the game mechanic points.
 * 
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 */
class GamificationLeaderboardPoints implements GamificationInterfaceLeaderboard, Iterator, Countable, ArrayAccess {

    protected $units = array();
    
    /**
     * Database driver
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected $position = 0;
    
    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * $keys = array(
     * 	   "points_id" => 2
     * );
     *
     * $options = array(
     *      "sort_direction" => "DESC",
     *      "limit"          => 10
     * );
     *
     * $leaderboard = new GamificationLeaderboardPoints($keys, $options);
     *
     * </code>
     *
     * @param array $keys
     * @param array $options
     */
    public function __construct($keys = array(), $options = array()) {
        
        $this->db       = JFactory::getDbo();
        if(!empty($keys)) {
            $this->load($keys, $options);
        }
        
    }
    
    /**
     * Load the data that will be displayed on the leaderboard.
     * 
     * <code>
     *
     * $keys = array(
     * 	   "points_id" => 2
     * );
     * 
     * $options = array(
     *      "sort_direction" => "DESC",
     *      "limit"          => 10
     * );
     * 
     * $leaderboard = new GamificationLeaderboardPoints();
     * $leaderboard->load($keys, $options);
     *
     * </code>
     *
     * @param array $keys
     * @param array $options
     */
    public function load($keys, $options = array()) {
        
        $pointsId = JArrayHelper::getValue($keys, "points_id");
        $sortDir  = JArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir  = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";
        
        $limit    = JArrayHelper::getValue($options, "limit", 10, "int");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select(
                "a.points, a.user_id, " .
                "b.title, b.abbr, " . 
                "c.name ")
            ->from($this->db->quoteName("#__gfy_userpoints") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_points") . ' AS b ON a.points_id = b.id')
            ->innerJoin($this->db->quoteName("#__users") . ' AS c ON a.user_id = c.id')
            ->where("a.points_id = ". (int)$pointsId)
            ->order("a.points ". $sortDir);
        
        $this->db->setQuery($query, 0, $limit);
        $results = $this->db->loadObjectList();
        
        if(!empty($results)) {
            $this->units = $results;
        } 
        
    }
    
    /**
     * Rewind the Iterator to the first element.
     * 
     * @see Iterator::rewind()
     */
    public function rewind() {
        $this->position = 0;
    }
    
    /**
     * Return the current element.
     * 
     * @see Iterator::current()
     */
    public function current() {
        return (!isset($this->units[$this->position])) ? null : $this->units[$this->position];
    }
    
    /**
     * Return the key of the current element.
     * 
     * @see Iterator::key()
     */
    public function key() {
        return $this->position;
    }
    
    /**
     * Move forward to next element.
     * 
     * @see Iterator::next()
     */
    public function next() {
        ++$this->position;
    }
    
    /**
     * Checks if current position is valid.
     * 
     * @see Iterator::valid()
     */
    public function valid() {
        return isset($this->units[$this->position]);
    }
    
    /**
     * Count elements of an object.
     * 
     * @see Countable::count()
     */
    public function count() {
        return (int)count($this->units);
    }

    /**
     * Offset to set.
     * 
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->units[] = $value;
        } else {
            $this->units[$offset] = $value;
        }
    }
    
    /**
     * Whether a offset exists.
     * 
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return isset($this->units[$offset]);
    }
    
    /**
     * Offset to unset.
     * 
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        unset($this->units[$offset]);
    }
    
    /**
     * Offset to retrieve.
     * 
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        return isset($this->units[$offset]) ? $this->units[$offset] : null;
    }
    
}


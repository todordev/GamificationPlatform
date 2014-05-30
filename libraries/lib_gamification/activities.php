<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing activities.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationActivities implements Iterator, Countable, ArrayAccess
{
    protected $userId;
    protected $activities = array();

    protected $db;

    protected $position = 0;

    /**
     * Initialize the object and load user activities.
     *
     * <code>
     *
     * $options = array(
     *      "user_id"        => 1,
     *      "limit"         => 10,
     *      "sort_direction" => "DESC"
     * );
     *
     * $activities = new GamificationActivities($options);
     *
     * </code>
     *
     * @param array $options Options that will be used for filtering results.
     */
    public function __construct($options = array())
    {
        $this->db     = JFactory::getDbo();
        $this->userId = JArrayHelper::getValue($options, "user_id", 0, "integer");

        if (!empty($this->userId)) {
            $this->load($options);
        }
    }

    /**
     * Load all user activities.
     *
     * <code>
     *
     * $options = array(
     *        "limit"         => 10,
     *        "sort_direction" => "DESC"
     * );
     *
     * $activities = new GamificationActivities();
     * $activities->load($options);
     *
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     */
    public function load($options = array())
    {
        $userId = JArrayHelper::getValue($options, "user_id", 0, "integer");

        $sortDir = JArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";

        $limit = JArrayHelper::getValue($options, "limit", 10, "int");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.info, a.image, a.url, a.created, a.user_id, " .
                "b.name"
            )
            ->from($this->db->quoteName("#__gfy_activities", "a"))
            ->innerJoin($this->db->quoteName("#__users", "b") . ' ON a.user_id = b.id');

        if (!empty($userId)) {
            $this->userId = $userId;
            $query->where("a.user_id = " . (int)$this->userId);
        }

        $query->order("a.created " . $sortDir);

        $this->db->setQuery($query, 0, $limit);
        $results = $this->db->loadObjectList();

        if (!empty($results)) {
            $this->activities = $results;
        }

    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Return the current element.
     *
     * @see Iterator::current()
     */
    public function current()
    {
        return (!isset($this->activities[$this->position])) ? null : $this->activities[$this->position];
    }

    /**
     * Return the key of the current element.
     *
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Move forward to next element.
     *
     * @see Iterator::next()
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Checks if current position is valid.
     *
     * @see Iterator::valid()
     */
    public function valid()
    {
        return isset($this->activities[$this->position]);
    }

    /**
     * Count elements of an object.
     *
     * @see Countable::count()
     */
    public function count()
    {
        return (int)count($this->activities);
    }

    /**
     * Offset to set.
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->activities[] = $value;
        } else {
            $this->activities[$offset] = $value;
        }
    }

    /**
     * Whether a offset exists.
     *
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return isset($this->activities[$offset]);
    }

    /**
     * Offset to unset.
     *
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->activities[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return isset($this->activities[$offset]) ? $this->activities[$offset] : null;
    }
}

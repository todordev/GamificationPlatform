<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('gamification.interface.usermechanic');

/**
 * This class contains methods that are used for managing user points.
 * The user points are collected units by users.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 *
 * @todo            Improve loading data
 */
class GamificationUserPoints implements GamificationInterfaceUserMechanic
{
    /**
     * Users points ID
     * @var integer
     */
    public $id;

    public $title;
    public $abbr;
    public $group_id;
    public $user_id;
    public $points_id;
    public $points = 0;

    /**
     * Database driver.
     *
     * @var JDatabaseMySQLi
     */
    protected $db;

    protected static $instances = array();

    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = new GamificationUserPoints($keys);
     *
     * </code>
     *
     * @param array $keys
     */
    public function __construct($keys = array())
    {
        $this->db = JFactory::getDbo();
        if (!empty($keys)) {
            $this->load($keys);
        }
    }

    /**
     * Create an object and load user level.
     *
     * <code>
     *
     * // Create and initialize the object using points ID.
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     *
     * // Create and initialize the object using abbreviation.
     * $keys = array(
     *       "user_id"  => 1,
     *       "abbr"     => "P"
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     *
     * </code>
     *
     * @param  array $keys
     *
     * @return null|GamificationUserPoints
     */
    public static function getInstance(array $keys)
    {
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $pointsId = JArrayHelper::getValue($keys, "points_id");
        $abbr     = JArrayHelper::getValue($keys, "abbr");

        $index = null;

        if (!empty($pointsId)) {
            $index = md5($userId . ":" . $pointsId);
        } elseif (!empty($abbr)) {
            $index = md5($userId . ":" . $abbr);
        }

        if (!is_null($index) and empty(self::$instances[$index])) {
            $item                    = new GamificationUserPoints($keys);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load user points using some indexes - user_id, abbr or points_id.
     *
     * <code>
     *
     * // Load data by points ID.
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints    = new GamificationUserPoints();
     * $userPoints->load($keys);
     *
     * // Load data by abbreviation.
     * $keys = array(
     *       "user_id"  => 1,
     *       "abbr"     => "P"
     * );
     *
     * $userPoints    = new GamificationUserPoints();
     * $userPoints->load($keys);
     *
     * </code>
     *
     * @param array $keys
     *
     */
    public function load($keys)
    {
        $userId   = JArrayHelper::getValue($keys, "user_id");
        $pointsId = JArrayHelper::getValue($keys, "points_id");
        $abbr     = JArrayHelper::getValue($keys, "abbr");

        if (!empty($pointsId)) {
            $result = $this->loadByPointsId($userId, $pointsId);
        } elseif (!empty($abbr)) {
            $result = $this->loadByAbbrId($userId, $abbr);
        }

        if (!empty($result)) { // Set values to variables
            $this->bind($result);
        }

    }

    /**
     * Load user points by userId and pointsId.
     *
     * @param integer $userId
     * @param integer $pointsId
     *
     * @return array
     */
    protected function loadByPointsId($userId, $pointsId)
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id AS points_id, a.title, a.abbr, a.group_id")
            ->from($this->db->quoteName("#__gfy_points") . ' AS a')
            ->where("a.id = " . (int)$pointsId);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        $resultUserPoints = $this->getUserPoints($userId, $pointsId);

        if (!empty($resultUserPoints)) {
            $result = array_merge($result, $resultUserPoints);
        } else {
            $result["user_id"] = (int)$userId;
        }

        return $result;
    }

    /**
     * Load user points by user ID and abbreviation.
     *
     * @param integer $userId
     * @param string  $abbr
     *
     * @return array
     */
    protected function loadByAbbrId($userId, $abbr)
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id AS points_id, a.title, a.abbr")
            ->from($this->db->quoteName("#__gfy_points") . ' AS a')
            ->where("a.abbr   = " . $this->db->quote($abbr));

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        // Get points ID
        $pointsId         = JArrayHelper::getValue($result, "points_id");
        $resultUserPoints = $this->getUserPoints($userId, $pointsId);

        if (!empty($resultUserPoints)) {
            $result = array_merge($result, $resultUserPoints);
        } else {
            $result["user_id"] = (int)$userId;
        }

        return $result;
    }

    protected function getUserPoints($userId, $pointsId)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.points, a.user_id")
            ->from($this->db->quoteName("#__gfy_userpoints") . ' AS a')
            ->where("a.user_id=" . (int)$userId . " AND a.points_id = " . (int)$pointsId);

        $this->db->setQuery($query);

        return $this->db->loadAssoc();
    }

    /**
     * Set the data to the object parameters.
     *
     * <code>
     *
     * $data = array(
     *        "user_id"   => 2,
     *        "points_id" => 3,
     *        "points"    => 200
     * );
     *
     * $userPoints   = new GamificationUserPoints();
     * $userPoints->bind($data);
     *
     * </code>
     *
     * @param array $data
     */
    public function bind($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Increase user points.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = GamificationUserPoints::getInstance($keys);
     * $userPoints->increase(100);
     * $userPoints->store();
     *
     * </code>
     *
     */
    public function increase($points)
    {
        $this->points += abs($points);
    }

    /**
     * Decrease user points.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = GamificationUserPoints::getInstance($keys);
     * $userPoints->decrease(100);
     * $userPoints->store();
     *
     * </code>
     *
     */
    public function decrease($points)
    {
        $this->points -= abs($points);
    }

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update("#__gfy_userpoints")
            ->set("points = " . (int)$this->points)
            ->where("id   = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert("#__gfy_userpoints")
            ->set("points    = " . (int)$this->points)
            ->set("user_id   = " . (int)$this->user_id)
            ->set("points_id = " . (int)$this->points_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Decrease user points.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = GamificationUserPoints::getInstance($keys);
     * $userPoints->decrease(100);
     *
     * </code>
     *
     */
    public function store()
    {
        if (!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }

    /**
     * Return the number of points and abbreviation as a string.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = GamificationUserPoints::getInstance($keys);
     * $amount      = $userPoints->getPointsString();
     *
     * </code>
     *
     * @return string
     */
    public function getPointsString()
    {
        return $this->points . " " . $this->abbr;
    }

    /**
     * Return the number of points.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = GamificationUserPoints::getInstance($keys);
     * $points      = $userPoints->getPoints();
     *
     * </code>
     *
     * @return integer
     */
    public function getPoints()
    {
        return (int)$this->points;
    }

    /**
     * Return abbreviation.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = GamificationUserPoints::getInstance($keys);
     * $abbreviation = $userPoints->getAbbr();
     *
     * </code>
     *
     * @return string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * Return title.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = GamificationUserPoints::getInstance($keys);
     * $title       = $userPoints->getTitle();
     *
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}

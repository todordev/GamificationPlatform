<?php
/**
 * @package         Gamification\User
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\TableObservable;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user points.
 * The user points are collected units by users.
 *
 * @package         Gamification\User
 * @subpackage      Points
 */
class Points extends TableObservable implements \JObservableInterface
{
    /**
     * Users points ID.
     * 
     * @var int
     */
    protected $id;

    protected $title;
    protected $abbr;
    protected $group_id;
    protected $user_id;
    protected $points_id;
    protected $points = 0;

    protected static $instances = array();

    /**
     * Create an object and load user level.
     *
     * <code>
     * // Create and initialize the object using points ID.
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create and initialize the object using abbreviation.
     * $keys = array(
     *       "user_id"  => 1,
     *       "abbr"     => "P"
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     * </code>
     *
     * @param  \JDatabaseDriver $db
     * @param  array $keys
     * @param  array $options
     *
     * @return null|self
     */
    public static function getInstance(\JDatabaseDriver $db, array $keys, array $options = array())
    {
        $userId   = ArrayHelper::getValue($keys, "user_id");
        $pointsId = ArrayHelper::getValue($keys, "points_id");
        $abbr     = ArrayHelper::getValue($keys, "abbr");

        $index = null;

        if (!empty($pointsId)) {
            $index = md5($userId . ":" . $pointsId);
        } elseif (!empty($abbr)) {
            $index = md5($userId . ":" . $abbr);
        }

        if (!isset(self::$instances[$index])) {
            $item = new Points($db);
            $item->load($keys, $options);

            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load user points using some indexes - user_id, abbr or points_id.
     *
     * <code>
     * // Load data by points ID.
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints    = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * // Load data by abbreviation.
     * $keys = array(
     *       "user_id"  => 1,
     *       "abbr"     => "P"
     * );
     *
     * $userPoints    = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * </code>
     *
     * @param array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.id, a.points, a.points_id, a.user_id," .
                "b.title, b.abbr, b.group_id"
            )
            ->from($this->db->quoteName("#__gfy_userpoints", "a"))
            ->rightJoin($this->db->quoteName("#__gfy_points", "b") . " ON a.points_id = b.id");

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName("a.".$column) . " = " . $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Increase user points and store the data in the database.
     *
     * <code>
     * $options = array(
     *    "context" = "com_user.registration"
     * );
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $userPoints->increase(100, $options);
     * $userPoints->store();
     * </code>
     * 
     * @param int $value Number of points.
     * @param array $options Options that provides additional data or settings. Those options will be forwarded to observer objects.
     */
    public function increase($value, $options = array())
    {
        // Implement JObservableInterface: Pre-processing by observers
        $this->observers->update('onBeforePointsIncrease', array(&$this, &$options));

        $this->points += abs($value);
        $this->store();

        // Implement JObservableInterface: Post-processing by observers
        $this->observers->update('onAfterPointsIncrease', array(&$this, &$options));
    }

    /**
     * Decrease user points and store the data in the database.
     *
     * <code>
     * $options = array(
     *    "context" = "com_user.registration"
     * );
     *
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * 
     * $userPoints->decrease(100);
     * $userPoints->store();
     * </code>
     * 
     * @param int $value The number of points.
     * @param array $options Options that provides additional data or settings. Those options will be forwarded to observer objects.
     */
    public function decrease($value, $options = array())
    {
        // Implement JObservableInterface: Pre-processing by observers
        $this->observers->update('onBeforePointsDecrease', array(&$this, &$options));

        $this->points -= abs($value);
        $this->store();

        // Implement JObservableInterface: Post-processing by observers
        $this->observers->update('onAfterPointsDecrease', array(&$this, &$options));
    }

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_userpoints"))
            ->set($this->db->quoteName("points") ." = " . (int)$this->points)
            ->where($this->db->quoteName("id") ." = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__gfy_userpoints"))
            ->set($this->db->quoteName("points") . " = " . (int)$this->points)
            ->set($this->db->quoteName("user_id") . " = " . (int)$this->user_id)
            ->set($this->db->quoteName("points_id") ." = " . (int)$this->points_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Decrease user points.
     *
     * <code>
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * 
     * $userPoints->decrease(100);
     * $userPoints->store();
     * </code>
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
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * 
     * $amount = $userPoints->__toString();
     *
     * // Alternatively
     * echo $userPoints;
     * </code>
     *
     * @return string
     */
    public function __toString()
    {
        return $this->points . " " . $this->abbr;
    }

    /**
     * Return ID of a record.
     *
     * <code>
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * if (!$userPoints->getId()) {
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Return the number of points.
     *
     * <code>
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * 
     * $points      = $userPoints->getPoints();
     * </code>
     *
     * @return int
     */
    public function getPoints()
    {
        return (int)$this->points;
    }

    /**
     * Return abbreviation.
     *
     * <code>
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints   = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * 
     * $abbreviation = $userPoints->getAbbr();
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
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * 
     * $title       = $userPoints->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $userId = $userPoints->getUserId();
     * </code>
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Return group ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $groupId = $userPoints->getGroupId();
     * </code>
     *
     * @return string
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Return points ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $pointsId = $userPoints->getPointsId();
     * </code>
     *
     * @return string
     */
    public function getPointsId()
    {
        return $this->points_id;
    }

    /**
     * Create a record to the database, adding first level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $data = array(
     *     "user_id"  => 1,
     *     "group_id" => 2,
     *     "level_id" => 3
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $userLevel->startLeveling($data);
     * </code>
     *
     * @param array $data
     */
    public function startCollectingPoints(array $data = array())
    {
        if (empty($data["user_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID"));
        }

        if (empty($data["points_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_POINTS_ID"));
        }

        $this->bind($data);
        $this->store();

        // Load data
        $keys = array(
            "user_id"   => $data["user_id"],
            "points_id" => $data["points_id"]
        );

        $this->load($keys);
    }
}

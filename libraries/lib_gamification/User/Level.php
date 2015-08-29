<?php
/**
 * @package         Gamification\User
 * @subpackage      Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\TableObservable;
use Gamification\Mechanic\PointsInterface;
use Gamification\Rank\Rank;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user level.
 *
 * @package         Gamification\User
 * @subpackage      Levels
 */
class Level extends TableObservable implements PointsInterface
{
    /**
     * The ID of the record that contains user level data.
     * @var integer
     */
    protected $id;

    protected $title;

    /**
     * This is the number of points needed to be reached this level.
     * @var integer
     */
    protected $points;

    /**
     * This is the value of the level in numerical value.
     *
     * @var integer
     */
    protected $value;
    protected $published;

    /**
     * This is the ID of the level record in table "#__gfy_levels".
     *
     * @var integer
     */
    protected $level_id;

    protected $group_id;
    protected $user_id;

    protected $points_id;
    protected $rank_id;

    /**
     * User rank if the level is part of a rank.
     *
     * @var object
     */
    protected $rank;

    protected static $instances = array();

    /**
     * Create an object and load user level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userLevel    = Gamification\User\Level::getInstance($keys);
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
        $userId  = ArrayHelper::getValue($keys, "user_id");
        $groupId = ArrayHelper::getValue($keys, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (!isset(self::$instances[$index])) {
            $item   = new Level($db, $options);
            $item->load($keys);

            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load user level data.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel     = new Gamification\User\Level(JFactory::getDbo());
     *
     * $userLevel->load($keys);
     * </code>
     *
     * @param array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.level_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.value, b.published, b.points_id, b.rank_id")
            ->from($this->db->quoteName("#__gfy_userlevels", "a"))
            ->leftJoin($this->db->quoteName("#__gfy_levels", "b") . ' ON a.level_id = b.id');

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

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_userlevels"))
            ->set($this->db->quoteName("level_id") ." = " . (int)$this->level_id)
            ->where($this->db->quoteName("id") ." = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__gfy_userlevels"))
            ->set($this->db->quoteName("user_id")  ." = " . (int)$this->user_id)
            ->set($this->db->quoteName("group_id") ." = " . (int)$this->group_id)
            ->set($this->db->quoteName("level_id") ." = " . (int)$this->level_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        "user_id"   => 2,
     *        "group_id"  => 3,
     *        "level_id"  => 4
     * );
     *
     * $userLevel   = new GamificationUserLevel($keys);
     * $userLevel->load($keys);
     *
     * $userLevel->bind($data);
     * $userLevel->store();
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
     * Return the ID of the level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * if (!$userLevel->getId()) {
     * ...
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
     * Return the title of the level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $title       = $userLevel->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return number of points.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $points      = $userLevel->getPoints();
     * </code>
     *
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Get the points ID used of the level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $level      = new Gamification\Level(\JFactory::getDbo());
     * $level->load($keysId);
     *
     * $pointsId   = $level->getPointsId();
     * </code>
     *
     * @return integer
     */
    public function getPointsId()
    {
        return $this->points_id;
    }

    /**
     * Return the numerical value of level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $value       = $userLevel->getLevel();
     * </code>
     *
     * @return string
     */
    public function getLevel()
    {
        return (int)$this->value;
    }

    /**
     * Return rank ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $rankId = $userLevel->getRankId();
     * </code>
     *
     * @return int
     */
    public function getRankId()
    {
        return (int)$this->rank_id;
    }

    /**
     * Return rank ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $levelId = $userLevel->getLevelId();
     * </code>
     *
     * @return int
     */
    public function getLevelId()
    {
        return (int)$this->level_id;
    }

    /**
     * Set the ID of the level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $levelId     = 1;
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $userLevel->setLevelId($levelId);
     * </code>
     *
     * @param integer $levelId
     */
    public function setLevelId($levelId)
    {
        $this->level_id = (int)$levelId;
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
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * if (!$userLevel->getId()) {
     *
     *      $data = array(
     *          "user_id"  => 1,
     *          "group_id" => 2,
     *          "level_id" => 3
     *      );
     *
     *      $userLevel->startLeveling($data);
     * }
     * </code>
     *
     * @param array $data
     */
    public function startLeveling(array $data = array())
    {
        if (empty($data["user_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID"));
        }

        if (empty($data["group_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_GROUP_ID"));
        }

        if (empty($data["level_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_LEVEL_ID"));
        }

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
     * Get the rank where the level is positioned.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $rank        = $userLevel->getRank();
     * </code>
     *
     * @return null|Rank
     */
    public function getRank()
    {
        if (!$this->rank_id) {
            return null;
        }

        if (!$this->rank) {
            $this->rank = Rank::getInstance(\JFactory::getDbo(), $this->rank_id);
        }

        return $this->rank;
    }
}

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
 * This is an object that represents user level.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationUserLevel implements GamificationInterfaceUserMechanic
{
    /**
     * The ID of the record that contains user level data.
     * @var integer
     */
    public $id;

    public $title;

    /**
     * This is the number of points needed to be reached this level.
     * @var integer
     */
    public $points;

    /**
     * This is the value of the level in numerical value.
     *
     * @var integer
     */
    public $value;
    public $published;

    /**
     * This is the ID of the level record in table "#__gfy_levels".
     *
     * @var integer
     */
    public $level_id;

    public $group_id;
    public $user_id;

    public $points_id;
    public $rank_id;

    /**
     * User rank if the level is part of a rank.
     *
     * @var object
     */
    protected $rank;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    protected static $instances = array();

    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userLevel    = new GamificationUserLevel($keys);
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
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userLevel    = GamificationUserLevel::getInstance($keys);
     *
     * </code>
     *
     * @param  array $keys
     *
     * @return null|GamificationUserLevel
     */
    public static function getInstance(array $keys)
    {
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (empty(self::$instances[$index])) {
            $item                    = new GamificationUserLevel($keys);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load user level data.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel     = new GamificationUserLevel();
     * $userLevel->load($keys);
     *
     * </code>
     *
     * @param $keys
     *
     */
    public function load($keys)
    {
        // Get keys
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.level_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.value, b.published, b.points_id, b.rank_id")
            ->from($this->db->quoteName("#__gfy_userlevels") . ' AS a')
            ->leftJoin($this->db->quoteName("#__gfy_levels") . ' AS b ON a.level_id = b.id')
            ->where("a.user_id  = " . (int)$userId)
            ->where("a.group_id = " . (int)$groupId);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) { // Set values to variables
            $this->bind($result);
        }
    }

    /**
     * Set the data to the object parameters.
     *
     * <code>
     *
     * $data = array(
     *        "user_id"   => 2,
     *        "group_id"  => 3,
     *        "level_id"  => 4
     * );
     *
     * $userLevel   = new GamificationUserLevel();
     * $userLevel->bind($data);
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

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update("#__gfy_userlevels")
            ->set("level_id = " . (int)$this->level_id)
            ->where("id     = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert("#__gfy_userlevels")
            ->set("user_id   = " . (int)$this->user_id)
            ->set("group_id  = " . (int)$this->group_id)
            ->set("level_id  = " . (int)$this->level_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Save the data to the database.
     *
     * <code>
     *
     * $data = array(
     *        "user_id"   => 2,
     *        "group_id"  => 3,
     *        "level_id"  => 4
     * );
     *
     * $userLevel   = new GamificationUserLevel($keys);
     * $userLevel->bind($data);
     * $userLevel->store();
     *
     * </code>
     *
     * @todo do it to update null values
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
     * Return the title of the level.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = GamificationUserLevel::getInstance($keys);
     * $title        = $userLevel->getTitle();
     *
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return the numerical value of level.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = GamificationUserLevel::getInstance($keys);
     * $value        = $userLevel->getLevel();
     *
     * </code>
     *
     * @return string
     */
    public function getLevel()
    {
        return (int)$this->value;
    }

    /**
     * Set the ID of the level.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $levelId     = 1;
     *
     * $userLevel   = GamificationUserLevel::getInstance($keys);
     * $userLevel->setLevelId($levelId);
     *
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
     *
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
     * $userLevel   = GamificationUserLevel::getInstance($keys);
     * $userLevel->startLeveling($data);
     *
     * </code>
     *
     * @param array $data
     */
    public function startLeveling($data)
    {
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
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevel   = GamificationUserLevel::getInstance($keys);
     * $rank        = $userLevel->getRank();
     *
     * </code>
     *
     * @return null|GamificationRank
     */
    public function getRank()
    {
        if (!$this->rank_id) {
            return null;
        }

        if (!$this->rank) {
            jimport("gamification.rank");
            $this->rank = GamificationRank::getInstance($this->rank_id);
        }

        return $this->rank;
    }
}

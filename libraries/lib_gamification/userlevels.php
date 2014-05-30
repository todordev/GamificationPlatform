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
 * This class contains methods that are used for managing user levels.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationUserLevels
{
    /**
     * Users ID
     * @var integer
     */
    public $userId;

    public $levels = array();

    /**
     * Database driver
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
     * $userLevels    = new GamificationUserLevels($keys);
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
     * Create an object and load user levels.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userLevels    = GamificationUserLevels::getInstance($keys);
     *
     * </code>
     *
     * @param  array $keys
     *
     * @return null|GamificationUserLevels
     */
    public static function getInstance(array $keys)
    {
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (empty(self::$instances[$index])) {
            $item                    = new GamificationUserLevels($keys);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load all user levels and set them to group index.
     * Every user can have only one level for a group.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevels     = new GamificationUserLevels();
     * $userLevels->load($keys);
     *
     * </code>
     *
     * @param $keys
     */
    public function load($keys)
    {
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.level_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.value, b.published, b.points_id, b.rank_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userlevels") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_levels") . ' AS b ON a.level_id = b.id')
            ->where("a.user_id  = " . (int)$userId);

        if (!empty($groupId)) {
            $query->where("a.group_id = " . (int)$groupId);
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!empty($results)) {

            $this->userId = $userId;

            foreach ($results as $result) {
                $level = new GamificationUserLevel();
                $level->bind($result);

                $this->levels[$result["group_id"]][$level->level_id] = $level;
            }
        }
    }

    /**
     * Return all levels.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevels  = GamificationUserLevels::getInstance($keys);
     * $levels      = $userLevels->getLevels();
     *
     * </code>
     *
     * @return array
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Get a level by group ID.
     * Users can have only one level in a group.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $groupId     = 1;
     *
     * $userLevels  = GamificationUserLevels::getInstance($keys);
     * $level       = $userLevels->getLevel($groupId);
     *
     * </code>
     *
     * @param integer $groupId
     *
     * @return mixed
     */
    public function getLevel($groupId)
    {
        return (!isset($this->levels[$groupId])) ? null : $this->levels[$groupId];
    }
}

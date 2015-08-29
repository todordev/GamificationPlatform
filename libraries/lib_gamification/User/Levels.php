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
use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user levels.
 *
 * @package         Gamification\User
 * @subpackage      Levels
 */
class Levels extends ArrayObject
{
    /**
     * User ID.
     *
     * @var int
     */
    protected $userId;

    /**
     * Group ID.
     *
     * @var int
     */
    protected $groupId;

    protected static $instances = array();

    /**
     * Create an object and load user levels.
     *
     * <code>
     * $options = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userLevels    = Gamification\User\Levels::getInstance(\JFactory::getDbo(), $options);
     * </code>
     *
     * @param  \JDatabaseDriver $db
     * @param  array $options
     *
     * @return null|self
     */
    public static function getInstance(\JDatabaseDriver $db, array $options)
    {
        $userId  = ArrayHelper::getValue($options, "user_id");
        $groupId = ArrayHelper::getValue($options, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (!isset(self::$instances[$index])) {
            $item   = new Levels($db);
            $item->load($options);

            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load all user levels and set them to group index.
     * Every user can have only one level for a group.
     *
     * <code>
     * $options = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevels     = new Gamification\User\Levels(\JFactory::getDbo());
     * $userLevels->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $userId  = ArrayHelper::getValue($options, "user_id");
        $groupId = ArrayHelper::getValue($options, "group_id");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.level_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.value, b.published, b.points_id, b.rank_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userlevels", "a"))
            ->innerJoin($this->db->quoteName("#__gfy_levels", "b") . ' ON a.level_id = b.id')
            ->where("a.user_id  = " . (int)$userId);

        if (!empty($groupId)) {
            $query->where("a.group_id = " . (int)$groupId);
        }

        $this->db->setQuery($query);
        $results = (array)$this->db->loadAssocList();

        if (!empty($results)) {

            $this->userId = $userId;

            if (!empty($groupId)) {
                $this->groupId = $groupId;
            }

            foreach ($results as $result) {
                $level = new Level(\JFactory::getDbo());
                $level->bind($result);

                $this->items[$result["group_id"]][$level->getLevelId()] = $level;
            }
        }
    }

    /**
     * Return all levels.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userLevels  = new Gamification\User\Levels(\JFactory::getDbo());
     * $userLevels->load($keys);
     *
     * $levels      = $userLevels->getLevels();
     * </code>
     *
     * @return array
     */
    public function getLevels()
    {
        return $this->items;
    }

    /**
     * Get a level by group ID.
     * Users can have only one level in a group.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $groupId     = 1;
     *
     * $userLevels  = new Gamification\User\Levels(\JFactory::getDbo());
     * $userLevels->load($keys);
     *
     * $level       = $userLevels->getLevel($groupId);
     * </code>
     *
     * @param int $groupId
     *
     * @return mixed
     */
    public function getLevel($groupId)
    {
        return (!isset($this->items[$groupId])) ? null : $this->items[$groupId];
    }
}

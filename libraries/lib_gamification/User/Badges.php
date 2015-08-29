<?php
/**
 * @package         Gamification\User
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user badges.
 *
 * @package         Gamification\User
 * @subpackage      Badges
 */
class Badges extends ArrayObject
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
     * Create an object and load user badges.
     *
     * <code>
     * $options = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userBadges    = GamificationUserBadges::getInstance(\JFactory::getDbo(), $options);
     * </code>
     *
     * @param  \JDatabaseDriver $db
     * @param  array $options
     *
     * @return null|self
     */
    public static function getInstance(\JDatabaseDriver $db, array $options = array())
    {
        $userId  = ArrayHelper::getValue($options, "user_id");
        $groupId = ArrayHelper::getValue($options, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (!isset(self::$instances[$index])) {
            $item = new Badges($db);
            $item->load($options);
            
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load all user badges and set them to group index.
     * Every user can have only one badge for a group.
     *
     * <code>
     * $options = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadges     = new Gamification\User\Badges(\JFactory::getDbo())
     * $userBadges->load($options);
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
            ->select(
                "a.id, a.badge_id, a.user_id, a.group_id, " .
                "b.title, b.description, b.points, b.image, b.published, b.points_id, b.group_id"
            )
            ->from($this->db->quoteName("#__gfy_userbadges", "a"))
            ->innerJoin($this->db->quoteName("#__gfy_badges", "b") . ' ON a.badge_id = b.id')
            ->where("a.user_id = " . (int)$userId);

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
                $badge = new Badge(\JFactory::getDbo());
                $badge->bind($result);

                $this->items[$result["group_id"]][$result["badge_id"]] = $badge;
            }
        }
    }

    /**
     * Return user badges. They can be obtained by group ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadges  = new GamificationUserBadges(\JFactory::getDbo());
     * $userBadges->load($options);
     *
     * $badges      = $userBadges->getBadges();
     * </code>
     *
     * @param  $groupId
     *
     * @return array
     */
    public function getBadges($groupId = null)
    {
        return (!is_null($groupId)) ? ArrayHelper::getValue($this->items, $groupId, array()) : $this->items;
    }

    /**
     * Get badge by badge ID and group ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $badgeId     = 1;
     *
     * $userBadges  = new GamificationUserBadges(\JFactory::getDbo());
     * $userBadges->load($options);
     *
     * $badge       = $userBadges->getBadge($badgeId);
     * </code>
     *
     * @param integer $badgeId
     * @param integer $groupId
     *
     * @return null|Badge
     */
    public function getBadge($badgeId, $groupId = null)
    {
        $item = null;

        if (!empty($groupId)) { // Get an item from a specific group
            $item = (!isset($this->items[$groupId])) ? null : ArrayHelper::getValue($this->items[$groupId], $badgeId);
        } else { // Look in all groups
            foreach ($this->items as $group) {
                $item = ArrayHelper::getValue($group, $badgeId);
                if (!empty($item) and $item->getId()) {
                    break;
                } else {
                    $item = null;
                }
            }
        }

        return $item;
    }
}

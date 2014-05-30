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
 * This class contains methods that are used for managing user badges.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationUserBadges
{
    protected $userId;

    protected $badges = array();

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
     * $userBadges    = new GamificationUserBadges($keys);
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
     * Create an object and load user badges.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userBadges    = GamificationUserBadges::getInstance($keys);
     *
     * </code>
     *
     * @param  array $keys
     *
     * @return null|GamificationUserBadges
     */
    public static function getInstance(array $keys)
    {
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (empty(self::$instances[$index])) {
            $item                    = new GamificationUserBadges($keys);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load all user badges and set them to group index.
     * Every user can have only one badge for a group.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadges     = new GamificationUserBadges();
     * $userBadges->load($keys);
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
            ->select("a.id, a.badge_id, a.user_id, a.group_id, a.note")
            ->select("b.title, b.points, b.image, b.published, b.points_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userbadges") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_badges") . ' AS b ON a.badge_id = b.id')
            ->where("a.user_id = " . (int)$userId);

        if (!empty($groupId)) {
            $query->where("a.group_id = " . (int)$groupId);
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!empty($results)) {

            $this->userId = $userId;

            foreach ($results as $result) {
                $badge = new GamificationUserBadge();
                $badge->bind($result);

                $this->badges[$result["group_id"]][$badge->badge_id] = $badge;
            }

        }
    }

    /**
     * Return user badges. They can be obtained by group ID.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadges  = GamificationUserBadges::getInstance($keys);
     * $badges      = $userBadges->getBadges();
     *
     * </code>
     *
     * @param  $groupId
     *
     * @return array
     *
     */
    public function getBadges($groupId = null)
    {
        return (!is_null($groupId)) ? JArrayHelper::getValue($this->badges, $groupId, array()) : $this->badges;
    }

    /**
     * Get badge by badge ID and group ID.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $badgeId     = 1;
     *
     * $userBadges  = GamificationUserBadges::getInstance($keys);
     * $badge       = $userBadges->getBadge($badgeId);
     *
     * </code>
     *
     * @param integer $badgeId
     * @param integer $groupId
     *
     * @return null|GamificationUserBadge
     *
     */
    public function getBadge($badgeId, $groupId = null)
    {
        $item = null;

        if (!empty($groupId)) { // Get an item from a specific group

            $item = (!isset($this->badges[$groupId])) ? null : JArrayHelper::getValue($this->badges[$groupId], $badgeId);

        } else { // Look in all groups

            foreach ($this->badges as $group) {
                $item = JArrayHelper::getValue($group, $badgeId);
                if (!empty($item->id)) {
                    return $item;
                }
            }
        }

        return $item;
    }
}

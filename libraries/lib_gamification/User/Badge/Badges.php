<?php
/**
 * @package         Gamification\User
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Badge;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user badges.
 *
 * @package         Gamification\User
 * @subpackage      Badges
 */
class Badges extends Collection
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

    /**
     * Load all user badges and set them to group index.
     * Every user can have only one badge for a group.
     *
     * <code>
     * $options = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userBadges     = new Gamification\User\Badge\Badges(\JFactory::getDbo())
     * $userBadges->load($options);
     * </code>
     *
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $userId  = $this->getOptionId($options, 'user_id');
        $groupId = $this->getOptionId($options, 'group_id');

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.badge_id, a.user_id, a.group_id, ' .
                'b.title, b.description, b.activity_text, b.points_number, b.image, b.published, b.points_id, b.group_id'
            )
            ->from($this->db->quoteName('#__gfy_userbadges', 'a'))
            ->innerJoin($this->db->quoteName('#__gfy_badges', 'b') . ' ON a.badge_id = b.id')
            ->where('a.user_id = ' . (int)$userId);

        if ($groupId > 0) {
            $query->where('a.group_id = ' . (int)$groupId);
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Return user badges. They can be obtained by group ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userBadges  = new Gamification\User\Badge\Badges(\JFactory::getDbo());
     * $userBadges->load($options);
     *
     * $badges      = $userBadges->getBadges();
     * </code>
     *
     * @param  $groupId
     *
     * @return array
     */
    public function getBadges($groupId = 0)
    {
        $results = array();

        foreach ($this->items as $item) {
            $badge = new Badge($this->db);
            $badge->bind($item);

            $badgeGroupId = (int)$badge->getGroupId();
            $results[$badgeGroupId][] = $badge;
        }

        return ($groupId > 0 and array_key_exists($groupId, $results)) ? (array)$results[$groupId] : (array)$results;
    }

    /**
     * Get badge by badge ID and group ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $badgeId     = 1;
     *
     * $userBadges  = new Gamification\User\Badge\Badges(\JFactory::getDbo());
     * $userBadges->load($options);
     *
     * $badge       = $userBadges->getBadge($badgeId);
     * </code>
     *
     * @param int $badgeId
     * @param int $groupId
     *
     * @return null|Badge
     */
    public function getBadge($badgeId, $groupId = 0)
    {
        $badge = null;

        if ($groupId > 0) { // Get an item from a specific group
            foreach ($this->items as $item) {
                if (((int)$badgeId === (int)$item['id']) and ((int)$groupId === (int)$item['group_id'])) {
                    $badge = new Badge($this->db);
                    $badge->bind($item);
                    break;
                }
            }
        } else { // Look in all groups
            foreach ($this->items as $item) {
                if ((int)$item['id'] === (int)$badgeId) {
                    $badge = new Badge($this->db);
                    $badge->bind($item);
                    break;
                }
            }
        }

        return $badge;
    }
}

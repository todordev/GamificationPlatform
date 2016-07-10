<?php
/**
 * @package         Gamification
 * @subpackage      Profiles
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Profile;

use Prism\Database\TableImmutable;
use Gamification\Badge\Badge;
use Gamification\Level\Level;
use Gamification\Rank\Rank;
use Gamification\Reward\Reward;
use Gamification\Achievement\Achievement;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing user profiles.
 *
 * @package         Gamification
 * @subpackage      Profiles
 */
class Profile extends TableImmutable
{
    protected $id;
    protected $name;
    protected $username;

    /**
     * Load profile data.
     *
     * <code>
     * $keys = array(
     *    "id" => 1,
     *    "registerDate" => "2015-03-03"
     * );
     *
     * $profile  = new Gamification\Profile\Profile(\JFactory::getDbo());
     * $profile->load($userId);
     * </code>
     *
     * @param int|array $keys User ID
     * @param array   $options This options are used for specifying the things for loading.
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.name, a.username')
            ->from($this->db->quoteName('#__users', 'a'));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName('a.'.$column) . ' = ' . $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    public function hasBadge(Badge $badge, $userId = 0)
    {
        if (!$userId and $this->id > 0) {
            $userId = $this->id;
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__gfy_userbadges', 'a'))
            ->where('a.badge_id = '. (int)$badge->getId())
            ->where('a.user_id  = '. (int)$userId)
            ->where('a.group_id = '. (int)$badge->getGroupId());

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }

    public function isLevelAchieved(Level $level, $userId = 0)
    {
        if (!$userId and $this->id > 0) {
            $userId = $this->id;
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__gfy_userlevels', 'a'))
            ->where('a.user_id  = '. (int)$userId)
            ->where('a.group_id = '. (int)$level->getGroupId())
            ->where('a.level_id = '. (int)$level->getId());

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }

    public function isRankAchieved(Rank $rank, $userId = 0)
    {
        if (!$userId and $this->id > 0) {
            $userId = $this->id;
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__gfy_userranks', 'a'))
            ->where('a.rank_id  = '. (int)$rank->getId())
            ->where('a.user_id  = '. (int)$userId)
            ->where('a.group_id = '. (int)$rank->getGroupId());

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }

    public function isRewardReceived(Reward $reward, $userId = 0)
    {
        if (!$userId and $this->id > 0) {
            $userId = $this->id;
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__gfy_userrewards', 'a'))
            ->where('a.reward_id  = '. (int)$reward->getId())
            ->where('a.user_id  = '. (int)$userId);

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }

    public function isAchievementAccomplished(Achievement $achievement, $userId = 0)
    {
        if (!$userId and $this->id > 0) {
            $userId = $this->id;
        }

        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__gfy_userachievements', 'a'))
            ->where('a.achievement_id  = '. (int)$achievement->getId())
            ->where('a.user_id  = '. (int)$userId);

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }
}

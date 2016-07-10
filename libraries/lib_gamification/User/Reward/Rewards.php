<?php
/**
 * @package         Gamification\User
 * @subpackage      Rewards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user rewards.
 *
 * @package         Gamification\User
 * @subpackage      Rewards
 */
class Rewards extends Collection
{
    /**
     * Users ID
     *
     * @var integer
     */
    public $userId;

    /**
     * Group ID.
     *
     * @var int
     */
    protected $groupId;

    /**
     * Load all user rewards and set them to group index.
     * Every user can have only one reward for a group.
     *
     * <code>
     * $options = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userRewards     = new Gamification\User\Rewards(JFactory::getDbo());
     * $userRewards->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        $userId  = $this->getOptionId($options, 'user_id');
        $groupId = $this->getOptionId($options, 'group_id');

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.reward_id, a.user_id, a.group_id')
            ->select('b.title, b.points, b.image, b.published, b.points_id, b.group_id')
            ->from($this->db->quoteName('#__gfy_userrewards', 'a'))
            ->innerJoin($this->db->quoteName('#__gfy_rewards', 'b') . ' ON a.reward_id = b.id')
            ->where('a.user_id = ' . (int)$userId);

        if ($groupId > 0) {
            $query->where('a.group_id = ' . (int)$groupId);
        }

        $this->db->setQuery($query);
        $results = (array)$this->db->loadAssocList();

        if (count($results) > 0) {

            $this->userId = $userId;

            foreach ($results as $result) {
                $reward = new Reward(\JFactory::getDbo());
                $reward->bind($result);

                $this->items[$result['group_id']][$reward->getRewardId()] = $reward;
            }
        }
    }

    /**
     * Return all rewards.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userRewards   = new Gamification\User\Rewards(JFactory::getDbo());
     * $userRewards->load($keys);
     *
     * $rewards       = $userRewards->getRewards();
     * </code>
     *
     * @return array
     */
    public function getRewards()
    {
        return $this->items;
    }

    /**
     * Get a reward by group ID.
     * Users can have only one reward in a group.
     *
     * <code>
     *
     * $keys = array(
     *       'user_id'  => 1
     * );
     *
     * // Get all user rewards
     * $userRewards  = new Gamification\User\Rewards(JFactory::getDbo());
     * $userRewards->load($keys);
     *
     * // Get reward by group ID.
     * $groupId    = 2;
     * $reward       = $userRewards->getReward($groupId);
     * </code>
     *
     * @param integer $groupId
     *
     * @return null|Reward
     */
    public function getReward($groupId)
    {
        return (!isset($this->items[$groupId])) ? null : $this->items[$groupId];
    }
}

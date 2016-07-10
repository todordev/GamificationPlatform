<?php
/**
 * @package         Gamification\User
 * @subpackage      Achievements
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user achievements.
 *
 * @package         Gamification\User
 * @subpackage      Achievements
 */
class Achievements extends Collection
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
     * Load all user achievements and set them to group index.
     * Every user can have only one achievement for a group.
     *
     * <code>
     * $options = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userAchievements     = new Gamification\User\Achievements(JFactory::getDbo());
     * $userAchievements->load($options);
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
            ->select('a.achievement_id, a.user_id, a.group_id')
            ->select('b.title, b.points, b.image, b.published, b.points_id, b.group_id')
            ->from($this->db->quoteName('#__gfy_userachievements', 'a'))
            ->innerJoin($this->db->quoteName('#__gfy_achievements', 'b') . ' ON a.achievement_id = b.id')
            ->where('a.user_id = ' . (int)$userId);

        if ($groupId > 0) {
            $query->where('a.group_id = ' . (int)$groupId);
        }

        $this->db->setQuery($query);
        $results = (array)$this->db->loadAssocList();

        if (count($results) > 0) {

            $this->userId = $userId;

            foreach ($results as $result) {
                $achievement = new Achievement(\JFactory::getDbo());
                $achievement->bind($result);

                $this->items[$result['group_id']][$achievement->getAchievementId()] = $achievement;
            }
        }
    }

    /**
     * Return all achievements.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userAchievements   = new Gamification\User\Achievements(JFactory::getDbo());
     * $userAchievements->load($keys);
     *
     * $achievements       = $userAchievements->getAchievements();
     * </code>
     *
     * @return array
     */
    public function getAchievements()
    {
        return $this->items;
    }

    /**
     * Get a achievement by group ID.
     * Users can have only one achievement in a group.
     *
     * <code>
     *
     * $keys = array(
     *       'user_id'  => 1
     * );
     *
     * // Get all user achievements
     * $userAchievements  = new Gamification\User\Achievements(JFactory::getDbo());
     * $userAchievements->load($keys);
     *
     * // Get achievement by group ID.
     * $groupId    = 2;
     * $achievement       = $userAchievements->getAchievement($groupId);
     * </code>
     *
     * @param integer $groupId
     *
     * @return null|Achievement
     */
    public function getAchievement($groupId)
    {
        return (!isset($this->items[$groupId])) ? null : $this->items[$groupId];
    }
}

<?php
/**
 * @package         Gamification
 * @subpackage      Rewards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Reward;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing rewards.
 *
 * @package         Gamification
 * @subpackage      Rewards
 */
class Rewards extends Collection
{
    /**
     * Load units from database.
     *
     * <code>
     * $rewards = new Gamification\Reward\Rewards(JFactory::getDbo());
     * $rewards->load();
     *
     * $options = $rewards->toOptions("id", "title");
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $ids      = $this->getOptionIds($options);
        $state    = $this->getOptionState($options);
        $groupId  = $this->getOptionId($options, 'group_id');
        $pointsId = $this->getOptionId($options, 'points_id');

        $orderColumn    = $this->getOptionOrderColumn($options, 'a.title');
        $orderDirection = $this->getOptionOrderDirection($options, 'ASC');
        
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.description, a.activity_text, a.points_number, a.image, a.note, a.number, a.published, a.points_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_rewards', 'a'))
            ->order('a.title ASC');

        // Filter by unit ID.
        if (count($ids) > 0) {
            $query->where('a.id IN (' . implode(',', $ids) . ')');
        }

        // Filter by points ID.
        if ($pointsId > 0) {
            $query->where('a.points_id = ' . (int)$pointsId);
        }

        // Filter by group ID.
        if ($groupId > 0) {
            $query->where('a.group_id = ' . (int)$groupId);
        }

        // Filter by state.
        if (!is_numeric($state)) {
            $query->where('a.published IN (1,0)');
        } else {
            $query->where('a.published = ' . (int)$state);
        }

        // Order by column.
        if ($orderColumn !== '') {
            $query->order($this->db->escape($orderColumn .' '. $orderDirection));
        }
        
        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Create a reward object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $rewards   = new Gamification\Reward\Rewards(\JFactory::getDbo());
     * $rewards->load($options);
     *
     * $rewardId = 1;
     * $reward   = $rewards->getReward($rewardId);
     * </code>
     *
     * @param int|string $id Reward ID
     *
     * @throws \UnexpectedValueException
     *
     * @return null|Reward
     */
    public function getReward($id)
    {
        $reward = null;

        foreach ($this->items as $item) {
            if ((int)$item['id'] === (int)$id) {
                $reward = new Reward($this->db);
                $reward->bind($item);
                break;
            }
        }

        return $reward;
    }

    /**
     * Return the rewards as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $rewards   = new Gamification\Reward\Rewards(\JFactory::getDbo());
     * $rewards->load($options);
     *
     * $rewards = $rewards->getRewards();
     * </code>
     *
     * @return array
     */
    public function getRewards()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $reward = new Reward($this->db);
            $reward->bind($item);

            $results[$i] = $reward;
            $i++;
        }

        return $results;
    }
}

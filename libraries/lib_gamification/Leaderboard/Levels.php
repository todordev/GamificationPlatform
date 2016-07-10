<?php
/**
 * @package         Gamification
 * @subpackage      Leaderboards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Leaderboard;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing leaderboard data.
 * The data is based on the game mechanic levels.
 *
 * @package         Gamification
 * @subpackage      Leaderboards
 */
class Levels extends Collection
{
    /**
     * Load the data that will be displayed on the leaderboard.
     *
     * <code>
     * $options = array(
     *      "group_id" => 2
     *      "order_column"    => "b.points",
     *      "order_direction" => "DESC",
     *      "limit"           => 10
     * );
     *
     * $leaderboard = new Gamification\Leaderboard\Levels(JFactory::getDbo());
     * $leaderboard->load($options);
     * </code>
     *
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $groupId        = $this->getOptionId($options, 'group_id');
        $orderColumn    = $this->getOptionOrderColumn($options, 'b.points_number');
        $orderDirection = $this->getOptionOrderDirection($options);
        $limit          = $this->getOptionLimit($options);

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.user_id, a.level_id, a.group_id, ' .
                'b.title, b.value, ' .
                'c.name, ' .
                'd.title AS rank'
            )
            ->from($this->db->quoteName('#__gfy_userlevels', 'a'))
            ->innerJoin($this->db->quoteName('#__gfy_levels', 'b') . ' ON a.level_id = b.id')
            ->innerJoin($this->db->quoteName('#__users', 'c') . ' ON a.user_id = c.id')
            ->leftJoin($this->db->quoteName('#__gfy_ranks', 'd') . ' ON b.rank_id = d.id')
            ->where('a.group_id = ' . (int)$groupId)
            ->order($this->db->quoteName($orderColumn) . ' ' . $orderDirection);

        $this->db->setQuery($query, 0, $limit);
        $this->items = (array)$this->db->loadObjectList();
    }
}

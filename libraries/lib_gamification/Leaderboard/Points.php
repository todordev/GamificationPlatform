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
 * The data is based on the game mechanic points.
 *
 * @package         Gamification
 * @subpackage      Leaderboards
 */
class Points extends Collection
{
    /**
     * Load the data that will be displayed on the leaderboard.
     *
     * <code>
     * $options = array(
     *      "points_id" => 2,
     *      "order_column" => "a.points",
     *      "order_direction" => "DESC",
     *      "limit"          => 10
     * );
     *
     * $leaderboard = new Gamification\Leaderboard\Points(JFactory::getDbo());
     * $leaderboard->load($options);
     * </code>
     *
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $pointsId       = $this->getOptionId($options, 'points_id');
        $orderColumn    = $this->getOptionOrderColumn($options, 'a.points_number');
        $orderDirection = $this->getOptionOrderDirection($options);
        $limit          = $this->getOptionLimit($options);

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.points_number, a.user_id, ' .
                'b.title, b.abbr, ' .
                'c.name '
            )
            ->from($this->db->quoteName('#__gfy_userpoints', 'a'))
            ->innerJoin($this->db->quoteName('#__gfy_points', 'b') . ' ON a.points_id = b.id')
            ->innerJoin($this->db->quoteName('#__users', 'c') . ' ON a.user_id = c.id')
            ->where('a.points_id = ' . (int)$pointsId)
            ->order($this->db->quoteName($orderColumn) . ' ' . $orderDirection);

        $this->db->setQuery($query, 0, $limit);
        $this->items = (array)$this->db->loadObjectList();
    }
}

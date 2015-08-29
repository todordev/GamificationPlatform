<?php
/**
 * @package         Gamification
 * @subpackage      Leaderboards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Leaderboard;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing leaderboard data.
 * The data is based on the game mechanic levels.
 *
 * @package         Gamification
 * @subpackage      Leaderboards
 */
class Levels extends ArrayObject
{
    /**
     * Load the data that will be displayed on the leaderboard.
     *
     * <code>
     * $options = array(
     *      "group_id" => 2
     *      "sort_direction" => "DESC",
     *      "limit"          => 10
     * );
     *
     * $leaderboard = new Gamification\Leaderboard\Levels(JFactory::getDbo());
     * $leaderboard->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $groupId = ArrayHelper::getValue($options, "group_id");

        $sortDir = ArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";

        $limit = ArrayHelper::getValue($options, "limit", 10, "int");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.user_id, a.level_id, a.group_id, " .
                "b.title, b.value, " .
                "c.name, " .
                "d.title AS rank"
            )
            ->from($this->db->quoteName("#__gfy_userlevels", "a"))
            ->innerJoin($this->db->quoteName("#__gfy_levels", "b") . ' ON a.level_id = b.id')
            ->innerJoin($this->db->quoteName("#__users", "c") . ' ON a.user_id = c.id')
            ->leftJoin($this->db->quoteName("#__gfy_ranks", "d") . ' ON b.rank_id = d.id')
            ->where("a.group_id = " . (int)$groupId)
            ->order("b.points " . $sortDir);

        $this->db->setQuery($query, 0, $limit);
        $results = $this->db->loadObjectList();

        if (!empty($results)) {
            $this->items = $results;
        }
    }
}

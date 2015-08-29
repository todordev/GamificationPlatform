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
 * The data is based on the game mechanic points.
 *
 * @package         Gamification
 * @subpackage      Leaderboards
 */
class Points extends ArrayObject
{
    /**
     * Load the data that will be displayed on the leaderboard.
     *
     * <code>
     *
     * $keys = array(
     *
     * );
     *
     * $options = array(
     *      "points_id" => 2,
     *      "sort_direction" => "DESC",
     *      "limit"          => 10
     * );
     *
     * $leaderboard = new Gamification\Leaderboard\Points(JFactory::getDbo());
     * $leaderboard->load($options);
     *
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $pointsId = ArrayHelper::getValue($options, "points_id");

        $sortDir  = ArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir  = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";

        $limit = ArrayHelper::getValue($options, "limit", 10, "int");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.points, a.user_id, " .
                "b.title, b.abbr, " .
                "c.name "
            )
            ->from($this->db->quoteName("#__gfy_userpoints") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_points") . ' AS b ON a.points_id = b.id')
            ->innerJoin($this->db->quoteName("#__users") . ' AS c ON a.user_id = c.id')
            ->where("a.points_id = " . (int)$pointsId)
            ->order("a.points " . $sortDir);

        $this->db->setQuery($query, 0, $limit);
        $results = $this->db->loadObjectList();

        if (!empty($results)) {
            $this->items = $results;
        }
    }
}

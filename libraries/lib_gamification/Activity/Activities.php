<?php
/**
 * @package         Gamification
 * @subpackage      Activities
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Activity;

use Prism\Database\ArrayObject;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing activities.
 *
 * @package         Gamification
 * @subpackage      Activities
 */
class Activities extends ArrayObject
{
    /**
     * Load all user activities.
     *
     * <code>
     *
     * $options = array(
     *        "user_id"       => 1,
     *        "limit"         => 10,
     *        "sort_direction" => "DESC"
     * );
     *
     * $activities = new Gamification\Activity\Activities(JFactory::getDbo());
     * $activities->load($options);
     *
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     */
    public function load($options = array())
    {
        $userId  = ArrayHelper::getValue($options, "user_id", 0, "integer");

        $sortDir = ArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";

        $limit = ArrayHelper::getValue($options, "limit", 10, "int");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.title, a.content, a.image, a.url, a.created, a.user_id, " .
                "b.name"
            )
            ->from($this->db->quoteName("#__gfy_activities", "a"))
            ->innerJoin($this->db->quoteName("#__users", "b") . ' ON a.user_id = b.id');

        if (!empty($userId)) {
            $query->where("a.user_id = " . (int)$userId);
        }

        $query->order("a.created " . $sortDir);

        $this->db->setQuery($query, 0, $limit);
        $this->items = (array)$this->db->loadAssocList();
    }
}

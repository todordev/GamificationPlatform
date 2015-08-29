<?php
/**
 * @package         Gamification
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Points;

use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing points.
 *
 * @package         Gamification
 * @subpackage      Points
 */
class PointsItems extends ArrayObject
{
    /**
     * Load points data from database.
     *
     * <code>
     * $points = new Gamification\Points\Points(JFactory::getDbo());
     * $points->load();
     *
     * $options = $points->toOptions();
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     */
    public function load($options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.title, a.abbr")
            ->from($this->db->quoteName("#__gfy_points", "a"))
            ->order("a.title ASC");

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }
}

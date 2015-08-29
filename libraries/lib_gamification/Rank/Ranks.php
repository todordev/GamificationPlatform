<?php
/**
 * @package         Gamification
 * @subpackage      Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Rank;

use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing ranks.
 *
 * @package         Gamification
 * @subpackage      Ranks
 */
class Ranks extends ArrayObject
{
    /**
     * Load ranks from database.
     *
     * <code>
     * $ranks = new Gamification\Rank\Ranks(JFactory::getDbo());
     * $ranks->load();
     *
     * $options = $ranks->toOptions("id", "title");
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     */
    public function load($options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.title")
            ->from($this->db->quoteName("#__gfy_ranks", "a"))
            ->order("a.title ASC");

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }
}

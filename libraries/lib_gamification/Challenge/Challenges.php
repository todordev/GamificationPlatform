<?php
/**
 * @package         Gamification
 * @subpackage      Challenges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Reward;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing achievements.
 *
 * @package         Gamification
 * @subpackage      Challenges
 */
class Challenges extends Collection
{
    /**
     * Load units from database.
     *
     * <code>
     * $achievements = new Gamification\Challenge\Challenges(JFactory::getDbo());
     * $achievements->load();
     *
     * $options = $achievements->toOptions("id", "title");
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     */
    public function load(array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title')
            ->from($this->db->quoteName('#__gfy_challenges', 'a'))
            ->order('a.title ASC');

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }
}

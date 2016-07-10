<?php
/**
 * @package         Gamification
 * @subpackage      Groups
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Group;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing groups.
 *
 * @package         Gamification
 * @subpackage      Groups
 */
class Groups extends Collection
{
    /**
     * Load groups from database.
     *
     * <code>
     * $groups = new Gamification\Group\Groups(JFactory::getDbo());
     * $groups->load();
     *
     * $options = $groups->toOptions();
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.name')
            ->from($this->db->quoteName('#__gfy_groups', 'a'))
            ->order('a.name ASC');

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }
}

<?php
/**
 * @package         Gamification
 * @subpackage      Profiles
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Profile;

use Prism\Database\TableImmutable;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing user profiles.
 *
 * @package         Gamification
 * @subpackage      Profiles
 */
class Profile extends TableImmutable
{
    protected $id = null;
    protected $name = null;
    protected $username = null;

    /**
     * Load profile data.
     *
     * <code>
     * $keys = array(
     *    "id" => 1,
     *    "registerDate" => "2015-03-03"
     * );
     *
     * $profile  = new Gamification\Profile\Profile(\JFactory::getDbo());
     * $profile->load($userId);
     * </code>
     *
     * @param int|array $keys User ID
     * @param array   $options This options are used for specifying the things for loading.
     */
    public function load($keys, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.name, a.username")
            ->from($this->db->quoteName("#__users", "a"));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName("a.".$column) . " = " . $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }
    }
}

<?php
/**
 * @package         Gamification
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Badge;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing badges.
 *
 * @package         Gamification
 * @subpackage      Badges
 */
class Badges extends Collection
{
    /**
     * Load items from database.
     *
     * <code>
     * $badges = new Gamification\Badge\Badges(JFactory::getDbo());
     * $badges->load();
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $ids      = $this->getOptionIds($options);
        $state    = $this->getOptionState($options);
        $groupId  = $this->getOptionId($options, 'group_id');
        $pointsId = $this->getOptionId($options, 'points_id');

        $orderColumn    = $this->getOptionOrderColumn($options, 'a.ordering');
        $orderDirection = $this->getOptionOrderDirection($options, 'ASC');

        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.description, a.activity_text, a.image, a.note, a.custom_data, ' .
                'a.published, a.params, a.group_id, a.points_id, a.points_number'
            )
            ->from($this->db->quoteName('#__gfy_badges', 'a'));

        // Filter by unit ID.
        if (count($ids) > 0) {
            $query->where('a.id IN (' . implode(',', $ids) . ')');
        }

        // Filter by points ID.
        if ($pointsId > 0) {
            $query->where('a.points_id = ' . (int)$pointsId);
        }
        
        // Filter by group ID.
        if ($groupId > 0) {
            $query->where('a.group_id = ' . (int)$groupId);
        }

        // Filter by state.
        if (!is_numeric($state)) {
            $query->where('a.published IN (1,0)');
        } else {
            $query->where('a.published = ' . (int)$state);
        }

        // Order by column.
        if ($orderColumn !== '') {
            $query->order($this->db->escape($orderColumn .' '. $orderDirection));
        }
        
        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Create a badge object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $badges   = new Gamification\Badge\Badges(\JFactory::getDbo());
     * $badges->load($options);
     *
     * $badgeId = 1;
     * $badge   = $badges->getBadge($badgeId);
     * </code>
     *
     * @param int|string $id Badge ID
     *
     * @throws \UnexpectedValueException
     *
     * @return null|Badge
     */
    public function getBadge($id)
    {
        $badge = null;

        foreach ($this->items as $item) {
            if ((int)$item['id'] === (int)$id) {
                $badge = new Badge($this->db);
                $badge->bind($item);
                break;
            }
        }

        return $badge;
    }

    /**
     * Return the badges as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $badges   = new Gamification\Badge\Badges(\JFactory::getDbo());
     * $badges->load($options);
     *
     * $badges = $badges->getBadges();
     * </code>
     *
     * @return array
     */
    public function getBadges()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $badge = new Badge($this->db);
            $badge->bind($item);

            $results[$i] = $badge;
            $i++;
        }

        return $results;
    }
}

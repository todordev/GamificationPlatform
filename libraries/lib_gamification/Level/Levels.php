<?php
/**
 * @package         Gamification
 * @subpackage      Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Level;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing levels.
 *
 * @package         Gamification
 * @subpackage      Levels
 */
class Levels extends Collection
{
    /**
     * Load items from database.
     *
     * <code>
     * $levels = new Gamification\Level\Levels(JFactory::getDbo());
     * $levels->load();
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
        $rankId   = $this->getOptionId($options, 'rank_id');

        $orderColumn    = $this->getOptionOrderColumn($options, 'a.points_number');
        $orderDirection = $this->getOptionOrderDirection($options, 'ASC');

        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.points_number, a.value, a.published, a.points_id, a.rank_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_levels', 'a'));

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

        // Filter by rank ID.
        if ($rankId > 0) {
            $query->where('a.rank_id = ' . (int)$rankId);
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
     * Create a level object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $levels   = new Gamification\Level\Levels(\JFactory::getDbo());
     * $levels->load($options);
     *
     * $levelId = 1;
     * $level   = $levels->getLevel($levelId);
     * </code>
     *
     * @param int|string $id Level ID
     *
     * @throws \UnexpectedValueException
     *
     * @return null|Level
     */
    public function getLevel($id)
    {
        $level = null;

        foreach ($this->items as $item) {
            if ((int)$item['id'] === (int)$id) {
                $level = new Level($this->db);
                $level->bind($item);
                break;
            }
        }

        return $level;
    }

    /**
     * Return the levels as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $levels   = new Gamification\Level\Levels(\JFactory::getDbo());
     * $levels->load($options);
     *
     * $levels = $levels->getLevels();
     * </code>
     *
     * @return array
     */
    public function getLevels()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $level = new Level($this->db);
            $level->bind($item);

            $results[$i] = $level;
            $i++;
        }

        return $results;
    }
}

<?php
/**
 * @package         Gamification
 * @subpackage      Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Rank;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing ranks.
 *
 * @package         Gamification
 * @subpackage      Ranks
 */
class Ranks extends Collection
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
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $ids      = $this->getOptionIds($options);
        $state    = $this->getOptionState($options);
        $groupId  = $this->getOptionId($options, 'group_id');
        $pointsId = $this->getOptionId($options, 'points_id');

        $orderColumn    = $this->getOptionOrderColumn($options, 'a.title');
        $orderDirection = $this->getOptionOrderDirection($options, 'ASC');
        
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.description, a.note, a.activity_text, a.image, a.published, a.points_number, a.points_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_ranks', 'a'));

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
     * Create a rank object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $ranks   = new Gamification\Rank\Ranks(\JFactory::getDbo());
     * $ranks->load($options);
     *
     * $rankId = 1;
     * $rank   = $ranks->getRank($rankId);
     * </code>
     *
     * @param int|string $id Rank ID
     *
     * @throws \UnexpectedValueException
     *
     * @return null|Rank
     */
    public function getRank($id)
    {
        $rank = null;

        foreach ($this->items as $item) {
            if ((int)$item['id'] === (int)$id) {
                $rank = new Rank($this->db);
                $rank->bind($item);
                break;
            }
        }

        return $rank;
    }

    /**
     * Return the ranks as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $ranks   = new Gamification\Rank\Ranks(\JFactory::getDbo());
     * $ranks->load($options);
     *
     * $ranks = $ranks->getRanks();
     * </code>
     *
     * @return array
     */
    public function getRanks()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $rank = new Rank($this->db);
            $rank->bind($item);

            $results[$i] = $rank;
            $i++;
        }

        return $results;
    }
}

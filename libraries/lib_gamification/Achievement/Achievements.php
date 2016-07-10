<?php
/**
 * @package         Gamification
 * @subpackage      Achievements
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Achievement;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing achievements.
 *
 * @package         Gamification
 * @subpackage      Achievements
 */
class Achievements extends Collection
{
    /**
     * Load units from database.
     *
     * <code>
     * $achievements = new Gamification\Achievement\Achievements(JFactory::getDbo());
     * $achievements->load();
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $orderColumn    = $this->getOptionOrderColumn($options, 'a.ordering');
        $orderDirection = $this->getOptionOrderDirection($options);

        $ids     = $this->getOptionIds($options);
        $groupId  = $this->getOptionId($options, 'group_id');
        $pointsId = $this->getOptionId($options, 'points_id');
        $context  = !array_key_exists('context', $options) ? null : $options['context'];

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.context, a.description, a.activity_text, a.image, a.image_small, a.image_square, ' .
                'a.points_id, a.points_number, a.published, a.custom_data, a.rewards, a.group_id'
            )
            ->from($this->db->quoteName('#__gfy_achievements', 'a'))
            ->order($this->db->escape($orderColumn . ' ' . $orderDirection));

        // Filter by unit ID.
        if (count($ids) > 0) {
            $query->where('a.id IN (' . implode(',', $ids) . ')');
        }
        
        // Filter by group ID.
        if ($groupId > 0) {
            $query->where('a.group_id = ' . (int)$groupId);
        }

        // Filter by points ID.
        if ($pointsId > 0) {
            $query->where('a.points_id = ' . (int)$pointsId);
        }

        // Filter by context.
        if ($context !== null and $context !== '') {
            $query->where('a.context = ' . $this->db->quote($context));
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Create a achievement object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $achievements   = new Gamification\Achievement\Achievements(\JFactory::getDbo());
     * $achievements->load($options);
     *
     * $achievementId = 1;
     * $achievement = $achievements->getAchievement($achievementId);
     * </code>
     *
     * @param int|string $id Achievement ID or Achievement context.
     *
     * @return Achievement|null
     */
    public function getAchievement($id)
    {
        $achievement = null;

        foreach ($this->items as $item) {
            if (is_numeric($id) and (int)$id === (int)$item['id']) {
                $achievement = new Achievement($this->db);
                $achievement->bind($this->items[$id]);
                break;
            } elseif (strcmp($id, $item['context']) === 0) {
                $achievement = new Achievement($this->db);
                $achievement->bind($item);
                break;
            }
        }

        return $achievement;
    }

    /**
     * Return the achievements as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $achievements   = new Gamification\Achievement\Achievements(\JFactory::getDbo());
     * $achievements->load($options);
     *
     * $achievements = $achievements->getAchievements();
     * </code>
     *
     * @return array
     */
    public function getAchievements()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $achievement = new Achievement($this->db);
            $achievement->bind($item);
            
            $results[$i] = $achievement;
            $i++;
        }

        return $results;
    }

    /**
     * Return contexts of the items.
     *
     * <code>
     * $achievements   = new Gamification\Achievement\Achievements(\JFactory::getDbo());
     * $context = $achievements->getContexts();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function getContexts()
    {
        $contexts = array();
        
        if (count($this->items) > 0) {
            foreach ($this->items as $item) {
                $contexts[] = $item['context'];
            }

            $contexts = array_unique($contexts);
        } else {
            $query = $this->db->getQuery(true);

            $query
                ->select('DISTINCT a.context')
                ->from($this->db->quoteName('#__gfy_achievements', 'a'));

            $this->db->setQuery($query);
            $contexts = (array)$this->db->loadColumn();
        }
        
        return $contexts;
    }
}

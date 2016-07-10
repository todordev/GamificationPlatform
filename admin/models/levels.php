<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class GamificationModelLevels extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array   $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'points_number', 'a.points_number',
                'value', 'a.value',
                'group_name', 'b.name',
                'rank_name', 'd.title',
                'published', 'a.published',
            );
        }

        parent::__construct($config);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        $value = $this->getUserStateFromRequest($this->context . '.filter.group', 'filter_group');
        $this->setState('filter.group', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.rank', 'filter_rank');
        $this->setState('filter.rank', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $value);

        // List state information.
        parent::populateState('a.points_number', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.group');
        $id .= ':' . $this->getState('filter.rank');
        $id .= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.points_number, a.value, a.group_id, a.rank_id, a.published, ' .
                'b.name AS group_name, ' .
                'c.abbr AS points_type, c.title AS points_name, ' .
                'd.title AS rank_title'
            )
        );
        $query->from($db->quoteName('#__gfy_levels', 'a'));
        $query->innerJoin($db->quoteName('#__gfy_groups', 'b') . ' ON a.group_id = b.id');
        $query->innerJoin($db->quoteName('#__gfy_points', 'c') . ' ON a.points_id = c.id');
        $query->leftJoin($db->quoteName('#__gfy_ranks', 'd') . ' ON a.rank_id = d.id');

        // Filter by group id
        $groupId = (int)$this->getState('filter.group');
        if ($groupId > 0) {
            $query->where('a.group_id = ' . (int)$groupId);
        }

        // Filter by rank id
        $rankId = (int)$this->getState('filter.rank');
        if ($rankId > 0) {
            $query->where('a.rank_id = ' . (int)$rankId);
        }

        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.published = ' . (int)$state);
        } elseif ($state === '') {
            $query->where('(a.published IN (0, 1))');
        }

        // Filter by search in title
        $search = (string)$this->getState('filter.search');
        if ($search !== '') {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote('%' . $escaped . '%', false);
                $query->where('a.title LIKE ' . $quoted);
            }
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return 'a.group_id ASC, d.points_number ASC, ' . $orderCol . ' ' . $orderDirn;
    }
}

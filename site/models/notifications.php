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

jimport('joomla.application.component.modellist');

/**
 * This class contains methods tha manage notifications.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 */
class GamificationModelNotifications extends JModelList
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
                'created', 'a.created',
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // List state information.
        parent::populateState('a.created', 'desc');

        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);

        $value = JFactory::getUser()->get('id');
        $this->setState('filter.user_id', $value);

        // Result limit that comes from notification bar after click on the bell.
        $value = $app->input->getUint('rl');
        if ($value > 0) {
            $this->setState('list.limit', $value);
        }
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
//        $id .= ':' . $this->getState('filter.search');
//        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.user_id');

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
        // Create a new query object.
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.content, a.image, a.url, a.created, a.status, a.user_id '
            )
        );
        $query->from($db->quoteName('#__gfy_notifications', 'a'));

        // Filter by receiver
        $userId = $this->getState('filter.user_id');
        $query->where('a.user_id =' . (int)$userId);

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }
}

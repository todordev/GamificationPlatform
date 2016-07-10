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

class GamificationModelNotification extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   $type   string    The table type to instantiate
     * @param   $prefix string  A prefix for the table class name. Optional.
     * @param   $config array   Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Notification', $prefix = 'GamificationTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.notification', 'notification', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.notification.data', array());
        if (!$data) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Save data into the DB.
     *
     * @param array $data The data about item
     *
     * @return  int
     */
    public function save($data)
    {
        $id         = Joomla\Utilities\ArrayHelper::getValue($data, 'id');
        $content    = Joomla\Utilities\ArrayHelper::getValue($data, 'content');
        $url        = Joomla\Utilities\ArrayHelper::getValue($data, 'url');
        $image      = Joomla\Utilities\ArrayHelper::getValue($data, 'image');
        $status     = Joomla\Utilities\ArrayHelper::getValue($data, 'status', 0, 'int');

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set('content', $content);
        $row->set('url', (!$url) ? null : $url);
        $row->set('image', (!$image) ? null : $image);
        $row->set('status', $status);

        $row->store(true);

        return $row->get('id');
    }

    /**
     * Change state of notification to read or not read.
     *
     * @param array $ids
     * @param int $value
     */
    public function read(array $ids, $value)
    {
        if (count($ids) > 0) {
            $db = $this->getDbo();

            $query = $db->getQuery(true);

            $query
                ->update($db->quoteName('#__gfy_notifications'))
                ->set($db->quoteName('status') . '=' . (int)$value)
                ->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');

            $db->setQuery($query);
            $db->execute();
        }
    }
}

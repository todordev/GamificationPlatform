<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

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
        if (empty($form)) {
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
        if (empty($data)) {
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
        $id    = JArrayHelper::getValue($data, "id");
        $note  = JArrayHelper::getValue($data, "note");
        $url   = JArrayHelper::getValue($data, "url");
        $image = JArrayHelper::getValue($data, "image");
        $read  = JArrayHelper::getValue($data, "read", 0, "int");

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set("note", $note);
        $row->set("url", (!$url) ? null : $url);
        $row->set("image", (!$image) ? null : $image);
        $row->set("read", $read);

        $row->store(true);

        return $row->get("id");
    }
}

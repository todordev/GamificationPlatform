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

class GamificationModelPoint extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   $prefix string  A prefix for the table class name. Optional.
     * @param  $config array   Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Point', $prefix = 'GamificationTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.point', 'point', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.point.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Save data into the DB.
     *
     * @param array $data   The data about item
     *
     * @return   int
     */
    public function save($data)
    {
        $id        = JArrayHelper::getValue($data, "id");
        $title     = JArrayHelper::getValue($data, "title");
        $abbr      = JArrayHelper::getValue($data, "abbr");
        $groupId   = JArrayHelper::getValue($data, "group_id");
        $published = JArrayHelper::getValue($data, "published");

        $note = JArrayHelper::getValue($data, "note");
        if (!$note) {
            $note = null;
        }

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set("title", $title);
        $row->set("abbr", $abbr);
        $row->set("group_id", $groupId);
        $row->set("published", $published);
        $row->set("note", $note);

        $row->store(true);

        return $row->get("id");
    }
}

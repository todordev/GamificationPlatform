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

jimport('joomla.application.component.modelitem');

class GamificationModelNotification extends JModelItem
{
    protected $item = array();

    /**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    }

    /**
     * Method to get an object.
     *
     * @param    int  $id  The id of the object to get.
     * @param    int  $userId  User ID.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($id, $userId)
    {
        // If missing ID, I have to return null, because there is no item.
        if (!$id or !$userId) {
            return null;
        }

        $storedId = $this->getStoreId($id);

        if (!isset($this->item[$storedId])) {

            $this->item[$storedId] = null;

            // Get a level row instance.
            $table = JTable::getInstance('Notification', 'GamificationTable');
            /** @var $table GamificationTableNotification */

            $keys = array("id" => $id, "user_id" => $userId);

            // Attempt to load the row.
            if ($table->load($keys)) {

                $properties = $table->getProperties();
                $properties = JArrayHelper::toObject($properties);

                $this->item[$storedId] = $properties;
            }

        }

        return (!isset($this->item[$storedId])) ? null : $this->item[$storedId];
    }

    /**
     * Set notification as read.
     *
     * @param integer $id
     * @param integer $userId
     * @param int $status
     *
     * @todo fix this. Use GamificationNotification object.
     */
    public function changeStatus($id, $userId, $status)
    {
        $status = (!$status) ? 0 : 1;

        $db = $this->getDbo();

        $query = $db->getQuery(true);

        $query
            ->update($db->quoteName("#__gfy_notifications"))
            ->set($db->quoteName("status") . "=" . (int)$status)
            ->where($db->quoteName("id") . "=" . (int)$id)
            ->where($db->quoteName("user_id") . "=" . (int)$userId);

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * @param null $id
     * @param null $userId
     *
     * @return bool
     *
     * @todo Remove this. Use Validator objects.
     */
    public function isValid($id = null, $userId = null)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("COUNT(*)")
            ->from($db->quoteName("#__gfy_notifications", "a"))
            ->where("a.id      = " . (int)$id)
            ->where("a.user_id = " . (int)$userId);

        $db->setQuery($query);
        $result = $db->loadResult();

        return (!$result) ? false : true;
    }
}

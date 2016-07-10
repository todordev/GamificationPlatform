<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

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

        if (!array_key_exists($storedId, $this->item)) {

            $this->item[$storedId] = null;

            // Get a level row instance.
            $table = JTable::getInstance('Notification', 'GamificationTable');
            /** @var $table GamificationTableNotification */

            $keys = array('id' => $id, 'user_id' => $userId);

            // Attempt to load the row.
            if ($table->load($keys)) {
                $properties = $table->getProperties();
                $properties = ArrayHelper::toObject($properties);

                $this->item[$storedId] = $properties;
            }

        }

        return $this->item[$storedId];
    }
}

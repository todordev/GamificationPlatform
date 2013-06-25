<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification Platform is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class GamificationModelNotification extends JModelItem {
    
    protected $item = array();
    
    /**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState() {
    
        $app       = JFactory::getApplication();
        /** @var $app JSite **/
    
        $value = JFactory::getUser()->id;
        $this->setState('user_id', $value);
        
        $value = $app->input->getInt("id");
        $this->setState($this->getName().'.id', $value);
         
        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    
    }
    
	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($id = null, $userId = null) {
	    
	    if (!$id) {
	        $id = $this->getState($this->getName().'.id');
	    }
	    	
	    if (!$userId) {
	        $userId = $this->getState('user_id');
	    }
	    
	    // If missing ID, I have to return null, because there is no item.
	    if(!$id) {
	        return null;
	    }
	    
	    $storedId = $this->getStoreId($id);
	    
		if (!isset($this->item[$storedId])) {
		    
		    $this->item[$storedId] = null;
		    
			// Get a level row instance.
			$table = JTable::getInstance('Notification', 'GamificationTable');

			// Attempt to load the row.
			if ($table->load($id)) {
				$this->item[$storedId] = $table;
			}
		}

		return $this->item[$storedId];
	}
    
	/**
	 * Set notification as read
	 * @param integer $id
	 * @param integer $userId
	 */
	public function read($id = null, $userId = null) {
	    
	    if (!$id) {
	        $id = $this->getState($this->getName().'.id');
	    }
	    
	    if (!$userId) {
	        $userId = $this->getState('user_id');
	    }
	    
	    $item = $this->getItem($id, $userId);
	    
	    if(!empty($item->id)) {
	        $item->read();
	    }
	    
	}
	
	public function isValid($id = null, $userId = null) {
	    
	    $db     = JFactory::getDbo();
	    $query  = $db->getQuery(true);
	    $query
	        ->select("COUNT(*)")
	        ->from($db->quoteName("#__gfy_notifications") . " AS a")
	        ->where("a.id      = ". (int)$id)
	        ->where("a.user_id = ". (int)$userId);
	    
	    $db->setQuery($query);
	    $result = $db->loadResult();
	        
	    return (!$result) ? false : true;
	}
	
}
<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Gamification notification controller.
 *
 * @package     Gamification Platform
 * @subpackage  Components
  */
class GamificationControllerNotification extends JController {
    
	/**
     * Method to get a model object, loading it if required.
     *
     * @param	string	$name	The model name. Optional.
     * @param	string	$prefix	The class prefix. Optional.
     * @param	array	$config	Configuration array for model. Optional.
     *
     * @return	object	The model.
     * @since	1.5
     */
    public function getModel($name = 'Notification', $prefix = 'GamificationModel', $config = array('ignore_request' => false)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    
    /**
     * Method to load data via AJAX
     */
    public function remove() {
        
        // Get the input
		$app     = JFactory::getApplication();
    
		$itemId  = $app->input->getUint("id");
		$userId  = JFactory::getUser()->id;
		
		// Get the model
		$model   = $this->getModel();
		/** @var $model CrowdFundingModelNotification **/
		
		if (!$model->isValid($itemId, $userId)) {
		     
		    $response = array(
	            "success"  => false,
	            "title"    => JText::_( 'COM_GAMIFICATION_FAIL' ),
	            "text"     => JText::_( 'COM_GAMIFICATION_INVALID_NOTIFICATION' ),
		    );
		
		    echo json_encode($response);
		    JFactory::getApplication()->close();
		}
		
        try {
            
            jimport("gamification.notification");
            $notification = new GamificationNotification($itemId);
            $notification->remove();
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }
        
        $response = array(
        	"success" => true,
            "title"   => JText::_("COM_GAMIFICATION_SUCCESS"),
            "text"    => JText::_("COM_GAMIFICATION_NOTIFICATION_REMOVED_SUCCESSFULLY")
        );
            
        echo json_encode($response);
        
        JFactory::getApplication()->close();
        
    }
    
    
	
}
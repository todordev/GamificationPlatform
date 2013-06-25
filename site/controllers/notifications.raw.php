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
 * Gamification notifications controller.
 *
 * @package     Gamification Platform
 * @subpackage  Components
  */
class GamificationControllerNotifications extends JController {
    
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
    public function getModel($name = 'Notifications', $prefix = 'GamificationModel', $config = array('ignore_request' => false)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    
    /**
     * Method to load data via AJAX
     */
    public function getNumber() {
        
        // Get the input
		$app     = JFactory::getApplication();
    
		// Get the model
		$model = $this->getModel();
		/** @var $model CrowdFundingModelNotifications **/

        try {
            
            $items   = $model->getItems();
            $notRead = $model->countNotRead($items);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_CROWDFUNDING_ERROR_SYSTEM'));
        }
        
        $response = array(
        	"success" => true,
            "data"    => array("results" => $notRead)
        );
            
        echo json_encode($response);
        
        JFactory::getApplication()->close();
        
    }
    
    
	
}
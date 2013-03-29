<?php
/**
 * @package      ITPrism Components
 * @subpackage   SocialCommunity
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * SocialCommunity is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport( 'socialcommunity.controller.admin' );

/**
 * SocialCommunity Projects Controller
 *
 * @package     ITPrism Components
 * @subpackage  SocialCommunity
  */
class SocialCommunityControllerProfiles extends SocialCommunityControllerAdmin {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Profile', $prefix = 'SocialCommunityModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function create() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $msg     = "";
        $link    = "";
        
        // Get form data 
        $pks     = $app->input->post->get('cid', array(), 'array');
        $model   = $this->getModel("Profile", "SocialCommunityModel");
        /** @var $model SocialCommunityModelProfile **/
        
        JArrayHelper::toInteger($pks);
        
        // Check for validation errors.
        if(empty($pks)){
            $this->defaultLink .= "&view=".$this->view_list;
            
            $this->setMessage(JText::_("COM_SOCIALCOMMUNITY_INVALID_ITEM"), "notice");
            $this->setRedirect(JRoute::_($this->defaultLink, false));
            return;
        }
            
        try {
            
            $pks = $model->filterProfiles($pks);
            
            if(!$pks) {
                $this->defaultLink .= "&view=".$this->view_list;
            
                $this->setMessage(JText::_("COM_SOCIALCOMMUNITY_INVALID_ITEM"), "notice");
                $this->setRedirect(JRoute::_($this->defaultLink, false));
                return;
            }
            
            $model->create($pks);
            
        }catch(Exception $e){
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_SOCIALCOMMUNITY_ERROR_SYSTEM'));
        }
        
        $msg  = JText::plural('COM_SOCIALCOMMUNITY_N_PROFILES_CREATED', count(pks));
        $link = $this->defaultLink ."&view=".$this->view_list;
        
        $this->setRedirect(JRoute::_($link, false), $msg);
        
    }
}
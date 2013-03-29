<?php
/**
 * @package      ITPrism Components
 * @subpackage   Gamification
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form');

/**
 * Gamification badge controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Gamification
 * @since		1.6
 */

class GamificationControllerBadge extends ITPrismControllerForm {
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Badge', $prefix = 'GamificationModel', $config = array('ignore_request' => true)) {
        
        $model = parent::getModel($name, $prefix, $config);
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Load the component parameters.
        $params       = JComponentHelper::getParams($this->option);
        
        // Extension parameters
        $model->imagesFolder    = JPATH_SITE . DIRECTORY_SEPARATOR .  JPath::clean( $params->get("images_directory", "images/gamification") );
        return $model;
    }
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $data    = $app->input->post->get('jform', array(), 'array');
        $file    = $app->input->files->get('jform', array(), 'array');
        $file     = JArrayHelper::getValue($file, "image");
        
        $itemId  = JArrayHelper::getValue($data, "id");
        
        $model   = $this->getModel();
        /** @var $model GamificationModelBadge **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Validate the form
        $validData = $model->validate($form, $data);
        
        // Check for errors
        if($validData === false){
            
            $this->defaultLink .= "&view=".$this->view_item.$this->getRedirectToItemAppend($itemId);
            
            $this->setMessage($model->getError(), "notice");
            $this->setRedirect(JRoute::_($this->defaultLink, false));
            return ;
        }
            
        try{
            
            // Upload picture
            if(!empty($file['name'])) {
                jimport("itprism.file.upload");
                $upload   = new ITPrismFileUpload($file);
                $upload->validate();
            
                $dest     = $model->imagesFolder . DIRECTORY_SEPARATOR . substr(JApplication::getHash(time()), 0, 16)."_badge.png";
                
                $upload->upload($dest);
                
                $fileName = JFile::getName($dest);
                $validData["image"] = $fileName;
                
            }
            
            $itemId = $model->save($validData);
                
        } catch (Exception $e){
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        
        }
        
        $link = $this->prepareRedirectLink($itemId);
        $this->setRedirect(JRoute::_($link, false), JText::_('COM_GAMIFICATION_BADGE_SAVED'));
    
    }
    
    public function removeImage() {
        
        JSession::checkToken("get") or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $itemId  = $app->input->get->get('id', 0, 'int');
        
        $model   = $this->getModel();
        /** @var $model GamificationModelRank **/
        
        // Check for errors
        if(!$itemId){
            
            $this->defaultLink .= "&view=".$this->view_item.$this->getRedirectToItemAppend($itemId);
            
            $this->setMessage(JText::_("COM_GAMIFICATION_INVALID_ITEM"), "notice");
            $this->setRedirect(JRoute::_($this->defaultLink, false));
            return ;
        }
            
        try{
            
            $model->removeImage($itemId);
                
        } catch (Exception $e){
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        
        }
        
        $link = $this->prepareRedirectLink($itemId, "id", "badge");
        $this->setRedirect(JRoute::_($link, false), JText::_('COM_GAMIFICATION_IMAGE_DELETED'));
        
    }
    
}
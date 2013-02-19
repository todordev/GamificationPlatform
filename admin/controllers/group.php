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

jimport('gamification.controller.form');

/**
 * Gamification group controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Gamification
 * @since		1.6
 */
class GamificationControllerGroup extends GamificationControllerForm {
    
    /**
     * Save an item
     *
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $msg     = "";
        $link    = "";
        $data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id");
        
        $model   = $this->getModel();
        /** @var $model GamificationModelGroup **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Validate the form
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            $this->defaultLink .= "&view=".$this->view_item."&layout=edit";
            
            if($itemId) {
                $this->defaultLink .= "&id=" . $itemId;
            } 
            
            $this->setMessage($model->getError(), "notice");
            $this->setRedirect(JRoute::_($this->defaultLink, false));
            return;
        }
            
        try{
            $itemId = $model->save($validData);
        }catch(Exception $e){
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }
        
        $msg  = JText::_('COM_GAMIFICATION_GROUP_SAVED');
        $link = $this->prepareRedirectLink($itemId);
        
        $this->setRedirect(JRoute::_($link, false), $msg);
    
    }
    
    protected function prepareRedirectLink($itemId = 0) {
        
        $task = $this->getTask();
        $link = $this->defaultLink;
        
        // Prepare redirection
        switch($task) {
            case "apply":
                $link .= "&view=".$this->view_item."&layout=edit";
                if(!empty($itemId)) {
                    $link .= "&id=" . (int)$itemId; 
                }
                break;
                
            case "save2new":
                $link .= "&view=".$this->view_item."&layout=edit";
                break;
                
            default:
                $link .= "&view=".$this->view_list;
                break;
        }
        
        return $link;
    }
    
}
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

jimport('joomla.application.component.view');

class GamificationViewBadge extends JViewLegacy {
    
    protected $state;
    protected $item;
    protected $form;
    
    protected $documentTitle;
    protected $option;
    
    protected $imagesFolder;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    /**
     * Display the view
     */
    public function display($tpl = null){
        
        $this->state= $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
        // Load the component parameters.
        $params              = JComponentHelper::getParams($this->option);
        $this->imagesFolder  = $params->get("images_directory", "images/gamification");
        
        // Prepare actions, behaviors, scritps and document
        $this->addToolbar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        
        $this->documentTitle = $isNew  ? JText::_('COM_GAMIFICATION_NEW_BADGE')
                                       : JText::_('COM_GAMIFICATION_EDIT_BADGE');

        JToolbarHelper::title($this->documentTitle);
		                             
        JToolbarHelper::apply('badge.apply');
        JToolbarHelper::save2new('badge.save2new');
        JToolbarHelper::save('badge.save');
    
        if(!$isNew){
            JToolbarHelper::cancel('badge.cancel', 'JTOOLBAR_CANCEL');
        }else{
            JToolbarHelper::cancel('badge.cancel', 'JTOOLBAR_CLOSE');
        }
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle($this->documentTitle);
        
		// Add scripts
		JHtml::_('behavior.tooltip');
		JHtml::_('behavior.formvalidation');
		
		$this->document->addScript('../media/'.$this->option.'/js/admin/'.JString::strtolower($this->getName()).'.js');
        
	}

}
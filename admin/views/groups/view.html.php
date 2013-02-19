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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class GamificationViewGroups extends JView {
    
    protected $state;
    protected $items;
    protected $pagination;
    
    public function display($tpl = null){
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        
        // Prepare filters
        $listOrder        = $this->escape($this->state->get('list.ordering'));
        $listDirn         = $this->escape($this->state->get('list.direction'));
        $saveOrder        = (strcmp($listOrder, 'a.ordering') != 0 ) ? false : true;
        
        $this->listOrder  = $listOrder;
        $this->listDirn   = $listDirn;
        $this->saveOrder  = $saveOrder;
        
        // Add submenu
        GamificationHelper::addSubmenu($this->getName());
        
        // Prepare actions
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
        
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_GAMIFICATION_GROUPS_MANAGER'), 'itp-groups');
        JToolBarHelper::addNew('group.add');
        JToolBarHelper::editList('group.edit');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList(JText::_("COM_GAMIFICATION_DELETE_ITEMS_QUESTION"), "groups.delete");
        JToolBarHelper::divider();
        JToolBarHelper::custom('groups.backToDashboard', "itp-dashboard-back", "", JText::_("COM_GAMIFICATION_DASHBOARD"), false);
        
    }
    
	/**
	 * Method to set up the document properties
	 * @return void
	 */
	protected function setDocument() {
		$this->document->setTitle(JText::_('COM_GAMIFICATION_GROUPS_MANAGER'));
	}
    
}
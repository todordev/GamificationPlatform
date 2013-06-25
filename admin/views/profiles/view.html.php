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
jimport('joomla.application.categories');

class GamificationViewProfiles extends JView {
    
    protected $items;
    protected $pagination;
    protected $state;
    
    protected $option;
    
    protected $imagesFolder;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        
        // Prepare filters
        $listOrder        = $this->escape($this->state->get('list.ordering'));
        $listDirn         = $this->escape($this->state->get('list.direction'));
        
        $this->listOrder  = $listOrder;
        $this->listDirn   = $listDirn;
        
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
        JToolBarHelper::title(JText::_('COM_GAMIFICATION_PROFILES'), 'itp-profiles');
//        JToolBarHelper::editList('profile.edit');
//        JToolBarHelper::divider();
        JToolBarHelper::custom('profiles.backToDashboard', "itp-dashboard-back", "", JText::_("COM_GAMIFICATION_DASHBOARD"), false);
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle(JText::_('COM_GAMIFICATION_PROFILES'));

		// Styles
		$this->document->addStyleSheet('../media/'.$this->option.'/css/admin/style.css');
		
		JHtml::_('behavior.tooltip');
	}
    
}
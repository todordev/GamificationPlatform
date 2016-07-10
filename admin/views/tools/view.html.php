<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class GamificationViewTools extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $sidebar;
    protected $option;

    protected $projects = array();
    protected $lists = array();

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        $this->params = JComponentHelper::getParams($this->option);

        JLoader::register('GamificationInstallHelper', GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR . '/helpers/install.php');

        // Load library language
        $lang = JFactory::getLanguage();
        $lang->load('com_gamification.sys', GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR);

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        // Add submenu
        GamificationHelper::addSubmenu($this->getName());
        
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_GAMIFICATION_TOOLS'));

        // Add custom buttons
        $bar = JToolbar::getInstance('toolbar');

        // Go to script manager
        $link = JRoute::_('index.php?option=com_gamification&view=dashboard', false);
        $bar->appendButton('Link', 'dashboard', JText::_('COM_GAMIFICATION_DASHBOARD'), $link);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_GAMIFICATION_TOOLS'));

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('Prism.ui.pnotify');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}

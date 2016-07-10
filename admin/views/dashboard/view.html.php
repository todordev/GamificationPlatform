<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class GamificationViewDashboard extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $option;
    protected $sidebar;

    protected $version;
    protected $prismVersion;
    protected $prismVersionLowerMessage;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->version = new Gamification\Version();

        // Load Prism library version
        if (!class_exists('Prism\\Version')) {
            $this->prismVersion = JText::_('COM_GAMIFICATION_PRISM_LIBRARY_DOWNLOAD');
        } else {
            $prismVersion       = new Prism\Version();
            $this->prismVersion = $prismVersion->getShortVersion();

            if (version_compare($this->prismVersion, $this->version->requiredPrismVersion, '<')) {
                $this->prismVersionLowerMessage = JText::_('COM_GAMIFICATION_PRISM_LIBRARY_LOWER_VERSION');
            }
        }

        // Add submenu
        GamificationHelper::addSubmenu($this->getName());

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
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_GAMIFICATION_DASHBOARD'));

        JToolbarHelper::preferences('com_gamification');
        JToolbarHelper::divider();

        // Help button
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_GAMIFICATION_HELP_URL'));
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_GAMIFICATION_DASHBOARD_ADMINISTRATION'));
    }
}

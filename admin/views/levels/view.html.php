<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class GamificationViewLevels extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var JRegistry
     */
    protected $state;

    protected $items;
    protected $pagination;

    protected $groups;
    protected $ranks;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        JHtml::addIncludePath(JPATH_COMPONENT_SITE . '/helpers/html');

        $this->groups = GamificationHelper::getGroupsOptions();
        $this->ranks  = GamificationHelper::getRanksOptions();

        // Add submenu
        GamificationHelper::addSubmenu($this->getName());

        // Prepare sorting data
        $this->prepareSorting();

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0) ? false : true;

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.title'     => JText::_('COM_GAMIFICATION_TITLE'),
            'a.published' => JText::_('JSTATUS'),
            'a.points'    => JText::_('COM_GAMIFICATION_POINTS'),
            'a.value'     => JText::_('COM_GAMIFICATION_VALUE'),
            'd.title'     => JText::_('COM_GAMIFICATION_RANK'),
            'b.name'      => JText::_('COM_GAMIFICATION_GROUP'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("archived" => false, "trash" => false)), 'value', 'text', $this->state->get('filter.state'), true)
        );

        $ranksOptions = GamificationHelper::getRanksOptions();
        JHtmlSidebar::addFilter(
            JText::_('COM_GAMIFICATION_SELECT_RANK'),
            'filter_rank',
            JHtml::_('select.options', $ranksOptions, 'value', 'text', $this->state->get('filter.rank'), true)
        );

        $groupsOptions = GamificationHelper::getGroupsOptions();
        JHtmlSidebar::addFilter(
            JText::_('COM_GAMIFICATION_SELECT_GROUP'),
            'filter_group',
            JHtml::_('select.options', $groupsOptions, 'value', 'text', $this->state->get('filter.group'), true)
        );

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
        JToolbarHelper::title(JText::_('COM_GAMIFICATION_LEVELS_MANAGER'));
        JToolbarHelper::addNew('level.add');
        JToolbarHelper::editList('level.edit');
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_("COM_GAMIFICATION_DELETE_ITEMS_QUESTION"), "levels.delete");
        JToolbarHelper::divider();
        JToolbarHelper::custom('levels.backToDashboard', "dashboard", "", JText::_("COM_GAMIFICATION_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_GAMIFICATION_LEVELS_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('bootstrap.tooltip');

        $this->document->addScript('../media/' . $this->option . '/js/admin/list.js');
    }
}

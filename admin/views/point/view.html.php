<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\String\String;
use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

class GamificationViewPoint extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Registry
     */
    protected $state;

    protected $item;
    protected $form;

    protected $documentTitle;
    protected $option;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        // Prepare actions, behaviors, scripts and document.
        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);

        $this->documentTitle = $isNew ? JText::_('COM_GAMIFICATION_NEW_POINTS') : JText::_('COM_GAMIFICATION_EDIT_POINTS');

        JToolbarHelper::title($this->documentTitle);

        JToolbarHelper::apply('point.apply');
        JToolbarHelper::save2new('point.save2new');
        JToolbarHelper::save('point.save');

        if (!$isNew) {
            JToolbarHelper::cancel('point.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('point.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Add behaviors
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

        // Add scripts
        $this->document->addScript('../media/' . $this->option . '/js/admin/' . String::strtolower($this->getName()) . '.js');
    }
}

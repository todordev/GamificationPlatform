<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

class GamificationViewActivity extends JViewLegacy
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
    
    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

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
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $this->documentTitle = JText::_('COM_GAMIFICATION_EDIT_ACTIVITY');

        JToolbarHelper::title($this->documentTitle);

        JToolbarHelper::apply('activity.apply');
        JToolbarHelper::save('activity.save');

        JToolbarHelper::cancel('activity.cancel', 'JTOOLBAR_CANCEL');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Add scripts
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}

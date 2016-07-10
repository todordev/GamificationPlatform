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

class GamificationViewBadge extends JViewLegacy
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

    protected $mediaFolder;
    
    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        // Load the component parameters.
        $params             = JComponentHelper::getParams($this->option);
        
        $filesystemHelper   = new Prism\Filesystem\Helper($params);
        $this->mediaFolder  = $filesystemHelper->getMediaFolder();

        // Prepare actions, behaviors, scripts and document
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
        $isNew = ((int)$this->item->id === 0);

        $this->documentTitle = $isNew ? JText::_('COM_GAMIFICATION_NEW_BADGE') : JText::_('COM_GAMIFICATION_EDIT_BADGE');

        JToolbarHelper::title($this->documentTitle);

        JToolbarHelper::apply('badge.apply');
        JToolbarHelper::save2new('badge.save2new');
        JToolbarHelper::save('badge.save');

        if (!$isNew) {
            JToolbarHelper::cancel('badge.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('badge.cancel', 'JTOOLBAR_CLOSE');
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

        // Load language string in JavaScript
        JText::script('COM_GAMIFICATION_DELETE_IMAGE_QUESTION');

        // Add scripts
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}

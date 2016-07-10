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

class GamificationViewNotifications extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $params;
    protected $pagination;

    protected $option;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->items  = $this->get('Items');
        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        parent::display($tpl);
    }
}

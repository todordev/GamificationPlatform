<?php
/**
 * @package      Gamification
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

class GamificationViewNotifications extends JViewLegacy {
    
	protected $state;
	protected $items;
	protected $params;
	protected $pagination;
	
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null) {
        
        // Initialise variables
        $this->items      = $this->get('Items');
		$this->state	  = $this->get('State');
		$this->params	  = $this->state->get('params');
        
        parent::display($tpl);
    }
    
}
<?php
/**
 * @package      Gamification Platform
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Default Controller
 *
 * @package      Gamification Platform
 * @subpackage   Component
 */
class GamificationController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        $viewName = $this->input->getCmd('view', 'dashboard');
        $this->input->set("view", $viewName);

        parent::display();

        return $this;
    }
}

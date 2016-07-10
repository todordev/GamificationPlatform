<?php
/**
 * @package      Gamification Platform
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

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
        $this->input->set('view', $viewName);

        JHtml::stylesheet('com_gamification/backend.style.css', false, true, false);

        parent::display();

        return $this;
    }
}

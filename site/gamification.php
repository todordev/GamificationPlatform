<?php
/**
 * @package      Gamification Platform
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Gamification.init');

$controller = JControllerLegacy::getInstance('Gamification');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();

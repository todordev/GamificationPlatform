<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Controller\Admin;

// no direct access
defined('_JEXEC') or die;

/**
 * Gamification activities controller
 *
 * @package     Gamification
 * @package     Components
 */
class GamificationControllerActivities extends Admin
{
    public function getModel($name = 'Activity', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}

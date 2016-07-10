<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Controller\Admin;


// No direct access
defined('_JEXEC') or die;

/**
 * Gamification levels controller class.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class GamificationControllerLevels extends Admin
{
    public function getModel($name = 'Level', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}

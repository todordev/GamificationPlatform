<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Gamification achievements controller class.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class GamificationControllerAchievements extends Prism\Controller\Admin
{
    /**
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return GamificationModelAchievement
     */
    public function getModel($name = 'Achievement', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}

<?php
/**
 * @package         Gamification\User
 * @subpackage      Achievements
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Achievement;

use Gamification\Achievement\Achievement as BasicAchievement;

defined('JPATH_PLATFORM') or die;

/**
 * Achievement manager interface.
 *
 * @package         Gamification\User
 * @subpackage      Achievements
 */
interface AchievementManagerInterface
{
    public function setAchievement(BasicAchievement $achievement);
    public function accomplish($context, $userId, array $options = array());
}

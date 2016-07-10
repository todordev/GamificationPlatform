<?php
/**
 * @package         Gamification\User
 * @subpackage      Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Level;

use Gamification\Level\Level as BasicLevel;

defined('JPATH_PLATFORM') or die;

/**
 * Level manager interface.
 *
 * @package         Gamification\User
 * @subpackage      Levels
 */
interface LevelManagerInterface
{
    public function setLevel(BasicLevel $level);
    public function levelUp($context, $userId, array $options = array());
}

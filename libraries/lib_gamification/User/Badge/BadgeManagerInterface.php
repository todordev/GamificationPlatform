<?php
/**
 * @package         Gamification\User
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Badge;

use Gamification\Badge\Badge as BasicBadge;

defined('JPATH_PLATFORM') or die;

/**
 * Badge manager interface.
 *
 * @package         Gamification\User
 * @subpackage      Badges
 */
interface BadgeManagerInterface
{
    public function setBadge(BasicBadge $badge);
    public function give($context, $userId, array $options = array());
}

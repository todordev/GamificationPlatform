<?php
/**
 * @package         Gamification\User
 * @subpackage      Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Rank;

use Gamification\Rank\Rank as BasicRank;

defined('JPATH_PLATFORM') or die;

/**
 * Rank manager interface.
 *
 * @package         Gamification\User
 * @subpackage      Ranks
 */
interface RankManagerInterface
{
    public function setRank(BasicRank $rank);
    public function give($context, $userId, array $options = array());
}

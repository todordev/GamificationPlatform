<?php
/**
 * @package         Gamification\User
 * @subpackage      Rewards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Reward;

use Gamification\Reward\Reward as BasicReward;

defined('JPATH_PLATFORM') or die;

/**
 * Reward manager interface.
 *
 * @package         Gamification\User
 * @subpackage      Rewards
 */
interface RewardManagerInterface
{
    public function setReward(BasicReward $reward);
    public function give($context, $userId, array $options = array());
}

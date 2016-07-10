<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Rewards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Reward;

use Gamification\Reward\Reward as BasicReward;
use Prism\Observer\Observable;
use Gamification\Profile\Profile;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user reward based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Rewards
 */
class RewardManager extends Observable implements RewardManagerInterface
{
    /**
     * @var BasicReward
     */
    protected $reward;

    public function setReward(BasicReward $reward)
    {
        $this->reward = $reward;
    }

    /**
     * Change user reward to higher one.
     *
     * <code>
     * $context = "com_user.registration";
     *
     * $keys = array(
     *     "id" => 1,
     *     "group_id" => 2
     * );
     *
     * // Create user reward object based.
     * $reward  = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($keys);
     *
     * $rewardManager = new Gamification\User\Reward\RewardManager(\JFactory::getDbo());
     * $rewardManager->setReward($reward);
     *
     * if ($rewardManager->give($context, $userId, $options)) {
     * // ...
     * }
     * </code>
     *
     * @param string $context
     * @param int $userId
     * @param array $options
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return null|Reward
     */
    public function give($context, $userId, array $options = array())
    {
        if (!($this->reward instanceof BasicReward)) {
            throw new \UnexpectedValueException('It is missing user reward object.');
        }

        // Check if this reward already exists in user profile.
        $userProfile = new Profile($this->db);
        $rewardReceived = $userProfile->isRewardReceived($this->reward, $userId);

        if (!$rewardReceived) {
            $data = array(
                'user_id'        => $userId,
                'group_id'       => $this->reward->getGroupId(),
                'reward_id'      => $this->reward->getId()
            );

            // Create user reward.
            $userReward = new Reward(\JFactory::getDbo());
            $userReward->bind($data);

            // Implement JObservableInterface: Pre-processing by observers
            $this->observers->update('onBeforeGiveReward', array($context, &$userReward, &$options));

            $userReward->store();

            // Implement JObservableInterface: Post-processing by observers
            $this->observers->update('onAfterGiveReward', array($context, &$userReward, &$options));

            return $userReward;
        }

        return null;
    }
}

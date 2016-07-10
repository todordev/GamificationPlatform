<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Reward
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Reward;

use Gamification\User\PointsSeeker;
use Gamification\Reward\Reward as BasicReward;
use Gamification\Reward\Rewards as BasicRewards;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user reward based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Reward
 */
class RewardPointsSeeker extends PointsSeeker
{
    /**
     * Find the reward that has to be reached by the user.
     *
     * <code>
     * $keys   = array(
     *    'user_id' => 1,
     *    'points_id' => 2
     * );
     * $points = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $rewardSeeker = new Gamification\User\Reward\RewardPointsSeeker(\JFactory::getDbo());
     * $rewardSeeker->setUserPoints($points);
     *
     * $newReward = $rewardSeeker->find();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return null|BasicReward
     */
    public function find()
    {
        // Get basic rewards based on specific points.
        $options = array(
            'points_id' => (int)$this->userPoints->getPointsId(),
            'state'     => (int)Constants::PUBLISHED
        );

        $rewards = new BasicRewards($this->db);
        $rewards->load($options);

        $results = $rewards->toArray();
        /** @var array $results */

        $rewardData = array();
        for ($i = 0, $max = count($results); $i < $max; $i++) {
            // Get current item
            $current = array_key_exists($i, $results) ? $results[$i] : array();
            /** @var $current array */

            // Get next item
            $n    = $i + 1;
            $next = array_key_exists($n, $results) ? $results[$n] : array();
            /** @var $next array */

            if (count($next) > 0) {
                // Check for coincidence with next item
                if ((int)$next['points_number'] === (int)$this->userPoints->getPointsNumber()) {
                    $rewardData = $next;
                    break;
                }

                // Check for coincidence with current item
                if (((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber())
                    and
                    ((int)$next['points_number'] > (int)$this->userPoints->getPointsNumber())
                ) {
                    $rewardData = $current;
                    break;
                }

            } else { // If there is not next item, we compare with last (current).

                if ((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber()) {
                    $rewardData = $current;
                    break;
                }
            }
        }

        // Create a reward object.
        $reward = null;
        if (count($rewardData) > 0) {
            $reward = new BasicReward($this->db);
            $reward->bind($rewardData);
        }

        return $reward;
    }
}

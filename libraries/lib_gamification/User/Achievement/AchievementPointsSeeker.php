<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Achievement
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Achievement;

use Gamification\User\PointsSeeker;
use Gamification\Achievement\Achievement as BasicAchievement;
use Gamification\Achievement\Achievements as BasicAchievements;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user achievement based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Achievement
 */
class AchievementPointsSeeker extends PointsSeeker
{
    /**
     * Find the achievement that has to be reached by the user.
     *
     * <code>
     * $keys   = array(
     *    'user_id' => 1,
     *    'points_id' => 2
     * );
     * $points = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $achievementSeeker = new Gamification\User\Achievement\AchievementPointsSeeker(\JFactory::getDbo());
     * $achievementSeeker->setUserPoints($points);
     *
     * $newAchievement = $achievementSeeker->find();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return null|BasicAchievement
     */
    public function find()
    {
        // Get basic achievements based on specific points.
        $options = array(
            'points_id' => (int)$this->userPoints->getPointsId(),
            'state'     => (int)Constants::PUBLISHED
        );

        $achievements = new BasicAchievements($this->db);
        $achievements->load($options);

        $results = $achievements->toArray();
        /** @var array $results */

        $achievementData = array();
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
                    $achievementData = $next;
                    break;
                }

                // Check for coincidence with current item
                if (((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber())
                    and
                    ((int)$next['points_number'] > (int)$this->userPoints->getPointsNumber())
                ) {
                    $achievementData = $current;
                    break;
                }

            } else { // If there is not next item, we compare with last (current).

                if ((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber()) {
                    $achievementData = $current;
                    break;
                }
            }
        }

        // Create a achievement object.
        $achievement = null;
        if (count($achievementData) > 0) {
            $achievement = new BasicAchievement($this->db);
            $achievement->bind($achievementData);
        }

        return $achievement;
    }
}

<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Rank
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Rank;

use Gamification\User\PointsSeeker;
use Gamification\Rank\Rank as BasicRank;
use Gamification\Rank\Ranks as BasicRanks;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user rank based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Rank
 */
class RankPointsSeeker extends PointsSeeker
{
    /**
     * Find the rank that has to be reached by the user.
     *
     * <code>
     * $keys   = array(
     *    'user_id' => 1,
     *    'points_id' => 2
     * );
     * $points = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $rankSeeker = new Gamification\User\Rank\RankPointsSeeker(\JFactory::getDbo());
     * $rankSeeker->setUserPoints($points);
     *
     * $newRank = $rankSeeker->find();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return null|BasicRank
     */
    public function find()
    {
        // Get basic ranks based on specific points.
        $options = array(
            'points_id' => (int)$this->userPoints->getPointsId(),
            'state'     => (int)Constants::PUBLISHED
        );

        $ranks = new BasicRanks($this->db);
        $ranks->load($options);

        $results = $ranks->toArray();
        /** @var array $results */

        $rankData = array();
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
                    $rankData = $next;
                    break;
                }

                // Check for coincidence with current item
                if (((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber())
                    and
                    ((int)$next['points_number'] > (int)$this->userPoints->getPointsNumber())
                ) {
                    $rankData = $current;
                    break;
                }

            } else { // If there is not next item, we compare with last (current).

                if ((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber()) {
                    $rankData = $current;
                    break;
                }
            }
        }

        // Create a rank object.
        $rank = null;
        if (count($rankData) > 0) {
            $rank = new BasicRank($this->db);
            $rank->bind($rankData);
        }

        return $rank;
    }
}

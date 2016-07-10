<?php
/**
 * @package         Gamification\User
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Badge;

use Gamification\User\PointsSeeker;
use Gamification\Badge\Badge as BasicBadge;
use Gamification\Badge\Badges as BasicBadges;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods for searching a badge
 * that should be given to a user.
 *
 * @package         Gamification\User
 * @subpackage      Badges
 */
class BadgePointsSeeker extends PointsSeeker
{
    /**
     * Find a badge that has to be given to the user.
     *
     * <code>
     * $keys   = array(
     *    'user_id' => 1,
     *    'points_id' => 2
     * );
     * $points = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $badgeSeeker = new Gamification\User\Badge\BadgePointsSeeker(\JFactory::getDbo());
     * $badgeSeeker->setUserPoints($points);
     *
     * $newBadge = $badgeSeeker->find();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return null|BasicBadge
     */
    public function find()
    {
        // Get basic badges based on specific points.
        $options = array(
            'points_id' => (int)$this->userPoints->getPointsId(),
            'state'     => (int)Constants::PUBLISHED
        );
        
        $badges = new BasicBadges($this->db);
        $badges->load($options);

        $results = $badges->toArray();
        /** @var array $results */

        $badgeData = array();
        for ($i = 0, $max = count($results); $i < $max; $i++) {
            // Get current item
            $current = array_key_exists($i, $results) ? $results[$i] : array();
            /** @var $current array */

            // Get next item
            $n    = $i + 1;
            $next = array_key_exists($n, $results) ? $results[$n] : array();
            /** @var $next array */

            if (count($next) > 0) {
                // Check for coincidence with next item.
                if ((int)$next['points_number'] === $this->userPoints->getPointsNumber()) {
                    $badgeData = $next;
                    break;
                }

                // Check for coincidence with current item.
                if (((int)$current['points_number'] <= $this->userPoints->getPointsNumber())
                    and
                    ((int)$next['points_number'] > $this->userPoints->getPointsNumber())
                ) {
                    $badgeData = $current;
                    break;
                }
            } else { // If there is not next item, we compare it with last (current one).
                if ((int)$current['points_number'] <= $this->userPoints->getPointsNumber()) {
                    $badgeData = $current;
                    break;
                }
            }
        }

        // Create a badge object.
        $badge = null;
        if (count($badgeData) > 0) {
            $badge = new BasicBadge($this->db);
            $badge->bind($badgeData);
        }

        return $badge;
    }
}

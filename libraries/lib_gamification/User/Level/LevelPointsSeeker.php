<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Level;

use Gamification\User\PointsSeeker;
use Gamification\Level\Level as BasicLevel;
use Gamification\Level\Levels as BasicLevels;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user level based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Levels
 */
class LevelPointsSeeker extends PointsSeeker
{
    /**
     * Find the level that has to be reached by the user.
     *
     * <code>
     * $keys   = array(
     *    'user_id' => 1,
     *    'points_id' => 2
     * );
     * $points = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $levelSeeker = new Gamification\User\Level\LevelPointsSeeker(\JFactory::getDbo());
     * $levelSeeker->setUserPoints($points);
     *
     * $newLevel = $levelSeeker->find();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return null|BasicLevel
     */
    public function find()
    {
        // Get basic levels based on specific points.
        $options = array(
            'points_id'       => (int)$this->userPoints->getPointsId(),
            'state'           => (int)Constants::PUBLISHED,
            'order_column'    => 'a.points_number',
            'order_direction' => 'ASC',
        );

        $levels = new BasicLevels($this->db);
        $levels->load($options);

        $results = $levels->toArray();
        /** @var array $results */

        $levelData = array();
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
                    $levelData = $next;
                    break;
                }

                // Check for coincidence with current item
                if (((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber())
                    and
                    ((int)$next['points_number'] > (int)$this->userPoints->getPointsNumber())
                ) {
                    $levelData = $current;
                    break;
                }
            } else { // If there is not next item, we compare with last (current).
                if ((int)$current['points_number'] <= (int)$this->userPoints->getPointsNumber()) {
                    $levelData = $current;
                    break;
                }
            }
        }

        // Create a level object.
        $level = null;
        if (count($levelData) > 0) {
            $level = new BasicLevel($this->db);
            $level->bind($levelData);
        }
        
        return $level;
    }
}

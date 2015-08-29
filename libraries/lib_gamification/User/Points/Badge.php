<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Points;

use Gamification\User;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user badges based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Badges
 */
class Badge extends User\Badge
{
    /**
     * This is user points object.
     *
     * @var User\Points
     */
    protected $userPoints;

    /**
     * Set the user points to the object.
     *
     * <code>
     * $keys = array(
     *       "user_id" => 1,
     *       "group_id" => 2
     * );
     * 
     * // Get user points.
     * $userPoints  = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create user badges object based on points.
     * $badge  = new Gamification\User\Points\Badge(\JFactory::getDbo());
     * $badge->setUserPoints($userPoints);
     * </code>
     *
     * @param User\Points $userPoints
     */
    public function setUserPoints(User\Points $userPoints)
    {
        $this->userPoints = $userPoints;
    }

    /**
     * Give a new badge.
     *
     * <code>
     * $options = array(
     *    "context" = "com_user.registration"
     * );
     *
     * $keys = array(
     *       "user_id" => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points.
     * $userPoints  = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create user badges object based on points.
     * $badge  = new Gamification\User\Points\Badge(\JFactory::getDbo());
     * $badge->load($keys);
     *
     * $badge->setUserPoints($userPoints);
     * if ($badge->giveBadge($options)) {
     * // ...
     * }
     * </code>
     *
     * @param array $options
     *
     * @return null|int NULL if badge not given; Badge ID if a badge has been given.
     */
    public function giveBadge(array $options = array())
    {
        // Get next badge
        $actualBadge = $this->findActualBadge();

        // Check for existing badge
        if (!empty($actualBadge["badge_id"])) {
            $query = $this->db->getQuery(true);
            $query
                ->select("COUNT(*)")
                ->from($this->db->quoteName("#__gfy_userbadges", "a"))
                ->where("a.badge_id = ". (int)$actualBadge["badge_id"])
                ->where("a.user_id = ". (int)$this->userPoints->getUserId())
                ->where("a.group_id = ". (int)$this->userPoints->getGroupId());

            $this->db->setQuery($query, 0, 1);
            $badgeExists = (bool)$this->db->loadResult();

            if (!$badgeExists) {

                $keys = array(
                    "badge_id" => $actualBadge["badge_id"],
                    "group_id" => $this->userPoints->getGroupId(),
                    "user_id" => $this->userPoints->getUserId(),
                );

                $this->bind($keys);

                // Implement JObservableInterface: Pre-processing by observers
                $this->observers->update('onBeforeGiveBadge', array(&$this, &$options));

                $this->store();
                $this->load($keys);

                // Implement JObservableInterface: Post-processing by observers
                $this->observers->update('onAfterGiveBadge', array(&$this, &$options));

                return true;
            }
        }

        return false;
    }

    /**
     * Find a badge that has to be given to the user.
     *
     * @return null|array
     */
    protected function findActualBadge()
    {
        // Get all badges for given points.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id AS badge_id, a.title, a.points")
            ->from($this->db->quoteName("#__gfy_badges", "a"))
            ->where("a.points_id = " . (int)$this->userPoints->getPointsId())
            ->where("a.published = " . (int)Constants::PUBLISHED);

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        $badge = null;
        for ($i = 0, $max = count($results); $i < $max; $i++) {

            // Get current item
            $current = (isset($results[$i])) ? $results[$i] : null;
            /** @var $current object */

            // Get next item
            $n    = abs($i + 1);
            $next = (isset($results[$n])) ? $results[$n] : null;
            /** @var $next object */

            if (!empty($next)) {

                // Check for coincidence with next item.
                if ($next["points"] == $this->userPoints->getPoints()) {
                    $badge = $next;
                    break;
                }

                // Check for coincidence with current item.
                if (($current["points"] <= $this->userPoints->getPoints())
                    and
                    ($next["points"] > $this->userPoints->getPoints())
                ) {
                    $badge = $current;
                    break;
                }

            } else { // If there is not next item, we compare it with last (current one).

                if ($current["points"] <= $this->userPoints->getPoints()) {
                    $badge = $current;
                    break;
                }

            }
        }

        return $badge;
    }
}

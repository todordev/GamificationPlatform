<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Points;

use Gamification\User;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user level based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Levels
 */
class Level extends User\Level
{
    /**
     * This is user points object.
     *
     * @var \Gamification\User\Points
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
     * // Get user points
     * $userPoints  = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create user level object, which is based on points.
     * $level  = new Gamification\User\Points\Level(\JFactory::getDbo(), $keys);
     * $level->setUserPoints($userPoints);
     * </code>
     *
     * @param User\Points $userPoints
     */
    public function setUserPoints(User\Points $userPoints)
    {
        $this->userPoints = $userPoints;
    }

    /**
     * Update level to new one.
     *
     * <code>
     * $options = array(
     *    "context" = "com_user.registration"
     * );
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points
     * $userPoints  = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create user level object, which is based on points.
     * $level  = new Gamification\User\Points\Level(\JFactory::getDbo(), $keys);
     * $level->setUserPoints($userPoints);
     *
     * if($level->levelUp()) {
     *  //....
     * }
     * </code>
     *
     * @param array $options
     *
     * @return boolean Return true if level up or false if not.
     */
    public function levelUp(array $options = array())
    {
        // Get next level
        $actualLevelId = $this->findActualLevelId();

        if (!empty($actualLevelId) and ($actualLevelId != $this->level_id)) {

            // Load data
            $keys = array(
                "user_id"  => $this->userPoints->getUserId(),
                "group_id" => $this->userPoints->getGroupId(),
                "level_id" => $actualLevelId,
            );

            $this->bind($keys);

            // Implement JObservableInterface: Pre-processing by observers
            $this->observers->update('onBeforeLevelUp', array(&$this, &$options));

            $this->store();

            $this->load($keys);

            // Implement JObservableInterface: Post-processing by observers
            $this->observers->update('onAfterLevelUp', array(&$this, &$options));

            return true;
        }

        return false;
    }

    /**
     * Find the level that has to be reached by the user.
     *
     * @return null|integer
     */
    protected function findActualLevelId()
    {
        // Get all levels
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.points")
            ->from($this->db->quoteName("#__gfy_levels", "a"))
            ->where("a.points_id = " . (int)$this->userPoints->getPointsId());

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        $levelId = null;
        for ($i = 0, $max = count($results); $i < $max; $i++) {

            // Get current item
            $current = (isset($results[$i])) ? $results[$i] : null;
            /** @var $current object */

            // Get next item
            $n    = abs($i + 1);
            $next = (isset($results[$n])) ? $results[$n] : null;
            /** @var $next object */

            if (!empty($next)) {

                // Check for coincidence with next item
                if ($this->userPoints->getPoints() == $next->points) {
                    $levelId = $next->id;
                    break;
                }

                // Check for coincidence with current item
                if (($this->userPoints->getPoints() >= $current->points)
                    and
                    ($this->userPoints->getPoints() < $next->points)
                ) {
                    $levelId = $current->id;
                    break;
                }

            } else { // If there is not next item, we compare with last (current).

                if ($this->userPoints->getPoints() >= $current->points) {
                    $levelId = $current->id;
                    break;
                }

            }

        }

        return $levelId;
    }

    /**
     * Create a record to the database, adding first level.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points
     * $userPoints  = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create user level object, which is based on points.
     * $level  = new Gamification\User\Points\Level(\JFactory::getDbo(), $keys);
     * $level->load($keys);
     *
     * if(!$level->getId()) {
     *    $data = array(
     *        "user_id"  => 1,
     *        "group_id" => 2
     *    );
     *
     *    $level->startLeveling($data);
     * }
     * </code>
     *
     * @param array $data
     */
    public function startLeveling(array $data = array())
    {
        if (empty($data["user_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID"));
        }

        if (empty($data["group_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_GROUP_ID"));
        }

        if (empty($data["level_id"])) {
            $data["level_id"] = $this->findActualLevelId();
        }

        $this->bind($data);
        $this->store();

        // Load data
        $keys = array(
            "user_id"  => $data["user_id"],
            "group_id" => $data["group_id"]
        );

        $this->load($keys);
    }
}

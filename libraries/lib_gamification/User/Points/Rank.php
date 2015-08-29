<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Rank
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Points;

use Gamification\User;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user rank based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Rank
 */
class Rank extends User\Rank
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
     * // Create user rank object based on points.
     * $ranks  = new Gamification\User\Points\Rank(\JFactory::getDbo(), $keys);
     * $ranks->setUserPoints($userPoints);
     * </code>
     *
     * @param User\Points $userPoints
     */
    public function setUserPoints(User\Points $userPoints)
    {
        $this->userPoints = $userPoints;
    }

    /**
     * Update rank to new one.
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
     * // Get user points.
     * $userPoints  = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create user rank object based on points.
     * $ranks  = new Gamification\User\Points\Rank(\JFactory::getDbo(), $keys);
     * $ranks->setUserPoints($userPoints);
     *
     * if($rank->giveRank()) {
     *  //....
     * }
     * </code>
     *
     * @param array $options
     *
     * @return boolean TRUE if rank has been given. FALSE if rank has not been given.
     */
    public function giveRank(array $options = array())
    {
        // Get next rank
        $actualRankId = $this->findActualRankId();

        if (!empty($actualRankId) and ($actualRankId != $this->rank_id)) {

            $keys = array(
                "rank_id"  => $actualRankId,
                "user_id"  => $this->userPoints->getUserId(),
                "group_id" => $this->userPoints->getGroupId()
            );

            $this->bind($keys);

            // Implement JObservableInterface: Pre-processing by observers
            $this->observers->update('onBeforeGiveRank', array(&$this, &$options));

            $this->store();

            $this->load($keys);

            // Implement JObservableInterface: Post-processing by observers
            $this->observers->update('onAfterGiveRank', array(&$this, &$options));

            return true;
        }

        return false;
    }

    /**
     * Find a rank that actual have to be.
     *
     * @return null|int Rank ID
     */
    protected function findActualRankId()
    {
        // Get all ranks
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.points")
            ->from($this->db->quoteName("#__gfy_ranks", "a"))
            ->where("a.points_id = " . (int)$this->userPoints->getPointsId());

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        $rankId = null;
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
                if ($next["points"] == $this->userPoints->getPoints()) {
                    $rankId = $next["id"];
                    break;
                }

                // Check for coincidence with current item
                if (($current["points"] <= $this->userPoints->getPoints())
                    and
                    ($next["points"] > $this->userPoints->getPoints())
                ) {
                    $rankId = $current["id"];
                    break;
                }

            } else { // If there is not next item, we compare it with last (current one).

                if ($current["points"] <= $this->userPoints->getPoints()) {
                    $rankId = $current["id"];
                    break;
                }
            }
        }

        return $rankId;
    }

    /**
     * Create a record to the database, adding first rank based on points.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points.
     * $userPoints  = Gamification\User\Points::getInstance(\JFactory::getDbo(), $keys);
     *
     * // Create user rank object based on points.
     * $rank  = new Gamification\User\Points\Rank(\JFactory::getDbo(), );
     * $rank->load($keys);
     * 
     * if(!$rank->getId()) {
     *      $data = array(
     *           "user_id"  => $userId,
     *           "group_id" => $groupId
     *      );
     *
     *      $rank->startRanking($data);
     * }
     * </code>
     *
     * @param array $data
     */
    public function startRanking(array $data = array())
    {
        if (empty($data["user_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID"));
        }

        if (empty($data["group_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_GROUP_ID"));
        }

        if (empty($data["rank_id"])) {
            $data["rank_id"] = $this->findActualRankId();
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

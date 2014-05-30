<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('gamification.interface.table');
jimport('gamification.userpoints');
jimport('gamification.userrank');

/**
 * This class contains methods that manage user rank based on points.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationUserRankPoints extends GamificationUserRank
{
    /**
     * This is user points object.
     *
     * @var GamificationUserPoints
     */
    protected $userPoints;

    public static $instances = array();

    /**
     * Create and initialize user rank.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id" => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points
     * $userPoints  = GamificationUserPoints::getInstance($keys);
     *
     * // Get user rank.
     * $rank       = GamificationUserRankPoints::getInstance($userPoints);
     *
     * </code>
     *
     * @param  GamificationUserPoints $userPoints
     *
     * @return null|GamificationUserRankPoints
     */
    public static function getInstance(GamificationUserPoints $userPoints)
    {
        // Prepare keys
        if ($userPoints instanceof GamificationUserPoints) {

            $keys = array(
                "user_id"  => $userPoints->user_id,
                "group_id" => $userPoints->group_id
            );

            $index = md5($userPoints->user_id . ":" . $userPoints->group_id);

        } else {
            return null;
        }

        if (empty(self::$instances[$index])) {
            $item = new GamificationUserRankPoints($keys);
            $item->setUserPoints($userPoints);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Set the user points to the object.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id" => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points
     * $userPoints  = GamificationUserPoints::getInstance($keys);
     *
     * // Create user rank object, which is based on points.
     * $rank  = new GamificationUserRankPoints($keys);
     * $rank->setUserPoints($userPoints);
     *
     * </code>
     *
     * @param GamificationUserPoints $userPoints
     */
    public function setUserPoints($userPoints)
    {
        $this->userPoints = $userPoints;
    }

    /**
     * Update rank to new one.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points
     * $userPoints  = GamificationUserPoints::getInstance($keys);
     *
     * // Create user rank object, which is based on points.
     * $rank        = GamificationUserRankPoints::getInstance($userPoints);
     *
     * $newRank     = $rank->giveRank();
     * if($newRank) {
     *  //....
     * }
     *
     * </code>
     *
     * @return boolean TRUE if we are giving a new rank. FALSE if we do not giving a new rank.
     */
    public function giveRank()
    {
        // Get next rank
        $actualRankId = $this->findActualRankId();

        if (!empty($actualRankId) and ($actualRankId != $this->rank_id)) {
            $this->setRankId($actualRankId);
            $this->store();

            // Load the data for the new rank
            $keys = array(
                "user_id"  => $this->userPoints->user_id,
                "group_id" => $this->userPoints->group_id
            );
            $this->load($keys);

            return true;
        }

        return false;
    }

    /**
     * Find a rank that actual have to be.
     *
     * @return null:integer
     */
    protected function findActualRankId()
    {
        // Get all ranks
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.points")
            ->from($this->db->quoteName("#__gfy_ranks") . " AS a")
            ->where("a.points_id = " . (int)$this->userPoints->points_id);

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

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
                if ($this->userPoints->points == $next->points) {
                    $rankId = $next->id;
                    break;
                }

                // Check for coincidence with current item
                if (($this->userPoints->points >= $current->points)
                    and
                    ($this->userPoints->points < $next->points)
                ) {

                    $rankId = $current->id;
                    break;
                }

            } else { // If there is not next item, we compare with last (current).

                if ($this->userPoints->points >= $current->points) {

                    $rankId = $current->id;
                    break;
                }

            }

        }

        return $rankId;
    }

    /**
     * Create a record to the database, adding first rank.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * // Get user points
     * $userPoints  = GamificationUserPoints::getInstance($keys);
     *
     * // Create user rank object, which is based on points.
     * $rank        = GamificationUserRankPoints::getInstance($userPoints);
     *
     * if(!$rank->id) {
     *      $data = array(
     *           "user_id"  => $userPoints->user_id,
     *           "group_id" => $userPoints->group_id
     *      );
     *
     *      $rank->startRanking($data);
     * }
     *
     * </code>
     *
     * @param array $data
     *
     */
    public function startRanking($data)
    {
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

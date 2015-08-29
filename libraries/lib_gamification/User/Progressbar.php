<?php
/**
 * @package         Gamification\User
 * @subpackage      Progressbars
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Gamification\Mechanic\PointsInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user progress.
 *
 * @package         Gamification\User
 * @subpackage      Progressbars
 */
class Progressbar
{
    /**
     * This is the number of points needed to be reached this level.
     *
     * @var Points
     */
    protected $points;

    /**
     * Game mechanic - level, badge or rank.
     * @var PointsInterface
     */
    protected $currentUnit;

    protected $nextUnit;

    /**
     * This is the percentages of completed progress.
     *
     * @var integer
     */
    protected $percentage;

    /**
     * These are the percentages that remain to the next unit.
     *
     * @var integer
     */
    protected $percentNext;

    /**
     * It is the game mechanic, on which is based the progress.
     * It could be level, rank or badge.
     *
     * @var string
     */
    protected $gameMechanic;

    /**
     * Database driver.
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object and load data.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar->build($gameMechanic);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param Points $points
     */
    public function __construct(\JDatabaseDriver $db, Points $points)
    {
        $this->db           = $db;
        $this->points       = $points;
    }

    /**
     * Initialize the progress data.
     * 
     * @param string $gameMechanic  A game mechanic - levels, ranks, badges,...
     */
    public function build($gameMechanic)
    {
        $keys = array(
            "user_id"  => $this->points->getUserId(),
            "group_id" => $this->points->getGroupId()
        );

        switch ($gameMechanic) {

            case "levels":
                $this->prepareLevels($keys);
                break;

            case "ranks":
                $this->prepareRanks($keys);
                break;

            case "badges":
                $this->prepareBadges($keys);
                break;
        }

        $this->gameMechanic = $gameMechanic;
    }

    /**
     * Prepare current and next level.
     *
     * @param array $keys
     */
    protected function prepareLevels($keys)
    {
        $this->currentUnit = Level::getInstance($this->db, $keys);

        $userPoints = $this->points->getPoints();

        // Get all units
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.points, a.value, a.published, a.points_id, a.rank_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_levels", "a"))
            ->where("a.points_id = " . (int)$this->points->getPointsId())
            ->where("a.points > " . (int)$userPoints);

        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadObject();

        if (!empty($result)) {

            $this->nextUnit    = new \Gamification\Level\Level($this->db);
            $this->nextUnit->bind($result);

            $this->percentage  = $this->calculatePercentage($userPoints, $this->nextUnit->getPoints());
            $this->percentNext = 100 - $this->percentage;

        } else {
            $this->percentage  = 100;
            $this->percentNext = 100;
        }
    }

    /**
     * Prepare current and next ranks.
     *
     * @param array $keys
     */
    protected function prepareRanks($keys)
    {
        $this->currentUnit = Rank::getInstance($this->db, $keys);

        $userPoints = $this->points->getPoints();

        // Get all units
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.points, a.image, a.published, a.points_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_ranks", "a"))
            ->where("a.points_id = " . (int)$this->points->getPointsId())
            ->where("a.published = 1")
            ->where("a.points > " . (int)$userPoints);

        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadObject();

        if (!empty($result)) {
            $this->nextUnit    = new \Gamification\Rank\Rank($this->db);
            $this->nextUnit->bind($result);

            $this->percentage  = $this->calculatePercentage($userPoints, $this->nextUnit->getPoints());
            $this->percentNext = 100 - $this->percentage;

        } else {
            $this->percentage  = 100;
            $this->percentNext = 100;
        }
    }

    /**
     * Prepare current and next badges.
     *
     * @param array $keys
     */
    protected function prepareBadges($keys)
    {
        $this->currentUnit = Badge::getInstance($this->db, $keys);

        $userPoints = $this->points->getPoints();

        // Get all units
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.points, a.image, a.note, a.published, a.points_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_badges", "a"))
            ->where("a.points_id = " . (int)$this->points->getPointsId())
            ->where("a.published = 1")
            ->where("a.points > " . (int)$userPoints);

        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadObject();

        if (!empty($result)) {
            $this->nextUnit    = new \Gamification\Badge\Badge($this->db);
            $this->nextUnit->bind($result);

            $this->percentage  = $this->calculatePercentage($userPoints, $this->nextUnit->getPoints());
            $this->percentNext = 100 - $this->percentage;

        } else {
            $this->percentage  = 100;
            $this->percentNext = 100;
        }
    }

    protected function calculatePercentage($currentValue, $nextValue)
    {
        $percent = ($currentValue / $nextValue) * 100;
        return abs($percent);
    }

    /**
     * Return the percent of the progress.
     *
     * <code>
     *
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $progressBar->build($gameMechanic);
     *
     * $percentage    = $progressBar->getPercent();
     * </code>
     *
     * @return number
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Return percentages that remain to the end.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $progressBar->build($gameMechanic);
     *
     * $percentage    = $progressBar->getPercentNext();
     * </code>
     *
     * @return number
     */
    public function getPercentNext()
    {
        return $this->percentNext;
    }

    /**
     * Return last ( the current ) object that has been reached.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $progressBar->build($gameMechanic);
     *
     * $currentUnit   = $progressBar->getCurrentUnit();
     * </code>
     *
     * @return object
     */
    public function getCurrentUnit()
    {
        return $this->currentUnit;
    }

    /**
     * Return the object that will have to be reached.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $progressBar->build($gameMechanic);
     *
     * $nextUnit      = $progressBar->getNextUnit();
     * </code>
     *
     * @return object
     */
    public function getNextUnit()
    {
        return $this->nextUnit;
    }

    /**
     * Return the number of points, which the user has.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $progressBar->build($gameMechanic);
     *
     * $points        = $progressBar->getPoints();
     * </code>
     *
     * @return integer
     */
    public function getPoints()
    {
        return (!empty($this->points)) ? $this->points->getPoints() : 0;
    }

    /**
     * Return the title of the current unit.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $progressBar->build($gameMechanic);
     *
     * $title         = $progressBar->getTitleCurrent();
     * </code>
     *
     * @return string
     */
    public function getTitleCurrent()
    {
        return (!empty($this->currentUnit)) ? $this->currentUnit->getTitle() : null;
    }

    /**
     * Return the number points of the current unit.
     * Those points are the value, needed to be reached from the user,
     * to receive the unit.
     *
     * <code>
     *
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $points        = $progressBar->getPointsCurrent();
     *
     * </code>
     *
     * @return integer
     */
    public function getPointsCurrent()
    {
        return (!empty($this->currentUnit)) ? $this->currentUnit->getPoints() : null;
    }

    /**
     * Return true if next unit exists.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    = Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * if(!$progressBar->hasNext()) {
     *     // ...
     * }
     * </code>
     *
     * @return boolean
     */
    public function hasNext()
    {
        return (!empty($this->nextUnit)) ? true : false;
    }

    /**
     * Return the name of the game mechanic, used in the process of calculation progress.
     *
     * <code>
     *
     * // Get user points
     * $keys = array(
     *       "user_id"   => 1,
     *       "points_id" => 2
     * );
     * $userPoints    =Gamification\User\Points::getInstance(\JFactory::getDbo, $keys);
     *
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     *
     * $progressBar   = new Gamification\User\ProgressBar(\JFactory::getDbo, $userPoints);
     * $progressBar->build($gameMechanic);
     *
     * if ("levels" == $progressBar->getGameMechanic()) {
     * ...
     * }
     * </code>
     *
     * @return string
     */
    public function getGameMechanic()
    {
        return $this->gameMechanic;
    }
}

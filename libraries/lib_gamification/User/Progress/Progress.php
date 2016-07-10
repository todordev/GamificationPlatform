<?php
/**
 * @package         Gamification\User
 * @subpackage      Progress
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Progress;

use Gamification\Mechanic\PointsBased;
use Gamification\User\Points\Points;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user progress based on points.
 *
 * @package         Gamification\User
 * @subpackage      Progress
 */
abstract class Progress
{
    /**
     * User points object.
     *
     * @var Points
     */
    protected $points;

    /**
     * Units objects.
     *
     * @var PointsBased
     */
    protected $currentUnit;
    protected $nextUnit;

    /**
     * This is the percentages of completed progress.
     *
     * @var int
     */
    protected $percentageCurrent;

    /**
     * These are the percentages that remain to the next unit.
     *
     * @var int
     */
    protected $percentageNext;

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
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     * $userPoints    = Gamification\User\Points\Points(\JFactory::getDbo);
     * $userPoints->load($keys);
     *
     * $progress   = new Gamification\User\Progress\Progress(\JFactory::getDbo, $userPoints);
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
     * Prepare current and next units data.
     */
    abstract public function prepareData();

    /**
     * Return the percent of the progress.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     * $userPoints    = Gamification\User\Points\Points(\JFactory::getDbo);
     * $userPoints->load($keys);
     *
     * $progress      = new Gamification\User\Progress\Progress(\JFactory::getDbo, $userPoints);
     * $percentage    = $progress->getPercentageCurrent();
     * </code>
     *
     * @return number
     */
    public function getPercentageCurrent()
    {
        return $this->percentageCurrent;
    }

    /**
     * Return percentages that remain to the end.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     * $userPoints    = Gamification\User\Points\Points(\JFactory::getDbo);
     * $userPoints->load($keys);
     *
     * $progress      = new Gamification\User\Progress\Progress(\JFactory::getDbo, $userPoints);
     *
     * $percentage    = $progress->getPercentageNext();
     * </code>
     *
     * @return int
     */
    public function getPercentageNext()
    {
        return $this->percentageNext;
    }

    /**
     * Return last ( the current ) object that has been reached.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     * $userPoints    = Gamification\User\Points\Points(\JFactory::getDbo);
     * $userPoints->load($keys);
     *
     * $progress      = new Gamification\User\Progress\Progress(\JFactory::getDbo, $userPoints);
     *
     * $currentUnit   = $progress->getCurrentUnit();
     * </code>
     *
     * @return PointsBased
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
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     * $userPoints    = Gamification\User\Points\Points(\JFactory::getDbo);
     * $userPoints->load($keys);
     *
     * $progress      = new Gamification\User\Progress\Progress(\JFactory::getDbo, $userPoints);
     *
     * $nextUnit      = $progress->getNextUnit();
     * </code>
     *
     * @return PointsBased
     */
    public function getNextUnit()
    {
        return $this->nextUnit;
    }

    /**
     * Return true if next unit exists.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     * $userPoints    = Gamification\User\Points\Points(\JFactory::getDbo);
     * $userPoints->load($keys);
     *
     * $progress      = new Gamification\User\Progress\Progress(\JFactory::getDbo, $userPoints);
     *
     * if(!$progress->hasNext()) {
     *     // ...
     * }
     * </code>
     *
     * @return bool
     */
    public function hasNext()
    {
        return (bool)($this->nextUnit !== null);
    }

    /**
     * Return user Points object.
     *
     * <code>
     * // Get user points
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     * $userPoints    = Gamification\User\Points\Points(\JFactory::getDbo);
     * $userPoints->load($keys);
     *
     * $progress      = new Gamification\User\Progress\Progress(\JFactory::getDbo, $userPoints);
     *
     * $points      = $progress->getPoints();
     * </code>
     *
     * @return Points
     */
    public function getPoints()
    {
        return $this->points;
    }
}

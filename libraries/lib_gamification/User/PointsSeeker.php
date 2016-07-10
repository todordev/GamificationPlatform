<?php
/**
 * @package         Gamification\User
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Gamification\User\Points\Points;

defined('JPATH_PLATFORM') or die;

/**
 * Abstract class that provides an base interface for points seekers.
 *
 * @package         Gamification\User
 * @subpackage      Points
 */
abstract class PointsSeeker
{
    /**
     * Database driver.
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     * @var Points
     */
    protected $userPoints;

    /**
     * Initialize the object.
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Set database object.
     *
     * <code>
     * $seeker   = new Gamification\User\Level\LevelPointsSeeker();
     * $seeker->setDb(\JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver $db
     */
    public function setDb(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

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
     * $userPoints  = Gamification\User\Points(\JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * // Create user levels object based on points.
     * $level  = new Gamification\User\Level\LevelPointsSeeker(\JFactory::getDbo());
     * $level->setUserPoints($userPoints);
     * </code>
     *
     * @param Points $userPoints
     */
    public function setUserPoints(Points $userPoints)
    {
        $this->userPoints = $userPoints;
    }
    
    abstract public function find();
}

<?php
/**
 * @package         Gamification\User
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Points;

use Prism\Observer\Observable;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user points.
 *
 * @package         Gamification\User
 * @subpackage      Points
 */
class PointsManager extends Observable implements PointsManagerInterface
{
    /**
     * @var Points
     */
    protected $points;

    /**
     * Set user points object.
     *
     * <code>
     * $keys = array(
     *     "id" => 1,
     *     "group_id" => 2
     * );
     *
     * // Create user points object based.
     * $userPoints  = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $pointsManager = new Gamification\User\Points\PointsManager(\JFactory::getDbo());
     * $pointsManager->setPoints($userPoints);
     * </code>
     *
     * @param Points $points
     *
     * @return self
     */
    public function setPoints(Points $points)
    {
        $this->points = $points;
        
        return $this;
    }

    /**
     * Return user points object.
     *
     * <code>
     * $pointsManager = new Gamification\User\Points\PointsManager(\JFactory::getDbo());
     * $userPoints    = $pointsManager->getPoints();
     * </code>
     *
     * @return Points
     */
    public function getPoints()
    {
        return $this->points;
    }
    
    /**
     * Increase user points.
     *
     * <code>
     * $context = "com_content.reading";
     *
     * $keys = array(
     *     "id" => 1,
     *     "group_id" => 2
     * );
     *
     * // Create user points object based.
     * $points  = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $pointsManager = new Gamification\User\Points\PointsManager(\JFactory::getDbo());
     * $pointsManager->setPoints($points);
     *
     * $pointsManager->increase($context, 100, $options);
     *
     * </code>
     *
     * @param string $context
     * @param int $value
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function increase($context, $value, array $options = array())
    {
        if (!($this->points instanceof Points)) {
            throw new \UnexpectedValueException('It is missing user points object.');
        }

        // Implement JObservableInterface: Pre-processing by observers
        $this->observers->update('onBeforePointsIncrease', array($context, $value, &$this->points, &$options));

        $this->points->add($value);
        $this->points->store();

        // Implement JObservableInterface: Post-processing by observers
        $this->observers->update('onAfterPointsIncrease', array($context, $value, &$this->points, &$options));
    }

    /**
     * Decrease user points.
     *
     * <code>
     * $context = "com_content.reading";
     *
     * $keys = array(
     *     "id" => 1,
     *     "group_id" => 2
     * );
     *
     * // Create user points object based.
     * $points  = new Gamification\User\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $pointsManager = new Gamification\User\Points\PointsManager(\JFactory::getDbo());
     * $pointsManager->setPoints($points);
     *
     * $pointsManager->decrease($context, 100, $options);
     *
     * </code>
     *
     * @param string $context
     * @param int $value
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function decrease($context, $value, array $options = array())
    {
        if (!($this->points instanceof Points)) {
            throw new \UnexpectedValueException('It is missing user points object.');
        }

        // Implement JObservableInterface: Pre-processing by observers
        $this->observers->update('onBeforePointsDecrease', array($context, $value, &$this->points, &$options));

        $this->points->subtract($value);
        $this->points->store();

        // Implement JObservableInterface: Post-processing by observers
        $this->observers->update('onAfterPointsDecrease', array($context, $value, &$this->points, &$options));
    }

    /**
     * Check for existing records in the database.
     *
     * <code>
     * $keys = array(
     *    'user_id' => 1,
     *    'points_id' => 2,
     *    'hash' => md5($ip . $userId . $itemId)
     * );
     *
     * $pointsHistory     = new Gamification\Points\Points(\JFactory::getDbo());
     * if ($pointsHistory->isExists($keys)) {
     * .....
     * }
     * </code>
     *
     * @param int|array $keys
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function hasGiven($keys)
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__gfy_points_history', 'a'));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName('a.'.$column) . ' = ' . $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }
}

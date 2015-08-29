<?php
/**
 * @package         Gamification
 * @subpackage      Observers
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Observer;

defined('JPATH_PLATFORM') or die;

/**
 * Table class supporting modified pre-order tree traversal behavior based on points.
 *
 * @package     Gamification
 * @subpackage  Observers
 */
abstract class PointsObserver implements \JObserverInterface
{
    /**
     * The observed object.
     *
     * @var    object
     * @since  3.1.2
     */
    protected $unit;

    /**
     * Constructor: Associates to $unit $this observer
     *
     * @param   \JObservableInterface $unit Object to be observed.
     *
     * @since   3.1.2
     */
    public function __construct(\JObservableInterface $unit)
    {
        $unit->attachObserver($this);
        $this->unit = $unit;
    }

    /**
     * Pre-processor for $object->increase($points, $options).
     *
     * @param   \Gamification\User\Points   $points
     * @param   array $options
     *
     * @return  void
     */
    public function onBeforePointsIncrease($points, $options = array())
    {
    }

    /**
     * Post-processor for $object->increase($points, $options).
     *
     * @param   \Gamification\User\Points   $points
     * @param   array $options
     *
     * @return  void
     */
    public function onAfterPointsIncrease($points, $options = array())
    {
    }

    /**
     * Pre-processor for $object->decrease($points, $options).
     *
     * @param   \Gamification\User\Points   $points
     * @param   array $options
     *
     * @return  void
     */
    public function onBeforePointsDecrease($points, $options = array())
    {
    }

    /**
     * Post-processor for $object->decrease($points, $options).
     *
     * @param   \Gamification\User\Points   $points
     * @param   array $options
     *
     * @return  void
     */
    public function onAfterPointsDecrease($points, $options = array())
    {
    }
}

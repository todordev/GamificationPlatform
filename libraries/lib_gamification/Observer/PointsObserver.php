<?php
/**
 * @package         Gamification
 * @subpackage      Observers
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Observer;

use Gamification\User\Points\Points;

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
     * @var    \JObservableInterface
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
     * Pre-processor for $object->increase($context, $value, $points, $options).
     *
     * @param   string   $context
     * @param   int      $value
     * @param   Points   $points
     * @param   array    $options
     *
     * @return  void
     */
    public function onBeforePointsIncrease($context, $value, Points $points, array $options = array())
    {
    }

    /**
     * Post-processor for $object->increase($context, $value, $points, $options).
     *
     * @param   string   $context
     * @param   int      $value
     * @param   Points   $points
     * @param   array    $options
     *
     * @return  void
     */
    public function onAfterPointsIncrease($context, $value, Points $points, array $options = array())
    {
    }

    /**
     * Pre-processor for $object->decrease($context, $value, $points, $options).
     *
     * @param   string   $context
     * @param   int      $value
     * @param   Points   $points
     * @param   array    $options
     *
     * @return  void
     */
    public function onBeforePointsDecrease($context, $value, Points $points, array $options = array())
    {
    }

    /**
     * Post-processor for $object->decrease($context, $value, $points, $options).
     *
     * @param   string   $context
     * @param   int      $value
     * @param   Points   $points
     * @param   array    $options
     *
     * @return  void
     */
    public function onAfterPointsDecrease($context, $value, Points $points, array $options = array())
    {
    }
}

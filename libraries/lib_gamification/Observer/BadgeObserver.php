<?php
/**
 * @package         Gamification
 * @subpackage      Observers
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Observer;

use Gamification\User\Badge\Badge;

defined('JPATH_PLATFORM') or die;

/**
 * Table class supporting modified pre-order tree traversal badge behaviors.
 *
 * @package     Gamification
 * @subpackage  Observers
 */
abstract class BadgeObserver implements \JObserverInterface
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
     * Pre-processor for giving a badge.
     *
     * @param   string $context
     * @param   Badge  $badge
     * @param   array  $options
     *
     * @return  void
     */
    public function onBeforeGiveBadge($context, Badge $badge, array $options = array())
    {
    }

    /**
     * Post-processor for giving a badge.
     *
     * @param   string $context
     * @param   Badge  $badge
     * @param   array  $options
     *
     * @return  void
     */
    public function onAfterGiveBadge($context, Badge $badge, array $options = array())
    {
    }
}

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
 * Table class supporting modified pre-order tree traversal behavior.
 *
 * @package     Gamification
 * @subpackage  Observers
 */
abstract class Observer implements \JObserverInterface
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
     * @param   object $unit Object to be observed.
     *
     * @since   3.1.2
     */
    public function __construct($unit)
    {
        $unit->attachObserver($this);
        $this->unit = $unit;
    }

    /**
     * Pre-processor for $table->load($keys, $reset)
     *
     * @param   mixed   $keys    An optional primary key value to load the row by, or an array of fields to match.  If not
     *                           set the instance property value is used.
     * @param   boolean $reset   True to reset the default values before loading the new row.
     *
     * @return  void
     *
     * @since   3.1.2
     */
    public function onBeforeLoad($keys, $reset)
    {
    }

    /**
     * Post-processor for $table->load($keys, $reset)
     *
     * @param   boolean &$result The result of the load
     * @param   array   $row     The loaded (and already binded to $this->table) row of the database table
     *
     * @return  void
     *
     * @since   3.1.2
     */
    public function onAfterLoad(&$result, $row)
    {
    }

    /**
     * Pre-processor for $table->store($updateNulls)
     *
     * @param   boolean $updateNulls The result of the load
     * @param   string  $tableKey    The key of the table
     *
     * @return  void
     *
     * @since   3.1.2
     */
    public function onBeforeStore($updateNulls, $tableKey)
    {
    }

    /**
     * Post-processor for $table->store()
     *
     * @param   object &$unit Game mechanic unit.
     *
     * @return  void
     *
     * @since   3.1.2
     */
    public function onAfterStore(&$unit)
    {
    }

    /**
     * Pre-processor for $table->delete($pk)
     *
     * @param   mixed $pk An optional primary key value to delete.  If not set the instance property value is used.
     *
     * @return  void
     *
     * @since   3.1.2
     * @throws  \UnexpectedValueException
     */
    public function onBeforeDelete($pk)
    {
    }

    /**
     * Post-processor for $table->delete($pk)
     *
     * @param   mixed $pk The deleted primary key value.
     *
     * @return  void
     *
     * @since   3.1.2
     */
    public function onAfterDelete($pk)
    {
    }
}

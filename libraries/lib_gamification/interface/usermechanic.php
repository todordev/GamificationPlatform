<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This interface provides methods
 * that should be used for game mechanic classes.
 *
 * @package         GamificationPlatform
 * @subpackage      Interfaces
 */
interface GamificationInterfaceUserMechanic
{
    public function load($keys);
    public function bind($data);
    public function store();
}

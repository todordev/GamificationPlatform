<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport("gamification.interface.leaderboard");

/**
 * This class contains methods which creates leaderboard objects,
 * based on game mechanic.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
abstract class GamificationLeaderboard
{
    /**
     * Create an object based on game mechanic.
     *
     * <code>
     *
     * $keys = array(
     *     "group_id" => 1
     * );
     *
     * $options = array(
     *     "sort_direction" => "DESC",
     *     "limit"          => 10
     * );
     *
     * $leaderboard    = GamificationLeaderboard::factory("levels", $keys, $options);
     *
     * </code>
     *
     * @param  string $mechanic This is the mechanic, on which is based the results.
     * @param  array  $keys     These are the keys, which will be used for loading leaderboard data.
     * @param  array  $options
     *
     * @return object
     * @throws Exception
     */
    public static function factory($mechanic, $keys = array(), $options = array())
    {
        $mechanic = JString::strtolower($mechanic);
        $loaded   = jimport("gamification.leaderboard." . $mechanic);

        if (!$loaded) {
            throw new Exception('This game mechanic does not exists.');
        } else {
            // Build the name of the class, instantiate, and return
            $className = 'GamificationLeaderboard' . ucfirst($mechanic);

            return new $className($keys, $options);
        }
    }
}

<?php
/**
 * @package		 Gamification Platform
 * @subpackage	 Gamification Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('JPATH_PLATFORM') or die;

jimport("gamification.interface.leaderboard");

/**
 * This class contains methods which creates leaderboard objects,
 * based on game mechanic.
 */
abstract class GamificationLeaderboard {

    /**
     * Create an object based on game mechanics.
     *
     * @param  string $mechanic This is the mechanic, on which is based the results.
     * @param  array  $keys     These are the keys, which will be used for loading leaderboard data.
     *  
     * @return object
     * @throws Exception
     */
    public static function factory($mechanic, $keys = array(), $options = array())  {
    
        $mechanic = JString::strtolower($mechanic);
        $loaded = jimport("gamification.leaderboard.".$mechanic);
        
        if(!$loaded) {
            throw new Exception('This game mechanic does not exists.');
        } else {
            // Build the name of the class, instantiate, and return
            $className = 'GamificationLeaderboard'.ucfirst($mechanic);
            return new $className($keys, $options);
        }
    }
    
}


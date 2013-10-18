<?php
/**
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This interface provides methods that should be used for laederboard classes.
 *
 * @package		 GamificationPlatform
 * @subpackage	 Interfaces
 */
interface GamificationInterfaceLeaderboard {
    
    public function load($keys);
    
}

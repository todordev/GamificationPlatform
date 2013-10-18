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
 * This interface provides methods that should be used for classes,
 * which are based on Data Access Object Pattern.
 *
 * @package		 GamificationPlatform
 * @subpackage	 Interfaces
 */
interface GamificationInterfaceTable {
    
    public function load($keys, $reset = true);
    public function bind($data, $ignore = array());
    public function store($updateNulls = false);
    
}
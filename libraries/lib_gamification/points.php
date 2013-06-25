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

JLoader::register("GamificationTablePoint", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_gamification".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."point.php");

class GamificationPoints extends GamificationTablePoint {

    protected static $instances = array();
    
    public function __construct($id = 0) {
        
        // Set database driver
        $db = JFactory::getDbo();
        parent::__construct($db);
        
        if(!empty($id)) {
            $this->load($id);
        }
    }
    
    public static function getInstance($id = 0)  {
        
        // If it is array with user id and currency id, 
        // I will generate a new array index.
        if(!is_numeric($id)) {
            $keys = array(
                "abbr" => $id
            );
            
            $index = JApplication::stringURLSafe($id);
        } else {
            $keys  = $id;
            $index = $id;
        }
        
        if (empty(self::$instances[$index])){
            $item = new GamificationPoints($keys);
            self::$instances[$index] = $item;
        }
    
        return self::$instances[$index];
    }
    
    
}


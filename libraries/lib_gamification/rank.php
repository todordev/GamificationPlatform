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

JLoader::register("GamificationTableRank", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_gamification".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."rank.php");

class GamificationRank extends GamificationTableRank {

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
        
        if (empty(self::$instances[$id])){
            $item = new GamificationRank($id);
            self::$instances[$id] = $item;
        }
    
        return self::$instances[$id];
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getImage() {
        return $this->image;
    }
}


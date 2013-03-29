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

class GamificationProfile {

    protected $db    = null;
    protected $id    = null;

    public function __construct($db){
        $this->db = $db;
    }
    
    public function load($id = null) {
        
        $db     = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Create a new query object.
        $query  = $db->getQuery(true);
        
        $query
            ->select("id")
            ->from($db->quoteName("#__gfy_profiles"))
            ->where("id = ".(int)$id);
            
        $db->setQuery($query);
        $result = $db->loadAssoc();
        
        if(!empty($result)) {
            $this->bind($result);
        }
        
    }
    
    public function bind($data) {
        
        $this->id   = JArrayHelper::getValue($data, "id");
        
    }
    
    public function save() {
        
        $db     = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Create a new query object.
        $query  = $db->getQuery(true);
        $query
            ->insert($db->quoteName("#__gfy_profiles"))
            ->set($db->quoteName("id")  ." = " . (int)$this->id);
            
        $db->setQuery($query);
        $db->query();
    }
}

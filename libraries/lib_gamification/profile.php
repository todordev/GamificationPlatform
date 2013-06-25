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

/**
 * This class contains methods used for gamifying users. 
 * 
 * @todo It is not completed. Complete it!
 */
class GamificationProfile {

    public $id            = null;
    public $name          = null;
    public $username      = null;
    
    protected $db         = null;

    /**
     * Initialize user profile and his gamification units.
     * 
     * @param integer $id
     * @param array $options     This options are used for specifying some things for loading.
     */
    public function __construct($id = 0, $options = array()){
        $this->db = JFactory::getDbo();
        
        if(!empty($id)) {
            $this->load($id, $options);
        }
    }
    
    public function load($id = null, $options = array()) {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.name, a.username")
            ->from($this->db->quoteName("#__users"))
            ->where("a.id = ".(int)$id);
            
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!empty($result)) {
            $this->bind($result);
        }
        
    }
    
    public function bind($data) {
        
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
        
    }
    
}

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

class GamificationNotification {

    /**
     * Notification ID
     * @var integer
     */
    public $id;
    
    public $note     = "";
    public $read     = 0;
    public $image;
    public $url;
    public $created;
    public $user_id;
    
    /**
     * Database driver
     * @var JDatabaseMySQLi
     */
    protected $db;
    
	public function __construct($id = 0) {
        $this->db = JFactory::getDbo();
        
        if(!empty($id)) {
            $this->load($id);
        } else {
            $this->init();
        }
    }
    
    /**
     * Load user notification using.
     * @param integer $id
     */
    public function load($id) {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->select("a.*")
            ->from($this->db->quoteName("#__gfy_notifications") . ' AS a' )
            ->where("a.id   = ". (int)$id);
            
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!empty($result)) { // Set values to variables
            $this->bind($result);
        } else {
            $this->init();
        }
        
    }
    
    public function init() {
        
        $date          = new JDate();
        $this->created = $date->format("Y-m-d H:i:s");
        $this->read    = 0;
        $this->id      = null;
        
    }
    
    public function bind($data) {
        
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
        
    }
    
    public function setRead() {
        $this->read = 1;
    }
    
    public function setNotRead() {
        $this->read = 0;
    }
    
    protected function updateObject() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->update($this->db->quoteName("#__gfy_notifications"))
            ->set($this->db->quoteName("note")    ." = " . $this->db->quote($this->note) )
            ->set($this->db->quoteName("image")   ." = " . $this->db->quote($this->image) )
            ->set($this->db->quoteName("url")     ." = " . $this->db->quote($this->url) )
            ->set($this->db->quoteName("read")    ." = " . (int)$this->read)
            ->set($this->db->quoteName("user_id") ." = " . (int)$this->user_id)
            ->where($this->db->quoteName("id")    ." = " . (int)$this->id);
            
        $this->db->setQuery($query);
        $this->query();
    }
    
    protected function insertObject() {
        
        if(!$this->user_id) {
            throw new Exception("Invalid user id", 500);
        }
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $date = new JDate($this->created);
        $unixTimestamp = $date->toSql();
        
        $query
            ->insert($this->db->quoteName("#__gfy_notifications"))
            ->set($this->db->quoteName("note")    ." = " . $this->db->quote($this->note) )
            ->set($this->db->quoteName("created") ." = " . $this->db->quote($unixTimestamp) )
            ->set($this->db->quoteName("read")    ." = " . (int)$this->read)
            ->set($this->db->quoteName("user_id") ." = " . (int)$this->user_id);
            
        if(!empty($this->image)) {
            $query->set($this->db->quoteName("image")   ." = " . $this->db->quote($this->image) );
        }
        
        if(!empty($this->image)) {
            $query->set($this->db->quoteName("url")     ." = " . $this->db->quote($this->url) );
        }
        
        $this->db->setQuery($query);
        $this->db->query();
        
        return $this->db->insertid();
        
    }
    
    public function store() {
        
        if(!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }
    
    
    public function remove() {
        
        if(!$this->id) {
            throw new Exception(JText::_("Invalid notification."), ITPrismErrors::CODE_WARNING);
        }
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__gfy_notifications"))
            ->where($this->db->quoteName("id") ." = " . (int)$this->id);
        
        $this->db->setQuery($query);
        $this->db->query();
        
        $this->init();
        
    }
    
    /**
     * 
     * Initialize main variables, create a new notification 
     * and send it to user.
     * 
     * @param string $note
     * @param integer $userId    This is the receiver of the message.
     */
    public function send($note = null, $userId = null) {
        
        if(!empty($note)) {
            $this->note = $note;
        }
        if(!empty($userId)) {
            $this->user_id = (int)$userId;
        }
        
        // Initialize the properties read, id, created. 
        $this->init();
        
        $this->store();
    }
    
    public function setUserId($userId) {
        $this->user_id = $userId;
    }
    
}


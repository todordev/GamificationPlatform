<?php
/**
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("GamificationTableRank", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_gamification".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."rank.php");
JLoader::register("GamificationInterfaceTable", JPATH_LIBRARIES .DIRECTORY_SEPARATOR."gamification".DIRECTORY_SEPARATOR."interface".DIRECTORY_SEPARATOR."table.php");

/**
 * This class contains methods used for managing a rank.
 *
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 */
class GamificationRank implements GamificationInterfaceTable {

	protected $table;
	
    protected static $instances = array();
    
    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * $rankId  = 1;
     * $rank    = new GamificationRank($rankId);
     *
     * </code>
     *
     * @param number $id
     */
    public function __construct($id = 0) {
        
    	$this->table = new GamificationTableRank(JFactory::getDbo());
        
        if(!empty($id)) {
            $this->table->load($id);
        }
        
    }
    
    /**
     *
     * Create an instance of the object and load data.
     *
     * <code>
     *
     * $rankId = 1;
     * $rank   = GamificationRank::getInstance($rankId);
     *
     * </code>
     *
     * @param number $id
     *
     * @return null:GamificationRank
     */
    public static function getInstance($id = 0)  {
        
        if (empty(self::$instances[$id])){
            $item = new GamificationRank($id);
            self::$instances[$id] = $item;
        }
    
        return self::$instances[$id];
    }
    
    /**
     *
     * Get rank title.
     *
     * <code>
     *
     * $rankId = 1;
     * $rank   = GamificationRank::getInstance($rankId);
     * 
     * $title  = $rank->getTitle();
     *
     * </code>
     *
     * @return string
     */
    public function getTitle() {
        return $this->table->title;
    }
    
    /**
     *
     * Get rank image.
     *
     * <code>
     *
     * $rankId = 1;
     * $rank   = GamificationRank::getInstance($rankId);
     *
     * $image  = $rank->getImage();
     *
     * </code>
     *
     * @return string
     */
    public function getImage() {
        return $this->table->image;
    }
    
    /**
     * Load level data using the table object.
     *
     * <code>
     *
     * $levelId    = 1;
     * $level      = new GamificationLevel();
     * $level->load($levelId);
     *
     * </code>
     *
     * @param $keys
     * @param $reset
     *
     */
    public function load($keys, $reset = true) {
    	$this->table->load($keys, $reset);
    }
    
    /**
     * Set the data to the object parameters.
     *
     * <code>
     *
     * $data = array(
     * 	    "title" 	=> "......",
     * 		"points"   	=> 100,
     * 		"value" 	=> 1,
     * 		"published" => 1,
     * 		"points_id" => 2,
     * 		"rank_id"   => 3,
     * 		"group_id"  => 4
     * );
     *
     * $level   = new GamificationLevel();
     * $level->bind($data);
     *
     * </code>
     *
     * @param array $src
     * @param array $ignore
     */
    public function bind($src, $ignore = array()) {
    	$this->table->bind($src, $ignore);
    }
    
    /**
     * Save the data to the database.
     *
     * <code>
     *
     * $data = array(
     * 	    "title" 	=> "......",
     * 		"points"   	=> 100,
     * 		"value" 	=> 1,
     * 		"published" => 1,
     * 		"points_id" => 2,
     * 		"rank_id"   => 3,
     * 		"group_id"  => 4
     * );
     *
     * $level   = new GamificationLevel();
     * $level->bind($data);
     * $level->store(true);
     *
     * </code>
     *
     * @param $updateNulls
     *
     */
    public function store($updateNulls = false) {
    	$this->table->store($updateNulls);
    }
    
}


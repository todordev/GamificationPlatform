<?php
/**
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("GamificationTableBadge", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_gamification".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."badge.php");
JLoader::register("GamificationInterfaceTable", JPATH_LIBRARIES .DIRECTORY_SEPARATOR."gamification".DIRECTORY_SEPARATOR."interface".DIRECTORY_SEPARATOR."table.php");

/**
 * This class contains methods that are used for managing a badge.
 *
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 */
class GamificationBadge implements GamificationInterfaceTable {

	protected $table;
	
    protected static $instances = array();
    
    /**
     * Initialize the object and load data.
     * 
     * <code>
     * 
     * $badgeId = 1;
     * $badge   = new GamificationBadge($badgeId);
     * 
     * </code>
     * 
     * @param number $id
     */
    public function __construct($id = 0) {
        
        $this->table = new GamificationTableBadge(JFactory::getDbo());
        
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
     * $badgeId = 1;
     * $badge   = GamificationBadge::getInstance($badgeId);
     * 
     * </code>
     * 
     * @param number $id
     * 
     * @return GamificationBadge:null
     */
    public static function getInstance($id = 0)  {
        
        if (empty(self::$instances[$id])){
            $item = new GamificationBadge($id);
            self::$instances[$id] = $item;
        }
    
        return self::$instances[$id];
    }
    
    /**
     * Get badge title.
     * 
     * <code>
     * 
     * $badgeId    = 1;
     * $badge      = GamificationBadge::getInstance($badgeId);
     * $title 	   = $badge->getTitle();
     * 
     * </code>
     * 
     * @return string
     */
    public function getTitle() {
    	return $this->table->title;
    }
    
    /**
     * Get badge points.
     *
     * <code>
     *
     * $badgeId    = 1;
     * $badge      = GamificationBadge::getInstance($badgeId);
     * $points	   = $badge->getPoints();
     *
     * </code>
     *
     * @return number
     */
    public function getPoints() {
    	return $this->table->points;
    }
    
    /**
     * Get badge image.
     *
     * <code>
     *
     * $badgeId    = 1;
     * $badge      = GamificationBadge::getInstance($badgeId);
     * $image	   = $badge->getImage();
     *
     * </code>
     *
     * @return string
     */
    public function getImage() {
    	return $this->table->image;
    }
    
    /**
     * Get badge note.
     *
     * <code>
     *
     * $badgeId    = 1;
     * $badge      = GamificationBadge::getInstance($badgeId);
     * $note 	   = $badge->getNote();
     *
     * </code>
     *
     * @return string
     */
    public function getNote() {
    	return $this->table->note;
    }
    
    /**
     * Check for published badge.
     *
     * <code>
     *
     * $badgeId     = 1;
     * $badge       = GamificationBadge::getInstance($badgeId);
     * 
     * if(!$badge->isPublished()) {
     * }
     *
     * </code>
     *
     * @return boolean
     */
    public function isPublished() {
    	return (!$this->table->published) ? false : true;
    }
    
    /**
     * Get the points ID used of the badge.
     *
     * <code>
     *
     * $badgeId    = 1;
     * $badge      = GamificationBadge::getInstance($badgeId);
     * $pointsId   = $badge->getPointsId();
     *
     * </code>
     *
     * @return integer
     */
    public function getPointsId() {
    	return $this->table->points_id;
    }
    
    /**
     * Get the group ID of the badge.
     *
     * <code>
     *
     * $badgeId    = 1;
     * $badge      = GamificationBadge::getInstance($badgeId);
     * $groupId	   = $badge->getGroupId();
     *
     * </code>
     *
     * @return integer
     */
    public function getGroupId() {
    	return $this->table->group_id;
    }
    
    /**
     * Load badge data using the table object.
     *
     * <code>
     *
     * $badgeId    = 1;
     * $badge      = new GamificationBadge();
     * $badge->load($badgeId);
     *
     * </code>
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
     * 		"image" 	=> "picture.png",
     * 		"note"   	=> "......",
     * 		"published" => 1,
     * 		"points_id" => 2,
     * 		"group_id"  => 3
     * );
     *
     * $badge   = new GamificationBadge();
     * $badge->bind($data);
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
     * 		"image" 	=> "picture.png",
     * 		"note"   	=> null,
     * 		"published" => 1,
     * 		"points_id" => 2,
     * 		"group_id"  => 3
     * );
     *
     * $badge   = new GamificationBadge();
     * $badge->bind($data);
     * $badge->store(true);
     *
     * </code>
     *
     * @param $updateNulls
     */
    public function store($updateNulls = false) {
    	$this->table->store($updateNulls);
    }
    
}


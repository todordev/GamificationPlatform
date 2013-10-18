<?php
/**
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('gamification.interface.table');

/**
 * This is an object that represents user progress.
 * 
 * @package		 GamificationPlatform
 * @subpackage	 GamificationLibrary
 */
class GamificationUserProgressBar {

    /**
     * This is the number of points needed to be reached this level.
     * 
     * @var GamificationUserPoints
     */
    protected $points;
    
    protected $currentUnit;
    protected $nextUnit;
    
    /**
     * This is the percentages of completed progress.
     * 
     * @var integer
     */
    protected $percent;
    
    /**
     * These are the percentages that remain to the next unit.
     *
     * @var integer
     */
    protected $percentNext;
    
    /**
     * It is the game mechaninc, on which is based the progress.
     * It could be level, rank or badge.
     * 
     * @var string 
     */
    protected $gameMechanic;
    
    /**
     * Database driver
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     *
     * </code>
     *
     * @param GamificationUserPoints $points
     * @param string $gameMechanic
     */
    public function __construct(GamificationUserPoints $points, $gameMechanic) {
        
        $this->db           = JFactory::getDbo();
        $this->points       = $points;
        $this->gameMechanic = $gameMechanic;
        
        $this->init();
    }
    
    /**
     * Initialize the progress data.
     */
    protected function init() {
        
        $keys = array(
            "user_id"  => $this->points->user_id,
            "group_id" => $this->points->group_id
        );
        
        switch($this->gameMechanic) {
            
            case "levels":
                $this->prepareLevels($keys);
                break;
            
            case "ranks":
                $this->prepareRanks($keys);
                break;
                
            case "badges":
                $this->prepareBadges($keys);
                break;
        }
        
    }
    
    /**
     * Prepare current and next level.
     *
     * @param array $keys
     */
    protected function prepareLevels($keys) {
        
        jimport("gamification.userlevel");
        $this->currentUnit = GamificationUserLevel::getInstance($keys);
        
        $userPoints = $this->points->getPoints();
        
        // Get all units
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.title, a.points, a.value, a.published, a.points_id, a.rank_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_levels") . " AS a")
            ->where("a.points_id = ". (int)$this->points->points_id)
            ->where("a.points > ". (int)$userPoints);
        
        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadObject();
        
        if(!empty($result)) {
            $this->nextUnit     = $result;
            
            $this->percent      = $this->calcualtePercant($userPoints, $this->getPointsNext());
            $this->percentNext  = 100 - $this->percent;
            
        } else {
            $this->percent = 100;
            $this->percentNext = 100;
        }
        
    }
    
    /**
     * Prepare current and next ranks.
     * 
     * @param array $keys
     */
    protected function prepareRanks($keys) {
    
        jimport("gamification.userrank");
        $this->currentUnit = GamificationUserRank::getInstance($keys);
    
        $userPoints = $this->points->getPoints();
        
        // Get all units
        $query = $this->db->getQuery(true);
    
        $query
            ->select("a.id, a.title, a.points, a.image, a.published, a.points_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_ranks") . " AS a")
            ->where("a.points_id = ". (int)$this->points->points_id)
            ->where("a.published = 1")
            ->where("a.points > ". (int)$userPoints);
    
        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadObject();
    
        if(!empty($result)) {
            $this->nextUnit = $result;
    
            $this->percent      = $this->calcualtePercant($userPoints, $this->getPointsNext());
            $this->percentNext  = 100 - $this->percent;
    
        } else {
            $this->percent = 100;
            $this->percentNext = 100;
        }
    
    }
    
    /**
     * Prepare current and next badges.
     * 
     * @param array $keys
     */
    protected function prepareBadges($keys) {
    
        jimport("gamification.userbadges");
        $this->currentUnit = GamificationUserBadge::getInstance($keys);
    
        $userPoints = $this->points->getPoints();
        
        // Get all units
        $query = $this->db->getQuery(true);
    
        $query
            ->select("a.id, a.title, a.points, a.image, a.note, a.published, a.points_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_badges") . " AS a")
            ->where("a.points_id = ". (int)$this->points->points_id)
            ->where("a.published = 1")
            ->where("a.points > ". (int)$userPoints);
    
        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadObject();
    
        if(!empty($result)) {
            $this->nextUnit     = $result;
    
            $this->percent      = $this->calcualtePercant($userPoints, $this->getPointsNext());
            $this->percentNext  = 100 - $this->percent;
    
        } else {
            $this->percent      = 100;
            $this->percentNext  = 100;
        }
    
    }
    
    protected function calcualtePercant($currentValue, $nextValue) {
        
        $percent = ($currentValue/$nextValue) * 100;
        
        return abs($percent);
    }

    /**
     * Return the percent of the progress.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $precent       = $progressBar->getPercent();
     *
     * </code>
     * 
     * @return number
     */
    public function getPercent() {
        return $this->percent;
    }
    
    /**
     * Return percentages that remain to the end.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $precent       = $progressBar->getPercentNext();
     *
     * </code>
     * 
     * @return number
     */
    public function getPercentNext() {
        return $this->percentNext;
    }
    
    /**
     * Return the last ( the current ) object which has been reached.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $currentUnit   = $progressBar->getCurrentUnit();
     *
     * </code>
     * 
     * @return object
     */
    public function getCurrentUnit() {
        return $this->currentUnit;
    }
    
    /**
     * Return the object that will have to be reached.
     *
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $nextUnit      = $progressBar->getNextUnit();
     *
     * </code>
     * 
     * @return object
     */
    public function getNextUnit() {
        return $this->nextUnit;
    }
    
    /**
     * Return the number of points, which the user has.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $points        = $progressBar->getPoints();
     *
     * </code>
     * 
     * @return integer
     */
    public function getPoints() {
        return (!empty($this->points)) ? $this->points->getPoints() : 0;
    }
    
    /**
     * Return the title of the current unit.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $title         = $progressBar->getTitleCurrent();
     *
     * </code>
     * 
     * @return string
     */
    public function getTitleCurrent() {
        return (!empty($this->currentUnit)) ? $this->currentUnit->getTitle() : null;
    }
    
    /**
     * Return the title of the next unit.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $title         = $progressBar->getTitleNext();
     *
     * </code>
     * 
     * @return string
     */
    public function getTitleNext() {
        return (!empty($this->nextUnit)) ? $this->nextUnit->title : null;
    }
    
    /**
     * Return the number points of the current unit.
     * Those points are the value, needed to be reached from the user,
     * to receive the unit.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $points        = $progressBar->getPointsCurrent();
     *
     * </code>
     * 
     * @return integer
     */
    public function getPointsCurrent() {
        return (!empty($this->currentUnit)) ? $this->currentUnit->getPoints() : null;
    }
    
    /**
     * Return the number of points of the next unit.
     * Those points are the value, needed to be reached from the user,
     * to receive the unit.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $points        = $progressBar->getPointsNext();
     *
     * </code>
     * 
     * @return integer
     */
    public function getPointsNext() {
        return (!empty($this->nextUnit)) ? $this->nextUnit->points : null;
    }
    
    /**
     * Return true if a next unit exists.
     * 
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * if(!$progressBar->hasNext()) {
     *     // ...
     * }
     *
     * </code>
     * 
     * @return boolean
     */
    public function hasNext() {
        return (!empty($this->nextUnit)) ? true : false;
    }
    
    /**
     * Return the name of the game mechanic, used in the process of calculation progress.
     *
     * <code>
     *
     * // Get user points
     * $keys = array(
     * 	   "user_id"   => 1,
     * 	   "points_id" => 2
     * );
     * $userPoints    = GamificationUserPoints::getInstance($keys);
     * 
     * // A game mechanic - levels, ranks, badges,...
     * $gameMechanic  = "levels";
     * 
     * $progressBar   = new GamificationUserProgressBar($userPoints, $gameMechanic);
     * $gameMechanic  = $progressBar->getGameMechanic();
     * 
     * </code>
     * 
     * @return string
     */
    public function getGameMechanic() {
        return $this->gameMechanic;
    }
}


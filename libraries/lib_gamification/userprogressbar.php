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

jimport('gamification.interface.table');

/**
 * This is an object that represents user progress.
 */
class GamificationUserProgressBar {

    /**
     * This is the number of points needed to be reached this level.
     * 
     * @var GamificationUserPoints
     */
    public $points;
    
    public $groupId;
    public $userId;
    
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
    
    public function __construct(GamificationUserPoints $points, $gameMechanic) {
        
        $this->userId  = $points->user_id;
        $this->groupId = $points->group_id;
        
        $this->points  = $points;
        
        $this->gameMechanic   = $gameMechanic;
        
        $this->db = JFactory::getDbo();
        
        $this->init();
    }
    
    /**
     * Initialize the progress data.
     */
    protected function init() {
        
        $keys = array(
            "user_id"  => $this->userId,
            "group_id" => $this->groupId
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
            $this->nextUnit = $result;
            
            $this->percent      = $this->calcualtePercante($userPoints, $this->getPointsNext());
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
    
            $this->percent      = $this->calcualtePercante($userPoints, $this->getPointsNext());
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
    
            $this->percent      = $this->calcualtePercante($userPoints, $this->getPointsNext());
            $this->percentNext  = 100 - $this->percent;
    
        } else {
            $this->percent = 100;
            $this->percentNext = 100;
        }
    
    }
    
    protected function calcualtePercante($currentValue, $nextValue) {
        
        $percent = ($currentValue/$nextValue) * 100;
        
        return abs($percent);
    }

    public function getPercent() {
        return $this->percent;
    }
    
    public function getPercentNext() {
        return $this->percentNext;
    }
    
    public function getCurrentUnit() {
        return $this->currentUnit;
    }
    
    public function getNextUnit() {
        return $this->nextUnit;
    }
    
    /**
     * Return the user points.
     * 
     * @return integer
     */
    public function getPoints() {
        return (!empty($this->points)) ? $this->points->getPoints() : 0;
    }
    
    /**
     * Return the title of the current unit.
     */
    public function getTitleCurrent() {
        return (!empty($this->currentUnit)) ? $this->currentUnit->getTitle() : null;
    }
    
    /**
     * Return the title of the next unit.
     */
    public function getTitleNext() {
        return (!empty($this->nextUnit)) ? $this->nextUnit->title : null;
    }
    
    /**
     * Return the points of the current unit.
     */
    public function getPointsCurrent() {
        return (!empty($this->currentUnit)) ? $this->currentUnit->getPoints() : null;
    }
    
    /**
     * Return the points of the next unit.
     */
    public function getPointsNext() {
        return (!empty($this->nextUnit)) ? $this->nextUnit->points : null;
    }
    
    /**
     * Return flag true if next unit exists.
     * 
     * @return boolean
     */
    public function hasNext() {
        return (!empty($this->nextUnit)) ? true : false;
    }
    
    public function getGameMechanic() {
        return $this->gameMechanic;
    }
}


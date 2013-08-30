<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Gamification Html Helper
 *
 * @package		Gamification
 * @subpackage	Components
 * @since		1.6
 */
abstract class JHtmlGamification {
    
    /**
     * @var   array   array containing information for loaded files
     */
    protected static $loaded = array();
    
    public static function helper() {
    
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }
    
        JHtml::_('script', 'media/com_gamification/js/helper.js', false, false, false, false, false);
        self::$loaded[__METHOD__] = true;
    
        return;
    
    }
    
    public static function points($value, $name, $abbr) {
    
        if(!$value) {
            $html = '--';
        } else {
            $html = '<span class="hasTooltip" title="'.htmlspecialchars($name, ENT_QUOTES, "UTF-8").'">'.$value.' [ ' .$abbr.' ]'.'</span>';
        }
    
        return $html;
    }
    
    public static function badge($image, $alt, $tip = false, $note = "") {
    
        $title   = "";
        $class   = "";
        $classes = array();
        
        if(!empty($tip) AND !empty($note)) {
            
            JHtml::_("bootstrap.tooltip");
            
            $classes[] = "hasTooltip";
            
            $tip   = strip_tags(JString::trim($note));
            $title = ' title="'.htmlspecialchars($note, ENT_QUOTES, "UTF-8").'"';
            
        }
        
        // Preapare class property
        if(!empty($classes)) {
            $class = ' class="' . implode(" ", $classes) .'"';
        }
        
        // Preapare alt property
        $alt = strip_tags(JString::trim($alt));
        if(!empty($alt)) {
            $alt = ' alt="'.htmlspecialchars($alt, ENT_QUOTES, "UTF-8").'"';
        }
        
        $html = '<img src="'.$image.'"' . $class . $alt. $title.' />';
    
        return $html;
    }
    
    public static function progress($progress, $tip = false) {
    
        $titleCurrent   = "";
        $titleNext      = "";
        $classes        = array();
    
        $html = array();
        
        $userPoints     = $progress->getPoints();
        $gameMechanic   = $progress->getGameMechanic();
        
        // Prepare current level
        if(!empty($tip)) {
            
            JHtml::_("bootstrap.tooltip");
            
            $classes[]    = "hasTooltip";
            $titleCurrent = ' title="'.JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_INFORMATION", $userPoints).'"';
        }
        
        // START Labels
        $html[] = '<div class="gfy-progress-labels">';
        
        $html[] = '<div class="gfy-prgss-lbl-current">';
        $html[] = htmlspecialchars($progress->getTitleCurrent(), ENT_QUOTES, "UTF-8");
        $html[] = '</div>';
        
        // Prepare next level
        if($progress->hasNext()) {
        
            $titleNext = htmlspecialchars($progress->getTitleNext(), ENT_QUOTES, "UTF-8");
        
            $html[] = '<div class="gfy-prgss-lbl-next">';
            $html[] = $titleNext;
            $html[] = '</div>';
        
            if(!empty($tip)) {
                $pointsNext   = $progress->getPointsNext();
                $neededPoints = abs($pointsNext - $userPoints);
                
                switch($gameMechanic) {
                
                    case "badges":
                        $titleNext = ' title="'.JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_BADGES_INFORMATION_REACH", $neededPoints, $titleNext).'"';
                        break;
                
                    case "ranks":
                        $titleNext = ' title="'.JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_RANKS_INFORMATION_REACH", $neededPoints, $titleNext).'"';
                        break;
                
                    default:
                        $titleNext = ' title="'.JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_LEVELS_INFORMATION_REACH", $neededPoints, $titleNext).'"';
                        break;
                }
            }
        
        }
        
        // END Labels
        $html[] = '</div>';
        
        $html[] = '<div class="clearfix"></div>';
        $html[] = '<div class="progress">';
        $html[] = '<div class="bar bar-success '.implode(" ", $classes).'" ' . $titleCurrent. ' style="width: '. $progress->getPercent() .'%;"></div>';
        
        if($progress->hasNext()) {
            $html[] = '<div class="bar bar-warning '.implode(" ", $classes).'" ' . $titleNext. ' style="width: '. $progress->getPercentNext().'%;"></div>';
        }
        
        $html[] = '</div>';
    
        return implode("\n", $html);
    }
    
    public static function boolean($value, $title = "") {
	    
        $title = addslashes(htmlspecialchars(JString::trim($title), ENT_COMPAT, 'UTF-8'));
        
	    if(!$value) { // unpublished
		    $class  = "unpublish";
	    } else {
	        $class  = "ok";
	    }
	    
	    if(!empty($title)) {
	        $title  = ' title="'.$title.'"';
	    }
		
		$html[] = '<a class="btn btn-micro" rel="tooltip" ';
		$html[] = ' href="javascript:void(0);" ' . $title. '">';
		$html[] = '<i class="icon-' . $class . '"></i>';
		$html[] = '</a>';
		
		return implode($html);
	}
    
}
